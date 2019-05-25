<?php

use Eventus\Includes\DAO as DAO;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusCalendrier is a class use add elements table in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/
if (!class_exists( 'EventusCalendrier') && class_exists('aviaShortcodeTemplate')) {
	class EventusCalendrier extends aviaShortcodeTemplate {
        use TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= __('Match Calendar', 'eventus'); 
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-table.png";
			$this->config['order']		= 96;
			$this->config['shortcode'] 	= 'eventus_calendrier';
			$this->config['tooltip'] 	= __('Match Calendar', 'eventus');
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
	        $myMatches = DAO\MatchDAO::getInstance()->getMatchesWithDate(); 
			if ($myMatches) {
				$arrays = "";
				foreach ($myMatches as $key => $match) {
					$ext = $match->getExt() == 0 ? "DOM" : "EXT";
					$domTeam = $match->getLocalTeam();
					$extTeam = $match->getVisitingTeam();
					if ($match->getExt()) {
						$temp = $domTeam;
						$domTeam = $extTeam;
						$extTeam = $temp;
					}
					$domTeam = $match->getTeam()->getName()." ".$this->getSexLabel($match->getTeam()->getBoy(), $match->getTeam()->getGirl(), $match->getTeam()->getMixed());
					
					if($key == 0){
						$arrays .= "[av_heading heading='".$this->toFrenchDate(date_create_from_format('Y-m-d', $match->getDate())->format('l d F Y'))."' tag='h2' style='' size='' subheading_active='' subheading_size='15' margin='' margin_sync='true' padding='10' color='' custom_font='' av-medium-font-size-title='' av-small-font-size-title='' av-mini-font-size-title='' av-medium-font-size='' av-small-font-size='' av-mini-font-size='' av_uid='av-jl3st843' custom_class='' admin_preview_bg=''][/av_heading]
						[av_table purpose='tabular' pricing_table_design='avia_pricing_default' pricing_hidden_cells='' caption='' responsive_styling='avia_responsive_table' av_uid='' custom_class='']
						[av_row row_style=''][av_cell col_style='']Catégorie[/av_cell][av_cell col_style='']Lieu[/av_cell][av_cell col_style='']Rencontre[/av_cell][av_cell col_style='']RDV[/av_cell][av_cell col_style='']Match[/av_cell][/av_row]";
					} else if ($key > 0 && ($myMatches[$key]->getDate() !== $myMatches[$key-1]->getDate())) {                              
						$arrays .= "[/av_table][av_heading heading='".$this->toFrenchDate(date_create_from_format('Y-m-d', $match->getDate())->format('l d F Y'))."' tag='h2' style='' size='' subheading_active='' subheading_size='15' margin='' margin_sync='true' padding='10' color='' custom_font='' av-medium-font-size-title='' av-small-font-size-title='' av-mini-font-size-title='' av-medium-font-size='' av-small-font-size='' av-mini-font-size='' av_uid='av-jl3st843' custom_class='' admin_preview_bg=''][/av_heading]
						[av_table purpose='tabular' pricing_table_design='avia_pricing_default' pricing_hidden_cells='' caption='' responsive_styling='avia_responsive_table' av_uid='' custom_class='']
						[av_row row_style=''][av_cell col_style='']Catégorie[/av_cell][av_cell col_style='']Lieu[/av_cell][av_cell col_style='']Rencontre[/av_cell][av_cell col_style='']RDV[/av_cell][av_cell col_style='']Match[/av_cell][/av_row]";
					}
					if ($key > 0 && $myMatches[$key]->getDate() == $myMatches[$key-1]->getDate() && $myMatches[$key]->getTeam()->getId() == $myMatches[$key-1]->getTeam()->getId()) {
						$hourRDV = "";
					} else {
						$hourRDV = $match->getHourRDV();
					}  
					$arrays .= "[av_row row_style=''][av_cell col_style='']".$domTeam."[/av_cell][av_cell col_style='']".$ext."[/av_cell][av_cell col_style='']".$extTeam."[/av_cell][av_cell col_style='']".$hourRDV."[/av_cell][av_cell col_style='']".$match->getHourStart()."[/av_cell][/av_row]";
	
					if ($key == sizeof($myMatches)-1) {
						$arrays .= "[/av_table]";
					}
				}
	
				return do_shortcode($arrays);
			} else {
				return do_shortcode("[av_heading heading='Aucune match trouvé' tag='h2' style='' size='' subheading_active='' subheading_size='15' margin='' margin_sync='true' padding='10' color='' custom_font='' av-medium-font-size-title='' av-small-font-size-title='' av-mini-font-size-title='' av-medium-font-size='' av-small-font-size='' av-mini-font-size='' av_uid='av-jl3st843' custom_class='' admin_preview_bg=''][/av_heading]");
			}
	    }
					
			
	}
}
