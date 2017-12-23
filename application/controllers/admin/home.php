<?php

class Home extends Admin_Controller
{

//    public $responsible_userid;  //负责的设计师,主要给助理用

    function __construct()
    {
        parent::__construct();
//		$this->output->enable_profiler(TRUE);

        //控制自己的权限
        if(!$this->_need_admin_entrance_role()){
            $this->_error(bp_operation_user_forbidden,$this->lang->line('bp_operation_user_forbidden_hint'));
        }
	$this->load->model('data_model', 'data');

    }

    /*
    *	默认检查用户是否有管理员权限，如果没有，直接返回相应的错误处理方法
    */
    private function _need_admin_entrance_role()
    {
        if ($this->_data['usertype'] == USERTYPE_DESIGNER || $this->_data['usertype'] == USERTYPE_ADMIN || $this->_data['usertype'] == USERTYPE_BUYER) {
            return true;
        }
        if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_ENTRANCE)) {
//            $responsible_userid = $this->user_model->get_responsible_userid($this->userid);
//            if ($responsible_userid) {
////                $this->responsible_userid = $responsible_userid;
//                return true;
//            } else {
////                $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'), true, $this->config->item('status_code')['forbidden']);
//                return false;
//            }
            return true;
        }
    }

    public function index()
    {
	$statistics = $this->data->get_statistics();
	$this->_data['statistics'] = $statistics;
	//print_r($this->_data); die;
        $this->setOutputTpl('admin/index');
        $this->_result(bp_operation_ok,$this->_data);
    }


}//end
