<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Yiqizuoye{

    private $app_key = 'AiFuDao';
    private $app_secret = 'Z7lWMlxFzbnz';
    private $yiqizuoye_url      = 'http://api.17zuoye.com';
    private $get_user_info_url  = 'http://api.17zuoye.com/v1/user/get.api';
    private $notify_user_url    = 'http://api.17zuoye.com/v1/user/wechat/notifyar.api';
    private $token_code;
    private $CI;


    public function __construct(){
        $this->token_code == md5('AIFUDAOWX');
		$this->CI =& get_instance();
    }
    //get user info
    public function get_user_info($session_key){
        $params = array(
            'app_key'=>$this->app_key
            ,'session_key'=>$session_key
        );
        $sig = $this->make_sig($params);
        $params['sig'] = $sig;
        //echo "<br>curl_post".$this->get_user_info_url.' at: '.time();
        $user_info = curl_post($this->get_user_info_url,$params);
        //echo "<br>curl_post".$this->get_user_info_url.' end at: '.time();
        return $user_info;
    }
    /**
     * 计算签名
     * @param $params
     * @return string
     */
    private function make_sig($params) {
        ksort($params);
        $queryString = array();
        foreach ($params as $key => $val ) {
            array_push($queryString, $key . '=' . $val);
        }
        $sig = join('&', $queryString);
        $sig = md5($sig .$this->app_secret);
        return $sig;
    }

//如果登录了，正常
//如果没有登录，则跳转到登录页面
    public function need_login(){
        $access_token = $this->CI->session->userdata('wx_access_token');
        if(!$access_token){
            $this->login();   //Have not ever logined .
            return;
        }
        $openid = $this->CI->session->userdata('username');
/*
    $auth = $this->_check_auth($access_token,$openid);
        if((int)$auth['errcode'] != 0){

            $this->login();   //Have not ever logined .
            return;
        }
 */
        $expires_in = (int) $this->CI->session->userdata('wx_expires_in');
        if(time() > $expires_in) { //expired

            $refresh_token = $this->CI->session->userdata('wx_refresh_token');
            $tk = $this->refresh_token($refresh_token);
            if(!$tk){//refresh token expired
                $this->login();
            }
            $access_token = $tk['access_token'];
            $openid = $tk['openid'];
            $userdata = array(
                'wx_refresh_token' =>$tk['refresh_token'],
                'wx_access_token' =>$tk['access_token'],
                'wx_expires_in' => (int) (time()+$tk['expires_in']),
                'wx_scope' =>$tk['scope']
            );
            $this->CI->session->set_userdata($userdata);
        }

    }
    public function login(){
        //1.Get code 2.verify_return redirect to step 1 page
        $current_url = base64_encode(current_whole_url());
        $redirect_url = 'http://www.aifudao.com/wx/verify_return?b='.$current_url;
        //$redirect_url = 'http://www.aifudao.com/wx/verify_return';
        $this->get_code($redirect_url);
    }


    public function get_code($redirect_url){
        //https://open.weixin.qq.com/connect/qrconnect?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect;
        //https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
        //$get_code_url = 'https://open.weixin.qq.com/connect/qrconnect?appid='.$this->app_id.'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope=snsapi_login&state='.$this->token_code.'#wechat_redirect';
        $get_code_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->app_id.'&redirect_uri='.urlencode($redirect_url).'&response_type=code&scope=snsapi_userinfo&state='.$this->token_code.'#wechat_redirect';
        redirect($get_code_url);
    }
    public function get_token_code(){
        return $this->token_code;
    }
    //get access_token
    //@return array false
    public function get_access_token($code){

        $_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->app_id.'&secret='.$this->app_secret.'&code='.$code.'&grant_type=authorization_code';
        $res = curl_get($_url);

        $info = json_decode($res,true);
        if(!empty($info['access_token'])){
            return $info;
        }else{
            log_message('debug','Access_token error:'.$res);
            return false;
        }

    }
    public function refresh_token($rt){
        $_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->app_id.'&grant_type=refresh_token&refresh_token='.$rt;
        $res = curl_get($_url);

        $info = json_decode($res,true);
        if(!empty($info['access_token'])){
            return $info;
        }else{
            log_message('debug','Refresh Access_token error:'.$res);
            return false;
        }

    }
    public function get_uinfo($access_token=null,$openid=null){
        if(empty($access_token) && empty($openid)){
            $access_token = $this->CI->session->userdata('wx_access_token');
            $openid = $this->CI->session->userdata('username');
            $expires_in = $this->CI->session->userdata('wx_expires_in');
            if(time() > $expires_in) { //expired

                $refresh_token = $this->CI->session->userdata('wx_refresh_token');
                $tk = $this->refresh_token($refresh_token);
                if(!$tk){//refresh token expired
/*
        $userdata = array(
            'status'=>0,
            'logout_time'=>standard_date('DATE_MYSQL',time())
        );
        $this->CI->session->set_userdata($userdata);
        redirect();
 */
                    $data = array(
                        'res' => $this->refresh_token_expired_error_no,
                        'hint' => $this->refresh_token_expired_error_hint
                    );
                    return $data;

                }
                $access_token = $tk['access_token'];
                $openid = $tk['openid'];
                $userdata = array(
                    'wx_refresh_token' =>$tk['refresh_token'],
                    'wx_access_token' =>$tk['access_token'],
                    'wx_expires_in' => (int) (time()+$tk['expires_in']),
                    'wx_scope' =>$tk['scope']
                );
                $this->CI->session->set_userdata($userdata);
            }

        }
        $_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid;
        $res = curl_get($_url);

        $info = json_decode($res,true);
        return $info;
    }
    //Check whether access_token is valid
    private function _check_auth($access_token,$openid){
        $_url = 'https://api.weixin.qq.com/sns/auth?access_token='.$access_token.'&openid='.$openid;
        $res = curl_get($_url);

        $info = json_decode($res,true);
        return $info;

    }
/*
question_id string  问题的唯一标识
answer_link string  问题的解答地址
user_id Big Int 用户Id
通知用户的微信接口
*/
    public function notify_user($question_id,$answer_link,$user_id){
        $params = array(
            'app_key'=>$this->app_key
        );
        $params['question_id'] = $question_id;
        $params['answer_link'] = $answer_link;
        $params['user_id'] = $user_id;
        $sig = $this->make_sig($params);
        $params['sig'] = $sig;
        $res = curl_post($this->notify_user_url,$params);
        log_message('debug','library/yiqizuoye/notify_user notify res: '.json_encode($res));
        return true;
    }

}
