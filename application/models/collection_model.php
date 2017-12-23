<?php

class Collection_model extends MY_Model
{

    const COLLECTION_INFO_CACHE_TIME = 300; // 5 min
    const COLLECTION_LIST_CACHE_TIME = 300; // 5 min
    const CACHE_OBJECT = 'collection';
    const LIST_KEY = 'col_list';

    function __construct()
    {
        parent::__construct();
//        $this->getRedis();
        $this->load->library('cache');
    }

    /**
     * collection info cache 5 minutes
     * @param $collection_id
     * @return string
     */
    private static function _mem_key_collection_info($collection_id,$additional=''){
        return self::CACHE_OBJECT.':'.$collection_id.':'.$additional;
    }
    /**
     * collection info cache 5 minutes
     * @param $collection_id
     * @return string
     */
    private static function _mem_key_collection_list($userid,$offset,$limit,$filter,$order){
        if(!empty($userid)){
            return false;
        }

        $key = array(self::CACHE_OBJECT,'list',$offset,$limit,serialize($filter),$order);
        return md5(implode(':',$key));
    }



    /**
     * 支持我的发布商品,按照tag|cat筛选
     * $filter=array('collection.category'=>cat_id,'collection_tag.tag_id'=>tag_id)
     * 查看设计师的发布商品,按照tag|cat筛选
     * 查看tag|cat商品
     * @param $userid
     * @param int $offset
     * @param int $limit
     * @param array $filter
     * @return array
     */
    public function get_list($userid = null, $offset = 0, $limit = 20, $filter = array(), $order = null)
    {
        $key = $this->_mem_key_collection_list($userid,$offset,$limit,$filter,$order);
        if($res = $this->cache->get($key)){
            log_debug('get collection list from cache');
            return $res;
        }
        $this->db_slave->start_cache();
        $this->db_slave->from(TBL_COLLECTION);
        $this->db_slave->select(TBL_COLLECTION . '.*,' . TBL_USER . '.facepic as author_facepic,' . TBL_USER . '.nickname as author_nickname,'.TBL_USER.'.introduce as author_introduce');
        $this->db_slave->join(TBL_USER, TBL_COLLECTION . '.author = ' . TBL_USER . '.userid', 'left');

        if (!empty($userid)) {
            //按照author筛选
            $this->db_slave->where(TBL_COLLECTION . '.author', $userid);
        }

        if (!empty($filter)) {
//            if (array_key_exists(TBL_collection_TAG . '.tag_id', $filter)) {
//                $this->db_slave->join(TBL_PRODUCT_TAG, TBL_PRODUCT_TAG . '.collection_id = ' . TBL_COLLECTION . '.id', 'left');
//            }
            $this->db_slave->where($filter);
        }
        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_COLLECTION);


        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (empty($order)) {
            $order = 'id';    //Set default order field;
        }
        $this->db_slave->order_by(TBL_COLLECTION . '.' . $order, 'desc');  //Desc sort
        $query = $this->db_slave->get(TBL_COLLECTION);
	//logger('collection_list_sql:'.$this->db_slave->last_query());
        $this->db_slave->flush_cache();

        $data =array('total' => $total, 'list' => get_query_result($query));

//        $this->_set_collection_list_cache($key, $data, self::COLLECTION_LIST_CACHE_TIME);
        $this->cache->add_list(self::LIST_KEY,$key, $data, self::COLLECTION_LIST_CACHE_TIME);

