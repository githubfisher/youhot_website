<?php

class Livecast_model extends MY_Model
{
    const NAME = 'color';
    const CACHE_TIME = 3600;

    const STATUS_INIT = 0;
    const STATUS_STARTING = 1;
    const STATUS_END = 2;
    const STATUS_END_RECORD = 3;

    function __construct()
    {
        parent::__construct();
//        $this->getRedis();
    }

    public function create($data)
    {
        //controller做好检查
        $this->db_master->insert(TBL_LIVECAST, $data);
        if ($this->db_master->affected_rows() > 0) {
            return $this->info($this->db_master->insert_id());

        }
        return DB_OPERATION_FAIL;
    }

    public static function gen_token($liveid,$userid){
        return md5('1^&scc'.$liveid.'cannot'.$userid);
    }
    public static function get_cast_url($liveid,$userid){
        $token = self::gen_token($liveid,$userid);
        return  sprintf('rtmp://101.200.145.116/hls/%s?author=%s&token=%s',$liveid,$userid,$token);
    }

    public function update_info($live_id, $data)
    {
        $this->db_master->where('live_id', (int)$live_id);
        $this->db_master->update(TBL_LIVECAST, $data);
        return ($this->db_master->modified_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function delete($live_id)
    {
        $this->db_master->where('live_id', (int)$live_id)
            ->delete(TBL_LIVECAST);
        return ($this->db_master->affected_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function get_list( $filter = array(), $offset = 0, $limit = 20, $order = '')
    {

//        $key = get_list_mem_key(self::NAME,array($author,$offset,$limit,$filter,$need_system,$order));
//        $this->load_memcached();
//        if($res = $this->mem->get($key)){
//            log_debug('get color list from cache');
//            return $res;
//        }

        $this->db_slave->start_cache();

        if(!empty($filter)){
            if(array_key_exists(TBL_LIVECAST.'.status',$filter) && is_array($filter[TBL_LIVECAST.'.status'])){
                array_walk($filter[TBL_LIVECAST.'.status'],function(&$item,$key){$item = TBL_LIVECAST.'.status='. (int) $item;});
                $this->db_slave->where( '('. implode(' or ',$filter[TBL_LIVECAST.'.status']) . ')');

                unset($filter[TBL_LIVECAST.'.status']);
            }
        }
        !empty($filter) && $this->db_slave->where($filter);

        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(TBL_LIVECAST);

        $this->db_slave->select(TBL_LIVECAST . '.*, au.facepic as author_facepic,au.nickname as author_nickname');

        $this->db_slave->join(TBL_USER . ' au', 'au.userid = ' . TBL_LIVECAST . '.author', 'left');

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (!empty($order)) {
            $this->db_slave->order_by($order, 'desc');
        } else {
            $this->db_slave->order_by('live_id', 'desc');
        }

        $query = $this->db_slave->get(TBL_LIVECAST);

        $this->db_slave->flush_cache();
        $_list = get_query_result($query);
        if(!empty($_list)){
            foreach($_list as &$row){
                $row['cast_url'] = self::get_cast_url($row['live_id'],$row['author']);
            }

        }
        $data =array('total' => $total, 'list' => $_list);
//        $this->mem->set($key,$data,self::CACHE_TIME);
        return $data;
    }

    public function get_owner($live_id)
    {
        $this->db_slave->select('author')
            ->where('live_id', (int)$live_id);
        $query = $this->db_slave->get(TBL_LIVECAST);
        return get_row_array($query);
    }

    public function info($live_id)
    {
        $this->db_slave->where('live_id', $live_id);


        $this->db_slave->select(TBL_LIVECAST . '.*, au.facepic as author_facepic,au.nickname as author_nickname');

        $this->db_slave->join(TBL_USER . ' au', 'au.userid = ' . TBL_LIVECAST . '.author', 'left');


        $query = $this->db_slave->get(TBL_LIVECAST);
        $res =  get_row_array($query);
        if(!empty($res)){
            $res['cast_url'] = self::get_cast_url($res['live_id'],$res['author']);
        }

        return $res;


    }

}
