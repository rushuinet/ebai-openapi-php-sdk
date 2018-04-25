<?php

namespace eBaiOpenApi\Api;


class ProductService extends RequestService
{

    /**
     * 获取后台分类
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_category_list
     * @return mixed
     */
    public function get_cates($level=1,$pid=0,$keyword='')
    {
        $params = array(
            'depth'=>$level,
            'parent_id'=>$pid,
            'keyword'=>$keyword,
        );
        return $this->call('sku.category.list',$params);
    }

    /**
     * 获取分类
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_category_list
     * @return mixed
     */
    public function get_category($shop_id)
    {
        $params = array(
            'shop_id'=>$shop_id
        );
        return $this->call('sku.shop.category.get',$params);
    }

    /**
     * 创建商品分类
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_shop_category_create
     * @param $shop_id
     * @param $name
     * @param $sort
     * @param $category_id
     * @return mixed
     */
    public function create_category($shop_id,$name,$sort)
    {
        $params = array(
            'shop_id'=>$shop_id,
            'parent_category_id'=>0,
            'name'=>$name,
            'rank'=>$sort
        );
        return $this->call('sku.shop.category.create',$params);
    }

    /**
     * 修改商品分类
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_shop_category_update
     * @param $id
     * @param $name
     * @return mixed
     */
    public function update_category($shop_id,$name,$old_name,$sort)
    {
        $params = array(
            'shop_id'=>$shop_id,
            'category_name_origin'=>$old_name,
            'category_name'=>$name,
            'sequence'=>$sort
        );
        return $this->call('sku.shop.category.update',$params);
    }

    /**
     * 删除分类
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_shop_category_delete
     * @param $shop_id
     * @param $pid
     * @return mixed
     */
    public function category_sorting($shop_id,$category_id)
    {
        $params = array(
            'shop_id'=>$shop_id,
            'category_id'=>$category_id
        );
        return $this->call('sku.shop.category.delete',$params);
    }

    /**
     * 获取商品列表
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_list
     * @param $shop_id
     * @return mixed
     */
    public function get_product_list($shop_id)
    {
        $params = [
            'shop_id'=>$shop_id
        ];
        return $this->call('sku.list',$params);
    }

    /**
     * 创建商品
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_create
     * @param $params
     * @return mixed
     */
    public function create_product($params)
    {
        return $this->call('sku.create',$params);
    }

    /**
     * 修改商品
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_update
     * @param $params
     * @return mixed
     */
    public function update_product($params)
    {
        return $this->call('sku.update',$params);
    }


    /**
     * 删除商品
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_delete
     * @param $shop_id
     * @param $product_id
     * @return mixed
     */
    public function delete_product($shop_id,$product_id)
    {
        $params = [
            'shop_id'=>$shop_id,
            'custom_sku_id'=>$product_id,
        ];
        return $this->call('sku.delete',$params);
    }

    /**
     * 商品上架
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_online
     * @param $shop_id
     * @param $product_ids_str
     * @return mixed
     * @throws \Exception
     */
    public function batch_shelf_online($shop_id,$product_ids_str)
    {
        $params = array(
            'shop_id'=>$shop_id,
            'custom_sku_id'=>$product_ids_str
        );
        return $this->call('sku.online',$params);
    }

    /**
     * 商品下架
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_offline
     * @param $shop_id
     * @param $product_ids_str
     * @return mixed
     * @throws \Exception
     */
    public function batch_shelf_offline($shop_id,$product_ids_str)
    {
        $params = array(
            'shop_id'=>$shop_id,
            'custom_sku_id'=>$product_ids_str
        );
        return $this->call('sku.offline',$params);
    }


    /**
     * 批量更新商品库存
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Sku-sku_stock_update_batch
     * @param $shop_id
     * @param $food_data
     * @return mixed
     */
    public function batch_product_stock($shop_id,$food_data)
    {
        $params = array(
            'shop_id'=>$shop_id,
            'custom_sku_id'=>$food_data,
        );
        return $this->call('sku.stock.update.batch',$params);
    }

    /**
     * 上传图片
     * @doc https://open-be.ele.me/dev/api/doc/v3/#api-Common-picture_upload
     * @param $shop_id
     * @param $list
     * @return mixed
     */
    public function image_upload($img_url)
    {
        $params = array(
            'url'=>$img_url
        );
        return $this->call('picture.upload',$params);
    }

}