<?php

namespace Eventus\Admin\Screens;

/**
* MasterScreen is a parent class for all Screen
*
* @package  Admin/Screens
* @access   public
*/
abstract class MasterScreen {    	
    protected function __construct() {  
        wp_register_script('commonJs', plugin_dir_url( __FILE__ ).'/../../js/common.js', '', '', true); 
        wp_localize_script('commonJs', 'translations', 
            array(                
                'defMessage' => __('This action is irreversible. Do you really want to delete the element?', 'eventus' ),
                'loading' => __('Loading in progress....', 'eventus' )
            )
        );
        wp_enqueue_script('commonJs');
	}
    /**
    * Function to display the screen
    *
    * @return void
    * @access public
    */	
    public function display(){
        echo "<div class='wrap'><h1 class='wp-heading-inline'>".__('Hello There', 'eventus')."</h1></div>";
        return;
    }
    
    /**
    * Show notice when action post has been done
    *
    * @return string    The notice message
    * @access protected
    */
    protected function showNotice(){
        $notices = array(
            'succesSyncMatch'=>array(
                'state' => "success", 
                'str'   => __('The match data has been synchronized.', 'eventus')
            ), 
            'warningSyncMatch'=>array(
                'state' => "warning", 
                'str'   => __('The match data was synchronized well, despite some errors. For more information, see ', 'eventus') . ' <a href="admin.php?page=eventus_logs">' . __('the logs', 'eventus') .'</a>.'
            ),
            'succesDelMatch'=>array(
                'state' => "success", 
                'str'   => __('The matches have been deleted.', 'eventus')
            ), 
            'succesUpMatch'=>array(
                'state' => "success", 
                'str'   => __('The matches have been updated.', 'eventus')
            ),  
            'succesUpHoursMatch'=>array(
                'state' => "success", 
                'str'   => __('The appointment times have been updated.', 'eventus')
            ),   
            'warningUpHoursMatch'=>array(
                'state' => "warning", 
                'str'   => __('The appointment times have been updated, despite some errors. For more information, see ', 'eventus') . ' <a href="admin.php?page=eventus_logs">' . __('the logs', 'eventus') .'</a>'
            ),  
            'succesUpTeam'=>array(
                'state' => "success", 
                'str'   => __('The team has been well updated.', 'eventus')
            ), 
            'succesNewTeam'=>array(
                'state' => "success", 
                'str'   => __('The team has been added well.', 'eventus')
            ),    
            'errorUpTeam'=>array(
                'state' => "error", 
                'str'   => __('The team could not be modified. Some fields are missing.', 'eventus')
            ), 
            'errorNewTeam'=>array(
                'state' => "error", 
                'str'   => __('The team could not be added. Some fields are missing.', 'eventus')
            ), 
            'succesDelTeam'=>array(
                'state' => "success", 
                'str'   => __('The team has been removed.', 'eventus')
            ), 
            'succesDelTeams'=>array(
                'state' => "success", 
                'str'   => __('The teams have been deleted.', 'eventus')
            ),  
            'succesUpClub'=>array(
                'state' => "success", 
                'str'   => __('The club has been updated.', 'eventus')
            ),
            'succesNewClub'=>array(
                'state' => "success", 
                'str'   => __('The club has been well added.', 'eventus')
            ),  
            'errorUpClub'=>array(
                'state' => "error", 
                'str'   => __('The club could not be modified. Some fields are missing.', 'eventus')
            ), 
            'errorNewClub'=>array(
                'state' => "error", 
                'str'   => __('The club could not be added. Some fields are missing.', 'eventus')
            ), 
            'succesDelClub'=>array(
                'state' => "success", 
                'str'   => __('The club has been deleted.', 'eventus')
            ), 
            'succesDelLog'=>array(
                'state' => "success", 
                'str'   => __('The logs have been deleted.', 'eventus')
            ), 
            'succesReset'=>array(
                'state' => "success", 
                'str'   => __('The plugin has been reset.', 'eventus')
            ), 
            'succesUpSet'=>array(
                'state' => "success", 
                'str'   => __('The parameters have been updated.', 'eventus')
            ),
            'noMapApiKey'=>array(
                'state' => "error", 
                'str'   => __('Please specify your Google Map API key in', 'eventus') . ' <a href="admin.php?page=eventus_admin">' . __('the parameters', 'eventus') .'</a>.'
            ),
            'succesMultiIcs'=>array(
                'state' => "success", 
                'str'   => "Les calendriers ont bien été mise à jour."
            ),
            'succesOneIcs'=>array(
                'state' => "success", 
                'str'   => "Le calendrier a bien été mise à jour."
            ),
            'succesDelIcs'=>array(
                'state' => "success", 
                'str'   => "Les calendriers ont bien été supprimé."
            )
        );
        if ($_GET['message'] && $notices[$_GET['message']]) {
            return '<div class="notice notice-'.$notices[$_GET['message']]['state'].' is-dismissible"><p><strong>'.$notices[$_GET['message']]['str'].'</strong></p></div>'; 
        }
        return;
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
    protected function getSexIco($boy, $girl, $mixed){
        if ($boy){
            return '<img draggable="false" class="emoji" alt="♂" src="https://s.w.org/images/core/emoji/11/svg/2642.svg">';
        } else if ($girl){
            return '<img draggable="false" class="emoji" alt="♀" src="https://s.w.org/images/core/emoji/11/svg/2640.svg">';
        } else if ($mixed){ 
            return '<img draggable="false" class="emoji" alt="♂" src="https://s.w.org/images/core/emoji/11/svg/2642.svg"><img draggable="false" class="emoji" alt="♀" src="https://s.w.org/images/core/emoji/11/svg/2640.svg">';
        }	
        return;
    }
    
    /**
    * Get escaped text without double anti slash and ready to be put in html
    *
    * @param string     Text to be modified
    * @return string    The icon(s)
    * @access protected
    */
    protected function toProperText($text){
        return htmlspecialchars(stripcslashes($text));
    }
}

?>