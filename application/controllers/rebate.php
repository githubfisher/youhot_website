<?php
class Rebate extends User_Controller
{
    const REBATE = 0.01;
    const OVER = 0.02;
    const OVER_VL = 10000;
    const DEFAULT_SORT = 'browse_at';

    public function __construct()
    {
	parent::__construct();
	$this->load->model('rebate_model', 'rebate'); 
    }

    public function index($fm=false, $sort=self::DEFAULT_SORT)
    {
	$this->_need_login(true);

	$customers = $yesterday_order = 0;
	$income = $balance = $yesterday_earnings = 0;
	$deals = $this->rebate->getRebateDeal($this->userid);
	$marks = $this->rebate->getFootMark($this->userid); // foot_mark
	if (is_array($deals) && count($deals)) {
	    $group = [];
	    foreach ($deals as $k => $v) {
		$group[$v['userid']]['deals'][] = $v;
	    }
	    foreach ($group as $k => $v) {
		$customers++;
		$monetary = $total_income = $total_over_income = $browse_at = $recent_at = 0;
		foreach ($v['deals'] as $x => $y) {
		    $timestamp = strtotime($y['last_paid_time']);
		    if (empty($browse_at) || $timestamp < $browse_at)  {
			$browse_at = $timestamp;
		    }
		    if (empty($recent_at) || $timestamp > $recent_at)  {
                        $recent_at = $timestamp;
                    }
		    $value = $y['product_count'] * $y['product_price'];
		    $monetary += $value;
		    if ($monetary > self::OVER_VL) {
			$over = $monetary - self::OVER_VL;
			if ($over >= $value) {
			    $come = $over_come = self::OVER * $value;	
			} else {
			    $over_come = self::OVER * $over;
                            $come = self::REBATE * ($value - $over);
                            $come += $over_come;
			}
		    } else {
		        $come = self::REBATE * $value;
			$over_come = 0;
		    }
		    $total_income += $come;
		    $total_over_income += $over_come;
		    $income += $come;
                    if ($y['status'] == ORDER_STATUS_SHIP_START) { // receive goods
                        $balance += $come;
                    }
		    if ($y['last_paid_time'] > strtotime('yesterday') && $y['last_paid_time'] <= strtotime(date('Y-m-d'))) {
		        $yesterday_order++;
		    	$yesterday_earnings += $come;
		    }
		}
	        $group[$k]['total_monetary'] = sprintf('%.2f', $monetary);
	        $group[$k]['income'] = sprintf('%.2f', $total_income);
	        $group[$k]['extra_income'] = sprintf('%.2f', $total_over_income);
		$group[$k]['userid'] = $v['deals'][0]['userid'];
		$group[$k]['nickname'] = $v['deals'][0]['nickname'];
		$group[$k]['facepic'] = $v['deals'][0]['facepic'];
	        $group[$k]['browse_at'] = $browse_at;
	        $group[$k]['browse_date'] = date('Y-m-d', $browse_at);
	        $group[$k]['recent_at'] = $recent_at;
		unset($group[$k]['deals']);
	    }
	    if (is_array($marks) && count($marks)) {
		$new_customer = 0;
		foreach ($marks as $k => $v) {
		    $c =  1;
		    foreach ($group as $x => $y) {
			if ($v['userid'] == $x) {
			    $group[$x]['browse_at'] = (int)$v['browse_at'];
			    $group[$x]['browse_date'] = date('Y-m-d', $v['browse_at']);
			} else {
			    if ($c == count($group)) {
			 	$group[] = array(
				    'total_monetary' => sprintf('%.2f', 0),
				    'income' => sprintf('%.2f', 0),
				    'extra_income' => sprintf('%.2f', 0),
				    'userid' => $v['userid'],
				    'nickname' => $v['nickname'],
				    'facepic' => $v['facepic'],
				    'browse_at' => (int)$v['browse_at'],
				    'browse_date' => date('Y-m-d', $v['browse_at']),
				    'recent_at' => 0,
				);
				$new_customer++;
			    }
			    $c++;
			}
		    }
		}
	        $customers += $new_customer;
	    }
	    $cash = $this->rebate->withdrawCashSum($this->userid);
	    if (is_array($cash) && !empty($cash[0]['value'])) {
		$balance -= $cash[0]['value'];
	    }
	} else {
	    $group = [];
	    foreach ($marks as $k => $v) {
		$group[] = array(
                    'total_monetary' => sprintf('%.2f', 0),
                    'income' => sprintf('%.2f', 0),
                    'extra_income' => sprintf('%.2f', 0),
                    'userid' => $v['userid'],
                    'nickname' => $v['nickname'],
                    'facepic' => $v['facepic'],
                    'browse_at' => $v['browse_at'],
                    'browse_date' => date('Y-m-d', $v['browse_at']),
		    'recent_at' => 0,
                );
	    }
	    $customers = count($marks);
	}

	if ($fm) {
	    $timestamps = array_column($group, $sort);// browse_at, total_monetary, recent_at
	    array_multisort($timestamps, SORT_DESC, $group);
	    exit(json_encode(['res' => 0, 'hits' => 'success', 'customers' => $group])); 
	} else {
	    exit(json_encode([
	    	'res' => 0,
	    	'hits' => 'success',
	    	'income' => sprintf('%.2f', $income),
	    	'balance' => sprintf('%.2f', $balance),
	    	'yesterday_earnings' => sprintf('%.2f', $yesterday_earnings),
	    	'yesterday_order' => $yesterday_order,
	    	'customers' => $customers
	    ]));
	}
    }

