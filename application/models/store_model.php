<?php
class Store_model extends MY_Model
{
	const TBL_STORE = 'store';
	const TBL_SHIPPING = 'shipping';
	const STORE_LIST_CACHE_TIME = 300; // 5 min
        const LIST_KEY = 'store_list';
        const TBL_COUNTRY = 'country';
	const TBL_SAlES = 'sales';
	const TBL_SAlES_OPTIONS = 'sales_options';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function getRows()
    {
    	$key = md5('store_list:store_list');
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get store rows from cache');
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
    	$key = md5('store_list:offset:'.$offset.':page:'.$page_size);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get store list from cache');
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
    	$this->db_slave->select('a.*, b.flag_url');
        $this->db_slave->from(self::TBL_STORE.' as a');
        $this->db_slave->join(self::TBL_COUNTRY.' as b', "a.country=b.name");
	$this->db_slave->where('a.id =', $id);
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        
	return @$res[0];
    }

    public function get_shippings($id)
    {
    	$this->db_slave->select(self::TBL_SHIPPING.'.*');
    	$this->db_slave->where(self::TBL_SHIPPING.'.store_id =', $id);
        $query = $this->db_slave->get(self::TBL_SHIPPING);
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

    public function shipping_add($data)
    {
        $this->db_master->insert(self::TBL_SHIPPING, $data);
        if ($this->db_master->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function shipping_update($id, $data)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update(self::TBL_SHIPPING, $data);
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }

    ///////////////////////// sales_promotion //////////////////////////
    public function getSalesRows($store=0)
    {
/*        $key = md5('store_sales_list:'.$store);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get store rows from cache');
            return $res;
        }
*/
	$this->db_slave->where('store_id', $store);
        $rows = $this->db_slave->count_all_results(self::TBL_SAlES);

/*        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $rows, self::STORE_LIST_CACHE_TIME);
        }
*/
        return $rows;
    }

    public function getSalesData($store=0, $offset=0, $page_size = 10)
    {
/*        $key = md5('store_sales_list:offset:'.$offset.':page:'.$page_size.':store:'.$store);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get store sales list from cache');
            return $res;
        }
*/
        $this->db_slave->select('*');
        $this->db_slave->offset($offset);
        $this->db_slave->limit($page_size);
        $this->db_slave->where('store_id = ', $store);
        $query = $this->db_slave->get(self::TBL_SAlES);
        $res = get_query_result($query);

/*        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, '30');
        }
*/
        return $res;
    }

    public function getSalesDetail($id)
    {
        $this->db_slave->select('*');
        $this->db_slave->where('id =', $id);
        $query = $this->db_slave->get(self::TBL_SAlES);
        $res = get_query_result($query);

        return @$res[0];
    }

    public function getSalesOptions($id)
    {
        $this->db_slave->select('*');
        $this->db_slave->where('sales_id =', $id);
	$this->db_slave->where('status =', 1);
        $query = $this->db_slave->get(self::TBL_SAlES_OPTIONS);
        $res = get_query_result($query);

        return $res;
    }
   
    public function sales_update($id, $data)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update(self::TBL_SAlES, $data);
	//echo $this->db_master->last_query();die;
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }

    public function sales_options_add($data)
    {
        $this->db_master->insert(self::TBL_SAlES_OPTIONS, $data);
        if ($this->db_master->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function sales_options_update($id, $data)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update(self::TBL_SAlES_OPTIONS, $data);
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }

    public function getRecentSales($store, $field = '*')
    {
/*	$key = md5('store_sales_recent_list:store:'.$store);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get store sales recent list from cache');
            return $res;
        }
*/
        $this->db_slave->select($field);
        $this->db_slave->where('store_id = ', $store);
        $this->db_slave->where('status = ', 1);
        $this->db_slave->order_by('order', 'ASC');
        $query = $this->db_slave->get(self::TBL_SAlES);
        $res = get_query_result($query);
/*
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, '30');
        }
*/
        return $res;
    }

    public function  sales_new_add($data)
    {
	$this->db_master->insert(self::TBL_SAlES, $data);
        if ($this->db_master->affected_rows() > 0) {
            return true;
        }
        return false;
    }   
}

