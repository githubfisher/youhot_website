<?php

class User extends User_Controller
{
    private $page_config;
    const OAUTH_USERID = 'oauth_uid';

    public function __construct()
    {

        parent::__construct();

        $this->page_config ['query_string_segment'] = 'pn';
        $this->page_config ['per_page'] = 20;

    }

    /**
     * 用户个人中心页面（非api)
     */
    public function index()
    {
        $this->info();
        //用户是否已经登录
        $this->_need_login();
        //		$this->output->enable_profiler(TRUE);

        // 根据用户类型，学生还是老师，载入各自的view
        $_usertype = $this->_data['usertype'];
        $this->load->library('table');

        switch ($_usertype) {

            case USERTYPE_USER:
                redirect("/teacher/");
                break;

            case USERTYPE_DESIGNER:
                $this->load->library('pagination');
                $this->load->model('course_model', 'course');
                $config['per_page'] = 20;
                $config['query_string_segment'] = 'cpn';
                $offset = $this->input->get_post('cpn') ? $this->input->get_post('cpn') : 0;
                $audited = ($this->input->get_post('audited') ? true : false);

                $action = $this->input->get_post('action') ? $this->input->get_post('action') : 'list_all';

                $this->load->model('teacher_model', 'teacher');
                $audit_subject_id = $this->teacher->get_teacher_audit_subject_id($this->_un);
                $this->_data['is_teacher_auditor'] = ($audit_subject_id !== false);
                $this->_data['audit_subject_id'] = $audit_subject_id;
                $this->_data['skilled_subject_id'] = $this->teacher->get_teacher_skilled_subject($this->_un);

                if ($action == 'list_all') {
                    $config['base_url'] = bp_url_teacher_home;
                    $courses = $this->course->get_teacher_courses($this->_un, 2, true, false, $offset, $config['per_page'], $audited);
                }

                //除了正式上课之外的所有课程和答疑==>答疑
                if ($action == 'list_answer') {
                    $config['base_url'] = '/user/index?action=list_answer';
                    $courses = $this->course->get_teacher_courses($this->_un, 2, false, false, $offset, $config['per_page'], $audited, 2);
                }

                //正式上课 type=1
                if ($action == 'list_teach') {
                    $config['base_url'] = '/user/index?action=list_teach';
                    $courses = $this->course->get_teacher_courses($this->_un, 2, false, false, $offset, $config['per_page'], $audited, 1);

                    //通过循环$courses['list']  增加每个记录对应的老师日志lid
                    $this->_get_log_id($courses['list']);
                }

                $config['total_rows'] = $courses['total'];
                $this->pagination->initialize($config);
                $this->_data['course_pagination'] = $this->pagination->create_links();

                //echo"<pre/>";print_r($courses['list']);die;

                $this->_data['fdsession'] = $courses['list'];
                $this->_data['course_count'] = $courses['total'];
                $this->_data['action'] = $action;
                $this->load->model('subject_model', 'subject');
                $this->_data['subjects'] = $this->subject->get_list();

                $this->_data['usertype'] = USERTYPE_DESIGNER;
                $this->_data['title'] = '课程列表';
                unset($config);

                $this->template("teacher/index", $this->_data);
                break;


            case USERTYPE_ADMIN:
                redirect("/admin/");
                break;

            default:
                $this->message('你的账号未知，请重新注册账号', base_url());
                break;
        }

    }

    /**
     * 和index同样
     * Enter description here ...
     */
    public function home()
    {
        $this->index();
    }


    //获得用户类型
    public function usertype()
    {
        $username = $this->input->get_post('username');
        $usertype = $this->user_model->get_usertype($username);
        $data = array('res' => 0);
        if ($usertype !== false) {
            $data['usertype'] = $usertype;
        } else {
            $data['res'] = bp_operation_db_not_find;
            $data['hint'] = '用户不存在';
        }
        echo_json($data);
    }


    // 用户登录成功之后的处理
    function _user_login_success($userinfo)
    {
        $username = $userinfo['username'];
        $usertype = $userinfo['usertype'];


        $userdata = array('userid' => $userinfo['userid'], 'nickname' => $userinfo['nickname'],
            'username' => $userinfo['username'], 'status' => 1, 'login_time' => standard_date('DATE_MYSQL'),
            'usertype' => $userinfo['usertype']

        );

        $update_data = array('bp_lastlogin' => $userdata['login_time'], 'self_status' => 1, //用户自定义的状态
        );
//        $uuid = $this->input->get_post('uuid');
//        $device_type = 0;
//        if (check_uuid($uuid, $did, $device_type)) {
//            $update_data['uuid'] = $did;
//            $update_data['oid'] = $device_type;
//        }

        //Record ty_token when signup
        if (array_key_exists('ry_token', $userinfo) && !empty($userinfo['ry_token'])) {
            $update_data['ry_token'] = $userinfo['ry_token'];
        }

        $this->user_model->update_user_by_username($username, $update_data);

        $this->session->set_userdata($userdata);

        //登录时候记录dinfo信息
        $this->record_device_info($username);

        //function setcookie ($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null) {}
        //ios app specified

        setcookie(
            'login_status',
            1,
            $this->config->item('sess_expiration') + time(),
            $this->config->item('cookie_path'),
            $this->config->item('cookie_domain')
        );
//        echo $this->config->item('sess_expiration');
        return $userdata;
    }

    /**
     * 记录用户设备（暂时没用)
     * @param $username
     */
    function record_device_info($username)
    {

        $data = array();
        $data['ip'] = $this->input->ip_address();
        $data['username'] = $username;
        $_dinfo = $this->input->get('dinfo');
        if (!empty($_dinfo)) {
            $_dinfo = explode(']', str_replace('[', '', $_dinfo));

            $data['device'] = $_dinfo[0];
            $data['sys_name'] = $_dinfo[1];
            $data['sys_version'] = $_dinfo[2];
            $data['device_name'] = $_dinfo[3];
            $data['network'] = $_dinfo[4];
            $data['idfa'] = $_dinfo[5];
            $data['idfv'] = $_dinfo[6];
            $data['mac'] = $_dinfo[7];
            $data['device_id'] = unique_device_id($data['mac'], $data['idfa']);

            //当从客户端登陆有dinfo时候记录

            $this->load->model('user_model', 'user');
            $this->user->add_user_device($data);
        }


    }

    // 通过oauthor2接口提供的登录服务，需要传递 token、openid 及 source来源站点的标记
    //先微信了
    function oauth_login()
    {

        $token = $this->input->post('token');
        $source = $this->input->post('source');
        $openid = $this->input->post('openid');

        $this->_validation([
            ['token', 'token', 'trim|required'],
            ['openid', 'openid', 'trim|required'],
            ['source', 'source', 'trim|required'],
        ]);

        //检验

        $info = $this->_oauth_info($token, $openid, $source);
        if ($info['errcode'] === bp_operation_ok) {

            $unionid = $info['unionid'];
            $userinfo = $this->user_model->check_oauth_login($unionid, $source, $token);


            if (empty($userinfo)) {
                //不存在，注册用户
//                $userinfo = $this->_oauth_reg($info, $token, $source);
//                $userinfo['is_reg'] = 1;
//                //增加一个关联，用于异步处理关联关系
//                $this->user_model->oauth2_add_relation($userinfo['username'], $userinfo['usertype'], $source, $token);
                $this->_result(bp_operation_invalid_pass_un, ['hint' => $this->lang->line('invalid_un_and_pass'), 'unionid' => $unionid]);
                return;
            }

            $this->_user_login_success($userinfo);    //oauth login
            $userinfo['is_reg'] = 0;
            $data = array('userid' => $userinfo['userid'], 'username' => $userinfo['username'], 'sid' => $this->session->userdata('session_id'), 'utype' => $userinfo['usertype'], 'is_reg' => $userinfo['is_reg'], 'oauth_source' => $source,);
            $this->_result(bp_operation_ok, $data);

        } else {
            $this->_error(bp_operation_verify_fail, '登录失败，请重试[info:' . json_encode($info) . ']');
        }
    }

    /**
     * 把微信,微博各种获取信息接口统一处理
     * @param $token
     * @param $openid
     * @param $source
     * @return array
     */
    private function _oauth_info($token, $openid, $source)
    {

        $data = [
            'errcode' => bp_operation_ok,
        ];


        if ($source == 'wx') {
            $info = get_file_via_curl("https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}");
            if ($info && strpos($info, '{') == 0) {
                $info = json_decode($info, TRUE);
                if (isset($info['errcode'])) {
                    $data = $info;
                } else {
                    $data = array_merge($data, $info);
                }
            } else {
                $data['errcode'] = bp_operation_fail;
                $data['errmsg'] = $this->lang->line('unsupported_auth_source');
            }
        } else {
            $data['errcode'] = bp_operation_fail;
            $data['errmsg'] = $this->lang->line('unsupported_auth_source');
        }

        return $data;


    }

