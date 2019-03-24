<?php

namespace Eventus\Admin\Screens\Main;
use Eventus\Admin\Screens as Screens;
use Eventus\Includes\Datas as DAO;
use Eventus\Includes\Entities as Entities;

/**
* MainTeamScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class MainTeamScreen extends Screens\MasterScreen {
    /**
    * @var MainTeamScreen   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return MainTeamScreen
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new MainTeamScreen();
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
    	wp_enqueue_script('teamJs', plugin_dir_url( __FILE__ ).'/../../../js/screens/teamDetailScreen.js', '', '', true); 
    }

    /**
    * Function to display the screen
    *
    * @return void
    * @access public
    */
    function display(){
        if (isset($_GET['teamId'])){  
            $team = DAO\TeamDAO::getInstance()->getTeamById($_GET['teamId']);
            if (!$team->getId()) {
                echo "<h2>Erreur : L'équipe n'a pas pu être trouvée...</h2>";
                return;
            }            
    	} else {
            $team = new Entities\Team(null, "", "", "", 0, 0, 0, 0, 0, "", "", null);
        }
	    ?>
        <div class='wrap'>
        	<h1 class="wp-heading-inline">
                <?php 
                    echo ($team->getName() ? $this->toProperText($team->getName()) : _e('New team', 'eventus')).' '.$this->getSexIco($team->getBoy(), $team->getGirl(), $team->getMixed())         
                ?>                
            </h1> 
            <?php       
                if ($team->getId()) { ?>
                    <a href="<?php echo "admin.php?page=eventus&action=matchs&teamId=".$team->getId(); ?>"  class="page-title-action"><?php _e('Matches', 'eventus') ?></a> 
                    <hr class="wp-header-end">
                <?php
                }
                echo $this->showNotice(); 
            ?>
            <h2><?php echo $team->getClub() ? $this->toProperText($team->getClub()->getName()) : ''; ?></h2>
        	<form action="<?php echo admin_url( 'admin-post.php' ) ?>" method='post'>     
                <div>
                    <table class='form-table'>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="club"><?php _e('Club', 'eventus') ?><span class="required">*</span></label>
                                </th>
                                <td>
                                <select name="club" id='club'>
                                    <?php
                                        $myClubs = DAO\ClubDAO::getInstance()->getAllClubs();
                                        foreach ($myClubs as $key => $club) { ?>
                                            <option 
                                                value="<?php echo $club->getId()?>" 
                                                <?php if($team->getClub() && $team->getClub()->getId() == $club->getId()) { echo 'selected'; } ?>
                                            >
                                            <?php echo $this->toProperText($club->getName())?>
                                            </option>
                                        <?php
                                        }
                                    ?>
                                </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label><?php _e('Sex', 'eventus') ?><span class="required">*</span></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="radio" value="h" name="sexe" title="<?php _e('Male', 'eventus') ?>" <?php echo ($team->getBoy() ? 'checked=\'1\'' : ''); ?> required/>
                                        <?php _e('Male', 'eventus') ?>
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="radio" value="f" name="sexe" title="<?php _e('Female', 'eventus') ?>" <?php echo ($team->getGirl() ? 'checked=\'1\'' : ''); ?>/>
                                        <?php _e('Female', 'eventus') ?>                                        
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="radio" value="m" name="sexe" title="<?php _e('Mixed', 'eventus') ?>" <?php echo ($team->getMixed() ? 'checked=\'1\'' : ''); ?>/>
                                        <?php _e('Mixed', 'eventus') ?>                                        
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='nom'><?php _e('Name', 'eventus') ?><span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='nom' id='nom' value='<?php echo $this->toProperText($team->getName()) ?>' class='regular-text' type='text' required title="<?php _e('Name', 'eventus') ?>" placeholder="<?php _e('Name', 'eventus') ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='urlOne' name="urlOne"><?php _e('Link n°1 of the championship results', 'eventus') ?></label>
                                </th>
                                <td>
                                    <input name='urlOne' id='urlOne' value='<?php echo $this->toProperText($team->getUrlOne()) ?>' class='regular-text' type='url' title="<?php _e('Link n°1 of the championship results', 'eventus') ?>" placeholder="<?php _e('Link n°1 of the championship results', 'eventus') ?>">
                                    <a type='button' class='button-primary ico ico-link ico-no-text' href='<?php echo $this->toProperText($team->getUrlOne()) ?>' target='_blank'></a>
                                </td>
                            </tr>
                            <tr <?php echo !$team->getUrlOne() ? "style='display: none'" : ''?>>
                                <th scope='row'>
                                    <label for='urlTwo' name="urlTwo"><?php _e('Link n°2 of the championship results', 'eventus') ?></label>
                                </th>
                                <td>
                                    <input name='urlTwo' id='urlTwo' value='<?php echo $this->toProperText($team->getUrlTwo()) ?>' class='regular-text' type='url' title="<?php _e('Link n°2 of the championship results', 'eventus') ?>" placeholder="<?php _e('Link n°2 of the championship results', 'eventus') ?>">
                                    <a type='button' class='button-primary ico ico-link ico-no-text' href='<?php echo $this->toProperText($team->getUrlTwo()) ?>' target='_blank'></a>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='time'><?php _e('Time for pre-match RDV (in minutes)', 'eventus') ?><span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='time' id='time' value='<?php echo ($team->getTime() ? stripcslashes($team->getTime()) : '45'); ?>' class='regular-text' type='number' min='0' required title="<?php _e('Time for pre-match RDV (in minutes)', 'eventus') ?>" placeholder="<?php _e('Time for pre-match RDV (in minutes)', 'eventus') ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='img'><?php _e('Image', 'eventus') ?></label>
                                </th>
                                <td>                                 
                                    <input id="upload_image_button" type="button" class="button" value="<?php _e('Select an image', 'eventus') ?>" />
                                    <input id="delete_image_button" type="button" class="button" value="<?php _e('Delete the image', 'eventus') ?>" disabled/>
                                    <input id='image_attachment_id' type='hidden' name='img' value='<?php echo $team->getImg() ?>'>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </div>
                <input type='hidden' name='teamId' value="<?php echo $team->getId(); ?>">	
                <input type="submit" name="action" value="majTeam" class="hiddenSubmit">
                <br>

                <?php if($team->getId()) { ?>
	                <!-- <a type='button' class='button-primary ico ico-add' href='admin.php?page=eventus&action=team'><?php _e('Add a team', 'eventus') ?></a> -->
                    <button type="submit" name="action" value="delTeam" class="button-primary ico ico-del" onclick="return validate(null)" ><?php _e('Delete the team', 'eventus') ?></button>           
                    <br />
                    
                    <?php //<button type="submit" name="action" value="syncMatch" class="button-primary ico ico-sync">Synchroniser les données des matchs</button><button type="submit" name="action" value="delMatch" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger les matchs de l\équipe ?')" class="button-primary ico ico-del">Purger les matchs de l'équipe</button> ?>
                <?php } ?>
                <br/>
                <button type="submit" name="action" value="majTeam" class="button-primary ico ico-save"><?php _e('Save changes', 'eventus') ?></button>
            </form>
        </div>
        <?php
    }  
}

?>