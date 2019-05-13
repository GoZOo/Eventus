<?php

use Eventus\Includes\DAO as DAO;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusCirclePosPts is a class use add an element text in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/
if (!class_exists( 'EventusCirclePosPts') && class_exists('aviaShortcodeTemplate')) {
	class EventusCirclePosPts extends aviaShortcodeTemplate {
        use TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= __('Position and Score', 'eventus'); 
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-icon.png";
			$this->config['order']		= 98;
			$this->config['shortcode'] 	= 'eventus_circle_pos_score';
			$this->config['tooltip'] 	= __('Display position and score', 'eventus');
		}
		
		function popup_elements() {
			$font_size_array = array( 
				__("Default Size", 'avia_framework' ) => '',
				__("Flexible font size (adjusts to screen width)" , 'avia_framework') => AviaHtmlHelper::number_array(3,7,0.5 , array(), "vw", "", "vw"),
				__("Fixed font size" , 'avia_framework') => AviaHtmlHelper::number_array(11,150,1, array(), "px", "", "")
			);	

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

	        return do_shortcode("[av_textblock size='' font_color='' color='' av-medium-font-size='' av-small-font-size='' av-mini-font-size='' av_uid='' admin_preview_bg='']<p style='text-align: center;'><span style='font-size: 28px;'>".$myTeam->getPosition()."e</span><br>".$myTeam->getPoints()." pts</p>[/av_textblock]");
	    }  
	}
}
