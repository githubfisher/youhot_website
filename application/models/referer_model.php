<?php
class Referer_model extends MY_Model
{
    const TBL_TABLE = 'deal';
    const LIST_CACHE_TIME = 30;
    const LIST_KEY = 'referer_list';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function getRows($start=0, $end=0)
    {
        $key = md5('referer_list:referer_list');
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get referer rows from cache');
            return $res;
        }
	$start = empty($start) ? date('Y-m-01', strtotime('-1 month')) : $start;
	$end = empty($end) ? date('Y-m-01', strtotime(date('Y-m-d'))) : $end;
        $query = $this->db_slave->query('SELECT COUNT(T1.`sum`) AS rows FROM (SELECT COUNT(*) AS sum FROM '.self::TBL_TABLE.'  WHERE `referer` > 0 AND `last_paid_time` > \''.$start.'\' AND `last_paid_time` < \''.$end.'\' AND `status` IN ('.ORDER_STATUS_LAST_PAID.','.ORDER_STATUS_SHIP_START.','.ORDER_STATUS_SHIP_RECEIVED.') GROUP BY `referer`) T1');
      	//echo $this->db_slave->last_query();die;
        $res = get_query_result($query);
        $rows = $res[0]['rows'];

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $rows, self::LIST_CACHE_TIME);
        }

        return $rows;
    }

    public function getData($offset=0,$page_size = 10, $start=0, $end=0)
    {
        $key = md5('referer_list:offset:'.$offset.':page:'.$page_size);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get referer list from cache');
            return $res;
        }
	$start = empty($start) ? date('Y-m-01', strtotime('-1 month')) : $start;
	$end = empty($end) ? date('Y-m-01', strtotime(date('Y-m-d'))) : $end;
	$query = $this->db_slave->query('SELECT T1.* FROM (SELECT `referer`, COUNT(*) as sum FROM '.self::TBL_TABLE.'  WHERE `referer` > 0 AND `last_paid_time` > \''.$start.'\' AND `last_paid_time` < \''.$end.'\' AND `status` IN ('.ORDER_STATUS_LAST_PAID.','.ORDER_STATUS_SHIP_START.','.ORDER_STATUS_SHIP_RECEIVED.') GROUP BY `referer`)  T1 ORDER BY T1.`sum` DESC, T1.`referer` ASC LIMIT '.$page_size.' OFFSET '.$offset);
        //echo $this->db_slave->last_query();die;
        $res = get_query_result($query);
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::LIST_CACHE_TIME);
        }

        return $res;
    }

    public function getDetailRows($referer, $start=0, $end=0)
    {
        $key = md5('referer:'.$referer.'start:'.$start.'end:'.$end);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get referer detail rows from cache');
            return $res;
        }

	$start = empty($start) ? date('Y-m-01', strtotime('-1 month')) : $start;
	$end = empty($end) ? date('Y-m-01', strtotime(date('Y-m-d'))) : $end;
        $query = $this->db_slave->query('SELECT COUNT(T1.`order_id`) AS rows FROM (SELECT order_id FROM '.self::TBL_TABLE.' WHERE `referer` = '.$referer.' AND `last_paid_time` > \''.$start.'\' AND `last_paid_time` < \''.$end.'\' AND `status` IN ('.ORDER_STATUS_LAST_PAID.','.ORDER_STATUS_SHIP_START.','.ORDER_STATUS_SHIP_RECEIVED.')) T1');
        //echo $this->db_slave->last_query();die;
        $res = get_query_result($query);
        $rows = $res[0]['rows'];

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $rows, self::LIST_CACHE_TIME);
        }

        return $rows;
    }

    public function getDetailData($referer, $offset=0, $page_size = 10, $start=0, $end=0)
    {
        $key = md5('referer_detail_list:offset:'.$offset.':page:'.$page_size.'referer:'.$referer);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get referer detail list from cache');
            return $res;
        }

	$start = empty($start) ? date('Y-m-01', strtotime('-1 month')) : $start;
        $end = empty($end) ? date('Y-m-01', strtotime(date('Y-m-d'))) : $end;
	$query = $this->db_slave->query('SELECT T1.* FROM (SELECT d.`order_id`,d.`pid`,d.`status`,d.`product_id`,d.`product_price`,d.`product_count`,d.`product_title`,d.`product_cover_image`,d.`last_payment`,d.`last_paid_time`,u.`nickname`,d.`last_pay_coupon_value`,d.`promotion_value`,d.`freight`,d.`tax`,d.`last_paid_payinfo`,d.`last_paid_money` FROM '.self::TBL_TABLE.' AS d JOIN '.TBL_USER.' AS u ON u.`userid` = d.`buyer_userid` WHERE d.`referer` = "'.$referer.'" AND d.`last_paid_time` > \''.$start.'\' AND d.`last_paid_time` < \''.$end.'\' AND d.`status` IN ('.ORDER_STATUS_LAST_PAID.','.ORDER_STATUS_SHIP_START.','.ORDER_STATUS_SHIP_RECEIVED.') ORDER BY d.`last_paid_time` DESC, d.`order_id` DESC) T1 LIMIT '.$page_size.' OFFSET '.$offset);
        //echo $this->db_slave->last_query();die;
        $res = get_query_result($query);
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::LIST_CACHE_TIME);
        }

        return $res;
    }

    public function getPidDetail($pids)
    {
	$key = md5('pids:'.json_encode($pids));
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get pids detail list from cache');
            return $res;
        }

	$this->db_slave->select('order_id,status,freight,tax,last_pay_coupon_value,promotion_value,last_payment');
	$this->db_slave->from(TBL_CDB_DEAL);
	$this->db_slave->where_in('order_id', $pids);
	$query = $this->db_slave->get();
	//echo $this->db_slave->last_query();die;
	$res = get_query_result($query);

	if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::LIST_CACHE_TIME);
        }

        return $res;
    }

}
