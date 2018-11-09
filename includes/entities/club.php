<?php 
class Club {
   private $id;
   private $name;
   private $string;
   private $boy;
   private $girl;
   private $mixed;
   private $address;

   public function __construct($id=null, $name, $string, $boy, $girl, $mixed, $address) {
       $this->id = $id;
       $this->name = $name;
       $this->string = $string;
       $this->boy = $boy;
       $this->girl = $girl;
       $this->mixed = $mixed;
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

    public function getaddress() {
        return $this->address;
    }

    public function setaddress($address) {
        $this->address = $address;
        return $this;
    }
}
?>