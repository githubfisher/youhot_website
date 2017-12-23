<?php

/**
 * Class category
 * @method
 */
class Category extends Normal_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model', 'category');
    }

    /**
     * save tag
     * @todo save tag
     */
    public function save()
    {

    }

    public function get_list($ordered = '')
    {

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $limit = 0;  //暂时全部取出来
	$filter = array(
	    'is_show' => 1
	);
        $res = $this->category->get_list($offset, $limit, $filter);
        // res is ordered by order asc
        if (empty($res['list'])) {
            log_message('error', 'tag list error:' . json_encode($res));
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        } else {
            $res['count'] = count($res['list']);
//            $categories = $res['list'];
            $categories = array();
            static $pointer = 100;
            foreach ($res['list'] as $category) {
                if ($category['parent_id'] == 1) {
                    $categories[$category['id']] = array_merge(array_key_exists($category['id'], $categories) ? $categories[$category['id']] : array(), $category);
                } else {
                    if ($category['order'] == 0) {
                        $category['order'] = ($pointer++);
                    }
                    if($ordered == 'ordered'){
                        $categories[$category['parent_id']]['sub_category'][] = $category;
                    }else{
                        $categories[$category['parent_id']]['sub_category'][$category['order']] = $category;
                    }
//                    print_r($categories[$category['parent_id']]['sub_category'][$category['order']]);
                }

            }
            usort($categories,array($this,'cat_camp'));
            $res['list'] = array_values($categories);
            $this->_result(bp_operation_ok, $res);
        }


    }

    private function cat_camp($a,$b){

        return @((int)$a['order'] - (int)$b['order']) ;
    }
}
