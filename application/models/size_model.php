<?php

class Size_model extends MY_Model
{
    const NAME = 'size';
    const CACHE_TIME = 3600;
    const LIST_KEY = 'size_list';

    function __construct()
    {
        parent::__construct();
//        $this->getRedis();
        $this->load->library('cache');
    }

    public function get_list($offset = 0, $limit = 20, $filter = array())
    {
        $key = get_list_mem_key(self::NAME,array($offset,$limit,$filter));
        if($res = $this->cache->get($key)){
            log_debug('get size list from cache');
            return $res;
        }
        $this->db_slave->start_cache();
        $this->db_slave->where($filter);
        $this->db_slave->stop_cache();

        $total = $this->db_slave->count_all_results(TBL_SIZE);

        $this->db_slave->offset($offset);
        if (!empty($limit)) {
            $this->db_slave->limit($limit);
        }
        $this->db_slave->order_by('order', 'asc');  //Maybe sort by other field

        $query = $this->db_slave->get(TBL_SIZE);

        $this->db_slave->flush_cache();

        $data =array('total' => $total, 'list' => get_query_result($query));
        $this->cache->add_list(self::LIST_KEY,$key,$data,self::CACHE_TIME);
        return $data;
    }

    /**
     * 获取对应分类、品牌商品的尺码图
     * 返回商品尺码图的阿里云地址
     * @return string
     * author fisher
     * date 2017-03-15
     */
    public function get_url($filter)
    {
        $res = $this->getUrl($filter);
	// print_r($res);die;
        if (!$res) {
            if ($filter['category']) {
		$cateName =& $filter['category'];
		do {
		    $sql = 'SELECT b.`name`, b.`parent_id`, b.`level` FROM `category` a JOIN `category` b on a.`parent_id` = b.`id` WHERE a.`name` = "'.$cateName.'"';
// print_r($sql);die;
                    $query = $this->db_slave->query($sql);
		    $res = get_query_result($query);
// print_r($res);die;
		    if ($res) {
			$filter['category'] = $res[0]['name'];
			$cateLevel =& $res[0]['level']; 
			$res = $this->getUrl($filter);
			if (!$res) {
			    continue;
			}
		    }
	 	    break;
                } while ($cateLevel >= 1);
	    }
        }
        if (!$res) { // 还没找到，再提取默认图
            $this->db_slave->select('url')->from(TBL_SIZE_CHART)->where('is_default', 1);
            $query = $this->db_slave->get();
            $res = get_query_result($query);
        }

        return $res[0];
    }

    private function getUrl($filter)
    {
        $this->db_slave->select('url');
        $this->db_slave->where($filter);
        $query = $this->db_slave->get(TBL_SIZE_CHART);
        $res = get_query_result($query);

	return $res;
    }

    /**
     * 获取全部分类、品牌商品的尺码图
     * 返回商品尺码图的阿里云地址
     * @return string
     * author fisher
     * date 2017-03-15
     */
    public function get_list_url()
    {
        // $this->db_slave->from(TBL_SIZE_CHART);
        // $this->db_slave->select(TBL_SIZE_CHART.'.*, '.TBL_CATEGORY.'.name, '.TBL_USER.'.nickname');
        // $this->db_slave->join(TBL_USER, TBL_SIZE_CHART . '.brand = ' . TBL_USER . '.userid', 'left');
        // $this->db_slave->join(TBL_CATEGORY, TBL_SIZE_CHART . '.category = ' . TBL_CATEGORY . '.id', 'left');
        // $query = $this->db_slave->get(TBL_PRODUCT);

        // $query = 'select '.TBL_SIZE_CHART.'.*, '.TBL_CATEGORY.'.name, '.TBL_USER.'nickname from ('.TBL_SIZE_CHART.' left join '.TBL_CATEGORY.' on '.TBL_CATEGORY.'.id = '.TBL_SIZE_CHART.'.category)';
        // $query .= ' left join '.TBL_USER.' on '.TBL_USER.'.userid = '.TBL_SIZE_CHART.'.brand';

        $query = 'select u.username,category.name,user.nickname,size_chart.* from (size_chart left join category on category.id = size_chart.category) left join user on user.userid = size_chart.brand left join user as u on u.userid = size_chart.userid';
        $query = $this->db_slave->query($query);
        $res = get_query_result($query);
        return $res;
    }

