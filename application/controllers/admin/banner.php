<?php

/**
 * Class banner
 * @method
 */
class Banner extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('banner_model', 'banner');
    }


    /**
     * create product
     */

    private static $_validation_rules = array(
        array('pic', 'pic', 'trim|required')
    , array('text', 'text', 'trim|min_length[2]|max_length[600]|required')
    , array('type', 'type', 'trim|numeric')
    , array('url', 'url', 'trim|required')
    );


    public function index(){
        $this->setOutputTpl('admin/banner');
        $this->_result(bp_operation_ok);
    }


    public function  create()
    {
        $this->_check_permission_and_return();

        //save_time   current_time
        //publish_time null


        $this->_validation(self::$_validation_rules);
        $filter = array('pic',  'text' , 'type','url');
        $data = filter_data($this->input->post(),$filter);

        $res = $this->banner->create($data);
        if ($res == DB_OPERATION_FAIL) {
            log_message('error', 'create product wrong:' . json_encode($data));
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

        $this->_result(bp_operation_ok, array('bid'=>$res));

    }

    private function _check_permission_and_return()
    {
        if (!$this->is_admin) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'), 403);
        }
        return true;
    }

    /**
     * Save product
     * 根据需求在完善
     */
    public function update()
    {
        $this->_check_permission_and_return();

        $this->_validation(
            array(array('bid','cat id','numeric|required'))
        );

        $filter = array('pic',  'text' , 'type','url');
        $data = filter_data($this->input->post(),$filter);

//        $up_data['save_time'] = standard_date('DATE_MYSQL');
//        $up_data['author'] = $this->userid;  //Even need to change author ,author should be responsible_userid
        $res = $this->banner->update_info((int) $this->input->get_post('bid'), $data);
        $this->_deal_res($res);
    }

    /**
     * Save product
     * 根据需求在完善
     */
    public function delete()
    {
        $this->_check_permission_and_return();
        $_POST = $_REQUEST;
        $this->_validation(
            array(
                array('bid','banner id','numeric|required')
            )
        );

        $res = $this->banner->delete($this->input->get_post('bid'));

        $this->_deal_res($res);

    }
}
