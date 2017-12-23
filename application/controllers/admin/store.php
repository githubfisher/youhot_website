<?php
class Store extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('store_model', 'store');
    }

    public function index()
    {
        $this->load->library('pagination');
        $page_size=20;
        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/store/index?page=1");
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
        //$config['anchor_class']="class='ajax_fPage'";//借鉴第一篇文章的大神，这里为每个a标签加样式
         $config['attributes'] = array('class' => 'myclass');//给所有<a>标签加上class
        $config['per_page']=$page_size;
        $config['first_link']= '首页';
        $config['next_link']= '下一页';
        $config['prev_link']= '上一页';
        $config['last_link']= '末页';
        $rows = $this->store->getRows();
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = $this->input->get('pn');
        $this->_data["arr"] = $this->store->getData($offset, $page_size);
        $this->template('admin/store/list', $this->_data);
    }

    public function add()
    {

    }

    public function delete()
    {

    }

    public function update()
    {
        $id = $this->input->get_post('id');
        $data['name'] = $this->input->get_post('name');
        $data['show_name'] = $this->input->get_post('show_name');
        $data['country'] = $this->input->get_post('country');
        $data['currency'] = $this->input->get_post('currency');
        $data['url'] = $this->input->get_post('url');
        $data['direct_mail'] = $this->input->get_post('direct_mail');
        $data['number_one'] = $this->input->get_post('number_one');
        $data['direct_mail_rate'] = $this->input->get_post('direct_mail_rate');
        $data['update_at'] = time();

        $result = $this->store->update($id, $data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function edit()
    {
    	$store_id = $this->input->get_post('id');
    	$this->_data['store'] = $this->store->getDetail($store_id);
    	$this->_data['shipping'] = $this->store->get_shippings($store_id);
    	// echo '<pre>'; print_r($this->_data); die;
    	$template = 'admin/store/edit';
        $this->template($template, $this->_data);
    }

    public function shipping_add()
    {
        $data['store_id'] = $this->input->get_post('sid');
        $data['type'] = $this->input->get_post('type');
        $data['country'] = $this->input->get_post('country');
        $data['currency'] = $this->input->get_post('currency');
        $data['low'] = $this->input->get_post('low');
        $data['high'] = $this->input->get_post('high');
        $data['low_fee'] = $this->input->get_post('low_fee');
        $data['base_fee'] = $this->input->get_post('base_fee');
        $data['days'] = $this->input->get_post('days');
	$data['count_type'] = $this->input->get_post('count_type');
	$data['count_unit'] = $this->input->get_post('count_unit');
	if (empty($data['days'])) {
	    unset($data['days']);
	}
        $data['create_at'] = time();

        $result = $this->store->shipping_add($data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }
    public function shipping_update()
    {
        $id = $this->input->get_post('id');
        $data['type'] = $this->input->get_post('type');
        $data['country'] = $this->input->get_post('country');
        $data['currency'] = $this->input->get_post('currency');
        $data['low'] = $this->input->get_post('low');
        $data['high'] = $this->input->get_post('high');
        $data['low_fee'] = $this->input->get_post('low_fee');
        $data['base_fee'] = $this->input->get_post('base_fee');
        $data['days'] = $this->input->get_post('days');
	$data['count_type'] = $this->input->get_post('count_type');
        $data['count_unit'] = $this->input->get_post('count_unit');
	if (empty($data['days'])) {
            unset($data['days']);
        }
        $data['status'] = $this->input->get_post('status');

        $result = $this->store->shipping_update($id, $data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function sales()
    {
	$store = $this->input->get_post('id');
        $this->load->library('pagination');
        $page_size=10;
        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/store/index?page=1");
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
        $config['attributes'] = array('class' => 'myclass');//给所有<a>标签加上class
        $config['per_page']=$page_size;
        $config['first_link']= '首页';
        $config['next_link']= '下一页';
        $config['prev_link']= '上一页';
        $config['last_link']= '末页';
        $rows = $this->store->getSalesRows($store);
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = $this->input->get('pn');
        $this->_data["arr"] = $this->store->getSalesData($store, $offset, $page_size);
	$this->_data['sales_id'] = $store;
        $this->template('admin/store/sales_list', $this->_data);
    }

    public function sales_edit()
    {
	$id = $this->input->get_post('id');
        $this->_data['sales'] = $this->store->getSalesDetail($id);
        $this->_data['options'] = $this->store->getSalesOptions($id);
//        echo '<pre>'; print_r($this->_data); die;
        $template = 'admin/store/sales_edit';
        $this->template($template, $this->_data);
    }

    public function sales_update()
    {
	$id = $this->input->get_post('id');
        $data['name'] = $this->input->get_post('name');
        $data['description'] = $this->input->get_post('description');
        $data['type'] = $this->input->get_post('type');
        $data['status'] = $this->input->get_post('status');
        $data['start_at'] = $this->dealWithTime($this->input->get_post('start'));
        $data['end_at'] = $this->dealWithTime($this->input->get_post('end'));
        $data['order'] = $this->input->get_post('order');
        $data['updated_at'] = time();
        $result = $this->store->sales_update($id, $data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    private function dealWithTime($time)
    {
	if (!empty($time)) {
	   if ($t = strtotime($time)) {
	       return $t;
	   }
        }

        return 0;
    }

    public function sales_status()
    {
	$id = $this->input->get_post('id');
        $data['status'] = $this->input->get_post('status');
        $data['updated_at'] = time();
        $result = $this->store->sales_update($id, $data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function sales_options_add()
    {
        $data['sales_id'] = $this->input->get_post('id');
        $data['currency'] = $this->input->get_post('currency');
        $data['lower'] = $this->input->get_post('lower');
        $data['upper'] = $this->input->get_post('upper');
        $data['promotion'] = $this->input->get_post('promotion');
        $data['created_at'] = time();

        $result = $this->store->sales_options_add($data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function sales_options_update()
    {
        $id = $this->input->get_post('id');
        $data['currency'] = $this->input->get_post('currency');
        $data['lower'] = $this->input->get_post('lower');
        $data['upper'] = $this->input->get_post('upper');
        $data['promotion'] = $this->input->get_post('promotion');
        $data['status'] = $this->input->get_post('status');
        $data['updated_at'] = time();

        $result = $this->store->sales_options_update($id, $data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function sales_add()
    {
        $id = $this->input->get_post('id');
        $this->_data['sales_id'] = $id;
        $template = 'admin/store/sales_add';
        $this->template($template, $this->_data);
    }

    public function sales_new_add()
    {
        $data['store_id'] = $this->input->get_post('id');
        $data['type'] = $this->input->get_post('type');
        $data['name'] = $this->input->get_post('name');
        $data['description'] = $this->input->get_post('description');
	$data['status'] = $this->input->get_post('status');
        $data['start_at'] = $this->dealWithTime($this->input->get_post('start_at'));
        $data['end_at'] = $this->dealWithTime($this->input->get_post('end_at'));
        $data['order'] = $this->input->get_post('order');
        $data['created_at'] = time();

        $result = $this->store->sales_new_add($data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }
}
