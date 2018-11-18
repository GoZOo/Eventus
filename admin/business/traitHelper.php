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
            return __('Male', 'eventus');
        } else if ($girl) {
            return __('Female', 'eventus');
        } else if ($mixed) {
            return __('Mixed', 'eventus');
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
        $french_days = array( __('Monday', 'eventus'),  __('Tuesday', 'eventus'),  __('Wednesday', 'eventus'),  __('Thursday', 'eventus'),  __('Friday', 'eventus'),  __('Saturday', 'eventus'),  __('Sunday', 'eventus'));

        $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'Décember');
        $french_months = array(__('January', 'eventus'),__('February', 'eventus'),__('March', 'eventus'),__('April', 'eventus'),__('May', 'eventus'),__('June', 'eventus'),__('July', 'eventus'),__('August', 'eventus'),__('September', 'eventus'),__('October', 'eventus'),__('November', 'eventus'),__('Décember', 'eventus'));
        $myDate = str_replace($english_months, $french_months, $myDate);
        return str_replace($english_days, $french_days, $myDate);
    }
}
?>