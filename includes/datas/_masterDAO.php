<?php 

abstract class MasterDAO {
    protected function __construct() {
        $this->wpdb = $GLOBALS['wpdb'];
        $this->t1 = $this->wpdb->prefix.Constants::NAME.Constants::TABLE_NAME1;       
        $this->t2 = $this->wpdb->prefix.Constants::NAME.Constants::TABLE_NAME2;
        $this->t3 = $this->wpdb->prefix.Constants::NAME.Constants::TABLE_NAME3;  
    }
}

?>