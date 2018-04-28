<?php

namespace eBaiOpenApi\Api;


class OrderService extends RequestService
{
    /**
     * 获取订单
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Order_Up-order_get
     * @param $order_id
     * @return mixed
     */
    public function get_order($order_id)
    {
        $params = array(
            'order_id'=>$order_id
        );
        return $this->call('order.get',$params);
    }

    /** 确认接单
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Order_Up-order_confirm
     * @param string $order_id
     * @return mixed
     */
    public function confirm_order($order_id)
    {
        $params = array(
            'order_id'=>$order_id,
        );
        return $this->call("order.confirm", $params);
    }

    /**
     * 取消订单单
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Order_Up-order_cancel
     * @param  string $order_id
     * @return mixed
     */
    public function cancel_order($order_id,$type,$reason)
    {
        $params = array(
            'order_id'=>$order_id,
            'type'=>$type,
            'reason'=>$reason,
        );
        return $this->call("order.cancel", $params);
    }

    /**
     * 获取订单状态
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Order_Up-order_status_get
     * @param  string $order_id
     * @return mixed
     */
    public function get_order_status($order_id)
    {
        $params = array(
            'order_id'=>$order_id,
        );
        return $this->call("order.status.get", $params);
    }

    /**
     * 订单创建
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Order_Down-order_create
     * @param  string $order_id
     * @return mixed
     */
    public function order_create($order_id)
    {
        $params = array(
            'order_id'=>$order_id,
        );
        return $this->call("order.create", $params);
    }

    /**
     * 推送订单状态
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Order_Down-order_status_push
     * @param  string $order_id
     * @param  string $status
     * @return mixed
     */
    public function push_order_status($order_id,$status)
    {
        $params = array(
            'order_id'=>$order_id,
            'status'=>$status,
        );
        return $this->call("order.status.push", $params);
    }

}