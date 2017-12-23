<?php

/**
 * Class Product
 * @method
 */
class Collection extends User_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('collection_model', 'collection');
	$this->load->model('superscript_model', 'superscript');
    }


    public function index($id)
    {

        $this->detail($id);

    }


    /**
     * create product
     */



    /**
     * 检查是否有权限,暂时不存session,存session如果权限变更需要退出重新登录(不好操作)后续用缓存来做
     * @param $collection_id
     * @return bool
     */
    private function _has_edit_permission($collection_id)
    {

//        if (array_key_exists('collection_edit_role', $this->session->all_userdata())) {
//            return $this->session->userdata('collection_edit_role');
//        }
        $role = false;
        if ($this->is_admin && $this->has_admin_role(ADMIN_ROLE_COLLECTION)) {
            $role = TRUE;
        }
        $res = $this->collection->get_owner($collection_id);
        $owner = element('author', $res, null);
        if ($owner == $this->userid) {
            $role = true;
        }
        if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_COLLECTION) && $this->user_model->is_my_assistant($owner, $this->userid)) {
            $role = true;
        }
//        $this->session->set_userdata('collection_edit_role', $role);
        return $role;


    }

    private function _check_permission_and_return($collection_id)
    {
        if (!$this->_has_edit_permission($collection_id)) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
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
        $collection_id = $this->_get_collection_id();
        $this->_check_permission_and_return($collection_id);

        $filter_array = array('title', 'description', 'background_image', 'cover_image', 'subhead');
        $up_data = filter_data($this->input->post(), $filter_array);
        $res = $this->collection->update_info($collection_id, $up_data);
        $this->_deal_res($res);

    }

    public function detailOld($collection_id = null)
    {
        $this->setOutputTpl('collection/detail');
        if (empty($collection_id)) {
            $collection_id = $this->_get_collection_id();
        }

        $res = $this->collection->info($collection_id);
	if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        } else {

            $res = filter_data($res, $this->config->item('json_filter_collection_detail'));

            //$res['related_products'] = $this->_get_related_products($res['author'], array_column($res['item_list'], 'product_id')); // 取消查询相关商品 by fisher at 2017-04-25
            //if($collection_id == 2){  //for test
                $res['related_products'] = array();
            //}
            //$this->load->model('comments_model');
            //$cdata = $this->comments_model->get_list('c'.$collection_id,0,1);//get total
            //$res['comments_total'] = element('total',$cdata,0);

            $this->collection->update_view_count($collection_id,$res['author']);

            $this->_result(bp_operation_ok, $res);
        }
    }

    public function preview($id = null, $type = null)
    {
	if (empty($id)) {
            $id = $this->_get_collection_id();
        }
	if (empty($type)) {
	    $type = $this->input->get_post('type');
	}
	$this->_data['signature'] = getSignPackage();
	$this->_data['collection_id'] = $id;	
	$this->_data['type'] = $type;
        $this->layout->set_layout('layout/h5');
	switch ($type) {
	    case 1:
		$this->setOutputTpl('h5/share1');
		break;
	    case 2:
		$this->setOutputTpl('h5/share2');
		break;
	    case 3:
		$this->setOutputTpl('h5/share3');
		break;
	    default:
	  	break;
	}
        $this->_result(bp_operation_ok,$this->_data);
    }

    public function detail($id = null, $type = null)
    {
	$rt = $this->input->get_post('rt');
        $type = $this->input->get_post('type');
        if (empty($type)) {
            $type = 2;
        }
        if (empty($id)) {
            $id = $this->_get_collection_id();
        }

        switch ($type) {
            case 1:
                $res = $this->getDetailT1($id);
		break;
            case 3:
                $res = $this->getDetailT3($id);
		break;
            default:
                $res = $this->getDetailT2($id);
        }

        if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        } else {
	    if ($rt === 'jsonp') {
                 $callback = $this->input->get_post('callback');
                 exit($callback."(".json_encode($res).");");
            }
	    $this->_result(bp_operation_ok,$res);
	}
    }

    private function getDetailT2($id)
    {
        $res = $this->collection->info($id);

        if ($res == DB_OPERATION_FAIL) {
            return DB_OPERATION_FAIL;
        }
	$liked_products = array();
        if ($this->userid && $this->is_login) {
            $res2 = $this->user_model->get_like_products($this->userid);
            $liked_products = array_column($res2, 'product_id');
        }
        foreach ($res['item_list'] as $key => $row) {
            $res['item_list'][$key]['like'] = in_array($row['id'], $liked_products) ? "1" : "0";
	    if ($row['presale_price'] > 0) {
	 	$dc = floor(($row['product_price']/$row['presale_price'])*10);
            	$res['item_list'][$key]['superscript'] = $this->superscript->getList($dc);
	    } else {
		$res['item_list'][$key]['superscript'] = [
		    [
                    	'size' => '2x',
                    	'url' => 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/album/fVLLPn_MEAyeC5wbmc.png',
                    ],
                    [
                    	'size' => '3x',
                    	'url' => 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/album/RSvRre_MEAzeC5wbmc.png',
                    ],
                ];
	    }
        }
        $res = filter_data($res, $this->config->item('json_filter_collection_detail'));
        $this->collection->update_view_count($id,$res['author']);

        return $res;
    }

    private function getDetailT3($id)
    {
        $res = $this->collection->infoT3($id);
        
	if ($res == DB_OPERATION_FAIL) {
            return DB_OPERATION_FAIL;
        }

        $this->collection->update_view_count($id,$res['author']);

        return $res;
    }

    private function getDetailT1($id)
    {
        $res = $this->collection->infoT1($id);

        if ($res == DB_OPERATION_FAIL) {
            return DB_OPERATION_FAIL;
        }
        if (!empty($res['background_image'])) {
            $of = $this->input->get_post('of');
            $of = !empty($of) ? $of : 0;
            $lm = $this->input->get_post('lm');
            $lm = !empty($lm) ? $lm : 10;
            $result = $this->getCollectionItems($res['background_image'],$of,$lm);
	    //$res['params'] = $result['params'];
        }
        if (!empty($result['items']['list'][0]['id'])) {
            $res['item_list'] = $result['items']['list'];
	    $res['total'] = $result['items']['total'];
        } else {
            $res['item_list'] = array();
        }

	$res['filterList'] = $this->filterListV3(true, false);
	$res['filterList']['categorys']['list'] = $this->dealWithList($res['filterList']['categorys']['list']);
	//$res['filterList'] = $this->addSelectedOption($res['filterList'], $result['params']);
        $this->collection->update_view_count($id,$res['author']);

        return $res;
    }

    private function addSelectedOption($list, $params)
    {
        $list['categorys']['list'] = $this->findSelectedCategorys($list['categorys']['list'], $params['category']);
        $list['brands']['list'] = $this->findSelectedOptions($list['brands']['list'], $params['brand'], 'brandid');
        $list['prices']['list'] = $this->findSelectedOptions($list['prices']['list'], $params['price']);
        $list['discounts']['list'] = $this->findSelectedOptions($list['discounts']['list'], $params['discount']);
       // $list['sorts']['list'] = $this->findSelectedSorts($list['sorts']['list'], $params['sort']);   
        return $list;
    }

    private function findSelectedCategorys($list, $options)
    {
        if (is_array($options) && !empty($options[0]) && count($options)) {
            foreach ($list as $k => $v) {
		$unselected = 0;
                foreach ($v['sub_category'] as $m => $n) {
                    foreach ($options as $y) {
                        if ($y == $n['id']) {
                            $list[$k]['sub_category'][$m]['is_selected'] = 1;
                            break;
                        }
                    }
		    if ($list[$k]['sub_category'][$m]['is_selected'] == 0) {
			$unselected++;
		    }
                }
		if (($unselected == 1) && ($list[$k]['id'] != 0)) {
		    $list[$k]['is_selected'] = $list[$k]['sub_category'][0]['is_selected'] = 1;
		}
            }
        }

        return $list;
    }

    private function findSelectedOptions($list, $options, $field='id')
    {
        //$options = json_decode($options, true);
        if (is_array($options) && !empty($options[0]) && count($options)) {
            foreach ($list as $k => $v) {
                foreach ($options as $y) {
                    $list[$k]['is_selected'] = 0;
                    if ($y == $v[$field]) {
                        $list[$k]['is_selected'] = 1;
                        break;
                    }
                }
            }
        } else {
            foreach ($list as $k => $v) {
                $list[$k]['is_selected'] = 0;
            }
        }
        return $list;
    }

    private function findSelectedSorts($list, $options)
    {
        //$options = json_decode($options, true);
        if (is_array($options) && !empty($options[0]) && count($options)) {
            foreach ($list as $k => $v) {
                foreach ($options as $y) {
                    $list[$k]['is_selected'] = 0;
                    if (($y[0] == $v['type']) && ($y[1] == $v['sort'])) {
                        $list[$k]['is_selected'] = 1;
                        break;
                    }
                }
            }
        } else {
            foreach ($list as $k => $v) {
                $list[$k]['is_selected'] = 0;
            }
        }
        return $list;
    }

    private function getCollectionItems($params,$of=0,$lm=10)
    {
        $params = json_decode($params, true);
        
	$p = [];
        if (is_array($params['brand']) && count($params['brand'])) {
            $p['brand'] = json_encode($params['brand']);
        }
        if (is_array($params['category']) && count($params['category'])) {
            $this->config->load('categorys', TRUE);
            $categorys_map = $this->config->item('map', 'categorys');
            $new_cid = array();
            foreach ($params['category'] as $c) {
                if (array_key_exists($c, $categorys_map['women'])) {
                    foreach ($categorys_map['women'][$c] as $value) {
                        array_push($new_cid, $value);
                    }
                }
            }
            $cid = array_merge($params['category'], $new_cid);
            $cid = array_unique($cid);
            $cid = array_values($cid);
            $p['category'] = json_encode($cid);
        }
        $p['of'] = $of;
        $p['lm'] = $lm;
	if (is_array($params['price']) && count($params['price'])) {
            $p['price'] = json_encode($params['price']);
        }
	if (is_array($params['discount']) && count($params['discount'])) {
            $p['discount'] = json_encode($params['discount']);
        }
	if (is_array($params['store']) && count($params['store'])) {
            $p['store'] = json_encode($params['store']);
        }
	if (is_array($params['sort']) && count($params['sort'])) {
            $p['sort'] = json_encode(array_merge([['position','desc']], $params['sort']));
        } else {
	    $p['sort'] = json_encode([['position','desc']]);
	}
        $result = $this->get_search('search', $p);
        $result = $this->dealWithSearch($result);

        return ['items' => $result, 'params' => $params];
    }

    private function get_search($api='search',$params)
    {
        $url = 'http://114.55.40.32/index.php/api/'.$api.'?'.http_build_query($params);
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($con, CURLOPT_TIMEOUT, 10);
         
        return curl_exec($con);
    }

    private function dealWithSearch($res)
    {
        $res = json_decode($res, true);
        if ($res['hits']['total']) {
            $result = array();
            $result['res'] = 0;
            $result['total'] = $res['hits']['total'];
            $liked_products = array();
            if ($this->userid && $this->is_login) {
                $res2 = $this->user_model->get_like_products($this->userid);
                $liked_products = array_column($res2, 'product_id');    //my liked products
            }
            foreach ($res['hits']['hits'] as $key => $row) {
                $result['list'][$key] = filter_data($row['_source'], $this->config->item('json_filter_product_list'));
                $result['list'][$key]['id'] = $row['_id'];
                $result['list'][$key]['like'] = in_array($row['_id'], $liked_products) ? "1" : "0";   //like or not
		$dc = floor($row['_source']['discount']*10);
                $result['list'][$key]['superscript'] = $this->superscript->getList($dc);
		$result['list'][$key]['author_nickname'] = isset($row['_source']['author_nickname']) ? $row['_source']['author_nickname'] : '';
		$result['list'][$key]['chinese_name'] = isset($row['_source']['author_chinese_name']) ? $row['_source']['author_chinese_name'] : '';
		$result['list'][$key]['flag_url'] = 'http://product-album.img-cn-hangzhou.aliyuncs.com/album/r2zg8a_dXNhQDJ4LnBuZw.png';
            }
            $result['count'] = count($result['list']);
            // $result['filterList'] = $this->filterList(true,false);
            return $result;
        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    } 

    /**
     * 临时的相关商品 (也是promotion商品)
     * 根据tag获取,不重复的
     * @return mixed
     */
    private function _get_related_products($userid, $collection_products)
    {

        //get collection's author's tag
        //get products from tag
        if (!isset($this->product_model)) {
            $this->load->model('product_model');
        }


        $res = $this->user_model->get_follow_tags($userid);
        $tags = $res['list'];
        unset($res);
//        var_dump(array_column($tags, 'tag_id'));
        if (!empty($tags)) {
            $res = $this->product_model->get_products_by_tag(array_column($tags, 'tag_id'),0,20,array(TBL_PRODUCT.'.status'=>PRODUCT_STATUS_PUBLISHED));
            $related_products = $res;
        } else {
            $p = $this->product_model->get_list(null, 0, 20, array('promotion' => 1));
            $related_products = $p['list'];
        }


        if (!empty($related_products)) {
            $num = 1;
            $data = array();
            foreach ($related_products as $key => $product) {
                //只要6个
                if ($num > 6) {
                    break;
                }
                //去除collection里面已经有的
                if (in_array($product['id'], $collection_products)) {
                    continue;
                }
                $data[$key] = filter_data($product, $this->config->item('json_filter_product_list'));

                $num++;

            }
            $related_products = $data;
        }
//        var_dump(count($related_products));
        return $related_products;
    }


    private function _get_collection_id()
    {
        $collection_id = $this->input->get_post('collection_id');
        if (empty($collection_id)) {
            log_message('error', 'No product id:' . json_encode($collection_id));
            $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'));
        }
        return $collection_id;
    }


    public function album($action)
    {
        $this->_need_login(true);


        if ($action == 'add') {
            $collection_id = $this->_get_collection_id();
            $album = array();
            $album['content'] = $this->input->post('content');  //image,video  url
            $album['type'] = $this->input->post('type');  //ALBUM_RESOURCE_TYPE_IMAGE....
            $album['text'] = $this->input->post('text');  //ALBUM_RESOURCE_TYPE_IMAGE....
            $album['position'] = $this->input->post('position');  //ALBUM_RESOURCE_TYPE_IMAGE....
            $album['product_id'] = $this->input->post('product_id'); //related product
            $res = $this->collection->add_album_resource($collection_id, $album);
            if($res == DB_OPERATION_FAIL){
                $this->_error(bp_operation_fail,$this->lang->line('bp_operation_fail_hint'));
            }
	    if (!empty($album['position'])) {
		$this->update_product_info($album['product_id'], ['position' => $album['position']]);
	    }
            $this->_result(bp_operation_ok,array('item_id'=>$res));
        } elseif ($action == 'delete') {
            //@todo permission check
            $album_id = $this->input->post('item_id');
            $product_id = $this->input->post('product_id');
            if (empty($album_id)) {
                $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'));
            }
            $res = $this->collection->delete_album_resource($album_id);
	    $this->update_product_info($product_id);
            $this->_deal_res($res);
        } elseif ($action == 'update_order') {
            /**
             * up_data = {'id':{"collection_id":**,"position":**,"content":**,"type":**},'id':{}}
             * up_data = [album_id1,id2,id3];顺序
             * collection_id = 1
             */
            $up_data = $this->input->post('up_data');
            $collection_id = $this->_get_collection_id();
            $ok_times = 0;
            if (!empty($up_data) && is_array($up_data)) {
                foreach ($up_data as $key=>$album_id) {
                    if (empty($album_id)) continue;  //album id没有时候跳过
                    //@todo 空的内容不提交
                    $row = array('collection_id'=>$collection_id,'position'=>$key);
                    $res = $this->collection->update_album_resource($album_id, $row);
                    if ($res == DB_OPERATION_OK) {
                        $ok_times++;
                    } else {
                        log_message('error', 'product/album/update get wrong :' . json_encode($up_data));
                    }
                }
                if ($ok_times == count($up_data)) {
                    $this->_success();
                } elseif ($ok_times = 0) {
                    $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
                } else {
                    $this->_error(bp_operation_fail, $this->lang->line('bp_operation_partial_fail_hint'));
                }

            } else {
                $this->_error(bp_operation_verify_fail, $this->lang->line('bp_operation_data_is_null'));
            }


        }elseif($action == 'update'){
            $item_id = $this->input->post('item_id');
            if (empty($item_id)) {
                $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'));
            }
            $filter_rule = array('collection_id','position','content','type','product_id','text');
            $up_data = filter_data($this->input->post(),$filter_rule,false);
            $res = $this->collection->update_album_resource($item_id,$up_data);
	    $this->update_product_info($up_data['product_id'], ['position' => $up_data['position']]);
            $this->_deal_res($res);
        }else{
            $this->_error(bp_operation_verify_fail,'illegal action');
        }

    }

    public function tags($action)
    {
        $this->_need_login(true);
        $collection_id = $this->_get_collection_id();
        if ($action == 'save') {
            $tag_ids = $this->input->post('tag_ids');
            $res = $this->collection->update_product_tag_relation($collection_id, $tag_ids);
            $this->_deal_res($res);
        }

    }


    /**
     * All type of list
     */

    /**
     * 我的推荐商品列表
     */
    public function list_for_me()
    {
        //All product order by rank
        $res = $this->_get_list('publish');
//        if (!empty($res['list'])) {
//            $product_list = $res['list'];
//           //Get my follow tags and reorder
//            $this->load->model('user_model');
//            $res = $this->user_model->get_follow_tags($this->_un);
//            $tags = array_column($res['list'],'tag_id');
//            //@todo 这里根据lm取出了前几条,排序是意义不大.需要重新设计.暂时先这样
//
//
//        }
    }

    /**
     * Product list of what I have published
     * @param userid
     * @return {
     *
     *
     * }
     */

    function published_list()
    {
        $this->_get_list(PRODUCT_STATUS_PUBLISHED);

    }

    /**
     * Product list of what I have published
     * @param userid
     */

    public function saved_list()
    {
        $this->_get_list(PRODUCT_STATUS_DRAFT);

    }
    /**
 * Product list of what I have published
 * @param userid
 */

    public function my_list()
    {
        $status = $this->input->get_post('status');
        if($status!==false){
            $this->_get_list((int) $status);
        }else{
            $this->_get_list('all');
        }
    }

    private function _get_list($status_filter, $order = null, $output = true, $type = '2')
    {
//        $_POST = $_REQUEST;
//        $this->form_validation->set_rules('of', 'offset', 'trim|integer');
//        $this->form_validation->set_rules('of', 'limit', 'trim|integer');
//
//        if ($this->form_validation->run() == FALSE) {
//            $this->_error(
//                bp_operation_verify_fail,
//                validation_errors(' ', ' ')
//            );
//        }
//        $_userid = $this->_un;
//        if ($this->isAdmin && $this->input->get('userid')) {
        $_userid = $this->input->get_post('userid');
//        }
        //support get list by tag or category

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        if (empty($order)) {
            $order = $this->input->get_post('od');
        }
        $tid = $this->input->get_post('tid');
        $cid = $this->input->get_post('cid');

        $permit_orders = array('id', 'rank');
        if (!in_array($order, $permit_orders)) {
            $order = null;
        }

        $filter = array();
        if(in_array($status_filter,[PRODUCT_STATUS_DELETED,PRODUCT_STATUS_DRAFT,PRODUCT_STATUS_INAUDIT,PRODUCT_STATUS_AUDITREFUSE,PRODUCT_STATUS_OFFSHELF,PRODUCT_STATUS_PUBLISHED],true)){
            $filter = array(TBL_COLLECTION . '.status' => $status_filter);
        }
	if ($type != 'all') {
	    $filter[TBL_COLLECTION . '.type'] = $type;
	}

        if ($tid) {
            $filter[TBL_PRODUCT_TAG . '.tag_id'] = $tid;
        }
        if ($cid) {
            $filter[TBL_COLLECTION . '.category'] = $cid;
        }

        $res = $this->collection->get_list($_userid, $offset, $limit, $filter, (string)$order);
        //$res {"list":[],"count":22,"total":200,"res":}
        //Do not need output ,I need result
        if (!$output) {
            return $res;
        }
        if (!empty($res['list'])) {
            $res['count'] = count($res['list']);
            foreach ($res['list'] as $key => $row) {
//                $row['like'] = in_array($row['id'],$liked_products) ? 1 : 0;
                $res['list'][$key] = filter_data($row, $this->config->item('json_filter_collection_list'));
                $money = $this->collection->collection_countmoney($row['id']);
                $res['list'][$key]['total_money'] = $money * PROPROTION;
            }
            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(bp_operation_db_not_find, $this->lang->line('hint_collection_empty'));
        }

    }

    public function recommend()
    {
	$res = array(
	    'res' => 0,
	    'id' => 1,
	    'type' => 100,
	    'recommond_image' => 'http://product-album-n.oss-cn-hangzhou.aliyuncs.com/album/other/1%402x.png',
	    'cover_image' => 'http://product-album-n.oss-cn-hangzhou.aliyuncs.com/album/other/1%402x.png',
	    'title' => '',
	    'description' => '',
	);
	$this->_result(bp_operation_ok, $res);
	return;
	$res = $this->collection->recommend();
	//logger('recommond'.var_export($res,true));
	if (!empty($res[0]['id'])) {
	    if (!empty($res[0]['recommond_image'])) {
		$res[0]['cover_image'] =& $res[0]['recommond_image'];
	    }
	    $this->_result(bp_operation_ok, $res[0]);
        } else {
	    $this->_error(bp_operation_db_not_find, $this->lang->line('hint_collection_empty'));
	}
    }

    public function filterListV3($ordered = true, $json = true)
    {
	$this->load->model('product_model', 'product');
        $lists = $this->product->get_filter_list();

        $categories = array();
        foreach ($lists['categorys']['list'] as $key => $category) {
            $category['result_count'] = 0;
            if ($category['parent_id'] != 0) {
                $categories[] = $category;
            }
        }
        $lists['categorys']['list'] = $categories;
        $brand_order = array();
        foreach ($lists['brands']['list'] as $brand) {
            $brand_order[] = $brand['english_name'];
        }
        asort($brand_order);
        $new_brands = array();
        foreach ($brand_order as $k => $v) {
            $new_brands[] = $lists['brands']['list'][$k];
        }
        $lists['brands']['list'] = $new_brands;
        if ($json) {
            echo_json($lists);
        } else {
            return $lists;
        }
    }

    private function dealWithList($list)
    {
        // category
        $category = [];
        //print_r($list);die;
        foreach ($list as $k => $v) {
            $v['is_selected'] = 0;
            $category[$v['level']][] = $v;
        }
        $level = array_keys($category);
        rsort($level);
        $count = [];
        foreach ($level as $i) {
             foreach ($category[$i] as $k => $v) {
                $pos = (int)$i-1;
                if (!isset($category[$pos][0]['id'])) {
                     $category[$i][$k]['result_count'] = $category[$i][$k]['sub_category'][0]['result_count'];
                    continue;
                }
                foreach ($category[$pos] as $m => $n) {
                   if ($v['parent_id'] == $n['id']) {
                        if (!isset($category[$pos][$m]['sub_category'])) {
                           $category[$pos][$m]['sub_category'][0] = array(
                                'id' => $n['id'],
                                'parent_id' => $n['parent_id'],
                                'order' => 0,
                                'level' => $i,
                                'chinese_name' => '全部',
                                'english_name' => 'all',
                                'result_count' => 0,
                                'is_selected' => 0
                            );
                        }
                        if (!isset($v['result_count'])) {
			    $v['result_count'] = 0;
                        }
                        $category[$pos][$m]['sub_category'][] = $v;
                        if (!isset($count[$m])) {
                            $count[$m] = 0;
                        }
                        $count[$m] += $v['result_count'];
                        break;
                    }
                }
                foreach ($count as $x => $y) {
                    $category[$pos][$x]['sub_category'][0]['result_count'] = $y;
                }
            }
        }
        $end_index = end($level);
        $count = 0;
        foreach ($category[$end_index] as $k => $v) {
            $count += $v['result_count'];
        }
        array_unshift($category[$end_index], array('id'=>0,'parent_id'=>0,'chinese_name'=>'全部','english_name'=>'all','order'=>0,'level'=>$end_index,'result_count'=>$count,'is_selected' => 0,'sub_category'=>array(0=>array('chinese_name'=>'','id'=>'1','is_selected' => 0))));
//'sub_category'=>array(0=>array('chinese_name'=>''))

        return $category[$end_index];
    }

    private function update_product_info($product_id, $data=['position' => 0])
    {
	$data['position_at'] = time();
	$this->load->model('product_model', 'product');
	$this->product->update_info($product_id, $data);
	unset($data['position_at']);
	$update_data = array(
            'type' => 'update',
            'condition' => $product_id,
            'data' => $data
        );
        //$this->logger('Update_ES:'.var_export($update_data,true)."\n"); //debug
        $res = curl_es($update_data);

	return true;
    }

}
