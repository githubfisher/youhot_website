<?php
class Tool_model extends MY_Model
{
    public function __construct()
    {
	parent::__construct();
    }

    public function getMapping($table, $fields, $filter=array())
    {
	$this->db_slave->select($fields);
	$this->db_slave->where($filter);
	$query = $this->db_slave->get($table);
	$res = get_query_result($query);

	return $res;
    }
}
