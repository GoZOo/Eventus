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
        
		if (isset($_GET['action']) && $_GET['action']=="club") {
            wp_enqueue_media();    
            wp_register_script('upImgJs', plugin_dir_url( __FILE__ ).'/../../js/uploadImg.js', '', '', true); 
            wp_localize_script('upImgJs', 'translations', 
                array(                
                    'selectAnImg' => __('Select the default team image', 'eventus' ),
                    'selectImg' => __('Use this image', 'eventus' )
                )
            );
            wp_enqueue_script('upImgJs');

			$this->displayClub();
		} else {
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
        $club = DAO\ClubDAO::getInstance()->getClubById($_GET['clubId']);
        if (!$_GET['clubId']) $club = new Entities\Club(null, "", "", "");

        $this->context['club'] = $club;
        $this->context['isNew'] = $_GET['clubId'] ? false : true;

        $this->render('club'); 
    }
    
    

}
?>