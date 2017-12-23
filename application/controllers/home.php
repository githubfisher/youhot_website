<?php

class Home extends Normal_Controller
{



    public function __construct()
    {

        parent::__construct();


    }

    public function index()
    {
        $this->about();
    }

    public function about(){
//        redirect()
//        $this->output->cache(1);

        $this->layout->set_layout('layout/mobile');
        $this->setOutputTpl('about');
        $this->_result(bp_operation_ok,$this->_data);

    }

    public function search_hot_spot()
    {
        $data = array(
            '流苏','条纹','波点','牛仔','蕾丝','印花','棉麻','毛呢'
        );
        $this->_result(bp_operation_ok,$data);
    }

    /**
     * app 自动跳转
     * @todo 需要判断客户端型号,跳转到ios还是an
     */
    public function app($action=null){
        switch($action){
            case 'applytest':
                $this->layout->set_layout('layout/mobile');
                $this->setOutputTpl('app/applytest');
                $this->load->library('mobile_detect');
                $this->_data['is_ios'] = $this->mobile_detect->isiOS();
                $_udid = $this->input->get_post('UDID');
                if( $_udid){

                    if(preg_match('/^[0-9a-z]+$/',$_udid) !== 1){
                        $this->_error(bp_operation_verify_fail,$this->lang->line('bp_operation_verify_fail_hint'));
                    }

                    $this->db_master->insert('udid',array('udid'=> $_udid,'ua'=>$this->input->user_agent(),'ip'=>$this->input->ip_address()));
                    if($this->db_master->affected_rows()>0){
                        $this->_data['udid'] = $_udid;
                    }else{
                        $this->_data['udid'] = false;
                    }

                }
                $this->_result(bp_operation_ok,$this->_data);
                break;
            default:
                $this->_app_open();
                break;
        }

    }

    private function _app_open(){
        $app_store_url = '/';
        $redirect_url = $this->input->get_post('url');
        if(empty($redirect_url)){
            redirect($app_store_url);
            exit();
        }
        $redirect_url = urldecode($redirect_url);
        $html = <<<EOD
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>STYL</title>
</head>
<body>
<h1>正在打开STYL,请稍后……</h1>
<script type="text/javascript">
var redirect = function (location) {
    var iframe = document.createElement('iframe');
    iframe.setAttribute('src', location);
    iframe.setAttribute('width', '1');
    iframe.setAttribute('height', '1');
    iframe.setAttribute('frameborder', '0');
    iframe.setAttribute('position', 'absolute');
    iframe.setAttribute('top', '0');
    iframe.setAttribute('left', '0');
    document.documentElement.appendChild(iframe);
    iframe.parentNode.removeChild(iframe);
    iframe = null;
};

setTimeout(function () {
//    location.href = ('{$app_store_url}');
    redirect('{$app_store_url}');
}, 25);

redirect('{$redirect_url}');
//window.location = "";
//setTimeout(
//function(){
//window.location="";
//}, 1000);
</script>
</body>
</html>
EOD;

        echo $html;
    }

}//end
