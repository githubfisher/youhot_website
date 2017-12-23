<?php
class Coupon extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('coupon_model', 'coupon');
    }

    public function index()
    {
        $this->load->library('pagination');
        $page_size=20;
        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/coupon/index?page=1");
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
        $rows = $this->coupon->getRows();
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = $this->input->get('pn');
        $this->_data["arr"] = $this->coupon->getData($offset, $page_size);
        $this->template('admin/coupon/list', $this->_data);
    }

    public function nnew()
    {
        $this->_data['cates'] = $this->coupon->get_category_list();
        $this->_data['stores'] = $this->coupon->get_store_list();
        $template = 'admin/coupon/add';
        $this->template($template, $this->_data);
    }

    public function add()
    {
	$data['name'] = $this->input->get_post('name');
        $data['value'] = $this->input->get_post('value');
        $data['limit'] = $this->input->get_post('limit');
        $data['store'] = $this->input->get_post('store');
        $data['category'] = $this->input->get_post('category');
        $data['get_at'] = strtotime($this->input->get_post('get_at'));
        $data['get_end'] = strtotime($this->input->get_post('get_end'));
        $data['use_at'] = strtotime($this->input->get_post('use_at'));
        $data['use_end'] = strtotime($this->input->get_post('use_end'));
        $data['type'] = $this->input->get_post('type');
        $data['is_exclusive'] = $this->input->get_post('is_exclusive');
 	$data['description'] = $this->input->get_post('description');
	$data['time_limit'] = $this->input->get_post('time_limit');
        $data['get_limit'] = $this->input->get_post('get_limit');
        $data['mobile_limit'] = $this->input->get_post('mobile_limit');
        $data['reg_at'] = strtotime($this->input->get_post('reg_at'));
        $data['reg_end'] = strtotime($this->input->get_post('reg_end'));
        $data['reg_min'] = $this->input->get_post('reg_min');
        $data['repeat'] = $this->input->get_post('repeat');
        $data['get_cid'] = $this->input->get_post('get_cid');
	$data['created_at'] = time();

        $result = $this->coupon->add($data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }

    }

    public function update()
    {
        $id = $this->input->get_post('id');
        $data['name'] = $this->input->get_post('name');
        $data['value'] = $this->input->get_post('value');
        $data['limit'] = $this->input->get_post('limit');
        $data['store'] = $this->input->get_post('store');
        $data['category'] = $this->input->get_post('category');
        $data['get_at'] = strtotime($this->input->get_post('get_at'));
        $data['get_end'] = strtotime($this->input->get_post('get_end'));
        $data['use_at'] = strtotime($this->input->get_post('use_at'));
        $data['use_end'] = strtotime($this->input->get_post('use_end'));
        $data['type'] = $this->input->get_post('type');
        $data['is_exclusive'] = $this->input->get_post('is_exclusive');
        $data['description'] = $this->input->get_post('description');
	$data['time_limit'] = $this->input->get_post('time_limit');
        $data['get_limit'] = $this->input->get_post('get_limit');
        $data['mobile_limit'] = $this->input->get_post('mobile_limit');
	$data['reg_at'] = strtotime($this->input->get_post('reg_at'));
        $data['reg_end'] = strtotime($this->input->get_post('reg_end'));
        $data['reg_min'] = $this->input->get_post('reg_min');
        $data['repeat'] = $this->input->get_post('repeat');
        $data['get_cid'] = $this->input->get_post('get_cid');
	$data['updated_at'] = time();

        $result = $this->coupon->update($id, $data);
        if ($result) {
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function edit()
    {
    	$store_id = $this->input->get_post('id');
    	$this->_data['store'] = $this->coupon->getDetail($store_id);
    	$this->_data['cates'] = $this->coupon->get_category_list();	
    	$this->_data['stores'] = $this->coupon->get_store_list();
    	// echo '<pre>'; print_r($this->_data); die;
    	$template = 'admin/coupon/edit';
        $this->template($template, $this->_data);
    }

    public function create()
    {
	$id = $this->input->get_post('id');
        $sum = $this->input->get_post('sum');
	$res = $this->create_coupon($id,$sum);
	$result = $this->update_sum($id, 'created_sum', $sum);

	if ($result && $res) {
	    $this->_result(bp_operation_ok, ['id'=>$id,'sum'=>$sum]);
	} else {
	    $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
	}
    }

    private function update_sum($id, $field, $sum)
    {
	return $this->coupon->update_sum($id, $field, $sum);
    }

    private function create_coupon($id, $sum)
    {
	$data = $this->getCouponCode($id, $sum);
	return $this->coupon->create_coupon_list($data);
    }

    private function getCouponCode($id, $sum)
    {
	$nums = [];
	for ($i=0;$i<$sum;$i++) {
	    do {
		$str = getRandomStr();
		if ($res = !in_array($str, $nums)) {
		    $nums[] = $str;
		}
	    } while (!$res);
	}

	foreach ($nums as $k => $v) {
	    $data[] = [
		'coupon_id' => $id,
	  	'coupon_code' => $v,
		'created_at' => time(),
		'salt' => getRandomStr('23456789', 4)
	    ];
	}

	return $data;
    }

    public function export()
    {
	$id = $this->input->get_post('id');
	$list = $this->coupon->get_coupon_list($id, 0);
	
    }
}
