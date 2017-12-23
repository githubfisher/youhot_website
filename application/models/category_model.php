<?php

class Category_model extends MY_Model
{
    const NAME = 'category';
    const LIST_KEY = 'cat_list';
    const CACHE_TIME = 3600;

    function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function get_list($offset = 0, $limit = 20, $filter = array()){
        $key = get_list_mem_key(self::NAME,array($offset,$limit,$filter));

        if($res = $this->cache->get($key)){
            log_debug('get category list from cache');
            return $res;
        }
        $this->db_slave->start_cache();
        $this->db_slave->where($filter);
        $this->db_slave->join('category_icae', 'category.name = category_icae.english_name'); // unsupport Upper Function
        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(TBL_CATEGORY);

        $this->db_slave->offset($offset);
        if(!empty($limit)){
            $this->db_slave->limit($limit);
        }
//        $this->db_slave->order_by('parent_id', 'asc');  //Maybe sort by other field
        $this->db_slave->order_by('order', 'asc');  //Maybe sort by other field

        $query = $this->db_slave->get(TBL_CATEGORY);

        $this->db_slave->flush_cache();
        $data =array('total' => $total, 'list' => get_query_result($query));
        $this->cache->add_list(self::LIST_KEY,$key,$data,self::CACHE_TIME);
        return $data;
    }


    public function create($data)
    {
        //controller做好检查
        $this->db_master->insert(TBL_CATEGORY, $data);
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
    public function update_info($product_id, $data)
    {
        $this->db_master->where('id', (int)$product_id);
        $this->db_master->update(TBL_CATEGORY, $data);
        if ($this->db_master->modified_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }


    public function delete($product_id)
    {
        $this->db_master->where('id', $product_id)
            ->delete(TBL_CATEGORY);
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function getAllList()
    {
	$query = $this->db_slave->select('id, name, chinese_name, weight, tax_rate, is_show, parent_id, level')
	    ->from(TBL_CATEGORY)
	    ->get();
	$res = get_query_result($query);

	return $res;
    }

    public function getBrandCategoryList()
    {
	$query = $this->db_slave->select('id, brand_name, category_name, weight, tax_rate, brand_id, category_id, status')
            ->from('brand_category')
            ->get();
        $res = get_query_result($query);

        return $res;
    }

    public function update_bc_info($id, $data)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update('brand_category', $data);
        if ($this->db_master->modified_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

    public function add_bc_info($data)
    {
	$this->db_master->insert('brand_category', $data);
        if ($this->db_master->affected_rows() > 0) {
            $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return $this->db_master->insert_id();

        }
        return DB_OPERATION_FAIL;
    }
}
