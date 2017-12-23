<?php
/**
 * Class Balance Accounts
 * @method
 */
class Recount extends User_Controller
{
    const TAX = 0.12;
    const MIDDLE_FEE = 20;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('count_model', 'count');
    }

    public function index()
    {
	$this->_need_login(TRUE);

	$select = $this->input->get_post('selected');
	if (empty($select)) {
	    $this->_error(bp_operation_fail, '缺少参数,请重试');
	} else {
	    $selected = json_decode($select, true);
	    if (!is_array($selected) && !count($selected)) {
		$this->_error(bp_operation_fail, '请选择物流方式');
	    }
	}
    	$product = $this->input->get_post('products');
	if (empty($product)) {
            $this->_error(bp_operation_fail, '缺少参数,请重试');
        }
	$product = json_decode($product, true);
	if (is_array($product) && count($product)) {
	    $pids = array_column($product, 'cartid');
	    $pds = $this->count->getProductsByCartId($pids);
	    if (!is_array($pds) || !count($pds)) {
		$this->_error(bp_operation_fail, '获取商品数据失败，请重试');
	    }
            $products = $this->groupByField($pds, $product);
	    $this->config->load('rate', TRUE);
            $rates = $this->config->item('rates', 'rate');
            $this->config->load('delivery', TRUE);
            $ems = $this->config->item('ems', 'delivery');
            $fedex = $this->config->item('fedex', 'delivery');
            $bs = $this->config->item('bs', 'delivery');
            $ue = $this->config->item('UE', 'delivery');
            $this->config->load('weight', TRUE);
            $weights = $this->config->item('weights', 'weight');
    	    $result = ['res' => 0, 'hint' => '结算金额成功', 'total' => 0, 'price' => 0, 'fee' => 0, 'tax' => 0];
	    foreach ($products as $key => $value) {
		$shippings = $this->count->get_shipping($value['store_id']);
		if (is_array($shippings) && count($shippings)) {
		    // check store's sales promotion START
                    $value['cut'] = 0;
                    $value['cut_info'] = '';
                    $value['sales']['list'] = $value['sales']['type'] = [];
                    $this->load->model('store_model', 'store');
                    $sales = $this->store->getRecentSales($value['store_id']);
                    if (is_array($sales) && count($sales)) {
                        $value = $this->dealWithSales($value, $sales, $rates);
                    }
                    // check store's sales promotion END
    		    $result['hits'][$key] = $value;
    		    $result['hits'][$key]['store_id'] = $products[$key]['store_id'];
    		    $result['hits'][$key]['store_name'] = $products[$key]['store_name'];
    		    $result['price'] += $value['price'];
		    if (!empty($value['sales']['type']) && in_array(6, $value['sales']['type'])) {
                        $result['hits'][$key]['shipping'] = $this->addFreeDirectMail($shippings, $value, $rates, $weights, $bs, $ue);
                    } else {
    		    	$c = 0;
    		    	$commond = 0;
    		    	foreach ($shippings as $x => $y) {
    			    switch ($y['type']) {
    			    	case 1:
    				    if ($ship = $this->getFeeOne($y, $value, $rates, $weights)) {
				        $ship['store_id'] = $products[$key]['store_id'];
                                    	$ship['store_name'] = $products[$key]['store_name'];
                                    	$ship['is_commond'] = 0;
				    	$result['hits'][$key]['shipping'][] = $ship;
				    }
    				    break;
    			    	case 2:
				    if ($ship = $this->getFeeTwo($y, $value, $rates, $weights, $ems)) {
					$ship['store_id'] = $products[$key]['store_id'];
                                    	$ship['store_name'] = $products[$key]['store_name'];
                                    	$ship['is_commond'] = 0;
                                     	$result['hits'][$key]['shipping'][] = $ship;
                                    }
    				    break;
    			    	case 3:
				    if ($ship = $this->getFeeThree($y, $value, $rates, $weights, $ems)) {
					$ship['store_id'] = $products[$key]['store_id'];
                                    	$ship['store_name'] = $products[$key]['store_name'];
                                    	$ship['is_commond'] = 0;
                                    	$result['hits'][$key]['shipping'][] = $ship;
                                    }
    				    break;
    			    	case 4:
				    if ($ship = $this->getFeeFour($y, $value, $rates, $weights, $ue)) {
					$ship['store_id'] = $products[$key]['store_id'];
                                    	$ship['store_name'] = $products[$key]['store_name'];
                                    	$ship['is_commond'] = 0;
                                    	$result['hits'][$key]['shipping'][] = $ship;
                                    }
    				    break;
			    	case 5:
                                    if ($ship = $this->getFeeFive($y, $value, $rates, $weights, $fedex)) {
					$ship['store_id'] = $products[$key]['store_id'];
                                    	$ship['store_name'] = $products[$key]['store_name'];
                                    	$ship['is_commond'] = 0;
                                    	$result['hits'][$key]['shipping'][] = $ship;
                                    }
                                    break;
    			    	default:
    				    break;
    			    }
    		        }
		    	if (isset($result['hits'][$key]['shipping']) && is_array($result['hits'][$key]['shipping']) && count($result['hits'][$key]['shipping'])) {
			    $shipping_types = array_column($result['hits'][$key]['shipping'], 'shipping_type');
			    $luxuries = array_column($value['products'], 'is_luxury');
			    if (in_array(1, $luxuries) && !in_array(4, $shipping_types)) {
			   	if ($ship = $this->addLuxuryShipping($shippings, $value, $rates, $weights, $ue, $ue)) {
				    $ship['store_id'] = $products[$key]['store_id'];
                                    $ship['store_name'] = $products[$key]['store_name'];
                                    $ship['is_commond'] = 0;
				    $result['hits'][$key]['shipping'][] = $ship;
			   	}
			    }
			    $result = $this->clearStoreShippings($result, $key);
			    //$result = $this->getCommondShipping($result, $key);
		    	}
		    }
    		} else {
	    	    $this->_error(bp_operation_fail, '获取运费计算方式失败，请重试');
	    	}
	    }
	    $result['hits'] = array_merge($result['hits']);
	    $result = $this->setSelectedShipping($result, $selected);
	    $result['total'] += $result['price'];
	    $result['coupons'] = [];
	    $coupons = $this->getCouponList();
	    if (is_array($coupons) && count($coupons)) {
		$types = array_column($coupons, 'type');
		if (in_array('3', $types)) { 
		    $cates = $this->groupByField($pds, $product, 'category');
		    $coupons = $this->getAvailableCoupons($coupons, $result, $cates);
		} else {
		    $coupons = $this->getAvailableCoupons($coupons, $result);
		}
		$result['coupons'] = $this->setRecommondCoupons($coupons);
	    }
    	    exit(json_encode($result));
	} else {
	    $this->_error(bp_operation_fail, '参数错误，请重试');
   	}
    }

    private function dealWithSales($value, $sales, $rates)
    {
        $time = time();
        foreach ($sales as $t => $s) {
            if ((($s['end_at'] == 0) && ($time > $s['start_at'])) || (($time > $s['start_at']) && ($time < $s['end_at']))) {
                switch ($s['type']) {
                    case 1:
                        $options = $this->store->getSalesOptions($s['id']);
                        $value = $this->checkOptionsFee($options, $value, $s, $rates, 'money');
                        break;
                    case 2:
                        $options = $this->store->getSalesOptions($s['id']);
                        $value = $this->checkOptionsFee($options, $value, $s, $rates);
                        break;
                    case 3:
                        $options = $this->store->getSalesOptions($s['id']);
                        $value = $this->checkOptionsCount($options, $value, $s, $rates);
                        break;
                    case 4:
                        $options = $this->store->getSalesOptions($s['id']);
                        $value = $this->checkOptionsCount($options, $value, $s, $rates, 'money');
                        break;
                    case 5:
                        $options = $this->store->getSalesOptions($s['id']);
                        $value = $this->checkOptionsFee($options, $value, $s, $rates, 'LDF');
                        break;
                    case 6:
                        $options = $this->store->getSalesOptions($s['id']);
                        $value = $this->checkOptionsFee($options, $value, $s, $rates, 'DM');
                        break;
                    case 7:
                        $value = $this->checkOptionsNum($value, $s);
                        break;
                    case 8:
                        $value = $this->checkOptionsCutCheap($value, $s);
                        break;
                    case 9:
			$value = $this->checkOptionsNum($value, $s, 'half');
                        break;
                }
            }
        }

        return $value;
    }

    private function checkOptionsFee($options, $value, $s, $rates, $type='discount')
    {
        foreach ($options as $k => $v) {
            if ($v['status'] == 1) {
                $rate = $rates[$v['currency']];
                if ((($v['upper'] == 0) && ($value['product_price'] >= ($v['lower'] * $rate))) || (($value['product_price'] >= ($v['lower'] * $rate)) && ($value['product_price'] < ($v['upper'] * $rate)))) {
                    if ($type == 'discount') {
                        $cut = floor($value['product_price'] * (1 - $v['promotion']));
                        $ty = 2;
                    } elseif ($type == 'money') {
                        $ty = 1;
                        $cut = floor($v['promotion'] * $rate);
                    } elseif ($type == 'LDF') {
                        $cut = 0;
                        $ty = 5;
                    } elseif ($type == 'DM') {
                        $ty = 6;
                        $cut = 0;
                    }
                    $value['sales']['list'][] = array(
                        'cut' => $cut,
                        'type' => $ty,
                        'sales_id' => $s['id'],
                        'options_id' => $v['id'],
                        'sales_name' => $s['name'],
                        'end_at' => $s['end_at'],
                        'is_available' => 1,
                        'store_id' => $value['store_id'],
                        'store_name' => $value['store_name']
                    );
                    $value['sales']['type'][] = $ty;
                    $value['cut'] += $cut;
                    $value['cut_info'] .= ' '.$s['name'];
                }
            }
        }

	return $value;
    }

    private function checkOptionsCount($options, $value, $s, $rates, $type='discount')
    {
        foreach ($options as $k => $v) {
            if ($v['status'] == 1) {
                if ((($v['upper'] == 0) && ($value['num'] >= $v['lower'])) || (($value['num'] >= $v['lower']) && ($value['num'] < $v['upper']))) {
                    if ($type == 'discount') {
                        $ty = 3;
                        $cut = floor($value['product_price'] * (1 - $v['promotion']));
                    } else {
                        $ty = 4;
                        $rate = $rates[$v['currency']];
                        $cut = floor($v['promotion'] * $rate);
                    }
                    $value['sales']['list'][] = array(
                        'cut' => $cut,
                        'type' => $ty,
                        'sales_id' => $s['id'],
                        'options_id' => $v['id'],
                        'sales_name' => $s['name'],
                        'end_at' => $s['end_at'],
                        'is_available' => 1,
                        'store_id' => $value['store_id'],
                        'store_name' => $value['store_name']
                    );
                    $value['sales']['type'][] = $ty;
                    $value['cut'] += $cut;
                    $value['cut_info'] .= ' '.$s['name'];
                }
            }
        }

        return $value;
    }

    private function checkOptionsNum($value, $s, $type='freeOne')
    {
        if ($value['num'] >= 2) {
            $cut['half'] = $cut['cut'] = 0;
            foreach ($value['products'] as $k => $v) {
                if ($v['num'] >=2) {
                    $cut['half'] += floor($v['price'] * 0.5);
                    $cut['cut'] += $v['price'];
                }
            }
            if ($type == 'half') {
                $ty = 9;
                $cut = $cut['half'];
            } else {
                $ty = 7;
                $cut = $cut['cut'];
            }
            $value['sales']['list'][] = array(
                'cut' => $cut,
                'type' => $ty,
                'sales_id' => $s['id'],
                'options_id' => 0,
                'sales_name' => $s['name'],
                'end_at' => $s['end_at'],
                'is_available' => 1,
                'store_id' => $value['store_id'],
                'store_name' => $value['store_name']
            );
            $value['sales']['type'][] = $ty;
            $value['cut'] += $cut;
            $value['cut_info'] .= ' '.$s['name'];
        }

        return $value;
    }

    private function checkOptionCutCheap($value, $s)
    {
        if ($value['num'] >= 2) {
            $price = array_column($value['products'], 'price');
            sort($price);
            $value['sales']['list'][] = array(
                'cut' => $price[0],
                'type' => $s['type'],
                'sales_id' => $s['id'],
                'options_id' => 0,
                'sales_name' => $s['name'],
                'end_at' => $s['end_at'],
                'is_available' => 1,
                'store_id' => $value['store_id'],
                'store_name' => $value['store_name']
            );
            $value['sales']['type'][] = $s['type'];
            $value['cut'] += $price[0];
            $value['cut_info'] .= ' '.$s['name'];
        }

        return $value;
    }

    private function addFreeDirectMail($sps, $store, $rates, $weights, $bs, $ue)
    {
        $count = array(
                    'store_shipping_name' => '直邮中国（商城活动）',
                    'shipping_id'   => '0',
                    'shipping_type' => '7',
                    'shipping_name' => '官网直邮',
                    'shipping_days' => '10-15',
                    'tax_type'      => 3,
                    'tax_name'      => '海关短信通知，用户自缴',
                    'tax_fee'       => 0,
                    'tax_fee_info'  => '抽查（）',
		    'shipping_fee'  => 0,
		    'store_promotion_mail_fee' => 0,
                    'shipping_fee_info'  => '（物流由海外商家提供）',
                    'is_commond'    => 1
        );

        return $count;
    }

    private function CompleteProducts($pids, $pts)
    {
	$pds = [];
	foreach ($pids as $id) {
	    foreach ($pts as $k => $v) {
		if ($id == $v['id']) {
		    $pds[] = $v;
		    break;
		}
	    }
	}

	return $pds;
    }

    private function setRecommondCoupons($coupons)
    {
	$cp = [
	    'available' => [],
	    'unavailable' => [],
	    'no_exclusive_price' => 0
	];
	foreach ($coupons as $k => $v) {
	    $v['is_recommond'] = 0;
	    if ($v['is_available'] == 1) {
		$cp['available'][] = $v;
		if ($v['is_exclusive'] == 1) {
		    if (isset($cp['recommond'])) {
			if ($v['value'] > $cp['recommond']['value']) {
			    $cp['recommond'] = $v;
			}
		    } else {
			$cp['recommond'] = $v;
		    }
		} else {
		    $cp['no_exclusive'][] = $v;
		    $cp['no_exclusive_price'] += $v['value'];
	        }
	    } else {
		$cp['unavailable'][] = $v;
	    }
	}
	if (!isset($cp['recommond'])) {
            $cp['recommond']['value'] = 0;
            $cp['recommond']['id'] = 0;
        }
	if ($cp['no_exclusive_price'] >= $cp['recommond']['value']) {
	    foreach ($cp['available'] as $k => $v) {
		foreach ($cp['no_exclusive'] as $x => $y) {
		    if ($v['id'] == $y['id']) {
			$cp['available'][$k]['is_recommond'] = 1;
			break;
		    }
		}
	    }
	} else {
	   foreach ($cp['available'] as $k => $v) {
		if ($v['id'] == $cp['recommond']['id']) {
		    $cp['available'][$k]['is_recommond'] = 1;
                    break;
		}
	   } 
	}
	unset($cp['recommond']);
	unset($cp['no_exclusive']);
	unset($cp['no_exclusive_price']);
	
	return $cp;
    }

    private function getAvailableCoupons($coupons, $result, $cates = [])
    {
	foreach ($coupons as $k => $v) {
	    if ($v['is_available'] == 1) {
	    	switch ($v['type']) {
		    case 1:
		    	$coupons[$k] = $this->checkAllAvailable($v, $result['price']);
		    	break;
		    case 2:
		    	$coupons[$k] = $this->checkStoreAvailable($v, $result['hits']);
                    	break;
		    case 3:
                        $coupons[$k] = $this->checkCategoryAvailable($v, $cates);
                        break;
		}
	    }
	}
	
	return $coupons;
    }

    private function checkAllAvailable($coupon, $price)
    {
	if ($price >= $coupon['limit']) {
	    $coupon['is_available'] = 1;
	} else {
	    $coupon['is_available'] = 0;
	}

	return $coupon;
    }

    private function checkStoreAvailable($coupon, $stores)
    {
	$coupon['is_available'] = 0;
	foreach ($stores as $k => $v) {
	    if (($v['store_id'] == $coupon['store']) && ($v['price'] >= $coupon['limt'])) {
		$coupon['is_available'] = 1;
		break;		
	    }
	}

	return $coupon;
    }

    private function checkCategoryAvailable($coupon, $cates)
    {
        $coupon['is_available'] = 0;
        foreach ($cates as $k => $v) {
            if (($v['category'] == $coupon['category']) && ($v['price'] >= $coupon['limt'])) {
                $coupon['is_available'] = 1;    
                break;
            }
        }

	return $coupon;
    }

    private function addLuxuryShipping($sps, $store, $rates, $weights, $bs, $ue)
    {
        $count = array(
                    'store_shipping_name' => '包邮包税（奢侈品，智能推荐）',
                    'shipping_id'   => 'a',
                    'shipping_type' => '6',
                    'shipping_name' => '包邮包税',
                    'shipping_days' => '20-30',
                    'tax_type'      => 3,
                    'tax_name'      => '海关短信通知，用户自缴',
                    'tax_fee'       => 0,
                    'tax_fee_info'  => '抽查（youhot凭税单报销）',
		    'store_id' => $store['store_id'],
                    'store_name' => $store['store_name']
        );
        $store_fee = $this->getStoreCommondFee($sps, $store, $rates, $weights);
        $express_fee = $this->getExpressFee($rates, $weights, $store, $bs);
	$local_tax = $store['price'] * self::TAX;

        if (isset($store_fee) && isset($express_fee) && isset($local_tax)) {
	    $count['shipping_fee'] = $count['store_shipping_fee'] = ceil($store_fee);
            $count = $this->clearIncludingMailFee($count, $store);
            $store_fee = $count['shipping_fee'];
            $count['shipping_fee'] = ceil($store_fee + $express_fee + $local_tax);
	    $count['shipping_fee_info'] = '';
            return $count;
        }
        return false;
    }

    private function getStoreCommondFee($shippings, $store, $rates, $weights)
    {
	foreach ($shippings as $x => $y) {
	    $rate = $rates[$y['currency']];
	    if ($y['type'] == 2) {
		$fee = $this->getStoreFee($y, $store, $rate, $weights);	
		if (isset($fee)) {
		    return $fee;
		}
	    }
	    if ($y['type'] == 3) {
		$fee = $this->getStoreFee($y, $store, $rate, $weights);
                if (isset($fee)) {
                    return $fee;
                }
	    }
	}
	return false;
    }

    private function clearStoreShippings($result, $key)
    {
	$shipping_types = array_column($result['hits'][$key]['shipping'], 'shipping_type');
	if (in_array(5, $shipping_types)) {
	    return $this->clearShipping([2,3], $result, $key);
	}
	if (in_array(2, $shipping_types)) {
            return $this->clearShipping([3], $result, $key);
        }
	return $result;
    }

    private function clearShipping($indexes, $result, $key)
    {
	foreach ($result['hits'][$key]['shipping'] as $k => $sp) {
	    if (in_array($sp['shipping_type'], $indexes)) {
		unset($result['hits'][$key]['shipping'][$k]);
	    }
        }
	return $result;
    }

    private function getCommondShipping($result, $key)
    {
	$shipping_types = array_column($result['hits'][$key]['shipping'], 'shipping_type');
	if (in_array(1, $shipping_types)) {
	    return $this->setCommondShipping(1, $result, $key);
	}
	if (in_array(4, $shipping_types)) {
            return $this->setCommondShipping(4, $result, $key);
        }
	if (in_array(6, $shipping_types)) {
	    return $this->setCommondShipping(6, $result, $key);
     	}
	if (in_array(5, $shipping_types)) {
            return $this->setCommondShipping(5, $result, $key);
        }
        if (in_array(2, $shipping_types)) {
            return $this->setCommondShipping(2, $result, $key);
        }
        if (in_array(3, $shipping_types)) {
            return $this->setCommondShipping(3, $result, $key);
        }
    }

    private function setCommondShipping($index, $result, $key)
    {
	foreach ($result['hits'][$key]['shipping'] as $k => $sp) {
            if ($sp['shipping_type'] == $index) {
               $result['hits'][$key]['shipping'][$k]['is_commond'] = 1;
               $result['fee'] += $sp['shipping_fee'];
               $result['tax'] += $sp['tax_fee'];
               $result['total'] += $sp['shipping_fee'] + $sp['tax_fee'];
            } else {
               $result['hits'][$key]['shipping'][$k]['is_commond'] = 0;
            }
        }
	return $result;
    }

   private function groupByField($pds, $products, $field = 'store')
   {
	$st = [];
	foreach ($products as $k => $v) {
	    foreach ($pds as $x => $y) {
		if ($v['cartid'] == $y['cartid']) {
		    $keys = array_keys($st);
		    $merge = array_merge($y, $v);
		    $merge['is_luxury'] = 0; // close is_luxury
		    if (!in_array($merge[$field], $keys)) {
			$st[$merge[$field]][$field] = $merge[$field];
			if ($field == 'store') {
			    $st[$merge[$field]]['store_name'] = $merge['store_name'];
			    $st[$merge[$field]]['store_id'] = $merge[$field];
		 	}
			$st[$merge[$field]]['price'] = 0;
                        $st[$merge[$field]]['weight'] = 0;
                        $st[$merge[$field]]['tax'] = 0;
                        $st[$merge[$field]]['num'] = 0;
	 		$st[$merge[$field]]['product_price'] = 0;
    			$st[$merge[$field]]['including_mfee'] = 0;
		    }
		    $merge['tax'] = $merge['tax_rate'] * $merge['price'];
                    $st[$merge[$field]]['products'][] = $merge;
                    $st[$merge[$field]]['price'] += ($merge['price'] * $merge['num']);
                    $st[$merge[$field]]['weight'] += ($merge['weight'] * $merge['num']);
                    $st[$merge[$field]]['tax'] += ($merge['tax_rate'] * $merge['price'] * $merge['num']);
                    $st[$merge[$field]]['num'] += $merge['num'];
		    $st[$merge[$field]]['product_price'] += ($merge['pdt_price'] * $merge['num']);
                    $st[$merge[$field]]['including_mfee'] += ($merge['including_mfee'] * $merge['num']);
		    break;
		}
	    }
	}

	return $st;
   }

    private function getFeeOne($y, $store, $rates, $weights)
    {
    	$rate = $rates[$y['currency']];
    	$count = array(
		    'store_shipping_name' => '官网直邮',//'邮政速递',
		    'shipping_id'   => $y['id'],
		    'shipping_type' => $y['type'],
		    'shipping_name' => '官网直邮',//'邮政速递',
		    'shipping_days' => $y['days'],
		    'tax_type'      => 1,
		    'tax_name'      => '海关短信通知，用户自缴',
		    'tax_fee'       => 0,
		    'tax_fee_info'      => '抽查（化妆品60%，其他30%）',
		    'store_id' => $store['store_id'],
                    'store_name' => $store['store_name']
        );
	$count['shipping_fee'] = $this->getStoreFee($y, $store, $rate, $weights);

	if (isset($count['shipping_fee'])) {
	    $count['shipping_fee'] = $count['store_shipping_fee'] = ceil($count['shipping_fee']);
	    $count = $this->clearIncludingMailFee($count, $store);
	    $count['shipping_fee_info'] = '（官网收取）';
	    return $count;
	}
	return false;
   }

   private function clearIncludingMailFee($count, $store)
   {
	$cha = $count['shipping_fee'] - $store['including_mfee'];
	if ($cha >= 0) {
	    $count['shipping_fee'] = $cha;
	    $count['store_promotion_mail_fee'] = 0;
	} else {
	    $count['shipping_fee'] = 0;
	    $count['store_promotion_mail_fee'] = abs($cha);
	}

        return $count;
   }

   private function getStoreFee($y, $store, $rate, $weights)
   {
	/*if (in_array(5, $store['sales']['type'])) {
            return 0;
        }*/
	if ($y['count_type'] == 3) {
            $wei = $weights[$y['count_unit']];
            if ((($y['high'] == 0) && (($store['weight']*$wei) > $y['low'])) || (($y['high'] != 0) && (($store['weight']*$wei) >= $y['low']) && (($store['weight']*$wei) < $y['high']))) {
                $store_fee  = $rate*$y['low_fee'];
            }
        } elseif ($y['count_type'] == 2) {
            if ((($y['high'] == 0) && ($store['num'] > $y['low'])) || (($y['high'] != 0) && ($store['num'] >= $y['low']) && ($store['num'] <= $y['high']))) {
                $store_fee  = $rate*($y['base_fee'] + $y['low_fee'] * ($store['num'] - $y['low']));
            }
        } else {
            if ((($y['high'] == 0) && ($store['product_price'] > ($rate*$y['low']))) || (($y['high'] != 0) && ($store['product_price'] >= ($rate*$y['low'])) && ($store['product_price'] <= ($rate*$y['high'])))) {
                $store_fee  = $rate*$y['low_fee'];
            }
        }

	if (isset($store_fee)) {
	    if (in_array(5, $store['sales']['type'])) {
            	return 0;
            }
	    return $store_fee;
	}
	return null;
   }

   // MiddleMan Service Fee
   private function getMiddleManFee($rates)
   {
	return $rates['USD'] * self::MIDDLE_FEE;
   }

   // Express Transport Fee
   private function getExpressFee($rates, $weights, $store, $express)
   {
	$wei = $weights[$express['count_unit']];

	if (($store['weight'] * $wei) <= $express['weight']) {
	    return $rates[$express['currency']] * $express['first'];
	} else {
	    return $rates[$express['currency']] * ( $express['first'] + $express['per'] * ceil(($store['weight'] * $wei - $express['weight']) / $express['per_weight']));
	}
   }

   // Store Service, Transfer: DHL,FeDex
   private function getFeeFive($y, $store, $rates, $weights, $fedex)
   {
        $rate = $rates[$y['currency']];
        $count = array(
                    'store_shipping_name' => '省心转运（税费代缴）',
                    'shipping_id'   => $y['id'],
                    'shipping_type' => $y['type'],
                    'shipping_name' => '省心转运',
                    'shipping_days' => $y['days'],
                    'tax_type'      => 2,
                    'tax_name'      => '代缴税费,请于地址栏处提供身份信息',
                    'tax_fee'       => $y['tax'],
                    'tax_fee_info'      => '100%缴税（化妆品30%，其他11.9%）',
		    'store_id' => $store['store_id'],
                    'store_name' => $store['store_name']
        );
        $store_fee = $this->getStoreFee($y, $store, $rate, $weights);
	$express_fee = $this->getExpressFee($rates, $weights, $store, $fedex);

        if (isset($store_fee) && isset($express_fee)) {
	    $count['shipping_fee'] = $count['store_shipping_fee'] = ceil($store_fee);
            $count = $this->clearIncludingMailFee($count, $store);
	    $store_fee = $count['shipping_fee'];
            $count['shipping_fee'] = ceil($store_fee + $express_fee);
            $count['shipping_fee_info'] = '（官网¥'.ceil($store_fee).', 转运¥'.ceil($express_fee).'）';
            return $count;
        }
        return false;
   }

   // PortLand MiddleMan Service
   private function getFeeThree($y, $store, $rates, $weights, $ue)
   {
        $rate = $rates[$y['currency']];
        $count = array(
                    'store_shipping_name' => '省心转运（PortLand Little Brother）',
                    'shipping_id'   => $y['id'],
                    'shipping_type' => $y['type'],
                    'shipping_name' => '省心转运',
                    'shipping_days' => $y['days'],
                    'tax_type'      => 1,
                    'tax_name'      => 'youhot接海关通知协助用户代缴',//'海关短信通知，用户自缴',
                    'tax_fee'       => 0,
                    'tax_fee_info'      => '抽查（化妆品60%，其他30%）',
		    'store_id' => $store['store_id'],
                    'store_name' => $store['store_name']
        );
        $store_fee = $this->getStoreFee($y, $store, $rate, $weights);
        // $express_fee = $this->getExpressFee($rates, $weights, $store, $ems);
	$mm_fee = $this->getMiddleManFee($rates);
	$local_express = $this->getExpressFee($rates, $weights, $store, $ue);

        if (isset($store_fee) && isset($mm_fee) && isset($local_express)) {
	    $count['shipping_fee'] = $count['store_shipping_fee'] = ceil($store_fee);
            $count = $this->clearIncludingMailFee($count, $store);
	    $store_fee = $count['shipping_fee'];
            $count['shipping_fee'] = ceil($store_fee + $mm_fee + $local_express);
            $count['shipping_fee_info'] = '（官网¥'.ceil($store_fee).', 转运¥'.ceil($mm_fee + $local_express).'）';
            return $count;
        }
        return false;
   }

   // Tax Gross-Up Service
   private function getFeeFour($y, $store, $rates, $weights, $bs)
   {
        $rate = $rates[$y['currency']];
        $count = array(
                    'store_shipping_name' => '包邮包税（Miami line）',
                    'shipping_id'   => $y['id'],
                    'shipping_type' => $y['type'],
                    'shipping_name' => '包邮包税',
                    'shipping_days' => $y['days'],
                    'tax_type'      => 3,
                    'tax_name'      => '海关短信通知，用户自缴',
                    'tax_fee'       => 0,
                    'tax_fee_info'      => '抽查（youhot凭税单报销）',
		    'store_id' => $store['store_id'],
                    'store_name' => $store['store_name']
        );
        $store_fee = $this->getStoreFee($y, $store, $rate, $weights);
        $express_fee = $this->getExpressFee($rates, $weights, $store, $bs);
        // $mm_fee = $this->getMiddleManFee($rates);
	// $local_express = $this->getExpressFee($rates, $weights, $store, $ue);
	$local_tax = $store['price'] * self::TAX;

        if (isset($store_fee) && isset($express_fee) && isset($local_tax)) {
	    $count['shipping_fee'] = $count['store_shipping_fee'] = ceil($store_fee);
            $count = $this->clearIncludingMailFee($count, $store);
	    $store_fee = $count['shipping_fee'];
            $count['shipping_fee'] = ceil($store_fee + $express_fee + $local_tax);
            //$count['shipping_fee_info'] = '海外商城收取: '.ceil($store_fee).', 转运公司收取：'.ceil($express_fee + $local_tax);
	    $count['shipping_fee_info'] = '';
            return $count;
        }
        return false;
   }

   // Store Transfer Service
   private function getFeeTwo($y, $store, $rates, $weights, $ems)
   {
        $rate = $rates[$y['currency']];
        $count = array(
                    'store_shipping_name' => '省心转运（商城转运）',
                    'shipping_id'   => $y['id'],
                    'shipping_type' => $y['type'],
                    'shipping_name' => '省心转运',
                    'shipping_days' => $y['days'],
                    'tax_type'      => 1,
                    'tax_name'      => 'youhot接海关通知协助用户代缴',//'海关短信通知，用户自缴',
                    'tax_fee'       => 0,
                    'tax_fee_info'  => '抽查（化妆品60%，其他30%）',
		    'store_id' => $store['store_id'],
                    'store_name' => $store['store_name']
        );
        $store_fee = $this->getStoreFee($y, $store, $rate, $weights);
        $express_fee = $this->getExpressFee($rates, $weights, $store, $ems);

        if (isset($store_fee) && isset($express_fee)) {
	    $count['shipping_fee'] = $count['store_shipping_fee'] = ceil($store_fee);
            $count = $this->clearIncludingMailFee($count, $store);
	    $store_fee = $count['shipping_fee'];
            $count['shipping_fee'] = ceil($store_fee + $express_fee);
            $count['shipping_fee_info'] = '（官网¥ '.ceil($store_fee).', 转运¥'.ceil($express_fee).'）';
            return $count;
        }
        return false;
   }

    public function getCouponList()
    {
	$this->load->model('coupon_model', 'coupon');	
        $list = $this->coupon->myList($this->userid, $this->_un);
        if (is_array($list) && count($list)) {
            foreach ($list as $k => $v) {
                if (($v['use_at'] <= time()) && (time() <= $v['use_end']) && empty($v['used_at'])) {
                    $list[$k]['is_available'] = 1;
                } else {
                    $list[$k]['is_available'] = 0;
                }
		$list[$k]['is_selected'] = 0;
            }
        }

        return $list;
    }

    private function setSelectedShipping($res, $selected)
    {
        $selected = array_unique($selected);
        $selected = array_filter($selected);
        foreach ($selected as $v) {
            foreach ($res['hits'] as $x => $y) {
                foreach ($y['shipping'] as $m => $n) {
                    if ($v == $n['shipping_id']) {
                        foreach ($y['shipping'] as $p => $q) {
                            $res['hits'][$x]['shipping'][$p]['is_commond'] = 0;
                            if ($v == $q['shipping_id']) {
                                //print_r($q['shipping_fee']);die;
                                $res['hits'][$x]['shipping'][$p]['is_commond'] = 1;
                                $res['fee'] += $q['shipping_fee'];
                                $res['tax'] += $q['tax_fee'];
                                $res['total'] += $q['shipping_fee'] + $q['tax_fee'];
                            }
                        }
                        break;
                    }
                }
            }
        }

        return $res;
    }
}
