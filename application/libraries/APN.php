<?php 

/*
 *	class for  Apple Push Notification Service 
 * 
*/
class APN {


 // 	private $apnsHost = 'gateway.sandbox.push.apple.com'; 
	// private $file_flag = '_dev.pem'; //证书后缀

 	private $apnsHost = 'gateway.push.apple.com'; 
 	private $file_flag = '.pem'; //证书后缀

	private $apnsCertPath = 'application/data/';


	private $apnsPort = 2195;
	private $_errno = 0;
	private $_errmsg = 'ok';



	public function __construct() {
	}


	public function error_handler($errno ,$errmsg ,$file,$line){
		$this->_errno = $errno;
		$this->_errmsg = $errmsg;
		// echo 'ERROR:['.$errno.']'.$errmsg." file : $file  line: $line \n";
		die();
	}
	

	/**
	 * 	官方文档中说明：
	 * 		The maximum size allowed for a notification payload is 256 bytes;
	 * 		[ref](http://developer.apple.com/library/mac/#documentation/NetworkingInternet/Conceptual/RemoteNotificationsPG/ApplePushService/ApplePushService.html)
	 * 
	 * 	不过好像不是这么回事 [ref](http://stackoverflow.com/questions/6307748/what-is-the-maximum-length-of-a-push-notification-alert-text)
	 * 	
	 *	message:最长可以到1400字节，但会影响接收。在ios上，推送的数据最多只显示107个字，所以，推107字节以内的数据就好了
	 *  $badge如果不传，则客户端的数字不变
	 * 	
	 * 
	 * 
	*/
	public function push($token , $message= false , $badge =  false ,$client = 'ipad' ){

			set_error_handler(array(&$this,'error_handler'));

			$streamContext = stream_context_create();
			stream_context_set_option($streamContext, 'ssl', 'local_cert', $this->apnsCertPath.'aifudao_'.$client.$this->file_flag);
			$apns = stream_socket_client('ssl://' . $this->apnsHost . ':' . $this->apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT, $streamContext);
			if($apns){
				$payload = array();
				/*$payload['acme1']= 'bar';
				$payload['acme2']= 42;*/

				$payload['aps'] = array('sound' => 'default');
				if(is_numeric($badge)){
					$payload['aps']['badge'] = $badge;
				}
				if(!empty($message)){
					$payload['aps']['alert'] = $message;
				}
				//$payload['aps'] = array('alert' => $message, 'badge' => $badge, );
				$payload = json_encode($payload);
				$apnsMessage = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $token)) . chr(0) . chr(strlen($payload)) . $payload;
				fwrite($apns, $apnsMessage);

			}else{       
		       // echo "Connection Failed";
		       
			}
			fclose($apns);
			
			return  (0 == $this->_errno );
	}

	public function error_number() {
		return $this->_errno;
	}


	public function error_message(){
		return $this->_errmsg;
	}



}