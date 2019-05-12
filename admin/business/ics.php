<?php
// Source https://gist.github.com/jakebellacera/635416
// namespace Eventus\Admin\Business;

/**
* ICS is a class use to generate ics calendars
*
* @package  Includes
* @access   public
*/

class Ics {
    use TraitHelper;
    const DT_FORMAT = 'Ymd\THis';
    const TIMEZONE = 'Europe/Paris';
    private static $events;
    private static $clubName;
    private static $teamName;

    public static function init($matches) {
        if($matches !== null && sizeof($matches)){
            self::$events = array();
            self::$clubName = $matches[0]->getTeam()->getClub()->getName();
            self::$teamName = $matches[0]->getTeam()->getName();
            $url = get_option("siteurl");
            foreach ($matches as $key => $match){
                if ($match->getDate() && $match->getHourStart()){
                    self::$events[] = array(
                        'location' => implode(" ".__('at', 'eventus')." ", array(($match->getStreet() ? $match->getStreet() : ""), ($match->getCity() ? $match->getCity() : ""))),
                        'description' =>  
                            ($match->getLocalTeamScore() && $match->getVisitingTeamScore() ? "Score : " . $match->getLocalTeamScore() ." - ". $match->getVisitingTeamScore() . " ". self::getState($match) . "\\n" : '' ). 
                            ($match->getHourRdv() ? "RDV : " . $match->getHourRdv() ."\\n" : '' ). 
                            ($match->getGym() ? __('Hall', 'eventus')." : " . $match->getGym() ."\\n" : '' ). 
                            ($match->getTeam()->getName() ? __('Team', 'eventus')." : " . $match->getTeam()->getName() ." " . 
                                self::getSexLabel($match->getTeam()->getBoy(), $match->getTeam()->getGirl(), $match->getTeam()->getMixed())
                            : '' ),
                        'dtstart' => ($match->getDate() && $match->getHourStart() ? $match->getDate(). ' ' . $match->getHourStart() : ''),
                        'dtend' => ($match->getDate() && $match->getHourStart() ? $match->getDate(). ' ' . $match->getHourStart() : ''),
                        'summary' => ($match->getMatchDay() ? __('D.', 'eventus').$match->getMatchDay(). " : "  : '') . $match->getLocalTeam() . " vs. " .$match->getVisitingTeam(), 
                        'url' => $url
                    );
                }            
            }
            
            if (count(self::$events) > 0) {
                for ($p = 0; $p <= count(self::$events) - 1; $p++) {
                    foreach (self::$events[$p] as $key => $val) {
                        self::$events[$p][$key] = self::sanitize_val($val, $key);
                    }
                }
            } 
            file_put_contents(
                plugin_dir_path( __FILE__ ).'../../public/ics/'.str_replace(' ', '_', self::$clubName .'_'.self::$teamName .'_'. $matches[0]->getTeam()->getId()).'.ics',
                self::prepare()
            );  
        }
             
    }

    private function prepare() {
        $cp = array();
        if (count(self::$events) > 0) {
            $cp[] = 'BEGIN:VCALENDAR';
            $cp[] = 'VERSION:2.0';
            $cp[] = 'PRODID:-//hacksw/handcal//NONSGML v1.0//EN';          
            $cp[] = 'CALSCALE:GREGORIAN';
            $cp[] = 'X-WR-CALNAME:'. self::$clubName.' : '.self::$teamName;  

            for ($p = 0; $p <= count(self::$events) - 1; $p++) {
                $cp[] = 'BEGIN:VEVENT';
                foreach (self::$events[$p] as $key => $val) {
                    if ($key == "dtstart" || $key == "dtend") {
                        $cp[] = strtoupper($key) . ';TZID='.self::TIMEZONE.':' . $val;
                    } else {
                        $cp[] = strtoupper($key) . ':' . $val;
                    }      
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
                $val = self::format_timestamp($val);
                break;
            case 'dtend':                
                $val = self::format_timestamp_end($val);
                break;
            default:
                $val = self::escape_string($val);
        }
        return $val;
    }

    private function format_timestamp($timestamp) {      
        $dt = new DateTime($timestamp, new DateTimeZone(self::TIMEZONE));
        date_default_timezone_set("UTC");
        return $dt->format(self::DT_FORMAT);
    }

    private function format_timestamp_end($timestamp) {
        $dt = new DateTime($timestamp, new DateTimeZone(self::TIMEZONE));
        $dt->modify('+1 hour +30 minutes');
        date_default_timezone_set("UTC");
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