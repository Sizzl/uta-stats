<?php 
if (empty($import_adminkey) or isset($_REQUEST['import_adminkey']) or $import_adminkey != $adminkey) die('bla');
// --// Sizzl notes: Server list is actually built from grouping of matches by server name + ip, 
// --// then any match id is used to fetch the remaining options
$options['title'] = 'Delete match';
$options['showlist'] = true;
$i = 0;
$options['vars'][$i]['name'] = 'server';
$options['vars'][$i]['type'] = 'server';
$options['vars'][$i]['prompt'] = 'Choose the server where the match took place:';
$options['vars'][$i]['caption'] = 'Server:';
$i++;
$options['vars'][$i]['name'] = 'mid';
$options['vars'][$i]['type'] = 'match';
$options['vars'][$i]['whereserver'] = 'server';
$options['vars'][$i]['prompt'] = 'Choose the match to delete:';
$options['vars'][$i]['caption'] = 'Match to delete:';
$i++;

$results = adminselect($options);


$matchid = $results['mid'];

echo'<br><table border="0" cellpadding="1" cellspacing="2" width="600">
<tr>
	<td class="smheading" align="center" colspan="2">Deleting Match ID '.$matchid.'</td>
</tr>
<tr>
	<td class="smheading" align="left" width="200">Adjusting Rankings</td>';
$sql_radjust = "SELECT `pid`, `gid`, `rank` FROM `uts_player` WHERE `matchid` = '".$matchid."';";
$q_radjust = mysql_query($sql_radjust) or die("dm:getplayer".msysql_error());
$pids = array();
while ($r_radjust = mysql_fetch_array($q_radjust)) {

	$pid = $r_radjust['pid'];
	$pids[] = $pid;
	$gid = $r_radjust['gid'];
	$rank = $r_radjust['rank'];

	$sql_crank = small_query("SELECT `id`, `rank`, `matches` FROM `uts_rank` WHERE `pid` = '".$pid."' AND `gid` = '".$gid."';");
	if (!$sql_crank) continue;
	
	$rid = $sql_crank['id'];
	$newrank = $sql_crank['rank']-$rank;
	$oldrank = $sql_crank['rank'];
	$matchcount = $sql_crank['matches']-1;

	// Crude, but this will be corrected the next time import is run
	mysql_query("UPDATE `uts_rank` SET `rank` = '".$newrank."', `prevrank` = '".$oldrank."', `matches` = '".$matchcount."' WHERE `id` = '".$rid."';") or die("dm:rank1".mysql_error());
	mysql_query("DELETE FROM `uts_rank` WHERE `matches` = '0';") or die("dm:rank2".mysql_error());
}
echo'<td class="grey" align="left" width="400">Done</td>
</tr>
<tr>
	<td class="smheading" align="left" width="200">Removing Match Record:</td>';

// --// Sizzl: Feb 2022 - Cache the year for ws updates
$year = date("Y");
$r_match = small_query("SELECT `year` FROM `uts_match` WHERE `id` = '".$matchid."';");
if ($sql_match)
	$year = $r_match['year'];

mysql_query("DELETE FROM `uts_match` WHERE `id` = '".$matchid."';") or die("dm:match".mysql_error());
echo'<td class="grey" align="left" width="400">Done</td>
</tr>';

// CRATOS: Remove Objective Stats
echo'
<tr>
	<td class="smheading" align="left" width="200">Removing Objective stats:</td>';
mysql_query("DELETE FROM `uts_smartass_objstats` WHERE `matchid` = '".$matchid."';") or die("dm:objs".mysql_error());
	echo'<td class="grey" align="left" width="400">Done</td>
</tr>';


echo'
<tr>
	<td class="smheading" align="left" width="200">Removing Player Records:</td>';
mysql_query("DELETE FROM `uts_player` WHERE `matchid` = '".$matchid."';") or die("dm:player".mysql_error());
	echo'<td class="grey" align="left" width="400">Done</td>
</tr>
<tr>
	<td class="smheading" align="left" width="200">Removing Kill Matrix Entries:</td>';
