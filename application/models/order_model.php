<?php

class Order_model extends MY_Model
{
    const TBL = 'account';
    const RETURN_BACK_LIST_CACHE_TIME = 300;
    const LIST_KEY = 'return_back_list';

    function __construct()
    {
        parent::__construct();
	$this->load->library('cache');
    }


    public function get_notify_url($paytype, $payment)
    {
        $notify_url = '';
        if ($paytype == PAY_TYPE_PREPAY) {
            if ($payment == $this->config->item('payment')['ali']) {
                $notify_url = base_url() . 'order/ali_prepay_notify';
            } elseif ($payment == $this->config->item('payment')['weixin']) {
                $notify_url = base_url() . 'order/wx_prepay_notify';
            }
        }
        if ($paytype == PAY_TYPE_LASTPAY) {
            if ($payment == $this->config->item('payment')['ali']) {
                $notify_url = base_url() . 'order/ali_lastpay_notify';
            } elseif ($payment == $this->config->item('payment')['weixin']) {
                $notify_url = base_url() . 'order/wx_lastpay_notify';
            }
        }
        return $notify_url;
    }


    /**
     * 修改用户信息
     *
     * @access public
     * @param int - $userid 用户ID
     * @param array - $data 用户信息
     * @return boolean - success/failure
     */
    public function update_info($order_id, $data)
    {
        if(is_array($order_id)){
            $this->db_master->where_in('order_id', $order_id);
        }else{
            $this->db_master->where('order_id', (string)$order_id);
        }

	if (isset($data['status'])) { // if change status, change son deal too
	    if(is_array($order_id)){
            	$this->db_master->or_where_in('pid', $order_id);
            }else{
            	$this->db_master->or_where('pid', (string)$order_id);
            }
	}
        $this->db_master->update(TBL_CDB_DEAL, $data);
	//echo $this->db_master->last_query();die;
        return ($this->db_master->modified_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function get_owner($order_id)
    {
        $this->db_slave->select('buyer_userid,seller_userid')->where('order_id', (string)$order_id);
        $query = $this->db_slave->get(TBL_CDB_DEAL);
        return get_row_array($query);
    }



    //-----------deal-----
    /**
     * 获取订单列表
     * @param null $userid
     * @param int $offset
     * @param int $limit
     * @param array $filter
     * @param null $order
     * @param string $type
     * @return array
     */
    public function get_list($userid = null, $offset = 0, $limit = 20, $filter = array(), $order = null,$type = 'buyer')
    {
        $this->db_slave->start_cache();
        $this->db_slave->from(TBL_CDB_DEAL);
        if($type == 'buyer'){
            $userid_selector = TBL_CDB_DEAL.'.buyer_userid';
        }
        if($type == 'seller'){
            $userid_selector = TBL_CDB_DEAL.'.seller_userid';
        }
        if (!empty($userid)) {
            //按照author筛选
            $this->db_slave->where($userid_selector, $userid);
        }
        if (!empty($filter)) {
            $this->db_slave->where($filter);
        }
	$this->db_slave->where(TBL_CDB_DEAL.'.pid', 0);
	$this->db_slave->where(TBL_CDB_DEAL.'.status > ', 0);
        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_CDB_DEAL);

        $this->db_slave->select('buyer.nickname as buyer_nickname,buyer.facepic as buyer_facepic');
        $this->db_slave->select('seller.nickname as seller_nickname,seller.facepic as seller_facepic');
        $this->db_slave->join(TBL_USER.' buyer','buyer.userid = '.TBL_CDB_DEAL.'.buyer_userid','left');
        $this->db_slave->join(TBL_USER.' seller','seller.userid = '.TBL_CDB_DEAL.'.seller_userid','left');
        $this->db_slave->select(TBL_CDB_DEAL.'.*,'.TBL_COLOR.'.name as color_name ,'.TBL_COLOR.'.description as color_desc ,'.TBL_SIZE.'.name as size_name ,'.TBL_SIZE.'.description as size_desc ');
        $this->db_slave->join(TBL_COLOR,TBL_COLOR.'.color_id = '.TBL_CDB_DEAL.'.product_color','left');
        $this->db_slave->join(TBL_SIZE,TBL_SIZE.'.size_id = '.TBL_CDB_DEAL.'.product_size','left');
	$this->db_slave->select('store.show_name as store_name, country.flag_url,store.id as store_id');
        $this->db_slave->join(TBL_PRODUCT, TBL_CDB_DEAL.'.product_id = ' . TBL_PRODUCT . '.id', 'left');
        $this->db_slave->join('store', 'store.id = ' . TBL_PRODUCT . '.store', 'left');
        $this->db_slave->join('country', 'country.name = store.country', 'left');

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            log_debug('order limit:'.$limit);
            $this->db_slave->limit($limit);
        }
        if (empty($order)) {
            $order = 'create_time';    //Set default order field;
        }
        $this->db_slave->order_by(TBL_CDB_DEAL . '.' . $order, 'desc');  //Desc sort

        $this->db_slave->where('deal.pid', 0);

        $query = $this->db_slave->get(TBL_CDB_DEAL);

        $this->db_slave->flush_cache();

        $list = get_query_result($query);
       // echo $this->db_slave->last_query();exit;
	//echo '<pre>'; print_r($list);die;

        //把子订单合并到主订单结构中
        foreach($list AS $key=>$list_one){
            if( $list_one['order_sum'] ){
                $x = $this->get_list_one($list_one['order_id']);
                $list[$key]['products'] = $x;
            } else {
		$list[$key]['products'][0] = array(
		    'product_id' => $list_one['product_id'],
		    'product_price' => $list_one['product_price'],
		    'product_count' => $list_one['product_count'],
		    'product_title' => $list_one['product_title'],
                    'product_color' => $list_one['product_color'],
		    'product_size' => $list_one['product_size'], 
		    'product_cover_image' => $list_one['product_cover_image'],
		    'store_id' => $list_one['store_id'],
		    'store_name' => $list_one['store_name'],
		    'flag_url' => $list_one['flag_url']
		);
	    }
        }
        return array('total' => $total, 'list' => $list);
    }
    //获取子订单列表
    public function get_list_one($pid, $type='buyer')
    {
        $this->db_slave->start_cache();
        $this->db_slave->from(TBL_CDB_DEAL);
        if($type == 'buyer'){
            $userid_selector = TBL_CDB_DEAL.'.buyer_userid';
        }
        if($type == 'seller'){
            $userid_selector = TBL_CDB_DEAL.'.seller_userid';
        }
        //Need to get buyer basic info
        //Need to get seller basic info

        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_CDB_DEAL);

