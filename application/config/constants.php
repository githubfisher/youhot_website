<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/

define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');


/*
|--------------------------------------------------------------------------
| DataBase Table Names
|--------------------------------------------------------------------------
|
| 数据库表名汇总
|
*/

/**
 * StylShow Tables
 */
define('TBL_PRODUCT', 'product');
define('TBL_PRODUCT_TAG', 'product_tag');
define('TBL_PRODUCT_ALBUM', 'product_album');
define('TBL_PRODUCT_LIKER', 'product_liker');
define('TBL_PRODUCT_TAGS', 'product_tags'); // add by fisher
define('TBL_SIZE_CHART', 'size_chart'); // add by fisher_
define('TBL_PRODUCT_COLOR', 'product_color');
define('TBL_PRODUCT_SIZE', 'product_size');
define('TBL_PRODUCT_FREIGHT', 'product_freight');
define('TBL_COLLECTION', 'collection');
define('TBL_COLLECTION_ITEM', 'collection_item');
define('TBL_TAGS', 'tags');
define('TBL_USER_FOLLOW_USER', 'user_fo_user');
define('TBL_USER_FOLLOW_TAG', 'user_fo_tag');
define('TBL_USER_LOVE_PRODUCT', 'user_love_product');
define('TBL_CATEGORY', 'category');
define('TBL_COLOR', 'color');
define('TBL_SIZE', 'size');
define('TBL_SHIP_INFO', 'ship_info');
define('TBL_ADMIN_ACTION_RECORD', 'admin_action_record');
define('TBL_APPLICANT', 'designer_applicant');
define('TBL_COMMENTS', 'comments');
define('TBL_PROFILER', 'profiler');

define('TBL_USER', 'user');
define('TBL_USER_ROLE', 'user_role');
define('TBL_USER_REP_USER', 'user_rep_user');

define('TBL_USER_OAUTH', 'oauth_map');

define('TBL_USER_GROUP', 'user_group');
define('TBL_USER_GROUP_INFO', 'user_group_info');
define('TBL_OAUTH_RELATION', 'oauth_relation_update');
define('TBL_ADMIN_USER_INFO', 'admin_user_info');
define('TBL_DESIGNER_TOP', 'designer_top');
define('TBL_TASK_TIME', 'task_time');
define('TBL_LIVECAST', 'livecast');

//mongodb evn
define('MONGO_DB_PRODUCT', 'stat_product');
define('MONGO_DB_COLLECTION', 'stat_col');
define('MONGO_COL_PRODUCT_PREFIX', 'prod_');
define('MONGO_COL_COLL_PREFIX', 'coll_');

//product status
define('PRODUCT_STATUS_DRAFT',0);
define('PRODUCT_STATUS_INAUDIT',10);
define('PRODUCT_STATUS_AUDITREFUSE',12);
define('PRODUCT_STATUS_PUBLISHED',1);  //上架
define('PRODUCT_STATUS_OFFSHELF',3); //下架
define('PRODUCT_STATUS_DELETED',-1);

define('PRODUCT_STATUS_APPLY_EDIT',20);

//define('PRODUCT_STATUS_UP_TO_STANDARD',2);
define('PRODUCT_STATUS_PRESALE_END',4);
define('PRODUCT_STATUS_PRESALE_GOINON',3);
define('PRODUCT_STATUS_PRESALE_NOT_START',5);

//COLLECTION status
define('COLLECTION_STATUS_DRAFT',0);
define('COLLECTION_STATUS_INAUDIT',10);
define('COLLECTION_STATUS_AUDITREFUSE',12);
define('COLLECTION_STATUS_PUBLISHED',1);
define('COLLECTION_STATUS_OFFSHELF',3); //下架
define('COLLECTION_STATUS_DELETED',-1);

//专辑审核编辑状态
define('COLLECTION_STATUS_APPLY_EDIT',20);

define('SELL_TYPE_PRE_SALE',1);
define('SELL_TYPE_SALE',2);

define('PAY_TYPE_PREPAY',0);
define('PAY_TYPE_LASTPAY',1);



