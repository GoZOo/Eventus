<?php 

class MatchDAO extends MasterDAO {
    private static $_instance;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new MatchDAO();
        }
        return self::$_instance;
    }

    protected function __construct() {
        parent::__construct();
    }
    /**************************
    *********** GET ***********
    ***************************/
    function getAllMatchesByTeamId($idTeam){  
        $allMatches = [];
        $teams = $this->wpdb->get_results("
            SELECT 
                a.*, 
                b.*, 
                c.*, 
                d.match_id                  as 'refMatch_id',
                d.match_numMatch            as 'refMatch_numMatch',
                d.match_matchDay            as 'refMatch_matchDay',
                d.match_date                as 'refMatch_date',
                d.match_hourRdv             as 'refMatch_hourRdv',
                d.match_hourStart           as 'refMatch_hourStart',
                d.match_localTeam           as 'refMatch_localTeam',
                d.match_localTeamScore      as 'refMatch_localTeamScore',
                d.match_visitingTeam        as 'refMatch_visitingTeam',
                d.match_visitingTeamScore   as 'refMatch_visitingTeamScore',
                d.match_ext                 as 'refMatch_ext',
                d.match_street              as 'refMatch_street',
                d.match_city                as 'refMatch_city',
                d.match_gym                 as 'refMatch_gym',
                d.match_type                as 'refMatch_type'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t3} b ON a.match_idTeam = b.team_id 
            LEFT JOIN {$this->t1} c ON b.team_clubId = c.club_id 
            LEFT JOIN {$this->t2} d ON a.match_idMatchRef = d.match_id
            WHERE 
                a.match_idTeam=$idTeam"
        );
        foreach($teams as $row) { 
            $allMatches[] = new Match(
                $row->match_id, 
                $row->match_matchDay,
                $row->match_numMatch,
                $row->match_date, 
                $row->match_hourRdv, 
                $row->match_hourStart, 
                $row->match_localTeam, 
                $row->match_localTeamScore, 
                $row->match_visitingTeam, 
                $row->match_visitingTeamScore, 
                $row->match_ext, 
                $row->match_street, 
                $row->match_city, 
                $row->match_gym, 
                $row->match_type, 
                new Team(
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
                ),
                $row->idMatchRef ? 
                    new Match(
                        $row->refMatch_id, 
                        $row->refMatch_matchDay,
                        $row->refMatch_numMatch,
                        $row->refMatch_date,
                        $row->refMatch_hourRdv, 
                        $row->refMatch_hourStart, 
                        $row->refMatch_localTeam, 
                        $row->refMatch_localTeamScore, 
                        $row->refMatch_visitingTeam, 
                        $row->refMatch_visitingTeamScore, 
                        $row->refMatch_ext, 
                        $row->refMatch_street, 
                        $row->refMatch_city, 
                        $row->refMatch_gym, 
                        $row->refMatch_type, 
                        new Team(
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
                        )
                    ) : null
            );
        }
        return $allMatches;
    }

    //TODO les where/orderby sont basé uniquement sur les matchs parents...
    function getAllMatchesByTeamIdAndType($idTeam, $type){  
        $teams = $this->wpdb->get_results("
            SELECT 
                a.*, 
                b.*, 
                c.*, 
                d.match_id                  as 'refMatch_id',
                d.match_numMatch            as 'refMatch_numMatch',
                d.match_matchDay            as 'refMatch_matchDay',
                d.match_date                as 'refMatch_date',
                d.match_hourRdv             as 'refMatch_hourRdv',
                d.match_hourStart           as 'refMatch_hourStart',
                d.match_localTeam           as 'refMatch_localTeam',
                d.match_localTeamScore      as 'refMatch_localTeamScore',
                d.match_visitingTeam        as 'refMatch_visitingTeam',
                d.match_visitingTeamScore   as 'refMatch_visitingTeamScore',
                d.match_ext                 as 'refMatch_ext',
                d.match_street              as 'refMatch_street',
                d.match_city                as 'refMatch_city',
                d.match_gym                 as 'refMatch_gym',
                d.match_type                as 'refMatch_type'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t3} b ON a.match_idTeam = b.team_id 
            LEFT JOIN {$this->t1} c ON b.team_clubId = c.club_id 
            LEFT JOIN {$this->t2} d ON a.match_idMatchRef = d.match_id
            WHERE 
                a.match_idTeam=$idTeam AND
                a.match_type=$type
            ORDER BY 
                a.match_matchDay,
                CASE WHEN a.match_date IS NULL THEN 1 ELSE 0 END, 
                a.match_date ASC, 
                a.match_hourStart;
        ");
        $allMatches = [];
        foreach($teams as $row) { 
            $allMatches[] = new Match(
                $row->match_id, 
                $row->match_matchDay,
                $row->match_numMatch,
                $row->match_date, 
                $row->match_hourRdv, 
                $row->match_hourStart, 
                $row->match_localTeam, 
                $row->match_localTeamScore, 
                $row->match_visitingTeam, 
                $row->match_visitingTeamScore, 
                $row->match_ext, 
                $row->match_street, 
                $row->match_city, 
                $row->match_gym, 
                $row->match_type, 
                new Team(
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
                ),
                $row->match_idMatchRef ? 
                    new Match(
                        $row->refMatch_id, 
                        $row->refMatch_matchDay,
                        $row->refMatch_numMatch,
                        $row->refMatch_date,
                        $row->refMatch_hourRdv, 
                        $row->refMatch_hourStart, 
                        $row->refMatch_localTeam, 
                        $row->refMatch_localTeamScore, 
                        $row->refMatch_visitingTeam, 
                        $row->refMatch_visitingTeamScore, 
                        $row->refMatch_ext, 
                        $row->refMatch_street, 
                        $row->refMatch_city, 
                        $row->refMatch_gym, 
                        $row->refMatch_type, 
                        new Team(
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
                        )
                    ) : null
            );
        }
        return $allMatches;
    }

    //TODO les where/orderby sont basé uniquement sur les matchs parents...
    function getCloseMatchByTeamId($teamId, $close){  
        $row = $this->wpdb->get_row("
            SELECT                 
                a.*, 
                b.match_id                  as 'refMatch_id',
                b.match_numMatch            as 'refMatch_numMatch',
                b.match_matchDay            as 'refMatch_matchDay',
                b.match_date                as 'refMatch_date',
                b.match_hourRdv             as 'refMatch_hourRdv',
                b.match_hourStart           as 'refMatch_hourStart',
                b.match_localTeam           as 'refMatch_localTeam',
                b.match_localTeamScore      as 'refMatch_localTeamScore',
                b.match_visitingTeam        as 'refMatch_visitingTeam',
                b.match_visitingTeamScore   as 'refMatch_visitingTeamScore',
                b.match_ext                 as 'refMatch_ext',
                b.match_street              as 'refMatch_street',
                b.match_city                as 'refMatch_city',
                b.match_gym                 as 'refMatch_gym',
                b.match_type                as 'refMatch_type'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t2} b ON a.match_idMatchRef = b.match_id
            WHERE 
                a.match_idTeam=$teamId AND
                a.match_date ". ($close=='next' ? '>=' : '<')." CURDATE()
            ORDER BY 
                a.match_date ". ($close=='next' ? 'ASC' : 'DESC').", 
                a.match_hourStart 
            ASC LIMIT 1;
        ");      
        if (!$row->match_idMatchRef){
            return new Match(
                $row->match_id, 
                $row->match_matchDay,
                $row->match_numMatch,
                $row->match_date, 
                $row->match_hourRdv, 
                $row->match_hourStart, 
                $row->match_localTeam, 
                $row->match_localTeamScore, 
                $row->match_visitingTeam, 
                $row->match_visitingTeamScore, 
                $row->match_ext, 
                $row->match_street, 
                $row->match_city, 
                $row->match_gym, 
                $row->match_type, 
                null,
                null
            );
        } else {
            return new Match(
                $row->refMatch_id, 
                $row->refMatch_matchDay,
                $row->refMatch_numMatch,
                $row->refMatch_date,
                $row->refMatch_hourRdv, 
                $row->refMatch_hourStart, 
                $row->refMatch_localTeam, 
                $row->refMatch_localTeamScore, 
                $row->refMatch_visitingTeam, 
                $row->refMatch_visitingTeamScore, 
                $row->refMatch_ext, 
                $row->refMatch_street, 
                $row->refMatch_city, 
                $row->refMatch_gym, 
                $row->refMatch_type, 
                null,
                null
            );
        }
        return;     
    }
    
    //TODO les where/orderby sont basé uniquement sur les matchs parents...
    function getMatchesWithDate(){  
        $allMatches = [];
        $matches = $this->wpdb->get_results("
            SELECT                 
                a.*,            
                b.*,            
                c.*, 
                d.match_id                  as 'refMatch_id',
                d.match_numMatch            as 'refMatch_numMatch',
                d.match_matchDay            as 'refMatch_matchDay',
                d.match_date                as 'refMatch_date',
                d.match_hourRdv             as 'refMatch_hourRdv',
                d.match_hourStart           as 'refMatch_hourStart',
                d.match_localTeam           as 'refMatch_localTeam',
                d.match_localTeamScore      as 'refMatch_localTeamScore',
                d.match_visitingTeam        as 'refMatch_visitingTeam',
                d.match_visitingTeamScore   as 'refMatch_visitingTeamScore',
                d.match_ext                 as 'refMatch_ext',
                d.match_street              as 'refMatch_street',
                d.match_city                as 'refMatch_city',
                d.match_gym                 as 'refMatch_gym',
                d.match_type                as 'refMatch_type'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t3} b ON a.match_idTeam = b.team_id 
            LEFT JOIN {$this->t1} c ON b.team_clubId = c.club_id 
            LEFT JOIN {$this->t2} d ON a.match_idMatchRef = d.match_id
            WHERE 
                a.match_date IS NOT NULL AND 
                a.match_type IN (0,2) AND
                a.match_date > DATE_SUB(NOW(), INTERVAL 1 DAY)
            ORDER BY 
                a.match_date, 
                b.team_name asc,
                a.match_numMatch asc;"
        );  
        foreach($matches as $row) { 
            $allMatches[] = new Match(
                $row->match_id, 
                $row->match_matchDay,
                $row->match_numMatch,
                $row->match_date, 
                $row->match_hourRdv, 
                $row->match_hourStart, 
                $row->match_localTeam, 
                $row->match_localTeamScore, 
                $row->match_visitingTeam, 
                $row->match_visitingTeamScore, 
                $row->match_ext, 
                $row->match_street, 
                $row->match_city, 
                $row->match_gym, 
                $row->match_type, 
                new Team(
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
                ),
                $row->match_idMatchRef ? 
                    new Match(
                        $row->refMatch_id, 
                        $row->refMatch_matchDay,
                        $row->refMatch_numMatch,
                        $row->refMatch_date,
                        $row->refMatch_hourRdv, 
                        $row->refMatch_hourStart, 
                        $row->refMatch_localTeam, 
                        $row->refMatch_localTeamScore, 
                        $row->refMatch_visitingTeam, 
                        $row->refMatch_visitingTeamScore, 
                        $row->refMatch_ext, 
                        $row->refMatch_street, 
                        $row->refMatch_city, 
                        $row->refMatch_gym, 
                        $row->refMatch_type, 
                        new Team(
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
                        )
                    ) : null
            );      
        }
        return $allMatches;
    }

    /***************************
    ********** UPDATE **********
    ****************************/
    function updateMatchesSync($allMatches){
        $matchesToInsert = [];
        foreach($allMatches as $match) {
            var_dump($match);
            $myId = $this->wpdb->get_row("
                SELECT match_id 
                FROM {$this->t2} 
                WHERE 
                    match_matchDay={$match->getMatchDay()} AND 
                    match_idTeam={$match->getTeam()->getId()} AND 
                    match_numMatch={$match->getNumMatch()}"
            )->match_id;
            var_dump($myId);
            if($myId){
                $data = array(
                    'match_matchDay' => $match->getMatchDay(), 
                    'match_numMatch' => $match->getNumMatch(), 
                    'match_date' => $match->getDate(), 
                    'match_hourRdv' => $match->getHourRdv(), 
                    'match_hourStart' => $match->getHourStart(), 
                    'match_localTeam' => $match->getLocalTeam(), 
                    'match_localTeamScore' => $match->getLocalTeamScore(), 
                    'match_visitingTeam' => $match->getVisitingTeam(), 
                    'match_visitingTeamScore' => $match->getVisitingTeamScore(), 
                    'match_ext' => $match->getExt(),
                    'match_street' => $match->getStreet(), 
                    'match_city' => $match->getCity(),
                    'match_gym' => $match->getGym(),
                    'match_type' => $match->getType(), 
                    'match_idTeam' => $match->getTeam()->getId(),
                    'match_idMatchRef' => ($match->getMatchRef() ? $match->getMatchRef() : null)
                );
                $this->wpdb->update("{$this->t2}", $data, $where);
            } else {
                $matchesToInsert[] = $match;
            }
        }
        //var_dump($matchesToInsert);
        $this->insertMatches($matchesToInsert);
    }

    function updateMatchesScreen($allMatches, $type, $teamId){
        foreach($allMatches as $matches) {
            if ($matches->getId()){
                $matchesIdToDelete .= $matches->getId().", ";
            }            
        }
        //echo $matchesIdToDelete;
        $this->deleteMatchesNotIn(substr($matchesIdToDelete,0 , -2), $type, $teamId);

        $matchesToInsert = [];
        foreach($allMatches as $match) {
            //var_dump($match);            
            if($match->getId()){
                //echo "<br>IN IF<br>";
                $data = array(
                    'match_matchDay' => $match->getMatchDay(), 
                    'match_numMatch' => $match->getNumMatch(), 
                    'match_date' => $match->getDate(), 
                    'match_hourRdv' => $match->getHourRdv(), 
                    'match_hourStart' => $match->getHourStart(), 
                    'match_localTeam' => $match->getLocalTeam(), 
                    'match_localTeamScore' => $match->getLocalTeamScore(), 
                    'match_visitingTeam' => $match->getVisitingTeam(), 
                    'match_visitingTeamScore' => $match->getVisitingTeamScore(), 
                    'match_ext' => $match->getExt(),
                    'match_street' => $match->getStreet(), 
                    'match_city' => $match->getCity(),
                    'match_gym' => $match->getGym(),
                    'match_type' => $match->getType(), 
                    'match_idTeam' => $match->getTeam(),
                    'match_idMatchRef' => ($match->getMatchRef() ? $match->getMatchRef() : null)
                );
                $where = array('match_id' => $match->getId());
                //var_dump($data);
                $this->wpdb->update("{$this->t2}", $data, $where);
            } else {
                //echo "<br>IN ELSE<br>";
                $matchesToInsert[] = $match;
            }
        }
        //var_dump($matchesToInsert);
        $this->insertMatches($matchesToInsert);
    }

    function updateMatchesHours($allMatches){
        foreach($allMatches as $match) { 
            //var_dump($match);
            $data = array(
                'match_hourRdv' => $match->getHourRdv()
            ); 
            $where = array('match_id' => $match->getId());
            $this->wpdb->update("{$this->t2}", $data, $where);
        }
    }

    /***************************
    ********** INSERT **********
    ****************************/
    function insertMatches($allMatches){
        foreach($allMatches as $match) {
            if (!$match->getId()){
                $data = array(
                    'match_matchDay' => $match->getMatchDay(), 
                    'match_numMatch' => $match->getNumMatch(), 
                    'match_date' => $match->getDate(), 
                    'match_hourRdv' => $match->getHourRdv(), 
                    'match_hourStart' => $match->getHourStart(), 
                    'match_localTeam' => $match->getLocalTeam(), 
                    'match_localTeamScore' => $match->getLocalTeamScore(), 
                    'match_visitingTeam' => $match->getVisitingTeam(), 
                    'match_visitingTeamScore' => $match->getVisitingTeamScore(), 
                    'match_ext' => $match->getExt(),
                    'match_street' => $match->getStreet(), 
                    'match_city' => $match->getCity(),
                    'match_gym' => $match->getGym(),
                    'match_type' => $match->getType(), 
                    'match_idTeam' => (is_object($match->getTeam()) ? $match->getTeam()->getId() : $match->getTeam()),
                    'match_idMatchRef' => ($match->getMatchRef() ? $match->getMatchRef() : null)
                );
                //var_dump($data);
                $this->wpdb->insert("{$this->t2}", $data);
            } 
        }
    }

    /***************************
    ********** DELETE **********
    ****************************/
    function deleteMatchesNotIn($myMatchesId, $type, $teamId){  
        //echo "DELETE FROM {$this->t2} WHERE match_id NOT IN ($myMatchesId) AND match_type=$type AND match_idTeam=$teamId";
        if ($myMatchesId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE match_id NOT IN ($myMatchesId) AND match_type=$type AND match_idTeam=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE match_type=$type AND match_idTeam=$teamId", null));
        }
    }
    function deleteMatches($teamId){  
        if ($teamId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE match_idTeam=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2}", null));
        }
    }
}
?>