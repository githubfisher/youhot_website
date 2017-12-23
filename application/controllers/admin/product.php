<?php

class Product extends Admin_Controller
{

    public $responsible_userid;  //负责的设计师,主要给助理用

    function __construct()
    {
        parent::__construct();
//		$this->output->enable_profiler(TRUE);
        $this->load->model('product_model');
        //控制自己的权限
        if (!$this->_has_product_admin_role()) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        $this->_data['responsible_userid'] = $this->responsible_userid;

    }

    /*
    *	默认检查用户是否有管理员权限，如果没有，直接返回相应的错误处理方法
    */
    private function _has_product_admin_role($product_id = null)
    {
        $perm = false;
        if ($this->_data['usertype'] == USERTYPE_DESIGNER) {
            $this->responsible_userid = $this->userid;   //self
            $perm = true;
        }
        if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_PRODUCT)) {
            $responsible_userid = $this->user_model->get_responsible_userid($this->userid);
            if ($responsible_userid) {
                $this->responsible_userid = $responsible_userid;
                $perm = true;
            } else {
                $perm = false;
            }
        }

        if ($perm && !empty($product_id)) {
            $res = $this->product_model->get_owner($product_id);
            $owner = element('author', $res, null);
            if ($owner == $this->responsible_userid) {
                $perm = true;
            }
        }

        if ($this->_data['usertype'] == USERTYPE_ADMIN && $this->has_admin_role(ADMIN_ROLE_PRODUCT)) {
            $this->responsible_userid = '';   //空,选择所有的
            $perm = true;
        }

        return $perm;
    }


    /**
     * create product
     */


    public function  create()
    {
        $author = $this->responsible_userid;
        if($this->is_admin){
            $author = $this->input->get_post('author');   //管理员可以给设计师添加内容
        }

        //save_time   current_time
        //publish_time null

        $title = $this->input->post('title');
        //$brand_id = $this->input->post('brand_id');
        $sell_type = $this->input->post('sell_type') ? $this->input->post('sell_type') : SELL_TYPE_PRE_SALE;

        /**
         * Create product
         * Product table
         */
        $data = array('author' => $author, 'title' => $title, 'create_time' => standard_date('DATE_MYSQL'), 'sell_type' => $sell_type, 'status' => 1, 'inventory' => 10);
        //Ready to compromise unstable requirements;
        $res = $this->product_model->create_product($data);
	//print_r($res);die;
        if ($res == DB_OPERATION_FAIL) {
            log_message('error', 'create product wrong:' . json_encode($data));
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }
        $res = filter_data($res, $this->config->item('json_filter_product_detail'));
        $this->_result(bp_operation_ok, $res);

    }

