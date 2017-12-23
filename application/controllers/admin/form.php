<?php
class Form extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('form_model', 'form');
    }
    
    public function index()
    {
	$this->template('admin/form/index', $this->_data);	
    }

    public function export()
    {
        $date = $this->input->get_post('date');
        if (strtotime($date) > time()) {
            exit(json_encode(['res'=>1,'hits'=>'date is error!']));
        }
        $all_deals = $this->form->get_all($date);
        if (is_array($all_deals) && count($all_deals)) {
	    $deals = [];
	    foreach ($all_deals as $k => $v) {
		$deals[$v['order_id']] = $v;
	    }
	    $deals = generateTree($deals, 'pid', 'order_id');
 	    //echo '<pre>'; print_r($deals);die;
            $str = "order_id,pid,status,product_id,product_price,product_count,cost,rebate,last_paid_money,";
	    $str .= "freight,tax,last_pay_coupon_value,promotion_value,last_payment,last_paid_time,buyer_userid";
	    $str .= ",last_paid_info,create_time,store,store_name,rebate_rate\n";
            foreach ($deals as $k => $v) {
		if (isset($v['son']) && count($v['son'])) {
		    if ($v['last_paid_money'] > 0) {
		        $rebate = $cost = 0;
		        foreach ($v['son'] as $m => $n) {
			    $son_rebate = empty($n['rebate_value']) ? $n['product_price'] * $n['product_count'] * 0.1 : $n['rebate_value'];
			    $son_cost = empty($n['cost']) ? $n['product_price'] * $n['product_count'] * 0.9 : $n['cost'];
			    $rebate += $son_rebate;
			    $cost += $son_cost;
			    if ($n['last_paid_money'] > 0) {
		                $str .= $n['order_id'].",".$n['pid'].",".$n['status'].",".$n['product_id'].",".$n['product_price'].",".$n['product_count'].",".$son_cost.",".$son_rebate;
			        $str .= ",".$n['last_paid_money'].",".$n['freight'].",".$n['tax'].",".$n['last_pay_coupon_value'].",".$n['promotion_value'].",".$n['last_payment'];
		                $str .= ",".$n['last_paid_time'].",".$n['buyer_userid'].",".$n['last_paid_payinfo'].",".$n['create_time'].",".$n['store'].",".$n['show_name'].",".$n['rebate']."\n";
			    }
		        }
		        $str .= $v['order_id'].",".$v['pid'].",".$v['status'].",".$v['product_id'].",".$v['product_price'].",".$v['product_count'].",".$cost.",".$rebate;
		        $str .= ",".$v['last_paid_money'].",".$v['freight'].",".$v['tax'].",".$v['last_pay_coupon_value'].",".$v['promotion_value'].",".$v['last_payment'];
		        $str .= ",".$v['last_paid_time'].",".$v['buyer_userid'].",".$v['last_paid_payinfo'].",".$v['create_time'].", , , ,"."\n";
		    }
		} else {
		    if ($v['last_paid_money'] > 0) {
			if ($v['pid'] == 0) {
			    $rebate = (empty($v['rebate_value']) || $v['rebate_value'] == 0) ? $v['product_price'] * $v['product_count'] * 0.1 : $v['rebate_value'];
			    $cost = empty($v['cost']) ? $v['product_price'] * $v['product_count'] * 0.9 : $v['cost'];
		            $str .= $v['order_id'].",".$v['pid'].",".$v['status'].",".$v['product_id'].",".$v['product_price'].",".$v['product_count'].",".$cost.",".$rebate;
			    $str .= ",".$v['last_paid_money'].",".$v['freight'].",".$v['tax'].",".$v['last_pay_coupon_value'].",".$v['promotion_value'].",".$v['last_payment'];
			    $str .= ",".$v['last_paid_time'].",".$v['buyer_userid'].",".$v['last_paid_payinfo'].",".$v['create_time'];
			    $str .= ",".$v['store'].",".$v['show_name'].",".$v['rebate']."\n";
			} else {
		            $str .= $v['order_id'].",".$v['pid'].",".$v['status'].",".$v['product_id'].",".$v['product_price'].",".$v['product_count'].",".$v['cost'];
			    $str .= ",".$v['rebate_value'].",".$v['last_paid_money'].",".$v['freight'].",".$v['tax'].",".$v['last_pay_coupon_value'].",".$v['promotion_value'];
			    $str .= ",".$v['last_payment'].",".$v['last_paid_time'].",".$v['buyer_userid'].",".$v['last_paid_payinfo'];
			    $str .= ",".$v['create_time'].",".$v['store'].",".$v['show_name'].",".$v['rebate']."\n";
			}
		    }
		}
            }
            $filename = 'Form_'.$date.'_'.time().'.csv'; //设置文件名
            $this->export_csv($filename,$str); //导出
        } else {
            exit(json_encode(['res'=>1,'hits'=>'no deals!']));
        }
    }

        //导出到服务器本地文件
        public function export_file($filename,$data){
                header("Content-type:text/csv"); 
                header("Content-Disposition:attachment;filename=".$filename); 
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0'); 
                header('Expires:0'); 
                header('Pragma:public'); 
                $folder = './Export/';
                if(!file_exists($folder)){
		    mkdir($folder,0777,TRUE);
                }
                file_put_contents($folder.$filename,$data,FILE_APPEND);
                return true;
        }
        //导出操作写入函数
        public function export_csv($filename,$data) {
                header("Content-type:text/csv");
                header("Content-Disposition:attachment;filename=".$filename);
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
                header('Expires:0');
                header('Pragma:public');
                echo $data;
                exit;
        }
}
