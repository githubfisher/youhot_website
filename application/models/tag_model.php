<?php

class Tag_model extends MY_Model
{
    const NAME = 'tag';
    const CACHE_TIME = 3600;
    const LIST_KEY='tag_list';
    //@todo redis 缓存

    function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function get_list($offset = 0, $limit = 20, $filter = array()){
        $key = get_list_mem_key(self::NAME,array($offset,$limit,$filter));
        if($res = $this->cache->get($key)){
            log_debug('get tag list from cache');
            return $res;
        }

        $this->db_slave->start_cache();
        $this->db_slave->where($filter);
        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(TBL_TAGS);

        $this->db_slave->offset($offset);
        if(!empty($limit)){
            $this->db_slave->limit($limit);
        }
        $this->db_slave->order_by('id', 'desc');  //Maybe sort by other field

        $query = $this->db_slave->get(TBL_TAGS);

        $this->db_slave->flush_cache();
        $data =array('total' => $total, 'list' => get_query_result($query));
        $this->cache->add_list(self::LIST_KEY,$key,$data,self::CACHE_TIME);
        return $data;
    }




}
