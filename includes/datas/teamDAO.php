<?php 
/**
* TeamDAO is a class use to manage acces to the Database to get Team objects
*
* @package  Includes/Datas
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
            ORDER BY 
                a.team_name DESC 
        ");
        foreach($teams as $row) { 
            $allTeams[] = new Team(
                $row->team_id, 
                $row->team_name, 
                $row->team_url, 
                $row->team_boy, 
                $row->team_girl, 
                $row->team_mixed, 
                $row->team_position, 
                $row->team_points, 
                $row->team_time, 
                $row->team_img, 
                new Club(
                    $row->club_id, 
                    $row->club_name, 
                    $row->club_string, 
                    $row->club_boy, 
                    $row->club_girl, 
                    $row->club_mixed, 
                    $row->club_address
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
        return new Team(
                $row->team_id, 
                $row->team_name, 
                $row->team_url, 
                $row->team_boy, 
                $row->team_girl, 
                $row->team_mixed, 
                $row->team_position, 
                $row->team_points, 
                $row->team_time, 
                $row->team_img, 
                new Club(
                    $row->club_id, 
                    $row->club_name, 
                    $row->club_string, 
                    $row->club_boy, 
                    $row->club_girl, 
                    $row->club_mixed, 
                    $row->club_address
                )
            );
    }

    //TODO : optimisation ?
    /**
    * Return teams corresponding to a club and sex label
    *
    * @param Club       The club
    * @param string     Sex label
    * @return Team[]    All the teams corresponding
    * @access public
    */
    function getAllTeamsByClubAndSex($club, $sex){  
        $boy = $girl = $mixed = 0;
        if ($sex == "boy"){ 
            $boy = 1;
        } else if ($sex == "girl"){
            $girl = 1;
        } else if ($sex == "mixed"){
            $mixed = 1;
        } 
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
                a.team_clubId={$club->getId()} AND 
                a.team_boy=$boy AND 
                a.team_girl=$girl AND 
                a.team_mixed=$mixed
            ORDER BY 
                a.team_name DESC 
        ");
        foreach($teams as $row) { 
            $allTeams[] = new Team(
                $row->team_id, 
                $row->team_name, 
                $row->team_url, 
                $row->team_boy, 
                $row->team_girl, 
                $row->team_mixed, 
                $row->team_position, 
                $row->team_points, 
                $row->team_time, 
                $row->team_img, 
                new Club(
                    $row->club_id, 
                    $row->club_name, 
                    $row->club_string, 
                    $row->club_boy, 
                    $row->club_girl, 
                    $row->club_mixed, 
                    $row->club_address
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
                a.team_name DESC 
        "); 
        foreach($teams as $row) { 
            $allTeams[] = new Team(
                $row->team_id, 
                $row->team_name, 
                $row->team_url, 
                $row->team_boy, 
                $row->team_girl, 
                $row->team_mixed, 
                $row->team_position, 
                $row->team_points, 
                $row->team_time, 
                $row->team_img, 
                new Club(
                    $row->club_id, 
                    $row->club_name, 
                    $row->club_string, 
                    $row->club_boy, 
                    $row->club_girl, 
                    $row->club_mixed, 
                    $row->club_address
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
                'team_url' => $team->getUrl(), 
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
                'team_url' => $team->getUrl(), 
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
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3} WHERE team_id=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3}", null));
        }
    }
}
?>