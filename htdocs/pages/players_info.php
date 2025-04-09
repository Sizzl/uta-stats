<?php 
include_once ("includes/config.php");
include_once ("includes/uta_functions.php");
global $t_rank, $t_match, $t_pinfo, $t_player, $t_games; // fetch table globals.
global $admin_ip, $pic_enable, $htmlcp, $rank_year, $t_width, $dbversion;
$t_width="710";
$pid = isset($pid) ? addslashes($pid) : addslashes($_GET['pid']);
$rank_year = 0;
if (isset($_GET['year']) && strlen($_GET['year'])==4 && is_numeric($_GET['year']))
	$rank_year = intval(my_addslashes($_GET['year']));
if ($rank_year < 2005 || $rank_year > date("Y"))
	$rank_year = 0;

$r_info = small_query("SELECT name, country, banned FROM ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." WHERE id = '".$pid."'");
if (!$r_info) {
	echo "Player not found";
	include_once("includes/footer.php");
	exit;
}

if ($r_info['banned'] == 'Y') {
	if (isset($is_admin) && $is_admin) {
		echo "Warning: Banned player - Admin override<br>";
	} else {
		echo "Sorry, this player has been banned!";
		include_once("includes/footer.php");
		exit;
	}
}

$playername = $r_info['name'];

if (isset($_GET['togglewatch'])) {
	$status = ToggleWatchStatus($pid);
	include_once('includes/header.php');
	if ($status == 1) {
		echo htmlentities($playername,ENT_SUBSTITUTE,$htmlcp) ." has been added to your watchlist";
	} else {
		echo htmlentities($playername,ENT_SUBSTITUTE,$htmlcp) ." has been removed from your watchlist";
	}
	echo "<br>";
	$target = $PHP_SELF .'?p=pinfo&amp;pid='. $pid;
	echo 'You will be taken back to the <a href="'. $target .'">'. htmlentities($playername,ENT_SUBSTITUTE,$htmlcp) .'\'s page</a> in a moment.';
	echo '<meta http-equiv="refresh" content="2;URL='. $target .'">';
	return;
}


if (isset($_GET['pics'])) {
	$gid = $_GET['gid'];
	if (!$pic_enable) {
		echo "Sorry, pictures are disabled by the administrator";
		return;
	}
	$oururl = $_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
	$oururl = str_replace("index.php", "", $oururl);

	echo '<div class="pages" align="left">';
	require_once('includes/config_pic.php');
	$disp = false;
	foreach($pic as $num => $options) {
		if	(!$options['enabled']) continue;
		if ($options['gidrequired'] && empty($gid)) continue;
		$disp = true;
		$pinfourl = "http://".$oururl."?p=pinfo&pid=".$pid;
		$lgid = ($options['gidrequired']) ? $gid : 0;
		$imgurl = "http://".$oururl."pic.php/".$num."/".$pid."/".$lgid."/.".$options['output']['type'];
		echo '<table class="box" border="0" cellspacing="2" cellpadding="1" align="center"><tr>';
		echo '<td colspan="2" align="center"><img src="'. $imgurl .'" border="0" /></td>';
		echo '</tr><tr>';
		echo '<td class="smheading">BB Code:</td><td><textarea rows="1" cols="85">'. str_replace(' ', '&nbsp;', htmlentities('[url='.$pinfourl.'][img]'.$imgurl.'[/img][/url]')) .'</textarea></td>';
		echo '</tr><tr>';
		echo '<td class="smheading">HTML Code:</td><td><textarea rows="1" cols="85">'. str_replace(' ', '&nbsp;', htmlentities('<a href="'.$pinfourl.'" target="_blank"><img src="'.$imgurl.'" border="0"></img></a>')) .'</textarea></td>';
		echo '</tr></table><br><br>';
	}
	if (!$disp) echo "Sorry, no pictures in this category";
	echo '</div>';
	return;
}

echo'
<table class="box" border="0" cellpadding="1" cellspacing="2" width="'.$t_width.'">
  <tbody><tr>
    <td class="heading" colspan="12" align="center">'.($rank_year > 0 ? $rank_year." Annual" : "Career").' Summary for '.FlagImage($r_info['country'], false).' '.htmlentities($playername,ENT_SUBSTITUTE,$htmlcp).' ';

if (PlayerOnWatchlist($pid)) {
 	echo '<a href="?p=pinfo&amp;pid='.$pid.'&amp;togglewatch=1&amp;noheader=1"><img src="images/unwatch.png" width="17" height="11" border="0" alt="" title="You are watching this player. Click to remove from your watchlist."></a>';
} else {
 	echo '<a href="?p=pinfo&amp;pid='.$pid.'&amp;togglewatch=1&amp;noheader=1"><img src="images/watch.png" width="17" height="11" border="0" alt="" title="Click to add this player to your watchlist."></a>';
}

echo '
	 </td>
  </tr>
  <tr>
	 <td class="smheading" align="center">Match Type</td>
    <td class="smheading" align="center">Score</td>
    <td class="smheading" align="center" '.OverlibPrintHint('F').'>F</td>
    <td class="smheading" align="center" '.OverlibPrintHint('K').'>K</td>
    <td class="smheading" align="center" '.OverlibPrintHint('D').'>D</td>
    <td class="smheading" align="center" '.OverlibPrintHint('S').'>S</td>
    <td class="smheading" align="center" '.OverlibPrintHint('TK').'>TK</td>
    <td class="smheading" align="center" '.OverlibPrintHint('EFF').'>Eff.</td>
    <td class="smheading" align="center" '.OverlibPrintHint('ACC').'>Acc.</td>
    <td class="smheading" align="center" '.OverlibPrintHint('TTL').'>Avg TTL</td>
    <td class="smheading" align="center">Matches</td>
    <td class="smheading" align="center">Hours</td>
  </tr>';

$sql_plist = "SELECT 'players_info.php(list)' AS script_name, g.name AS gamename, SUM(p.gamescore) AS gamescore, 
		SUM(p.frags) AS frags, SUM(p.kills) AS kills, SUM(p.deaths) AS deaths,
		SUM(p.suicides) AS suicides, SUM(p.teamkills) AS teamkills, (SUM(p.kills)+SUM(p.deaths)+SUM(p.suicides)+SUM(p.teamkills)) AS sumeff, 
		AVG(p.accuracy) AS accuracy, AVG(p.ttl) AS ttl, COUNT(p.id) AS games, SUM(p.gametime) as gametime
		FROM ".(isset($t_player) ? $t_player : "uts_player")." p,
			".(isset($t_games) ? $t_games : "uts_games")." g
			".($rank_year > 0 ? ", ".(isset($t_match) ? $t_match : "uts_match")." m" : "")."
		WHERE p.gid = g.id AND p.pid = '".$pid."' ".($rank_year > 0 ? "AND m.id = p.matchid AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959'" : "")."
		GROUP BY p.gid";

$q_plist = mysql_query($sql_plist) or die(mysql_error());
while ($r_plist = mysql_fetch_array($q_plist)) {

	$gametime = sec2hour($r_plist['gametime']);
	$eff = ($r_plist['sumeff']>0 ? get_dp($r_plist['kills']/$r_plist['sumeff']*100) : 0); // Fixed division by zero for non ignored matches --// Timo 01/05/07
	$acc = get_dp($r_plist['accuracy']);
	$ttl = GetMinutes($r_plist['ttl']);
	if (strpos($r_plist['gamename'],'Assault') !== false)
		$playedAS = true;

	if (strpos($r_plist['gamename'],'Flag') !== false)
		$playedCTF = true;

	echo'<tr>
		<td class="dark" align="center">'.$r_plist['gamename'].'</td>
		<td class="grey" align="center">'.$r_plist['gamescore'].'</td>
		<td class="grey" align="center">'.$r_plist['frags'].'</td>
		<td class="grey" align="center">'.$r_plist['kills'].'</td>
		<td class="grey" align="center">'.$r_plist['deaths'].'</td>
		<td class="grey" align="center">'.$r_plist['suicides'].'</td>
		<td class="grey" align="center">'.$r_plist['teamkills'].'</td>
		<td class="grey" align="center">'.$eff.'</td>
		<td class="grey" align="center">'.$acc.'</td>
		<td class="grey" align="center">'.$ttl.'</td>
		<td class="grey" align="center">'.$r_plist['games'].'</td>
		<td class="grey" align="center">'.$gametime.'</td>
	  </tr>';
}

$r_sumplist = small_query("SELECT SUM(p.gamescore) AS gamescore, SUM(p.frags) AS frags, SUM(p.kills) AS kills, SUM(p.deaths) AS deaths,
SUM(p.suicides) AS suicides, SUM(p.teamkills) AS teamkills, SUM(p.kills+p.deaths+p.suicides+p.teamkills) AS sumeff,
AVG(p.accuracy) AS accuracy, AVG(p.ttl) AS ttl, COUNT(p.id) AS games, SUM(p.gametime) as gametime
FROM ".(isset($t_player) ? $t_player : "uts_player")." p ".($rank_year > 0 ? ", ".(isset($t_match) ? $t_match : "uts_match")." m" : "")."
WHERE p.pid = '".$pid."' ".($rank_year > 0 ? "AND m.id = p.matchid AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959'" : ""));

$gametime = sec2hour($r_sumplist['gametime']);
if ($r_sumplist['sumeff']>0) {
	$eff = get_dp($r_sumplist['kills']/$r_sumplist['sumeff']*100);
} else {
	$eff = 0;
}
$acc = get_dp($r_sumplist['accuracy']);
$ttl = GetMinutes($r_sumplist['ttl']);

  echo'
  <tr>
    <td class="dark" align="center"><b>Totals</b></td>
	<td class="darkgrey" align="center">'.$r_sumplist['gamescore'].'</td>
	<td class="darkgrey" align="center">'.$r_sumplist['frags'].'</td>
	<td class="darkgrey" align="center">'.$r_sumplist['kills'].'</td>
	<td class="darkgrey" align="center">'.$r_sumplist['deaths'].'</td>
	<td class="darkgrey" align="center">'.$r_sumplist['suicides'].'</td>
	<td class="darkgrey" align="center">'.$r_sumplist['teamkills'].'</td>
	<td class="darkgrey" align="center">'.$eff.'</td>
	<td class="darkgrey" align="center">'.$acc.'</td>
	<td class="darkgrey" align="center">'.$ttl.'</td>
	<td class="darkgrey" align="center">'.$r_sumplist['games'].'</td>
	<td class="darkgrey" align="center">'.$gametime.'</td>
  </tr>
</tbody></table>';

if (isset($playedCTF))
{
// BRAJAN 13-01-2006
	$sql_cdatot = zero_out(small_query("SELECT SUM(flag_taken) AS flag_taken,
		 SUM(flag_pickedup) AS flag_pickedup, SUM(flag_dropped) AS flag_dropped, SUM(flag_assist) AS flag_assist, SUM(flag_cover) AS flag_cover,
		 SUM(flag_seal) AS flag_seal, SUM(flag_capture) AS flag_capture, SUM(flag_kill)as flag_kill,
		 SUM(flag_return) AS flag_return FROM ".(isset($t_player) ? $t_player : "uts_player")." WHERE pid = '".$pid."'"));
	if (isset($sql_cdatot) && count($sql_cdatot))
	{
		echo'
<br>
<table class="box" border="0" cellpadding="1" cellspacing="2" width="'.$t_width.'">
  <tbody><tr>
    <td class="heading" colspan="11" align="center">CTF Events Summary</td>
  </tr>
  <tr>
    <td class="dark" align="center">Flag Takes</td>
    <td class="dark" align="center">Flag Pickups</td>
    <td class="dark" align="center">Flag Drops</td>
    <td class="dark" align="center">Flag Assists</td>
    <td class="dark" align="center">Flag Covers</td>
    <td class="dark" align="center">Flag Seals</td>
    <td class="dark" align="center">Flag Captures</td>
    <td class="dark" align="center">Flag Kills</td>
    <td class="dark" align="center">Flag Returns</td>
  </tr>';

		echo'
  <tr>
    <td class="grey" align="center">'.$sql_cdatot['flag_taken'].'</td>
    <td class="grey" align="center">'.$sql_cdatot['flag_pickedup'].'</td>
    <td class="grey" align="center">'.$sql_cdatot['flag_dropped'].'</td>
    <td class="grey" align="center">'.$sql_cdatot['flag_assist'].'</td>
    <td class="grey" align="center">'.$sql_cdatot['flag_cover'].'</td>
    <td class="grey" align="center">'.$sql_cdatot['flag_seal'].'</td>
    <td class="grey" align="center">'.$sql_cdatot['flag_capture'].'</td>
    <td class="grey" align="center">'.$sql_cdatot['flag_kill'].'</td>
    <td class="grey" align="center">'.$sql_cdatot['flag_return'].'</td>
  </tr>
</tbody></table>';
	}
}
// CRATOS
if (isset($playedAS))
{
	echo '<br>
<table class="box" border="0" cellpadding="1" cellspacing="2" width="'.$t_width.'">
  <tbody><tr>
    <td class="heading" colspan="8" align="center">Assault Events Summary'.($rank_year > 0 ? " for ".$rank_year : "").'</td>
  </tr>
  <tr>
    <td class="dark" align="center" rowspan="2">Objectives</td>
    <td class="dark" align="center" rowspan="2">Assists</td>
    <td class="dark" align="center" rowspan="2">Hammerjumps</td>
    <td class="dark" align="center" colspan="2">Hammerlaunches</td>
    <td class="dark" align="center" colspan="2">Rocketlaunches</td>
    <td class="dark" align="center" rowspan="2">Coop Suicides</td>
    
  </tr>
  <tr>
    <td class="dark" align="center">Launcher</td>
    <td class="dark" align="center">Passenger</td>
    <td class="dark" align="center">Launcher</td>
    <td class="dark" align="center">Passenger</td>
  </tr>';
  	
	 $sql_cdatot = zero_out(small_query("SELECT 
		SUM(p.ass_obj) as ass_obj, 
		SUM(p.ass_assist) AS ass_assist,	 
		SUM(p.ass_h_jump) AS ass_h_jump, 
		SUM(p.ass_h_launch) AS ass_h_launch, 
		SUM(p.ass_r_launch) AS ass_r_launch, 
		SUM(p.ass_h_launched) AS ass_h_launched, 
		SUM(p.ass_r_launched) AS ass_r_launched, 
		SUM(p.ass_suicide_coop) AS ass_suicide_coop
		FROM ".(isset($t_player) ? $t_player : "uts_player")." p".($rank_year > 0 ? ", ".(isset($t_match) ? $t_match : "uts_match")." m" : "")."
		WHERE p.pid = '".$pid."' ".($rank_year > 0 ? "AND p.matchid = m.id AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959'": "").""));
	
	  echo'
	  <tr>
	    <td class="grey" align="center">'.$sql_cdatot['ass_obj'].'</td>
	    <td class="grey" align="center">'.$sql_cdatot['ass_assist'].'</td>
	    <td class="grey" align="center">'.$sql_cdatot['ass_h_jump'].'</td>
	    <td class="grey" align="center">'.$sql_cdatot['ass_h_launch'].'</td>
	    <td class="grey" align="center">'.$sql_cdatot['ass_r_launch'].'</td>
	    <td class="grey" align="center">'.$sql_cdatot['ass_h_launched'].'</td>
	    <td class="grey" align="center">'.$sql_cdatot['ass_r_launched'].'</td>
	    <td class="grey" align="center">'.$sql_cdatot['ass_suicide_coop'].'</td>
	  </tr>
</tbody></table>'; 
}
echo '
<br>
<table class="box" border="0" cellpadding="0" cellspacing="2" width="'.$t_width.'">
  <tbody><tr>
    <td class="heading" colspan="10" align="center">Special Events'.($rank_year > 0 ? " in ".$rank_year : "").'</td>
  </tr>
  <tr>
    <td class="smheading" align="center" rowspan="2" width="40">First Blood</td>
    <td class="smheading" align="center" colspan="4" width="160" '.OverlibPrintHint('Multis').'>Multis</td>
    <td class="smheading" align="center" colspan="5" width="200" '.OverlibPrintHint('Sprees').'>Sprees</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('DK').'>Dbl</td>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('MK').'>Multi</td>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('UK').'>Ultra</td>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('MOK').'>Mons</td>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('KS').'>Kill</td>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('RA').'>Ram</td>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('DO').'>Dom</td>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('US').'>Uns</td>
    <td class="smheading" align="center" width="40" '.OverlibPrintHint('GL').'>God</td>
  </tr>';

$sql_firstblood = zero_out(small_query("SELECT COUNT(m.id) AS fbcount FROM ".(isset($t_match) ? $t_match : "uts_match")." m WHERE m.firstblood = '".$pid."'".($rank_year > 0 ? " AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959'": "")));

$sql_multis = zero_out(small_query("SELECT SUM(p.spree_double) AS spree_double, SUM(p.spree_multi) AS spree_multi,
			SUM(p.spree_ultra) AS spree_ultra, SUM(p.spree_monster)  AS spree_monster,
			SUM(p.spree_kill) AS spree_kill, SUM(p.spree_rampage) AS spree_rampage, SUM(p.spree_dom) AS spree_dom,
			SUM(p.spree_uns) AS spree_uns, SUM(p.spree_god) AS spree_god
			FROM ".(isset($t_player) ? $t_player : "uts_player")." p".($rank_year > 0 ? ", ".(isset($t_match) ? $t_match : "uts_match")." m" : "")." 
			WHERE p.pid = '".$pid."'".($rank_year > 0 ? " AND p.matchid = m.id AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959'": "")));

  echo'
  <tr>
	<td class="grey" align="center">'.$sql_firstblood['fbcount'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_double'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_multi'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_ultra'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_monster'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_kill'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_rampage'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_dom'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_uns'].'</td>
	<td class="grey" align="center">'.$sql_multis['spree_god'].'</td>
  </tr>
  </tbody></table>
<br>
<table class="box" border="0" cellpadding="0" cellspacing="2" width="'.$t_width.'">
  <tbody><tr>
    <td class="heading" colspan="6" align="center">Pickups Summary'.($rank_year > 0 ? " for ".$rank_year : "").'</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="80">Pads</td>
    <td class="smheading" align="center" width="80">Armour</td>
    <td class="smheading" align="center" width="80">Keg</td>
    <td class="smheading" align="center" width="80">Invisibility</td>
    <td class="smheading" align="center" width="80">Shield Belt</td>
    <td class="smheading" align="center" width="80">Damage Amp</td>
  </tr>';

if ($rank_year > 0)
{
	$r_pickups = zero_out(small_query("SELECT SUM(p.pu_pads) AS pu_pads, SUM(p.pu_armour) AS pu_armour, SUM(p.pu_keg) AS pu_keg,
			SUM(p.pu_invis) AS pu_invis, SUM(p.pu_belt) AS pu_belt, SUM(p.pu_amp) AS pu_amp
			FROM ".(isset($t_player) ? $t_player : "uts_player")." p,
			".(isset($t_match) ? $t_match : "uts_match")." m
			WHERE p.pid = '".$pid."' AND p.matchid = m.id AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959';"));
}
else
{
	$r_pickups = zero_out(small_query("SELECT SUM(pu_pads) AS pu_pads, SUM(pu_armour) AS pu_armour, SUM(pu_keg) AS pu_keg,
			SUM(pu_invis) AS pu_invis, SUM(pu_belt) AS pu_belt, SUM(pu_amp) AS pu_amp
			FROM ".(isset($t_player) ? $t_player : "uts_player")." WHERE pid = '".$pid."';"));
}
  echo'
  <tr>
	<td class="grey" align="center">'.$r_pickups['pu_pads'].'</td>
	<td class="grey" align="center">'.$r_pickups['pu_armour'].'</td>
	<td class="grey" align="center">'.$r_pickups['pu_keg'].'</td>
	<td class="grey" align="center">'.$r_pickups['pu_invis'].'</td>
	<td class="grey" align="center">'.$r_pickups['pu_belt'].'</td>
	<td class="grey" align="center">'.$r_pickups['pu_amp'].'</td>
  </tr>
  </tbody></table>
<br>';

include_once('includes/weaponstats.php');
weaponstats(0, $pid,($rank_year > 0 ? "Weapons Summary for ".$rank_year : "Weapons Summary"));

echo '<br>';

// Do graph stuff
if ($rank_year > 0)
	$bgwhere = "pid = '".$pid."' AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959'";
else
	$bgwhere = "pid = '".$pid."'";

include_once("pages/graph_utapbreakdown.php");

$sn = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];

// Player's all-time ranks --// Timo 13/02/2021 - Added filter to all-time (year=0) for now; may consider adding other ranking tables / columns in future
echo'<table class="box" border="0" cellpadding="1" cellspacing="1">
  <tbody><tr>
    <td class="heading" colspan="6" align="center">'.($rank_year > 0 ? $rank_year : "All Time").' Ranking</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="50">'.htmlentities("N�",ENT_SUBSTITUTE,$htmlcp).'</td>
    <td class="smheading" align="center" width="140">Match Type</td>
    <td class="smheading" align="center" width="80">Rank</td>
    <td class="smheading" align="center" width="50">Matches</td>
	 <td class="smheading" align="center" width="50">Explain</td>';
	 if ($pic_enable && basename($sn) != 'admin.php') echo '<td class="smheading" align="center" width="50">Pics</td>';
echo '</tr>';

$q_ytest = mysql_query("SHOW COLUMNS FROM `".(isset($t_rank) ? $t_rank : "uts_rank")."` LIKE 'year';");
if (mysql_num_rows($q_ytest))
	$where_year = " AND r.year = '".$rank_year."'";
else
	$where_year = "";

$sql_rank = "SELECT g.name AS gamename, r.rank, r.prevrank, r.matches, r.gid, r.pid 
		FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." AS r, ".(isset($t_games) ? $t_games : "uts_games")." AS g 
		WHERE r.gid = g.id AND r.pid = '".$pid."'".$where_year.";";
$q_rank = mysql_query($sql_rank) or die(mysql_error());
while ($r_rank = mysql_fetch_array($q_rank)) {
	$q_no = "SELECT (COUNT(*) + 1) AS no FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." WHERE `year` = '".$rank_year."' AND `gid` = '".$r_rank['gid']."' AND `rank` > '".get_dp($r_rank['rank'])."9';";
	$r_no = small_query($q_no);
	echo'<tr>
				<td class="grey" align="center">'.RankImageOrText($r_rank['pid'], $name, $r_no['no'], $r_rank['gid'], $r_rank['gamename'], false, '%IT%').'</td>
		<td class="grey" align="center">'.$r_rank['gamename'].'</td>
		<td class="grey" align="center">'.get_dp($r_rank['rank']) .' '. RankMovement($r_rank['rank'] - $r_rank['prevrank']) . '</td>
		<td class="grey" align="center">'.$r_rank['matches'].'</td>';
		echo '<td class="grey" align="center"><a class="grey" href="?p=pexplrank&amp;pid='.$pid.'&amp;gid='.$r_rank['gid'].($rank_year > 0 ? "&amp;year=".$rank_year : "").'">(Click)</a></td>';
	if ($pic_enable && basename($sn) != 'admin.php') echo '<td class="grey" align="center"><a class="grey"  href="?p=pinfo&amp;pid='.$pid.'&amp;gid='.$r_rank['gid'].'&amp;pics=1">(Click)</a></td>';
	echo '</tr>';
}

echo '</tbody></table>';

if ($rank_year > 0)
	$r_pings = small_query("SELECT MIN(p.lowping * 1) AS lowping, AVG(p.avgping * 1) AS avgping, MAX(p.highping * 1) AS highping
				FROM ".(isset($t_player) ? $t_player : "uts_player")." p,
				".(isset($t_match) ? $t_match : "uts_match")." m
				WHERE p.pid = $pid AND p.matchid = m.id AND p.lowping > 0 AND p.highping < 65535
				AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959'");
else
	$r_pings = small_query("SELECT MIN(lowping * 1) AS lowping, AVG(avgping * 1) AS avgping, MAX(highping * 1) AS highping FROM ".(isset($t_player) ? $t_player : "uts_player")." WHERE pid = ".$pid." AND lowping > 0 AND highping < 65535");
if ($r_pings && $r_pings['lowping']) {
echo '
	<br>
	<table class="box" border="0" cellpadding="0" cellspacing="2">
	<tbody><tr>
		<td class="heading" colspan="3" align="center">Pings'.($rank_year > 0 ? " in ".$rank_year : "").'</td>
	</tr>
	<tr>
		<td class="smheading" align="center" width="80">Min</td>
		<td class="smheading" align="center" width="80">Avg</td>
		<td class="smheading" align="center" width="80">Max</td>
	</tr>
	<tr>
		<td class="grey" align="center">'.ceil($r_pings['lowping']).'</td>
		<td class="grey" align="center">'.ceil($r_pings['avgping']).'</td>
		<td class="grey" align="center">'.ceil($r_pings['highping']).'</td>
	</tr>
	</tbody></table>';
}




echo'<br><table class="box" border="0" cellpadding="2" cellspacing="1">
  <tbody><tr>
    <td class="heading" colspan="6" align="center">Last 50 maps played'.($rank_year > 0 ? " in ".$rank_year : "" ).'</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="70">Match ID</td>
    <td class="smheading" align="center" width="190">Date/Time</td>  
    <td class="smheading" align="center" width="100">Match Type</td>
    <td class="smheading" align="center" width="150">Map</td>
    <td class="smheading" align="center" width="150">Server</td>';
	if (isset($is_admin) && $is_admin) echo '<td class="smheading" align="center">IP Used</td>';
  echo'</tr>';

$sql_recent = "SELECT m.id, m.time, g.name AS gamename, m.mapfile, INET_NTOA(p.ip) AS ip, m.servername, m.serverip 
		FROM ".(isset($t_match) ? $t_match : "uts_match")." m,
		".(isset($t_player) ? $t_player : "uts_player")." p,
		".(isset($t_games) ? $t_games : "uts_games")." g
		WHERE p.pid = '".$pid."' AND m.id = p.matchid AND m.gid = g.id ".($rank_year > 0 ? "AND m.time >= '".$rank_year."0101000000' AND m.time <= '".$rank_year."1231235959'": "")." 
		ORDER BY time DESC LIMIT 0,50";
$q_recent = mysql_query($sql_recent) or die(mysql_error());
while ($r_recent = mysql_fetch_array($q_recent)) {

	  $r_time = mdate($r_recent['time']);
	  $r_mapfile = un_ut($r_recent['mapfile']);
	  $r_servername =  get_short_servername($r_recent['servername']);
	  $r_serverip = $r_recent['serverip'];

	  echo'
	  <tr>
		<td class="dark" align="center"><a class="darkid" href="./?p=match&amp;mid='.$r_recent['id'].'">'.$r_recent['id'].'</a></td>
		<td class="dark" align="center"><a class="darkhuman" href="./?p=match&amp;mid='.$r_recent['id'].'">'.$r_time.'</a></td>
		<td class="grey" align="center">'.$r_recent['gamename'].'</td>
		<td class="grey" align="center">'.$r_mapfile.'</td>
		<td nowrap class="grey" align="center"><a class="grey" href="./?p=sinfo&amp;serverip='.$r_serverip.'">'.$r_servername.'</a></td>';
		if (isset($is_admin) && $is_admin) echo '<td class="grey" align="center">'. $r_recent['ip'].'</td>';

	  echo '</tr>';
}

echo'
</tbody></table>
';
// IP & NICK CHECKING - START (brajan 20050801)
	if ((isset($is_admin) && $is_admin) || (isset($admin_ip) && in_array($_SERVER['REMOTE_ADDR'], $admin_ip)) ){
		if(isset($_POST['v_pid']))
			$pid = $_POST['v_pid'];
		elseif(isset($_GET['pid']))
			$pid = $_GET['pid'];
echo'<br><table class="box" border="0" cellpadding="1" cellspacing="1">
  <tbody><tr>
    <td class="heading" colspan="6" align="center">Used IPs</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="80">Country</td>
    <td class="smheading" align="center" width="220">IP</td>
    <td class="smheading" align="center" width="140">Host</td>
    <td class="smheading" align="center" width="140">Other nicks</td>
	</tr>';
if (isset($dbversion) && floatval($dbversion) > 5.6)
{
        $zapytanie = "SELECT ip, ANY_VALUE(country) FROM ".(isset($t_player) ? $t_player : "uts_player")." WHERE pid='".$pid."' GROUP BY ip";
}
else
{
        $zapytanie = "SELECT ip, country FROM ".(isset($t_player) ? $t_player : "uts_player")." WHERE pid='".$pid."' GROUP BY ip";
}
$ip_query = mysql_query($zapytanie) or die("adminip1".mysql_error());
while ($ip_row = mysql_fetch_assoc($ip_query)) {
echo'<tr>
	<td class="dark" align="center" valign="top"><img src="images/flags/'.$ip_row['country'].'.png"></td>
	<td class="dark" align="center" valign="top"><a href="./pages/whois.php?q='.long2ip($ip_row['ip']).'" target="_blank">'.long2ip($ip_row['ip']).'</a></td>
	<td class="grey" align="center" valign="top">'.gethostbyaddr(long2ip($ip_row['ip'])).'</td>
	<td class="grey" align="left" valign="top">';
		$zapytanie_id = "SELECT pid FROM ".(isset($t_player) ? $t_player : "uts_player")." WHERE ip='".$ip_row['ip']."' GROUP BY pid";
		$id_query = mysql_query($zapytanie_id) or die(mysql_error());
			while ($id_row = mysql_fetch_assoc($id_query)) {
				
				$zapytanie_name = "SELECT id,name FROM ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." WHERE id='".$id_row['pid']."' GROUP BY name";
				$name_query = mysql_query($zapytanie_name) or die(mysql_error());
					while ($name_row = mysql_fetch_assoc($name_query)) {
						if( substr($name_row['name'], -1) == chr(174) ){
							echo "- <strong style=\"color:red\">".htmlspecialchars($name_row['name'])."</strong><br>\n";
						}
						else{
							echo "- ".htmlspecialchars($name_row['name'])."<br>\n";	
						}
			}
		}
echo '</td></tr>';
}
echo '</tbody></table>';
} // IP & NICK CHECKING - END
?>