        $this->db_slave->select('buyer.nickname as buyer_nickname,buyer.facepic as buyer_facepic');
        $this->db_slave->select('seller.nickname as seller_nickname,seller.facepic as seller_facepic');
        $this->db_slave->join(TBL_USER.' buyer','buyer.userid = '.TBL_CDB_DEAL.'.buyer_userid','left');
        $this->db_slave->join(TBL_USER.' seller','seller.userid = '.TBL_CDB_DEAL.'.seller_userid','left');
        $this->db_slave->select(TBL_CDB_DEAL.'.*,'.TBL_COLOR.'.name as color_name ,'.TBL_COLOR.'.description as color_desc ,'.TBL_SIZE.'.name as size_name ,'.TBL_SIZE.'.description as size_desc ');
        $this->db_slave->join(TBL_COLOR,TBL_COLOR.'.color_id = '.TBL_CDB_DEAL.'.product_color','left');
        $this->db_slave->join(TBL_SIZE,TBL_SIZE.'.size_id = '.TBL_CDB_DEAL.'.product_size','left');
	$this->db_slave->select('store.show_name as store_name, country.flag_url,store.id as store_id');
	$this->db_slave->join(TBL_PRODUCT, TBL_CDB_DEAL.'.product_id = ' . TBL_PRODUCT . '.id');
	$this->db_slave->join('store', 'store.id = ' . TBL_PRODUCT . '.store', 'left');
        $this->db_slave->join('country', 'country.name = store.country', 'left');

        if (empty($order)) {
            $order = 'create_time';    //Set default order field;
        }
        $this->db_slave->order_by(TBL_CDB_DEAL . '.' . $order, 'desc');  //Desc sort

        $this->db_slave->where('deal.pid', $pid);

        $query = $this->db_slave->get(TBL_CDB_DEAL);

        $this->db_slave->flush_cache();

