<?php
class Message extends User_Controller
{
    const TBL_COLLECTION = 'collection';
    const TBL_SHIPPING = 'shipping';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('message_model', 'msg');
    }

    public function msgList()
    {
        $this->_need_login(true);
        $type = $this->input->get_post('type');
        if (empty($type) || !is_numeric($type)) {
            $this->_error(bp_operation_fail, '缺少参数,请重试');
        }
	$offset = $this->input->get_post('of');
	$offset = empty($offset) ? 0 : $offset;
	$limit = $this->input->get_post('lm');
	$limit = empty($limit) ? 10 : $limit;

	switch ($type) {
	    case '1':
		$data = $this->msg->getNoteList($type, $offset, $limit);
		// print_r($list);die;
		$readList = $this->msg->getMsgList($this->userid, $type);
		// print_r($readList);die;
		$data['list'] = $this->dealWith($data['list'], $readList);
		break;
	    default:
        	$data = $this->msg->getList($this->userid, $type, $offset, $limit);
		foreach ($data['list'] as $k => $v) {
		    $data['list'][$k]['is_available'] = 0;
                    if ($v['available_end'] > time()) {
                        $data['list'][$k]['is_available'] = 1;
                    }
		}
		break;
        }
        
	if (isset($data['list'][0]['type'])) {
	    $data['res'] = 0;
	    exit(json_encode($data));	
        }
        $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
    }

    private function dealWith($list, $read)
    {
	if (isset($list[0]['type'])) {
	    foreach ($list as $k => $v) {
		$list[$k]['is_read'] = '0';
		$list[$k]['is_available'] = 0;
		if ($v['available_end'] > time()) {
		    $list[$k]['is_available'] = 1;
		}
		foreach ($read as $x => $y) {
		    if ($v['id'] == $y['msgid']) {
			$list[$k]['is_read'] = '1';
			break;
  		    }
	    	}
		$list[$k]['product_status'] = 1;
	    }
	}

	return $list;
    }

   public function read()
   {
	$this->_need_login(true);
	$filter['userid'] = $this->userid;
        $type = $this->input->get_post('type');
        if (empty($type) || !is_numeric($type)) {
            $this->_error(bp_operation_fail, '缺少参数，请重试');
        }
	$filter['type'] = $type;
	$msgId = $this->input->get_post('msg_id');
	if (!empty($msgId) && is_numeric($msgId)) {
	    $filter['msgid'] = $msgId;
	}
	switch ($type) {
	    case 1:
		$res = $this->createReadMsgs($this->userid);
		break;
	    default:
		$res = $this->readMsgs($filter);	
	    	break;
	}
        if ($res == DB_OPERATION_FAIL) {
            $this->_error(bp_operation_fail, '更新消息状态失败');
        }
	//$this->_success();
	$data = array(
	    'isread' => 0
	);
	exit(json_encode([$data,$data,$data,$data]));
   }

   private function readMsgs($filter)
   {
	return $this->msg->readMsgs($filter);
   }

   private function createReadMsgs($userid)
   {
	$data = $this->create_read_data($userid);
	if (is_array($data) && count($data)) {
	    return $this->msg->createReadDatas($data);
	}
	return DB_OPERATION_FAIL;
   }

   private function create_read_data($userid)
   {
	$msgs = $this->msg->getNoteList(1, 0, 1000);
	$readList = $this->msg->getMsgList($userid, 1);
	if (is_array($msgs) && count($msgs['list'])) {
	    foreach ($msgs['list'] as $k => $v) {
		$data[$k]['userid'] = $userid;
		$data[$k]['msgid'] = $v['id'];
		$data[$k]['status'] = 1;
		$data[$k]['type'] = 1;
		$data[$k]['create_at'] = $data[$k]['read_at'] = time();
		foreach ($readList as $x => $y) {
		   if ($y['msgid'] == $v['id']) {
			unset($data[$k]);
			break;
		   } 
		}
	    }
	    return $data;
        }
	return false;	
   }

   public function hasUnread()
   {
	//$this->_need_login(true);
     if (!empty($this->userid)) {
	$offset = 0;
	$limit = 1000;
	$msgs = $this->msg->getUnreadList($this->userid, $offset, $limit);
	$res = $this->dealWithUnreadList($msgs);
	$type1 = $this->msg->getMsgType(1);
	if (is_array($type1) && count($type1)) {
	    $read1 = $this->msg->getReadType(1, $this->userid);
	    if (is_array($read1) && count($read1)) {
		if ($this->dealWithUnreadType1($type1, $read1)) {
		    $res['list'][3]['unread'] = 0;
		    $res['unread'] = 0;
		}	
	    } else {
		$res['list'][3]['unread'] = 0;
                $res['unread'] = 0;
	    }
	}
	$res['isLogin'] = 1;
     } else {
	$res = $this->dealWithUnreadList([]);
	$res['isLogin'] = 0;
     }

     $this->_result(bp_operation_ok, $res);	
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

   private function dealWithUnreadList($list)
   {
	$res = array(
	   'list' =>  array(
		array(
                    'type' => 2,
                    'name' => '价格变动通知',
                    'icon' => 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/album/4nRc8o_aW1nXzFfbWVzc2FnZUAyeC5wbmc.png',
                    'unread' => 1
            	),
		array(
                    'type' => 3,
                    'name' => '客服',
                    'icon' => 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/album/vl7vb6_aW1nXzJfbWVzc2FnZUAyeC5wbmc.png',
                    'unread' => 1
                ),
		array(
                    'type' => 4,
                    'name' => '发货/断货通知',
                    'icon' => 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/album/rOCQQG_aW1nXzNfbWVzc2FnZUAyeC5wbmc.png',
                    'unread' => 1
                ),
		array(
                    'type' => 1,
                    'name' => '运营活动通知',
                    'icon' => 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/album/fHE6Kf_aW1nXzRfbWVzc2FnZUAyeC5wbmc.png',
                    'unread' => 1
                ),
	    ),
	    'unread' => 1
	);
	if(is_array($list) && count($list)) {
	    foreach ($list as $k => $v) {
		switch ($v['type']) {
		    case 2:
                        $res['list'][0]['unread'] = 0;
			$res['unread'] = 0;
                        break;
		    case 4:
                        $res['list'][2]['unread'] = 0;
			$res['unread'] = 0;
                        break;
		}
	    }
        }
	
	return $res;	
   } 
}
