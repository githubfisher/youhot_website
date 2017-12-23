<?php

class User extends Admin_Controller
{

    public function __construct()
    {

        parent::__construct();
        //控制自己的权限


    }

    /*
   *	默认检查用户是否有管理员权限，如果没有，直接返回相应的错误处理方法
   */
    private function _has_user_admin_role()
    {
//        var_dump($this->_data['usertype']);
        if ($this->_data['usertype'] == USERTYPE_ADMIN && $this->has_admin_role(ADMIN_ROLE_USER)) {
            return true;
        }

        return false;
    }

    public function index()
    {
        if (!$this->_has_user_admin_role()) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        $this->setOutputType('html');
        $this->setOutputTpl('admin/user/index');
        $this->_result(bp_operation_ok, $this->_data);

    }

    public function get_list()
    {
        if (!$this->_has_user_admin_role()) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $order = $this->input->get_post('od');
        $type = ($this->input->get_post('t')==2)?2:0;
        $type_st = ($this->input->get_post('st')==1)?1:0;

        $permit_orders = array('userid', 'rank');
        if (!in_array($order, $permit_orders)) {
            $order = null;
        }
        $filter = array();
        $res = $this->user_model->get_list_v2($offset, $limit, $filter, $order, $type, $type_st);

        if (!empty($res['list'])) {

            foreach ($res['list'] as $key => $row) {
                $res['list'][$key] = filter_data($row, $this->config->item('json_filter_user_info'));
                $res['list'][$key]['istop'] = $row['istop'];
            }

            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No user list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function edit($userid = null)
    {
        $this->_data['has_admin_role'] = $this->_has_user_admin_role();
        //修改自己的.
        if (!$this->_has_user_admin_role() && $this->userid != $userid) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        $this->_data ['res'] = bp_operation_ok;
        $this->_data['edit_userid'] = $userid;

        if (empty($userid)) {
            $userid = $this->_get_userid();
        }

        $res = $this->user_model->info($userid, 'admin');
        $this->_data['edit_user'] = $res;

        $template = 'admin/user/edit';
        $this->template($template, $this->_data);

    }

    public function delete($userid = null)
    {
        $this->_data['has_admin_role'] = $this->_has_user_admin_role();

        $this->_data ['res'] = bp_operation_ok;
        $this->_data['edit_userid'] = $userid;

        if (empty($userid)) {
            $userid = $this->_get_userid();
        }

        $res = $this->user_model->remove_user($userid);
        $this->_deal_res($res);

    }

    public function block($userid = null, $blockvalue = '1')
    {
        $this->_data['has_admin_role'] = $this->_has_user_admin_role();

        $this->_data ['res'] = bp_operation_ok;
        $this->_data['edit_userid'] = $userid;

        if (empty($userid)) {
            $userid = $this->_get_userid();
        }

        $res = $this->user_model->update_user($userid, array("isblocked" => $blockvalue));
        $this->_deal_res($res);

    }

    public function unblock($userid = null)
    {
        $this->block($userid, 0);
    }

    /**
     * 推荐设计师-匿名用户推荐的钱设计师作品
     */
    public function totop($userid = null)
    {
        if( $this->input->get_post('untop') ){
            //取消
            $topval = 0;
        }else{
            //推荐
            $topval = time();
        }
        $this->_data['has_admin_role'] = $this->_has_user_admin_role();

        $this->_data ['res'] = bp_operation_ok;
        $this->_data['edit_userid'] = $userid;

        if (empty($userid)) {
            $userid = $this->_get_userid();
        }

        $res = $this->user_model->update_user($userid, array("istop" => $topval));
        if( $res===true ){
            if( $this->input->get_post('untop') ){
                $data['top'] = 0;
            }else{
                $data['top'] = 1;
            }
            $data['res'] = bp_operation_ok;
            $this->load->view('json_view',array('json'=>$data));   //Data is outputed by json_view file
        }else{
            $this->_deal_res($res);
        }
    }

    private function _get_userid()
    {
        $userid = $this->input->get_post('userid');
        if (empty($userid)) {
            log_message('error', 'No user id:' . json_encode($userid));
            $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'), true);
        }
        return $userid;
    }


    public function create()
    {
        if (!$this->_has_user_admin_role()) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        $this->_validation(array(
            array('username', '手机号', 'trim|required|max_length[40]|unique[' . TBL_USER . '.username]'),
            array('password', '密码', 'trim|required'),
        ));
        $filter = array('username', 'password', 'usertype');
        $data = filter_data($this->input->post(), $filter);

        $res = $this->user_model->add_user($data);
        if (!$res) {
            $this->_error(bp_operation_fail, $this->language->line('bp_operation_fail_hint'));
        }

        $this->_success();


    }


}//end
