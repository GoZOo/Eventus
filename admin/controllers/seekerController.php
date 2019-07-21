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
            $this->displayRes();
        } else {
            $this->displayIndex();
        }        
    }	

    private function displayIndex(){
        $this->context['champ'] = Business\Seeker::getInstance()->getChampionship();        
        $this->context['clubs'] = DAO\ClubDAO::getInstance()->getAllClubs();

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
        $this->context['seeked'] = json_decode(stripslashes($_GET['seeked']), JSON_UNESCAPED_SLASHES);

        $this->render('seeked');
    }
}

?>