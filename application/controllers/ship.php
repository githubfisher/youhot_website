<?php

/**
 * Class Product
 * @method
 */
class Ship extends User_Controller
{
    const IMGURL = 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/idcard/NZ27Is93hIm6e7c70e299b99c444766537e88c6f2e0a.png';
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ship_model');
    }

    /**
     * add coupon
     * @return json
     */


    public function create()
    {
        $this->_need_login(true);
	$total = $this->ship_model->getSum($this->userid);
	if ($total>=10) {
	    exit(json_encode(['res' => 99, 'hits' => '只能创建10条地址']));
	}

        $this->form_validation->set_rules('receiver', '收件人', 'trim|required|min_length[1]|max_length[32]');
        $this->form_validation->set_rules('phone_num', '手机号', 'trim|required|min_length[2]|max_length[32]|alpha_dash');
        $this->form_validation->set_rules('district', '所在区', 'trim|required');
        $this->form_validation->set_rules('address', '收件地址', 'trim|required|min_length[2]');
        $this->form_validation->set_rules('default', '是否默认', 'trim|numeric');

        $return_type = $this->input->get_post(RETURN_TYPE);

        if ($this->form_validation->run() == FALSE) {

            $hint = validation_errors(' ', ' ');
            $this->_error(bp_operation_verify_fail, $hint);
        }


        $filter_array = array('receiver', 'phone_num', 'district', 'address', 'default', 'idcard','fullname','idimg1','idimg2');
        $data = filter_data($this->input->post(), $filter_array);
        $data['author'] = $this->userid;
	$data['idimg1'] = empty($data['idimg1']) ? self::IMGURL : $data['idimg1'];
	$data['idimg2'] = empty($data['idimg2']) ? self::IMGURL : $data['idimg2'];
	
        $res = $this->ship_model->create($data);
        if ($res == DB_OPERATION_FAIL) {
            log_message('error', 'create product wrong:' . json_encode($data));
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }
//        $res = filter_data($res,$this->config->item('json_filter_collection_detail'));
        $this->_result(bp_operation_ok, $res);


    }
    public function update()
    {
        $this->_need_login(TRUE);
        $ship_id = $this->_get_ship_id();
        $this->_check_permission_and_return($ship_id);

        $filter_array = array('receiver', 'phone_num', 'district', 'address', 'default', 'idcard','fullname','idimg1','idimg2');
        $up_data = filter_data($this->input->post(), $filter_array);

        $res = $this->ship_model->update_info($ship_id, $up_data,$this->userid);

        $this->_deal_res($res);

    }

    public function detail($ship_id=null)
    {
        if(empty($ship_id)){
            $ship_id = $this->_get_ship_id();
        }

        //@todo check permission

        $res = $this->ship_model->info($ship_id);
        if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        } else {
//            $res = filter_data($res,$this->config->item('json_filter_collection_detail'));
            $this->_result(bp_operation_ok, $res);
        }
    }

    /**
     * Product list of what I have published
     * @param username
     * @return {
     *
     *
     * }
     */


    public function get_list()
    {
	$this->_need_login(true);
        $_username = $this->userid;
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        if (empty($order)) {
            $order = $this->input->get_post('od');
        }
        $permit_orders = array('create_time');
        if (!in_array($order, $permit_orders)) {
            $order = null;
        }
        $filter = array();
        $res = $this->ship_model->get_list($_username, $offset, $limit, $filter, (string)$order);
        if (!empty($res['list'])) {
            $res['count'] = count($res['list']);
	    foreach ($res['list'] as $k => $v) {
		if (empty($v['idimg1'])) $res['list'][$k]['idimg1'] = self::IMGURL;
		if (empty($v['idimg2'])) $res['list'][$k]['idimg2'] = self::IMGURL;
	    }
            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No order list:' . json_encode($res));
            $this->_error(bp_operation_fail, $this->lang->line('hint_list_get_fail'));
        }
    }


    private function _has_edit_permission($ship_id)
    {
        if ($this->is_admin) return TRUE;
        $res = $this->ship_model->get_owner($ship_id);
        $owner = element('author', $res, null);
        if ($owner == $this->userid) {
            return true;
        }
        return false;
    }

    private function _check_permission_and_return($ship_id)
    {
        if (!$this->_has_edit_permission($ship_id)) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        return true;
    }


    private function _get_ship_id()
    {
        $ship_id = $this->input->post('ship_id');
        if (empty($ship_id)) {
            log_message('error', 'No shp id:' . json_encode($ship_id));
            $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'));
        }
        return $ship_id;
    }

    public function delete()
    {
        $this->_need_login(TRUE);
        $ship_id = $this->_get_ship_id();
        $this->_check_permission_and_return($ship_id);
        $res = $this->ship_model->delete($ship_id);
        $this->_deal_res($res);
    }

}
