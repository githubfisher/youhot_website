<?php
/**
 * Class Admin/Size
 * @author fisher
 * @date 2017-05-24
 */
class Search extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('collection_model');
    }

    public function index()
    {
    	// 获取分类 by fisher at 2017-5-24
        $this->_data['cbpd'] = $this->filterList();
        // echo '<pre>'; print_r($this->_data['cbpd']); die;
    	$template = 'admin/search/list';
        $this->template($template, $this->_data);
    }
    
    /**
     * 商品筛选条件
     * 返回商品筛选的可用条件
     * @return array
     * author fisher
     * date 2017-03-14
     */
    private function filterList($ordered = true)
    {
        $lists = $this->collection_model->get_filter_list();
        //  梳理category
        $categories = array();
        static $pointer = 100;
        foreach ($lists['categorys']['list'] as $category) {
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
            }
        }
        // 品牌排序
        $order = array();
        foreach ($lists['brands']['list'] as $k => $v) {
            $order[] = $v['english_name'];
        }
        asort($order);
        $newList = array();
        foreach ($order as $k => $v) {
            $newList[] = $lists['brands']['list'][$k];
        }
        $lists['brands']['list'] = $newList;
        $lists['categorys']['list'] = array_merge($categories);
        return $lists;
    }
}
