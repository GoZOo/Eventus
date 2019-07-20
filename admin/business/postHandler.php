<?php

namespace Eventus\Admin\Business;
use Eventus\Includes\DAO as DAO;
use Eventus\Includes\DTO as Entities;
use Eventus\Admin\Business\Helper as Helper;

/**
* PostHandler is a class use to manage submit form
*
* @package  Admin/Business
* @access   public
*/
class PostHandler {
    /**
    * @var PostHandler   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return PostHandler
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new PostHandler();
        }
        return self::$_instance;
    }

    private function __construct() {
        add_action('admin_post_eventus_syncMatch', array($this, 'synchronizeMatch'));
        add_action('admin_post_eventus_delMatch', array($this, 'deleteMatch'));
        add_action('admin_post_eventus_majMatch', array($this, 'updateMatch'));
        add_action('admin_post_eventus_majHours', array($this, 'updateHoursMatch'));
    
        add_action('admin_post_eventus_majClub', array($this, 'updateClub'));
        add_action('admin_post_eventus_delClub', array($this, 'deleteClub'));    
    
        add_action('admin_post_eventus_majTeam', array($this, 'updateTeam'));
        add_action('admin_post_eventus_delTeam', array($this, 'deleteTeam'));
    
        add_action('admin_post_eventus_clearLog', array($this, 'deleteLog'));  
    
        add_action('admin_post_eventus_resetEventus', array($this, 'resetEventus'));   
    
        add_action('admin_post_eventus_majSettings', array($this, 'updateSettings'));         
        
        add_action('admin_post_eventus_majIcs', array($this, 'updateIcs'));    
        add_action('admin_post_eventus_delIcs', array($this, 'deleteIcs'));  

        add_action('admin_post_eventus_seek', array($this, 'seek'));  
    }
    
       
    /**************************
    ********** Match **********
    ***************************/
    function synchronizeMatch(){  
        if (get_option("eventus_mapapikey")) {
            if (isset($_POST['teamId']) && $_POST['teamId']) {
                PostHandler::getInstance()->setUpdateMatch();
                Finder::getInstance()->updateMatches(DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId']));
            } else {
                foreach (DAO\TeamDAO::getInstance()->getAllTeams() as $team) {
                    Finder::getInstance()->updateMatches($team);
                }
            }
            date_default_timezone_set("Europe/Paris");
            update_option('eventus_datetimesynch', date("Y-m-d H:i:s"), false);
            if (!filesize(plugin_dir_path( __FILE__ ).'../../finder.log')) {
                wp_redirect( add_query_arg( 'message', 'succesSyncMatch',  wp_get_referer() ));
            } else {
                wp_redirect( add_query_arg( 'message', 'warningSyncMatch',  wp_get_referer() ));
            }    
        } else {
            wp_redirect( add_query_arg( 'message', 'noMapApiKey',  wp_get_referer() ));
        }           
    }
    
    function deleteMatch(){   
        if (isset($_POST['teamId']) && $_POST['teamId']) {
            DAO\MatchDAO::getInstance()->deleteMatches($_POST['teamId']);
        } else {
            DAO\MatchDAO::getInstance()->deleteMatches();
            DAO\Database::getInstance()->resetAutoIncr('matches');
        }
        wp_redirect( add_query_arg( 'message', 'succesDelMatch',  wp_get_referer() ));
    }

    function updateMatch(){
        PostHandler::getInstance()->setUpdateMatch();
        wp_redirect( add_query_arg( 'message', 'succesUpMatch',  wp_get_referer() ));
    }
    
    function updateHoursMatch(){
        if (get_option("eventus_mapapikey")) {
            PostHandler::getInstance()->setUpdateMatch();
            DAO\MatchDAO::getInstance()->updateMatchesHours(Finder::getInstance()->setNewHoursRdv(DAO\MatchDAO::getInstance()->getAllMatchesByTeamId($_POST['teamId'])));
            if (!filesize(plugin_dir_path( __FILE__ ).'../../finder.log')) {
                wp_redirect( add_query_arg( 'message', 'succesUpHoursMatch',  wp_get_referer() ));
            } else {
                wp_redirect( add_query_arg( 'message', 'warningUpHoursMatch',  wp_get_referer() ));
            }    
        } else {
            wp_redirect( add_query_arg( 'message', 'noMapApiKey',  wp_get_referer() ));
        }  
    }

    function setUpdateMatch(){
        $myTeam = DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId']);

        if (isset($_POST['sonMatches'])) {
            $allMatchesSon = [];
            foreach ($_POST['sonMatches'] as $sonMatch) {
                $allMatchesSon[] = new Entities\Match(
                    $sonMatch['idSon'] ? $sonMatch['idSon'] : null, 
                    $sonMatch['matchDaySon'] ? $sonMatch['matchDaySon'] : null,
                    $sonMatch['numMatchSon'] ? $sonMatch['numMatchSon'] : null,
                    $sonMatch['dateSon'] ? $sonMatch['dateSon'] : null, 
                    $sonMatch['hourRdvSon'] ? $sonMatch['hourRdvSon'] : (
                        $sonMatch['hourStartSon'] ? 
                        date_create_from_format('H:i', $sonMatch['hourStartSon'])->modify('-'. $myTeam->getTime() .'minutes')->format('H:i:s') :
                        null
                    ), 
                    $sonMatch['hourStartSon'] ? $sonMatch['hourStartSon'] : null, 
                    $sonMatch['localTeamSon'] ? $sonMatch['localTeamSon'] : null, 
                    $sonMatch['localTeamScoreSon'] ? $sonMatch['localTeamScoreSon'] : null,
                    $sonMatch['visitingTeamSon'] ? $sonMatch['visitingTeamSon'] : null, 
                    $sonMatch['visitingTeamScoreSon'] ? $sonMatch['visitingTeamScoreSon'] : null, 
                    strpos(strtolower(Helper\StaticHelper::stripAccents($sonMatch['localTeamSon'])),strtolower(Helper\StaticHelper::stripAccents($myTeam->getClub()->getString()))) !== false ? 0 : 1,
                    $sonMatch['streetSon'] ? $sonMatch['streetSon'] : null, 
                    $sonMatch['citySon'] ? $sonMatch['citySon'] : null, 
                    $sonMatch['gymSon'] ? $sonMatch['gymSon'] : null,
                    1, 
                    $sonMatch['matchChampSon'] ? $sonMatch['matchChampSon'] : null,
                    $_POST['teamId'],
                    $sonMatch['idMatchRefSon'] ? $sonMatch['idMatchRefSon'] : null
                );
            } 
            DAO\MatchDAO::getInstance()->updateMatchesScreen($allMatchesSon, 1, DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId()); 
        } else {            
            DAO\MatchDAO::getInstance()->updateMatchesScreen([], 1, DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId()); 
        }   

        if (isset($_POST['otherMatches'])) {
            $allMatchesOther = [];
            foreach ($_POST['otherMatches'] as $otherMatch) {
                if ($otherMatch['localTeamOther'] && $otherMatch['visitingTeamOther']) {
                    $allMatchesOther[] = new Entities\Match(
                        isset($_POST['idOther']) && $otherMatch['idOther'] ? $otherMatch['idOther'] : null, 
                        null,
                        null,
                        $otherMatch['dateOther'] ? $otherMatch['dateOther'] : null, 
                        $otherMatch['hourRdvOther']? $otherMatch['hourRdvOther'] :  (
                            $otherMatch['hourStartOther'] ? 
                            date_create_from_format('H:i', $otherMatch['hourStartOther'])->modify('-'. $myTeam->getTime() .'minutes')->format('H:i:s') :
                            null
                        ), 
                        $otherMatch['hourStartOther'] ? $otherMatch['hourStartOther'] : null,
                        $otherMatch['localTeamOther'] ? $otherMatch['localTeamOther'] : null, 
                        $otherMatch['localTeamScoreOther'] ? $otherMatch['localTeamScoreOther'] : null,
                        $otherMatch['visitingTeamOther'] ? $otherMatch['visitingTeamOther'] : null, 
                        $otherMatch['visitingTeamScoreOther'] ? $otherMatch['visitingTeamScoreOther'] : null, 
                        strpos(strtolower(Helper\StaticHelper::stripAccents($otherMatch['localTeamOther'])),strtolower(Helper\StaticHelper::stripAccents($myTeam->getClub()->getString()))) !== false ? 0 : 1,
                        $otherMatch['streetOther'] ? $otherMatch['streetOther'] : null, 
                        $otherMatch['cityOther'] ? $otherMatch['cityOther'] : null, 
                        $otherMatch['gymOther'] ? $otherMatch['gymOther'] : null,
                        2, 
                        null,
                        $_POST['teamId'],
                        null
                    );
                }                
            }
            DAO\MatchDAO::getInstance()->updateMatchesScreen($allMatchesOther, 2, DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId());  
        } else {            
            DAO\MatchDAO::getInstance()->updateMatchesScreen([], 2, DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId());  
        } 
    }
    
    /**************************
    ********** Club ***********
    ***************************/
    function updateClub(){
        if (isset($_POST['clubId']) && $_POST['clubId']) {
            $club = DAO\ClubDAO::getInstance()->getClubById($_POST['clubId']);
            $club->setName(($_POST['nom'] ? $_POST['nom'] : $club->getName()));
            $club->setString(($_POST['chaine'] ? $_POST['chaine'] : $club->getString()));
            $club->setAddress(($_POST['adresse'] ? $_POST['adresse'] : $club->getAddress()));
            $club->setImg(($_POST['img'] ? $_POST['img'] : null)); //$club->getImg()
        } else {
            $club = new Entities\Club(
                null, 
                $_POST['nom'] ? $_POST['nom'] : "", 
                $_POST['chaine'] ? $_POST['chaine'] : "", 
                $_POST['adresse'] ? $_POST['adresse'] : null, 
                $_POST['img'] ? $_POST['img'] : null, 
                get_option("eventus_season")
            );
        }   

        if($club->getName() && $club->getString() && $club->getAddress()){
            if ($club->getId()) {
                DAO\ClubDAO::getInstance()->updateClub($club); 
                wp_redirect( add_query_arg( 'message', 'succesUpClub',  wp_get_referer() )); 
            } else {
                $newId = DAO\ClubDAO::getInstance()->insertClub($club); 
                wp_redirect( add_query_arg( 'message', 'succesNewClub', 'admin.php?page=eventus_club&action=club&clubId='.$newId )); 
            }  
        } else {    
            if ($club->getId()) {
                wp_redirect( add_query_arg( 'message', 'errorUpClub',  wp_get_referer() )); 
            } else {
                wp_redirect( add_query_arg( 'message', 'errorNewClub',  wp_get_referer() )); 
            }         
        }            
    }

    function deleteClub(){
        if (isset($_POST['clubId']) && $_POST['clubId']) {
            DAO\ClubDAO::getInstance()->deleteClub($_POST['clubId']); 
        } else {
            DAO\ClubDAO::getInstance()->deleteClub(); 
            DAO\Database::getInstance()->resetAutoIncr('clubs');
        }        
        wp_redirect( add_query_arg( 'message', 'succesDelClub', 'admin.php?page=eventus_club')); 
    }
    
    /**************************
    *********** Team ***********
    ***************************/
    function updateTeam(){  
        if (isset($_POST['teamId']) && $_POST['teamId']) {
            $team = DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId']);
            $team->setName(($_POST['nom'] ? $_POST['nom'] : $team->getName()));
            if ( $_POST['urlTwo'] && !$_POST['urlOne'] ) {
                $team->setUrlOne(($_POST['urlTwo'] ? $_POST['urlTwo'] : $team->getUrlOne()));
                $team->setUrlTwo(null);
            } else {
                $team->setUrlOne(($_POST['urlOne'] ? $_POST['urlOne'] : $team->getUrlOne()));
                $team->setUrlTwo(($_POST['urlTwo'] ? $_POST['urlTwo'] : $team->getUrlTwo()));
            }
            $team->setBoy(($_POST['sexe'] == "h" ? 1 : 0));
            $team->setGirl(($_POST['sexe'] == "f" ? 1 : 0));
            $team->setMixed(($_POST['sexe'] == "m" ? 1 : 0));
            $team->setTime(($_POST['time'] ? $_POST['time'] : 50));
            $team->setImg(($_POST['img'] ? $_POST['img'] : null)); //$team->getImg()
            $team->setClub(DAO\ClubDAO::getInstance()->getClubById(($_POST['club'] ? $_POST['club'] : $team->getClub()->getId())));
        } else {
            $team = new Entities\Team(
                null, 
                ($_POST['nom'] ? $_POST['nom'] : ""), 
                ($_POST['urlOne'] ? $_POST['urlOne'] : ""), 
                ($_POST['urlTwo'] ? $_POST['urlTwo'] : ""), 
                ($_POST['sexe'] == "h" ? 1 : 0), 
                ($_POST['sexe'] == "f" ? 1 : 0), 
                ($_POST['sexe'] == "m" ? 1 : 0), 
                0, 
                0, 
                ($_POST['time'] ? $_POST['time'] : 50), 
                ($_POST['img'] ? $_POST['img'] : ""),
                (DAO\ClubDAO::getInstance()->getClubById(($_POST['club'] ? $_POST['club'] : "")))
            );
        }  
        
        if($team->getName() && $team->getClub()){
            if ($team->getId()) {
                DAO\TeamDAO::getInstance()->updateTeam($team); 
                wp_redirect( add_query_arg( 'message', 'succesUpTeam',  wp_get_referer() )); 
            } else {
                $newId = DAO\TeamDAO::getInstance()->insertTeam($team); 
                wp_redirect( add_query_arg( 'message', 'succesNewTeam', 'admin.php?page=eventus&action=team&teamId='.$newId )); 
            }
        } else {    
            if ($team->getId()) {
                wp_redirect( add_query_arg( 'message', 'errorUpTeam',  wp_get_referer() )); 
            } else {
                wp_redirect( add_query_arg( 'message', 'errorNewTeam',  wp_get_referer() )); 
            }         
        } 
    }

    function deleteTeam(){
        if (isset($_POST['teamId']) && $_POST['teamId']) {
            DAO\TeamDAO::getInstance()->deleteTeam($_POST['teamId']); 
            wp_redirect( add_query_arg( 'message', 'succesDelTeam', 'admin.php?page=eventus')); 
        } else {
            DAO\TeamDAO::getInstance()->deleteTeam(); 
            DAO\Database::getInstance()->resetAutoIncr('teams');
            wp_redirect( add_query_arg( 'message', 'succesDelTeams', 'admin.php?page=eventus')); 
        }        
    }
    
    /**************************
    ********** Log ***********
    ***************************/
    function deleteLog(){
        file_put_contents(plugin_dir_path( __FILE__ ).'../../finder.log', '');  
        wp_redirect( add_query_arg( 'message', 'succesDelLog',  wp_get_referer() ));
    }

    /**************************
    ********* Eventus *********
    ***************************/
    function resetEventus(){
        DAO\Database::getInstance()->resetTables();
        delete_option('eventus_mapapikey');
        delete_option('eventus_datetimesynch');
        delete_option('eventus_emailnotif');
        delete_option('eventus_season');
        wp_redirect( add_query_arg( 'message', 'succesReset',  'admin.php?page=eventus' ));
    }

    function updateSettings(){
        update_option('eventus_mapapikey', $_POST['mapApiKey'], false);
        update_option('eventus_emailnotif', $_POST['emailNotif'], false);
        update_option('eventus_resetlog', $_POST['resetlog'], false);
        update_option('eventus_season', $_POST['season'], false);
        wp_redirect( add_query_arg( 'message', 'succesUpSet',  wp_get_referer() ));
    }

    /**************************
    ******** Calendar *********
    ***************************/
    function updateIcs(){
        if (isset($_POST['teamId']) && $_POST['teamId']) {
            Ics::init(DAO\MatchDAO::getInstance()->getAllMatchesByTeamId($_POST['teamId']));
            wp_redirect( add_query_arg( 'message', 'succesOneIcs',  wp_get_referer() ));  
        } else {
            foreach (DAO\TeamDAO::getInstance()->getAllTeams() as $team) {
                Ics::init(DAO\MatchDAO::getInstance()->getAllMatchesByTeamId($team->getId()));
            }
            wp_redirect( add_query_arg( 'message', 'succesMultiIcs',  wp_get_referer() ));  
        } 
    }

    function deleteIcs(){
        if (isset($_POST['teamId']) && $_POST['teamId']) {
            $team = DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId']);
            unlink(plugin_dir_path( __FILE__ ).'../../public/ics/'.$team->getClub()->getName().'_'.$team->getName().'_'.$team->getId().'.ics');            
        } else {
            $files = glob(plugin_dir_path( __FILE__ ).'../../public/ics/' . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned    
            foreach( $files as $file ){
                unlink( $file );
            } 
        }   
        wp_redirect( add_query_arg( 'message', 'succesDelIcs',  wp_get_referer() ));       
    }

    /**************************
    *********** Seek **********
    ***************************/
    function seek(){
        // var_dump($_POST);
        // if (isset($_POST['clubId']) && $_POST['clubId']) {
        //     set_time_limit(0);
        //     $club = DAO\ClubDAO::getInstance()->getClubById($_POST['clubId']);
        //     $res = array();
        //     $seeker = new Seeker();
        //     if (isset($_POST['departemental']) && $_POST['departemental'] !== ''){
        //         array_push($res, $seeker->seek($_POST['departemental'], $club->getString()));
        //     }            
        //     if (isset($_POST['regional']) && $_POST['regional'] !== ''){
        //         array_push($res, $seeker->seek($_POST['regional'], $club->getString()));
        //     }            
        //     if (isset($_POST['national']) && filter_var($_POST['national'], FILTER_VALIDATE_BOOLEAN)){
        //         array_push($res, $seeker->seek("national", $club->getString()));
        //     }
        //     var_dump($res); exit;
        // }
        wp_redirect( add_query_arg( 'data', urlencode('{"0":{"team":{"position":3,"name":"THOUARE HBC 2","points":22,"games":10,"wins":6,"draws":0,"defeats":4,"scored":237,"missed":243,"difference":-6},"name":"-10 ANS MIXTE","phase":"-10 ANS MIXTE 2EME PHASE","pool":"D 6","url":"https:\/\/ffhandball.fr\/fr\/competition\/11587#poule-57749"},"1":{"team":{"position":5,"name":"THOUARE HBC 1","points":13,"games":9,"wins":2,"draws":0,"defeats":7,"scored":233,"missed":268,"difference":-35},"name":"-10 ANS MIXTE","phase":"-10 ANS MIXTE 2EME PHASE","pool":"D 2","url":"https:\/\/ffhandball.fr\/fr\/competition\/11587#poule-59959"},"2":{"team":{"position":6,"name":"THOUARE HANDBALL CLUB","points":14,"games":10,"wins":2,"draws":0,"defeats":8,"scored":148,"missed":257,"difference":-109},"name":"-11 ANS F","phase":"-11 ANS F 2EME PHASE","pool":"D 1 B","url":"https:\/\/ffhandball.fr\/fr\/competition\/11573#poule-57803"},"3":{"team":{"position":2,"name":"THOUARE HBC","points":36,"games":14,"wins":11,"draws":0,"defeats":3,"scored":333,"missed":244,"difference":89},"name":"-12 ANS F","phase":"-12 ANS F 2EME PHASE","pool":"D 1","url":"https:\/\/ffhandball.fr\/fr\/competition\/11043#poule-53752"},"4":{"team":{"position":4,"name":"THOUARE HBC 1","points":26,"games":14,"wins":6,"draws":0,"defeats":8,"scored":325,"missed":335,"difference":-10},"name":"-12 ANS M","phase":"-12 ANS M 2EME PHASE","pool":"D 1 A","url":"https:\/\/ffhandball.fr\/fr\/competition\/11034#poule-53746"},"5":{"team":{"position":5,"name":"THOUARE HBC 2","points":16,"games":10,"wins":2,"draws":2,"defeats":6,"scored":231,"missed":255,"difference":-24},"name":"-12 ANS M","phase":"-12 ANS M 2EME PHASE","pool":"D 4 ","url":"https:\/\/ffhandball.fr\/fr\/competition\/11034#poule-57663"},"6":{"team":{"position":3,"name":"THOUARE HANDBALL CLUB","points":31,"games":14,"wins":7,"draws":3,"defeats":4,"scored":277,"missed":258,"difference":19},"name":"-14 ANS F","phase":"-14 ANS F 2EME PHASE","pool":"D 1","url":"https:\/\/ffhandball.fr\/fr\/competition\/11042#poule-53750"},"7":{"team":{"position":6,"name":"THOUARE HANDBALL CLUB","points":13,"games":10,"wins":1,"draws":1,"defeats":8,"scored":262,"missed":311,"difference":-49},"name":"-14 ANS M","phase":"-14 ANS M 2EME PHASE","pool":"D 5","url":"https:\/\/ffhandball.fr\/fr\/competition\/11002#poule-57633"},"8":{"team":{"position":3,"name":"THOUARE HANDBALL CLUB","points":20,"games":10,"wins":5,"draws":0,"defeats":5,"scored":226,"missed":224,"difference":2},"name":"-16 ANS M","phase":"-16 ANS M 2EME PHASE","pool":"D 7","url":"https:\/\/ffhandball.fr\/fr\/competition\/11000#poule-57581"},"9":{"team":{"position":1,"name":"THOUARE HANDBALL CLUB","points":63,"games":22,"wins":20,"draws":1,"defeats":1,"scored":676,"missed":492,"difference":184},"name":"2EME D. T. M.","phase":"2EME D. T. M. ","pool":"2EME DTM","url":"https:\/\/ffhandball.fr\/fr\/competition\/10930#poule-47504"}}'),  wp_get_referer() ));   
        
    }
}
