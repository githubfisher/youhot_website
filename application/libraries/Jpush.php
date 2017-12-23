<?php

/*
 * jpush operations
 *
*/
require_once 'jiguang/autoload.php';
use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

class Jpush
{

    private $conf = array();
    private $client;

    public function __construct($config = array())
    {
        $CI = &get_instance();
        $CI->load->config('vendor', true);
        $this->conf = $CI->config->item('vendor')['jpush'];

        if (!empty($config)) {
            $this->conf = $config;
        }


        $this->client = $this->getClient();
    }

    public function  getClient()
    {
        $app_key = $this->conf['app_key'];
        $master_secret = $this->conf['master_secret'];

        $client = new JPushClient($app_key, $master_secret);

        return $client;
    }

    /**
     * @param string $platform
     * @param $receivers
     * @param $content
     */
    public function tuisong($receivers,$content,$platform='all')
    {
        return true;
        //@todo 未设置好,默认发送成功
        $receivers = array(
            'alias'=>$receivers
        );
        $result = $this->client->push()
            ->setPlatform(M\all)
            ->setAudience($receivers)
            ->setNotification(M\notification($content))
            ->send();
        $br = "<br>";
        echo 'Push Success.' . $br;
        echo 'sendno : ' . $result->sendno . $br;
        echo 'msg_id : ' . $result->msg_id . $br;
        echo 'Response JSON : ' . $result->json . $br;

    }

}

