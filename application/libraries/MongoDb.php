<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/4/26
 * Time: 下午2:44
 */
require_once 'vendor/autoload.php'; // include Composer goodies

class Mongodb
{
    private $client;
    public function __construct()
    {
        $CI = &get_instance();
        if(!$this->client){
            $this->client = new MongoDB\Client($CI->config->item('mongodb'),[], ['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]);
        }

    }
    public function get_collection($db_name,$coll_name){
        return $this->client->$db_name->$coll_name;
//        return $this->client->stat_col->coll_9;
    }

}