    public function withdrawCash()
    {
	$this->_need_login(true);
	
	$data = ['res' => 0, 'hits' => 'success'];
	$card =  $this->rebate->getCard($this->userid);
	if (is_array($card) && count($card)) {
	    $data['bank_logo'] = $card[0]['bank_facepic'];
	    $data['bank_name'] = $card[0]['bank_name'];
	    $data['name'] = $card[0]['name'];
	    $data['card'] = $card[0]['card'];
	}
	
  	exit(json_encode($data));
    }

    public function checkCardsBank()
    {
	$this->_need_login(true);

	$card = preg_replace('# #', '', $this->input->get_post('card'));
	if (empty($card)) {
	    $this->_error(bp_operation_fail, 'param \'card\' can\'t be empty');
	}
   	$url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=$card&cardBinCheck=true";
	$res = httPost($url);
	if (!empty($res)) {
	    $res = json_decode($res, true);
	    if ($res['stat'] == 'ok' && isset($res['bank'])) {
		$data = ['res' => 0, 'hits' => 'failed'];
		$this->config->load('bank', TRUE);
                $bank = $this->config->item('bank', 'bank');		
		if (isset($bank[$res['bank']])) {
		    $data = array(
			'res' => 0,
			'hits' => 'success',
			'bank_name' => $bank[$res['bank']],
			'card_type' => $res['cardType'],
		    );
		}
		if (file_exists(dirname(__FILE__).'/../../static/images/bank/'.$res['bank'].'.png')) {
		    $data['bank_logo'] = 'http://'.$_SERVER['HTTP_HOST'].'/static/images/bank/'.$res['bank'].'.png';
                } else {
		    $data['bank_logo'] = 'http://'.$_SERVER['HTTP_HOST'].'/static/images/bank/default.png';
		}
		exit(json_encode($data));
	    }
	}
	
	exit(json_encode(['res' => 1, 'hits' => 'failed']));
    }

    public function bindBankCard()
    {
	$this->_need_login(true);
	
	$bank = $this->input->get_post('bank');
	if (empty($bank)) {
            $this->_error(bp_operation_fail, 'param \'bank\' can\'t be empty');
        }
	$card = $this->input->get_post('card');
	if (empty($card)) {
            $this->_error(bp_operation_fail, 'param \'card\' can\'t be empty');
        }
	$name = $this->input->get_post('name'); 
	if (empty($name)) {
            $this->_error(bp_operation_fail, 'param \'name\' can\'t be empty');
        }
	$logo = $this->input->get_post('bank_logo');
	if (empty($logo)) {
            $this->_error(bp_operation_fail, 'param \'bank_logo\' can\'t be empty');
        }
	$type = empty($this->input->get_post('type')) ? 1 : $this->input->get_post('type'); // 0 new | 1 change
	$card_id = $this->input->get_post('card_id');
	$data = array(
	    'bank_name' => $bank,
	    'bank_facepic' => $logo,
	    'card' => $card,
	    'name' => $name,
	);
	if ($type == 1) {
	    $data['created_at'] = $data['updated_at'] = time();
	    $data['userid'] = $this->userid;
	    $res = $this->rebate->addCard($data);
	} else {
	    if (empty($card_id)) {
            	$this->_error(bp_operation_fail, 'param \'card_id\' can\'t be empty');
            }
	    $data['updated_at'] = time();
	    $res = $this->rebate->updateCard($card_id, $data);
	}

	if ($res == DB_OPERATION_FAIL) {
	    $res = ['res' => 1, 'hits' => 'failed'];
	} else { 
	    $res = ['res' => 0, 'hits' => 'success'];
	}
	exit(json_encode($res));
    }

    public function commitWithdrawCash()
    {
	$this->_need_login(true);

	$bank = $this->input->get_post('bank');
        if (empty($bank)) {
            $this->_error(bp_operation_fail, 'param \'bank\' can\'t be empty');
        }
        $card = $this->input->get_post('card');
        if (empty($card)) {
            $this->_error(bp_operation_fail, 'param \'card\' can\'t be empty');
        }
        $name = $this->input->get_post('name');
        if (empty($name)) {
            $this->_error(bp_operation_fail, 'param \'name\' can\'t be empty');
        }
        $sum = $this->input->get_post('sum');
        if (empty($sum)) {
            $this->_error(bp_operation_fail, 'param \'sum\' can\'t be empty');
        }
	$data = array(
	    'userid' => $this->userid,
	    'bank_name' => $bank,
	    'card' => $card,
	    'name' => $name,
	    'value' => $sum
	);
	$data['created_at'] = $data['updated_at'] = time();
	$res = $this->rebate->addWithdrawCash($data);

	if ($res == DB_OPERATION_FAIL) {
	    exit(json_encode(['res' => 1, 'hits' => 'failed']));
	}
	exit(json_encode(['res' => 0, 'hits' => 'success']));
    }

    public function myCustomers()
    {
	$this->_need_login(true);
	$sort = empty($this->input->get_post('sort')) ? self::DEFAULT_SORT : $this->input->get_post('sort');
	$this->index(true, $sort);
    }

    public function myBankCard()
    {
        $this->_need_login(true);

        $data = ['res' => 0, 'hits' => 'success', 'type' => 1];
        $card =  $this->rebate->getCard($this->userid);
        if (is_array($card) && count($card)) {
            $data['bank_logo'] = $card[0]['bank_facepic'];
            $data['bank_name'] = $card[0]['bank_name'];
            $data['name'] = $card[0]['name'];
            $data['card'] = $card[0]['card'];
	    $data['card_id'] = $card[0]['id'];
	    $data['type'] = 2;
        }

        exit(json_encode($data));
    }
}
