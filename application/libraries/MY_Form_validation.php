<?php

/*
class MY_Form_validation extends CI_Form_validation
{




}*/

class MY_Form_validation extends CI_Form_validation
{

    function unique($value, $params)
    {

        $CI =& get_instance();
        if (!isset($CI->db_slave)) {
            $CI->db_slave = $this->load->database('style_slave', TRUE);
        }

        $CI->form_validation->set_message('unique', '%s 已存在');

        list($table, $field) = explode(".", $params, 2);

        $query = $CI->db_slave->select($field)->from($table)->where($field, $value)->limit(1)->get();

        if ($query->row()) {
            return false;
        } else {
            return true;
        }

    }

    function legal($str)
    {
        $CI =& get_instance();
        $CI->form_validation->set_message('legal', '%s 不合法(请不要使用aifudao，baidu作为用户名)');
        $ret = true;
        if (preg_match("/^(baidu_)/i", $str)) {
            $ret = false;
        }
        if (preg_match("/(aifudao)/i", $str)) {
            $ret = false;
        }
        return $ret;
    }

    function code_verify($str, $field)
    {
        $CI =& get_instance();
        $CI->form_validation->set_message('code_verify', '%s 输入错误,请检查');
        if (!isset($_POST[$field])) {
            return FALSE;
        }
        $field = $_POST[$field];

//        return true;//@todo 为了便于测试,先不验证短信,上线需要打开@styl

        if ($CI->session->userdata('bp_verifycode') && $CI->session->userdata('verify_phone') &&
            $CI->session->userdata('verify_phone') == $field && $CI->session->userdata('bp_verifycode') == $str &&
            $CI->session->userdata('verify_code_lifetime') > time()) {
            //verify_code is correct
            return TRUE;
        } else {
            return FALSE;
        }
    }


    function email_phone($str)
    {
        $CI =& get_instance();
        $CI->form_validation->set_message('email_phone', '%s 不合法(请用email或者手机号注册)');
        $str = trim($str);
        return ($this->valid_email($str) || $this->numeric($str));
    }

}

//end
