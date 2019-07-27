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
            'data' => array(
                'departemental' => array(),
                'regional' => array()
            ),
            'error' => false            
        );
        foreach (array_keys($promises) as $key) {    
            $data = json_decode($results[$key]->getBody(), true); 
            $final['data'][$key] = $data && array_key_exists('events', $data) ? $data['events'] : array();
            $col = array_column($final['data'][$key], 'sequence');
            array_multisort($col, SORT_ASC, $final['data'][$key]);
        }
        $final['error'] = $results['departemental']->getStatusCode() >= 400 || $results['regional']->getStatusCode() >= 400;
        
        return $final;
    }

    /**
    * Seek for all teams by a club
    *
    * @param string        Code of the championship
    * @param string        String of a club used to search team
    * @param string        Level of competition
    * @return array[]    
    * @access public
    */
    public function seek($champCode, $string, $level = null){  
        $competitions = $this->parseJson($this->client->get($this->_competition . $champCode)->getBody(), 'events'); //Get all the competitions
        $promises = array();

        foreach ($competitions as $competition) {
            if(!array_key_exists('eventId', $competition)) continue;            
            $promises[$competition['eventId']] = $this->client->getAsync($this->_pools . $competition['eventId']) //Get all the pools
                ->then(
                    function (ResponseInterface $res) use ($string, $level, $competition) {                        
                        $pools = $this->parseJson($res->getBody(), 'pools');
                        $promises1 = array();

                        foreach ($pools as $pool) { 
                            if(!array_key_exists('poolId', $pool)) continue; 
                            $promises1[$pool['poolId']] = $this->client->getAsync($this->_pool . $pool['poolId']) //Get data of a given pool
                                ->then(
                                    function (ResponseInterface $res1) use ($string, $level, $competition, $pool) { 
                                        $teams = $this->parseJson($res1->getBody(), 'teams');
                                        $team = $this->getTeamInPool($teams, $string);
                                        return array(
                                            'status' => $res1->getStatusCode(),
                                            'content' => $team !== null ? array(
                                                'name' => array_key_exists('name', $team) ? $team['name'] : null,
                                                'cat' => array_key_exists('eventName', $competition) ? $competition['eventName'] : null,
                                                'phase' => array_key_exists('phaseName', $pool) ? $pool['phaseName'] : null,
                                                'url' => $competition['eventId']."#poule-".$pool['poolId'],
                                                // 'pool' => array_key_exists('poolName', $pool) ? $pool['poolName'] : null,
                                                // 'level' => $level,
                                            ) : null                                        
                                        );
                                    },
                                    function (RequestException $e) { return array('status' => $e->getResponse()->getStatusCode(), 'content' => null); }
                                );                        
                        }
                        return Promise\settle($promises1)->wait();      
                    },
                    function (RequestException $e) { return array('status' => $e->getResponse()->getStatusCode(), 'content' => null); }
                );
        } 

        $results = Promise\settle($promises)->wait();
                
        $final = array( 
            'data' => array(),
            'error' => false            
        );  

        foreach ($results as $res) {
            foreach ($res['value'] as $r) {
                if ($r['value']['content'] !== null) array_push($final['data'], $r['value']['content']);
                if ($r['value']['status'] >= 400) $final['error'] = true;
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
