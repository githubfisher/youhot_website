<?php
/* *
 *  快钱的配置文件
 */

$config['paytypes']= array(
	0=>array(
	//神州行网关账户号
		'merchantAcctId' => '1002270241902',
	//神州行网关密钥
		'key' => 'HYWDRTTNWYU96ACD',
	),
	// 联通卡
	1=>array(
		'merchantAcctId' => '1002270241903',
		'key' => 'QB8W3ER3BACEYH44',
	),	
	// 电信卡
	2=>array(
		'merchantAcctId' => '1002270241904',
		'key' => 'S3Y6FE7CMZUWT3IN',
	),	
	// 骏网一卡通
	3=>array(
		'merchantAcctId' => '1002270241905',
		'key' => '3F4Z9GQYUKS3JM2D',
	),
	// 盛大一卡通
	10=>array(
		'merchantAcctId' => '1002270241906',
		'key' => '4MCFZRWCJHAIHMUW',
	),
	// 征途卡
	11=>array(
		'merchantAcctId' => '1002270241907',
		'key' => '3K9RL8TJJQ3SSK6S',
	),
	// 完美游戏卡
	12=>array(
		'merchantAcctId' => '1002270241908',
		'key' => 'IX5BTM9DTUQJ4M5N',
	),
	// 搜狐游戏卡
	13=>array(
		'merchantAcctId' => '1002270241909',
		'key' => '2X4HNEAU8IM6SHCD',
	),
	// 网易游戏卡
	14=>array(
		'merchantAcctId' => '1002270241910',
		'key' => 'CCQESE3LMN8S2ETX',
	),
	// 纵游游戏卡
	15 =>array(
		'merchantAcctId' => '1002270241911',
		'key' => 'KIJIURZDM4WUDUKU',
	),
	// 网易游戏卡
	14=>array(
		'merchantAcctId' => '1002270241910',
		'key' => 'CCQESE3LMN8S2ETX',
	),

);

//字符集.固定选择值。可为空。
///只能选择1、2、3、5
///1代表UTF-8; 2代表GBK; 3代表gb2312; 5 代表big5
///默认值为1
$config['inputCharset']="1";

$config['bgUrl'] = 'http://aifudao.com/accounts/bill99_return';
// 网关版本，固定为2.1
$config['version'] ='v2.0';
//语言种类.固定选择值。
///只能选择1、2、3
///1代表中文；2代表英文
///默认值为1
$config['language'] = '1';
//签名类型.固定值
///1代表MD5签名
///当前版本固定为1
$config['signType'] ="1";
// 与paytypes中的id对应，9为全部
$config['bossType'] ="9";

//支付方式.固定选择值
///可选择00、41、42、52
///00 代表快钱默认支付方式，目前为神州行卡密支付和快钱账户支付；41 代表快钱账户>支付；42 代表神州行卡密支付和快钱账户支付；52 代表神州行卡密支付
$config['payType'] ="42";

// 全额支付标志
// 0 代表非全额支付方式,支付完成后返回订单金额为 商 户提交的订单金额。如果充值卡卡面额小于订单金 额 时,返回支付结果为失败;充值卡面额大于或等于订单 金额时,返回支付结果为成功。
// 1 代表全额支付方式,支付完成后返回订单金额为用户 充值卡的面额。只要充值销卡成功,返回支付结果都为 成功。 如果商户定制卡密直连时,本参数固定值为 1
$config['fullAmountFlag'] ='1';

//支付人联系方式类型.固定选择值
///只能选择1
///1代表Email
$config['payerContactType'] ='1';

// 商户查询接口
$config['bill99_check_url'] ='https://www.99bill.com/szx_gateway/services/szxGatewayPayOrderQuery?wsdl';