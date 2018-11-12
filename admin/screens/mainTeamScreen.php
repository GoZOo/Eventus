<?php
/**
* MainTeamScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class MainTeamScreen extends MasterScreen {
    /**
    * @var int              $imgId      Store the id of the team's image
    * @var MainTeamScreen   $_instance  Var use to store an instance
    */
    private static $imgId = 0;
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
		
    private function __construct() {  
        wp_enqueue_media();     
        add_action( 'admin_footer', array($this, 'media_selector_print_scripts') );
    }

    /**
    * Function to display the screen
    *
    * @return void
    * @access public
    */
    function display(){
        if (isset($_GET['teamId'])){  
            $team = TeamDAO::getInstance()->getTeamById($_GET['teamId']);
            MainTeamScreen::$imgId = $team->getImg();
            if (!$team->getId()) {
                echo "<h2>Erreur : L'équipe n'a pas pu être trouvée...</h2>";
                return;
            }            
    	} else {
            $team = new Team(null, "", "", "", 0, 0, 0, 0, 0, "", "", null);
        }
	    ?>
        <div class='wrap'>
        	<h1 class="wp-heading-inline">
                <?php 
                    echo ($team->getName() ? $this->toProperText($team->getName()) : 'Nouvelle équipe').' '.$this->getSexIco($team->getBoy(), $team->getGirl(), $team->getMixed())         
                ?>                
            </h1> 
            <?php       
                if ($team->getId()) { ?>
                    <a href="<?php echo "admin.php?page=eventus&action=matchs&teamId=".$team->getId(); ?>"  class="page-title-action">Matchs</a> 
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
                                    <label for="club">Club<span class="required">*</span></label>
                                </th>
                                <td>
                                <select name="club" id='club'>
                                    <?php
                                        $myClubs = ClubDAO::getInstance()->getAllClubs();
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
                                    <label>Sexe<span class="required">*</span></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="radio" value="h" name="sexe" title="Masculin" <?php echo ($team->getBoy() ? 'checked=\'1\'' : ''); ?> required/>
                                        Masculin
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="radio" value="f" name="sexe" title="Féminin" <?php echo ($team->getGirl() ? 'checked=\'1\'' : ''); ?>/>
                                        Féminin
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="radio" value="m" name="sexe" title="Mixte" <?php echo ($team->getMixed() ? 'checked=\'1\'' : ''); ?>/>
                                        Mixte
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='nom'>Nom<span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='nom' id='nom' value='<?php echo $this->toProperText($team->getName()) ?>' class='regular-text' type='text' required title="Nom" placeholder="Nom">
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='urlOne' name="urlOne">Lien n°1 des résultats du championnat</label>
                                </th>
                                <td>
                                    <input name='urlOne' id='urlOne' value='<?php echo $this->toProperText($team->getUrlOne()) ?>' class='regular-text' type='url' title="Lien n°1 des résultats du championnat" placeholder="Lien n°1 des résultats du championnat">
                                </td>
                            </tr>
                            <tr <?php echo !$team->getUrlOne() ? "style='display: none'" : ''?>>
                                <th scope='row'>
                                    <label for='urlTwo' name="urlTwo">Lien n°2 des résultats du championnat</label>
                                </th>
                                <td>
                                    <input name='urlTwo' id='urlTwo' value='<?php echo $this->toProperText($team->getUrlTwo()) ?>' class='regular-text' type='url' title="Lien n°2 des résultats du championnat" placeholder="Lien n°2 des résultats du championnat">
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='time'>Temps pour RDV d'avant match (en minutes)<span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='time' id='time' value='<?php echo ($team->getTime() ? stripcslashes($team->getTime()) : '45'); ?>' class='regular-text' type='number' min='0' required title="Temps pour RDV d'avant match (en minutes)" placeholder="Temps pour RDV d'avant match (en minutes)">
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='img'>Image par défaut</label>
                                </th>
                                <td>                                 
                                    <input id="upload_image_button" type="button" class="button" value="Sélectionnez une image" />
                                    <input id="delete_image_button" type="button" class="button" value="Supprimer l'image" disabled/>
                                    <input id='image_attachment_id' type='hidden' name='img' disabled value='<?php echo $team->getImg() ?>'>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </div>
                <input type='hidden' name='teamId' value="<?php echo $team->getId(); ?>">	
                <input type="submit" name="action" value="majTeam" class="hiddenSubmit">
                <br>

                <?php if($team->getId()) { ?>
	                <button type='button' class='button-primary ico ico-add' onclick="location.href='admin.php?page=eventus&action=team'">Ajouter une équipe</button>
                    <button type="submit" name="action" value="delTeam" class="button-primary ico ico-del" onclick="return validate(null)" >Supprimer l'équipe</button>           
                    <br />
                    
                    <?php //<button type="submit" name="action" value="syncMatch" class="button-primary ico ico-sync">Synchroniser les données des matchs</button><button type="submit" name="action" value="delMatch" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger les matchs de l\équipe ?')" class="button-primary ico ico-del">Purger les matchs de l'équipe</button> ?>
                <?php } ?>
                <br/>
                <button type="submit" name="action" value="majTeam" class="button-primary ico ico-save">Enregistrer les modifications</button>
            </form>
        </div>
        <?php
    }   

    /**
    * Function use to print script that allowed user to select an image
    *
    * @return void
    * @access public
    */
    function media_selector_print_scripts() {
        ?><script type='text/javascript'>
            jQuery( document ).ready( function( $ ) {
                // Uploading files
                var file_frame;
                var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                var set_to_post_id = <?php echo MainTeamScreen::$imgId ? MainTeamScreen::$imgId : 0; ?>; // Set this
                jQuery('#upload_image_button').on('click', function( event ){
                    event.preventDefault();
                    // If the media frame already exists, reopen it.
                    if ( file_frame ) {
                        // Set the post ID to what we want
                        file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                        // Open frame
                        file_frame.open();
                        return;
                    } else {
                        // Set the wp.media post id so the uploader grabs the ID we want when initialised
                        wp.media.model.settings.post.id = set_to_post_id;
                    }
                    // Create the media frame.
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: 'Sélectionnez l\'image par défaut de l\'équipe',
                        button: {
                            text: 'Utiliser cette image',
                        },
                        multiple: false	// Set to true to allow multiple files to be selected
                    });
                    // When an image is selected, run a callback.
                    file_frame.on( 'select', function() {
                        // We set multiple to false so only get one image from the uploader
                        attachment = file_frame.state().get('selection').first().toJSON();
                        // Do something with attachment.id and/or attachment.url here
                        $( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
                        $( '#image_attachment_id' ).val( attachment.id );
                        // Restore the main post ID
                        wp.media.model.settings.post.id = wp_media_post_id;
                    });
                        // Finally, open the modal
                        file_frame.open();
                });
                // Restore the main ID when the add media button is pressed
                jQuery( 'a.add_media' ).on( 'click', function() {
                    wp.media.model.settings.post.id = wp_media_post_id;
                });
            });
        </script><?php
    }
}

?>