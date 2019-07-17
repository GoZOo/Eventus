<?php 

namespace Eventus\Admin\Business;
use Eventus\Includes\DAO as DAO;
use Eventus\Includes\DTO as Entities;
use Eventus\Admin\Business\Helper as Helper;

/**
* Finder is a class that allows you to manage all synchronization actions of matches.
*
* @package  Admin/Business
* @access   public
*/
class Finder {
    use Helper\TraitHelper;
    /**
    * @var Finder   $_instance  Var use to store an instance
    */
    private static $_instance;
    private $_baseUrl = "https://jjht57whqb.execute-api.us-west-2.amazonaws.com/prod/pool/";
    
    /**
    * Returns an instance of the object
    *
    * @return Finder
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new Finder();
        }
        return self::$_instance;
    }
    private function __construct() {}   

    /**
    * Synchronize matches by team with FFHB website informations
    *
    * @param Team   Team to update synchronize matches
    * @return void
    * @access public
    */
    public function updateMatches($team){
        $turn = 0;
        if ($team->getUrlOne()) {
            $turn++;
            if ($team->getUrlTwo()) $turn++;
        }
        for ($i=0; $i < $turn; $i++) { 
            $allMatches = [];
            switch ($i) {
                case 0:
                    $url = $this->getPoolId($team->getUrlOne());
                    break;
                case 1:
                    $url = $this->getPoolId($team->getUrlTwo());
                    break;                
                default:
                    $url = null;
                    break;
            }
            if (!$url) continue;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch,CURLOPT_ENCODING , "gzip");            
            $output = curl_exec($ch);
            
            if(curl_errno($ch)) {
                $this->addLog("Error Url (TeamId: ".$team->getId().", TeamUrl :".($i+1)." ,Error cUrl: ".curl_errno($ch).") : Unable to access this page");
                curl_close($ch); 
                continue;
            }
            
            // var_dump(json_decode($output, true)); exit;      
            curl_close($ch); 
            $output = json_decode($output, true);   

            //Update teams infos
            $teamInfos = array_filter($output['teams'], function ($var) use($team) {
                return strpos(mb_strtolower($var['name']), mb_strtolower($team->getClub()->getString())) !== false;
            });    
            $teamInfos = array_values($teamInfos);
            
            if (!$teamInfos || sizeof($teamInfos) === 0) {
                $this->addLog("Error String (TeamId: ".$team->getId().") : Can't find the string in results list");
                continue;
            } else {
                if ($i+1 == $turn) { //Check if last url, so last champ
                    $team->setPosition($teamInfos[0]['position']);
                    $team->setPoints($teamInfos[0]['points']);
                    DAO\TeamDAO::getInstance()->updateTeam($team);
                }
            }          
            
            //Update Matches infos
            foreach($output['dates'] as $matchDay => $rows) {
                $prevMatchDay = $matchDay;
                $numMatch = 0;
                foreach($rows['events'] as $row) {
                    if (
                        strpos(mb_strtolower($row['teams'][0]['name']), mb_strtolower($team->getClub()->getString())) !== false || 
                        strpos(mb_strtolower($row['teams'][1]['name']), mb_strtolower($team->getClub()->getString())) !== false
                    ) {
                        $hour = $row['date'] && $row['date']['hour'] && $row['date']['minute'] ? $row['date']['hour'].':'.$row['date']['minute'] : null;
                        if ($matchDay == $prevMatchDay) $numMatch++;                        

                        if ($row['teams'][0] && $row['teams'][1] && $row['teams'][0]['name'] && $row['teams'][1]['name']) {
                            $allMatches[] = 
                            new Entities\Match(
                                null, //id
                                $matchDay, //matchDay 
                                $numMatch, //numMatch 
                                $row['date'] ? $row['date']['date'] : null, //date
                                $hour ? date_create_from_format('H:i', $hour)->modify('-'. $team->getTime() .'minutes')->format('H:i:s') : null, //hourRdv
                                $hour ? date_create_from_format('H:i', $hour)->format('H:i:s') : null, //hourStart
                                $this->getCleanString($row['teams'][0]['name']), //localTeam
                                $row['teams'][0] ? $row['teams'][0]['score'] : null, //localTeamScore
                                $this->getCleanString($row['teams'][1]['name']), //visitingTeam
                                $row['teams'][1] ? $row['teams'][1]['score'] : null, //visitingTeamScore
                                strpos(mb_strtolower($row['teams'][0]['name']), mb_strtolower($team->getClub()->getString())) !== false ? false : true, //ext
                                $row['location'][1] ? $this->getCleanString($row['location'][1]) : null, //street
                                $row['location'][2] ? $this->getCleanString($row['location'][2]) : null, //city
                                $row['location'][0] ? $this->getCleanString($row['location'][0]) : null, //gym
                                0, //Type
                                $i+1, //Championship
                                $team, //team
                                null //matchRef
                            );
                        }                            
                    }                                        
                }
            }
            // var_dump($allMatches); exit;
            $allMatches = $this->setNewHoursRdv($allMatches);
            DAO\MatchDAO::getInstance()->updateMatchesSync($allMatches);
        }      
    }

    /**
    * Update hours rdv of matches
    *
    * @param Match[]    Match to update hours rdv
    * @return Match[]   Match with updated hours rdv
    * @access public
    */
    function setNewHoursRdv($allMatches){
        $allAdresses = "";
        foreach($allMatches as $match) {       
            if ($match->getExt() && $match->getStreet() && $match->getCity() && date($match->getDate()) > date('Y-m-d') ) { 
                $allAdresses .= str_replace(" ","%20",$match->getStreet())."%20".str_replace(" ","%20",$match->getCity())."|";
            }
        }
        if ($allAdresses) {
            $requestGoogleMap = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?key=".get_option("eventus_mapapikey")."&origins=".urlencode($allMatches[0]->getTeam()->getClub()->getAddress())."&destinations=".$allAdresses),true);
            // var_dump($requestGoogleMap);
            $key = 0;
            foreach($allMatches as $match) {
                if ($match->getExt() && $match->getStreet() && $match->getCity() && date($match->getDate()) > date('Y-m-d') ) {
                    if ($requestGoogleMap['rows'][0]['elements'][$key]['status'] != "OK" || $requestGoogleMap['status'] != "OK") {
                        $this->addLog("Error GoogleMap (TeamId: ".$match->getTeam()->getId().", MatchId: ".$match->getId().", matchDay: ".$match->getMatchDay().", Error Api: ".($requestGoogleMap['rows'][0]['elements'][$key]['status'] ? $requestGoogleMap['rows'][0]['elements'][$key]['status'] : $requestGoogleMap['status'] ).")");
                    } else {
                        $travelTime = round($requestGoogleMap['rows'][0]['elements'][$key]['duration']['value'] / 60);
                        $lastDigit = substr($travelTime,-1);
                        if (0 < $lastDigit && $lastDigit < 5) {
                            $travelTime = floor($travelTime/10)*10+5;
                        } else if(5 < $lastDigit && $lastDigit <= 9) {
                            $travelTime = floor($travelTime/10)*10+10;
                        }
                        $match->setHourRdv(date_create_from_format('H:i', $match->getHourStart())->modify('-'. ($travelTime+$allMatches[0]->getTeam()->getTime()) .'minutes')->format('H:i:s'));
                    }
                    $key++;                    
                }
            }
        }
        return $allMatches;
    }

    /**
    * Add log in file log
    *
    * @param string    String of the log to be added
    * @return void 
    * @access private
    */
    private function addLog($myLog){    
        date_default_timezone_set("Europe/Paris");
        file_put_contents(plugin_dir_path( __FILE__ ).'../../finder.log', "[".date("d/m/y H:i:s")."] ".$myLog."\n", FILE_APPEND);
    }

    /**
    * Transfom string to an UTF-8 string
    *
    * @param string     String to be updated
    * @return string    String updated
    * @access private
    */
    private function getCleanString($myString){
        if ($myString[0] == " ") $myString = substr($myString, 1);
        return mb_convert_case(mb_strtolower($myString), MB_CASE_TITLE, "UTF-8");
    }

    /**
    * Get pool id from url
    *
    * @param string     Url 
    * @return string    Pool id
    * @access private
    */
    private function getPoolId($url){
        $parsed = parse_url($url, PHP_URL_FRAGMENT);
        if ($parsed) {
            $exploded = explode("-", $parsed);
            if ($exploded && sizeof($exploded) > 1){
                if (is_numeric($exploded[1])) {
                    return $this->_baseUrl . $exploded[1];
                }
            }
        }
        return null;
    }
}
?>