<?php

/**
 * Class Product
 * @method
 */
class Size extends Normal_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('size_model', 'size');
    }

    /**
     * save size
     * @todo save size
     */
    public function save()
    {

    }

    public function get_list()
    {

        $offset = (int)$this->input->get_post('of');
        $limit = (int)$this->input->get_post('lm');
        $res = $this->size->get_list($offset, $limit);
        if (empty($res['list'])) {
            log_message('error', 'size list error:' . json_encode($res));
            $this->_error(bp_operation_fail, $this->lang->line('hint_data_is_empty'));
        } else {
            $res['count'] = count($res['list']);
            $this->_result(bp_operation_ok, $res);
        }


    }

    /**
     * 获取对应分类、品牌商品的尺码图
     * 返回商品尺码图的阿里云地址
     * @return string
     * author fisher
     * date 2017-03-15
     */
    public function getSizeUrl()
    {
        $category = $this->input->get_post('cate'); // 分类ID
        $brand = $this->input->get_post('br'); // 品牌ID

        $filter = array();
        if (!empty($category)) {
            $filter['category'] = $category;
        }
        if (!empty($brand)) {
            $filter['brand'] = $brand;
        }
        if (empty($filter)) {
            $this->_error(bp_operation_fail, '缺少查询参数');
        }

        $res = $this->size->get_url($filter);
        if (!$res) {
            $res = array(
                'res' => 0,
                'url' => 'http://default.com'  // 如果没有查询到对应尺码图，返回默认的尺码图
            );
        }
        echo_json($res);
    }

    /**
     * 获取全部分类、品牌商品的尺码图 测试
     * author fisher
     * date 2017-03-15
     */
    public function getAllSizeUrl()
    {
        $res = $this->size->get_list_url();
        echo_json($res);
    }
}
