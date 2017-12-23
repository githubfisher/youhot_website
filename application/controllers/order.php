<?php

class Order extends User_Controller
{

    const PAYINFO_PREPAY_SUFFIX = 'p';
    const PAYINFO_LASTPAY_SUFFIX = 'l';
    //Cautions: Don't repeat with constants such as ORDER_STATUS_* defined in constants.php
    const ORDER_FILTER_PREORDER = 101;
    const ORDER_FILTER_SHIP = 303;
    const ORDER_FILTER_NEED_TO_DO = 50;
    const ORDER_FILTER_ALL = 60;

    public function __construct()
    {

        parent::__construct();
        $this->load->model('order_model', 'order');

    }

    public function index()
    {
        $this->detail();
    }

    public function  create()
    {
        $this->_need_login(TRUE);

        //save_time   current_time
        //publish_time null

        $this->form_validation->set_rules('order_type', 'order type', 'trim|integer');
        $this->form_validation->set_rules('product_color', 'color', 'trim|required');
        $this->form_validation->set_rules('product_size', 'size', 'trim|integer');
        $this->form_validation->set_rules('payment', 'payment', 'trim|required');
        $this->form_validation->set_rules('referer', 'referer', 'trim|integer');

        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $data = array();
        //order_id: product_id_userid_time_random?

        $data['create_time'] = standard_date('DATE_MYSQL');

        $data['buyer_userid'] = $this->userid;
        $data['status'] = ORDER_STATUS_INIT;

//        $filter_rule = array(
//        'order_type'
//        , 'seller_userid'
//        , 'product_id'
//        , 'product_count'
//        , 'product_color'
//
//        , 'product_size'
//        , 'ship_info_id'
//        , 'freight'
//        , 'freight_id'
//        , 'coupon_code'
//        , 'payment'
//    );
//        $filter_data = filter_data($this->input->post(),$filter_rule);
//
//        $data = array_merge($data,$filter_data);

        $data['type'] = $this->input->post('order_type');  //可以认为等同于sell_type
//        $data['seller_userid'] = $this->input->post('seller_userid');
//        $data['buyer_userid'] = $this->input->post('buyer_userid');
        $data['product_id'] = $this->input->post('product_id');
        $data['product_count'] = $this->input->post('product_count') ? $this->input->post('product_count') : 1;
        $data['product_color'] = $this->input->post('product_color');
        $data['product_size'] = $this->input->post('product_size');
        $data['ship_info_id'] = $this->input->post('ship_info_id');
        $data['freight'] = $this->input->post('freight');
        $data['freight_id'] = $this->input->post('freight_id');
        $data['pre_pay_coupon_code'] = $this->input->post('coupon_code');  //验证时候需要把用total_fee+freight=coupon_code+need_pay的钱拿过来check,以免客户端数据的伪造.
        $data['pre_payment'] = $this->input->post('payment');
        $data['referer'] = $this->input->post('referer');
        $data['money'] = $this->input->post('money');


        $this->load->model('user_model');
//        $_order_id = time() . $data['product_id'] . $this->userid . rand(1000, 9999);
        $_order_id = time() . rand(0, 9) . $this->userid . rand(10, 99);
        $data['order_id'] = $_order_id;

        $data = $this->_pre_check_and_supply_data($data);  //pre_check
        unset($data['money']);
        if (!$this->order->add_deal($data)) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

        $return_data = array('out_trade_no' => $_order_id,
//用paypal支付时候notifyurl 没做处理
            'order_id' => $_order_id, "notify_url" => $this->order->get_notify_url(PAY_TYPE_PREPAY, $data['pre_payment']));
        $this->_result(bp_operation_ok, $return_data);

    }


    public function  create_last_pay()
    {
        $this->_need_login(TRUE);

        //save_time   current_time
        //publish_time null
        $order_id = $this->_get_order_id();
        $data = array();

        $data['ship_info_id'] = (int)$this->input->post('ship_info_id');
        $data['freight'] = $this->input->post('freight');
        $data['freight_id'] = (int)$this->input->post('freight_id');
        $data['last_pay_coupon_code'] = $this->input->post('coupon_code');
        $data['memo'] = $this->input->post('memo');   //买家留言
        $data['last_payment'] = $this->input->post('payment');
        $data['money'] = $this->input->post('money');

        $data = $this->_last_pay_pre_check($data);  //pre_check
        unset($data['money']);
        if (!$this->order->update_deal($order_id, $data)) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

        $return_data = array('out_trade_no' => $order_id . 'P', 'order_id' => $order_id . 'P', "notify_url" => $this->order->get_notify_url(PAY_TYPE_LASTPAY, $data['last_payment']));
        $this->_result(bp_operation_ok, $return_data);

    }

