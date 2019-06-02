<?php

use Eventus\Includes\DAO as DAO;
use Eventus\Admin\Business\Helper as Helper;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusCirclePosPts is a class use add an element title in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/
if (!class_exists( 'EventusMatch') && class_exists('aviaShortcodeTemplate')) {
	class EventusMatch extends aviaShortcodeTemplate {
        use Helper\TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= __('Matche', 'eventus');
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-heading.png";
			$this->config['order']		= 99;
			$this->config['shortcode'] 	= 'eventus_next_match';
			$this->config['tooltip'] 	= __('Display next/last match', 'eventus');
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
					"name" 	=> __('Display next/last match', 'eventus'), 
					"desc" 	=> __('Select a type', 'eventus'),
					"id" 	=> "type",
					"type" 	=> "select",
					"std" 	=> "0",
					"subtype" => array(__('Next match', 'eventus')=>'0',__('Last match', 'eventus')=>'1')
					),

				array(	
					"name" 	=> __('Format to display', 'eventus'),
					"desc" 	=> __('Select a format', 'eventus'),
					"id" 	=> "format",
					"type" 	=> "select",
					"std" 	=> "0",
					"subtype" => array(__('Multiple lines', 'eventus')=>'0',__('Two lines', 'eventus')=>'1')
					),
					
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
	                'teamid' => '',
	                'type' => '',
	                'format' => ''
	            ),
			$atts));			
			
			\Timber\Timber::$locations = plugin_dir_path( __FILE__ ).'../../../public/views/screens/aviaComponents/';
			$this->context = \Timber\Timber::get_context();	
    		
			$this->context['stringDisplay'] = __('No matches available', 'eventus');	
			$this->context['stringDisplay2'] = __('No matches available', 'eventus');	
			$this->context['format'] = $format;
	        if ($format == 0) {
	        	if ($type == 0) {
	        		$myMatch = DAO\MatchDAO::getInstance()->getCloseMatchByTeamId($teamid, "next"); 
					$this->context['title'] = __('Next match', 'eventus');

			        if ($myMatch->getId()){
						$this->context['stringDisplay'] = 
			                $this->toFrenchDate(date_create_from_format('Y-m-d', $myMatch->getDate())->format('l d F Y'))." : ".
			                str_replace(":", "H", $myMatch->getHourStart()). 
			                ($myMatch->getMatchDay() ? " / Journée ".$myMatch->getMatchDay() :"")."<br>".
			                $myMatch->getLocalTeam()." - ".
			                $myMatch->getVisitingTeam();
			        } 
		        } else {
		        	$myMatch = DAO\MatchDAO::getInstance()->getCloseMatchByTeamId($teamid, "last"); 
		            $this->context['title'] = __('Last match', 'eventus');

			        if ($myMatch->getId()){
			            $this->context['stringDisplay'] =
			                $this->toFrenchDate(date_create_from_format('Y-m-d', $myMatch->getDate())->format('l d F Y'))." : ".
			                str_replace(":", "H", $myMatch->getHourStart()). 
			                ($myMatch->getMatchDay() ? " / Journée ".$myMatch->getMatchDay() :"") ."<br>".
			                $myMatch->getLocalTeam(). " : ".
			                $myMatch->getLocalTeamScore()." - ".
			                $myMatch->getVisitingTeamScore(). " : ".
			                $myMatch->getVisitingTeam();
			        }		       
		        }
		    } else {
		    	if ($type == 0) {
		    		$myMatch = DAO\MatchDAO::getInstance()->getCloseMatchByTeamId($teamid, "next"); 
					$this->context['title'] =  __('Next match', 'eventus');

		    		if ($myMatch->getId()){
			            $this->context['stringDisplay'] = 
							$myMatch->getExt() ? 
								"<span style=\"color: white;\">". $myMatch->getLocalTeam()." -</span> ". $myMatch->getVisitingTeam() 
								:
								$myMatch->getLocalTeam() . " - <span style=\"color: white;\">". $myMatch->getVisitingTeam() ." </span> ";
						$this->context['stringDisplay2'] = 
			                date_create_from_format('Y-m-d', $myMatch->getDate())->format('d/m/Y')." - ".
			                str_replace(":", "H", $myMatch->getHourStart());
			        } 
		    	} else {
		    		$myMatch = DAO\MatchDAO::getInstance()->getCloseMatchByTeamId($teamid, "last"); 
		            $this->context['title'] = __('Last match', 'eventus');
		    		if ($myMatch->getId()){
						$this->context['stringDisplay'] = 
							$myMatch->getExt() ? 
								"<span style=\"color: white;\">". $myMatch->getLocalTeam()." : ". $myMatch->getLocalTeamScore()." -</span> ". $myMatch->getVisitingTeamScore()." : ". $myMatch->getVisitingTeam() 
								:
								$myMatch->getLocalTeam()." : ". $myMatch->getLocalTeamScore()." - <span style=\"color: white;\">". $myMatch->getVisitingTeamScore()." : ". $myMatch->getVisitingTeam()." </span> ";
			                
						$this->context['stringDisplay2'] = 
			                date_create_from_format('Y-m-d', $myMatch->getDate())->format('d/m/Y')." - ".
			                str_replace(":", "H", $myMatch->getHourStart());
			        } 
		    	}
			}
			
			return \Timber\Timber::fetch("match.twig", $this->context);
	    }
	}
}
