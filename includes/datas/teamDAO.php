<?php 

class TeamDAO extends MasterDAO {
    private static $_instance;

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
    function getAllTeams(){  
        $allTeams = [];
        $teams = $this->wpdb->get_results("SELECT * FROM {$this->t3} ORDER BY name DESC");
        foreach($teams as $row) { 
            $allTeams[] = new Team(
                $row->id, 
                $row->name, 
                $row->url, 
                $row->boy, 
                $row->girl, 
                $row->mixed, 
                $row->position, 
                $row->points, 
                $row->time, 
                $row->img, 
                ClubDAO::getInstance()->getClubById($row->clubId));
        }
        return $allTeams;
    }

    function getTeamById($myTeamId){    
        $row = $this->wpdb->get_row("SELECT * FROM {$this->t3} WHERE id=$myTeamId");
        return new Team(
                $row->id, 
                $row->name, 
                $row->url, 
                $row->boy, 
                $row->girl, 
                $row->mixed, 
                $row->position, 
                $row->points, 
                $row->time, 
                $row->img, 
                ClubDAO::getInstance()->getClubById($row->clubId)
            );
    }

    function getAllTeamsByClubAndSex($club, $sex){  
        $boy = $girl = $mixed = 0;
        if ($sex == "boy"){ 
            $boy = 1;
        } else if ($sex == "girl"){
            $girl = 1;
        } else if ($sex == "mixed"){
            $mixed = 1;
        } 
        $allTeams = [];
        $teams = $this->wpdb->get_results("SELECT * FROM {$this->t3} WHERE clubId={$club->getId()} AND boy=$boy AND girl=$girl AND mixed=$mixed");
        foreach($teams as $row) { 
            $allTeams[] = new Team($row->id, $row->name, $row->url, $row->boy, $row->girl, $row->mixed, $row->position, $row->points, $row->time, $row->img, ClubDAO::getInstance()->getClubById($row->clubId));
        }
        return $allTeams;
    }

    function getAllTeamsByClubOrderBySex($club){  
        $allTeams = [];
        $teams = $this->wpdb->get_results("SELECT * FROM {$this->t3} WHERE clubId={$club->getId()} ORDER BY name DESC");
        foreach($teams as $row) { 
            $allTeams[] = new Team($row->id, $row->name, $row->url, $row->boy, $row->girl, $row->mixed, $row->position, $row->points, $row->time, $row->img, ClubDAO::getInstance()->getClubById($row->clubId));
        }
        return $allTeams;
    }

    /***************************
    ********** UPDATE **********
    ****************************/
    function updateTeam($team){    
        if ($team->getId()){
            $data = array(
                'name' => $team->getName(), 
                'url' => $team->getUrl(), 
                'boy' => $team->getBoy(), 
                'girl' => $team->getGirl(), 
                'mixed' => $team->getMixed(), 
                'position' => $team->getPosition(), 
                'points' => $team->getPoints(),                      
                'time' => $team->getTime(), 
                'img' => $team->getImg(),  
                'clubId' => $team->getClub()->getId()
            );
            $where = array('id' => $team->getId());
            $this->wpdb->update("{$this->t3}", $data, $where);
        }
    }

    /***************************
    ********** INSERT **********
    ****************************/
    function insertTeam($team){
        if (!$team->getId()){
            $data = array(
                'name' => $team->getName(), 
                'url' => $team->getUrl(), 
                'boy' => $team->getBoy(), 
                'girl' => $team->getGirl(), 
                'mixed' => $team->getMixed(), 
                'position' => $team->getPosition(), 
                'points' => $team->getPoints(),
                'time' => $team->getTime(), 
                'img' => $team->getImg(),    
                'clubId' => $team->getClub()->getId()
            );
            $this->wpdb->insert("{$this->t3}", $data);
        }
        return $this->wpdb->insert_id;
    }

    /***************************
    ********** DELETE **********
    ****************************/
    function deleteTeamsNotIn($myTeamsId){  
        $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3} WHERE id NOT IN ($myTeamsId)", null));
    }
    function deleteTeam($teamId){ 
        if ($teamId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3} WHERE id=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3}", null));
        }
    }
}
?>