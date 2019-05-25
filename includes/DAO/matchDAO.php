<?php 

namespace Eventus\Includes\DAO;
use Eventus\Includes\DTO as Entities;

/**
* MatchDAO is a class use to manage acces to the Database to get Match objects
*
* @package  Includes//DAO
* @access   public
*/
class MatchDAO extends MasterDAO {
    /**
    * @var MatchDAO   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return MatchDAO
    * @access public
    */
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
    /**
    * Return every matches by team Id
    *
    * @param int        Id of the team
    * @return Match[]   All matches corresponding
    * @access public
    */
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
                d.match_type                as 'refMatch_type',
                d.match_champ               as 'refMatch_champ'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t3} b ON a.match_idTeam = b.team_id 
            LEFT JOIN {$this->t1} c ON b.team_clubId = c.club_id 
            LEFT JOIN {$this->t2} d ON a.match_idMatchRef = d.match_id
            WHERE 
                a.match_idTeam=$idTeam"
        );
        foreach($teams as $row) { 
            $allMatches[] = new Entities\Match(
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
                $row->match_champ, 
                new Entities\Team(
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
                ),
                $row->match_idMatchRef ? 
                    new Entities\Match(
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
                        $row->refMatch_champ,  
                        new Entities\Team(
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
                        )
                    ) : null
            );
        }
        return $allMatches;
    }

    /**
    * Return every matches by team Id and type
    *
    * @param int        Id of the team
    * @param int        Type of match : 0 = championship; 1 = son championship; 2 = other
    * @return Match[]   All matches corresponding
    * @access public
    */
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
                d.match_type                as 'refMatch_type',
                d.match_champ               as 'refMatch_champ'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t3} b ON a.match_idTeam = b.team_id 
            LEFT JOIN {$this->t1} c ON b.team_clubId = c.club_id 
            LEFT JOIN {$this->t2} d ON a.match_idMatchRef = d.match_id
            WHERE 
                a.match_idTeam=$idTeam AND
                a.match_type=$type
            ORDER BY 
                a.match_champ ASC,
                a.match_matchDay ASC,
                a.match_numMatch ASC
        ");
        $allMatches = [];
        foreach($teams as $row) { 
            $allMatches[] = new Entities\Match(
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
                $row->match_champ, 
                new Entities\Team(
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
                ),
                $row->match_idMatchRef ? 
                    new Entities\Match(
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
                        $row->refMatch_champ,  
                        new Entities\Team(
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
                        )
                    ) : null
            );
        }
        return $allMatches;
    }

    /**
    * Return closest match before of after today
    *
    * @param int        Id of the team
    * @param string     Before or After : 'after'; 'before'
    * @return Match     Match corresponding
    * @access public
    */
    function getCloseMatchByTeamId($teamId, $close){  
        $row = $this->wpdb->get_row("
        SELECT                 
            a.*
        FROM {$this->t2} a
        WHERE              
            a.match_idTeam=$teamId AND
            a.match_date ". ($close=='next' ? '>=' : '<')." CURDATE()  AND
            (
                (
                    a.match_type=0 AND
                    (
                        SELECT 
                            z.match_id 
                        FROM {$this->t2} z 
                        WHERE 
                            z.match_idMatchRef = a.match_id
                    ) IS NULL                        
                )
                OR
                a.match_type IN (1,2)
            ) 
            ".($close !='next' ? '
                AND
                (
                    a.match_localTeamScore IS NOT NULL OR
                    a.match_visitingTeamScore IS NOT NULL
                )' 
            : '')."            
        ORDER BY 
            a.match_date ". ($close=='next' ? 'ASC' : 'DESC').", 
            a.match_hourStart ". ($close=='next' ? 'ASC' : 'DESC'). "
        LIMIT 1;
        ");  
        return new Entities\Match(
            $row ? $row->match_id : null, 
            $row ? $row->match_matchDay : null,
            $row ? $row->match_numMatch : null,
            $row ? $row->match_date : null, 
            $row ? $row->match_hourRdv : null, 
            $row ? $row->match_hourStart : null, 
            $row ? $row->match_localTeam : null,
            $row ? $row->match_localTeamScore : null, 
            $row ? $row->match_visitingTeam : null,
            $row ? $row->match_visitingTeamScore : null,
            $row ? $row->match_ext : null,
            $row ? $row->match_street : null,
            $row ? $row->match_city : null,
            $row ? $row->match_gym : null, 
            $row ? $row->match_type : null, 
            $row ? $row->match_champ : null,
            null,
            null
        );          
    }
    
    /**
    * Return matches with a date for calender
    *
    * @return Match[]     Match corresponding
    * @access public
    */
    function getMatchesWithDate(){  
        $allMatches = [];
        $matches = $this->wpdb->get_results("
            SELECT                 
                a.*,            
                b.*,            
                c.*
            FROM {$this->t2} a 
            LEFT JOIN {$this->t3} b ON a.match_idTeam = b.team_id 
            LEFT JOIN {$this->t1} c ON b.team_clubId = c.club_id 
            WHERE   
                a.match_date IS NOT NULL AND 
                a.match_date > DATE_SUB(NOW(), INTERVAL 1 DAY) AND
                (
                    (
                        a.match_type=0 AND
                        (
                            SELECT 
                                z.match_id 
                            FROM {$this->t2} z 
                            WHERE 
                                z.match_idMatchRef = a.match_id
                        ) IS NULL
                                
                    ) OR
                    a.match_type IN (1,2)
                )
            ORDER BY 
                a.match_date, 
                b.team_name asc,
                a.match_numMatch asc;"
        );  
        foreach($matches as $row) { 
            $allMatches[] = new Entities\Match(
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
                $row->match_champ, 
                new Entities\Team(
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
                ),
                null
            );      
        }
        return $allMatches;
    }

    /***************************
    ********** UPDATE **********
    ****************************/
    /**
    * Update matches when synchronization
    *
    * @param Match[]    Matches sync to be updated
    * @return void      
    * @access public
    */
    function updateMatchesSync($allMatches){
        $matchesToInsert = [];
        foreach($allMatches as $match) {
            //var_dump($match);
            $myId = $this->wpdb->get_row("
                SELECT match_id 
                FROM {$this->t2} 
                WHERE 
                    match_matchDay={$match->getMatchDay()} AND 
                    match_idTeam={$match->getTeam()->getId()} AND 
                    match_numMatch={$match->getNumMatch()} AND 
                    match_champ={$match->getChamp()}"
            )->match_id;
            //var_dump($myId);
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
                    'match_champ' => $match->getChamp(), 
                    'match_idTeam' => $match->getTeam()->getId(),
                    'match_idMatchRef' => ($match->getMatchRef() ? $match->getMatchRef() : null)
                );
                $where = array('match_id' => $myId);
                //var_dump($data);
                $this->wpdb->update("{$this->t2}", $data, $where);
            } else {
                $matchesToInsert[] = $match;
            }
        }
        //var_dump($matchesToInsert);
        $this->insertMatches($matchesToInsert);
    }

    /**
    * Update matches when update from screen
    *
    * @param Match[]    Matches to updated
    * @param string     Type of matches : 0 = championship; 1 = son championship; 2 = other
    * @param int        Id of the team
    * @return void      
    * @access public
    */
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
                    'match_champ' => $match->getChamp(), 
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

    /**
    * Update matches when update only new hours rdv
    *
    * @param Match[]    Matches to updated
    * @return void      
    * @access public
    */
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
    /**
    * Insert matches
    *
    * @param Matches[]  Matches to be inserted
    * @return void    
    * @access public
    */
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
                    'match_champ' => $match->getChamp(),  
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
    /**
    * Delete matches not in a list of id, with a type and a team id
    *
    * @param int[]    Matches id to be deleted
    * @param string   Type of matches : 0 = championship; 1 = son championship; 2 = other
    * @param int      Id of the team
    * @return void    
    * @access public
    */
    function deleteMatchesNotIn($myMatchesId, $type, $teamId){  
        //echo "DELETE FROM {$this->t2} WHERE match_id NOT IN ($myMatchesId) AND match_type=$type AND match_idTeam=$teamId";
        if ($myMatchesId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE match_id NOT IN ($myMatchesId) AND match_type=$type AND match_idTeam=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE match_type=$type AND match_idTeam=$teamId", null));
        }
    }

    /**
    * Delete matches with a team id
    *
    * @param int|null   Id of Team to delete matches
    * @return void    
    * @access public
    */
    function deleteMatches($teamId = null){  
        if ($teamId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE match_idTeam=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2}", null));
        }
    }
}
?>