    /**
     * 第三方注册（暂时没用）
     * @param $info
     * @param $token
     * @param $source
     * @return array
     */
    function _oauth_reg($info, $token, $source)
    {

        if (empty($info)) {
            $this->_error(bp_operation_verify_fail, '用户信息不存在，请重新登录');
        }
        $this->load->model('location_model');
        $this->load->helper('string');

        $username = exec('python application/libraries/pinyin.py/word.py ' . $info['name']);

        $pass = $username = str_replace('_', '', $username);

        $un = $username = ($info['type'] == 1 ? '' : '') . $username;
        //$un = $username = ($info['type'] ==1?'XYTT_':'XYT_').$username;
        $i = 0;
        while ($this->user_model->check_exist('username', $un)) {
            $un = $username . $i;
            $i++;
        }
        $username = $un;
        $pass = $pass . '_' . random_string('alnum', 4);

        $info['location'] = $this->location_model->get_location_by_code($info['city']);


        $_user_data = array('username' => $username, 'password' => ($pass), 'usertype' => USERTYPE_USER, 'regtime' => standard_date("DATE_MYSQL"), 'user_ip' => $this->input->ip_address(), 'from' => $this->input->post('from', true), 'oauth_token' => $token, 'oauth_userid' => $info['bsid'], 'oauth_source' => $source,);

        $res = false;
        if ($info['type'] == 5 or $info['type'] == '0') {
            $res = $this->_auth_stu_reg($username, $info, $_user_data);
        } elseif ($info['type'] == 1) {
            //老师
            $_user_data['usertype'] = USERTYPE_DESIGNER;
            $res = $this->_auth_tea_reg($username, $info, $_user_data);

        } else {
            $this->_error(bp_operation_verify_fail, '用户类型不匹配');
        }

        if ($res) {
            //发消息，通知用户用户名及密码

            $this->load->model('message_model', 'msg');
            $this->msg->send('admin', $username, '欢迎使用爱辅导服务，你的用户名是：' . $username . ', 密码是:' . $pass . ' .你可以直接使用用户名密码登录APP及http://www.aifudao.com 网站');

            return $_user_data;
        } else {
            $this->_error(bp_operation_verify_fail, '登录失败，请重试[res:' . $res . ' type:' . $info['type'] . ' username:' . $username . ' info:' . $info . ']');
        }


    }


    function _auth_stu_reg($username, $info, $user_data)
    {
        $this->load->model('student_model');

        $grade = array(4, 4, 4, 4, 30, 31, 3, 5, 6, 22, 23, 24);

        $_student_data = array('username' => $username, 'realname' => $info['name'], 'gender' => ($info['sex'] + 1) % 2, 'birthday' => '', 'location' => $info['location'], 'school' => '-', 'grade' => (array_key_exists($info['grade'], $grade) ? $grade[$info['grade']] : 99), 'department' => '-', 'score' => '-', 'telephone' => '-', 'father' => '', 'telephone2' => '', 'homephone' => '', 'info' => $user_data['oauth_source'], 'student_type' => $user_data['oauth_source'],

        );


        if ($this->student_model->add_student($_student_data, $user_data, array(30))) {
            $sess_userdata = array('username' => $username, 'status' => 1, 'login_time' => standard_date('DATE_MYSQL'), 'usertype' => USERTYPE_USER);
            $this->session->set_userdata($sess_userdata);

            return true;
        }
        return false;
    }


    // 通过oauth方式注册的老师
    function _auth_tea_reg($username, $info, $user_data)
    {
        $this->load->model('teacher_model');
        if ($this->teacher_model->oauth2_reg($username, $info, $user_data)) {
            $sess_userdata = array('username' => $username, 'status' => 1, 'login_time' => standard_date('DATE_MYSQL'), 'usertype' => USERTYPE_DESIGNER);
            $this->session->set_userdata($sess_userdata);

            return true;
        }

        return false;
    }


    //登录验证码的json接口
    public
    function captcha()
    {
        $this->load->model('captcha_model', 'captcha');
        $captcha = $this->captcha->create(CAPTCHA_TYPE_LOGIN);

        echo_json(array('res' => bp_operation_ok, 'data' => $captcha,

        ));
    }

    /**
     * user/do_reg
     * 首页
     */


    private
    function _login_output()
    {
        if ($this->input->get_post(RETURN_TYPE) == 'json') {
            $this->load->view('user/login_json', $this->_data);
        } else {
            $this->template('user/login', $this->_data);;
        }
    }

    /**
     * 登录
     */
    public function login()
    {

        //duplicate code with home.php/do_login
        $return_type = $this->input->get_post(RETURN_TYPE);

        $USE_CAPTCHA = false;

        if ($return_type == 'json') $_POST = $_REQUEST;
        //echo $return_type;
        $this->load->helper('date');


        //echo $this->date->standard_date('DATE_MYSQL');

        //用户名，密码检查（前端检查）检查是否合法
        $this->form_validation->set_rules('username', '手机号', 'trim|required|max_length[40]');
        $this->form_validation->set_rules('password', '密码', 'trim|required');

        $this->_data["title"] = "登录styl";
        $this->_data["E_fail"] = "";
        $this->_data['need_captcha'] = false;
        $this->_data['captcha'] = '';

        $username = $this->input->get_post('username');
        $failed_times = 0;
        $forbidden_check = false;
        if ($USE_CAPTCHA && !empty($username)) {
            $check_data = $this->user_model->get_check_list_info($username);
            if (!empty($check_data)) {
                $this->_data['need_captcha'] = true;
                $failed_times = $check_data['times'];
                if ($check_data['times'] > 5) {
                    $this->_data[bp_result_field] = bp_operation_user_forbidden;
                    $this->_data[bp_result_hint_field] = '你好，你今天登录失败次数过多，请稍后再试';
                    return $this->_login_output();
                }


                if ($failed_times > 0 && $return_type != 'json') {// TODO：在应用里加上验证码处理后，这个就可以去掉这个IF了
                    $this->form_validation->set_rules('captcha', '验证码', 'trim|required|callback_captcha_check');
                    $forbidden_check = true;
                }

            }
        }

        if (!$this->input->post()) {
            switch ($this->input->get_post(RETURN_TYPE)) {
                case 'json':
                    $this->load->view('user/login_json', $this->_data);
                    break;

                default:
                    $this->template('user/login', $this->_data);;
                    break;
            }
            return;
        }

        if ($this->form_validation->run() == FALSE) {

            if ($return_type == 'json') {

                $this->_data[bp_result_field] = bp_operation_verify_fail;
                $this->form_validation->set_error_delimiters('', '');
                $this->_data['hint'] = '验证失败 ' . form_error('username', '', '') . form_error('password', '', '');
                if ($forbidden_check) {
                    $this->_data['hint'] .= form_error('captcha', '', '');
                }

            }
            if ($forbidden_check) {
                $this->load->model('captcha_model', 'captcha_model');
                $this->_data['captcha'] = $this->captcha_model->create(CAPTCHA_TYPE_LOGIN);
            }
            $this->_login_output();
        }


        $param = array('username' => $username, 'password' => $this->input->get_post('password'), 'captcha' => $this->input->get_post('captcha'),);


//retrieve userdata from db user to see if provided correct usname and password.
        $userinfo = $this->user_model->checkuser_is_ok($param);
        if ($userinfo) {

            if (empty($userinfo['ry_token'])) {
                //get ry token
                $this->load->library('rongyun');
//                $rongyun = $this->rongyun->getRongyun();
                $res = $this->rongyun->getToken($userinfo['userid'], $userinfo['nickname'], element('facepic', $userinfo, $this->config->item('default_facepic')));
                $token = json_decode($res);
                if ($token->code == '200') {
                    $userinfo['ry_token'] = $token->token;
                }
            }

            //Bind oauth unionid if have
            if ($unionid = $this->input->get_post(self::OAUTH_USERID)) {
                $this->user_model->oauth2_bind($userinfo['userid'], $unionid, $this->input->get_post('source'));
                if ($res != DB_OPERATION_OK) {
                    $this->_error($res, $this->lang->line('oauth_bind_error'));
                    return;
                }
            }

            $_data = $this->_user_login_success($userinfo);   //login

            //todo flashdata
            //$this->session->set_flashdata('ok_info',"s:sss");
            switch ($return_type) {
                case 'json':
                    $this->_data[bp_result_field] = bp_operation_ok;
                    $this->_data['userinfo'] = filter_data($userinfo, $this->config->item('json_filter_user_info_basic'));
                    break;

                default:
                    $r = trim($this->input->get_post('r') ? base64_decode(urldecode($this->input->get_post('r'))) : "/");
//                    var_dump($_REQUEST);
//                    echo $this->input->get_post('r');
//                    var_dump($r);

                    //note : 对$r值做验证，包含http://的不处理,对包含.的不处理
                    if (strpos($r, 'http') === 0 || strpos($r, '.') > 0) {
                        log_message('error', '恶意回跳url : ' . $r);
                        $r = '/';
                    }

                    redirect($r);
                    break;
            }
        } else {
            log_error('login got error:' . $username);
            $_hint = $this->lang->line('invalid_un_and_pass');
            $this->_data['E_fail'] = $_hint;

            if ($USE_CAPTCHA) {
                $this->user_model->update_check_list_times($username, $failed_times + 1);
                $this->_data['need_captcha'] = true;
                $this->load->model('captcha_model', 'captcha_model');
                $this->_data['captcha'] = $this->captcha_model->create(CAPTCHA_TYPE_LOGIN);;
            }

            if ($return_type == 'json') {
                $this->_data[bp_result_field] = bp_operation_invalid_pass_un;
                $this->_data[bp_result_hint_field] = $_hint;
            }
        }

        switch ($this->input->get_post(RETURN_TYPE)) {
            case 'json':
                $this->load->view('user/login_json', $this->_data);
//                $this->_result(bp_operation_ok,filter_data($this->_data['userinfo']);
//                echo_json($this->_data);
                break;

            default:
                $this->template('user/login', $this->_data);;
                break;
        }
    }

