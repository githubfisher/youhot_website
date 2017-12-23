<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/4/7
 * Time: 下午5:11
 */
class Rank extends Normal_Controller
{
    const FANS_WEIGHT = 10;
    const PRESOLD_WEIGHT = 20;

    private $last_runtime;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
        $this->load->model('user_model');
        $this->load->model('task_model');
    }

    /**
     * 定期计算designer的rank
     * 每天跑一次
     */
    public function designer_run()
    {
        $this->last_runtime = $this->task_model->get_last_run_time(Task_model::TASK_ID_DESIGNER_RANK);

        $filter = array(
            TBL_USER . '.usertype' => USERTYPE_DESIGNER,
//            'need_top' => true
        );
        $res = $this->user_model->get_list(0, 0, $filter);
        if(empty($res['list'])){
            echo "Designers list is null ; Return";
            return;
        }

        foreach($res['list'] as $row){
            $has_update = false;

            $rank_fans = $row['rank_fans'];
            $rank_presold = $row['rank_presold'];

            if($rank_fans != $row['follower_num']){
                $has_update = true;
                $rank_fans = $row['follower_num'];
            }

            $new_presold = $this->_get_new_presold($row['userid']);
            if($new_presold >0){
                $has_update = true;
                $rank_presold = $new_presold;
            }

            if($has_update){
                $data = array(
                    'rank'=> self::compute_rank($row['boost'],$rank_presold,$rank_fans)
                    ,'rank_presold'=> $rank_presold
                    ,'rank_fans' => $rank_fans
                );

                $res = $this->user_model->update_user($row['userid'],$data);
                if($res){
                    echo "Designer rank updated: ".$row['userid'].json_encode($data)."\n";
                }
            }

        }

        $this->task_model->save_run_time(Task_model::TASK_ID_DESIGNER_RANK);

        echo "Design rank run successfully at ".standard_date('DATE_MYSQL');

    }


    public static function compute_rank($boost,$fans,$presold){
        return ((int) $boost + (int) $fans * self::FANS_WEIGHT + (int) $presold * self::PRESOLD_WEIGHT);
    }


    private function _get_new_presold($userid){
        $filter = array(
            TBL_CDB_DEAL.'.seller_userid'=>$userid,
            TBL_CDB_DEAL.'.status'=>ORDER_STATUS_PRE_PAID,
            TBL_CDB_DEAL.'.create_time > '=>$this->last_runtime,
        );
        $this->db_slave->where($filter);
        $res = $this->db_slave->count_all_results(TBL_CDB_DEAL);
        return $res;

    }

}