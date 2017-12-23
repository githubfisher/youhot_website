<?php


/**
 * @Description: 银联支付
 * @version 1.0 对应银联支付5.
 * @author conzi
 */

//phplog
require_once __DIR__.'/func/log.class.php';

class Unionpay{
	var $conf = false;
	public static   $log = null;
	//日志级别
	const SDK_LOG_LEVEL = 'INFO';

	public function __construct ( $config = array() ) {	
		$this->conf = $config;
	}

	function Unionpay($conf = array()){
		$this->__construct($conf);
	}

	static function log($info){
		if(self::$log == null){
			self::$log =  new PhpLog ( APPPATH.'/logs/unionpay/', "PRC", self::SDK_LOG_LEVEL ); 
		}
		self::$log->LogInfo($info);
	}



	function getDealData($orderId, $amount , $info , $channel='pc'){
		$params = array(
			'version' => '5.0.0',				//版本号
			'encoding' => 'utf-8',				//编码方式
			'certId' => $this->getSignCertId (),			//证书ID
			'txnType' => '01',				//交易类型	
			'txnSubType' => '01',				//交易子类
			'bizType' => '000201',				//业务类型:B2C网关
			'frontUrl' =>  $this->conf['SDK_FRONT_NOTIFY_URL'],  		//前台通知地址
			'backUrl' => $this->conf['SDK_BACK_NOTIFY_URL'],		//后台通知地址	
			'signMethod' => '01',		//签名方法
			'channelType' => ($channel == 'pc'?'07': '08'),		//渠道类型，07-PC，08-手机
			'accessType' => '0',		//接入类型
			'merId' => $this->conf['merId'],		        //商户代码，请改自己的测试商户号
			'orderId' =>$orderId,	//商户订单号，不能含有-或_
			'txnTime' => date('YmdHis'),	//订单发送时间
			'txnAmt' => $amount,		//交易金额，单位分
			'currencyCode' => '156',	//交易币种
			'defaultPayType' => '0001',	//默认支付方式	
			//'orderDesc' => '订单描述',  //订单描述，网关支付和wap支付暂时不起作用
			'reqReserved' =>$info , //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
		);
		return $params;

	}
	



/** == common.php == */

/**
 * 数组 排序后转化为字体串
 *
 * @param array $params        	
 * @return string
 */
static function coverParamsToString($params) {
	$sign_str = '';
	// 排序
	ksort ( $params );
	foreach ( $params as $key => $val ) {
		if ($key == 'signature') {
			continue;
		}
		$sign_str .= sprintf ( "%s=%s&", $key, $val );
		// $sign_str .= $key . '=' . $val . '&';
	}
	return substr ( $sign_str, 0, strlen ( $sign_str ) - 1 );
}

/**
 * 字符串转换为 数组
 *
 * @param unknown_type $str        	
 * @return multitype:unknown
 */
static function coverStringToArray($str) {
	$result = array ();

	if (! empty ( $str )) {
		$temp = preg_split ( '/&/', $str );
		if (! empty ( $temp )) {
			foreach ( $temp as $key => $val ) {
				$arr = preg_split ( '/=/', $val, 2 );
				if (! empty ( $arr )) {
					$k = $arr ['0'];
					$v = $arr ['1'];
					$result [$k] = $v;
				}
			}
		}
	}
	return $result;
}
/**
 * 处理返回报文 解码客户信息 , 如果编码为utf-8 则转为utf-8
 *
 * @param unknown_type $params        	
 */
static function deal_params(&$params) {
	/**
	 * 解码 customerInfo
	 */
	if (! empty ( $params ['customerInfo'] )) {
		$params ['customerInfo'] = base64_decode ( $params ['customerInfo'] );
	}
	
	if (! empty ( $params ['encoding'] ) && strtoupper ( $params ['encoding'] ) == 'utf-8') {
		foreach ( $params as $key => $val ) {
			$params [$key] = iconv ( 'utf-8', 'UTF-8', $val );
		}
	}
}

/**
 * 压缩文件 对应java deflate
 *
 * @param unknown_type $params        	
 */
static function deflate_file(&$params) {
	foreach ( $_FILES as $file ) {
		self::log ( "---------处理文件---------" );
		if (file_exists ( $file ['tmp_name'] )) {
			$params ['fileName'] = $file ['name'];
			
			$file_content = file_get_contents ( $file ['tmp_name'] );
			$file_content_deflate = gzcompress ( $file_content );
			
			$params ['fileContent'] = base64_encode ( $file_content_deflate );
			self::log ( "压缩后文件内容为>" . base64_encode ( $file_content_deflate ) );
		} else {
			self::log ( ">>>>文件上传失败<<<<<" );
		}
	}
}

/**
 * 处理报文中的文件
 *
 * @param unknown_type $params        	
 */
static function deal_file($params) {
	
	if (isset ( $params ['fileContent'] )) {
		self::log ( "---------处理后台报文返回的文件---------" );
		$fileContent = $params ['fileContent'];
		
		if (empty ( $fileContent )) {
			self::log ( '文件内容为空' );
		} else {
			// 文件内容 解压缩
			$content = gzuncompress ( base64_decode ( $fileContent ) );
			$root = APPPATH.'/../images/upload/'; //SDK_FILE_DOWN_PATH
			$filePath = null;
			if (empty ( $params ['fileName'] )) {
				self::log ( "文件名为空" );
				$filePath = $root . $params ['merId'] . '_' . $params ['batchNo'] . '_' . $params ['txnTime'] . 'txt';
			} else {
				$filePath = $root . $params ['fileName'];
			}
			$handle = fopen ( $filePath, "w+" );
			if (! is_writable ( $filePath )) {
				self::log ( "文件:" . $filePath . "不可写，请检查！" );
			} else {
				file_put_contents ( $filePath, $content );
				self::log ( "文件位置 >:" . $filePath );
			}
			fclose ( $handle );
		}
	}
}

/**
 * 构造自动提交表单
 *
 * @param unknown_type $params        	
 * @param unknown_type $action        	
 * @return string
 */
 function create_html($params, $action) {
	$encodeType = isset ( $params ['encoding'] ) ? $params ['encoding'] : 'UTF-8';
	$html = <<<eot
	<html>
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset={$encodeType}" />
	</head>
	<body  onload="javascript:document.pay_form.submit();">
	    <form id="pay_form" name="pay_form" action="{$action}" method="post">
		
eot;
	foreach ( $params as $key => $value ) {
		$html .= "    <input type=\"hidden\" name=\"{$key}\" id=\"{$key}\" value=\"{$value}\" />\n";
	}
	$html .= <<<eot
    <input type="submit" type="hidden">
    </form>
</body>
</html>
eot;
	return $html;
}





/**
 * 签名
 *
 * @param String $params_str
 */
 function sign(&$params) {
	self::log ( '=====签名报文开始======' );
	if(isset($params['transTempUrl'])){
		unset($params['transTempUrl']);
	}
	// 转换成key=val&串
	$params_str = self::coverParamsToString ( $params );
	self::log ( "签名key=val&...串 >" . $params_str );
	
	$params_sha1x16 = sha1 ( $params_str, FALSE );
	self::log ( "摘要sha1x16 >" . $params_sha1x16 );
	// 签名证书路径
	$cert_path = $this->conf["SDK_SIGN_CERT_PATH"];
	$private_key = $this->getPrivateKey ( $cert_path );
	self::log ( "private_key len >  ". strlen($private_key));

	// 签名
	$sign_falg = openssl_sign ( $params_sha1x16, $signature, $private_key, OPENSSL_ALGO_SHA1 );
	if ($sign_falg) {
		$signature_base64 = base64_encode ( $signature );
		self::log ( "签名串为 >" . $signature_base64 );
		$params ['signature'] = $signature_base64;
	} else {
		self::log ( ">>>>>签名失败<<<<<<<" );
	}
	self::log ( '=====签名报文结束======' );
}

/**
 * 验签
 *
 * @param String $params_str        	
 * @param String $signature_str        	
 */
function verify($params) {
	// 公钥
	$public_key = $this->getPulbicKeyByCertId ( $params ['certId'] );	
//	echo $public_key.'<br/>';
	// 签名串
	$signature_str = $params ['signature'];
	unset ( $params ['signature'] );
	$params_str = self::coverParamsToString ( $params );
	self::log ( '报文去[signature] key=val&串>' . $params_str );
	$signature = base64_decode ( $signature_str );
//	echo date('Y-m-d',time());
	$params_sha1x16 = sha1 ( $params_str, FALSE );
	self::log ( '摘要shax16>' . $params_sha1x16 );	
	$isSuccess = openssl_verify ( $params_sha1x16, $signature,$public_key, OPENSSL_ALGO_SHA1 );
	self::log ( $isSuccess ? '验签成功' : '验签失败' );
	return $isSuccess;
}

/**
 * 根据证书ID 加载 证书
 *
 * @param unknown_type $certId        	
 * @return string NULL
 */
function getPulbicKeyByCertId($certId) {
	self::log ( '报文返回的证书ID>' . $certId );
	// 证书目录
	$cert_dir = ($this->conf['SDK_VERIFY_CERT_DIR']);
	self::log ( '验证签名证书目录 :>' . $cert_dir );
	$handle = opendir ( $cert_dir );
	if ($handle) {
		while ( $file = readdir ( $handle ) ) {
			clearstatcache ();
			$filePath = $cert_dir . '/' . $file;
			if (is_file ( $filePath )) {
				if (pathinfo ( $file, PATHINFO_EXTENSION ) == 'cer') {
					if ($this->getCertIdByCerPath ( $filePath ) == $certId) {
						closedir ( $handle );
						self::log ( '加载验签证书成功' );
						return $this->getPublicKey ( $filePath );
					}
				}
			}
		}
		self::log ( '没有找到证书ID为[' . $certId . ']的证书' );
	} else {
		self::log ( '证书目录 ' . $cert_dir . '不正确' );
	}
	closedir ( $handle );
	return null;
}

/**
 * 取证书ID(.pfx)
 *
 * @return unknown
 */
function getCertId($cert_path) {
	$pkcs12certdata = file_get_contents ( $cert_path );
	// self::log("pkcs12certdata:". $pkcs12certdata);
	// self::log("cert_path". $cert_path);
	// self::log("SDK_SIGN_CERT_PWD". $this->conf['SDK_SIGN_CERT_PWD'] );
	openssl_pkcs12_read ( $pkcs12certdata, $certs, $this->conf['SDK_SIGN_CERT_PWD'] );
	$x509data = $certs ['cert'];
	openssl_x509_read ( $x509data );
	$certdata = openssl_x509_parse ( $x509data );
	$cert_id = $certdata ['serialNumber'];
	return $cert_id;
}

/**
 * 取证书ID(.cer)
 *
 * @param unknown_type $cert_path        	
 */
function getCertIdByCerPath($cert_path) {
	$x509data = file_get_contents ( $cert_path );
	openssl_x509_read ( $x509data );
	$certdata = openssl_x509_parse ( $x509data );
	$cert_id = $certdata ['serialNumber'];
	return $cert_id;
}

/**
 * 签名证书ID
 *
 * @return unknown
 */
function getSignCertId() {
	// 签名证书路径
	
	return $this->getCertId ( $this->conf['SDK_SIGN_CERT_PATH'] );
}
function getEncryptCertId() {
	// 签名证书路径
	return $this->getCertIdByCerPath ( $this->conf['SDK_ENCRYPT_CERT_PATH'] );
}

/**
 * 取证书公钥 -验签
 *
 * @return string
 */
function getPublicKey($cert_path) {
	return file_get_contents ( $cert_path );
}
/**
 * 返回(签名)证书私钥 -
 *
 * @return unknown
 */
function getPrivateKey($cert_path) {
	$pkcs12 = file_get_contents ( $cert_path );
	openssl_pkcs12_read ( $pkcs12, $certs, $this->conf['SDK_SIGN_CERT_PWD'] );
	return $certs ['pkey'];
}

/**
 * 加密 卡号
 *
 * @param String $pan
 *        	卡号
 * @return String
 */
// function encryptPan($pan) {
// 	$cert_path = MPI_ENCRYPT_CERT_PATH;
// 	$public_key = $this->getPublicKey ( $cert_path );
	
// 	openssl_public_encrypt ( $pan, $cryptPan, $public_key );
// 	return base64_encode ( $cryptPan );
// }
/**
 * pin 加密
 *
 * @param unknown_type $pan        	
 * @param unknown_type $pwd        	
 * @return Ambigous <number, string>
 */
// function encryptPin($pan, $pwd) {
// 	$cert_path = $this->conf['SDK_ENCRYPT_CERT_PATH'];
// 	$public_key = $this->getPublicKey ( $cert_path );

// 	return self::EncryptedPin ( $pwd, $pan, $public_key );
// }


/**
 * cvn2 加密
 *
 * @param unknown_type $cvn2        	
 * @return unknown
 */
function encryptCvn2($cvn2) {
	$cert_path = $this->conf['SDK_ENCRYPT_CERT_PATH'];
	$public_key = getPublicKey ( $cert_path );
	
	openssl_public_encrypt ( $cvn2, $crypted, $public_key );
	
	return base64_encode ( $crypted );
}
/**
 * 加密 有效期
 *
 * @param unknown_type $certDate        	
 * @return unknown
 */
function encryptDate($certDate) {
	$cert_path = $this->conf['SDK_ENCRYPT_CERT_PATH'];
	$public_key = $this->getPublicKey ( $cert_path );
	
	openssl_public_encrypt ( $certDate, $crypted, $public_key );
	
	return base64_encode ( $crypted );
}

/**
 * 加密 数据
 *
 * @param unknown_type $certDatatype
 * @return unknown
 */
function encryptDateType($certDataType) {
	$cert_path = $this->conf['SDK_ENCRYPT_CERT_PATH'];
	$public_key = $this->getPublicKey ( $cert_path );

	openssl_public_encrypt ( $certDataType, $crypted, $public_key );

	return base64_encode ( $crypted );
}


/**
	 * Author: gu_yongkang 
	 * data: 20110510
	 * 密码转PIN 
	 * Enter description here ...
	 * @param $spin
	 */
	static function  Pin2PinBlock( &$sPin )
	{
	//	$sPin = "123456";
		$iTemp = 1;
		$sPinLen = strlen($sPin);
		$sBuf = array();
		//密码域大于10位
		$sBuf[0]=intval($sPinLen, 10);
	
		if($sPinLen % 2 ==0)
		{
			for ($i=0; $i<$sPinLen;)
			{
				$tBuf = substr($sPin, $i, 2);
				$sBuf[$iTemp] = intval($tBuf, 16);
				unset($tBuf);
				if ($i == ($sPinLen - 2))
				{
					if ($iTemp < 7)
					{
						$t = 0;
						for ($t=($iTemp+1); $t<8; $t++)
						{
							$sBuf[$t] = 0xff;
						}
					}
				}
				$iTemp++;
				$i = $i + 2;	//linshi
			}
		}
		else
		{
			for ($i=0; $i<$sPinLen;)
			{
				if ($i ==($sPinLen-1))
				{
					$mBuf = substr($sPin, $i, 1) . "f";
					$sBuf[$iTemp] = intval($mBuf, 16);
					unset($mBuf);
					if (($iTemp)<7)
					{
						$t = 0;
						for ($t=($iTemp+1); $t<8; $t++)
						{
							$sBuf[$t] = 0xff;
						}
					}
				}
				else 
				{
					$tBuf = substr($sPin, $i, 2);
					$sBuf[$iTemp] = intval($tBuf, 16);
					unset($tBuf);
				}
				$iTemp++;
				$i = $i + 2;
			}
		}
		return $sBuf;
	}
	/**
	 * Author: gu_yongkang
	 * data: 20110510
	 * Enter description here ...
	 * @param $sPan
	 */
	static function FormatPan(&$sPan) {
		$iPanLen = strlen($sPan);
		$iTemp = $iPanLen - 13;
		$sBuf = array();
		$sBuf[0] = 0x00;
		$sBuf[1] = 0x00;
		for ($i=2; $i<8; $i++)
		{
			$tBuf = substr($sPan, $iTemp, 2);
			$sBuf[$i] = intval($tBuf, 16);
			$iTemp = $iTemp + 2;		
		}
		return $sBuf;
	}
	
