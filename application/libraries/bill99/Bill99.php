<?php


/**
 * @Description: 快钱神州行支付网关接口封装
 * @version 2.0
 * @author conzi
 */

class Bill99{

	var $conf = false;
	public static $send_keys = array(
						'inputCharset',
						"bgUrl",						
						"version",
						"language",			
						"signType",			
						"merchantAcctId",

						'payerName',
						'payerContactType',
						'payerContact',
						'orderId',
						'orderAmount',
						
						'payType',
						'cardNumber',
						'cardPwd',
						'fullAmountFlag',
						'orderTime',

						'productName',
						'productNum',
						'productId',
						'productDesc',

						'ext1',
						'ext2',
						'bossType',
						'key',
				);

	public static $receive_keys =  array(
					'merchantAcctId',
					'version',
					'language',
					'payType',
					'cardNumber',
					'cardPwd',
					'orderId',
					'orderAmount',
					'dealId',
					'orderTime',
					'ext1',
					'ext2',
					'payAmount',
					'billOrderTime',
					'payResult',
					'signType',
					'bossType',
					'receiveBossType',
					'receiverAcctId',
					'key',
			);

	public function __construct ( $config = array() ) {	
		$this->conf = $config;

	}


	function Bill99($conf = array()){
		$this->__construct($conf);
	}


	function get_datas($bossType = 9){
		$data = array();
		foreach (self::$send_keys as $key) {
			if(array_key_exists($key , $this->conf)){
				$data[$key] = $this->conf[$key];
			}else{
				$data[$key] = '';
			}
		}
		if($bossType ==9){
			$bossType =0; //默认选择移动充值卡
		}
		if(array_key_exists($bossType, $this->conf['paytypes'])){
			$data['merchantAcctId'] = $this->conf['paytypes'][$bossType]['merchantAcctId'];
			$data['key'] = $this->conf['paytypes'][$bossType]['key'];
		}

		return $data;
	}

// 返回值校验
	 function check_sign($data, $sign){

		$bossType =$data['bossType']; //默认选择移动充值卡
		if(array_key_exists($bossType, $this->conf['paytypes'])){
			$data['key'] = $this->conf['paytypes'][$bossType]['key'];
			if($data['receiverAcctId'] != $this->conf['paytypes'][$bossType]['merchantAcctId']){
				return false;
			}
		}else{
			return false;
		}

		$str = array();

		foreach (self::$receive_keys as $key) {
			if($data[$key] != ''){
				$str[] = $key.'='.$data[$key];
			}
		}

		return strtoupper(md5(implode('&', $str))) == $sign;
	}

// 生成校验码
	static function sign($data){
		$str = array();
		foreach (self::$send_keys as $key) {
			if($data[$key] != ''){
				if($key !=='bgUrl'){
					$str[] = $key.'='.urlencode($data[$key]);
				}else{
					$str[] = $key.'='.$data[$key];
				}
			}
		}
		return strtoupper(md5(implode('&', $str)));
	}

	function get_receive_keys(){
		return self::$receive_keys;
	}
}
