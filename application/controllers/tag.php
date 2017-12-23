<?php

/**
 * Class Product
 * @method
 */
class Tag extends Normal_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tag_model', 'tag');
    }

    /**
     * save tag
     * @todo save tag
     */
    public function save()
    {

    }

    public function get_list()
    {

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $res = $this->tag->get_list($offset, $limit);
        if (empty($res['list'])) {
            log_message('error', 'tag list error:' . json_encode($res));
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        } else {
            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);
        }


    }
}
