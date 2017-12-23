<?php
class History_model extends MY_Model
{
    const TBL_HISTORY = 'history';
    const LIST_CACHE_TIME = 30;
    const LIST_KEY = 'history_list';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function getRows()
    {
        $key = md5('history_list:history_list');
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get history rows from cache');
            return $res;
        }

	$query = $this->db_slave->query('SELECT COUNT(T1.`sum`) AS rows FROM (SELECT COUNT(*) AS sum FROM '.self::TBL_HISTORY.' GROUP BY keywords) T1');
        $res = get_query_result($query);
	$rows = $res[0]['rows'];
	
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $rows, self::LIST_CACHE_TIME);
        }

        return $rows;
    }

    public function getData($offset=0,$page_size = 10)
    {
        $key = md5('history_list:offset:'.$offset.':page:'.$page_size);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get history list from cache');
            return $res;
        }

	$query = $this->db_slave->query('SELECT T1.* FROM (SELECT *, COUNT(*) as sum FROM '.self::TBL_HISTORY.' GROUP BY `keywords`)  T1 ORDER BY T1.`sum` DESC, T1.`keywords` ASC LIMIT '.$page_size.' OFFSET '.$offset);
	//echo $this->db_slave->last_query();die;
        $res = get_query_result($query);
	if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::LIST_CACHE_TIME);
        }

        return $res;
    }

    public function getDetailRows($keywords)
    {
        $key = md5('history_detail_list:history_detail_list'.$keywords);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get history detail rows from cache');
            return $res;
        }

        $query = $this->db_slave->query('SELECT COUNT(T1.`sum`) AS rows FROM (SELECT COUNT(*) AS sum FROM '.self::TBL_HISTORY.' WHERE `keywords` = "'.$keywords.'" GROUP BY `userid`) T1');
        //echo $this->db_slave->last_query();die;
        $res = get_query_result($query);
        $rows = $res[0]['rows'];

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $rows, self::LIST_CACHE_TIME);
        }

        return $rows;
    }

    public function getDetailData($keywords, $offset=0, $page_size = 10)
    {
        $key = md5('history_detail_list:offset:'.$offset.':page:'.$page_size.'keywords:'.$keywords);
        if ($key && ($res = $this->cache->get($key))) {
            log_debug('get history detail list from cache');
            return $res;
        }

        $query = $this->db_slave->query('SELECT T1.*,user.`nickname`,user.`username` FROM (SELECT *, COUNT(*) as sum FROM '.self::TBL_HISTORY.' WHERE `keywords` = "'.$keywords.'" GROUP BY `userid`) T1 JOIN user ON user.`userid` = T1.`userid` ORDER BY T1.`sum` DESC, user.`username` ASC LIMIT '.$page_size.' OFFSET '.$offset);
       // echo $this->db_slave->last_query();die;
        $res = get_query_result($query);
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::LIST_CACHE_TIME);
        }

        return $res;
    }

}
