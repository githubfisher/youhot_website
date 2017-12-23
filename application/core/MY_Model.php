<?php

class MY_Model extends CI_Model
{
    public $db_master;
    public $db_slave;

    function __construct()
    {
        parent::__construct();
        //公用super object ci的db连接
        $CI = &get_instance();
        if (!isset($CI->db_master)) {
            $CI->db_master = $this->load->database('style', TRUE);
        }
        if (!isset($CI->db_slave)) {
            $CI->db_slave = $CI->db_master;  //暂时是一样的,没有区分
        }
        $this->db_master = $CI->db_master;
        $this->db_slave = $CI->db_slave;
    }

}

