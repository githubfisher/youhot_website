<?php

class Comments_model extends MY_Model
{
    private $redis;

    //@todo redis 缓存

    function __construct()
    {
        parent::__construct();
//        $this->getRedis();
    }

    public function create($data)
    {
        //controller做好检查
        $this->db_master->insert(TBL_COMMENTS, $data);
        if ($this->db_master->affected_rows() > 0) {
            return $this->info($this->db_master->insert_id());

        }
        return DB_OPERATION_FAIL;
    }

    public function update_info($comment_id, $data)
    {
        $this->db_master->where('cid', (int)$comment_id);
        $this->db_master->update(TBL_COMMENTS, $data);
        return ($this->db_master->modified_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }


    public function delete($comment_id)
    {
        $this->db_master->where('cid', (int)$comment_id)
            ->delete(TBL_COMMENTS);
        return ($this->db_master->affected_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function get_list($host, $offset = 0, $limit = 20, $filter = array(), $order = '')
    {
        $this->db_slave->start_cache();
        $this->db_slave->where('host_id', $host);
        $this->db_slave->where($filter);
        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(TBL_COMMENTS);

        $this->db_slave->select(TBL_COMMENTS . '.*, au.facepic as author_facepic,au.nickname as author_nickname,reply.facepic as reply_facepic,reply.nickname as reply_nickname');

        $this->db_slave->join(TBL_USER . ' au', 'au.userid = ' . TBL_COMMENTS . '.author', 'left');
        $this->db_slave->join(TBL_USER . ' reply', 'reply.userid = ' . TBL_COMMENTS . '.reply_userid', 'left');

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (!empty($order)) {
            $this->db_slave->order_by($order, 'desc');
        } else {
            $this->db_slave->order_by('cid', 'desc');
        }

        $query = $this->db_slave->get(TBL_COMMENTS);

        $this->db_slave->flush_cache();
        return array('total' => $total, 'list' => get_query_result($query));
    }

    public function get_owner($comment_id)
    {
        $this->db_slave->select('author')
            ->where('cid', (int)$comment_id);
        $query = $this->db_slave->get(TBL_COMMENTS);
        return get_row_array($query);
    }

    public function info($comment_id)
    {
        $this->db_slave->where('cid', $comment_id);


        $this->db_slave->select(TBL_COMMENTS . '.*, au.facepic as author_facepic,au.nickname as author_nickname,reply.facepic as reply_facepic,reply.nickname as reply_nickname');

        $this->db_slave->join(TBL_USER . ' au', 'au.userid = ' . TBL_COMMENTS . '.author', 'left');
        $this->db_slave->join(TBL_USER . ' reply', 'reply.userid = ' . TBL_COMMENTS . '.reply_userid', 'left');


        $query = $this->db_slave->get(TBL_COMMENTS);
        return get_row_array($query);
    }

}
