<?php 
/**
* Club is a class use to manage clubs
*
* @package  Includes/Entities
* @access   public
*/
class Club {
    /**
    * @var int|null $id         Id of the club
    * @var string   $name       Name of the club
    * @var string   $string     String that will be use to parse FFHB website
    * @var string   $address    Address of the gym. Used to calculate hours rdv
    */

    private $id, $name, $string, $address;

    public function __construct($id, $name, $string, $address) {
        $this->id = $id;
        $this->name = $name;
        $this->string = $string;
        $this->address = $address;
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

    public function getaddress() {
        return $this->address;
    }

    public function setaddress($address) {
        $this->address = $address;
        return $this;
    }
}
?>