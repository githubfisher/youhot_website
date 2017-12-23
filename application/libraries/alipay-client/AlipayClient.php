<?php

require_once("alipay_function.php");

class AlipayClient {
    /**
     * 应答解析
     * @param respString 应答报文
     * @param resp 应答要素
     * @return 应答是否成功
     */
    static function verifyNotify($sign, $sign_type, $data, &$deal_id, &$money, &$deal_time, &$status) {
	 	if ($sign_type != "RSA" && $sign_type != "rsa") {
            //echo("debug:not RSA<br />");
	  		return FALSE;
	 	}

		$isVerify = verify("notify_data=".$data, $sign);
		if (!$isVerify) {
            //echo("debug:verify failed<br />");
		 	return FALSE;
		}
		else {
            //echo("debug:verify OK<br />");
		    $deal_id = (string)getDataForXML($data , '/notify/out_trade_no');
		    $money = (float)getDataForXML($data , '/notify/total_fee');
		    $deal_time = (string)getDataForXML($data , '/notify/gmt_payment');
		    $status = (string)getDataForXML($data , '/notify/trade_status');
			return TRUE;
		}
    }
}

?>
