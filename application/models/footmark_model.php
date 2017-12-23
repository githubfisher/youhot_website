<?php
class Footmark_model extends MY_Model
{
    const INFO_CACHE_TIME = 300; // 5 min
    const LIST_CACHE_TIME = 300; // 5 min
    const TABLE = 'footmark';
    const LIST_KEY = 'footmark_list';

    function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function update($userid, $productid, $referer=0)
    {
	$referer = $userid == $referer ? 0 : $referer; // refuse self-referer
        $item = $this->getItem($userid, $productid);
        if (empty($item[0]['id'])) {
            $res = $this->createItem($userid, $productid, $referer);
        } else {
            $res = $this->updateItem($item[0]['id'], array('browse_at' => time(), 'referer' => $referer));
        }

        //return $res;
    }

    private function createItem($userid, $productid, $referer)
    {
        $data = array(
            'user_id' => $userid,
            'product_id' => $productid,
            'browse_at' => time(),
	    'referer' => $referer,
        );
        $this->db_slave->insert(self::TABLE, $data);
        if ($this->db_slave->affected_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    private function getItem($userid, $productid)
    {
        $query = $this->db_slave->select('id')
            ->where('user_id =', $userid)
            ->where('product_id =', $productid)
            ->from(self::TABLE)
            ->get();
        $res = get_query_result($query);

        return $res;
    }

    private function updateItem($id, $data)
    {
        $this->db_slave->where('id', $id)
            ->update(self::TABLE, $data);
        if ($this->db_slave->affected_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function getItemList($userid, $offset = 0, $limit = 10)
    {


    }

}
