<?php

class MainMatchScreen extends MasterScreen {	
	private static $_instance;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new MainMatchScreen();
        }
        return self::$_instance;
    }

    private function __construct() {}

    function display(){    	
		if (isset($_GET['teamId'])){ 
			$team = TeamDAO::getInstance()->getTeamById($_GET['teamId']);
			if ($team->getId() == null){
				echo "<h2>Erreur : L'équipe n'a pas pu être trouvée...</h2>";
				return;
			}
		}
		?>
		<div class="wrap">
	        <h1 class="wp-heading-inline">
				<?php 
					echo ($team->getName() ? $team->getName() : 'Nouvelle équipe').' '.$this->getSexIco($team->getBoy(), $team->getGirl(), $team->getMixed());
                ?>                
			</h1>
			<?php 
				$myMatchParent = MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(), 0); 
			?>
			<a href="<?php echo "admin.php?page=eventus&action=team&teamId=".$team->getId(); ?>" class="page-title-action">Équipe</a>
			<?php  
                echo $this->showNotice(); 
            ?> 
	        <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">   
				<h2>
					<?php echo $team->getClub()->getName(); ?>
				</h2>
					<div class="overflow">
						<table class='matchTable parentMatches' <?php if (!$myMatchParent) { echo "style=display:none;"; } ?>>
							<tr>
								<th>Id</th>
								<th>J.</th>
								<th>Date</th>
								<th>Heure<br>RDV</th>
								<th>Heure<br>Match</th>
								<th>Loc.<span class="required">*</span></th>
								<th>Loc.<br>buts</th>
								<th>Adv.<span class="required">*</span></th>
								<th>Adv.<br>buts</th>
								<th>Rue</th>
								<th>Ville</th>
								<th>Salle</th>
							</tr>   	
						<?php
						foreach ($myMatchParent as $match) {
							?>
							<tr class="<?php echo $match->getId() ?>">					 		
								<th>
									<?php echo $match->getId() ?>
								</th>			 		
								<th>
									<?php echo $match->getMatchDay() ?>
								</th>
								<td>
									<input type='date' value="<?php echo $match->getDate() ?>" class='regular-text' disabled data-name="dateSon">
								</td>
								<td>
									<input type='time' value="<?php echo $match->getHourRdv() ?>" class='regular-text' disabled data-name="hourRdvSon"> 
								</td>
								<td>
									<input type='time' value="<?php echo $match->getHourStart() ?>" class='regular-text' disabled data-name="hourStartSon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getLocalTeam() ?>" class='regular-text' disabled data-name="localTeamSon">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getLocalTeamScore() ?>" class='regular-text' disabled data-name="localTeamScoreSon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getVisitingTeam() ?>" class='regular-text' disabled data-name="visitingTeamSon">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getVisitingTeamScore() ?>" class='regular-text' disabled data-name="visitingTeamScoreSon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getStreet() ?>" class='regular-text' disabled data-name="streetSon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getCity() ?>" class='regular-text' disabled data-name="citySon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getGym() ?>" class='regular-text' disabled data-name="gymSon">
								</td>
								<td>
									<button type='button' onclick='editMatch(<?php echo $match->getId() ?>)' class='button-primary' title="Editer le match">
										<div class="edit">
											<?php echo file_get_contents(plugin_dir_path( __FILE__ ).'../svg/edit.svg'); ?>						    				
										</div>
										<div class="delete" style="display:none;">
											<?php echo file_get_contents(plugin_dir_path( __FILE__ ).'../svg/del.svg'); ?>
										</div>			                		
									</button>
								</td>
								<input type='hidden' value="<?php echo $match->getMatchDay() ?>" data-name="matchDaySon">
								<input type='hidden' value="<?php echo $match->getNumMatch() ?>" data-name="numMatchSon">
								<input type='hidden' value="<?php echo $match->getId() ?>" data-name="idMatchRefSon">
								<input type='hidden' value="<?php echo $match->getId() ?>" data-name="idSon">
							</tr>
						<?php
						}					
						?>
						</table>
						<?php
						$myMatchSon = MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(),1); 
						?>
						<br class="sonMatches" <?php if (!$myMatchSon) { echo "style=display:none;"; } ?> >
						<h3 class="sonMatches" <?php if (!$myMatchSon) { echo "style=display:none;"; } ?> >Matchs fils :</h3>
						<table class='matchTable sonMatches' <?php if (!$myMatchSon) { echo "style=display:none;"; } ?> >
							<tr>
								<th>Id<br>Orig.</th>
								<th>J.</th>
								<th>Date</th>
								<th>Heure<br>RDV</th>
								<th>Heure<br>Match</th>
								<th>Loc.<span class="required">*</span></th>
								<th>Loc.<br>buts</th>
								<th>Adv.<span class="required">*</span></th>
								<th>Adv.<br>buts</th>
								<th>Rue</th>
								<th>Ville</th>
								<th>Salle</th>
							</tr>   	
						<?php
						$nbrSonMatch = 0;					
						foreach ($myMatchSon as $key => $match) {
							$nbrSonMatch++;
							?>
							<tr class="<?php echo $match->getMatchRef()->getId() ?>">
								<th>
									<?php echo $match->getMatchRef()->getId() ?>
								</th>		 		
								<th>
									<?php echo $match->getMatchDay() ?>
								</th>
								<td>
									<input type='date' value="<?php echo $match->getDate() ?>" class='regular-text' data-name="dateSon">
								</td>
								<td>
									<input type='time' value="<?php echo $match->getHourRdv() ?>" class='regular-text' data-name="hourRdvSon">
								</td>						    
								<td>
									<input type='time' value="<?php echo $match->getHourStart() ?>" class='regular-text' data-name="hourStartSon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getLocalTeam() ?>" class='regular-text' data-name="localTeamSon">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getLocalTeamScore() ?>" class='regular-text' data-name="localTeamScoreSon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getVisitingTeam() ?>" class='regular-text' data-name="visitingTeamSon">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getVisitingTeamScore() ?>" class='regular-text' data-name="visitingTeamScoreSon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getStreet() ?>" class='regular-text' data-name="streetSon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getCity() ?>" class='regular-text' data-name="citySon">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getGym() ?>" class='regular-text' data-name="gymSon">
								</td>
								<td>
									<button type='button' onclick='deleMatch(<?php echo $match->getMatchRef()->getId() ?>, "sonMatches")' class='button-primary' title="Supprimer le match">
										<?php echo file_get_contents(plugin_dir_path( __FILE__ ).'../svg/del.svg'); ?>
									</button>
								</td>
								<input type='hidden' value="<?php echo $match->getMatchDay() ?>" data-name="matchDaySon">
								<input type='hidden' value="<?php echo $match->getNumMatch() ?>" data-name="numMatchSon">
								<input type='hidden' value="<?php echo $match->getMatchRef()->getId() ?>" data-name="idMatchRefSon">
								<input type='hidden' name='idSon<?php echo $nbrOtherMatch ?>' value="<?php echo $match->getId() ?>" data-name="idSon">
							</tr>
						<?php
						}
						?>	            	
						</table>
						<input type="hidden" value="<?php echo $nbrSonMatch ?>" name="nbrSonMatch">
						<br <?php if (!$myMatchParent) { echo "style=display:none;"; } ?>>
						<h3 class="">Autre Matchs :</h3>
						<table class='matchTable otherMatches'>
							<tr>
								<th>Date</th>
								<th>Heure<br>RDV</th>
								<th>Heure<br>Match</th>
								<th>Loc.<span class="required">*</span></th>
								<th>Loc.<br>buts</th>
								<th>Adv.<span class="required">*</span></th>
								<th>Adv.<br>buts</th>
								<th>Rue</th>
								<th>Ville</th>
								<th>Salle</th>
							</tr>   	
						<?php
						$myMatchOther = MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(),2);
						if (!$myMatchOther){
							$myMatchOther[] = new Match(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
						}
						$nbrOtherMatch = 1;
						foreach ($myMatchOther as $match) {
							$tempId = substr(md5(uniqid(rand(), true)), 2, 9);
							?>
							<tr class="<?php if($match->getId()) { echo $match->getId(); } else { echo $tempId; }  ?>">
								<td>
									<input type='date' value="<?php echo $match->getDate() ?>" class='regular-text' data-name="dateOther">
								</td>
								<td>
									<input type='time' value="<?php echo $match->getHourRdv() ?>" class='regular-text' data-name="hourRdvOther">
								</td>						    
								<td>
									<input type='time' value="<?php echo $match->getHourStart() ?>" class='regular-text' data-name="hourStartOther">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getLocalTeam() ?>" class='regular-text' data-name="localTeamOther">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getLocalTeamScore() ?>" class='regular-text' data-name="localTeamScoreOther">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getVisitingTeam() ?>" class='regular-text' data-name="visitingTeamOther">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getVisitingTeamScore() ?>" class='regular-text' data-name="visitingTeamScoreOther">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getStreet() ?>" class='regular-text' data-name="streetOther">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getCity() ?>" class='regular-text' data-name="cityOther">
								</td>
								<td>
									<input type='text' value="<?php echo $match->getGym() ?>" class='regular-text' data-name="gymOther">
								</td>
								<td>
									<button type='button' onclick="deleMatch('<?php if($match->getId()) { echo $match->getId(); } else { echo $tempId; } ?>', 'otherMatches')" class='button-primary' title="Supprimer le match">
										<?php echo file_get_contents(plugin_dir_path( __FILE__ ).'../svg/del.svg'); ?>
									</button>
								</td>
								<input type='hidden' name='idOther<?php echo $nbrOtherMatch ?>' value="<?php echo $match->getId() ?>" data-name="idOther">
							</tr>				
							<?php
							$nbrOtherMatch++;
						}
						?>
						</table>
					</div>
	            	<input type="hidden" value="<?php echo $nbrOtherMatch-1 ?>" name="nbrOtherMatch">					  
                <br><br>
				<input type='hidden' name='teamId' value="<?php echo $team->getId(); ?>">	
				<input type="hidden" name="path" value="http://<?php echo $_SERVER[HTTP_HOST], $_SERVER[REQUEST_URI]; ?>" >
				<button type='button' onclick='addOtherMatch()' class='button-primary ico ico-add'>Ajouter un match</button>
				<br><br>  
                <button type="submit" name="action" value="majMatch" class="button-primary ico ico-save">Enregistrer les modifications</button>
                <button type="submit" name="action" value="majHours" onclick="setLoading(this)" class="button-primary ico ico-time">Recalculer les horaires de RDV</button>
				<?php  if ($team->getUrl()) { ?>
					<button type="submit" name="action" value="syncMatch" onclick="setLoading(this)" class="button-primary ico ico-sync">Synchroniser les données des matchs</button>
				<?php } ?>
				<?php //<button type="submit" name="action" value="delMatch" class="button-primary ico ico-del"  onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger tous les matchs ?')" >Purger les matchs de l'équipe</button> ?>
	        </form>
    	</div>
    	<?php
	}
}
?>