    /**
     * 登录
     */
    public function admin_login()
    {

        //duplicate code with home.php/do_login
        $return_type = $this->input->get_post(RETURN_TYPE);

        $USE_CAPTCHA = false;

        if ($return_type == 'json') $_POST = $_REQUEST;
//        echo $return_type;die;
        $this->load->helper('date');


//        echo $this->date->standard_date('DATE_MYSQL');

        //用户名，密码检查（前端检查）检查是否合法
        $this->form_validation->set_rules('username', '手机号', 'trim|required|max_length[40]');
        $this->form_validation->set_rules('password', '密码', 'trim|required');

        $this->_data["title"] = "登录styl";
        $this->_data["E_fail"] = "";
        $this->_data['need_captcha'] = false;
        $this->_data['captcha'] = '';

        $username = $this->input->get_post('username');
        $failed_times = 0;
        $forbidden_check = false;
        if ($USE_CAPTCHA && !empty($username)) {
            $check_data = $this->user_model->get_check_list_info($username);
            if (!empty($check_data)) {
                $this->_data['need_captcha'] = true;
                $failed_times = $check_data['times'];
                if ($check_data['times'] > 5) {
                    $this->_data[bp_result_field] = bp_operation_user_forbidden;
                    $this->_data[bp_result_hint_field] = '你好，你今天登录失败次数过多，请稍后再试';
                    return $this->_login_output();
                }


                if ($failed_times > 0 && $return_type != 'json') {// TODO：在应用里加上验证码处理后，这个就可以去掉这个IF了
                    $this->form_validation->set_rules('captcha', '验证码', 'trim|required|callback_captcha_check');
                    $forbidden_check = true;
                }

            }
        }

        if (!$this->input->post()) {
            switch ($this->input->get_post(RETURN_TYPE)) {
                case 'json':
                    $this->load->view('user/login_json', $this->_data);
                    break;

                default:
                    $this->template('user/login', $this->_data);;
                    break;
            }
            return;
        }

        if ($this->form_validation->run() == FALSE) {

            if ($return_type == 'json') {

                $this->_data[bp_result_field] = bp_operation_verify_fail;
                $this->form_validation->set_error_delimiters('', '');
                $this->_data['hint'] = '验证失败 ' . form_error('username', '', '') . form_error('password', '', '');
                if ($forbidden_check) {
                    $this->_data['hint'] .= form_error('captcha', '', '');
                }

            }
            if ($forbidden_check) {
                $this->load->model('captcha_model', 'captcha_model');
                $this->_data['captcha'] = $this->captcha_model->create(CAPTCHA_TYPE_LOGIN);
            }
            $this->_login_output();
        }


        $param = array('username' => $username, 'password' => $this->input->get_post('password'), 'captcha' => $this->input->get_post('captcha'),);


//retrieve userdata from db user to see if provided correct usname and password.
        $userinfo = $this->user_model->checkuser_is_ok($param);
        #判断权限
        if(!in_array($userinfo['usertype'], array(USERTYPE_ADMIN, USERTYPE_DESIGNER, USERTYPE_BUYER))){
            $this ->_error(bp_operation_fail, $this->lang->line('bp_operation_user_forbidden_hint'));
        }

        if ($userinfo) {
            #判断rong yun账户是否登录
            if (empty($userinfo['ry_token'])) {
                //get ry token
                $this->load->library('rongyun');
//                $rongyun = $this->rongyun->getRongyun();
                $res = $this->rongyun->getToken($userinfo['userid'], $userinfo['nickname'], element('facepic', $userinfo, $this->config->item('default_facepic')));
                $token = json_decode($res);
                if ($token->code == '200') {
                    $userinfo['ry_token'] = $token->token;
                }
            }

            //Bind oauth unionid if have
            if ($unionid = $this->input->get_post(self::OAUTH_USERID)) {
                $this->user_model->oauth2_bind($userinfo['userid'], $unionid, $this->input->get_post('source'));
                if ($res != DB_OPERATION_OK) {
                    $this->_error($res, $this->lang->line('oauth_bind_error'));
                    return;
                }
            }

            $_data = $this->_user_login_success($userinfo);   //login

            //todo flashdata
            //$this->session->set_flashdata('ok_info',"s:sss");
            switch ($return_type) {
                case 'json':
                    $this->_data[bp_result_field] = bp_operation_ok;
                    $this->_data['userinfo'] = filter_data($userinfo, $this->config->item('json_filter_user_info_basic'));
                    break;

                default:
                    $r = trim($this->input->get_post('r') ? base64_decode(urldecode($this->input->get_post('r'))) : "/");
//                    var_dump($_REQUEST);
//                    echo $this->input->get_post('r');
//                    var_dump($r);

                    //note : 对$r值做验证，包含http://的不处理,对包含.的不处理
                    if (strpos($r, 'http') === 0 || strpos($r, '.') > 0) {
                        log_message('error', '恶意回跳url : ' . $r);
                        $r = '/';
                    }

                    redirect($r);
                    break;
            }
        } else {
            log_error('login got error:' . $username);
            $_hint = $this->lang->line('invalid_un_and_pass');
            $this->_data['E_fail'] = $_hint;

            if ($USE_CAPTCHA) {
                $this->user_model->update_check_list_times($username, $failed_times + 1);
                $this->_data['need_captcha'] = true;
                $this->load->model('captcha_model', 'captcha_model');
                $this->_data['captcha'] = $this->captcha_model->create(CAPTCHA_TYPE_LOGIN);;
            }

            if ($return_type == 'json') {
                $this->_data[bp_result_field] = bp_operation_invalid_pass_un;
                $this->_data[bp_result_hint_field] = $_hint;
            }
        }

        switch ($this->input->get_post(RETURN_TYPE)) {
            case 'json':
                $this->load->view('user/login_json', $this->_data);
//                $this->_result(bp_operation_ok,filter_data($this->_data['userinfo']);
//                echo_json($this->_data);
                break;

            default:
                $this->template('user/login', $this->_data);;
                break;
        }
    }

    /**
     * 第三方账号解绑定，比如通过微信登录过来的用户，会和系统已有用户解绑
     */
    public function oauth_unbind()
    {
        $this->_need_login(true);

        $this->_validation([
            ['source', 'source', 'trim|required']
        ]);

        $res = $this->user_model->oauth2_unbind($this->userid, $this->input->get_post('source'));
        $this->_deal_res($res);

    }

    /**
     * 验证验证码
     * @param string $captcha
     * @return bool
     */
    function captcha_check($captcha = '')
    {
        $this->load->model('captcha_model', 'captcha');
        $word = trim($captcha);
        if (trim($word) === '') {
            $this->form_validation->set_message(__FUNCTION__, '验证码不能为空');
            return FALSE;
        }

        if ($this->captcha->check($word, CAPTCHA_TYPE_LOGIN)) {
            return TRUE;
        } else {
            $this->form_validation->set_message(__FUNCTION__, '验证码错误，请重新输入');
            return FALSE;
        }
    }


    /**
     * 注册
     * @param string $type
     */
    public function signup($type = 'user')
    {


        //获取推荐码
        $ucode = $this->input->get_post('ucode');
        if (!$ucode) {
            $ref = $this->input->get_request_header('Referer');

            if (preg_match("/ucode=([^&]*)/", $ref, $xcode)) {
                $ucode = $xcode[1];
            } else {
                $ucode = '';
            }
        }


        if (!is_numeric($ucode)) {
            $ucode = '';
        }

        $this->_data['ucode'] = $ucode;
        //end
        $_POST = $_REQUEST;
        switch ($type) {
            case 'user':
                $this->_stu_reg();

                break;

            case 'teacher':
                $this->_tea_reg();
                break;

            case 'addface':
                $this->_tea_addface();
                break;
        }

    }

