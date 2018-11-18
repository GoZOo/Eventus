<?php 

namespace Eventus\Admin\Business;
use Eventus\Includes\Datas as DAO;
use Eventus\Includes\Entities as Entities;

/**
* FinEventusWidgetDashboard is a class that add a widget element to Wordpress dashboard's
*
* @access   public
*/
class EventusWidgetDashboard {
    /**
    * @var EventusWidgetDashboard   $_instance  Var use to store an instance
    */
    private static $_instance;
    
    /**
    * Returns an instance of the object
    *
    * @return EventusWidgetDashboard
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new EventusWidgetDashboard();
        }
        return self::$_instance;
    }
    private function __construct() {
		wp_enqueue_style('styleEventus', WP_PLUGIN_URL.'/eventus/admin/css/styles.css'); 
		wp_enqueue_script('jsEventus', WP_PLUGIN_URL.'/eventus/admin/js/common.js', '', '', true); 
    } 
      
    /**
    * Function to display the widget
    *
    * @return void
    * @access public
    */
    public function display(){
        ?>
        <p><?php _e('Find below the information related to the management of Eventus teams and clubs.', 'eventus') ?></p>

        <hr/>

        <div class="eventus-row">
            <div>
                <h3><b><?php _e('Team(s): ', 'eventus') ?><?php echo DAO\TeamDAO::getInstance()->getNumbersTeams()->nbr_teams; ?></b></h3>
                <button class="button-primary ico ico-team" onclick="location.href='admin.php?page=eventus'"><?php _e('See the team(s)', 'eventus') ?></button>
            </div> 
            <div>
                <h3><b><?php _e('Club(s): ', 'eventus') ?><?php echo DAO\ClubDAO::getInstance()->getNumbersClubs()->nbr_clubs; ?></b></h3>
                <button class="button-primary ico ico-club" onclick="location.href='admin.php?page=eventus_club'"><?php _e('See the club(s)', 'eventus') ?></button>
            </div> 
            <div>
                <h3><b><?php _e('Logs: ', 'eventus') ?><?php echo count(file(WP_PLUGIN_URL.'/eventus/finder.log')); ?></b></h3>
                <button class="button-primary ico ico-info" onclick="location.href='admin.php?page=eventus_logs'"><?php _e('See the logs', 'eventus') ?></button>
            </div>
        </div>
        
        <hr/>

        <div class="eventus-row">
            <div>
                <h3><b><?php _e('Last synchronization of matches: ', 'eventus') ?></b><br>
                    <?php 
                        $date = new \DateTime(get_option("eventus_datetimesynch")); 
                        echo $date->format('d/m/Y à H:i:s');
                    ?>
                </h3>
                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">   
                    <div class="myTooltip">
                        <button type="submit" name="action" value="syncMatch" onclick="setLoading(this)" class="button-primary ico ico-sync"><?php _e('Synchronize match data', 'eventus') ?></button>
                        <span class="myTooltiptext"><?php _e('Saves & synchronizes match data with the Federation website.', 'eventus') ?></span>
                    </div> 
                </form>
            </div> 
        </div>

        <hr/>

        <div>
            <?php $pluginData = get_plugin_data(WP_PLUGIN_DIR.'/eventus/eventus.php'); ?>
            <p>
                <b><?php echo $pluginData['Name']; ?></b>
                (v. <b><?php echo $pluginData['Version']; ?></b>) 
                <?php _e('by', 'eventus') ?> 
                <a target="_blank" href="<?php echo $pluginData['AuthorURI']; ?>">
                    <?php echo $pluginData['AuthorName']; ?>
                </a>
                    - 
                <a target="_blank" href="https://github.com/KirianCaumes/eventus">
                    GitHub
                </a>
            </p>
            </p>
                <?php _e('In case of problems, several ways to contact me are available on my website or via my GitHub.', 'eventus') ?>                    
            <p>  
        </div>  
        <?php
    }

}