<?php

class MainScreen extends MasterScreen {	
	private static $_instance;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new MainScreen();
        }
        return self::$_instance;
    }
		
    private function __construct() {  
    	wp_enqueue_script('teamJs', plugin_dir_url( __FILE__ ).'/../../js/team.js', '', '', true); 
    	wp_enqueue_script('matchJs', plugin_dir_url( __FILE__ ).'/../../js/match.js', '', '', true); 
		wp_enqueue_script('commonJs', plugin_dir_url( __FILE__ ).'/../../js/common.js', '', '', true); 
	}

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
			<?php  
                echo $this->showNotice(); 
				$allClubs = ClubDAO::getInstance()->getAllClubs();
				if (!$allClubs) { 
					?>
					<h2>Veuillez ajouter un club dans un premier temps...</h2>	
					<button class="button-primary ico ico-add" onclick="location.href='admin.php?page=eventus_clubs'">
						Ajouter un club
					</button>
					<?php
					return;
				}
				foreach ($allClubs as $club) {
			?>
				<div class="teamList">
					<h2><?php echo $club->getName()?></h2>	 
					<div>       				
						<?php 
							$allTeams = TeamDAO::getInstance()->getAllTeamsByClubOrderByName($club);
							if (!$allTeams) {?>
								<p style="flex: auto;">Aucune équipe n'a été trouvée pour ce club...</p>
							<?php
							}
							foreach ($allTeams as $team) { 
								?>
								<div class="myCard">
									<?php echo $team->getImg() ? wp_get_attachment_image($team->getImg(), 'portfolio', false, ["class"=>"card-img-top", "alt"=>"Team"]) : ('<img class="card-img-top" alt="Team" src="'.plugin_dir_url( __FILE__ ).'../../includes/img/team-default.png'.'">'); ?>
									<div class="card-body">
										<h5 class="card-title">
											<?php echo $team->getName().' '.$this->getSexIco($team->getBoy(), $team->getGirl(), $team->getMixed()); ?>
										</h5>
										<p class="card-text">
											<?php echo $club->getName() ?>
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
										<?php $infos = MatchDAO::getInstance()->getInfosByTeamId($team->getId());?>
										ID : 
										<b><?php echo $infos->id; ?></b>										
										 / Club ID : 
										<b><?php echo $infos->clubId; ?></b>
										 / Matchs : 
										<b><?php echo $infos->number ?></b>
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
			if (TeamDAO::getInstance()->getAllTeams()){ 
				?>
				<form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post" class="fakeForm">
					<button type="submit" name="action" value="syncMatch" onclick="setLoading(this)" class="button-primary ico ico-sync">
						Synchroniser les données des matchs
					</button>	
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
            <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post" class="fakeForm">
				<button type="submit" name="action" value="delMatch" class="button-primary ico ico-del"  onclick="return validate('Cette action est iréversible. Voulez-vous vraiment purger tous les matchs ?')" >Purger tous les matchs </button>*/ ?>
			</form>	
		</div>
	<?php 
	}
}
?>