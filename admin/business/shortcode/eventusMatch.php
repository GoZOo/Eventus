<?php

if (!defined('ABSPATH')) {  // Exit if accessed directly
	exit;  
}

if (!class_exists( 'EventusMatch') && class_exists('aviaShortcodeTemplate')) {
	class EventusMatch extends aviaShortcodeTemplate {
        use MasterTrait;

		function shortcode_insert_button() {
			$this->config['self_closing']	=	'yes';
			
			$this->config['name']		= "Match";
			$this->config['tab']		= "Eventus";
			$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-heading.png";
			$this->config['order']		= 99;
			$this->config['shortcode'] 	= 'eventus_next_match';
			$this->config['tooltip'] 	= "Afficher le prochain/dernier match";
		}
		
		function popup_elements() {
			$font_size_array = array( 
				__("Default Size", 'avia_framework' ) => '',
				__("Flexible font size (adjusts to screen width)" , 'avia_framework') => AviaHtmlHelper::number_array(3,7,0.5 , array(), "vw", "", "vw"),
				__("Fixed font size" , 'avia_framework') => AviaHtmlHelper::number_array(11,150,1, array(), "px", "", "")
			);	
		
			foreach (TeamDAO::getInstance()->getAllTeams() as $team) {
				$allTeamsDisplay[$team->getName()." ".$this->getSexLabel2($team->getBoy(), $team->getGirl(), $team->getMixed())." - ".$team->getClub()->getName()] = $team->getId();
			}		
			
			$this->elements = array(
				array(	
					"name" 	=> "Type du match à afficher",
					"desc" 	=> "Sélectionnez un type",
					"id" 	=> "type",
					"type" 	=> "select",
					"std" 	=> "0",
					"subtype" => array("Prochain match"=>'0',"Dernier Match"=>'1')
					),

				array(	
					"name" 	=> "Format à afficher",
					"desc" 	=> "Sélectionnez un format",
					"id" 	=> "format",
					"type" 	=> "select",
					"std" 	=> "0",
					"subtype" => array("Lignes multiples"=>'0',"Deux lignes"=>'1')
					),
					
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
	                'teamid' => '',
	                'type' => '',
	                'format' => ''
	            ),
	        $atts));

    		
    		$stringDisplay = $stringDisplay2 = "Aucun match disponible";

	        if ($format == 0) {
	        	if ($type == 0) {
	        		$myMatch = MatchDAO::getInstance()->getCloseMatchByTeamId($teamid, "next"); 
	               	$title = 'Prochain match';

			        if ($myMatch->getId()){
			            $stringDisplay = 
			                $this->toFrenchDate(date_create_from_format('Y-m-d', $myMatch->getDate())->format('l d F Y'))." : ".
			                str_replace(":", "H", $myMatch->getHourStart())." / ". 
			                ($myMatch->getMatchDay() ? "Journée ".$myMatch->getMatchDay() :"")."<br>".
			                $myMatch->getLocalTeam()." - ".
			                $myMatch->getVisitingTeam();
			        } 
		        } else {
		        	$myMatch = MatchDAO::getInstance()->getCloseMatchByTeamId($teamid, "last"); 
		            $title = 'Dernier match';

			        if ($myMatch->getId()){
			            $stringDisplay = 
			                $this->toFrenchDate(date_create_from_format('Y-m-d', $myMatch->getDate())->format('l d F Y'))." : ".
			                str_replace(":", "H", $myMatch->getHourStart())." / ". 
			                ($myMatch->getMatchDay() ? "Journée ".$myMatch->getMatchDay() :"") ."<br>".
			                $myMatch->getLocalTeam(). " : ".
			                $myMatch->getLocalTeamScore()." - ".
			                $myMatch->getVisitingTeamScore(). " : ".
			                $myMatch->getVisitingTeam();
			        }		       
		        }

		        return do_shortcode("[av_heading heading='".$title."' tag='h2' style='blockquote modern-quote modern-centered' size='' subheading_active='subheading_below' subheading_size='15' margin='0' margin_sync='true' padding='' color='' custom_font='' av-medium-font-size-title='' av-small-font-size-title='' av-mini-font-size-title='' av-medium-font-size='' av-small-font-size='' av-mini-font-size='' av_uid='' admin_preview_bg='']".$stringDisplay."[/av_heading]");
		    } else {
		    	if ($type == 0) {
		    		$myMatch = MatchDAO::getInstance()->getCloseMatchByTeamId($teamid, "next"); 
	               	$title = 'Prochain match';

		    		if ($myMatch->getId()){
			            $stringDisplay = 
			                "<span style=\"color: white;\">".
			                $myMatch->getLocalTeam()." -</span> ".
			                $myMatch->getVisitingTeam();
			            $stringDisplay2 = 
			                date_create_from_format('Y-m-d', $myMatch->getDate())->format('d/m/Y')." - ".
			                str_replace(":", "H", $myMatch->getHourStart());
			        } 
		    	} else {
		    		$myMatch = MatchDAO::getInstance()->getCloseMatchByTeamId($teamid, "last"); 
		            $title = 'Dernier match';

		    		if ($myMatch->getId()){
			            $stringDisplay = 
			                "<span style=\"color: white;\">".
			                $myMatch->getLocalTeam()." : ".
			                $myMatch->getLocalTeamScore()." -</span> ".
			                $myMatch->getVisitingTeamScore()." : ".
			                $myMatch->getVisitingTeam();
			            $stringDisplay2 = 
			                date_create_from_format('Y-m-d', $myMatch->getDate())->format('d/m/Y')." - ".
			                str_replace(":", "H", $myMatch->getHourStart());
			        } 
		    	}

		        return do_shortcode("[av_heading heading='".$stringDisplay."' tag='h6' style='blockquote modern-quote modern-centered' size='' subheading_active='subheading_above' subheading_size='15' margin='0' margin_sync='true' padding='0' color='' custom_font='' av-medium-font-size-title='' av-small-font-size-title='' av-mini-font-size-title='' av-medium-font-size='' av-small-font-size='' av-mini-font-size='' av_uid='av-jl3p1zhc' custom_class='' admin_preview_bg='']
		            ".$title."<span style='float: right;''>".$stringDisplay2."</span>
			        [/av_heading]");

		    }
	    }
	}
}
