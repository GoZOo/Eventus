<?php

class LogScreen extends MasterScreen {
    private static $_instance;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new LogScreen();
        }
        return self::$_instance;
    }

    private function __construct() {}	
    
    function display(){
        ?>
        <div class='wrap'>
            <h1 class="wp-heading-inline">Affichage des logs</h1>
            <?php  
                echo $this->showNotice(); 
            ?> 
            <ul>
                <li>
                <?php
                $content = file_get_contents(plugin_dir_path( __FILE__ ).'../../finder.log');
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
                <button class="button-primary ico ico-del" name="action" value="clearLog" class="button-primary" type="submit" onclick="return validate('Cette action est iréversible. Voulez-vous vraiment supprimer les logs ?')">Supprimer les logs</button>                
            </form>
        </div>
        <?php
    }
}

?>