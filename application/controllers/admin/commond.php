<?php
/**
 * Class Admin/Commond
 * @author fisher
 * @date 2017-06-06
 */
class Commond extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('commond_model', 'commond');
    }

    public function index()
    {
        $this->load->library('pagination');
        $page_size=10;
        $this->load->helper('url');//分页一定要用它！！！！！！
        $config['base_url']=site_url("admin/commond/index?page=1");
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
        $rows = $this->commond->getRows();
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = $this->input->get('pn');
        $this->_data["arr"] = $this->commond->getData($offset, $page_size);
	//print_r($this->_data); die; //debug
    	$template = 'admin/commond/list';
        $this->template($template, $this->_data);
    }

}
