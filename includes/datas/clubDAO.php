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

    /***************************
    ********** UPDATE **********
    ****************************/
    function updateClubs($myClubs){    

        $clubsIdToDelete;
        foreach($myClubs as $club) {
            if ($club->getId()) {
                $clubsIdToDelete .= $club->getId().", ";
            }
        }
        $this->deleteClubsNotIn(substr($clubsIdToDelete,0 , -2));

        $clubsToInsert = [];
        foreach($myClubs as $club) {
            if ($club->getId()){
                $data = array('name' => $club->getName(), 'string' => $club->getString(), 'boy' => $club->getBoy(), 'girl' => $club->getGirl(), 'mixed' => $club->getMixed(), 'adress' => $club->getAdress());
                $where = array('id' => $club->getId());
                $this->wpdb->update("{$this->t1}", $data, $where);
            } else {
                $clubsToInsert[] = $club;
            }
        }
        $this->insertClubs($clubsToInsert);
    }

    /***************************
    ********** INSERT **********
    ****************************/
    function insertClubs($myClubs){
        foreach($myClubs as $club) {
            if (!$club->getId()){
                $data = array('name' => $club->getName(), 'string' => $club->getString(), 'boy' => $club->getBoy(), 'girl' => $club->getGirl(), 'mixed' => $club->getMixed(), 'adress' => $club->getAdress());
                $this->wpdb->insert("{$this->t1}", $data);
            } 
        }
    }

    /***************************
    ********** DELETE **********
    ****************************/
    function deleteClubsNotIn($myClubsId){  
        $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t1} WHERE id NOT IN ($myClubsId)", null));
    }
    
    function deleteClub($clubId){ 
        if ($clubId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t1} WHERE id=$clubId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t1}", null));
        }
    }
}
?>