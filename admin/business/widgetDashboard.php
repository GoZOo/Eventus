<?php 

namespace Eventus\Admin\Business;
use Eventus\Includes\DAO as DAO;

/**
* FinEventusWidgetDashboard is a class that add a widget element to Wordpress dashboard's
*
* @package  Admin/Business
* @access   public
*/
class EventusWidgetDashboard {
    private $context; 
    
    function __construct() {
		wp_enqueue_style('styleEventus', WP_PLUGIN_URL.'/eventus/admin/views/css/styles.css'); 
        wp_register_script('eventus', plugin_dir_url( __FILE__ ).'/../../views/js/_eventus.js', '', '', true); 
        wp_localize_script('eventus', 'translations', 
            array(                
                'defMessage' => __('This action is irreversible. Do you really want to delete the element?', 'eventus' ),
                'loading' => __('Loading in progress....', 'eventus' )
            )
        );
        wp_enqueue_script('eventus');
		wp_enqueue_script('eventus_defaultScreen', plugin_dir_url( __FILE__ ).'/../../views/js/screens/_defaultScreen.js', '', '', true); 
        \Timber\Timber::$locations = plugin_dir_path( __FILE__ ).'../views/screens/components/';
        $this->context = \Timber\Timber::get_context();
        $this->context['adminPostUrl'] = admin_url('admin-post.php'); 
        
        $this->display();
    } 
      
    /**
    * Function to display the widget
    *
    * @return void
    * @access public
    */
    private function display(){
        $this->context['nbr_teams'] = DAO\TeamDAO::getInstance()->getNumbersTeams()->nbr_teams;
        $this->context['nbr_clubs'] = DAO\ClubDAO::getInstance()->getNumbersClubs()->nbr_clubs;
        $this->context['nbr_logs'] = count(file_exists(WP_PLUGIN_URL.'/eventus/finder.log') ? file(WP_PLUGIN_URL.'/eventus/finder.log') : array());

        date_default_timezone_set("Europe/Paris");
        $date = new \DateTime(get_option("eventus_datetimesynch")); 
        $this->context['last_sync'] =  $date->format('d/m/Y à H:i:s');
        $this->context['plugin_data'] = get_plugin_data(WP_PLUGIN_DIR.'/eventus/eventus.php');

        \Timber\Timber::render("widgetDashboard.twig", $this->context);
    }

}