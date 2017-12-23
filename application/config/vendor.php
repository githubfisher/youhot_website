<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15/11/30
 * Time: PM4:50
 */
/**
 * aliyun上传配置文件在application/libraries/aliyun-oss/Config里面,注意修改
 */

$config['aliyun'] = array(
    'access_id'=> 'LTAIBxjAg6XhWH6q',
    'access_secret'=>'PtXDC2VufWif8rVxAC3SKrKGz1L7x7',
    'opensearch_host'=>'http://intranet.opensearch-cn-hangzhou.aliyuncs.com',
    'opensearch_app'=>'styl_product',
);

$config['redis'] = array(
    'host'=> '127.0.0.1',
    'port'=>6379
);
//$app_key, $master_secret
$config['jpush'] = array(
    'app_key' =>'784016a42633d5de3fcd0186',
    'master_secret' =>'1137e35a586d5dbe9c847bad'
);

//$app_key, $master_secret
//$config['rongyun_dev'] = array(
//    'app_key' =>'pkfcgjstfdg88',
//    'app_secret' =>'mDhggkO0RVWoRz'
//);
$config['rongyun'] = array(
      'app_key' => 'p5tvi9dsp5h84',
      'app_secret' => 'WNuVadKVovE4'
//    'app_key' =>'pkfcgjstfdg88',
//    'app_secret' =>'mDhggkO0RVWoRz'
);

$config['paypal_receiver_user'] = array(
    'receiver_email' =>'cyan@sina.com',
);
//$config['rongyun_production'] = array(
//    'app_key' =>'c9kqb3rdkub2j',
//    'app_secret' =>'QOQHfOQJnOp'
//);
