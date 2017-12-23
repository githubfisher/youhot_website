<?php

/**
 * 定时执行脚本
 * crontab运行
 *
 *
 */
class Udid extends Normal_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function export(){
        if(!($start = $this->input->get_post('s'))){
            $this->_error(bp_operation_verify_fail,'请指定开始时间');
        }

        $this->db_slave->select('distinct(udid)',false);
        $this->db_slave->where('last_update >= ', ($start));

        if($end = $this->input->get_post('e')){
            $this->db_slave->where('last_update <= ', $end);
        }
        $query = $this->db_slave->get('udid');

        if($query->num_rows()<1){
            echo "$start 至今没有新的udid";
            return;
        }

        $res = get_query_result($query);

        array_walk($res,array($this,'merge_by_tab'));

        $str = "Device ID\tDevice Name\n".implode("\n",$res);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=udid_list.txt');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($str));
        echo $str;
        exit;

    }
    private function merge_by_tab(&$item,$key){
        $item = $item['udid'] . "\t" . 'styl' . $key;
    }

}

