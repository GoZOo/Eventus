<?php

namespace Eventus\Admin\Screens\Admin;
use Eventus\Admin\Screens as Screens;

/**
* AdminScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class AdminScreen extends Screens\MasterScreen {
    /**
    * @var AdminScreen   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return AdminScreen
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new AdminScreen();
        }
        return self::$_instance;
    }

    protected function __construct() {
		parent::__construct();
		wp_enqueue_script('matchJs', plugin_dir_url( __FILE__ ).'/../../../js/screens/adminScreen.js', '', '', true); 
	}	
    
    /**
    * Function to display the screen
    *
    * @return void
    * @access public
    */
    function display(){
        ?>
        <div class='wrap'>
            <h1 class="wp-heading-inline"><?php _e('Administration', 'eventus') ?></h1>
            <hr class="wp-header-end">
            <?php  
                echo $this->showNotice(); 
            if(current_user_can('administrator')) {  
                ?>  
                <h2><?php _e('Paramètres', 'eventus') ?></h2>  
                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method='post'>     
                    <div>
                        <table class='form-table'>
                            <tbody>
                                <tr>
                                    <th scope='row'>
                                        <label for='mapApiKey'><?php _e('Google Map API Key', 'eventus') ?><span class="required">*</span></label>
                                    </th>
                                    <td>
                                        <input name='mapApiKey' id='mapApiKey' value='<?php echo get_option("eventus_mapapikey");?>' class='regular-text' type='text' required title="<?php _e('API Key', 'eventus') ?>" placeholder="<?php _e('API Key', 'eventus') ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope='row'>
                                        <label for='emailNotif'><?php _e('Notification email', 'eventus') ?><span class="required">*</span></label>
                                    </th>
                                    <td>
                                        <input name='emailNotif' id='emailNotif' value='<?php echo get_option("eventus_emailnotif");?>' class='regular-text' type='email' title="<?php _e('Notification email', 'eventus') ?>" placeholder="<?php _e('Notification email', 'eventus') ?>">
                                        <p class='description' id='tagline-description'><?php _e('Leave blank if you do not want notifications.', 'eventus') ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope='row'>
                                        <label><?php _e('Reset logs for each email sent', 'eventus') ?><span class="required">*</span></label>
                                    </th>
                                    <td>
                                        <label>
                                            <input type="radio" value="1" name="resetlog" title="<?php _e('Yes', 'eventus') ?>" <?php echo (get_option("eventus_resetlog") ? 'checked=\'1\'' : ''); ?> required/>
                                            <?php _e('Yes', 'eventus') ?>
                                        </label>
                                        &nbsp;&nbsp;
                                        <label>
                                            <input type="radio" value="0" name="resetlog" title="<?php _e('No', 'eventus') ?>" <?php echo (!get_option("eventus_resetlog") ? 'checked=\'1\'' : ''); ?>/>
                                            <?php _e('No', 'eventus') ?>                                        
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="action" value="eventus_majSettings" class="button-primary ico ico-save"><?php _e('Save changes', 'eventus') ?></button>
                </form>   

                <br>
                <hr/>
                <br>

                <h2><?php _e('Reset actions ', 'eventus') ?><img draggable="false" class="emoji" alt="⚠" src="https://s.w.org/images/core/emoji/11/svg/26a0.svg"></h2>  
                <br>     
                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
                    <button type="submit" name="action" value="eventus_delIcs" onclick="return validate('<?php _e('This action is irreversible. Do you really want to purge all the calendars?', 'eventus') ?>')" class="button-primary ico ico-del"><?php _e('Purge all calendars?', 'eventus') ?></button>
                    <button type="submit" name="action" value="eventus_delMatch" onclick="return validate('<?php _e('This action is irreversible. Do you really want to purge all the matches?', 'eventus') ?>')" class="button-primary ico ico-del"><?php _e('Purge all matches', 'eventus') ?></button>
                    <button type="submit" name="action" value="eventus_delTeam" onclick="return validate('<?php _e('This action is irreversible. Do you really want to purge all the teams?', 'eventus') ?>')" class="button-primary ico ico-del"><?php _e('Purge all teams', 'eventus') ?></button>
                    <button type="submit" name="action" value="eventus_delClub" onclick="return validate('<?php _e('This action is irreversible. Do you really want to purge all the clubs?', 'eventus') ?>')" class="button-primary ico ico-del"><?php _e('Purge all clubs', 'eventus') ?></button>                
                    <button type="submit" name="action" value="eventus_resetEventus" onclick="return validate('<?php _e('This action is irreversible. Do you really want to reset Eventus?', 'eventus') ?>')" class="button-primary ico ico-reset"><?php _e('Reset Eventus', 'eventus') ?></button>
                </form>

                
                <?php 
            } else {
                ?>
                <p><?php _e('You are not authorized to access this page.', 'eventus') ?></p>
                <?php
            }
            $pluginData = get_plugin_data(WP_PLUGIN_DIR.'/eventus/eventus.php');
            ?>

            <br>
            <hr/>
            <br>

            <h3><?php _e('Credits', 'eventus') ?></h3>  
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

?>