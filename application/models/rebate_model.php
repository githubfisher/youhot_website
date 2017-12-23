<?php

class Rebate_model extends MY_Model
{
    function __construct()
    {
	parent::__construct();
	$this->load->library('cache');
    }

    public function getRebateDeal($userid)
    {
	$this->db_slave->select(TBL_CDB_DEAL.'.order_id,'.TBL_CDB_DEAL.'.status,'.TBL_CDB_DEAL.'.product_price,'.TBL_CDB_DEAL.'.product_count,'.TBL_CDB_DEAL.'.last_paid_time');
	$this->db_slave->select(TBL_USER.'.userid,'.TBL_USER.'.nickname,'.TBL_USER.'.facepic');
	$this->db_slave->from(TBL_CDB_DEAL);
	$this->db_slave->join(TBL_USER, TBL_USER.'.userid = '.TBL_CDB_DEAL.'.buyer_userid');
	$this->db_slave->where(TBL_CDB_DEAL.'.referer = ', $userid);
	$this->db_slave->where_in(TBL_CDB_DEAL.'.status', [ORDER_STATUS_LAST_PAID, ORDER_STATUS_SHIP_START, ORDER_STATUS_SHIP_RECEIVED]);
	$query = $this->db_slave->get();
	logger('rebate_sql: '.$this->db_slave->last_query()); //debug

	return get_query_result($query);
    }

    public function getFootMark($userid)
    {
  
        $this->db_slave->select('footmark.*');
        $this->db_slave->select(TBL_USER.'.userid,'.TBL_USER.'.nickname,'.TBL_USER.'.facepic');
        $this->db_slave->from('footmark');
        $this->db_slave->join(TBL_USER, TBL_USER.'.userid = footmark.user_id');
        $this->db_slave->where('footmark.referer = ', $userid);
	$this->db_slave->group_by('footmark.user_id');
        $query = $this->db_slave->get();
        //logger('get_footmark_sql: '.$this->db_slave->last_query()); //debug

        return get_query_result($query);
    }

    public function getCard($userid)
    {
	$this->db_slave->select('id,bank_name, bank_facepic, card, name');
	$this->db_slave->where('userid = ', $userid);
	$this->db_slave->order_by('created_at', 'desc');
	$this->db_slave->limit(1);
	$query = $this->db_slave->get('bank_card');

	return get_query_result($query);
    }

    public function addCard($data)
    {
	$this->db_slave->insert('bank_card', $data);

	if ($this->db_slave->affected_rows() > 0) {
          //  $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return $this->db_slave->insert_id();
        }

        return DB_OPERATION_FAIL;
    }

    public function updateCard($card_id, $data)
    {
	$this->db_master->where('id', (int)$card_id);
        $this->db_master->update('bank_card', $data);
        if ($this->db_master->modified_rows() > 0) {
            //$this->cache->delete(self::_mem_key_product_info($product_id, 'basic'));
            //$this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            return DB_OPERATION_OK;
        }

        return DB_OPERATION_FAIL;
    }

    public function addWithdrawCash($data)
    {
	$this->db_slave->insert('withdraw_cash', $data);
    
        if ($this->db_slave->affected_rows() > 0) {
          //  $this->cache->delete_list(self::LIST_KEY);//清楚list cache
            return $this->db_slave->insert_id();
        }
        
        return DB_OPERATION_FAIL;
    }

    public function updateWithdrawCash($id, $data)
    {
        $this->db_master->where('id', (int)$id);
        $this->db_master->update('withdraw_cash', $data);
        if ($this->db_master->modified_rows() > 0) {
            //$this->cache->delete(self::_mem_key_product_info($product_id, 'basic'));
            //$this->cache->delete(self::_mem_key_product_info($product_id, 'detail'));
            return DB_OPERATION_OK;
        }

        return DB_OPERATION_FAIL;
    }

    public function withdrawCashList($userid=0)
    {
	$this->db_slave->select('withdraw_cash.*');
	$this->db_slave->select(TBL_USER.'.userid,'.TBL_USER.'.nickname,'.TBL_USER.'.facepic');
        $this->db_slave->from(withdraw_cash);
        $this->db_slave->join(TBL_USER, TBL_USER.'.userid = withdraw_cash.userid');
	if (!empty($userid)) {
            $this->db_slave->where(TBL_CDB_DEAL.'.referer = ', $userid);
	}
        $query = $this->db_slave->get();
        //logger('rebate_sql: '.$this->db_slave->last_query()); //debug

        return get_query_result($query);
    }

    public function withdrawCashSum($userid=0)
    {
	$this->db_slave->select_sum('value');
	$this->db_slave->where('userid = ', $userid);
	$query = $this->db_slave->get('withdraw_cash');
	//logger('withdrawCashSum_sql: '.$this->db_slave->last_query()); //debug

        return get_query_result($query);
    }
}
