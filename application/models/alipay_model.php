<?php

class Alipay_model extends MY_Model
{
    var $alipay_config = '';

    function __construct()
    {
        parent::__construct();
        $this->config->load('alipay', TRUE);
        $this->alipay_config = $this->config->item('alipay');
        log_message('debug', 'alipay_model');
        $this->load->model('order_model', 'order');
    }

    /**
     *
     * 服务器异步通知
     */
    function notify_verify($paytype)
    {
        log_message('debug', 'notify');
        //计算得出通知验证结果
        //$alipayNotify = new AlipayNotify($this->alipay_config);
        $this->load->library('alipay/alipay_notify', $this->alipay_config);
        $verify_result = $this->alipay_notify->verifyNotify();


        $this->order->log_pay_notify('alipay_notify', $_POST, ($verify_result ? 1 : 0));

        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //@todo 需要检查,看是普通版还是高级版.http://www.wbphp.cn/html/y12/8058.html
            if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {    //交易成功结束
                //判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                $this->update_order($_POST, $paytype);

//                ob_end_clean();#清除之前的缓冲内容，这是必需的，如果之前的缓存不为空的话，里面可能有http头或者其它内容，导致后面的内容不能及时的输出
//                header("Connection: close");#告诉浏览器，连接关闭了，这样浏览器就不用等待服务器的响应
//                #可以发送200状态码，以这些请求是成功的，要不然可能浏览器会重试，特别是有代理的情况下
//                header("HTTP/1.1 200 OK");
//                ob_start();#当前代码缓冲

                echo "success";        //请不要修改或删除

//                $size=ob_get_length();
//                header("Content-Length: $size");
//                ob_end_flush();#输出当前缓冲
//                flush();#输出PHP缓冲


//                //直接执行，后面再通过异步脚本来补充未正常提交的处理
//                ignore_user_abort ( TRUE );
//                set_time_limit ( 0 );
//                log_message('debug','Continue executing after redirect');
//
//                $order_id = $_POST['out_trade_no'];
//                $this->order->update_after_paid($order_id,$paytype);

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            } else {
                echo "success";        //其他状态判断。普通即时到帐中，其他状态不用判断，直接打印success。

                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    /**
     *
     * 页面跳转同步通知
     */
    function return_verify()
    {
        log_message('debug', 'return' . TBL_USER);
        //计算得出通知验证结果

        $this->load->library('alipay/alipay_notify', $this->alipay_config);
        //$alipayNotify = new AlipayNotify($aliapy_config);
        //$verify_result = $alipayNotify->verifyReturn();
        $verify_result = $this->alipay_notify->verifyReturn();


        $this->order->log_pay_notify('alipay_return', $_GET, ($verify_result ? 1 : 0));

        $_res = '';
        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                log_message('debug', "return - success");
                $this->update_status($_GET);
            } else {
                log_message('error', 'trade_status!=success,请查看trade_status=' . $_GET['trade_status']);
                $res['payres'] = $this->alipay_config['res_status_pay_not_complete'];
                $res['hint'] = '支付未完成，请查看';

            }
            $res['payres'] = $this->alipay_config['res_status_success'];
            $res['hint'] = '支付成功，订单号为：' . $_GET['out_trade_no'];


            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数，比对sign和mysign的值是否相等，或者检查$responseTxt有没有返回true
            //echo "验证失败";
            $res['payres'] = $this->alipay_config['res_status_verify_fail'];
            $res['hint'] = '验证失败，请电话联系 4008180190　或发邮件给管理员 ' . $this->config->item('admin_email');
            log_message('error', '支付宝验证出错');
            //  $this->update_status($_GET);
        }
        return $res;
    }

    /**
     *
     * 更新订单状态（更新支付宝订单号）
     * @param string $alipay_trade_no 支付宝订单号
     * @param string $order_sn 商户订单号
     */
    function update_order($data, $paytype = PAY_TYPE_PREPAY)
    {
        //请在这里加上商户的业务逻辑程序代
        //

        log_message('debug', 'update status');

        $pay_info_id = $this->_record_pay_info($data);
        //@todo _record_pay_info


        $order_id = $data['out_trade_no'];

        if ($paytype == PAY_TYPE_LASTPAY) {
            $sqldata = array(
                'last_paid_time' => $data['notify_time'],
                'last_paid_money' => $data['total_fee'],
                'status' => ORDER_STATUS_LAST_PAID,
                'last_paid_payinfo' => $pay_info_id
            );
        }
        if ($paytype == PAY_TYPE_PREPAY) {
            $sqldata = array(
                'pre_paid_time' => $data['notify_time'],
                'pre_paid_money' => $data['total_fee'],
                'status' => ORDER_STATUS_PRE_PAID,
                'pre_paid_payinfo' => $pay_info_id
            );
        }

        //var_dump($sqldata);

        $qu = $this->order->update_deal_and_product($order_id, $sqldata,array('status' => ORDER_STATUS_INIT),$paytype);

        return $qu;

    }

