<?php

/*
 * redis操作
 *
*/
require_once "RongServerAPI.php";

class Rongyun extends RongServerAPI
{

    private $conf = array();
    private $rong;

    public function __construct($config = array())
    {
        $CI = &get_instance();
        $CI->load->config('vendor', true);
        $this->conf = $CI->config->item('vendor')['rongyun'];

        if (!empty($config)) {
            $this->conf = $config;
        }
        parent::__construct($this->conf['app_key'], $this->conf['app_secret']);
    }

//    public function  getRongyun()
//    {
//        //todo:调整一下配置
//        if(!$this->rong){
//            $this->rong = new RongServerAPI($this->conf['app_key'],$this->conf['app_secret']);
//        }
//        return $this->rong;
//
//    }

    /**
     *刷新用户信息 方法  说明：当您的用户昵称和头像变更时，您的 App Server 应该调用此接口刷新在融云侧保存的用户信息，以便融云发送推送消息的时候，能够正确显示用户信息
     *定制:name,portrait 可以为空
     * @param $userId   用户 Id，最大长度 32 字节。是用户在 App 中的唯一标识码，必须保证在同一个 App 内不重复，重复的用户 Id 将被当作是同一用户。（必传）
     * @param string $name 用户名称，最大长度 128 字节。用来在 Push 推送时，或者客户端没有提供用户信息时，显示用户的名称。
     * @param string $portraitUri 用户头像 URI，最大长度 1024 字节
     * @return mixed
     */
    public function userRefresh($userId, $name = '', $portraitUri = '')
    {
        try {
            if (empty($userId))
                throw new Exception('用户 Id 不能为空');
//            if(empty($name))
//                throw new Exception('用户名称不能为空');
//            if(empty($portraitUri))
//                throw new Exception('用户头像 URI 不能为空');
            $data = array('userId' => $userId);
            if (!empty($name))
                $data['name'] = $name;
            if (empty($portraitUri))
                $data['portraitUri'] = $portraitUri;

            $ret = $this->curl('/user/refresh', $data);
            if (empty($ret))
                throw new Exception('请求失败');
            return $ret;
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

}

