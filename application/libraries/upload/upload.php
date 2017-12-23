<?php
/**
 * 文件上传服务，源自jQuery-File-Upload
 *
 *
 * @author tconzi@gmail.com
 * @version 1.0.0
 * @package
 */

require_once ( 'upload.class.php' );


class Upload {

	/**
	 * __construct
	 *
	 * 用户关注：是
	 *
	 * @access public
	 *
	 * @version 1.0.0.0
	 */
	public function __construct ( $config = array() ) {
	//	var_dump($config);
        $this->uploader = new UploadHandler($config);
	}

	function Upload($config){
		$this->__construct($config);
	}

	function post(){
		$this->uploader->post();
	}
	function get(){
		$this->uploader->get();
	}

}

