<?php 
// error_reporting(E_ALL^E_NOTICE);
include_once('includes/teamstats.php');
// include('includes/uta_functions.php');
$matchcode = $_GET['matchcode'];
global $t_match, $t_pinfo, $t_player, $t_games; // fetch table globals.
// FINAL SCORE brajan 26082005
$score0 ='0';
$score1 ='0';
		$sql_matchsummary = "SELECT * FROM ".(isset($t_match) ? $t_match : "uts_match")." WHERE matchmode = 1 AND matchcode='".$matchcode."' ORDER BY mapsequence";	  
		$q_matchsummary = mysql_query($sql_matchsummary) or die(mysql_error());
		$total_time = 0;
		$starttime = $endtime = 0;
			while ($r_matchsummary = mysql_fetch_array($q_matchsummary)) {
				if ($starttime == 0)
					$starttime = $r_matchsummary[time];
				$endtime = $r_matchsummary[time];
				$total_time = $total_time + $r_matchsummary[gametime];
			 	$score0 = $r_matchsummary[score0];
			  	$score1 = $r_matchsummary[score1];
			  	$serverip = $r_matchsummary[serverip];
			  	$servername = $r_matchsummary[servername];
			  	$serverinfo = $r_matchsummary[serverinfo];
			  	$gameinfo = $r_matchsummary[gameinfo];
			  	$team1a = htmlspecialchars($r_matchsummary[teamname1]);
				$team1 = "<a class=\"heading\" href=\"./?p=utateams&amp;team=".urlencode($r_matchsummary[teamname1])."\">".$team1a."</a>";
				$team0a = htmlspecialchars($r_matchsummary[teamname0]);
				$team0 = "<a class=\"heading\" href=\"./?p=utateams&amp;team=".urlencode($r_matchsummary[teamname0])."\">".$team0a."</a>";
		  	}
		  	$server_info = preg_split('/\n | \r/', $serverinfo, -1, PREG_SPLIT_NO_EMPTY);
	// SERVER INFO
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
		<td  align="center" rowspan="2"><a href="?p=uta_match&matchcode='.$matchcode.'&sort=pname">Player</a></td>		
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
	// brajan 2006-09-15
	// Added sorting
	// protect against mysql injections using $_GET['sort'] variable
	// If $_GET['sort'] is empty or not allowed, "objs" will be used as default 
	$sort_allowed = array("pname", "objs", "ass_assist", "kills", "ass_h_launch", "ass_h_launched", "ass_r_launch", "ass_r_launched", "deaths", "maps", "ping", "ass_h_jump");
	$sort_by = ( (!empty($_GET['sort'])) && (in_array($_GET['sort'], $sort_allowed)) ) ? $_GET['sort'] : 'objs';
	
	$sql =  "SELECT 'broken_match_1' AS dohquery, sum(".(isset($t_player) ? $t_player : "uts_player").".frags) as frags, 
			(sum(".(isset($t_player) ? $t_player : "uts_player").".kills)-sum(".(isset($t_player) ? $t_player : "uts_player").".teamkills)) as kills, sum(".(isset($t_player) ? $t_player : "uts_player").".deaths) as deaths, avg(".(isset($t_player) ? $t_player : "uts_player").".avgping) as ping, 
			count(".(isset($t_player) ? $t_player : "uts_player").".matchid) as maps, avg(".(isset($t_player) ? $t_player : "uts_player").".team) as team,
			sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launch) as ass_h_launch, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launch) as ass_r_launch,
			sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launched) as ass_h_launched, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launched) as ass_r_launched,
			sum(".(isset($t_player) ? $t_player : "uts_player").".ass_assist) as ass_assist, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_jump) as ass_h_jump, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_obj) as objs,
			".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id as pid, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name as pname, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country as pcountry
			from ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." inner join ".(isset($t_player) ? $t_player : "uts_player")." on ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id = ".(isset($t_player) ? $t_player : "uts_player").".pid
			inner join ".(isset($t_match) ? $t_match : "uts_match")." on ".(isset($t_player) ? $t_player : "uts_player").".matchid = ".(isset($t_match) ? $t_match : "uts_match").".id and ".(isset($t_match) ? $t_match : "uts_match").".matchmode = 1 and ".(isset($t_match) ? $t_match : "uts_match").".matchcode = '".$matchcode."'	
			group by ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country 
			ORDER BY ".$sort_by." DESC";
						
	$q_sql = mysql_query($sql) or die(mysql_error());	
	while ($p_sql = mysql_fetch_assoc($q_sql)) 
	{	
		if ($p_sql[team] < 0.5 ) {
			$teamname = $team0a; 
			$tr_color = "#4C0000";
			} 
		else{ 
			$teamname = $team1a;
			$tr_color = "#00005C";
		}
		
		echo'<tr class="grey" style="background-color:'.$tr_color.'; height:20px; vertical-align:middle">';
		echo '<td nowrap align="left"><b>'.FormatPlayerName($p_sql[pcountry], $p_sql[pid], $p_sql[pname]).'</b></td>';
		echo '<td nowrap align="center">'.$p_sql[objs].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_assist].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_h_launch].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_h_launched].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_r_launch].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_r_launched].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_h_jump].'</td>';
		$effi = (!empty($p_sql[kills])) ? (float) $p_sql[kills] / ($p_sql[kills] + $p_sql[deaths]) * 100 : '0'; // match effi // brajan 2007-05-28
		echo '<td nowrap align="center" title="'.$p_sql[pname].'\'s average eff: '.round($effi, 2).'%">'.$p_sql[kills].'</td>';
		echo '<td nowrap align="center" title="'.$p_sql[pname].'\'s average eff: '.round($effi, 2).'%">'.$p_sql[deaths].'</td>';
		echo '<td nowrap align="center">'.intval($p_sql[maps] / 2).'</td>';
		echo '<td nowrap align="center">'.intval($p_sql[ping]).'</td>';
		echo'</tr>';
	}
	
	// Team Summary
	echo'<tr class="grey"><td align="center" colspan="12">Match Team Totals</td></tr>';	
	$sql =  "SELECT 'broken_match2' AS dohquery, sum(".(isset($t_player) ? $t_player : "uts_player").".frags) as frags, 
			(sum(".(isset($t_player) ? $t_player : "uts_player").".kills)-sum(".(isset($t_player) ? $t_player : "uts_player").".teamkills)) as kills, sum(".(isset($t_player) ? $t_player : "uts_player").".deaths) as deaths, avg(".(isset($t_player) ? $t_player : "uts_player").".avgping) as ping, 
			count(".(isset($t_player) ? $t_player : "uts_player").".matchid) as maps, ".(isset($t_player) ? $t_player : "uts_player").".team as team,
			sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launch) as ass_h_launch, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launch) as ass_r_launch,
			sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launched) as ass_h_launched, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launched) as ass_r_launched,
			sum(".(isset($t_player) ? $t_player : "uts_player").".ass_assist) as ass_assist, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_jump) as ass_h_jump, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_obj) as objs,
			".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id as pid, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name as pname, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country as pcountry
			from ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." inner join ".(isset($t_player) ? $t_player : "uts_player")." on ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id = ".(isset($t_player) ? $t_player : "uts_player").".pid
			inner join ".(isset($t_match) ? $t_match : "uts_match")." on ".(isset($t_player) ? $t_player : "uts_player").".matchid = ".(isset($t_match) ? $t_match : "uts_match").".id and ".(isset($t_match) ? $t_match : "uts_match").".matchmode = 1 and ".(isset($t_match) ? $t_match : "uts_match").".matchcode = '".$matchcode."'	
			group by ".(isset($t_player) ? $t_player : "uts_player").".team
			order by ".(isset($t_player) ? $t_player : "uts_player").".team";
			
	$q_sql = mysql_query($sql) or die(mysql_error());
	while ($p_sql = mysql_fetch_assoc($q_sql)) 
	{	
		if ($p_sql[team] < 0.5 ) {
			$teamname = $team0a; 
			$tr_color = "#4C0000";
			} 
		else{ 
			$teamname = $team1a;
			$tr_color = "#00005C";
		}
		
		echo'<tr class="grey" style="background-color:'.$tr_color.'; height:20px; vertical-align:middle">';
		echo '<td nowrap align="left"><b>'.$teamname.'</b></td>';
		echo '<td nowrap align="center">'.$p_sql[objs].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_assist].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_h_launch].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_h_launched].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_r_launch].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_r_launched].'</td>';
		echo '<td nowrap align="center">'.$p_sql[ass_h_jump].'</td>';
		$clan_effi = (!empty($p_sql[kills])) ? (float) $p_sql[kills] / ($p_sql[kills] + $p_sql[deaths]) * 100 : '0';  // match effi // brajan 2007-05-28
		echo '<td nowrap align="center" title="Average clan eff: '.round($clan_effi, 2).'%">'.$p_sql[kills].'</td>';
		echo '<td nowrap align="center" title="Average clan eff: '.round($clan_effi, 2).'%">'.$p_sql[deaths].'</td>';
		echo '<td nowrap align="center"></td>';
		echo '<td nowrap align="center">'.intval($p_sql[ping]).'</td>';
		echo'</tr>';
	}
	
	
	echo'</td>';
	echo'</tr></tbody></table>';
	echo'<br/></tbody></table><br><hr>';
		
	// MATCHSTATS - END
	
