<?php

class Applicant_model extends MY_Model
{
    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        log_message('debug', "Users Model Class Initialized");
    }


    public function get_list($offset = 0, $limit = 20, $filter = array(), $order = null)
    {
        $this->db_slave->start_cache();

        $this->db_slave->select(TBL_APPLICANT . '.*');
        $this->db_slave->from(TBL_APPLICANT);
        if (!empty($filter)) {
            $this->db_slave->where($filter);
        }
        $this->db_slave->stop_cache();
        $total = $this->db_slave->count_all_results(TBL_APPLICANT);


        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        if (!empty($order)) {
            $this->db_slave->order_by(TBL_APPLICANT . '.' . $order, 'desc');  //Desc sort
        }
        $this->db_slave->order_by(TBL_APPLICANT . '.app_id', 'desc');  //Desc sort


        $query = $this->db_slave->get(TBL_APPLICANT);

        $this->db_slave->flush_cache();

        return array('total' => $total, 'list' => get_query_result($query));


    }

    public function update_info($app_id, $data)
    {
        $this->db_master->where('app_id', (int)$app_id);
        $this->db_master->update(TBL_APPLICANT, $data);
        return ($this->db_master->modified_rows() > 0) ? TRUE : FALSE;
    }
    /**
     * 添加一个用户
     *
     * @access publicupdate_info
     * @param int - $data 用户信息
     * @return boolean - success/failure
     */
    public function add($data)
    {
        $this->db_master->insert(TBL_APPLICANT, $data);
        return ($this->db_master->affected_rows() > 0) ?TRUE : FALSE;
    }

    function get_info($app_id)
    {
        $this->db_slave->where(TBL_APPLICANT.'.app_id', $app_id);

        $query = $this->db_slave->get(TBL_APPLICANT);
        $user_info = get_row_array($query);
        return $user_info;
    }
}