	static function Pin2PinBlockWithCardNO(&$sPin, &$sCardNO) {
		$sPinBuf = self::Pin2PinBlock($sPin);
		$iCardLen = strlen($sCardNO);

//		self::log("CardNO length : " . $iCardLen);
		if ($iCardLen <= 10)
		{
			return (1);
		}
		elseif ($iCardLen==11){
			$sCardNO = "00" . $sCardNO;
		}
		elseif ($iCardLen==12){
			$sCardNO = "0" . $sCardNO;
		}
		$sPanBuf = self::FormatPan($sCardNO);
		$sBuf = array();
		
		for ($i=0; $i<8; $i++)
		{
//			$sBuf[$i] = $sPinBuf[$i] ^ $sPanBuf[$i];	//十进制
//			$sBuf[$i] = vsprintf("%02X", ($sPinBuf[$i] ^ $sPanBuf[$i]));
			$sBuf[$i] = vsprintf("%c", ($sPinBuf[$i] ^ $sPanBuf[$i]));
		}
		unset($sPinBuf);
		unset($sPanBuf);
//		return $sBuf;
		$sOutput = implode("", $sBuf);	//数组转换为字符串
		return $sOutput;
	}


/*func/httpClient.php*/

	/**
	 * 后台交易 HttpClient通信
	 * @param unknown_type $params
	 * @param unknown_type $url
	 * @return mixed
	 */
	 static function sendHttpRequest($params, $url) {
		$opts = $self::getRequestParamString ( $params );
		
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false);//不验证证书
	    curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false);//不验证HOST
		curl_setopt ( $ch, CURLOPT_SSLVERSION, 3);
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'Content-type:application/x-www-form-urlencoded;charset=UTF-8' 
		) );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $opts );
		
		/**
		 * 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		 */
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		
		// 运行cURL，请求网页
		$html = curl_exec ( $ch );
		// close cURL resource, and free up system resources
		curl_close ( $ch );
		return $html;
	}


	/**
	 * 组装报文
	 *
	 * @param unknown_type $params        	
	 * @return string
	 */
	static function getRequestParamString($params) {
		$params_str = '';
		foreach ( $params as $key => $value ) {
			$params_str .= ($key . '=' . (!isset ( $value ) ? '' : urlencode( $value )) . '&');
		}
		return substr ( $params_str, 0, strlen ( $params_str ) - 1 );
	}



