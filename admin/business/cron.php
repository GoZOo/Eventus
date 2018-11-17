<?php 

namespace Eventus\Admin\Business;
use Eventus\Includes\Datas as DAO;

include_once plugin_dir_path( __FILE__ ).'includes/constants.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/club.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/match.php';
include_once plugin_dir_path( __FILE__ ).'includes/entities/team.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/_masterDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/teamDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/clubDAO.php';
include_once plugin_dir_path( __FILE__ ).'includes/datas/matchDAO.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/finder.php';
include_once plugin_dir_path( __FILE__ ).'admin/business/postHandler.php';
include_once plugin_dir_path( __FILE__ ).'admin/librairies/simple_html_dom.php';

foreach (DAO\TeamDAO::getInstance()->getAllTeams() as $team) {
    Finder::getInstance()->updateMatches($team);
}
?>