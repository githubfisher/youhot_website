<?php
class Tool extends Normal_Controller
{
    public function __construct()
    {
	parent::__construct();
	$this->load->model('tool_model', 'tool');
    }

    public function getMapping()
    {
	$table = $this->input->get_post('table');
	$english = $this->input->get_post('english');
	$chinese = $this->input->get_post('chinese');
	$filter = json_decode($this->input->get_post('filter'), true);

	$res = $this->tool->getMapping($table, $english.','.$chinese, $filter);
	if ($res) {
	    $map = [];
	    foreach ($res as $k => $v) {
	  	$map[$v[$english]] = $v[$chinese];
	    }
	    logger(var_export($map, true), $table, $table);
	}
    }
}
