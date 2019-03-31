<?php
// Source https://gist.github.com/jakebellacera/635416
class Ics {
    const DT_FORMAT = 'Ymd\THis';
    private $events;
    private $clubName;
    private $teamName;

    function __construct($matches) {
        $this->events = array();
        $this->clubName = $matches[0]->getTeam()->getClub()->getName();
        $this->teamName = $matches[0]->getTeam()->getName();
        $url = get_option("siteurl");
        foreach ($matches as $key => $match){
            if ($match->getDate() && $match->getHourStart()){
                $this->events[] = array(
                    'location' => implode(" à ", array($match->getStreet(), $match->getCity())),
                    'description' =>  
                        ($match->getLocalTeamScore() && $match->getVisitingTeamScore() ? "Score : " . $match->getLocalTeamScore() ." - ". $match->getVisitingTeamScore() . " ". $this->getState($match) . "\\n" : '' ). 
                        ($match->getHourRdv() ? "RDV : " . $match->getHourRdv() ."\\n" : '' ). 
                        ($match->getGym() ? "Salle : " . $match->getGym() ."\\n" : '' ). 
                        ($match->getTeam()->getName() ? "Équipe : " . $match->getTeam()->getName() ." " . 
                            ($match->getTeam()->getBoy() ? 'Masculin' : '') . ($match->getTeam()->getGirl() ? 'Féminin' : '') . ($match->getTeam()->getMixed() ? 'Mixte' : '') 
                        : '' ),
                    'dtstart' => ($match->getDate() && $match->getHourStart() ? $match->getDate(). ' ' . $match->getHourStart() : ''),
                    'dtend' => ($match->getDate() && $match->getHourStart() ? $match->getDate(). ' ' . $match->getHourStart() : ''),
                    'summary' => ($match->getMatchDay() ? "J.".$match->getMatchDay(). " : "  : '') . $match->getLocalTeam() . " vs. " .$match->getVisitingTeam(),  
                    'url' => $url
                );
            }            
        }
        
        if (count($this->events) > 0) {
            for ($p = 0; $p <= count($this->events) - 1; $p++) {
                foreach ($this->events[$p] as $key => $val) {
                    $this->events[$p][$key] = $this->sanitize_val($val, $key);
                }
            }
        } 
        file_put_contents(
            plugin_dir_path( __FILE__ ).'../../public/ics/'.str_replace(' ', '_', $this->clubName .'_'.$this->teamName .'_'. $matches[0]->getTeam()->getId()).'.ics',
            $this->prepare()
        );       
    }

    private function prepare() {
        $cp = array();
        if (count($this->events) > 0) {
            $cp[] = 'BEGIN:VCALENDAR';
            $cp[] = 'VERSION:2.0';
            $cp[] = 'PRODID:-//hacksw/handcal//NONSGML v1.0//EN';          
            $cp[] = 'CALSCALE:GREGORIAN';
            $cp[] = 'X-WR-CALNAME:'. $this->clubName.' : '.$this->teamName;  

            for ($p = 0; $p <= count($this->events) - 1; $p++) {
                $cp[] = 'BEGIN:VEVENT';
                foreach ($this->events[$p] as $key => $val) {
                    $cp[] = strtoupper($key) . ':' . $val;
                }
                $cp[] = 'END:VEVENT';
            }
            $cp[] = 'END:VCALENDAR';
        }
        return implode("\r\n", $cp);
    }

    private function sanitize_val($val, $key = false) {
        switch ($key) {
            case 'dtstamp':
            	break;
            case 'dtstart':
                $val = $this->format_timestamp($val);
                break;
            case 'dtend':                
	        	$val = $this->format_timestamp_end($val);
                break;
            default:
                $val = $this->escape_string($val);
        }
        return $val;
    }

    private function format_timestamp($timestamp) {
		$dt = new DateTime($timestamp, new DateTimeZone('Europe/Paris'));
		return $dt->format(self::DT_FORMAT);
	}

	private function format_timestamp_end($timestamp) {
		$dt = new DateTime($timestamp, new DateTimeZone('Europe/Paris'));
        $dt->modify('+1 hour +30 minutes');
		return $dt->format(self::DT_FORMAT);
    }
    
	private function escape_string($str) {
		return preg_replace('/([,;])/','\$1', $str);
    }
    
    private function getState($match){
        $res = "";
        if ((!$match->getExt() && $match->getLocalTeamScore() > $match->getVisitingTeamScore()) || ($match->getExt() && $match->getLocalTeamScore() < $match->getVisitingTeamScore())) {
            $res = "(V)";
        } else if ((!$match->getExt() && $match->getLocalTeamScore() < $match->getVisitingTeamScore()) || ($match->getExt() && $match->getLocalTeamScore() > $match->getVisitingTeamScore())) {
            $res = "(D)";
        } else if ($match->getLocalTeamScore() == $match->getVisitingTeamScore()) {
            $res = "(N)";
        } 
        return $res;
    }
}