<?php 

namespace Eventus\Includes\Datas;
use Eventus\Includes\Entities as Entities;

/**
* ClubDAO is a class use to manage acces to the Database to get Club objects
*
* @package  Includes/Datas
* @access   public
*/
class ClubDAO extends MasterDAO {
    /**
    * @var Finder   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return ClubDAO
    * @access public
    */
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
    /**
    * Return every clubs
    *
    * @return Club[] All the clubs that exist
    * @access public
    */
    function getAllClubs(){    
        $allClubs = [];
        $clubs = $this->wpdb->get_results("
            SELECT * 
            FROM 
                {$this->t1}"
        );
        foreach($clubs as $row) { 
            $allClubs[] = new Entities\Club(
                $row->club_id, 
                $row->club_name, 
                $row->club_string, 
                $row->club_address, 
                $row->club_img
            );
        }
        return $allClubs;
    }

    /**
    * Return the club corresponding to an id
    *
    * @param int        Id of the club
    * @return Club      All the clubs that exist with the ClubId
    * @access public
    */
    function getClubById($myClubId){   
        $row = $this->wpdb->get_row("
            SELECT * 
            FROM 
                {$this->t1} 
            WHERE 
                club_id=$myClubId");
        return new Entities\Club(
            $row->club_id, 
            $row->club_name, 
            $row->club_string, 
            $row->club_address, 
            $row->club_img
        );
    }

    /**
    * Return informations by club id
    *
    * @param int        club id
    * @return string[]  Informations
    * @access public
    */
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

    /**
    * Return numbers of club
    *
    * @return string[]  Informations
    * @access public
    */
    function getNumbersClubs(){  
        return $this->wpdb->get_row("
            SELECT 
                count(DISTINCT club_id) as nbr_clubs
            FROM {$this->t1};
        ");
    }
    /***************************
    ********** UPDATE **********
    ****************************/
    /**
    * Update a club
    *
    * @param Club       Club to be updated
    * @return void  
    * @access public
    */
    function updateClub($club){    
        if ($club->getId()){
            $data = array(
                'club_name' => $club->getName(), 
                'club_string' => $club->getString(), 
                'club_address' => $club->getAddress(), 
                'club_img' => $club->getImg()
            );
            $where = array('club_id' => $club->getId());
            $this->wpdb->update("{$this->t1}", $data, $where);
        }
    }

    /***************************
    ********** INSERT **********
    ****************************/
    /**
    * Insert a Club
    *
    * @param Club       Club to be inserted
    * @return int       Id of the club inserted      
    * @access public
    */
    function insertClub($club){
        if (!$club->getId()){            
            $data = array(
                'club_name' => $club->getName(), 
                'club_string' => $club->getString(), 
                'club_address' => $club->getAddress(), 
                'club_img' => $club->getImg()
            );
            $this->wpdb->insert("{$this->t1}", $data);
        }
        return $this->wpdb->insert_id;
    }

    /***************************
    ********** DELETE **********
    ****************************/
    /**
    * Delete a club
    *
    * @param int|null   Id of Club to be deleted
    * @return void    
    * @access public
    */
    function deleteClub($clubId = null){ 
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