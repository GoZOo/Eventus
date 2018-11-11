<?php
/**
* ClubDetailScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class ClubDetailScreen extends MasterScreen {
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
		
    private function __construct() {
        wp_enqueue_script('clubJs', plugin_dir_url( __FILE__ ).'/../../js/club.js', '', '', true); 
    }

    /**
    * Function to display the screen
    *
    * @return void
    * @access public
    */
    function display(){
        if (isset($_GET['clubId'])){  
            $club = ClubDAO::getInstance()->getClubById($_GET['clubId']);
            if (!$club->getId()) {
                echo "<h2>Erreur : Le club n'a pas pu être trouvé...</h2>";
                return;
            }            
    	} else {
            $club = new Club(null, "", "", 0, 0, 0, "");
        }
	    ?>
        <div class='wrap'>
        	<h1 class="wp-heading-inline">
                <?php 
                    echo ($club->getName() ? $club->getName() : 'Nouveau club').' '.$this->getSexIcoClub($club->getBoy(), $club->getGirl(), $club->getMixed())         
                ?>                
            </h1> 
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
                                    <label for='nom' data-name="nom">Nom<span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='nom' id='nom' value='<?php echo stripcslashes($club->getName()) ?>' class='regular-text' type='text' required title="Nom" placeholder="Nom">
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='chaine' data-name="chaine">Chaîne de caractères<span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='chaine' id='chaine' aria-describedby='tagline-description' value='<?php echo stripcslashes($club->getString()) ?>' class='regular-text' type='text' required title="Chaîne de caractères" placeholder="Chaîne de caractères">
                                    <p class='description' id='tagline-description'>Chaîne de caractères que l'algorithme vas chercher sur le site de la FFHB</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label for='adresse' data-name="chaine">Adresse de la salle<span class="required">*</span></label>
                                </th>
                                <td>
                                    <input name='adresse' id='adresse' value='<?php echo stripcslashes($club->getAddress()) ?>' class='regular-text' type='text' required title="Adresse" placeholder="Adresse">
                                    <p class='description' id='tagline-description'>Adresse utilisé pour calculer les horaires de rdv des matchs</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope='row'>
                                    <label>Sexe(s)<span class="required">*</span></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" value="1" name="h" <?php if ($club->getBoy()){ echo "checked='1'"; } ?> title="Masculin" required/>
                                        Masculin
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="checkbox" value="1" name="f" <?php if ($club->getGirl()){ echo "checked='1'"; } ?> title="Féminin" required/>
                                        Féminin
                                    </label>
                                    &nbsp;&nbsp;
                                    <label>
                                        <input type="checkbox" value="1" name="m" <?php if ($club->getMixed()){ echo "checked='1'"; } ?> title="Mixte" required/>
                                        Mixte
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table> 

                </div>
				<input type='hidden' name='clubId' value="<?php echo $club->getId(); ?>">	
                <input type="submit" name="action" value="majClub" class="hiddenSubmit">

                <?php if($club->getId()) { ?>
	                <button type='button' class='button-primary ico ico-add' onclick="location.href='admin.php?page=eventus_club&action=club'">Ajouter un club</button>
                    <button type="submit" name="action" value="delClub" class="button-primary ico ico-del" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment supprimer le club ?')" >Supprimer le club</button>           
                    <br />
                    
                <?php } ?>
                <br/>
                <button type="submit" name="action" value="majClub" class="button-primary ico ico-save">Enregistrer les modifications</button>
            </form>
        </div>
        <?php
    }   
}

?>