<?php 
// include_once('pages/match_info_killsmatrix.php');
global $dbversion;

if (!isset($rank_year))
    $rank_year = 0;

include('includes/weaponstats.php');

if (!isset($mid2))
{
	// Find an associated round. If this is a "warm-up" map, there won't be one.
	$asscode = small_query("SELECT `matchcode`, `mapfile`, `mapsleft` FROM `uts_match` WHERE `id` = '".$mid."' AND `matchmode` = '1';");
	if ($asscode)
	{
		$mid2sql = "SELECT `id` FROM `uts_match` WHERE `id` <> '".$mid."' AND `matchmode` = '1' AND `mapsleft` = '".$asscode['mapsleft']."' AND `matchcode` = '".$asscode['matchcode']."' AND mapname = '".$asscode['mapfile']."';";

		$assmatch = small_query($mid2sql);
		if ($assmatch)
		{
			$mid2 = $assmatch['id'];
		}
	}	
}

include('includes/killstats.php');
if (killstats($mid,-1,true))
{
}
if (isset($mid2))
{
	echo "<table border='0' width='800'><tr><td width='50%'>";
	weaponstats($mid, 0, 'Weapons Summary - '.$ass_att.' Attacking');
	echo "</td><td width='50%'>";
	weaponstats($mid2, 0, 'Weapons Summary - '.$ass_att2.' Attacking');
	echo "</td></tr></table>";
}
else
{
	weaponstats($mid, 0, 'Weapons Summary - '.$ass_att.' Attacking');
}

echo'
<br>
<table border="0" cellpadding="0" cellspacing="2" width="720">
  <tbody><tr>
    <td class="heading" colspan="7" align="center">Pickups Summary - '.$ass_att.' Attacking</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Player</td>
    <td class="smheading" align="center" width="80">Pads</td>
    <td class="smheading" align="center" width="80">Armour</td>
    <td class="smheading" align="center" width="80">Keg</td>
    <td class="smheading" align="center" width="80">Invisibility</td>
    <td class="smheading" align="center" width="80">Shield Belt</td>
    <td class="smheading" align="center" width="80">Damage Amp</td>
  </tr>';
if (isset($dbversion) && floatval($dbversion) > 5.6)
{
	$sql_pickups = "SELECT `p`.`pid`, `pi`.`name`, ANY_VALUE(`p`.`country`), SUM(`p`.`pu_pads`) AS `pu_pads`, SUM(`p`.`pu_armour`) AS `pu_armour`,
	SUM(`p`.`pu_keg`) AS `pu_keg`, SUM(`p`.`pu_invis`) AS `pu_invis`, SUM(`p`.`pu_belt`) AS `pu_belt`, SUM(`p`.`pu_amp`) AS `pu_amp`
	FROM `uts_player` as `p`, `uts_pinfo` AS `pi` WHERE `p`.`pid` = `pi`.`id` AND `pi`.`banned` <> 'Y' AND `matchid` = '".$mid."' GROUP BY `pid` ORDER BY `name` ASC;";
}
else
{
	$sql_pickups = "SELECT `p`.`pid`, `pi`.`name`, `p`.`country`, SUM(`p`.`pu_pads`) AS `pu_pads`, SUM(`p`.`pu_armour`) AS `pu_armour`,
	SUM(`p`.`pu_keg`) AS `pu_keg`, SUM(`p`.`pu_invis`) AS `pu_invis`, SUM(`p`.`pu_belt`) AS `pu_belt`, SUM(`p`.`pu_amp`) AS `pu_amp`
	FROM `uts_player` as `p`, `uts_pinfo` AS `pi` WHERE `p`.`pid` = `pi`.`id` AND `pi`.`banned` <> 'Y' AND `matchid` = '".$mid."' GROUP BY `pid` ORDER BY `name` ASC;";
}
$q_pickups = mysql_query($sql_pickups) or die("mi_o2:".mysql_error());
$i = 0;
while ($r_pickups = zero_out(mysql_fetch_array($q_pickups))) {
     $i++;
     $class = ($i % 2) ? 'grey' : 'grey2';

	  $r_pname = $r_pickups['name'];
	  $myurl = urlencode($r_pname);
	  echo'
	  <tr>
		<td nowrap class="darkhuman" align="left"><a class="darkhuman" href="./?p=matchp&amp;mid='.$mid.'&amp;pid='.$r_pickups['pid'].'">'.FormatPlayerName($r_pickups['country'], $r_pickups['pid'], $r_pname, $gid, $gamename,true,null,$rank_year).'</a></td>
		<td class="'.$class.'" align="center">'.$r_pickups['pu_pads'].'</td>
		<td class="'.$class.'" align="center">'.$r_pickups['pu_armour'].'</td>
		<td class="'.$class.'" align="center">'.$r_pickups['pu_keg'].'</td>
		<td class="'.$class.'" align="center">'.$r_pickups['pu_invis'].'</td>
		<td class="'.$class.'" align="center">'.$r_pickups['pu_belt'].'</td>
		<td class="'.$class.'" align="center">'.$r_pickups['pu_amp'].'</td>
	  </tr>';
}

