<?php

namespace Eventus\Admin\Business\Helper;

/**
* TraitHelper is a trait use to help AviaShortcode
*
* @package  Admin/Business
* @access   public
*/
class StaticHelper{
    /**
    * Get sex label by a key
    *
    * @param string     Boy key
    * @param string     Girl key
    * @param string     Mixed key
    * @return string    Label
    * @access public
    */
    static function getSexLabel($boy, $girl, $mixed) {
        if ($boy){
            return __('Male', 'eventus');
        } else if ($girl) {
            return __('Female', 'eventus');
        } else if ($mixed) {
            return __('Mixed', 'eventus');
        }
        return;
    }

    
    /**
    * Transform character with accent to characters without accents
    *
    * @param string    String to strip accents
    * @return Match[]  String with accents strip 
    * @access public
    */
    static function stripAccents($str) {
        return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}
?>