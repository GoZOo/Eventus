<?php

namespace Eventus\Admin\Screens\Club;
use Eventus\Admin\Screens as Screens;
use Eventus\Includes\Datas as DAO;
use Eventus\Includes\Entities as Entities;

/**
* ClubDetailScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class ClubDetailScreen extends Screens\MasterScreen {
    /**
    * @var ClubDetailScreen   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return ClubDetailScreen
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new ClubDetailScreen();
        }
        return self::$_instance;
    }
		
    protected function __construct() {
        parent::__construct();
        wp_enqueue_media();    
        wp_register_script('upImgJs', plugin_dir_url( __FILE__ ).'/../../../js/uploadImg.js', '', '', true); 
        wp_localize_script('upImgJs', 'translations', 
            array(                
                'selectAnImg' => __('Select the default team image', 'eventus' ),
                'selectImg' => __('Use this image', 'eventus' )
            )
        );
        wp_enqueue_script('upImgJs');
    }

    /**
    * Function to display the screen
    *
    * @return void
    * @access public
    */
    function display(){
        if (isset($_GET['clubId'])){  
            $club = DAO\ClubDAO::getInstance()->getClubById($_GET['clubId']);
            if (!$club->getId()) {
                echo "<h2>".__('Error: The club could not be found...', 'eventus')."</h2>";
                return;
            }            
    	} else {
            $club = new Entities\Club(null, "", "", "");
        }
	    ?>
        <div class='wrap'>
        	<h1 class="wp-heading-inline">
                <?php 
                    echo ($club->getName() ? $this->toProperText($club->getName()) : __('New club', 'eventus'));         
                ?>                
            </h1> 
            <hr class="wp-header-end">
            <?php      
                echo $this->showNotice(); 
            ?>
            <h2></h2>
        	<form action="<?php echo admin_url( 'admin-post.php' ) ?>" method='post'>     
                <div>
                    
                    <table class='form-table'>
                        <tbody>
                            <tr>
                                <th scope='row'>
                                    <label for='nom'><?php _e('Name', 'eventus') ?><span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='nom' id='nom' value="<?php echo $this->toProperText($club->getName()) ?>" class='regular-text' type='text' required title="<?php _e('Name', 'eventus') ?>" placeholder="<?php _e('Name', 'eventus') ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='chaine'><?php _e('String', 'eventus') ?><span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='chaine' id='chaine' aria-describedby='tagline-description' value='<?php echo $this->toProperText($club->getString()) ?>' class='regular-text' type='text' required title="<?php _e('String', 'eventus') ?>" placeholder="<?php _e('String', 'eventus') ?>">
                                    <p class='description' id='tagline-description'><?php _e('String that the algorithm will search on the FFHB website', 'eventus') ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='adresse' ><?php _e('Address of the gym', 'eventus') ?><span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='adresse' id='adresse' value='<?php echo $this->toProperText($club->getAddress()) ?>' class='regular-text' type='text' required title="<?php _e('Address', 'eventus') ?>" placeholder="<?php _e('Address', 'eventus') ?>">
                                    <p class='description' id='tagline-description'><?php _e('Address used to calculate match rdv schedules', 'eventus') ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='img'><?php _e('Image', 'eventus') ?></label>
                                </th>
                                <td>                                 
                                    <input id="upload_image_button" type="button" class="button" value="<?php _e('Select an image', 'eventus') ?>" />
                                    <input id="delete_image_button" type="button" class="button" value="<?php _e('Delete the image', 'eventus') ?>" disabled/>
                                    <input id='image_attachment_id' type='hidden' name='img' value='<?php echo $club->getImg() ?>'>
                                </td>
                            </tr>
                        </tbody>
                    </table> 

                </div>
				<input type='hidden' name='clubId' value="<?php echo $club->getId(); ?>">	
                <input type="submit" name="action" value="eventus_majClub" class="hiddenSubmit">
                <br>

                <?php if($club->getId()) { ?>
	                <a type='button' class='button-primary ico ico-add' href='admin.php?page=eventus_club&action=club'><?php _e('Add a club', 'eventus') ?></a>
                    <button type="submit" name="action" value="eventus_delClub" class="button-primary ico ico-del" onclick="return validate('<?php _e('This action is irreversible. Do you really want to delete the club?', 'eventus') ?>')" ><?php _e('Delete the club', 'eventus') ?></button>           
                    <br />
                    
                <?php } ?>
                <br/>
                <button type="submit" name="action" value="eventus_majClub" class="button-primary ico ico-save"><?php _e('Save changes', 'eventus') ?></button>
            </form>
        </div>
        <?php
    }   
}

?>