    public function directpay()
    {
        //prepay
        //lastpay
        $this->_need_login(TRUE);

        //save_time   current_time
        //publish_time null

        $this->form_validation->set_rules('product_color', 'color', 'trim|required');
        $this->form_validation->set_rules('product_size', 'size', 'trim|integer');
        $this->form_validation->set_rules('payment', 'payment', 'trim|required');
        $this->form_validation->set_rules('referer', 'referer', 'trim|integer');

        //@todo maybe other rules such as freight must provide

        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $data = array();
        //order_id: product_id_userid_time_random?

        $data['create_time'] = standard_date('DATE_MYSQL');

        $data['buyer_userid'] = $this->userid;
        $data['status'] = ORDER_STATUS_INIT;

        $data['product_id'] = $this->input->post('product_id');
        $data['product_count'] = $this->input->post('product_count') ? $this->input->post('product_count') : 1;
        $data['product_color'] = $this->input->post('product_color');
        $data['product_size'] = $this->input->post('product_size');
        $data['ship_info_id'] = $this->input->post('ship_info_id');
        $data['freight'] = $this->input->post('freight');
        $data['freight_id'] = $this->input->post('freight_id');
//        $data['pre_pay_coupon_code'] = $this->input->post('coupon_code');  //验证时候需要把用total_fee+freight=coupon_code+need_pay的钱拿过来check,以免客户端数据的伪造.
//        $data['pre_payment'] = $this->input->post('payment');
        $data['referer'] = $this->input->post('referer');
        $data['money'] = $this->input->post('money');

        $data['ship_info_id'] = (int)$this->input->post('ship_info_id');
        $data['last_pay_coupon_code'] = $this->input->post('coupon_code');
        $data['memo'] = $this->input->post('memo');   //买家留言
        $data['last_payment'] = $this->input->post('payment');

        $_order_id = time() . rand(0, 9) . $this->userid . rand(10, 99);
        $data['order_id'] = $_order_id;

        $data = $this->_direct_pay_check($data);
        //需要把prepay信息设置好
        unset($data['money']);
        if (!$this->order->add_deal($data)) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

//        $return_data = array('out_trade_no' => $_order_id,
//
//            'order_id' => $_order_id, "notify_url" => $this->order->get_notify_url(PAY_TYPE_PREPAY, $data['pre_payment']));
//        $this->_result(bp_operation_ok, $return_data);


        $return_data = array('out_trade_no' => $_order_id . 'P', 'order_id' => $_order_id . 'P', "notify_url" => $this->order->get_notify_url(PAY_TYPE_LASTPAY, $data['last_payment']));
        $this->_result(bp_operation_ok, $return_data);

    }
    /**
     * 创建直接付款订单(多商品)
     */
    public function directpaymore()
    {
        $this->_need_login(TRUE);
        $products = json_decode($this->input->get_post('ps'), true);
        $cartIds = json_decode($this->input->get_post('cartid'), true); // 删除购物车中的对应商品 by  fisher at 2017-04-27
	$directPay = $this->input->get_post('directpay');
	if (empty($directPay)) {
       	    if (!is_array($cartIds)) {
            	$this->_error(bp_operation_fail, '缺少参数');
            }
	}
        $is_more = false;
        $idList = array();
        if( ! is_array($products) ){
            $this->_error(bp_operation_fail, '缺少参数');
        }
        $order_pid = 0;
        if( count($products)>1 ){
            $is_more = true;
            $order_pid = time() . rand(0, 9) . $this->userid . rand(10, 99);
            $idList[] = $order_pid;
        }
	$freight = empty($this->input->get_post('freight')) ? 0 : $this->input->get_post('freight');
	$tax = empty($this->input->get_post('tax')) ? 0 : $this->input->get_post('tax');
	$coupon_value = empty($this->input->get_post('coupon_value')) ? 0 : $this->input->get_post('coupon_value');
        $promotion_value = empty($this->input->get_post('promotion_value')) ? 0 : $this->input->get_post('promotion_value');
        $product_count = 0;
        $products_price = 0;
        $products_freight = 0;
        $order_sum = 0;
        foreach($products AS $product){
            do{
                $_order_id = time() . rand(0, 9) . $this->userid . rand(10, 99);
                if( ! in_array($_order_id, $idList) ){
                    break;
                }
            }while(1);
            $idList[] = $_order_id;
	    $referer = isset($product['referer']) ? $product['referer'] : 0;
            $referer = $referer == $this->userid ? 0 : $referer;
            //此ci版本不支持自定义数组，重构POST
            $_POST['product_color'] = isset($product['product_color'])?$product['product_color']:'';
            $_POST['product_size']  = isset($product['product_size'])?$product['product_size']:'';
            $_POST['payment']       = $this->input->get_post('payment');
            $_POST['referer']       = $referer;
            $this->form_validation->set_rules('payment', 'payment', 'trim|required');
            $this->form_validation->set_rules('referer', 'referer', 'trim|integer');
            if ($this->form_validation->run() == FALSE) {
                $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
            }
            $data = array();
            if( $order_pid!=0 ){
                $data['pid'] = $order_pid;
            }
            $data['create_time'] = standard_date('DATE_MYSQL');
            $data['buyer_userid'] = $this->userid;
            $data['status'] = ORDER_STATUS_LAST_PAY_START;
            if( isset($product['product_id']) ){
                $data['product_id'] = $product['product_id'];
            }else{
                $this->_error(bp_operation_fail, '商品不存在');
            }
            $data['product_count'] = isset($product['product_count']) ? $product['product_count'] : 1;
            if( isset($product['product_color']) ){
                $data['product_color'] = $product['product_color'];
            }
            if( isset($product['product_size']) ){
                $data['product_size']  = $product['product_size'];
            }
	    $data['tax'] = empty($product['tax']) ? 0 : $product['tax'];
            $data['referer'] = $referer;
            if( isset($product['money']) ){
                $data['money'] = $product['money'];
            }else{
                $this->_error(bp_operation_fail, '商品缺少价格');
            }
            $data['last_pay_coupon_code'] = '';
            if( $is_more ){
            }else{
                $data['ship_info_id'] = (int)$this->input->post('ship_info_id');
                $data['memo']         = $this->input->post('memo');   //买家留言
                $data['last_payment'] = $this->input->post('payment');
		$data['freight'] = $freight;
                $data['shipping_info'] = empty($this->input->get_post('shipping_info')) ? null : $this->input->get_post('shipping_info');
                $data['tax'] = $tax;
		$data['last_pay_coupon_value'] = $coupon_value;
		$data['last_pay_coupon_code'] = empty($this->input->get_post('coupon_code')) ? '' : $this->input->get_post('coupon_code');
		$data['promotion_code'] = empty($this->input->get_post('promotion_code')) ? '' : $this->input->get_post('promotion_code');
		$data['promotion_value'] = $promotion_value;
            }
            $data['order_id'] = $_order_id;
            $data = $this->_direct_pay_check($data);
	    $products_price = bcadd($products_price, $data['product_price']*$data['product_count'], 2); //订单总价=商品单价*商品数量 
	    // weixin prepay Not more
	    if ($this->input->get_post('payment') == 'weixin') {
                $wx = $this->getPrepayMsg($_order_id, ($products_price + $freight + $tax - $coupon_value - $promotion_value));
		$data['wx_prepay_id'] = $wx['prepay_id'];
            }
            unset($data['money']);
            if (!$this->order->add_deal($data)) {
              $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
            }
            $order_sum++;
            $product_count += $data['product_count'];
        }
        //父订单
        if( $is_more ){
            $data2 = array();
            $data2['create_time']  = standard_date('DATE_MYSQL');
            $data2['buyer_userid'] = $this->userid;
            $data2['status']       = ORDER_STATUS_LAST_PAY_START;
            $data2['product_id']    = 0;
            $data2['product_count'] = $product_count;
            $data2['last_pay_coupon_code'] = empty($this->input->get_post('coupon_code')) ? '' : $this->input->get_post('coupon_code');
            $data2['ship_info_id']         = (int)$this->input->post('ship_info_id'); //address
            $data2['memo']                 = $this->input->post('memo');   //买家留言
            $data2['last_payment']         = $this->input->post('payment');
            $data2['order_id'] = $order_pid;
            //$data2 = $this->_direct_pay_check_more($data2);
            $data2['product_price'] = $products_price;
            $data2['order_sum']     = $order_sum;
	    $data2['freight'] = $freight;
	    $data2['shipping_info'] = empty($this->input->get_post('shipping_info')) ? null : $this->input->get_post('shipping_info');
	    $data2['tax'] = $tax;
	    $data2['last_pay_coupon_value'] = $coupon_value;
	    $data2['promotion_code'] = empty($this->input->get_post('promotion_code')) ? '' : $this->input->get_post('promotion_code');
	    $data2['promotion_value'] = $promotion_value;
	    // weixin prepay more
            if ($this->input->get_post('payment') == 'weixin') {
                $wx = $this->getPrepayMsg($order_pid, ($products_price + $freight + $tax - $coupon_value - $promotion_value));
                $data2['wx_prepay_id'] = $wx['prepay_id'];
            }
            //需要把prepay信息设置好
            //unset($data2['money']);
            if (!$this->order->add_deal($data2)) {
                $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
            }
            $return_data = array('out_trade_no' => $order_pid . 'P', 'order_id' => $order_pid . 'P', "notify_url" => $this->order->get_notify_url(PAY_TYPE_LASTPAY, $data2['last_payment']));
        }else{
            $return_data = array('out_trade_no' => $_order_id . 'P', 'order_id' => $_order_id . 'P', "notify_url" => $this->order->get_notify_url(PAY_TYPE_LASTPAY, $data['last_payment']));
        }
	// weixin sign
	if ($this->input->get_post('payment') == 'weixin') {
            /*$wata = array(
                'appid'        => $this->config->item('weixin')['appid'],
                'mch_id'       => $this->config->item('weixin')['partnerid'],
                'out_trade_no' => $_order_id.'P',
		'body'         => 'youhot洋火在线购买',
		'total_fee'    => 1,//($products_price+$freight+$tax-$coupon_value-$promotion_value) * 100,
		'trade_type'   => 'APP',
		'notify_url'   => $this->order->get_notify_url(PAY_TYPE_LASTPAY, 'weixin'),
		'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
		'nonce_str'     => createNonceStr(),
            );
	    $wx = $this->unifiedOrder($wata);*/
	    $payData = array(
		'appid'        => $this->config->item('weixin')['appid'],
                'partnerid'       => $this->config->item('weixin')['partnerid'],
                'prepayid' => $wx['prepay_id'],
		'package' => 'Sign=WXPay',
		'noncestr'     => createNonceStr(),
		'timestamp' => time()
	    );
	    $payData['sign'] = $this->makeSign($payData);

	    $return_data = array_merge($return_data, $payData);
        }

	// 删除购物车中对应商品
	if (empty($directPay)) {
            $this->load->model('cart_model');
            $this->cart_model->delete_more($cartIds, $this->userid);
            $this->_result(bp_operation_ok, $return_data);
	} else {
	    $this->_result(bp_operation_ok, $return_data);
	}
    }

    private function getPrepayMsg($order_id, $price=0.01) {
            $wata = array(
                'appid'        => $this->config->item('weixin')['appid'],
                'mch_id'       => $this->config->item('weixin')['partnerid'],
                'out_trade_no' => $order_id.'P',
                'body'         => 'youhot洋火在线购买',
                'total_fee'    => $price * 100,//($products_price+$freight+$tax-$coupon_value-$promotion_value) * 100,
                'trade_type'   => 'APP',
                'notify_url'   => $this->order->get_notify_url(PAY_TYPE_LASTPAY, 'weixin'),
                'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
                'nonce_str'     => createNonceStr(),
            );
            $wx = $this->unifiedOrder($wata);

	    return $wx;
    }

