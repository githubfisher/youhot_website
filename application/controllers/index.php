<?php
class Index extends User_Controller
{
	const TBL_COLLECTION = 'collection';
	const TBL_SHIPPING = 'shipping';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model', 'user');
        $this->load->model('product_model', 'product');
        $this->load->model('commond_model', 'commond');
        $this->load->model('collection_model', 'collection');
	$this->load->model('superscript_model', 'superscript');
    }

    public function index()
    {
    	$offset = $this->input->get_post('of');
    	$limit = $this->input->get_post('lm');
    	if ($limit != 4) $limit = 4;
	if (empty($offset)) $offset = 0;

	$userid = 0;
    	if($this->is_login){
            $userid = $this->userid;
        }

        $collection = $this->collection->get_list(null, 0, 20, array(self::TBL_COLLECTION . '.status' => COLLECTION_STATUS_PUBLISHED));
        $commond = $this->commond->get_list();
        $brand = $this->product->get_personalized_list_v2($userid,$offset*$limit,$limit,null,3);
	if (isset($collection['list'][$offset]) && isset($commond[$offset]) && ($brand['list'] && (count($brand['list'] == 4)))) {
	    $brand1 = array_slice($brand['list'],0,2);
            $brand2 = array_slice($brand['list'],2,2);
	    $likeProducts = $this->getLikeProduct();
            $data['total'] = $brand['total'];
            $data['group'][] = array(
            	'collection' => $collection['list'][$offset],
		'brand1' => $this->dealWithBrand($brand1, $likeProducts),	
		'brand2' => $this->dealWithBrand($brand2, $likeProducts),
		'commond' => $this->dealWithSingle($commond[$offset], $likeProducts)
	    );
	    $data['brands'] = [];
	} else if ($brand['list'] && (count($brand['list'] > 0))) {
	    $likeProducts = $this->getLikeProduct();
            $data['total'] = $brand['total'];
	    $data['group'] = [];
            $data['brands'] = $this->dealWithBrand($brand['list'], $likeProducts);	    
	} else {
	    $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }

        $this->_result(bp_operation_ok, $data);
    }

    private function getLikeProduct()
    {
    	$likeProducts = array();
    	if($this->is_login && $this->userid){
            $likeProducts = $this->user_model->get_like_products($this->userid);
            $likedProducts = array_column($likeProducts, 'product_id');
        }
 	
	return $likeProducts;
    }

    private function dealWithBrand($brand, $likeProducts)
    {
    	foreach ($brand as $k => $v) {
    	    foreach ($v['list'] as $m => $n) {
    		$brand[$k]['list'][$m] = filter_data($n, $this->config->item('json_filter_product_list'));
                $brand[$k]['list'][$m]['like'] = in_array($n['id'], $likeProducts) ? "1" : "0";
		$dc = floor($n['discount']*10);
                $brand[$k]['list'][$m]['superscript'] = $this->superscript->getList($dc);
    	    }
    	}

    	return $brand;
    }

    private function dealWithSingle($single, $likeProducts)
    {
    	$single['like'] = in_array($single['id'], $likeProducts) ? "1" : "0";

    	return $single;
    }

    public function group()
    {
	logger('Index_group_start:'.time()); // debug
        $offset = $this->input->get_post('of');
        $limit = $this->input->get_post('lm');
        $offset = isset($offset) ? $offset : 0;
        $limit  = $limit == 4 ? $limit : 4;

	$start = microtime(true); // debug
        $all = $this->getAll();
	//$mt = $this->debug('Index_group_getAll_use:', $start); // debug
        if (($all['total'] > 0) && ($all['total'] > $offset)) {
            $likeProducts = $this->getLikeProduct();
	    //$mt = $this->debug('Index_group_getLikeProduct_use:', $mt); // debug
	    $likeProducts = array_column($likeProducts, 'product_id');
	    if (!$userid = $this->userid) {
                $userid = 0;
            }
            $brand = $this->product->get_personalized_list_v3($userid, $all['brand']['list'], $offset*$limit, $limit, 15);
	    //$mt = $this->debug('Index_group_getProduct_use:', $mt); // debug
	    $brand = $this->addPageInfo($brand, $offset, 'page');
	    $brand1 = array_slice($brand,0,2);
	    $brand2 = array_slice($brand,2,2);
            $data['total'] = $all['total'];
            $data['list'][] = array(
                'collection' => $all['collection']['list'][$offset],
                'commond' => $this->dealWithSingle($all['commond']['list'][$offset], $likeProducts),
                'brand1' => $this->dealWithBrand($brand1, $likeProducts),
                'brand2' => $this->dealWithBrand($brand2, $likeProducts),
            );
	    //$mt = $this->debug('Index_group_dealWith_use:', $mt); // debug
	    // $data['unread'] = $this->hasUnread();
	    // $data['userid'] = $userid;
	    // $data['brand_list'] = $all['brand']['list'];
	    logger('Index_group_end:'.time()."\n"); // debug
            $this->_result(bp_operation_ok, $data);          
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    public function brand()
    {
        $offset = $this->input->get_post('of');
        $limit = $this->input->get_post('lm');
        $offset = isset($offset) ? $offset : 0;
        $limit  = !empty($limit) ? $limit : 4;

        $all = $this->getAll();
        $offset = $all['total']*4 + $offset*$limit;
        if ($all['brand']['total'] >= ($offset+$limit)) {
            $likeProducts = $this->getLikeProduct();
            $brand = $this->product->get_personalized_list_v3($all['brand']['list'], $offset, $limit, 3);
            $data['total'] = $all['brand']['total'] - $all['total'] * 4;
            $data['list'] = $this->dealWithBrand($brand, $likeProducts);
            $this->_result(bp_operation_ok, $data);
        } else {
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }
    }

    private function getAll()
    {	
        $userid = 0;
        if($this->is_login){
            $userid = $this->userid;
        }
	logger('Index_getAll_userid:'.$userid); //debug
	//$mt = microtime(true); // debug
        $collection = $this->collection->get_list(null, 0, 20, array(self::TBL_COLLECTION . '.status' => COLLECTION_STATUS_PUBLISHED), 'last_update');
	//$mt = $this->debug('Index_group_getALL_getCollection_use:', $mt); // debug
        $commond = $this->commond->get_list();
	//$mt = $this->debug('Index_group_getALL_getCommond_use:', $mt); // debug
        $brand = $this->product->get_personalized_list_count($userid);
	//$mt = $this->debug('Index_group_getALL_getBrandProduct_use:', $mt); // debug
	$brand = array_filter($brand);
        $com_sum = count($commond);
        $brand_sum = count($brand);
        $brand_min = floor($brand_sum/4);
        $min = $collection['total'] < $com_sum ? $collection['total'] : $com_sum;
        if ($brand_min < $min) {
	    $this->load->model('user_model', 'user');
	    $filter = sprintf('(userid in (%s)) ', implode(',', $this->config->item('recommend_follow_userids')));
	    $commond_brands = $this->user->get_list_v2(0, 40, $filter, null, 0, 1);
	    $coms = array_column($commond_brands['list'], 'userid');
	    $coms = array_filter($coms);
	    $brand = array_merge($brand,$coms);
	    $brand = array_unique($brand);
	    $brand_sum = count($brand);
	    $brand_min = floor($brand_sum/4);
	}
	//$mt = $this->debug('Index_group_getALL_dealWithAll_use:', $mt); // debug
	$total = $min < $brand_min ? $min : $brand_min;
        $all = array(
            'collection' => $collection,
            'commond' => array(
                'total' => $com_sum,
                'list' => $commond,
            ),
            'brand' => array(
                'total' => $brand_sum,
                'list' => $brand,
            ),
            'total' => $total,
	    'userid' => $userid
        );
        return $all;
    }

    private function addPageInfo($brand, $offset = 0, $filed = 'page')
    {
	foreach ($brand as $k => $v) {
	   $brand[$k][$filed] = $offset;
	}
	return $brand;
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

   private function debug($info, $start)
   {
	$mid = microtime(true); // debug
        logger($info.(round($mid - $start, 3) * 1000)); // debug

	return $mid;
   }

   public function getRelateProduct()  //debug
   {
	$list = $this->product->getRelateProByPosition(1, 'author_nickname', 3);

	print_r($list);
   }
}
