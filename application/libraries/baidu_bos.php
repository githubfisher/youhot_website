<?php
/**
 * 百度开放云存储服务 调用SDK
 * 
 * 
 * @author tconzi@gmail.com
 * @version 1.0.0
 * @package
 */

require_once ( __DIR__.'/BaiduBce.phar' );
use BaiduBce\BceClientConfigOptions;
use BaiduBce\Util\Time;
use BaiduBce\Util\MimeTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Services\Bos\BosClient;

define('__BOS_CLIENT_ROOT', dirname(__DIR__));

class Baidu_Bos {

	/**
	 * __construct
	 *  
	 * 用户关注：是
	 * 
	 * @access public
	 * @param array config { string accessKey 用户在管理平台上申请到的accessKey,
	 *						 string $secretKey 用户在管理平台上申请到的SecretKey,
	 * 						 string $host 百度服务API的域名，不包括http://,
	 *						 string $bucket 百度
	 *						}
	 * @throws BcmsException 如果出错，则抛出异常，异常号是self::BCMS_SDK_INIT_FAIL
	 * 
	 * @version 2.0.0.0
	 */

	private $client = null ;
	public $name ="BOS";
	public function __construct ( $config = array() ) {	
		$this->set_config($config );
	}


	function Baidu_Bos($config){
		$this->__construct($config);
	}
	function set_config($config){
		$conf = array(
	        'credentials' => array(
	            'ak' => $config['accessKey'],
	            'sk' => $config['secretKey'],
	        ),
	        'endpoint' => 'http://bj.bcebos.com',
    	);
		$this->client =  new BosClient($conf);
		$this->bucket = $config['bucket'];

		// log_error("BOS:config ".json_encode($config));
		// log_error("BOS:config ".json_encode($conf));
	}

	/*上传或覆盖文件*/
	function  create_object($file , $object , $opt = array(),$bucket =false ){
			
			if(false === $bucket ){
				$bucket =  $this->bucket;
			}try {
				$this->client->putObjectFromFile($bucket, $object , $file);
				// log_error("BOS:create_object:".$object ." -=== ". $file);

			}catch(Exception $e){
				log_error("BOS:create_object:".$e);
				return false;
			}
			return true;
	}


	function get_object_info(  $object ,$bucket = false ){
		if(false === $bucket ){
			$bucket =  $this->bucket;
		}
        $response = $this->client->getObjectMetadata($this->bucket, $object);
		return $response;
	}



	function get_object($object ,$opt, $bucket = false) {
		//need $opt['fileWriteTo'];
		if(false === $bucket ){
			$bucket =  $this->bucket;
		}
		if(empty( $opt['fileWriteTo'])){
			return false; 
		}
		$filepath =  $opt['fileWriteTo'];
		try{
        	$this->client->getObjectToFile($bucket, $object, $filepath);
		}catch(Exception $e){
			log_error("BOS:get_object_tofile:".$e);
			return false ;
		}
		return true ;
	}

	function get_object_string($object  , $bucket = false ){
		if(false === $bucket ){
			$bucket =  $this->bucket;
		}

		try{
			$response = $this->client->getObjectAsString ( $bucket, $object );
		}catch(Exception $e){
			log_error("BOS:get_object:".$e);
			return false ;
		}
		return $response;
	}


	/*列出所有的文件*/
	function list_object($path ='/' , $start=0 ,$limit= 20,$bucket = false ) {
		if(false === $bucket ){
			$bucket =  $this->bucket;
		}
		// 	PREFIX	限定返回的object key必须以Prefix作为前缀。
		// DELIMITER	是一个用于对Object名字进行分组的字符。所有名字包含指定的前缀且第一次出现Delimiter字符之间的object作为一组元素: CommonPrefixes。
		// MAX_KEYS	限定此次返回object的最大数，此数值不能超过1000，如果不设定，默认为1000。
		// MARKER	设定结果从Marker之后按字母排序的第一个开始返回。


		 $options = array(
            BosOptions::MAX_KEYS=>$limit,
            BosOptions::PREFIX=>$path,
            BosOptions::DELIMITER=>"/",
        );
		 if ($start >0 ){
			$options[BosOptions::MARKER]=$path.$start;//这个地方其实不太准确，bos只提供了字母序，连时间序都不给
		}
		$response = $this->client->listObjects($bucket, $options);
		return $response;
	}

};