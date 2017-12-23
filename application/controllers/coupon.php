<?php
class Coupon extends User_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('coupon_model', 'coupon');
    }

    public function myList()
    {
	$this->_need_login(true);

	$list = $this->getCouponList();	
	if (is_array($list) && count($list)) {
	    $this->_result(bp_operation_ok, ['list' => $list]);
	} else {
	    $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
	}
    }

    public function getCouponList()
    {
	$this->_need_login(true);
	$list = $this->coupon->myList($this->userid, $this->_un);
	if (is_array($list) && count($list)) {
	    foreach ($list as $k => $v) {
		if (($v['use_at'] <= time()) && (time() <= $v['use_end']) && empty($v['used_at'])) {
		    $list[$k]['is_available'] = 1;
		    unset($list[$k]);
		    array_unshift($list, $v);
		} else {
		   $list[$k]['is_available'] = 0;
		}	
	    }
	}

	return $list;
    }

    // get coupon function 1
    public function addCoupon()
    {
	$this->_need_login(true);
	$code = $this->input->get_post('code');
	if (strlen($code) == 12) {
	    $salt = substr($code, 8);
	    $code = substr($code,0,8);
	    $res = $this->coupon->addCoupon($this->userid, $code, $salt);
	    if ($res) {
		$id = $this->coupon->getCouponId($code, $salt);
		if (isset($id[0]['coupon_id'])) {
		    $this->coupon->update_sum($id[0]['coupon_id'], 'geted_sum', 1);
		}	
		$this->_result(bp_operation_ok, []);
	    } else {
		$this->_error(BP_OPERATION_LIST_EMPTY, '添加失败，请重试');
	    }
	} else {
	    $this->_error(BP_OPERATION_LIST_EMPTY, '优惠券码有误，请重试');
	}
    }

    // get coupon function 2 for H5

    public function showCoupon()
    {
	$id = $this->input->get_post('coupon_id');
	$detail = $this->coupon->getDetail($id);
	if ($detail) {
	    $res['res'] = 0;
	    $res['hits'] = '成功';
	    $dt = filter_data($detail, $this->config->item('json_filter_coupon_detail'));
	    if (empty($dt['category'])) {
		$dt['category'] = '不限品类';
	    }
	    if (empty($dt['store'])) {
                $dt['store'] = '不限商城';
            }
	    if (empty($dt['use_at'])) {
                $dt['use_at'] = '不限期';
            } else {
		$dt['use_at'] = date('Y-m-d', $dt['use_at']);
	    }
	    if (empty($dt['use_end'])) {
                $dt['use_end'] = '不限期';
            } else {
                $dt['use_end'] = date('Y-m-d', $dt['use_end']);
            }
	    for ($i=0;$i<$detail['get_limit'];$i++) {
		$res['coupons'][] = $dt;
	    }
	    $res['description'] = $dt['description'];
	} else {
	    $res['res'] = 1;
            $res['hits'] = '获取优惠券信息失败，请重试';	
	}

	if ($this->input->get_post('rt') == 'jsonp') {	
    	    $callback = $this->input->get_post('callback');
            exit($callback."(".json_encode($res).");");	
	}	
	exit(json_encode($res));
	
    }

    public function getCoupon()
    {
	$id = $this->input->get_post('coupon_id');
	$mobile = $this->input->get_post('mobile');
	$rt = $this->input->get_post('rt');

  	$detail = $this->coupon->getDetail($id);
	if ($detail['time_limit'] > 0) {
	    $times = $this->coupon->getTimes($id, $mobile); //only mobile
	    if (($times + $detail['get_limit']) > ($detail['time_limit'] * $detail['get_limit'])) {
		$this->result('error', $rt, '对不起,您已经领取过该优惠券,请勿重复领取!');
	    }
	}	
	if (!empty($detail['mobile_limit'])) {
	    $mobiles = explode(',', $detail['mobile_limit']);
	    $values = array_values($mobiles);
//print_r($values);die;
	    if (!in_array($mobile, $values)) {
		$this->result('error', $rt, '很抱歉，您不在邀请范围内，感谢您的关注！');
	    }
	}
	$res = $this->coupon->getCouponByMobile($id, $mobile, $detail['get_limit']);	
	if ($res) {
	    $this->coupon->update_sum($id, 'geted_sum', $detail['get_limit']);
	    $this->result('ok', $rt, '领取成功！');
	} else {
	    $this->result('error', $rt, '领取失败，请重试！');
	}
    }

    public function result($type, $rt, $info, $result = array())
    {
	if ($type == 'error') {
	    $res = array(
                'res' => 1,
                'hits' => $info, 
            );
	} elseif ($type == 'success') {
	    $res = array(
		'res' => 0,
		'hits' => $info
	    );
	}
	$res = array_merge($result, $res);

	if ($rt == 'jsonp') {
	    $callback = $this->input->get_post('callback');
            exit($callback."(".json_encode($res).");");
        } else {
	    exit(json_encode($res));
        }
    }

    public function showc()
    {
	$this->_data['coupon_id'] = $this->input->get_post('id');
	$this->_data['signature'] = getSignPackage();
	$this->layout->set_layout('layout/h5');
        $this->setOutputTpl('coupon/show');
        $this->_result(bp_operation_ok,$this->_data);  	
    }

    public function FriendCouponInfo()
    {
	$list = $this->coupon->getCouponList(COUPON_TYPE_NEWER);
	$rt = $this->input->get_post('rt');
	if ($list) {
	    $this->result('success', $rt, 'success', ['value' => $list[0]['value'], 'deadline' => date('Y年m月d日', $list[0]['use_end'])]);
	} else {
	    $this->result('success', $rt, 'success', ['value' => 50, 'deadline' => '2018年1月31日']);
	    //$this->result('error', $rt, '没有可用的好友券');
	}		
    }

    public function showFriendCoupon()
    {
	$this->_data['signature'] = getSignPackage();
	$this->layout->set_layout('layout/h5');
        $this->setOutputTpl('h5/friend_coupon');
        $this->_result(bp_operation_ok,$this->_data);
    }
}
