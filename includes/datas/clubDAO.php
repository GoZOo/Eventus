<?php 

class ClubDAO extends MasterDAO {
    private static $_instance;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new ClubDAO();
        }
        return self::$_instance;
    }

    protected function __construct() {
        parent::__construct();
    }

    /**************************
    *********** GET ***********
    ***************************/
    function getAllClubs(){    
        $allClubs = [];
        $clubs = $this->wpdb->get_results("SELECT * FROM {$this->t1}");
        foreach($clubs as $row) { 
            $allClubs[] = new Club($row->id, $row->name, $row->string, $row->boy, $row->girl, $row->mixed, $row->adress);
        }
        return $allClubs;
    }

    function getClubById($myClubId){   
        $row = $this->wpdb->get_row("SELECT * FROM {$this->t1} WHERE id=$myClubId");
        return new Club($row->id, $row->name, $row->string, $row->boy, $row->girl, $row->mixed, $row->adress);
    }

    function getInfosByClubId($idClub){  
        return $this->wpdb->get_row("SELECT a.id, count(b.id) as teams FROM {$this->t1} a LEFT JOIN {$this->t3} b ON a.id=b.clubId WHERE b.clubId=$idClub;");
    }
    /***************************
    ********** UPDATE **********
    ****************************/
    function updateClub($club){    
        if ($club->getId()){
            $data = array(
                'name' => $club->getName(), 
                'string' => $club->getString(), 
                'boy' => $club->getBoy(), 
                'girl' => $club->getGirl(), 
                'mixed' => $club->getMixed(), 
                'adress' => $club->getAdress()
            );
            $where = array('id' => $club->getId());
            $this->wpdb->update("{$this->t1}", $data, $where);
        }
    }

    /***************************
    ********** INSERT **********
    ****************************/
    function insertClub($club){
        if (!$club->getId()){            
            $data = array(
                'name' => $club->getName(), 
                'string' => $club->getString(), 
                'boy' => $club->getBoy(), 
                'girl' => $club->getGirl(), 
                'mixed' => $club->getMixed(), 
                'adress' => $club->getAdress()
            );
            $this->wpdb->insert("{$this->t1}", $data);
        }
        return $this->wpdb->insert_id;
    }

    /***************************
    ********** DELETE **********
    ****************************/
    function deleteClub($clubId){ 
        if ($clubId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t1} WHERE id=$clubId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t1}", null));
        }
    }
}
?>