    /**
     * 学生注册，在这里一般是用户的注册
     */
    private
    function _stu_reg()
    {


        $rule = "user_signup";
        $view = "student/signup";


        if ($this->form_validation->run($rule) == FALSE) {
            //echo validation_errors();
            if ($this->input->get_post(RETURN_TYPE) == 'json') {

                echo_json(array(bp_result_field => bp_operation_verify_fail, bp_result_hint_field => validation_errors(' ', ' ')));
                return;
            }
            $this->template($view, $this->_data);
        } else {
            //推荐人
            $_user_data = array('username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
//                'verify_code' => $this->input->post('verify_code'),
                'regtime' => standard_date("DATE_MYSQL"),
                'user_ip' => $this->input->ip_address(),
//                'nickname' => substr($this->input->get_post('username'),-4)

            );


            $ucode = $this->_data['ucode'];
            $recommend_username = '';
            if (is_numeric($ucode)) {
                $recommend_username = $this->user_model->get_username($ucode);
            } else {
                //支持推荐人直接传用户名
                $recommend_user = $this->input->get_post('referee'); // 推荐人d手机号
                if (!empty($recommend_user)) {
                    $recommend_username = $recommend_user;
                }
            }

            $_user_data['recommend_username'] = $recommend_username;
            $_user_data['nickname'] = ($this->config->item('default_nickname') . substr($_user_data['username'],-4));

            if ($_userid = $this->user_model->add_user($_user_data)) {

//                $username = $this->input->post('username');
//                $sess_userdata = array('username' => $username, 'status' => 1, 'login_time' => standard_date('DATE_MYSQL'));
//                $this->session->set_userdata($sess_userdata);
                log_debug('reg userid:' . $_userid);
                //auto logi:
                $_user_data['usertype'] = USERTYPE_USER;  //default user type
                $_user_data['userid'] = $_userid;
		$_user_data['facepic'] = 'http://product-album.img-cn-hangzhou.aliyuncs.com/avatar/default_avatar.png';

                log_debug("_userdata:",$_user_data);

                //Get rongyun token http://www.rongcloud.cn/docs/server.html#获取_Token_方法
                //$this->load->library('rongyun');
//                $rongyun = $this->rongyun->getRongyun();
                //$res = $this->rongyun->getToken($_userid, $_user_data['nickname'], $this->config->item('default_facepic'));
                //$token = json_decode($res);
                //if ($token->code == '200') {
                   // $_user_data['ry_token'] = $token->token;
                //}

                //Bind oauth unionid if have
                if ($unionid = $this->input->get_post(self::OAUTH_USERID)) {
                    $this->user_model->oauth2_bind($_userid, $unionid, $this->input->get_post('source'));
                    if ($res != DB_OPERATION_OK) {
                        $this->_error($res, $this->lang->line('oauth_bind_error'));
                        return;
                    }
                }
                $this->getCoupon($_userid); // 赠送新手券
		if (!empty($recommend_user)) {
		    logger('Referee: '.$recommend_user); // debug
                    //$this->getCoupon($_userid); // 赠送新手券
                    $this->getRefereeCoupon($recommend_user); // 推荐人送券
                }
                $this->_user_login_success($_user_data);  //When signup
                $this->_result(bp_operation_ok, filter_data($_user_data, $this->config->item('json_filter_user_info_basic')));

            } else {
                log_message('error', 'add student:' . $this->input->post('username') . " get wrong!!");
                if ($this->input->get_post(RETURN_TYPE) == 'json') {
                    echo_json(array(bp_result_field => bp_operation_unknown_error, bp_result_hint_field => $this->lang->line('add_user_into_db_wrong')));
                    return;
                }
                $this->message($this->lang->line('add_user_into_db_wrong'), '', FALSE);

            }
        }


    }

    private
    function _tea_reg()
    {
        $this->load->model('teacher_model');

        $rule = "teacher_signup";
        $view = "teacher/signup";

        //skilled_subject
        $this->load->model('subject_model', 'subject');
        $_da = $this->subject->get_list();
        $this->_data ["skilled_subject_options"] = array("" => "请选择") + $_da; //return array

        //teacher grade
        $this->load->model('teacher_grade');
        $this->_data ["grade_options"] = array("" => "请选择") + $this->teacher_grade->get_list(); //return array

        /*
         *ablesubject model
         */
        $able_subject = "";
        //print_r($_da);
        foreach ($_da as $key => $val) {
            $able_subject .= form_checkbox('able_subject[]', $key, set_checkbox('able_subject', $key), 'id=as' . $key) . form_label($val, 'as' . $key);
        };

        $this->_data ["able_subject"] = $able_subject; //return array
        /*
         * teacher grade model
         */
        $this->load->model('stu_grade');

        $able_grade = $this->stu_grade->get_all(); //return array

        //echo $able_grade;
        $this->_data ["able_grade"] = $able_grade;
        $teacher_grade = $this->input->get_post('able_grade');
        if (!$teacher_grade) {
            $teacher_grade = array();
        }
        $this->_data['teacher_grade'] = $teacher_grade;

        $cooperate_teacher_type = $this->input->get_post('ccode');

        if (!$this->user_model->is_valid_cooperation_code($cooperate_teacher_type)) {
            $cooperate_teacher_type = '';
        }
        $this->_data['ccode'] = $cooperate_teacher_type;

        /*
        foreach ($_POST as $key => $value) {
            $this->form_validation->set_rules($key,$key,'');
        }
*/
        //$this->form_validation->set_rules('able_grade[]','able grade','required');
        if ($this->form_validation->run($rule) == FALSE) {
            //echo validation_errors();
            if ($this->input->get_post(RETURN_TYPE) == 'json') {
                echo_json(array(bp_result_field => bp_operation_unknown_error, bp_result_hint_field => bp_operation_unknown_error_hint));
                return;
            }
            $this->template($view, $this->_data);
        } else {

            $_un = $this->input->post('username');
            //user info
            $recommend_username = '';
            $ucode = $this->input->get_post('ucode');
            if (!empty($ucode)) {
                $this->load->model('user_model', 'user');
                $recommend_username = $this->user->get_username($ucode);
            }

            $_user_data = array('username' => $_un, 'password' => ($this->input->post('password')), 'usertype' => $this->input->post('usertype'), 'regtime' => standard_date("DATE_MYSQL"), 'user_ip' => $this->input->ip_address(), 'recommend_username' => $recommend_username);
            if (!empty($cooperate_teacher_type)) {
                $_user_data['belong'] = $cooperate_teacher_type;
            }

            //teacher info
            $_teacher_data = array('username' => $_un, 'familyname' => htmlspecialchars($this->input->post('familyname', true)), 'realname' => htmlspecialchars($this->input->post('realname', true)), 'telephone' => $this->input->post('telephone'), 'email' => $this->input->post('email'), 'gender' => $this->input->post('gender'), 'birthday' => $this->input->post('birthday_year') . '-' . $this->input->post('birthday_month'), 'school' => htmlspecialchars(strip_tags($this->input->post('school', true))), 'grade' => $this->input->post('grade'), 'major' => strip_tags($this->input->post('major', true)), 'skilled_subject' => $this->input->post('skilled_subject'), 'award' => $this->input->post('award', true), 'exp_year' => $this->input->post('exp_year'), 'teach_exp' => $this->input->post('teach_exp', TRUE),

                'intro' => $this->input->post('intro', TRUE));

            if (!empty($cooperate_teacher_type)) {
                $_teacher_data['teacher_type'] = $cooperate_teacher_type;
            }

            //able_subject
            $_subject_data = array();
            foreach ($this->input->post('able_subject') as $key => $val) {
                $_subject_data[] = array('username' => $_un, 'sid' => $val);
            }

            //able grade
            $_grade_data = array();
            foreach ($this->input->post('able_grade') as $key => $val) {
                $_grade_data[] = array('username' => $_un, 'gid' => $val);
            }


            if ($this->teacher_model->add_teacher($_un, $_user_data, $_teacher_data, $_subject_data, $_grade_data)) {
                $userdata = array('username' => $_un, 'status' => 1, 'login_time' => standard_date('DATE_MYSQL'), 'usertype' => USERTYPE_DESIGNER);
                $this->session->set_userdata($userdata);

                if ($cooperate_teacher_type == 'scichat') {
                    $this->load->model('course_model', 'course');
                    $this->course->create($_un, 'scichat default class', '2004-09-01', '2114-09-01', 0, 0, 'scichat default class', 'no plan now', 0, 0, 0, 0, 1024, 1024, 1, 1, 0, 0, array('image_id' => 0, 'detail_image_id' => 0));
                    $doudou = 2;
                    $this->load->model('account_model', 'account');
                    $this->account->add_doudou_deal_and_account($_un, $doudou, '注册帐户成功，赠送豆豆' . $doudou . '粒', 'doudou_user_reg_scichat', '', array('from' => PAY_FROM_USER_ACTIVE));
                }

                if ($this->input->get_post(RETURN_TYPE) == 'json') {
                    echo_json(array(bp_result_field => bp_operation_ok));
                    return;
                }

                $welcome_message = "<p class='green big'>恭喜你，老师帐号 %s 注册成功!</p>
					<p>A、你可以给学生上课了，点击\"<a href='%s' target='_blank'><strong>未认证老师如何上课？</strong></a>\"获得更多帮助：</p>
					<p>
                	<ul>
                	<li><a href='%s' target='_blank'><strong>下载客户端</strong></a></li>
					</ul>
					</p>
                	<p>B、要成为爱辅导的正式收费老师，你还需要提交身份认证申请，<a href='%s' target='_blank'><strong>点击这里在线提交申请</strong></a></p>
                	<ul>
                	<li><a href='%s' target='_blank'>完善个人资料</a></li>
                	<li><a href='%s' target='_blank'>了解如何使用爱辅导产品</a></li>
                	</ul>";
                $msg = sprintf($welcome_message, $userdata['username'], '/blog/archives/567', bp_url_aifudao_download, bp_teacher_profile_auth_url, bp_teacher_profile_personal_url, bp_url_aifudao_help);
                $this->message($msg, bp_url_teacher_home, FALSE);
            } else {
                $this->message($this->lang->line('add_tea_into_db_wrong'), '', FALSE);
                log_message('error', 'add teacher:' . $this->input->post['username'] . " get wrong!!");
            }
        }
    }


