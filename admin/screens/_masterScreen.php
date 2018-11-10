<?php
/**
* MasterScreen is a parent class for all Screen
*
* @package  Admin/Screens
* @access   public
*/
abstract class MasterScreen {
    /**
    * Function to display the screen
    *
    * @return void
    * @access public
    */	
    public function display(){
        echo "<div class='wrap'><h1 class='wp-heading-inline'>Hello There</h1></div>";
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
                'str'   => "Les données des matchs ont bien été synchronisées."
            ), 
            'warningSyncMatch'=>array(
                'state' => "warning", 
                'str'   => "Les données des matchs ont bien été synchronisées, malgré des erreurs. Pour plus d'informations, consultez <a href=\"admin.php?page=eventus_logs\">les logs</a>"
            ),
            'succesDelMatch'=>array(
                'state' => "success", 
                'str'   => "Les matchs ont bien été supprimés."
            ), 
            'succesUpMatch'=>array(
                'state' => "success", 
                'str'   => "Les matchs ont bien été mis à jour."
            ),  
            'succesUpHoursMatch'=>array(
                'state' => "success", 
                'str'   => "Les heures de rendez-vous ont bien été mises à jour."
            ),   
            'warningUpHoursMatch'=>array(
                'state' => "warning", 
                'str'   => "Les heures de rendez-vous ont bien été mises à jour, malgré des erreurs. Pour plus d'informations, consultez <a href=\"admin.php?page=eventus_logs\">les logs</a>"
            ),  
            'succesUpTeam'=>array(
                'state' => "success", 
                'str'   => "L'équipe à bien été mise à jour."
            ), 
            'succesNewTeam'=>array(
                'state' => "success", 
                'str'   => "L'équipe à bien été ajoutée."
            ),  
            'succesDelTeam'=>array(
                'state' => "success", 
                'str'   => "L'équipe à bien été supprimée."
            ), 
            'succesDelTeams'=>array(
                'state' => "success", 
                'str'   => "Les équipes ont bien été supprimée."
            ),  
            'succesUpClub'=>array(
                'state' => "success", 
                'str'   => "Le club a bien été mis à jour."
            ),
            'succesNewClub'=>array(
                'state' => "success", 
                'str'   => "Le club à bien été ajoutée."
            ),  
            'succesDelClub'=>array(
                'state' => "success", 
                'str'   => "Le club a bien été supprimés."
            ), 
            'succesDelLog'=>array(
                'state' => "success", 
                'str'   => "Les logs ont bien été supprimés."
            ), 
            'succesReset'=>array(
                'state' => "success", 
                'str'   => "Les plugin à bien été réinitialiser."
            ), 
            'succesUpSet'=>array(
                'state' => "success", 
                'str'   => "Les paramètres ont bien été mis à jour."
            ),
            'noMapApiKey'=>array(
                'state' => "error", 
                'str'   => "Veuillez préciser votre clé Api Google Map dans <a href=\"admin.php?page=eventus_admin\">les paramètres</a>."
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
    * Get sex icon of a club
    *
    * @param bool       Is boy ?
    * @param bool       Is girl ?
    * @param bool       Is mixed ?
    * @return string    The icon(s)
    * @access protected
    */
    protected function getSexIcoClub($boy, $girl, $mixed){
        $return = [];
        if ($boy){
            $return[] = '<img draggable="false" class="emoji" alt="♂" src="https://s.w.org/images/core/emoji/11/svg/2642.svg">';
        } 
        if ($girl){
            $return[] = '<img draggable="false" class="emoji" alt="♀" src="https://s.w.org/images/core/emoji/11/svg/2640.svg">';
        } 
        if ($mixed){ 
            $return[] = '<img draggable="false" class="emoji" alt="♂" src="https://s.w.org/images/core/emoji/11/svg/2642.svg"><img draggable="false" class="emoji" alt="♀" src="https://s.w.org/images/core/emoji/11/svg/2640.svg">';
        }	
        return implode("/", $return);
    }
}

?>