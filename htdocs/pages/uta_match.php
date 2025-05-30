<?php 
// error_reporting(E_ALL^E_NOTICE);
include_once 'includes/teamstats.php';
// include('includes/uta_functions.php');
$matchcode = isset($_GET['matchcode']) ? my_addslashes($_GET['matchcode']) : '';
global $t_match, $t_pinfo, $t_player, $t_games, $dbversion; // fetch table globals.
// FINAL SCORE brajan 26082005
$score0 ='0';
$score1 ='0';
$sql_matchsummary = "SELECT * FROM ".(isset($t_match) ? $t_match : "uts_match")." WHERE matchmode = 1 AND matchcode='".$matchcode."' ORDER BY mapsequence";	  
$q_matchsummary = mysql_query($sql_matchsummary) or die("sum:".mysql_error());
$total_time = 0;
$starttime = $endtime = 0;
while ($r_matchsummary = mysql_fetch_array($q_matchsummary)) {
	if ($starttime == 0)
		$starttime = $r_matchsummary['time'];
	$endtime = $r_matchsummary['time'];
	$total_time = $total_time + $r_matchsummary['gametime'];
	$score0 = $r_matchsummary['score0'];
	$score1 = $r_matchsummary['score1'];
	$serverip = $r_matchsummary['serverip'];
	$servername = $r_matchsummary['servername'];
	$serverinfo = $r_matchsummary['serverinfo'];
	$gameinfo = $r_matchsummary['gameinfo'];
	$team1a = htmlspecialchars($r_matchsummary['teamname1']);
	$team1 = "<a class=\"heading\" href=\"./?p=utateams&amp;team=".urlencode($r_matchsummary['teamname1'])."\">".$team1a."</a>";
	$team0a = htmlspecialchars($r_matchsummary['teamname0']);
	$team0 = "<a class=\"heading\" href=\"./?p=utateams&amp;team=".urlencode($r_matchsummary['teamname0'])."\">".$team0a."</a>";
}
$server_info = preg_split('/\n | \r/', $serverinfo, -1, PREG_SPLIT_NO_EMPTY);
// SERVER INFO
if (!isset($format) || (isset($format) && $format != "json")) {
	echo'
	<table border="0" cellpadding="3" cellspacing="3" width="720" style="background-color:#0F1D2F">
	<tbody>
	<tr><td align="center"><a href="unreal://'.$serverip.'">'. $servername.' - '.$serverip.'</a></td></tr>
	<tr><td align="center" class="grey">'.$gameinfo.'</td></tr>
	<tr><td align="center" class="heading"><strong> '.$team0.' '.$score0.' - '.$score1.'  '.$team1.' </strong></td></tr>
	<tr><td align="center" class="grey"><p>'.mdate($starttime).' - '.mdate($endtime).'</p>
		<p>Total playing time: '. GetMinutes($total_time).' minutes</p>
		</td></tr>
	</tbody></table>';
	// SERVER INFO - END
	// MATCHSTATS - START - Cratos 2006-03-26
	// SORTING - Added Brajan 2006-09-15
	echo'<br />
	<table border="0" cellpadding="0" cellspacing="2" width="720">
	<tbody>
	<tr><td class="hlheading" colspan="15" align="center">UTA Player Match Summary</td></tr>';		
	echo'
	<tr class="lggrey"><td align="center"><br/>
	<table border="0" cellpadding="0" cellspacing="2" width="690">
	<tbody>';
	echo'
	<tr class="smheading" style="height:20px">';
	echo'
		<td align="center" rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=pname">Player</a></td>		
		<td align="center" rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=objs">Objs</a></td>
		<td align="center"rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=ass_assist">Assists</a></td>
		<td align="center" colspan="2">Hammerlaunches</td>
		<td align="center" colspan="2">Rocketlaunches</td>
		<td align="center" rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=ass_h_jump">H-Jumps</td>	
		<td align="center" rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=kills">Kills</a></td>
		<td align="center" rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=deaths">Death</a></td>
		<td align="center" rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=maps">Maps</a></td>
		<td align="center" rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=ping">Ping</a></td>
		</tr><tr class="smheading" style="height:20px">		
		<td align="center"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=ass_h_launch">Launcher</a></td>
		<td align="center"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=ass_h_launched">Pass.</a></td>
		<td align="center"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=ass_r_launch">Launcher</a></td>
		<td align="center"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=ass_r_launched">Pass.</a></td>
	</tr>';
} else {
	header('Content-Type: application/json; charset=windows-1252');
	echo "{\r\n";
	echo "  \"matchid\": \"".$matchcode."\",\r\n";
	echo "  \"time_start\": ".$starttime.",\r\n";
	echo "  \"time_end\": ".$endtime.",\r\n";
	echo "  \"duration\": \"".$total_time."\",\r\n";
	echo "  \"score_red\": ".$score0.",\r\n";
	echo "  \"score_blue\": ".$score1.",\r\n";
	echo "  \"server_name\": \"".$servername."\",\r\n";
	echo "  \"server_ip\": \"".$serverip."\",\r\n";
	echo "  \"match_summary\" : [";
}
// brajan 2006-09-15
// Added sorting
// protect against mysql injections using $_GET['sort'] variable
// If $_GET['sort'] is empty or not allowed, "objs" will be used as default
if (isset($format) && $format == "json") {
	$sort_by = "team ASC, pname";
} else {
	$sort_allowed = array("team", "pname", "objs", "ass_assist", "kills", "ass_h_launch", "ass_h_launched", "ass_r_launch", "ass_r_launched", "deaths", "maps", "ping", "ass_h_jump");
	$sort_by = ( (!empty($_GET['sort'])) && (in_array($_GET['sort'], $sort_allowed)) ) ? $_GET['sort'] : 'objs';
}
$d_sql = mysql_query("SELECT table_name FROM information_schema.tables WHERE table_name like '%".(isset($t_discord_players) ? $t_discord_players : "discord_players")."%';");
$dsql_join = mysql_num_rows($d_sql) > 0 ? "LEFT JOIN ".(isset($t_discord_players) ? $t_discord_players : "uts_discord_players")." ON ".(isset($t_player) ? $t_player : "uts_player").".pid = ".(isset($t_discord_players) ? $t_discord_players : "uts_discord_players").".pid " : "";
if (isset($dbversion) && floatval($dbversion) > 5.6) {
	$dsql_fields = mysql_num_rows($d_sql) > 0 ? ", ANY_VALUE(".(isset($t_discord_players) ? $t_discord_players : "uts_discord_players").".fid) AS fid, ANY_VALUE(".(isset($t_discord_players) ? $t_discord_players : "uts_discord_players").".did) AS did" : ", 0 AS fid, 0 AS did"; 
} else {
	$dsql_fields = mysql_num_rows($d_sql) > 0 ? ", ".(isset($t_discord_players) ? $t_discord_players : "uts_discord_players").".fid AS fid, ".(isset($t_discord_players) ? $t_discord_players : "uts_discord_players").".did AS did" : ", 0 AS fid, 0 AS did"; 
}
$sql =  "SELECT 'broken_match_1' AS dohquery, sum(".(isset($t_player) ? $t_player : "uts_player").".frags) AS frags, 
		(sum(".(isset($t_player) ? $t_player : "uts_player").".kills)-sum(".(isset($t_player) ? $t_player : "uts_player").".teamkills)) AS kills, sum(".(isset($t_player) ? $t_player : "uts_player").".deaths) as deaths, avg(".(isset($t_player) ? $t_player : "uts_player").".avgping) as ping, 
		count(".(isset($t_player) ? $t_player : "uts_player").".matchid) AS maps, avg(".(isset($t_player) ? $t_player : "uts_player").".team) AS team,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launch) AS ass_h_launch, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launch) AS ass_r_launch,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launched) AS ass_h_launched, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launched) AS ass_r_launched,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_assist) AS ass_assist, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_jump) AS ass_h_jump, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_obj) AS objs,
		".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id AS pid, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name AS pname, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country AS pcountry ".$dsql_fields."
		FROM ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." INNER JOIN ".(isset($t_player) ? $t_player : "uts_player")." ON ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id = ".(isset($t_player) ? $t_player : "uts_player").".pid
		".$dsql_join."INNER JOIN ".(isset($t_match) ? $t_match : "uts_match")." ON ".(isset($t_player) ? $t_player : "uts_player").".matchid = ".(isset($t_match) ? $t_match : "uts_match").".id AND ".(isset($t_match) ? $t_match : "uts_match").".matchmode = 1 AND ".(isset($t_match) ? $t_match : "uts_match").".matchcode = '".$matchcode."'	
		GROUP BY ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country 
		ORDER BY ".$sort_by." DESC";
					