//album
define('ALBUM_RESOURCE_TYPE_IMAGE',1);
define('ALBUM_RESOURCE_TYPE_VIDEO',2);
define('ALBUM_RESOURCE_TYPE_TEXT',3);
define('ALBUM_RESOURCE_TYPE_COVER_IMAGE',11);
define('ALBUM_RESOURCE_TYPE_COVER_TEXT',31);

//userinfo view permission
define('USERINFO_VIEW_PERMISSION_NORMAL',10);
define('USERINFO_VIEW_PERMISSION_MYSELF',20);
define('USERINFO_VIEW_PERMISSION_ADMIN',30);

//order status
define('ORDER_STATUS_DELETE',-1);
define('ORDER_STATUS_INIT',0);   //订单初始

define('ORDER_STATUS_PRE_PAID',10);  //预付完成  等等成团1

define('ORDER_STATUS_LAST_PAY_START',20);  //开始付尾款   待付尾款2
//define('ORDER_STATUS_LAST_PAY_END',21);//结束付尾款
define('ORDER_STATUS_LAST_PAID',24);//付尾款完成,生产中,3

define('ORDER_STATUS_SHIP_START',30);//已发货,待收货4
define('ORDER_STATUS_SHIP_RECEIVED',31);  //已收货  ,  交易成功
define('ORDER_STATUS_END_SUCCEED',31);  //成功完成   //暂时没用,==receive
define('ORDER_STATUS_END_FAIL',41);//失败   成团失败
define('ORDER_STATUS_CANCEL_UNPAY',25); //未付款 取消订单
define('ORDER_STATUS_CANCEL_PAY',26); //已付款 取消订单

//60 全部
//50 全部-完成(成功,失败)



//ship info default status
define('SHIP_INFO_IS_DEFAULT',1);
define('SHIP_INFO_IS_NOT_DEFAULT',0);


/* aifudao表 */


define('TBL_FACE', 'face');
define('TBL_BP_SESSION', 'fd_session');
define('TBL_LOCATION', 'location');
define('TBL_LOCATION_ZCODE', 'location_zcode');
define('TBL_MOBILE_MSG', 'mobile_msg');
define('TBL_MOBILE_MSG_RECEIVED', 'mobile_msg_received');
define('TBL_MESSAGE', 'message');
define('TBL_FP_SESSION', 'session');
define('TBL_SPARETIME', 'sparetime');
define('TBL_STUDENT', 'student');

define('TBL_PARENT', 'parent');
define('TBL_SUBJECT', 'subject');

define('TBL_DEVICE_USER', 'device_user');
define('TBL_ACTIVITY', 'activity');
define('TBL_CAPTCHA', 'captcha');


define('TBL_DEVICE', 'device');
define('TBL_USER_DEVICE_INFO', 'user_device_info');
define('TBL_USER_ALIPAY', 'user_alipay');
define('TBL_DESIGNER_ADDRESS', 'designer_address');

define('TBL_PAY_INFO', 'pay_info');
define('TBL_USER_CHECK_LIST', 'user_check_list');
/* CDB表 */
define('TBL_CDB_ACCOUNT', 'account');
define('TBL_CDB_DEAL', 'deal');
define('TBL_CDB_CHARGE_CARD', 'charge_card');
define('TBL_CDB_VIP_CARD', 'vip_card');
define('TBL_CDB_ACCOUNT_CHANGE_RECORD', 'account_change_record');
define('TBL_CDB_DOUDOU_DEAL', 'doudou_deal');
define('TBL_CDB_USER_REPORT', 'user_month_report');
define('TBL_CDB_DEVICE_LIMIT', 'device_limit');
define('TBL_CDB_USER_LIMIT', 'user_limit');
define('TBL_CDB_PAY_RESULT_NOTIFY', 'pay_result_notify');
define('TBL_CDB_DOUDOU_CARD', 'doudou_card');
define('TBL_CDB_JIFEN_DEAL', 'jifen_deal');

define('TBL_EARN_DOUDOU_RECORD', 'earn_doudou_record');

/*shells*/
define('TBL_SHELLS_TO_RUN', 'shells_to_run');

define('TBL_ACTIVATE', 'activate');

/* server ip settings for fudao */
define('TBL_SERVER', 'server');

