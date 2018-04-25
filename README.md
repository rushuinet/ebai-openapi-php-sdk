# 饿百开放平台 PHP SDK 接入指南

## 接入指南

  1. PHP version >= 5.4 & curl extension support
  2. 通过composer安装SDK
  3. 创建Config配置类，填入key，secret和sandbox参数
  4. 使用sdk提供的接口进行开发调试
  5. 线上环境将Config中$sandbox值设为false

### 安装

```
php
    composer require ddxq/ebai-openapi-php-sdk
```

### 基本用法

```
php
    use eBaiOpenApi\Config\Config;
    use eBaiOpenApi\Api\ShopService;
    
    //实例化一个配置类
    $config = new Config($app_key, $app_secret, false);
    
    //使用config和token对象，实例化一个服务对象
    $shop_service = new ShopService($config);
    
    //调用服务方法，获取资源
    $shop = shop_service->get_shop_list();
```