<?php

class Stat extends Admin_Controller
{

    public $responsible_userid;  //负责的设计师,主要给助理用

    function __construct()
    {
        parent::__construct();
//		$this->output->enable_profiler(TRUE);

        //控制自己的权限
        if (!$this->_need_stat_admin_role()) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        $this->_data['responsible_userid'] = $this->responsible_userid;

        $this->load->model('order_model');

    }

    /*
    *	默认检查用户是否有管理员权限，如果没有，直接返回相应的错误处理方法
    */
    private function _need_stat_admin_role()
    {
        if ($this->_data['usertype'] == USERTYPE_DESIGNER) {
            $this->responsible_userid = $this->userid;   //self
            return true;
        }
        if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_STAT)) {
            $responsible_userid = $this->user_model->get_responsible_userid($this->userid);
            if ($responsible_userid) {
                $this->responsible_userid = $responsible_userid;
                return true;
            } else {
//                $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'), true, $this->config->item('status_code')['forbidden']);
                return false;
            }
        }
        if ($this->_data['usertype'] == USERTYPE_ADMIN && $this->has_admin_role(ADMIN_ROLE_STAT)) {
            $this->responsible_userid = '';   //空,选择所有的
            return true;
        }
    }


    public function index()
    {
        $this->setOutputTpl('admin/stat/index');
        $this->_result(bp_operation_ok, $this->_data);
    }

    /**
     * @param $data_model  product,collection
     * @param $scope  pv,uv,sales,revenue,summary
     */
    public function api($data_model, $scope)
    {
        $this->load->model('stat_model');

//        $this->stat_model->set_params(array('data_model' => $data_model, 'userid' => $this->userid));
        $this->stat_model->set_params(array('data_model' => $data_model, 'userid' => 9));

        $arr = array(
            'pv' => 'get_pv',
            'uv' => 'get_uv',
            'sales' => 'get_sales',
            'revenue' => 'get_revenue',
            'summary' => 'get_summary',
        );

        if (!array_key_exists($scope, $arr)) {
            $this->_error(bp_operation_verify_fail, $this->lang->line('bp_operation_verify_fail_hint'));
        }
        $method = $arr[$scope];
        $_limit = $this->input->get_post('lm') ? $this->input->get_post('lm') : 10;
        $_offset = $this->input->get_post('of') ? $this->input->get_post('of') : 0;
        $_period = $this->input->get_post('p') ? $this->input->get_post('p') : 'day';
        $filter = array(
            'start' => $this->input->get_post('s') ? human_to_unix($this->input->get_post('s')) : strtotime('-7 days'),
            'end' => $this->input->get_post('e') ? human_to_unix($this->input->get_post('e')) : strtotime('+1 minute'),
        );
        $res = $this->stat_model->$method($_offset, $_limit, $_period, $filter);

        $this->setOutputType('json');
        $this->_result(bp_operation_ok, $res);
    }

    public function collection_count(){
        $collection_id = (int)$this->input->get_post('collection_id');
        $this->_validation(array(
            array('collection_id', '专辑ID', 'trim|required'),
        ));
        $this->load->model('collection_model');
        $res = $this->collection_model->collection_count($collection_id);
        $total = 0;
        $returnresult=array();
        if($res) {
            foreach ($res as $key=>$value) {
                $value['count'] = $value['count'] * PROPROTION;
                $total += $value['count'];
                $result[$value['product_id']]['product_id'] = $value['product_id'];
                $result[$value['product_id']]['product_title'] = $value['product_title'];
                $result[$value['product_id']]['product_moeny'][] = array(
                    'count'=> $value['count'],
                    'weeks'=> $value['weeks'],
                    'first_day'=> $value['first_day'],
                    'last_day' => $value['last_day'],
                );
            }
        }

        foreach( $result as &$item){
            $returnresult[] = $item;
        }
        $this->_result(bp_operation_ok, array('total' => $total,'list'=>$returnresult));
    }
}




