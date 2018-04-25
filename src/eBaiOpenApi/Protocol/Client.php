<?php
namespace eBaiOpenApi\Protocol;
use eBaiOpenApi\Api\RequestService;
use eBaiOpenApi\Config\Config;
use Exception;
/**
 * Baidu Waimai Openapi SDK For PHP, Version:1.0
 * Api WebSite : http://api.waimai.baidu.com
 * @filename : Openapi.php
 * @author   : zhangjianguo@baidu.com
 * @date     : 2015-08-13 20:30:33
 * $Ids$
 */
/* vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 */
class Client{
    /**
     * SDK 版本号
     */
    const SDK_VERSION = '1.1';
    /**
     * 本地异常错误码
     */
    const LOCAL_ERRNO = -1;

    /**
     * 请求有效期，默认0为不检查
     */
    private $_expireTime = 0;

    /**
     * http timeout
     */
    private $_httpTimeout = 8;

    /**
     * 失败重试次数
     */
    private $_httpRetry = 3;
    /**
     * 合作方账号
     */
    private $_source;
    /**
     * 合作方密钥
     */
    private $_secret;

    /**
     * url
     */
    private $_url;

    /**
     * api版本
     */
    private $_version = 3;

    /**
     * 加密方式
     */
    private $_encrypt = '';

    /**
     * 上次响应码
     */
    private $_lastErrno;

    /**
     * 上次请求的命令
     */
    private $_lastCmd;

    /**
     * 上次响应信息
     */
    private $_lastError;

    /**
     * 上次响应数据
     */
    private $_lastData;

    /**
     * 上次响应数据body
     */
    private $_lastBody;

    /**
     * 上次返回的原始数据
     */
    private $_lastDataRaw;

    /**
     * 构造函数
     *
     * @param Config $config = array(
     *  'source' => '', //合作方source
     *  'secret' => '', //密钥
     *  'url' => '', //api地址
     *  'version' => '', //api版本号，默认为2
     *  'expire' => '', //命令有效期，默认为不配置
     *  'timeout' => '', //http超时设置单位秒
     *  'retry' => '', //http失败重试次数，默认为3
     * );
     */
    public function __construct($config){
        $this->_source = $config->get_app_key();
        $this->_secret = $config->get_app_secret();
        $this->_url = $config->get_request_url();
    }


    /**
     * 发送命令
     * @param RequestService $request
     * @return bool
     */
    public function send($request){
        $this->_reset();
        $req = $this->_buildCmd($request->action(), $request->params());
        $res = $this->_sendReq($req);
        $this->_lastDataRaw = $res;
        $arr = @json_decode($res, true);
        return $this->_parseRes($arr);
    }

    /**
     * 创建响应数据
     */
    public function buildRes($cmd, $ticket, $errno, $error, $data = array()){
        $this->_reset();
        $cmd = 'resp.' . $cmd;
        $body = array();
        $body['errno'] = $errno;
        $body['error'] = $error;
        $body['data'] = $data;
        return $this->_buildCmd($cmd, $body, $ticket);
    }

    /**
     * 解析命令
     */
    public function parseCallBack($res){
        $this->_reset();
        return $this->_parseCmd($res);
    }

    /**
     * 获取上次请求的错误码
     */
    public function getLastErrno(){
        return $this->_lastErrno;
    }

    /**
     * 获取上次请求错误描述
     */
    public function getLastError(){
        return $this->_lastError;
    }

    /**
     * 获取上次请求的响应数据
     */
    public function getLastData(){
        return $this->_lastData;
    }

    /**
     * 获取上次请求的响应body
     */
    public function getLastBody(){
        return $this->_lastBody;
    }

    /**
     * 获取上次请求返回的原始数据
     */
    public function getLastDataRaw(){
        return $this->_lastDataRaw;
    }

    /**
     * 获取上次请求返回的命令
     */
    public function getLastCmd(){
        return $this->_lastCmd;
    }

    /**
     * 获取上次请求返回的Ticket
     */
    public function getLastTicket(){
        return $this->_lastTicket;
    }

    /**
     * 重置上次请求结果
     */
    private function _reset(){
        $this->_lastErrno = 0;
        $this->_lastError = '';
        $this->_lastData = null;
        $this->_lastBody = null;
        $this->_lastDataRaw = null;
        $this->_lastCmd = null;
        $this->_lastVersion = null;
        $this->_lastTimestamp = null;
        $this->_lastTicket = null;
    }

    /**
     * 构建请求数据
     */
    private function _buildCmd($cmd, $body, $ticket = ''){
        if(empty($ticket)){
            $ticket = $this->_genTicket();
        }
        $req = array();
        $req['cmd'] = $cmd;
        $req['source'] = $this->_source;
        $req['secret'] = $this->_secret;
        $req['ticket'] = $ticket;
        $req['version'] = $this->_version;
        $req['encrypt'] = $this->_encrypt;
        $req['timestamp'] = time();
        $req['body'] = $body;
        $req['body'] = json_encode($req['body']);
        $req['sign'] = $this->_genSign($req);
        foreach ($req as $key => $v) {
            $dataR[] = "$key=$v";
        }
        $dataR = implode('&', $dataR);
        return $dataR;
    }

