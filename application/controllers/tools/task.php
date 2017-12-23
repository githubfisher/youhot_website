<?php

/**
 * 定时执行脚本
 * crontab运行
 *
 *
 */
class Task extends User_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->model('task_model');
    }

    /**
     * 取得前3条商品
     * 前期每天运行一次,也可以挂个监听到管理员审核通过后
     */

    public function designer_run()
    {

        $last_runtime = $this->task_model->get_last_run_time(Task_model::TASK_ID_DESIGNER);

        echo "\nLast runtime:".standard_date('DATE_MYSQL',$last_runtime);
        echo "\nExecute time:".standard_date('DATE_MYSQL');

        $query = $this->product_model->db_slave->select('distinct author', false)->where('publish_time >', standard_date('DATE_MYSQL', $last_runtime))->get(TBL_PRODUCT);

        $res = get_query_result($query);
        if (empty($res)) {
            echo 'List  is empty';
            return;
        }
        foreach ($res as $row) {
            $userid = $row['author'];
            $products = $this->product_model->get_list($userid, 0, 3, array(TBL_PRODUCT . '.status' => PRODUCT_STATUS_PUBLISHED));

            $filter = array('cover_image', 'title', 'id');
            $p = array();
            foreach ($products['list'] as $product) {
                $p[] = filter_data($product, $filter);
            }


            //get tags info
            $query = $this->product_model->db_slave->where(TBL_USER_FOLLOW_TAG . '.userid', $userid)->join(TBL_TAGS, TBL_USER_FOLLOW_TAG . '.tag_id = ' . TBL_TAGS . '.id', 'left')->get(TBL_USER_FOLLOW_TAG);
            $tags = array();
            $res = get_query_result($query);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    $tags[] = array('id' => $row['id'], 'name' => $row['name'], 'description' => $row['description']);
                }
            }