    /**
     *
     * 手机和座机必须填写一个
     * @param str $str
     * @param field $field
     */
    function validate_telephone2_homephone($str, $field)
    {
        $this->form_validation->set_message('validate_telephone2_homephone', ' %s至少填写一个.');
        if (isset ($_POST [$field]) or isset ($str)) {

            return TRUE;
        }

        return FALSE;
    }

    public
    function logout()
    {
        $this->session->set_userdata('logout_time', standard_date('DATE_MYSQL', time()));
        $this->session->sess_destroy();

        // Kill the cookie
        setcookie(
            'login_status',
            0,
            (time() - 31500000),
            $this->config->item('cookie_path'),
            $this->config->item('cookie_domain'),
            0
        );

        if ($this->input->get_post(RETURN_TYPE) == 'json') {
            $this->_success();
            return;
        }
        $r = "/";
        redirect($r);
    }


    /**
     *
     * 检查是否有重名用户名
     * @return userexistres=true/false;
     */
    public
    function checkusername()
    {
        $un = $this->input->get_post('username');
        $q = $this->user_model->check_exist('username', $un);
        if ($q) {
            $this->_success();
        } else {
            $this->_error(bp_operation_fail, $this->lang->line('hint_user_not_exists'));
        }

    }


//按用户名查询，用于客户端完整查询
    function sugguest()
    {
        $name = trim($this->input->get_post('q'));
        $type = array(1, 2);//只查老师与学生
        $res = $this->user_model->username_sugguestion($name, $type);

        $res = order_by_match($name, $res);

        echo_json(array_slice($res, 0, 20));
    }


// 客户端每一次启动都会调用这个报告
    function activate()
    {
        $_dinfo = $this->input->get_post('dinfo');
        $_store = $this->input->get_post('store');
        $_p_dinfo = $_dinfo;
        $data = array();
        $data['ip'] = $this->input->ip_address();

        if (!empty($_store)) {
            $data['store'] = $_store;
        }
        if (!empty($_dinfo)) {
            $_dinfo = explode(']', str_replace('[', '', $_dinfo));

            $data['device'] = $_dinfo[0];
            $data['sys_name'] = $_dinfo[1];
            $data['sys_version'] = $_dinfo[2];
            $data['device_name'] = $_dinfo[3];
            $data['network'] = $_dinfo[4];
            $data['idfa'] = $_dinfo[5];
            $data['idfv'] = $_dinfo[6];
            $data['mac'] = $_dinfo[7];
            $data['device_id'] = unique_device_id($data['mac'], $data['idfa']);
        }
        $this->load->model('activate_model', 'activate');
        $res = $this->activate->add($data);
        if ($res == DB_OPERATION_FAIL) {
            log_message('error', 'insert into activate get wrong ' . json_encode($data));
        }
        //$this->check_from_ad($data);
        echo_json(array('res' => 0));

    }

    function check_from_ad($data)
    {
        $idfa = (array_key_exists('idfa', $data)) ? $data['idfa'] : 'NOIDFA';
        $mac = (array_key_exists('mac', $data)) ? $data['mac'] : 'NOMAC';
        $device = (array_key_exists('device', $data)) ? $data['device'] : 'UNKNOWN';
        $this->load->model('ad_model', 'ad');
        $ad_data = $this->ad->get($idfa, $mac);
        if (!empty($ad_data)) {
            //@todo 不同的verdor要区分
            if ($ad_data['vendor'] == AD_VENDOR_MOBSMAR and $ad_data['status'] != '1') {
                if ($device == 'iPad') {
                    $this->ad->mobsmar_callback($idfa, $mac, $device);
                } else {
                    $this->ad->mobsmar_record($idfa, $mac, $device, json_encode($data));
                }
            }

        }
    }


    /**
     * get mobile verify code
     * @param phone_num
     * @return json
     */
    public
    function get_verify_code()
    {
        $receiver = $this->input->get_post('username');
        $forgot_pwd = $this->input->get_post('forgot_pwd');
        if (!check_phone($receiver)) {
            $this->_data ['res'] = bp_operation_verify_fail;
            $this->_data ['hint'] = '手机号码格式不正确，请核对';
            $this->_error($this->_data['res'], $this->_data['hint']);
        } else {
            //是否已经注册过
            $registed = $this->user_model->check_exist('username', $receiver);

            if ($registed && !$forgot_pwd) {
                $this->_data ['res'] = bp_operation_data_used;
                $this->_data ['hint'] = $this->lang->line('error_user_dup');
                $this->_error($this->_data['res'], $this->_data['hint']);
            }
            if (!$registed && $forgot_pwd) {
                $this->_data ['res'] = bp_operation_verify_fail;
                $this->_data ['hint'] = $this->lang->line('hint_user_not_exists');
                $this->_error($this->_data['res'], $this->_data['hint']);
            }
            $this->_send_verify_code($receiver, $forgot_pwd);
            return true;
        }
    }

    /**
     * get mobile verify code
     * @param phone_num
     * @return json
     */
    public function get_verify_code2()
    {
        $receiver = $this->input->get_post('username');
        $forgot_pwd = $this->input->get_post('forgot_pwd');

        #判断username是邮箱还是电话号码
        $type = 2; //默认电话号码注册
        //验证邮箱
        $this->load->helper('email');
        if (valid_email($receiver)) {
            $type = 1;
        } elseif (check_phone($receiver)) {//验证手机号
            $type = 2;
        } else {//既不是手机也不是电话号码
            $this->_data ['res'] = bp_operation_verify_fail;
            $this->_data ['hint'] = '手机号码或邮箱格式不正确，请核对';
            $this->_error($this->_data['res'], $this->_data['hint']);
        }


        //是否已经注册过
        $registed = $this->user_model->check_exist('username', $receiver);

        if ($registed && !$forgot_pwd) {
            $this->_data ['res'] = bp_operation_data_used;
            $this->_data ['hint'] = $this->lang->line('error_user_dup');
            $this->_error($this->_data['res'], $this->_data['hint']);
        }

        if (!$registed && $forgot_pwd) {
            $this->_data ['res'] = bp_operation_verify_fail;
            $this->_data ['hint'] = $this->lang->line('hint_user_not_exists');
            $this->_error($this->_data['res'], $this->_data['hint']);
        }
        if ($type == 2) {
            $this->_send_verify_code($receiver, $forgot_pwd);
        } else {
            $this->_send_email_verify_code($receiver, $forgot_pwd);
        }
        return true;
    }

