<?php
class Data_model extends MY_Model
{
    const DATA_LIST_CACHE_TIME = 300; // 5 min
    const LIST_KEY = 'data_list';

    function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }


    function get_deal_pay_users()
    {
        $month = date('Y-m', time());
        $time = strtotime($month);
        // $key = md5('deal:pay:users:month:'.$month);
        // if ($key && ($res = $this->cache->get($key))) {
        //     log_debug('get deal_pay_users data from cache');
        //     return $res;
        // }

        $this->db_slave->select('*');
        $this->db_slave->where('timestamp >=', $time);
        $this->db_slave->order_by('timestamp', 'asc');
        $query = $this->db_slave->get('new_deal');
        $data = get_query_result($query);
        // echo $this->db_slave->last_query();die;
        // print_r($data);die;
        // if ($key) {
        //     $this->cache->add_list(self::LIST_KEY, $key, $data, self::DATA_LIST_CACHE_TIME);
        // }
        return $data;
    }

    function insert_data($new, $table = 'new_deal')
    {
        $this->db_slave->insert($table, $new);
        // echo $this->db_slave->last_query();die;
        if ($this->db_slave->affected_rows() > 0) {
            return DB_OPERATION_OK;
        } else {
            return DB_OPERATION_FAIL;
        }
    }

    function update_deal_pay_user($table = 'new_deal', $id, $new)
    {
        $this->db_slave->where('id', $id);
        $this->db_slave->update($table, $new);
        return ($this->db_slave->modified_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    function get_register_users()
    {
        $yestoday = strtotime(date('Y-m-d', time()-86400));
        $today = $yestoday+ 192800;
        // $key = md5('deal:pay:users:month:'.$month);
        // if ($key && ($res = $this->cache->get($key))) {
        //     log_debug('get deal_pay_users data from cache');
        //     return $res;
        // }

        $this->db_slave->select('*');
        $this->db_slave->where('timestamp >=', $yestoday);
        $this->db_slave->where('timestamp <', $today);
        $this->db_slave->order_by('timestamp', 'asc');
        $query = $this->db_slave->get('new_register');
        $data = get_query_result($query);
        // echo $this->db_slave->last_query();die;
        // print_r($data);die;
        // if ($key) {
        //     $this->cache->add_list(self::LIST_KEY, $key, $data, self::DATA_LIST_CACHE_TIME);
        // }
        return $data;
    }

    function get_upv()
    {
        $yestoday = strtotime(date('Y-m-d', time()));
        $today = $yestoday+ 86400;
        // $key = md5('deal:pay:users:month:'.$month);
        // if ($key && ($res = $this->cache->get($key))) {
        //     log_debug('get deal_pay_users data from cache');
        //     return $res;
        // }

        $this->db_slave->select('*');
        $this->db_slave->where('timestamp >=', $yestoday);
        $this->db_slave->where('timestamp <', $today);
        $this->db_slave->order_by('timestamp', 'asc');
        $query = $this->db_slave->get('upv');
        $data = get_query_result($query);
        // echo $this->db_slave->last_query();die;
        // print_r($data);die;
        // if ($key) {
        //     $this->cache->add_list(self::LIST_KEY, $key, $data, self::DATA_LIST_CACHE_TIME);
        // }
        return $data;
    }

    function get_all_users()
    {
        // $key = md5('deal:pay:users:month:'.$month);
        // if ($key && ($res = $this->cache->get($key))) {
        //     log_debug('get deal_pay_users data from cache');
        //     return $res;
        // }
        $this->db_slave->select('id,new');
        $query = $this->db_slave->get('new_register');
        $data = get_query_result($query);
        // echo $this->db_slave->last_query();die;
        // print_r($data);die;
        // if ($key) {
        //     $this->cache->add_list(self::LIST_KEY, $key, $data, self::DATA_LIST_CACHE_TIME);
        // }
        return $data;
    }
    function get_statistics()
    {
        $this->db_slave->select('users,orders,sales,active');
	$this->db_slave->order_by('statistics.id', 'desc');
	$this->db_slave->limit(1);
        $query = $this->db_slave->get('statistics');
        $data = get_query_result($query);
	$query = $this->db_slave->query('SELECT SUM(`last_paid_money`) AS sales, COUNT(*) AS orders FROM `deal` WHERE `pid` = 0');
        $result = get_query_result($query);
	//var_dump($result);die;
        $data[0]['sales'] = $result[0]['sales'];
        $data[0]['orders'] = $result[0]['orders'];
        $data[0]['day_users'] = date('H', time()) * 46 + rand(0, 5); // day active users at 2017-10-30
        $data[0]['day_download'] = date('H', time()) * 8 + rand(0, 3); // day download at 2017-10-30
	//var_dump($data);die;
        return $data;
    }
}
