<?php

use Eventus\Includes\Datas as DAO;

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}
/**
* EventusCirclePosPts is a class use add an element in avia constructor
*
* @package  Admin/Business/Shortcode
* @access   public
*/
if (!class_exists( 'EventusResults') && class_exists('aviaShortcodeTemplate')) {
	class EventusResults extends aviaShortcodeTemplate {
        use TraitHelper;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= __('Results List', 'eventus');
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-accordion.png";
			$this->config['order']		= 95;
			$this->config['shortcode'] 	= 'eventus_results';
			$this->config['tooltip'] 	= __('Display the results', 'eventus');
		}
		
		function popup_elements() {
			
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
			wp_enqueue_script('scriptEventusResults', plugin_dir_url( __FILE__ ).'/../../../../public/js/eventusResults.js', '', '', true); 
			wp_enqueue_style('cssEventusResults', plugin_dir_url( __FILE__ ).'/../../../../public/css/eventusResults.css');
			$allClubs = DAO\ClubDAO::getInstance()->getAllClubs();
			
			$output  = 
			"<div class='allClbus'>";

	        foreach ($allClubs as $i => $club) {
				$output .= "
					<div class='blockClub' style='right:-".$i++."00%'>
						<div class='ligneNomSuivant'>
							<p>".$club->getName()."</p>".
							(sizeof($allClubs) > 1 ? "<button type='button' class='clubSuivant' >&#9658;&#9658;</button>" : "")."
						</div>";
				$allTeams = DAO\TeamDAO::getInstance()->getAllTeamsByClubOrderBySex($club);
				$tempSex = "";
				foreach ($allTeams as $team) { 
					//$myMatch = MatchDAO::getInstance()->getCloseMatchByTeamId($team->getId(), "next"); //wrong method : temporary when no matches
					$myMatch = DAO\MatchDAO::getInstance()->getCloseMatchByTeamId($team->getId(), "last");
					if ($myMatch->getId()) {
						$newSex = $this->getSexLabel($team->getBoy(), $team->getGirl(), $team->getMixed());
						$output .= ($tempSex != $newSex ? "<p class='sexe'>".$newSex." :</p>" : "");
						$tempSex = $newSex;
						$output .= "
						<div class='resultat'>
							<div class='ligneEqDate'>
								<a href='".($team->getUrlTwo() ? $team->getUrlTwo() : $team->getUrlOne())."' target='_blank'>".$team->getName()."</a>
								<p>".date_create_from_format('Y-m-d',$myMatch->getDate())->format('d/m')."</p>
							</div>
							<div class='equipe1'>
								<p>".$myMatch->getLocalTeamScore()."</p>
								<p style=''>".$myMatch->getLocalTeam()."</p>
							</div>
							<div class='equipe2'>
								<p>".$myMatch->getVisitingTeamScore()."</p>
								<p>".$myMatch->getVisitingTeam()."</p>
							</div>
						</div>";
					}        
	            } 
				$output .= 
					"</div>";  
	        }
			$output  .= 
			"</div>";

	        return $output;
	    }  
	}
}
