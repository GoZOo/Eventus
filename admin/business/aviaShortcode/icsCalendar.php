<?php
use Eventus\Includes\Datas as DAO;

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
        use TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= "Ics calendrier";  
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-icon_box.png";
			$this->config['order']		= 94;
			$this->config['shortcode'] 	= 'eventus_button_results';
			$this->config['tooltip'] 	= 'Affiche le lien vers le calendrier ICS';
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
			$team = DAO\TeamDAO::getInstance()->getTeamById($teamid);
			$js = "<script>
				window.onload = () => {
					document.getElementsByClassName('rowIcs')[0].getElementsByTagName('a')[1].removeAttribute('href')
					document.getElementsByClassName('rowIcs')[0].getElementsByTagName('a')[1].style.cursor = 'pointer'
					document.getElementsByClassName('rowIcs')[0].getElementsByTagName('a')[1].addEventListener('click', 
						() => {
							let dummy = document.createElement('input');
							document.body.appendChild(dummy);
							dummy.setAttribute('value', '".get_site_url().'/wp-content/plugins/eventus/public/ics/' . $team->getClub()->getName().'_'.$team->getName().'_'.$team->getId().'.ics'."');
							dummy.select();
							document.execCommand('copy');
							document.body.removeChild(dummy);
						}
					);
				}				
			</script>";
			
			$sc = 
				"[av_buttonrow alignment='center' button_spacing='5' button_spacing_unit='px' av_uid='av-r75dw' custom_class='' admin_preview_bg='' custom_class='rowIcs']
				 [av_buttonrow_item label='".__('Download the calendar', 'eventus')."' link='manually,".get_site_url().'/wp-content/plugins/eventus/public/ics/' . $team->getClub()->getName().'_'.$team->getName().'_'.$team->getId().'.ics'."' link_target='' size='large' label_display='' icon_select='yes-right-icon' icon_hover='aviaTBaviaTBicon_hover' icon='ue82d' font='entypo-fontello' color='theme-color' custom_bg='#444444' custom_font='#ffffff']
				 [av_buttonrow_item label='".__('Copy the link of the calendar', 'eventus')."' size='large' label_display='' icon_select='yes-right-icon' icon_hover='aviaTBaviaTBicon_hover' icon='ue822' font='entypo-fontello' color='theme-color' custom_bg='#444444' custom_font='#ffffff']
				 [/av_buttonrow]";
			
			return $js . do_shortcode($sc);
	    }  
	}
}
