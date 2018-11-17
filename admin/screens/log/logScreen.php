<?php

namespace Eventus\Admin\Screens\Log;
use Eventus\Admin\Screens as Screens;

/**
* LogScreen is a class use to manage admin screen
*
* @package  Admin/Screens
* @access   public
*/
class LogScreen extends Screens\MasterScreen {
    /**
    * @var LogScreen   $_instance  Var use to store an instance
    */
    private static $_instance;

    /**
    * Returns an instance of the object
    *
    * @return LogScreen
    * @access public
    */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new LogScreen();
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
            <h1 class="wp-heading-inline">Affichage des logs</h1>
            <hr class="wp-header-end">
            <?php  
                echo $this->showNotice(); 
            ?> 
            <ul>
                <li>
                <?php
                $content = file_get_contents(plugin_dir_path( __FILE__ ).'../../../finder.log');
                if ($content) {
                    $content = str_replace("]", "]</b>", $content);
                    $content = str_replace("[", "<b>[", $content);
                    echo str_replace( "\n", '</li><li>', $content ); 
                } else {
                    echo "<li>Aucun log à afficher</li>";
                }	    
                ?>
                </li>
            </ul>
            <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
                <button class="button-primary ico ico-del" name="action" value="clearLog" class="button-primary" type="submit" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment supprimer les logs ?')">Effacer les logs</button>                
            </form>
        </div>
        <?php
    }
}

?>