    public function error_code( $code ){
	$errList = array(
	    'NOAUTH' => '商户未开通此接口权限',
	    'NOTENOUGH' => '用户帐号余额不足',
	    'ORDERNOTEXIST' => '订单号不存在',
	    'ORDERPAID' => '商户订单已支付，无需重复操作',
	    'ORDERCLOSED' => '当前订单已关闭，无法支付',
	    'SYSTEMERROR' => '系统错误!系统超时',
	    'APPID_NOT_EXIST' => '参数中缺少APPID',
	    'MCHID_NOT_EXIST' => '参数中缺少MCHID',
	    'APPID_MCHID_NOT_MATCH' => 'appid和mch_id不匹配',
	    'LACK_PARAMS' => '缺少必要的请求参数',
	    'OUT_TRADE_NO_USED' => '同一笔交易不能多次提交',
	    'SIGNERROR' => '参数签名结果不正确',
	    'XML_FORMAT_ERROR' => 'XML格式错误',
	    'REQUIRE_POST_METHOD' => '未使用post传递参数 ',
	    'POST_DATA_EMPTY' => 'post数据不能为空',
	    'NOT_UTF8' => '未使用指定编码格式',
	);
	if( array_key_exists( $code , $errList ) ){
	    return $errList[$code];
	}
    }

