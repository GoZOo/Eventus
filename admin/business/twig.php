<?php

namespace Eventus\Admin\Business;

/**
* Twig is a class used to add more content to twig
*
* @package  Admin/Business
* @access   public
*/
class Twig {
    function __construct() {        
		add_filter('timber/twig', array($this, 'new_twig_filters'));	
		add_filter('timber/twig', array($this, 'new_twig_functions'));	
    }    
    
    /**
    * Callback to had new twig filter
    *
    * @return Twig    twig
    * @access public
    */
	function new_twig_filters($twig) {
		$twig->addExtension(new \Twig\Extension\StringLoaderExtension());
		$twig->addFilter(new \Twig_SimpleFilter('toProperText', array($this, 'toProperText')));
		return $twig;
    }   

    /**
    * Callback to had new twig function
    *
    * @return Twig    twig
    * @access public
    */
    function new_twig_functions($twig) {
		$twig->addFunction(new \Twig_Function('getSexIco', array($this, 'getSexIco')));
		$twig->addFunction(new \Twig_Function('generateId', array($this, 'generateId')));
		return $twig;
    } 

    /**
    * Generate a uniq id
    *
    * @return string    Id
    * @access public
    */
    function generateId(){
        return substr(md5(uniqid(rand(), true)), 2, 9);
    }
        
    /**
    * Get escaped text without double anti slash and ready to be put in html
    *
    * @param string     Text to be modified
    * @return string    Text modified
    * @access public
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
    * @access public
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