<?php 
include_once plugin_dir_path( __FILE__ ).'includes/constants.php';
include_once plugin_dir_path( __FILE__ ).'includes/master.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/club.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/match.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/team.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/teamDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/clubDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/MatchDAO.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/clubScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/teamScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/matchScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/finder.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/shortcode.php';
include_once plugin_dir_path( __FILE__ ).'admin/librairies/simple_html_dom.php';

foreach (TeamDAO::getInstance()->getAllTeams() as $team) {
    Finder::getInstance()->updateMatches($team);
}
?>