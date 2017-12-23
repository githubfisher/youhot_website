<?php
class Msg extends Normal_Controller
{
    const TOKEN = 'fruit';

    public function __construct()
    {
	parent::__construct();
    }

    public function index()
    {
	$res = $this->checkSignature();
	if ($res) {
	    echo $_GET['echostr'];
	}
	return $res;
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = self::TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
