<?php 

namespace Eventus\Includes\Datas;

/**
* MasterDAO is a parent class for all DAOs
*
* @package  Includes/Datas
* @access   public
*/
abstract class MasterDAO {
    const NAME = "eventus";
    const TABLE_NAME1 = '_clubs';
    const TABLE_NAME2 = '_matches';
    const TABLE_NAME3 = '_teams';

    protected function __construct() {
        $this->wpdb = $GLOBALS['wpdb'];
        $this->t1 = $this->wpdb->prefix.MasterDAO::NAME.MasterDAO::TABLE_NAME1;       
        $this->t2 = $this->wpdb->prefix.MasterDAO::NAME.MasterDAO::TABLE_NAME2;
        $this->t3 = $this->wpdb->prefix.MasterDAO::NAME.MasterDAO::TABLE_NAME3;  
    }
}

?>