<?php

namespace Eventus\Admin\Business;

class Twig {
    function __construct() {        
		add_filter('timber/twig', array($this, 'new_twig_filters'));	
		add_filter('timber/twig', array($this, 'new_twig_functions'));	
    }    
	
	function new_twig_filters($twig) {
		$twig->addExtension(new \Twig\Extension\StringLoaderExtension());
		$twig->addFilter(new \Twig_SimpleFilter('toProperText', array($this, 'toProperText')));
		return $twig;
    }   

    function new_twig_functions($twig) {
		$twig->addFunction(new \Twig_Function('getSexIco', array($this, 'getSexIco')));
		return $twig;
    } 
    
    
    /**
    * Get escaped text without double anti slash and ready to be put in html
    *
    * @param string     Text to be modified
    * @return string    The icon(s)
    * @access protected
    */
    function toProperText($text){
        return htmlspecialchars(stripcslashes($text));
    }

    
    /**
    * Get sex icon of a team
    *
    * @param bool       Is boy ?
    * @param bool       Is girl ?
    * @param bool       Is mixed ?
    * @return string    The icon
    * @access protected
    */
    function getSexIco($boy, $girl, $mixed){
        if ($boy){
            return '<img draggable="false" class="emoji" alt="♂" src="https://s.w.org/images/core/emoji/11/svg/2642.svg">';
        } else if ($girl){
            return '<img draggable="false" class="emoji" alt="♀" src="https://s.w.org/images/core/emoji/11/svg/2640.svg">';
        } else if ($mixed){ 
            return '<img draggable="false" class="emoji" alt="♂" src="https://s.w.org/images/core/emoji/11/svg/2642.svg"><img draggable="false" class="emoji" alt="♀" src="https://s.w.org/images/core/emoji/11/svg/2640.svg">';
        }	
        return;
    }
}

?>