mysql_query("DELETE FROM `uts_killsmatrix` WHERE `matchid` = '".$matchid."';") or die("dm:km".mysql_error());
	echo'<td class="grey" align="left" width="400">Done</td>
</tr>
<tr>
	<td class="smheading" align="left" width="200">Removing Weapon Stats:</td>';
mysql_query("DELETE FROM `uts_weaponstats` WHERE `matchid` = '".$matchid."';") or die("dm:ws".mysql_error());
	echo'<td class="grey" align="left" width="400">Done</td>
</tr>
<tr>
	<td class="smheading" align="left" width="200">Amending Player Weapon Stats:</td>';
foreach($pids as $pid) {
	mysql_query("DELETE FROM `uts_weaponstats` WHERE `matchid` IN ('".$matchid."','0') AND `year` = '0' AND `pid` = '".$pid."'") or die("dm:pws1".mysql_error());

	$q_weaponstats = mysql_query("SELECT weapon, SUM(kills) AS kills, SUM(shots) AS shots, SUM(hits) as hits, SUM(damage) as damage, AVG(acc) AS acc FROM uts_weaponstats WHERE pid = '".$pid."' GROUP BY weapon") or die("dm:pws2".mysql_error());
	while ($r_weaponstats = mysql_fetch_array($q_weaponstats)) {
		mysql_query("INSERT INTO uts_weaponstats SET matchid='0', year='0' pid='".$pid."', weapon='${r_weaponstats['weapon']}', kills='${r_weaponstats['kills']}', shots='${r_weaponstats['shots']}', hits='${r_weaponstats['hits']}', damage='${r_weaponstats['damage']}', acc='${r_weaponstats['acc']}'") or die("dm:pws3".mysql_error());
	}
	$q_weaponstats = mysql_query("SELECT weapon, SUM(kills) AS kills, SUM(shots) AS shots, SUM(hits) as hits, SUM(damage) as damage, AVG(acc) AS acc FROM uts_weaponstats WHERE pid = '".$pid."' AND year = '".$year."' GROUP BY weapon") or die("dm:pws4".mysql_error());
	while ($r_weaponstats = mysql_fetch_array($q_weaponstats)) {
		mysql_query("INSERT INTO uts_weaponstats SET matchid='0', year='".$year."' pid='".$pid."', weapon='${r_weaponstats['weapon']}', kills='${r_weaponstats['kills']}', shots='${r_weaponstats['shots']}', hits='${r_weaponstats['hits']}', damage='${r_weaponstats['damage']}', acc='${r_weaponstats['acc']}'") or die("dm:pws5".mysql_error());
	}

}
	echo'<td class="grey" align="left" width="400">Done</td>
</tr>
<tr>
	<td class="smheading" align="left" width="200">Amending Global Weapon Stats:</td>';
	mysql_query("DELETE FROM uts_weaponstats WHERE matchid='0' AND pid='0'") or die("dm:gws1".mysql_error());

	$q_weaponstats = mysql_query("SELECT weapon, SUM(kills) AS kills, SUM(shots) AS shots, SUM(hits) as hits, SUM(damage) as damage, AVG(acc) AS acc FROM uts_weaponstats WHERE matchid = '0'  GROUP BY weapon") or die("dm:gws2".mysql_error());
	while ($r_weaponstats = mysql_fetch_array($q_weaponstats)) {
		mysql_query("INSERT INTO uts_weaponstats SET matchid='0', pid='0', weapon='${r_weaponstats['weapon']}', kills='${r_weaponstats['kills']}', shots='${r_weaponstats['shots']}', hits='${r_weaponstats['hits']}', damage='${r_weaponstats['damage']}', acc='${r_weaponstats['acc']}'") or die("dm:gws3".mysql_error());
	}

	echo'<td class="grey" align="left" width="400">Done</td>
</tr>
<tr>
	<td class="smheading" align="center" colspan="2">Match Deleted - <a href="./admin.php?key='.$_REQUEST['key'].'">Go Back To Admin Page</a></td>
</tr></table>';


?>
