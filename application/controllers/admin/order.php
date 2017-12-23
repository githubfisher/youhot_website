<?php

class Order extends Admin_Controller
{

    public $responsible_userid;  //负责的设计师,主要给助理用

    function __construct()
    {
        parent::__construct();
//		$this->output->enable_profiler(TRUE);

        //控制自己的权限
        if (!$this->_need_order_admin_role()) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        $this->_data['responsible_userid'] = $this->responsible_userid;

        $this->load->model('order_model');

    }

    /*
    *	默认检查用户是否有管理员权限，如果没有，直接返回相应的错误处理方法
    */
    private function _need_order_admin_role()
    {
        if ($this->_data['usertype'] == USERTYPE_DESIGNER) {
            $this->responsible_userid = $this->userid;   //self
            return true;
        }
        if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_ORDER)) {
            $responsible_userid = $this->user_model->get_responsible_userid($this->userid);
            if ($responsible_userid) {
                $this->responsible_userid = $responsible_userid;
                return true;
            } else {
//                $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'), true, $this->config->item('status_code')['forbidden']);
                return false;
            }
        }
        if ($this->_data['usertype'] == USERTYPE_ADMIN && $this->has_admin_role(ADMIN_ROLE_ORDER)) {
            $this->responsible_userid = '';   //空,选择所有的
            return true;
        }
    }


    public function index()
    {
        $this->order_list();
    }

    public function test()
    {
        $this->setOutputTpl('admin/order/test');
        $this->_result(bp_operation_ok);
    }

    public function edit($order_id = null)
    {

        $this->_data ['res'] = bp_operation_ok;
        $this->_data['order_id'] = $order_id;


        if (empty($order_id)) {
            $order_id = $this->_get_order_id();
        }

        $res = $this->order_model->detail($order_id);
        $this->_data['order'] = $res;

        $product_id = element('product_id',$res,0);
        $pdata = [];
        if($product_id > 0){
            $this->load->model('product_model');
            $pdata = $this->product_model->info($product_id);

        }

        //$this->_data['size_list'] = element('available_size',$pdata,[]);
        //$this->_data['color_list'] = element('available_color',$pdata,[]);

        $this->_data['orderid'] = $order_id;
        $this->_data['deliver'] = array();
        foreach ($res as $k => $v) {
            if (($v['pid'] == 0 ) && strlen($v['overseas'])) {
                $json = '['.$v['overseas'].']';
                $arr = json_decode($json, true);
                if (is_array($arr) && count($arr)) {
                    $this->_data['deliver'] = $arr;
		    $this->_data['overseas'] = str_replace('"','@quot;', $v['overseas']);
                }
                //$res[$k]['overseas'] = str_replace('"','@quot;', $res[$k]['overseas']);
                break;
            }
        }

        $template = 'admin/order/edit';
        $this->template($template, $this->_data);
    }

    public function detail($order_id = null)
    {
        if (empty($order_id)) {
            $order_id = $this->_get_order_id();
        }

	//$this->_check_permission_and_return($order_id);

        $res = $this->order_model->detail($order_id);
	//echo '<pre>'; print_r($res);die;
        $this->_data['orderid'] = $order_id;
        $this->_data['deliver'] = array();
	$this->_data['overseas'] = '';
	$this->_data['shipping_info'] = '';
        foreach ($res as $k => $v) {
            if ($v['pid'] == 0 ) {
		if (strlen($v['overseas'])) {
               	    $json = '['.$v['overseas'].']';
                    $arr = json_decode($json, true);
                    if (is_array($arr) && count($arr)) {
                    	$this->_data['deliver'] = $arr;
		    	$this->_data['overseas'] = str_replace('"','@quot;', $v['overseas']);
                    }
                    //$this->_data['overseas'] = str_replace('"','@quot;', $v['overseas']);
		}
		if (strlen($v['shipping_info']) > 1) {
		    $json = json_decode($v['shipping_info'], true);
		    if (is_array($json) && count($json)) {
			$this->_data['shipping_info'] = $json;
		    }
		}
                break;
            }
        }

        if (!$res) {
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        }

	//$res = filter_data($res, $this->config->item('json_filter_order_detail'));

        $this->setOutputTpl('admin/order/detail');
        $this->_data['order'] = $res;
        $this->_result(bp_operation_ok);

    }


    private function _get_order_id()
    {
        $order_id = $this->input->get_post('order_id');
        if (empty($order_id)) {
            log_message('error', 'No order id:' . json_encode($order_id));
            $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'), true);
        }
        return $order_id;
    }

    public function order_list()
    {
        $template = 'admin/order/list';
        $this->template($template, $this->_data);
        return;
    }

    /**
     * 统计
     * @return mixed
     */
    public function stat()
    {
        $start_dt = ($this->input->get_post('s') ? $this->input->get_post('s') : standard_date('DATE_YMD', strtotime('-1 week'))) . ' 00:00:00';
        $end_dt = ($this->input->get_post('e') ? $this->input->get_post('e') : standard_date('DATE_YMD')) . ' 23:59:59';

        $type = $this->input->get_post('ro') ? $this->input->get_post('ro') : 'seller';

        $res = $this->order_model->list_deals_in_range($start_dt, $end_dt, $this->responsible_userid, $type);
        $this->_data['stat_data'] = $this->_organize_stat_original_data(substr($start_dt, 0, 10), substr($end_dt, 0, 10), $res);
        $this->setOutputType('html');
        $this->setOutputTpl('admin/stat/order');
//        var_dump($this->_data['stat_data']);
        $this->_result(bp_operation_ok,$this->_data);
    }

    private function _organize_stat_original_data($start_date, $end_date, $ori_data)
    {
//        echo $start_date;
//        echo $end_date;
        $data = array();

        $start_date = new DateTime($start_date);
        //fill out day array;
        $i = 0;
        $ph_day_data = array();
        $ph_month_data = array();
        while ($i < 20) {

            $date_str = $start_date->format('Y-m-d');
            $data['day']['columns'][] = $date_str;
            $ph_day_data[] = 0;  //init day data
            if ($date_str == $end_date) break;
            $start_date->add(DateInterval::createFromDateString('1 day'));
            $i++;

        }
        //fill out month array;
        $i = 0;
        while ($i < 2) {
            $date_str = $start_date->format('Y-m');
            $data['month']['columns'][] = $date_str;
            $ph_month_data[] = 0;  //init day data
            if ($date_str == substr($end_date, 0, 7)) break;
            $start_date->add(DateInterval::createFromDateString('1 month'));

            $i++;
        }
        $data['day']['series']  = array(array('name' => '未完成', 'data' => $ph_day_data), array('name' => '完成', 'data' => $ph_day_data));
        $data['month']['series'] = array(array('name' => '未完成', 'data' => $ph_month_data), array('name' => '完成', 'data' => $ph_month_data));
        unset($ph_day_data);
        unset($ph_month_data);
//        var_dump($data);
//
//        $data = array(
//            'day'=>array(
//                'columns'=>array('09-01','09-02')
//                ,'series'=>array(array("name"=>'init','data'=>array(2,3,4,54)),array("name"=>'complete','data'=>array(2,3,4,54)))
//            ),
//            'month'=>array(
//                'columns'=>array('2015-09','2015-10')
//            ,'series'=>array(array("name"=>'init','data'=>array(2,3,4,54)),array("name"=>'complete','data'=>array(2,3,4,54)))
//            ),
//        );
        $day_data = &$data['day'];
        $month_data = &$data['month'];
        foreach ($ori_data as $key => $row) {
            $day = substr($row['create_time'], 0, 10);
            $month = substr($row['create_time'], 0, 7);

            $day_index = array_search($day, $day_data['columns']);
            if ($day_index === false) continue;
            $_status = ($row['status'] == ORDER_STATUS_INIT) ? 0 : 1;
            $day_data['series'][$_status]['data'][$day_index]++;

            $month_index = array_search($month, $month_data['columns']);
            if ($month_index === false) {
                continue;
            }
            $month_data['series'][$_status]['data'][$month_index]++;
        }
//        var_dump($data);
        return $data;
    }

    public function update_deliver_detail()
    {
        $order_id = $this->input->get_post('order_id');
        $str = $this->input->get_post('str');
        $id = $this->input->get_post('id');
        $overseas = $this->input->get_post('overseas');

        $data = array();
        if (is_numeric($id)) {
            $res = false;
            if (isset($overseas)) {
                $overseas = str_replace('@quot;', '"', $overseas);
                $json = '['.$overseas.']';
                $arr = json_decode($json, true);
                if (is_array($arr) && count($arr)) {
                    $arr[$id] = json_decode($str, true);
                    $json = json_encode($arr);
                    $data['overseas'] = rtrim(ltrim($json,'['),']');
                    $res = $this->order_model->update_deliver_detail($order_id, $data);
                }
            }
        } else {
            $data['overseas'] = str_replace('@quot;', '"', $str);
            $res = $this->order_model->update_deliver_detail($order_id, $data);
        }

        if ($res == DB_OPERATION_OK) {
            exit(json_encode(array('res'=>0)));
        }
        exit(json_encode(array('res'=>1,'hint'=>'操作失败，请重试')));
    }

}
