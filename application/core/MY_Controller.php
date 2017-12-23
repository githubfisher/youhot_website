<?php

/*
 * blank controller
 */

abstract class Normal_Controller extends CI_Controller
{
    var $_data = array();
    var $_un = '';
    public $userid = "";
    private $output_type = 'html';
    private $output_tpl;
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $sid = $this->input->get('sid');
        if (!empty($sid)) {
            // 除了99dak，其他的做转换
            if (strpos($_SERVER['SERVER_NAME'], '99dak') === false) {
                $url = current_whole_url();
                $url = preg_replace("/([?&])sid=[^&]+(&?)/", "\\1", $url) . "\n";
                redirect($url);
                exit();
            }
        }
        $this->_un = $this->session->userdata('username');
        $this->userid = $this->session->userdata('userid');

        $this->layout->set_layout('layout/main');  //default layout (html)

        if($this->input->get_post(RETURN_TYPE) == 'mobile'){
            $this->output_type = 'mobile';
            $this->layout->set_layout('layout/mobile');   //mobile layout
        }
        if($this->input->get_post(RETURN_TYPE) == 'json'){
            $this->output_type = 'json';
        }
        $p = '/(application\/json)|(text\/javascript)/i';
        $accept = element('HTTP_ACCEPT',$_SERVER);
        if(preg_match($p,$accept) === 1){
            $this->output_type = 'json';
        }

        if(mt_rand(0,100)>70){  //30%
            $this->output->enable_profiler();
        }

//        $this->set_track();
    }

    /**
     * 暂时没用,客户端添加track参数
     */
    private function set_track(){
        $cookie_name = $this->config->item('track_cookie_name');
        $track_cookie = $this->input->cookie($cookie_name);
        if(!$track_cookie){
            $this->load->helper('string');
            $_v = array(
                ip2long($this->input->ip_address()),
                random_string('numeric','6'),
                time(),
            );

            $cookie = array(
                'name'   => $cookie_name,
                'value'  => implode('.',$_v),
                'expire' => $this->config->item('track_expiration'),
                'domain' => $this->config->item('cookie_domain'),
                'path'   => $this->config->item('cookie_path'),
                'prefix' => $this->config->item('cookie_prefix'),
                'secure' => $this->config->item('cookie_secure'),
            );
            $this->input->set_cookie($cookie);
        }
    }

    /**
     * @return mixed
     */
    public function getOutputTpl()
    {
        return $this->output_tpl;
    }

    /**
     * @param mixed $output_tpl
     */
    public function setOutputTpl($output_tpl)
    {
        $this->output_tpl = $output_tpl;
    }

    /**
     * @return string
     */
    public function getOutputType()
    {
        return $this->output_type;
    }

    /**
     * @param string $output_type
     */
    public function setOutputType($output_type)
    {
        $this->output_type = $output_type;
    }



    /**
     *
     * 输出消息，exit
     * @param string $msg
     * @param string $goto
     * @param bool(default:true) $auto
     * @param string $fix
     */
    function message($msg, $goto = '', $auto = true, $fix = '')
    {
        if ($goto == '') {
            $goto = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url();
        } else {
            $goto = strpos($goto, 'http') !== false ? $goto : ($goto);
        }
        $goto .= $fix;
        $this->template('sys_message', array_merge($this->_data, array('msg' => $msg, 'goto' => $goto, 'auto' => $auto)));
        echo $this->output->get_output();
        exit();
    }

    /**
     *
     * 载入模版
     * @param string(tpl file name) $template
     * @param array $data
     */
    function template($template, $data = array())
    {
        if (count($data) == 0) $data = $this->_data;
        $this->layout->view($template, $data);
    }


    /**
     * 输出不同格式的模版
     * @param string $tpl name
     * @param array $data (this->_data)
     * @param string $type (default html,json的话请传json)
     */
    function loadview($tpl, $data, $type = 'html')
    {
        switch ($type) {
            case 'json' :
                $this->load->view($tpl . '_json', $this->_data);
                break;
            case 'txt' :
                $this->load->view($tpl . '_txt', $this->_data);
                break;

            default :
                $this->template($tpl . '_html', $this->_data);
                break;
        }
    }


    /**
     * 根据url和total值做分页，默认是20条记录一页
     * @param string $url 基础url
     * @param number $total 总记录数
     * @param number $limit 每页条数
     */

    function page($url = '', $total = 0, $limit = 20)
    {
        $this->load->library('pagination');
        $config ['base_url'] = $url;
        $config ['total_rows'] = $total;
        $config ['per_page'] = $limit;
        $this->pagination->initialize($config);
        $this->_data ['pagination'] = $this->pagination->create_links();
    }

    /**
     * 下面3个函数主用，为以后可能的支持html扩展做准备
     * @param $res
     * @param $hint
     * @param $status_code
     */
    /* 显示错误信息，用于json */
    function _error($res, $hint, $status_code = 200)
    {
        $html = false;
        if($this->output_type == 'html'){
            $html = true;
        }
        $res = ($res === false)?bp_operation_fail:$res;
        $data = array('res' => $res, 'hint' => $hint);

        if ($html) {
            show_error($hint, $status_code);
        } else {
            set_status_header($status_code);
//            $this->load->view('json_view',array('json'=>$data));   //Data is outputed by json_view file
            echo_json($data);
            exit();
        }
    }

    /* 显示结果信息，用于json/html */
    function _result($res, $data = array())
    {
        if (count($data) == 0) $data = $this->_data; //兼容_data
        $data = array_merge(array(bp_result_field => $res), $data);

        if($this->output_type == 'json'){
            $this->load->view('json_view',array('json'=>$data));   //Data is outputed by json_view file
            return ;
        }

        $this->template($this->output_tpl,$data);
        return;

    }


    /* 显示操作成功信息，用于json */
    function _success()
    {
        if($this->output_type == 'html'){
            $this->message('操作成功');
            return;
        }
        $data = array('res' => bp_operation_ok);
        $this->load->view('json_view',array('json'=>$data));   //Data is outputed by json_view file
    }

    function _redirect($res, $hint, $html = false, $status_code = 500)
    {
        redirect();
        $data = array('res' => $res, 'hint' => $hint);
        if ($html) {
            show_error($hint, $status_code);
        } else {
            set_status_header($status_code);
            $this->load->view('json_view',array('json'=>$data));   //Data is outputed by json_view file
            exit();
        }
    }

    /* 简单处理res信息，用于json */
    function _deal_res($res)
    {
        if ($res === true) {
            $res = DB_OPERATION_OK;
        }
        if ($res === DB_OPERATION_OK) {
            $this->_success();
        } else {
            log_message('error', 'Deal res wrong: ' . json_encode($res));
            $this->_error($res, $this->lang->line('bp_operation_fail_hint'));
        }
    }


}

