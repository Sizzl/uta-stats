<?php
include_once ("includes/mysql-shim/lib/mysql.php");
include ("includes/functions.php");
include ("includes/config.php");

echo'
<table class="box" border="0" cellpadding="1" cellspacing="1" width="450">
  <tbody><tr>
    <td class="heading" colspan="5" align="center">UTStats Summary</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Players</td>
    <td class="smheading" align="center">Servers</td>
    <td class="smheading" align="center">Maps</td>
    <td class="smheading" align="center">Matches Logged</td>
    <td class="smheading" align="center">Player Hours</td>
  </tr>';

// Get Summary Info
$r_playercount = small_query("SELECT COUNT(*) AS result FROM uts_pinfo");
$playercount = $r_playercount['result'];
$servercount = small_count("SELECT DISTINCT servername FROM uts_match");	
$mapcount = small_count("SELECT COUNT(mapfile) AS result FROM uts_match GROUP BY mapfile");
$r_matchcount = small_query("SELECT COUNT(*) AS result FROM uts_match");
$matchcount= $r_matchcount['result'];
$hourscount = small_query("SELECT SUM(gametime) AS result FROM uts_player");	

$gametime = sec2hour($hourscount['result']);

echo'
  <tr>
    <td class="lggrey" align="center">'.$playercount.'</td>
    <td class="lggrey" align="center">'.$servercount.'</td>
    <td class="lggrey" align="center">'.$mapcount.'</td>
    <td class="lggrey" align="center">'.$matchcount.'</td>
    <td class="lggrey" align="center">'.$gametime.'</td>
  </tr>
</tbody></table>
<br />

<table border="0" width="600">
  <tbody><tr>
    <td align="center">
      <div class="titlemsg">
      <p>Welcome to UTStats.<br>
      Here you can look up information on UT matches and players.<br>
      Select a category from the column on the left.<br></p>
      </div>

	<table class="box" border="0" cellpadding="1" cellspacing="2">
	  <tbody><tr>
		<td class="heading" colspan="8" align="center">Game Summary</td>
	  </tr>
	  <tr>
		<td class="smheading" align="center" width="150">Match Type</td>
		<td class="smheading" align="center" width="52">Frags</td>
		<td class="smheading" align="center" width="52">Kills</td>
		<td class="smheading" align="center" width="52">Suicides</td>
		<td class="smheading" align="center" width="40">Team Kills</td>
		<td class="smheading" align="center" width="52">Matches</td>
		<td class="smheading" align="center" width="52">Game Hours</td>
	  </tr>';

if (isset($dbversion) && floatval($dbversion) > 5.6) {
	$sql_gamesummary = "SELECT 'home_makestatic.php(gamesummary)' AS `script_name`, ANY_VALUE(`g`.`id`) AS `gid`, `g`.`name` AS `gamename`, SUM(p.frags) AS frags, SUM(p.kills) AS kills, SUM(p.suicides) AS suicides, SUM(p.teamkills) AS teamkills, COUNT(DISTINCT p.matchid) AS matchcount
		FROM uts_player AS p, uts_games AS g WHERE p.gid = g.id GROUP BY `g`.`name` ORDER BY `g`.`name` ASC";  // 4sec
} else {
	$sql_gamesummary = "SELECT 'home_makestatic.php(gamesummary)' AS script_name, g.id AS gid, g.name AS gamename, SUM(p.frags) AS frags, SUM(p.kills) AS kills, SUM(p.suicides) AS suicides, SUM(p.teamkills) AS teamkills, COUNT(DISTINCT p.matchid) AS matchcount
		FROM uts_player AS p, uts_games AS g WHERE p.gid = g.id GROUP BY `g`.`name` ORDER BY `gamename` ASC";  // 5.3sec
}
$q_gamesummary = mysql_query($sql_gamesummary) or die(mysql_error());
$sumfrags = $sumkills = $sumsuicides = $sumteamkills = $summatchcount = $sumgametime = 0;
while ($r_gamesummary = mysql_fetch_array($q_gamesummary)) {

	$gid = $r_gamesummary['gid'];

	$q_gametime = small_query("SELECT SUM(gametime) AS gametime FROM uts_match WHERE gid = '".$gid."'");
	$gametime = sec2hour($q_gametime['gametime']);
	
	$sumfrags+=$r_gamesummary['frags'];
	$sumkills+=$r_gamesummary['kills'];
	$sumsuicides+=$r_gamesummary['suicides'];
	$sumteamkills+=$r_gamesummary['teamkills'];
	$summatchcount+=$r_gamesummary['matchcount'];
	$sumgametime+=$gametime;

	echo'<tr><td class="dark" align="center">'.$r_gamesummary['gamename'].'</td>
		<td class="grey" align="center">'.$r_gamesummary['frags'].'</td>
		<td class="grey" align="center">'.$r_gamesummary['kills'].'</td>
		<td class="grey" align="center">'.$r_gamesummary['suicides'].'</td>
		<td class="grey" align="center">'.$r_gamesummary['teamkills'].'</td>
		<td class="grey" align="center">'.$r_gamesummary['matchcount'].'</td>
		<td class="grey" align="center">'.$gametime.'</td>';
}

//$totalsummary = small_query("SELECT SUM(p.frags) AS frags, SUM(p.kills) AS kills, SUM(p.suicides) AS suicides, SUM(p.teamkills) AS teamkills, COUNT(DISTINCT p.matchid) AS matchcount, SUM(p.gametime) AS gametime
//FROM uts_player AS p, uts_games AS g WHERE p.gid = g.id");	// 5sec

//$q_gametime = small_query("SELECT SUM(gametime) AS gametime FROM uts_match");
//$gametime = sec2hour($q_gametime['gametime']);	

echo'  <tr>
		<td class="dark" align="center"><b>Totals for All Players</b></td>
		<td class="darkgrey" align="center">'.$sumfrags.'</td>
		<td class="darkgrey" align="center">'.$sumkills.'</td>
		<td class="darkgrey" align="center">'.$sumsuicides.'</td>
		<td class="darkgrey" align="center">'.$sumteamkills.'</td>
		<td class="darkgrey" align="center">'.$summatchcount.'</td>
		<td class="darkgrey" align="center">'.$sumgametime.'</td>
	  </tr>
	</tbody></table>
</tbody></table><br>';

// Do graph stuff
$gtitle = "Across All Servers";
$bgwhere = "id >= 0";

include("pages/graph_mbreakdown.php");
?>
