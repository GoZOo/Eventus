<?php 

namespace Eventus\Admin\Business;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;


/**
* Seeker is a class that allows you to seek for all teams in your club.
*
* @package  Admin/Business
* @access   public
*/
class Seeker {
    /**
    * @var Seeker   $_instance  Var use to store an instance
    */
    private static $_instance;

    private $_baseUrl = "https://jjht57whqb.execute-api.us-west-2.amazonaws.com/prod/";    
    private $_championship = "championship/";
    private $_competition = "competition/";
    private $_pools = "competitionPool/";
    private $_pool = "pool/";
    private $_departemental = "D";
    private $_regional = "R";
    
    /**
    * Returns an instance of the object
    *
    * @return Seeker
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) self::$_instance = new Seeker();
        return self::$_instance;
    }

    private function __construct() {        
        $this->client = new Client(['base_uri' => $this->_baseUrl]);
    }     

    /**
    * Get constants list of championship for departements and regions
    *
    * @return array[]    
    * @access public
    */
    public function getChampionship() {
        $promises = [
            'departemental' => $this->client->getAsync($this->_championship . $this->_departemental),
            'regional' => $this->client->getAsync($this->_championship . $this->_regional)
        ];
        $results = Promise\unwrap($promises);
        
        $final = array( 
            "departemental" => array(),
            "regional" => array()
        );
        foreach (array_keys($promises) as $key) {    
            $data = json_decode($results[$key]->getBody(), true); 
            $final[$key] = $data && array_key_exists('events', $data) ? $data['events'] : array();
            $col = array_column($final[$key], 'sequence');
            array_multisort($col, SORT_ASC, $final[$key]);
        }
        return $final;
    }

    /**
    * Seek for all teams by a club
    *
    * @param string[]      Codes of the championships
    * @param string        String of a club used to search team
    * @return array[]    
    * @access public
    */
    public function seek($champsCodes, $string){ 
        set_time_limit(0);
        $promises = array();      
        foreach ($champsCodes as $level => $champCode) {
            if ($champCode === false) continue;
            $promises[$level] = $this->client->getAsync($this->_competition . $champCode) //Get all the competitions
                ->then(
                    function (ResponseInterface $res) use ($string, $level) {                  
                        $competitions = $this->parseJson($res->getBody(), 'events');
                        $promises1 = array();

                        foreach ($competitions as $competition) {
                            if(!array_key_exists('eventId', $competition)) continue;            
                            $promises1[$competition['eventId']] = $this->client->getAsync($this->_pools . $competition['eventId']) //Get all the pools
                                ->then(
                                    function (ResponseInterface $res1) use ($string, $level, $competition) {                        
                                        $pools = $this->parseJson($res1->getBody(), 'pools');
                                        $promises2 = array();

                                        foreach ($pools as $pool) { 
                                            if(!array_key_exists('poolId', $pool)) continue; 
                                            $promises2[$pool['poolId']] = $this->client->getAsync($this->_pool . $pool['poolId']) //Get data of a given pool
                                                ->then(
                                                    function (ResponseInterface $res2) use ($string, $level, $competition, $pool) {                  
                                                        $teams = $this->parseJson($res2->getBody(), 'teams');
                                                        $team = $this->getTeamInPool($teams, $string);
                                                        if ($team !== null) return array(
                                                            "name" => array_key_exists('name', $team) ? $team['name'] : null,
                                                            "cat" => array_key_exists('eventName', $competition) ? $competition['eventName'] : null,
                                                            "phase" => array_key_exists('phaseName', $pool) ? $pool['phaseName'] : null,
                                                            "url" => $competition['eventId']."#poule-".$pool['poolId'],
                                                            // "pool" => array_key_exists('poolName', $pool) ? $pool['poolName'] : null,
                                                            // "level" => $level,
                                                        );
                                                    },
                                                    function (RequestException $e) { $e->getMessage(); }
                                                );                        
                                        }
                                        return array_filter(Promise\unwrap($promises2));      
                                    },
                                    function (RequestException $e) { $e->getMessage(); }
                                );
                        } 
                        return array_filter(Promise\unwrap($promises1));   
                    },
                    function (RequestException $e) { $e->getMessage(); }
                );    
        } 
        
        $results = array_filter(Promise\unwrap($promises));
        $final = array();
        foreach ($results as $res) {
            foreach ($res as $r) {
                array_push($final, $r);
            }
        }
        return $final;
    }

    /**
    * Parse json from data from API
    *
    * @param string        Data to be parsed
    * @param string        Key to find
    * @return array[]    
    * @access private
    */
    private function parseJson($data, $key){
        $json = json_decode($data, true);
        return $json !== null && json_last_error() === JSON_ERROR_NONE && array_key_exists($key, $json) ? $json[$key] : array();
    }
    
    /**
    * Find team in a pool
    *
    * @param array[]       Pool data
    * @param string        String to find
    * @return array[]    
    * @access private
    */
    private function getTeamInPool($pool, $string){
        $team = array_filter($pool, function ($var) use($string) {
            return strpos(mb_strtolower($var['name']), mb_strtolower($string)) !== false;
        });  
        $team = array_values($team); 
        return $team && sizeof($team) !== 0 ? $team[0] : null;
    }
}
