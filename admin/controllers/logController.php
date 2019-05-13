<?php

namespace Eventus\Admin\Controllers;

/**
* LogScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class LogController extends MasterController {
    public function __construct() {
        parent::__construct();
        
        $this->displayIndex();
	}	    
    
    private function displayIndex(){
        $content = @file_get_contents(plugin_dir_path( __FILE__ ).'../../finder.log');
        if ($content) {
            $content = str_replace("]", "]</b>", $content);
            $content = str_replace("[", "<b>[", $content);
            $content = str_replace( "\n", '</li><li>', $content); 
        }
        $this->context['content'] = $content;

        $this->render('log');
    }
}

?>