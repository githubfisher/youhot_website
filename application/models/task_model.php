<?php

class Task_model extends MY_Model
{


    const TASK_ID_ORDER = 1;
    const TASK_ID_DESIGNER = 2;
    const TASK_ID_USER_FO_USER = 3;
    const TASK_ID_DESIGNER_RANK = 4;

    function __construct()
    {
        parent::__construct();

    }

    /**
     * @param $task
     * @return int
     */
    function get_last_run_time($task)
    {
        $query = $this->db_slave->where('task_id', $task)->get(TBL_TASK_TIME);
        $res = get_row_array($query);
//        var_dump($res);
        return empty($res) ? 1000 : $res['task_time'];
    }

    /**
     * @param $task
     * @return int
     */
    function save_run_time($task)
    {
        $query = $this->db_slave->query('replace into '.TBL_TASK_TIME .' values('.$task.','.time().')');
//        $query = $this->db_slave->where('task_id', $task)->update(TBL_TASK_TIME, array('task_time' => time()));
        if ($this->db_slave->affected_rows() > 0) {
            return DB_OPERATION_OK;
        }
        return DB_OPERATION_FAIL;
    }

}
