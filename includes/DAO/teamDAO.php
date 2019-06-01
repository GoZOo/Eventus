<?php 

namespace Eventus\Includes\DAO;
use Eventus\Includes\DTO as Entities;

/**
* TeamDAO is a class use to manage acces to the Database to get Team objects
*
* @package  Includes//DAO
* @access   public
*/
class TeamDAO extends MasterDAO {
    /**
    * @var Finder   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return TeamDAO
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new TeamDAO();
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
    * Return every teams
    *
    * @return Team[] All the teams that exist
    * @access public
    */
    function getAllTeams(){  
        $teams = $this->wpdb->get_results("
            SELECT 
                *
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.team_clubId = b.club_id 
            WHERE
                b.club_season='{$this->season}'
            ORDER BY 
                a.team_name DESC 
        ");
        $allTeams = [];
        foreach($teams as $row) { 
            $allTeams[] = new Entities\Team(
                $row->team_id, 
                $row->team_name, 
                $row->team_urlOne, 
                $row->team_urlTwo, 
                $row->team_boy, 
                $row->team_girl, 
                $row->team_mixed, 
                $row->team_position, 
                $row->team_points, 
                $row->team_time, 
                $row->team_img, 
                new Entities\Club(
                    $row->club_id, 
                    $row->club_name, 
                    $row->club_string, 
                    $row->club_address, 
                    $row->club_img,
                    $row->club_season
                )
            );
        }
        return $allTeams;
    }

    /**
    * Return the team corresponding to an id
    *
    * @param int        Id of the team
    * @return Team      Team that exist with the TeamId
    * @access public
    */
    function getTeamById($myTeamId){    
        $row = $this->wpdb->get_row("
            SELECT 
                *
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.team_clubId = b.club_id 
            WHERE 
                a.team_id=$myTeamId
        ");
        return new Entities\Team(
                $row ? $row->team_id : 0, 
                $row ? $row->team_name : null,
                $row ? $row->team_urlOne : null,
                $row ? $row->team_urlTwo : null,
                $row ? $row->team_boy : null,
                $row ? $row->team_girl : null,
                $row ? $row->team_mixed : null,
                $row ? $row->team_position : null,
                $row ? $row->team_points : null,
                $row ? $row->team_time : null,
                $row ? $row->team_img : null,
                new Entities\Club(
                    $row ? $row->club_id : null,
                    $row ? $row->club_name : null,
                    $row ? $row->club_string : null,
                    $row ? $row->club_address : null,
                    $row ? $row->club_img : null,
                    $row ? $row->club_season : null
                )
            );
    }

    /**
    * Return teams corresponding to a club for match carousel
    *
    * @param Club       The club
    * @param string     Sex label
    * @return Team[]    All the teams corresponding
    * @access public
    */
    function getAllTeamsByClubOrderBySex($club){  
        $teams = $this->wpdb->get_results("
            SELECT 
                *
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.team_clubId = b.club_id 
            WHERE 
                a.team_clubId={$club->getId()} 
            ORDER BY 
                a.team_boy=1 DESC, 
                a.team_girl=1 DESC,
                a.team_mixed=1 DESC,
                (CASE
                    WHEN LOWER(a.team_name) LIKE '%senior%' THEN 1 
                    WHEN LOWER(a.team_name) LIKE '-%' THEN 2  
                    WHEN LOWER(a.team_name) LIKE '%ecole%' THEN 3  
                    WHEN LOWER(a.team_name) LIKE '%mini%' THEN 4 
                    WHEN LOWER(a.team_name) LIKE '%loisir%' THEN 5 
                    ELSE 6 
                END)
        ");
        $allTeams = [];
        foreach($teams as $row) { 
            $allTeams[] = new Entities\Team(
                $row->team_id, 
                $row->team_name, 
                $row->team_urlOne, 
                $row->team_urlTwo,  
                $row->team_boy, 
                $row->team_girl, 
                $row->team_mixed, 
                $row->team_position, 
                $row->team_points, 
                $row->team_time, 
                $row->team_img, 
                new Entities\Club(
                    $row->club_id, 
                    $row->club_name, 
                    $row->club_string, 
                    $row->club_address, 
                    $row->club_img,
                    $row->club_season
                )
            );
        }
        return $allTeams;
    }

    /**
    * Return teams corresponding to a club order by name
    *
    * @param Club       The club
    * @return Team[]    All the teams corresponding
    * @access public
    */
    function getAllTeamsByClubOrderByName($club){ 
        $teams = $this->wpdb->get_results("
            SELECT 
                *
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.team_clubId = b.club_id 
            WHERE 
                a.team_clubId={$club->getId()}   
            ORDER BY             
                (CASE
                    WHEN LOWER(a.team_name) LIKE '%senior%' THEN 1 
                    WHEN LOWER(a.team_name) LIKE '-%' THEN 2  
                    WHEN LOWER(a.team_name) LIKE '%ecole%' THEN 3  
                    WHEN LOWER(a.team_name) LIKE '%mini%' THEN 4 
                    WHEN LOWER(a.team_name) LIKE '%loisir%' THEN 5 
                    ELSE 6 
                END)
        "); 
        $allTeams = [];
        foreach($teams as $row) { 
            $allTeams[] = new Entities\Team(
                $row->team_id, 
                $row->team_name, 
                $row->team_urlOne, 
                $row->team_urlTwo, 
                $row->team_boy, 
                $row->team_girl, 
                $row->team_mixed, 
                $row->team_position, 
                $row->team_points, 
                $row->team_time, 
                $row->team_img, 
                new Entities\Club(
                    $row->club_id, 
                    $row->club_name, 
                    $row->club_string, 
                    $row->club_address, 
                    $row->club_img,
                    $row->club_season
                )
            );
        }
        return $allTeams;
    }
    
    /**
    * Return informations by team id
    *
    * @param int        Team id
    * @return string[]  Informations
    * @access public
    */
    function getInfosByTeamId($idTeam){  
        return $this->wpdb->get_row("
            SELECT 
                a.team_id,
                a.team_clubId,
                count(b.match_id) as matchsNbr 
            FROM {$this->t3} a 
            LEFT JOIN {$this->t2} b ON a.team_id = b.match_idTeam 
            WHERE 
                b.match_type IN (0,2) AND 
                b.match_idTeam=$idTeam;
        ");
    }

    /**
    * Return numbers of team
    *
    * @return string[]  Informations
    * @access public
    */
    function getNumbersTeams(){  
        return $this->wpdb->get_row("
            SELECT 
                count(DISTINCT team_id) as nbr_teams
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.team_clubId = b.club_id 
            WHERE
                b.club_season='{$this->season}';
        ");
    }

    /***************************
    ********** UPDATE **********
    ****************************/
    /**
    * Update a team
    *
    * @param Team       Team to be updated
    * @return void  
    * @access public
    */
    function updateTeam($team){    
        if ($team->getId()){
            $data = array(
                'team_name' => $team->getName(), 
                'team_urlOne' => $team->getUrlOne(), 
                'team_urlTwo' => $team->getUrlTwo(), 
                'team_boy' => $team->getBoy(), 
                'team_girl' => $team->getGirl(), 
                'team_mixed' => $team->getMixed(), 
                'team_position' => $team->getPosition(), 
                'team_points' => $team->getPoints(),                      
                'team_time' => $team->getTime(), 
                'team_img' => $team->getImg(),  
                'team_clubId' => $team->getClub()->getId()
            );
            $where = array('team_id' => $team->getId());
            $this->wpdb->update("{$this->t3}", $data, $where);
        }
    }

    /***************************
    ********** INSERT **********
    ****************************/
    /**
    * Insert a team
    *
    * @param Team       Team to be inserted
    * @return int       Id of the team inserted      
    * @access public
    */
    function insertTeam($team){
        if (!$team->getId()){
            $data = array(
                'team_name' => $team->getName(), 
                'team_urlOne' => $team->getUrlOne(), 
                'team_urlTwo' => $team->getUrlTwo(), 
                'team_boy' => $team->getBoy(), 
                'team_girl' => $team->getGirl(), 
                'team_mixed' => $team->getMixed(), 
                'team_position' => $team->getPosition(), 
                'team_points' => $team->getPoints(),                      
                'team_time' => $team->getTime(), 
                'team_img' => $team->getImg(),  
                'team_clubId' => $team->getClub()->getId()
            );
            $this->wpdb->insert("{$this->t3}", $data);
        }
        return $this->wpdb->insert_id;
    }

    /***************************
    ********** DELETE **********
    ****************************/
    /**
    * Delete a team
    *
    * @param int|null   Id of Team to be deleted
    * @return void    
    * @access public
    */
    function deleteTeam($teamId = null){ 
        if ($teamId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3} WHERE team_id=%", $teamId));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3} WHERE 1=%d", 1));
        }
    }
}
?>