<?php 

namespace Eventus\Includes\DTO;

/**
* Team is a class use to manage teams
*
* @package  Includes/DTO
* @access   public
*/
class Team {    
    /**
    * @var int|null     $id         Id of the team
    * @var string       $name       Name of the team
    * @var string|null  $urlOne     First url of the FFHB website that contain result
    * @var string|null  $urlTwo     Second url of the FFHB website that contain result
    * @var bool         $boy        Is the team a boy team?
    * @var bool         $girl       Is the team agirl team?
    * @var bool         $mixed      Is the team amixed team?
    * @var int|null     $position   Position of the team in the classement
    * @var int|null     $points     Points of the team in the classement
    * @var int          $time       Time for the rdv before match start
    * @var int|null     $img        Id of the photo of the team
    * @var Club         $club       Club of the team
    */

    private $id, $name, $urlOne, $urlTwo, $boy, $girl, $mixed, $position, $points, $time, $img, $club;

    public function __construct($id=null, $name, $urlOne, $urlTwo, $boy, $girl, $mixed, $position, $points, $time, $img, $club=null) {
        $this->id = $id;
        $this->name = $name;
        $this->urlOne = $urlOne;
        $this->urlTwo = $urlTwo;
        $this->boy = $boy;
        $this->girl = $girl;
        $this->mixed = $mixed;
        $this->position = $position;
        $this->points = $points;
        $this->time = $time;
        $this->img = $img;
        $this->club = $club;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getUrlOne() {
        return $this->urlOne;
    }

    public function setUrlOne($urlOne) {
        $this->urlOne = $urlOne;
        return $this;
    }

    public function getUrlTwo() {
        return $this->urlTwo;
    }

    public function setUrlTwo($urlTwo) {
        $this->urlTwo = $urlTwo;
        return $this;
    }

    public function getBoy() {
        return $this->boy;
    }

    public function setBoy($boy) {
        $this->boy = $boy;
        return $this;
    }

    public function getGirl() {
        return $this->girl;
    }

    public function setGirl($girl) {
        $this->girl = $girl;
        return $this;
    } 

    public function getMixed() {
        return $this->mixed;
    }

    public function setMixed($mixed) {
        $this->mixed = $mixed;
        return $this;
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position) {
        $this->position = $position;
        return $this;
    }

    public function getPoints() {
        return $this->points;
    }

    public function setPoints($points) {
        $this->points = $points;
        return $this;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
        return $this;
    }

    public function getImg() {
        return $this->img;
    }

    public function setImg($img) {
        $this->img = $img;
        return $this;
    }

    public function getClub() {
        return $this->club;
    }

    public function setClub($club) {
        $this->club = $club;
        return $this;
    }
}
?>