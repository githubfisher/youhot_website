<?php

/**
 * Class Product
 * @method
 */
class Banner extends User_Controller
{
    private static $welcome_msg = [
        USERTYPE_USER=>'优雅不是那些从青年时代挣脱过来的人,而是掌握了自己未来的人的特权',
        USERTYPE_DESIGNER=>'优雅不是那些从青年时代挣脱过来的人,而是掌握了自己未来的人的特权',
        USERTYPE_BUYER=>'优雅不是那些从青年时代挣脱过来的人,而是掌握了自己未来的人的特权',
        USERTYPE_ADMIN=>'优雅不是那些从青年时代挣脱过来的人,而是掌握了自己未来的人的特权',

    ]   ;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('banner_model', 'banner');
    }
    public function get_list()
    {

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $res = $this->banner->get_list($offset, $limit);
        if (empty($res['list'])) {
            log_message('error', 'banner list error:' . json_encode($res));
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        } else {
            $res['count'] = count($res['list']);

            $res['hi'] = (($this->is_login)? $this->session->userdata('nickname'):"") . " 你好";
            $res['msg'] = self::$welcome_msg[$this->_data['usertype']];

            $this->_result(bp_operation_ok, $res);
        }


    }
}
