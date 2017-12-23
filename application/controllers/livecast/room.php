<?php

/**
 * Class category
 * @method
 */
class Room extends User_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('livecast_model', 'livecast');
        $this->setOutputType('json');
    }

    /**
     * save tag
     * @todo save tag
     */
    public function check($roomid)
    {
        $_POST = $_REQUEST;
        $this->_validation([
           ["userid",'user id','trim|numeric|required'],
            ["token",'token','trim|required'],
        ]);
        if(Livecast_model::gen_token($roomid,$this->input->get_post('userid')) == $this->input->get_post('token')){
            $this->_success();
        }
        $this->_error(bp_operation_verify_fail,$this->lang->line('bp_operation_verify_fail_hint'));
    }

    public function index()
    {
        $this->_need_login(true);

        $_POST = $_REQUEST;
        $this->_validation([
            ["title",'title','trim|required|max_length[32]|min_length[2]'],
            ["location",'location','trim|required'],
            ["cover",'cover','trim'],
        ]);

        $data = filter_data($this->input->post(),['title','cover','description','location','lat_long']);
        $data['start_time'] = standard_date("DATE_MYSQL");
        $data['status'] = 1;
        $data['author'] = $this->userid;

        $res = $this->livecast->create($data);
        if($res !== DB_OPERATION_FAIL){
            $this->_result(bp_operation_ok,$res);
            return;
        }
        $this->_error(bp_operation_fail,$this->lang->line('bp_operation_fail_hint'));
    }
    public function close($roomid)
    {
        $roomid = (int) $roomid;
        $data['status'] = 0;
        $data['end_time'] = standard_date("DATE_MYSQL");
        $res = $this->livecast->update_info($roomid,$data);
        $this->_deal_res($res);
    }
    public function info($roomid)
    {
        $roomid = (int) $roomid;
        $res = $this->livecast->info($roomid);
        if(!empty($res)){
            $this->_result(bp_operation_ok,$res);
            return;
        }
        $this->_error(bp_operation_fail,$this->lang->line('bp_operation_fail_hint'));
    }

    public function get_list()
    {

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');

        $filter = array();
        $this->input->get_post('userid') && $filter['author'] = (int) $this->input->get_post('userid');
        $this->input->get_post('status') && $filter['status'] = (int) $this->input->get_post('status');

        $res = $this->livecast->get_list($filter,$offset, $limit);
        // res is ordered by order asc
        if (empty($res['list'])) {
            log_message('error', 'room list error:' . json_encode($res));
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        } else {
            $this->_result(bp_operation_ok, $res);
        }


    }

    private function cat_camp($a,$b){

        return ((int)$a['order'] - (int)$b['order']) ;
    }
}
