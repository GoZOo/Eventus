<?php

use Eventus\Includes\DAO as DAO;
use Eventus\Admin\Business\Helper as Helper;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusCirclePosPts is a class use add an element in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/
if (!class_exists( 'EventusResults') && class_exists('aviaShortcodeTemplate')) {
	class EventusResults extends aviaShortcodeTemplate {
        use Helper\TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= __('Results List', 'eventus');
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-accordion.png";
			$this->config['order']		= 95;
			$this->config['shortcode'] 	= 'eventus_results';
			$this->config['tooltip'] 	= __('Display the results', 'eventus');
		}
		
		function popup_elements() {
			
		}		
		 
		
		/**
		 * Frontend Shortcode Handler
		 *
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element 
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @return string $output returns the modified html string 
		 */
		function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "") {	
			wp_enqueue_script('scriptEventusResults', WP_PLUGIN_URL.'/eventus/public/views/js/eventusResults.js'); 
			wp_enqueue_style('cssEventusResults', WP_PLUGIN_URL.'/eventus/public/views/css/eventusResults.css');
			$allClubs = DAO\ClubDAO::getInstance()->getAllClubs();
			
			\Timber\Timber::$locations = plugin_dir_path( __FILE__ ).'../../../public/views/screens/aviaComponents/';
			$this->context = \Timber\Timber::get_context();	
			
			$clubsTemp = array();

	        foreach ($allClubs as $club) {
				$clubTemp = array(
					'club'=> $club->getName(),
					'allTeams' => array()
				);
				$allTeams = DAO\TeamDAO::getInstance()->getAllTeamsByClubOrderBySex($club);
				$teamsTemp = array();
				foreach ($allTeams as $team) {
					array_push($teamsTemp, array(
						'team' => $team,
						'sex' => $this->getSexLabel($team->getBoy(), $team->getGirl(), $team->getMixed()),
						'match'=> DAO\MatchDAO::getInstance()->getCloseMatchByTeamId($team->getId(), "last")
					));
				}
				$clubTemp['allTeams'] = $teamsTemp;
				array_push($clubsTemp, $clubTemp);
			}

			$this->context['allClubs'] = $clubsTemp;

			return \Timber\Timber::fetch("results.twig", $this->context);
	    }  
	}
}
