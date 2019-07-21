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
class Seeker {
    // use Helper\TraitHelper;
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
        if (is_null(self::$_instance)) {
            self::$_instance = new Seeker();
        }
        return self::$_instance;
    }
    private function __construct() {}     

    public function getChampionship() {
        $competitions = array(            
            "departemental" => $this->_baseUrl . $this->_championship . $this->_departemental,
            "regional" => $this->_baseUrl . $this->_championship . $this->_regional
        );
        $res = array(
            "departemental" => array(),
            "regional" => array()
        );
        foreach ($competitions as $key => $url) {            
            $output = $this->fetch($url);
            $res[$key] = $output && $output['events'] ? $output['events'] : array();
            $col = array_column($res[$key], 'sequence');
            array_multisort($col, SORT_ASC, $res[$key]);
        }
        return $res;
    }

    public function seek($champCode, $string, $compet){
        $res = array();
        $competitions = $this->getCompetitions($champCode);   
                
        foreach ($competitions as $competition) {
            if(!array_key_exists('eventId', $competition)) continue;
            $pools = $this->getPools($competition['eventId']);

            foreach ($pools as $pool) {
                if(!array_key_exists('poolId', $pool)) continue;
                $team = $this->getTeamInPool($this->getPoolDetail($pool['poolId']), $string);

                if ($team !== null) array_push($res, array(
                    "name" => array_key_exists('name', $team) ? $team['name'] : null,
                    "compet" => $compet,
                    "cat" => array_key_exists('eventName', $competition) ? $competition['eventName'] : null,
                    "phase" => array_key_exists('phaseName', $pool) ? $pool['phaseName'] : null,
                    // "pool" => array_key_exists('poolName', $pool) ? $pool['poolName'] : null,
                    "url" => "https://ffhandball.fr/fr/competition/".$competition['eventId']."#poule-".$pool['poolId']
                ));
            }          
        }
        return $res;
    }

    private function getCompetitions($champId){
        $output = $this->fetch($this->_baseUrl . $this->_competition . $champId);
        return $output && array_key_exists('events', $output) ? $output['events'] : array();
    }

    private function getPools($competId){
        $output = $this->fetch($this->_baseUrl . $this->_pools . $competId);
        return $output && array_key_exists('pools', $output) ? $output['pools'] : array();
    }

    private function getPoolDetail($poolId){
        $output = $this->fetch($this->_baseUrl . $this->_pool . $poolId);
        return $output && array_key_exists('teams', $output) ? $output['teams'] : array();
    }
    
    private function getTeamInPool($pool, $string){
        $team = array_filter($pool, function ($var) use($string) {
            return strpos(mb_strtolower($var['name']), mb_strtolower($string)) !== false;
        });  
        $team = array_values($team); 
        return $team && sizeof($team) !== 0 ? $team[0] : null;
    }

    private function fetch($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch,CURLOPT_ENCODING , "gzip");            
        $output = curl_exec($ch);
        curl_close($ch); 
        return json_decode($output, true);   
    }
}
