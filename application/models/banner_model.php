<?php

class Banner_model extends MY_Model
{
    const NAME = 'banners';
    const CACHE_TIME = 3600;
    const TABLE = "banners";
    const LIST_KEY = 'banner_list';



    function __construct()
    {
        parent::__construct();
//        $this->getRedis();
        $this->load->library('cache');
    }

    public function get_list($offset = 0, $limit = 20, $filter = array())
    {
        $key = get_list_mem_key(self::NAME,array($offset,$limit,$filter));
        if($res = $this->cache->get($key)){
            log_debug('get banner list from cache');
            return $res;
        }
        $this->db_slave->start_cache();
        $this->db_slave->where($filter);
        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(self::TABLE);

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        $query = $this->db_slave->get(self::TABLE);

        $this->db_slave->flush_cache();

        $data =array('total' => $total, 'list' => get_query_result($query));
        $this->cache->add_list(self::LIST_KEY,$key,$data,self::CACHE_TIME);
        return $data;
    }


    public function create($data)
    {
        //controller做好检查
        $this->db_master->insert(self::TABLE, $data);
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return $this->db_master->insert_id();

        }
        return DB_OPERATION_FAIL;
    }


    /**
     * 修改用户信息
     *
     * @access public
     * @param int - $userid 用户ID
     * @param array - $data 用户信息
     * @return boolean - success/failure
     */
    public function update_info($banner_id, $data)
    {
        $this->db_master->where('bid', (int)$banner_id);
        $this->db_master->update(self::TABLE, $data);
        if ($this->db_master->modified_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }


    public function delete($banner_id)
    {
        $this->db_master->where('bid', $banner_id)
            ->delete(self::TABLE);
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

}
