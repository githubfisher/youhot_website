<?php

/*
 * redis操作
 *
*/
require_once "predis-1.0/autoload.php";

class Redis
{

    private $conf = array();

    public function __construct($config = array())
    {
        $CI = &get_instance();
        $CI->load->config('vendor', true);
        $this->conf = $this->config->item('vendor')['redis'];

        if (!empty($config)) {
            $this->conf = $config;
        }
    }

    public function  getRedis()
    {
        //todo:调整一下配置
        $redis = new Predis\Client($this->conf);
        return $redis;
    }

}