/*
 * form related.login,logout,signup
 * 用户相关控制
 */

abstract class User_Controller extends Api_Controller
{
    var $_data = array();
    public $is_login;
    public $is_admin;

    function __construct()
    {
        parent::__construct();
        $this->load->helper("form");

        $this->load->model('user_model');

        $this->load->library('Form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->_data = array("status" => $this->session->userdata('status'), "usertype" => $this->session->userdata('usertype'),);

        //用于判断用户是否已登录
        $this->is_login = ($this->session->userdata('status') == '1');

        $this->is_admin = ($this->_data['usertype'] == USERTYPE_ADMIN);

    }


    function has_admin_role($role)
    {
        //管理角色，放到用户这里来判断

        if (!array_key_exists('role', $this->_data)) {
            $_role = $this->user_model->get_user_role($this->userid);
            $this->_data['role'] = $_role;
        }

//        var_dump($this->_data['role']);
//        var_dump($role);


        if (is_numeric($role)) {
            return (($this->_data['role'] & $role) != 0);
        } else {
            return false;
        }
    }


    /* 需要登录才可以 */
    /**
     * 需要登录才可以
     * @param bool|FALSE $json
     * @return bool
     */
    function _need_login($json = FALSE)
    {
        if ($this->session->userdata('status') == '1') {
            return true;
        }
        if ($json or ($this->getOutputType() == 'json')) {

            echo_json(array('res' => bp_operation_user_notlogin, 'hint' => $this->lang->line('bp_operation_user_notlogin_hint')));
            exit();
        } else {
            $r = encoded_current_url();
            redirect('/user/login?r=' . $r);
        }
        return false;

    }


    /**
     *
     * 管理员操作记录
     * @param string $target 操作对象，t_designername ,s_studentname ,session_sessionid, ...
     * @param string $action 操作方法
     * @param array|string $info 操作内容
     */
    function admin_trace($target, $action, $info)
    {
        if (is_array($info)) {
            $info = json_encode($info);
        }
        $this->user_model->admin_action_recored($this->userid, $action, $target, $info);
    }


    /*数据校验,传入数组:[[field , label , rule ], [field , label , rule]]*/
    function _validation($rules = array())
    {
        foreach ($rules as $row) {
            if (is_array($row) and count($row) == 3) {
                $this->form_validation->set_rules($row[0], $row[1], $row[2]);
            }
        }
        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, '数据验证失败：' . validation_errors(' ', ' '));
        }
    }
}


abstract class Api_Controller extends Normal_Controller
{

    function __construct()
    {
        parent::__construct();

    }

    /*
     * 输出内容到页面
     * @param array $data
     * @return 无，直接到页面
     */
    public function output_json_view($dataarray)
    {
        header('Content-Type: text/javascript; charset=utf-8');
        echo json_encode($dataarray);
    }
}

abstract class Admin_Controller extends User_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->config->load('admin', TRUE);
        $this->adminconfig = $this->config->item('admin');
        //need login

        $this->_need_login();
        //need admin

//        $this->need_admin(ADMIN_ROLE_ENTRANCE);  //需要有管理入口权限  不控制@2-1

        $this->_data['user'] = filter_data($this->user_model->info($this->userid,'admin'),$this->config->item('json_filter_user_info'));

        if($this->getOutputType() != 'json'){
            $this->layout->set_layout('layout/admin');
        }

    }


    function need_admin($role = ADMIN_ROLE_ENTRANCE)
    {
        $type = $this->input->get_post(RETURN_TYPE);

        if (!($this->has_admin_role($role))) {
            if ($type == 'json') {
                echo_json(array('res' => bp_operation_user_forbidden, 'hint' => '你没有权限，请使用管理员帐号登录后重试'));
                exit;
            } else {
                show_error('你没有权限，请使用管理员帐号<a href="/user/login?r=' . base64_encode($this->config->item('url')['admin']) . '"">登录</a>后重试', $this->config->item('status_code')['forbidden'], '出错了！');
            }
        }
    }



}