//    private function _has_edit_permission($product_id)
//    {
//
//        $role = false;
//        if ($this->is_admin && $this->has_admin_role(ADMIN_ROLE_PRODUCT)) {
//            $role = TRUE;
//        }
//        $res = $this->product_model->get_owner($product_id);
//        $owner = element('author', $res, null);
//        if ($owner == $this->userid) {
//            $role = true;
//        }
//        if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_PRODUCT) && $this->user_model->is_my_assistant($owner, $this->userid)) {
//            $role = true;
//        }
//        return $role;
//
//    }
    /**
     * 检查权限并报错
     * @param $product_id
     * @return bool
     */
    private function _check_permission_and_return($product_id)
    {
        if (!$this->_has_product_admin_role($product_id)) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'), 403);
        }
        return true;
    }

    /**
     * Save product
     * 根据需求在完善
     */
    public function save()
    {
        $this->_need_login(TRUE);
        $product_id = $this->_get_product_id();
        $this->_check_permission_and_return($product_id);



        $filter_array = array('title', 'desc_title', 'desc_content', 'category', 'price', 'presale_price', 'inventory', 'sell_type', 'presale_minimum', 'presale_maximum', 'presale_days', 'production_days', 'cover_image', 'available_color', 'rank','status');
        $up_data = filter_data($this->input->post(), $filter_array);
        if (array_key_exists('desc_title', $up_data) || array_key_exists('desc_content', $up_data)) {
            $up_data['description'] = desc_encode($up_data['desc_title'], $up_data['desc_content']);
            unset($up_data['desc_title']);
            unset($up_data['desc_content']);
        }


        $up_data['save_time'] = standard_date('DATE_MYSQL');	
        $up_data['status'] = 1; // add by fisher at 2017-06-13
	$up_data['m_sku'] = time();
	$up_data['store'] = 22;
	$up_data['tmp_img'] = '{"jurl":""}';
//        $up_data['author'] = $this->userid;  //Even need to change author ,author should be responsible_userid
        $pres = $this->product_model->update_info($product_id, $up_data);

        $size_ids = $this->input->post('size_ids');
        if(is_array($size_ids)){
            $res = $this->product_model->update_product_size_relation($product_id, $size_ids);
            if($res == DB_OPERATION_FAIL){
                $this->_error(bp_operation_fail,$this->lang->line('product_size_save_fail'));
                return;
            }
        }
        $tag_ids = $this->input->post('tag_ids');
        if(is_array($tag_ids)){
            $res = $this->product_model->update_product_tag_relation($product_id, $tag_ids);
            if($res == DB_OPERATION_FAIL){
                $this->_error(bp_operation_fail,$this->lang->line('product_tag_save_fail'));
                return;
            }
        }
        $tag_ids = $this->input->post('tag_ids');
        if(is_array($tag_ids)){
            $res = $this->product_model->update_product_tag_relation($product_id, $tag_ids);
            if($res == DB_OPERATION_FAIL){
                $this->_error(bp_operation_fail,$this->lang->line('product_tag_save_fail'));
                return;
            }
        }

        $color_ids = $this->input->post('color_ids');
        if(is_array($color_ids)){
            $res = $this->product_model->update_product_color_relation($product_id, $color_ids);
            if($res == DB_OPERATION_FAIL){
                $this->_error(bp_operation_fail,$this->lang->line('product_color_save_fail'));
                return;
            }
        }

        $this->_deal_res($pres);

    }

    /**
     * 删除 product
     * 根据需求在完善
     */
    public function delete()
    {
        //@todo 需要根据产品状态判断
        $product_id = $this->_get_product_id();
        $this->_check_permission_and_return($product_id);

        $res = $this->product_model->delete($product_id);

        $this->_deal_res($res);

    }
    /**
     * 下架
     * [over]
     */
    public function unpublish()
    {
        $_POST = $_REQUEST;
        $this->form_validation->set_rules('product_id', 'product id', 'trim|integer|required');

        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $product_id = $this->_get_product_id();
        $this->_check_permission_and_return($product_id);
//        $publish_time current_time
        $up_data = array();

        $up_data['publish_time'] = 0;
        $up_data['status'] = PRODUCT_STATUS_OFFSHELF;

        $res = $this->product_model->update_info($product_id, $up_data);
        $this->_deal_res($res);
    }
    /**
     * 发布，上架
     * [over]
     */
    public function publish()
    {
        $_POST = $_REQUEST;
        $this->form_validation->set_rules('product_id', 'product id', 'trim|integer|required');
        $this->form_validation->set_rules('presale_days', 'presale days', 'trim|integer|required');
        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $product_id = $this->_get_product_id();
        $this->_check_permission_and_return($product_id);
//        $publish_time current_time
        $up_data = array();
        $presale_days = $this->input->get_post('presale_days');
        $up_data['publish_time'] = standard_date('DATE_MYSQL');
        $up_data['status'] = PRODUCT_STATUS_PUBLISHED;
        $up_data['presale_end_time'] = strtotime('+' . $presale_days . ' day');

        $res = $this->product_model->update_info($product_id, $up_data);
        $this->_deal_res($res);
    }

    /**
     * 提交审核
     * [over]
     */
    public function to_audit()
    {
        $_POST = $_REQUEST;
        $this->form_validation->set_rules('product_id', 'product id', 'trim|integer|required');

        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $product_id = $this->_get_product_id();
        $this->_check_permission_and_return($product_id);
//        $publish_time current_time
        $up_data = array();
        $up_data['status'] = PRODUCT_STATUS_INAUDIT;

        $res = $this->product_model->update_info($product_id, $up_data);
        $this->_deal_res($res);
    }
    /**
     * 申请编辑
     *
     * [over]
     */
    public function apply_edit()
    {
        $_POST = $_REQUEST;
        $this->form_validation->set_rules('product_id', 'product id', 'trim|integer|required');

        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $product_id = $this->_get_product_id();
        $this->_check_permission_and_return($product_id);
//        $publish_time current_time
        $up_data = array();
        $up_data['status_plus'] = PRODUCT_STATUS_APPLY_EDIT;   //a new field

        $res = $this->product_model->update_info($product_id, $up_data);
        $this->_deal_res($res);
    }

    // /**
    //  * 首页入口
    //  */
    // public function index()
    // {
    //     $this->product_list();
    // }

    /**
     * 商品列表+分页
     * @author fisher
     * @date 2017-03-22
     * @return array
     */
    public function index()
    {
        //装载类文件
        $this->load->library('pagination');
        //每一页显示的数据条数的变量
        $page_size=10;

        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/product/index?page=1");
        // $config['uri_segment']=3;//分页的偏移量查询在那一段上面

        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        //已经废弃
        //$config['anchor_class']="class='ajax_fPage'";//借鉴第一篇文章的大神，这里为每个a标签加样式
         $config['attributes'] = array('class' => 'myclass');//给所有<a>标签加上class

        //每一页显示的数据条数
        $config['per_page']=$page_size;
        $config['first_link']= '首页';
        $config['next_link']= '下一页';
        $config['prev_link']= '上一页';
        $config['last_link']= '末页';

        //一共有多少条数据
        
        $rows = $this->product_model->getRows();
        $config['total_rows']=$rows;

        //初始化 ，传入配置
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();

        $offset = $this->input->get('pn');
        // //传入的数据
        // $offset=intval($this->uri->segment(3));//用intval使空格转0，显示出来0
        $this->_data["arr"] = $this->product_model->getData($offset, $page_size);

        $this->template('admin/product/new_list', $this->_data);
        return;
    }

    /**
     * Admin edit page. Main purpose is to get data;
     * 后台的编辑页面，不是api
     * @param null $product_id
     */
    public function edit($product_id = null)
    {

        $this->_data ['res'] = bp_operation_ok;
        $this->_data['product_id'] = $product_id;


        if (empty($product_id)) {
            $product_id = $this->_get_product_id();
        }

        $res = $this->product_model->info($product_id);
        $this->_data['product'] = $res;
        if(empty($res)){
            $this->_error(bp_operation_db_not_find,'读取商品出错');
        }

        if($res['status']!=PRODUCT_STATUS_DRAFT){
            $this->product_model->update_info($product_id,array('status'=>PRODUCT_STATUS_DRAFT));   //move into draft
        }

        $this->load->model('tag_model');
        $tag_res = $this->tag_model->get_list(0, 0); //get all
        $this->_data['tag_list'] = $tag_res['list'];

        $this->load->model('category_model');
        $cat_res = $this->category_model->get_list(0, 0); //get all
        $this->_data['category_list'] = $cat_res['list'];


        $this->load->model('size_model');
        $cat_res = $this->size_model->get_list(0, 0); //get all
        $this->_data['size_list'] = $cat_res['list'];
        unset($cat_res);

        //$this->load->model('color_model'); // color is too many by fisher at 2017-04-20
        //$res = $this->color_model->get_list($this->responsible_userid, 0, 0); //get all
        //$this->_data['color_list'] = $res['list'];
        //unset($res);
	$this->_data['color_list'] = array();

	//echo "<pre>";
        //var_dump($this->_data['color_list']);die;

        $template = 'admin/product/edit';
        $this->template($template, $this->_data);
    }


    private function _get_product_id()
    {
        $product_id = $this->input->get_post('product_id');
        if (empty($product_id)) {
            log_message('error', 'No product id:' . json_encode($product_id));
            $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'));
        }
        return $product_id;
    }

    /**
     * 后台商品列表页面，不是api
     */
    public function product_list()
    {

        if($this->is_admin && $this->has_admin_role(ADMIN_ROLE_PRODUCT)){
            $this->_data['can_audit'] = true;
        }
        $template = 'admin/product/list';
        $this->template($template, $this->_data);
        return;
    }

    /**
     * 后台商品统计页面，非api
     */
    public function stat()
    {
        $this->setOutputType('html');
        $this->setOutputTpl('admin/stat/product');
        $this->_result(bp_operation_ok, $this->_data);
    }

    /**
     * 商品颜色的增删改查
     * api
     * @param $action
     */
    public function color($action)
    {
        $this->load->model('color_model');
        switch ($action) {
            case "add":
                $this->_validation(
                    array(
                        array('name', 'name', 'trim|required|max_length[10]')
                    , array('image', 'image url', 'trim|required')
                    )
                );
                $data = array(
                    'author' => $this->responsible_userid,
                    'name' => $this->input->get_post('name'),
                    'image' => $this->input->get_post('image'),
                );
                $res = $this->color_model->create($data);
                if ($res == 'DB_OPERATION_FAIL') {
                    $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
                }
                $this->_result(bp_operation_ok, $res);

                break;
            case "delete":
                $_POST = $_REQUEST;
                $this->_validation(array(array('color_id', 'color id', 'trim|required|numeric')));
                $color_id = $this->input->post('color_id');
                $res = $this->color_model->delete($color_id);
                $this->_deal_res($res);
                break;
            case "update":
                $_POST = $_REQUEST;
                $this->_validation(array(array('color_id', 'color id', 'trim|required|numeric')));
                $color_id = $this->input->post('color_id');
                $filter = array('name', 'image');
                $data = filter_data($_REQUEST, $filter, true);
                $res = $this->color_model->update_info($color_id, $data);
                $this->_deal_res($res);
                break;
            case "list":
                $offset = (int)$this->input->get_post('of');
                $limit = (int)$this->input->get_post('lm');
                $res = $this->color_model->get_list($this->responsible_userid, $offset, $limit);
                if (empty($res['list'])) {
                    $this->_error(bp_operation_db_not_find, $this->lang->line('hint_list_is_empty'));
                }
                $this->_result(bp_operation_ok, $res);
                break;
            case "save":
                $product_id = $this->_get_product_id();
                $color_ids = $this->input->post('color_ids');
                $res = $this->product_model->update_product_color_relation($product_id, $color_ids);
                $this->_deal_res($res);
                break;
            default:
                $this->_success();
                break;
        }
    }

    public function onsale()
    {
	$id = $this->input->get_post('id');
	$data['last_update'] = date('Y-m-d H:i:s', time());
	$data['status'] = 1;

	$res = $this->product_model->update_info($id, $data);
	
	$this->_deal_res($res);
    }
}