/*
|--------------------------------------------------------------------------
| URI LIST
|--------------------------------------------------------------------------
|
| 各种URL及URL字段名列表 TODO:清理
|
*/

define('ADMIN_TYPE_IS_CONSULTANT', '1');
define('ADMIN_TYPE_IS_HEADTEACHER', '2');


/*通用参数*/

define('RETURN_TYPE', 'rt');
define('bp_result_field', 'res');
define('bp_result_hint_field', 'hint');


//统一的erro页面
define('BP_ERROR_TPL_NOT_LOGIN', 'error/notlogin');
define('BP_ERROR_TPL_DB_NOT_FIND', 'error/dbnotfind');



/*
|--------------------------------------------------------------------------
| SYSTEM STATUS/TYPE LIST
|--------------------------------------------------------------------------
|
| 系统中各种状态/类型变量定义
|
*/

/*用户相关*/


define('USERTYPE_USER', 0);
define('USERTYPE_DESIGNER', 2);
define('USERTYPE_BUYER', 3);
//define('USERTYPE_ASSISTANT', 3);
define('USERTYPE_ADMIN', 9);


//两用户之间的状态判断 {非登录用户：0，登录用户：1，有关系：2，自己或者是管理员:3,有预约:4}
define('PERMISSION_NOT_LOGIN', 0);
define('PERMISSION_LOGIN', 1);
define('PERMISSION_RELATED', 2);
define('PERMISSION_MYSELF', 3);
define('PERMISSION_APPOINTED', 4);


//roles, 按位计算
define('ADMIN_ROLE_ENTRANCE', 1);   //管理入口(管理员,设计师,助理)
define('ADMIN_ROLE_USER', 2);    //用户管理(管理员,设计师)
define('ADMIN_ROLE_FINANCE', 4);   //系统财务(管理员)
define('ADMIN_ROLE_STAT', 8);  //系统统计(管理员)
define('ADMIN_ROLE_ORDER', 16);     //订单跟踪(管理员)
define('ADMIN_ROLE_PRODUCT', 32);   //商品管理统计(管理员,设计师,设计师助理)
define('ADMIN_ROLE_COLLECTION', 64);//专题管理,统计(管理员,设计师,设计师助理)
define('ADMIN_ROLE_COUPON', 128);//优惠券  (管理员)
define('ADMIN_ROLE_USER_PRIVILEGE', 256);//用户权限管理  (管理员)
define('ADMIN_ROLE_APPLICANT', 512);//认证管理  (管理员)
define('ADMIN_ROLE_ALL', 65535);

/* 消息短信相关 */
//message status
define('MESSAGE_STATUS_UNSEND', 0); //未发送
define('MESSAGE_STATUS_SENDED', 1); //已发送
define('MESSAGE_SCAN_MIN_SPAN', 300); //消息扫描间隔，默认为5分钟(５*６０)

//mms，消息发送状态表示
define ('mms_need_send', 0);
define ('mms_submit_ok', 1);
define ('mms_send_ok', 2);
define ('mms_submit_fail', 3);
define ('mms_error_phone_number_error', 100);

//follow status
define ('FOLLOW_STATUS_NOT_FOLLOW', 0);
define ('FOLLOW_STATUS_FOLLOWED', 1);
define ('FOLLOW_STATUS_MUTUAL', 2);


//pay type
define('PAYTYPE_OUT', 0);
define('PAYTYPE_IN', 1);
define('PAYTYPE_PAID', 2);//预付过的，在系统留条记录而已

//doudou pay type
define('PAYTYPE_DOUDOU_MINUS', 2);
define('PAYTYPE_DOUDOU_ADD', 3);

//coupon状态
define('CHARGE_CARD_STATUS_NORMAL', 0);//未使用
define('CHARGE_CARD_STATUS_USED', 1);//已使用
define('CHARGE_CARD_STATUS_EXPIRED', 2);//已过期


//充值卡类型
define('CHARGE_CARD_TYPE_ACTIVITY', 0);//活动赠送
define('CHARGE_CARD_TYPE_ADMIN', 1);//管理员发放
define('CHARGE_CARD_TYPE_USERBUY', 2);//用户购买


