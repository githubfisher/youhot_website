<?php

class Collection extends Admin_Controller
{

    public $responsible_userid;  //负责的设计师,主要给助理用

    function __construct()
    {
        parent::__construct();
//		$this->output->enable_profiler(TRUE);

        //控制自己的权限
        if(!$this->_has_collection_admin_role()){
            $this->_error(bp_operation_user_forbidden,$this->lang->line('bp_operation_user_forbidden_hint'));
        }

        $this->load->model('collection_model');
        
        $this->_data['responsible_userid'] = $this->responsible_userid;

    }
    /*
       *	默认检查用户是否有管理员权限，如果没有，直接返回相应的错误处理方法
       */
    private function _has_collection_admin_role($collection_id=null)
    {
        $perm = false;
        if ($this->_data['usertype'] == USERTYPE_DESIGNER || $this->_data['usertype'] == USERTYPE_BUYER ) {
            $this->responsible_userid = $this->userid;   //self
            $perm = true;
        }
        if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_COLLECTION)) {
            $responsible_userid = $this->user_model->get_responsible_userid($this->userid);
            if ($responsible_userid) {
                $this->responsible_userid = $responsible_userid;
                $perm = true;
            } else {
                $perm =  false;
            }
        }

        if($perm && !empty($collection_id)){
            $res = $this->collection_model->get_owner($collection_id);
            $owner = element('author', $res, null);
            if ($owner == $this->responsible_userid) {
                $perm = true;
            }
        }

        if ($this->_data['usertype'] == USERTYPE_ADMIN && $this->has_admin_role(ADMIN_ROLE_COLLECTION)) {
            $this->responsible_userid = '';   //空,选择所有的
            $perm = true;
        }

        return $perm;
    }

    public function index()
    {
//
//		$this->_data ['res'] = bp_operation_ok;
//        $template = 'admin/collection/index';
//        $this->template($template, $this->_data);
        $this->collection_list();
    }


    public function  create()
    {
        $author = $this->responsible_userid;
        if($this->is_admin){
            $author = $this->input->get_post('author');   //管理员可以给设计师添加内容
        }

        $filter = array('title','description','background_image','cover_image');
        $data = filter_data($this->input->post(),$filter,false);
        $data['author'] = $author;

//        $data = array('author' => $author, 'title' => $title, 'description' => $description, 'background_image' => $background_image, 'cover_image' => $cover_image);
        //Ready to compromise unstable requirements;
        $res = $this->collection_model->create($data);
        if ($res == DB_OPERATION_FAIL) {
            log_message('error', 'create product wrong:' . json_encode($data));
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }
        $res = filter_data($res, $this->config->item('json_filter_collection_detail'));
        $this->_result(bp_operation_ok, $res);

    }

    public function edit($collection_id = null)
    {

        $this->_data ['res'] = bp_operation_ok;
        $this->_data['collection_id'] = $collection_id;

        if (empty($collection_id)) {
            $collection_id = $this->_get_collection_id();
        }

        $res = $this->collection_model->info($collection_id);
        $this->_data['collection'] = $res;

        /*if($res['status']!=COLLECTION_STATUS_DRAFT){
            $this->collection_model->update_info($collection_id,array('status'=>COLLECTION_STATUS_DRAFT));   //move into draft
        }*/

        // 获取分类 by fisher at 2017-03-29
        $this->_data['cbpd'] = $this->filterList();
        //echo '<pre>'; print_r($this->_data['cbpd']); die;
        // 获取分类 END
        $template = 'admin/collection/edit';
        $this->template($template, $this->_data);
    }



    private function _get_collection_id()
    {
        $collection_id = $this->input->get_post('collection_id');
        if (empty($collection_id)) {
            log_message('error', 'No collection id:' . json_encode($collection_id));
            $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'), true);
        }
        return $collection_id;
    }

    public function collection_list()
    {
        if($this->is_admin && $this->has_admin_role(ADMIN_ROLE_COLLECTION)){
            $this->_data['can_audit'] = true;
        }
        $template = 'admin/collection/list';
        $this->template($template, $this->_data);
        return;
    }

    public function stat(){
        $this->setOutputTpl('admin/stat/collection');
        $this->_result(bp_operation_ok,$this->_data);
    }

    private function _check_permission_and_return($collection_id)
    {
        if (!$this->_has_collection_admin_role($collection_id)) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'), 403);
        }
        return true;
    }


    /**
     * publish
     * [over]
     */

    public function publish($status=COLLECTION_STATUS_PUBLISHED)
    {
        //@todo 只有管理员可以发布,设计师自己不能发布
        $_POST = $_REQUEST;
        $this->form_validation->set_rules('collection_id', 'collection id', 'trim|integer|required');
        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $collection_id = $this->_get_collection_id();
        $this->_check_permission_and_return($collection_id);
//        $publish_time current_time
        $up_data = array();
        if($status == COLLECTION_STATUS_PUBLISHED){
            $up_data['publish_time'] = standard_date('DATE_MYSQL');
        }
        $up_data['status'] = $status;

        $res = $this->collection_model->update_info($collection_id, $up_data);
        $this->_deal_res($res);
    }

    public function unpublish()
    {
        $this->publish(COLLECTION_STATUS_DRAFT);
    }
    public function to_audit()
    {
        $this->publish(COLLECTION_STATUS_INAUDIT);
    }

    public function delete()
    {

        $_POST = $_REQUEST;
        $this->form_validation->set_rules('collection_ids', 'collection id', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $collection_ids = $this->input->get_post('collection_ids');
//        $this->_check_permission_and_return($collection_ids);
//        $publish_time current_time
        $limit_author = null;
        if(!$this->is_admin){
            $limit_author = array('author'=>$this->responsible_userid);
        }
        $res = $this->collection_model->del($collection_ids,$limit_author);
        $this->_deal_res($res);
    }


    /**
     * 申请专辑编辑
     *
     * [over]
     */
    public function apply_edit()
    {
        $_POST = $_REQUEST;
        $this->form_validation->set_rules('collection_id', 'collection id', 'trim|integer|required');

        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $collection_id = $this->_get_collection_id();
        $this->_check_permission_and_return($collection_id);
//        $publish_time current_time
        $up_data = array();
        $up_data['status_plus'] = COLLECTION_STATUS_APPLY_EDIT;   //a new field

        $res = $this->collection_model->update_info($collection_id, $up_data);
        $this->_deal_res($res);
    }

    /**
     * 商品筛选条件
     * 返回商品筛选的可用条件
     * @return array
     * author fisher
     * date 2017-03-14
     */
    private function filterList($ordered = true)
    {
        $lists = $this->collection_model->get_filter_list();
        //  梳理category
        $categories = array();
        static $pointer = 100;
        foreach ($lists['categorys']['list'] as $category) {
            if ($category['parent_id'] == 1) {
                $categories[$category['id']] = array_merge(array_key_exists($category['id'], $categories) ? $categories[$category['id']] : array(), $category);
            } else {
                if ($category['order'] == 0) {
                    $category['order'] = ($pointer++);
                }
                if($ordered == 'ordered'){
                    $categories[$category['parent_id']]['sub_category'][] = $category;
                }else{
                    $categories[$category['parent_id']]['sub_category'][$category['order']] = $category;
                }
            }
        }
        // 品牌排序
        $order = array();
        foreach ($lists['brands']['list'] as $k => $v) {
            $order[] = $v['english_name'];
        }
        asort($order);
        $newList = array();
        foreach ($order as $k => $v) {
            $newList[] = $lists['brands']['list'][$k];
        }
        $lists['brands']['list'] = $newList;
        $lists['categorys']['list'] = array_merge($categories);
        return $lists;
    }
    public function ct1_list()
    {
        $this->load->library('pagination');
        $page_size=10;
        $this->load->helper('url');
        $config['base_url']=site_url("admin/collection/ct1_list?page=1");
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
        $rows = $this->collection_model->getCollectionRows(1);
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = $this->input->get('pn');
        $this->_data["arr"] = $this->collection_model->getCollectionData(1,$offset, $page_size);

        $this->template('admin/collection/ct1_list', $this->_data);
    }

    public function ct1_edit()
    {
        $id = $this->input->get_post('id');
        $this->_data['collection'] = $this->collection_model->infoT1($id);
        $this->_data['cbpd'] = $this->filterList();
        // echo '<pre>'; print_r($this->_data['cbpd']);die;

        $this->template('admin/collection/ct1_edit', $this->_data);
    }

    public function ct1_upload_cover()
    {
        $collection_id = $this->input->get_post('collection_id');
        $config['upload_path']      = './uploads/';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['max_size']     = 2048;
        $config['max_width']        = 0;
        $config['max_height']       = 0;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('file')) {
            $data = array('status' => 0, 'error' => $this->upload->display_errors(),'collection_id' => $collection_id);
        } else {
            $this->load->library('aliyun-oss/Common');
            $bucket = Common::getBucketName();
            $ossClient = Common::getOssClient();
            if (is_null($ossClient)) exit(1);
            $file = $this->upload->data();
            $fn = $this->_safe_file_name($file['file_name']);
            $_config = 'aliyun_oss_product_album_dir';
            $this->load->helper('string');
            $filename = $this->config->item($_config) . '/' . (random_string('alnum', 6) . '_' . $fn);
            try{
                $ossClient->putObject($bucket, $filename, file_get_contents($file['full_path']));
            } catch(OssException $e) {
                $this->_error(bp_operation_fail,$e->getMessage());
                return;
            }
            $url = $this->config->item('aliyun_oss_img_service_url') . $filename;
            $result = $this->collection_model->update_ct1_cover($collection_id, array('cover_image' => $url));
            if ($result) {
                $data = array('status' => 1, 'upload_data' => $file, 'collection_id' => $collection_id, 'url' => $url);
            } else {
                $data = array('status' => 0, 'upload_data' => $file, 'collection_id' => $collection_id, 'url' => $url, 'update' => false);
            }
            @unlink($file['full_path']);
        }
        exit(json_encode($data));
    }

    private function _safe_file_name($fn){
        $ext = get_file_ext($fn);
        return str_replace('=', '', base64_encode($fn)).'.'.$ext;
    }

    public function ct1_update()
    {
        $id = $this->input->get_post('id');
        $data['title'] = str_replace(';', '', $this->input->get_post('title'));	
//	echo $data['title']; die;
        $data['description'] = $this->input->get_post('description');
        $data['status'] = $this->input->get_post('status');
        $data['background_image'] = $this->input->get_post('str');
        $data['last_update'] = time();//date('Y-m-d H:i:s', time());
	$data['subhead'] = $this->input->get_post('subhead');
        $res = $this->collection_model->update_info($id, $data);

        $this->_deal_res($res);
    }

    public function ct1_new()
    {
        $this->_data['cbpd'] = $this->filterList();
        $this->template('admin/collection/ct1_new', $this->_data);
    }

    public function ct1_add()
    {
        $data['title'] = $this->input->get_post('title');
        $data['description'] = $this->input->get_post('description');
        $data['status'] = $this->input->get_post('status');
        $data['background_image'] = $this->input->get_post('str');
        $data['type'] = 1;
        $data['author'] = 21;
        $data['publish_time'] = date('Y-m-d H:i:s', time());
	$data['subhead'] = $this->input->get_post('subhead');

        $res = $this->collection_model->create($data);

        if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }
        $this->_result(bp_operation_ok, $res);
    }

    public function collection_delete()
    {
        $id = $this->input->get_post('id');
        $res = $this->collection_model->collection_delete($id);

        if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }
        $this->_result(bp_operation_ok, array('result' => 'success'));
    }

    public function ct3_list()
    {
        $this->load->library('pagination');
        $page_size=10;
        $this->load->helper('url');
        $config['base_url']=site_url("admin/collection/ct3_list?page=1");
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
        $rows = $this->collection_model->getCollectionRows(3);
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = $this->input->get('pn');
        $this->_data["arr"] = $this->collection_model->getCollectionData(3, $offset, $page_size);

        $this->template('admin/collection/ct3_list', $this->_data);
    }

    public function ct3_edit()
    {
        $id = $this->input->get_post('id');
        $this->_data['collection'] = $this->collection_model->infoT3($id, false);
        //echo '<pre>';print_r($this->_data['collection']);die;

        $this->template('admin/collection/ct3_edit', $this->_data);
    }

    public function ct3_update()
    {
        $id = $this->input->get_post('id');
        $data['title'] = $this->input->get_post('title');
        $data['description'] = $this->input->get_post('description');
        $data['status'] = $this->input->get_post('status');
        $data['last_update'] = time();//date('Y-m-d H:i:s', time());
	$data['subhead'] = $this->input->get_post('subhead');

        $res = $this->collection_model->update_info($id, $data);

        $this->_deal_res($res);
    }

    public function ct3_update_item()
    {
        $id = $this->input->get_post('id');
        $data['text1'] = $this->input->get_post('text1');
        $data['text2'] = $this->input->get_post('text2');
        $data['products'] = $this->input->get_post('products');
        $data['order'] = $this->input->get_post('order');

        $res = $this->collection_model->update_ct3_item($id, $data);

        $this->_deal_res($res);
    }

    public function ct3_add_item()
    {
        $data['collection_id'] = $this->input->get_post('collection_id');
        $data['text1'] = $this->input->get_post('text1');
        $data['text2'] = $this->input->get_post('text2');
        $data['products'] = $this->input->get_post('products');
        $data['order'] = $this->input->get_post('order');
        $data['create_at'] = time();
	$data['subhead'] = $this->input->get_post('subhead');

        $res = $this->collection_model->add_ct3_item($data);

        $this->_deal_res($res);
    }

    public function ct3_del_item()
    {
        $id = $this->input->get_post('id');
        $res = $this->collection_model->ct3_del_item($id);
        $this->_deal_res($res);
    }

    public function ct3_item_image()
    {
        $collection_id = $this->input->get_post('collection_id');
        $type = $this->input->get_post('type');
        $item = $this->input->get_post('item_id');
        $config['upload_path']      = './uploads/';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['max_size']     = 2048;
        $config['max_width']        = 0;
        $config['max_height']       = 0;
        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('file')) {
            $data = array('status' => 0, 'error' => $this->upload->display_errors(),'collection_id' => $collection_id);
        } else {
            $this->load->library('aliyun-oss/Common');
            $bucket = Common::getBucketName();
            $ossClient = Common::getOssClient();
            if (is_null($ossClient)) exit(1);
            $file = $this->upload->data();
            $fn = $this->_safe_file_name($file['file_name']);
            $_config = 'aliyun_oss_product_album_dir';
            $this->load->helper('string');
            $filename = $this->config->item($_config) . '/' . (random_string('alnum', 6) . '_' . $fn);
            try{
                $ossClient->putObject($bucket, $filename, file_get_contents($file['full_path']));
            } catch(OssException $e) {
                $this->_error(bp_operation_fail,$e->getMessage());
                return;
            }
            $url = $this->config->item('aliyun_oss_img_service_url') . $filename;
            if (empty($item)) {
                $data = array(
                    'collection_id' => $collection_id,
		    'img'.$type => $url,
                    'create_at' => time(),
                );
                $result = $this->collection_model->add_ct3_item($data);
            } else {
                $data = array(
                    'img'.$type => $url
                );
                $result = $this->collection_model->update_ct3_item($item, $data);
            }
            if ($result) {
                $data = array('status' => 1, 'upload_data' => $file, 'collection_id' => $collection_id, 'url' => $url);
            } else {
                $data = array('status' => 0, 'upload_data' => $file, 'collection_id' => $collection_id, 'url' => $url, 'update' => false);
            }
            @unlink($file['full_path']);
        }
        exit(json_encode($data));
    }

    public function ct3_new()
    {
        $this->template('admin/collection/ct3_new', $this->_data);
    }

    public function ct3_add()
    {
        $data['title'] = $this->input->get_post('title');
        $data['description'] = $this->input->get_post('description');
        $data['status'] = $this->input->get_post('status');
        $data['type'] = 3;
        $data['author'] = 21;
        $data['publish_time'] = date('Y-m-d H:i:s', time());

        $res = $this->collection_model->create($data);

        if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }
        $this->_result(bp_operation_ok, $res);
    }

    public function ctl_list()
    {
        $this->load->library('pagination');
        $page_size=10;
        $this->load->helper('url');
        $config['base_url']=site_url("admin/collection/ctl_list?page=1");
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
        $rows = $this->collection_model->getCollectionRows('all');
        $config['total_rows']=$rows;
        $this->pagination->initialize($config);
        $this->_data['links'] = $this->pagination->create_links();
        $offset = $this->input->get('pn');
        $this->_data["arr"] = $this->collection_model->getCollectionData('all', $offset, $page_size);

        $this->template('admin/collection/ctl_list', $this->_data);
    }

    public function recommend()
    {
	$id = $this->input->get_post('id');
	$cd = $this->input->get_post('cd');
	if ($cd == 1) {
	    $this->collection_model->cancel_all_recommend();
	    $data = array(
		'status' => 1,
		'is_recommended' => 1,
		'last_update' => time()
	    );
        } else {
	    $data = array(
		'is_recommended' => 0,
		'last_update' => time()
	    );
        }
	$res = $this->collection_model->update_info($id, $data);
	$this->_deal_res($res);
    }

    public function pushed()
    {
	$data['id'] = $this->input->get_post('id');
	$data['title'] = $this->input->get_post('title');
	$data['content'] = $this->input->get_post('content');
	$data['ctype'] = $this->input->get_post('type');
	$data['type'] = 1;

	$this->collection_model->update_pushed($data['id']);
	$res = $this->send_jpush($data);
	$this->_deal_res($res);
    }

    private function send_jpush($msg)
    {
	$send_url = "http://10.26.95.72/index.php/note?" . http_build_query($msg);
        $res = $this->curl_note($send_url);
	
	return true;	
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

    public function update_time()
    {
        $id = $this->input->get_post('id');
        $data['last_update'] =  time();
	$res = $this->collection_model->update_info($id, $data);

        $this->_deal_res($res);
    }

    public function ctl_image()
    {
        $collection_id = $this->input->get_post('collection_id');
        $type = $this->input->get_post('type');
        $config['upload_path']      = './uploads/';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['max_size']     = 2048;
        $config['max_width']        = 0;
        $config['max_height']       = 0;
        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('file')) {
            $data = array('status' => 0, 'error' => $this->upload->display_errors(),'collection_id' => $collection_id);
        } else {
            $this->load->library('aliyun-oss/Common');
            $bucket = Common::getBucketName();
            $ossClient = Common::getOssClient();
            if (is_null($ossClient)) exit(1);
            $file = $this->upload->data();
            $fn = $this->_safe_file_name($file['file_name']);
            $_config = 'aliyun_oss_product_album_dir';
            $this->load->helper('string');
            $filename = $this->config->item($_config) . '/' . (random_string('alnum', 6) . '_' . $fn);
            try{
                $ossClient->putObject($bucket, $filename, file_get_contents($file['full_path']));
            } catch(OssException $e) {
                $this->_error(bp_operation_fail,$e->getMessage());
                return;
            }
            $url = $this->config->item('aliyun_oss_img_service_url') . $filename;
	    switch ($type) {
		case 1:
		    $field = 'cover_image';
		    break;
		case 2:
                    $field = 'content_image';
                    break;
		default:
		    $field = 'recommond_image';
		    break;
	    }
            $data = array(
                    $field => $url,
                    'last_update' => time(),//date('Y-m-d H:i:s', time()),
            );
            $result = $this->collection_model->update_info($collection_id, $data);
            if ($result) {
                $data = array('status' => 1, 'upload_data' => $file, 'collection_id' => $collection_id, 'url' => $url);
            } else {
                $data = array('status' => 0, 'upload_data' => $file, 'collection_id' => $collection_id, 'url' => $url, 'update' => false);
            }
            @unlink($file['full_path']);
        }
        exit(json_encode($data));
    }
}
