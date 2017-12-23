<?php
class Referer extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('referer_model', 'referer');
    }

    public function index()
    {
	$start = $this->input->get_post('start');
	$end = $this->input->get_post('end');
	$start = empty($start) ? date('Y-m-01', strtotime('-1 month')) : $start;
        $end = empty($end) ? date('Y-m-01', strtotime(date('Y-m-d'))) : $end;
        $this->load->library('pagination');
        $page_size=20;
        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/referer/index?page=0&start=".$start."&end=".$end);
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
        $rows = $this->referer->getRows($start, $end);
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
	$offset = empty($this->input->get('pn')) ? 0 : $this->input->get('pn');
        $this->_data["arr"] = $this->referer->getData($offset, $page_size, $start, $end);
	$this->_data['start'] = $start;
	$this->_data['end'] = $end;
        $this->template('admin/referer/list', $this->_data);
    }

    public function detail()
    {
        $referer = $this->input->get_post('referer');
	if (empty($referer)) {
	    $this->_error(bp_operation_fail, 'param `referer` can\'t be null');
	}
	$start = $this->input->get_post('start');
	$end = $this->input->get_post('end');
	$start = empty($start) ? date('Y-m-01', strtotime('-1 month')) : $start;
        $end = empty($end) ? date('Y-m-01', strtotime(date('Y-m-d'))) : $end;
        $this->load->library('pagination');
        $page_size=10;
        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/referer/detail?page=0&referer=".$referer."&start=".$start."&end=".$end);
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
        $rows = $this->referer->getDetailRows($referer, $start, $end);
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = empty($this->input->get('pn')) ? 0 : $this->input->get('pn');
        $deals = $this->referer->getDetailData($referer, $offset, $page_size, $start, $end);
        //print_r($deals);die;
	if (is_array($deals) && count($deals)) {
	    $pids = array_column($deals, 'pid');
	    $pids = array_unique($pids);
	    $pids = array_filter($pids);
	    $p_deals = $this->referer->getPidDetail($pids);
	    if (is_array($p_deals) && count($p_deals)) {
	     	foreach ($deals as $k => $v) {
		    if ($v['pid'] > 0) {
			foreach ($p_deals as $p => $pv) {
			    if ($pv['order_id'] == $v['pid']) {
				$deals[$k]['last_pay_coupon_value'] = $pv['last_pay_coupon_value'];
				$deals[$k]['freight'] = $pv['freight'];
				$deals[$k]['tax'] = $pv['tax'];
				$deals[$k]['pstatus'] = $pv['status'];
				$deals[$k]['promotion_value'] = $pv['promotion_value'];
				$deals[$k]['last_payment'] = $pv['last_payment'];
				break;
			    }
			}
		    }
		}
	    }
	}
        $this->_data["arr"] = $deals;
        $this->_data['referer'] = $referer;
	$this->_data['start'] = $start;
        $this->_data['end'] = $end;
   	//echo '<pre>';print_r($this->_data);
	$this->template('admin/referer/detail', $this->_data);
    }
}