$q_sql = mysql_query($sql) or die("m1:".mysql_error());
$playercount = 0;
while ($p_sql = mysql_fetch_assoc($q_sql)) 
{	
	$playercount++;
	if ($p_sql['team'] < 0.5 ) {
		$teamname = $team0a; 
		$tr_color = "#4C0000";
		$gameteam = "Red";
	}
	elseif ($p_sql['team'] > 7 ) {
		$teamname = "Spectators"; 
		$tr_color = "#6C6C6C";
		$gameteam = $teamname;
	} 
	else { 
		$teamname = $team1a;
		$tr_color = "#00005C";
		$gameteam = "Blue";
	}
	if ($p_sql['kills']+$p_sql['deaths']==0)
		$effi = '0';
	else
		$effi = (!empty($p_sql['kills'])) ? (float) $p_sql['kills'] / ($p_sql['kills'] + $p_sql['deaths']) * 100 : '0'; // match effi // brajan 2007-05-28
	if (!isset($format) || (isset($format) && $format != "json")) {
		echo '<tr class="grey" style="background-color:'.$tr_color.'; height:20px; vertical-align:middle">';
		echo '<td nowrap align="left"><b>'.FormatPlayerName($p_sql['pcountry'], $p_sql['pid'], $p_sql['pname']).'</b></td>';
		echo '<td nowrap align="center">'.$p_sql['objs'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_assist'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_h_launch'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_h_launched'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_r_launch'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_r_launched'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_h_jump'].'</td>';

		echo '<td nowrap align="center" title="'.$p_sql['pname'].'\'s average eff: '.round($effi, 2).'%">'.$p_sql['kills'].'</td>';
		echo '<td nowrap align="center" title="'.$p_sql['pname'].'\'s average eff: '.round($effi, 2).'%">'.$p_sql['deaths'].'</td>';
		echo '<td nowrap align="center">'.intval($p_sql['maps'] / 2).'</td>';
		echo '<td nowrap align="center">'.intval($p_sql['ping']).'</td>';
		echo'</tr>';
	} else {
		if ($playercount > 1) {
			echo ",";
		}
		echo "\r\n    {\r\n";
		echo "      \"pid\": ".intval($p_sql['pid']).",\r\n";
		echo "      \"fid\": ".(isset($p_sql['fid']) ? intval($p_sql['fid']) : intval(0)).",\r\n";
		echo "      \"did\": ".(isset($p_sql['did']) ? intval($p_sql['did']) : intval(0)).",\r\n";
		echo "      \"playername\": \"".preg_replace('/[\x{0}-\x{1F}]|[\x{22}]/i','',$p_sql['pname'])."\",\r\n";
		echo "      \"country\": \"".$p_sql['pcountry']."\",\r\n";
		echo "      \"teamcode\": ".intval($p_sql['team']).",\r\n";
		echo "      \"team\": \"".$gameteam."\",\r\n";
		echo "      \"teamname\": \"".preg_replace('/[\x{0}-\x{1F}]|[\x{22}]/i','',$teamname)."\",\r\n";
		echo "      \"maps\": ".intval($p_sql['maps']/2).",\r\n";
		echo "      \"ping\": ".intval($p_sql['ping']).",\r\n";
		echo "      \"objectives\": ".$p_sql['objs'].",\r\n";
		echo "      \"assists\": ".$p_sql['ass_assist'].",\r\n";
		echo "      \"kills\": ".$p_sql['kills'].",\r\n";
		echo "      \"deaths\": ".$p_sql['deaths'].",\r\n";
		echo "      \"eff\": ".intval(round($effi, 0))."\r\n";
		echo "    }";
	}
}

