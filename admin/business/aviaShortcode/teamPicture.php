<?php

use Eventus\Includes\DAO as DAO;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusCirclePosPts is a class use add an element image in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/
if (!class_exists( 'EventusTeamPicture') && class_exists('aviaShortcodeTemplate')) {
	class EventusTeamPicture extends aviaShortcodeTemplate {
        use TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= __('Photo Team', 'eventus');
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-image.png";
			$this->config['order']		= 95;
			$this->config['shortcode'] 	= 'eventus_team_picture';
			$this->config['tooltip'] 	= __('Photo of a Team', 'eventus');
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
			
			global $_wp_additional_image_sizes;
			
			$this->elements = array(
				array(	
					"name" 	=> __('Image format', 'eventus'),
					"desc" 	=> __('Select a format', 'eventus'),
					"id" 	=> "format",
					"type" 	=> "select",
					"std" 	=> "0",
					"subtype" => array(
									"Extra large (1500x1500) - Recommended"=>'extra_large',
									"Orignal" => 'full',
									"Widget (36x36)"=>'widget',
									"Square (180x180)"=>'square',
									"Featured (1500x430)"=>'featured',
									"Featured large (1500x630)"=>'featured_large',
									"Portfolio (495x400)"=>'portfolio',
									"Portfolio small (260x185)"=>'portfolio_small',
									"Gallery (845x684)"=>'gallery',
									"Magazine (710x375)"=>'magazine',
									"Masonry (705x705)"=>'masonry',
									"Entry with sidebar (845x321)"=>'entry_with_sidebar',
									"Entry without_sidebar (1210x423)"=>'entry_without_sidebar',
									"Shop thumbnail (120x120)"=>'shop_thumbnail',
									"Shop catalog (450x450)"=>'shop_catalog',
									"Shop single (450x999)"=>'shop_single'
								)
					),					
				 array(	
					"name" 	=> __('Team', 'eventus'),
					"desc" 	=> __('Select a team', 'eventus'),
					"id" 	=> "teamid",
					"type" 	=> "select",
					"std" 	=> $allTeamsDisplay ? reset($allTeamsDisplay) : '',
					"subtype" => $allTeamsDisplay
					)	
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
	                'teamid' => '',
	                'format' => ''
	            ),
			$atts));
			$myTeam = DAO\TeamDAO::getInstance()->getTeamById($teamid);
			if (!$myTeam->getImg()) {
				return do_shortcode("[av_image src='".plugin_dir_url( __FILE__ ).'../../../includes/img/img-default.png'."' attachment='' attachment_size='' align='center' styling='' hover='' link='' target='' caption='' font_size='' appearance='' overlay_opacity='0.4' overlay_color='#000000' overlay_text_color='#ffffff' copyright='' animation='no-animation' av_uid='' custom_class='' admin_preview_bg=''][/av_image]"); 
			}
		    return do_shortcode("[av_image src='".wp_get_attachment_image_src($myTeam->getImg(), $format)[0]."' attachment='".$myTeam->getImg()."' attachment_size='".$format."' align='center' styling='' hover='' link='' target='' caption='' font_size='' appearance='' overlay_opacity='0.4' overlay_color='#000000' overlay_text_color='#ffffff' copyright='' animation='no-animation' av_uid='' custom_class='' admin_preview_bg=''][/av_image]"); 
	    }
	}
}
