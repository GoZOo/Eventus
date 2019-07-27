<?php

namespace Eventus\Admin\Controllers;

use Eventus\Admin\Business as Business;
use Eventus\Includes\DAO as DAO;

/**
* SeekerController is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/

class SeekerController extends MasterController {
    public function __construct() {
		parent::__construct();

        if ($this->get['seeked']) {
			wp_enqueue_script('eventus_seekedScreen', plugin_dir_url( __FILE__ ).'/../../views/js/screens/seekedScreen.js', '', '', true); 
            
            $this->displayRes();
        } else {
			wp_enqueue_script('eventus_seekerScreen', plugin_dir_url( __FILE__ ).'/../../views/js/screens/seekerScreen.js', '', '', true); 

            $this->displayIndex();
        }        
    }	

    private function displayIndex(){   
        $this->context['clubs'] = DAO\ClubDAO::getInstance()->getAllClubs();
        if(!!$this->context['clubs']) $this->context['champ'] = Business\Seeker::getInstance()->getChampionship();     

        $this->render('seeker');
    }

    private function displayRes(){
        if ($this->get['clubId']){  
            $club = DAO\ClubDAO::getInstance()->getClubById($this->get['clubId']);
            if (!$club->getId()) return $this->render('error');   
    	} else {
            return $this->render('error');
        }
            
        $this->context['club'] = $club;
        $this->context['seeked'] = json_decode(stripslashes($this->get['seeked']), JSON_UNESCAPED_SLASHES);
        $this->context['error'] = $this->get['err'];

        $this->render('seeked');
    }
}

?>