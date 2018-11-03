<?php

class TeamScreen extends MasterScreen {
    private static $imgId = 0;
    private static $_instance;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new TeamScreen();
        }
        return self::$_instance;
    }
		
    private function __construct() {  
        wp_enqueue_media();     
        add_action( 'admin_footer', array($this, 'media_selector_print_scripts') );
    }

    function display(){
        if (isset($_GET['teamId'])){  
            $team = TeamDAO::getInstance()->getTeamById($_GET['teamId']);
            TeamScreen::$imgId = $team->getImg();
            if (!$team->getId()) {
                echo "<h2>Erreur : L'équipe n'a pas pu être trouvée...</h2>";
                return;
            }            
    	} else {
            $team = new Team(null, "", "", 0, 0, 0, 0, 0, "", "", null);
        }
	    ?>
        <div class='wrap'>
        	<h1 class="wp-heading-inline">
                <?php 
                    echo ($team->getName() ? $team->getName() : 'Nouvelle équipe').' '.$this->getSexIco($team->getBoy(), $team->getGirl(), $team->getMixed())         
                ?>                
            </h1> 
            <?php       
                if ($team->getId()) { ?>
                    <a href="<?php "admin.php?page=eventus&action=matchs&teamId=".$team->getId(); ?>"  class="page-title-action">Matchs</a> 
                <?php
                }
                echo $this->showNotice(); 
            ?>
            <h2><?php echo $team->getClub() ? $team->getClub()->getName() : ''; ?></h2>
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
                                                data-boy="<?php echo $club->getBoy()?>" 
                                                data-girl="<?php echo $club->getGirl()?>" 
                                                data-mixed="<?php echo $club->getMixed()?>"
                                            >
                                            <?php echo $club->getName()?>
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
                                        <input type="radio" value="h" name="sexe" <?php if ($team->getBoy()){ echo "checked='1'"; } ?> title="Masculin" required disabled/>
                                        Masculin
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="radio" value="f" name="sexe" <?php if ($team->getGirl()){ echo "checked='1'"; } ?> title="Féminin" disabled/>
                                        Féminin
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="radio" value="m" name="sexe" <?php if ($team->getMixed()){ echo "checked='1'"; } ?> title="Mixte" disabled/>
                                        Mixte
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='nom'>Nom<span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='nom' id='nom' value='<?php echo stripcslashes($team->getName()) ?>' class='regular-text' type='text' required title="Nom" placeholder="Nom">
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='url' name="url">Lien des résultats du championnat</label>
                                </th>
                                <td>
                                    <input name='url' id='url' value='<?php echo stripcslashes($team->getUrl()) ?>' class='regular-text' type='url' title="Lien des résultats du championnat" placeholder="Lien des résultats du championnat">
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
                                    <input id="delete_image_button" type="button" class="button" value="Supprimer l'image" />
                                    <input id='image_attachment_id' type='hidden' name='img' disabled value='<?php echo $team->getImg() ?>'>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </div>
				<input type='hidden' name='teamId' value="<?php echo $team->getId(); ?>">	

                <?php if($team->getId()) { ?>
	                <button type='button' class='button-primary ico ico-add' onclick="location.href='admin.php?page=eventus?page=eventus&action=team'">Ajouter une équipe</button>
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

    function media_selector_print_scripts() {
        ?><script type='text/javascript'>
            jQuery( document ).ready( function( $ ) {
                // Uploading files
                var file_frame;
                var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
                var set_to_post_id = <?php echo TeamScreen::$imgId ? TeamScreen::$imgId : 0; ?>; // Set this
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