<?php

class Captcha_model extends MY_Model
{

    /*表结构：

    CREATE TABLE IF NOT EXISTS `captcha` (
      `id` bigint(13) unsigned NOT NULL AUTO_INCREMENT,
      `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `ip` varchar(16) NOT NULL DEFAULT '0',
      `word` varchar(20) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `word` (`word`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

    */

    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        log_message('debug', "Captcha Model Class Initialized");
    }

    //生成新的验证码
    public function create($type = CAPTCHA_TYPE_MOBILE)
    {
        $this->load->helper('captcha');


        $word = '';
        for ($i = 0; $i < 4; $i++) {
            $word .= mt_rand(0, 9);
        }

        $vals = array(
            'img_path' => './captcha/',
            'img_url' => '/captcha/',
            'word' => $word,
            'img_width' => 80,
        );


        $cap = create_captcha($vals);


        $data = array(
            'ip' => $this->input->ip_address(),
            'word' => strtolower($cap['word']),
            'time' => $cap['time'],
            'type' => $type,
        );

        $query = $this->db_master->insert(TBL_CAPTCHA, $data);
        return $cap['image'];
    }

    public function check($captcha = "", $type = CAPTCHA_TYPE_MOBILE)
    {
        $captcha = strtolower(trim($captcha));
        if (!is_numeric($captcha)) {//格式检查，否则可能影响到代码执行的稳定性
            return false;
        }

        $this->clear();
        $this->db_slave->where('word', $captcha)
            ->where('ip', $this->input->ip_address())//这个方法不是很靠谱，尤其是在移动网络情况下
            ->where('time >', time() - 1200)
            ->where('type', $type)
            ->limit(1)
            ->order_by('id', 'desc');
        $query = $this->db_slave->get(TBL_CAPTCHA);

        if ($query->num_rows() == 1) {
            log_message('debug', 'check captcha sucess!');
            return true;
        }

        log_message('debug', 'check captcha fail!');

        return false;

    }

    public function clear()
    {
        $this->db_master->where('time <', time() - 1200)->delete(TBL_CAPTCHA);

    }

}