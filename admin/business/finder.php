<?php 

namespace Eventus\Admin\Business;
use Eventus\Includes\Datas as DAO;
use Eventus\Includes\Entities as Entities;

/**
* Finder is a class that allows you to manage all synchronization actions of matches.
*
* @access   public
*/
class Finder {
    /**
    * @var Finder   $_instance  Var use to store an instance
    */
    private static $_instance;
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
            if ($team->getUrlTwo()) {
                $turn++;
            }
        }
        for ($i=0; $i < $turn; $i++) { 
            $allMatches = [];
            switch ($i) {
                case 0:
                    $ch = curl_init($team->getUrlOne());
                    break;
                case 1:
                    $ch = curl_init($team->getUrlTwo());
                    break;                
                default:
                    $ch = null;
                    break;
            }
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $output = curl_exec($ch);
            if(curl_errno($ch)) {
                $this->addLog("Error Url (TeamId: ".$team->getId().", TeamUrl :".($i+1)." ,Error cUrl: ".curl_errno($ch).") : Unable to access this page");
                curl_close($ch); 
                continue;
            } 
            
            curl_close($ch); 
            $html = str_get_html($output);

            //Update teams infos
            if (strpos(mb_strtolower($output), mb_strtolower($team->getClub()->getString())) === false) {
                $this->addLog("Error String (TeamId: ".$team->getId().") : Can't find the string in matches list");
                $html->clear();
                continue;
            }
            if ($i+1 == $turn) {
                foreach($html->find('div.round tr') as $row) {
                    if (strpos(mb_strtolower($row), mb_strtolower($team->getClub()->getString()))){
                        $team->setPosition($row->find('td.num',0)->plaintext);
                        $team->setPoints($row->find('td.pts',0)->plaintext);
                        break;
                    }
                }
                DAO\TeamDAO::getInstance()->updateTeam($team);
            }
            
            //Update Matches infos
            if ($html->getElementById('ul#journeelist') == null ){
                $this->addLog("Error Url (TeamId: ".$team->getId().") : Can't find match informations");
                $html->clear();
                continue;
            } 
            foreach($html->getElementById('ul#journeelist')->find('.touchcarousel-item') as $matchDay => $rows) {
                $prevMatchDay = $matchDay;
                $numMatch = 0;
                foreach($rows->find('tr') as $row) {
                    if ( strpos( mb_strtolower($row->find('td.eq',0)), mb_strtolower($team->getClub()->getString()) ) ){
                        $clubFound = true;
                        $fullAdress = explode("#/#", $row->find('td.info a',0)->attr['data-text-tooltip']);
                        $fullDate = explode("<br>", $row->find('td.date',0)->innertext);
                        $matchDay == $prevMatchDay ? $numMatch++ : '';

                        if ( explode(" -  ", $row->find('td.eq p',0)->plaintext)[1] && explode(" -  ", $row->find('td.eq p',1)->plaintext)[1]) {
                            $allMatches[] = 
                            new Entities\Match(
                                null, //id
                                ($matchDay+1), //matchDay 
                                $numMatch, //numMatch 
                                strlen($fullDate[1])>0 ? date_create_from_format('d/m/Y', $fullDate[0])->format('Y-m-d') : null, //date
                                strlen($fullDate[1])>0 ? date_create_from_format('H:i:s', $fullDate[1])->modify('-'. $team->getTime() .'minutes')->format('H:i:s') : null, //hourRdv
                                strlen($fullDate[1])>0 ? date_create_from_format('H:i:s', $fullDate[1])->format('H:i:s') : null, //hourStart
                                $this->getCleanString(explode(" -  ", $row->find('td.eq p',0)->plaintext)[1]), //localTeam
                                $row->find('td.eq p',0)->find('strong',0)->plaintext ? $row->find('td.eq p',0)->find('strong',0)->plaintext : null, //localTeamScore
                                $this->getCleanString(explode(" -  ", $row->find('td.eq p',1)->plaintext)[1]), //visitingTeam
                                $row->find('td.eq p',1)->find('strong',0)->plaintext ? $row->find('td.eq p',1)->find('strong',0)->plaintext : null, //visitingTeamScore
                                strpos(mb_strtolower($this->stripAccents(explode(" -  ", $row->find('td.eq p',0)->plaintext)[1])),mb_strtolower($this->stripAccents($team->getClub()->getString()))) !== false ? 0 : 1, //ext
                                $this->getCleanString($fullAdress[count($fullAdress)-3]) ? $this->getCleanString($fullAdress[count($fullAdress)-3]) : null, //street
                                $this->getCleanString($fullAdress[count($fullAdress)-2]) ? $this->getCleanString($fullAdress[count($fullAdress)-2]) : null, //city
                                $this->getCleanString($fullAdress[count($fullAdress)-4]) ? $this->getCleanString($fullAdress[count($fullAdress)-4]) : null, //gym
                                0, //Type
                                $i+1, //Championship
                                $team, //team
                                null //matchRef
                            );
                        }                            
                    }                                        
                }
            }
            $html->clear(); 
            //var_dump($allMatches);
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
        if ($allAdresses){
            $requestGoogleMap = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?key=".get_option("eventus_mapapikey")."&origins=".urlencode($allMatches[0]->getTeam()->getClub()->getAddress())."&destinations=".$allAdresses),true);
            //var_dump($requestGoogleMap);
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
    * Transform character with accent to characters without accents
    *
    * @param string    String to strip accents
    * @return Match[]  String with accents strip 
    * @access public
    */
    function stripAccents($str) {
        return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
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
    * @return string      String updated
    * @access private
    */
    private function getCleanString($myString){
        if ($myString[0] == " "){
            $myString = substr($myString, 1);
        }
        return mb_convert_case(mb_strtolower(iconv("UTF-8", "ISO-8859-1//TRANSLIT", $myString)), MB_CASE_TITLE, "UTF-8");
    }
}
?>