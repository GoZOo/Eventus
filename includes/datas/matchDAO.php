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
    //To be deleted
    function getMatchById($idMatch){
        if ($idMatch) {
            $match = $this->wpdb->get_row("SELECT * FROM {$this->t2} WHERE id=$idMatch");            
            return new Match(
                    $match->id, 
                    $match->matchDay,
                    $row->numMatch, 
                    $match->date, 
                    $match->hourRdv, 
                    $match->hourStart, 
                    $match->localTeam, 
                    $match->localTeamScore, 
                    $match->visitingTeam, 
                    $match->visitingTeamScore, 
                    $match->ext, 
                    $match->street, 
                    $match->city, 
                    $match->gym, 
                    $row->type, 
                    TeamDAO::getInstance()->getTeamById($match->idTeam), 
                    $this->getMatchById($match->idMatchRef)
                );
        } 
        return;
    }

    function getAllMatchesByTeamId($idTeam){  
        $allMatches = [];
        $teams = $this->wpdb->get_results("
            SELECT 
                a.*, 
                b.name as 'teamName',
                b.url as 'teamUrl',
                b.boy as 'teamBoy',
                b.girl as 'teamGirl',
                b.mixed as 'teamMixed',
                b.position as 'teamPosition',
                b.points as 'teamPoints',
                b.time as 'teamTime',
                b.img as 'teamImg',
                b.clubId as 'clubId',
                c.name as 'clubName', 
                c.string as 'clubString', 
                c.boy as 'clubBoy', 
                c.girl as 'clubGirl', 
                c.mixed as 'clubMixed', 
                c.adress as 'clubAdress',
                d.numMatch as 'refNumMatch',
                d.matchDay as 'refMatchDay',
                d.date as 'refDate',
                d.hourRdv as 'refHourRdv',
                d.hourStart as 'refHourStart',
                d.localTeam as 'refLocalTeam',
                d.localTeamScore as 'refLocalTeamScore',
                d.visitingTeam as 'refVisitingTeam',
                d.visitingTeamScore as 'refVisitingTeamScore',
                d.ext as 'refExt',
                d.street as 'refStreet',
                d.city as 'refCity',
                d.gym as 'refGym',
                d.type as 'refType'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t3} b ON a.idTeam = b.id 
            LEFT JOIN {$this->t1} c ON b.clubId = c.id 
            LEFT JOIN {$this->t2} d ON a.idMatchRef = b.id
            WHERE 
                a.idTeam=$idTeam"
        );
        foreach($teams as $row) { 
            $allMatches[] = new Match(
                $row->id, 
                $row->matchDay,
                $row->numMatch,
                $row->date, 
                $row->hourRdv, 
                $row->hourStart, 
                $row->localTeam, 
                $row->localTeamScore, 
                $row->visitingTeam, 
                $row->visitingTeamScore, 
                $row->ext, 
                $row->street, 
                $row->city, 
                $row->gym, 
                $row->type, 
                new Team(
                    $row->idTeam,
                    $row->teamName,
                    $row->teamUrl,
                    $row->teamBoy,
                    $row->teamGirl,
                    $row->teamMixed,
                    $row->teamPosition,
                    $row->teamPoints,
                    $row->teamTime,
                    $row->teamImg,
                    new Club(
                        $row->clubId,
                        $row->clubName,
                        $row->clubString,
                        $row->clubBoy,
                        $row->clubGirl,
                        $row->clubMixed,
                        $row->clubAdress
                    )
                ),
                $row->idMatchRef ? 
                    new Match(
                        $row->idMatchRef, 
                        $row->refMatchDay,
                        $row->refNumMatch,
                        $row->refDate, 
                        $row->refHourRdv, 
                        $row->refHourStart, 
                        $row->refLocalTeam, 
                        $row->refLocalTeamScore, 
                        $row->refVisitingTeam, 
                        $row->refVisitingTeamScore, 
                        $row->refExt, 
                        $row->refStreet, 
                        $row->refCity, 
                        $row->refGym, 
                        $row->refType, 
                        new Team(
                            $row->idTeam,
                            $row->teamName,
                            $row->teamUrl,
                            $row->teamBoy,
                            $row->teamGirl,
                            $row->teamMixed,
                            $row->teamPosition,
                            $row->teamPoints,
                            $row->teamTime,
                            $row->teamImg,
                            new Club(
                                $row->clubId,
                                $row->clubName,
                                $row->clubString,
                                $row->clubBoy,
                                $row->clubGirl,
                                $row->clubMixed,
                                $row->clubAdress
                            )
                        )
                    ) : null
            );
        }
        return $allMatches;
    }

    function getAllMatchesByTeamIdAndType($idTeam, $type){  
        $allMatches = [];
        $teams = $this->wpdb->get_results("
            SELECT 
                a.*, 
                b.name as 'teamName',
                b.url as 'teamUrl',
                b.boy as 'teamBoy',
                b.girl as 'teamGirl',
                b.mixed as 'teamMixed',
                b.position as 'teamPosition',
                b.points as 'teamPoints',
                b.time as 'teamTime',
                b.img as 'teamImg',
                b.clubId as 'clubId',
                c.name as 'clubName', 
                c.string as 'clubString', 
                c.boy as 'clubBoy', 
                c.girl as 'clubGirl', 
                c.mixed as 'clubMixed', 
                c.adress as 'clubAdress',
                d.numMatch as 'refNumMatch',
                d.matchDay as 'refMatchDay',
                d.date as 'refDate',
                d.hourRdv as 'refHourRdv',
                d.hourStart as 'refHourStart',
                d.localTeam as 'refLocalTeam',
                d.localTeamScore as 'refLocalTeamScore',
                d.visitingTeam as 'refVisitingTeam',
                d.visitingTeamScore as 'refVisitingTeamScore',
                d.ext as 'refExt',
                d.street as 'refStreet',
                d.city as 'refCity',
                d.gym as 'refGym',
                d.type as 'refType'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t3} b ON a.idTeam = b.id 
            LEFT JOIN {$this->t1} c ON b.clubId = c.id 
            LEFT JOIN {$this->t2} d ON a.idMatchRef = b.id
            WHERE 
                a.idTeam=$idTeam AND
                a.type=$type
            ORDER BY 
                a.matchDay,
                CASE WHEN a.date IS NULL THEN 1 ELSE 0 END, 
                a.date ASC, 
                a.hourStart;
        ");
        foreach($teams as $row) { 
            $allMatches[] = new Match(
                $row->id, 
                $row->matchDay,
                $row->numMatch,
                $row->date, 
                $row->hourRdv, 
                $row->hourStart, 
                $row->localTeam, 
                $row->localTeamScore, 
                $row->visitingTeam, 
                $row->visitingTeamScore, 
                $row->ext, 
                $row->street, 
                $row->city, 
                $row->gym, 
                $row->type, 
                new Team(
                    $row->idTeam,
                    $row->teamName,
                    $row->teamUrl,
                    $row->teamBoy,
                    $row->teamGirl,
                    $row->teamMixed,
                    $row->teamPosition,
                    $row->teamPoints,
                    $row->teamTime,
                    $row->teamImg,
                    new Club(
                        $row->clubId,
                        $row->clubName,
                        $row->clubString,
                        $row->clubBoy,
                        $row->clubGirl,
                        $row->clubMixed,
                        $row->clubAdress
                    )
                ),
                $row->idMatchRef ? 
                    new Match(
                        $row->idMatchRef, 
                        $row->refMatchDay,
                        $row->refNumMatch,
                        $row->refDate, 
                        $row->refHourRdv, 
                        $row->refHourStart, 
                        $row->refLocalTeam, 
                        $row->refLocalTeamScore, 
                        $row->refVisitingTeam, 
                        $row->refVisitingTeamScore, 
                        $row->refExt, 
                        $row->refStreet, 
                        $row->refCity, 
                        $row->refGym, 
                        $row->refType, 
                        new Team(
                            $row->idTeam,
                            $row->teamName,
                            $row->teamUrl,
                            $row->teamBoy,
                            $row->teamGirl,
                            $row->teamMixed,
                            $row->teamPosition,
                            $row->teamPoints,
                            $row->teamTime,
                            $row->teamImg,
                            new Club(
                                $row->clubId,
                                $row->clubName,
                                $row->clubString,
                                $row->clubBoy,
                                $row->clubGirl,
                                $row->clubMixed,
                                $row->clubAdress
                            )
                        )
                    ) : null
            );
        }
        return $allMatches;
    }


    function getCloseMatchByTeamId($teamId, $close){  
        $myMatch = $this->wpdb->get_row("
            SELECT 
                a.*, 
                b.numMatch as 'refNumMatch',
                b.matchDay as 'refMatchDay',
                b.date as 'refDate',
                b.hourRdv as 'refHourRdv',
                b.hourStart as 'refHourStart',
                b.localTeam as 'refLocalTeam',
                b.localTeamScore as 'refLocalTeamScore',
                b.visitingTeam as 'refVisitingTeam',
                b.visitingTeamScore as 'refVisitingTeamScore',
                b.ext as 'refExt',
                b.street as 'refStreet',
                b.city as 'refCity',
                b.gym as 'refGym',
                b.type as 'refType'
            FROM {$this->t2} a 
            LEFT JOIN {$this->t2} b ON a.idMatchRef = b.id
            WHERE 
                a.idTeam=$teamId AND
                a.date ". ($close=='next' ? '>=' : '<')." CURDATE()
            ORDER BY 
                a.date ". ($close=='next' ? 'ASC' : 'DESC').", 
                a.hourStart 
            ASC LIMIT 1;
        ");      
        if (!$myMatch->idMatchRef){
            return new Match(
                $myMatch->id, 
                $myMatch->matchDay, 
                $myMatch->numMatch,
                $myMatch->date, 
                $myMatch->hourRdv, 
                $myMatch->hourStart, 
                $myMatch->localTeam, 
                $myMatch->localTeamScore, 
                $myMatch->visitingTeam, 
                $myMatch->visitingTeamScore, 
                $myMatch->ext, 
                $myMatch->street, 
                $myMatch->city, 
                $myMatch->gym, 
                $myMatch->type, 
                null,
                null
            );
        } else {
            return new Match(
                $myMatch->idMatchRef, 
                $myMatch->refMatchDay,
                $myMatch->refNumMatch,
                $myMatch->refDate, 
                $myMatch->refHourRdv, 
                $myMatch->refHourStart, 
                $myMatch->refLocalTeam, 
                $myMatch->refLocalTeamScore, 
                $myMatch->refVisitingTeam, 
                $myMatch->refVisitingTeamScore, 
                $myMatch->refExt, 
                $myMatch->refStreet, 
                $myMatch->refCity, 
                $myMatch->refGym, 
                $myMatch->refType, 
                null,
                null
            );
        }
        return;     
    }
    
    function getMatchesWithDate(){  
        $allMatches = [];
        $matches = $this->wpdb->get_results("
            SELECT * FROM {$this->t2} a
            INNER JOIN {$this->t3} b
            ON a.idTeam = b.id
            WHERE 
                date IS NOT NULL AND 
                type IN (0,2) AND
                date > DATE_SUB(NOW(), INTERVAL 1 DAY)
            ORDER BY 
                date, 
                b.name asc,
                numMatch asc;");  
        foreach($matches as $row) { 
            $mySonMatch = $this->wpdb->get_row("SELECT * FROM {$this->t2} WHERE idMatchRef={$row->id} AND type=1 LIMIT 1;");   
            if ($mySonMatch->id){
                $row = $mySonMatch;
            } 
            $allMatches[] = new Match(
                $row->id, 
                $row->matchDay, 
                $row->numMatch,
                $row->date, 
                $row->hourRdv, 
                $row->hourStart, 
                $row->localTeam, 
                $row->localTeamScore, 
                $row->visitingTeam, 
                $row->visitingTeamScore, 
                $row->ext, 
                $row->street, 
                $row->city, 
                $row->gym, 
                $row->type, 
                TeamDAO::getInstance()->getTeamById($row->idTeam), 
                null
            );      
        }
        return $allMatches;
    }

    function getMatchByDayNumTeam($matchDay, $numMatch, $team){  
        return $this->wpdb->get_row("SELECT id FROM {$this->t2} WHERE matchDay=$matchDay AND idTeam={$team->getId()} AND numMatch=$numMatch")->id;        
    }

    function getInfosByTeamId($idTeam){  
        return $this->wpdb->get_row("SELECT a.id, a.clubId, count(b.id) as number FROM {$this->t3} a LEFT JOIN {$this->t2} b ON a.id=b.idTeam WHERE b.type IN (0,2) AND b.idTeam=$idTeam;");
    }

    /***************************
    ********** UPDATE **********
    ****************************/
    function updateMatches($allMatches){

        $matchesToInsert = [];
        foreach($allMatches as $match) {
            $myId = $this->getMatchByDayNumTeam($match->getMatchDay(), $match-> getNumMatch(), $match->getTeam());
            if($myId){
                $data = array(
                    'matchDay' => $match->getMatchDay(), 
                    'numMatch' => $match->getNumMatch(), 
                    'date' => $match->getDate(), 
                    'hourRdv' => $match->getHourRdv(), 
                    'hourStart' => $match->getHourStart(), 
                    'localTeam' => $match->getLocalTeam(), 
                    'localTeamScore' => $match->getLocalTeamScore(), 
                    'visitingTeam' => $match->getVisitingTeam(), 
                    'visitingTeamScore' => $match->getVisitingTeamScore(), 
                    'ext' => $match->getExt(),
                    'street' => $match->getStreet(), 
                    'city' => $match->getCity(),
                    'gym' => $match->getGym(),
                    'type' => $match->getType(), 
                    'idTeam' => $match->getTeam()->getId()
                );
                $matchRef = $this->getMatchById($match->getMatchRef());
                if ($matchRef) {
                    $data['idMatchRef'] = $matchRef->getId();
                }
                $where = array('id' => $myId);
                $this->wpdb->update("{$this->t2}", $data, $where);
            } else {
                $matchesToInsert[] = $match;
            }
        }
        $this->insertMatches($matchesToInsert);
    }

    function updateMatches2($allMatches, $type, $teamId){
        foreach($allMatches as $matches) {
            if ($matches->getId()){
                $matchesIdToDelete .= $matches->getId().", ";
            }            
        }
        $this->deleteOtherMatchesNotIn(substr($matchesIdToDelete,0 , -2), $type, $teamId);

        $matchesToInsert = [];
        foreach($allMatches as $match) {            
            if($match->getId()){
                $data = array(
                    'matchDay' => $match->getMatchDay(), 
                    'numMatch' => $match->getNumMatch(), 
                    'date' => $match->getDate(), 
                    'hourRdv' => $match->getHourRdv(), 
                    'hourStart' => $match->getHourStart(), 
                    'localTeam' => $match->getLocalTeam(), 
                    'localTeamScore' => $match->getLocalTeamScore(), 
                    'visitingTeam' => $match->getVisitingTeam(), 
                    'visitingTeamScore' => $match->getVisitingTeamScore(), 
                    'ext' => $match->getExt(),
                    'street' => $match->getStreet(), 
                    'city' => $match->getCity(),
                    'gym' => $match->getGym(),
                    'type' => $match->getType(), 
                    'idTeam' => $match->getTeam()->getId()
                );
                $matchRef = $this->getMatchById($match->getMatchRef());
                if ($matchRef) {
                    $data['idMatchRef'] = $matchRef->getId();
                }
                $where = array('id' => $match->getId());
                $this->wpdb->update("{$this->t2}", $data, $where);
            } else {
                $matchesToInsert[] = $match;
            }
        }
        $this->insertMatches($matchesToInsert);
    }

    function updateMatches3($allMatches){
        foreach($allMatches as $match) { 
            $data = array(
                'matchDay' => $match->getMatchDay(), 
                'numMatch' => $match->getNumMatch(), 
                'date' => $match->getDate(), 
                'hourRdv' => $match->getHourRdv(), 
                'hourStart' => $match->getHourStart(), 
                'localTeam' => $match->getLocalTeam(), 
                'localTeamScore' => $match->getLocalTeamScore(), 
                'visitingTeam' => $match->getVisitingTeam(), 
                'visitingTeamScore' => $match->getVisitingTeamScore(), 
                'ext' => $match->getExt(),
                'street' => $match->getStreet(), 
                'city' => $match->getCity(),
                'gym' => $match->getGym(),
                'type' => $match->getType(), 
                'idTeam' => $match->getTeam()->getId()
            );
            if ($match->getMatchRef()){
                $data['idMatchRef'] = $match->getMatchRef()->getId();
            }   
            $where = array('id' => $match->getId());
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
                    'matchDay' => $match->getMatchDay(), 
                    'numMatch' => $match->getNumMatch(), 
                    'date' => $match->getDate(), 
                    'hourRdv' => $match->getHourRdv(), 
                    'hourStart' => $match->getHourStart(), 
                    'localTeam' => $match->getLocalTeam(), 
                    'localTeamScore' => $match->getLocalTeamScore(), 
                    'visitingTeam' => $match->getVisitingTeam(), 
                    'visitingTeamScore' => $match->getVisitingTeamScore(), 
                    'ext' => $match->getExt(),
                    'street' => $match->getStreet(), 
                    'city' => $match->getCity(),
                    'gym' => $match->getGym(),
                    'type' => $match->getType(), 
                    'idTeam' => $match->getTeam()->getId()
                );
                $matchRef = $this->getMatchById($match->getMatchRef());
                if ($matchRef) {
                    $data['idMatchRef'] = $matchRef->getId();
                }
                //var_dump($data);
                $this->wpdb->insert("{$this->t2}", $data);
            } 
        }
    }

    /***************************
    ********** DELETE **********
    ****************************/
    function deleteOtherMatchesNotIn($myMatchesId, $type, $teamId){  
        if ($myMatchesId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE id NOT IN ($myMatchesId) AND type=$type AND idTeam=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE type=$type AND idTeam=$teamId", null));
        }
    }
    function deleteMatches($teamId){  
        if ($teamId){
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2} WHERE idTeam=$teamId", null));
        } else {
            $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->t2}", null));
        }
    }
}
?>