//                $user_info['tags'] = $tags;
            $data = array('userid' => $userid, 'top_three' => json_encode($p), 'product_count' => $products['total'], 'tags' => json_encode($tags)

            );
            $query = $this->product_model->db_master->where('userid', $userid)->get(TBL_DESIGNER_TOP);
            if ($query->num_rows() > 0) {
                $this->product_model->db_master->where('userid', $userid)->update(TBL_DESIGNER_TOP, $data);
            } else {
                $this->product_model->db_master->insert(TBL_DESIGNER_TOP, $data);

            }

            echo "Updated User:".$userid;


        }

        $this->task_model->save_run_time(Task_model::TASK_ID_DESIGNER);

        return;
    }

    /**
     * 每5分钟执行一次
     */
    public function order_run()
    {
        $this->load->model('order_model');
        $last_runtime = $this->task_model->get_last_run_time(Task_model::TASK_ID_ORDER);
        //published,in presale time,presold_count>0
        $filter = array(TBL_PRODUCT . '.status' => PRODUCT_STATUS_PUBLISHED, TBL_PRODUCT . '.presale_end_time < ' => time(), TBL_PRODUCT . '.presale_end_time > ' => $last_runtime);
//        $this->order_model->db_slave->
        $data = $this->product_model->get_list(null, 0, 20, $filter, 'presale_end_time');

        $this->task_model->save_run_time(Task_model::TASK_ID_ORDER);
        //1. Is presale_sold_count > presale_minimum   (presale succeed)
        //2. Presale_sold_count < presale_minimum   (presale fail)
//        var_dump($presale_end_products);
        if (empty($data['list'])) {
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
        $this->admin_trace(json_encode($order_ids), 'update_order_status', 'Update order status ' . ORDER_STATUS_LAST_PAY_START . 'when order last_pay start');
        echo "Order status updated " . json_encode($order_ids) . "\n";

        //Notify users . What's the strategy
        $buyer_userids = array_column($orders, 'buyer_userid');
        $buyer_userids = array_unique($buyer_userids);
//        $this->load->library('jpush');
        $message = sprintf($this->lang->line('tpl_presale_start'), $product['title']);
//        $this->jpush->tuisong($buyer_userids, $message);
        $this->_sendMsg($buyer_userids,$message);

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
        $this->admin_trace(json_encode($order_ids), 'update_order_status', 'Update order status ' . ORDER_STATUS_END_FAIL . ' when order last_pay start');
        echo "Order status updated " . json_encode($order_ids) . "\n";

        //Notify users
        $buyer_userids = array_column($orders, 'buyer_userid');
        $buyer_userids = array_unique($buyer_userids);
//        $this->load->library('jpush');
        $message = sprintf($this->lang->line('tpl_presale_start'), $product['title']);
//        $this->jpush->tuisong($buyer_userids, $message);

        $this->_sendMsg($buyer_userids,$message);

        //Need to refund!!
        $pre_paid_payinfos = array_column($orders, 'pre_paid_payinfo');
        $this->_refund($pre_paid_payinfos);
        $this->admin_trace(json_encode($pre_paid_payinfos), 'Refund', 'Refund when order fail');
        echo "Refund " . json_encode($pre_paid_payinfos) . "\n";

        return;
    }

    private function _sendMsg($buyer_userids,$message){
        if(!isset($this->rongyun)){
            $this->load->library('rongyun');
        }
        $res = $this->rongyun->messageSystemPublish($this->config->item('admin_userid'),$buyer_userids,'RC:TxtMsg','{"content":'.$message.',"extra":"helloExtra"}');
        $res = json_decode($res);
        if($res->code == '200'){
            $this->admin_trace(json_encode($buyer_userids), 'Notify_user', 'Nofity when order last_pay start');
            echo "Notify user succeed" . json_encode($buyer_userids) . " content $message \n";
        }else{
            echo "Notify user fail :" . json_encode($buyer_userids) . " content $message \n";
        }
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


    private function _refund($payinfos)
    {
        //@todo refund:
        echo 'refund';
    }


    public function create_users()
    {
        $url = 'http://10.47.88.245/user/signup';
        for ($idx = 2; $idx < 1000; $idx++) {
            $post_body = array(
                'username' => 13000000000 + $idx,
                'verify_code' => 0000,
                'password' => 'aaaa'

            );
            curl_post($url, $post_body);
            sleep(1);
        }

    }

    /**
     * Get rongyun token scheduly
     * 避免注册时候没有拿到
     * Every 2 hours
     */
    public function get_ry_token(){
        $query = $this->user_model->db_slave->where('ry_token', '')->get(TBL_USER);
        $res = get_query_result($query);
        $this->load->library('rongyun');

        foreach($res as $row){
            $facepic = element('facepic',$row,$this->config->item('default_facepic'));
            $res = $this->rongyun->getToken($row['userid'],$row['nickname'],$facepic);
            $token = json_decode($res);
            if($token->code == '200'){
                $up_data = array(
                    'ry_token'=>$token->token
                );
                $this->user_model->update_user($row['userid'],$up_data);

            }
        }
    }
    /**
     * Correct follow num
     * Every 2 hours
     */
//    public function correct_follow_num(){
//        $last_runtime = $this->task_model->get_last_run_time(Task_model::TASK_ID_USER_FO_USER);
//        echo "\n Correct follow num Last runtime:".$last_runtime;
//        echo "\n Correct follow num Execute time:".standard_date('DATE_MYSQL');
//
//        $query = $this->user_model->db_slave->where('id >',  $last_runtime)->get(TBL_USER_FOLLOW_USER);
//
//        $res = get_query_result($query);
//        if (empty($res)) {
//            echo 'List  is empty';
//            return;
//        }
//        foreach ($res as $row) {
//            $userid = $row['author'];
//            $products = $this->product_model->get_list($userid, 0, 3, array(TBL_PRODUCT . '.status' => PRODUCT_STATUS_PUBLISHED));
//
//            $filter = array('cover_image', 'title', 'id');
//            $p = array();
//            foreach ($products['list'] as $product) {
//                $p[] = filter_data($product, $filter);
//            }
//
//
//            //get tags info
//            $query = $this->product_model->db_slave->where(TBL_USER_FOLLOW_TAG . '.userid', $userid)->join(TBL_TAGS, TBL_USER_FOLLOW_TAG . '.tag_id = ' . TBL_TAGS . '.id', 'left')->get(TBL_USER_FOLLOW_TAG);
//            $tags = array();
//            $res = get_query_result($query);
//            if (!empty($res)) {
//                foreach ($res as $key => $row) {
//                    $tags[] = array('id' => $row['id'], 'name' => $row['name'], 'description' => $row['description']);
//                }
//            }
////                $user_info['tags'] = $tags;
//            $data = array('userid' => $userid, 'top_three' => json_encode($p), 'product_count' => $products['total'], 'tags' => json_encode($tags)
//
//            );
//            $query = $this->product_model->db_master->where('userid', $userid)->get(TBL_DESIGNER_TOP);
//            if ($query->num_rows() > 0) {
//                $this->product_model->db_master->where('userid', $userid)->update(TBL_DESIGNER_TOP, $data);
//            } else {
//                $this->product_model->db_master->insert(TBL_DESIGNER_TOP, $data);
//
//            }
//
//            echo "Updated User:".$userid;
//
//
//        }
//
//        $this->task_model->save_run_time(Task_model::TASK_ID_DESIGNER);
//
//        return;
//    }

}

