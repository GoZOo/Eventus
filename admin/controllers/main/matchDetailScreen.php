<?php

namespace Eventus\Admin\Screens\Main;
use Eventus\Admin\Screens as Screens;
use Eventus\Includes\Datas as DAO;
use Eventus\Includes\Entities as Entities;

/**
* MainMatchScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class MainMatchScreen {	
	/**
    * @var MainMatchScreen   $_instance  Var use to store an instance
    */
	private static $_instance;

	/**
    * Returns an instance of the object
    *
    * @return MainMatchScreen
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new MainMatchScreen();
        }
        return self::$_instance;
    }

    protected function __construct() {
        parent::__construct();
		wp_enqueue_script('matchJs', plugin_dir_url( __FILE__ ).'/../../../js/screens/matchDetailScreen.js', '', '', true); 
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
			if ($team->getId() == null){
				echo "<h2>".__('Error: The team could not be found....', 'eventus')."</h2>";
				return;
			}
		}
		?>
		<div class="wrap">
	        <h1 class="wp-heading-inline">
				<?php 
					echo ($team->getName() ? $this->toProperText($team->getName()) :  __('New team', 'eventus')).' '.$this->getSexIco($team->getBoy(), $team->getGirl(), $team->getMixed());
                ?>                
			</h1>
			<?php 
				$myMatchParent = DAO\MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(), 0); 
			?>
			<a href="<?php echo "admin.php?page=eventus&action=team&teamId=".$team->getId(); ?>" class="page-title-action"><?php _e('Team', 'eventus') ?></a>
			<hr class="wp-header-end">
			<?php  
                echo $this->showNotice(); 
            ?> 
	        <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">   
				<h2>
					<?php echo $this->toProperText($team->getClub()->getName()); ?>
				</h2>
					<div class="overflow">
						<table class='matchTable parentMatches' <?php if (!$myMatchParent) { echo "style=display:none;"; } ?>>
							<tr>
								<th><?php _e('Id', 'eventus') ?></th>
								<th><?php _e('Ch.', 'eventus') ?></th>
								<th><?php _e('D.', 'eventus') ?></th>
								<th><?php _e('Date', 'eventus') ?></th>
								<th><?php _e('Time', 'eventus') ?><br><?php _e('RDV', 'eventus') ?></th>
								<th><?php _e('Time', 'eventus') ?><br><?php _e('Match', 'eventus') ?></th>
								<th><?php _e('Loc.', 'eventus') ?><span class="required">*</span></th>
								<th><?php _e('Loc.', 'eventus') ?><br><?php _e('goals', 'eventus') ?></th>
								<th><?php _e('Opp.', 'eventus') ?><span class="required">*</span></th>
								<th><?php _e('Opp.', 'eventus') ?><br><?php _e('goals', 'eventus') ?></th>
								<th><?php _e('Street', 'eventus') ?></th>
								<th><?php _e('City', 'eventus') ?></th>
								<th><?php _e('Gym', 'eventus') ?></th>
							</tr>   	
						<?php
						foreach ($myMatchParent as $match) {
							?>
							<tr class="<?php echo $match->getId() ?>">					 		
								<th>
									<?php echo $match->getId() ?>
								</th>
								<th>
									<?php echo $match->getChamp() ?>
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
									<input type='text' value="<?php echo $this->toProperText($match->getLocalTeam()) ?>" class='regular-text' disabled required data-name="localTeamSon" >
								</td>
								<td>
									<input type='number' value="<?php echo $match->getLocalTeamScore() ?>" class='regular-text' disabled data-name="localTeamScoreSon">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getVisitingTeam()) ?>" class='regular-text' disabled required data-name="visitingTeamSon">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getVisitingTeamScore() ?>" class='regular-text' disabled data-name="visitingTeamScoreSon">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getStreet()) ?>" class='regular-text' disabled data-name="streetSon">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getCity()) ?>" class='regular-text' disabled data-name="citySon">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getGym()) ?>" class='regular-text' disabled data-name="gymSon">
								</td>
								<td>
									<button type='button' onclick='editMatch(<?php echo $match->getId() ?>)' class='button-primary' title="Editer le match">
										<div class="edit">
											<?php echo file_get_contents(plugin_dir_path( __FILE__ ).'../../svg/edit.svg'); ?>						    				
										</div>
										<div class="delete" style="display:none;">
											<?php echo file_get_contents(plugin_dir_path( __FILE__ ).'../../svg/del.svg'); ?>
										</div>			                		
									</button>
								</td>
								<input type='hidden' value="<?php echo $match->getMatchDay() ?>" data-name="matchDaySon">
								<input type='hidden' value="<?php echo $match->getNumMatch() ?>" data-name="numMatchSon">
								<input type='hidden' value="<?php echo $match->getChamp() ?>" data-name="matchChampSon">
								<input type='hidden' value="<?php echo $match->getId() ?>" data-name="idMatchRefSon">
								<input type='hidden' value="<?php echo $match->getId() ?>" data-name="idSon">
							</tr>
						<?php
						}					
						?>
						</table>
						<?php
						$myMatchSon = DAO\MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(),1); 
						?>
						<br class="sonMatches" <?php if (!$myMatchSon) { echo "style=display:none;"; } ?> >
						<h3 class="sonMatches" <?php if (!$myMatchSon) { echo "style=display:none;"; } ?> ><?php _e('Son matches', 'eventus') ?></h3>
						<table class='matchTable sonMatches' <?php if (!$myMatchSon) { echo "style=display:none;"; } ?> >
							<tr>
								<th><?php _e('Id', 'eventus') ?><br><?php _e('Orig.', 'eventus') ?></th>
								<th><?php _e('Ch.', 'eventus') ?></th>
								<th><?php _e('D.', 'eventus') ?></th>
								<th><?php _e('Date', 'eventus') ?></th>
								<th><?php _e('Time', 'eventus') ?><br><?php _e('RDV', 'eventus') ?></th>
								<th><?php _e('Time', 'eventus') ?><br><?php _e('Match', 'eventus') ?></th>
								<th><?php _e('Loc.', 'eventus') ?><span class="required">*</span></th>
								<th><?php _e('Loc.', 'eventus') ?><br><?php _e('goals', 'eventus') ?></th>
								<th><?php _e('Opp.', 'eventus') ?><span class="required">*</span></th>
								<th><?php _e('Opp.', 'eventus') ?><br><?php _e('goals', 'eventus') ?></th>
								<th><?php _e('Street', 'eventus') ?></th>
								<th><?php _e('City', 'eventus') ?></th>
								<th><?php _e('Gym', 'eventus') ?></th>
							</tr>   	
						<?php
						$nbrSonMatch = 0;
						foreach ($myMatchSon as $key => $match) {
							$nbrSonMatch++;
							?>
							<tr class="<?php echo ($match->getMatchRef() ? $match->getMatchRef()->getId() : '') ?>">
								<th>
									<?php echo ($match->getMatchRef()->getId() ? $match->getMatchRef()->getId() : '') ?>
								</th>									
								<th>
									<?php echo $match->getChamp() ?>
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
									<input type='text' value="<?php echo $this->toProperText($match->getLocalTeam()) ?>" class='regular-text' data-name="localTeamSon" required>
								</td>
								<td>
									<input type='number' value="<?php echo $match->getLocalTeamScore() ?>" class='regular-text' data-name="localTeamScoreSon">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getVisitingTeam()) ?>" class='regular-text' data-name="visitingTeamSon" required>
								</td>
								<td>
									<input type='number' value="<?php echo $match->getVisitingTeamScore() ?>" class='regular-text' data-name="visitingTeamScoreSon">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getStreet()) ?>" class='regular-text' data-name="streetSon">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getCity()) ?>" class='regular-text' data-name="citySon">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getGym()) ?>" class='regular-text' data-name="gymSon">
								</td>
								<td>
									<button type='button' onclick='deleMatch(<?php echo $match->getMatchRef()->getId() ?>, "sonMatches")' class='button-primary' title="Supprimer le match">
										<?php echo file_get_contents(plugin_dir_path( __FILE__ ).'../../svg/del.svg'); ?>
									</button>
								</td>
								<input type='hidden' value="<?php echo $match->getMatchDay() ?>" data-name="matchDaySon">
								<input type='hidden' value="<?php echo $match->getNumMatch() ?>" data-name="numMatchSon">
								<input type='hidden' value="<?php echo $match->getChamp() ?>" data-name="matchChampSon">
								<input type='hidden' value="<?php echo $match->getMatchRef()->getId() ?>" data-name="idMatchRefSon">
								<input type='hidden' name='idSon<?php echo $nbrOtherMatch ?>' value="<?php echo $match->getId() ?>" data-name="idSon">
							</tr>
						<?php
						}
						?>	            	
						</table>
						<input type="hidden" value="<?php echo $nbrSonMatch ?>" name="nbrSonMatch">
						<br <?php if (!$myMatchParent) { echo "style=display:none;"; } ?>>
						<h3 class=""><?php _e('Other matches', 'eventus') ?></h3>
						<table class='matchTable otherMatches'>
							<tr>
								<th><?php _e('Date', 'eventus') ?></th>
								<th><?php _e('Time', 'eventus') ?><br><?php _e('RDV', 'eventus') ?></th>
								<th><?php _e('Time', 'eventus') ?><br><?php _e('Match', 'eventus') ?></th>
								<th><?php _e('Loc.', 'eventus') ?><span class="required">*</span></th>
								<th><?php _e('Loc.', 'eventus') ?><br><?php _e('goals', 'eventus') ?></th>
								<th><?php _e('Opp.', 'eventus') ?><span class="required">*</span></th>
								<th><?php _e('Opp.', 'eventus') ?><br><?php _e('goals', 'eventus') ?></th>
								<th><?php _e('Street', 'eventus') ?></th>
								<th><?php _e('City', 'eventus') ?></th>
								<th><?php _e('Gym', 'eventus') ?></th>
							</tr>   	
						<?php
						$myMatchOther = DAO\MatchDAO::getInstance()->getAllMatchesByTeamIdAndType($team->getId(),2);
						if (!$myMatchOther){
							$myMatchOther[] = new Entities\Match(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
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
									<input type='text' value="<?php echo $this->toProperText($match->getLocalTeam()) ?>" class='regular-text' data-name="localTeamOther">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getLocalTeamScore() ?>" class='regular-text' data-name="localTeamScoreOther">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getVisitingTeam()) ?>" class='regular-text' data-name="visitingTeamOther">
								</td>
								<td>
									<input type='number' value="<?php echo $match->getVisitingTeamScore() ?>" class='regular-text' data-name="visitingTeamScoreOther">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getStreet()) ?>" class='regular-text' data-name="streetOther">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getCity()) ?>" class='regular-text' data-name="cityOther">
								</td>
								<td>
									<input type='text' value="<?php echo $this->toProperText($match->getGym()) ?>" class='regular-text' data-name="gymOther">
								</td>
								<td>
									<button type='button' onclick="deleMatch('<?php if($match->getId()) { echo $match->getId(); } else { echo $tempId; } ?>', 'otherMatches')" class='button-primary' title="Supprimer le match">
										<?php echo file_get_contents(plugin_dir_path( __FILE__ ).'../../svg/del.svg'); ?>
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
				<button type='button' onclick='addOtherMatch()' class='button-primary ico ico-add'><?php _e('Add a match', 'eventus') ?></button>
				<br><br> 

				<button type="submit" name="action" value="eventus_majMatch" class="button-primary ico ico-save"><?php _e('Save changes', 'eventus') ?></button>	

				<div class="myTooltip">
					<button type="submit" name="action" value="eventus_majHours" onclick="setLoading(this)" class="button-primary ico ico-time"><?php _e('Recalculate RDV schedules', 'eventus') ?></button>
					<span class="myTooltiptext"><?php _e('Saves & updates schedules of upcoming outdoor games with a valid address.', 'eventus') ?></span>
				</div>  
				
				<?php  if ($team->getUrlOne()) { ?>
                <div class="myTooltip">
					<button type="submit" name="action" value="eventus_syncMatch" onclick="setLoading(this)" class="button-primary ico ico-sync"><?php _e('Synchronize match data', 'eventus') ?></button>
					<span class="myTooltiptext"><?php _e('Saves & synchronizes match data with the Federation website.', 'eventus') ?></span>
				</div> 
				<?php } ?>
                
				<div class="myTooltip">
					<button type="submit" name="action" value="eventus_majIcs" onclick="setLoading(this)" class="button-primary ico ico-calendar"><?php _e('Update the calendar', 'eventus') ?></button>
					<span class="myTooltiptext"><?php _e('Update the team\'s ICS calendar', 'eventus') ?></span>
				</div>  			
					
				<?php //<button type="submit" name="action" value="eventus_delMatch" class="button-primary ico ico-del"  onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger tous les matchs ?')" >Purger les matchs de l'équipe</button>
				//<button type="submit" name="action" value="eventus_delIcs" onclick="return validate() class="button-primary ico ico-del">Purger tous les calendriers</button>
				?>
	        </form>
    	</div>
    	<?php
	}
}
?>