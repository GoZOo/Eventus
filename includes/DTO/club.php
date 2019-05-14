<?php 

namespace Eventus\Includes\DTO;

/**
* Club is a class use to manage clubs
*
* @package  Includes/DTO
* @access   public
*/
class Club {
    /**
    * @var int|null $id         Id of the club
    * @var string   $name       Name of the club
    * @var string   $string     String that will be use to parse FFHB website
    * @var string   $address    Address of the gym. Used to calculate hours rdv
    * @var int|null $img        Id of the photo of the club
    * @var string   $season     Season of the club
    */

    private $id, $name, $string, $address, $img, $season;

    public function __construct($id=null, $name, $string, $address, $img=null, $season) {
        $this->id = $id;
        $this->name = $name;
        $this->string = $string;
        $this->address = $address;
        $this->img = $img;
        $this->season = $season;
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

    public function getString() {
        return $this->string;
    }

    public function setString($string) {
        $this->string = $string;
        return $this;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
        return $this;
    }

    public function getImg() {
        return $this->img;
    }

    public function setImg($img) {
        $this->img = $img;
        return $this;
    }

    public function getSeason() {
        return $this->season;
    }

    public function setSeason($season) {
        $this->season = $season;
        return $this;
    }
}
?>