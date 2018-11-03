<?php 
class Match {
   private $id;
   private $matchDay;
   private $numMatch;
   private $date;
   private $hourRdv;
   private $hourStart;
   private $localTeam;
   private $localTeamScore;
   private $visitingTeam;
   private $visitingTeamScore;
   private $ext;
   private $street;
   private $city;
   private $gym;
   private $type; /* 0 = championship; 1 = son championship; 2 = other */
   private $team;
   private $matchRef;

   public function __construct($id=null, $matchDay, $numMatch, $date=null, $hourRdv=null, $hourStart=null, $localTeam, $localTeamScore=null, $visitingTeam, $visitingTeamScore=null, $ext=null, $street=null, $city=null, $gym=null, $type, $team, $matchRef=null) {
       $this->id = $id;
       $this->matchDay = $matchDay;
       $this->numMatch = $numMatch;
       $this->date = $date;
       $this->hourRdv = $hourRdv;
       $this->hourStart = $hourStart;
       $this->localTeam = $localTeam;
       $this->localTeamScore = $localTeamScore;
       $this->visitingTeam = $visitingTeam;
       $this->visitingTeamScore = $visitingTeamScore;
       $this->ext = $ext;
       $this->street = $street;
       $this->city = $city;
       $this->gym = $gym;
       $this->type = $type;
       $this->team = $team;
       $this->matchRef = $matchRef;
   }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getMatchDay() {
        return $this->matchDay;
    }

    public function setMatchDay($matchDay) {
        $this->matchDay = $matchDay;
        return $this;
    }
    
    public function getNumMatch() {
        return $this->numMatch;
    }

    public function setNumMatch($numMatch) {
        $this->numMatch = $numMatch;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function getHourRdv() {
        return explode(":", $this->hourRdv)[0].":".explode(":", $this->hourRdv)[1];
    }

    public function setHourRdv($hourRdv) {
        $this->hourRdv = $hourRdv;
        return $this;
    }

    public function getHourStart() {
        return explode(":", $this->hourStart)[0].":".explode(":", $this->hourStart)[1];
    }

    public function setHourStart($hourStart) {
        $this->hourStart = $hourStart;
        return $this;
    }

    public function getLocalTeam() {
        return $this->localTeam;
    }

    public function setLocalTeam($localTeam) {
        $this->localTeam = $localTeam;
        return $this;
    }

    public function getLocalTeamScore() {
        return $this->localTeamScore;
    }

    public function setLocalTeamScore($localTeamScore) {
        $this->localTeamScore = $localTeamScore;
        return $this;
    }

    public function getVisitingTeam() {
        return $this->visitingTeam;
    }

    public function setVisitingTeam($visitingTeam) {
        $this->visitingTeam = $visitingTeam;
        return $this;
    }

    public function getVisitingTeamScore() {
        return $this->visitingTeamScore;
    }

    public function setVisitingTeamScore($visitingTeamScore) {
        $this->visitingTeamScore = $visitingTeamScore;
        return $this;
    }

    public function getExt() {
        return $this->ext;
    }

    public function setExt($ext) {
        $this->ext = $ext;
        return $this;
    }

    public function getStreet() {
        return $this->street;
    }

    public function setStreet($street) {
        $this->street = $street;
        return $this;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    public function getGym() {
        return $this->gym;
    }

    public function setGym($gym) {
        $this->gym = $gym;
        return $this;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function getTeam() {
        return $this->team;
    }

    public function setTeam($team) {
        $this->team = $team;
        return $this;
    }

    public function getMatchRef() {
        return $this->matchRef;
    }

    public function setMatchRef($matchRef) {
        $this->matchRef = $matchRef;
        return $this;
    }
}
?>