if (isset($mid2)) {
	echo'</tbody></table>
	<br>
	<table border="0" cellpadding="0" cellspacing="2" width="720">
	  <tbody><tr>
		<td class="heading" colspan="7" align="center">Pickups Summary - '.$ass_att2.' Attacking</td>
	  </tr>
	  <tr>
		<td class="smheading" align="center">Player</td>
		<td class="smheading" align="center" width="80">Pads</td>
		<td class="smheading" align="center" width="80">Armour</td>
		<td class="smheading" align="center" width="80">Keg</td>
		<td class="smheading" align="center" width="80">Invisibility</td>
		<td class="smheading" align="center" width="80">Shield Belt</td>
		<td class="smheading" align="center" width="80">Damage Amp</td>
	  </tr>';
	if (isset($dbversion) && floatval($dbversion) > 5.6)
	{
		$sql_pickups = "SELECT `p`.`pid`, `pi`.`name`, ANY_VALUE(`p`.`country`), SUM(`p`.`pu_pads`) AS `pu_pads`, SUM(`p`.`pu_armour`) AS `pu_armour`,
		SUM(`p`.`pu_keg`) AS `pu_keg`, SUM(`p`.`pu_invis`) AS `pu_invis`, SUM(`p`.`pu_belt`) AS `pu_belt`, SUM(`p`.`pu_amp`) AS `pu_amp`
		FROM `uts_player` as `p`, `uts_pinfo` AS `pi` WHERE `p`.`pid` = `pi`.`id` AND `pi`.`banned` <> 'Y' AND `matchid` = '".$mid2."' GROUP BY `pid` ORDER BY `name` ASC";
	}
	else
	{
		$sql_pickups = "SELECT `p`.`pid`, `pi`.`name`, `p`.`country`, SUM(`p`.`pu_pads`) AS `pu_pads`, SUM(`p`.`pu_armour`) AS `pu_armour`,
		SUM(`p`.`pu_keg`) AS `pu_keg`, SUM(`p`.`pu_invis`) AS `pu_invis`, SUM(`p`.`pu_belt`) AS `pu_belt`, SUM(`p`.`pu_amp`) AS `pu_amp`
		FROM `uts_player` as `p`, `uts_pinfo` AS `pi` WHERE `p`.`pid` = `pi`.`id` AND `pi`.`banned` <> 'Y' AND `matchid` = '".$mid2."' GROUP BY `pid` ORDER BY `name` ASC";
	}
	$q_pickups = mysql_query($sql_pickups) or die(mysql_error());
	$i = 0;
	while ($r_pickups = zero_out(mysql_fetch_array($q_pickups))) {
     $i++;
     $class = ($i % 2) ? 'grey' : 'grey2';

	  $r_pname = $r_pickups['name'];
	  $myurl = urlencode($r_pname);

		  echo'
		  <tr>
			<td nowrap class="darkhuman" align="left"><a class="darkhuman" href="./?p=matchp&amp;mid='.$mid.'&amp;pid='.$r_pickups['pid'].'">'.FormatPlayerName($r_pickups['country'], $r_pickups['pid'], $r_pname, $gid, $gamename,true,null,$rank_year).'</a></td>
			<td class="'.$class.'" align="center">'.$r_pickups['pu_pads'].'</td>
			<td class="'.$class.'" align="center">'.$r_pickups['pu_armour'].'</td>
			<td class="'.$class.'" align="center">'.$r_pickups['pu_keg'].'</td>
			<td class="'.$class.'" align="center">'.$r_pickups['pu_invis'].'</td>
			<td class="'.$class.'" align="center">'.$r_pickups['pu_belt'].'</td>
			<td class="'.$class.'" align="center">'.$r_pickups['pu_amp'].'</td>
		  </tr>';
	}
}

