<?php

namespace Eventus\Admin\Controllers;

/**
* AdminScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/

class SettingsController extends MasterController {
    public function __construct() {
		parent::__construct();
        wp_enqueue_script('matchJs', plugin_dir_url( __FILE__ ).'/../../views/js/screens/adminScreen.js', '', '', true); 

        $this->displayIndex();
    }	

    private function displayIndex(){
        $this->context['options'] = [ 
            'apikey' => get_option("eventus_mapapikey"),
            'emailnotif' => get_option("eventus_emailnotif"),
            'resetlog' => get_option("eventus_resetlog"),
            'season' => get_option("eventus_season")
        ];
        $this->context['pluginData'] = get_plugin_data(WP_PLUGIN_DIR.'/eventus/eventus.php');

        $this->render('settings');
    }
}

?>