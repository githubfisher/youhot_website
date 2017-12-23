<?php

class Stat_model extends MY_Model
{
    const NAME = 'size';
    const CACHE_TIME = 3600;

    private $table;

    function __construct()
    {
        parent::__construct();

//        $this->getRedis();
        $this->load->library('mongodb');

    }

    public function set_params($options)
    {

        if ($options['data_model'] == 'product') {
            $this->table = $this->mongodb->get_collection(MONGO_DB_PRODUCT, MONGO_COL_PRODUCT_PREFIX . $options['userid']);
        } elseif ($options['data_model'] == 'collection') {
            $this->table = $this->mongodb->get_collection(MONGO_DB_COLLECTION, MONGO_COL_COLL_PREFIX . $options['userid']);
        } else {
            throw new ErrorException("data_model in options need to be product or collection");
        }
    }

//    public function get_list($user = null, $offset = 0, $limit = 20, $period = 'day', $filter = array())
//    {
////        $key = get_list_mem_key(self::NAME,array($offset,$limit,$filter));
////        $this->load_memcached();
////        if($res = $this->mem->get($key)){
////            log_debug('get size list from cache');
////            return $res;
////        }
//
//        //get user's product list
//        //get product's pv
//
//        $this->db_slave->start_cache();
//        $this->db_slave->where($filter);
//        $this->db_slave->stop_cache();
//
//        $total = $this->db_slave->count_all_results(TBL_SIZE);
//
//        $this->db_slave->offset($offset);
//        if (!empty($limit)) {
//            $this->db_slave->limit($limit);
//        }
//        $this->db_slave->order_by('order', 'asc');  //Maybe sort by other field
//
//        $query = $this->db_slave->get(TBL_SIZE);
//
//        $this->db_slave->flush_cache();
//
//        $data = array('total' => $total, 'list' => get_query_result($query));
//        $this->mem->set($key, $data, self::CACHE_TIME);
//        return $data;
//    }

    /**
     * @param int $offset
     * @param int $limit
     * @param string $period
     * @param null $filter ['db'=>*,'userid'=>*,'start'=>'','end'=>,'offset','limit','period']
     * @return array
     */
    public function get_pv($offset = 0, $limit = 10, $period = 'day', $filter = null)
    {

//        $author = element('author', $filter, null);
        try {

            $_aggregate = array();
//            if ($author) {
//                $_aggregate[] = ['$match' => array('author' => $author)];
//            }
            $_aggregate[] = ['$group' => ['_id' => '$col_id', 'total' => ['$sum' => 1]]];
            $_aggregate[] = ['$limit' => $limit];
            $_aggregate[] = ['$skip' => $offset];

            $cursor = $this->table->aggregate($_aggregate);
//            var_dump($this->table->find());
//            $cursor = $this->table->find([],['typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array']]);
        } catch (Exception $e) {
            echo $e->getMessage();
            log_error('[mongo]' . $e->getMessage());
            return DB_OPERATION_FAIL;
        }


        $data = [];
        foreach ($cursor as $document) {
//            var_dump($document);
//            $a = $document->bsonSerialize();
//            $a = serialize($document);
//            var_dump($a);
            $data[] = $document;
        }


        return $data;


    }

    public function get_uv()
    {
        $data = array(
            'total' => 200,
            'list' => array(
                'table' => $this->table,
                'data' => 'uv',
            )

        );

        return $data;
    }

    public function get_sales()
    {
        $data = array(
            'total' => 200,
            'list' => array(
                'table' => $this->table,
                'data' => 'sales',
            )

        );

        return $data;

    }

    public function get_revenue()
    {
        $data = array(
            'total' => 200,
            'list' => array(
                'table' => $this->table,
                'data' => 'revenue',
            )

        );

        return $data;

    }

    public function get_summary()
    {
        $data = array(
            'total' => 200,
            'list' => array(
                'table' => $this->table,
                'data' => 'sum',
            )

        );

        return $data;

    }


}
