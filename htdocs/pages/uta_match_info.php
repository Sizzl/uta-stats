<?php 
global $t_match, $t_pinfo, $t_player, $t_games; // fetch table globals.
$r_info = small_query("SELECT teamgame, t0score, t1score, t2score, t3score, matchcode FROM ".(isset($t_match) ? $t_match : "uts_match")." WHERE id = '".$mid."'");
if (!$r_info) die("Match not found");
$teamgame = ($r_info['teamgame'] == 'True') ? true : false;

include_once("pages/match_info_server.php");

$GLOBALS['gid'] = $gid;
$_GLOBALS['gid'] = $gid;
$GLOBALS['gamename'] = $gamename;
$_GLOBALS['gamename'] = $gamename;

include_once('includes/teamstats.php');
switch($real_gamename) {
	case "Assault":
	case "Assault (insta)":
	case "Assault (pro)":
		include_once("pages/match_info_ass.php");
		break;
	default:
		if ($teamgame) {
			teamstats($mid, 'Match Summary');
   		} else {
			teamstats($mid, 'Player Summary');
		}
}
	

if (substr($gamename,0,7) == "Assault") {
	include_once("pages/match_info_other2.php");
} else {
	include_once("pages/match_info_other.php");
}
?>
