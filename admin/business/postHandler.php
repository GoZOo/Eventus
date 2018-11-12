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
                setUpdateMatch();
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
    		MatchDAO::getInstance()->deleteMatches();
            Database::getInstance()->resetAutoIncr('matches');
    	}
		wp_redirect( add_query_arg( 'message', 'succesDelMatch',  wp_get_referer() ));
    }

    function updateMatch(){
    	setUpdateMatch();
		wp_redirect( add_query_arg( 'message', 'succesUpMatch',  wp_get_referer() ));
    }
    
    function updateHoursMatch(){
        if (get_option("eventus_mapapikey")) {
            setUpdateMatch();
            MatchDAO::getInstance()->updateMatchesHours(Finder::setNewHoursRdv(MatchDAO::getInstance()->getAllMatchesByTeamId($_POST['teamId'])));
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
    	$myTeam = TeamDAO::getInstance()->getTeamById($_POST['teamId']);
       	$allMatchesSon = [];
        for($i=1; $i < $_POST['nbrSonMatch']+1; $i++) { 
            $allMatchesSon[] = new Match(
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
            	$_POST['matchChampSon'.$i] ? $_POST['matchChampSon'.$i] : null,
            	$_POST['teamId'],
            	$_POST['idMatchRefSon'.$i] ? $_POST['idMatchRefSon'.$i] : null
            );
        }	
        MatchDAO::getInstance()->updateMatchesScreen($allMatchesSon, 1, TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId()); 

       	$allMatchesOther = [];
        for($i=1; $i < $_POST['nbrOtherMatch']+1; $i++) { 
            $allMatchesOther[] = new Match(
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
            	null,
            	$_POST['teamId'],
            	null
            );
        }
        MatchDAO::getInstance()->updateMatchesScreen($allMatchesOther, 2, TeamDAO::getInstance()->getTeamById($_POST['teamId'])->getId());  
    }
    
    /**************************
    ********** Club ***********
    ***************************/
    function updateClub(){
        if ($_POST['clubId']) {
            $club = ClubDAO::getInstance()->getClubById($_POST['clubId']);
        } else {
            $club = new Club(null, "", "", "");
        }        
        $club->setName(($_POST['nom'] ? $_POST['nom'] : ""));
        $club->setString(($_POST['chaine'] ? $_POST['chaine'] : ""));
        $club->setAddress(($_POST['adresse'] ? $_POST['adresse'] : null));

        if($club->getName() && $club->getString() && $club->getAddress()){
            if ($club->getId()) {
                ClubDAO::getInstance()->updateClub($club); 
                wp_redirect( add_query_arg( 'message', 'succesUpClub',  wp_get_referer() )); 
            } else {
                $newId = ClubDAO::getInstance()->insertClub($club); 
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
            ClubDAO::getInstance()->deleteClub($_POST['clubId']); 
    	} else {
            ClubDAO::getInstance()->deleteClub(); 
            Database::getInstance()->resetAutoIncr('clubs');
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
        $team->setClub(ClubDAO::getInstance()->getClubById(($_POST['club'] ? $_POST['club'] : null)));
            
        
        if($team->getName() && $team->getClub()){
            if ($team->getId()) {
                TeamDAO::getInstance()->updateTeam($team); 
                wp_redirect( add_query_arg( 'message', 'succesUpTeam',  wp_get_referer() )); 
            } else {
                $newId = TeamDAO::getInstance()->insertTeam($team); 
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
            TeamDAO::getInstance()->deleteTeam($_POST['teamId']); 
            wp_redirect( add_query_arg( 'message', 'succesDelTeam', 'admin.php?page=eventus')); 
    	} else {
            TeamDAO::getInstance()->deleteTeam(); 
            Database::getInstance()->resetAutoIncr('teams');
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