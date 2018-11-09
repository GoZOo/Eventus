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
        $clubs = $this->wpdb->get_results("
            SELECT * 
            FROM 
                {$this->t1}"
        );
        foreach($clubs as $row) { 
            $allClubs[] = new Club(
                $row->club_id, 
                $row->club_name, 
                $row->club_string, 
                $row->club_boy, 
                $row->club_girl, 
                $row->club_mixed, 
                $row->club_address
            );
        }
        return $allClubs;
    }

    function getClubById($myClubId){   
        $row = $this->wpdb->get_row("
            SELECT * 
            FROM 
                {$this->t1} 
            WHERE 
                id=$myClubId");
        return new Club(
            $row->club_id, 
            $row->club_name, 
            $row->club_string, 
            $row->club_boy, 
            $row->club_girl, 
            $row->club_mixed, 
            $row->club_address
        );
    }

    function getInfosByClubId($idClub){  
        return $this->wpdb->get_row("
            SELECT 
                a.club_id, 
                count(b.team_id) as teamsNbr 
            FROM 
                {$this->t1} a 
            LEFT JOIN 
                {$this->t3} b 
            ON 
                a.club_id = b.team_clubId 
            WHERE 
                b.team_clubId=$idClub;");
    }
    /***************************
    ********** UPDATE **********
    ****************************/
    function updateClub($club){    
        if ($club->getId()){
            $data = array(
                'club_name' => $club->getName(), 
                'club_string' => $club->getString(), 
                'club_boy' => $club->getBoy(), 
                'club_girl' => $club->getGirl(), 
                'club_mixed' => $club->getMixed(), 
                'club_address' => $club->getAddress()
            );
            $where = array('club_id' => $club->getId());
            $this->wpdb->update("{$this->t1}", $data, $where);
        }
    }

    /***************************
    ********** INSERT **********
    ****************************/
    function insertClub($club){
        if (!$club->getId()){            
            $data = array(
                'club_name' => $club->getName(), 
                'club_string' => $club->getString(), 
                'club_boy' => $club->getBoy(), 
                'club_girl' => $club->getGirl(), 
                'club_mixed' => $club->getMixed(), 
                'club_address' => $club->getAddress()
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
            $this->wpdb->query($this->wpdb->prepare("
                DELETE FROM 
                    {$this->t1} 
                WHERE  
                    club_id=$clubId", 
                null)
            );
        } else {
            $this->wpdb->query($this->wpdb->prepare("
                DELETE FROM 
                    {$this->t1}", 
                null)
            );
        }
    }
}
?>