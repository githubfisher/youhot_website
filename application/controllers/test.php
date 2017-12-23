<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Test extends User_Controller
{
    public $is_login = 'asdfasdf';
    function __construct()
    {
        parent::__construct();
    }
    public function paypal(){
        define("DEBUG", 1);
// Set to 0 once you're ready to go live
        define("USE_SANDBOX", 1);
        define("LOG_FILE", "./ipn.log");
// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
// read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        if(function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        log_debug('post:'.json_encode($myPost));
        exit;
// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data
        if(USE_SANDBOX == true) {
            $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        } else {
            $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
        }
        $ch = curl_init($paypal_url);
        if ($ch == FALSE) {
            return FALSE;
        }
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        if(DEBUG == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        }
// CONFIG: Optional proxy configuration
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
// Set TCP timeout to 30 seconds
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.
//$cert = __DIR__ . "./cacert.pem";
//curl_setopt($ch, CURLOPT_CAINFO, $cert);
        $res = curl_exec($ch);
        if (curl_errno($ch) != 0) // cURL error
        {
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
            exit;
        } else {
            // Log the entire HTTP response if debug is switched on.
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
                error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
        }
// Inspect IPN validation result and act accordingly
// Split response headers and payload, a better way for strcmp
        $tokens = explode("\r\n\r\n", trim($res));
        $res = trim(end($tokens));
        if (strcmp ($res, "VERIFIED") == 0) {
            // check whether the payment_status is Completed
            // check that txn_id has not been previously processed
            // check that receiver_email is your PayPal email
            // check that payment_amount/payment_currency are correct
            // process payment and mark item as paid.
            // assign posted variables to local variables
            //$item_name = $_POST['item_name'];
            //$item_number = $_POST['item_number'];
            //$payment_status = $_POST['payment_status'];
            //$payment_amount = $_POST['mc_gross'];
            //$payment_currency = $_POST['mc_currency'];
            //$txn_id = $_POST['txn_id'];
            //$receiver_email = $_POST['receiver_email'];
            //$payer_email = $_POST['payer_email'];

            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
            }
        } else if (strcmp ($res, "INVALID") == 0) {
            // log for manual investigation
            // Add business logic here which deals with invalid IPN messages
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
            }
        }
    }
    function work()
    {

//
//        $client = new GearmanClient();
//        $client->addServer();
//        print $client->doNormal("reverse", "Hello World!");
    }

    public function emptyDb(){
        $tables = $this->user_model->db_master->list_tables();
//        var_dump($tables);
        foreach($tables as $table){
            $res = $this->user_model->db_master->query('TRUNCATE TABLE '.$table);
            echo $res.$table;
        }
    }

