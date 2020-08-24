<?php 
function row($name = NULL, $amount = 0, $multiplier = 0, $extra_multiplier = true, $amount_rated = -1) {
	static $i = 0;
	if (empty($name)) {
		echo '<tr><td colspan="4" height="3"></td></tr>';
		$i = 0;
		return(0);
	}
	$i++;
	$class = ($i%2) ? 'grey' : 'grey2';
	if ($extra_multiplier) $multiplier *= 600;
	if ($amount_rated > -1)
		$points = $amount_rated * $multiplier;
	else
		$points = $amount * $multiplier;
	
	$d_points = get_dp($points);
	if ($points % 1 == 0) $d_points = ceil($points); 
	echo '<tr>';
	echo '<td class="dark">'. htmlentities($name) .'</td>';	
	echo '<td class="'.$class.'" align="center">'. intval($amount) .'</td>';
	if ($amount_rated > -1)	echo '<td class="'.$class.'" align="center">'.number_format($amount_rated,1,'.','').'</td>';
	else echo '<td class="'.$class.'" align="center"></td>';
	echo '<td class="'.$class.'" align="center">'. $multiplier .'</td>';
	echo '<td class="'.$class.'" align="right">'. $d_points .'</td>';
	echo '</tr>';
	return($points);
}

function rowtotal($title, $sum) {
	
	$class = ($i%2) ? 'grey' : 'grey2';
	echo '<tr>	<td class="dark">'. $title .'</td>
				<td class="'.$class.'" align="center"></td>
				<td class="'.$class.'" align="center"></td>
				<td class="'.$class.'" align="center"></td>
				<td class="'.$class.'" align="right">'. intval($sum) .'</td>
		</tr>';
}


global $t_match, $t_pinfo, $t_player, $t_games; // fetch table globals.

$pid = isset($pid) ? addslashes($pid) : addslashes($_GET['pid']);
$gid = isset($gid) ? addslashes($gid) : addslashes($_GET['gid']);