    /**
     * 发送邮箱验证码
     * @param $receiver
     * @param bool|false $forgot_pwd
     */
    private function _send_email_verify_code($receiver, $forgot_pwd = false)
    {
        //@todo 对每个手机号作频次限制,60s内不能重复获取.redis配合支持


        $this->load->model('email_msg_model');
        $verify_code = random_string('numeric', 4);
        log_message('debug', 'verify_code:' . $verify_code);;
        // $res_send['code'] = DB_OPERATION_OK;


        ob_end_clean();//#清除之前的缓冲内容，这是必需的，如果之前的缓存不为空的话，里面可能有http头或者其它内容，导致后面的内容不能及时的输出

        ob_start();//#当前代码缓冲
        header("Connection: close"); //#告诉浏览器，连接关闭了，这样浏览器就不用等待服务器的响应
        //#可以发送200状态码，以这些请求是成功的，要不然可能浏览器会重试，特别是有代理的情况下
        header("HTTP/1.1 200 OK");

        $this->session->set_userdata('bp_verifycode', $verify_code);
        $this->session->set_userdata('verify_phone', $receiver);
        $this->session->set_userdata('verify_code_lifetime', time() + $this->config->item('verify_code_lifetime'));
        $this->_data ['res'] = bp_operation_ok;
        //ob_start();
//        $this->_result(bp_operation_ok, array('verify_code' => $verify_code));
        echo json_encode([
            'res' => bp_operation_ok,
            'verify_code' => $verify_code
        ]);

        $size = ob_get_length();
        header("Content-Length: $size");
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
//        ob_end_flush();  //#输出当前缓冲
        flush();  //#输出PHP缓冲
        log_debug("echoed" . ob_get_level());

//                //直接执行，后面再通过异步脚本来补充未正常提交的处理
        ignore_user_abort(TRUE);
        set_time_limit(0);

//

        $res_send = $this->email_msg_model->send_signup_verify_code($verify_code, $receiver, $forgot_pwd);
        if ($res_send == DB_OPERATION_FAIL) {
            log_message('error', "user/get_verify_code:email->send($receiver,$verify_code);result:" . json_encode($res_send));
        }
        log_message('debug', 'Continue executing after redirect');
//        $size=ob_get_length();
//        header("Content-Length: $size");
//        ob_end_flush();#输出当前缓冲
//        flush();#输出PHP缓冲
//        //直接执行，后面再通过异步脚本来补充未正常提交的处理
//        ignore_user_abort ( TRUE );
//        set_time_limit ( 0 );
    }

    /**
     * 发送手机验证码
     *
     */
    private function _send_verify_code($receiver, $forgot_pwd = false)
    {

//                if ((int)$this->session->userdata('verify_code_lifetime') > time()) {
//                    $this->_error(bp_operation_fail, $this->lang->line('error_verify_code_early'));
//                }
        //@todo 对每个手机号作频次限制,60s内不能重复获取.redis配合支持
        $this->load->model('mobile_msg_model', 'message');
        $this->load->helper('string');
        $verify_code = random_string('numeric', 4);
        log_message('debug', 'verify_code:' . $verify_code);
        if ($forgot_pwd) {
            $res_send = $this->message->send_forgot_pwd_verify_code($verify_code, $receiver);
        } else {
            $res_send = $this->message->send_signup_verify_code($verify_code, $receiver);
        }
        //$this->_result(bp_operation_ok, $res_send);return;
        if ($res_send['code'] == DB_OPERATION_FAIL) {
            log_message('error', "user/get_verify_code:message->send($receiver,$verify_code);result:" . json_encode($res_send));
            $this->_data ['res'] = bp_operation_fail;
            $this->_data ['hint'] = '网络异常，请检查网络'; //$this->lang->line('error_verify_code_send_fail') . '(' . $res_send['hint'] . ')';
            $this->_error($this->_data['res'], $this->_data['hint']);
        }
        $this->session->set_userdata('bp_verifycode', $verify_code);
        $this->session->set_userdata('verify_phone', $receiver);
        $this->session->set_userdata('verify_code_lifetime', time() + $this->config->item('verify_code_lifetime'));
        $this->_data ['res'] = bp_operation_ok;
	//$this->_data['verify_code'] = $verify_code;
        $this->_result(bp_operation_ok, array('verify_code' => $verify_code));
    }

    /**
     * 修改用户信息，可以修改任意字段，任意组合
     * @return json
     */
    public function supply_detail()
    {
        $this->_need_login(true);
        if ($this->form_validation->run('user_update') == FALSE) {
            //echo validation_errors();
            if ($this->input->get_post(RETURN_TYPE) == 'json') {
                $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
                return;
            }

        }
        $filter_array = array('facepic', 'nickname', 'password', 'name', 'gender', 'age', 'city', 'introduce', 'description', 'usertype', 'level', 'cover', 'follower_num', 'following_num', 'boost');
//        $input = array_merge($_GET,$_POST);
        $up_data = filter_data($this->input->post(), $filter_array, false);
        $up_data['gender'] = $this->input->post('gender');  //gender may be omited when is 0
        $up_data['rank'] = element('boost', $up_data, 0) + (int)$this->input->get_post('rank_other');  //rank-其他rank值即boost值

        //update role
        $userid = $this->userid;
        if ($this->is_admin && $this->has_admin_role(ADMIN_ROLE_USER)) {  //Admin can modify user's info
            $userid = $this->input->get_post('userid') ? $this->input->get_post('userid') : $this->userid;
        }

        if (empty($userid)) {
            $this->_error(bp_operation_verify_fail, $this->lang->line('userid_is_null'));
        }

        $res = $this->user_model->update_user($userid, $up_data);

        if ($this->is_admin && $this->has_admin_role(ADMIN_ROLE_USER)) {  //Admin can modify user's info
            $this->admin_trace('u_' . $userid, 'update_user_info', $up_data);
        }
        //Update rongyun token info
        if (!empty($up_data['facepic']) || !empty($up_data['nickname'])) {
            $this->load->library('rongyun');
            $this->rongyun->userRefresh($userid, element('nickname', $up_data, $this->config->item('default_nickname')), element('facepic', $up_data, $this->config->item('default_facepic')));
        }


        $this->_deal_res($res);
    }

    function set_role()
    {
        $this->form_validation->set_rules('userid', '用户id', 'trim|required');
        $this->form_validation->set_rules('role', '权限', 'trim|required|numeric');

        if ($this->form_validation->run() == false) {
            $this->_data ['res'] = bp_operation_verify_fail;
            $this->_data['hint'] = '验证失败:' . validation_errors();
            $this->_error($this->_data ['res'], $this->_data['hint']);
        } else {

            if (!$this->has_admin_role(ADMIN_ROLE_ENTRANCE + ADMIN_ROLE_USER + ADMIN_ROLE_USER_PRIVILEGE)) {
                $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
            }

            $userid = $this->input->post('userid');
            $role = (int)$this->input->post('role');
            $res = $this->user_model->set_role($userid, $role);
            if ($res == DB_OPERATION_OK) {
                $this->_data['res'] = bp_operation_ok;
                $this->admin_trace('admin_' . $userid, 'update_user_role', $this->_data);
                $this->_success();
            } else {
                $this->_error(bp_operation_db_got_fail, $this->lang->line('bp_operation_fail_hint'));
            }
        }

    }

