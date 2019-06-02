<?php
use Eventus\Includes\DAO as DAO;
use Eventus\Admin\Business\Helper as Helper;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusButtonResults is a class use add an element button in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/

if (!class_exists( 'EventusButtonResults') && class_exists('aviaShortcodeTemplate')) {
	class EventusButtonResults extends aviaShortcodeTemplate {
        use Helper\TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= __('Results button', 'eventus');  
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-buttonrow.png";
			$this->config['order']		= 97;
			$this->config['shortcode'] 	= 'eventus_button_results';
			$this->config['tooltip'] 	= __('Display a button containing the results links', 'eventus');
		}
		
		function popup_elements() {
			$font_size_array = array( 
				__("Default Size", 'avia_framework' ) => '',
				__("Flexible font size (adjusts to screen width)" , 'avia_framework') => AviaHtmlHelper::number_array(3,7,0.5 , array(), "vw", "", "vw"),
				__("Fixed font size" , 'avia_framework') => AviaHtmlHelper::number_array(11,150,1, array(), "px", "", "")
			);	
	
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

	        extract(shortcode_atts(
	            array(
	                'teamid' => ''
	            ),
	        $atts));
	        
			$myTeam = DAO\TeamDAO::getInstance()->getTeamById($teamid); 
			
			\Timber\Timber::$locations = plugin_dir_path( __FILE__ ).'../../../public/views/screens/aviaComponents/';
			$this->context = \Timber\Timber::get_context();	
			$this->context['url_one'] = str_replace("]", "&#93;", str_replace("[", "&#91;", $myTeam->getUrlOne()));
			$this->context['url_two'] = str_replace("]", "&#93;", str_replace("[", "&#91;", $myTeam->getUrlTwo()));

			return \Timber\Timber::fetch("buttonResults.twig", $this->context);
	    }  
	}
}
