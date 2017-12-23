<?php
class Count_model extends MY_Model
{
    const SHIPPING_INFO_CACHE_TIME = 60; // 5 min
    const INFO_KEY = 'shipping_info';
    const TBL_DEALCART = 'dealcart';
    const TBL_STORE = 'store';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function get_shipping($id){
        $key = md5('store_id:' . $id.':status:1');
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get store shipping list from cache');
            return $res;
        }

    	$this->db_slave->select('shipping.*'); // store.direct_mail, store.number_one, store.country, store.direct_mail_rate
	$this->db_slave->from('shipping');
	// $this->db_slave->join('store', 'store.id = shipping.store_id');
    	$this->db_slave->where('shipping.store_id =', $id);
    	$this->db_slave->where('shipping.status =', 1);
    	$query = $this->db_slave->get();
    	$res = get_query_result($query);

        if ($key) {
            $this->cache->add_list(self::INFO_KEY, $key, $res, self::SHIPPING_INFO_CACHE_TIME);
        }

    	return $res;
    }

    public function get_products($products)
    {
    	$this->db_slave->select(TBL_PRODUCT.'.id,'.TBL_PRODUCT.'.price,'.TBL_PRODUCT.'.cover_image,'.TBL_PRODUCT.'.title,'.TBL_PRODUCT.'.author as brand');
	$this->db_slave->select(TBL_PRODUCT.'.category,'.TBL_CATEGORY.'.tax_rate,'.TBL_CATEGORY.'.weight,'.TBL_PRODUCT.'.pdt_price,'.TBL_PRODUCT.'.including_mfee');
    	$this->db_slave->join(TBL_CATEGORY, TBL_PRODUCT.'.category = '.TBL_CATEGORY.'.id', 'left');
    	$this->db_slave->where_in(TBL_PRODUCT.'.id', $products);
    	$this->db_slave->where(TBL_PRODUCT.'.status =', 1);
    	$query = $this->db_slave->get(TBL_PRODUCT);
    	$res = get_query_result($query);

    	return $res;
    }

    public function getTaxRate($pinfo)
    {
	$res = [];
	if (is_array($pinfo) && count($pinfo)) {
	    $query = 'SELECT `brand_id`,`category_id`,`tax_rate` FROM `brand_category` WHERE';
	    foreach ($pinfo as $k => $v) {
		$query .= ' (`brand_id` = '.$k.' AND `category_id` = '.$v.') OR';
	    }
	    $query = rtrim($query, 'OR');
	    logger('getTaxRate_SQL:'.$query); // debug
	    $q = $this->db_slave->query($query);
	    $res = get_query_result($q);
	}

	return $res;	
    }

    public function getProductsByCartId($cartids)
    {
        $this->db_slave->select(TBL_PRODUCT.'.id,'.TBL_PRODUCT.'.price,'.TBL_PRODUCT.'.cover_image,'.TBL_PRODUCT.'.title,'.TBL_PRODUCT.'.author as brand,'.TBL_PRODUCT.'.category');
        $this->db_slave->select(TBL_CATEGORY.'.tax_rate,'.TBL_CATEGORY.'.weight,'.TBL_PRODUCT.'.pdt_price,'.TBL_PRODUCT.'.including_mfee,'.self::TBL_DEALCART.'.is_luxury');
        $this->db_slave->select(self::TBL_DEALCART.'.referer,'.self::TBL_DEALCART.'.product_count AS num,'.self::TBL_DEALCART.'.product_size AS size,'.TBL_PRODUCT.'.pdt_price');
        $this->db_slave->select(self::TBL_DEALCART.'.product_color AS color,'.self::TBL_DEALCART.'.cartid,'.self::TBL_STORE.'.show_name AS store_name,'.TBL_PRODUCT.'.store');
        $this->db_slave->from(self::TBL_DEALCART);
        $this->db_slave->join(TBL_PRODUCT, TBL_PRODUCT.'.id = '.self::TBL_DEALCART.'.product_id', 'left');
        $this->db_slave->join(TBL_CATEGORY, TBL_PRODUCT.'.category = '.TBL_CATEGORY.'.id', 'left');
        $this->db_slave->join(self::TBL_STORE, self::TBL_STORE.'.id = '.TBL_PRODUCT.'.store');
        $this->db_slave->where_in(self::TBL_DEALCART.'.cartid', $cartids);
        $this->db_slave->where(TBL_PRODUCT.'.status =', 1);
        $query = $this->db_slave->get();
        //echo $this->db_slave->last_query();die;
        $res = get_query_result($query);

        return $res;
    }
}
