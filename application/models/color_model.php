<?php

class Color_model extends MY_Model
{
    const NAME = 'color';
    const LIST_KEY = 'color_list';
    const CACHE_TIME = 3600;

    function __construct()
    {
        parent::__construct();
//        $this->getRedis();
        $this->load->library('cache');
    }

    public function create($data)
    {
        //controller做好检查
        $this->db_master->insert(TBL_COLOR, $data);
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache

            return $this->info($this->db_master->insert_id());

        }
        return DB_OPERATION_FAIL;
    }

    public function update_info($color_id, $data)
    {
        if (empty($data)) return DB_OPERATION_FAIL;
        $this->db_master->where('color_id', (int)$color_id);
        $this->db_master->update(TBL_COLOR, $data);
        if ($this->db_master->modified_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return DB_OPERATION_OK;
        }

        return DB_OPERATION_FAIL;
    }

    public function delete($color_id)
    {
        $this->db_master->where('color_id', (int)$color_id)
            ->delete(TBL_COLOR);
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function get_list($author, $offset = 0, $limit = 20, $need_system = true, $filter = array(), $order = '')
    {
        $key = get_list_mem_key(self::NAME, array($author, $offset, $limit, $filter, $need_system, $order));
        if ($res = $this->cache->get($key)) {
            log_debug('get category list from cache');
            return $res;
        }

        $this->db_slave->start_cache();
        $this->db_slave->where('author', $author);
        if ($need_system) {
            $this->db_slave->or_where('author', 0);  //filter system
        }
        $this->db_slave->where($filter);
        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(TBL_COLOR);

        $this->db_slave->select(TBL_COLOR . '.*, au.facepic as author_facepic,au.nickname as author_nickname');

        $this->db_slave->join(TBL_USER . ' au', 'au.userid = ' . TBL_COLOR . '.author', 'left');

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (!empty($order)) {
            $this->db_slave->order_by($order, 'desc');
        } else {
            $this->db_slave->order_by('color_id', 'desc');
        }

        $query = $this->db_slave->get(TBL_COLOR);
        
	$this->db_slave->flush_cache();
        $data = array('total' => $total, 'list' => get_query_result($query));
        $this->cache->add_list(self::LIST_KEY, $key, $data, self::CACHE_TIME);
        return $data;
    }

    public function get_owner($color_id)
    {
        $this->db_slave->select('author')
            ->where('color_id', (int)$color_id);
        $query = $this->db_slave->get(TBL_COLOR);
        return get_row_array($query);
    }

    public function info($color_id)
    {
        $this->db_slave->where('color_id', $color_id);

        $this->db_slave->select(TBL_COLOR . '.*, au.facepic as author_facepic,au.nickname as author_nickname');

        $this->db_slave->join(TBL_USER . ' au', 'au.userid = ' . TBL_COLOR . '.author', 'left');

        $query = $this->db_slave->get(TBL_COLOR);
        return get_row_array($query);
    }

}
