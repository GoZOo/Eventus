<?php 

namespace Eventus\Admin\Business;
use Eventus\Includes\DAO as DAO;
use Eventus\Includes\DTO as Entities;
use Eventus\Admin\Business\Helper as Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

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
    private $_baseUrlFFHB = "https://www.ffhandball.fr/wp-json/competitions/v1/computeBlockAttributes";
    private $_baseUrlMap = "https://maps.googleapis.com/maps/api/distancematrix/json";
    
    /**
    * Returns an instance of the object
    *
    * @return Finder
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) self::$_instance = new Finder();
        return self::$_instance;
    }
    private function __construct() {
        $this->clientFFHB = new Client(['base_uri' => $this->_baseUrlFFHB]);
        $this->clientMap = new Client(['base_uri' => $this->_baseUrlMap]);
    }   

    /**
    * Synchronize matches by team with FFHB website informations 
    *
    * @param Team[]   Team to update synchronize matches and sync hours rdv
    * @return void
    * @access public
    */
    public function updateMatches($teams){
        $this->updateMatchesData($teams);

        foreach ($teams as $team) {
            DAO\MatchDAO::getInstance()->updateMatchesHours(
                $this->setNewHoursRdv(
                    DAO\MatchDAO::getInstance()->getAllMatchesByTeamId(
                        $team->getId()
                    )
                )
            );
        }
    }

    /**
    * Synchronize matches by team with FFHB website informations 
    *
    * @param Team[]   Team to update synchronize matches
    * @return void
    * @access public
    */
    public function updateMatchesData($teams){
        $cfk = Decipher::getInstance()->getCfk();

        $promises = array();
        foreach ($teams as $team) { 
            $urlTeam = $team->getUrlOne();
            $urlPool = $team->getUrlTwo();

            $expression = "/competitions\/saison-[0-9]+-[0-9]+-([0-9]+)\/([^\/]+)\/([^\/]+)\/[^\/]+-([0-9]+)/";
            preg_match($expression, $urlPool, $urlDatas);
            $seasonId = $urlDatas[1];
            $competitionType = $urlDatas[2];
            $competition = $urlDatas[3];
            $poolId = $urlDatas[4];
            preg_match($expression, $urlTeam, $urlDatas);
            $teamId = $urlDatas[4];

//echo "<pre>" . print_r([
//        'ext_saison_id' =>  $seasonId,
//        'url_competition_type' => $competitionType,
//        'url_competition' => $competition,
//        'ext_poule_id' => $poolId,
//        'ext_equipe_id' => $teamId,
//    ], true) . "</pre>";
            $promises[$urlPool] = $this->clientFFHB->getAsync('', [
                'query' => [
                    'block' => 'competitions---mini-classement-or-ads',
                    'ext_saison_id' =>  $seasonId,
                    'url_competition_type' => $competitionType,
                    'url_competition' => $competition,
                    'ext_poule_id' => $poolId,
                ]
            ])
            ->then(
                function (ResponseInterface $res) use ($team, $cfk) {
                    $classementResponseData = $res->getBody()->getContents();
                    $classementResponse = Decipher::getInstance()->decipher($classementResponseData, $cfk);

                    //Update teams infos
                    $teamInfos = array_values(array_filter($classementResponse['classements'], function ($var) use($team) {
                        return preg_match('/'.mb_strtolower($team->getClub()->getString()).'/', mb_strtolower($var['equipe_libelle']));
                    }));

                    if (!$teamInfos || sizeof($teamInfos) === 0) {
                        $this->addLog("Error String (TeamId: ".$team->getId().") : Can't find the string in results list");
                    } else {
                        $team->setPosition($teamInfos[0]['place']);
                        $team->setPoints($teamInfos[0]['point']);
                        DAO\TeamDAO::getInstance()->updateTeam($team);
                    }
                },
                function (RequestException $e) use ($team, $urlPool) {
                    return array('status' => $e->getResponse()->getStatusCode(), 'content' => null);
                    $this->addLog("Error Url (TeamId: ".$team->getId().", TeamUrl :".($urlPool).", Http code: ".$e->getResponse()->getStatusCode().") : Unable to access this page");
                }
            );
            $promises[$urlTeam] = $this->clientFFHB->getAsync('', [
                'query' => [
                    'block' => 'competitions---rencontre-list',
                    'ext_saison_id' =>  $seasonId,
                    'url_competition_type' => $competitionType,
                    'url_competition' => $competition,
                    'ext_equipe_id' => $teamId,
                ]
            ])
            ->then(
                function (ResponseInterface $res) use ($team, $cfk) {
                    $classementResponseData = $res->getBody()->getContents();
                    $classementResponse = Decipher::getInstance()->decipher($classementResponseData, $cfk);

                    $allMatches = [];

                    // Update Matches infos
                    foreach($classementResponse['rencontres'] as $matchDay => $row) {

                        if (mb_strtolower($row['equipe1Libelle']) ===  mb_strtolower($team->getClub()->getString())) {
                            $equipeNum = 1;
                        }
                        elseif (mb_strtolower($row['equipe2Libelle']) === mb_strtolower($team->getClub()->getString())) {
                            $equipeNum = 2;
                        }
                        else {
                            continue;
                        }

                        $date = !empty($row['date']) ? $row['date'] : null;

                        $allMatches[] = new Entities\Match(
                            null, //id
                            $row['journeeNumero'], //matchDay
                            $row['ext_rencontreId'], //numMatch
                            $date, //date
                            $date ? date_create_from_format('Y-m-d H:i:s.v', $date)->modify('-'. $team->getTime() .'minutes')->format('H:i:s') : null, //hourRdv
                            $date ? date_create_from_format('Y-m-d H:i:s.v', $date)->format('H:i:s') : null, //hourStart
                            $this->getCleanString($row['equipe1Libelle']), //localTeam
                            $row['equipe1Score'], //localTeamScore
                            $this->getCleanString($row['equipe2Libelle']), //visitingTeam
                            $row['equipe2Score'], //visitingTeamScore
                            $equipeNum === 2, //ext
                            null, //street
                            null, //city
                            null, //gym
                            0, //Type
                            1, //Championship
                            $team, //team
                            null //matchRef
                        );
                    }
//echo "<pre>" . print_r($classementResponse, true) . "</pre>";
                    DAO\MatchDAO::getInstance()->updateMatchesSync($allMatches);
                },
                function (RequestException $e) use ($team, $urlTeam) {
                    return array('status' => $e->getResponse()->getStatusCode(), 'content' => null);
                    $this->addLog("Error Url (TeamId: ".$team->getId().", TeamUrl :".($urlTeam).", Http code: ".$e->getResponse()->getStatusCode().") : Unable to access this page");
                }
            );
        }
        Promise\settle($promises)->wait();
    }

    /**
    * Update hours rdv of matches
    *
    * @param Match[]    Match to update hours rdv
    * @return Match[]   Match with updated hours rdv
    * @access public
    */
    function setNewHoursRdv($matches){
        $limit = 5;
        //Get all matches to be calculated
        $allMatches = [];
        foreach($matches as $match) {       
            if ($match->getExt() && $match->getStreet() && $match->getCity() && date($match->getDate()) > date('Y-m-d') ) { 
                array_push($allMatches, $match);
            }
        }
        $allMatches = array_chunk($allMatches, $limit); //Split by limit
        
        //Foreach list
        foreach ($allMatches as $list) {
            $allAdresses = "";
            foreach($list as $match) {       
                $allAdresses .= str_replace(" ","%20",$match->getStreet())."%20".str_replace(" ","%20",$match->getCity())."|";
            }

            //Get and merge data from Google api
            if ($allAdresses && $allAdresses != "") {
                $requestGoogleMap = $this->parseJson($this->clientMap->get(
                    "?key=".get_option("eventus_mapapikey").
                    "&origins=".urlencode($matches[0]->getTeam()->getClub()->getAddress()).
                    "&destinations=".$allAdresses)->getBody()
                );    

                foreach($list as $y => $match) {
                    if ($requestGoogleMap['status'] != "OK" || $requestGoogleMap['rows'][0]['elements'][$y]['status'] != "OK") {
                        $this->addLog("Error GoogleMap (TeamId: ".$match->getTeam()->getId().", MatchId: ".$match->getId().", matchDay: ".$match->getMatchDay().", Error Api: ".($requestGoogleMap['status'] . ' ' . $requestGoogleMap['rows'][0]['elements'][$y]['status']).")");
                    } else {
                        $travelTime = round($requestGoogleMap['rows'][0]['elements'][$y]['duration']['value'] / 60);
                        $lastDigit = substr($travelTime,-1);
                        if (0 < $lastDigit && $lastDigit < 5) {
                            $travelTime = floor($travelTime/10)*10+5;
                        } else if(5 < $lastDigit && $lastDigit <= 9) {
                            $travelTime = floor($travelTime/10)*10+10;
                        }
                        $match->setHourRdv(date_create_from_format('H:i', $match->getHourStart())->modify('-'. ($travelTime+$list[0]->getTeam()->getTime()) .'minutes')->format('H:i:s'));
                    }
                }
            }
            sleep(2); // We need to not spam google api
        }
        $final = [];
        foreach ($allMatches as $list) {
            foreach ($list as $match) {
                array_push($final, $match);
            }
        }
        return $final;
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
    * Get url for API
    *
    * @param string     Url 
    * @return string    Pool id
    * @access private
    */
    private function getUrlApi($url){
        $parsed = parse_url($url, PHP_URL_FRAGMENT);
        if ($parsed) {
            $exploded = explode("-", $parsed);
            if ($exploded && sizeof($exploded) > 1){
                if (is_numeric($exploded[1])) {
                    return $this->_baseUrlFFHB . $exploded[1];
                }
            }
        }
        return null;
    }

    /**
    * Parse json from data from API
    *
    * @param string        Data to be parsed
    * @return array[]    
    * @access private
    */
    private function parseJson($data){
        $json = json_decode($data, true);
        return $json !== null && json_last_error() === JSON_ERROR_NONE ? $json : array();
    }
}
?>
