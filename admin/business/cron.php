<?php 

namespace Eventus\Admin\Business;
use Eventus\Includes\Datas as DAO;

include_once '../../includes/entities/club.php';
include_once '../../includes/entities/match.php';
include_once '../../includes/entities/team.php';
include_once '../../includes/datas/_masterDAO.php';
include_once '../../includes/datas/teamDAO.php';
include_once '../../includes/datas/clubDAO.php';
include_once '../../includes/datas/matchDAO.php';
include_once '../../admin/business/finder.php';
include_once '../../admin/librairies/simple_html_dom.php';
include_once '../../../../../wp-config.php';

foreach (DAO\TeamDAO::getInstance()->getAllTeams() as $team) {
    Finder::getInstance()->updateMatches($team);
}
update_option('eventus_datetimesynch', date("Y-m-d H:i:s"), false);

if (get_option("eventus_emailnotif")){
	$message = "<p>The update has been succesfully done with: <b>". count(file('../../finder.log')) ."</b> issue(s), the <b>".date("d/m/Y")."</b> at <b>".date("H:i:s")."</b>.</p>";
	$content = explode("\n", file_get_contents('../../finder.log'));
	($content ? array_pop($content) : '' );
	$message .= ($content ? "<ul><li>".str_replace("[", "<b>[", str_replace("]", "]</b>", implode("</li><li>", $content)))."</ul>" : '');
	mail(
		get_option("eventus_emailnotif"), 
		"Eventus - Update ".date("d/m/Y H:i:s"), 
		$message, 
		"From: eventus@".$_SERVER['HTTP_HOST']. "\r\n" .
		"Reply-To: eventus@".$_SERVER['HTTP_HOST'] ."\r\n" .
		"X-Mailer: PHP/" . phpversion()
	);
}

?> 