    /**
     * 解析响应结果
     */
    private function _parseRes($res){
        if(false === $this->_parseCmd($res)){
            return false;
        }
        if(!isset($this->_lastBody['errno'])){
            return $this->_setError($_error);
        }
        if(!isset($this->_lastBody['error'])){
            $_error = 'response error not found';
            return $this->_setError($_error);
        }
        $_errno = $this->_lastBody['errno'];
        $_error = $this->_lastBody['error'];
        $_data = isset($this->_lastBody['data']) ? $this->_lastBody['data'] : null;
        return $this->_setError($_error, $_errno, $_data);
    }

    /**
     * 解析响应结果
     */
    private function _parseCmd($res){
        if(empty($res) || !is_array($res)){
            $_error = 'invalid data';
            return $this->_setError($_error);
        }
        if(!isset($res['cmd']) || empty($res['cmd'])){
            $_error = 'empty cmd';
            return $this->_setError($_error);
        }
        if(!isset($res['ticket']) || !$this->_isTicket($res['ticket'])){
            $_error = 'invalid ticket';
            return $this->_setError($_error);
        }
        if(!isset($res['source']) || $res['source'] != $this->_source){
            $_error = 'invalid source';
            return $this->_setError($_error);
        }
        if(!isset($res['version']) || !is_numeric($res['version'])){
            $_error = 'invalid version';
            return $this->_setError($_error);
        }
        if(!isset($res['timestamp']) || !$this->_isTimestamp($res['timestamp'])){
            $_error = 'invalid timestamp';
            return $this->_setError($_error);
        }
        if($this->_expireTime > 0){
            $ttl = time() - $res['timestamp'];
            if(abs($ttl) > $this->_expireTime){
                $_error = 'cmd expired';
                return $this->_setError($_error);
            }
        }
        if(!isset($res['sign']) || !$this->_isSign($res['sign'])){
            $_error = 'invalid sign';
            return $this->_setError($_error);
        }
        if(!isset($res['body'])){
            $_error = 'response body not found';
            return $this->_setError($_error);
        }

        $_sign_res = $res;
        //
        $_sign_res['body'] = json_encode($_sign_res['body']);
        $sign = $this->_genSign($_sign_res
        );
        if($sign !== $res['sign']){
            $_error = sprintf('sign not match, required[%s] given[%s]', $sign, $res['sign']);
            return $this->_setError($_error);
        }
        $this->_lastCmd = $res['cmd'];
        $this->_lastTicket = $res['ticket'];
        $this->_lastTimestamp = $res['timestamp'];
        $this->_lastVersion = $res['version'];
        $this->_lastBody = $res['body'];
        return true;
    }

    /**
     * 校验ticket
     */
    private function _isTicket($ticket){
        return preg_match('/^[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}$/', $ticket);
    }

    /**
     * 校验时间戳
     */
    private function _isTimestamp($timestamp){
        return preg_match('/^\d{10}$/', $timestamp);
    }

    /**
     * 校验签名格式
     */
    private function _isSign($sign){
        return preg_match('/^[A-Z0-9]{32}$/', $sign);
    }

    /**
     * 设置异常信息
     */
    private function _setError($error, $errno = self::LOCAL_ERRNO, $data = null){
        $this->_lastError = $error;
        $this->_lastErrno = intval($errno);
        $this->_lastData = $data;
        return 0 === $this->_lastErrno;
    }

    /**
     * 数组排序
     */
    private function _arraySort(&$arr){
        ksort($arr);
        foreach($arr as &$v){
            if(is_array($v)){
                $this->_arraySort($v);
            }
        }
        return true;
    }

    /**
     * 生成ticket
     */
    private function _genTicket(){
        $uuid = '';
        if(function_exists('com_create_guid')){
            $uuid = trim(com_create_guid(), '{}');
        }else{
            mt_srand((double)microtime()*10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
        }
        return strtoupper($uuid);
    }

    /**
     * 生成sign
     */
    private function _genSign($data){
        $arr = array();
        $arr['body'] = $data['body'];
        $arr['cmd'] = $data['cmd'];
        $arr['encrypt'] = $data['encrypt'];
        $arr['secret'] = $this->_secret;
        $arr['source'] = $data['source'];
        $arr['ticket'] = $data['ticket'];
        $arr['timestamp'] = $data['timestamp'];
        $arr['version'] = $data['version'];
        ksort($arr);
        $tmp = array();
        foreach ($arr as $key => $value) {
            $tmp[] = "$key=$value";
        }
        $strSign = implode('&', $tmp);

        $sign = strtoupper(md5($strSign));
        return $sign;
    }



    /**
     * 发送请求
     */
    private function _sendReq($data){
        $output = '';
        $retry = 0;
        $userAgent = sprintf('Baidu-Waimai-Openapi-SDK-PHP-%s/%s', self::SDK_VERSION, $this->_source);
        do{
            $url = sprintf('%s?retry=%s', $this->_url, $retry);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->_httpTimeout);
            curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
            //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }
            $output = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            if(isset($info['http_code']) && 200 == $info['http_code']){
                return $output;
            }
            $retry++;
        }while($retry <= $this->_httpRetry && $retry < 10);
        return $output;
    }
}
