<?php
/**
* TraitHelper is a trait use to help AviaShortcode
*
* @package  Includes
* @access   public
*/
trait TraitHelper{
    /**
    * Get sex label by a key
    *
    * @param string     Boy key
    * @param string     Girl key
    * @param string     Mixed key
    * @return string    Label
    * @access public
    */
    function getSexLabel($boy, $girl, $mixed) {
        if ($boy){
            return "Masculin";
        } else if ($girl) {
            return "Féminin";
        } else if ($mixed) {
            return "Mixte";
        }
        return;
    }

    /**
    * Convert english string date to a french date
    *
    * @param string     English date
    * @return string    French date
    * @access public
    */
    function toFrenchDate($myDate) {
        $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $french_days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'Décember');
        $french_months = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
        $myDate = str_replace($english_months, $french_months, $myDate);
        return str_replace($english_days, $french_days, $myDate);
    }
}
?>