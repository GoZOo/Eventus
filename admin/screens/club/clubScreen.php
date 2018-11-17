<?php

namespace Eventus\Admin\Screens\Club;
use Eventus\Admin\Screens as Screens;
use Eventus\Includes\Datas as DAO;

/**
* ClubScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class ClubScreen extends Screens\MasterScreen {	
	/**
    * @var ClubScreen   $_instance  Var use to store an instance
    */
	private static $_instance;

	/**
    * Returns an instance of the object
    *
    * @return ClubScreen
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new ClubScreen();
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
		if (isset($_GET['action']) && $_GET['action']=="club") {
			ClubDetailScreen::getInstance()->display(); 
			return;
		}
		?>
		<div class="wrap">
	        <h1 class="wp-heading-inline">Liste des Clubs</h1>
			<hr class="wp-header-end">
			<?php  
                echo $this->showNotice(); 	        
			$allClubs = DAO\ClubDAO::getInstance()->getAllClubs();
			if (!$allClubs){
				?>
				<h2>Veuillez ajouter un club dans un premier temps...</h2>	
				<?php
			}
			?>
				<div class="eventusCardList">
					<h2></h2>	 
					<div>  
					<?php 							
						foreach ($allClubs as $club) { ?>
							<div class="eventusCard eventusCardClub">
								<?php echo $club->getImg() ? wp_get_attachment_image($club->getImg(), 'portfolio', false, ["class"=>"card-img-top", "alt"=>"Club"]) : ('<img class="card-img-top" alt="Club" src="'.plugin_dir_url( __FILE__ ).'../../../includes/img/img-default.png'.'">'); ?>
								<div class="card-body">
									<h5 class="card-title">
										<?php echo $this->toProperText($club->getName()); ?>
									</h5>
									<button class="button-primary ico ico-club" onclick="location.href='<?php echo 'admin.php?page=eventus_club&action=club&clubId='.$club->getId(); ?>'">
										Club
									</button>
								</div>
								<div class="card-footer text-muted">
									<?php $infos = DAO\ClubDAO::getInstance()->getInfosByClubId($club->getId()); ?>
									ID : 
									<b><?php echo $infos->club_id; ?></b>	 
									/ Équipes : 
									<b><?php echo $infos->teamsNbr; ?></b>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
	            <button type='button' class='button-primary ico ico-add' onclick="location.href='admin.php?page=eventus_club&action=club'">Ajouter un club</button>
    	</div>
		<?php   
	}

}
?>