    /**
     * 获取全部分类、品牌商品的尺码图
     * 返回商品尺码图的阿里云地址
     * @return array
     * author fisher
     * date 2017-03-15
     */
    public function get_list_url_new()
    {
       $query = 'select user.username,size_chart.* from size_chart left join user on user.userid = size_chart.userid';
        $query = $this->db_slave->query($query);
        $res = get_query_result($query);
        return $res;
    }

    /**
     * 返回分类ID
     * @return string
     * author fisher
     * date 2017-03-16
     */
    public function get_category_id($category_name)
    {
        $this->db_slave->select('id')->from(TBL_CATEGORY)->where('name', $category_name);
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        if ($res) {
            return $res[0]['id'];
        }
        return FALSE;
    }

    /**
     * 返回品牌ID
     * @return string
     * author fisher
     * date 2017-03-16
     */
    public function get_brand_id($brand_name)
    {
        $this->db_slave->select('userid')->from(TBL_USER)->where('nickname', $brand_name);
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        if ($res) {
            return $res[0]['userid'];
        }
        return FALSE; // 如果不存在对应品牌，返回否
    }

    /**
     * 新增尺码图表新记录
     * @return bool
     * author fisher
     * date 2017-03-16
     */
    public function create_size_chart($data)
    {
        $this->db_slave->insert(TBL_SIZE_CHART, $data);

        return ($this->db_slave->affected_rows() > 0) ? $this->db_slave->insert_id() : FALSE;
    }

    /**
     * 删除尺码图记录
     * @return bool
     * author fisher
     * date 2017-03-17
     */
    public function delete_size_chart($id)
    {
        $this->db_slave->where('id', (int)$id)->delete(TBL_SIZE_CHART);
        return ($this->db_slave->affected_rows() > 0) ? TRUE : FALSE;
    }

    /**
     * 读取尺码图记录
     * @return array
     * author fisher
     * date 2017-03-17
     */
    public function get_size_chart_info($id)
    {
        $query = 'select u.username,category.name,user.nickname,size_chart.* from (size_chart left join category on category.id = size_chart.category) left join user on user.userid = size_chart.brand left join user as u on u.userid = size_chart.userid where size_chart.id = '.$id;
        $query = $this->db_slave->query($query);
        $res = get_query_result($query);
        return $res[0];
    }

    
    /**
     * 读取尺码图记录
     * @return array
     * author fisher
     * date 2017-03-17
     */
    public function get_size_chart_info_new($id)
    {
        $this->db_slave->select('*')->from(TBL_SIZE_CHART)->where('id', (int)$id);
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        return $res[0];
    }

    /**
     * 更新尺码图记录
     * @return bool
     * author fisher
     * date 2017-03-17
     */
    public function update_size_chart($id, $data)
    {
        $this->db_slave->where('id', (int)$id);
        $this->db_slave->update(TBL_SIZE_CHART, $data);
        return ($this->db_slave->modified_rows() > 0) ? TRUE : FALSE;
    }

    /**
     * 所有分类
     * @return array
     * author fisher
     * date 2017-03-17
     */
    public function get_all_category()
    {
        $this->db_slave->select('id,name')->from(TBL_CATEGORY);
        $this->db_slave->order_by('name', 'asc');
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        return $res;
    }

    /**
     * 所有品牌
     * @return array
     * author fisher
     * date 2017-03-17
     */
    public function get_all_brand()
    {
        $this->db_slave->select('userid,nickname')->from(TBL_USER)->where('usertype',2);
        $this->db_slave->order_by('nickname', 'asc');
        $query = $this->db_slave->get();
        $res = get_query_result($query);
        return $res;
    }

    /**
     * 设某尺码图为默认尺码图
     * @return array
     * author fisher
     * date 2017-03-17
     */
    public function set_default($id)
    {
        $info = TRUE;
        $this->db_slave->where(array('is_default' => 1));
        $total = $this->db_slave->count_all_results(TBL_SIZE_CHART);
        if( $total > 0) {
            // 删除原默认图
            $this->db_slave->where('is_default', 1);
            $this->db_slave->update(TBL_SIZE_CHART, array('is_default' => 0));
            $info = ($this->db_slave->modified_rows() > 0) ? TRUE : FALSE;
        }
        
        if ($info) {
            $this->db_slave->where('id', (int)$id);
            $this->db_slave->update(TBL_SIZE_CHART, array('is_default' => 1));
            return ($this->db_slave->modified_rows() > 0) ? TRUE : FALSE;
        }
        return FALSE;
    }
}
