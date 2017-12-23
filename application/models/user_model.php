<?php

class User_model extends MY_Model
{

    const TBL_USERS = TBL_USER;
    const PASSWORD_RANDOM = '&*xc_@12';

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        log_message('debug', "Users Model Class Initialized");
    }

    /**
     * 获取单个用户信息
     *
     * @access public
     * @param int $username 用户id
     * @return array - 用户信息
     */
    public function get_user_by_username($username)
    {
        $data = array();
        if (empty($username)) {
            return false;
        }

        $this->db_slave->select('*')->from(self::TBL_USERS)->where('username', $username)->limit(1);
        $query = $this->db_slave->get();
        if ($query->num_rows() == 1) {
            $data = $query->row_array();
        }
        $query->free_result();

        return $data;
    }

    public function get_user($userid)
    {
        $data = array();
        if (empty($userid)) {
            return false;
        }
        $this->db_slave->select('*')->from(self::TBL_USERS)->where('userid', $userid)->limit(1);
        $query = $this->db_slave->get();
        if ($query->num_rows() == 1) {
            $data = $query->row_array();
        }
        $query->free_result();

        return $data;
    }

    /*
     * 检查用户是否存在，存在返回true，否则返回false
     */
    public function check_user($username)
    {
        $exists = false;

        $this->db_slave->select('*')->from(self::TBL_USERS)->where('username', $username)->limit(1);
        $query = $this->db_slave->get();
        if ($query->num_rows() == 1) {
            $exists = true;
        }
        $query->free_result();

        return $exists;
    }

    /**
     * 获取批量用户在线状态信息
     *
     * @access public
     * @param array $username 用户id
     * @return array - 用户信息
     */
    public function get_status_in_username($username)
    {
        $data = array();

        $this->db_slave->select('username,status')->from(self::TBL_USERS)->where_in('username', $username);
        $query = $this->db_slave->get();
        if ($query->num_rows() > 0) {
            $data = $query->result_array();
        }
        $query->free_result();

        return $data;
    }

    /**
     * 获取所有用户信息
     *
     * @access public
     * @return array - 用户信息
     */
    public function get_users()
    {
        return $this->db_slave->get(self::TBL_USERS);
    }


    /**
     * 获取所有pad的学生用户
     */
    public function get_pad_users($offset = 0, $limit = 20, $renew = false)
    {
        $this->db_slave->where('usertype', USERTYPE_USER);
        $this->db_slave->where('isblocked !=', '1');
        $this->db_slave->where('last_DeviceInfo !=', '');
        if ($renew === true) {
            $this->db_slave->where_in('from', array('an_pad', 'anpad_hd'));
        } else {
            $this->db_slave->where('from', 'an_pad');
            $this->db_slave->where('device is NULL');
        }
        $this->db_slave->order_by('regtime', 'desc');
        $this->db_slave->offset($offset);
        $this->db_slave->limit($limit);
        $query = $this->db_slave->get(self::TBL_USERS);
        //echo $this->db_slave->last_query();

        if ($query && $query->num_rows() > 0) {
            $res = $query->result_array();
            $query->free_result();
        } else {
            $res = DB_OPERATION_FAIL;
        }
        return $res;
    }

    public function get_pad_users_sum($renew = false)
    {
        $this->db_slave->where('usertype', USERTYPE_USER);
        $this->db_slave->where('isblocked !=', '1');
        $this->db_slave->where('last_DeviceInfo !=', '');
        //operation by command
        if (!empty($renew)) {
            $this->db_slave->where_in('from', array('an_pad', 'anpad_hd'));
        } else {
            $this->db_slave->where('from', 'an_pad');
            $this->db_slave->where('device is NULL');
        }
        return $this->db_slave->count_all_results(TBL_USER);
    }

    /**
     * 删除一个用户
     *
     * @access public
     * @param int - $username 用户id
     * @return boolean - success/failure
     */
    public function remove_user($userid)
    {
        $this->db_master->delete(self::TBL_USERS, array('userid' => intval($userid)));

        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;
    }

    public static function password_encode($pwd){
//        return md5($pwd);  //正式上线需要价格随机码
        return md5($pwd . self::PASSWORD_RANDOM);
    }

    /**
     * 添加一个用户
     *
     * @access public
     * @param int - $data 用户信息
     * @return boolean - success/failure
     */
    public function add_user($data)
    {
        if (array_key_exists('password', $data)) {
            $data['password'] = self::password_encode($data['password']);
        }
        $data['regtime'] = standard_date('DATE_MYSQL');
        if(!in_array('facepic',$data)) {
            $data['facepic'] = 'http://product-album.img-cn-hangzhou.aliyuncs.com/avatar/default_avatar.png';
        }
        $this->db_master->insert(self::TBL_USERS, $data);

        return ($this->db_master->affected_rows() > 0) ? $this->db_master->insert_id() : FALSE;
    }

    /**
     * 修改用户信息
     *
     * @access public
     * @param int - $username 用户
     * @param array - $data 用户信息
     * @return boolean - success/failure
     */
    public function update_user($userid, $data,$by_username = false)
    {
        if (array_key_exists('password', $data)) {
            $data['password'] = self::password_encode($data['password']);
        }
        if($by_username){
            $this->db_master->where('username', (string)$userid);    //Same as login
        }else{
            $this->db_master->where('userid', (int)$userid);    //Same as login
        }

        $this->db_master->update(self::TBL_USERS, $data);

        return ($this->db_master->modified_rows() > 0) ? TRUE : FALSE;
    }
    /**
     * 修改用户信息
     *
     * @access public
     * @param int - $username 用户
     * @param array - $data 用户信息
     * @return boolean - success/failure
     */
    public function update_user_by_username($username, $data)
    {
       return $this->update_user($username,$data,true);
    }

    /**
     * 检查是否存在相同{用户名/昵称/邮箱}
     *
     * @access public
     * @param int - $key {name,screenName,mail}
     * @param int - $value {用户名/昵称/邮箱}的值
     * @param int - $exclude_uid 需要排除的uid
     * @return boolean - success/failure
     */
    public function check_exist($key = 'name', $value = '', $exclude_uid = 0)
    {
        if (!empty($value)) {
            //fix issue 2
            $this->db_slave->select('username')->from(self::TBL_USERS)->where($key, $value);

            if (!empty($exclude_uid) && is_numeric($exclude_uid)) {
                $this->db_slave->where('uid <>', $exclude_uid);
            }

            $query = $this->db_slave->limit(1)->get();
            $num = $query->num_rows();
            $query->free_result();

            return ($num > 0) ? TRUE : FALSE;
        }

        return FALSE;
    }


    /**
     * 检查用户是否存在
     * todo:需要权限判断，需要看用户是否是被封禁
     *
     * @access public
     * @param string - $username 用户名
     * @param string - $password 密码
     * @return mixed - FALSE/true
     */


    function checkuser_is_ok($param = array('username' => 's', 'password' => 's'))
    {

        $this->db_slave->where('username', $param['username']);

        $this->db_slave->where('password', self::password_encode($param['password']));
        $this->db_slave->where('isblocked !=', 1);

        $query = $this->db_slave->get(self::TBL_USERS);

        log_message('debug', 'query username,passwd from ' . self::TBL_USERS);


        $row = get_row_array($query);

        if (empty($row)) {
            return false;
        }
        return $row;
    }


    //获得用户登录失败的信息，看是否需要出验证码，如果失败次数超过5次，则禁止登录2个小时
    function get_check_list_info($username)
    {
        $time = strtotime('-2 hour');
        $query = $this->db_slave->where('username', $username)->where('update_time >', standard_date('DATE_MYSQL', $time))->where('times >', 0)->get(TBL_USER_CHECK_LIST);
        $data = get_row_array($query);
        return $data;
    }

    // 登录封禁用户列表，2小时有效，可手动删除
    function fetch_check_list_users()
    {
        $time = strtotime('-2 hour');
        $query = $this->db_slave->where('update_time >', standard_date('DATE_MYSQL', $time))->where('times >', 0)->get(TBL_USER_CHECK_LIST);
        $list = get_query_result($query);
        return $list;
    }

    //更新用户失败次数，update_time会自动更新
    function update_check_list_times($username, $times)
    {
        if (empty($username)) {
            return;
        }
        if ($times < 0) {
            $times = 1;
        }

        $res = $this->db_master->where('username', $username)->update(TBL_USER_CHECK_LIST, array('times' => $times));

        if ($this->db_master->modified_rows() == 0) {
            $res = $this->db_master->insert(TBL_USER_CHECK_LIST, array('username' => $username, 'times' => $times));
        }
        return DB_OPERATION_OK;
    }

    public function set_follower($userid, $follows)
    {
        $vals = array();
        log_debug(json_encode($follows));
        foreach ($follows as $follow) {
            $vals[] = sprintf('(%d,%d)', $userid, $follow);
        }
        $sql = sprintf('INSERT IGNORE INTO `%s` (`userid`, `follow_userid`) values %s', TBL_USER_FOLLOW_USER, implode(',', $vals));
        $query = $this->db_master->query($sql);
        $num = $this->db_master->affected_rows();
        if ($query) {
            if ($num > 0) {

//更新我的关注数
                $sql = sprintf("update %s set following_num = (following_num + %d) where userid=%s", TBL_USER, $num, $this->db_master->escape($userid));
                $query = $this->db_master->query($sql);
                if (!$query || $this->db_master->modified_rows() < 0) {
                    log_error("update following count :$userid $follow");
                }

                //更新每个用户的粉丝数
                $sql = sprintf("update %s set follower_num = (follower_num + 1) where userid in (%s)", TBL_USER, implode(',', $follows));
                $query = $this->db_master->query($sql);
                if (!$query || $this->db_master->modified_rows() < 0) {
                    log_error("update follower count :$userid $follow");
                }

            }
	    // delete index brand cache
	    $this->load->library('cache');
	    $this->cache->delete(md5('gplc:'.$userid));
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;

    }

    public function remove_follower($userid, $follows)
    {
        $vals = array();
        log_debug(json_encode($follows));
        if (!is_array($follows)) {
            $follows = array($follows);
        }
        foreach ($follows as $follow) {
            $vals[] = sprintf('(%d,%d)', $userid, $follow);
        }
        $sql = sprintf('delete from `%s` where (`userid`, `follow_userid`) in (%s)', TBL_USER_FOLLOW_USER, implode(',', $vals));
        $query = $this->db_master->query($sql);
        $num = $this->db_master->affected_rows();
        if ($query) {
            if ($num > 0) {

//更新我的关注数
                $sql = sprintf("update %s set following_num = (following_num - %d) where userid=%s", TBL_USER, $num, $this->db_master->escape($userid));
                $query = $this->db_master->query($sql);
                if (!$query || $this->db_master->modified_rows() < 0) {
                    log_error("update following count :$userid $follow");
                }
//var_dump($follows);
                //更新每个用户的粉丝数
                $sql = sprintf("update %s set follower_num = (follower_num - 1) where userid in (%s)", TBL_USER, implode(',', $follows));
                $query = $this->db_master->query($sql);
                if (!$query || $this->db_master->modified_rows() < 0) {
                    log_error("update follower count :$userid $follow");
                }

            }
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;

    }
//    public function remove_follow($userid, $follow)
//    {
//        $this->db_master->where('userid',$userid)->where('follow_userid',$follow)->delete(TBL_USER_FOLLOW_USER);
//
//        $num = $this->db_master->affected_rows();
//        if ($num >0) {
//            $sql = sprintf("update %s set following_num = (following_num - %d) where userid=%s",TBL_USER,$num,$this->db_master->escape($userid));
//            $query = $this->db_master->query($sql);
//            if(!$query || $this->db_master->modified_rows()<0){
//                log_error("Remove following count :$userid $follow");
//            }
//            return DB_OPERATION_OK;
//        }
//        return DB_OPERATION_FAIL;
//    }


    public function set_follow_tags($userid, $tags)
    {
        $vals = array();
        foreach ($tags as $tag) {
            $vals[] = sprintf('(%d,%d)', $userid, $tag);
        }
        $sql = sprintf('INSERT IGNORE INTO `%s` (`userid`, `tag_id`) values %s', TBL_USER_FOLLOW_TAG, implode(',', $vals));
        $query = $this->db_master->query($sql);
        if ($query) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }


    public function remove_follow_tag($userid, $tags)
    {
        $vals = array();
        foreach ($tags as $tag) {
            $vals[] = sprintf('(%d,%d)', $userid, $tag);
        }
        $sql = sprintf('delete from `%s` where (`userid`, `tag_id`) in (%s) ', TBL_USER_FOLLOW_TAG, implode(',', $vals));
        $query = $this->db_master->query($sql);
        if ($query) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }


    public function get_follow_tags($userid, $offset = 0, $limit = 20, $filter = array())
    {
        $this->db_slave->start_cache();
        $this->db_slave->where(TBL_USER_FOLLOW_TAG.'.userid', $userid);
        $this->db_slave->where($filter);
        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(TBL_USER_FOLLOW_TAG);

        $this->db_slave->join(TBL_TAGS,TBL_TAGS.'.id='.TBL_USER_FOLLOW_TAG.'.tag_id','left');

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        $this->db_slave->order_by(TBL_USER_FOLLOW_TAG.'.id', 'desc');  //Maybe sort by other field

        $query = $this->db_slave->get(TBL_USER_FOLLOW_TAG);

        $this->db_slave->flush_cache();
        return array('total' => $total, 'list' => get_query_result($query));
    }

    public function get_follow_users($userid, $offset = 0, $limit = 20, $filter = array(), $follow = true)
    {
        $this->db_slave->start_cache();
        $this->db_slave->select(TBL_USER_FOLLOW_USER . '.*,' . TBL_USER . '.usertype')->select(TBL_USER . '.facepic')->select(TBL_USER . '.nickname')->select(TBL_USER . '.level')->select(TBL_USER . '.follower_num')->select(TBL_USER . '.following_num')->from(TBL_USER_FOLLOW_USER);
        if ($follow) {

            $this->db_slave->join(TBL_USER, TBL_USER . '.userid = ' . TBL_USER_FOLLOW_USER . '.follow_userid', 'left');
            $this->db_slave->where(TBL_USER_FOLLOW_USER . '.userid', $userid);
        } else {
            $this->db_slave->join(TBL_USER, TBL_USER . '.userid = ' . TBL_USER_FOLLOW_USER . '.userid', 'left');
            $this->db_slave->where(TBL_USER_FOLLOW_USER . '.follow_userid', $userid);
        }
        $this->db_slave->where($filter);
        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(TBL_USER_FOLLOW_USER);

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        $this->db_slave->order_by(TBL_USER_FOLLOW_USER . '.id', 'desc');  //Maybe sort by other field

        $query = $this->db_slave->get(TBL_USER_FOLLOW_USER);

        $this->db_slave->flush_cache();
        return array('total' => $total, 'list' => get_query_result($query));
    }

    public function get_fans($userid, $offset = 0, $limit = 20, $filter = array())
    {
        return $this->get_follow_users($userid, $offset = 0, $limit = 20, $filter = array(), false);
    }

    /**
     * 是否有关注关系
     * @param $userid
     * @param $follow_userid
     * @return bool
     */
    public function get_follow_status($userid, $follow_userid)
    {
        $this->db_slave->where('follow_userid', $follow_userid);
        $this->db_slave->where('userid', $userid);
        $query = $this->db_slave->get(TBL_USER_FOLLOW_USER);

        $status = ($query->num_rows() == 1) ? FOLLOW_STATUS_FOLLOWED : FOLLOW_STATUS_NOT_FOLLOW;
        $query->free_result();
        if ($status == FOLLOW_STATUS_FOLLOWED) {
            $this->db_slave->where('follow_userid', $userid);
            $this->db_slave->where('userid', $follow_userid);
            $query = $this->db_slave->get(TBL_USER_FOLLOW_USER);
            if ($query->num_rows() == 1 && $status = FOLLOW_STATUS_FOLLOWED) {
                $status = FOLLOW_STATUS_MUTUAL;
            }
            $query->free_result();
        }
        return $status;

    }

    //检查是否老用户
    function checkuser_is_old($username, $datetime = '2013-09-24')
    {

        $this->db_slave->where('username', $username);
        $this->db_slave->where('regtime < \'' . $datetime . '\'');
        $query = $this->db_slave->get(self::TBL_USERS);

        $row = get_row_array($query);

        if (empty($row)) {
            return false;
        } else {
            return true;
        }
    }


    function login_process($param)
    {
        $userinfo = $this->checkuser_is_ok($param);
        if ($userinfo) {

            $userdata = array('username' => $this->input->post('username'), 'status' => 1, 'login_time' => standard_date('DATE_MYSQL'));
            $this->session->set_userdata($userdata);
            //todo flashdata
            //$this->session->set_flashdata('ok_info',"s:sss");
            $r = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : "/";
            redirect($r);
        } else //login failed
        {
            //TODO:record password wrong times through cookie;
            $times = $this->session->userdata('pass_wrong_times');

            $this->_data['E_fail'] = '用户名或者密码错误，请重试';


            //$this->session->set_userdata('pass_wrong_times',($times+1));
            echo "you have failed " . ($times + 1) . " times";
            //TODO 密码出错次数提示：可以通过ajax实现，这里返回状态码，js控制
        };
    }


    // 检查通过oauth2方式登录的情况
    function check_oauth_login($uid, $source)
    {
        $query = $this->db_slave
            ->where(TBL_USER_OAUTH.'.oid', $uid)->where(TBL_USER_OAUTH.'.source', $source)
            ->join(TBL_USER,TBL_USER.'.userid = '.TBL_USER_OAUTH.'.userid',left)
            ->get(TBL_USER_OAUTH);
        $row = get_row_array($query);

        return $row;
    }

    //用户登录默认的操作：修改session状态，修改用户记录等
    function user_login($username, $sessdata, $userdata)
    {
        $this->session->set_userdata($userdata);

    }


    /**
     *
     * get usertype by username
     * @param string $username
     * @return usertype | false
     */
    function get_usertype($username)
    {
        $query = $this->db_slave->select('usertype')->from(TBL_USER)->where('username', $username)->get();

        return ($query->num_rows() == 1) ? $query->row()->usertype : false;

    }

    /**
     *
     * get userid by username
     * @param string $un
     * @return userid
     */
    function get_userid($un)
    {
        $query = $this->db_slave->select('userid')->from(TBL_USER)->where('username', $un)->get();

        return ($query->num_rows() == 1) ? $query->row()->userid : '7314';

    }

    /**
     *
     * get username by userid
     * @param ini $userid
     * @return username || false
     */
    function get_username($uid = 0)
    {
        $query = $this->db_slave->select('username')->where('userid', $uid)->get(TBL_USER);

        return ($query->num_rows() == 1) ? $query->row()->username : '';
    }


    /**
     *
     * 记录管理员操作历史
     * @param string $username
     * @param string $info
     * @return bool
     */
    function admin_action_recored($admin = 'admin', $action = '', $target = '', $info = '')
    {
        $this->load->helper('url');

        $url = current_url();
        $ip = $this->input->ip_address();

        $data = array('admin' => $admin, 'action' => $action, 'target' => $target, 'info' => $info, 'ip' => $ip, 'url' => $url);
        $this->db_master->insert(TBL_ADMIN_ACTION_RECORD, $data);

        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;

    }


    /**
     *
     * 用户与设备信息对应关系记录
     * @param string $username
     * @param string $devicetoken
     * @return bool
     */
    function set_devicetoken($username, $devicetoken, $client = 'ipad', $uuid = false, $dinfo = false)
    {
        $query = sprintf('replace into %s (token ,username, update_time, client, uuid, dinfo ) values ("%s", "%s", "%s", "%s", "%s", "%s")', TBL_DEVICE_USER, $devicetoken, $username, standard_date('DATE_MYSQL'), $client, ($uuid === false ? '' : $uuid), ($dinfo === false ? '' : $dinfo));

        $this->db_master->query($query);

        if ($this->db_master->modified_rows() > 0) {
            return DB_OPERATION_OK;
        } else {
            return DB_OPERATION_FAIL;
        }
    }

    /**
     *
     * 获得用户的设备token
     * @param string $username
     * @param string $devicetoken
     * @return array (devicetoken)
     */
    function get_devicetokens($username)
    {
        $res = array();
        if (is_string($username)) {
            $this->db_slave->where('username', $username)->limit(2)->order_by('create_time', 'desc'); //最多同步２台设备
            $query = $this->db_slave->get(TBL_DEVICE_USER);
            if (false != $query and $query->num_rows() > 0) {
                $res = $query->result_array();
                $query->free_result();
            }
        }

        if (is_array($username)) {
            fix_where_in_array($username);
            foreach ($username as $user) {
                $res[$user] = array();
            }

            $query = $this->db_slave->where_in('username', $username)->order_by('create_time', 'desc')->order_by('update_time', 'desc')->get(TBL_DEVICE_USER);

            if (false !== $query and $query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    $res[$row['username']][] = $row;
                }

                $query->free_result();

            }
        }


        return $res;
    }

    function get_user_belong($username)
    {
        $query = $this->db_slave->select('belong')->where('username', $username)->get(TBL_USER);
        $row = get_row_array($query);
        if (!empty($row)) {
            return $row['belong'];
        }
        return '';
    }

    function username_sugguestion($name = '', $type = false)
    {
        $res = array();
        if (empty($name)) return $res;

        $this->db_slave->select('username')->like('username', $name)->limit(50);
        if ($type !== FALSE and !is_array($type)) {
            $this->db_slave->where('usertype', $type);
        }
        if (is_array($type)) {
            $this->db_slave->where_in('usertype', $type);
        }
        $query = $this->db_slave->get(TBL_USER);
        foreach ($query->result_array() as $item) {
            $res[] = $item['username'];
        }
        $query->free_result();
        return $res;
    }

    function get_admin_list()
    {
        $query = $this->db_slave->select('username , usertype')->where('usertype', USERTYPE_ADMIN)->get(TBL_USER);
        $res = array();
        foreach ($query->result_array() as $user) {
            $res[] = $user['username'];
        }
        return $res;

    }

    function get_user_role($userid)
    {
        $query = $this->db_slave->where('userid', $userid)->get(TBL_USER_ROLE);
        $role = 0;
        if ($query && ($query->num_rows() > 0)) {
            $row = $query->row();
            $role = (int)$row->role;
        }
        return $role;
    }

    function set_role($userid, $role = 0)
    {
        $sql = sprintf('replace into %s (userid , role ) values ("%s", %d)', TBL_USER_ROLE, $userid, $role);
        $this->db_master->query($sql);
        if ($this->db_master->modified_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    function get_role_userlist()
    {
        $query = $this->db_slave->get(TBL_USER_ROLE);
        $res = array();
        if ($query && $query->num_rows() > 0) {
            $res = $query->result_array();
            $query->free_result();
        }
        return $res;
    }


    /*取得推荐人信息*/
    function get_user_recommender($username)
    {
        $query = $this->db_slave->select('recommend_username')->where('username', $username)->limit(1)->get(TBL_USER);
        $data = get_row_array($query);
        return $data['recommend_username'];
    }
    public function oauth2_bind($userid, $unionid,$source='wx')
    {
        $query = $this->db_master->where('source',$source)->where('userid',$userid)->get(TBL_USER_OAUTH);
        if($query->num_rows()>0){
            return DB_OPERATION_DATA_EXIST;
        }
        $data = [
            'oid'=>$unionid,
            'source'=>$source,
            'userid'=>$userid,
        ];
        $this->db_master->insert(TBL_USER_OAUTH,$data);
        if ($this->db_master->affected_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }
    public function oauth2_unbind($userid,$source='wx')
    {
        $data = [
            'source'=>$source,
            'userid'=>$userid,
        ];
        $this->db_master->delete(TBL_USER_OAUTH,$data);
        if ($this->db_master->affected_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    function oauth2_add_relation($username, $usertype, $source, $token)
    {
        $this->db_master->insert(TBL_OAUTH_RELATION, array('username' => $username, 'usertype' => $usertype, 'source' => $source, 'oauth_token' => $token, 'status' => 0));
        if ($this->db_master->affected_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }


// 取得未处理的关联关系
    function oauth_get_undeal_relation($count = 10)
    {
        $query = $this->db_master->where('status', 0)->limit($count)->get(TBL_OAUTH_RELATION);
        return get_query_result($query);
    }

    function oauth_update_relation_status($id, $status = 1)
    {
        $data = array('status' => $status, 'update_time' => standard_date('DATE_MYSQL'));
        $this->db_master->where('id', $id)->update(TBL_OAUTH_RELATION, $data);
        if ($this->db_master->modified_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }


    function info($userid, $level = 'detail')
    {
        $this->db_slave->where(TBL_USER.'.userid', $userid);
        $this->db_slave->select(TBL_USER.'.*');
        if ($level == 'admin') {
            $this->db_slave->select(TBL_USER_ROLE.'.role');
            $this->db_slave->join(TBL_USER_ROLE, TBL_USER_ROLE . '.userid = ' . TBL_USER . '.userid', 'left');
        }

        $query = $this->db_slave->get(TBL_USER);
        $user_info = get_row_array($query);

        if (empty($user_info)) {
            log_message('error', 'Get product info error:' . $this->db_master->last_query());
            return DB_OPERATION_FAIL;
        }
        if ($level == 'detail' || $level == 'admin_detail') {
            //get tags info
            $query = $this->db_master->where(TBL_USER_FOLLOW_TAG . '.userid', $userid)
                ->join(TBL_TAGS, TBL_USER_FOLLOW_TAG . '.tag_id = ' . TBL_TAGS . '.id', 'left')->get(TBL_USER_FOLLOW_TAG);
            $tags = array();
            $res = get_query_result($query);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    $tags[] = array('id' => $row['id'], 'name' => $row['name'], 'description' => $row['description']);
                }
            }
            $user_info['tags'] = $tags;

            //get role info

        }

        return $user_info;
    }

    public function get_like_products($userid)
    {
        $query = $this->db_slave->select('product_id')->where('userid', $userid)->get(TBL_PRODUCT_LIKER);
        return get_query_result($query);
    }


    function get_admin_infolist($uns)
    {
        fix_where_in_array($uns);

        $query = $this->db_slave->where_in('username', $uns)->get(TBL_ADMIN_USER_INFO);
        $res = get_query_result($query);

        return $res;
    }


    function get_students_head_teacher($students)
    {
        if (!is_array($students)) {
            $students = array($students);
        }

        $query = $this->db_slave->select('student,head_teacher')->distinct()->where_in('student', $students)->get(TBL_TEACHER_STUDENT);

        $res = get_query_result($query);
        if ($res === false) return false;

        $list = array();
        foreach ($res as $row) {
            $list[$row['student']] = $row['head_teacher'];
        }

        return $list;
    }

    /**
     *拿到学生的班主任详细信息
     * return array('username'=>..,'realname'=>..)
     */
    function get_student_head_teacher_info($student)
    {
        $query = $this->db_slave->select(TBL_ADMIN_USER_INFO . '.*')->join(TBL_ADMIN_USER_INFO, TBL_TEACHER_STUDENT . '.head_teacher = ' . TBL_ADMIN_USER_INFO . '.username', 'left')->where(TBL_TEACHER_STUDENT . '.student', $student)->where(TBL_ADMIN_USER_INFO . '.username is not null')->limit(1)->get(TBL_TEACHER_STUDENT);
        $res = get_row_array($query);
        return $res;
    }


    //使用一个设备的注册的次数
    function count_fuuid($device_id)
    {
        if (empty($device_id)) {
            return 100;
        }
        $count = $this->db_slave->where('fuuid', $device_id)->count_all_results(TBL_USER);

        return $count;

    }

    //记录登录用户的device info

    function add_user_device($data)
    {

        $this->db_master->insert(TBL_USER_DEVICE_INFO, $data);

        if ($this->db_master->affected_rows() == 0) {
            log_message('error', 'record device info get wrong:' . json_encode($data));
        }

    }

    // 检查是否为合法的合作商户
    function is_valid_cooperation_code($ccode)
    {
        $this->db_slave->select('*')->from(self::TBL_USERS)->where('username', $ccode)->where('usertype', USERTYPE_BUSINESS)->limit(1);
        $query = $this->db_slave->get();
        return ($query->num_rows() == 1);
    }

    //设计师助手使用,获取负责账号
    public function get_responsible_userid($userid)
    {
        $query = $this->db_slave->where('userid', $userid)->get(TBL_USER_REP_USER);
        $res = get_row_array($query);
        return element('rep_userid', $res, false);
    }

    //设计师助手使用,获取负责账号
    public function get_assistants($userid)
    {
        $query = $this->db_slave->where('rep_userid', $userid)->get(TBL_USER_REP_USER);
        $res = get_query_result($query);
        return $res;
    }

    //设计师助手使用,获取负责账号
    public function is_my_assistant($userid, $assistant)
    {
        $host = $this->get_responsible_userid($assistant);
        return ($host == $userid);
    }

    public function get_list($offset = 0, $limit = 20, $filter = null, $order = null)
    {
        $this->db_slave->start_cache();
        $this->db_slave->select(TBL_USER.'.*');
        $this->db_slave->from(TBL_USER);
//        $this->db_slave->join(TBL_USER_FOLLOW_TAG,TBL_USER_FOLLOW_TAG.'.userid='.TBL_USER.'.userid','left')
//        ->join(TBL_TAGS,TBL_TAGS.'.id='.TBL_USER_FOLLOW_TAG.'.tag_id','left');
        if (!empty($filter)) {
            //需要显示top 3 商品
            if (is_array($filter) && array_key_exists('need_top', $filter)) {
//                $this->db_slave->select()
                $this->db_slave->select(TBL_DESIGNER_TOP.'.top_three,'.TBL_DESIGNER_TOP.'.product_count,'.TBL_DESIGNER_TOP.'.tags');
                $this->db_slave->join(TBL_DESIGNER_TOP, TBL_DESIGNER_TOP . '.userid = ' . TBL_USER . '.userid', 'left');
                unset($filter['need_top']);
            }
            $this->db_slave->where($filter);
        }
        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_USER);
        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (!empty($order)) {
            $this->db_slave->order_by(TBL_USER . '.' . $order, 'desc');  //Desc sort
        }
        //$this->db_slave->order_by(TBL_USER . '.userid', 'desc');  //Desc sort
        $query = $this->db_slave->get(TBL_USER);
        $this->db_slave->flush_cache();
        return array('total' => $total, 'list' => get_query_result($query));
    }
    public function get_list_v2($offset = 0, $limit = 20, $filter = null, $order = null, $type=0, $type_st=0)
    {
        $this->db_slave->start_cache();
        //$this->db_slave->select(TBL_USER.'.*');
        //$this->db_slave->from(TBL_USER);        
//        $this->db_slave->join(TBL_USER_FOLLOW_TAG,TBL_USER_FOLLOW_TAG.'.userid='.TBL_USER.'.userid','left')
//        ->join(TBL_TAGS,TBL_TAGS.'.id='.TBL_USER_FOLLOW_TAG.'.tag_id','left');
        if (!empty($filter) && $type==0 && $type_st==0) {
            //需要显示top 3 商品
            if (is_array($filter) && array_key_exists('need_top', $filter)) {
//                $this->db_slave->select()
                $this->db_slave->select(TBL_DESIGNER_TOP.'.top_three,'.TBL_DESIGNER_TOP.'.product_count,'.TBL_DESIGNER_TOP.'.tags');
                $this->db_slave->join(TBL_DESIGNER_TOP, TBL_DESIGNER_TOP . '.userid = ' . TBL_USER . '.userid', 'left');
                unset($filter['need_top']);
            }

            $this->db_slave->where($filter);
        }
        
        if( $type==2 ){
            $this->db_slave->where(array('usertype'=>2))->like($filter);
        }
        if( $type_st==1 ){
            $this->db_slave->where(array('istop >'=>1));
        }
        
        $this->db_slave->join('brand_icae', 'brand_icae.english_name = '.TBL_USER.'.nickname', 'left');
        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_USER);

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (!empty($order)) {
            $this->db_slave->order_by(TBL_USER . '.' . $order, 'desc');  //Desc sort
        }
        $this->db_slave->order_by(TBL_USER . '.userid', 'desc');  //Desc sort
        
        $query = $this->db_slave->get(TBL_USER);
        
        $this->db_slave->flush_cache();
        $data = array('total' => $total, 'list' => get_query_result($query));
        //$this->cache->add_list(self::LIST_KEY,$key,$data,self::CACHE_TIME);
        
        return $data;
        //return array('total' => $total, 'list' => get_query_result($query));
    }


    /**
     * @param $user_id
     * @param $alipay_num
     * @return int
     */
    public function insert_alipay($user_id, $alipay_num){
        #检查该用户是否已经绑定支付宝账号
        $query = $this->db_slave->where('user_id',$user_id)->get(TBL_USER_ALIPAY);
        $row = get_row_array($query);
        if($row) return DB_OPERATION_DATA_EXIST;

        #组合插入的信息
        $data = array('user_id' => $user_id, 'alipay_num' => $alipay_num);
        $this->db_master->insert(TBL_USER_ALIPAY, $data);

        if ($this->db_master->affected_rows() == 0) {
            log_message('error', 'record device info get wrong:' . json_encode($data));
            return DB_OPERATION_FAIL;
        }
        return DB_OPERATION_OK;
    }

    /**
     * 获取用户的alipay账户信息
     * @param $user_id 用户的ID
     * @return array
     */
    public function get_user_alipay($user_id){
        $query = $this->db_slave->select('alipay_num')->where('user_id',$user_id)->get(TBL_USER_ALIPAY);
        $row = get_row_array($query);
        return $row ? $row : array();
    }

    /**
     * 插入设计师的店铺地址
     */
    public  function add_designer_address($data){
        #如果是默认的店铺地址则该用户其他所有的地址都设置成非默认地址
        if($data['is_default'] == 1){
            $this->db_master->where('user_id', $data['user_id'])->update(TBL_DESIGNER_ADDRESS, array('is_default'=>0));
        }

        $this->db_master->insert(TBL_DESIGNER_ADDRESS, $data);
        if ($this->db_master->affected_rows() == 0) {
            log_message('error', 'add designer address is wrong:' . json_encode($data));
            return DB_OPERATION_FAIL;
        }
        return DB_OPERATION_OK;
    }

    public function get_designer_address($user_id, $of, $lm){
        $this->db_slave->select('*')->from(TBL_DESIGNER_ADDRESS)->where('user_id', $user_id)
            ->offset($of)->limit($lm)-> order_by('is_default desc, create_time desc ');
        $query = $this->db_slave->get();
        $list = get_query_result($query);
        $total = $this->db_slave->where('user_id',$user_id)->count_all_results(TBL_DESIGNER_ADDRESS);
        return array('total'=>$total, 'list'=>$list);
    }

    /**
     * 获取当前用户关注的品牌列表
     * @author fisher
     * @date 2017-03-22
     * @return array
     */
    public function get_followed_list($userid)
    {
        $this->db_slave->select('follow_userid')->from(TBL_USER_FOLLOW_USER)->where(array('userid' => $userid));
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        return $res;
    }

    /*
     * test ,for deleting user by self
     * by fisher at 2017-03-26
     */
     //public function del_user_by_self($name)
     //{
	//$this->db_slave->delete(self::TBL_USERS, array('username'=>$name));
	//return ($this->db_slave->affected_rows() > 0) ? TRUE : FALSE;
     //}

    public function get_follow_rows($userid)
    {
        $rows = $this->db_slave->where('follow_userid = ', $userid)
            ->count_all_results(TBL_USER_FOLLOW_USER);

        return $rows;
    }

    public function get_list_v3($type=2)
    {
        $this->db_slave->select('userid,nickname');
        $this->db_slave->where('usertype = ', $type);
        $query = $this->db_slave->get(TBL_USER);
        $res = get_query_result($query);
        return $res;
    }

    public function getLuxuries()
    {
	$this->db_slave->select('userid');
        $this->db_slave->where('brand_type = ', 1);
        $query = $this->db_slave->get(TBL_USER);
        $res = get_query_result($query);
        return $res;	
    }

    public function getRefereeRegUsers($username, $reg_at, $reg_end)
    {
	$this->db_slave->where('recommend_username =', $username);
        $this->db_slave->where('usertype =', 0);
        $this->db_slave->where('regtime >=', date('Y-m-d H:i:s', $reg_at));
        $this->db_slave->where('regtime <', date('Y-m-d H:i:s', $reg_end));
        $total = $this->db_slave->count_all_results(TBL_USER);

        return $total;
    }
}
