<?php

class Product_model extends MY_Model
{
    const PRODUCT_INFO_CACHE_TIME = 300; // 5 min
    const PRODUCT_LIST_CACHE_TIME = 300; // 5 min
    const FOLLOW_RANK_WEIGHT = 1000;   //Rank weight of followed product
    const CACHE_OBJECT = 'product';
    const LIST_KEY = 'product_list';

    function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    /**
     * product info cache 5 minutes
     * @param $product_id
     * @param $additional  detail liked_users basic
     * @return string
     */
    private static function _mem_key_product_info($product_id, $additional = '')
    {
        if ($additional != 'detail' && $additional != 'liked_users') {
            $additional = 'basic';
        }
        return 'product:' . $product_id . ':' . $additional;
    }

    /**
     * product info cache 5 minutes.
     * Only public list . User's list not cached
     * @param $product_id
     * @return string
     */
    private static function _mem_key_product_list($userid, $offset, $limit, $filter, $order)
    {
        //userid should not be null
        if (!empty($userid)) {
            return false;
        }
        //users' liked product not cached
        if (array_key_exists(TBL_PRODUCT_LIKER . '.userid', $filter)) {
            return false;
        }

        $key = array(self::CACHE_OBJECT, 'list', $offset, $limit, serialize($filter), $order);
        return md5(implode(':', $key));
    }


