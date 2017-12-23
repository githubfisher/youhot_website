<?php

class Applicant extends Admin_Controller
{

    const APPLY_STATUS_APPROVED = 1;
    const APPLY_STATUS_NOT_APPROVED = 0;

    public function __construct()
    {

        parent::__construct();
        //控制自己的权限
        if (!$this->_need_user_admin_role()) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }

        $this->load->model('applicant_model');

    }

    /*
   *	默认检查用户是否有管理员权限，如果没有，直接返回相应的错误处理方法
   */
    private function _need_user_admin_role()
    {
        if ($this->_data['usertype'] == USERTYPE_ADMIN && $this->has_admin_role(ADMIN_ROLE_APPLICANT)) {
            return true;
        }
    }

    public function index()
    {

        $this->setOutputType('html');
        $this->setOutputTpl('admin/applicant/list');
        $this->_result(bp_operation_ok, $this->_data);

    }

    /**
     * 申请认证列表
     */
    public function get_list()
    {

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');

        $filter = array();
        $res = $this->applicant_model->get_list($offset, $limit, $filter);

        if (!empty($res['list'])) {

            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No user list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function approve($app_id)
    {


        $this->_data ['res'] = bp_operation_ok;


        if (empty($app_id)) {
            $app_id = $this->_get_applicant_id();
        }

        $res = $this->applicant_model->get_info($app_id);
        $userid = element('userid', $res, false);
        if (!$userid) {
            $this->_error(bp_operation_fail, $this->language->line('applicant_userid_is_null'));
        }

        $data = array(
            'status' => self::APPLY_STATUS_APPROVED,
        );
        $res1 = $this->applicant_model->update_info($app_id, $data);
        $this->load->model('user_model');
        $res2 = $this->user_model->update_user($userid, array('usertype' => USERTYPE_DESIGNER));

        if ($res1 && $res2) {
            $this->_success();
        } else {
            $this->_error(bp_operation_fail, $this->language->line('bp_operation_fail_hint'));
        }

    }

    public function refuse($app_id)
    {


        $this->_data ['res'] = bp_operation_ok;


        if (empty($app_id)) {
            $app_id = $this->_get_applicant_id();
        }


        $data = array(
            'status' => self::APPLY_STATUS_NOT_APPROVED,
            'reason' => $this->input->get_post('reson')
        );
        $res = $this->applicant_model->update_info($app_id, $data);

        $this->_deal_res($res);
    }

    private function _get_applicant_id()
    {
        $userid = $this->input->get_post('applicant_id');
        if (empty($userid)) {
            log_message('error', 'No user id:' . json_encode($userid));
            $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'), true);
        }
        return $userid;
    }


    /**
     */
    public function save()
    {
        $this->_need_login(TRUE);
        $app_id = $this->_get_applicant_id();
        $_POST = $_REQUEST;
        $this->_validation(array(array('userid','user id','numeric')));
        $filter_array = array('userid');
        $up_data = filter_data($this->input->post(), $filter_array);

//        if(!is_numeric(element('userid',$up_data,false))){
//            $this->_error(bp_operation_verify_fail,$this->language->line(''));
//        }


        $res = $this->applicant_model->update_info($app_id, $up_data);
        $this->_deal_res($res);

    }



}//end
