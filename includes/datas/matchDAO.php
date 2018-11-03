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
    function getAllMatches(){  
        $allMatches = [];
        $teams = $this->wpdb->get_results("SELECT * FROM {$this->t2}");
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
                TeamDAO::getInstance()->getTeamById($row->idTeam), 
                $this->getMatchById($row->idMatchRef)
            );
        }
        return $allMatches;
    }

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
        $teams = $this->wpdb->get_results("SELECT * FROM {$this->t2} WHERE idTeam=$idTeam;");
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
                TeamDAO::getInstance()->getTeamById($row->idTeam), 
                $this->getMatchById($row->idMatchRef)
            );
        }
        return $allMatches;
    }

    function getAllMatchesParentsByTeamId($idTeam){  
        $allMatches = [];
        $teams = $this->wpdb->get_results("
            SELECT * FROM {$this->t2} 
            WHERE 
                idTeam=$idTeam 
                AND idMatchRef IS NULL 
                AND matchDay IS NOT NULL 
            ORDER BY 
                matchDay;
        ");
        //ORDER BY CASE WHEN date IS NULL THEN 1 ELSE 0 END, date ASC, hourStart
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
                TeamDAO::getInstance()->getTeamById($row->idTeam), 
                $this->getMatchById($row->idMatchRef)
            );
        }
        return $allMatches;
    }

    function getAllMatchesSonByTeamId($idTeam){  
        $allMatches = [];
        $teams = $this->wpdb->get_results("
            SELECT * FROM {$this->t2} 
            WHERE 
                idTeam=$idTeam 
                AND idMatchRef IS NOT NULL 
                AND type = 1 
            ORDER BY 
                matchDay;
        ");
        //ORDER BY CASE WHEN date IS NULL THEN 1 ELSE 0 END, date ASC, hourStart;
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
                TeamDAO::getInstance()->getTeamById($row->idTeam), 
                $this->getMatchById($row->idMatchRef)
            );
        }
        return $allMatches;
    }

    function getAllMatchesOtherByTeamId($idTeam){  
        $allMatches = [];
        $teams = $this->wpdb->get_results("
            SELECT * FROM {$this->t2} 
            WHERE 
                idTeam=$idTeam 
                AND idMatchRef IS NULL 
                AND matchDay IS NULL 
                AND type = 2 
            ORDER BY 
                CASE WHEN date IS NULL THEN 1 ELSE 0 END, 
                date ASC, hourStart;
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
                TeamDAO::getInstance()->getTeamById($row->idTeam), 
                $this->getMatchById($row->idMatchRef)
            );
        }
        return $allMatches;
    }

    function getMatchByDayNumTeam($matchDay, $numMatch, $team){  
        return $this->wpdb->get_row("SELECT id FROM {$this->t2} WHERE matchDay=$matchDay AND idTeam={$team->getId()} AND numMatch=$numMatch")->id;        
    }

    function getNextMatchByTeamId($teamId){  
        $myMatch = $this->wpdb->get_row("SELECT * FROM {$this->t2} WHERE idTeam=$teamId AND date >= CURDATE() ORDER BY date, hourStart ASC LIMIT 1;");         
        $mySonMatch = $this->wpdb->get_row("SELECT * FROM {$this->t2} WHERE idTeam=$teamId AND date >= CURDATE() AND idMatchRef={$myMatch->id} ORDER BY date, hourStart ASC LIMIT 1;");   
        if ($mySonMatch){
           $myMatch = $mySonMatch;
        }
        return new Match(
                $myMatch->id, 
                $myMatch->matchDay, 
                $row->numMatch,
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
    }

    function getLastMatchByTeamId($teamId){  
        $myMatch = $this->wpdb->get_row("SELECT * FROM {$this->t2} WHERE idTeam=$teamId AND date < CURDATE() ORDER BY date DESC, hourStart DESC LIMIT 1;");         
        $mySonMatch = $this->wpdb->get_row("SELECT * FROM {$this->t2} WHERE idTeam=$teamId AND date < CURDATE() AND idMatchRef={$myMatch->id} ORDER BY date DESC, hourStart DESC LIMIT 1;");   
        if ($mySonMatch){
           $myMatch = $mySonMatch;
        }
        return new Match(
                $myMatch->id, 
                $myMatch->matchDay, 
                $row->numMatch,
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