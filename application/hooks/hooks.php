<?php

function closeDb(){
    $CI = & get_instance();
    if(isset($CI->db_master)){
        $CI->db_master->close();
        //is null
        log_debug('post system:db master closed');
    }
    if(isset($CI->db_slave)){
        $CI->db_slave->close();
    }
    if(isset($CI->cdb_master)){
        $CI->cdb_master->close();
    }
    if(isset($CI->cdb_slave)){
        $CI->cdb_slave->close();
    }
}
