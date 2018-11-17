<?php
/* Plugin Name: Eventus
 * Plugin URI: 
 * Description: Useful plugin that allow you to manage handball teams results through FFHB website.
 * Version: 2.4
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
include_once plugin_dir_path( __FILE__ ).'admin/business/postHandler.php';
include_once plugin_dir_path( __FILE__ ).'admin/librairies/simple_html_dom.php';

if (file_exists(get_template_directory().'/config-templatebuilder/avia-template-builder/php/shortcode-template.class.php')){
	include_once get_template_directory().'/config-templatebuilder/avia-template-builder/php/shortcode-template.class.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/shortcode/eventusCalendrier.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/shortcode/eventusMatch.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/shortcode/eventusCirclePosPts.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/shortcode/eventusButtonResults.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/shortcode/eventusResults.php';
	include_once plugin_dir_path( __FILE__ ).'admin/business/shortcode/eventusTeamPicture.php';
}

class Eventus {	
    public function __construct() {	
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
	    add_menu_page( 'Équipes & Résultats - Eventus', 'Eventus', 'manage_options', 'eventus', array($this, 'callbackMain'), $icon);
	    add_submenu_page( 'eventus', 'Équipes & Résultats - Eventus', 'Equipes', 'manage_options', 'eventus');
	    add_submenu_page( 'eventus', 'Clubs - Eventus', 'Clubs', 'manage_options', 'eventus_club', array($this, 'callbackClubs'));
	    add_submenu_page( 'eventus', 'Logs - Eventus', 'Logs', 'manage_options', 'eventus_logs', array($this, 'callbackLogs'));
	    add_submenu_page( 'eventus', 'Paramètres - Eventus', 'Paramètres', 'manage_options', 'eventus_admin', array($this, 'callbackAdmin'));
	} 

	function dashboard() {
	    wp_add_dashboard_widget('dashboard_eventus', 'Eventus : erreur(s)', array($this, 'callbackDashboard'));  
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
	    if (isset($_POST['clearLog'])){
	        file_put_contents(plugin_dir_path( __FILE__ ).'finder.log', '');
		}
		$content = file_get_contents(plugin_dir_path( __FILE__ ).'finder.log');
	    if ($content) {
	    	echo nl2br(htmlspecialchars($content));
	    } else {
	    	echo "Aucun log à afficher";
	    }	    
	    ?>
	    <form action="" method="post">
	        <button style="margin-top: 10px;" name="clearLog" class="button-primary" >Effacer les logs</button>
	    </form>
	<?php	    
	}
}
new Eventus();
?>