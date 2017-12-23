<?php
class Message_model extends MY_Model
{
    const TBL_MSG = 'message';
    const TBL_USER_MSG = 'user_msg';
    const LIST_KEY = 'msg_list';
    const MSG_LIST_CACHE_TIME = 3; // 5 min

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function getList($userid, $type, $offset = 0, $limit = 10)
    {
	$all_key = md5('get_list_counts:'.$type);
	if ($all_key && ($total = $this->cache->get($all_key))) {
	    $data['total'] =& $total;
	} else {
	    $this->db_slave->where(self::TBL_USER_MSG.'.userid =', $userid);
            $this->db_slave->where(self::TBL_USER_MSG.'.type', $type);
	    $total = $this->db_slave->count_all_results(self::TBL_USER_MSG);

	    if ($all_key) {
                $this->cache->add_list(self::LIST_KEY, $all_key, $total, self::MSG_LIST_CACHE_TIME);
            }
	    $data['total'] =& $total;
	}

	$key = md5('get_list:'.$userid.':'.$type.':'.$offset.':'.$limit);
        if ($key && ($list = $this->cache->get($key))) {
            log_debug('get store rows from cache');
	    $data['list'] = $list;
	    $data['count'] = count($list);

            return $data;
        }
	

        $this->db_slave->select(self::TBL_MSG.'.*,'.self::TBL_USER_MSG.'.status as is_read,'.TBL_PRODUCT.'.cover_image,'.TBL_PRODUCT.'.status as product_status');
        $this->db_slave->join(self::TBL_USER_MSG, self::TBL_USER_MSG.'.msgid = '.self::TBL_MSG.'.id');
        $this->db_slave->join(TBL_PRODUCT, self::TBL_MSG.'.tid = '.TBL_PRODUCT.'.id');
        $this->db_slave->where(self::TBL_USER_MSG.'.userid =', $userid);
        // $this->db_slave->where(self::TBL_USER_MSG.'.status =', 0);
        // $this->db_slave->where(self::TBL_MSG.'.available_end >', time());
	$this->db_slave->where(self::TBL_USER_MSG.'.type', $type);
	$this->db_slave->order_by(self::TBL_USER_MSG.'.create_at', 'desc');
	$this->db_slave->offset($offset);
	$this->db_slave->limit($limit);
        $query = $this->db_slave->get(self::TBL_MSG);
        $list = get_query_result($query);
//echo $this->db_slave->last_query();die;

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $list, self::MSG_LIST_CACHE_TIME);
        }
	$data['list'] =& $list;
	$data['count'] = count($list);

        return $data;
    }

    public function getNoteList($type = 1, $offset = 0, $limit = 10)
    {

	$all_key = md5('get_list_counts:'.$type);

        if ($all_key && ($total = $this->cache->get($all_key))) {
            $data['total'] =& $total;
        } else {
            $this->db_slave->where(self::TBL_MSG.'.type', $type);
            $total = $this->db_slave->count_all_results(self::TBL_MSG);
 
            if ($all_key) {
                $this->cache->add_list(self::LIST_KEY, $all_key, $total, self::MSG_LIST_CACHE_TIME);
            }
            $data['total'] =& $total;
        }

	$key = md5('get_note_list:'.$type.':'.$offset.':'.$limit);
        if ($key && ($list = $this->cache->get($key))) {
            log_debug('get store rows from cache');
	    $data['list'] =& $list;
	    $data['count'] = count($list);

            return $data;
        }

        $this->db_slave->select(self::TBL_MSG.'.*, collection.cover_image');
        // $this->db_slave->where(self::TBL_MSG.'available_end >', time());	
        $this->db_slave->join('collection', 'collection.id = '.self::TBL_MSG.'.tid', 'left');
        $this->db_slave->where(self::TBL_MSG.'.type =', $type);
	$this->db_slave->order_by(self::TBL_MSG.'.create_at', 'desc');
        $this->db_slave->offset($offset);
        $this->db_slave->limit($limit);
        $query = $this->db_slave->get(self::TBL_MSG);
        $list = get_query_result($query);
//echo $this->db_slave->last_query();die;
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $list, self::MSG_LIST_CACHE_TIME);
        }
	$data['list'] =& $list;
	$data['count'] = count($list);

        return $data;
    }

    public function getMsgList($userid, $type)
    {
	$key = md5('get_note_list:'.$userid.':'.$type);
        if ($key && ($data = $this->cache->get($key))) {
            log_debug('get store rows from cache');
            return $data;
        }

        $this->db_slave->select('msgid,status,userid');
        $this->db_slave->where(self::TBL_USER_MSG.'.userid =', $userid);
        $this->db_slave->where(self::TBL_USER_MSG.'.status =', 1);
	$this->db_slave->where(self::TBL_USER_MSG.'.type =', $type);
        $query = $this->db_slave->get(self::TBL_USER_MSG);
        $data = get_query_result($query);
        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $data, self::MSG_LIST_CACHE_TIME);
        }

        return $data;	
    }

    public function readMsgs($filter)
    {
	if (is_array($filter) && count($filter)) {
	    $this->db_slave->where($filter);
	    $this->db_slave->where('status =', 0);
	    $this->db_slave->update(self::TBL_USER_MSG, ['status' => 1, 'read_at' => time()]);
	    // echo $this->db_slave->last_query(); die;
	    if ($this->db_slave->modified_rows() > 0) {
		return DB_OPERATION_OK;
	    }
        }
	return DB_OPERATION_FAIL;
    }

    public function createReadDatas($data)
    {
	if (is_array($data) && count($data)) {
	    $this->db_slave->insert_batch(self::TBL_USER_MSG, $data);
            //echo $this->db_master->last_query();die;
            if ($this->db_slave->affected_rows() > 0) {
              	return DB_OPERATION_OK;
            }
	}
	return DB_OPERATION_FAIL;
    }

    public function getUnreadList($userid, $offset=0, $limit=1000)
    {
	$key = md5('get_unread_list:'.$userid);
        if ($key && ($data = $this->cache->get($key))) {
            log_debug('get unread_list rows from cache');
            return $data;
        }

        $this->db_slave->select('msgid,status,userid,type');
        $this->db_slave->where(self::TBL_USER_MSG.'.userid =', $userid);
        $this->db_slave->where(self::TBL_USER_MSG.'.status =', 0);
        $query = $this->db_slave->get(self::TBL_USER_MSG);
        $data = get_query_result($query);

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $data, self::MSG_LIST_CACHE_TIME);
        }

        return $data;	
    }

    public function getMsgType($type=1, $days=7)
    {
        $this->db_slave->select('id,type');
        $this->db_slave->where(self::TBL_MSG.'.type =', $type);
	$this->db_slave->where(self::TBL_MSG.'.create_at >=', time()-86400*$days);
	$query = $this->db_slave->get(self::TBL_MSG);

	return get_query_result($query);
    }
    public function getReadType($type=1, $userid, $days=7)
    {
        $this->db_slave->select('msgid,type');
	$this->db_slave->where(self::TBL_USER_MSG.'.type =', $type);
	$this->db_slave->where(self::TBL_USER_MSG.'.userid =', $userid);
        $this->db_slave->where(self::TBL_USER_MSG.'.create_at >=', time()-86400*$days);
        $query = $this->db_slave->get(self::TBL_USER_MSG);

        return get_query_result($query);
    }
}
