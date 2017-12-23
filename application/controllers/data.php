<?php
/**
 * Class Data
 * @method
 */
class Data extends User_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('data_model', 'data');
    }

    public function get_data()
    {
        $res['res'] = 0;
        $res['deal'] = $this->get_deal_pay_users();
        $res['register'] = $this->get_register_users();
        $res['upv'] = $this->get_upv();
        $res['user'] = $this->count_user($res['register']);
        echo_json($res);
    }

    private function get_deal_pay_users() // 每月30天
    {
        $data = $this->data->get_deal_pay_users();
        $max = count($data);
        $day = date('d', time());
        $new_data = $data;
        for ($i=0;$i<$day;$i++) {
            if ($max >= 1) {
                $start = strtotime(date('Y-m-d', time()-$i*86400));
                $end = $start + 86400;
                $sum = 0;
                foreach ($data as $da) {
                    if ($da['timestamp'] >= $start && $da['timestamp'] < $end) {
                        break;
                    } else {
                        $sum++;
                        if ($sum >= $max) {
                            $now_day = $day-$i;
                            $date = date('Y-m', time()).'-'.$now_day;
                            $new = $this->get_new_users($date);
                            $this->data->insert_data($new);
                            array_unshift($new_data, $new);
                        }
                    }
                }
            } else {
                $now_day = $day-$i;
                $date = date('Y-m', time()).'-'.$now_day;
                $new = $this->get_new_users($date);
                $this->data->insert_data($new);
                array_unshift($new_data, $new);
            }
        }
        $res = array();
        foreach ($new_data as $d) {
            $res['order'][] = date('d', $d['timestamp']);
            $res['deal'][] = (int)$d['deal'];
            $res['pay'][] = (int)$d['pay'];
        }

        return $res;
    }
    private function get_rand($x = 0, $y = 1, $num = 1)
    {
        $res = 0;
        for ($i=1;$i<=$num;$i++) {
            $r = rand(0,9);
            $n = $r/pow(10, $i);
            $res += $n;
        }
        if (($y-$x) == 1) {
            $res += $x;
        } else {
            $res += rand($x, $y-1);
        }
        return $res;
    }
    private function get_new_users($date = false)
    {
        $rand = $this->get_rand();
        while ($rand == 0) {
            $rand = $this->get_rand();
        }
        $new['deal'] = rand(0,10)*$rand;
        $new['pay'] = $new['deal']*$rand;
        if (!$date) {
            $new['timestamp'] = time();
        } else {
            $new['timestamp'] = strtotime($date);
        }
        return $new;
    }
    private function get_register_users() // 昨天今日48小时
    {
        $data = $this->data->get_register_users();
        $max = count($data);
        // echo '<pre>';
        // var_dump($data);die;
        $hour = 24 + (int)date('H', time());
        $new_data = $data;
        for ($i=0;$i<$hour;$i++) {
            // echo 'I:'.$i.'<br>';
            if ($max >= 1) {
                $start = strtotime(date('Y-m-d', time()-($i*3600)).' '.date('H', time()-($i*3600)).':0:0');
                $end = $start + 3600;
                $sum = 0;
                // echo 'Start:'.$start.' End;'.$end.'<br>';
                foreach ($data as $da) {
                    if ($da['timestamp'] >= $start && $da['timestamp'] < $end) {
                        break;
                    } else {
                        $sum++;
                        if ($sum >= $max) {
                            $now_hour = $hour-$i;
                            if ($now_hour >= 24) {
                                $now_hour -= 24;
                                $date = date('Y-m-d', time()).' '.$now_hour.':0:0';
                            } else {
                                $date = date('Y-m-d', time()-86400).' '.$now_hour.':0:0';
                            }
                            // echo 'Date:'.$date.'<br>';
                            $new = $this->get_new_register($date);
                            $this->data->insert_data($new, 'new_register');
                            array_unshift($new_data, $new);
                        }
                    }
                }
            } else {
                $now_hour = $hour-$i;
                if ($now_hour >= 24) {
                    $now_hour -= 24;
                    $date = date('Y-m-d', time()).' '.$now_hour.':0:0';
                } else {
                    $date = date('Y-m-d', time()-86400).' '.$now_hour.':0:0';
                }
                $new = $this->get_new_register($date);
                $this->data->insert_data($new, 'new_register');
                array_unshift($new_data, $new);
            }
        }
        $res = array();
        foreach ($new_data as $k => $d) {
            if ($k<24) {
                $res['order'][] = $k;
                $res['yestoday'][] = (int)$d['new'];
            } else {
                $res['today'][] = (int)$d['new'];
                $res['active'][] = (int)$d['active'];
                $res['start'][] = (int)$d['start'];
                if ($k >= 48) break;
            }
        }

        return $res;
    }
    private function get_new_register($date = false)
    {
        $rand = $this->get_rand();
        while ($rand == 0) {
            $rand = $this->get_rand();
        }
        $new['new'] = rand(10,50)*$rand;
        $new['active'] = $new['new']*rand(10,99);
        $new['start'] = $new['new']*rand(1,3);
        if (!$date) {
            $new['timestamp'] = time();
        } else {
            $new['timestamp'] = strtotime($date);
        }
        return $new;
    }
    private function get_upv() //uv/pv 每半小时
    {
        $data = $this->data->get_upv();
        $max = count($data);
        // echo '<pre>';
        // var_dump($data);die;
        $halfHour = (int)date('H', time())*2;
        $new_data = $data;
        for ($i=0;$i<$halfHour;$i++) {
            // echo 'I:'.$i.'<br>';
            if ($max >= 1) {
                if (date('i', time()-$i*1800) >=30) {
                    $start = strtotime(date('Y-m-d', time()-($i*1800)).' '.date('H', time()-($i*1800)).':30:0');
                } else {
                    $start = strtotime(date('Y-m-d', time()-($i*1800)).' '.date('H', time()-($i*1800)).':0:0');
                }
                $end = $start + 1800;
                $sum = 0;
                // echo 'Start:'.$start.' End;'.$end.'<br>';
                foreach ($data as $da) {
                    if ($da['timestamp'] >= $start && $da['timestamp'] < $end) {
                        break;
                    } else {
                        $sum++;
                        if ($sum >= $max) {
                            $now_hour = ($halfHour-$i)%2;
                            if ($now_hour == 0) {
                                $now_hour = ($halfHour-$i)/2;
                                $date = date('Y-m-d', time()).' '.$now_hour.':0:0';
                            } else {
                                $now_hour = ($halfHour-$i-1)/2;
                                $date = date('Y-m-d', time()).' '.$now_hour.':30:0';
                            }
                            // echo 'Date:'.$date.'<br>';
                            $new = $this->get_new_upv($date);
                            $this->data->insert_data($new, 'upv');
                            array_unshift($new_data, $new);
                        }
                    }
                }
            } else {
                $now_hour = ($halfHour-$i)%2;
                if ($now_hour == 0) {
                    $now_hour = ($halfHour-$i)/2;
                    $date = date('Y-m-d', time()).' '.$now_hour.':0:0';
                } else {
                    $now_hour = ($halfHour-$i-1)/2;
                    $date = date('Y-m-d', time()).' '.$now_hour.':30:0';
                }
                // echo 'Date:'.$date.'<br>';
                $new = $this->get_new_upv($date);
                $this->data->insert_data($new, 'upv');
                array_unshift($new_data, $new);
            }
        }
        $res = array();
        foreach ($new_data as $k => $d) {
            $res['order'][] = $k;
            $res['uv'][] = (int)$d['uv'];
            $res['pv'][] = (int)$d['pv'];
        }

        return $res;
    }
    private function get_new_upv($date = false)
    {
        $rand = $this->get_rand();
        while ($rand == 0) {
            $rand = $this->get_rand();
        }
        $new['uv'] = rand(1,15)*$rand;
        $new['pv'] = $new['uv']*rand(5,20);
        if (!$date) {
            $new['timestamp'] = time();
        } else {
            $new['timestamp'] = strtotime($date);
        }
        return $new;
    }
    private function count_user($register)
    {
        $data = array(
            'new_reg' => 0,
            'active' => 0,
            'starts' => 0
        );
        foreach ($register['today'] as $k => $r) {
            $data['new_reg'] += $r;
            $data['active'] += $register['active'][$k];
            $data['starts'] += $register['start'][$k];
        } 
        $data['allusers'] = $this->get_all_users();
        return $data;
    }
    private function get_all_users()
    {
        $count = 0;
        $data = $this->data->get_all_users();
        foreach ($data as $k => $d) {
            $count += $d['new'];
        }

        return $count;
    }

    // insert to mongo data
    public function addData()
    {
	$data = $this->input->get_post('data');
	$data = json_decode($data, true);	
	if (is_array($data) && count($data)) {
   	    //logger('data: '.var_export($data,true));
	    $data['ip'] = $_SERVER['REMOTE_ADDR'];
	    $data['userid'] = empty($this->userid) ? 0 : $this->userid;
	    $data['session_id'] = $this->session->userdata('session_id');
	    $res = httPost('http://114.55.40.32/index.php/api/data/add', ['data' => json_encode($data)]);	    
	    exit($res);
        } else {
	    exit(json_encode(['res' => 4, 'hit' => '信息不能为空'])); 
        } 	
    }
}
