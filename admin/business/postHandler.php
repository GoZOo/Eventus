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
        add_action('admin_post_eventus_seekAdd', array($this, 'addTeamSeek'));  
    }
    
       
    /**************************
    ********** Match **********
    ***************************/
    function synchronizeMatch(){  
        if (get_option("eventus_mapapikey")) {
            if (isset($_POST['teamId']) && $_POST['teamId']) {
                PostHandler::getInstance()->setUpdateMatch();
                Finder::getInstance()->updateMatches([DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId'])]);
            } else {
                $matches = DAO\TeamDAO::getInstance()->getAllTeams();
                Finder::getInstance()->updateMatches($matches);
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
                    !preg_match('/'.strtolower(Helper\StaticHelper::stripAccents($myTeam->getClub()->getString())).'/', strtolower(Helper\StaticHelper::stripAccents($sonMatch['localTeamSon']))),
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
                        isset($otherMatch['idOther']) && $otherMatch['idOther'] ? $otherMatch['idOther'] : null, 
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
                        !preg_match('/'.strtolower(Helper\StaticHelper::stripAccents($myTeam->getClub()->getString())).'/', strtolower(Helper\StaticHelper::stripAccents($otherMatch['localTeamOther']))),
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
        $rdvTime = get_option("eventus_rdvTime");
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
            $team->setTime(($_POST['time'] ? $_POST['time'] : $rdvTime));
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
                ($_POST['time'] ? $_POST['time'] : $rdvTime), 
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
        delete_option('eventus_rdvTime');
        wp_redirect( add_query_arg( 'message', 'succesReset',  'admin.php?page=eventus' ));
    }

    function updateSettings(){
        update_option('eventus_mapapikey', $_POST['mapApiKey'], false);
        update_option('eventus_emailnotif', $_POST['emailNotif'], false);
        update_option('eventus_resetlog', $_POST['resetlog'], false);
        update_option('eventus_season', $_POST['season'], false);
        update_option('eventus_rdvTime', $_POST['rdvTime'], false);
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
        if (isset($_POST['clubId']) && $_POST['clubId']) {
            set_time_limit(0);
            $club = DAO\ClubDAO::getInstance()->getClubById($_POST['clubId']);
            $final = array();
            $error = false;
            if (isset($_POST['departemental']) && $_POST['departemental'] !== '') {
                $res = Seeker::getInstance()->seek($_POST['departemental'], $_POST['seasonId'], $club->getString(), "departemental");
                $final = array_merge($final, $res['data']);
                $error = $res['error'] ? true : $error;
            }                
            if (isset($_POST['regional']) && $_POST['regional'] !== '') {
                $res = Seeker::getInstance()->seek($_POST['regional'], $_POST['seasonId'], $club->getString(), "regional");
                $final = array_merge($final, $res['data']);
                $error = $res['error'] ? true : $error;   
            }                       
            if (isset($_POST['national']) && filter_var($_POST['national'], FILTER_VALIDATE_BOOLEAN)) {
                $res = Seeker::getInstance()->seek("national", $_POST['seasonId'], $club->getString(), "national");
                $final = array_merge($final, $res['data']);
                $error = $res['error'] ? true : $error;  
            }
                 
            wp_redirect( 
                add_query_arg(
                    array(
                        'seeked' => urlencode(json_encode($final)),
                        'clubId' => $_POST['clubId'],
                        'err' => $error,
                    ), 
                    wp_get_referer()
                )
            );   
        } else {
            wp_redirect( add_query_arg( 'message', 'errorSeeker',  wp_get_referer() )); 
        }
    }

    function addTeamSeek(){
        $rdvTime = get_option("eventus_rdvTime");
        if (isset($_POST['data']) && $_POST['data'] && isset($_POST['clubId']) && $_POST['clubId']) {
            $club = DAO\ClubDAO::getInstance()->getClubById(($_POST['clubId'] ? $_POST['clubId'] : ""));
            foreach ($_POST['data'] as $team) {
                if (isset($team['add']) && filter_var($team['add'], FILTER_VALIDATE_BOOLEAN)){
                    $newTeam = new Entities\Team(
                        null, 
                        ($team['nom'] ? $team['nom'] : ""), 
                        ($team['urlOne'] ? $team['urlOne'] : ""), 
                        ($team['urlTwo'] ? $team['urlTwo'] : ""), 
                        ($team['sexe'] == "h" ? 1 : 0), 
                        ($team['sexe'] == "f" ? 1 : 0), 
                        ($team['sexe'] == "m" ? 1 : 0), 
                        0, 
                        0, 
                        $rdvTime, 
                        "",
                        $club
                    );
                    DAO\TeamDAO::getInstance()->insertTeam($newTeam); 
                }                
            }
            wp_redirect( add_query_arg( 'message', 'succesSeeked', 'admin.php?page=eventus' )); 
        } else {
            wp_redirect( add_query_arg( 'message', 'errorNewTeam',  wp_get_referer() )); 
        }        
    }
}
