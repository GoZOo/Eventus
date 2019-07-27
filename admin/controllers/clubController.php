<?php

namespace Eventus\Admin\Controllers;
use Eventus\Includes\DAO as DAO;
use Eventus\Includes\DTO as Entities;

/**
* ClubScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class ClubController extends MasterController {
    public function __construct() {  
        parent::__construct();
        
		if ($this->get['action'] == "club") {
            wp_enqueue_media();    
            wp_register_script('upImgJs', plugin_dir_url( __FILE__ ).'/../../views/js/uploadImg.js', '', '', true); 
            wp_localize_script('upImgJs', 'translations', $this->translationsJs);
            wp_enqueue_script('upImgJs');
			wp_enqueue_script('eventus_defaultScreen', plugin_dir_url( __FILE__ ).'/../../views/js/screens/_defaultScreen.js', '', '', true); 

			$this->displayClub();
		} else {
			wp_enqueue_script('eventus_defaultScreen', plugin_dir_url( __FILE__ ).'/../../views/js/screens/_defaultScreen.js', '', '', true); 
            
            $this->displayIndex();
        }	
    }

    function displayIndex(){
        $clubs = DAO\ClubDAO::getInstance()->getAllClubs();
        $allClubs = [];
        foreach ($clubs as $club) {
            $allClubs[] = [
                "details" => $club,
                "infos" => DAO\ClubDAO::getInstance()->getInfosByClubId($club->getId()),
                'img' => $club->getImg() ? wp_get_attachment_image_src($club->getImg(), 'medium')[0] : plugin_dir_url( __FILE__ ).'../../includes/img/img-default.png'
            ];
        }
        $this->context['allClubs'] = $allClubs;
        $this->render('clubs'); 
    }

    function displayClub(){
        if ($this->get['clubId']){  
            $club = DAO\ClubDAO::getInstance()->getClubById($this->get['clubId']);
            if (!$club->getId()) return $this->render('error');            
    	} else {
            $club = new Entities\Club(null, "", "", "", null, "");
        }

        $this->context['club'] = $club;

        $this->render('club'); 
    }
    
    

}
?>