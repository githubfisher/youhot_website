<?php
class Returnback extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model', 'order');
    }

    public function index()
    {
        $this->load->library('pagination');
        $page_size=20;
        $this->load->helper('url');
        $config['base_url']=site_url("admin/returnback/index?page=1");
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
        $config['attributes'] = array('class' => 'myclass');
        $config['per_page']=$page_size;
        $config['first_link']= '首页';
        $config['next_link']= '下一页';
        $config['prev_link']= '上一页';
        $config['last_link']= '末页';
        $rows = $this->order->getRows();
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = $this->input->get('pn');
        $this->_data["arr"] = $this->order->getData($offset, $page_size);
//	echo '<pre>';	print_r($this->_data);die;
	
        $this->template('admin/returnback/list', $this->_data);
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
        $data['order_id'] = $this->input->get_post('name');
        $data['price'] = $this->input->get_post('price');
        $data['reason'] = $this->input->get_post('reason');
        $data['reply'] = $this->input->get_post('reply');
        $data['status'] = $this->input->get_post('status');
        $data['type'] = $this->input->get_post('type');
	$data['reply_at'] = time();

        $result = $this->order->update_return($id, $data);
        if ($result == DB_OPERATION_OK) {
	    if ($data['status'] == 1) {
		$send_url = "http://10.26.95.72/index.php/note?" . http_build_query(['id'=>$id, 'type'=>4]);
		$res = $this->curl_note($send_url);
	    }
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    private function curl_note($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }


    public function edit()
    {
    	$id = $this->input->get_post('id');
    	$this->_data['order'] = $this->order->getReturnDetail($id);
    	$template = 'admin/returnback/edit';
	//echo '<pre>'; print_r($this->_data);die;

        $this->template($template, $this->_data);
    }
}
