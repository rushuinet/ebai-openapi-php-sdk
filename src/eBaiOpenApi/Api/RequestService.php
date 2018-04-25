<?php

namespace eBaiOpenApi\Api;

use eBaiOpenApi\Config\Config;
use eBaiOpenApi\Protocol\Client;

class RequestService
{
    /** @var Client  */
    protected $client;
    protected $action='';
    protected $params=array();
    protected $rows_num = "50";

    public function __construct(Config $config)
    {
        $this->client = new Client($config);
    }

    public function check()
    {
        return true;
    }

    public function action()
    {
        return $this->action;
    }

    public function params()
    {
        return $this->params;
    }

    public function call($action='',$params=[])
    {
        $this->action = $action;
        $this->params = $params;
        if($this->client->send($this) === false)
        {
            throw new \Exception($this->client->getLastError(),$this->client->getLastErrno());
        }
        return $this->client->getLastData();
    }

}