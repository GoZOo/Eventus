<?php
/* Plugin Name: Eventus
 * Plugin URI: 
 * Description: Useful plugin that allow you to manage handball teams results through FFHB website.
 * Version: 2.7
 * Author: Kirian Caumes
 * Author URI: https://github.com/KirianCaumes/Eventus
 * License: MIT
 */
namespace Eventus;

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

include_once plugin_dir_path(__FILE__) . 'includes/DTO/club.php';
include_once plugin_dir_path(__FILE__) . 'includes/DTO/match.php';
include_once plugin_dir_path(__FILE__) . 'includes/DTO/team.php';
include_once plugin_dir_path(__FILE__) . 'includes/DAO/_masterDAO.php';
include_once plugin_dir_path(__FILE__) . 'includes/DAO/_database.php';
include_once plugin_dir_path(__FILE__) . 'includes/DAO/teamDAO.php';
include_once plugin_dir_path(__FILE__) . 'includes/DAO/clubDAO.php';
include_once plugin_dir_path(__FILE__) . 'includes/DAO/matchDAO.php';
include_once plugin_dir_path(__FILE__) . 'admin/controllers/_masterController.php';
include_once plugin_dir_path(__FILE__) . 'admin/controllers/homeController.php';
include_once plugin_dir_path(__FILE__) . 'admin/controllers/clubController.php';
include_once plugin_dir_path(__FILE__) . 'admin/controllers/seekerController.php';
include_once plugin_dir_path(__FILE__) . 'admin/controllers/logController.php';
include_once plugin_dir_path(__FILE__) . 'admin/controllers/settingsController.php';
include_once plugin_dir_path(__FILE__) . 'admin/business/helper/traitHelper.php';
include_once plugin_dir_path(__FILE__) . 'admin/business/helper/staticHelper.php';
include_once plugin_dir_path(__FILE__) . 'admin/business/finder.php';
include_once plugin_dir_path(__FILE__) . 'admin/business/ics.php';
include_once plugin_dir_path(__FILE__) . 'admin/business/postHandler.php';
include_once plugin_dir_path(__FILE__) . 'admin/business/seeker.php';
include_once plugin_dir_path(__FILE__) . 'admin/business/widgetDashboard.php';
include_once plugin_dir_path(__FILE__) . 'admin/business/twig.php';

if (file_exists(get_template_directory() . '/config-templatebuilder/avia-template-builder/php/shortcode-template.class.php')) {
	include_once get_template_directory() . '/config-templatebuilder/avia-template-builder/php/shortcode-template.class.php';
	include_once plugin_dir_path(__FILE__) . 'admin/business/aviaShortcode/calendar.php';
	include_once plugin_dir_path(__FILE__) . 'admin/business/aviaShortcode/match.php';
	include_once plugin_dir_path(__FILE__) . 'admin/business/aviaShortcode/circlePosPts.php';
	include_once plugin_dir_path(__FILE__) . 'admin/business/aviaShortcode/buttonResults.php';
	include_once plugin_dir_path(__FILE__) . 'admin/business/aviaShortcode/results.php';
	include_once plugin_dir_path(__FILE__) . 'admin/business/aviaShortcode/teamPicture.php';
	include_once plugin_dir_path(__FILE__) . 'admin/business/aviaShortcode/icsCalendar.php';
}

class Eventus {
	public function __construct() {
		//Translations
		add_action('init', function () {
			load_textdomain('eventus', plugin_dir_path(__FILE__) . 'lang/eventus-' . get_locale() . '.mo');
			load_plugin_textdomain('eventus', false, plugin_dir_path(__FILE__) . 'lang');
		});

		//Settings Link
		add_filter("plugin_action_links_" . plugin_basename(__FILE__), function ($links) {
			array_unshift($links, '<a href="admin.php?page=eventus">' . __('Settings') . '</a>');
			return $links;
		});

		//Dashboard
		add_action('wp_dashboard_setup', function () {
			wp_add_dashboard_widget('dashboard_eventus',  'Eventus - ' . __('Overview', 'eventus'), function () {
				new Admin\Business\EventusWidgetDashboard();
			});
		});

		//Style
		add_action('admin_enqueue_scripts', function () {			
			wp_enqueue_style('eventus', plugin_dir_url(__FILE__) . 'admin/views/css/styles.css', array(), null, 'all');
		});
		// wp_enqueue_style('eventus', plugin_dir_url(__FILE__) . 'admin/views/css/styles.css', []);

		//Menu
		add_action('admin_menu', function () {
			$icon = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(plugin_dir_path(__FILE__) . 'admin/views/svg/handball.svg'));
			add_menu_page(__('Teams & Results', 'eventus') . ' - Eventus', 'Eventus', 'manage_options', 'eventus', function () {
				new Admin\Controllers\HomeController;
			}, $icon);
			add_submenu_page('eventus', __('Teams & Results', 'eventus') . ' - Eventus', __('Teams', 'eventus'), 'manage_options', 'eventus');
			add_submenu_page('eventus', __('Clubs', 'eventus') . ' - Eventus', __('Clubs', 'eventus'), 'manage_options', 'eventus_club', function () {
				new Admin\Controllers\ClubController;
			});
			add_submenu_page('eventus', __('Logs', 'eventus') . ' - Eventus', __('Logs', 'eventus'), 'manage_options', 'eventus_logs', function () {
				new Admin\Controllers\LogController;
			});	
			add_submenu_page('eventus', __('Seeker', 'eventus') . ' - Eventus', __('Seeker', 'eventus'), 'manage_options', 'eventus_seeker', function () {
				new Admin\Controllers\SeekerController;
			});
			add_submenu_page('eventus', __('Settings', 'eventus') . ' - Eventus', __('Settings', 'eventus'), 'manage_options', 'eventus_admin', function () {
				new Admin\Controllers\SettingsController;
			});		
		});

		//Bdd
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		register_activation_hook(__FILE__, function () {
			update_option('eventus_season', (date('n') < 9 ? date("Y")-1 . " - " . (date("Y")) : date("Y") . " - " . (date("Y") + 1)), false);
        	update_option('eventus_rdvTime', 45, false);
			Includes\DAO\Database::getInstance()->createTables();
		});
		register_deactivation_hook(__FILE__, array($this, 'uninstall'));
		register_uninstall_hook(__FILE__, 'uninstall');

		//PostHandler
		Admin\Business\PostHandler::getInstance();

		//Twig settings		
		new Admin\Business\Twig;
		
		//Js settings to enable import/export
		add_filter( 'script_loader_tag', function ( $tag, $handle, $source ) {
			$scirpts = array("eventus", "eventus_defaultScreen", "eventus_adminScreen","eventus_seekedScreen","eventus_seekerScreen", "eventus_teamScreen","eventus_matchScreen", 
			"eventusFront", "eventusFront_icsCalendar");	
			if (in_array($handle, $scirpts)) {
				$tag = '<script src="' . $source . '" type="module"></script>';
			}
			return $tag;
		}, 10, 3 );
		
	}

	function uninstall() {
		Includes\DAO\Database::getInstance()->deleteTables();
	}	
}

new Eventus();
