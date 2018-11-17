<?php
use Eventus\Includes\Datas as DAO;

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
        use TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= "Bouton Résultats";
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-buttonrow.png";
			$this->config['order']		= 97;
			$this->config['shortcode'] 	= 'eventus_button_results';
			$this->config['tooltip'] 	= "Afficher un bouton contenant le liens des résultats";
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
					"name" 	=> "Équipe",
					"desc" 	=> "Sélectionnez une équipe",
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
			// $myUrlOne = $myTeam->getUrlOne();
			// $myUrlOne = str_replace("[", "&#91;", $myUrlOne);
			// $myUrlOne = str_replace("]", "&#93;", $myUrlOne);
			// $myUrlTwo = $myTeam->getUrlTwo();
			// $myUrlTwo = str_replace("[", "&#91;", $myUrlTwo);
			// $myUrlTwo = str_replace("]", "&#93;", $myUrlTwo);
			
			$sc = "[av_buttonrow alignment='center' button_spacing='5' button_spacing_unit='px' av_uid='av-r75dw' custom_class='' admin_preview_bg='']";

			$myTeam->getUrlOne() ? $sc .= "[av_buttonrow_item label='Voir l’ensemble des résultats' link='manually,".str_replace("]", "&#93;", str_replace("[", "&#91;", $myTeam->getUrlOne()))."' link_target='_blank' size='large' label_display='' icon_select='yes-right-icon' icon_hover='aviaTBaviaTBicon_hover' icon='ue889' font='entypo-fontello' color='theme-color' custom_bg='#444444' custom_font='#ffffff']" : '';	

			$myTeam->getUrlTwo() ? $sc .= "[av_buttonrow_item label='Voir l’ensemble des résultats' link='manually,".str_replace("]", "&#93;", str_replace("[", "&#91;", $myTeam->getUrlTwo()))."' link_target='_blank' size='large' label_display='' icon_select='yes-right-icon' icon_hover='aviaTBaviaTBicon_hover' icon='ue889' font='entypo-fontello' color='theme-color' custom_bg='#444444' custom_font='#ffffff']" : '';

			$sc .= "[/av_buttonrow]";

			return do_shortcode($sc);
	    }  
	}
}
