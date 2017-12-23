<?php

/**
 * Class Product
 * @method
 */
class Comments extends User_Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->load->model('comments_model');
    }


    public function index()
    {

        $this->_get_list();

    }


    public function get_list( $output = true)
    {
        $_POST = $_REQUEST;

        $host_id = $this->input->get_post('host_id');
        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $order = $this->input->get_post('od');


        $res = $this->comments_model->get_list($host_id, $offset, $limit, array(), (string)$order);
        //$res {"list":[],"count":22,"total":200,"res":}
        //Do not need output ,I need result

        if (!$output) {
            return $res;
        }
//        var_dump($res['list']);
        if (!empty($res['list'])) {

            foreach ($res['list'] as $key => $row) {
                $res['list'][$key] = filter_data($row, $this->config->item('json_filter_comments_list'));
                $res['list'][$key]['is_self'] = (string)(int)($this->userid == $row['author']);
            }

            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);

        } else {
            log_message('error', 'No comments list:' . json_encode($res));
            $this->_error(BP_OPERATION_LIST_EMPTY, $this->lang->line('hint_list_is_empty'));
        }

    }

    /**
     * create product
     */


    public function  create()
    {

        $this->_need_login(true);

        log_debug("comments content:".$this->input->get_post('content'));
        $this->_validation(array(
            array('content','内容','trim|required')
            ,array('host_id','host id','trim|required')
        ));

        $author = $this->userid;

        /**
         * Create product
         * Product table
         */

        $filter = array('content','host_id','reply_userid','reply_cid');
        $data = filter_data($this->input->post(),$filter,true);
        $data['author'] = $author;
        //Ready to compromise unstable requirements;
        $res = $this->comments_model->create($data);
        if ($res == DB_OPERATION_FAIL) {
            log_message('error', 'create comments wrong:' . json_encode($data));
            $this->_error(bp_operation_fail, $this->lang->line('bp_operation_fail_hint'));
        }
        $res = filter_data($res, $this->config->item('json_filter_comments_detail'));
        $res['is_self'] = (string)(int)($this->userid == element('author',$res,null));
        $this->_result(bp_operation_ok, $res);

    }


    private function _has_edit_role($cid=null)
    {
        $perm = false;


        if(!empty($cid)){
            $res = $this->comments_model->get_owner($cid);
            $owner = element('author', $res, null);
            if ($owner == $this->userid) {     //Author Self
                $perm = true;
            }
        }

        if ($this->_data['usertype'] == USERTYPE_ADMIN) {
            $perm = true;
        }

        return $perm;
    }
    private function _check_permission_and_return($cid)
    {
        if (!$this->_has_edit_role($cid)) {
//            echo "aa";
            $this->_error(bp_operation_user_forbidden, $this->lang->line('bp_operation_user_forbidden_hint'), 403);
        }
        return true;
    }

    /**
     * Save product
     * 根据需求在完善
     */
    public function delete()
    {
        $_POST = $_REQUEST;
        $this->_validation(array(
            array('cid','comment id','trim|required|numeric')
        ));
        $comment_id = $this->input->get_post('cid');


        $this->_check_permission_and_return($comment_id);

        $res = $this->comments_model->delete($comment_id);

        $this->_deal_res($res);

    }

}
