<?php

class Ship_model extends MY_Model
{
    private $redis;

    //@todo redis 缓存

    function __construct()
    {
        parent::__construct();
//        $this->getRedis();
    }

    function getRedis()
    {
        if (empty($this->redis)) {
            $this->load->library('Redis', array(), 'redis_lib');
            $this->redis = $this->redis_lib->getRedis();
        }
    }

    /**
     * 支持我的发布商品,按照tag|cat筛选
     * $filter=array('product.category'=>cat_id,'product_tag.tag_id'=>tag_id)
     * 查看设计师的发布商品,按照tag|cat筛选
     * 查看tag|cat商品
     * @param $userid
     * @param int $offset
     * @param int $limit
     * @param array $filter
     * @return array
     */
    public function get_list($userid = null, $offset = 0, $limit = 20, $filter = array(), $order = null)
    {
        $this->db_slave->start_cache();
        $this->db_slave->from(TBL_SHIP_INFO);


        if (!empty($userid)) {
            //按照author筛选
            $this->db_slave->where(TBL_SHIP_INFO . '.author', $userid);
        }

        if (!empty($filter)) {
//            if (array_key_exists(TBL_PRODUCT_TAG . '.tag_id', $filter)) {
//                $this->db_slave->join(TBL_PRODUCT_TAG, TBL_PRODUCT_TAG . '.ship_id = ' . TBL_SHIP_INFO . '.id', 'left');
//            }
            $this->db_slave->where($filter);
        }
        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_SHIP_INFO);


        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (empty($order)) {
            $order = 'ship_id';    //Set default order field;
        }
        $this->db_slave->order_by(TBL_SHIP_INFO . '.default', 'desc');  //default sort
        $this->db_slave->order_by(TBL_SHIP_INFO . '.' . $order, 'desc');  //Desc sort


        $query = $this->db_slave->get(TBL_SHIP_INFO);

        $this->db_slave->flush_cache();

        return array('total' => $total, 'list' => get_query_result($query));

    }


    public function create($data)
    {
        $filter_rule = array('receiver', 'phone_num', 'district', 'address', 'default', 'author', 'idcard','fullname','idimg1','idimg2');
        $insert_data = filter_data($data, $filter_rule);

        //先把之前的default信息清空
        if (array_key_exists('default', $data) && $data['default'] == SHIP_INFO_IS_DEFAULT) {
            $this->_clear_default($insert_data['author']);
        }

        $this->db_master->insert(TBL_SHIP_INFO, $insert_data);

        if ($this->db_master->affected_rows() > 0) {
            return $this->info($this->db_master->insert_id());
        }
        return DB_OPERATION_FAIL;
    }

    /**
     * @param $ship_id
     * @return array|int
     */
    public function info($ship_id)
    {
        //Get data from master db because slave db may have a sysnc delay.
//        @todo redis 缓存
//        if($this->redis->exists('product:'.$ship_id)){
//            return json_decode($this->redis->get('product:'.$ship_id));
//        }
        $query = $this->db_master->where(TBL_SHIP_INFO . '.ship_id', $ship_id)->get(TBL_SHIP_INFO);
        $product_info = get_row_array($query);
        if (empty($product_info)) {
            log_message('error', 'Get product info error:' . $this->db_master->last_query());
            return DB_OPERATION_FAIL;
        }

        return $product_info;

    }


    /**
     * 修改用户信息
     *
     * @access public
     * @param int - $userid 用户ID
     * @param array - $data 用户信息
     * @return boolean - success/failure
     */
    public function update_info($ship_id, $data, $author)
    {

        //先把之前的default信息清空
        if (array_key_exists('default', $data) && $data['default'] == SHIP_INFO_IS_DEFAULT) {
            $this->_clear_default($author);
        }

        $this->db_master->where('ship_id', (int)$ship_id);
        $this->db_master->update(TBL_SHIP_INFO, $data);

        return ($this->db_master->modified_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function get_owner($ship_id)
    {
        $this->db_slave->select('author')->where('ship_id', (int)$ship_id);
        $query = $this->db_slave->get(TBL_SHIP_INFO);
        return get_row_array($query);
    }

    /**
     * clear default
     * @param $author
     * @return bool
     */
    private function _clear_default($author){
        $default_data = array('default' => SHIP_INFO_IS_NOT_DEFAULT);
        $query = $this->db_master->where('author', $author)->update(TBL_SHIP_INFO, $default_data);
        return (bool) $query;
    }

    /**
     * @param $ship_id
     * @return int
     */
    public function delete($ship_id){
        $this->db_master->where('ship_id', (int)$ship_id)->delete(TBL_SHIP_INFO);
        return ($this->db_master->affected_rows() > 0) ? DB_OPERATION_OK : DB_OPERATION_FAIL;
    }

    public function getSum($userid)
    {
        $this->db_slave->where(TBL_SHIP_INFO . '.author', $userid);
        return $this->db_slave->count_all_results(TBL_SHIP_INFO);
    }
}
