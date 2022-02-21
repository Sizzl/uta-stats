<?php 
include_once ("includes/config.php");
include_once ("includes/uta_functions.php");
include_once ("includes/functions.php");
if (!isset($_GET['noheader']))
{
	if ($_SESSION["themelocation"]) // Themed header --// 19/07/05 Timo: Added customisable header (& sidebar !)
	{
		if (file_exists($_SESSION["themelocation"]."header.php"))
			include($_SESSION["themelocation"]."header.php");
		else
			include ("includes/header.php");
	}
	else
		include ("includes/header.php");
}
$pagehandler = mks($_GET["p"]);

if (checkLoadavg()==true)
	$pagehandler = "busy";

switch ($pagehandler)

{
	case "": page(); break; 			// Our opening page

	case "recent": recent(); break;			// list of recent public games, 30 in date order
	
	case "utapugrecent": utapugrecent(); break;	// list of recent PUG games, 30 in date order
	case "utapugsummary": utapugsummary(); break;	// summary of all PUG games
	
	case "utapugeventeaster": utapugevents(); break;
	case "utapugeventhalloween": utapugevents(); break;
	case "utapugeventxmas": utapugevents(); break;
	case "utapugeventloveday": utapugevents(); break;

	case "utarecent": utarecent(); break;	// list of recent league games, 30 in date order
	case "uta_match": uta_match(); break;	// list of recent league games, 30 in date order
	case "utarecordzone": utarecordzone(); break;	// UTA Recordzone
	case "utateam": utateam(); break;		// UTA Team viewer
	case "utateams": utateam(); break;		// UTA Team viewer
	case "utapinfo": utapinfo(); break;		// UTA Player info
	
	case "match": match(); break;		// Single Match stats
	case "matchp": matchp(); break;		// Player stats for single match
	case "report": report(); break;		// Report generator

	case "rank": rank(); break;			// Rankings
	case "ext_rank": ext_rank(); break;	// Extended rankings

	case "servers": servers(); break;	// Server listings
	case "sinfo": sinfo(); break;		// Server info
	case "squery": squery(); break;		// Server query page

	case "players": players(); break;	// Players list
	case "psearch": psearch(); break;	// Player search
	case "pinfo": pinfo(); break;		// Player info
	case "pexplrank": pexplrank(); break;		// Explain ranking

	case "maps": maps(); break;			// Maps list
	case "minfo": minfo(); break;		// Map info

	case "totals": totals(false); break;		// Totals summary
	case "totalslive": totals(true); break;		// Totals summary

	case "watchlist": watchlist(); break;		// The viewer's watchlist
	
	case "credits": credits(); break;	// Credits
	case "help": help(); break;			// Help Page
	case "busy": busy(); break;		// Server over-loaded with this rubbish.
}
function utapugrecent()
{
	include("pages/uta_pug_recent.php");
}
function utapugsummary()
{
	include("pages/uta_pug_sum.php");
}
function utapugevents()
{
	include("pages/uta_pug_events.php");
}

function utapinfo()
{
	include("pages/uta_players_info.php");
}

function page()
{
	if (is_file("pages/home.php") && filesize("pages/home.php")>1024) {
		include("pages/home.php");
	} else {
		echo "<meta http-equiv=\"refresh\" content=\"1; URL='index.php?p=utapugrecent'\" />
<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"710\">
  <tbody><tr>
    <td class=\"heading\" align=\"center\">Stats Summary Currently Unavailable - Switching to Live Results...</td>
  </tr>
</tbody></table>";
	}
}

function admin()
{
	include("admin.php");
}

function recent()
{
	include("pages/recent.php");
}

function utarecent()
{
	include("pages/uta_recent.php");
}

function uta_match()
{
	include("pages/uta_match.php");
}

function match()
{
	include("pages/match.php");
}

function matchp()
{
	include("pages/match_player.php");
}

function report()
{
	include("pages/report.php");
}

function rank()
{
	include("pages/rank.php");
}

function ext_rank()
{
	include("pages/rank_extended.php");
}

function servers()
{
	include("pages/servers.php");
}

function sinfo()
{
	include("pages/servers_info.php");
}

function squery()
{
	include("pages/servers_query.php");
}

function players()
{
	include("pages/players.php");
}

function psearch()
{
	include("pages/players_search.php");
}

function pinfo()
{
	include("pages/players_info.php");
}

function pexplrank()
{
	include("pages/players_explain_ranking.php");
}

function pmatchs()
{
	include("pages/players_matchs.php");
}

function pmaps()
{
	include("pages/players_maps.php");
}

function maps()
{
	include("pages/maps.php");
}

function minfo()
{
	include("pages/maps_info.php");
}

function totals($live=false)
{
	if ($live)
	{
		include("pages/totals_live.php");
	}
	else
	{
		if (is_file("pages/totals.php") && filesize("pages/totals.php") > 1024)
		{
			include("pages/totals.php");
		}
		else
		{
			echo "<meta http-equiv=\"refresh\" content=\"4; URL='index.php?p=utapugrecent'\" /> 
	<table border=\"0\" cellpadding=\"1\" cellspacing=\"2\" width=\"710\">
	  <tbody><tr>
	    <td class=\"heading\" align=\"center\">Totals Summary - Currently Unavailable</td>
	  </tr>
	</tbody></table>";
		}		
	}
}

function watchlist()
{
	include("pages/watchlist.php");
}

function credits()
{
	include("pages/credits.php");
}

function help()
{
	include("pages/help.php");
}
function busy()
{
	include("pages/busy.php");
}

function utarecordzone()
{	
	include("pages/uta_recordzone.php");
}

function utateam()
{	
	include("pages/uta_teamviewer.php");
}
if ($_SESSION["themelocation"]) // Themed footer --// 19/07/05 Timo: Added customisable footer
{
	if (file_exists($_SESSION["themelocation"]."footer.php"))
		include($_SESSION["themelocation"]."footer.php");
	else
		include("includes/footer.php");
}
else
	include("includes/footer.php");
?>
