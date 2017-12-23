<?php

class Mem_model extends MY_Model
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

    function get($id)
    {
	//echo '<pre>'; print_r($this->cache->on);die;
	//$info = $this->cache->cache_info();
	//print_r($info);die;
	$key = md5($id);
	//echo $key;die;
	if($key && ($res = $this->cache->get($key))) {
	    echo 'From Cache';
	    return $res;
	}
	$this->db_slave->select('id,price,cover_image,inventory,store');
	$this->db_slave->where('id = ', $id);
	$query = $this->db_slave->get('product');
	$res = get_query_result($query);

	if ($key) {
	   $result = $this->cache->save($key, $res, self::PRODUCT_INFO_CACHE_TIME);
	   //echo $result;
	}

	return $res;
    }
}