        //echo $this->db_slave->last_query();exit;
        return get_query_result($query);
    }


    public function get_users_by_product($product_id ,$offset = 0, $limit = 20, $filter = array(), $order = null)
    {
        $this->db_slave->select('count(distinct buyer_userid) as total',false);
        $this->db_slave->start_cache();
//        $this->db_slave->from(TBL_CDB_DEAL);

        if (!empty($product_id)) {
            //按照author筛选
            $this->db_slave->where(TBL_CDB_DEAL.'.product_id', $product_id);
        }

        if (!empty($filter)) {
            $this->db_slave->where($filter);
        }
        $this->db_slave->stop_cache();
        $total = get_row_array($this->db_slave->get(TBL_CDB_DEAL));
        $total = $total['total'];
        $this->db_slave->select('distinct '.TBL_CDB_DEAL.'.buyer_userid as userid',false);
        $this->db_slave->select(' buyer.* ,buyer.facepic');
//        $this->db_slave->select('seller.nickname as seller_nickname,seller.facepic as seller_facepic');
        $this->db_slave->join(TBL_USER.' buyer','buyer.userid = '.TBL_CDB_DEAL.'.buyer_userid','left');
//        $this->db_slave->join(TBL_USER.' seller','seller.userid = '.TBL_CDB_DEAL.'.seller_userid','left');
//        $this->db_slave->select(TBL_CDB_DEAL.'.*,'.TBL_COLOR.'.name as color_name ,'.TBL_COLOR.'.description as color_desc ,'.TBL_SIZE.'.name as size_name ,'.TBL_SIZE.'.description as size_desc ');
//        $this->db_slave->join(TBL_COLOR,TBL_COLOR.'.color_id = '.TBL_CDB_DEAL.'.product_color','left');
//        $this->db_slave->join(TBL_SIZE,TBL_SIZE.'.size_id = '.TBL_CDB_DEAL.'.product_size','left');

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (empty($order)) {
            $order = 'create_time';    //Set default order field;
        }
        $this->db_slave->order_by(TBL_CDB_DEAL . '.' . $order, 'desc');  //Desc sort


        $query = $this->db_slave->get(TBL_CDB_DEAL);

        $this->db_slave->flush_cache();

        return array('total' => $total, 'list' => get_query_result($query));

    }

    public function get_count($userid, $paystatus = 1)
    {
        $this->db_slave->where('userid', $userid)
            ->where('type <', 2);//取0、1，支出与收入;
        if ($paystatus == 1) {
            $this->db_slave->where('status', 1);
        }
        return $this->db_slave->count_all_results(TBL_CDB_DEAL);
    }

    function add_deal($param = array())
    {
        $this->db_master->insert(TBL_CDB_DEAL, $param);
        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;
    }


    function get_product_order_count($product_id)
    {
        //log_debug('product_id:'.$product_id);

        $this->db_slave->select('COUNT(*) as total');
        $this->db_slave->where('product_id',$product_id);
        $this->db_slave->where('TIMESTAMPDIFF(MINUTE,create_time,NOW()) <',30);
        $this->db_slave->where('status',ORDER_STATUS_INIT);
        $query = $this->db_slave->get(TBL_CDB_DEAL);

        log_debug('current query:',$this->db_slave->last_query());
        if($query->num_rows() > 0){
            $row = $query->row_array();
            //log_debug('count order:',$row);
            //log_debug('count order:'.$row['total'].'end');
            return $row['total'];
        }
        return 0;
//        where('product_id',$product_id)->get(TBL_CDB_DEAL);
        //        SELECT COUNT(order_id) FROM `deal` WHERE 327 = product_id AND TIMESTAMPDIFF(MINUTE,create_time,NOW())  < 30 AND status = 20
    }

    /**
     *
     * 删除某个交易数据，需要详细记录数据本身
     * @param unknown_type $dealid
     * @param unknown_type $data
     */

    function remove_deal($order_id)
    {
        $query = $this->db_slave->where('order_id', $order_id)->get(TBL_CDB_DEAL);
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $str = print_r($row, true);
            $query->free_result();
            log_message('debug', sprintf('[CDB_RECORD] DELETE %s , DATA: %s', $order_id, $str));

            $this->db_master->where('order_id', $order_id)->delete(TBL_CDB_DEAL);
            return $row;
        }

        return false;
    }

    /**
     *
     * 更新订单状态
     * @param unknown_type $dealid
     * @param unknown_type $data
     */

    function update_deal($dealid, $data, $filter = false, $field = 'order_id')
    {
        $this->db_master->where($field, $dealid);
        if ($filter != false) {
            $this->db_master->where($filter);
        }
        $this->db_master->update(TBL_CDB_DEAL, $data);
        return ($this->db_master->modified_rows() > 0) ? TRUE : FALSE;
    }

    function log_pay_notify($pay_type, $data, $verify_result)
    {

        $sqldata = array(
            'pay_type' => $pay_type,
            'notify_time' => standard_date('DATE_MYSQL'),
            'notify' => json_encode($data),
            'verify_result' => $verify_result,
        );
        $this->db_master->insert(TBL_CDB_PAY_RESULT_NOTIFY, $sqldata);
        if ($this->db_master->affected_rows() > 0) {
            return DB_OPERATION_OK;
        } else {
            return DB_OPERATION_FAIL;
        }
    }


    /**
     *
     * 获取某条记录的基本记录
     * @param string 记录号 $order_id
     */
    function get_deal($order_id, $field='*')
    {
        $this->db_slave->select($field);
        $this->db_slave->where('order_id', $order_id);
        $query = $this->db_slave->get(TBL_CDB_DEAL);
        $res = get_row_array($query);
        return (!empty($res)) ? $res : FALSE;
    }
    //父订单
    function get_deal_more($order_id)
    {
        $this->db_slave->where('pid', $order_id);
        $query = $this->db_slave->get(TBL_CDB_DEAL);
        $res = get_query_result($query);
        return (!empty($res)) ? $res : FALSE;
    }

    /**
     *
     * 获取某条记录的详细记录
     * @param string 记录号 $order_id
     */
    function detail($order_id)
    {
        $this->db_slave->select(TBL_CDB_DEAL . '.*,' . TBL_COLOR . '.name as color_name,' . TBL_SIZE . '.name as size_name,' . TBL_SHIP_INFO . '.*');

        $this->db_slave->select('buyer.nickname as buyer_nickname,buyer.facepic as buyer_facepic,buyer.username as buyer_username,buyer.city as buyer_city');
        $this->db_slave->select('seller.nickname as seller_nickname,seller.facepic as seller_facepic'.','.TBL_PRODUCT.'.m_url as m_url');
        $this->db_slave->join(TBL_USER.' buyer','buyer.userid = '.TBL_CDB_DEAL.'.buyer_userid','left');
        $this->db_slave->join(TBL_USER.' seller','seller.userid = '.TBL_CDB_DEAL.'.seller_userid','left');
        $this->db_slave->join(TBL_PRODUCT, TBL_PRODUCT . '.id = ' . TBL_CDB_DEAL . '.product_id', 'left');
        $this->db_slave->join(TBL_COLOR, TBL_COLOR . '.color_id = ' . TBL_CDB_DEAL . '.product_color', 'left')
            ->join(TBL_SIZE, TBL_SIZE . '.size_id = ' . TBL_CDB_DEAL . '.product_size', 'left')
            ->join(TBL_SHIP_INFO, TBL_SHIP_INFO . '.ship_id = ' . TBL_CDB_DEAL . '.ship_info_id', 'left');
	$this->db_slave->select('store.show_name as store_name, country.flag_url,store.id as store_id');
        $this->db_slave->join('store', 'store.id = ' . TBL_PRODUCT . '.store', 'left');
        $this->db_slave->join('country', 'country.name = store.country', 'left');
        $this->db_slave->where(TBL_CDB_DEAL . '.order_id', $order_id);
	$this->db_slave->or_where(TBL_CDB_DEAL . '.pid', $order_id); // 子订单也要查出来 by fisher at 2017-04-24
        $query = $this->db_slave->get(TBL_CDB_DEAL);
	//echo $this->db_slave->last_query();die;
	$res = get_query_result($query); // 只返回一条数据，改成返回所有数据 by fisher at 2017-04-24
        //$res = get_row_array($query);
        //echo '<pre>';var_dump($res);die;
        return $res;
    }