    /**
     * 原密码修改密码
     */
    public function change_password()
    {

        $return_type = $this->input->get_post(RETURN_TYPE);
        if ($return_type == 'json') {
            $_POST = $_REQUEST;
        }
        $this->form_validation->set_rules('new_pass', '新密码', 'trim|alpha_dash|required|min_length[4]|max_length[16]');
        $this->form_validation->set_rules('old_pass', '现有密码', 'trim|callback_checkpass');
        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        } else {
            $data ['password'] = $this->input->get_post('new_pass');
            $db_res = $this->user_model->update_user($this->userid, $data);
            $this->_deal_res($db_res);
        }
    }

    /**
     * 忘记密码 修改密码
     */
    public function set_new_pwd()
    {

        $return_type = $this->input->get_post(RETURN_TYPE);
        if ($return_type == 'json') {
            $_POST = $_GET + $_POST;
        }
        $this->form_validation->set_rules('username', '手机号', 'trim|required|min_length[4]|max_length[20]|alpha_dash|prep_for_form');
        $this->form_validation->set_rules('password', '新密码', 'trim|alpha_dash|required|min_length[4]|max_length[16]');
        $this->form_validation->set_rules('verify_code', '验证码', 'trim|code_verify[username]');
        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        } else {
            $data ['password'] = $this->input->get_post('password');
            $username = $this->input->get_post('username');
            $db_res = $this->user_model->update_user_by_username($username, $data);
            if ($db_res) {
                //auto login
                $this->login();    //set new pwd
            } else {
                $this->_error(bp_operation_db_got_fail, $this->lang->line('bp_operation_db_got_fail_hint'));
            }
        }
    }

    /**
     * 验证密码
     * @return bool
     */
    function checkpass()
    {
        $uq = $this->user_model->get_user($this->userid);

        $server_oldpass = $uq ['password'];

        if (User_model::password_encode($this->input->get_post('old_pass')) != $server_oldpass) {
            $this->form_validation->set_message('checkpass', '%s输入不正确');
            return FALSE;
        };
        return TRUE;
    }

    /**
     * 关注验证用户id
     * @param $userids
     */
    private function _check_userid(&$userids)
    {
        foreach ($userids as $key => $uid) {
            if (empty($uid) || is_array($uid)) {
                $this->_error(bp_operation_verify_fail, $this->lang->line('bp_operation_verify_fail_hint'));
                break;
            }
            if ($uid == $this->userid) {
                $this->_error(bp_operation_verify_fail, "Can't follow self");
                break;
            }
        }

    }

    /**
     * Follow users or tags
     * @param usernames
     * @param tags
     * @return json
     */
    public function follow()
    {
        $userids = $this->input->get_post('userids');
	//$userids = json_decode($userids, true);
        $this->_check_userid($userids);
        if ($userids) {
            $res = $this->user_model->set_follower($this->userid, $userids);
        }
        $tags = $this->input->get_post('tags');
        if ($tags) {
            $res = $this->user_model->set_follow_tags($this->userid, $tags);
        }
        $this->_deal_res($res);
    }

    /**
     * unFollow users or tags
     * @param userid
     * @param tags
     * @return json
     */
    public function unfollow()
    {
        $userids = $this->input->get_post('userids');
        $this->_check_userid($userids);
        if ($userids) {
            $res = $this->user_model->remove_follower($this->userid, $userids);
        }
        $tags = $this->input->get_post('tags');
        if ($tags) {
            $res = $this->user_model->remove_follow_tag($this->userid, $tags);
        }
        $this->_deal_res($res);
    }

    /**
     * 我关注的用户列表
     */
    public function get_follow_users()
    {
        $this->_get_follow_users('follow');
    }

    /**
     * 我的粉丝
     */
    public function get_fans()
    {
        $this->_get_follow_users('fans');
    }

    private function _get_follow_users($type)
    {
        $userid = $this->userid;
        if ($this->input->get_post('userid')) {
            $userid = $this->input->get_post('userid');
        }
        //support get list by tag or category

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        if ($type == 'follow') {

            $res = $this->user_model->get_follow_users($userid, $offset, $limit);
        }
        if ($type == 'fans') {

            $res = $this->user_model->get_fans($userid, $offset, $limit);
        }


        if (!empty($res['list'])) {
            $cu_followed_users = array();
            if ($this->userid && $this->is_login) {
                $res2 = $this->user_model->get_follow_users($this->userid, $offset, 0);//get all
                if (!empty($res2['list'])) {
                    $cu_followed_users = array_column($res2['list'], 'follow_userid');
                }
//                var_dump($cu_followed_users);

            }

            $res['count'] = count($res['list']);
            foreach ($res['list'] as $key => $row) {
                $res['list'][$key] = filter_data($row, $this->config->item('json_filter_user_and_followers'));
                $res['list'][$key]['is_followed'] = in_array($row['follow_userid'], $cu_followed_users) ? FOLLOW_STATUS_FOLLOWED : FOLLOW_STATUS_NOT_FOLLOW;   //Follow or not
            }
            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(bp_operation_db_not_find, $this->lang->line('hint_empty_follow_users'));
        }
    }


    /**
     * 我关注的tag列表
     */
    public function get_follow_tags()
    {
        $userid = $this->userid;
        if ($this->input->get_post('userid')) {
            $userid = $this->input->get_post('userid');
        }
        //support get list by tag or category

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $res = $this->user_model->get_follow_tags($userid, $offset, $limit);
        if (!empty($res['list'])) {
            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(bp_operation_db_not_find, $this->lang->line('hint_empty_follow_tags'));
        }
    }


    /*
     * : sinfores：< student-info-result：0表示操作成功，其他表示失败，后续版本可以具体定义错误码>，
     * hint：<hint：操作失败的提示原因，成功时不需要这个域>，
     * viewpm:<view-permission，查看权限，非登录用户：0，登录用户：1，有关系：2>,
     * username：<username>，
     *  realname：<realname>，
     *  school：<school-name>，grade：<grade-name，譬如初中三年级>，telephone：<telephone-number>，email：<email-address>，father：<parent-name>，fatherphone：<telephone of father>，intro：<other-info submitted when student registering>，status：<status of student，3表示正在接受辅导，即忙，不能打扰，2表示已经在客户端登陆，1表示在正在使用WEB服务，0：表示不在线，也没有使用WEB服务>
     */
    public function info()
    {
        $userid = $this->input->get_post('userid');
        $level = $this->input->get_post('basic') ? 'basic' : 'detail';
        if (empty($userid)) {
            if ($this->userid) {
                $userid = $this->userid;

            } else {
                $this->_error(bp_operation_user_notlogin, $this->lang->line('bp_operation_user_notlogin_hint'));
            }
        }
        /**
         * $permission = get_permission($is_login,$username,$student)
         * $tdetail_data = get_student_detail($permission,$student_id)
         * $data_hicracy=$tdetail_data['data_hicracy']
         * Enter description here ...
         * @var unknown_type
         */

        $studentinfo = $this->user_model->info($userid, $level);

        if ($studentinfo == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_db_not_find, $this->lang->line('hint_get_userinfo_fail'));
        }

        if ($level == 'basic') {
            $this->_result(bp_operation_ok, filter_data($studentinfo, $this->config->item('json_filter_user_info_basic')));
            return false;
        }

        if ($this->is_login) {
            //是否是自己的判断
            $studentinfo['is_self'] = (string)(int)($this->userid == $userid);
            //是否关注了的判断
            $studentinfo['is_followed'] = $this->user_model->get_follow_status($this->userid, $userid);
        } else {
            //是否是自己的判断
            $studentinfo['is_self'] = '0';
            //是否关注了的判断
            $studentinfo['is_followed'] = FOLLOW_STATUS_NOT_FOLLOW;
        }


        $this->_data ['res'] = bp_operation_ok;
        $this->_data['title'] = '学生信息';
        $this->_data['datalevel'] = USERINFO_VIEW_PERMISSION_NORMAL;
        $this->_data['student'] = $studentinfo;


        if ($this->input->get_post(RETURN_TYPE) == 'json') {
            $this->_result(bp_operation_ok, filter_data($studentinfo, $this->config->item('json_filter_user_info')));
        } else {

            $this->template('student/info_html', $this->_data);

        }


    }

