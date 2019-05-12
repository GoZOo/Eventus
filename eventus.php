<?php
/* Plugin Name: Eventus
 * Plugin URI: 
 * Description: Useful plugin that allow you to manage handball teams results through FFHB website.
 * Version: 2.5
 * Author: Kirian Caumes
 * Author URI: http://kiriancaumes.fr
 * License: 
 */
namespace Eventus;

include_once plugin_dir_path( __FILE__ ).'includes/entities/club.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/match.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/team.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/_masterDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/_database.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/teamDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/clubDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/matchDAO.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/_masterScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/main/mainScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/main/matchDetailScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/main/teamDetailScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/club/clubScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/club/clubDetailScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/log/logScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/admin/adminScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/finder.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/traitHelper.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/ics.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/postHandler.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/widgetDashboard.php';
include_once plugin_dir_path( __FILE__ ).'admin/librairies/simple_html_dom.php';

if (file_exists(get_template_directory().'/config-templatebuilder/avia-template-builder/php/shortcode-template.class.php')){
	include_once get_template_directory().'/config-templatebuilder/avia-template-builder/php/shortcode-template.class.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/aviaShortcode/calendar.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/aviaShortcode/match.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/aviaShortcode/circlePosPts.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/aviaShortcode/buttonResults.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/aviaShortcode/results.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/aviaShortcode/teamPicture.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/aviaShortcode/icsCalendar.php';	
}

class Eventus {	
    public function __construct() {	
		//Translations
		add_action('init', array($this, 'loadTranslation') );
	
		//Settings Link
		add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), array($this, 'settingsLink'));

		//Dashboard
		add_action('wp_dashboard_setup', array($this, 'dashboard'));

		//Style
		wp_enqueue_style( 'style', plugin_dir_url( __FILE__ ).'/admin/css/styles.css' ); 

    	//Menu
		add_action('admin_menu', array($this, 'menu'));	

		//Bdd
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        register_activation_hook( __FILE__, array($this, 'createTables'));
		register_deactivation_hook( __FILE__, array($this, 'deleteTables'));
		register_uninstall_hook( __FILE__, array($this, 'deleteTables'));

		//PostHandler
		Admin\Business\PostHandler::getInstance();
    }
    function menu() {
    	$icon = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(plugin_dir_path( __FILE__ ).'admin/svg/handball.svg'));
	    add_menu_page( __('Teams & Results', 'eventus').' - Eventus', 'Eventus', 'manage_options', 'eventus', array($this, 'callbackMain'), $icon);
	    add_submenu_page( 'eventus', __('Teams & Results', 'eventus').' - Eventus', __('Teams', 'eventus'), 'manage_options', 'eventus');
	    add_submenu_page( 'eventus', __('Clubs', 'eventus').' - Eventus', __('Clubs', 'eventus'), 'manage_options', 'eventus_club', array($this, 'callbackClubs'));
	    add_submenu_page( 'eventus', __('Logs', 'eventus').' - Eventus', __('Logs', 'eventus'), 'manage_options', 'eventus_logs', array($this, 'callbackLogs'));
	    add_submenu_page( 'eventus', __('Settings', 'eventus').' - Eventus', __('Settings', 'eventus'), 'manage_options', 'eventus_admin', array($this, 'callbackAdmin'));
	} 

	function dashboard() {
	    wp_add_dashboard_widget('dashboard_eventus',  'Eventus - '.__('Overview', 'eventus'), array($this, 'callbackDashboard'));  
	}

	function settingsLink( $links ) {
	    array_unshift( $links, '<a href="admin.php?page=eventus">' . __( 'Settings' ) . '</a>' );
	  	return $links;
	}

	function createTables() {
		Includes\Datas\Database::getInstance()->createTables();
	}

	function deleteTables() {
		Includes\Datas\Database::getInstance()->deleteTables();
	}	

	function callbackMain(){  
        Admin\Screens\Main\MainScreen::getInstance()->display(); 
	}

	function callbackClubs(){  
		Admin\Screens\Club\ClubScreen::getInstance()->display(); 
	}

	function callbackLogs(){  
        Admin\Screens\Log\LogScreen::getInstance()->display(); 
	}

	function callbackAdmin(){  
        Admin\Screens\Admin\AdminScreen::getInstance()->display(); 
	}	

	function callbackDashboard() { 
        Admin\Business\EventusWidgetDashboard::getInstance()->display(); 
	}
	
	function loadTranslation(){
		load_textdomain('eventus', plugin_dir_path( __FILE__ ).'lang/eventus-'.get_locale().'.mo' );
		load_plugin_textdomain('eventus', false, plugin_dir_path( __FILE__ ).'lang' ); 
	}
}
new Eventus();
?>