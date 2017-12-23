<?php
class Commond_model extends MY_Model
{
    const TBL_COMMOND = 'commond';
    const TBL_PRODUCT = 'product';
    const TBL_STORE = 'store';
    const TBL_COUNTRY = 'country';
    const INFO_CACHE_TIME = 300; // 5 min
    const LIST_CACHE_TIME = 300; // 5 min
    const CACHE_OBJECT = 'commond';
    const LIST_KEY = 'col_list';

    public function __construct()
    {
        parent::__construct();
	$this->load->library('cache');
    }

    public function get_list()
    {
	$key = $this->_mem_key_commond_list();
        if($res = $this->cache->get($key)){
            log_debug('get commond list from cache');
            return $res;
        }

        $this->db_slave->select('b.id,b.title,b.cover_image,b.price,b.presale_price,b.status,b.inventory,b.discount,a.cover_url,c.id as store_id,c.show_name as store_name,d.flag_url');
        $this->db_slave->from(self::TBL_COMMOND.' as a');
        $this->db_slave->join(self::TBL_PRODUCT.' as b', "a.product_id=b.id");
	$this->db_slave->join(self::TBL_STORE.' as c', "b.store=c.id");
        $this->db_slave->join(self::TBL_COUNTRY.' as d', "c.country=d.name");
	$this->db_slave->where('b.status', PRODUCT_STATUS_PUBLISHED);
	$this->db_slave->where('b.cover_image != ', ''); 
        $query = $this->db_slave->get();
        $res = get_query_result($query);

	if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $res, self::LIST_CACHE_TIME);
        }

        return $res;
    }

    private static function _mem_key_commond_list(){
        return self::CACHE_OBJECT.':commond_list';
    }

    public function add($id)
    {
        $param = array(
            'product_id' => $id,
            'create_at' => time()
        );
        $this->db_master->insert(self::TBL_COMMOND, $param);
        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;
    }

    public function delete($id)
    {
        $this->db_master->where('product_id', (int)$id)->delete(self::TBL_COMMOND);
        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;
    }

    public function getRows()
    {
        $rows = $this->db_slave->count_all(self::TBL_COMMOND);
        return $rows;
    }

    public function getData($offset=0,$page_size = 10)
    {
        $this->db_slave->select('b.id,b.title,b.cover_image,b.price,b.status,b.inventory,b.discount,a.cover_url,b.m_url');
        $this->db_slave->from(self::TBL_COMMOND.' as a');
        $this->db_slave->join(self::TBL_PRODUCT.' as b', "a.product_id=b.id");
        $this->db_slave->offset($offset);
        $this->db_slave->limit($page_size);
        $this->db_slave->order_by('a.id', 'asc');
        $query = $this->db_slave->get();
        $res = get_query_result($query);

        return $res;
    }

    public function update($id, $data)
    {
	$this->db_master->where('product_id', (int)$id);
        $this->db_master->update(self::TBL_COMMOND, $data);
        if ($this->db_master->modified_rows() > 0) {
            return true;
        }
        return false;
    }
}
