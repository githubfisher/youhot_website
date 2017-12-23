<?php
class Superscript_model extends MY_Model
{
	const TBL_SS = 'superscript';
    const LIST_KEY = 'ss_list';
	const SS_LIST_CACHE_TIME = 300; // 5 min

    public function __construct()
    {
        parent::__construct();
        $this->load->library('cache');
    }

    public function getList($dc)
    {
	if ($dc == 10) {
	    return [
		[
                    'size' => '2x',
                    'url' => 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/album/fVLLPn_MEAyeC5wbmc.png',
                ],
                [
                    'size' => '3x',
                    'url' => 'http://product-album-n.img-cn-hangzhou.aliyuncs.com/album/RSvRre_MEAzeC5wbmc.png',
                ],
            ];
	}
        $key = md5('get_list:'.$dc);
        if ($key && ($data = $this->cache->get($key))) {
            log_debug('get store rows from cache');
            return $data;
        }

        $this->db_slave->select('size,url');
        $this->db_slave->order_by('size', 'asc');
        $this->db_slave->where('name =', $dc);
        $this->db_slave->where('type =', 1);
        $this->db_slave->where('status =', 1);
        $query = $this->db_slave->get(self::TBL_SS);
        $data = get_query_result($query);

        if ($key) {
            $this->cache->add_list(self::LIST_KEY, $key, $data, self::SS_LIST_CACHE_TIME);
        }

        return $data;
    }

}

