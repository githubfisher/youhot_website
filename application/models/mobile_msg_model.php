<?php

class Mobile_msg_model extends MY_Model
{

    public $top_client;

    public function __construct()
    {

        parent::__construct();
        log_message('debug', 'mobile_msg_model Initialized');
        include "application/libraries/taobaosms/Topsdk.php";
        $this->top_client = new TopClient;
        $this->top_client->appkey = '23573747';
        $this->top_client->secretKey = '60fb6608eb9cf5235645c515c2c949dd';
        $this->top_client->format = 'json';
    }

    //注册手机验证码
    public function send_signup_verify_code($verify_code, $phone_num)
    {
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("string");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("洋火");
        $params = array(
            "code" => $verify_code
        //, "product" => "styleshow"
        , "product" => "洋火"
        );
        $req->setSmsParam(json_encode($params));
        $req->setRecNum($phone_num);
        //$req->setSmsTemplateCode("SMS_2650207");
        $req->setSmsTemplateCode("SMS_21755194");
        $resp = $this->top_client->execute($req);

        return $this->resolve_resutl($resp);
    }

    /**
     * 找回密码
     */

    public function send_forgot_pwd_verify_code($code, $phone_num)
    {

        //@todo 需要换成找回密码的模版  等待审核中
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("string");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("洋火");
        $params = array(
            "code" => $code
        //, "product" => "styleshow"
        , "product" => "洋火"
        );
        $req->setSmsParam(json_encode($params));
        $req->setRecNum($phone_num);
        //$req->setSmsTemplateCode("SMS_2980063");
        $req->setSmsTemplateCode("SMS_21755192");
        $resp = $this->top_client->execute($req);

        return $this->resolve_resutl($resp);
    }

    private function resolve_resutl($result){
        $ret = array('res'=>$result);

        if (is_object($result) && property_exists($result,'result') && $result->result->success) {
            $ret['code'] = DB_OPERATION_OK;
        }else{
            $ret['code'] = DB_OPERATION_FAIL;
            $ret['hint'] = $this->_get_error_hint_from_alidayu($result);
        }
        return $ret;
    }

    private function _get_error_hint_from_alidayu($result)
    {
        $str = '暂时无法完成您的操作,请稍后重试';
        if(is_object($result) && property_exists($result,'sub_code')){
            if($result->sub_code == 'isv.MOBILE_NUMBER_ILLEGAL'){
                $str = '手机号码格式错误';
            }elseif($result->sub_code == 'isv.BUSINESS_LIMIT_CONTROL'){
                $str = '您的请求过于频繁,请稍后再试';
            }elseif($result->sub_code == 'isv.MOBILE_COUNT_OVER_LIMIT'){
                $str = '手机号码数量超过限制';

            }elseif($result->sub_code == 'isv.TEMPLATE_MISSING_PARAMETERS'){
                $str = '参数异常';

            }elseif($result->sub_code == 'isv.INVALID_PARAMETERS'){
                $str = '参数异常';

            }
        }
        return $str;
    }

}
