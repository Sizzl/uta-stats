<?php
include ("includes/config.php");
// include ("includes/uta_functions.php");
include ("uta_recordzone_filters.php");

if ($_GET['team'])
	$teamname = addslashes($_GET['team']);

function InvertSort($curr_field, $filter, $sort) {
	if ($curr_field != $filter) return(($curr_field == "mapfile") ? "ASC" : "DESC");
	if ($sort == 'ASC') return('DESC');
	return('ASC');
}

function SortPic($curr_field, $filter, $sort) {
	if ($curr_field != $filter) return;
	$fname = 'images/s_'. strtolower($sort) .'.png';
	if (!file_exists($fname)) return;
	return('&nbsp;<img src="'. $fname .'" border="0" width="11" height="9" alt="" title="('.strtolower($sort).'ending)">');
}


if ($teamname)
	$thiscolspan = "17";
else
	$thiscolspan = "5";

echo'<table 
class="box" border="0" cellpadding="2" cellspacing="1"> <tbody>
	<tr>
		<td 
class="heading" colspan="'.$thiscolspan.'" align="center">UTA Team Viewer</td></tr>';
echo'	<tr>
		<td 
class="heading" colspan="'.$thiscolspan.'" align="center" valign="bottom">';
echo '<br />';
uta_rz_FilterFormMini(); // Show Filter form

	if (!$teamname)
	{
		$team_sql = "(SELECT DISTINCT teamname0 As teams FROM uts_match
	WHERE teamname0 <> '' AND teamname0 <> 'Red' AND teamname0 <> 'Red Team' ".$record_condition_gametypes."
	ORDER BY teams) UNION
	(SELECT DISTINCT teamname1 As teams FROM uts_match
	WHERE teamname1 <> '' AND teamname1 <> 'Blue' AND teamname1 <> 'Blue Team' AND teamname1 <> '(none)' ".$record_condition_gametypes."
	ORDER BY teams);";
		debugprint($team_sql,"SQL","37");
		$team_query = mysql_query($team_sql) OR die(mysql_error());
		if (mysql_num_rows($team_query))
		{
			echo '</td></tr>
			<tr>
				<td class="smheading" align="center" colspan="5">All teams:-</td>
			</tr>';
			$i = 0;
			while ($teams = mysql_fetch_object($team_query))
			{
				if (intval($i%5)==0)
				{
					if ($i > 0)
						echo '</tr>';
					echo "\r\n";
					echo '<tr>';
					echo "\r\n";
				}
				echo '<td class="grey" align="center" nowrap="nowrap"><a class="grey" href="?p='.$_GET['p'].'&team='.urlencode($teams->teams).'">'.htmlspecialchars($teams->teams).'</a></td>';
				echo "\r\n";
				$i++;
			}
			while (intval($i%5)!=0)
			{
				echo '<td class="grey">&nbsp;</td>';
				$i++;
			}
			if ($i > 0)
				echo '</tr>';
		}
		else
			echo '</td></tr>
			<tr>
				<td class="smheading" align="center" colspan="'.$thiscolspan.'">(No teams available)</td>
			</tr>';
	}
	else
	{
		echo '</td></tr>
		<tr>
			<td class="medheading" align="center" colspan="'.$thiscolspan.'"><br />Viewing team:- &nbsp;
				<a class="medheading" href="?p='.$_GET['p'].'&team='.urlencode($teamname).'">'.htmlspecialchars($teamname).'</a>
				<br />&nbsp;
			</td>
		</tr>';
		unset($players_matches);
		unset($players_teams);

		$players_sql = "SELECT 'fixed_teamviewer' as dohquery, 
					uts_match.id AS matchid, uts_match.teamname0, uts_match.teamname1, uts_player.pid, uts_pinfo . *
				FROM uts_player
				INNER JOIN uts_pinfo ON ( uts_player.pid = uts_pinfo.id )
				INNER JOIN uts_match ON ( uts_player.matchid = uts_match.id )
				WHERE uts_pinfo.name LIKE '%®' AND 
					uts_match.matchmode=1 AND (
						(uts_match.`teamname0` = '".addslashes($teamname)."' AND uts_player.team=0) OR
						(uts_match.`teamname1` = '".addslashes($teamname)."' AND uts_player.team=1)
					) ".$record_condition_gametypes."
				GROUP BY uts_player.pid;";
		if (1==1) { // save me fixing the brackets
/*
		$matches_sql = "SELECT `id`, `teamname0`, `teamname1` FROM `uts_match` WHERE (`teamname0` = '".addslashes($teamname)."' OR `teamname1` = '".addslashes($teamname)."') AND matchmode='1' ".$record_condition_gametypes.";";
		$matches_query = mysql_query($matches_sql) or die(mysql_error());
		if (mysql_num_rows($matches_query))
		{
			while ($matches = mysql_fetch_object($matches_query))
			{
				$players_matches[] = $matches->id;
				if ($matches->teamname1==$teamname)
					$players_teams[] = "1";
				else
					$players_teams[] = "0";
			}
			$players_sql = "SELECT 'broken_teamviewer' AS dohquery, uts_player.pid, uts_pinfo . * 
					FROM uts_player
					INNER JOIN uts_pinfo ON ( uts_player.pid = uts_pinfo.id ) 
					WHERE uts_pinfo.name LIKE '%®' AND (";

 			for ($i=0;$i<count($players_matches);$i++)
			{
				$players_sql .="(
							uts_player.matchid = '".$players_matches[$i]."'
							AND uts_player.team = '".$players_teams[$i]."'
						)
						OR ";
			}

			$players_sql .="(
						uts_player.matchid = '-1'
						AND uts_player.team = '-1'
					)";

			$players_sql .= ")
					 GROUP BY uts_player.pid;"; */
			debugprint($players_sql,"SQL","121");
// 			echo "<!-- SQL: ".$players_sql." -->\r\n";
			$players_query = mysql_query($players_sql) or die(mysql_error());
			if (mysql_num_rows($players_query))
			{
				$plclass = "smheading";
				$opclass = "smheading"; // was grey2
				$mpclass = "smheading";
				$spclass = "searchform\" style=\"font-weight: bold";
				echo '		<tr>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="150" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="30" height="1" alt=" " /></td>
					<td align="center" valign="bottom"><img src="images/spacer.gif" width="150" height="1" alt=" " /></td>
				</tr>';
				echo '		<tr>
					<td class="'.$plclass.'" rowspan="2" align="center" width="150" valign="middle">&nbsp;</td>
					<td class="'.$plclass.'" rowspan="2" align="center" width="150" valign="middle">Player</td>
					<td class="'.$plclass.'" align="center" width="210" colspan="7">This Month</td>
					<td class="'.$opclass.'" style="font-weight: bold;" align="center" width="210" colspan="7"><span class="'.$spclass.'">Total</span></td>
					<td class="'.$plclass.'" rowspan="2" align="center" width="150" valign="middle">Last Match</td>
				</tr>';
				echo '		<tr>
					<td class="'.$plclass.'" width="30" align="center" '.OverlibPrintHint('Score').'><span class="'.$mpclass.'">S</span></td>
					<td class="'.$plclass.'" width="30" align="center" '.OverlibPrintHint('F').'><span class="'.$mpclass.'">F</span></td>
					<td class="'.$plclass.'" width="30" align="center" '.OverlibPrintHint('EFF').'><span class="'.$mpclass.'">E</span></td>
					<td class="'.$plclass.'" width="30" align="center" '.OverlibPrintHint('ACC').'><span class="'.$mpclass.'">A</span></td>
					<td class="'.$plclass.'" width="30" align="center" '.OverlibPrintHint('TTL').'><span class="'.$mpclass.'">T</span></td>
					<td class="'.$plclass.'" width="30" align="center" '.OverlibPrintHint('Maps').'><span class="'.$mpclass.'">M</span></td>
					<td class="'.$plclass.'" width="30" align="center" '.OverlibPrintHint('Hours').'><span class="'.$mpclass.'">H</span></td>
					<td class="'.$opclass.'" style="font-weight: bold;" width="30" align="center" '.OverlibPrintHint('Score').'><span class="'.$spclass.'">S</span></td>
					<td class="'.$opclass.'" style="font-weight: bold;" width="30" align="center" '.OverlibPrintHint('F').'><span class="'.$spclass.'">F</span></td>
					<td class="'.$opclass.'" style="font-weight: bold;" width="30" align="center" '.OverlibPrintHint('EFF').'><span class="'.$spclass.'">E</span></td>
					<td class="'.$opclass.'" style="font-weight: bold;" width="30" align="center" '.OverlibPrintHint('ACC').'><span class="'.$spclass.'">A</span></td>
					<td class="'.$opclass.'" style="font-weight: bold;" width="30" align="center" '.OverlibPrintHint('TTL').'><span class="'.$spclass.'">T</span></td>
					<td class="'.$opclass.'" style="font-weight: bold;" width="30" align="center" '.OverlibPrintHint('Maps').'><span class="'.$spclass.'">M</span></td>
					<td class="'.$opclass.'" style="font-weight: bold;" width="30" align="center" '.OverlibPrintHint('Hours').'><span class="'.$spclass.'">H</span></td>
				</tr>';

				$thismonth_start = date("Ym")."01000000";
				$thismonth_end   = date("Ymt")."235959";
				$i = 0;
				while ($player = mysql_fetch_object($players_query))
				{
					$playerid = $player->id;
					$plclass = (intval($i%2)==0) ? ("greyhuman") : ("darkhuman");
					$opclass = (intval($i%2)==0) ? ("greyhuman") : ("darkhuman");
// 					$opclass = (intval($i%2)==0) ? ("darkgrey2") : ("grey2");
					$mpclass = "";
					$spclass = "searchform";
					echo '		<tr>
						<td class="'.$plclass.'" align="center" width="30">'.FlagImage($player->country,false).'</td>
						<td class="'.$plclass.'" align="center" width="150"><a class="'.$plclass.'" href="?p=pinfo&pid='.$player->pid.'">'.htmlspecialchars($player->name).'</a></td>';
$sql_plist = "SELECT SUM(p.gamescore) AS gamescore, SUM(p.frags) AS frags,
SUM(p.kills) AS kills, SUM(p.deaths) AS deaths,
SUM(p.suicides) AS suicides, SUM(p.teamkills) AS teamkills, SUM(p.kills+p.deaths+p.suicides+p.teamkills) AS sumeff,
AVG(p.accuracy) AS accuracy, AVG(p.ttl) AS ttl,
COUNT(p.id) AS games, SUM(p.gametime) as gametime
FROM uts_player AS p, uts_match AS m WHERE p.pid = '$playerid'
 AND p.matchid = m.id AND (m.time >= '$thismonth_start' AND m.time <= '$thismonth_end');";
					debugprint($sql_plist,"SQL","182 [".$playerid."]");
				        $tme = "-";
				        $eff = "-";
				        $acc = "-";
				        $ttl = "-";
					$scr = "-";
					$frg = "-";
					$mtc = "-";

					$q_plist = mysql_query($sql_plist) or die(mysql_error());
					if (mysql_num_rows($q_plist))
					{
						$r_plist = mysql_fetch_array($q_plist);
						if ($r_plist[gametime])
						        $tme = sec2hour($r_plist[gametime]);	
						if ($r_plist[sumeff] > 0 && $r_plist[kills] > 0)
						        $eff = get_dp($r_plist[kills]/$r_plist[sumeff]*100);
						if ($r_plist[accuracy])
						        $acc = get_dp($r_plist[accuracy]);
						if ($r_plist[ttl])
						        $ttl = GetMinutes($r_plist[ttl]);
						if ($r_plist[gamescore])
							$scr = $r_plist[gamescore];
						if ($r_plist[frags])
							$frg = $r_plist[frags];
						if ($r_plist[games])
							$mtc = $r_plist[games];
					}
					echo '
						<td class="'.$plclass.'" width="30" align="center">'.$scr.'</td>'; // thismonth S
					echo '
						<td class="'.$plclass.'" width="30" align="center">'.$frg.'</td>'; // thismonth F
					echo '
						<td class="'.$plclass.'" width="30" align="center">'.$eff.'</td>'; // thismonth Eff
					echo '
						<td class="'.$plclass.'" width="30" align="center">'.$acc.'</td>'; // thismonth Acc
					echo '
						<td class="'.$plclass.'" width="30" align="center">'.$ttl.'</td>'; // thismonth TTL
					echo '
						<td class="'.$plclass.'" width="30" align="center">'.$mtc.'</td>'; // thismonth M
					echo '
						<td class="'.$plclass.'" width="30" align="center">'.$tme.'</td>'; // thismonth H
$sql_plist = "SELECT SUM(p.gamescore) AS gamescore, SUM(p.frags) AS frags,
SUM(p.kills) AS kills, SUM(p.deaths) AS deaths,
SUM(p.suicides) AS suicides, SUM(p.teamkills) AS teamkills, SUM(kills+deaths+suicides+teamkills) AS sumeff,
AVG(p.accuracy) AS accuracy, AVG(p.ttl) AS ttl,
COUNT(p.id) AS games, SUM(p.gametime) as gametime
FROM uts_player AS p WHERE p.pid = '$playerid';";
					debugprint($sql_plist,"SQL","226 [".$playerid."]");
				        $tme = "-";
				        $eff = "-";
				        $acc = "-";
				        $ttl = "-";
					$scr = "-";
					$frg = "-";	
					$mtc = "-";

					$q_plist = mysql_query($sql_plist) or die(mysql_error());
					if (mysql_num_rows($q_plist))
					{
						$r_plist = mysql_fetch_array($q_plist);
						if ($r_plist[gametime])
						        $tme = sec2hour($r_plist[gametime]);	
						if ($r_plist[sumeff] > 0 && $r_plist[kills] > 0)
						        $eff = get_dp($r_plist[kills]/$r_plist[sumeff]*100);
						if ($r_plist[accuracy])
						        $acc = get_dp($r_plist[accuracy]);
						if ($r_plist[ttl])
						        $ttl = GetMinutes($r_plist[ttl]);
						if ($r_plist[gamescore])
							$scr = $r_plist[gamescore];
						if ($r_plist[frags])
							$frg = $r_plist[frags];
						if ($r_plist[games])
							$mtc = $r_plist[games];
					}
					echo '
						<td class="'.$opclass.'" width="30" align="center"><span class="'.$spclass.'">'.$scr.'</span></td>'; // totals S
					echo '
						<td class="'.$opclass.'" width="30" align="center"><span class="'.$spclass.'">'.$frg.'</span></td>'; // totals F
					echo '
						<td class="'.$opclass.'" width="30" align="center"><span class="'.$spclass.'">'.$eff.'</span></td>'; // totals Eff
					echo '
						<td class="'.$opclass.'" width="30" align="center"><span class="'.$spclass.'">'.$acc.'</span></td>'; // totals Acc
					echo '
						<td class="'.$opclass.'" width="30" align="center"><span class="'.$spclass.'">'.$ttl.'</span></td>'; // totals TTL
					echo '
						<td class="'.$opclass.'" width="30" align="center"><span class="'.$spclass.'">'.$mtc.'</span></td>'; // totals M
					echo '
						<td class="'.$opclass.'" width="30" align="center"><span class="'.$spclass.'">'.$tme.'</span></td>'; // totals H
					$lastmatch = "-";

					$sql_recent = "SELECT m.id, m.time, g.name AS gamename, m.mapfile, INET_NTOA(p.ip) AS ip,
					m.servername, m.serverip FROM uts_match m, uts_player p, uts_games g
					WHERE p.pid = '$playerid' AND m.id = p.matchid AND m.gid = g.id ORDER BY time DESC LIMIT 0,1";
					debugprint($sql_recent,"SQL","267 [".$playerid."]");
					$q_recent = mysql_query($sql_recent) or die(mysql_error());
					if (mysql_num_rows($q_recent))
					{
						$r_recent = mysql_fetch_array($q_recent);
					        $r_time = mdate($r_recent[time]);
					        $r_mapfile = un_ut($r_recent[mapfile]);
					        $r_servername =  get_short_servername($r_recent[servername]);
					        $r_serverip = $r_recent[serverip];
						$r_time = str_replace("at","<br />at",$r_time);
						$lastmatch = "<a class=\"".$plclass."\" href=\"./?p=match&amp;mid=".$r_recent[id]."\">".$r_time."</a>";
						unset($r_recent,$r_time,$r_mapfile,$r_servername,$r_serverip);
					}

					echo '
						<td class="'.$plclass.'" align="center" nowrap="nowrap" width="150">
							'.$lastmatch.'
						</td>'; // last match
					echo '
					</tr>';	
					$i++;
				}
			}
			else
			{
				echo '<tr>
					<td class="smheading" align="center" colspan="'.$thiscolspan.'">(No Players Found)</td>
				</tr>';
			}
		}
		else
		{
			echo '<tr>
				<td class="smheading" align="center" colspan="'.$thiscolspan.'">(No Matches Found)</td>
			</tr>';
		}
			echo '<tr>
				<td class="smheading" align="center" colspan="'.$thiscolspan.'"><span class="smheading">(<a class="smheading" href="?p='.$_GET['p'].'">Return to Team List</a>)</span></td>
			</tr>';


	}
echo'
</tbody></table><br />';
?>
