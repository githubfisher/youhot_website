<?php
class Mem extends User_Controller
{
    public function __construct()
    {
	parent::__construct();
        $this->load->model('mem_model', 'mem');
    }

    public function get()
    {
	$id = $this->input->get_post('id');
	$id = isset($id) ? $id : 845517;
	$res = $this->mem->get($id);
	
	exit(json_encode($res));
    }
}