        return $data;
        //@todo Product information need to be merged with author info as well as album images;

    }


    public function create($data)
    {
        $filter_rule = array('author','title','description','background_image','cover_image','collection_id','type','publish_time','status','last_update');
        $insert_data = filter_data($data,$filter_rule);
	$insert_data['last_update'] = time();
        $this->db_master->insert(TBL_COLLECTION, $insert_data);
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return $this->info($this->db_master->insert_id());
        }
        return DB_OPERATION_FAIL;
    }

    /**
     * @param $collection_id
     * @return array|int
     */
    public function info($collection_id)
    {
        if($col_info = $this->cache->get(self::_mem_key_collection_info($collection_id))){
            log_debug('Collection info loaded from cache:'.$collection_id.' at '.standard_date('DATE_MYSQL'));
            return $col_info;
        }
//        $this->db_slave->select(TBL_COLLECTION.'.*',false);
        $this->db_master->select(TBL_COLLECTION . '.*,' . $this->get_user_join_field());
        $query = $this->db_slave->where(TBL_COLLECTION . '.id', $collection_id)->join(TBL_USER, TBL_USER . '.userid = ' . TBL_COLLECTION . '.author', 'left')->get(TBL_COLLECTION);
        $collection_info = get_row_array($query);
        if (empty($collection_info)) {
            log_message('error', 'Get collection info error:' . $this->db_slave->last_query());
            return DB_OPERATION_FAIL;
        }
        //get item list
        $query = $this->db_slave
            ->select(TBL_COLLECTION_ITEM.'.*,'.TBL_PRODUCT.'.cover_image as product_cover_image,'.TBL_PRODUCT.'.title as product_title,'.TBL_PRODUCT.'.price as product_price,'.TBL_PRODUCT.'.presale_price as presale_price,'.TBL_PRODUCT.'.status,store.name as store_name,'.TBL_USER.'.nickname as author_nickname,'.TBL_USER.'.chinese_name,country.flag_url')
            ->where(TBL_COLLECTION_ITEM.'.collection_id', $collection_id)
            ->join(TBL_PRODUCT,TBL_PRODUCT.'.id = '.TBL_COLLECTION_ITEM.'.product_id','left')
            ->join(TBL_USER,TBL_PRODUCT.'.author = '.TBL_USER.'.userid','left')
            ->join('store',TBL_PRODUCT.'.store = store.id','left')
            ->join('country','store.country = country.name','left')
            ->order_by(TBL_COLLECTION_ITEM.'.position','desc')
            ->get(TBL_COLLECTION_ITEM);
        //filter
	//logger('collection_info_SQL: '.$this->db_slave->last_query()); //debug
        $res = get_query_result($query);
        $album = $res;

        $collection_info['item_list'] = $album;


//        $collection_info['tags'] = $tags;

//        $this->redis->set('collection:'.$collection_id,json_encode($collection_info));
        //when update $this->redis->del(key);
        $this->cache->save(self::_mem_key_collection_info($collection_id),$collection_info,self::COLLECTION_INFO_CACHE_TIME);
        return $collection_info;

    }

    public function infoT3($id, $detail=true)
    {
        if($col_info = $this->cache->get(self::_mem_key_collection_info($id, (string)$detail))){
            log_debug('Collection info loaded from cache:'.$id.' at '.standard_date('DATE_MYSQL'));
            return $col_info;
        }

        $query = $this->db_master->select('id,type,title,description,status,view_count,cover_image,author,subhead,content_image')
            ->where(TBL_COLLECTION.'.id', $id)
            //->where(TBL_COLLECTION.'.status', 1)
            ->get(TBL_COLLECTION);
	//echo $this->db_master->last_query();die;
        $collection_info = get_query_result($query);
        
	if (!empty($collection_info[0]['id'])) {
            $query = $this->db_master->select('id,img1,img2,text1,text2,products,order')
                ->where('collection3_item.collection_id', $id)
                ->order_by('collection3_item.order', 'asc')
                ->get('collection3_item');
	    //echo $this->db_master->last_query();die;
            $items = get_query_result($query);

            if (is_array($items) && count($items)) {
                foreach ($items as $k => $v) {
                    if (!empty($v['products'])) {
			$ids = json_decode($v['products'], true);
			if ($detail === false) {
			    $items[$k]['products'] = $ids;
			} else {
                            $query = $this->db_master->select('id,title,price,presale_price,cover_image,discount,status')
                                ->where_in(TBL_PRODUCT.'.id', $ids)
                                //->where(TBL_PRODUCT.'.status', 1)
                                ->get(TBL_PRODUCT);
                            $pts = get_query_result($query);
                            if (is_array($pts) && count($pts)) {
                                foreach ($pts as $x => $y) {
                                    $dc = floor($y['discount']*10);
                                    $this->db_slave->select('size,url');
                                    $this->db_slave->order_by('size', 'asc');
                                    $this->db_slave->where('name =', $dc);
                                    $this->db_slave->where('type =', 1);
                                    //$this->db_slave->where('status =', 1);
                                    $query = $this->db_slave->get('superscript');
                                    $data = get_query_result($query);
                                    if ($data) {
                                        $pts[$x]['superscript'] = $data;
                                    }
                                }
                                $items[$k]['products'] = $pts; 
                            }
			}
                    }
                }
            }
            $collection_info[0]['item_list'] = $items;
        } else {
            return false;
        }

        $this->cache->save(self::_mem_key_collection_info($id),$collection_info[0],self::COLLECTION_INFO_CACHE_TIME);
       
        return $collection_info[0];
    }

    public function infoT1($id)
    {
        if($col_info = $this->cache->get(self::_mem_key_collection_info($id))){
            log_debug('Collection info loaded from cache:'.$id.' at '.standard_date('DATE_MYSQL'));
            return $col_info;
        }

        $query = $this->db_master->select('id,type,title,description,status,view_count,cover_image,background_image,author,subhead,content_image')
            ->where(TBL_COLLECTION.'.id', $id)
            //->where(TBL_COLLECTION.'.status', 1)
            ->get(TBL_COLLECTION);
        $collection_info = get_query_result($query);

        $this->cache->save(self::_mem_key_collection_info($id),$collection_info[0],self::COLLECTION_INFO_CACHE_TIME);
        
        return $collection_info[0];
    }

    /**
     * 修改用户信息
     *
     * @access public
     * @param int - $userid 用户ID
     * @param array - $data 用户信息
     * @return boolean - success/failure
     */
    public function update_info($collection_id, $data)
    {
	$data['last_update'] = time();
        $this->db_master->where('id', (int)$collection_id);
        $this->db_master->update(TBL_COLLECTION, $data);
        if($this->db_master->modified_rows() > 0){

            $this->cache->delete(self::_mem_key_collection_info($collection_id));
            if (!empty(array_intersect($this->config->item('json_filter_collection_list'), array_keys($data)))) {
                //if updated info exist in list ,update
                log_debug('update info items exist in collection list');
                $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            }
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;

    }

    public function get_owner($collection_id)
    {
        $this->db_slave->select('author')->where('id', (int)$collection_id);
        $query = $this->db_slave->get(TBL_COLLECTION);
        return get_row_array($query);
    }

    public function get_owner_and_assistant($collection_id)
    {
        $owner = element('author',$this->get_owner($collection_id),false);

        if($owner){
            if(!isset($this->user_model)){
                $this->load->model('user_model');
            }
            $assistant = $this->user_model->get_assistants($owner);
            $assistant = array_column($assistant,'userid');
            $assistant[] = $owner;
            return $assistant;
        }
        return DB_OPERATION_FAIL;
    }

    /**
     * Update item which belong to collection
     */

    /**
     * @param $album_id
     * @return int
     */
    public function delete_album_resource($album_id)
    {
        $query = $this->db_slave->select('collection_id')->where('id',$album_id)->get(TBL_COLLECTION_ITEM);
        $res = get_row_array($query);
        $collection_id = element('collection_id',$res,null);

        $this->db_master->where('id', $album_id)->delete(TBL_COLLECTION_ITEM);
        if($this->db_master->affected_rows() > 0){

            if(!empty($collection_id)){
                $this->cache->delete(self::_mem_key_collection_info($collection_id));
            }

            return  DB_OPERATION_OK;
        }
        return   DB_OPERATION_FAIL;
    }

    public function add_album_resource($collection_id, $album)
    {
        $album['collection_id'] = (int)$collection_id;
        $this->db_master->insert(TBL_COLLECTION_ITEM, $album);

        if($this->db_master->affected_rows() > 0){

            if(!empty($collection_id)){
                $this->cache->delete(self::_mem_key_collection_info($collection_id));
            }

            return  $this->db_master->insert_id();
        }
        return   DB_OPERATION_FAIL;
    }

    /**
     * You can update anything
     * @param $album_id
     * @param $up_data
     * @return int
     */
    public function update_album_resource($album_id, $up_data)
    {
//        $up_data =
        $this->db_master->where('id', $album_id)->update(TBL_COLLECTION_ITEM, $up_data);
        $query = $this->db_slave->select('collection_id')->where('id',$album_id)->get(TBL_COLLECTION_ITEM);
        $res = get_row_array($query);
        $collection_id = element('collection_id',$res,null);
        if($this->db_master->modified_rows() > 0){

            if(!empty($collection_id)){
                $this->cache->delete(self::_mem_key_collection_info($collection_id));
            }

            return  DB_OPERATION_OK;
        }
        return   DB_OPERATION_FAIL;
    }

    /**
     * End of item operation
     */

    /**
     * @param $coid
     */
    public function update_view_count($coid,$author)
    {
        $this->db_master->where('id',$coid)->set('view_count','view_count+1',false)->update(TBL_COLLECTION);
        try{
            require 'vendor/autoload.php'; // include Composer goodies
            //关闭MongoDB缓存功能
            //$client = new MongoDB\Client($this->config->item('mongodb'));
            $col = MONGO_COL_COLL_PREFIX.$author;
            $db = MONGO_DB_COLLECTION;
            //关闭MongoDB缓存功能
            //$collection = $client->$db->$col;

            $data = [
                'col_id' => $coid,
                'author' => $author,
                'time' => now() ,
                'track'=>$this->input->get_post('track'),
                'agent'=>$this->input->user_agent(),
                'ip'    =>$this->input->ip_address()
            ] ;
            //关闭MongoDB缓存功能
            //$result = $collection->insertOne( $data );
        }catch (Exception $e){
            log_error('[Mondo]'.$e->getMessage());
        }
    }

    public function del($col_id,$filter=null)
    {
        if(!is_array($col_id)){
            $col_id = array((int)$col_id);
        }
        $col_id = array_map("intval",$col_id);
        $this->db_master->where_in('id',$col_id);
        if(!empty($filter)){
            $this->db_master->where($filter);
        }
        $this->db_master->delete(TBL_COLLECTION);
        if($this->db_master->affected_rows() > 0){
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    /**
     * @Caution: description is conflict
     * @return string
     */

    private function get_user_join_field()
    {
        $str = array();
        $fields = $this->config->item('json_filter_user_info_basic');
        foreach ($fields as $field) {
            if($field == 'description'){
                $str[] = TBL_USER . '.' . $field .' as user_'.$field;
            }else{
                $str[] = TBL_USER . '.' . $field ;
            }
        }
        return implode(',', $str);
    }

    public function collection_count($collectionid){
        #组合来源信息
        $referer['ios_referer'] = REFERER_IOS.REFERER_TYPE_COLLECTION.$collectionid;
        $referer['android_referer'] = REFERER_ANDROID.REFERER_TYPE_COLLECTION.$collectionid;
        //"select order_id,create_time, product_id, status, product_title, sum(pre_paid_money+last_paid_money) count,DATE_FORMAT(create_time,'%Y%u'
        //weeks from deal where status !=0 and referer=110 group by weeks,product_id"
        $query = $this->db_slave->select(" product_id, product_title, sum(pre_paid_money+last_paid_money) count, DATE_FORMAT(last_update, '%u-%Y') as weeks", false)
            ->where('status', ORDER_STATUS_END_SUCCEED)->where_in('referer',$referer)
            ->group_by(array("weeks","product_id"))->get(TBL_CDB_DEAL);
        $result = get_query_result($query);
        foreach($result as &$value){
            $param = explode("-",$value['weeks']);
            $dayArr = $this->GetWeekDate($param[0],$param[1]);
            $value['first_day'] = $dayArr['first_day'];
            $value['last_day'] = $dayArr['last_day'];
        }
        #获取该商品下所有的已完成订单的支付金额
        return $result;
    }

    /**
     * 根据某年第几周 某年 获取当周的开始时间和结束时间
     * @param $week 周
     * @param $year 年
     * @return array 数组开始时间，结束时间
     */
    private function GetWeekDate($week,$year)
    {
        $timestamp = mktime(0,0,0,1,1,$year);
        $dayofweek = date("w",$timestamp);
        $distance = 0;
        if( $week != 1) {
            $distance = ($week - 1) * 7 - $dayofweek + 1;
        }
        $passed_seconds = $distance * 86400;
        $timestamp += $passed_seconds;
        $firt_date_of_week = date("Y,m,d", $timestamp);
        if($week == 1) {
            $distance = 7 - $dayofweek;
        }else{
            $distance = 6;
        }
        $timestamp += $distance * 86400;
        $last_date_of_week = date("Y,m,d",$timestamp);
        return array('first_day'=>$firt_date_of_week,'last_day'=>$last_date_of_week);
    }

    /**
     * 获取专辑的总收益
     * @param $collectionid 专辑ID
     * @return int
     */
    public function collection_countmoney($collectionid){
        $referer['ios_referer'] = REFERER_IOS.REFERER_TYPE_COLLECTION.$collectionid;
        $referer['android_referer'] = REFERER_ANDROID.REFERER_TYPE_COLLECTION.$collectionid;
        //"select order_id,create_time, product_id, status, product_title, sum(pre_paid_money+last_paid_money) count,DATE_FORMAT(create_time,'%Y%u'
        //weeks from deal where status !=0 and referer=110 group by weeks,product_id"
        $query = $this->db_slave->select(" sum(pre_paid_money+last_paid_money) count", false)
            ->where('status', ORDER_STATUS_END_SUCCEED)->where_in('referer',$referer)
            ->get(TBL_CDB_DEAL);
        $result = get_row_array($query);

        return isset($result['count']) ? $result['count'] : 0;
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

        $this->db_slave->select('category.id, category.parent_id, category.order, category_icae.chinese_name, category_icae.english_name');
        $this->db_slave->join('category_icae', 'category.name = category_icae.english_name');
        $this->db_slave->where(array('is_show' => 1));
        $query = $this->db_slave->get(TBL_CATEGORY);
        $data['categorys']['list'] = get_query_result($query);
        $data['categorys']['value'] = '品类';

        //$this->db_slave->select('user.userid as brandid, brand_icae.chinese_name, brand_icae.english_name');
        //$this->db_slave->join('brand_icae', 'user.nickname = brand_icae.english_name');
	$this->db_slave->select('userid as brandid, nickname as english_name');
	$this->db_slave->where(TBL_USER.'.usertype', 2);
        $query = $this->db_slave->get(TBL_USER);
        $data['brands']['list'] = get_query_result($query);
        $data['brands']['value'] = '品牌';

        $query_discount_tags = 'SELECT name,id FROM discount_tags';
        $query = $this->db_slave->query($query_discount_tags);
        $data['discounts']['list'] = get_query_result($query);
        $data['discounts']['value'] = '折扣';

        $query_price_tags = 'SELECT id,name FROM tags';
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

    public function getCollectionRows($type)
    {
        $key = md5('collection_list:'.$type);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get collection rows from cache');
            return $res;
        }
	if ($type !== 'all') {
            $this->db_slave->where(TBL_COLLECTION.'.type = ', $type);
	}
        $rows = $this->db_slave->count_all_results(TBL_COLLECTION);

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $rows, self::COLLECTION_LIST_CACHE_TIME);
        }

        return $rows;
    }

    public function getCollectionData($type, $offset=0, $limit=10)
    {
        $key = md5('collection_data:'.$type.':'.$offset.':'.$limit);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get ct1 data from cache');
            return $res;
        }

        $this->db_slave->select(TBL_COLLECTION.'.*');
	if ($type !== 'all') {
            $this->db_slave->where(TBL_COLLECTION.'.type =', $type);
	}
        $this->db_slave->offset($offset);
        $this->db_slave->limit($limit);
        $this->db_slave->order_by(TBL_COLLECTION.'.id', 'desc');
        $query = $this->db_slave->get(TBL_COLLECTION);
        $res = get_query_result($query);
        
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::COLLECTION_LIST_CACHE_TIME);
        }

        return $res;
    }

    public function update_ct1_cover($id,$data)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update(TBL_COLLECTION, $data);
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }

    public function collection_delete($id)
    {
        $this->db_master->where('id', (int)$id)->delete(TBL_COLLECTION);
        if ($this->db_master->modified_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function update_ct3_item($id, $data)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update('collection3_item', $data);
        if($this->db_master->modified_rows() > 0){
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function add_ct3_item($data)
    {
        $this->db_master->insert('collection3_item', $data);
        if ($this->db_master->affected_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function ct3_del_item($id)
    {
        $this->db_master->where('id', (int)$id)->delete('collection3_item');
        if ($this->db_master->modified_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }
    
    public function recommend()
    {
	$this->db_slave->select('id,title,description,recommond_image, cover_image,type');
	$this->db_slave->where('is_recommended', 1);
	$this->db_slave->where('status', 1);
	$query = $this->db_slave->get(TBL_COLLECTION);
	//logger('recommond_SQL:'.$this->db_slave->last_query());
	$res = get_query_result($query);

	return $res;
    }

    public function cancel_all_recommend()
    {
	$data = array(
	    'is_recommended' => 0,
	    'last_update' => time()//date('Y-m-d H:i:s', time())
	);
	$this->db_master->where('is_recommended', 1);
        $this->db_master->update(TBL_COLLECTION, $data);
        if($this->db_master->modified_rows() > 0){
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;	
    }

    public function update_pushed($id)
    {
	$this->db_slave->where('id', $id);
	$this->db_slave->set('is_pushed', 'is_pushed + 1', false);
	$this->db_slave->update(TBL_COLLECTION);
	if($this->db_master->modified_rows() > 0){
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }
}
