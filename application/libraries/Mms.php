<?php
/**
 * 蝶信通平台接口
 *
 * @author tconzi@gmail.com
 * @version 1.0.0
 * @package
 */


class Mms {


	const host = 'http://114.215.130.61:8081/SendMT/';
	const usersms = 'zhiyuansms';
	const pwd = '123456';
	var $config ;

	public function __construct ($config = array() ) {
		$this->config = $config;
	}
	function Mms($config){
		$this->__construct($config);
	}


	// 查询短信余量
	// 返回值：短信条数，-1、帐号未注册；-2、其他错误；-3、密码错误
	public function get_balance(){
        //http://114.215.130.61:8081/SendMT/SearchStatus?username=*****&password=***
		$url = self::host.'SearchStatus?username='.self::usersms.'&password='.self::pwd;
		$result = get_file_via_curl($url);
		return $result;
	}

	// 查询短信余量
	// 返回值：返回格式：||手机号#上行内容#发送时间#扩展号||手机号#上行内容#发送时间#扩展号……
	public function receive(){
		$result = get_file_via_curl(self::host.'Get.aspx?CorpID='.self::usersms.'&Pwd='.self::pwd);

		if(!empty($result)){
			$res = explode('||', $result);
			$result = array();
			foreach ($res as $item) {
				if(!empty($item)){
					$result[] = explode('#', $item);
				}
			}
		}

		return $result;
	}


	/**
	 * 发送短信接口，支持300字以内的长短信
	 *
	 * @phone 手机号
	 * @msg  内容
	 * @msg_id mobile_msg表中的id
	 *
	 * @return 输出参数：大于0的数字，发送成功（得到大于0的数字、作为取报告的id）；-1、帐号未注册；-2、其他错误；-3、密码错误；-4、手机号格式不对；-5、余额不足；-6、定时发送时间不是有效的时间格式；-7、禁止10小时以内向同一手机号发送相同短信
	 */
	public function send($phone, $msg){
		 $msg = @iconv ( "UTF-8", "GB2312//IGNORE", $msg );
         $msg = urlencode($msg);

		if(empty($msg)||!check_phone($phone)){
			return false;
		}

		$result = get_file_via_curl(self::host.'SendMessage?UserName='.self::usersms.'&UserPass='.self::pwd .'&Mobile='.$phone. '&Content='.urlencode($msg));
		return $result;
	}


	/**
	 * 群发送短信接口，支持300字以内的长短信
	 *
	 * @phone 手机号,数组 ， 最多600个
	 * @msg  内容
	 * @msg_id mobile_msg表中的id
	 *
	 * @return 输出参数：大于0的数字，发送成功（得到大于0的数字、作为取报告的id）；-1、帐号未注册；-2、其他错误；-3、密码错误；-4、手机号格式不对；-5、余额不足；-6、定时发送时间不是有效的时间格式；-7、禁止10小时以内向同一手机号发送相同短信
	 */
	public function batch_send($phone = array(), $msg){
		if(empty($phone)|| empty($msg)){
			return false;
		}

		$result = get_file_via_curl(self::host.'BatchSend.aspx?CorpID='.self::usersms.'&Pwd='.self::pwd .'&Mobile='.implode(',', $phone). '&Content='.urlencode($msg).'&Cell=&SendTime=');
		return $result;
	}



};
