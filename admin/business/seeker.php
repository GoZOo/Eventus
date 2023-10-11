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

    private $_baseUrl = "https://www.ffhandball.fr/wp-json/competitions/v1/computeBlockAttributes";
    
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
        $final = array( 
            'data' => array(
                'departemental' => array(),
                'regional' => array()
            ),
            'seasonId' => 0,
            'error' => false            
        );

        $seasonsResponseData = $this->client
            ->get('', [
                'query' => ['block' => 'competitions---saison-selector']
            ])
            ->getBody()
            ->getContents();

        $cfk = Decipher::getInstance()->getCfk();
        $seasonsResponse = Decipher::getInstance()->decipher($seasonsResponseData, $cfk);

        // Pick active season
        if (array_search('1', array_column($seasonsResponse['saisons'], 'administrative')) !== FALSE) {
            $currentSeason = $seasonsResponse['saisons'][array_search('1', array_column($seasonsResponse['saisons'], 'administrative'))];
            $final['seasonId'] = $currentSeason['ext_saisonId'];
            $promises = [
                'departemental' => $this->client->getAsync('', [
                    'query' => [
                        'block' => 'competitions---competition-main-menu',
                        'ext_saison_id' => $currentSeason['ext_saisonId'],
                        'url_competition_type' => 'departemental',
                    ]
                ]),
                'regional' => $this->client->getAsync('', [
                    'query' => [
                        'block' => 'competitions---competition-main-menu',
                        'ext_saison_id' => $currentSeason['ext_saisonId'],
                        'url_competition_type' => 'regional',
                    ]
                ]),
            ];
            $results = Promise\unwrap($promises);

            foreach (array_keys($promises) as $key) {
                $data = Decipher::getInstance()->decipher($results[$key]->getBody()->getContents(), $cfk);

                $final['data'][$key] = $data && array_key_exists('structures', $data) ? $data['structures'] : array();
                $final['data'][$key] = array_map(function($x) { 
                    return [
                        'code' => strtolower(preg_replace('/\s+/', '-', $x['libelle']) .'-'.$x['ext_structureId']),
                        'name' => $x['oldUrl'] . ' - ' . $x['libelle'],
                    ];
                }, $final['data'][$key]);
                $col = array_column($final['data'][$key], 'name');
                array_multisort($col, SORT_ASC, $final['data'][$key]);
            }
            $final['error'] = $results['departemental']->getStatusCode() >= 400 || $results['regional']->getStatusCode() >= 400;
        }

        return $final;
    }

    /**
    * Seek for all teams by a club
    *
    * @param string        Code of the championship
    * @param string        Season id
    * @param string        String of a club used to search team
    * @param string        Level of competition
    * @return array[]    
    * @access public
    */
    public function seek($champCode, $seasonId, $string, $level = null){  
        $promises = array();

        //Get all the competitions
        $competitionsResponseData = $this->client->get('', [
            'query' => [
                'block' => 'competitions---competition-main-menu',
                'ext_saison_id' => $seasonId,
                'url_competition_type' => $level,
                'url_structure' => $champCode
            ]
        ])->getBody();
        
        $cfk = Decipher::getInstance()->getCfk();
        $competitionsResponse = Decipher::getInstance()->decipher($competitionsResponseData, $cfk);

        foreach ($competitionsResponse['competitions'] as $competition) {
            if(!array_key_exists('ext_competitionId', $competition)) continue;
            $competitionLibelle = preg_replace("/([ \-]+)/", '-', trim($competition['libelle']));
            $poolId = strtolower($competitionLibelle.'-'.$competition['ext_competitionId']);
            $promises[$poolId] = $this->client->getAsync('', [
                    'query' => [
                        'block' => 'competitions---poule-selector',
                        'ext_saison_id' => $seasonId,
                        'url_competition_type' => $level,
                        'url_competition' => $poolId
                    ]
                ]) //Get all the pools
                ->then(
                    function (ResponseInterface $res) use ($string, $level, $competition, $poolId, $seasonId, $cfk) {
                        $poolsResponseData = $res->getBody()->getContents();
                        $poolsResponse = Decipher::getInstance()->decipher($poolsResponseData, $cfk);     

                        $promises1 = array();

                        foreach ($poolsResponse['poules'] as $pool) { 
                            if(!array_key_exists('ext_pouleId', $pool)) continue; 
                            
                            $promises1[$pool['ext_pouleId']] = $this->client->getAsync('', [
                                    'query' => [
                                        'block' => 'competitions---poule-selector',
                                        'ext_saison_id' => $seasonId,
                                        'url_competition_type' => $level,
                                        'url_competition' => $poolId,
                                        'ext_poule_id' => $pool['ext_pouleId']
                                    ]
                                ]) //Get data of a given pool
                                ->then(
                                    function (ResponseInterface $res1) use ($string, $level, $competition, $pool, $poolId, $seasonId, $cfk) { 
                                        $champsResponseData = $res1->getBody()->getContents();
                                        $champsResponse = Decipher::getInstance()->decipher($champsResponseData, $cfk);

                                        $teams = $champsResponse['equipe_options'];
                                        $team = $this->getTeamInPool($teams, $string);
                                        return array(
                                            'status' => $res1->getStatusCode(),
                                            'content' => $team !== null ? array(
                                                'name' => array_key_exists('libelle', $team) ? $team['libelle'] : null,
                                                'cat' => array_key_exists('libelle', $competition) ? $competition['libelle'] : null,
                                                'phase' => array_key_exists('libelle', $pool) ? $pool['libelle'] : null,
                                                'url' => 'saison-0-0-' . $seasonId . '/' . $level . '/' . $poolId . '/equipe-' . $team['ext_equipeId'],
                                                'urlPool' => 'saison-0-0-' . $seasonId . '/' . $level . '/' . $poolId . '/poule-' . $pool['ext_pouleId'],
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
            return preg_match('/'.mb_strtolower($string).'/', mb_strtolower($var['libelle']));
        });  
        $team = array_values($team); 
        return $team && sizeof($team) !== 0 ? $team[0] : null;
    }
}
