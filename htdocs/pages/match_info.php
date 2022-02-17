<?php 
$r_info = small_query("SELECT teamgame, t0score, t1score, t2score, t3score FROM uts_match WHERE id = '$mid'");
if (!$r_info) die("Match not found");
$teamgame = ($r_info['teamgame'] == 'True') ? true : false;

$GLOBALS['gid'] = $gid;
$_GLOBALS['gid'] = $gid;
$GLOBALS['gamename'] = $gamename;
$_GLOBALS['gamename'] = $gamename;

include_once ("pages/match_info_server.php");
include('includes/teamstats.php');
switch($real_gamename) {
	case "Assault":
	case "Assault (insta)":
	case "Assault (pro)":
		// defer server_stats output
		include("pages/match_info_ass.php");
		break;
		
	case "Capture the Flag":
	case "Capture the Flag (insta)":
		$matchinfo = server_stats($mid);
		include("pages/match_info_ctf.php");
		teamstats($mid, 'Match Summary');
  		break;
		
	case "Domination":
	case "Domination (insta)":
		$matchinfo = server_stats($mid);
		teamstats($mid, 'Match Summary', 'dom_cp', 'Dom Pts');
		break;
	
	case "JailBreak":
	case "JailBreak (insta)":
		$matchinfo = server_stats($mid);
		teamstats($mid, 'Match Summary', 'ass_obj', 'Team Releases');
		break;
		
	default:
		$matchinfo = server_stats($mid);
		if ($teamgame) {
			teamstats($mid, 'Match Summary');
   		} else {
			teamstats($mid, 'Player Summary');
		}
}
	

if (substr($gamename,0,7) == "Assault") {
	include("pages/match_info_other2.php");
} else {
	include("pages/match_info_other.php");
}

if ($real_gamename == "Capture the Flag" or $real_gamename== "Capture the Flag (insta)") {
   include("pages/match_report.php");
}

?>
