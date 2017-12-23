<?php
class Coupon_model extends MY_Model
{
	const TBL_STORE = 'coupon';
	const TBL_SHIPPING = 'shipping';
	const STORE_LIST_CACHE_TIME = 5; // 5 min
    const LIST_KEY = 'coupon_list';
    const TBL_COUNTRY = 'country';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function getRows()
    {
    	$key = md5('coupon_list:coupon_list');
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get coupon rows from cache');
            return $res;
        }

        $rows = $this->db_slave->count_all(self::TBL_STORE);

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $rows, self::STORE_LIST_CACHE_TIME);
        }

        return $rows;
    }

    public function getData($offset=0,$page_size = 10)
    {
    	$key = md5('coupon_list:offset:'.$offset.':page:'.$page_size);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get coupon list from cache');
            return $res;
        }

        $this->db_slave->select(self::TBL_STORE.'.*');
        $this->db_slave->offset($offset);
        $this->db_slave->limit($page_size);
        $this->db_slave->order_by(self::TBL_STORE.'.name', 'asc');
        $query = $this->db_slave->get(self::TBL_STORE);
        $res = get_query_result($query);

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::STORE_LIST_CACHE_TIME);
        }

        return $res;
    }

    public function getDetail($id)
    {
    	$this->db_slave->select('*');
        $this->db_slave->from(self::TBL_STORE);
	$this->db_slave->where(self::TBL_STORE.'.id =', $id);
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        
	return @$res[0];
    }

    public function get_category_list()
    {
    	$this->db_slave->select('id,name');
        $query = $this->db_slave->get(TBL_CATEGORY);
        $res = get_query_result($query);

        return $res;
    }

    public function get_store_list()
    {
        $this->db_slave->select('id,show_name');
	$this->db_slave->order_by('show_name', 'asc');
        $query = $this->db_slave->get('store');
        $res = get_query_result($query);

        return $res;
    }

    public function update($id, $data)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update(self::TBL_STORE, $data);
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }

    public function add($data)
    {
        $this->db_master->insert(self::TBL_STORE, $data);
        if ($this->db_master->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function update_sum($id, $field, $num = 1)
    {
        $this->db_master->where('id', $id)
            ->set($field, $field.'+'.$num, false)
            ->update(self::TBL_STORE, ['updated_at' => time()]);

	if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }

    public function create_coupon_list($data)
    {
	$this->db_master->insert_batch('user_coupon', $data);
	//echo $this->db_master->last_query();die;
        if ($this->db_master->affected_rows() > 0) {
            return true;
        }
        return false;	
    }

    public function myList($userid, $username)
    {
	$query = $this->db_master->select('c.id,c.name,c.type,c.value,c.limit,c.description,
	    c.use_at,c.use_end,c.store,c.category,c.is_exclusive,u.id as coupon_id,
	    u.coupon_code,u.salt,u.used_at,u.deal_id,u.used_at')
	    ->join('coupon as c', 'c.id = u.coupon_id')
	    ->where('u.user_id', $userid)
	    ->or_where('u.user_mobile', $username)
	    ->order_by('c.use_end', 'asc')
	    ->get('user_coupon as u');
	$res = get_query_result($query);

	return $res;
    }

    public function addCoupon($userid, $code, $salt)
    {
	$this->db_master->where('salt', $salt);
	$this->db_master->where('coupon_code', $code);
	$this->db_master->where('user_id', 0);
	$this->db_master->where('user_mobile', 0);
        $this->db_master->update('user_coupon', ['user_id' => $userid]);
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;	
    }

    public function getCouponId($code, $salt) 
    {
        $this->db_slave->select('coupon_id');
        $this->db_slave->from('user_coupon');
        $this->db_slave->where('user_coupon.coupon_code =', $code);
        $this->db_slave->where('user_coupon.salt =', $salt);
        $query = $this->db_slave->get();
        $res = get_query_result($query);

        return $res;	
    }

    public function getTimes($id, $mobile)
    {
	$this->db_master->where('coupon_id', $id);
	$this->db_master->where('user_mobile', $mobile);
	return $this->db_master->count_all_results('user_coupon');
    }

    public function getCouponByMobile($id, $mobile, $num, $source=0)
    {
	$this->db_master->where('coupon_id', $id);
	$this->db_master->where('user_id', 0);
        $this->db_master->where('user_mobile', 0);
	$this->db_master->limit($num);
        $this->db_master->update('user_coupon', ['user_mobile' => $mobile,'source' => $source,'get_at' => time()]);
        if (($rows = $this->db_master->modified_rows()) > 0) {
            return $rows;
        }
        return false;
    }

    public function useCoupon($ids, $orderId)
    {
        foreach ($ids as $i) {
           $this->db_master->or_where('id', (int)$i);
        }
        $this->db_master->update('user_coupon', ['deal_id' => $orderId, 'used_at' => time()]);
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }

    public function update_use_sum($ids, $field, $num=1)
    {
        foreach ($ids as $i) {
           $this->db_master->where('id', (int)$i);
	   $this->db_master->set($field, $field.'+'.$num, false);
           $this->db_master->update('coupon', ['updated_at' => time()]);
        }
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }

    // 新手券
    public function getCouponByUserid($id, $userid, $num, $source=0)
    {
        $this->db_master->where('coupon_id', $id);
        $this->db_master->where('user_id', 0);
        $this->db_master->where('user_mobile', 0);
        $this->db_master->limit($num);
        $this->db_master->update('user_coupon', ['user_id' => $userid,'source' => $source,'get_at' => time()]);
        if (($rows = $this->db_master->modified_rows()) > 0) {
            return $rows;
        }
        return false;
    }

    public function getCouponList($type=5)
    {
	$this->db_slave->select('id,type,get_at,get_end,repeat,created_sum,geted_sum,reg_at,reg_end,reg_min,value,use_end');
        $this->db_slave->from('coupon');
        $this->db_slave->where('type =', $type);
        $query = $this->db_slave->get();
        $res = get_query_result($query);

        return $res;
    }

    public function getRefereeCoupon($username, $coupon_id, $reg_at, $reg_end)
    {
	$this->db_slave->where('user_mobile =', $username);
	$this->db_slave->where('coupon_id =', $coupon_id);
	$this->db_slave->where('get_at >=', $reg_at);
	$this->db_slave->where('get_at <', $reg_end);
	$total = $this->db_slave->count_all_results('user_coupon');

	return $total;
    }
}

