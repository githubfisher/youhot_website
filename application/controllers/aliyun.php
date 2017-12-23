<?php

class Aliyun extends Normal_Controller
{
    private $conf;

    public function __construct()
    {
        parent::__construct();
        $this->load->config('vendor', true);
        $this->conf = $this->config->item('vendor')['aliyun'];

    }

    public function oss_get_sig()
    {
        require_once 'application/libraries/oss_php_sdk_20140625/sdk.class.php';

        $id = $this->conf['access_id'];
        $key = $this->conf['access_secret'];

        $host = 'http://product-album-n.oss-cn-hangzhou.aliyuncs.com';
        $callback_body = '{"callbackUrl":"http://120.27.133.221:23450/","callbackHost":"120.27.133.221","callbackBody":"filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}&my_var=${x:var1}","callbackBodyType":"application/json"}';
//        $callback_body = '{"callbackUrl":"http://120.27.133.221:23450/","callbackHost":"120.27.133.221","callbackBody":"bucket=${bucket}&object=${object}&etag=${etag}&size=${size}&mimeType=${mimeType}&imageInfo.height=${imageInfo.height}&imageInfo.width=${imageInfo.width}&imageInfo.format=${imageInfo.format}&my_var=${x:my_var}","callbackBodyType":"application/json"}';
        $base64_callback_body = base64_encode($callback_body);
        $now = time();
        $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = $this->_gmt_iso8601($end);

        $oss_sdk_service = new alioss($id, $key, $host);
        $dir = 'album/';

        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;


        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        //echo json_encode($arr);
        //return;
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = array();
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        echo json_encode($response);
    }

    private function _gmt_iso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    public function upload($type = 'album')
    {
        $this->load->library('aliyun-oss/Common');
//        use OSS\OssClient;
//        use OSS\Core\OssException;
        $bucket = Common::getBucketName();
        $ossClient = Common::getOssClient();
        if (is_null($ossClient)) exit(1);

// 简单上传变量的内容到文件


        $this->load->library('Form_validation');

        $this->form_validation->set_rules('filename', '文件名', 'trim|required');
        $this->form_validation->set_rules('file_content', '文件内容', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }

        $this->load->helper('string');
        $fn = $this->input->post('filename');
//        $fn  = preg_replace("([^\w\s\d\-_.])", '', $fn);
        $fn = $this->_safe_file_name($fn);
        $_config = ($type == 'avatar') ? 'aliyun_oss_avatar_dir' : 'aliyun_oss_product_album_dir';
        $filename = $this->config->item($_config) . '/' . (random_string('alnum', 6) . '_' . $fn);

        $content = base64_decode($this->input->post('file_content'));

        try{
            $ossClient->putObject($bucket, $filename, $content);
        } catch(OssException $e) {

            $this->_error(bp_operation_fail,$e->getMessage());
            return;
        }
        $data = array("file_url" => $this->config->item('aliyun_oss_img_service_url') . $filename);
        $this->_result(bp_operation_ok, $data);

    }
    public function uploadfileOld($type = 'album')
    {
        $this->load->library('aliyun-oss/Common');
//        use OSS\OssClient;
//        use OSS\Core\OssException;
        $bucket = Common::getBucketName();
        $ossClient = Common::getOssClient();
        if (is_null($ossClient)) exit(1);

// 简单上传变量的内容到文件

//        print_r($_FILES);
        logger("file:".json_encode($_FILES));
	if (!isset($_FILES['image'])) {
	    exit(json_encode(['res' => 1, 'hits' => 'no image!']));
	}
        $fn = $this->_safe_file_name($_FILES['image']['name']);
//        $fn = str_replace('=', '', base64_encode($_FILES['facepic']['name']));
//        $fn = str_replace(' ', '_', $_FILES['facepic']['name']);

        if( $type=='idcard' ){
            $ext = '';
            $pinfo = pathinfo($fn);
            if( isset($pinfo['extension']) && $pinfo['extension'] ){
                $ext = '.'.$pinfo['extension'];
            }
            $md5s = $fn . time();
            $filename = 'idcard/' . (random_string('alnum', 12) . md5($md5s) . $ext);
        } elseif ($type=='return_back') {
	    $ext = '';
            $pinfo = pathinfo($fn);
            if( isset($pinfo['extension']) && $pinfo['extension'] ){
                $ext = '.'.$pinfo['extension'];
            }
            $md5s = $fn . time();
            $filename = 'return_back/' . (random_string('alnum', 12) . md5($md5s) . $ext);
	}else{
            $_config = ($type == 'avatar') ? 'aliyun_oss_avatar_dir' : 'aliyun_oss_product_album_dir';
            $filename = $this->config->item($_config) . '/' . (random_string('alnum', 6) . '_' . $fn);
        }

        $filePath = $_FILES['image']['tmp_name'];
	//$fileContent = file_get_contents($filePath);
        try{
            $ossClient->uploadFile($bucket, $filename, $filePath);
	    //$ossClient->putObject($bucket, $filename, $fileContent);
        } catch(OssException $e) {
            $this->_error(bp_operation_fail,$e->getMessage());
            return;
        }

        $data = array("file_url" => $this->config->item('aliyun_oss_img_service_url') . $filename);
	$data['res'] = 0;
	exit(json_encode($data));
        //$this->_result(bp_operation_ok, $data);
    }

    private function _safe_file_name($fn){
        $ext = get_file_ext($fn);
        return str_replace('=', '', base64_encode($fn)).'.'.$ext;

//        function safe_encode($m){
////    var_dump($m);
//            return str_replace("=",'',base64_encode($m[0]));
//        }
//        $file = preg_replace_callback("([^\w\s\d\-_.])", 'safe_encode', $ca);
    }

    public function uploadfile($type = 'album')
    {
	$filename = $this->input->get_post('filename');
        if (empty($filename)) {
	    exit(json_encode(['res' => 31, 'hits' => 'filename can\'t be null']));
        }
	//logger('filename:'. $filename."\n"); //debug
        $filecontent = $this->input->get_post('file_content');
	if (empty($filecontent)) {
            exit(json_encode(['res' => 31, 'hits' => 'file_content can\'t be null']));
        }
	//logger('filecontent:'. $filecontent."\n"); //debug
        $this->load->library('aliyun-oss/Common');
        $bucket = Common::getBucketName();
        $ossClient = Common::getOssClient();
        if (is_null($ossClient)) exit(1);
        $this->load->helper('string');
        $fn = $filename;
        $fn = $this->_safe_file_name($fn);
        $content = base64_decode($filecontent);
        if( $type=='idcard' ){
            $filename = 'idcard/' . (random_string('alnum', 12) . '_' .$fn);
        } elseif ($type=='return_back') {
            $filename = 'return_back/' . (random_string('alnum', 12) . '_' . $ext);
        }else{
            $_config = ($type == 'avatar') ? 'aliyun_oss_avatar_dir' : 'aliyun_oss_product_album_dir';
            $filename = $this->config->item($_config) . '/' . (random_string('alnum', 6) . '_' . $fn);
	}
        try{
            $ossClient->putObject($bucket, $filename, $content);
        } catch(OssException $e) {
            $this->_error(bp_operation_fail,$e->getMessage());
            return;
        }

        $data = array("file_url" => $this->config->item('aliyun_oss_img_service_url') . $filename);
        $data['res'] = 0;
        exit(json_encode($data));
    }
}
