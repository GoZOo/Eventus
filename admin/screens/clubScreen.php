<?php

class ClubScreen extends MasterScreen {	
	private static $_instance;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new ClubScreen();
        }
        return self::$_instance;
    }
		
    private function __construct() {  
    	wp_enqueue_script('clubJs', plugin_dir_url( __FILE__ ).'/../../js/club.js', '', '', true);
        wp_enqueue_script('commonJs', plugin_dir_url( __FILE__ ).'/../../js/common.js', '', '', true);  
    }

    function display(){
		?>
		<div class="wrap">
	        <h1 class="wp-heading-inline">Paramétrage des Clubs</h1>
			<?php  
                echo $this->showNotice(); 
            ?> 
	        <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
                <?php	        
                $myClubs = ClubDAO::getInstance()->getAllClubs();
                if (!$myClubs){
                	$myClubs[] = new Club(null, null, null, null, null, null, null);
                }
		        foreach ($myClubs as $i => $club) {
		        	$i++;
	                ?>
	                <div class="clubModel">
		                <h2 data-name="Club">Club <?php echo $i ?></h2>

		                <table class='form-table'>
		                    <tbody>
		                        <tr>
		                            <th scope='row'>
		                                <label for='nom<?php echo $i ?>' data-name="nom">Nom<span class="required">*</span></label>
		                            </th>
		                            <td>
		                                <input name='nom<?php echo $i ?>' id='nom<?php echo $i ?>' value='<?php echo stripcslashes($club->getName()) ?>' class='regular-text' type='text' required data-name="nom" title="Nom" placeholder="Nom">
		                            </td>
		                        </tr>
		                        <tr>
		                            <th scope='row'>
		                                <label for='chaine<?php echo $i ?>' data-name="chaine">Chaîne de caractères<span class="required">*</span></label>
		                            </th>
		                            <td>
		                                <input name='chaine<?php echo $i ?>' id='chaine<?php echo $i ?>' aria-describedby='tagline-description' value='<?php echo stripcslashes($club->getString()) ?>' class='regular-text' type='text' required data-name="chaine" title="Chaîne de caractères" placeholder="Chaîne de caractères">
		                                <p class='description' id='tagline-description'>Chaîne de caractères que l'algorithme vas chercher sur le site de la FFHB</p>
		                            </td>
		                        </tr>
		                        <tr>
		                            <th scope='row'>
		                                <label for='adresse<?php echo $i ?>' data-name="chaine">Adresse de la salle<span class="required">*</span></label>
		                            </th>
		                            <td>
		                                <input name='adresse<?php echo $i ?>' id='adresse<?php echo $i ?>' value='<?php echo stripcslashes($club->getAdress()) ?>' class='regular-text' type='text' required data-name="adresse" title="Adresse" placeholder="Adresse">
		                            </td>
		                        </tr>
		                        <tr>
		                            <th scope='row'>
		                                <label>Sexe(s)<span class="required">*</span></label>
		                            </th>
		                            <td>
		                            	<label>
		                            		<input type="checkbox" value="1" name="sexeH<?php echo $i ?>" <?php if ($club->getBoy()){ echo "checked='1'"; } ?> data-name="sexeH" title="Masculin" />
		                            		Masculin
		                            	</label>
		                            	&nbsp;&nbsp;
		                            	<label>
		                            		<input type="checkbox" value="1" name="sexeF<?php echo $i ?>" <?php if ($club->getGirl()){ echo "checked='1'"; } ?> data-name="sexeF" title="Féminin"/>
		                            		Féminin
		                            	</label>
		                            	&nbsp;&nbsp;
		                            	<label>
		                            		<input type="checkbox" value="1" name="sexeM<?php echo $i ?>" <?php if ($club->getMixed()){ echo "checked='1'"; } ?> data-name="sexeM" title="Mixte"/>
		                            		Mixte
		                            	</label>
		                            </td>
		                        </tr>
		                    </tbody>
		                </table> 
		                <input name='idClub<?php echo $i ?>' type='hidden' value="<?php echo $club->getId() ?>" data-name="idClub" /> 
		            </div>
	            <?php
                }
                ?>   
	            <input id='totalClub' name='totalClub' type='hidden' value='<?php echo $i; ?>'>     
	            <button type='button' onclick='addClub()' class='button-primary ico ico-add' id="ajouterClub" style="<?php if($i>=5) { echo 'display:none;'; } ?>" >Ajouter un club</button>
				<button type='button' class='button-primary ico ico-del' id="supprClub" style="<?php if($i<=1) { echo 'display:none;'; } ?>"  onclick="let res = validate('Voulez-vous vraiment supprimer le club ?'); return res ? delClub() : false;" >Supprimer un club</button><br><br>
				
                <button type="submit" name="action" value="majClub" class="button-primary ico ico-save">Enregistrer les modifications</button>
	        </form>
    	</div>
		<?php   
	}

}
?>