    /**
     * 支持我的发布商品,按照tag|cat筛选
     * $filter=array('product.category'=>cat_id,'product_tag.tag_id'=>tag_id)
     * 查看设计师的发布商品,按照tag|cat筛选
     * 查看tag|cat商品
     * @param $author
     * @param int $offset
     * @param int $limit
     * @param array $filter
     * @return array
     */
    public function get_list($author = null, $offset = 0, $limit = 20, $filter = array(), $order = null ,$status=1)
    {
        $key = $this->_mem_key_product_list($author, $offset, $limit, $filter, $order);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get product list from cache');
            return $res;
        }
        $this->db_slave->start_cache();
        $this->db_slave->from(TBL_PRODUCT);
        $this->db_slave->select(TBL_PRODUCT . '.*,'. TBL_USER . '.chinese_name,' . TBL_USER . '.facepic as author_facepic,' . TBL_USER . '.nickname as author_nickname,' . TBL_USER . '.introduce as author_introduce, store.name as store_name,country.flag_url');
        $this->db_slave->join(TBL_USER, TBL_PRODUCT . '.author = ' . TBL_USER . '.userid', 'left');
        $this->db_slave->join('store', TBL_PRODUCT . '.store = store.id', 'left');
        $this->db_slave->join('country', 'store.country = country.name', 'left');
        if (!empty($author)) {
            $this->db_slave->where(TBL_PRODUCT . '.author', $author);
        }
        if (!empty($filter)) {
            if (array_key_exists(TBL_PRODUCT_TAGS . '.brand_id', $filter)) {
                $this->db_slave->join(TBL_PRODUCT_TAGS, TBL_PRODUCT_TAGS . '.pid = ' . TBL_PRODUCT . '.id', 'left');
            }
            if (array_key_exists(TBL_PRODUCT_LIKER . '.userid', $filter)) {
		$this->db_slave->select(TBL_PRODUCT_LIKER.'.referer');
                $this->db_slave->join(TBL_PRODUCT_LIKER, TBL_PRODUCT_LIKER . '.product_id = ' . TBL_PRODUCT . '.id', 'left');
		$this->db_slave->order_by(TBL_PRODUCT_LIKER . '.created_at', 'desc');
            }
	    if (is_array(@$filter[TBL_PRODUCT . '.category'])) { // 分类集合情况 by fisher at 2017-04-19
                $categorys = $filter[TBL_PRODUCT . '.category'];
                $this->db_slave->where_in('category', $categorys);
                unset($filter[TBL_PRODUCT . '.category']);
            }
            $this->db_slave->where($filter);
        }
        $this->db_slave->where(TBL_PRODUCT . '.cover_image !=', '');
	if (!array_key_exists(TBL_PRODUCT_LIKER . '.userid', $filter)) {
	    $this->db_slave->where(TBL_PRODUCT . '.status =', $status); // by fisher at 2017-04-23
	}
        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_PRODUCT);
	//echo $this->db_slave->last_query();die;
        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (!empty($order)) {
            $this->db_slave->order_by(TBL_PRODUCT . '.' . $order, 'desc');  //Desc sort
        } else {
	   $this->db_slave->order_by(TBL_PRODUCT.'.rank', 'desc');
	   $this->db_slave->order_by(TBL_PRODUCT.'.save_time', 'desc');
	}
        $this->db_slave->order_by(TBL_PRODUCT . '.id', 'desc');  //Desc sort
        $query = $this->db_slave->get(TBL_PRODUCT);
	//echo $this->db_slave->last_query();die;
        $this->db_slave->flush_cache();
        $data = array('total' => $total, 'list' => get_query_result($query));
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $data, self::PRODUCT_LIST_CACHE_TIME);
        }

        return $data;


    }

    /**
     *
     * @param null $userid
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function get_personalized_list($userid, $offset = 0, $limit = 20, $filter = null)
    {
        $key = md5('pplist:' . $userid . ':' . $offset . ':' . $limit);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get personalized product list from cache');
            return $res;
        }

        empty($filter) && $filter = array(TBL_PRODUCT . '.status' => PRODUCT_STATUS_PUBLISHED);
        $this->db_slave->where($filter);
        $total = $this->db_slave->count_all_results(TBL_PRODUCT);

        /**
         * 规则:
         * 总的排序按rank排序
         * 我关注的设计师的未结束衣服+1000
         */
        $query = $this->db_slave->query('SELECT product.id FROM `product` left join user_fo_user on product.author=user_fo_user.follow_userid WHERE user_fo_user.userid=' . $userid . ' and product.presale_end_time>UNIX_TIMESTAMP()');
        //echo $this->db_slave->last_query();die;
        $ids = get_query_result($query);
        $ids = array_column($ids, 'id');

        // //SELECT id,title,rank,IF(id in (SELECT product.id FROM `product` left join user_fo_user on product.author=user_fo_user.follow_userid WHERE user_fo_user.userid=1 and product.presale_end_time>NOW()) ,rank+100,rank) as new_rank from product order by new_rank desc
        $this->db_slave->select(TBL_PRODUCT . '.*,' . TBL_USER . '.facepic as author_facepic,' . TBL_USER . '.nickname as author_nickname,' . TBL_USER . '.introduce as author_introduce');
        if (empty($ids)) {
            $this->db_slave->select(TBL_PRODUCT . '.rank as new_rank');
        } else {
            $this->db_slave->select('IF(product.id in (' . implode(',', $ids) . ') ,product.rank+' . self::FOLLOW_RANK_WEIGHT . ',product.rank) as new_rank', false);
        }
        $this->db_slave->join(TBL_USER, TBL_PRODUCT . '.author = ' . TBL_USER . '.userid', 'left');
        $this->db_slave->where($filter);
        $this->db_slave->offset($offset);

        (!empty($limit)) && $this->db_slave->limit($limit);
        $this->db_slave->order_by('new_rank', 'desc');  //Desc sort

        $query = $this->db_slave->get(TBL_PRODUCT);

        $data = array('total' => $total, 'list' => get_query_result($query));
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $data, self::PRODUCT_LIST_CACHE_TIME);
        }

        return $data;


    }
    public function get_personalized_list_v2($userid, $offset = 0, $limit = 2, $filter = null, $sum = 5)
    {
        $key = md5('pplistv2:' . $userid . ':' . $offset . ':' . $limit . ':' . $sum);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get personalized product list from cache');
            return $res;
        }
        //empty($filter) && $filter = array(TBL_PRODUCT . '.status' => PRODUCT_STATUS_PUBLISHED);
        //$this->db_slave->where($filter);
        //$total = $this->db_slave->count_all_results(TBL_PRODUCT);
        // var_dump($total); die;
        /**
         * 规则:
         * 总的排序按rank排序
         */
        if( $userid==0 ){
            //指定设计师
            //$ids = $userid;
            //$total = count($ids);
            //$query = $this->db_slave->query('SELECT count(*) AS count FROM `user` WHERE usertype=2 AND isblocked=0 AND istop>0');
            //$ids = get_query_result($query);
            //$total = $ids[0]['count'];

            $query = $this->db_slave->query('SELECT userid FROM `user` WHERE usertype=2 AND isblocked=0 AND istop>0 ORDER by istop DESC');
            $ids = get_query_result($query);
            $ids = array_column($ids, 'userid');
	    $total = count($ids);
            //echo $this->db_slave->last_query();die;
        }else{
            //我关注的设计师
            // $query = $this->db_slave->query('SELECT count(*) AS count
            //     FROM `user_fo_user` u, `product` 
            //     WHERE u.userid='.$userid.' AND u.follow_userid=product.author AND product.sell_type=2  AND product.status=1
            //     GROUP BY product.author');
            // $ids = get_query_result($query);
            // $total = $ids[0]['count'];
            // var_dump($total); die;
            //$query = $this->db_slave->query('SELECT product.author FROM `user_fo_user` u, `product` WHERE u.userid='.$userid.' AND u.follow_userid=product.author AND product.sell_type=2  AND product.status=1 GROUP BY product.author ORDER BY product.rank DESC ');
                // LIMIT '. (int)$offset .','. (int)$limit); // 不在这里做分页，和后面的品牌合并后，一起做分页
	    $query = $this->db_slave->query('SELECT user.userid,user.nickname FROM `user_fo_user` u LEFT JOIN `user` ON u.follow_userid=user.userid WHERE u.userid='.$userid); // by fisher 2017-04-19
            //$ids = get_query_result($query);
            //$ids = array_column($ids, 'userid'); // by fisher at 2017-04-19
            //print_r($ids);exit;
            //echo $this->db_slave->last_query();die;

            //$query = $this->db_slave->query('SELECT nickname FROM `user` WHERE userid IN (' . implode(',', $ids) . ')');
            $uinfo = get_query_result($query); 
            $ids = array_column($uinfo, 'userid'); // by fisher at 2017-04-19
            //print_r($ids);exit;

            //根据以上ids的category关键词搜索对应的ids
            //如需 like 模式，注释’where=‘，打开where like即可
	    foreach ($uinfo as $u) {
                $this->db_slave->or_where('COL1', $u['nickname']);
            }
            $query = $this->db_slave->get('excel_user2cate');
            $cates = get_query_result($query);
            //echo $this->db_slave->last_query();die;
	    //print_r($cates);exit;
	    $this->db_slave->select('user.userid');
            foreach ($cates as $c) {
                foreach ($c as $key => $value) {
                    if (($key == 'COL1') || ($value == '') || !preg_match('/^[\w \d]+$/', $value)) {
                        continue;
                    }
		    $this->db_slave->or_where('nickname', $value);
                }
            }
	    $query = $this->db_slave->get('user');
            $newids = get_query_result($query);
	    $newids = array_column($newids, 'userid');
	    //print_r($newids);die;
            //foreach($uinfo AS $u){
                //$this->db_slave->or_where('COL1', $u['nickname']);
                //$query = $this->db_slave->get('excel_user2cate');
                //$cates = get_query_result($query);
                //$new_cates = [];
                //if( $cates ){
                    //foreach($cates[0] AS $key=>$ccc){
                        //if( $key=='COL1' ) continue;
                        //if( $ccc=='' ) continue;
                        //if( ! preg_match('/^[\w \d]+$/', $ccc) ) continue;
                        //$new_cates[] = $ccc;
                    //}
                //}
                //$newids = [];
                //if( $new_cates ){
                    //$this->db_slave->select('user.userid');
                    //foreach($new_cates AS $c){
                        //$this->db_slave->or_where('nickname', $c);
                    //}
                    //$query = $this->db_slave->get('user');
                    //$newids = get_query_result($query);
                //}
                //if( $newids ){
                    //$newids = array_column($newids, 'userid');
                //}
                //if( $newids ){
                    //$ids = array_merge($ids, $newids);
                //}
            //}
	    $ids = array_merge($ids, $newids);
            $ids = array_unique($ids);
            $total = count($ids); // 关注和由关注而推荐的品牌 by fisher at 2017-03-28
		//echo $total;die;
            // 分页逻辑 by fisher at 2017-03-28
            //if ($total > $limit) {
                //$of = $offset*$limit;
                //$lm = ($offset+1)*$limit;
                //$ids = array_slice($ids, $of, $lm);
            //}
        }
        $query = $this->db_slave->query('SELECT user.userid,user.usertype, facepic as author_facepic, nickname as author_nickname, introduce as author_introduce
            FROM `user`
            WHERE userid IN (' . implode(',', $ids) . ') LIMIT '.$limit.' OFFSET '.$offset);
        $uinfo = get_query_result($query);
       // print_r($uinfo);exit; //305ms

        $d2 = Array();
        foreach($uinfo AS $u){
            //$query = $this->db_slave->query('
                //SELECT *
                //FROM (`product`)
                //WHERE author='. $u['userid'] .' AND status=1 
                //LIMIT 5');
            //$list = get_query_result($query);
            $list = $this->get_author_products($u['userid'], 7, $sum);
            //echo $this->db_slave->last_query();die;
	    $d2[] = array(
                'total' => count($list),
                'user'  => $u,
                'list'  => $list
            );
        }

        $data = array('total'=>$total, 'list'=>$d2);
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $data, self::PRODUCT_LIST_CACHE_TIME);
        }

        return $data;
    }
    public function get_personalized_list_count($userid)
    {
        $key = md5('gplc:'.$userid);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get personalized product list from cache');
            return $res;
        }

	//$mt = microtime(true); // debug
        if ($userid == 0) {
            $query = $this->db_slave->query('SELECT userid FROM `user` WHERE usertype=2 AND isblocked=0 AND istop>0 ORDER by istop DESC');
            $ids = get_query_result($query);
            $ids = array_column($ids, 'userid');
	    //$mt = debug('Model_product_get_personalized_list_count_userid_'.$userid.'_getIds_use:', $mt); // debug
        } else {
            $query = $this->db_slave->query('SELECT user.userid,user.nickname FROM `user_fo_user` u LEFT JOIN `user` ON u.follow_userid=user.userid WHERE u.userid='.$userid.' ORDER BY u.fo_time DESC');
	    //$mt = debug('Model_product_get_personalized_list_count_userid_'.$userid.'_getIds1_use:', $mt); // debug
            $uinfo = get_query_result($query);
            $ids = array_column($uinfo, 'userid');
	    if (is_array($uinfo) && count($uinfo)) {
                foreach ($uinfo as $u) {
                    $this->db_slave->or_where('COL1', $u['nickname']);
                }
                $query = $this->db_slave->get('excel_user2cate');
	        //logger('excel_user2Cate_SQL:'.$this->db_slave->last_query()); // debug
	        //$mt = debug('Model_product_get_personalized_list_count_userid_'.$userid.'_getIds2_use:', $mt); // debug
                $cates = get_query_result($query);
	    } else { 
	  	$cates = array();
	    }
	    if (is_array($cates) && count($cates)) {
                $this->db_slave->select('user.userid');
                foreach ($cates as $c) {
                    foreach ($c as $key => $value) {
                        if (($key == 'COL1') || ($value == '') || !preg_match('/^[\w \d]+$/', $value)) {
                            continue;
                        }
                        $this->db_slave->or_where('nickname', $value);
                    }
                }
                $query = $this->db_slave->get('user');
	        //logger('get_user_by_ids_SQL:'.$this->db_slave->last_query()); // debug
	        //$mt = debug('Model_product_get_personalized_list_count_userid_'.$userid.'_getIds3_use:', $mt); // debug
                $newids = get_query_result($query);
                $newids = array_column($newids, 'userid');
	    } else { 
		$newids = array();
	    }
            $ids = array_merge($ids, $newids);
            $ids = array_unique($ids);
	    //$mt = debug('Model_product_get_personalized_list_count_userid_'.$userid.'_getIds4End_use:', $mt); // debug
        }

        if ($key) {
            //$this->cache->add_list(self::LIST_KEY, $key, $ids, self::PRODUCT_LIST_CACHE_TIME);
        }
	//$mt = debug('Model_product_get_personalized_list_count_userid_'.$userid.'_CacheSave_use:', $mt); // debug

        return $ids;
    }

    public function get_personalized_list_v3($userid, $ids, $offset = 0, $limit = 4, $sum = 5)
    {
        $key = md5('gplcv3:'.$offset.':'.$limit.':'.$sum.':'.$userid);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get personalized product list from cache');
            return $res;
        }

	$new_ids = array_slice($ids,$offset,$limit);
        $query = $this->db_slave->query('SELECT user.userid,nickname as author_nickname FROM `user`
            WHERE userid IN (' . implode(',', $new_ids) . ')');
            //WHERE userid IN (' . implode(',', $ids) . ') LIMIT '.$limit.' OFFSET '.$offset);
        $uinfo = get_query_result($query);
	//echo $this->db_slave->last_query();die;
        $data = Array();
        foreach($uinfo AS $u){
            //$list = $this->get_author_products($u['userid'], 7, $sum);
	    //$list = $this->getRelateProducts($u['userid'], $u['author_nickname'], $sum);
	    $list = $this->getRelateProByPosition($u['userid'], $u['author_nickname'], $sum);
	    //logger($u['author_nickname'].' list_nums:'.count($list)); //debug
	    if (count($list) < $sum) {
		$list = $this->getRelateProducts($u['userid'], $u['author_nickname'], $sum, 10000);
	    }
            $data[] = array(
                'total' => count($list),
                'user'  => $u,
                'list'  => $list
            );
        }

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $data, self::PRODUCT_LIST_CACHE_TIME);
        }

        return $data;
    }

    public function getRelateProByPosition($brand, $nickname, $limit=5, $price=2500)
    {
	$query = 'SELECT T1.* FROM (SELECT p.*,p.position_at + 604800 AS position_new,u.nickname AS author_nickname,u.chinese_name,s.show_name AS store_name,c.flag_url FROM '.TBL_PRODUCT.' p LEFT JOIN user u ON u.userid = p.author LEFT JOIN store s ON s.id = p.store LEFT JOIN country c ON c.name = s.country WHERE p.status = 1 AND p.author = '.$brand.' AND p.cover_image != \'\' ORDER BY p.position DESC,p.rank DESC LIMIT 100 OFFSET 0) T1 WHERE T1.position_new > '.time().' ORDER BY T1.position DESC,T1.price ASC,T1.rank DESC LIMIT  '.$limit.' OFFSET 0'; 
	$query = $this->db_slave->query($query);
	//logger('RelateProByPosition_SQL:'.$this->db_slave->last_query()); //debug
	$list = get_query_result($query);

	return $list;
    }

    private function getRelateProducts($brand, $nickname, $limit = 5)
    {
        $this->db_slave->select(TBL_PRODUCT.'.*,'.TBL_USER.'.nickname as author_nickname,'.TBL_USER.'.chinese_name,store.show_name as store_name,country.flag_url');
	$this->db_slave->join(TBL_USER, TBL_USER.'.userid = '.TBL_PRODUCT.'.author');
	$this->db_slave->join('store', 'store.id = '.TBL_PRODUCT.'.store');
	$this->db_slave->join('country', 'store.country = country.name');
        $this->db_slave->where(TBL_PRODUCT.'.status = ', 1);
        $this->db_slave->where(TBL_PRODUCT.'.author = ', $brand);
        $this->db_slave->where(TBL_PRODUCT.'.cover_image != ', '');
        //$this->db_slave->like(TBL_PRODUCT.'.title', $nickname, 'after');
        $this->db_slave->limit($limit); 
	//$this->db_slave->order_by(TBL_PRODUCT.'.position', 'DESC');
        $this->db_slave->order_by(TBL_PRODUCT.'.rank', 'DESC');
        $query = $this->db_slave->get(TBL_PRODUCT);
	//logger($this->db_slave->last_query()); //debug
        $list = get_query_result($query);

        return $list;
    }
   
   private function get_author_products($brand, $discount, $limit=3)
   {
	$list = $this->get_dis_products($brand, $discount, $limit);
	if ($list) {
		$sum = $limit - count($list);
		if ($sum > 0) {
		   $new = $this->get_dis_products($brand, 6, $sum);
		   foreach ($new as $k => $v) {
			array_push($list, $v);
		   }
		}
		if (count($list) < $limit) {
		   $new = $this->get_dis_products($brand, 5, $sum);
		   foreach ($new as $k => $v) {
                        array_push($list, $v);
                   }
 		}
        } else {
		$list = $this->get_dis_products($brand, 6, $limit);
		$sum = $limit - count($list);
		if ($sum > 0) {
                   $new = $this->get_dis_products($brand, 5, $sum);
                   foreach ($new as $k => $v) {
                        array_push($list, $v);
                   }
                }
	}
	return $list;
   }
    private function get_dis_products($brand, $discount, $limit=3)
    {
	    $this->db_slave->select(TBL_PRODUCT.'.*');
            $this->db_slave->join(TBL_PRODUCT_TAGS, TBL_PRODUCT.'.id = '.TBL_PRODUCT_TAGS.'.pid', 'left');
            $this->db_slave->where(TBL_PRODUCT.'.status = ', 1);
            $this->db_slave->where(TBL_PRODUCT.'.cover_image != ', '');
            $this->db_slave->where(TBL_PRODUCT_TAGS.'.brand_id = ', $brand);
            $this->db_slave->where(TBL_PRODUCT_TAGS.'.discount_id = ', $discount);
            $this->db_slave->order_by(TBL_PRODUCT.'.rank', 'DESC');
            $this->db_slave->limit($limit);
            $query = $this->db_slave->get(TBL_PRODUCT);
            $list = get_query_result($query);
	    return $list;
    }

    /**
     * @param $product_id
     * @param int $limit
     * @return array
     */
    public function get_relate_products($product_id, $limit = 5)
    {
        $source_product = $this->info($product_id);
        if($source_product == DB_OPERATION_FAIL){
            return DB_OPERATION_FAIL;
        }

        //取同一tag下其他设计师的商品(在售)
        $tag_ids = array_column($source_product['tags'], 'id');
        $this->db_slave->select(  'distinct '. TBL_PRODUCT . '.*', false)
            ->from(TBL_PRODUCT_TAG)
            ->join(TBL_PRODUCT, TBL_PRODUCT . '.id=' . TBL_PRODUCT_TAG . '.product_id', 'left');
        if (!empty($source_product['id'])) {
            $this->db_slave->where_not_in(TBL_PRODUCT_TAG . '.product_id', $source_product['id']);
        }
        if (!empty($tag_ids)) {
            $this->db_slave->where_in(TBL_PRODUCT_TAG . '.tag_id', $tag_ids);
        }

        //$this->db_slave//->where(TBL_PRODUCT . '.presale_end_time >', now())
        //    ->where(TBL_PRODUCT . '.category', $source_product['category'])
        //    ->limit($limit);

        $this->db_slave->where(TBL_PRODUCT . '.status =', 1)
            ->where(TBL_PRODUCT . '.category', $source_product['category'])
            ->limit($limit);

        $query = $this->db_slave->get();
        //echo $this->db_slave->last_query();die;
        $first_list['list'] = get_query_result($query);
        $first_list['total'] = count($first_list['list']);

        //取得同一个设计师同一分类下其他商品(在售)
        if ($first_list['total'] < $limit) {
            $remain_limit = $limit - $first_list['total'];
            $notIds=array_column($first_list['list'], 'id');
            array_push($notIds, $source_product['id']);

            $filter = [
                //'category' => $source_product['category'],
                'presale_end_time > ' => now(),
                'author' => $source_product['author'],
            ];

            $second_list = $this->get_list($source_product['author'], 0, $remain_limit, $filter);
            $this->db_slave->select(  'distinct '. TBL_PRODUCT . '.*', false)
                ->from(TBL_PRODUCT)
                ->where($filter)
                ->where('id not in ('. join(',', $notIds).')',null,false);

            $querys = $this->db_slave->get();
            $second_list = get_query_result($querys);
            $first_list['list'] = array_merge($first_list['list'], $second_list);
            #根据ID去重
            //$first_list['list'] = $this ->assoc_unique($resultList, 'id');
            $first_list['total'] = count($first_list['list']);
        }

        return $first_list;
    }

    private function assoc_unique($arr, $key)
    {
        $tmp_arr = array();
        foreach ($arr as $k => $v) {
            if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($arr[$k]);
            } else {
                $tmp_arr[] = $v[$key];
            }
        }
        sort($arr); //sort函数对数组进行排序
        return $arr;
    }

    /**
     * 是否like了
     * @param $product_id
     * @param $userid
     * @return array|bool
     */
    public function check_is_like($product_id, $userid)
    {
        if (empty($userid)) return false;

        $query = $this->db_slave->where(TBL_PRODUCT_LIKER . '.product_id', $product_id)
            ->where(TBL_PRODUCT_LIKER . '.userid', $userid)
            ->get(TBL_PRODUCT_LIKER);
        return ($query->num_rows() == 1) ? true : false;
    }

    public function get_saved_list($userid, $offset = 0, $limit = 20, $filter = array())
    {
        return $this->get_list($userid, $offset, $limit, array('status' => PRODUCT_STATUS_DRAFT));
    }

    public function create_product($data)
    {
        //controller做好检查
        $this->db_master->insert(TBL_PRODUCT, $data);
	//echo $this->db_master->last_query();die;
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return $this->info($this->db_master->insert_id());

        }
        return DB_OPERATION_FAIL;
    }

    private function get_user_join_field()
    {
        $str = array();
        $fields = $this->config->item('json_filter_user_info_basic');
        foreach ($fields as $field) {
            $str[] = TBL_USER . '.' . $field . ' as author_' . $field;
        }
        return implode(',', $str);
    }

    /**
     * @param $product_id
     * @param $level  basic,detail(default)
     * @return array|int
     */
    public function info($product_id, $level = 'detail')
    {
        //Get data from master db because slave db may have a sysnc delay.
//        @todo redis 缓存
//        if($this->redis->exists('product:'.$product_id)){
//            return json_decode($this->redis->get('product:'.$product_id));
//        }

        if ($product_info = $this->cache->get(self::_mem_key_product_info($product_id, $level))) {
//            $this->cache->delete(self::_mem_key_product_info($product_id));
//            unset($product_info);
            log_debug('Product info loaded from cache:' . $product_id . ' at ' . standard_date('DATE_MYSQL'));
            return $product_info;
        }
        $this->db_master->select(TBL_PRODUCT . '.*,' . $this->get_user_join_field());
        $this->db_master->select(',' . TBL_CATEGORY . '.name as category_name,'.TBL_USER.'.chinese_name');
	$this->db_master->select('store.show_name as store_name, country.flag_url');	
	$this->db_master->select('product_tags.cate3, product_tags.price_id, product_tags.discount_id');
        //$this->db_master->select('brand_icae.chinese_name as chinese_name');
//        $this->db_master->select(' ( UNIX_TIMESTAMP() - ' . TBL_PRODUCT . '.`presale_end_time`) as remain_days', false);
        $query = $this->db_master->where(TBL_PRODUCT . '.id', $product_id)
            ->join(TBL_USER, TBL_USER . '.userid = ' . TBL_PRODUCT . '.author', 'left')
           //->join('brand_icae','brand_icae.english_name = '.TBL_USER.'.nickname','left')    
            ->join('store', 'store.id = ' . TBL_PRODUCT . '.store', 'left') 
            ->join('country', 'country.name = store.country', 'left')
            ->join(TBL_CATEGORY, TBL_CATEGORY . '.id = ' . TBL_PRODUCT . '.category', 'left') 
            ->join('product_tags', 'product_tags.pid = ' . TBL_PRODUCT . '.id', 'left')
            ->get(TBL_PRODUCT);
        $product_info = get_row_array($query);
        if (empty($product_info)) {
            log_message('error', 'Get product info error:' . $this->db_master->last_query());
            return DB_OPERATION_FAIL;
        }

        if ($level == 'detail') {

            //description format
            $product_info['description'] = desc_decode($product_info['description']);

            //get album info
            $query = $this->db_master->where('product_id', $product_id)->order_by('position', 'asc')
                ->get(TBL_PRODUCT_ALBUM);
            $album = array();
            $res = get_query_result($query);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    $album[] = array('id' => $row['id'], 'position' => $row['position'], 'content' => $row['content'], 'type' => $row['type']);
                }
            }
            //get tags info
            /*$query = $this->db_master->where(TBL_PRODUCT_TAG . '.product_id', $product_id)
                ->join(TBL_TAGS, TBL_PRODUCT_TAG . '.tag_id = ' . TBL_TAGS . '.id', 'left')
                ->get(TBL_PRODUCT_TAG);
            $tags = array();
            $res = get_query_result($query);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    $tags[] = array('id' => $row['id'], 'name' => $row['name'], 'description' => $row['description']);
                }
            }*/

            //get size info
            $query = $this->db_master->where(TBL_PRODUCT_SIZE . '.product_id', $product_id)
                ->join(TBL_SIZE, TBL_PRODUCT_SIZE . '.size_id = ' . TBL_SIZE . '.size_id', 'left')
                //->order_by(TBL_SIZE . '.order', 'asc')
                ->order_by(TBL_SIZE . '.name', 'asc')
                ->get(TBL_PRODUCT_SIZE);
            $available_size = array();
            $res = get_query_result($query);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    $available_size[] = array('size_id' => $row['size_id'], 'name' => $row['name'], 'description' => $row['description']);
                }
            }


            //get color info
            $query = $this->db_master->where(TBL_PRODUCT_COLOR . '.product_id', $product_id)
                ->join(TBL_COLOR, TBL_PRODUCT_COLOR . '.color_id = ' . TBL_COLOR . '.color_id', 'left')
		->order_by(TBL_COLOR . '.name', 'asc')
                ->get(TBL_PRODUCT_COLOR);
            $available_color = array();
            $res = get_query_result($query);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    $available_color[] = array('color_id' => $row['color_id'], 'name' => $row['name'], 'image' => $row['image']);
                }
            }

            //Get related collection which is published
            /*$query = $this->db_slave->select(TBL_COLLECTION_ITEM . '.collection_id')
                ->where(TBL_COLLECTION_ITEM . '.product_id', $product_id)
                ->where(TBL_COLLECTION . '.status', COLLECTION_STATUS_PUBLISHED)
                ->join(TBL_COLLECTION, TBL_COLLECTION_ITEM . '.collection_id = ' . TBL_COLLECTION . '.id', 'left')
                ->order_by(TBL_COLLECTION_ITEM . '.id', 'desc')
                ->limit(1)
                ->get(TBL_COLLECTION_ITEM);
            $res = get_row_array($query);
            $col_id = element('collection_id', $res, 0);
	    */

            //like users
