<?php
class Form_model extends MY_Model
{
     public function __construct()
     {
	parent::__construct();
     }

     public function get_all($date)
     {
	$this->db_slave->select('d.order_id,d.pid,d.status,d.product_id,d.product_price,d.product_count,d.freight,d.tax,d.last_pay_coupon_value,d.promotion_value,d.last_paid_money,d.last_payment,d.last_paid_time,d.buyer_userid,d.last_paid_payinfo,d.create_time');
	$this->db_slave->select('s.show_name, s.rebate, s.id AS store');
	$this->db_slave->select('d.product_price * d.product_count * s.rebate AS cost, d.product_price * d.product_count * (1 - s.rebate) AS rebate_value');
	$this->db_slave->from(TBL_CDB_DEAL.' AS d');
	$this->db_slave->join(TBL_PRODUCT.' AS p', 'p.id = d.product_id', 'LEFT');
	$this->db_slave->join('store AS s', 's.id = p.store', 'LEFT'); 
	$this->db_slave->where('d.create_time LIKE', $date.'%');
	$query = $this->db_slave->get();
	//echo $this->db_slave->last_query();die;
	$res = get_query_result($query);

	return $res;
     }
}
