<?php

namespace Eventus\Admin\Controllers;
use Eventus\Includes\DAO as DAO;
use Eventus\Includes\DTO as Entities;

/**
* HomeController is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class HomeController extends MasterController {	
	public function __construct() {
		parent::__construct();

		if ($this->get['action'] == "team") {
			wp_enqueue_media();     
			wp_register_script('upImgJs', plugin_dir_url( __FILE__ ).'/../../views/js/uploadImg.js', '', '', true); 
			wp_localize_script('upImgJs', 'translations', 
				array(                
					'selectAnImg' => __('Select the default team image', 'eventus' ),
					'selectImg' => __('Use this image', 'eventus' )
				)
			);
			wp_enqueue_script('upImgJs');
			wp_enqueue_script('teamJs', plugin_dir_url( __FILE__ ).'/../../views/js/screens/teamDetailScreen.js', '', '', true); 
			
			$this->displayTeam();
		} else if($this->get['action'] == "matchs"){
			wp_enqueue_script('matchJs', plugin_dir_url( __FILE__ ).'/../../views/js/screens/matchDetailScreen.js', '', '', true); 

			$this->displayMatches();
		} else {
			$this->displayIndex();
		}
    }	

	private function displayTeam(){	
		if ($this->get['teamId']){  
			$team = DAO\TeamDAO::getInstance()->getTeamById($this->get['teamId']);
            if (!$team->getId()) return $this->render('error');            
    	} else {
            $team = new Entities\Team(null, "", "", "", 0, 0, 0, 0, 0, "", "", null);
		}
			
		$this->context['team'] = $team;
		$this->context['clubs'] = DAO\ClubDAO::getInstance()->getAllClubs();
        $this->render('team');	
	}

	private function displayIndex(){ 
		$allClubs = [];
		foreach (DAO\ClubDAO::getInstance()->getAllClubs() as $club) {
			$allTeams = [];
			foreach (DAO\TeamDAO::getInstance()->getAllTeamsByClubOrderByName($club) as $team) {
				array_push($allTeams, 
					[
						'infos' => DAO\TeamDAO::getInstance()->getInfosByTeamId($team->getId()), 
						'detail' => $team, 
						'img' => $team->getImg() ? wp_get_attachment_image_src($team->getImg(), 'medium')[0] : plugin_dir_url( __FILE__ ).'../../includes/img/img-default.png'
					]
				);			
			}	
			array_push($allClubs, [ 'detail' => $club, 'teams' => $allTeams]);		
		}		
		$this->context['clubs'] = $allClubs;
		$this->context['teams'] = DAO\TeamDAO::getInstance()->getAllTeams();
		$this->context['lastSync'] = get_option("eventus_datetimesynch");

        $this->render('home');		
	}

	
	private function displayMatches(){
		if (!$this->get['teamId']) return $this->render('error');

		$team = DAO\TeamDAO::getInstance()->getTeamById($this->get['teamId']);
		if (!$team->getId()) return $this->render('error');  
		
		$this->context['team'] = $team;
		$this->context['myMatchParent'] = DAO\MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(), 0); 
		$this->context['myMatchSon'] = DAO\MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(),1); 
		$myMatchOther = DAO\MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(),2);
		if (!$myMatchOther) $myMatchOther[] = new Entities\Match(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
		$this->context['myMatchOther'] = $myMatchOther; 

		$this->context['icons'] = [
			'edit' => file_get_contents(plugin_dir_path( __FILE__ ).'../views/svg/edit.svg'),
			'del' => file_get_contents(plugin_dir_path( __FILE__ ).'../views/svg/del.svg')
		];
        $this->render('matches');		
	}


}
?>