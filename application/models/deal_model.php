<?php

class Deal_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function update_info($order_id, $data)
    {
        $this->db_slave->where('order_id', (string)$order_id);
        $this->db_slave->update('deal', $data);
    }    

    public function update_infoV2($order_id, $data)
    {
	//$fields = ['promotion_code','promotion_value','last_update','last_pay_coupon_value','last_pay_coupon_code'];
	$fields = ['promotion_value','last_update','last_pay_coupon_value'];
	$query = "UPDATE `deal` SET";
	$keys = array_keys($data);
	foreach ($fields as $v) {
	    if (in_array($v, $keys)) { 
		$query .= " `".$v."` = '".$data[$v]."',";
	    }
	}
	$query = rtrim($query, ',');
	$query .= " WHERE `order_id` = '".$order_id."'";
	$this->db_slave->query($query);
    }    

    public function update($id, $data)
    {
        $this->db_slave->where('info_id', (string)$id);
        $this->db_slave->update('pay_info', $data);
    }

    public function add_deal($data=array())
    {
	$this->db_slave->insert('deal', $data);
	return ($this->db_slave->affected_rows() > 0) ? TRUE : FALSE;
    }
}
