<?php

namespace Eventus\Admin\Screens\Main;
use Eventus\Admin\Screens as Screens;
use Eventus\Includes\Datas as DAO;

/**
* MainScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class MainScreen extends Screens\MasterScreen {	
	/**
    * @var MainScreen   $_instance  Var use to store an instance
    */
	private static $_instance;

	/**
    * Returns an instance of the object
    *
    * @return MainScreen
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new MainScreen();
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
		if (isset($_GET['action']) && $_GET['action']=="team") {
			MainTeamScreen::getInstance()->display(); 
			return;
		} else if(isset($_GET['action']) && $_GET['action']=="matchs"){
			MainMatchScreen::getInstance()->display(); 
			return;
		}
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php _e('List of teams', 'eventus') ?></h1>
			<hr class="wp-header-end">	
			<?php  
                echo $this->showNotice(); 
				$allClubs = DAO\ClubDAO::getInstance()->getAllClubs();
				if (!$allClubs) { 
					?>
					<h2><?php _e('Please add a club first...', 'eventus') ?></h2>	
					<a class="button-primary ico ico-add" href='admin.php?page=eventus_club&action=club'>
						<?php _e('Add a club', 'eventus') ?>						
					</a>
					<?php
					return;
				}
				foreach ($allClubs as $club) {
			?>
				<div class="eventusCardList">
					<h2><?php echo $this->toProperText($club->getName())?></h2>	 
					<div>       				
						<?php 
							$allTeams = DAO\TeamDAO::getInstance()->getAllTeamsByClubOrderByName($club);
							if (!$allTeams) {?>
								<p style="flex: auto;"><?php _e('No team has been found for this club...', 'eventus') ?></p>
							<?php
							}
							foreach ($allTeams as $team) { 
								?>
								<div class="eventusCard">
									<?php echo $team->getImg() ? ('<img class="card-img-top" alt="Team" src="'.wp_get_attachment_image_src($team->getImg(), 'medium')[0].'">') : ('<img class="card-img-top" alt="Team" src="'.plugin_dir_url( __FILE__ ).'../../../includes/img/img-default.png'.'">'); ?>
									<div class="card-body">
										<h5 class="card-title">
											<?php echo $this->toProperText($team->getName()).' '.$this->getSexIco($team->getBoy(), $team->getGirl(), $team->getMixed()); ?>
										</h5>
										<p class="card-text">
											<?php echo $this->toProperText($club->getName()) ?>
										</p>
										<a class="button-primary ico ico-fight" href="<?php echo 'admin.php?page=eventus&action=matchs&teamId='.$team->getId(); ?>">
											<?php _e('Matches', 'eventus') ?>
										</a>
										<a class="button-primary ico ico-team" href="<?php echo 'admin.php?page=eventus&action=team&teamId='.$team->getId(); ?>">
											<?php _e('Team', 'eventus') ?>
										</a>
										<a class="button-primary ico ico-club" href="<?php echo 'admin.php?page=eventus_club&action=club&clubId='.$team->getClub()->getId(); ?>">
											<?php _e('Club', 'eventus') ?>
										</a>
									</div>
									<div class="card-footer text-muted">
										<?php $infos = DAO\TeamDAO::getInstance()->getInfosByTeamId($team->getId());?>
										<?php _e('ID : ', 'eventus') ?>
										<b><?php echo $infos->team_id; ?></b>
										<?php _e(' / Club ID : ', 'eventus') ?>		
										<b><?php echo $infos->team_clubId; ?></b>
										<?php _e(' / Matches : ', 'eventus') ?>		
										<b><?php echo $infos->matchsNbr ?></b>
									</div>
								</div>
								<?php
							}
						?>
					</div>
				</div>
				<?php 
				}
			?>
			<br>

			<a class="button-primary ico ico-add" href="admin.php?page=eventus&action=team">
				<?php _e('Add a team', 'eventus') ?>		
			</a>
			<br/><br/>

			<?php 
			if (DAO\TeamDAO::getInstance()->getAllTeams()){ 
				?>
				<form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<div class="myTooltip">
						<button type="submit" name="action" value="eventus_syncMatch" onclick="setLoading(this)" class="button-primary ico ico-sync">
							<?php _e('Synchronize match data', 'eventus') ?>	
						</button>
						<span class="myTooltiptext"><?php _e('Saves & synchronizes match data with the Federation website.', 'eventus') ?></span>
					</div> 

					<div class="myTooltip">
						<button type="submit" name="action" value="eventus_majIcs" onclick="setLoading(this)" class="button-primary ico ico-calendar"><?php _e('Update calendars', 'eventus') ?>s</button>
						<span class="myTooltiptext"><?php _e('Update all ICS team calendars', 'eventus') ?></span>
					</div>  
					<br>
					<p class="dern-synch"><b><?php _e('Last. synch. : ', 'eventus') ?></b>
					<?php 					
						date_default_timezone_set("Europe/Paris");
                        $date = new \DateTime(get_option("eventus_datetimesynch")); 
                        echo $date->format('d/m/Y à H:i:s');
                    ?>
               		</p>
						
				</form>	
				<?php 
			}
			?>	
			<?php /*		
			<button class="button-primary ico ico-info" onclick="location.href='admin.php?page=eventus_logs'">
				Consulter les logs
			</button>	
			<?php			
            if(current_user_can('administrator')) {  
				?>
				<button class="button-primary ico ico-settings" onclick="location.href='admin.php?page=eventus_admin'">
					Paramètres d'administration
				</button>
			<?php
			}
			?>
            <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post" >
				<button type="submit" name="action" value="eventus_delMatch" class="button-primary ico ico-del"  onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger tous les matchs ?')" >Purger tous les matchs </button>*/ ?>
			</form>	
		</div>
	<?php 
	}
}
?>