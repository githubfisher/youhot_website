<?php


// cvn2加密 1：加密 0:不加密
$config['SDK_CVN2_ENC'] = 0;
// 有效期加密 1:加密 0:不加密
$config['SDK_DATE_ENC'] = 0;
// 卡号加密 1：加密 0:不加密
$config['SDK_PAN_ENC'] = 0;
 

$config['merId'] = '898111482990163' ; //商户代码 
// 签名证书路径
$config['SDK_SIGN_CERT_PATH'] = APPPATH.'libraries/unionpay/cert/0163.pfx';//快捷支付
// 签名证书密码
$config['SDK_SIGN_CERT_PWD'] = '120406';


// $config['merId'] = '898111482990161' ; //商户代码 
// // 签名证书路径
// $config['SDK_SIGN_CERT_PATH'] = APPPATH.'libraries/unionpay/cert/0161.pfx';
// // 签名证书密码
// $config['SDK_SIGN_CERT_PWD'] = '120406';



// 前台通知地址 (商户自行配置通知地址)
$config['SDK_FRONT_NOTIFY_URL'] = 'http://www.aifudao.com/accounts/unionpay_return';
// 后台通知地址 (商户自行配置通知地址)
$config['SDK_BACK_NOTIFY_URL'] = 'http://www.aifudao.com/accounts/unionpay_notify';


// /*测试*/
// $config['merId'] = '777290058113559' ; //商户代码 
// $config['SDK_SIGN_CERT_PATH'] = APPPATH.'libraries/unionpay/cert/700000000000001_acp.pfx';
// $config['SDK_SIGN_CERT_PWD'] = '000000';

// // 前台通知地址 (商户自行配置通知地址)
// $config['SDK_FRONT_NOTIFY_URL'] = 'http://apollo.aifudao.com:8070/accounts/unionpay_return';
// // 后台通知地址 (商户自行配置通知地址)
// $config['SDK_BACK_NOTIFY_URL'] = 'http://apollo.aifudao.com:8070/accounts/unionpay_notify';


// // 密码加密证书（这条用不到的请随便配）
$config['SDK_ENCRYPT_CERT_PATH'] = 'libraries/unionpay/cert/verify_sign_acp.cer';

// // 验签证书路径（请配到文件夹，不要配到具体文件）
$config['SDK_VERIFY_CERT_DIR'] =APPPATH.'libraries/unionpay/cert/';

// // 前台请求地址
// $config['SDK_FRONT_TRANS_URL'] = 'https://101.231.204.80:5000/gateway/api/frontTransReq.do';
// // 后台请求地址
// $config['SDK_BACK_TRANS_URL'] = 'https://101.231.204.80:5000/gateway/api/backTransReq.do';
// // 批量交易
// $config['SDK_BATCH_TRANS_URL'] = 'https://101.231.204.80:5000/gateway/api/batchTrans.do';
// //单笔查询请求地址
// $config['SDK_SINGLE_QUERY_URL'] = 'https://101.231.204.80:5000/gateway/api/queryTrans.do';
// //文件传输请求地址
// $config['SDK_FILE_QUERY_URL'] = 'https://101.231.204.80:9080/';
// //有卡交易地址
// $config['SDK_Card_Request_Url'] = 'https://101.231.204.80:5000/gateway/api/cardTransReq.do';
// //App交易地址
// $config['SDK_App_Request_Url'] = 'https://101.231.204.80:5000/gateway/api/appTransReq.do';

// 前台请求地址
$config['SDK_FRONT_TRANS_URL'] =  'https://gateway.95516.com/gateway/api/frontTransReq.do';
// 后台请求地址
$config['SDK_BACK_TRANS_URL'] = 'https://gateway.95516.com/gateway/api/backTransReq.do';
// 批量交易
$config['SDK_BATCH_TRANS_URL'] = 'https://gateway.95516.com/gateway/api/batchTrans.do';
//单笔查询请求地址
$config['SDK_SINGLE_QUERY_URL'] = 'https://gateway.95516.com/gateway/api/queryTrans.do';
//文件传输请求地址
$config['SDK_FILE_QUERY_URL'] ='https://filedownload.95516.com/';
//有卡交易地址
$config['SDK_Card_Request_Url'] = 'https://gateway.95516.com/gateway/api/cardTransReq.do';
//App交易地址
$config['SDK_App_Request_Url'] = 'https://gateway.95516.com/gateway/api/appTransReq.do';



// //文件下载目录 
// $config['SDK_FILE_DOWN_PATH'] = APPPATH.'/images/';

// //日志 目录 
// $config['SDK_LOG_FILE_PATH'] = APPPATH.'/logs/';

// //日志级别
// $config['SDK_LOG_LEVEL'] = 'INFO';


	
?>