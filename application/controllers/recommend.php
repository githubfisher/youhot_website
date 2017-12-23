<?php
/*
 *Class Recommend
 * @method
 */
class Recommend extends User_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('commond_model', 'commond');
    }

    public function get_list()
    {
	$res = $this->commond->get_list();
	$this->_result(bp_operation_ok, $res);
    }
}
