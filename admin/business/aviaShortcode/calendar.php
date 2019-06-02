<?php

use Eventus\Includes\DAO as DAO;
use Eventus\Admin\Business\Helper as Helper;

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
        use Helper\TraitHelper;

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
			$datas = array();
			if ($myMatches) {
				$content = array();
				foreach ($myMatches as $key => $match) {
					$domTeam = $match->getLocalTeam();
					$extTeam = $match->getVisitingTeam();
					if ($match->getExt()) {
						$temp = $domTeam;
						$domTeam = $extTeam;
						$extTeam = $temp;
					}

					$newDate = array(
						'date'=> $this->toFrenchDate(date_create_from_format('Y-m-d', $match->getDate())->format('l d F Y')),
						'content' => array()
					);

					array_push($content, 
						array(
							'domTeam' => 
								$match->getTeam()->getName()." ".$this->getSexLabel($match->getTeam()->getBoy(), $match->getTeam()->getGirl(), $match->getTeam()->getMixed()),
							'ext' => 
								$match->getExt() == 0 ? "DOM" : "EXT",
							'extTeam' => 
								$extTeam,
							'hourRDV' => 
								$key > 0 && $myMatches[$key]->getDate() == $myMatches[$key-1]->getDate() && $myMatches[$key]->getTeam()->getId() == $myMatches[$key-1]->getTeam()->getId() ? "" : $match->getHourRDV(),
							'hourStart' => 
								$match->getHourStart()
						)
					);					
					if (($key < sizeof($myMatches)-1 && ($myMatches[$key]->getDate() !== $myMatches[$key+1]->getDate())) || $key == sizeof($myMatches)-1) {     
						$newDate['content'] = $content;                        
						array_push($datas, $newDate);
						$content = array();
					}
				}
			} 

			\Timber\Timber::$locations = plugin_dir_path( __FILE__ ).'../../../public/views/screens/aviaComponents/';
			$this->context = \Timber\Timber::get_context();	
			$this->context['myMatches'] = $datas; 

			return \Timber\Timber::fetch("calendar.twig", $this->context);
	    }
					
			
	}
}