//    [__T__]介绍[__C__]设计师 James Perse 在设计出一款满意的棒球帽并在父亲的洛杉矶专卖店出售时，就立下从事时尚设计的志向。当电影导演想让演员和剧组工作人员带上这些棒球 帽，唱片公司想将棒球帽用作旅行装备，他决定正式推出 James Perse 系列。而后 Perse 致力于打造精美 T 恤，如今以经营大量奢华运动必备款而举世闻名，包括连衣裙、背心和裤装，以此演绎南加 州休闲生活方式。 查看所有 James Perse 的评论


    private  function get_list_mem_key($prefix,$params){
        $key = array($prefix,'list',serialize($params));
        echo (implode(':',$key));
    }

    public function cache(){
        $this->load->library('cache');
        var_dump($this->cache->on);
        if($this->cache->on){

            $this->cache->get('key');
        }

//        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
////        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
//
//        if ( ! $foo = $this->cache->get('foo'))
//        {
//            echo 'Saving to the cache!<br />';
//            $foo = 'foobarbaz!';
//
//        if ( ! $foo = $this->cache->get('foo'))
//        {
//            echo 'Saving to the cache!<br />';
//            $foo = 'foobarbaz!';
//
//            // Save into the cache for 5 minutes
//            $this->cache->save('foo', $foo, 300);
//        }
//
//        echo $foo;
    }

    function aaa()
    {
//        echo __DIR__;
//        echo __FILE__;

        $checksum = crc32("The quick brown fox jumped over the lazy dogss.");
        printf("%u\n", $checksum);

        echo standard_date('DATE_MYSQL',strtotime('+1 minute'));
        echo "<Pre>";

        require 'vendor/autoload.php'; // include Composer goodies
        try{
            $client = new MongoDB\Client($this->config->item('mongodb'));
            $a = 2;
            $col = 'ccc'.$a;
            $collection = $client->stat->$col;
            $collection->insertOne( [ 'name' => 'Hinterland', 'brewery' => 'BrewDog' ] );
            $cursor = $collection->find();

            foreach($cursor as $document){
                print_r($document);
            }
//
//            $result = $collection->insertOne( [ 'name' => 'Hinterland', 'brewery' => 'BrewDog' ] );
//
//            echo "Inserted with Object ID '{$result->getInsertedId()}'";
//            var_dump($collection->findOne(['_id'=>$result->getInsertedId()]));

        }catch (Exception $e){
            log_error($e->getMessage());
        }


        exit;

        $ip =  gethostbynamel("www.baidu.com");
        var_dump($ip);
        $services = array('http', 'ftp', 'ssh', 'telnet', 'imap',
            'smtp', 'nicname', 'gopher', 'finger', 'pop3', 'www');

        foreach ($services as $service) {
            $port = getservbyname($service, 'tcp');
            echo $service . ": " . $port . "<br />\n";
        }
//        echo $ip;
//        $hostname = gethostbyaddr($ip);
//
//        echo $hostname;



        echo str_replace('.','',$this->input->ip_address());
        exit;


        $a=array(1=>'aa',3=>'bb');
        $b = array('1'=>'cc','5'=>'dd');
        echo json_encode($a,true);
        echo json_encode($b);
        exit;


$_udid = 'df8b807311b5fabbeed4081a4793b1458ca7de8a';
        $_udid2 = 'df8b807311b5fabbeed4081a4793b1458ca7de8a">';
        $_udid3 = 'df8b807311b5fabbeed4081a4793b1458ca7de8a U">';
        $p = '/^[0-9a-z]+$/';
        $v1 = preg_match($p,$_udid);
        $v2 = preg_match($p,$_udid2);
        $v3 = preg_match($p,$_udid3);
var_dump($v1);
        var_dump($v2);
        var_dump($v3);
        exit();
        $this->load->library('rongyun');
        $res = $this->rongyun->messageSystemPublish($this->config->item('admin_userid'),array(1),'RC:TxtMsg','{"content":"[new]亲,您好,您预定的巧俪侬2016春夏新品条纹绣花连衣裙中长款已经开始付尾款了,请前去支付 ","extra":"helloExtra"}');
        $res = json_decode($res);
        if($res->code != '200'){
        }
        echo "send succeed!";
        exit;
        $mem = new Memcached();
        $mem->addServer('localhost',11211);

        $mem->set('a',array('a'=>2));
//        $mem->append('a','c');

        var_dump($mem->get('a'));

//        $this->load->model('product_model','product');
//
//        $this->product->_set_product_list_cache('ssss','aaaa');
//        $this->product->_set_product_list_cache('ssss2','aaaa2');
//        $this->product->_set_product_list_cache('ssss3','aaaa3');
//        $this->product->_delete_product_list_cache();
//        $data = array(
//            'publish_time'=>0,
//            'status'=>0
//
//        );
//        $a = array_intersect($this->config->item('json_filter_product_list'), array_keys($data));
//        var_dump($a);
//        var_dump($this->config->item('json_filter_product_list'));


//
//$this->load->library('profiler');
//        $this->get_list_mem_key('pro',array(0,9,array('name'=>'mbo')));
//        var_dump($this->is_login);
//        $ss = 'application/json, text/javascript, */*; q=0.01';
//
//        $all = $this->config->item('json_filter_collection_detail');
//        $sub = $this->config->item('json_filter_collection_list');
//
//        $mi = array_diff($all,$sub);
//        print_r($mi);
//
//        $this->load->library('rongyun');
//        $c = $this->rongyun->getToken('2','sfsf','sdfsdf');
//        var_dump($c);

//        $this->output->cache(1);
//
//        $this->setOutputTpl('ab');
//
//        $this->_result(bp_operation_ok,$this->_data);

//        var_dump(ini_get('safe_mode'));
//
//        $this->output->set_content_type('application/json');
//        $this->output->set_output(json_encode($this->_data));

//        $this->load->driver('cache');

        $a = '/alidata1/www/system/libraries/cache/drivers/Cache_memcached.php';
//        var_dump(file_exists($a));

//        $this->load->driver('Cache', array('adapter' => 'memcached'));
//        $this->cache->save('aaa','bb');

        $memc = new Memcached();
        $memc->addServer('localhost',11211,1);
        var_dump($memc->get('aaa'));
        var_dump($memc->get('test'));

        echo date('Ymd H:i:s',1458458647);
//        var_dump($this->cache->get('aaa'));
//        var_dump($this->cache->get('test'));

//        echo User_model::password_encode('123456');

//        $ss = "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
//
//        $p = '/[application\/json|text\/javascript]/i';
////        $accept = element('HTTP_ACCEPT',$_SERVER);
////        var_dump($accept);
//        if(preg_match($p,$ss) === 1){
//            echo 'json';
//        }

//        echo User_model::password_md5('ss');

//        print_r(get_defined_vars());
//        print_r(get_defined_functions());
//        print_r(get_defined_constants());
//        try{
//
////            if (empty($userId))
////                throw new Exception('用户 Id 不能为空');
//            if (empty($userId))
//                throw new ErrorException('test');
//        }catch(ErrorException $e){
//            print_r($e->getMessage());
//        }
//        catch(Exception $e){
//            print_r($e->getMessage());
//        }

//        echo standard_date('DATE_MYSQL',1423034032);
//        echo standard_date('DATE_MYSQL',1454570032);

//        $s = 'http://yuntv.letv.com/bcloud.html?uu=iju1rtam2k&vu=6e8d7c2f02&auto_play=1&gpcflag=1&width=640&height=360';
//        $url = parse_url($s);
//        $query = $url['query'];
//        parse_str($query);
//        var_dump(get_defined_vars());
//
//
//        var_dump( $this->userid);

//        $this->user_model->db_slave->where(TBL_PRODUCT.'.id',4);
//        $this->user_model->db_slave->join(TBL_USER.' fu',TBL_PRODUCT.'.author = fu.userid','left');
//        $this->user_model->db_slave->get(TBL_PRODUCT);


//        require_once('application/libraries/alipay/alipay_rsa.function.php');
//        $data = array();
//        $sign = 'sfas';

//        $ali_public_key_path = 'application/libraries/alipay/key/rsa_public_key.pem';
//        $ali_public_key_path = 'application/libraries/alipay/key/alipay_public_key.pem';
//        $pubKey = file_get_contents($ali_public_key_path);
//        var_dump($pubKey);
//        $res = openssl_get_publickey($pubKey);
//        var_dump($res);
//        $time = 1;
//        echo standard_date('DATE_MYSQL',$time);
//        $remain_days = -11238;
//        $days = '0天';
//        if($remain_days>0){
//            if( ($td = $remain_days/(24*3600)) >=1){
//                $days = round($td).'天';
//            }elseif( ($td = $remain_days/(3600)) >=1){
//                $days = round($td).'时';
//            }else{
//                $days = round($remain_days/60).'分';
//            }
//        }
//        echo $days;


//        var_dump(ADMIN_ROLE_ENTRANCE);
//        var_dump(ADMIN_ROLE_USER_PRIVILEGE);
//        var_dump(ADMIN_ROLE_ENTRANCE + ADMIN_ROLE_PRODUCT);
//        var_dump(ADMIN_ROLE_USER_PRIVILEGE + ADMIN_ROLE_PRODUCT);
//        E_ALL;

//echo "<Pre>";
//        $a=' {"title":"介绍","content":"设计师 James Perse 在设计出一款满意的棒球帽并在父亲的洛杉矶专卖店出售时，就立下从事时尚设计的志向。当电影导演想让演员和剧组工作人员带上这些棒球 帽，唱片公司想将棒球帽用作旅行装备，他决定正式推出 James Perse 系列。而后 Perse 致力于打造精美 T 恤，如今以经营大量奢华运动必备款而举世闻名，包括连衣裙、背心和裤装，以此演绎南加 州休闲生活方式。 查看所有 James Perse 的评论"}';
//        echo $a;
//        print_r(json_decode($a,true));
//        var_dump($_REQUEST);
//        var_dump($_SERVER);
//        $headers = apache_request_headers();
//        var_dump($headers);

//            print gearman_version() . "\n";
//        $this->load->library('jpush');
//       echo  unix_to_human('1452914418');

//        require_once 'application/libraries/aliyun-oss-php-sdk/autoload.php';
//        require_once __DIR__ . '/Common.php';
//        $this->load->library('alipay-client/AlipayClient');
//        $this->load->library('aliyun-oss/examples/Common');
//        $this->load->library('aliyun-oss/Common');

//        use OSS\OssClient;
//        use OSS\Core\OssException;

//        $bucket = Common::getBucketName();
//        $ossClient = Common::getOssClient();
//        if (is_null($ossClient)) exit(1);
////*******************************简单使用***************************************************************
//
//// 简单上传变量的内容到文件
//        $ossClient->putObject($bucket, "album/b.file", "hi, oss");
//        Common::println("b.file is created");

//        $data['age']=9;
//        $data = $this->_test_re($data);
//        var_dump($data);
//echo base_url();
//        echo $this->config->item('base_url');
//
//        echo $this->config->item('payment')['ali'];
//        echo
        return;
//        $this->load->model('stu_grade','stu');
//        $this->stu->get_list();
//        echo $this->lang->line('add_user_into_db_wrong');
//        $this->lang->load('date',"chinese");
//        echo $this->lang->line('UM12');

//        echo $this->input->get('a',true);
////        echo $this->input->get('a');
//$this->form_validation->set_rules('name','name','max_length[10]');
//        if($this->form_validation->run() == false){
//            echo validation_errors();
//        }
//
//        $this->load->model('product_model');
//
//        $filter = array('id'=>1,'status >='=>PRODUCT_STATUS_PUBLISHED);
//        $this->product_model->db_slave->where($filter);
//        $query = $this->product_model->db_slave->get(TBL_PRODUCT);
//        var_dump( $this->product_model->get_owner(9));
        $this->load->model('user_model');
        var_dump($this->user_model->get_follow_users($this->_un));
    }

    public function sendsms()
    {
        include "application/libraries/taobaosms/TopSdk.php";
        $c = new TopClient;
        $c->appkey = '23273881';
        $c->secretKey = '021c16189ee90e9c99a0469d55fa7336';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("mabo");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("注册验证");
        $req->setSmsParam('{"code":"7788","product":"mk"}');
        $req->setRecNum("13911190463");
        $req->setSmsTemplateCode("SMS_2650207");
        $resp = $c->execute($req);
        var_dump($resp);
    }

    public function sms()
    {
        // $this->load->library('Mms',array(),  'msg');
        // var_dump($this->msg);
        // $info = $this->msg->send('18500131801', '中文测试1231231,长度超过了也没有关系:fbsql_field_flags(result)， haba中文测试1231231,长度超过了也没有关系:fbsql_field_flags(result)， haba中文测试1231231,长度超过了也没有关系:fbsql_field_flags(result)， haba中文测试1231231,长度超过了也没有关系:fbsql_field_flags(result)， haba中文测试1231231,长度超过了也没有关系:fbsql_field_flags(result)， haba');
        // var_dump($info);
        //班主任提醒
        $this->load->model('mobile_msg_model', 'sms');

        $this->load->model('Op_user_review_model', 'review');
        $message = "[提醒]亲，和老师的课程： \n <b>计划开始</b>，<em>还没连</em>，速跟";
        echo "aaa";
        $a = $this->sms->mail(array('mabo@aifudao.com'), $message, array('from' => '89024505@qq.com', 'is_html' => false, 'mail_subject' => 'abc'));
        if ($a == bp_operation_ok) echo "success";
        else echo "error";

    }


    public function preg()
    {


        $s = array("username=guoliushuai; rk30sdk Build/JRO03H)",
            "username=Zhangdengsen; Q8 Build/JDQ39)",
            "username=ZhangdengsenQ888888 ; Build/JDQ39)",
            "username=AKAK; S8 Build/JDQ39)",
            "username=wxsr; Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)",
            " H9 Build/JDQ39)",
            "username=Lyna; Readboy_G50 Build/JDQ39)",
            "username=qqwwqqww; Q8 Build/JDQ39)",
            "username=huangjie2001; KORIDY H20 Build/JDQ39)",
            "username=jiatingting; Q3 Build/JRO03H)",
            "username=1936096776; Q3 Build/JRO03H)",
            "username=wangxue1; G12 Build/IMM76D)",
            "username=liuxiaohong123; Readboy_G50 Build/JDQ39)",
            "username=dzxch1971; Readboy_G50 Build/JDQ39)",
            "username=89134; Readboy_G50 Build/JDQ39)",
            "username=liguangyin; Q7 Build/JDQ39)",
            "username=xiangfuxiansheng; P88HD(M2P6) Build/JRO03H)",
            "username=%E5%BD%AD%E8%8A%B7%E7%9D%9B; V703Dualcore Build/JDQ39)",
            "username=%E5%BD%AD%E8%8A%B7%E7%9D%9B; V703Dualcore Build/JDQ39)",
            "username=20040606; V703Dualcore Build/JDQ39)",
            "username=wangxin666; rk3026 Build/JDQ39)",
            "username=2532777470; Q7 Build/JDQ39)",
            "username=2532777470; Q7 Build/JDQ39)",
            "username=2532777470; Q7 Build/JDQ39)",
            "username=13395362696; G10 Build/JRO03H)",
            "username=LuJingJie; Q8 Build/JDQ39)",
            "username=1259458094; Q7 Build/JDQ39)",
            "username=316856670; Q8 Build/JDQ39)",
            "username=tony11; Readboy_G30 Build/JDQ39)",
            "username=lyr668; KORIDY H16 Build/JDQ39)",
            "username=malata061; Q8 Build/JDQ39)",
            "username=LuJingJie; Q8 Build/JDQ39)",
            "username=yefei213; HUAWEI B199 Build/HuaweiB199)",
            "username=15890717604; Readboy_G50 Build/JDQ39)",
            "username=LuJingJie; Q8 Build/JDQ39)",
        );
        foreach ($s as $u) {
            echo "$u\n";
            read_device_info_fr_model($u);
            echo "\n";
        }

    }

    /*
        public function sendsms(){
            $this->load->library('Mobile_Detect');
            $ua = $this->Mobile_Detect->getUserAgent();
            var_dump($ua);
             $this->load->model('mobile_msg_model', 'mms');
             $res = $this->mms->send('13911190463', '中文测试1231231,[爱辅导]长度超过了【爱辅导】也没有关');
             var_dump($res);
        }
    */

    public function index()
    {
        // $name = escapeshellarg("中文");
        // echo exec('python application/libraries/pinyin.py/word.py '.$name );
        $device = $this->input->get('d');
        var_dump($device);
        var_dump(device_info($device));
    }

    public function decode_uuid($uuid)
    {
        // 	$device_id = '';
        // 	$type = 0;
        // if(function_exists("check_uuid")) {
        //  	echo("function don't exist\n");
        // 	return 0;
        // }

        // $uuid = $this->input->get_post('uuid');
        // 	if(!check_uuid($uuid, $device_id , $type)){
        //  	echo("error uuid: ".$uuid."\n");
        //       }
        // else {
        //  	echo("uuid: ".$uuid."\tdevice id:".$device_id."\ttype:".$type."\n");
        // }

        // return 0;
    }


    public function location()
    {
        echo_json("abc");

        // echo "location_model\n";
        // $this->load->model('location_model', 'location');
        // $res = $this->location->get_parent_by_names(array('井研县', '启东市', '哈密地区', '吴江市'));

        // var_dump($res);

    }


}
