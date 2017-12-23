<?php

/**
 * Class Product
 * @method
 */
class Product extends User_Controller
{
    const LIST_TYPE_PUBLISHED = 'publish';
    const LIST_TYPE_SAVED = 'saved';
    const LIST_TYPE_INDEX = 'index';
    const LIST_TYPE_LIKE = 'like';
    const LIST_TYPE_NEW = 'new';
    const LIST_TYPE_HOT = 'hot';
    const LIST_TYPE_ALL = 'all';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model', 'product');
	$this->load->model('superscript_model', 'superscript');
    }

    public function index()
    {
        $this->preview();
    }

    /**
     * 查看商品详情
     * @param null $product_id
     */
    public function preview($product_id = null)
    {
	$app_version = $this->input->get_post('api_version');
	$rt = $this->input->get_post('rt');
	$referer = $this->input->get_post('referer'); // referer recommend product to other people
	$referer = empty($referer) ? 0 : $referer;
        if (empty($product_id)) {
            $product_id = $this->_get_product_id();
        }
        $res = $this->product->info($product_id);
	if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        }
	$res = filter_data($res, $this->config->item('json_filter_product_detail'));
        $tmp = json_decode($res['tmp_img'], true);
        if( isset($tmp['jurl']) ){
	    $url = $tmp['jurl'];
	    $url = urldecode($url);
            $url = parse_url($url);
	    if (isset($url['scheme'])) {
            	if( $url['scheme'] == 'https' ){
                	$jurl = 'https://' . $url['host'];
            	}else{
                	$jurl = 'http://' . $url['host'];
            	}
	    } else {
	    	$jurl = $url['path'];
	    }
        }else{
            $jurl = '';
        }
        //like or not
        $res['like'] = (int)$this->product->check_is_like($product_id, $this->userid);
        $res['jurl'] =  $jurl;
	unset($res['tmp_img']);
        $res['is_followed'] = $this->product->is_followed($this->userid, $res['author']);
	if (!empty($res['property'])) {
	    $property = @json_decode($res['property'], true);
	    if (is_array($property)) {
		$res['property'] = $property;
       	    }
	}
	$res['others'] = $this->getBrandOther($res['author']); // Same Brand Others Product
	if (empty($app_version)) { 
	    logger('低版本APP请求'); //debug  before 2.2.6
	    $other_sum = count($res['others']);
	    if ($other_sum < 3) {
	        $other_others = $this->getBrandOther(rand(1,10));
	   	$res['others'] = array_merge($res['others'], array_slice($other_others, $other_sum));
	    }
	}
	$res['commonds'] = $this->guessCommond($product_id,$res['category'],$res['author'],$res['price_id'],$res['discount_id']); // Guess Your Likes
	$res['brand_follow'] = $this->getBrandFollow($res['author']); // Brand Follower Sum
	$res['sales'] = $this->getSales($res['store']); // Store Sales Promotion 
	$this->product->update_viewed($product_id);  // Increment viewers Sum
	if (isset($this->userid) && $this->userid > 0) {
	    $this->load->model('footmark_model', 'footmark');
            $this->footmark->update($this->userid, $product_id, $referer); // markdown footmark
	}
	$res['referer'] = $referer;
	if ($rt === 'jsonp') {
	    $res['description'] = $res['description'][0]['content'];
	    $callback = $this->input->get_post('callback');
	    exit($callback."(".json_encode($res).");");
	}
        $this->_result(bp_operation_ok, $res);
    }

    private function getSales($store, $field='name')
    {   
        $info = '';
	$this->load->model('store_model', 'store');
        $sales = $this->store->getRecentSales($store, $field);
        if (is_array($sales) && count($sales)) {
            $sales = array_column($sales, $field);
            $info = implode(' ', $sales);
        }

        return $info;
    }

    private function getBrandFollow($brand)
    {
	$this->load->model('user_model', 'user');
	$res = $this->user->get_follow_rows($brand);

	if (is_numeric($res)) {
	    return $res;
	}
	return 0;
    }

    private function getBrandOther($brand)
    {
	$res = $this->product->getBrandOther($brand);
	if (!isset($res[0]['id'])) {
	    return array();
	}
	return $res;
    }

    private function guessCommond($product_id,$ct,$br,$pr,$ds,$order='discount')
    {
	$p['category'] = $ct;
	$p['price'] = $pr;
	$p['brand'] = '[0]';
	$p['discount'] = '[0]';
	$p['lm'] = 13;
	$p['of'] = 0;
	$p['order'] = '["discount","asc"]';
	$result = $this->get_search($p);
	$result = $this->dealWithSearch($result,null,false);
	if (!isset($result['total'])) { 	
	    return array();
        }
	foreach ($result['list'] as $k => $v) {
	    if ($v['id'] == $product_id) {
		unset($result['list'][$k]);
		break;
	    }
	}
	if (count($result['list']) == 13) {
	    unset($result['list'][12]);
	}
	$result['list'] = array_merge($result['list']);
	return $result['list'];
    }
    /**
     * 相关商品
     * 1464680754
     */
    public function relate(){
        $product_id = $this->_get_product_id();
        $limit = $this->input->get_post('lm')?$this->input->get_post('lm'):5;

        $res = $this->product->get_relate_products($product_id,$limit);
        if($res == DB_OPERATION_FAIL){
            $this->_error(bp_operation_fail, '商品不存在');
        }
        $this->_res_list($res);


    }
    public function  ordered_users()
    {
        $product_id = $this->_get_product_id();
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        if(!isset($this->order_model)){
            $this->load->model('order_model');
        }
        $res = $this->order_model->get_users_by_product($product_id,$offset,$limit,array(TBL_CDB_DEAL.'.pre_paid_money >'=>0));
        if (!empty($res['list'])) {

            foreach ($res['list'] as $key => $row) {
                $res['list'][$key] = filter_data($row, $this->config->item('json_filter_user_info_basic'));
            }

            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    /**
     * 私有方法，从post、get里取得product_id参数
     * @return mixed
     */
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
     * 商品对应相册的处理逻辑
     * 封面图，内页展示图，视频url都在这里处理
     * @param $action
     */
    public function album($action)
    {
        $this->_need_login(true);

        if ($action == 'add') {
            $product_id = $this->_get_product_id();
            $album = array();
            $album['content'] = $this->input->post('content');  //image,video  url
            $album['type'] = $this->input->post('type');  //ALBUM_RESOURCE_TYPE_IMAGE....
            $album['position'] = $this->input->post('position');  //ALBUM_RESOURCE_TYPE_IMAGE....
            $res = $this->product->add_album_resource($product_id, $album);
            $this->_deal_res($res);
        } elseif ($action == 'delete') {
            //@todo permission check
            $album_id = $this->input->post('album_id');
            $product_id = $this->input->post('product_id');
            if (empty($album_id)) {
                $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'));
            }
            $res = $this->product->delete_album_resource($album_id,$product_id);
            $this->_deal_res($res);
        } elseif ($action == 'update') {
            /**
             * up_data = [{"album_id":**,"product_id":**,"position":**,"content":**,"type":**},{}]
             */
            $up_data = $this->input->post('up_data');
            $ok_times = 0;
            if (!empty($up_data) && is_array($up_data)) {
                foreach ($up_data as $row) {
                    $album_id = $row['album_id'];
                    unset($row['album_id']);
                    if (empty($album_id)) continue;  //album id没有时候跳过
                    $res = $this->product->update_album_resource($album_id, $row, $row['product_id']);
                    if ($res == DB_OPERATION_OK) {
                        $ok_times++;
                    } else {
                        log_message('error', 'product/album/update get wrong :' . json_encode($up_data));
                    }
                }

                if ($ok_times == count($up_data)) {
                    $this->_success();  //
                } elseif ($ok_times = 0) {
                    $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
                } else {
                    $this->_error(bp_operation_fail, $this->lang->line('bp_operation_partial_fail_hint').$ok_times);
                }

            } else {
                $this->_error(bp_operation_verify_fail, $this->lang->line('bp_operation_data_is_null'));
            }
        }
    }

    /**
     * 商品对应的tag标签的处理
     * @param $action
     */
    public function tags($action)
    {
        $this->_need_login(true);
        $product_id = $this->_get_product_id();
        if ($action == 'save') {
            $tag_ids = $this->input->post('tag_ids');
            $res = $this->product->update_product_tag_relation($product_id, $tag_ids);
            $this->_deal_res($res);
        }

    }

    /**
     * 商品对应的尺寸的处理
     * @param $action
     */
    public function size($action)
    {
        $this->_need_login(true);
        $product_id = $this->_get_product_id();
        if ($action == 'save') {
            $size_ids = $this->input->post('size_ids');
            $res = $this->product->update_product_size_relation($product_id, $size_ids);
            $this->_deal_res($res);
        }
    }

    /**
     * 喜欢一件商品接口
     * @param $like
     */
    public function like($like)
    {
        $this->_need_login(true);
        $product_id = $this->_get_product_id();
	$referer = $this->input->get_post('referer');
        $referer = empty($referer) ? 0 : $referer;
        $res = $this->product->update_product_like_relation($product_id, $this->userid, $like, $referer);
        $this->_deal_res($res);

    }

    /**
     * All type of list
     */

    /**
     * 我的推荐商品列表，一般用在首页列表
     */
    public function list_for_me()
    {
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        ($limit<1) && $limit = 10;
        if($this->is_login){
            $res = $this->product->get_personalized_list($this->userid,$offset,$limit);
        }else{

            $res = $this->_get_list(self::LIST_TYPE_INDEX,false);
        }
        $this->_res_list($res);
    }

    /**
     * 获取指定设计师的商品列表
     */
    public function list_for_sl()
    {
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        ($limit<=10) && $limit = 2;

        if($this->is_login){
            //登录返回
            $res = $this->product->get_personalized_list_v2($this->userid,$offset,$limit);
        }else{
            //非登录返回
            //$userids = Array( 1, 2, 3);
            $userids = 0;
            $res = $this->product->get_personalized_list_v2($userids,$offset,$limit);
        }
        $this->_res_list_v2($res);
    }
    
    /**
     * 对取得的商品列表内容处理并显示
     * @param $res
     */
    private function _res_list_v2($res){
        //        var_dump($res['list']);
        if (!empty($res['list'])) {

            $liked_products = array();
            if ($this->userid && $this->is_login) {
                $res2 = $this->user_model->get_like_products($this->userid);
                $liked_products = array_column($res2, 'product_id');    //my liked products
            }

            foreach ($res['list'] as $k => $v) {
                foreach($v['list'] AS $key=>$row){
                    $res['list'][$k]['list'][$key] = filter_data($row, $this->config->item('json_filter_product_list'));
                    $res['list'][$k]['list'][$key]['like'] = in_array($row['id'], $liked_products) ? "1" : "0";   //like or not
                    $rd = $row['presale_end_time'] - (int)time();
                    $res['list'][$k]['list'][$key]['remain_days'] = $rd;
                    //sale status
                    $res['list'][$k]['list'][$key]['sale_status'] = $this->_amend_sale_status($row['presale_end_time'],$row['status']);
                }
            }
            $res['count'] = count($res['list']);

            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function list_for_new()
    {
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        ($limit<1) && $limit = 10;
        $res = $this->_get_list(self::LIST_TYPE_NEW,false);
        $this->_res_list($res);
    }

    public function list_for_hot()
    {
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        ($limit<1) && $limit = 10;
        $res = $this->_get_list(self::LIST_TYPE_HOT,false);
        $this->_res_list($res);
    }

    /**
     * Product list of what I have published 用户的已发布商品列表
     * @param userid
     * @return {
     *
     *
     * }
     */

    public function published_list()
    {
        $this->_get_list(self::LIST_TYPE_PUBLISHED);

    }

    /**
     * Product list of what I have published
     * @param userid
     */

    public function saved_list()
    {
        $this->_get_list(self::LIST_TYPE_SAVED);

    }

    /**
     * 我的所有商品，包括草稿，未发布，发布.....
     */
    public function my_list()
    {
        $this->_get_list(self::LIST_TYPE_ALL);

    }

    /**
     * 我喜欢的商品列表
     */
    public function like_list()
    {
        $this->_need_login(true);
        $this->_get_list(self::LIST_TYPE_LIKE);
    }

    /**
     * 喜欢商品id的用户列表
     */

    public function  like_users()
    {
        $product_id = $this->_get_product_id();
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $res = $this->product->get_liked_users($product_id, $offset, $limit);
        if (!empty($res['list'])) {
            foreach ($res['list'] as $key => $row) {
                $res['list'][$key] = filter_data($row, $this->config->item('json_filter_user_info_basic'));
            }
            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);
        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    /**
     * 取商品列表的一端封装，上面的published list,saved list都是从这里调用的
     * @param $list_type
     * @param bool|true $output
     * @return mixed
     */
    private function _get_list($list_type, $output = true)
    {
        //统一的参数
        $_userid = $this->input->get_post('userid');
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $order = $this->input->get_post('od');
        $tid = $this->input->get_post('tid');
        $cid = $this->input->get_post('cid');
        $status = $this->input->get_post('status');
        $permit_orders = array('id', 'rank');
        if (!in_array($order, $permit_orders)) {
            $order = null;
        }
        $filter = array();
        if ($tid) {
            $filter[TBL_PRODUCT_TAGS . '.brand_id'] = $tid; // tag_id -> brand_id , product_tag -> product_tags
        }
        if ($cid) {
	    // 三级以下分类合并到三级分类，借助category_map配置文件归类 by fisher 2017-04-19
            $this->config->load('categorys', TRUE); // 改用category_map配置文件记录父子分类间关系
            $categorys_map = $this->config->item('map', 'categorys');
            if (array_key_exists($cid, $categorys_map['women'])) {
                $filter[TBL_PRODUCT . '.category'] = $categorys_map['women'][$cid];
            } else {
                $filter[TBL_PRODUCT . '.category'] = $cid;
            }
        }
        switch ($list_type) {
            case self::LIST_TYPE_PUBLISHED :
                $filter[TBL_PRODUCT . '.status'] = PRODUCT_STATUS_PUBLISHED;
                break;
            case self::LIST_TYPE_SAVED :
                $filter[TBL_PRODUCT . '.status'] = PRODUCT_STATUS_DRAFT;
                break;
            case self::LIST_TYPE_INDEX :
                $filter[TBL_PRODUCT . '.status'] = PRODUCT_STATUS_PUBLISHED;
                $order = 'rank';
                break;
            case self::LIST_TYPE_LIKE :
                $_userid = null;
                $filter[TBL_PRODUCT_LIKER . '.userid'] = $this->userid;
                break;
            case self::LIST_TYPE_ALL :
                if($status !== false){
                    $filter[TBL_PRODUCT . '.status'] = (int) $status;
                }
                break;
            case self::LIST_TYPE_NEW:
                $filter[TBL_PRODUCT . '.status'] = PRODUCT_STATUS_PUBLISHED;
                $order = 'save_time';
                break;
            case self::LIST_TYPE_NEW:
                $filter[TBL_PRODUCT . '.status'] = PRODUCT_STATUS_PUBLISHED;
                $order = 'lover_count';
                break;
            default :
                break;
        }
        $res = $this->product->get_list($_userid, $offset, $limit, $filter, (string)$order);
        if (!$output) {
            return $res;
        }
        $this->_res_list($res);
    }

    /**
     * 对取得的商品列表内容处理并显示
     * @param $res
     */
    private function _res_list($res){
        if (!empty($res['list'])) {
            $liked_products = array();
            if ($this->userid && $this->is_login) {
                $res2 = $this->user_model->get_like_products($this->userid);
                $liked_products = array_column($res2, 'product_id');    //my liked products
            }
            foreach ($res['list'] as $key => $row) {
                $res['list'][$key] = filter_data($row, $this->config->item('json_filter_product_list'));
                $res['list'][$key]['like'] = in_array($row['id'], $liked_products) ? "1" : "0";   //like or not
		$dc = floor($row['discount']*10);
                $res['list'][$key]['superscript'] = $this->superscript->getList($dc);
            }
            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);
        } else {
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    /**
     * 商品搜索
     * @return string
     */
    public function search()
    {
        // 实例化一个搜索类 search_obj
        $word = $this->input->get_post('kw');
        $offset = $this->input->get_post('of') ? $this->input->get_post('of') : 0;
        $limit = $this->input->get_post('lm') ? $this->input->get_post('lm') : 10;
        $this->load->library('opensearch');
        $client = $this->opensearch->getClient();
        $search_obj = new CloudsearchSearch($client);
        // 指定一个应用用于搜索
        $search_obj->addIndex($this->opensearch->getAppname());
        // 指定搜索关键词
        $search_obj->setQueryString("all:" . $word);
        $search_obj->setStartHit((int)$offset);
        $search_obj->setHits((int)$limit);
	// by fisher at 2017-03-28
	$pid = $this->input->get_post('pr');
	$cid = $this->input->get_post('cate');
	$bid = $this->input->get_post('br');	
	$did = $this->input->get_post('dr');
	if (!empty($pid)) {
	    $pid = json_decode($pid);
	    if (!empty($pid[0])) {
		$str = '(';
		foreach ($pid as $v) {
		    $str .= 'price_id ='.$v.' OR ';			
		}
		$str = rtrim($str, ' OR ');
		$str .= ')';	
		$search_obj->addFilter($str);
	    }
	}		
	if (!empty($cid)) {
	    $cid = json_decode($cid);
	    if (!empty($cid[0])) {
		$this->config->load('categorys', TRUE); // 改用category_map配置文件记录父子分类间关系 by fisher at 2017-04-19
                $categorys_map = $this->config->item('map', 'categorys');
                $new_cid = array();
                foreach ($cid as $c) {
                    if (array_key_exists($c, $categorys_map['women'])) {
                        foreach ($categorys_map['women'][$c] as $value) {
                            array_push($new_cid, $value);
                        }
                    }
                }
                $cid = array_merge($cid, $new_cid);
		$str = '(';
		foreach ($cid as $v) {
		    $str .= 'category_id ='.$v.' OR ';			
		}
		$str = rtrim($str, ' OR ');
		$str .= ')';	
		$search_obj->addFilter($str);
	    }
	}
	if (!empty($bid)) {
	    $bid = json_decode($bid);
	    if (!empty($bid[0])) {
		$str = '(';
		foreach ($bid as $v) {
		    $str .= 'author ='.$v.' OR ';			
		}
		$str = rtrim($str, ' OR ');
		$str .= ')';	
		$search_obj->addFilter($str);
	    }
	}
	if (!empty($did)) {
	    $did = json_decode($did);
	    if (!empty($did[0])) {
		$str = '(';
		foreach ($did as $v) {
		    $str .= 'discount_id ='.$v.' OR ';			
		}
		$str = rtrim($str, ' OR ');
		$str .= ')';	
		$search_obj->addFilter($str);
	    }
	}
	$search_obj->addFilter(' status = 1');
	// 指定返回的搜索结果的格式为json
        $search_obj->setFormat("json");
	// 执行搜索，获取搜索结果
        $json = $search_obj->search();
	//print_r(json_decode($json, true));die;
	$json = json_decode($json, true);
	$myLike = $this->product->get_my_likes($this->userid);
	//print_r($myLike);die;
	foreach($json['result']['items'] as $k => $v) {
	    //$json['result']['items'][$k]['cover_image'] = $json['result']['items'][$k]['cover_image'].'?x-oss-process=image/resize,h_140'; // 缩放图片
	    $json['result']['items'][$k]['like'] = 0;
	    if (!empty($myLike)) {
		foreach ($myLike as $x => $y) {
		    if ($y['product_id'] == $v['id']) {
			$json['result']['items'][$k]['like'] = 1;
		    }
		}
	    }
	}
	$json = json_encode($json);
        echo_json($json, TRUE);
    }

    /**
     * 搜索的提示词
     * @return string
     */
    public function suggest()
    {
        // 实例化一个搜索类 search_obj
        $word = $this->input->get_post('kw');
        $this->load->library('opensearch');
        $client = $this->opensearch->getClient();
        $suggest = new CloudsearchSuggest($client);
        $opts = array("index_name" => $this->opensearch->getAppname(), "suggest_name" => "suggest", "hit" => 10, "query" => $word);
        $json = $suggest->search($opts);
        echo_json($json, TRUE);
    }

    /**
     * 对销售状态的处理。是通过presale_end_time这个值来判断的，代表的是预售结束时间。
     * @param $presale_end_time
     * @param $product_status
     * @return int
     */
    private function _amend_sale_status($presale_end_time,$product_status)
    {
        $sale_satus = PRODUCT_STATUS_PRESALE_NOT_START;
        if($product_status == PRODUCT_STATUS_PUBLISHED){
            if ($presale_end_time < time()) {
                $sale_satus = PRODUCT_STATUS_PRESALE_END;
            } else {
                $sale_satus = PRODUCT_STATUS_PRESALE_GOINON;
            }
        }
        return $sale_satus;
    }

    public function filterProduct($output = true)
    {
	$start = microtime(true);
	$rt = $this->input->get_post('rt');
        //统一的参数
        $cid = $this->input->get_post('cate'); // 分类ID // 改用多选，传json字符串 at 2017-03-21
        if (!empty($cid)) { // 改用多选，传json字符串 at 2017-03-21
            $cid = json_decode($cid);
            if(!empty($cid[0])) {
                $this->config->load('categorys', TRUE); // 改用category_map配置文件记录父子分类间关系 by fisher at 2017-04-19
                $categorys_map = $this->config->item('map', 'categorys');
                $new_cid = array();
                foreach ($cid as $c) {
                    if (array_key_exists($c, $categorys_map['women'])) {
                        foreach ($categorys_map['women'][$c] as $value) {
                            array_push($new_cid, $value);
                        }
                    }
                }
                $cids = array_merge($cid, $new_cid);
                $cids = array_unique($cids);
                $cids = array_values($cids);
            } else {
		$cids = $cid;
	    }
	    $cids = json_encode($cids);
        } else {
	    $cids = $cid;
	}
        $params['category'] = $cids;
	//echo '<pre>'; print_r($params['category']); die;
        $params['brand'] = $this->input->get_post('br'); // 品牌ID  // 改用多选，传json字符串 at 2017-03-21
	if ( $params['brand'] == '[0]' || $params['brand'] == '["0"]' || $params['brand'] == "['0']") {
	    $params['brand'] = 0;
        }
	$sort = $this->input->get_post('sort');
	/*if (empty($sort)) {
	    $sort = json_encode([['position','desc']]);
	} else {
	    $sort = json_decode($sort, true);
	    $sort = json_encode(array_merge([['position','desc']], $sort)); 
	}*/
	//logger('FilterProductSort:'.$sort);// debug
        $params['price'] = $this->input->get_post('pr'); // 价格区间标签ID  // 改用多选，传json字符串 at 2017-03-21
        $params['discount'] = $this->input->get_post('dr'); // 折扣区间标签ID  // 改用多选，传json字符串 at 2017-03-21
        $params['of'] = (int)$this->input->get_post('of');
        $params['lm'] = (int)$this->input->get_post('lm');
        $params['sort'] = $sort; // 排序字段
        $params['status'] = $this->input->get_post('status'); // 商品状态
        $params['count'] = $this->input->get_post('count');
        $params['store'] = $this->input->get_post('store');
        $params['q'] = $this->input->get_post('kw');
        $result = $this->get_search($params);
	if (!empty($params['q'])) {
	    $userid = empty($this->userid) ? 4791 : $this->userid;
	    $end = microtime(true);
	    $use = round($end - $start, 3) * 1000;
	    $this->product->saveSearchHistory(['userid' => $userid, 'keywords' => $params['q'], 'used_time' => $use, 'created_at' => time()]);
	}
	//exit($result);
	$params['category'] = $cid;
	if ($rt === 'jsonp') {
            $result = $this->dealWithSearch($result, $params['q'], false);
	    $result['brand'] = '';
	    $brand_id = json_decode($params['brand'], true);
	    $brand_id = $brand_id[0];
	    $this->load->model('user_model', 'user');
	    $brand = $this->user->get_user($brand_id);
	    if (isset($brand['userid'])) {
		$result['brand'] = $brand['nickname'];
	    }	
	    $callback = $this->input->get_post('callback');
	    exit($callback."(".json_encode($result).");");
	}	
        $this->dealWithSearch($result, $params['q'], true, $params);	
    }

    private function get_search($params,$api='search')
    {
        $url = 'http://114.55.40.32/index.php/api/'.$api.'?'.http_build_query($params);
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($con, CURLOPT_TIMEOUT, 10);
         
         return curl_exec($con);
    }

    private function dealWithSearch($res, $kw, $type=true, $params=null)
    {
        $res = json_decode($res, true);
        if (isset($res['hits']) && $res['hits']['total']) {
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
	    $result['filterList'] = $this->filterListV3(true, false);
            if (is_array(@$res['aggregations']['category_count']['buckets']) && count($res['aggregations']['category_count']['buckets'])) {
                foreach ($res['aggregations']['category_count']['buckets'] as $key => $value) {
                    foreach ($result['filterList']['categorys']['list'] as $x => $y) {
                        if ($y['id'] == $value['key']) {
                            $result['filterList']['categorys']['list'][$x]['result_count'] = $value['doc_count'];
                            break;
                        }
                    }
                }
            }
	    $result['filterList']['categorys']['list'] = $this->dealWithList($result['filterList']['categorys']['list']);
	    /*if (is_array($params)) {
		$result['filterList'] = $this->addSelectedOption($result['filterList'], $params); 
	    }*/
	    if (isset($kw)) {
	        $result['find_brand'] = $this->dealWithKeywords($kw);
	    } else {
		$result['find_brand'] = array();
	    }
	    if (!$type) {
		return $result;
	    }
            exit(json_encode($result));
        } else {
	    if (!$type) {
		return false;
	    }
            log_message('error', 'No product list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    private function addSelectedOption($list, $params)
    {
	$list['categorys']['list'] = $this->findSelectedCategorys($list['categorys']['list'], $params['category']);	
	$list['brands']['list'] = $this->findSelectedOptions($list['brands']['list'], $params['brand'], 'brandid');	
	$list['prices']['list'] = $this->findSelectedOptions($list['prices']['list'], $params['price']);	
	$list['discounts']['list'] = $this->findSelectedOptions($list['discounts']['list'], $params['discount']);	
	$list['sorts']['list'] = $this->findSelectedSorts($list['sorts']['list'], $params['sort']);	
	$list['stores']['list'] = $this->findSelectedOptions($list['stores']['list'], $params['store']);	
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
	$options = json_decode($options, true);
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
	$options = json_decode($options, true);
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

    private function dealWithKeywords($kw)
    {
	$brand = array();
	if ($this->userid && $this->is_login) {
	    $like_users = $this->user_model->get_followed_list($this->userid); 
	}
	$brands = $this->user_model->get_list_v3();
	//print_r($brands);die;
	foreach ($brands as $k => $v) {
	    $pos = stripos($kw, $v['nickname']);
	    if ($pos || ($pos === 0)) {
		$v['is_like'] = 0;
		if (isset($like_users) && is_array($like_users) && count($like_users)) {
		    $users = array_column($like_users, 'follow_userid');
		    $v['is_like'] = in_array($v['userid'], $users) ? 1 : 0;
		}
		$brand[] = $v;
	    }
	}

	return $brand;
    }

    public function commond_product()
    {
        $data['data'] = $this->input->get_post('data');
        $data['id'] = $this->input->get_post('id');
        $this->load->model('commond_model', 'commond');
        $up_data = json_decode($data['data'], true);
        if ($up_data['is_commond'] == 1) {
            $this->commond->add($data['id']);
            $this->product->add_commond($data['id']);
        } else {
            $this->commond->delete($data['id']);
            $this->product->delete_commond($data['id']);
        }
        $this->updateSearchProduct($data);
        $this->_result(bp_operation_ok,array());
    }

    private function updateSearchProduct($data)
    {
        $result = $this->post_search($data);
        return $result;
    }

    private function post_search($params, $api='update')
    {
        $url = 'http://114.55.40.32/index.php/api/'.$api;
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($con, CURLOPT_POST, 1);
        curl_setopt($con, CURLOPT_POSTFIELDS, $params);
        curl_setopt($con, CURLOPT_TIMEOUT, 10);
         
        return curl_exec($con);
    }
    public function common_cover_upload()
    {
	$product_id = $this->input->get_post('product_id');
	$config['upload_path']      = './uploads/';
        $config['allowed_types']    = 'gif|jpg|png';
        $config['max_size']     = 2048;
        $config['max_width']        = 0;
        $config['max_height']       = 0;

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('file')) {
            $data = array('status' => 0, 'error' => $this->upload->display_errors(),'product_id' => $product_id);
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
	    $this->load->model('commond_model', 'commond');
	    $result = $this->commond->update($product_id, array('cover_url' => $url));
	    if ($result) {
            	$data = array('status' => 1, 'upload_data' => $file, 'product_id' => $product_id, 'url' => $url);
	    } else {
		$data = array('status' => 0, 'upload_data' => $file, 'product_id' => $product_id, 'url' => $url, 'update' => false);
	    }
	    @unlink($file['full_path']);
        }
	exit(json_encode($data));
    }
    private function _safe_file_name($fn){
        $ext = get_file_ext($fn);
        return str_replace('=', '', base64_encode($fn)).'.'.$ext;
    }

    private function dealWithList($list)
    {
        $category = [];
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
        /*array_unshift($category[$end_index], array(
	    'id'=>0,
	    'parent_id'=>0,
	    'chinese_name'=>'全部',
	    'english_name'=>'all',
	    'order'=>0,'level'=>$end_index,
	    'result_count'=>$count,
	    'is_selected' => 0,
	    'sub_category'=> array(
	        0 => array(
	  	    'chinese_name' =>'' ,
		    'id' => '1',
		    'is_selected' => 0
		)
	    )
	));*/
        
        return $category[$end_index];
    }

    public function filterListV3($ordered = true, $json = true)
    {
        $lists = $this->product->get_filter_list();
        //  梳理category
        $categories = array();
        // 把分类下的子分类集中在一起，二级分类->一层分类
        foreach ($lists['categorys']['list'] as $key => $category) {
            $category['result_count'] = 0;
            if ($category['parent_id'] != 0) {
                $categories[] = $category;
            }
        }
        $lists['categorys']['list'] = $categories;
        $brand_order = array(); // brands 排序 by fisher at 2017-04-22
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

    public function hotKeys()
    {
	$hotKeys = [
	    [
		'name' => 'Topshop'//'Balenciaga'//'Gucci'
	    ],
	    [
		'name' => 'Canada Goose'//'Vetements'//'Mercer'
	    ],
	    [
		'name' => 'Stuart Weitzman'//'Champion'//'pearly'
	    ],
	    [
		'name' => 'Michael Kors'//'UGG'//'T恤'
	    ],
	    [
		'name' => '外套'//'卫衣'//'Nicholas kirkwood'
	    ],
	    [
		'name' => '毛衣'//'靴子'//'拖鞋'
	    ],
	    [
		'name' => '围巾'//'Sneaker'//'Fenty puma'
	    ],
	    [
		'name' => '运动鞋'//'Loafer'//'Golden Goose'
	    ]
	];
	$unread = $this->hasUnread();
	exit(json_encode(['list' => $hotKeys, 'unread' => $unread]));
    }

    public function brandPage()
    {
	$brand = $this->input->get_post('brand');

	$this->_data['brandId'] = $brand;
	$this->_data['signature'] = getSignPackage();
	$this->layout->set_layout('layout/h5');
	$this->setOutputTpl('h5/brand');
	$this->_result(bp_operation_ok,$this->_data);
    }

    public function productDetail()
    {
	$id = $this->input->get_post('productId');
	$referer = $this->input->get_post('referer');
	$referer = empty($referer) ? 0 : $referer;
	$this->_data['referer'] = $referer;
	$this->_data['productId'] = $id;
	$this->_data['signature'] = getSignPackage();
	$this->layout->set_layout('layout/h5');
        $this->setOutputTpl('h5/detail');
        $this->_result(bp_operation_ok,$this->_data);
    }

    public function productAvaliable()
    {
	$id = $this->input->get_post('productId');
	if (is_numeric($id)) {
	    $res = $this->product->getProductAvaliable($id);
	    if (isset($res['available_size'][0]['size_id']) && $res['available_color'][0]['color_id']) {
		$res['res'] = 0;
		$data =& $res;
	    } else {
		$data['res'] = 4;
		$data['hint'] = '未获取有效颜色或尺码，请重试';
	    }
	    exit(json_encode($data));
        } else {
	    $this->_error(bp_operation_fail, '缺少参数,请重试');
	}
    }

    public function able()
    {
	$id = $this->input->get_post('productId');
    }

    public function getSignature()
    {
	$sign = getSignPackage();
	exit(json_encode($sign));
    }

    public function searchc()
    {
        $this->_data['brand'] = $this->input->get_post('brand');
	$this->_data['category'] = $this->input->get_post('category');
	$this->_data['price'] = $this->input->get_post('price');
	$this->_data['discount'] = $this->input->get_post('discount');
	$this->_data['store'] = $this->input->get_post('store');
	$this->_data['of'] = $this->input->get_post('of');
	$this->_data['lm'] = $this->input->get_post('lm');
	$this->_data['order'] = $this->input->get_post('order');
	$this->_data['kw'] = $this->input->get_post('kw');
        $this->layout->set_layout('layout/h5');
        $this->setOutputTpl('h5/search');
        $this->_result(bp_operation_ok,$this->_data);	
    }

    private function hasUnread()
   {
        if (isset($this->userid)) {
            $offset = 0;
            $limit = 1000;
            $this->load->model('message_model', 'msg');
            $msgs = $this->msg->getUnreadList($this->userid, $offset, $limit);
            $res = $this->dealWithUnreadList($msgs);
            $type1 = $this->msg->getMsgType(1);
            if (is_array($type1) && count($type1)) {
                $read1 = $this->msg->getReadType(1, $this->userid);
                if (is_array($read1) && count($read1)) {
                    if ($this->dealWithUnreadType1($type1, $read1)) {
                       $res = 0;
                    }
                } else {
                    $res = 0;
                }
            }
        } else {
            $res = 1;
        }
        return $res;
   }

   private function dealWithUnreadList($list)
   {
        $unread = 1;
        if (is_array($list) && count($list)) {
            $unread = 0;
        }
        return $unread;
   }

   private function dealWithUnreadType1($list, $read)
   {
        $ids = array_column($list, 'id');
        $readIds = array_column($read, 'msgid');
        foreach ($ids as $v) {
            if (!in_array($v, $readIds)) {
                return true;
                break;
            }
        }
        return false;
   }

   public function jump()
   {
        $this->layout->set_layout('layout/h5');
        $this->setOutputTpl('h5/jump');
        $this->_result(bp_operation_ok,$this->_data);
   }

   public function update_product_info($product_id)
   {
	$data = json_decode($this->input->get_post('data'), true);
	if (!is_array($data) || !count($data)) {
	    exit(json_encode(['res' => 10, 'hit' => 'no data']));
	}
        $this->load->model('product_model', 'product');
        $this->product->update_info($product_id, $data);
        $update_data = array(
            'type' => 'update',
            'condition' => $product_id,
            'data' => $data
        );
        $res = curl_es($update_data);

        exit(json_encode(['res' => 0, 'hit' => 'true']));
   }   
}
