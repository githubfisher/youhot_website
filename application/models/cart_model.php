<?php

class Cart_model extends MY_Model
{
    const TB_CART = 'dealcart';

    public function get_list($userid = null)
    {
        if( empty($userid) ) return false;

        $this->db_slave->select('a.*, b.title, b.price, b.inventory, status, cover_image,b.store');
        $this->db_slave->where('userid', $userid);
        $this->db_slave->from(self::TB_CART .' as a');
        $this->db_slave->join(TBL_PRODUCT .' as b', "a.product_id=b.id");
        $this->db_slave->order_by('a.cctimes', 'desc');
        $query = $this->db_slave->get();
        $res = get_query_result($query);

        return array('total' => count($res), 'list' => $res);
    }

    // 增加商品
    public function add($data){
        $this->db_master->where(array(
            'userid'=>$data['userid'],
	    'product_id' => $data['product_id'], //add product_id by fisher at 2017-03-20
            'product_color'=>$data['product_color'],
            'product_size'=>$data['product_size'],
	    'referer'=>$data['referer'],
        ));
        $this->db_master->limit(1);
        $query = $this->db_master->get(self::TB_CART);
        $res = get_row_array($query);
        if( empty($res) ){
            //add
	    logger('new add to cart'); //debug
            $data['cctimes'] = time();
            $this->db_master->insert(self::TB_CART, $data);
        }else{
            //update
	    logger('update cart: '.var_export($res, true)); //debug
            $this->db_master->where('cartid', $res['cartid']);
            $this->db_master->set('product_count', '`product_count`+'.$data['product_count'], FALSE);
            $this->db_master->update(self::TB_CART);
            logger('update_cart:'.$this->db_master->last_query()); //debug
        }
        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;
    }

    // 编辑商品
    public function edit($data){
        $this->db_master->where(array(
            'userid'=>$data['userid'],
	    'product_id' => $data['product_id'], // add product_id by fisher at 2017-03-20
            'product_color'=>$data['product_color'],
            'product_size'=>$data['product_size']
        ));
        $this->db_master->limit(1);
        $query = $this->db_master->get(self::TB_CART);
        $res = get_row_array($query);
        if( empty($res) ){
            return FALSE;
        }else{
            //update
            $this->db_master->where('cartid', $res['cartid']);
            $this->db_master->set('product_count', $data['product_count'], FALSE);
            $this->db_master->update(self::TB_CART);
        }
        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;


        $this->db_master->where(array(
            'cartid'=>$id,
            'userid'=>$data['userid'],
        ));
        $query = $this->db_master->get(self::TB_CART);
        $res = get_row_array($query);
        if( empty($res) ){
            return FALSE;
        }else{
            //update
            $this->db_master->where('cartid', $res['cartid']);
            $this->db_master->update(self::TB_CART, array(
                'product_count'=>$data['product_count'],
            ));
        }
        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;
    }

    //delete
    public function delete($id, $userid){
        $this->db_master->where(array(
            'cartid'=>$id,
            'userid'=>$userid,
        ))->delete(self::TB_CART);
        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;
    }
    // 删除多个购物车商品
    public function delete_more($cartIds, $userid){
        $this->db_master->where(array('userid'=>$userid));
        $this->db_master->where_in('cartid', $cartIds);
        $this->db_master->delete(self::TB_CART);
        return ($this->db_master->affected_rows() > 0) ? TRUE : FALSE;
    }
}
