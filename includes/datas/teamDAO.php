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
        $teams = $this->wpdb->get_results("
            SELECT 
                a.*, 
                b.name as 'clubName', 
                b.string as 'clubString', 
                b.boy as 'clubBoy', 
                b.girl as 'clubGirl', 
                b.mixed as 'clubMixed', 
                b.adress as 'clubAdress' 
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.clubId = b.id 
            ORDER BY 
                a.name DESC 
        ");
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
                new Club(
                    $row->clubId,
                    $row->clubName,
                    $row->clubString,
                    $row->clubBoy,
                    $row->clubGirl,
                    $row->clubMixed,
                    $row->clubAdress
                )
            );
        }
        return $allTeams;
    }

    function getTeamById($myTeamId){    
        $row = $this->wpdb->get_row("
            SELECT 
                a.*, 
                b.name as 'clubName', 
                b.string as 'clubString', 
                b.boy as 'clubBoy', 
                b.girl as 'clubGirl', 
                b.mixed as 'clubMixed', 
                b.adress as 'clubAdress' 
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.clubId = b.id 
            WHERE 
                a.id=$myTeamId
        ");
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
                new Club(
                    $row->clubId,
                    $row->clubName,
                    $row->clubString,
                    $row->clubBoy,
                    $row->clubGirl,
                    $row->clubMixed,
                    $row->clubAdress
                )
            );
    }

    //A opti ?
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
                a.*, 
                b.name as 'clubName', 
                b.string as 'clubString', 
                b.boy as 'clubBoy', 
                b.girl as 'clubGirl', 
                b.mixed as 'clubMixed', 
                b.adress as 'clubAdress' 
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.clubId = b.id 
            WHERE 
                a.clubId={$club->getId()} AND 
                a.boy=$boy AND 
                a.girl=$girl AND 
                a.mixed=$mixed
            ORDER BY 
                a.name DESC 
        ");
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
                new Club(
                    $row->clubId,
                    $row->clubName,
                    $row->clubString,
                    $row->clubBoy,
                    $row->clubGirl,
                    $row->clubMixed,
                    $row->clubAdress
                )
            );
        }
        return $allTeams;
    }

    function getAllTeamsByClubOrderByName($club){ 
        $teams = $this->wpdb->get_results("
            SELECT 
                a.*, 
                b.name as 'clubName', 
                b.string as 'clubString', 
                b.boy as 'clubBoy', 
                b.girl as 'clubGirl', 
                b.mixed as 'clubMixed', 
                b.adress as 'clubAdress' 
            FROM 
                {$this->t3} a 
            LEFT JOIN 
                {$this->t1} b 
            ON 
                a.clubId = b.id 
            WHERE 
                a.clubId={$club->getId()}
            ORDER BY 
                a.name DESC 
        "); 
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
                new Club(
                    $row->clubId,
                    $row->clubName,
                    $row->clubString,
                    $row->clubBoy,
                    $row->clubGirl,
                    $row->clubMixed,
                    $row->clubAdress
                )
            );
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
    function deleteTeam($teamId){ 
        if ($teamId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3} WHERE id=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t3}", null));
        }
    }
}
?>