$i = 0;
$team0score = 0;
$team1score = 0;

// MAPS INFO - START
$sql_assault = "SELECT * FROM ".(isset($t_match) ? $t_match : "uts_match")." WHERE matchcode = '".$matchcode."' AND matchmode = 1 ORDER BY time, mapsequence, id";
$q_assault = mysql_query($sql_assault) or die(mysql_error());

global $winclass; // Added winclass for team colours. --// Timo 25/07/05

while ($p_assault = mysql_fetch_assoc($q_assault)) {
	$bcg = ($i % 2) ? ' class="blank"' : ' class="lggrey"';
	$warmup = ($i == 0) ? '&nbsp; &nbsp; <span class="redteamscore" style="background: none; font-weight:bold"> [Warm-up] </span>' : '';
	$mid = $p_assault[id];
	$ass_id = $p_assault[assaultid];
	$gametime = $p_assault[gametime];
	unset($firstblood);
	$firstblood = $p_assault[firstblood];

	unset($winclass);
	
	// who was attacking
	$ass_att = $p_assault[ass_att];
		if($ass_att == 0) {
			$ass_att = $p_assault[teamname0];
			$ass_att2 = $p_assault[teamname1];
		} else {
			$ass_att = $p_assault[teamname1];
			$ass_att2 = $p_assault[teamname0];
		}
	// result
	$asswin = $p_assault[ass_win];
	$asswin2 = $p_assault[ass_win];
		if($asswin == 0) {
			$asswin = "$ass_att2 Successfully Defended in ". GetMinutes($gametime);
			$winclass = ($ass_att==$p_assault[teamname0]) ? ("blueteamscore") : ("redteamscore");
		
		} else {
			$asswin = "$ass_att Successfully Attacked in ". GetMinutes($gametime);
			if($i != 0) $team0score = $team0score+1;
			$winclass = ($ass_att==$p_assault[teamname1]) ? ("blueteamscore") : ("redteamscore");
		}
	
	if($asswin2 == 0) {
		$asswin2 = "$ass_att Successfully Defended in ". GetMinutes($gametime);
	} else {
		$asswin2 = "$ass_att2 Successfully Attacked in ". GetMinutes($gametime);
		if($i != 0)	$team1score = $team1score+1;
	}
	
	$gametime = sec2min($gametime);
	$gametime2 = sec2min($gametime2);
	echo'
	<table border="0" cellpadding="0" cellspacing="2" width="720">
	  <tbody>
	  <tr><td class="hlheading" colspan="15" align="center"><a href="?p=minfo&map='. str_replace(".unr", "", $p_assault[mapfile]).'">'.$p_assault[mapname].'</a>'.$warmup.'</td></tr>
	  <tr'.$bcg.'><td align="center"><br />';
	  uta_ass_objectiveinfo($mid, $ass_att);
		echo '<br />';
	  teamstats($mid, 'Match Summary - '.$ass_att.' Team Attacking', 'ass_obj', 'Ass Obj','gamescore DESC', $firstblood);
	echo '<br /></td></tr>
	<tr><td class="'.$winclass.'" style="font-size: 10pt;" colspan="15" align="center">'.$asswin.'</td></tr>
	</tbody></table><br><hr>';
	$i++;
	$content = explode('<br />', $p_assault[serverinfo]);
} // end while

echo "<span class=\"text2\">&sup1; = Player scored &quot;First Blood&quot;</span>";
// MAPS INFO - END
//echo '<span class="txttitle">'.str_replace('Admin: ','', $content[0]).'</span>';
?>
