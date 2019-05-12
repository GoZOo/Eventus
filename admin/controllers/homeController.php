<?php

namespace Eventus\Admin\Controllers;
use Eventus\Includes\Datas as DAO;

/**
* HomeController is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class HomeController extends MasterController {	
	public function __construct() {
		parent::__construct();

		if (isset($_GET['action']) && $_GET['action']=="team") {
			wp_enqueue_media();     
			wp_register_script('upImgJs', plugin_dir_url( __FILE__ ).'/../../../js/uploadImg.js', '', '', true); 
			wp_localize_script('upImgJs', 'translations', 
				array(                
					'selectAnImg' => __('Select the default team image', 'eventus' ),
					'selectImg' => __('Use this image', 'eventus' )
				)
			);
			wp_enqueue_script('upImgJs');
			wp_enqueue_script('teamJs', plugin_dir_url( __FILE__ ).'/../../../js/screens/teamDetailScreen.js', '', '', true); 
			$this->displayTeam();
		} else if(isset($_GET['action']) && $_GET['action']=="matchs"){
			$this->displayMatches();
		} else {
			$this->displayIndex();
		}
    }	

	private function displayTeam(){	
		if (isset($_GET['teamId'])){  
            $team = DAO\TeamDAO::getInstance()->getTeamById($_GET['teamId']);
            if (!$team->getId()) {
                echo "<h2>Erreur : L'équipe n'a pas pu être trouvée...</h2>";
                return;
            }            
    	} else {
            $team = new Entities\Team(null, "", "", "", 0, 0, 0, 0, 0, "", "", null);
		}
			
		$this->$context['team'] = $team;
		$this->$context['clubs'] = DAO\ClubDAO::getInstance()->getAllClubs();
        $this->render('team.twig');	
	}

	private function displayMatches(){
		
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
		$this->$context['clubs'] = $allClubs;
		$this->$context['teams'] = DAO\TeamDAO::getInstance()->getAllTeams();
		$this->$context['lastSync'] = get_option("eventus_datetimesynch");

        $this->render('home.twig');		
	}
}
?>