$sql_firstblood = small_query("SELECT `pi`.`name`, `pi`.`country`, `m`.`firstblood` FROM `uts_match` AS `m`, `uts_pinfo` AS `pi` WHERE `m`.`firstblood` = `pi`.`id` AND `m`.`id` = '".$mid."';");
if (!$sql_firstblood)
	$sql_firstblood = array('country' => '', 'name' => '(unknown)', 'firstblood' => NULL);
$sql_multis = small_query("SELECT SUM(spree_double) AS `spree_double`, SUM(spree_multi) AS `spree_multi`, SUM(spree_ultra) AS `spree_ultra`, SUM(spree_monster) AS `spree_monster` FROM `uts_player` WHERE `matchid` = '".$mid."';");

if (isset($mid2))
{
	$sql_firstblood2 = small_query("SELECT `pi`.`name`, `pi`.`country`, `m`.`firstblood` FROM `uts_match` AS `m`, `uts_pinfo` AS `pi` WHERE `m`.`firstblood` = `pi`.`id` AND `m`.`id` = '".$mid2."';");
	if (!$sql_firstblood2)
		$sql_firstblood2 = array('country' => '', 'name' => '(unknown)', 'firstblood' => NULL);
	$sql_multis2 = small_query("SELECT SUM(spree_double) AS `spree_double`, SUM(spree_multi) AS `spree_multi`, SUM(spree_ultra) AS `spree_ultra`, SUM(spree_monster) AS `spree_monster` FROM `uts_player` WHERE `matchid` = '".$mid2."';");
}
echo '</tbody></table>
<br>
<table border="0" cellpadding="1" cellspacing="2" width="'.(isset($mid2) ? "720" : "360").'">
  <tbody><tr>
    <td class="heading" colspan="2" align="center">Special Events - '.$ass_att.' Attacking</td>';
if (isset($mid2))
	echo '    <td class="heading" colspan="2" align="center">Special Events - '.$ass_att2.' Attacking</td>';
echo '
  </tr>
  <tr>
    <td class="dark" align="center" width="150">First Blood</td>
    <td class="grey" align="center" width="150">'.FormatPlayerName($sql_firstblood['country'], $sql_firstblood['firstblood'], $sql_firstblood['name'], $gid, $gamename,true,null,$rank_year).'</td>';
if (isset($mid2))
	echo '    <td class="dark" align="center" width="150">First Blood</td>
    <td class="grey" align="center" width="150">'.FormatPlayerName($sql_firstblood2['country'], $sql_firstblood2['firstblood'], $sql_firstblood2['name'], $gid, $gamename,true,null,$rank_year).'</td>';
echo '
  </tr>
  <tr>
    <td class="dark" align="center">Double Kills</td>
    <td class="grey2" align="center">'.$sql_multis['spree_double'].'</td>';
if (isset($mid2))
	echo '    <td class="dark" align="center">Double Kills</td>
    <td class="grey2" align="center">'.$sql_multis2['spree_double'].'</td>';
echo '
  </tr>
  <tr>
    <td class="dark" align="center">Multi Kills</td>
    <td class="grey" align="center">'.$sql_multis['spree_multi'].'</td>';
if (isset($mid2))
	echo '    <td class="dark" align="center">Multi Kills</td>
    <td class="grey" align="center">'.$sql_multis2['spree_multi'].'</td>';
echo '
  </tr>
  <tr>
    <td class="dark" align="center">Ultra Kills</td>
    <td class="grey2" align="center">'.$sql_multis['spree_ultra'].'</td>';
if (isset($mid2))
	echo '    <td class="dark" align="center">Ultra Kills</td>
    <td class="grey2" align="center">'.$sql_multis2['spree_ultra'].'</td>';
echo '
  </tr>
  <tr>
    <td class="dark" align="center">Monster Kills</td>
    <td class="grey" align="center">'.$sql_multis['spree_monster'].'</td>';
if (isset($mid2))
	echo '    <td class="dark" align="center">Monster Kills</td>
    <td class="grey" align="center">'.$sql_multis2['spree_monster'].'</td>';
echo '
  </tr>
</tbody></table>';
?>
