<?php
class History extends Admin_Controller
{
    public function __construct()
    {
	parent::__construct();
	$this->load->model('history_model', 'history');
    }

    public function index()
    {
	$this->load->library('pagination');
        $page_size=20;
        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/history/index?page=1");
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
        $rows = $this->history->getRows();
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = empty($this->input->get('pn')) ? 0 : $this->input->get('pn');
        $this->_data["arr"] = $this->history->getData($offset, $page_size);
        $this->template('admin/history/list', $this->_data);
    }

    public function detail()
    {
	$this->load->library('pagination');
        $page_size=20;
        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/history/detail?page=1");
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
	$keywords = empty($this->input->get('keywords')) ? '' : $this->input->get('keywords');
        $rows = $this->history->getDetailRows($keywords);
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = empty($this->input->get('pn')) ? 0 : $this->input->get('pn');
        $this->_data["arr"] = $this->history->getDetailData($keywords, $offset, $page_size);
	$this->_data['keywords'] = $keywords;
        $this->template('admin/history/detail', $this->_data);	
    }
}
