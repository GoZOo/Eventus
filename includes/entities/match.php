<?php 
/**
* Match is a class use to manage matches
*
* @package  Includes/Entities
* @access   public
*/
class Match {  
    /**
    * @var int|null     $id                 Id of the match
    * @var int|null     $matchDay           Day in championship
    * @var int|null     $numMatch           Number match in a day 
    * @var date|null    $date               Date of the match
    * @var time|null    $hourRdv            Rdv match time
    * @var time|null    $hourStart          Time match
    * @var string       $localTeam          Name of the local team
    * @var int|null     $localTeamScore     Score of the local team
    * @var string       $visitingTeam       Name of the visiting team
    * @var int|null     $visitingTeamScore  Score of the visiting team
    * @var bool         $ext                Is the match at home?
    * @var string|null  $street             Street of the match location
    * @var string|null  $city               City of the match location
    * @var string|null  $gym                Gym of the match location
    * @var int          $type               Type of match : 0 = championship; 1 = son championship; 2 = other
    * @var int          $champ              Numero of the corresponding championship
    * @var Team         $team               Team playing the match
    * @var Match|null   $matchRef           Match ref if it's a son match
    */

    private $id, $matchDay, $numMatch, $date, $hourRdv, $hourStart, $localTeam, $localTeamScore, $visitingTeam, $visitingTeamScore, $ext, $street, $city, $gym, $type, $champ, $team, $matchRef;

    public function __construct($id=null, $matchDay, $numMatch, $date=null, $hourRdv=null, $hourStart=null, $localTeam, $localTeamScore=null, $visitingTeam, $visitingTeamScore=null, $ext=null, $street=null, $city=null, $gym=null, $type, $champ, $team, $matchRef=null) {
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
        $this->champ = $champ;
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

    public function getChamp() {
        return $this->champ;
    }

    public function setChamp($champ) {
        $this->champ = $champ;
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