//            $liked_users = $this->get_liked_users($product_id);  //不是全部内容
//            $product_info['liked_users'] = $liked_users;

            /*$product_info['remain_days'] = (time() - $product_info['presale_end_time']);
            $product_info['collection_id'] = $col_id;
            $product_info['tags'] = $tags;
	    */
	    $product_info['album'] = $album;
            $product_info['available_size'] = $available_size;
            $product_info['available_color'] = $available_color;

        }

        $this->cache->save(self::_mem_key_product_info($product_id, $level), $product_info, self::PRODUCT_INFO_CACHE_TIME);

//        var_dump($product_info);
        //when update $this->redis->del(key);
        return $product_info;

    }


    /**
     * @param $product_id
     * @param $tag_ids array
     * @return int
     */
    public function update_product_tag_relation($product_id, $tag_ids)
    {
        //#This removes tags 1 and 2
//        DELETE FROM `taglinks` WHERE `ItemID` = 1 AND `TagID` NOT IN (3,4,5,6,7);
        $tag_ids = array_map('intval', $tag_ids);
        $query = $this->db_master->where('product_id =' . (int)$product_id . ' and tag_id not in (' . implode(',', $tag_ids) . ')')
            ->delete(TBL_PRODUCT_TAG);

//
//#This adds two new tags (6 and 7). Tags 3, 4 and 5 are unaffected
//INSERT IGNORE INTO `taglinks` (`ItemID`, `TagID`) VALUES (1,6),(1,7),(1,3),(1,4),(1,5);
        $vals = array();
        foreach ($tag_ids as $tag_id) {
            $vals[] = sprintf('(%d,%d)', $product_id, $tag_id);
        }
        $sql = sprintf('INSERT IGNORE INTO `%s` (`product_id`, `tag_id`) values %s', TBL_PRODUCT_TAG, implode(',', $vals));
        $query = $this->db_master->query($sql);
        if ($query) {
            $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function update_product_size_relation($product_id, $size_ids)
    {
        //#This removes tags 1 and 2
//        DELETE FROM `taglinks` WHERE `ItemID` = 1 AND `TagID` NOT IN (3,4,5,6,7);
        $size_ids = array_map('intval', $size_ids);
        $query = $this->db_master->where('product_id =' . (int)$product_id . ' and size_id not in (' . implode(',', $size_ids) . ')')
            ->delete(TBL_PRODUCT_SIZE);

//
//#This adds two new tags (6 and 7). Tags 3, 4 and 5 are unaffected
//INSERT IGNORE INTO `taglinks` (`ItemID`, `TagID`) VALUES (1,6),(1,7),(1,3),(1,4),(1,5);
        $vals = array();
        foreach ($size_ids as $size_id) {
            $vals[] = sprintf('(%d,%d)', $product_id, $size_id);
        }
        $sql = sprintf('INSERT IGNORE INTO `%s` (`product_id`, `size_id`) values %s', TBL_PRODUCT_SIZE, implode(',', $vals));
        $query = $this->db_master->query($sql);
        if ($query) {
            $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function update_product_color_relation($product_id, $color_ids)
    {
        //#This removes tags 1 and 2
//        DELETE FROM `taglinks` WHERE `ItemID` = 1 AND `TagID` NOT IN (3,4,5,6,7);
        $color_ids = array_map('intval', $color_ids);
        $query = $this->db_master->where('product_id =' . (int)$product_id . ' and color_id not in (' . implode(',', $color_ids) . ')')
            ->delete(TBL_PRODUCT_COLOR);

        $vals = array();
        foreach ($color_ids as $color_id) {
            $vals[] = sprintf('(%d,%d)', $product_id, $color_id);
        }
        $sql = sprintf('INSERT IGNORE INTO `%s` (`product_id`, `color_id`) values %s', TBL_PRODUCT_COLOR, implode(',', $vals));
        $query = $this->db_master->query($sql);
        if ($query) {
            $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function update_product_like_relation($product_id, $userid, $like=true, $referer=0)
    {
	$referer = $userid == $referer ? 0 : $referer; // refuse self-referer
        $insert = array("product_id" => (int)$product_id, "userid" => (int)$userid, 'referer' => $referer);
        if ($like) {
            $sql = sprintf('INSERT IGNORE INTO `%s` (`product_id`, `userid`, `created_at`, `referer`) values (%d,%d,%d,%d)', TBL_PRODUCT_LIKER, $product_id, $userid, time(), $referer);
            $query = $this->db_master->query($sql);
            if ($query) {
                $num = $this->db_master->affected_rows();
                if ($num > 0) {
                    $query_str = 'update ' . TBL_PRODUCT . ' set lover_count=(lover_count + ' . (int)$num . ')  ';
		    $query_str .= ', position = (position+2), position_at = '.time().' '; // mark user'click for home page recommond
                    $query_str .= ' where id=' . $insert['product_id'];
                    $query = $this->db_master->query($query_str);
                    $this->cache->delete(self::_mem_key_product_info($product_id, 'liked_users'));
                }
                return DB_OPERATION_OK;
            } else {
                return DB_OPERATION_FAIL;
            }
        } else {
            $this->db_master->where('product_id', $insert['product_id'])
                ->where('userid', $insert['userid']);
            $query = $this->db_master->delete(TBL_PRODUCT_LIKER);
            if (($num = $this->db_master->affected_rows()) > 0) {
                $query_str = 'update ' . TBL_PRODUCT . ' set lover_count=(lover_count - ' . (int)$num . ') ';
		$query_str .= ', position = (position-2) '; // mark user'click for home page recommond
                $query_str .= ' where id=' . $insert['product_id'];
                $query = $this->db_master->query($query_str);
                $this->cache->delete(self::_mem_key_product_info($product_id, 'liked_users'));
                return DB_OPERATION_OK;
            } else {
                return DB_OPERATION_FAIL;
            }
        }

    }

    /**
     * 修改用户信息
     *
     * @access public
     * @param int - $userid 用户ID
     * @param array - $data 用户信息
     * @return boolean - success/failure
     */
    public function update_info($product_id, $data)
    {
        $this->db_master->where('id', (int)$product_id);
        $this->db_master->update(TBL_PRODUCT, $data);
        if ($this->db_master->modified_rows() > 0) {
        //echo $this->db_slave->last_query();die;

            $this->cache->delete(self::_mem_key_product_info($product_id, 'basic'));
            $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            if (!empty(array_intersect($this->config->item('json_filter_product_list'), array_keys($data)))) {
                //if updated info exist in list ,update
                log_debug('update info items exist in product list');
                $this->cache->delete_list(self::LIST_KEY);
            }


            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }
    //仅改变增减操作为DB方式
    public function update_info_v2($product_id, $data)
    {
        $this->db_master->where('id', (int)$product_id);
        $this->db_master->set($data[0], '`'.$data[0].'`'.$data[1].$data[2], FALSE);
        $this->db_master->update(TBL_PRODUCT);
        //$this->db_master->update(TBL_PRODUCT, $data);
        if ($this->db_master->modified_rows() > 0) {

            $this->cache->delete(self::_mem_key_product_info($product_id, 'basic'));
            $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            if (!empty(array_intersect($this->config->item('json_filter_product_list'), array_keys($data)))) {
                //if updated info exist in list ,update
                log_debug('update info items exist in product list');
                $this->cache->delete_list(self::LIST_KEY);
            }


            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }


    public function get_owner($product_id)
    {
        $this->db_slave->select('author')
            ->where('id', (int)$product_id);
        $query = $this->db_slave->get(TBL_PRODUCT);
        return get_row_array($query);
    }


    public function delete_album_resource($album_id, $product_id = null)
    {
        $this->db_master->where('id', $album_id)
            ->delete(TBL_PRODUCT_ALBUM);
        if ($this->db_master->affected_rows() > 0) {

            if (!empty($product_id)) {
                $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            }

            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function add_album_resource($product_id, $album)
    {
        $album['product_id'] = (int)$product_id;
        $this->db_master->insert(TBL_PRODUCT_ALBUM, $album);
        if ($this->db_master->affected_rows() > 0) {

            if (!empty($product_id)) {
                $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            }

            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    /**
     * You can update anything
     * @param $album_id
     * @param $up_data
     * @return int
     */
    public function update_album_resource($album_id, $up_data, $product_id = null)
    {
//        $up_data =
        $this->db_master->where('id', $album_id)
            ->update(TBL_PRODUCT_ALBUM, $up_data);
        if ($this->db_master->modified_rows() > 0) {

            if (!empty($product_id)) {
                $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            }

            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }


    function update_count($product_id, $num, $field)
    {
        if (!is_numeric($num)) {
            return false;
        }
        $query = $this->db_slave->where('id', $product_id)
            ->get(TBL_PRODUCT);
        $pinfo = get_row_array($query);


        $query_str = 'update ' . TBL_PRODUCT . ' set ' . $field . '=(' . $field . ' + ' . $num . ')';

        if ($num > 0 && $field == 'presold_count') { //Updating presold_count
            if (($pinfo['presold_count'] + $num) >= $pinfo['presale_maximum']) {
                $query_str .= ' , presale_end_time = ' . time();
            }
        }

        $query_str .= ' where id=' . (int)$product_id;
        $this->db_master->query($query_str);

        if ($this->db_master->modified_rows() > 0) {
            if (!empty($product_id)) {
                $this->cache->delete(self::_mem_key_product_info($product_id, 'basic'));
                $this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            }
            return TRUE;
        }

        return FALSE;
    }

    public function update_viewed($product_id)
    {
        $this->db_master->where('id', $product_id)
            ->set('view_count', 'view_count+1', false)
            ->set('position', 'position+1', false) // mark user'click for home page recommond
            ->set('position_at', time(), false) // mark user'click for home page recommond
            ->update(TBL_PRODUCT);
    }
    

    public function delete($product_id)
    {
        $this->db_master->where('id', $product_id)
            ->delete(TBL_PRODUCT);
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function get_products_by_tag($tagids, $offset = 0, $limit = 20, $filter = array(), $order = null)
    {
        if (!is_array($tagids)) {
            $tagids = array((string)$tagids);
        }

        $this->db_slave->join(TBL_PRODUCT_TAG, TBL_PRODUCT_TAG . '.product_id = ' . TBL_PRODUCT . '.id', 'left');

        $this->db_slave->where_in(TBL_PRODUCT_TAG . '.tag_id', $tagids);

        if (!empty($filter)) {
            $this->db_slave->where($filter);
        }
        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (!empty($order)) {
            $this->db_slave->order_by(TBL_PRODUCT . '.' . $order, 'desc');  //Desc sort
        }
        $this->db_slave->order_by(TBL_PRODUCT . '.id', 'desc');  //Desc sort


        $query = $this->db_slave->get(TBL_PRODUCT);
        return get_query_result($query);

    }

//    public function get_liked_users($product_id)
//    {
//        $key = $this->_mem_key_product_info($product_id,'liked_users');
//        if($res = $this->cache->get($key)){
//            log_debug("get liked users of $product_id from cache");
//            return $res;
//        }
//        $query = $this->db_slave->select(TBL_PRODUCT_LIKER . '.userid,' . TBL_USER . '.facepic')
//            ->from(TBL_PRODUCT_LIKER)
//            ->join(TBL_USER, TBL_USER . '.userid = ' . TBL_PRODUCT_LIKER . '.userid', 'left')
//            ->where(TBL_PRODUCT_LIKER . '.product_id', $product_id)
//            ->get();
//        $data =  get_query_result($query);
//        $this->cache->save($key,$data,self::PRODUCT_INFO_CACHE_TIME);
//        return $data;
//
//    }
    public function  get_liked_users($product_id, $offset = 0, $limit = 20, $filter = array())
    {

        $key = $this->_mem_key_product_info($product_id, 'liked_users');
        if ($data = $this->cache->get($key) && ($offset == 0) && ($limit <= 20)) {
            log_debug("get liked users of $product_id from cache");
            if (is_array($data)) {
                $data['list'] = array_slice($data['list'], 0, $limit);
                return $data;
            }
        }

        $this->db_slave->start_cache();


        $this->db_slave->from(TBL_PRODUCT_LIKER);

        $this->db_slave->select(TBL_PRODUCT_LIKER . '.*,' . TBL_USER . '.*');
        $this->db_slave->join(TBL_USER, TBL_PRODUCT_LIKER . '.userid = ' . TBL_USER . '.userid', 'left');
        $this->db_slave->where(TBL_PRODUCT_LIKER . '.product_id', $product_id);

        if (!empty($filter)) {
            $this->db_slave->where($filter);
        }
        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_PRODUCT_LIKER);


        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }

        $query = $this->db_slave->get(TBL_PRODUCT_LIKER);

        $this->db_slave->flush_cache();
        $data = array('total' => $total, 'list' => get_query_result($query));
        if ($offset == 0 && $limit <= 20) {
            $this->cache->save($key, $data, self::PRODUCT_INFO_CACHE_TIME);
        }

        return $data;
    }

    /**
     * 支持我的发布商品,按照tag|cat筛选
     * $filter=array('product.category'=>cat_id,'product_tag.tag_id'=>tag_id)
     * 查看设计师的发布商品,按照tag|cat筛选
     * 查看tag|cat商品
     * @param $author
     * @param int $offset
     * @param int $limit
     * @param array $filter
     * @return array
     * author fisher
     * date 2017-03-13
     */
    public function get_list_new($userid, $filter = array(), $offset = 0, $limit = 20, $status = 1, $order = null)
    {
//echo 1;die;
        $key = $this->_mem_key_product_list($userid, $offset, $limit, $filter, $order);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get product list from cache');
            return $res;
        }

        $this->db_slave->start_cache();


        $this->db_slave->from(TBL_PRODUCT);

        $this->db_slave->select( TBL_PRODUCT . '.*');
	//$this->db_slave->select(TBL_USER . '.chinese_name,' . TBL_USER . '.facepic as author_facepic,' . TBL_USER . '.nickname as author_nickname,' . TBL_USER . '.introduce as author_introduce'); // hou xu jia shang  by fisher 2017-04-25
        //$this->db_slave->select('brand_icae.chinese_name as chinese_name'); //影响性能 by fisher at 2017-04-19
        //$this->db_slave->join(TBL_USER, TBL_PRODUCT . '.author = ' . TBL_USER . '.userid', 'left'); //hou xu jia shang by fisher 2017-04-25
        //$this->db_slave->join('brand_icae','brand_icae.english_name = '.TBL_USER.'.nickname','left'); //影响性能 by fisher at 2017-04-19

        if (!empty($filter)) {
            $this->db_slave->join(TBL_PRODUCT_TAGS, TBL_PRODUCT_TAGS.'.pid = '.TBL_PRODUCT.'.id', 'left');
            // $this->db_slave->where($filter);
            // 多条件，多选筛选 by fisher at 2017-03-21
            foreach ($filter as $key => $value) {
                if (count($value)>1) {
                    $this->db_slave->where_in($key, $value);
                } else {
                    $this->db_slave->where(array($key=>$value[0]));
                }
            }
        }
        // $this->db_slave->group_by( TBL_PRODUCT . '.id'); // 去重，不过没有实现应有的效果

        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_PRODUCT);
//echo $this->db_slave->last_query();die;
//echo $total;die;
	$this->db_slave->select(TBL_USER . '.chinese_name,' . TBL_USER . '.facepic as author_facepic,' . TBL_USER . '.nickname as author_nickname,' . TBL_USER . '.introduce as author_introduce');
	$this->db_slave->join(TBL_USER, TBL_PRODUCT . '.author = ' . TBL_USER . '.userid', 'left');
        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        //if (!empty($order)) {
            //$this->db_slave->order_by(TBL_PRODUCT . '.' . $order, 'desc');  //Desc sort
        //}
        //$this->db_slave->order_by(TBL_PRODUCT . '.id', 'desc');  //Desc sort

        //$this->db_slave->where(TBL_PRODUCT . '.cover_image !=', '');
	$this->db_slave->where(TBL_PRODUCT . '.status =', 1); // by fisher at 2017-04-23
        $query = $this->db_slave->get(TBL_PRODUCT);
        $this->db_slave->flush_cache();
	//echo $this->db_slave->last_query();die;
        log_debug('query = ' . $this->db_slave->last_query());

        $data = array('total' => $total, 'list' => get_query_result($query));
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $data, self::PRODUCT_LIST_CACHE_TIME);
        }

        return $data;
    }

    /**
     * 商品筛选条件 MODEL
     * 返回商品筛选的可用条件
     * @return array
     * author fisher
     * date 2017-03-14
     */
    public function get_filter_list()
    {
        $data = array();

        $this->db_slave->select('id, parent_id, order, chinese_name, name AS english_name, level');
        $this->db_slave->where(array('is_show' => 1));
        $query = $this->db_slave->get(TBL_CATEGORY);
        $data['categorys']['list'] = get_query_result($query);
        $data['categorys']['value'] = '品类';

        $this->db_slave->select('userid as brandid, chinese_name, nickname AS english_name');
	$this->db_slave->where(array('usertype' => 2));
        $query = $this->db_slave->get(TBL_USER);
        $data['brands']['list'] = get_query_result($query);
        $data['brands']['value'] = '品牌';

        $query_discount_tags = 'SELECT name,id FROM discount_tags ORDER BY `id` DESC';
        $query = $this->db_slave->query($query_discount_tags);
	$data['discounts']['list'] = get_query_result($query);
        $data['discounts']['value'] = '折扣';

        $query_price_tags = 'SELECT id,name FROM tags ORDER BY `order`';
        $query = $this->db_slave->query($query_price_tags);
        $data['prices']['list'] = get_query_result($query);
        $data['prices']['value'] = '价格';

	$data['sorts']['list'] = array(
	    0 => array(
		'type' => 'price',
		'sort' => 'asc',
		'name' => '价格 由低到高'
	    ),
	    1 => array(
                'type' => 'price',
                'sort' => 'desc',
                'name' => '价格 由高到低'
            ),
	    2 => array(
                'type' => 'rank',
                'sort' => 'asc',
                'name' => '新品 ↑'
            ),
	    3 => array(
                'type' => 'rank',
                'sort' => 'desc',
                'name' => '新品 ↓'
            )
	);
	$data['sorts']['value'] = '排序';

	$query_store = 'SELECT id,show_name AS name FROM store ORDER BY name';
        $query = $this->db_slave->query($query_store);
        $data['stores']['list'] = get_query_result($query);
        $data['stores']['value'] = '商城';

        return $data;
    }

    /**
     * 判断用户是否关注某品牌
     * @return int
     * author fisher
     * date 2017-03-17
     */
    public function is_followed($userid, $brand)
    {
        $this->db_slave->select('id')->from(TBL_USER_FOLLOW_USER)->where(array('userid' => $userid, 'follow_userid' => $brand));
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        $result = 0;
        if ($res) {
            $result = 1;
        }
        return $result;
    }

    /**
     * 获取客服用户的ID
     * @return int
     * author fisher
     * date 2017-03-21
     */
    public function get_customer_serv_id()
    {
        $this->db_slave->select('userid')->from(TBL_USER)->where(array('username' => 18888888888));
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        //return $res[0]['userid'];
	return $res;
    }

    public function getRows()
    {
        $rows = $this->db_slave->count_all(TBL_PRODUCT);
        // $this->db_slave->from(TBL_PRODUCT);
        // $this->db_slave->select(TBL_PRODUCT.'.*, '.TBL_CATEGORY.'.name');
        // $this->db_slave->join(TBL_CATEGORY, TBL_CATEGORY.'.id = '.TBL_PRODUCT.'.category');
        // $rows = $this->db_slave->count_all_results(TBL_PRODUCT);
        return $rows;
    }
    
    public function getData($offset=0,$page_size = 10)
    {
        // $this->db_slave->from(TBL_PRODUCT);
        $this->db_slave->select(TBL_PRODUCT.'.*, '.TBL_CATEGORY.'.name');
        $this->db_slave->join(TBL_CATEGORY, TBL_CATEGORY.'.id = '.TBL_PRODUCT.'.category');
        $this->db_slave->offset($offset);
        $this->db_slave->limit($page_size);
        $query = $this->db_slave->get(TBL_PRODUCT);
        $res = get_query_result($query);
        return $res;
    }

    public function get_allsec_categorys($key)
    {
        $this->db_slave->select('id')->from(TBL_CATEGORY)->where(array('parent_id'=>0,'name'=>$key));
        $query = $this->db_slave->get();
        $id = get_query_result($query);
        $this->db_slave->select('id')->from(TBL_CATEGORY)->where(array('parent_id'=>$id[0]['id']));
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        return $res;
    }

    // 获取我喜欢的商品
    public function get_my_likes($userid)
    {
        $this->db_slave->select()->from(TBL_PRODUCT_LIKER)->where(array('userid' => $userid));
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        return $res;
    }
    
    public function add_commond($id)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update(TBL_PRODUCT, array('is_commond'=>1));
        if ($this->db_master->modified_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function delete_commond($id)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update(TBL_PRODUCT, array('is_commond'=>0));
        if ($this->db_master->modified_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function getBrandOther($id)
    {
	$this->db_master->select('id,title,cover_image,price,presale_price,author');
	$this->db_master->where('author', $id);
	$this->db_master->where('status', 1);
	$this->db_master->limit(3);
	$query = $this->db_master->get(TBL_PRODUCT);
	$res = get_query_result($query);

	return $res;
    }

    public function getProductAvaliable($product_id)
    {
	//get size info
            $query = $this->db_master->where(TBL_PRODUCT_SIZE . '.product_id', $product_id)
                ->join(TBL_SIZE, TBL_PRODUCT_SIZE . '.size_id = ' . TBL_SIZE . '.size_id', 'left')
                ->order_by(TBL_SIZE . '.order', 'asc')
                ->get(TBL_PRODUCT_SIZE);
            $available_size = array();
            $res = get_query_result($query);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    $available_size[] = array('size_id' => $row['size_id'], 'name' => $row['name'], 'description' => $row['description']);
                }
            }


            //get color info
            $query = $this->db_master->where(TBL_PRODUCT_COLOR . '.product_id', $product_id)
                ->join(TBL_COLOR, TBL_PRODUCT_COLOR . '.color_id = ' . TBL_COLOR . '.color_id', 'left')
                ->get(TBL_PRODUCT_COLOR);
            $available_color = array();
            $res = get_query_result($query);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    $available_color[] = array('color_id' => $row['color_id'], 'name' => $row['name'], 'image' => $row['image']);
                }
            }
	$res = array(
	    'available_size' => $available_size,
            'available_color' => $available_color
	);

	return $res;
    }

    public function baseInfo($product_id)
    {
	$query = $this->db_slave->select('*')->where(TBL_PRODUCT.'.id', (int)$product_id)->get(TBL_PRODUCT);
	return get_query_result($query);
    }

    public function saveSearchHistory($data)
    {
	$this->db_master->insert('history', $data);
        //echo $this->db_master->last_query();die;
        if ($this->db_master->affected_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    private function debug($info, $start)
   {
        $mid = microtime(true); // debug
        logger($info.(round($mid - $start, 3) * 1000)); // debug

        return $mid;
   }

   public function update_position($product_id, $add_sum='2')
   {
        $this->db_master->where('id', $product_id)
            ->set('position', 'position+'.$add_sum, false) // mark user'click for home page recommond
            ->set('position_at', time(), false) // mark user'click for home page recommond
            ->update(TBL_PRODUCT);
   }
}
