<?php

/**
 * Class category
 * @method
 */
class Livecast extends User_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('livecast_model', 'livecast');
        $this->setOutputType('json');
    }

    private function _check_param(){
        $_POST = $_REQUEST;
        $this->_validation([
            ["author",'user id','trim|numeric|required'],
            ["name",'user id','trim|numeric|required'],
            ["token",'token','trim|required'],
        ]);
        if(Livecast_model::gen_token($this->input->get_post('name'),$this->input->get_post('author')) == $this->input->get_post('token')){
            return true;
        }
        return false;
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
//        $data['status'] = 1;
        $data['author'] = $this->userid;

        $res = $this->livecast->create($data);
        if($res !== DB_OPERATION_FAIL){
            $this->_result(bp_operation_ok,$res);
            return;
        }
        $this->_error(bp_operation_fail,$this->lang->line('bp_operation_fail_hint'));
    }
    public function close()
    {
        if(!$this->_check_param()){
            $this->_error(bp_operation_verify_fail,$this->lang->line('bp_operation_verify_fail_hint'),403);
            return;
        }
        $liveid = (int) $this->input->get_post('name');
        $data['status'] = Livecast_model::STATUS_END;
        $data['end_time'] = standard_date("DATE_MYSQL");
        $res = $this->livecast->update_info($liveid,$data);
        if ($res === DB_OPERATION_OK) {
            $this->_success();
        } else {
            $this->_error($res, $this->lang->line('bp_operation_fail_hint'),404);
        }
    }

    /**
     * 录制结束异步接口
     */
    public function endrecord()
    {
        log_debug('[livecast]'.json_encode($_REQUEST));
        $_POST = $_REQUEST;
        $this->_validation([
            ["name",'user id','trim|numeric|required'],
            ["replay_url",'replay_url id','trim|required'],
        ]);
        $liveid = (int) $this->input->get_post('name');
        $data['status'] = Livecast_model::STATUS_END_RECORD;
        $data['replay_url'] = $this->input->get_post('replay_url');
        $res = $this->livecast->update_info($liveid,$data);
        if ($res === DB_OPERATION_OK) {
            $this->_success();
        } else {
            $this->_error($res, $this->lang->line('bp_operation_fail_hint'),404);
        }
    }
    public function check()
    {
        if(!$this->_check_param()){
            $this->_error(bp_operation_verify_fail,$this->lang->line('bp_operation_verify_fail_hint'),403);
            return;
        }
        $liveid = (int) $this->input->get_post('name');
        $data['status'] = Livecast_model::STATUS_STARTING;
        $data['start_time'] = standard_date("DATE_MYSQL");
        $res = $this->livecast->update_info($liveid,$data);
        if ($res === DB_OPERATION_OK) {
            $this->_success();
        } else {
            $this->_error($res, $this->lang->line('bp_operation_fail_hint'),404);
        }
    }
    public function info($liveid)
    {
        $liveid = (int) $liveid;
        $res = $this->livecast->info($liveid);
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
        $this->input->get_post('userid') && $filter[TBL_LIVECAST.'.author'] = (int) $this->input->get_post('userid');
        $this->input->get_post('status') && $filter[TBL_LIVECAST.'.status'] = $this->input->get_post('status');  //status 支持数组,数组多选
        $res = $this->livecast->get_list($filter,$offset, $limit);
        // res is ordered by order asc
        if (empty($res['list'])) {
            log_message('error', 'live list error:' . json_encode($res));
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        } else {
            $this->_result(bp_operation_ok, $res);
        }


    }

    private function cat_camp($a,$b){

        return ((int)$a['order'] - (int)$b['order']) ;
    }
}