//deal status { 0:未完成，1：完成支付，200：老师辅导费预付}
define('DEAL_STATUS_COMPLETED', 1);
define('DEAL_STATUS_UNCOMPLETED', 0);
define('DEAL_STATUS_TEAPAY_IN_ADVANCE', 200); //预付
define('DEAL_STATUS_TEAPAY_TO_CHECK', 201); //预付，不入帐，由管理员手动支付

// pay from type
define('PAY_FROM_TENPAY', 10);
define('PAY_FROM_ALI', 20);
define('PAY_FROM_99BILL', 30);
define('PAY_FROM_ALI_IN_CLIENT', 40);
define('PAY_FROM_UPMP_IN_CLIENT', 50);
define('PAY_FROM_UNIONPAY', 60);

define('PAY_FROM_CHARGE_CARD', 100);
define('PAY_FROM_DOUDOU_CHARGE', 101); //豆豆兑换
define('PAY_FROM_VIP_CHARGE', 102); //VIP充值
define('PAY_FROM_COOPERATION_DOUDOU_PAY', 103); //合作方购买豆豆
define('PAY_FROM_COOPERATION_FREE_DOUDOU', 104); //合作方免费豆豆

define('PAY_FROM_BUYED_CHARGE_CARD', 108); //用户购买的充值卡
define('PAY_FROM_ACTION_CHARGE_CARD', 119); //活动赠送充值卡
define('PAY_FROM_BUY_CHARGE_CARD', 150); //代理商购买充值卡

define('PAY_FROM_ADMIN', 110);//管理员操作

define('PAY_BACK_FROM_ADMIN', 120);//退费到用户银行卡
define('PAY_FROM_ADMIN_FOR_USER_CHARGE', 121);//用户充值到银行卡
define('PAY_FROM_ADMIN_FOR_USER_MONEY_RETURN', 122);//辅导退费到用户帐户
define('PAY_FROM_ADMIN_FOR_TEACHER_MONEY_RETURN', 123);//老师费用结算,退款给老师

define('PAY_FROM_FUDAO', 200);//正常辅导产生费用
define('PAY_FROM_FUDAO_COMMENT', 201); //老师辅导评价
define('PAY_FROM_DOUDOU_TO_MONEY', 202); //豆豆转余额，管理员操作触发
define('PAY_FROM_VIP_DAYI', 203); //VIP答疑


define('PAY_FROM_CLASS', 210);//课堂付费
define('PAY_FROM_ANSWER', 220); //回答问题
define('PAY_FROM_ANSWER_QUESTION', 221); //回答问题,新
define('PAY_BACK_FROM_CLASS', 230);//课堂退费
define('PAY_BACK_FROM_AUDITOR', 240);//审核不通过，扣除豆豆
define('PAY_BACK_FROM_UNRESOLVED_QUESTION', 241);//问题未解决，扣除豆豆
define('PAY_FROM_AUDITOR', 250);//对审核人员的奖励

define('PAY_FROM_MONTH_RETURN', 300); //每月返还，主要用于免费时长，暂已不用
define('PAY_FROM_MONTH_EXPIRED', 304); //卡过期
define('PAY_FROM_FREE_RETURN', 301); //免费赠送
define('PAY_FROM_USER_ACTIVE', 302); //用户活动，比如注册等
define('PAY_FROM_USER_DAILY_ACTIVE', 303); //用户日常活动，比如每天领豆豆
define('PAY_FROM_SCAN_2D_CODE', 304); //用户扫描二维码活动赠送豆豆
define('PAY_FROM_SHARE_2D_CODE', 305); //用户分享的二维码被扫描获得赠送豆豆
define('PAY_FROM_TEACHER_LUCKY_EGG', 306); //老师答疑辅导彩蛋中奖获赠豆豆

define('PAY_FROM_USER_COMPLAINT', 401); //豆豆转余额，管理员操作触发

define('PAY_FROM_DOUDOU_BUYING', 501); //购买豆豆付出
define('PAY_FROM_DOUDOU_CHARGING', 502); //豆豆充值获得豆豆

