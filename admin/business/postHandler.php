<?php

namespace Eventus\Admin\Business;
use Eventus\Includes\Datas as DAO;
use Eventus\Includes\Entities as Entities;

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
        add_action('admin_post_syncMatch', array($this, 'synchronizeMatch'));
        add_action('admin_post_delMatch', array($this, 'deleteMatch'));
        add_action('admin_post_majMatch', array($this, 'updateMatch'));
        add_action('admin_post_majHours', array($this, 'updateHoursMatch'));
    
        add_action('admin_post_majClub', array($this, 'updateClub'));
        add_action('admin_post_delClub', array($this, 'deleteClub'));    
    
        add_action('admin_post_majTeam', array($this, 'updateTeam'));
        add_action('admin_post_delTeam', array($this, 'deleteTeam'));
    
        add_action('admin_post_clearLog', array($this, 'deleteLog'));  
    
        add_action('admin_post_resetEventus', array($this, 'resetEventus'));   
    
        add_action('admin_post_majSettings', array($this, 'updateSettings'));     
    }
    
       
    /**************************
    ********** Match **********
    ***************************/
    function synchronizeMatch(){  
        if (get_option("eventus_mapapikey")) {
            if ($_POST['teamId']) {
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
        if ($_POST['teamId']) {
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
            DAO\MatchDAO::getInstance()->updateMatchesHours(Finder::setNewHoursRdv(DAO\MatchDAO::getInstance()->getAllMatchesByTeamId($_POST['teamId'])));
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
       	$allMatchesSon = [];
        for($i=1; $i < $_POST['nbrSonMatch']+1; $i++) { 
            var_dump(date_create_from_format('H:i', $_POST['hourStartSon'.$i]));
            $allMatchesSon[] = new Entities\Match(
            	$_POST['idSon'.$i] ? $_POST['idSon'.$i] : null, 
            	$_POST['matchDaySon'.$i] ? $_POST['matchDaySon'.$i] : null,
            	$_POST['numMatchSon'.$i] ? $_POST['numMatchSon'.$i] : null,
            	$_POST['dateSon'.$i] ? $_POST['dateSon'.$i] : null, 
                $_POST['hourRdvSon'.$i] ? $_POST['hourRdvSon'.$i] : (
                    $_POST['hourStartSon'.$i] ? 
                    date_create_from_format('H:i', $_POST['hourStartSon'.$i])->modify('-'. $myTeam->getTime() .'minutes')->format('H:i:s') :
                    null
                ), 
            	$_POST['hourStartSon'.$i] ? $_POST['hourStartSon'.$i] : null, 
            	$_POST['localTeamSon'.$i] ? $_POST['localTeamSon'.$i] : null, 
            	$_POST['localTeamScoreSon'.$i] ? $_POST['localTeamScoreSon'.$i] : null,
            	$_POST['visitingTeamSon'.$i] ? $_POST['visitingTeamSon'.$i] : null, 
            	$_POST['visitingTeamScoreSon'.$i] ? $_POST['visitingTeamScoreSon'.$i] : null, 
            	strpos(strtolower(Finder::stripAccents($_POST['localTeamSon'.$i])),strtolower(Finder::stripAccents($myTeam->getClub()->getString()))) !== false ? 0 : 1,
            	$_POST['streetSon'.$i] ? $_POST['streetSon'.$i] : null, 
            	$_POST['citySon'.$i] ? $_POST['citySon'.$i] : null, 
            	$_POST['gymSon'.$i] ? $_POST['gymSon'.$i] : null,
            	1, 
            	$_POST['matchChampSon'.$i] ? $_POST['matchChampSon'.$i] : null,
            	$_POST['teamId'],
            	$_POST['idMatchRefSon'.$i] ? $_POST['idMatchRefSon'.$i] : null
            );
        }	
        DAO\MatchDAO::getInstance()->updateMatchesScreen($allMatchesSon, 1, DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId()); 

       	$allMatchesOther = [];
        for($i=1; $i < $_POST['nbrOtherMatch']+1; $i++) { 
            $allMatchesOther[] = new Entities\Match(
            	$_POST['idOther'.$i] ? $_POST['idOther'.$i] : null, 
            	null,
            	null,
            	$_POST['dateOther'.$i] ? $_POST['dateOther'.$i] : null, 
            	$_POST['hourRdvOther'.$i]? $_POST['hourRdvOther'.$i] :  (
                    $_POST['hourStartOther'.$i] ? 
                    date_create_from_format('H:i', $_POST['hourStartOther'.$i])->modify('-'. $myTeam->getTime() .'minutes')->format('H:i:s') :
                    null
                ), 
            	$_POST['hourStartOther'.$i] ? $_POST['hourStartOther'.$i] : null,
            	$_POST['localTeamOther'.$i] ? $_POST['localTeamOther'.$i] : null, 
            	$_POST['localTeamScoreOther'.$i] ? $_POST['localTeamScoreOther'.$i] : null,
            	$_POST['visitingTeamOther'.$i] ? $_POST['visitingTeamOther'.$i] : null, 
            	$_POST['visitingTeamScoreOther'.$i] ? $_POST['visitingTeamScoreOther'.$i] : null, 
            	strpos(strtolower(Finder::stripAccents($_POST['localTeamOther'.$i])),strtolower(Finder::stripAccents($myTeam->getClub()->getString()))) !== false ? 0 : 1,
            	$_POST['streetOther'.$i] ? $_POST['streetOther'.$i] : null, 
            	$_POST['cityOther'.$i] ? $_POST['cityOther'.$i] : null, 
            	$_POST['gymOther'.$i] ? $_POST['gymOther'.$i] : null,
            	2, 
            	null,
            	$_POST['teamId'],
            	null
            );
        }
        DAO\MatchDAO::getInstance()->updateMatchesScreen($allMatchesOther, 2, DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId());  
    }
    
    /**************************
    ********** Club ***********
    ***************************/
    function updateClub(){
        if ($_POST['clubId']) {
            $club = DAO\ClubDAO::getInstance()->getClubById($_POST['clubId']);
        } else {
            $club = new Entities\Club(null, "", "", "", "");
        }        
        $club->setName(($_POST['nom'] ? $_POST['nom'] : ""));
        $club->setString(($_POST['chaine'] ? $_POST['chaine'] : ""));
        $club->setAddress(($_POST['adresse'] ? $_POST['adresse'] : null));
        $club->setImg(($_POST['img'] ? $_POST['img'] : null));

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
        if ($_POST['clubId']) {
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
        $allTeams = [];
        if ($_POST['teamId']) {
            $team = DAO\TeamDAO::getInstance()->getTeamById($_POST['teamId']);
        } else {
            $team = new Entities\Team(null, "", "", 0, 0, 0, 0, 0, "", "", null);
        }        
        $team->setName(($_POST['nom'] ? $_POST['nom'] : null));
        if ( $_POST['urlTwo'] && !$_POST['urlOne'] ) {
            $team->setUrlOne($_POST['urlTwo']);
            $team->setUrlTwo(null);
        } else {
            $team->setUrlOne(($_POST['urlOne'] ? $_POST['urlOne'] : null));
            $team->setUrlTwo(($_POST['urlTwo'] ? $_POST['urlTwo'] : null));
        }
        $team->setBoy(($_POST['sexe'] == "h" ? 1 : 0));
        $team->setGirl(($_POST['sexe'] == "f" ? 1 : 0));
        $team->setMixed(($_POST['sexe'] == "m" ? 1 : 0));
        $team->setTime(($_POST['time'] ? $_POST['time'] : 45));
        $team->setImg(($_POST['img'] ? $_POST['img'] : null));
        $team->setClub(DAO\ClubDAO::getInstance()->getClubById(($_POST['club'] ? $_POST['club'] : null)));
            
        
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
        if ($_POST['teamId']) {
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
        wp_redirect( add_query_arg( 'message', 'succesReset',  'admin.php?page=eventus' ));
    }

    function updateSettings(){
        update_option('eventus_mapapikey', $_POST['mapApiKey'], false);
        update_option('eventus_emailnotif', $_POST['emailNotif'], false);
        update_option('eventus_resetlog', $_POST['resetlog'], false);
        wp_redirect( add_query_arg( 'message', 'succesUpSet',  wp_get_referer() ));
    }

}

?>