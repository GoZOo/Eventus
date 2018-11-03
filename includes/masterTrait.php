<?php

trait MasterTrait{
    function getSexLabel2($boy, $girl, $mixed) {
        if ($boy){
            return "Masculin";
        } else if ($girl) {
            return "Féminin";
        } else if ($mixed) {
            return "Mixte";
        }
        return;
    }

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