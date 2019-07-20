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
        $seeker = new Business\Seeker();
        $this->context['champ'] = $seeker->getChampionship();        
        $this->context['clubs'] = DAO\ClubDAO::getInstance()->getAllClubs();

        // var_dump($seeker->seek('C44',"thouare"));

        $this->render('seeker');
    }

    private function displayRes(){
        var_dump(json_decode(stripslashes($_GET['seeked']), JSON_UNESCAPED_SLASHES));

        // $this->render('seeked');
    }
}

?>