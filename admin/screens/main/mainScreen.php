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
			<h1 class="wp-heading-inline">Liste des équipes</h1>
			<hr class="wp-header-end">	
			<?php  
                echo $this->showNotice(); 
				$allClubs = DAO\ClubDAO::getInstance()->getAllClubs();
				if (!$allClubs) { 
					?>
					<h2>Veuillez ajouter un club dans un premier temps...</h2>	
					<button class="button-primary ico ico-add" onclick="location.href='admin.php?page=eventus_club&action=club'">
						Ajouter un club
					</button>
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
								<p style="flex: auto;">Aucune équipe n'a été trouvée pour ce club...</p>
							<?php
							}
							foreach ($allTeams as $team) { 
								?>
								<div class="eventusCard">
									<?php echo $team->getImg() ? wp_get_attachment_image($team->getImg(), 'portfolio', false, ["class"=>"card-img-top", "alt"=>"Team"]) : ('<img class="card-img-top" alt="Team" src="'.plugin_dir_url( __FILE__ ).'../../../includes/img/img-default.png'.'">'); ?>
									<div class="card-body">
										<h5 class="card-title">
											<?php echo $this->toProperText($team->getName()).' '.$this->getSexIco($team->getBoy(), $team->getGirl(), $team->getMixed()); ?>
										</h5>
										<p class="card-text">
											<?php echo $this->toProperText($club->getName()) ?>
										</p>
										<button class="button-primary ico ico-fight" onclick="location.href='<?php echo 'admin.php?page=eventus&action=matchs&teamId='.$team->getId(); ?>'">
											Matchs
										</button>
										<button class="button-primary ico ico-team" onclick="location.href='<?php echo 'admin.php?page=eventus&action=team&teamId='.$team->getId(); ?>'">
											Équipe
										</button>
										<button class="button-primary ico ico-club" onclick="location.href='<?php echo 'admin.php?page=eventus_club&action=club&clubId='.$team->getClub()->getId(); ?>'">
											Club
										</button>
									</div>
									<div class="card-footer text-muted">
										<?php $infos = DAO\TeamDAO::getInstance()->getInfosByTeamId($team->getId());?>
										ID : 
										<b><?php echo $infos->team_id; ?></b>										
										 / Club ID : 
										<b><?php echo $infos->team_clubId; ?></b>
										 / Matchs : 
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

			<button class="button-primary ico ico-add" onclick="location.href='admin.php?page=eventus&action=team'">
				Ajouter une équipe
			</button>
			<br/><br/>

			<?php 
			if (DAO\TeamDAO::getInstance()->getAllTeams()){ 
				?>
				<form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<div class="myTooltip">
						<button type="submit" name="action" value="syncMatch" onclick="setLoading(this)" class="button-primary ico ico-sync">
							Synchroniser les données des matchs
						</button>
						<span class="myTooltiptext">Enregistre & synchronise les données des matchs avec le site de la Fédération.</span>
					</div> 
						
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
				<button type="submit" name="action" value="delMatch" class="button-primary ico ico-del"  onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger tous les matchs ?')" >Purger tous les matchs </button>*/ ?>
			</form>	
		</div>
	<?php 
	}
}
?>