//=======
// static function EncryptedPin($sPin, $sCardNo ,$sPubKeyURL) {
// 		$fp = fopen($sPubKeyURL, "r");
// 		if ($fp != NULL)
// 		{
// 			$sCrt = fread($fp, 8192);
// 			fclose($fp);
// 		}
// 		$sPubCrt = openssl_x509_read($sCrt);
// 		if ($sPubCrt === FALSE)
// 		{
// 			print("openssl_x509_read in false!");
// 			return (-1);
// 		}
	
// 		$sPubKey = openssl_x509_parse($sPubCrt);
	
// 		$sInput = self::Pin2PinBlockWithCardNO($sPin, $sCardNo);
// 		if ($sInput == 1)
// 		{
// 			print("Pin2PinBlockWithCardNO Error ! : " . $sInput);
// 			return (1);
// 		}
// 		$iRet = openssl_public_encrypt($sInput, $sOutData, $sCrt, OPENSSL_PKCS1_PADDING);
// 		if ($iRet === TRUE)
// 		{
// 			$sBase64EncodeOutData = base64_encode($sOutData);		
// 			return $sBase64EncodeOutData;
// 		}
// 		else 
// 		{
// 			print("openssl_public_encrypt Error !");
// 			return (-1);
// 		}
// 	}



}