    public function unifiedOrder($params)
    {
	/*$params = array(
                'appid'        => $this->config->item('weixin')['appid'],
                'mch_id'       => $this->config->item('weixin')['partnerid'],
                'out_trade_no' => '100023456P',
                'body'         => 'youhotPayTest',
                'total_fee'    => 1,
                'trade_type'   => 'APP',
                'notify_url'   => $this->order->get_notify_url(PAY_TYPE_LASTPAY, 'weixin'),
                'spbill_create_ip' => $_SERVER['SERVER_ADDR'],
                'nonce_str'     => createNonceStr(),
            );*/
	$params['sign'] = $this->makeSign($params);
	$xml = $this->data_to_xml($params);
        $result = httPost('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
        $result = $this->xml_to_data($result);
	if(!empty($result['result_code']) && !empty($result['err_code'])){
	    exit(json_encode(['res'=>11,'msg'=>$result['err_code_des']]));
	}
	return $result;
    }

    private function xml_to_data($xml) {
	libxml_disable_entity_loader(true);
	$data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	return $data;
    }

    private function data_to_xml($params)
    {
	$xml = '<xml>';
	foreach ($params as $k => $v) {
	    $xml .= '<'.$k.'>'.$v.'</'.$k.'>';
	}
	$xml .= '</xml>';

	return $xml;
    }

    private function makeSign($params)
    {
	ksort($params);
	$str = $this->arrayToString($params).'key='.$this->config->item('weixin')['key'];
	return strtoupper(md5($str));
    }

    private function arrayToString($config)
    {
	$str = '';
	foreach ($config as $k => $v) {
	    $str .= $k.'='.$v.'&';
	}
	
	return $str;
    }    

    public function rePay()
    {
	$info = empty($_POST) ? $_GET : $_POST;
        logger('_rePay_params:'.var_export($info, true)); //debug
	$this->_need_login(TRUE);
	$orderId = $this->input->get_post('order_id');
	if (empty($orderId)) {
	    exit(json_encode(array('res' => 4, 'hint' => '缺少参数')));
	}
	$lastPayment = $this->input->get_post('last_payment');
	if (empty($lastPayment)) {
	    exit(json_encode(array('res' => 4, 'hint' => '缺少参数')));
	}
	$price = $this->input->get_post('price');
        if (empty($price)) {
            exit(json_encode(array('res' => 4, 'hint' => '缺少参数')));
        }
	$orderInfo = $this->order->get_deal($orderId, 'last_pay_coupon_code,promotion_code,wx_prepay_id');
        $un_coupon = 0;
	if (!empty($orderInfo['last_pay_coupon_code'])) {
            $coupon = json_decode($orderInfo['last_pay_coupon_code'], true);
            if (is_array($coupon) && count($coupon)) {
		$cc = $this->checkCoupon($coupon, $orderId);
                $un_coupon = $cc['un_value'];
	    }
	}

	$un_promotion = 0;
	if (!empty($orderInfo['promotion_code'])) {
	    $promotion = json_decode($orderInfo['promotion_code'], true);
            if (is_array($promotion) && count($promotion)) {
                $cp = $this->checkPromotion($promotion, $orderId);
		$un_promotion = $cp['un_value'];
            }
	}

	$return_data = array(
	    'res' => 0,
	    'hint' => '成功重新发起支付',
	    'price' => $price - $un_coupon - $un_promotion,
	    'out_trade_no' => $orderId.'P',
	    'order_id' => $orderId.'P',
	    "notify_url" => $this->order->get_notify_url(PAY_TYPE_LASTPAY, $lastPayment)
	);

	if ($lastPayment == 'weixin') {
	   /* $wata = array(
                'appid'        => $this->config->item('weixin')['appid'],
                'mch_id'       => $this->config->item('weixin')['partnerid'],
                'out_trade_no' => $orderId.'P',
                'body'         => 'youhot洋火在线购买',
                'total_fee'    => ($price + $un_coupon + $un_promotion) * 100,
                'trade_type'   => 'APP',
                'notify_url'   => $this->order->get_notify_url(PAY_TYPE_LASTPAY, 'weixin'),
                'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
                'nonce_str'     => createNonceStr(),
            );
            $wx = $this->unifiedOrder($wata);*/
	    if (empty($orderInfo['wx_prepay_id'])) {
		$wx = $this->getPrepayMsg($orderId, $price - $un_coupon - $un_promotion);
		$wx_prepay_id = $wx['prepay_id'];	
	    } else {
		$wx_prepay_id =& $orderInfo['wx_prepay_id'];
	    }
	    $payData = array(
		'appid'        => $this->config->item('weixin')['appid'],
                'partnerid' => $this->config->item('weixin')['partnerid'],
                'prepayid' => $wx_prepay_id,
                'package' => 'Sign=WXPay',
                'noncestr'     => createNonceStr(),
                'timestamp' => time()
            );
            $payData['sign'] = $this->makeSign($payData);

            $return_data = array_merge($return_data, $payData);
	}

	exit(json_encode($return_data));
    }

    public function directpay2jade()
    {
        //
        //prepay
        //lastpay
        $this->_need_login(TRUE);

        //save_time   current_time

//        $this->form_validation->set_rules('product_color', 'color', 'trim|required');
//        $this->form_validation->set_rules('product_size', 'size', 'trim|integer');
        $this->form_validation->set_rules('payment', 'payment', 'trim|required');
        $this->form_validation->set_rules('referer', 'referer', 'trim|integer');

        //@todo maybe other rules such as freight must provide

        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }
        $data = array();
        //order_id: product_id_userid_time_random?

        $data['create_time'] = standard_date('DATE_MYSQL');

        $data['buyer_userid'] = $this->userid;
        $data['status'] = ORDER_STATUS_INIT;
        $data['type'] = 2;

        $data['product_id'] = $this->input->post('product_id');
        $data['product_count'] = $this->input->post('product_count') ? $this->input->post('product_count') : 1;
        if( $this->input->post('product_color') ){
           $data['product_color'] = $this->input->post('product_color');
        }
        if( $this->input->post('product_size') ){
           $data['product_size'] = $this->input->post('product_size');
        }
        $data['ship_info_id'] = $this->input->post('ship_info_id');
        $data['freight'] = $this->input->post('freight');
        $data['freight_id'] = $this->input->post('freight_id');

//        $data['pre_pay_coupon_code'] = $this->input->post('coupon_code');  //验证时候需要把用total_fee+freight=coupon_code+need_pay的钱拿过来check,以免客户端数据的伪造.
//        $data['pre_payment'] = $this->input->post('payment');
        $data['referer'] = $this->input->post('referer');
        $data['money'] = $this->input->post('money');

        $data['ship_info_id'] = (int)$this->input->post('ship_info_id');
        $data['last_pay_coupon_code'] = $this->input->post('coupon_code');
        $data['memo'] = $this->input->post('memo');   //买家留言
        $data['last_payment'] = $this->input->post('payment');
//        $data['status'] = ORDER_STATUS_LAST_PAY_START;
        $_order_id = time() . rand(0, 9) . $this->userid . rand(10, 99);
        $data['order_id'] = $_order_id;

        $data = $this->_direct_pay_check($data);

        //需要把prepay信息设置好
        unset($data['money']);
        if (!$this->order->add_deal($data)) {
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

//        $return_data = array('out_trade_no' => $_order_id,
//
//            'order_id' => $_order_id, "notify_url" => $this->order->get_notify_url(PAY_TYPE_PREPAY, $data['pre_payment']));
//        $this->_result(bp_operation_ok, $return_data);


        $return_data = array('out_trade_no' => $_order_id . 'P', 'order_id' => $_order_id . 'P', "notify_url" => $this->order->get_notify_url(PAY_TYPE_LASTPAY, $data['last_payment']));
        $this->_result(bp_operation_ok, $return_data);

    }

    /**
     * @todo 需要验证付款钱数对不对,目前是在alipay异步通知的时候作了验证,可以提前到创建订单的时候验证
     * @param $data
     * @return mixed
     */
    private function _pre_check_and_supply_data($data)
    {
        $coupon_code = $data['pre_pay_coupon_code'];
        if ($coupon_code) {

            if (!isset($this->coupon_model)) {
                $this->load->model('coupon_model');
            }

            $check = $this->coupon_model->check_charge_card($coupon_code, $this->userid);

            if ($check === DB_OPERATION_OK) {

                $res = $this->coupon_model->get_card_info($coupon_code);
                $coupon_value = $res['money'];
                $data['pre_pay_coupon_code'] = $coupon_code;
                $data['pre_pay_coupon_value'] = $coupon_value;
            } else {

                $this->_error((int)$check, $this->coupon_model->get_check_hint($check));
            }

        }
        //coupon check end
        //Check product status begin-------
        $this->load->model('product_model');
        $product = $this->product_model->info($data['product_id'], 'basic');
        //下面这行怎么没有status的检查?
        if ($product['presale_end_time'] < (time() + 5) or ($product['presold_count'] >= $product['presale_maximum'])) {
            $this->_error(bp_operation_data_out_of_item_limit, $this->lang->line('product_sold_out_hint'));
        }
        $data['seller_userid'] = $product['author'];
        $data['product_title'] = $product['title'];
        $data['product_price'] = $product['price'];
        $data['product_presale_price'] = $product['presale_price'];
        $data['product_cover_image'] = $product['cover_image'];


        return $data;
    }

    /**
     *
     * @param $data
     * @return mixed
     */
    private function _last_pay_pre_check($data)
    {
        $coupon_code = $data['last_pay_coupon_code'];
        if (!empty($coupon_code)) {
            if (!isset($this->coupon_model)) {
                $this->load->model('coupon_model');
            }

            $check = $this->coupon_model->check_charge_card($coupon_code, $this->userid);
            if ($check === DB_OPERATION_OK) {

                $res = $this->coupon_model->get_card_info($coupon_code);
                $coupon_value = $res['money'];
                $data['last_pay_coupon_code'] = $coupon_code;
                $data['last_pay_coupon_value'] = $coupon_value;
            } else {
                $this->_error((int)$check, $this->coupon_model->get_check_hint($check));
            }
        }

        //coupon check end

        //@todo last pay is sufficient? lastpay+prepay=product_prise


        return $data;
    }

    private function _direct_pay_check($data)
    {
        //$data = $this->_last_pay_pre_check($data);

        $this->load->model('product_model');
        $product = $this->product_model->info($data['product_id'], 'basic');
        //log_debug('测试啊',$data);
        $count = (int)($this->order->get_product_order_count($data['product_id']));
        //if(((int)($product['inventory']) - $count) <= 0){
            //$this->_error(bp_operation_data_out_of_item_limit, $this->lang->line('product_sold_out_hint'));
        //}
        if ($product['status'] != PRODUCT_STATUS_PUBLISHED) {
            $this->_error(bp_operation_data_out_of_item_limit, '商品'.substr($product['title'],0,20).'...'.$this->lang->line('product_sold_out2_hint'));
        }
        $_coupon_value = element('last_pay_coupon_value', $data, 0);
        if (($_coupon_value + $data['money']) < $product['price']) {
            $this->_error(bp_operation_data_out_of_item_limit, $this->lang->line('money_not_sufficient_hint'));
        }
        $data['seller_userid'] = $product['author'];
        $data['product_title'] = $product['title'];
        $data['product_price'] = $product['price'];
        $data['product_presale_price'] = 0;  //?
        $data['product_cover_image'] = $product['cover_image'];

        return $data;
    }
    private function _direct_pay_check_more($data)
    {
        $data = $this->_last_pay_pre_check($data);
        return $data;
    }



    private function _has_edit_permission($order_id = null, $is_buyer_confirm_receive = false, $view_detail = false)
    {


        $res = $this->order->get_owner($order_id);
        if ($is_buyer_confirm_receive) {
            $owner = element('buyer_userid', $res, null);
            if ($owner == $this->userid) {
                return true;
            } else {
                return false;
            }
        }
        $role = false;
        if ($view_detail) {
            $buyer_seller = array_values($res);
            if (in_array($this->userid, $buyer_seller)) {
                $role = true;
            }
        }


        if ($this->is_admin && $this->has_admin_role(ADMIN_ROLE_ORDER)) {
            $role = TRUE;
        }

        $owner = element('buyer_userid', $res, null);

        if ($owner == $this->userid) {
            $role = true;
        }
        if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_ORDER) && $this->user_model->is_my_assistant($owner, $this->userid)) {
            $role = true;
        }
        return $role;

    }

    private function _check_permission_and_return($order_id = null, $is_buyer_confirm_receive = false, $view_detail = false)
    {
        if (!$this->_has_edit_permission($order_id, $is_buyer_confirm_receive, $view_detail)) {
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
        }
        return true;
    }

    /**
     * Save product
     * 根据需求在完善
     */
    private function _update($up_data, $is_buyer_confirm_receive = false)
    {
        $this->_need_login(TRUE);
        $order_id = $this->_get_order_id();
        $this->_check_permission_and_return($order_id, $is_buyer_confirm_receive);
        $res = $this->order->update_info($order_id, $up_data);
        if ($res == DB_OPERATION_OK) {
            $this->admin_trace('un_' . $this->userid, 'update status', $up_data);
        }
        $this->_deal_res($res);
    }

    public function update()
    {
        $this->_validation([
            ['product_presale_price', 'product_presale_price', 'numeric'],
            ['product_price', 'product_price', 'numeric'],
            ['product_size', 'product_size', 'integer'],
            ['product_color', 'product_color', 'integer'],
            ['product_count', 'product_count', 'integer'],
            ['freight', 'freight', 'numeric'],
            ['memo', 'memo', 'max_length[128]'],
            ['courier_company', 'courier_company', 'max_length[128]'],
            ['courier_number', 'courier_number', 'max_length[128]'],
	    ['remarks', 'remarks', 'max_length[256]'],
	    ['freight', 'freight', 'numeric'],
	    ['tax', 'tax', 'numeric'],
	    ['last_pay_coupon_value', 'last_pay_coupon_value', 'numeric'],
        ]);
        $filter_array = array('status', 'courier_company', 'courier_number', 'memo', 'product_presale_price', 'product_price', 'product_size', 'product_color', 'product_count', 'freight', 'last_paid_money', 'remarks', 'tax', 'last_pay_coupon_value');
        $up_data = filter_data($this->input->post(), $filter_array);
        if (element('status', $up_data, 0) == ORDER_STATUS_SHIP_START) {
            $up_data['courier_send_time'] = standard_date('DATE_MYSQL');
        }
        $this->_update($up_data);
    }

    /**
     * 确认接收.怎么处理?
     * 1)关闭订单
     */
    public function confirm_received()
    {
        $up_data = array(
            'status' => ORDER_STATUS_SHIP_RECEIVED,
            'courier_receive_time' => standard_date('DATE_MYSQL')
        );
        //buyer can confirm
        $this->_update($up_data, true);
    }

    public function detail($order_id = null)
    {
        if (empty($order_id)) {
            $order_id = $this->_get_order_id();
        }
        $this->_check_permission_and_return($order_id, false, true);
        $res = $this->order->detail($order_id);
        if (empty($res)) {
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        }
	$rt = $this->input->get_post('rt');
	if ($rt == 'jsonp') {
	    $products = array();
	    foreach ($res as $k => $v) {
		if (!empty($v['store_id'])) {
		    $products[$v['store_id']]['store_id'] = $v['store_id'];
		    $products[$v['store_id']]['flag_url'] = $v['flag_url'];
		    $products[$v['store_id']]['store_name'] = $v['store_name'];
		    $products[$v['store_id']]['list'][] = $v;
		}
		if ($v['pid'] == 0) {
		    $detail = $v;
		}
	    }
	    $detail['list'] = array_merge($products);
	    $detail['res'] = 0;
	    if (!empty($detail['overseas'])) {
                $overseas = json_decode('['.$detail['overseas'].']', true);
                if (is_array($overseas) && count($overseas)) {
                    $detail['overseas'] = $overseas;
                } else {
                    $detail['overseas'] = [];
                }
            }
            if (!empty($detail['shipping_info'])) {
                $shipping_info = json_decode($detail['shipping_info'], true);
                if (is_array($shipping_info)) {
                    $detail['shipping_info'] = $shipping_info;
                } else {
                    $detail['shipping_info'] = [];
                }
            }
	    if (!empty($detail['last_pay_coupon_code'])) {
                $coupon = json_decode($detail['last_pay_coupon_code'], true);
                if (is_array($coupon)  && count($coupon)) {
		    if ($detail['status'] == ORDER_STATUS_LAST_PAY_START) {
			$cc = $this->checkCoupon($coupon, $order_id);
			$coupon = $cc['coupon'];
			$detail['last_pay_coupon_value'] = $cc['able_value'];
                    }
                    $detail['last_pay_coupon_code'] = $coupon;
                } else {
                    $detail['last_pay_coupon_code'] = [];
                }
            }
	    if (!empty($detail['promotion_code'])) {
                $promotion = json_decode($detail['promotion_code'], true);
                if (is_array($promotion)  && count($promotion)) {
                    if ($detail['status'] == ORDER_STATUS_LAST_PAY_START) {
                        $cp = $this->checkPromotion($promotion, $order_id);
                        $promotion = $cp['promotion'];
                        $detail['promotion_value'] = $cp['able_value'];
                    }
                    $detail['promotion_code'] = $promotion;
                } else {
                    $detail['promotion_code'] = [];
                }
            }
            // float
	    $deal_sum = count($detail['list'][0]['list']);
	    $store_sum = count($detail['list']);
	    logger('deal_sum:'.$deal_sum.', store_sum:'.$store_sum); //debug
            if ($store_sum > 1 || $deal_sum > 1) {
                $detail['product_price'] = (float)$detail['product_price'];
            } else {
                $detail['product_price'] = (float)($detail['product_price'] * $detail['product_count']);
            } 
	    $detail['freight'] = (float)$detail['freight'];
	    $detail['last_pay_coupon_value'] = (float)$detail['last_pay_coupon_value'];
	    $detail['promotion_value'] = (float)$detail['promotion_value'];
	    $detail['tax'] = (float)$detail['tax'];

	    exit(json_encode($detail));
        }
        // $res = filter_data($res, $this->config->item('json_filter_order_detail'));
        $this->_result(bp_operation_ok, $res);
    }

    private function getUserCoupons()
    {
        $this->load->model('coupon_model', 'coupon');
        $list = $this->coupon->myList($this->userid, $this->_un);
        if (is_array($list) && count($list)) {
            foreach ($list as $k => $v) {
                if (($v['use_at'] <= time()) && (time() <= $v['use_end'])  && empty($v['used_at'])) {
                    $list[$k]['is_available'] = 1;
                } else {
                   $list[$k]['is_available'] = 0;
                }
            }
        }

        return $list;
    }

    private function checkCoupon($coupon, $order_id)
    {
	$able_value = 0;
        $un_value = 0;
	$user_coupons = $this->getUserCoupons();
        foreach ($coupon as $k => $v) {
           $coupon[$k]['is_available'] = 0;
           foreach ($user_coupons as $x => $y) {
                if ($v['coupon_user_id'] == $y['coupon_id']) {
                    $coupon[$k]['is_available'] = $y['is_available'];
                    if ($y['is_available'] == 1) {
                        $able_value += $v['coupon_value'];
			$user_coupons[$x]['is_available'] = 0;
                    } else {
                        unset($coupon[$k]);
                        $un_value += $v['coupon_value'];
                    }
                    break;
                }
            }
        }
        foreach ($coupon as $k => $v) {
            if ($v['is_available'] == 0) {
		$un_value += $v['coupon_value'];
                unset($coupon[$k]);
            }
        }
	if ($un_value > 0) {
	    $data = [
	      	'last_pay_coupon_value' => $able_value,
	    	'last_pay_coupon_code' => json_encode($coupon),
	    	'last_update' => date('Y-m-d H:i:s', time())
	    ]; 
            //$this->order->update_info($order_id, $data);
	    $this->load->model('deal_model', 'deal');
            $this->deal->update_infoV2($order_id, $data);
	}

	return ['coupon' => $coupon, 'able_value' => $able_value, 'un_value' => $un_value];
    }

    private function checkPromotion($promotion, $order_id)
    {
        $able_value = 0;
        $un_value = 0;
	$time = time();
        foreach ($promotion as $k => $v) {
           if (($v['end_at'] > $time) || ($v['end_at'] == 0)) {
                $able_value += $v['cut'];
           } else {
                $un_value += $v['cut'];
		unset($promotion[$k]);
           }
        }
	if ($un_value > 0) {
            $data = [
            	'promotion_value' => $able_value,
            	'promotion_code' => json_encode($promotion),
            	'last_update' => date('Y-m-d H:i:s', time())
            ];
            $this->deal->update_info($order_id, $data);
	    $this->load->model('deal_model', 'deal');
            $this->deal->update_infoV2($order_id, $data);
	}

        return ['promotion' => $promotion, 'able_value' => $able_value, 'un_value' => $un_value];
    }

    public function return_back()
    {
	$this->_need_login(TRUE);

	$data['type'] = $this->input->get_post('type');
	$data['order_id'] = $this->input->get_post('order_id');
	$data['products'] = $this->input->get_post('products');
	$data['price'] = $this->input->get_post('price');
	$data['reason'] = $this->input->get_post('reason');
	$data['img'] = $this->input->get_post('img');
	$data['create_at'] = time();
	$data['userid'] = $this->userid;

	$res = $this->order->return_back($data);

     	$this->_deal_res($res);	
    }

    public function cancel_return()
    {
        $this->_need_login(TRUE);
	$data['status'] = -1;
	$data['update_at'] = time();
	$back_id = $this->input->get_post('id');

	$res = $this->order->update_return($id, $data);

	$this->_deal_res($res);
    }

    /**
     * publish
     * [over]
     */
    public function del_order() //del
    {
        $this->_need_login(TRUE);
        $order_id = $this->_get_order_id();
        $this->_check_permission_and_return($order_id);
        $up_data = array();
        $up_data['status'] = ORDER_STATUS_DELETE;
        $res = $this->order->update_info($order_id, $up_data);
        //$this->admin_trace($order_id, 'del', $up_data);
	if ($res == DB_OPERATION_OK) {
	    $data['res'] = 0;
	    exit(json_encode($data)); 
	    $data['hint'] = '订单删除成功！';
	}
        $data['res'] = 4;
	$data['hint'] = '删除失败，请重试';
	exit(json_encode($data));
    }

    public function del() // cancel
    {
        $this->_need_login(TRUE);
        $order_id = $this->_get_order_id();
        $this->_check_permission_and_return($order_id);
        $orderInfo = $this->order->get_deal($order_id);
        if ($orderInfo) {
            switch ($orderInfo['status']) {
                case ORDER_STATUS_LAST_PAY_START:
                    $up_data['status'] = ORDER_STATUS_CANCEL_UNPAY;
                    break;
                case ORDER_STATUS_LAST_PAID:
                    $up_data['status'] = ORDER_STATUS_CANCEL_PAY;
                    break;
                case ORDER_STATUS_CANCEL_UNPAY:
                case ORDER_STATUS_CANCEL_PAY:
                    $this->_error(bp_operation_fail, '订单已取消，请勿重复操作！');
                    break;
                case ORDER_STATUS_SHIP_START:
                    $this->_error(bp_operation_fail, '订单已发货，操作失败！');
                    break;
                case ORDER_STATUS_SHIP_RECEIVED:
                    $this->_error(bp_operation_fail, '订单已完成，操作失败！');
                    break;
                case ORDER_STATUS_END_FAIL:
                    $this->_error(bp_operation_fail, '订单已关闭，操作失败！');
                    break;
            }
            if (isset($up_data['status'])) {
                $res = $this->order->update_info($order_id, $up_data);
                if ($res == DB_OPERATION_OK) {
                    $data['res'] = 0;
                    $data['hint'] = '操作成功！';
                } else {
                    $data['res'] = 4;
                    $data['hint'] = '操作失败，请重试';
                }
            	exit(json_encode($data));
            }
        } else {
            $this->_error(bp_operation_fail, '单号错误，请重试');
        }
    }

    private function _get_order_id()
    {
        $order_id = $this->input->get_post('order_id');
        if (empty($order_id)) {
            log_message('error', 'No product id:' . json_encode($order_id));
            $this->_error(bp_operation_verify_fail, $this->lang->line('error_verify_fail_need_item_id'));
        }
        return $order_id;
    }


    public function ali_lastpay_notify()
    {
        $this->paytype = PAY_TYPE_LASTPAY;
        $this->_ali_notify(PAY_TYPE_LASTPAY);
    }

    public function ali_prepay_notify()
    {
        $this->paytype = PAY_TYPE_PREPAY;
        $this->_ali_notify(PAY_TYPE_PREPAY);
    }


    public function paypal()
    {
        define("DEBUG", 1);
// Set to 0 once you're ready to go live
        define("USE_SANDBOX", 1);

// Read POST data
// reading posted data directly from $_POST causes serialization
// issues with array data in POST. Reading raw POST data from input stream instead.
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
// read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        log_debug('post:' . json_encode($myPost));

// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data
        if (USE_SANDBOX == true) {
            $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        } else {
            $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
        }
        $ch = curl_init($paypal_url);
        if ($ch == FALSE) {
            return FALSE;
        }
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        if (DEBUG == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        }
// CONFIG: Optional proxy configuration
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
// Set TCP timeout to 30 seconds
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.
//$cert = __DIR__ . "./cacert.pem";
//curl_setopt($ch, CURLOPT_CAINFO, $cert);
        $res = curl_exec($ch);
        if (curl_errno($ch) != 0) // cURL error
        {
            if (DEBUG == true) {
                log_debug(date('[Y-m-d H:i e] ') . "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL);
            }
            curl_close($ch);
            exit;
        } else {
            // Log the entire HTTP response if debug is switched on.
            if (DEBUG == true) {
                log_debug(date('[Y-m-d H:i e] ') . "HTTP request of validation request:" . curl_getinfo($ch, CURLINFO_HEADER_OUT) . " for IPN payload: $req" . PHP_EOL);
                log_debug(date('[Y-m-d H:i e] ') . "HTTP response of validation request: $res" . PHP_EOL);
            }
            curl_close($ch);
        }
// Inspect IPN validation result and act accordingly
// Split response headers and payload, a better way for strcmp
        $tokens = explode("\r\n\r\n", trim($res));
        $res = trim(end($tokens));
        if (strcmp($res, "VERIFIED") == 0) {
            // check whether the payment_status is Completed
            if (strtoupper($myPost['payment_status']) != strtoupper('Completed')) {
                log_error('payment status检查失败');
                exit;
            }
            #订单状态已控制
            // check that txn_id has not been previously processed

            $this->config->load('vendor', TRUE);
            $paypal_receiver_user = $this->config->item('paypal_receiver_user', 'vendor');

            // check that receiver_email is your PayPal email
            if ($myPost['receiver_email'] != $paypal_receiver_user['receiver_email']) {
                log_error('卖家账号有误');
                exit;
            }
            log_debug('test');
            // check that payment_amount/payment_currency are correct
            // process payment and mark item as paid.
            $data = $this->_paypal_to_zhifubao($myPost);
            $order_id = $data['out_trade_no'];
            $this->paytype = PAY_TYPE_PREPAY;
            if (strtoupper(substr($order_id, -1)) == 'P') {
                $this->paytype = PAY_TYPE_LASTPAY;
            }
            $this->_update_order($data);
            // assign posted variables to local variables
            //$item_name = $_POST['item_name'];
            //$item_number = $_POST['item_number'];
            //$payment_status = $_POST['payment_status'];
            //$payment_amount = $_POST['mc_gross'];
            //$payment_currency = $_POST['mc_currency'];
            //$txn_id = $_POST['txn_id'];
            //$receiver_email = $_POST['receiver_email'];
            //$payer_email = $_POST['payer_email'];

            if (DEBUG == true) {
                log_debug(date('[Y-m-d H:i e] ') . "Verified IPN: $req " . PHP_EOL);
            }
        } else if (strcmp($res, "INVALID") == 0) {
            // log for manual investigation
            // Add business logic here which deals with invalid IPN messages
            if (DEBUG == true) {
                log_debug(date('[Y-m-d H:i e] ') . "Invalid IPN: $req" . PHP_EOL);
            }
        }
    }

    private function _paypal_to_zhifubao(array $param)
    {
        $data = array();
        $data['discount'] = 0;
        $data['payment_type'] = 0;
        $data['subject'] = $param['item_name1'];
        $data['trade_no'] = $param['txn_id'];
        $data['buyer_email'] = $param['payer_email'];
        $data['gmt_create'] = standard_date('DATE_MYSQL');
        $data['notify_type'] = '';
        $data['quantity'] = $param['quantity1'];
        $data['out_trade_no'] = $param['item_number1'];
        $data['seller_id'] = $param['receiver_id'];
        $data['notify_time'] = standard_date('DATE_MYSQL');
        $data['body'] = '';
        $data['trade_status'] = $param['payment_status'];
        $data['is_total_fee_adjust'] = 'N';
        $data['total_fee'] = $param['mc_gross'];
        $data['gmt_payment'] = $param['payment_date'];
        $data['seller_email'] = $param['receiver_email'];
        $data['price'] = $param['mc_gross'] / $param['quantity1'];
        $data['buyer_id'] = $param['payer_email'] . '-' . $param['payer_id'];
        $data['notify_id'] = $param['ipn_track_id'];
        $data['use_coupon'] = 'N';
        $data['sign_type'] = '';
        $data['sign'] = $param['verify_sign'];
        $data['valid'] = 1;
        $data['remark'] = json_encode($param);
        return $data;
    }

    private function _ali_notify()
    {
        log_debug('ali_notify');

        $this->config->load('alipay', TRUE);
        $this->alipay_config = $this->config->item('alipay');
        log_message('debug', 'notify');
        //计算得出通知验证结果
        //$alipayNotify = new AlipayNotify($this->alipay_config);
        $this->load->library('alipay/alipay_notify', $this->alipay_config);
        $verify_result = $this->alipay_notify->verifyNotify();
        $this->order->log_pay_notify('alipay_notify', $_POST, ($verify_result ? 1 : 0));
	//$verify_result = true;
	$info = empty($_POST) ? $_GET : $_POST;
        logger('_ali_notify:'.var_export($info, true)); //debug
        if ($verify_result) {//验证成功
            ///////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	    /*
	    $info = empty($_POST) ? $_GET : $_POST;
	    logger('_ali_notify:'.var_export($info, true)); //debug
	    */
            //商户订单号
            $out_trade_no = $info['out_trade_no'];
            //支付宝交易号
            $trade_no = $info['trade_no'];
            //交易状态
            $trade_status = $info['trade_status'];

            if ($info['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //@todo 不知道需要处理什么...
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($info['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                log_debug('alinotify',$info);
                // $this->_update_product_inventory($_POST);
                $this->_update_order($info);

                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
                //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else if ($info['trade_status'] == 'TRADE_CLOSED'){
                log_debug('ali_closed',$info);
                //订单关闭
                $this->order->remove_deal($info['out_trade_no']);
            }


            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            echo "success";        //请不要修改或删除

            /////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }

    }

    public function wx_prepay_notify()
    {
        $this->ali_prepay_notify();
    }

    public function testWxNotify()
    {
	$params = array(
                'appid'        => $this->config->item('weixin')['appid'],
                'mch_id'       => $this->config->item('weixin')['partnerid'],
                'out_trade_no' => '100023456P',
                'body'         => 'youhotPayTest',
                'total_fee'    => 1,
                'trade_type'   => 'APP',
                'notify_url'   => $this->order->get_notify_url(PAY_TYPE_LASTPAY, 'weixin'),
                'spbill_create_ip' => $_SERVER['SERVER_ADDR'],
                'nonce_str'     => createNonceStr(),
        );
        $params['sign'] = $this->makeSign($params);
        $xml = $this->data_to_xml($params);
        $result = httPost('http://www.youhot.com.cn/order/wx_lastpay_notify', $xml);
        $result = $this->xml_to_data($result);
	print_r($result);
    }

    public function wx_lastpay_notify()
    {
        //$this->ali_lastpay_notify();
	log_debug('weixin_notify');
	$this->paytype = PAY_TYPE_LASTPAY;
	$info = file_get_contents("php://input");
	$info = $this->xml_to_data($info);	
	//print_r($info);die;
	if ($this->verifySign($info)) {
	    if ($info['return_code'] == 'SUCCESS') {
		if ($info['result_code'] == 'SUCCESS') {
		   $info['notify_time'] = date('Y-m-d H:i:s',strtotime($info['time_end']));
		   $info['total_fee'] = $info['total_fee']/100;
                   $info['trade_no'] = $info['transaction_id'];
		   log_debug('WXPAYNotify:',$info);
		   $this->_update_order($info); 
	        } else {
		    log_debug('WeiXin_Pay_Fail!out_trade_no:'.$info['out_trade_no'].',err_code:'.$info['err_code'].',err_code_des:'.$info['err_code_des'].',time:'.$info['time_end']);
	        }
	    } else {
		log_debug('weixin_notify_error:'.$info['return_msg']);
	    }
	} else {
	   log_debug('weixin_notify_verifySign_fail!!!');
	}
        $data['return_code'] = 'SUCCESS';
	$data['return_msg'] = 'OK';
	$xml = $this->data_to_xml( $data );
	echo $xml;
	die();
    }

    private function verifySign($info)
    {
	if(isset($info) && is_array($info)) {
	    $sign = $info['sign'];
	    unset($info['sign']);
	    $reSign = $this->makeSign($info);
	    return ($reSign == $sign) ? true : false;
	} else {
	    return false;
	}
    }

    /**
     * Product list of what I have published
     * @param userid
     * @return {
     *
     *
     * }
     */


    public function get_list($type = 'buyer')
    {
        $this->_need_login(TRUE);
        $_userid = $this->userid;
        //seller情况下:
        //助理  必须传userid
        //管理员  必须传userid,或null,userid空是所有人的
        if ($type == 'seller') {
            if ($this->_data['usertype'] == USERTYPE_USER && $this->has_admin_role(ADMIN_ROLE_ORDER) && $this->user_model->is_my_assistant($this->input->get_post('userid'), $this->userid)) {
                $_userid = $this->input->get_post('userid');
                if (empty($_userid)) {
                    $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'));
                }
            }
            if ($this->is_admin && $this->has_admin_role(ADMIN_ROLE_ORDER)) {
                $_userid = $this->input->get_post('userid');
            }

        }

        //support get list by tag or category

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');

        if (empty($order)) {
            $order = $this->input->get_post('od');
        }

        $permit_orders = array('create_time');
        if (!in_array($order, $permit_orders)) {
            $order = null;
        }


        $filter = null;
//        $filter[TBL_CDB_DEAL . '.status >'] = ORDER_STATUS_DELETE;   //Donot get deleted orders and having not pre paid orders by default

        if ($status = $this->input->get_post('status')) {
            if ($status == self::ORDER_FILTER_PREORDER) {//预定相关
                $filter = "(" . TBL_CDB_DEAL . ".status = " . ORDER_STATUS_INIT . " OR " . TBL_CDB_DEAL . ".status = " . ORDER_STATUS_PRE_PAID . ")";
            } elseif ($status == self::ORDER_FILTER_SHIP) {//ship相关
                $filter = "(" . TBL_CDB_DEAL . ".status = " . ORDER_STATUS_LAST_PAID . " OR " . TBL_CDB_DEAL . ".status = " . ORDER_STATUS_SHIP_START . ")";

            } elseif ($status == self::ORDER_FILTER_ALL) {
                $filter = TBL_CDB_DEAL . ".status > " . ORDER_STATUS_INIT;
            } elseif ($status == self::ORDER_FILTER_NEED_TO_DO) {
                $filter = '(' . implode(' OR ', array(
                        TBL_CDB_DEAL . '.status = ' . ORDER_STATUS_PRE_PAID,
                        TBL_CDB_DEAL . '.status = ' . ORDER_STATUS_LAST_PAY_START,
                        TBL_CDB_DEAL . '.status = ' . ORDER_STATUS_LAST_PAID,
                        TBL_CDB_DEAL . '.status = ' . ORDER_STATUS_SHIP_START,
                    )) . ')';
            } else {
                $filter = TBL_CDB_DEAL . '.status = ' . $status;
            }

        }
        //Search
        if ($search = $this->input->get_post('search')['value']) {
            $filter = "(" . TBL_CDB_DEAL . ".order_id like '%" . $this->order->db_master->escape_like_str($search) . "%')";
        }

        //Filter by product
        if ($pid = $this->input->get_post('product_id')) {
            $link = empty($filter) ? '' : ' and ';
            $filter .= $link . "(" . TBL_CDB_DEAL . ".product_id = " . $this->order->db_master->escape($pid) . ")";
        }
        $res = $this->order->get_list($_userid, $offset, $limit, $filter, (string)$order, $type);
        if (!empty($res['list'])) {
            $res['count'] = count($res['list']);
	    foreach ($res['list'] as $k => $v) {
		$price_sum = 0;
	    	foreach ($v['products'] as $m => $n) {
		    $price_sum += $n['product_price'] * $n['product_count']; 
	  	}
		$res['list'][$k]['product_price'] = $price_sum;
	    }
            if ($this->input->get_post('draw')) {
                $res['draw'] = (int)$this->input->get_post('draw');
            }
            $this->_result(bp_operation_ok, $res);
        } else {
            log_message('error', 'No order list:' . json_encode($res));
            $this->_error(bp_operation_fail, $this->lang->line('hint_list_get_fail'));
        }

    }

    /**
     * 查询待付款中的订单数量
     */


    public function update_product_inventory()
    {
        $this->_update_product_inventory($_REQUEST);
    }
    private function _update_product_inventory_old($data)
    {
        exit;
        $order_id = $data['out_trade_no'];
        $order_id = substr($order_id, 0, -1);
        $order = $this->order->get_deal($order_id);
        if(empty($order))
            return;
        $product_id = $order['product_id'];
        $this->load->model('product_model');
        $product = $this->product_model->info($product_id);
        if(empty($order))
            return;

        $inventory = (int)$product['inventory'];

        if($inventory != 0){
            $inventory -= 1;
        }

        $product['inventory'] = $inventory;

        $this->product_model->update_info($product_id,array('inventory' => $inventory));
    }
    //仅在原接口基础上修改，未改动订单逻辑和功能
    private function _update_product_inventory($data)
    {
        $order_id = $data['out_trade_no'];
        $order_id = substr($order_id, 0, -1);
        $order = $this->order->get_deal($order_id);
        if(empty($order))
            return;
        if( $order['product_id']==0 ){
            //子订单
            $orders = $this->order->get_deal_more($order_id);
            if(empty($orders))
                return;
            $this->load->model('product_model');
            foreach($orders AS $order){
                $product_id = $order['product_id'];
                $product = $this->product_model->info($product_id);
                if(empty($product))
                    return;

                $inventory = (int)$product['inventory'];
                if($inventory != 0){
                    $inventory -= 1;
                }
                $this->product_model->update_info_v2($product_id, array('inventory', '-', 1));
            }
        }else{
            //主订单
            $product_id = $order['product_id'];
            $this->load->model('product_model');
            $product = $this->product_model->info($product_id);
            if(empty($product))
                return;

            $inventory = (int)$product['inventory'];

            if($inventory != 0){
                $inventory -= 1;
            }

            $product['inventory'] = $inventory;

            $this->product_model->update_info($product_id,array('inventory' => $inventory));
        }
    }

    /**
     *
     * 更新订单状态（更新支付宝订单号）
     * @param string $alipay_trade_no 支付宝订单号
     * @param string $order_sn 商户订单号
     */
    private function _update_order($data)
    {
        //请在这里加上商户的业务逻辑程序代
        //

        log_message('debug', 'update status');

        //@todo 需要验证钱是否正确?
        /**
         * //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
         * prepay:  prepay+prepay_coupon_value==product_presale_price
         * lastpay: lastpay+lastpay_coupon_value+pre_paid_money = product_size
         */

        $order_id = $data['out_trade_no'];

        $valid = true;

        if ($this->paytype == PAY_TYPE_LASTPAY) {

            $order_id = substr($order_id, 0, -1);

            $sqldata = array(
                'last_paid_time' => $data['notify_time'],
                'last_paid_money' => $data['total_fee'],
                'status' => ORDER_STATUS_LAST_PAID,
                //'last_paid_payinfo' => $order_id . self::PAYINFO_LASTPAY_SUFFIX
		'last_paid_payinfo' => $data['trade_no'],
            );
            //$data['info_id'] = $sqldata['last_paid_'];
            $filter = array('status' => ORDER_STATUS_LAST_PAY_START);  //@need to check  IS OK WHEN MUST LAY_PAY_START

            $order_info = $this->order->detail($order_id);
            //if (($data['total_fee'] + $order_info['last_pay_coupon_value'] + $order_info['pre_paid_money'] + $order_info['pre_pay_coupon_value']) < $order_info['product_price']) {
            //    $data['valid'] = $valid = false;
            //}
            //运费 + 订单额 = total_fee
            //if( bccomp($data['total_fee'], bcadd($order_info['product_price'], $order_info['freight'], 2),2) < 0 ){
                $data['valid'] = $valid = false;
            //}
            /*if ($order_info['type'] == SELL_TYPE_SALE) {  //Order status before last pay is init in direct selling
                $filter['status'] = ORDER_STATUS_INIT;
            }*/
        }
        if ($this->paytype == PAY_TYPE_PREPAY) {
            $sqldata = array(
                'pre_paid_time' => $data['notify_time'],
                'pre_paid_money' => $data['total_fee'],
                'status' => ORDER_STATUS_PRE_PAID,
                'pre_paid_payinfo' => element('out_trade_no', $data, time()) . self::PAYINFO_PREPAY_SUFFIX
            );
            $data['info_id'] = $sqldata['pre_paid_payinfo'];
            $filter = array('status' => ORDER_STATUS_INIT);
            $order_info = $this->order->detail($order_id);
            //if (($data['total_fee'] + $order_info['pre_pay_coupon_value']) < $order_info['product_presale_price']) {
                $data['valid'] = $valid = false;
            //}
        }

        //var_dump($sqldata);

        if ($valid) {
            if ($data['seller_id'] != $this->alipay_config['partner']) {

                log_error("[alipay]Order sellerid is wrong. May be malicious deal!!" . json_encode($data));
                $data['valid'] = $valid = false;
            }
        }
        $pay_info_id = $this->_record_pay_info($data);
//        if($valid){   //@important! Need to check on production
        $qu = $this->order->update_deal_and_product($order_id, $sqldata, $filter, $this->paytype);
        return $qu;
//        }

        return false;

    }

    private function _record_pay_info($data)
    {
        //@todo  拆分不同的field
        $filter_rule = array('info_id', 'discount', 'payment_type', 'subject', 'trade_no', 'buyer_email', 'gmt_create', 'notify_type', 'quantity', 'out_trade_no', 'seller_id', 'notify_time', 'body', 'trade_status', 'is_total_fee_adjust', 'total_fee', 'gmt_payment', 'seller_email', 'price', 'buyer_id', 'notify_id', 'use_coupon', 'sign_type', 'sign', 'valid');
        $data = filter_data($data, $filter_rule);
        $res = $this->order->insert_payinfo($data);
        if ($res == DB_OPERATION_FAIL) {
            log_debug('');
        }

    }

    /**
     * 买家订单统计数据,每个状态下的订单数量
     */
    public function num()
    {
        $this->_need_login(TRUE);
        $res = $this->order->get_status_num($this->userid);
        $ret = array();
        $_total = 0;
        $_presold = 0;
        $_ship = 0;
        $_todo = 0;
        foreach ($res as $key => $row) {
            $ret[$row['status']] = (int)$row['count'];
            $_total += $row['count'];
            if ($row['status'] == ORDER_STATUS_INIT || $row['status'] == ORDER_STATUS_PRE_PAID) {
                $_presold += (int)$row['count'];
            }
            if ($row['status'] == ORDER_STATUS_LAST_PAID || $row['status'] == ORDER_STATUS_SHIP_START) {
                $_ship += (int)$row['count'];
            }
            if (in_array($row['status'], array(ORDER_STATUS_PRE_PAID, ORDER_STATUS_LAST_PAID, ORDER_STATUS_LAST_PAY_START, ORDER_STATUS_SHIP_START))) {
                $_todo += (int)$row['count'];
            }


        }
        $data = array(
            'total' => $_total,
            'list' => $ret + array(self::ORDER_FILTER_PREORDER => $_presold,
                    self::ORDER_FILTER_SHIP => $_ship,
                    self::ORDER_FILTER_NEED_TO_DO => $_todo
                )
        );

        $this->_result(bp_operation_ok, $data);


    }
    /*
     *  count freight
     *  @author fisher
     *  @param ps
     *  @return array 
     */
    public function count_freight()
    {
        // $this->_need_login(TRUE);

        $products = json_decode($this->input->get('ps'), true);
        // var_dump($products);die;
        if( ! is_array($products) ){
            $this->_error(bp_operation_fail, '缺少参数');
        }
        $new_products = array();
        foreach ($products as $product) {
            if( isset($product['product_id']) ){
                $data['product_id'] = $product['product_id'];
            }else{
                $this->_error(bp_operation_fail, '商品不存在');
            }
            // 获取商品信息及单独计算运费
            $info = $this->get_product_info($data);
            $new_products[$info['sale_shop']]['list'][] = $info;
        }
        // 目前每个商城固定运费60元
        foreach ($new_products as $key => $shop) {
            $new_products[$key]['total_freight'] = 60;
            $freight = 60/count($shop['list']);
            if ($freight == 60) {
                continue;
            } else {
                foreach ($shop['list'] as $k => $product) {
                    $new_products[$key]['list'][$k]['freight'] = $freight;
                }
            }
        }
        echo_json($new_products);
    }

    private function get_product_info($data)
    {
        $this->load->model('product_model');
        $product = $this->product_model->info($data['product_id'], 'basic');
        // 购买商城地址 by fisher at 2017-04-05
        $tmp_url = json_decode($product['tmp_img'], true);
        $product['sale_shop'] = strchr(ltrim(strchr($tmp_url['jurl'], '//'), '//'), '/', true);
        // 根据商品分类计算运费
        $product['freight'] = $this->get_freight($product); // 公式计算
        // 运费计算END
        return $product;
    }

    private function get_freight($product)
    {
        // 计算公式
        // 目前默认返回60元
        return 60;
    }

    public function out_of_stock_note()
    {
	// note
	$this->pushed();

	$this->update_out_of_stock_note();
    }

    private function update_out_of_stock_note($is_buyer_confirm_receive = false)
    {
	$this->_need_login(TRUE);
        $order_id = $this->_get_order_id();
        $this->_check_permission_and_return($order_id, $is_buyer_confirm_receive);
        $res = $this->order->update_out_of_stock_note($order_id);
        $this->_deal_res($res);
    }

    private function pushed()
    {
        $data['userid'] = $this->input->get_post('pt_buyer');
        $data['title'] = $this->input->get_post('note-title');
        $data['content'] = $this->input->get_post('note-content');
        $data['tid'] = $this->input->get_post('pt_id');
        $data['type'] = 4;
        $data['ctype'] = 1;

        $this->send_jpush($data);
    }

    private function send_jpush($msg)
    {
        $send_url = "http://10.26.95.72/index.php/note?" . http_build_query($msg);
        $res = $this->curl_note($send_url);

        return true;
    }

    private function curl_note($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    public function waitForDealWith()
    {
	$this->_need_login(true);
	$offset = $this->input->get_post('of');
	$offset = !empty($offset) ?: 0;
	$limit = $this->input->get_post('lm');
        $limit = !empty($limit) ?: 0; 
	$res = $this->order->getReturnBackList($this->userid, $offset, $limit);

	if (!empty($res['list'])) {
            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);
        } else {
            $this->_error(bp_operation_fail, $this->lang->line('hint_list_get_fail'));
        }
    }

    public function returnBackDetail()
    {
	$this->_need_login(true);
	$id = $this->input->get_post('id');
	if(empty($id)){
            $this->_error(bp_operation_fail, '缺少参数');
        }
	$res = $this->order->getReturnDetail($id);

	if ($res) {
	    $this->_result(bp_operation_ok, $res);
	} else {
	    $this->_error(bp_operation_fail, $this->lang->line('hint_list_get_fail'));
	}
    }
}
