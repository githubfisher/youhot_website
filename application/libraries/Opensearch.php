<?php

/*
 * redis操作
 *
*/
require_once("opensearch/CloudsearchClient.php");
require_once("opensearch/CloudsearchIndex.php");
//require_once("opensearch/CloudsearchDoc.php");
require_once("opensearch/CloudsearchSearch.php");
require_once("opensearch/CloudsearchSuggest.php");

class Opensearch
{

    private $conf = array();
//    private $client;
    private $appname;

    public function __construct($config = array())
    {
        $CI = &get_instance();
        $CI->load->config('vendor', true);
        $this->conf = $CI->config->item('vendor')['aliyun'];

        if (!empty($config)) {
            $this->conf = $config;
        }

        $this->appname = $this->conf['opensearch_app'];


    }

    public function  getClient()
    {
        $access_key = $this->conf['access_id'];
        $secret = $this->conf['access_secret'];

        $host = $this->conf['opensearch_host'];
        $key_type = "aliyun";  //固定值，不必修改
        $opts = array('host' => $host);
// 实例化一个client 使用自己的accesskey和Secret替换相关变量
        $client = new CloudsearchClient($access_key, $secret, $opts, $key_type);
//        $this->client = $client;
        return $client;
    }

    public function getAppname()
    {
        return $this->appname;
    }

}

