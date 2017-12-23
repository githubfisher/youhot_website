<?php

class Role_model extends MY_Model
{
    /**
     * 构造函数
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        log_message('debug', "Role_model Model Class Initialized");
    }

    public function has_admin_role($role,$userid,$rep_userid){
        switch($role){
            case "product_edit":

                break;
            case "collection":
                if ($this->is_admin && $this->has_admin_role(ADMIN_ROLE_PRODUCT)) return TRUE;
                $res = $this->collection->get_owner_and_assistant($collection_id);
                $owner = element('author', $res, null);
                if ($owner == $this->userid) {
                    return true;
                }
                return false;

                break;
            default:
                break;
        }



    }

}