    /**
     *
     * 构造提交表单
     * @param 订单编号 $order_id
     * @param 订单名 $order_name
     * @param 订单金额 $money
     */
    public function build_form($order_id, $order_name, $money, $return_in_object = FALSE)
    {

        //$this->load->library('alipay')

        /**************************请求参数**************************/

        //必填参数//

        //请与贵网站订单系统中的唯一订单号匹配
        $out_trade_no = $order_id;
        //订单名称，显示在支付宝收银台里的“商品名称”里，显示在支付宝的交易管理的“商品名称”的列表里。
        $subject = $order_name;
        //订单描述、订单详细、订单备注，显示在支付宝收银台里的“商品描述”里
        $body = "爱辅导充值";
        //订单总金额，显示在支付宝收银台里的“应付总额”里
        $total_fee = $money;


        //扩展功能参数——默认支付方式//

        //默认支付方式，取值见“即时到帐接口”技术文档中的请求参数列表
        $paymethod = '';
        //默认网银代号，代号列表见“即时到帐接口”技术文档“附录”→“银行列表”
        $defaultbank = '';


        //扩展功能参数——防钓鱼//

        //防钓鱼时间戳
        $anti_phishing_key = '';
        //获取客户端的IP地址，建议：编写获取客户端IP地址的程序
        $exter_invoke_ip = '';
        //注意：
        //1.请慎重选择是否开启防钓鱼功能
        //2.exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
        //3.开启防钓鱼功能后，服务器、本机电脑必须支持SSL，请配置好该环境。
        //示例：
        //$exter_invoke_ip = '202.1.1.1';
        //$ali_service_timestamp = new AlipayService($alipay_config);
        //$anti_phishing_key = $ali_service_timestamp->query_timestamp();//获取防钓鱼时间戳函数


        //扩展功能参数——其他//

        //商品展示地址，要用 http://格式的完整路径，不允许加?id=123这类自定义参数
        $show_url = '';
        //自定义参数，可存放任何内容（除=、&等特殊字符外），不会显示在页面上
        $extra_common_param = '';

        //扩展功能参数——分润(若要使用，请按照注释要求的格式赋值)
        $royalty_type = "";            //提成类型，该值为固定值：10，不需要修改
        $royalty_parameters = "";
        //注意：
        //提成信息集，与需要结合商户网站自身情况动态获取每笔交易的各分润收款账号、各分润金额、各分润说明。最多只能设置10条
        //各分润金额的总和须小于等于total_fee
        //提成信息集格式为：收款方Email_1^金额1^备注1|收款方Email_2^金额2^备注2
        //示例：
        //royalty_type 		= "10"
        //royalty_parameters= "111@126.com^0.01^分润备注一|222@126.com^0.01^分润备注二"

        /************************************************************/

        //构造要请求的参数数组
        $parameter = array(
            "service" => "create_direct_pay_by_user",
            "payment_type" => "1",

            "partner" => trim($this->alipay_config['partner']),
            "_input_charset" => trim(strtolower($this->alipay_config['input_charset'])),
            "seller_email" => trim($this->alipay_config['seller_email']),
            "return_url" => trim($this->alipay_config['return_url']),
            "notify_url" => trim($this->alipay_config['notify_url']),

            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "body" => $body,
            "total_fee" => $total_fee,

            "paymethod" => $paymethod,
            "defaultbank" => $defaultbank,

            "anti_phishing_key" => $anti_phishing_key,
            "exter_invoke_ip" => $exter_invoke_ip,

            "show_url" => $show_url,
            "extra_common_param" => $extra_common_param,

            "royalty_type" => $royalty_type,
            "royalty_parameters" => $royalty_parameters
        );

        if ($return_in_object) {
            return $parameter;
        } else {
            //构造即时到帐接口
            //$alipayService = new AlipayService($this->alipay_config);
            //$tmp = array('parameter' => $parameter, 'key' => $this->alipay_config['key'], 'sign_type' =>$this->alipay_config['sign_type']);
            //header('Content-Type: text/html; charset=UTF-8');
            //$this->load->library('alipay/alipay_service',$tmp,'');
            $this->load->library('alipay/alipay_service', $this->alipay_config);
            $html_text = $this->alipay_service->create_direct_pay_by_user($parameter);
            return $html_text;
        }
    }

    private function _record_pay_info($data)
    {
        //@todo  拆分不同的field
        $insert = array('content'=>json_encode($data));
        $this->db_master->insert(TBL_PAY_INFO,$data);
    }

}
