<?php 
//TODO FIX ME
include_once plugin_dir_path( __FILE__ ).'includes/constants.php';
include_once plugin_dir_path( __FILE__ ).'includes/masterTrait.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/club.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/match.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/team.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/_masterDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/_database.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/teamDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/clubDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/matchDAO.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/_masterScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/mainScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/mainTeamScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/mainMatchScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/clubScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/clubDetailScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/logScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/screens/adminScreen.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/finder.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/postHandler.php';
include_once plugin_dir_path( __FILE__ ).'admin/librairies/simple_html_dom.php';

foreach (TeamDAO::getInstance()->getAllTeams() as $team) {
    Finder::getInstance()->updateMatchesSync($team);
}
?>