<?php
// Source https://gist.github.com/jakebellacera/635416
class Ics
{
    const DT_FORMAT = 'Ymd\THis';
    private $events;

    function __construct($matches) {
        $this->events = array();
        $url = parse_url(get_option("siteurl"))['host'];
        foreach ($matches as $key => $match){
            if ($match->getDate() && $match->getHourStart()){
                $this->events[] = array(
                    'location' => ($match->getStreet() || $match->getCity() || $match->getGym()) ? $match->getStreet() . " / " . $match->getCity() . " / " . $match->getGym() : '',
                    'description' =>  ($match->getHourRdv() ? "Rdv : " . $match->getHourRdv() ." - " : '' ) . ($match->getTeam()->getName() ? $match->getTeam()->getName() ." " . ($match->getTeam()->getBoy() ? 'Masculin' : '') . ($match->getTeam()->getGirl() ? 'Féminin' : '') .($match->getTeam()->getMixed() ? 'Mixte' : '') : '' ),
                    'dtstart' => ($match->getDate() || $match->getHourStart() ? $match->getDate(). ' ' . $match->getHourStart() : ''),
                    // 'dtend' => ($match->getDate() || $match->getHourStart() ? $match->getDate(). ' ' . $match->getHourStart() : ''),
                    'summary' => ($match->getMatchDay() ? "J.".$match->getMatchDay(). " : "  : '') . $match->getLocalTeam() . " contre " .$match->getVisitingTeam(),  
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
        file_put_contents(plugin_dir_path( __FILE__ ).'../../public/ics/'.$matches[0]->getTeam()->getClub()->getName().'_'.$matches[0]->getTeam()->getName().'_'.$matches[0]->getTeam()->getId().'.ics', $this->prepare());
    }

    private function prepare() {
        $cp = array();
        if (count($this->events) > 0) {
            $cp[] = 'BEGIN:VCALENDAR';
            $cp[] = 'VERSION:2.0';
            $cp[] = 'PRODID:-//hacksw/handcal//NONSGML v1.0//EN';
            $cp[] = 'CALSCALE:GREGORIAN';

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
            case 'dtend':
            case 'dtstamp':
            case 'dtstart':
                $val = $this->format_timestamp($val);
                break;
            default:
                $val = $this->escape_string($val);
        }
        return $val;
    }

    private function format_timestamp($timestamp) {
        $dt = new DateTime($timestamp);
        return $dt->format(self::DT_FORMAT);
    }

    private function escape_string($str) {
        return preg_replace('/([,;])/', '\$1', $str);
    }
}