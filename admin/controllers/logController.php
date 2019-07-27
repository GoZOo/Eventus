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

		wp_enqueue_script('eventus_defaultScreen', plugin_dir_url( __FILE__ ).'/../../views/js/screens/_defaultScreen.js', '', '', true); 
        
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