// Team Summary
if (!isset($format) || (isset($format) && $format != "json")) {
	echo'<tr class="grey"><td align="center" colspan="12">Match Team Totals</td></tr>';
}
if (isset($dbversion) && floatval($dbversion) > 5.6) {
	// Only Full GROUP BY statements can be used
	$sql =  "SELECT 'broken_match2' AS dohquery, sum(".(isset($t_player) ? $t_player : "uts_player").".frags) as frags, 
		(sum(".(isset($t_player) ? $t_player : "uts_player").".kills)-sum(".(isset($t_player) ? $t_player : "uts_player").".teamkills)) as kills, sum(".(isset($t_player) ? $t_player : "uts_player").".deaths) as deaths, avg(".(isset($t_player) ? $t_player : "uts_player").".avgping) as ping, 
		count(".(isset($t_player) ? $t_player : "uts_player").".matchid) as maps, ".(isset($t_player) ? $t_player : "uts_player").".team as team,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launch) as ass_h_launch, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launch) as ass_r_launch,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launched) as ass_h_launched, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launched) as ass_r_launched,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_assist) as ass_assist, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_jump) as ass_h_jump, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_obj) as objs,
		ANY_VALUE(".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id) AS pid, ANY_VALUE(".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name) AS pname, ANY_VALUE(".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country) AS pcountry
		from ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." inner join ".(isset($t_player) ? $t_player : "uts_player")." on ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id = ".(isset($t_player) ? $t_player : "uts_player").".pid
		inner join ".(isset($t_match) ? $t_match : "uts_match")." on ".(isset($t_player) ? $t_player : "uts_player").".matchid = ".(isset($t_match) ? $t_match : "uts_match").".id AND ".(isset($t_match) ? $t_match : "uts_match").".matchmode = 1 AND ".(isset($t_match) ? $t_match : "uts_match").".matchcode = '".$matchcode."'	
		group by ".(isset($t_player) ? $t_player : "uts_player").".team
		order by ".(isset($t_player) ? $t_player : "uts_player").".team";
} else {
	// Pre 5.7 query (acceptable to use loose grouping)
	$sql =  "SELECT 'broken_match2' AS dohquery, sum(".(isset($t_player) ? $t_player : "uts_player").".frags) as frags, 
		(sum(".(isset($t_player) ? $t_player : "uts_player").".kills)-sum(".(isset($t_player) ? $t_player : "uts_player").".teamkills)) as kills, sum(".(isset($t_player) ? $t_player : "uts_player").".deaths) as deaths, avg(".(isset($t_player) ? $t_player : "uts_player").".avgping) as ping, 
		count(".(isset($t_player) ? $t_player : "uts_player").".matchid) as maps, ".(isset($t_player) ? $t_player : "uts_player").".team as team,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launch) as ass_h_launch, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launch) as ass_r_launch,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launched) as ass_h_launched, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launched) as ass_r_launched,
		sum(".(isset($t_player) ? $t_player : "uts_player").".ass_assist) as ass_assist, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_jump) as ass_h_jump, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_obj) as objs,
		".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id as pid, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name as pname, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country as pcountry
		from ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." inner join ".(isset($t_player) ? $t_player : "uts_player")." on ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id = ".(isset($t_player) ? $t_player : "uts_player").".pid
		inner join ".(isset($t_match) ? $t_match : "uts_match")." on ".(isset($t_player) ? $t_player : "uts_player").".matchid = ".(isset($t_match) ? $t_match : "uts_match").".id AND ".(isset($t_match) ? $t_match : "uts_match").".matchmode = 1 AND ".(isset($t_match) ? $t_match : "uts_match").".matchcode = '".$matchcode."'	
		group by ".(isset($t_player) ? $t_player : "uts_player").".team
		order by ".(isset($t_player) ? $t_player : "uts_player").".team";
}		
$q_sql = mysql_query($sql) or die("m2:".mysql_error());
while ($p_sql = mysql_fetch_assoc($q_sql)) 
{	
	if ($p_sql['team'] < 0.5 ) {
		$teamname = $team0a; 
		$tr_color = "#4C0000";
	}
	elseif ($p_sql['team'] > 7 ) {
		$teamname = "Spectators"; 
		$tr_color = "#6C6C6C";
	} 
	else { 
		$teamname = $team1a;
		$tr_color = "#00005C";
	}
	$clan_effi = (!empty($p_sql['kills'])) ? (float) $p_sql['kills'] / ($p_sql['kills'] + $p_sql['deaths']) * 100 : '0';  // match effi // brajan 2007-05-28
	if (!isset($format) || (isset($format) && $format != "json")) {
		echo '<tr class="grey" style="background-color:'.$tr_color.'; height:20px; vertical-align:middle">';
		echo '<td nowrap align="left"><b>'.$teamname.'</b></td>';
		echo '<td nowrap align="center">'.$p_sql['objs'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_assist'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_h_launch'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_h_launched'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_r_launch'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_r_launched'].'</td>';
		echo '<td nowrap align="center">'.$p_sql['ass_h_jump'].'</td>';
		echo '<td nowrap align="center" title="Average clan eff: '.round($clan_effi, 2).'%">'.$p_sql['kills'].'</td>';
		echo '<td nowrap align="center" title="Average clan eff: '.round($clan_effi, 2).'%">'.$p_sql['deaths'].'</td>';
		echo '<td nowrap align="center"></td>';
		echo '<td nowrap align="center">'.intval($p_sql['ping']).'</td>';
		echo'</tr>';
	}
}

if (!isset($format) || (isset($format) && $format != "json")) {
	echo'</td>';
	echo'</tr></tbody></table>';
	echo'<br/></tbody></table><br><hr>';
} else {
	echo "\r\n  ],\r\n  \"maps\": [\r\n";
}

	
// MATCHSTATS - END
	
$i = 0;
$team0score = 0;
$team1score = 0;

// MAPS INFO - START
$sql_assault = "SELECT * FROM ".(isset($t_match) ? $t_match : "uts_match")." WHERE matchcode = '".$matchcode."' AND matchmode = 1 ORDER BY time, mapsequence, id";
$q_assault = mysql_query($sql_assault) or die("mis:".mysql_error());

global $winclass; // Added winclass for team colours. --// Timo 25/07/05

while ($p_assault = mysql_fetch_assoc($q_assault)) {
	$bcg = ($i % 2) ? ' class="blank"' : ' class="lggrey"';
	$warmup = ($i == 0 && $p_assault['mapsleft'] > $p_assault['matchlength']) ? '&nbsp; &nbsp; <span class="redteamscore" style="background: none; font-weight:bold"> [Warm-up] </span>' : '';
	$mid = $p_assault['id'];
	$ass_id = $p_assault['assaultid'];
	$gametime = $p_assault['gametime'];
	unset($firstblood);
	$firstblood = $p_assault['firstblood'];

	unset($winclass);
	
	// who was attacking
	$ass_att = $p_assault['ass_att'];
		if($ass_att == 0) {
			$ass_att = $p_assault['teamname0'];
			$ass_att2 = $p_assault['teamname1'];
		} else {
			$ass_att = $p_assault['teamname1'];
			$ass_att2 = $p_assault['teamname0'];
		}
	// result
	$asswin = $p_assault['ass_win'];
	$asswin2 = $p_assault['ass_win'];
		if($asswin == 0) {
			$asswin = "$ass_att2 Successfully Defended in ". GetMinutes($gametime);
			$winclass = ($ass_att==$p_assault['teamname0']) ? ("blueteamscore") : ("redteamscore");
		
		} else {
			$asswin = "$ass_att Successfully Attacked in ". GetMinutes($gametime);
			if($i != 0) $team0score = $team0score+1;
			$winclass = ($ass_att==$p_assault['teamname1']) ? ("blueteamscore") : ("redteamscore");
		}
	
	if($asswin2 == 0) {
		$asswin2 = "$ass_att Successfully Defended in ". GetMinutes($gametime);
	} else {
		$asswin2 = "$ass_att2 Successfully Attacked in ". GetMinutes($gametime);
		if($i != 0)	$team1score = $team1score+1;
	}
	
	$gametime = sec2min($gametime);
	if (!isset($format) || (isset($format) && $format != "json")) {
		echo'
		<table border="0" cellpadding="0" cellspacing="2" width="720">
		<tbody>
		<tr><td class="hlheading" colspan="15" align="center"><a href="?p=match&mid='.$mid.'&o=minfo&map='. str_replace(".unr", "", $p_assault['mapfile']).'">'.$p_assault['mapname'].'</a>'.$warmup.'</td></tr>
		<tr'.$bcg.'><td align="center"><br />';
		uta_ass_objectiveinfo($mid, $ass_att);
		echo '<br />';
		teamstats($mid, 'Match Summary - '.$ass_att.' Team Attacking', 'ass_obj', 'Ass Obj','gamescore DESC', $firstblood);
		echo '<br /></td></tr>
		<tr><td class="'.$winclass.'" style="font-size: 10pt;" colspan="15" align="center">'.$asswin.'</td></tr>
		</tbody></table><br><hr>';
	} else {
		if ($i > 0) {
			echo ",\r\n";
		}
		echo "    {\r\n";
		echo "      \"mid\": ".$mid.",\r\n";
		echo "      \"asid\": \"".$ass_id."\",\r\n";
		echo "      \"map\": \"".str_replace(".unr", "", $p_assault['mapfile'])."\",\r\n";
		echo "      \"map_name\": \"".$p_assault['mapname']."\",\r\n";
		if (strlen($warmup) > 0) {
			echo "      \"round\": 1,\r\n";
			echo "      \"is_warmup\": true,\r\n";
		} else {
			echo "      \"round\": ".(($i%2) == 1 ? 1 : 2).",\r\n";
			echo "      \"is_warmup\": false,\r\n";
		}
		echo "      \"attacking_team_name\": \"".$ass_att."\",\r\n";
		echo "      \"defending_team_name\": \"".$ass_att2."\",\r\n";
		echo "      \"winning_team_name\": \"".($p_assault['ass_win'] == $p_assault['ass_att'] ? $ass_att : $ass_att2)."\",\r\n";
		echo "      \"completed_time\": \"".$gametime."\"\r\n";
		echo "    }";
	}
	$i++;
	$content = explode('<br />', $p_assault['serverinfo']);
} // end while
if (!isset($format) || (isset($format) && $format != "json")) {
	echo "<span class=\"text2\">&sup1; = Player scored &quot;First Blood&quot;</span>";
} else {
	echo "\r\n  ]\r\n}";
}
// MAPS INFO - END
