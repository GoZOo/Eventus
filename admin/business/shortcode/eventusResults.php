<?php

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}

if (!class_exists( 'EventusResults') && class_exists('aviaShortcodeTemplate')) {
	class EventusResults extends aviaShortcodeTemplate {
        use MasterTrait;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= "Liste Résultats";
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-accordion.png";
			$this->config['order']		= 95;
			$this->config['shortcode'] 	= 'eventus_results';
			$this->config['tooltip'] 	= "Afficher les résultats";
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
			wp_enqueue_script( 'script', '../wp-content/plugins/eventus/public/js/shortcodeResults.js', '', '', true);

			$sexes = array('boy', 'girl', 'mixed');
	        $sexesDisplay = array('Masculin', 'Féminin', 'Mixte');
	        $allClubs = ClubDAO::getInstance()->getAllClubs();

			$output = "";

	        $i=0;
	        $output  .= "<div class='allClbus'>";
	        foreach ($allClubs as $club) {
	            $output  .= "<div class='blockClub' style='right:-".$i++."00%'>";
	                $output  .= "<div class='ligneNomSuivant'>";
	                    $output  .= "<p>".$club->getName()."</p>";
	                    if (sizeof($allClubs)>1) { 
	                    	$output  .= "<button type='button' class='clubSuivant' >&#9658;&#9658;</button>";
	                    } 
	                $output  .= "</div>";
	                foreach ($sexes as $key => $sex) {
	                    if (($sex == 'boy' && $club->getBoy()) || ($sex == 'girl' && $club->getGirl()) || ($sex == 'mixed' && $club->getMixed())) {
	                        $allTeams = TeamDAO::getInstance()->getAllTeamsByClubAndSex($club, $sex);
							$output  .= "<p class='sexe'>".$sexesDisplay[$key]." :</p>";
							if ($allTeams) {
								foreach ($allTeams as $team) {  
									//$myMatch = MatchDAO::getInstance()->getCloseMatchByTeamId($team->getId(), "next"); //wrong method : temporary when no matches
									$myMatch = MatchDAO::getInstance()->getCloseMatchByTeamId($team->getId(), "last");
									if ($myMatch->getId()) {
										$output  .= "<div class='resultat'>
											<div class='ligneEqDate'>
												<a href='".$team->getUrl()."' target='_blank'>".$team->getName()."</a>
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
							}	                         
	                    }
	                                 
	                    
	                } 
	            $output  .= "</div>";  
	        }
	        $output  .= "</div>";
	        return $output;
	    }  
	}
}