define('PAY_FROM_UPGRADE_BUSINESS_LEVEL', 510); //business账号升级
define('PAY_FROM_BUSINESS_TIMECHANGE', 512); //business兑换时长
define('PAY_FROM_BUSINESS_TIMEUSING', 513); //business时长使用
define('PAY_FROM_BUSINESS_TIMEMONTYLYGIFT', 514); //business每月赠送时长

// pay related_id
define('PAY_RELATED_TYPE_BANK_CHARGE', 1);//用户银行充值
define('PAY_RELATED_TYPE_USER_MONEY_BACK', 2);//用户申请退款
define('PAY_RELATED_TYPE_USER_MONEY_RETURN', 3);//用户申请退钱回帐户
define('PAY_RELATED_TYPE_TEACHER_MONEY_RETURN', 4);//老师费用结算,退款给老师

//来源地址 1:IOS端  2:android  3:web  4:分享
define('REFERER_IOS', 1);//ios
define('REFERER_ANDROID', 2);//android
define('REFERER_WEB', 3);//网页端
define('REFERER_SHARE', 4);//分享

//来源类型1:首页   2:专辑     3:用户 4:推荐
define('REFERER_TYPE_INDEX', 1);
define('REFERER_TYPE_COLLECTION', 2);
define('REFERER_TYPE_USER', 3);
define('REFERER_TYPE_RECOMM', 4);


define('PROPROTION', 0.05);
/* 错误码大全 */

/* mysql code */
define('DB_INTERNAL_OK', 0);
define('DB_INTERNAL_ERROR', 1002);
define('DB_OPERATION_OK', 0);
define('DB_OPERATION_FAIL', 1002);
define('DB_DATA_INVALID', 1003);
define('DB_OPERATION_USER_FORBIDDEN', 1004);
define('DB_OPERATION_DATA_EXIST', 1005);

/* operation result definition */
define('bp_operation_ok', 0);
define('bp_operation_fail', 4);
define('BP_OPERATION_LIST_EMPTY', 60);
define('bp_operation_user_notlogin', 10);
define('bp_operation_user_forbidden', 11);
define('bp_operation_usertype_not_match', 12);

define('bp_operation_db_not_find', 100);
define('bp_operation_db_got_fail', 101);
define('bp_operation_db_data_invalid', 102);

define('bp_operation_invalid_pass_un', 20);
define('bp_operation_invalid_password', 21);
define('bp_operation_invalid_username', 22);
define('bp_operation_invalid_data', 23);
define('bp_operation_user_balance_not_enough', 24);

define('bp_operation_verify_fail', 30);
define('bp_operation_version_not_match', 32);
define('bp_operation_data_size_out_of_limit', 31);

define('bp_operation_data_out_of_total_limit', 33);
define('bp_operation_data_out_of_item_limit', 34);
define('bp_operation_data_out_of_user_total_limit', 35);
define('bp_operation_data_out_of_user_item_limit', 36);
define('bp_operation_user_alipay_exist', 37);

define('bp_operation_data_expired', 40);
define('bp_operation_data_used', 41);
define('BP_COUPON_BELONG_TO_OTHER', 49);
define('BP_COUPON_BELONG_TO_YOU',48);
define('bp_operation_need_check', 42);//需要确认
define('bp_socket_connect_error', 43);//socket
define('bp_socket_server_refuse', 44);//socket 拒绝
define('bp_operation_data_need_validate', 50);

define('bp_operation_unknown_error', 1024);


/* 验证码参数 */
define('CAPTCHA_TYPE_MOBILE', 0);//默认，手机绑定时使用的验证码
define('CAPTCHA_TYPE_CARD_CHARGE', 1);//用户coupon使用验证码
define('CAPTCHA_TYPE_LOGIN', 2);//用户登录使用验证码

/* ！静态文件参数 ！重要，经常修改*/

define('STATIC_VERSION', '6.7.18');
define('STATIC_ADMIN_VERSION', '15.01.24');

define('APPID', 'wx0754694bbf52b837');
define('APPSECRET', '3ba0620406ae83334e83aefa621a1f54');
define('TOKEN', 'youhot');
/* End of file constants.php */
/* Location: ./application/config/constants.php */

define('COUPON_TYPE_NEWER', 5);