$r_info = small_query("SELECT name, country, banned FROM ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." WHERE id = '$pid'");
if (!$r_info) {
	echo "Player not found";
	include("includes/footer.php");
	exit;
}

if ($r_info['banned'] == 'Y') {
	if (isset($is_admin) and $is_admin) {
		echo "Warning: Banned player - Admin override<br>";
	} else {
		echo "Sorry, this player has been banned!";
		include("includes/footer.php");
		exit;
	}
}

$playername = $r_info['name'];

$r_game = small_query("SELECT name, gamename FROM ".(isset($t_games) ? $t_games : "uts_games")." WHERE id = '$gid'");
if (!$r_game) {
	echo "Game ($gid) not found.";
	include("includes/footer.php");
	exit;
}
$real_gamename = $r_game['gamename'];


$r_cnt = small_query("SELECT
		SUM(p.frags) AS frags, SUM(p.deaths) AS deaths, SUM(p.suicides) AS suicides, SUM(p.teamkills) AS teamkills,
		SUM(p.flag_taken) AS flag_taken, SUM(p.flag_pickedup) AS flag_pickedup, SUM(p.flag_return) AS flag_return, SUM(p.flag_capture) AS flag_capture, SUM(p.flag_cover) AS flag_cover,
		SUM(p.flag_seal) AS flag_seal, SUM(p.flag_assist) AS flag_assist, SUM(p.flag_kill) AS flag_kill,
		SUM(p.dom_cp) AS dom_cp, SUM(p.ass_obj) AS ass_obj,
		SUM(p.ass_assist) AS ass_assist, SUM(p.ass_h_launch) AS ass_h_launch, SUM(p.ass_r_launch) AS ass_r_launch, 
		SUM(p.ass_h_launched) AS ass_h_launched, SUM(p.ass_r_launched) AS ass_r_launched,
		SUM(p.spree_double) AS spree_double, SUM(p.spree_multi) AS spree_multi, SUM(p.spree_ultra) AS spree_ultra, SUM(p.spree_monster) AS spree_monster,
		SUM(p.spree_kill) AS spree_kill, SUM(p.spree_rampage) AS spree_rampage, SUM(p.spree_dom) AS spree_dom, SUM(p.spree_uns) AS spree_uns, SUM(p.spree_god) AS spree_god,
		SUM(m.ass_att=p.team) as ass_att, SUM(m.ass_att<>p.team) as ass_def,
		SUM(p.gametime) AS gametime 
		FROM ".(isset($t_player) ? $t_player : "uts_player")." p inner join ".(isset($t_match) ? $t_match : "uts_match")." m on p.matchid = m.id
		WHERE p.pid = $pid and p.gid = $gid");

echo'
<table border="0" cellpadding="1" cellspacing="2" width="720">
  <tbody><tr>
    <td class="heading" align="center"><a href="?pinfo&amp;pid='.$pid.'">'.FlagImage($r_info['country'], false).' '.htmlentities($playername).'</a>\'s '. htmlentities($r_game['name']) .' ranking explained </td>
  </tr>
</tbody></table>';
echo '<br /><br />';





echo '
<table class="box" border="0" cellpadding="1" cellspacing="1">
<tbody>
	<tr>
		<td class="smheading" width="250"></td>
		<td class="smheading" width="80" align="center">Amount</td>
		<td class="smheading" width="80" align="center">Rated Amount</td>
		<td class="smheading" width="80" align="center">Multiplier</td>
		<td class="smheading" width="100" align="right">Points</td>
	</tr>';

$t_points = 0;
row();	

if (strpos($real_gamename, 'Assault') !== false) {
	
	$ass_att = $r_cnt['ass_att']; 
	$ass_def = $r_cnt['ass_def'];
	$ratio_att = intval($ass_att * 100 / ($ass_att + $ass_def));	
	$ratio_def = 100 - $ratio_att;
	
	$t_points_prev = $t_points;
	
	// Assault Objectives
	$objsql = "SELECT COUNT(stats.id) as objs, SUM(o.rating) as ratedobjs, def_teamsize, att_teamsize
			from ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats")." stats 
			inner join ".(isset($t_match) ? $t_match : "uts_match")." m on stats.matchid = m.id 
			inner join ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." p on p.id = stats.pid 
			INNER JOIN ".(isset($t_smartass_objs) ? $t_smartass_objs : "uts_smartass_objs")." o ON stats.objid = o.id
			WHERE p.id = $pid 
			and m.gid = $gid
			and stats.def_teamsize >= 2 
			and stats.att_teamsize >= 2
			group by def_teamsize, att_teamsize Order by def_teamsize DESC, att_teamsize DESC";
			$q_obj = mysql_query($objsql);
			
			while ($r_obj = mysql_fetch_array($q_obj)) 
			{
				$t_points += row('Assault Objectives '.$r_obj[att_teamsize].'v'.$r_obj[def_teamsize], $r_obj[objs], ($r_obj[def_teamsize]*2 - $r_obj[att_teamsize])*10.0/6.0, true, $r_obj[ratedobjs]);
			}
			$t_points += row('Assault Objective Assists', $r_cnt['ass_assist'], 2);		
	
	row();
		
	// Assault Events
	$t_points += row('Assault Hammerlaunch', $r_cnt['ass_h_launch'], 3);
	$t_points += row('Assault Hammerlaunched', $r_cnt['ass_h_launched'], 1);
	$t_points += row('Assault Rocketlaunch', $r_cnt['ass_r_launch'], 3);
	$t_points += row('Assault Rocketlaunched', $r_cnt['ass_r_launched'], 1);
	row();
	
	// Penalty Assault Attacking events
	$assault_sum = $t_points-$t_points_prev;
	rowtotal('Assault Events Total',intval($assault_sum));
	if ($ratio_att > 50)
	{
		$penaltyfactor = ($ass_att + $ass_def) / (2 * $ass_att) - 1; 
		if ($penaltyfactor <= 0)
			$t_points += row('Assault Penalty for '.intval($ratio_att).'% attacking', $assault_sum, get_dp($penaltyfactor), false);
	}
	else 
	{
		row('Assault Penalty for '.intval($ratio_att).'% attacking', 0, 0, false);
	}	
	
	row();	
	row();	
	
	// Frag Events
	$t_points_prev = $t_points;	
	$t_points += row('Frags', $r_cnt['frags'], 0.5);
	$t_points += row('Deaths', $r_cnt['deaths'], -1.0/6.0);
	//$t_points += row('Suicides', $r_cnt['suicides'], -0.25 );
	$t_points += row('Teamkills', $r_cnt['teamkills'], -2);		
	
	row();
	$t_points += row('Double Kills', $r_cnt['spree_double'], 0.5);
	$t_points += row('Multi Kills', $r_cnt['spree_multi'], 1);
	$t_points += row('Ultra Kills', $r_cnt['spree_ultra'], 2);
	$t_points += row('Monster Kills', $r_cnt['spree_monster'], 4);
	row();
	$t_points += row('Killing Sprees', $r_cnt['spree_kill'], 0.5);
	$t_points += row('Rampages', $r_cnt['spree_rampage'], 1.0);
	$t_points += row('Dominatings', $r_cnt['spree_dom'], 1.5);
	$t_points += row('Unstoppables', $r_cnt['spree_uns'], 2);
	$t_points += row('Godlikes', $r_cnt['spree_god'], 3);	
	row();	
	
	$frags_sum = $t_points - $t_points_prev;
	rowtotal('Frag Events Total',intval($frags_sum));
	if ($ratio_def > 50)
	{
		$penaltyfactor = ($ass_att + $ass_def) / (2 * $ass_def) - 1; 
		if ($penaltyfactor <= 0)
			$t_points += row('Frags Penalty for '.intval($ratio_def).'% defending', $frags_sum, get_dp($penaltyfactor), false);				
	}
	else 
	{
		row('Frags Penalty for '.intval($ratio_def).'% defending', 0, 0, false);
	}		
}
else
{
	$t_points += row('Frags', $r_cnt['frags'], 0.5);
	$t_points += row('Deaths', $r_cnt['deaths'], -0.25);
	$t_points += row('Suicides', $r_cnt['suicides'], -0.25 );
	$t_points += row('Teamkills', $r_cnt['teamkills'], -2);
	row();
	$t_points += row('Flag Takes', $r_cnt['flag_taken'], 1);
	$t_points += row('Flag Pickups', $r_cnt['flag_pickedup'], 1);
	$t_points += row('Flag Returns', $r_cnt['flag_return'], 1);
	$t_points += row('Flag Captures', $r_cnt['flag_capture'], 10);
	$t_points += row('Flag Covers', $r_cnt['flag_cover'], 3);
	$t_points += row('Flag Seals', $r_cnt['flag_seal'], 2);
	$t_points += row('Flag Assists', $r_cnt['flag_assist'], 5);
	$t_points += row('Flag Kills', $r_cnt['flag_kill'], 2);
	row();
	$t_points += row('Controlpoint Captures', $r_cnt['dom_cp'], 10);	
	if (strpos($real_gamename, 'JailBreak') !== false) {
		$t_points += row('Team Releases', $r_cnt['ass_obj'], 1.5);
	} else {
		$t_points += row('Team Releases', 0, 1.5);
	} 
	
	row();
	$t_points += row('Double Kills', $r_cnt['spree_double'], 1);
	$t_points += row('Multi Kills', $r_cnt['spree_multi'], 1);
	$t_points += row('Ultra Kills', $r_cnt['spree_ultra'], 1.5);
	$t_points += row('Monster Kills', $r_cnt['spree_monster'], 2);
	row();
	$t_points += row('Killing Sprees', $r_cnt['spree_kill'], 1);
	$t_points += row('Rampages', $r_cnt['spree_rampage'], 1);
	$t_points += row('Dominatings', $r_cnt['spree_dom'], 1.5);
	$t_points += row('Unstoppables', $r_cnt['spree_uns'], 2);
	$t_points += row('Godlikes', $r_cnt['spree_god'], 3);
}	

row();	
row();	
echo '<tr>	<td class="dark">Total</td>
				<td class="grey" align="center"></td>
				<td class="grey" align="center"></td>
				<td class="grey" align="center"></td>
				<td class="grey" align="right">'. ceil($t_points) .'</td>
		</tr>';

$gametime = ceil($r_cnt['gametime'] / 60);
$t_points = $t_points / $gametime;
echo '<tr>	<td class="dark">Divided by game minutes</td>
				<td class="grey2" align="center">'.$gametime.'</td>
				<td class="grey2" align="center"></td>
				<td class="grey2" align="center"></td>
				<td class="grey2" align="right">'. intval($t_points) .'</td>
		</tr>';
		
IF ($gametime < 10) {
	$t_points += row('Penalty for playing < 10 minutes', get_dp($t_points), 0, false);
}

IF ($gametime >= 10 && $gametime < 30) {
	$t_points += row('Penalty for playing < 30 minutes', get_dp($t_points), -0.90, false);
}

IF ($gametime >= 30 && $gametime < 50) {
	$t_points += row('Penalty for playing < 50 minutes', get_dp($t_points), -0.80, false);
}

IF ($gametime >= 50 && $gametime < 100) {
	$t_points += row('Penalty for playing < 100 minutes', get_dp($t_points), -0.5, false);
}

IF ($gametime >= 100 && $gametime < 200) {
	$t_points += row('Penalty for playing < 200 minutes', get_dp($t_points), -0.3, false);
}

IF ($gametime >= 200 && $gametime < 300) {
	$t_points += row('Penalty for playing < 300 minutes', get_dp($t_points), -0.15, false);
}

//IF ($gametime >= 1440) {
//	$t_points += row('Bonus for playing > 24 hours', get_dp($t_points), 0.00, false);
//}

row();	
echo '<tr>	<td class="darkgrey"><strong>Total</strong></td>
				<td class="darkgrey" align="center"></td>
				<td class="darkgrey" align="center"></td>
				<td class="darkgrey" align="center"></td>
				<td class="darkgrey" align="right"><strong>'. get_dp($t_points) .'</strong></td>
		</tr>';




echo '</tbody></table>';

?>
