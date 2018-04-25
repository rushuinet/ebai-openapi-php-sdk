<?php

namespace eBaiOpenApi\Api;


class ShopService extends RequestService
{
    /**
     * 店铺列表
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Shop-shop_list
     * @param $page
     * @return mixed
     */
    public function get_shop_list()
    {
        $params = array(
            //'sys_status'=>6 //拉取正常营业的状态
        );
        return $this->call('shop.list',$params);
    }

    /**
     * 店铺信息
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Shop-shop_get
     * @param $shop_id
     * @return mixed
     */
    public function get_shop_info($shop_id)
    {
        $params = array(
            'shop_id'=>$shop_id
        );
        return $this->call('shop.get',$params);
    }

    /**
     * 创建店铺
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=93acef27c3aa4d8286d5c8c26b493629
     * @param $params
     * @return mixed
     */
    public function create_shop($params)
    {
        return $this->call('shop.create',$params);
    }

    /**
     * 更新店铺
     * @doc https://opendj.jd.com/staticnew/widgets/resources.html?groupid=194&apiid=2600369a456446f0921e918f3d15e96a
     * @param $params
     * @return mixed
     */
    public function update_shop($params)
    {
        return $this->call('shop.update',$params);
    }


}