//认证设计师
    public function applicant($action)
    {
        switch ($action) {
            case "apply":
                //..
                $this->layout->set_layout('layout/mobile');
                $this->setOutputTpl('user/applycert');
                $this->_result(bp_operation_ok, $this->_data);

                break;
            case "submit":

                $this->_validation(array(
                    array('brand_name', '品牌名', 'trim|required|max_length[30]'),
                    array('name', '姓名', 'trim|required|max_length[10]'),
                    array('telephone', '电话', 'trim|required|alpha_dash'),
                ));
                $filter = array('brand_name', 'name', 'telephone');
                $data = filter_data($this->input->post(), $filter, false);
                $data['userid'] = $this->userid;

                $this->load->model('applicant_model');
                $res = $this->applicant_model->add($data);
                $this->_deal_res($res);


                break;
            case "refuse":
                //..
                break;
            case "refuse":
                //..
                break;
        }

    }

    /**
     * 发现里面的设计师列表
     */
    public function designers()
    {
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $order = $this->input->get_post('od');

        $permit_orders = array('userid', 'rank');
        if (!in_array($order, $permit_orders)) {
            $order = null;
        }

        $filter = array(
            TBL_USER . '.usertype' => USERTYPE_DESIGNER,
//            'need_top' => true
            TBL_USER.'.isblocked' => 0
        );
        $res = $this->user_model->get_list($offset, $limit, $filter, $order);
        if (!empty($res['list'])) {

            $cu_followed_users = array();
            if ($this->userid && $this->is_login) {
                $res2 = $this->user_model->get_follow_users($this->userid, $offset, 0);//get all
                if (!empty($res2['list'])) {
                    $cu_followed_users = array_column($res2['list'], 'follow_userid');
                }

            }

            foreach ($res['list'] as $key => $row) {
                if (array_key_exists('need_top', $filter)) {
                    $row['top_three'] = json_decode($row['top_three']);
                    $row['tags'] = json_decode($row['tags']);
                }
                $res['list'][$key] = filter_data($row, $this->config->item('json_filter_designer_top'));
                $res['list'][$key]['is_followed'] = in_array($row['userid'], $cu_followed_users) ? FOLLOW_STATUS_FOLLOWED : FOLLOW_STATUS_NOT_FOLLOW;   //Follow or not

                //cover
                if( $row['cover']=='' ){
                    $res['list'][$key]['cover'] = $this->config->item('aliyun_oss_img_service_url').'/avatar/default_cover.png';
                }
            }

            $res['count'] = count($res['list']);

            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function recommend_designers()
    {


        $data = array();

        ///fixed
        $data['fixed']['hello'] = $this->config->item('recommend_follow_word');

        $filter = sprintf('(userid in (%s)) ', implode(',', $this->config->item('recommend_follow_userids')));
//        var_dump($filter);
        //$res = $this->user_model->get_list(0, 0, $filter); /* 暂时保留此数据 */
        $res = $this->user_model->get_list_v2(0, 40, $filter, NULL, 0, 1);

        //$data['fixed']['list'] = array();
        //if (!empty($res['list'])) {
            //foreach ($res['list'] as $key => $row) {
                //$data['fixed']['list'][] = filter_data($row, $this->config->item('json_filter_designer_top'));
            //}


       // }
//        $data['fixed']['list'][] = filter_data($row, $this->config->item('json_filter_designer_top'));

        //not fixed
        //$filter = array(
          //  TBL_USER . '.usertype' => USERTYPE_DESIGNER,
       // );

        //$res = $this->user_model->get_list(0, 6, $filter);
        if (!empty($res['list'])) {


            foreach ($res['list'] as $key => $row) {
//                if ($key < 2) {
//                    $data['fixed']['list'][] = filter_data($row, $this->config->item('json_filter_designer_top'));
//                } else {
////                    var_dump($this->config->item('json_filter_designer_top'));

                $data['list'][] = filter_data($row, $this->config->item('json_filter_designer_top'));
//                }

            }

            $data['count'] = count($res['list']);
 
            // 判断用户是否关注了推荐品牌  by fisher at 2017-03-22
            $followedList = $this->user_model->get_followed_list($this->userid);
	    $order = array();
            foreach ($data['list'] as $k => $v) {
		$order[] = $v['nickname'];
                $data['list'][$k]['is_followed'] = 0;
                foreach ($followedList as $x => $y) {
                    if ($v['userid'] == $y['follow_userid']) {
                        $data['list'][$k]['is_followed'] = 1;
                        break;
                    }
                }
            }
	    // sort by nickname
	    asort($order);
	    foreach ($order as $k => $v) {
		$newList[] = $data['list'][$k];
	    }
	    $data['list'] = $newList;
            $this->_result(bp_operation_ok, $data);

        } else {
            log_message('error', 'No user list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    /**
     * 绑定支付宝账号信息
     */
    public function  insert_alipay()
    {
        $this->_need_login(true);
//        $user_id = (int)$this->input->get_post('user_id');
        $alipay_num = (string)$this->input->get_post('alipay_num');

        //TODO 验证支付宝账号是否正确
        $this->_validation([
            ['alipay_num', 'alipay num', 'trim|required|max_length[80]'],
        ]);


        $result = $this->user_model->insert_alipay($this->userid, $alipay_num);
        switch ($result) {
            case DB_OPERATION_OK:
                $this->_success();
                break;
            case DB_OPERATION_DATA_EXIST:
                $this->_error(bp_operation_user_alipay_exist, $this->lang->line('error_user_alipay_dup'));
                break;
            default:
                $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
                break;
        }

    }

    /**
     * 获取用户的alipay信息
     */
    public function get_user_alipay()
    {
        $this->_need_login(true);
        $user_id = (int)$this->input->get_post('user_id');
        $result = $this->user_model->get_user_alipay($user_id ? $user_id : $this->userid);
        if (!$result) {
            $this->_error(bp_operation_fail, '未绑定账号信息');
        }
        $this->_result(bp_operation_ok, array('list' => $result));
    }

    /**
     * 新增设计师店铺地址信息
     *
     * @param $user_id 用户的id
     * @param $city 城市信息
     * @param $address 地址信息
     * @param $lng 经度信息
     * @param $lat 维度信息
     * @param $is_default 是否为默认
     */
    public function add_designer_address()
    {
        $this->_need_login(true);
        $data['user_id'] = (int)$this->input->get_post('user_id') ? $this->input->get_post('user_id') : $this->userid;
        $data['city'] = $this->input->get_post('city');
        $data['address'] = $this->input->get_post('address');
        $data['lng'] = $this->input->get_post('lng');
        $data['lat'] = $this->input->get_post('lat');
        $data['is_default'] = $this->input->get_post('is_default');

        #检验用户是否为设计师
        $userData = $this->user_model->get_user($data['user_id']);
        if (!in_array($userData['usertype'], array(USERTYPE_DESIGNER, USERTYPE_ADMIN))) {
            $this->_error(bp_operation_fail, '权限不足');
        }
        #插入数据库
        $result = $this->user_model->add_designer_address($data);
        if ($result == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, '数据插入失败');
        }
        $this->_success();
    }

    public function get_designer_address(){
        $this->_need_login(true);
        $data['user_id'] = (int)$this->input->get_post('user_id');
        $data['lm'] = (int)$this->input->get_post('lm') ? $this->input->get_post('lm') : 1;
        $data['of'] = (int)$this->input->get_post('of') ? $this->input->get_post('of') : 0;
        if(empty($data['user_id'])){
            $this->_error(bp_operation_fail, '参数错误');
        }
        $result = $this -> user_model -> get_designer_address($data['user_id'], $data['of'], $data['lm']);
        $this->_result(bp_operation_ok, array('list'=>$result));
    }
   
   // test for self delete user
   //public function self_del()
   //{
	//$username = $this->input->get_get('username');
	//if (empty($username)) {
		//$this->_error(bp_operation_fail, 'miss username');
	//}
	//$result = $this->user_model->del_user_by_self($username);
	//$this->_result(bp_operation_ok, array('res'=>$result);
   //}
  
    public function md_pwd()
    {
	$password = $this->input->get_post('pwd');
	$pwd = md5($password.'&*xc_@12');

	exit(json_encode(['pwd'=>$pwd]));	
    }

    public function getCoupon($userid)
    {
	$this->load->model('coupon_model', 'coupon');
	$list = $this->coupon->getCouponList(COUPON_TYPE_NEWER);
	if ($list) {
	    if (($list[0]['reg_end'] < time()) || ($list[0]['reg_at'] > time())) {
		logger('User_Signup_getCoupon_error:新手注册券领取时间过早或过晚'); // error info
		return false;
	    }
	    if ($list[0]['created_sum'] < $list[0]['geted_sum']) {
		logger('User_Signup_getCoupon_error:新手注册券可领用数量不足！领券失败！'); // error info
		return false;
	    }
	    $res = $this->coupon->getCouponByUserid($list[0]['id'], $userid, 1, 1);
	    if ($res) {
		$this->coupon->update_sum($list[0]['id'], 'geted_sum', 1);
		logger('User_Signup_getCoupon_success:新手注册券,领券成功！'); // success info
            } else {
		logger('User_Signup_getCoupon_error:新手注册券,领券失败！'); // error info
	    }
        } else {
	    logger('User_Signup_getCoupon_error:未设置新手注册券！领券失败！'); // error info
	}

	return true;
    }

    public function getRefereeCoupon($username)
    {
	//logger('refername: '.$username); //debug
	$this->load->model('coupon_model', 'coupon');
        $list = $this->coupon->getCouponList(COUPON_TYPE_NEWER);
        if ($list) {
	    $getCoupons = $this->coupon->getRefereeCoupon($username, $list[0]['id'], $list[0]['reg_at'], $list[0]['reg_end']);
	    logger('Got coupon nums: '.$getCoupons); //debug
	    if (($list[0]['repeat'] == 0) && ($getCoupons >= 1)) {
		logger('User_Signup_getRefereeCoupon_error:新手注册券不能累计领取， 已经领取过了！'); // error info
		return false;
	    }
	    $reg_users = $this->user_model->getRefereeRegUsers($username, $list[0]['reg_at'], $list[0]['reg_end']);
	    logger('recommond user nums: '.$reg_users); //debug
	    $max = floor($reg_users/$list[0]['reg_min']);
	    logger('Could take coupon nums: '.$max); //debug
	    if ($max <= $getCoupons) {
		logger('User_Signup_getRefereeCoupon_error:推荐注册用户数量不足，不能领取优惠券！'); // error info
		return false;
	    }
	    if (($list[0]['repeat'] == 1) || (($list[0]['repeat'] == 0) && ($getCoupons == 0))) {
		$num = ($list[0]['repeat'] == 0) ? 1 : ($max - $getCoupons);
		$res = $this->coupon->getCouponByMobile($list[0]['id'], $username, $num, 1);
		if ($res) {
		    $this->coupon->update_sum($list[0]['id'], 'geted_sum', $num);
                    logger('User_Signup_getRefereeCoupon_success:推荐注册,领券成功！'); // success info
            	} else {
                    logger('User_Signup_getRefereeCoupon_error:推荐注册,领券失败！'); // error info
                }
	    } else {
		logger('User_Signup_getRefereeCoupon_error:新手注册券不能累计领取， 已经领取过了！'); // error info
	    }
	} else {
            logger('User_Signup_getRefereeCoupon_error:未设置新手注册券！'); // error info
        }	

	return true;
    }

    public function getUserMobile()
    {
	$userid = $this->input->get_post('referer');
	$rt = $this->input->get_post('rt');
	if (is_numeric($userid)) {
	    $userinfo = $this->user_model->get_user($userid);
	    if (is_array($userinfo) && isset($userinfo['username']) && is_numeric($userinfo['username'])) {
		$res = ['res' => 0, 'hits' => 'success', 'mobile' => $userinfo['username']];
	    } else {
		$res = ['res' => 2, 'hits' => '用户不存在或未设置手机号'];
	    }
	} else {
	    $res = ['res' => 1, 'hits' => 'referer参数类型不正确，请传入int类型'];
	}

	if ($rt == 'jsonp') {
	    $callback = $this->input->get_post('callback');
            exit($callback."(".json_encode($res).");");
	} else {
	    exit(json_encode($res));
	}
    }
}
