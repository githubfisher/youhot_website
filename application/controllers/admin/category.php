<?php

/**
 * Class category
 * @method
 */
class Category extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model', 'category');
    }


    /**
     * create product
     */

    private static $_validation_rules = array(
        array('name', 'name', 'trim|required|max_length[20]')
    , array('parent_id', 'parent_id', 'trim|numeric')
    , array('order', 'order', 'trim|numeric')
    );


    public function index(){
        $this->setOutputTpl('admin/category/index');
        $this->_result(bp_operation_ok);
    }


    public function  create()
    {
        $this->_check_permission_and_return();

        //save_time   current_time
        //publish_time null


        $this->_validation(self::$_validation_rules);
        $filter = array('name',  'description' , 'parent_id','order','cover_image');
        $data = filter_data($this->input->post(),$filter);

        $res = $this->category->create($data);
        if ($res == DB_OPERATION_FAIL) {
            log_message('error', 'create product wrong:' . json_encode($data));
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

        $this->_result(bp_operation_ok, array('cat_id'=>$res));

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
            array(array('cat_id','cat id','numeric|required'))
        );

        $filter = array('name',  'description' , 'parent_id','order');
        $data = filter_data($this->input->post(),$filter);

//        $up_data['save_time'] = standard_date('DATE_MYSQL');
//        $up_data['author'] = $this->userid;  //Even need to change author ,author should be responsible_userid
        $res = $this->category->update_info((int) $this->input->get_post('cat_id'), $data);
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
                array('cat_id','cat id','numeric|required')
            )
        );

        $res = $this->category->delete($this->input->get_post('cat_id'));

        $this->_deal_res($res);

    }

    public function getList()
    {
	$cates = $this->category->getAllList();
    	//echo '<pre>'; print_r($cates);die;
	$this->_data["arr"] = $this->generateTree($cates);;
	$this->template('admin/category/list', $this->_data);
    }

    public function generateTree($cates)
    {
	$new = array();
        foreach ($cates as $k => $v) {
            $new[$v['id']] = $v;
        }
        $new_cates = generateTree($new);
        //echo '<pre>'; print_r($new_cates);die;	
   
        return $new_cates;
    }

    public function brand_category_list()
    {
	$cates = $this->category->getAllList();
	$this->_data["cates"] = $this->generateTree($cates);
	$this->load->model('user_model', 'user');
	$filter = array(
            TBL_USER . '.usertype' => USERTYPE_DESIGNER,
            TBL_USER.'.isblocked' => 0
        );
        //$res = $this->user_model->get_list($offset, $limit, $filter, $order);
	$this->_data["brands"] = $this->user->get_list(0, 1000, $filter, 'nickname');
	$this->_data["arr"] = $this->category->getBrandCategoryList();
        $this->template('admin/category/b_c_list', $this->_data);
    }

    public function update_info()
    {
        $this->_check_permission_and_return();

        $filter = array('chinese_name' , 'tax_rate','weight');
        $data = filter_data($this->input->post(),$filter);
        $res = $this->category->update_info((int) $this->input->get_post('id'), $data);

        $this->_deal_res($res);
    }

    public function update_bc_rate_info()
    {
	$this->_check_permission_and_return();

        $filter = array('brand_id', 'category_id' , 'tax_rate','weight', 'status');
        $data = filter_data($this->input->post(),$filter);
        $res = $this->category->update_bc_info((int) $this->input->get_post('id'), $data);

        $this->_deal_res($res);
    }

    public function add_bc_rate_info()
    {
	$this->_check_permission_and_return();

        $filter = array('brand_id', 'category_id', 'brand_name', 'category_name', 'tax_rate','weight');
        $data = filter_data($this->input->post(),$filter);
	$data['created_at'] = time();
        $res = $this->category->add_bc_info($data);
	if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

        $this->_result(bp_operation_ok, array('id'=>$res));
    }
}
