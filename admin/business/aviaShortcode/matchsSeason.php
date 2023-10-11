<?php

use Eventus\Includes\DAO as DAO;
use Eventus\Admin\Business\Helper as Helper;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusMatchsSeason is a class use add an element title in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/
if (!class_exists( 'EventusMatchsSeason') && class_exists('aviaShortcodeTemplate')) {
	class EventusMatchsSeason extends aviaShortcodeTemplate {
        use Helper\TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= __('Matches of the season', 'eventus');
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-accordion.png";
			$this->config['order']		= 99;
			$this->config['shortcode'] 	= 'eventus_season_matchs';
			$this->config['tooltip'] 	= __('Display matches of the season', 'eventus');
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

				 array(
					"name" 	=> __('Saison', 'eventus'),
					"desc" 	=> __('Sélectionner la première année de la saison', 'eventus'),
					"id" 	=> "seasonyear",
					"type" 	=> "input",
                    "std" 	=> date('Y')
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
	                'seasonyear' => NULL,
	            ),
			$atts));

            wp_enqueue_style('eventusFront_results', WP_PLUGIN_URL.'/eventus/public/views/css/matchesSeason.css');
			\Timber\Timber::$locations = plugin_dir_path( __FILE__ ).'../../../public/views/screens/aviaComponents/';
			$this->context = \Timber\Timber::get_context();	
    		
			$this->context['stringDisplay'] = __('No matches available yet', 'eventus');

            if (empty($seasonyear)) {
                $seasonyear = date('Y');
            }

            $matches = DAO\MatchDAO::getInstance()->getSeasonMatchsByTeamId($teamid, $seasonyear);

            $this->context['matches'] = $matches;
            $this->context['title'] = __('Matchs de la saison ' . $seasonyear . '/' . ($seasonyear + 1), 'eventus');

			return \Timber\Timber::fetch("matchesSeason.twig", $this->context);
	    }
	}
}
