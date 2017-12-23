<?php

/**
 * 定时执行脚本, order相关的处理
 * crontab运行
 *
 *
 */
class Order extends User_Controller
{

    const ORDER_RUNTIME_FILE = 'application/logs/order_runtime.txt';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('product_model');
    }

    /**
     * 定期检查product status,达到条件出发相应的内容
     * 1，标准逻辑，按照预售截止时间判断是否生产。
     * 2，附加逻辑，当前时间＜预售截止时间，当前销售量≧最小预售量，设计师可以点击按键宣布预售结束提前开始生产。
     */

    public function status_run()
    {

        $last_runtime = $this->_get_last_runtime();
        //published,in presale time,presold_count>0
        $filter = array(TBL_PRODUCT . '.status' => PRODUCT_STATUS_PUBLISHED, TBL_PRODUCT . '.presale_end_time < ' => time(), TBL_PRODUCT . '.presale_end_time > ' => $last_runtime);
//        $this->order_model->db_slave->
        $data = $this->product_model->get_list(null, 0, 20, $filter, 'presale_end_time');

        $this->_save_runtime();
        //1. Is presale_sold_count > presale_minimum   (presale succeed)
        //2. Presale_sold_count < presale_minimum   (presale fail)
//        var_dump($presale_end_products);
        if(empty($data['list'])){
            echo 'List of product on presale is empty';
            return;
        }

        $presale_end_products = $data['list'];


        foreach ($presale_end_products as $product) {
            if ($product['presold_count'] >= $product['presale_minimum']) {
                echo "\nProduct presale succeed:" . $product['id'];
                $this->_presale_succeed($product);
            } else {
                echo "\nProduct presale fail:" . $product['id'];
                $this->_presale_fail($product);
            }

        }

        return;
    }

    /**
     * Presale succeed! Should do:
     * 1. Notify users who have prepaid
     * 2. Update orders
     * @param $product
     */
    private function _presale_succeed($product)
    {
        $orders = $this->_get_orders($product);
        if (empty($orders)) {
            return;
        }
        //Update orders
        $order_ids = array_column($orders, 'order_id');
        $up_data = array('status' => ORDER_STATUS_LAST_PAY_START);
        $this->order_model->update_info($order_ids, $up_data);
        $this->admin_trace(json_encode($order_ids),'update_order_status','Update order status '.ORDER_STATUS_LAST_PAY_START.'when order last_pay start');
        echo "Order status updated ". json_encode($order_ids)."\n";

        //Notify users . What's the strategy
        $buyer_userids = array_column($orders, 'buyer_userid');
        $this->load->library('jpush');
        $message = sprintf($this->lang->line('tpl_presale_start'), $product['title']);
        $this->jpush->tuisong($buyer_userids, $message);
        $this->admin_trace(json_encode($buyer_userids),'Notify_user','Nofity when order last_pay start');
        echo "Notify user ". json_encode($buyer_userids)."\n";

        return;
    }

    private function _presale_fail($product)
    {
        $orders = $this->_get_orders($product);
        if (empty($orders)) {
            echo "Order is empty";
            return;
        }

        //Update orders
        $order_ids = array_column($orders, 'order_id');
        $up_data = array('status' => ORDER_STATUS_END_FAIL);
        $this->order_model->update_info($order_ids, $up_data);
        $this->admin_trace(json_encode($order_ids),'update_order_status','Update order status '.ORDER_STATUS_END_FAIL.' when order last_pay start');
        echo "Order status updated ". json_encode($order_ids)."\n";

        //Notify users
        $buyer_userids = array_column($orders, 'buyer_userid');
        $this->load->library('jpush');
        $message = sprintf($this->lang->line('tpl_presale_start'), $product['title']);
        $this->jpush->tuisong($buyer_userids, $message);
        $this->admin_trace(json_encode($buyer_userids),'Notify_user','Nofity when order fail');
        echo "Notify user ". json_encode($buyer_userids)."\n";

        //Need to refund!!
        $pre_paid_payinfos = array_column($orders, 'pre_paid_payinfo');
        $this->_refund($pre_paid_payinfos);
        $this->admin_trace(json_encode($pre_paid_payinfos),'Refund','Refund when order fail');
        echo "Refund ". json_encode($pre_paid_payinfos)."\n";

        return;
    }

    private function _get_orders($product)
    {
        $filter = array(TBL_CDB_DEAL . '.product_id' => $product['id'], TBL_CDB_DEAL . '.status' => ORDER_STATUS_PRE_PAID,);
//        $this->order_model->db_slave->
        //Get all orders
        $data = $this->order_model->get_list(null, 0, 20000, $filter);
        //Get all userids
        $orders = $data['list'];
        return $orders;
    }

    private function _get_last_runtime()
    {

        $str = @file_get_contents(self::ORDER_RUNTIME_FILE);
        if ($str === false) {

            $str = 0;
        }

        return $str;
    }

    private function _save_runtime()
    {
        $str = time();
        if (!@file_put_contents(self::ORDER_RUNTIME_FILE, $str)) {
            $error = error_get_last();
            echo $error['message'];
        }
    }

    private function _refund($payinfos)
    {
        //@todo refund:
        echo 'refund';
    }

}