//------------------
    /**
     *
     * update deal and balance
     * @param unknown_type $order_id
     * @param unknown_type $data
     * @param unknown_type $who
     * @param unknown_type $ba_num
     */
    public function update_deal_and_product($order_id, $data, $filter = null, $paytype = PAY_TYPE_PREPAY)
    {
        $this->update_deal($order_id, $data, $filter);
	unset($data['last_paid_money']);
        $this->update_deal($order_id, $data, $filter, 'pid'); // update son deal
        $order_info = $this->get_deal($order_id);
        $product_id = element('product_id', $order_info);
        $coupon_code = element('last_pay_coupon_code', $order_info);
        if ($product_id) {
            $this->load->model('product_model', 'product');

            $field = 'presold_count';
            if ($paytype == PAY_TYPE_LASTPAY) {
                $field = 'sold_count';
            }
            if (!$this->product->update_count($product_id, 1, $field)) {
                log_error('update_info update product ' . $field . ' get wrong: ' . $product_id . ' orderid:' . $order_id);
            }

        }
        if ($coupon_code) {
	    $coupon = json_decode($coupon_code, true);
	    //print_r($coupon);die;
            if (is_array($coupon) && count($coupon)) {
                foreach ($coupon as $k => $v) {
                    if ($v['is_available'] == 1) {
                        $ids[] = $v['coupon_user_id'];
			$coupon_ids[] = $v['coupon_id'];
                    }
                }
                if (is_array($ids) && count($ids)) {
                    $this->load->model('coupon_model');
                    $res = $this->coupon_model->useCoupon($ids, $order_id);
                    if ($res != DB_OPERATION_OK) {
                        log_error('update_deal_and_product update coupon ' . $coupon_code . ' get wrong:  orderid:' . $order_id);
                    }
		    $this->coupon_model->update_use_sum($coupon_ids, 'used_sum', 1);
                }
            }
        }


        return DB_OPERATION_OK;
    }




    function list_deals_in_range($beginDate, $endDate, $belong_userid = null, $usertype = 'seller',$filter=array())
    {

//        $ignor_students = $this->config->item('test_students');
//        $ignor_teachers = $this->config->item('test_teachers');
//        $ignor_user = array_merge($ignor_teachers, $ignor_students);
        $this->db_slave->select('create_time,status');
        $this->db_slave->where('create_time >=', $beginDate)
            ->where('create_time <=', $endDate);
        if(!empty($belong_userid)){
            if($usertype == 'seller'){
                $this->db_slave->where('seller_userid', $belong_userid);
            }
            if($usertype == 'buyer'){
                $this->db_slave->where('buyer_userid', $belong_userid);
            }
        }

        $this->db_slave->order_by('create_time');

        $query = $this->db_slave->get(TBL_CDB_DEAL);

        return $query->result_array();

    }


    public function insert_payinfo($data)
    {
//        $sql = "INSERT IGNORE INTO `pay_info` (`info_id`, `discount`, `payment_type`, `subject`, `trade_no`, `buyer_email`, `gmt_create`, `notify_type`, `quantity`, `out_trade_no`, `seller_id`, `notify_time`, `body`, `trade_status`, `is_total_fee_adjust`, `total_fee`, `gmt_payment`, `seller_email`, `price`, `buyer_id`, `notify_id`, `use_coupon`, `sign_type`, `sign`) VALUES ('2341', '1.2', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')"";
        $this->db_master->insert(TBL_PAY_INFO, $data);
        if ($this->db_master->affected_rows() > 0) {
            return DB_OPERATION_OK;
        } else {
            return DB_OPERATION_FAIL;
        }
    }


    public function get_status_num($userid,$usertype='buyer_userid')
    {
        $this->db_slave->where($usertype,$userid);
        $this->db_slave->select('count(*) as count, status, '.$usertype,'false');
        $this->db_slave->group_by('status');
        $query = $this->db_slave->get(TBL_CDB_DEAL);
        return get_query_result($query);
    }

    public function update_deliver_detail($id, $data)
    {
        $this->db_master->where('order_id', (string)$id);
        $this->db_master->update(TBL_CDB_DEAL, $data);
        return ($this->db_master->modified_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function return_back($data)
    {
	$this->db_slave->insert('return_back', $data);
	if ($this->db_slave->affected_rows() > 0) {
            return DB_OPERATION_OK;
        } else {
            return DB_OPERATION_FAIL;
        }
    }

    public function update_return($id, $data)
    {
	$this->db_slave->where('id', $id);
	$this->db_slave->update('return_back', $data);
	return ($this->db_slave->modified_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function getRows()
    {
        $key = md5('return_back_list:return_back_list');
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get return_back rows from cache');
            return $res;
        }

        $rows = $this->db_slave->count_all('return_back');

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $rows, self::RETURN_BACK_LIST_CACHE_TIME);
        }

        return $rows;
    }

    public function getData($offset=0,$page_size = 10)
    {
        $key = md5('return_back_list:offset:'.$offset.':page:'.$page_size);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get return_back list from cache');
            return $res;
        }

        $this->db_slave->select('return_back.*,'.TBL_USER.'.nickname');
	$this->db_slave->join(TBL_USER, TBL_USER.'.userid = return_back.userid');
        $this->db_slave->offset($offset);
        $this->db_slave->limit($page_size);
        $this->db_slave->order_by('return_back.id', 'asc');
        $query = $this->db_slave->get('return_back');
        $res = get_query_result($query);

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::RETURN_BACK_LIST_CACHE_TIME);
        }

        return $res;
    }

    public function getReturnDetail($id)
    {
        $this->db_slave->select('a.*, b.nickname, b.username');
        $this->db_slave->from('return_back as a');
        $this->db_slave->join(TBL_USER.' as b', "a.userid=b.userid");
        $this->db_slave->where('a.id =', $id);
        $query = $this->db_slave->get();
        $res = get_query_result($query);

	if (isset($res[0]['id'])) {
	    $res[0]['products'] = $this->getReturnBackProducts($res[0]['products']);
/*
	    $products = json_decode($res[0]['products'], true);
	    if (is_array($products) && count($products)) {
		foreach ($products as $k => $v) {
		    $this->db_slave->select('id,title,price,presale_price,cover_image,m_url,status');
		    $this->db_slave->where('id = ', $v['id']);
		    $query = $this->db_slave->get(TBL_PRODUCT);
		    $result = get_query_result($query);
		    if (isset($result[0]['id'])) {
			$products[$k] = array_merge($result[0], $v);
		    }
		}
		$res[0]['products'] = $products;
	    }
*/
	}
        return @$res[0];
    }

    public function update_out_of_stock_note($order_id)
    {
	$this->db_master->where('order_id', $order_id)
            ->set('out_of_stock_note', 'out_of_stock_note+1', false)
            ->update(TBL_CDB_DEAL);	
        return ($this->db_master->modified_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function getReturnBackList($userid, $offset=0, $limit=10)
    {
	$this->db_slave->where('userid =', $userid);
        $total = $this->db_slave->count_all_results('return_back');

	$this->db_slave->select('id,order_id,status,type,products');
        $this->db_slave->where('userid =', $userid);
        $this->db_slave->offset($offset);
        $this->db_slave->limit($limit);
        $query = $this->db_slave->get('return_back');
        $res = get_query_result($query);

	if (is_array($res) && count($res)) {
	    foreach ($res as $k => $v) {
		$res[$k]['products'] = $this->getReturnBackProducts($v['products']);
	    }
        }

        return  ['total' => $total, 'list' => $res];
    }

    private function getReturnBackProducts($products)
    {
	$pds = json_decode($products, true);
	if (is_array($pds) && count($pds)) {
	    $ids = array_column($pds, 'id');
	    $ids = array_unique($ids);
	    $this->db_slave->select('a.id,a.title,a.price,a.presale_price,a.cover_image,a.m_url,a.status,b.id as store_id,b.name as store_name,c.flag_url');
	    $this->db_slave->from(TBL_PRODUCT.' AS a');
	    $this->db_slave->join('store AS b', 'a.store = b.id');
	    $this->db_slave->join('country AS c', 'b.country = c.name');
            $this->db_slave->where_in('a.id', $ids);
            $query = $this->db_slave->get('return_back');
            $result = get_query_result($query);
	    if (is_array($result) && count($result)) {
	     	foreach ($pds as $k => $v) {
		    foreach ($result as $x => $y) {
			if ($v['id'] == $y['id']) {
			    $pds[$k] = array_merge($v,$y);
			    break;
			}
		    }
		}
		return $pds;
	    }
	}
	
	return [];
    }

}
