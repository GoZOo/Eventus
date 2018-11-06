<?php
    add_action('admin_post_syncMatch', 'synchronizeMatch');
    add_action('admin_post_delMatch', 'deleteMatch');
    add_action('admin_post_majMatch', 'updateMatch');
    add_action('admin_post_majHours', 'updateHoursMatch');

    add_action('admin_post_majClub', 'updateClub');
    add_action('admin_post_delClub', 'deleteClub');    

    add_action('admin_post_majTeam', 'updateTeam');
    add_action('admin_post_delTeam', 'deleteTeam');

    add_action('admin_post_clearLog', 'deleteLog');  

    add_action('admin_post_resetEventus', 'resetEventus');   

    add_action('admin_post_majSettings', 'updateSettings');     
       
    /**************************
    ********** Match **********
    ***************************/
    function synchronizeMatch(){  
        if (get_option("eventus_mapapikey")) {
            if ($_POST['teamId']) {
                updateMatch();
                Finder::getInstance()->updateMatches(TeamDAO::getInstance()->getTeamById($_POST['teamId']));
            } else {
                foreach (TeamDAO::getInstance()->getAllTeams() as $team) {
                    Finder::getInstance()->updateMatches($team);
                }
            }
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
    		MatchDAO::getInstance()->deleteMatches($_POST['teamId']);
    	} else {
    		MatchDAO::getInstance()->deleteMatches(null);
    	}
		wp_redirect( add_query_arg( 'message', 'succesDelMatch',  wp_get_referer() ));
    }

    function updateMatch(){
    	$myTeam = TeamDAO::getInstance()->getTeamById($_POST['teamId']);
        $nbrSonMatch = $_POST['nbrSonMatch'];   
       	$allMatches = [];
        for($i=1; $i < $nbrSonMatch+1; $i++) { 
            $allMatches[] = new Match(
            	$_POST['idSon'.$i] ? $_POST['idSon'.$i] : null, 
            	$_POST['matchDaySon'.$i] ? $_POST['matchDaySon'.$i] : null,
            	$_POST['numMatchSon'.$i] ? $_POST['numMatchSon'.$i] : null,
            	$_POST['dateSon'.$i] ? $_POST['dateSon'.$i] : null, 
            	$_POST['hourRdvSon'.$i]? $_POST['hourRdvSon'.$i] : null, 
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
            	$myTeam,
            	$_POST['idMatchRefSon'.$i] ? $_POST['idMatchRefSon'.$i] : null
            );
        }	
        MatchDAO::getInstance()->updateMatches2($allMatches, 1, TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId());  

        $nbrOtherMatch = $_POST['nbrOtherMatch'];
       	$allMatches = [];
        for($i=1; $i < $nbrOtherMatch+1; $i++) { 
            $allMatches[] = new Match(
            	$_POST['idOther'.$i] ? $_POST['idOther'.$i] : null, 
            	null,
            	null,
            	$_POST['dateOther'.$i] ? $_POST['dateOther'.$i] : null, 
            	$_POST['hourRdvOther'.$i]? $_POST['hourRdvOther'.$i] : null, 
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
            	$myTeam,
            	null
            );
        }	
        MatchDAO::getInstance()->updateMatches2($allMatches, 2, TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId());  
		wp_redirect( add_query_arg( 'message', 'succesUpMatch',  wp_get_referer() ));
    }
    
    function updateHoursMatch(){
        if (get_option("eventus_mapapikey")) {
            updateMatch();
            MatchDAO::getInstance()->updateMatches3(Finder::setNewHoursRdv(MatchDAO::getInstance()->getAllMatchesByTeamId($_POST['teamId'])));
            if (!filesize(plugin_dir_path( __FILE__ ).'../../finder.log')) {
                wp_redirect( add_query_arg( 'message', 'succesUpHoursMatch',  wp_get_referer() ));
            } else {
                wp_redirect( add_query_arg( 'message', 'warningUpHoursMatch',  wp_get_referer() ));
            }    
        } else {
            wp_redirect( add_query_arg( 'message', 'noMapApiKey',  wp_get_referer() ));
        }  
    }
    
    /**************************
    ********** Club ***********
    ***************************/
    function updateClub(){
        if ($_POST['clubId']) {
            $club = ClubDAO::getInstance()->getClubById($_POST['clubId']);
        } else {
            $club = new Club(null, "", "", 0, 0, 0, "");
        }        
        $club->setName(($_POST['nom'] ? $_POST['nom'] : ""));
        $club->setString(($_POST['chaine'] ? $_POST['chaine'] : ""));
        $club->setBoy(($_POST['h'] ? 1 : 0));
        $club->setGirl(($_POST['f'] ? 1 : 0));
        $club->setMixed(($_POST['m'] ? 1 : 0));
        $club->setAdress(($_POST['adresse'] ? $_POST['adresse'] : null));

        if ($club->getId()) {
            ClubDAO::getInstance()->updateClub($club); 
            wp_redirect( add_query_arg( 'message', 'succesUpClub',  wp_get_referer() )); 
        } else {
            $newId = ClubDAO::getInstance()->insertClub($club); 
            wp_redirect( add_query_arg( 'message', 'succesNewClub', 'admin.php?page=eventus_club&action=club&clubId='.$newId )); 
        }        
    }

    function deleteClub(){
        if ($_POST['clubId']) {
            ClubDAO::getInstance()->deleteClub($_POST['clubId']); 
    	} else {
            ClubDAO::getInstance()->deleteClub(null); 
    	}
        
        wp_redirect( add_query_arg( 'message', 'succesDelClub', 'admin.php?page=eventus_club')); 
    }
    
    /**************************
    *********** Team ***********
    ***************************/
    function updateTeam(){    
        $allTeams = [];
        if ($_POST['teamId']) {
            $team = TeamDAO::getInstance()->getTeamById($_POST['teamId']);
        } else {
            $team = new Team(null, "", "", 0, 0, 0, 0, 0, "", "", null);
        }        
        $team->setName(($_POST['nom'] ? $_POST['nom'] : null));
        $team->setUrl(($_POST['url'] ? $_POST['url'] : null));
        $team->setBoy(($_POST['sexe'] == "h" ? 1 : 0));
        $team->setGirl(($_POST['sexe'] == "f" ? 1 : 0));
        $team->setMixed(($_POST['sexe'] == "m" ? 1 : 0));
        $team->setTime(($_POST['time'] ? $_POST['time'] : null));
        $team->setImg(($_POST['img'] ? $_POST['img'] : null));
        $team->setClub(ClubDAO::getInstance()->getClubById(($_POST['club'] ? $_POST['club'] : null)));
        if ($team->getId()) {
            TeamDAO::getInstance()->updateTeam($team); 
            wp_redirect( add_query_arg( 'message', 'succesUpTeam',  wp_get_referer() )); 
        } else {
            $newId = TeamDAO::getInstance()->insertTeam($team); 
            wp_redirect( add_query_arg( 'message', 'succesNewTeam', 'admin.php?page=eventus&action=team&teamId='.$newId )); 
        }        
    }

    function deleteTeam(){
        if ($_POST['teamId']) {
            TeamDAO::getInstance()->deleteTeam($_POST['teamId']); 
            wp_redirect( add_query_arg( 'message', 'succesDelTeam', 'admin.php?page=eventus')); 
    	} else {
            TeamDAO::getInstance()->deleteTeam(null); 
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
        Database::getInstance()->resetTables();
        delete_option('eventus_mapapikey');
        wp_redirect( add_query_arg( 'message', 'succesReset',  'admin.php?page=eventus' ));
    }

    function updateSettings(){
        update_option('eventus_mapapikey', $_POST['mapApiKey'], false);
        wp_redirect( add_query_arg( 'message', 'succesUpSet',  wp_get_referer() ));
    }



?>