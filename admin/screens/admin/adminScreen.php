<?php
/**
* AdminScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class AdminScreen extends MasterScreen {
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
            <h1 class="wp-heading-inline">Administration</h1>
            <hr class="wp-header-end">
            <?php  
                echo $this->showNotice(); 
            if(current_user_can('administrator')) {  
                ?>  
                <h2>Paramètres</h2>  
                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method='post'>     
                    <div>
                        <table class='form-table'>
                            <tbody>
                                <tr>
                                    <th scope='row'>
                                        <label for='mapApiKey'>Clé Api Google Map<span class="required">*</span></label>
                                    </th>
                                    <td>
                                        <input name='mapApiKey' id='mapApiKey' value='<?php echo get_option("eventus_mapapikey");?>' class='regular-text' type='text' required title="Clé Api" placeholder="Clé Api">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="action" value="majSettings" class="button-primary ico ico-save">Enregistrer les modifications</button>
                </form>   

                <br>
                <hr/>
                <br>

                <h2>Actions de réinitialisations <img draggable="false" class="emoji" alt="⚠" src="https://s.w.org/images/core/emoji/11/svg/26a0.svg"></h2>  
                <br>     
                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
                    <button type="submit" name="action" value="delMatch" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger tous les matchs ?')" class="button-primary ico ico-del">Purger tous les matchs</button>
                    <button type="submit" name="action" value="delTeam" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger toutes les équipes ?')" class="button-primary ico ico-del">Purger toutes les équipes</button>
                    <button type="submit" name="action" value="delClub" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger tous les clubs ?')" class="button-primary ico ico-del">Purger tous les club</button>                
                    <button type="submit" name="action" value="resetEventus" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment réinitialiser Eventus ?')" class="button-primary ico ico-reset">Réinitialiser Eventus</button>
                </form>

                
                <?php 
            } else {
                ?>
                <p>Vous n'êtes pas autorisé à accéder à cette page.</p>
                <?php
            }
            $pluginData = get_plugin_data(WP_PLUGIN_DIR.'/eventus/eventus.php');
            ?>

            <br><br><br>

            <h3>Crédits</h3>  
                <p>
                    <b><?php echo $pluginData['Name']; ?></b>
                    (v. <b><?php echo $pluginData['Version']; ?></b>) 
                    par 
                    <a target="_blank" href="<?php echo $pluginData['AuthorURI']; ?>">
                        <?php echo $pluginData['AuthorName']; ?>
                    </a>
                     - 
                    <a target="_blank" href="https://github.com/KirianCaumes/eventus">
                        GitHub
                    </a>
                </p>
                </p>
                    En cas de soucis, plusieurs moyens de me contacter sont disponibles sur mon site ou via mon GitHub.
                <p>
        </div>
        <?php
    }
}

?>