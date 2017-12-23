<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * image upload path
 * @author mabo
 */
$config['img_upload_path'] = 'images/';
$config['img_upload_path_in_url'] = 'images/';
/*
 * static file path
 * @author mabo
 */
$config['static_file_path'] = '/static/';
$config['default_nickname'] = '用户';
/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy IP
| addresses from which CodeIgniter should trust the HTTP_X_FORWARDED_FOR
| header in order to properly identify the visitor's IP address.
| Comma-delimited, e.g. '10.0.1.200,10.0.1.201'
|
*/
$config['proxy_ips'] = '';

$config['admin_email'] = 'admin@stylshow.com';
$config['recommend_follow_word'] = '欢迎,请选择关注下面设计师,获得设计师的最新作品和优惠信息';
$config['recommend_follow_userids'] = array(2247);
$config['admin_userid'] = 2247;

/**
 * Workflow Related
 */


$config['json_filter_product_list'] = array("id", "title", "cover_image", "category", "price", "presale_price","view_count", "author", "author_facepic","author_nickname", "status", 'discount', 'keywords', 'm_url','is_commond', 'store', 'rank', 'chinese_name', 'store_name','description','position','flag_url','referer');
$config['json_filter_product_detail'] = array_merge($config['json_filter_product_list'], array("category_name","description", "save_time", "create_time", "cover_image", "album", "available_size", 'available_color','chinese_name', 'store', 'property', 'store_name', 'flag_url', 'cate3', 'price_id', 'discount_id', 'tmp_img', 'referer'));

$config['json_filter_collection_list'] = array("id", "title","description", "cover_image", "background_image", "author", "author_facepic","author_nickname","author_introduce","view_count","status");
$config['json_filter_collection_detail'] = array('id', 'author', 'title', 'description', "background_image", "cover_image", "last_update", "nickname", "introduce", "level", "facepic", "item_list", "subhead", "content_image", "recommond_image");

$config['json_filter_user_info_basic'] = array("userid", "username", "usertype", "facepic", "nickname","introduce", 'ry_token', 'following_num', 'follower_num', 'regtime','bp_lastlogin');
$config['json_filter_user_info'] = array_merge($config['json_filter_user_info_basic'],array( "name", "introduce","description", "level", "city", "age", "gender", 'cover','isblocked', 'is_self', 'is_followed', 'tags','role'));
$config['json_filter_designer_top'] = array("userid", "username", "facepic", "nickname","introduce", 'following_num', 'follower_num', 'product_count', 'tags', 'top_three','cover');
$config['json_filter_user_and_followers'] = array("userid", "follow_userid", "nickname", "usertype", "facepic", "level", "follower_num", "following_num");
$config['json_filter_coupon_detail'] = array('id','name','type','value','limit','description','use_at','use_end','store','category');

$config['payment'] = array('ali' => "zhifubao", 'weixin' => "weixin");
$config['weixin']['appid'] = 'wx29786cbfe2199e6f';
$config['weixin']['key'] = 'youhotHHBJKJYXGS1234567890gyg123';
$config['weixin']['partnerid'] = '1453695502';



$config['verify_code_lifetime'] = 10 * 60;
$config['status_code'] = array(

    'OK' => 200, //    201	 'Created'=>201,
//    202	 'Accepted'=>202,
//    203	 'Non-Authoritative Information'=>203,
//    204	 'No Content'=>204,
//    205	 'Reset Content'=>
//    206	 'Partial Content',=>
//    300	=> 'Multiple Choices',=>
//    301	=> 'Moved Permanently',=>
//    302	=> 'Found',=>
//    304	=> 'Not Modified',=>
//    305	=> 'Use Proxy',=>
//    307	=> 'Temporary Redirect',=>
//    400	=> 'Bad Request',=>
    'unauthorized' => 401,
    'forbidden' => 403,
//    404	=> 'Not Found',=>
//    405	=> 'Method Not Allowed',=>
//    406	 'Not Acceptable',=>
//    407	 'Proxy Authentication Required',=>
//    408	 'Request Timeout',=>
//    409	 'Conflict',=>
//    410	 'Gone',=>
//    411	 'Length Required',=>
//    412	 'Precondition Failed',=>
//    413	 'Request Entity Too Large',=>
//    414	 'Request-URI Too Long',
//    415	 'Unsupported Media Type',
//    416	 'Requested Range Not Satisfiable',
//    417	 'Expectation Failed',
//    500	 'Internal Server Error',
//    501	 'Not Implemented',
//    502	 'Bad Gateway',
//    503	 'Service Unavailable',
//    504	 'Gateway Timeout',
//    505	 'HTTP Version Not Supported'
);
/**
 * url config
 */
$config['url'] = array('bp_signup' => "/user/signup",
    'admin' => "/admin"
, 'admin_category' => '/admin/category'
, 'admin_collection' => '/admin/collection'
, 'admin_product' => '/admin/product'
, 'admin_role' => '/admin/role'
, 'admin_stat' => '/admin/stat'
//, 'admin_category' => '/admin/category'
, 'admin_category' => '/admin/category/getList'
, 'admin_banner' => '/admin/banner'
, 'admin_user' => '/admin/user'
, 'admin_message' => '/admin/message'
, 'admin_order' => '/admin/order'
, 'admin_user' => '/admin/user'
, 'admin_applicant' => '/admin/applicant'
, 'color_add' => '/admin/product/color/add'
, 'open_app' => '/home/app'
, 'admin_size' => '/admin/size' // by fisher 2017-03-15
, 'admin_search' => '/admin/search' // by fisher 2017-05-25
, 'admin_store' => '/admin/store' // by fisher 2017-06-01
, 'admin_commond' => '/admin/commond' // by fisher 2017-06-06
, 'admin_ct1' => '/admin/collection/ct1_list' // by fisher 2017-06-14
, 'admin_ct3' => '/admin/collection/ct3_list' // by fisher 2017-06-14
, 'return_back' => '/admin/returnback'
, 'admin_ctl' => '/admin/collection/ctl_list' // by fisher 2017-06-14
, 'admin_coupon' => '/admin/coupon'
, 'admin_history' => '/admin/history'
, 'admin_referer' => '/admin/referer'
);

$config['default_facepic'] = 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/avatar/defaultface.png';


/* End of file config.php */
/* Location: ./application/config/config.php */
