<?php 
class Team {
   private $id;
   private $name;
   private $url;
   private $boy;
   private $girl;
   private $mixed;
   private $position;
   private $points;
   private $time;
   private $img;
   private $club;

   public function __construct($id=null, $name, $url, $boy, $girl, $mixed, $position, $points, $time, $img, $club) {
      $this->id = $id;
      $this->name = $name;
      $this->url = $url;
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

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
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