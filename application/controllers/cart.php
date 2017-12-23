<?php
/**
 * 购物车
 */
class Cart extends User_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('cart_model');
        $this->load->model('store_model', 'store');
        $this->load->model('product_model', 'product');
    }

    //获取列表
    public function all(){
        $this->_need_login(TRUE);
        //$this->userid=1;

        $res = $this->cart_model->get_list($this->userid);
        //print_r($res);
        $this->_result(bp_operation_ok, $res);
    }
    
    public function get_list()
    {
	logger('cart_get_list_login:'.$this->userid);
        //$this->_need_login(TRUE);
	if (!empty($this->userid)) {
            $res = $this->cart_model->get_list($this->userid);
            if ($res['total']) {
            	$stores = array();
            	$list = array();
            	foreach ($res['list'] as $k => $v) {
                    if (in_array($v['store'], $stores)) {
                    	$list[$v['store']][] = $v;
                    } else {
                    	$stores[] = $v['store'];
                    	$list[$v['store']][] = $v;
                    }
            	}
	    	$res['list'] = array();
            	for ($i=0;$i<count($stores);$i++) {
		    $res['list'][$i]  = $this->store->getDetail($list[$stores[$i]][0]['store']);	
                    $res['list'][$i]['list'] = $list[$stores[$i]];
		    foreach ($res['list'][$i]['list'] as $m => $n) {
		     	$res['list'][$i]['list'][$m]['store_name'] = @$res['list'][$i]['show_name'];
		    }
		    $res['list'][$i]['sales'] = $this->getSales($list[$stores[$i]][0]['store']);
                }
            }
	    $res['isLogin'] = 1;
	} else {
	    $res = array(
		'isLogin' => 0,
	    );
	}
	$res['unread'] = $this->hasUnread();
        $this->_result(bp_operation_ok, $res);
    }

    private function getSales($store, $field='name')
    {
	$info = '';
	$sales = $this->store->getRecentSales($store, $field);
	if (is_array($sales) && count($sales)) {
	    $sales = array_column($sales, $field);
	    $info = implode(' ', $sales);
	}

	return $info;
    }

    /**
     * 增加商品
     * @param 商品参数同订单设计
     */
    function add(){
        $this->_need_login(TRUE);

	$_POST = $_REQUEST;
        $this->form_validation->set_rules('product_id',   'product_id',   'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail,'商品信息不全，请重试');
        }
        $data = array();
        $data['userid']       = $this->userid;
        $data['product_id']   = $this->input->get_post('product_id');
        $data['product_count']= $this->input->get_post('product_count') ? $this->input->post('product_count') : 1;
        $data['product_color']= trim($this->input->get_post('product_color')) ? trim($this->input->get_post('product_color')) : '';
        $data['product_size'] = $this->input->get_post('product_size') ? $this->input->get_post('product_size') : '';
	$data['is_luxury'] = $this->getLuxuryTest($data['product_id']);
	$referer = $this->input->get_post('referer') ?: 0;
        $data['referer'] = $referer == $this->userid ? 0 : $referer;
	
        if( ! $this->cart_model->add($data) ){
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

        $this->product->update_position($this->input->get_post('product_id'), '3'); // mark user'click for home page recommond

        $this->_result(bp_operation_ok, array('status'=>true));
    }

    //更新商品
    function update(){
        $this->_need_login(TRUE);
        $this->form_validation->set_rules('product_id',   'product_id',   'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->_error(bp_operation_verify_fail, validation_errors(' ', ' '));
        }

        $data = array();
        $data['userid']       = $this->userid;
        $data['product_id']   = $this->input->post('product_id');
        $data['product_count']= $this->input->post('product_count') ? $this->input->post('product_count') : 1;
        $data['product_color']= trim($this->input->post('product_color')) ? trim($this->input->get_post('product_color')) : '';
        $data['product_size'] = $this->input->post('product_size') ? $this->input->post('product_size') : '';
	$data['is_luxury'] = $this->getLuxuryTest($data['product_id']);
        $referer = $this->input->get_post('referer') ?: 0;
        $data['referer'] = $referer == $this->userid ? 0 : $referer;

        if( ! $this->cart_model->edit($data) ){
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }

        $this->_result(bp_operation_ok, array('status'=>true));

    }

    //删除商品
    function del(){
        $this->_need_login(TRUE);
        $id = (int)$this->input->post('cartid');
        if( ! $this->cart_model->delete($id, $this->userid) ){
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }
        $this->_result(bp_operation_ok, array('status'=>true));
    }

    private function getLuxuryTest($id)
    {
	$this->load->model('product_model', 'product');
	$product = $this->product->baseInfo($id);
	$this->load->model('user_model', 'user');
	$luxuries = $this->user->getLuxuries();
	if (is_array($luxuries) && count($luxuries)) {
	    foreach ($luxuries as $k => $v) {
		if ($product[0]['author'] == $v['userid']) {
		    return 1;
		}
	    }
        }
	return 0;
    }

   private function hasUnread()
   {
        if (isset($this->userid)) {
            $offset = 0;
            $limit = 1000;
            $this->load->model('message_model', 'msg');
            $msgs = $this->msg->getUnreadList($this->userid, $offset, $limit);
            $res = $this->dealWithUnreadList($msgs);
            $type1 = $this->msg->getMsgType(1);
            if (is_array($type1) && count($type1)) {
                $read1 = $this->msg->getReadType(1, $this->userid);
                if (is_array($read1) && count($read1)) {
                    if ($this->dealWithUnreadType1($type1, $read1)) {
                       $res = 0;
                    }
                } else {
                    $res = 0;
                }
            }
        } else {
            $res = 1;
        }
        return $res;
   }

   private function dealWithUnreadList($list)
   {
        $unread = 1;
        if (is_array($list) && count($list)) {
            $unread = 0;
        }
        return $unread;
   }

   private function dealWithUnreadType1($list, $read)
   {
        $ids = array_column($list, 'id');
        $readIds = array_column($read, 'msgid');
        foreach ($ids as $v) {
            if (!in_array($v, $readIds)) {
                return true;
                break;
            }
        }
        return false;
   }
}
?>
