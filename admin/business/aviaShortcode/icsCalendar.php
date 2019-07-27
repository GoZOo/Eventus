<?php
use Eventus\Includes\DAO as DAO;
use Eventus\Admin\Business\Helper as Helper;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusIcsCalender is a class use to add an element link in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/

if (!class_exists( 'EventusIcsCalender') && class_exists('aviaShortcodeTemplate')) {
	class EventusIcsCalender extends aviaShortcodeTemplate {
        use Helper\TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= "Ics calendrier";  
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-icon_box.png";
			$this->config['order']		= 94;
			$this->config['shortcode'] 	= 'eventus_button_results_ics';
			$this->config['tooltip'] 	= 'Affiche le lien vers le calendrier ICS';
		}
		
		function popup_elements() {
			$allTeamsDisplay = array();
			foreach (DAO\TeamDAO::getInstance()->getAllTeams() as $team) {
				$allTeamsDisplay[$team->getName()." ".$this->getSexLabel($team->getBoy(), $team->getGirl(), $team->getMixed())." - ".$team->getClub()->getName()] = $team->getId();
			}		
			
			$this->elements = array(
				 array(	
					"name" 	=> __('Team', 'eventus'),
					"desc" 	=> __('Select a team', 'eventus'),
					"id" 	=> "teamid",
					"type" 	=> "select",
					"std" 	=> $allTeamsDisplay ? reset($allTeamsDisplay) : '',
					"subtype" => $allTeamsDisplay
					), 	
			);

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
			wp_enqueue_script('eventusFront', WP_PLUGIN_URL.'/eventus/public/views/js/_eventusFront.js', '', '', true); 
			wp_enqueue_script('eventusFront_icsCalendar', WP_PLUGIN_URL.'/eventus/public/views/js/icsCalendar.js', '', '', true); 

	        extract(shortcode_atts(
	            array(
	                'teamid' => ''
	            ),
			$atts));
			$team = DAO\TeamDAO::getInstance()->getTeamById($teamid);

			\Timber\Timber::$locations = plugin_dir_path( __FILE__ ).'../../../public/views/screens/aviaComponents/';
			$this->context = \Timber\Timber::get_context();	
			$this->context['path'] = get_site_url().'/wp-content/plugins/eventus/public/ics/' . str_replace(' ', '_',$team->getClub()->getName()).'_'.str_replace(' ', '_',$team->getName()).'_'.str_replace(' ', '_',$team->getId()).'.ics';	

			return \Timber\Timber::fetch("icsCalendar.twig", $this->context);
	    }  
	}
}
