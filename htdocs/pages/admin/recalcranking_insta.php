<?php 
// Instant ranking recalculation - no longer relies on regular import routine, less strain on db.

if (empty($import_adminkey) or isset($_REQUEST['import_adminkey']) or $import_adminkey != $adminkey) die('bla');

function rcalc($amount = 0, $multiplier = 0, $extra_multiplier = true, $amount_rated = -1) {
	if ($extra_multiplier) $multiplier *= 600;
	if ($amount_rated > -1)
		$points = $amount_rated * $multiplier;
	else
		$points = $amount * $multiplier;
	return($points);
}

// Debugging mode?
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : false;

// Output HTML?
$html = isset($_REQUEST['html']) ? $_REQUEST['html'] : true;

// Adding listing toggle --// Timo 01/05/07
$list = false;
$list = ($_GET['list'] == 'hide' && !isset($_POST['list'])) ? false : true;
$options['showlist'] = $list;

$options['title'] = 'Recalculate Rankings';
$options['requireconfirmation'] = false;
$i = 0;
$options['vars'][$i]['name'] = 'reset';
$options['vars'][$i]['type'] = 'static';
$options['vars'][$i]['options'] = 'No|Yes';
$options['vars'][$i]['prompt'] = 'Clear existing rankings';
$options['vars'][$i]['caption'] = 'Clear rankings:';
$i++;

$options['vars'][$i]['name'] = 'year';
$options['vars'][$i]['type'] = 'static';
$options['vars'][$i]['options'] = 'All + Annual|All-time only|'.join("|",range(date("Y"),2005));
$options['vars'][$i]['prompt'] = 'Select Year to Recalculate';
$options['vars'][$i]['caption'] = 'Ranking Year:';
$i++;

if (isset($_GET['piddebug']) || isset($_POST['values'])) {
	$options['vars'][$i]['name'] = 'piddebug';
	$options['vars'][$i]['type'] = 'static';
	$options['vars'][$i]['options'] = 'True';
	$options['vars'][$i]['prompt'] = 'PID debugging';
	$options['vars'][$i]['caption'] = 'Debugging On:';
	$i++;
	$options['vars'][$i]['name'] = 'debugpid';
	$options['vars'][$i]['type'] = 'player';
	$options['vars'][$i]['options'] = '0';
	$options['vars'][$i]['prompt'] = 'Select player';
	$options['vars'][$i]['caption'] = 'Debug player:';
	$i++;
	if (isset($_POST['values']) && (isset($_GET['piddebug']) != true && isset($_POST['piddebug']) != true)) {
		if (stristr($_POST['values'],"piddebug=>true")===false) {
			$i = $i - 2;
			unset($options['vars'][$i+1]);
			unset($options['vars'][$i+2]);
		}
	}

}
$options['vars'][$i]['name'] = 'start';
$options['vars'][$i]['type'] = 'static';
$options['vars'][$i]['options'] = 'No|Yes';
$options['vars'][$i]['exitif'] = 'No';
$options['vars'][$i]['prompt'] = 'Are you sure';
$options['vars'][$i]['caption'] = 'Sure:';
$i++;

$results = adminselect($options);

if ($results['start'] != 'Yes') {
	include('pages/admin/main.php');
	exit;
}
@ignore_user_abort(true);
@set_time_limit(0);

if (isset($results['year']))
{
	if (substr($results['year'],0,3)=="All")
	{
		if ($results['year']=="All-time only")
			$calc_rank_year = 0;
		else
			$calc_rank_year = -1;
		$rank_year = 0;
		$rank_time_start = "0";
		$rank_time_end   = date("Y")."1231235959";
	}
	else
	{
		$rank_year = $calc_rank_year = $results['year'];
		$rank_time_start = $calc_rank_year."0101000000";
		$rank_time_end   = $calc_rank_year."1231235959";
	}
}

echo'<br><table border="0" cellpadding="1" cellspacing="2" width="600">
<tr>
	<td class="smheading" align="center" colspan="2">Instant-ish Rankings Re-calculation</td>
</tr>';


echo'<tr>
	<td class="smheading" align="left" width="200">Deleting rankings</td>';

if($results['reset'] == 'Yes'){ // truncate table ONLY if selected
	if ($calc_rank_year == -1)
		mysql_query("TRUNCATE uts_rank;") or die(mysql_error());	
	else
		mysql_query("DELETE FROM uts_rank WHERE year = '".$calc_rank_year."';") or die(mysql_error());	

	echo'<td class="grey" align="left" width="400">Done</td>';
}
else
{
	echo'<td class="grey" align="left" width="400">Skipped</td>';
}
echo'</tr>
<tr>
	<td class="smheading" align="left">Recalculating Rankings:</td>';
echo'<td class="grey" align="left">';
$playerbanned = false;
$i = 0;
$q_pids = mysql_query("SELECT DISTINCT p.gid, p.pid
			FROM uts_player p, uts_pinfo pi, uts_match m
			WHERE pi.id = p.pid
			AND m.id = p.matchid
			AND m.time >= '".$rank_time_start."' AND m.time <= '".$rank_time_end."'
			AND pi.banned <> 'Y';");
			
while ($r_pid = mysql_fetch_array($q_pids)) {
	$i++;
	if ($i%50 == 0) {
		echo $i.'. ';
	}
	ob_start();
	$pid = $r_pid['pid'];
	$gid = $r_pid['gid'];
	
	$r_game = small_query("SELECT name, gamename FROM ".(isset($t_games) ? $t_games : "uts_games")." WHERE id = '$gid'");
	$real_gamename = $r_game['gamename'];
	$t_points = 0;

	if (strpos($real_gamename, 'Assault') !== false) {
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
			SUM(p.gametime) AS gametime, COUNT(m.id) AS matches
			FROM ".(isset($t_player) ? $t_player : "uts_player")." p inner join ".(isset($t_match) ? $t_match : "uts_match")." m on p.matchid = m.id
			WHERE p.pid = '".$pid."' and p.gid = '".$gid."';");
	
			
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
				// Objectives
				$t_points += rcalc($r_obj[objs], ($r_obj[def_teamsize]*2 - $r_obj[att_teamsize])*10.0/6.0, true, $r_obj[ratedobjs]);
			}
			$t_points += rcalc($r_cnt['ass_assist'], 2); // Objective Assists

			// Assault Events
			$t_points += rcalc($r_cnt['ass_h_launch'], 3); // Hammer launchee
			$t_points += rcalc($r_cnt['ass_h_launched'], 0.5); // Hammer launcher
			$t_points += rcalc($r_cnt['ass_r_launch'], 3); // Rocket launchee
			$t_points += rcalc($r_cnt['ass_r_launched'], 0.5); // Rocket launcher

			$assault_sum = $t_points-$t_points_prev;
			if ($ratio_att > 50)
			{
				$penaltyfactor = ($ass_att + $ass_def) / (2 * $ass_att) - 1; 
				if ($penaltyfactor <= 0)
					$t_points += rcalc($assault_sum, get_dp($penaltyfactor), false); // % Attacking penalty
			}
			
			// Frag Events
			$t_points_prev = $t_points;	
			$t_points += rcalc($r_cnt['frags'], 0.5); // Frags
			$t_points += rcalc($r_cnt['deaths'], -1.0/6.0); // Deaths
			//$t_points += rcalc($r_cnt['suicides'], -0.25 ); // Suicides
			$t_points += rcalc($r_cnt['teamkills'], -2); // TK's
			$t_points += rcalc($r_cnt['spree_double'], 0.5); // DKs
			$t_points += rcalc($r_cnt['spree_multi'], 1); // MuKs
			$t_points += rcalc($r_cnt['spree_ultra'], 2); // UKs
			$t_points += rcalc($r_cnt['spree_monster'], 4); // MoKs
			$t_points += rcalc($r_cnt['spree_kill'], 0.5); // KS
			$t_points += rcalc($r_cnt['spree_rampage'], 1.0); // Ramp
			$t_points += rcalc($r_cnt['spree_dom'], 1.5); // Dom
			$t_points += rcalc($r_cnt['spree_uns'], 2);  // Uns
			$t_points += rcalc($r_cnt['spree_god'], 3); // God

			$frags_sum = $t_points - $t_points_prev;
			if ($ratio_def > 50)
			{
				$penaltyfactor = ($ass_att + $ass_def) / (2 * $ass_def) - 1; 
				if ($penaltyfactor <= 0)
					$t_points += rcalc($frags_sum, get_dp($penaltyfactor), false);	// Frag penalty for % defending		
			}
	}
	else
	{
		// TO-DO - other game type instant recalc
	}


	$gametime = ceil($r_cnt['gametime'] / 60);
	$t_points = $t_points / $gametime;

	// Add steep penalties for newcomers (or fake nicks)
	if ($gametime < 10) {
		$t_points += rcalc(get_dp($t_points), 0, false); // < 10 mins
	}
	elseif ($gametime >= 10 && $gametime < 30) {
		$t_points += rcalc(get_dp($t_points), -0.90, false); // < 30 mins
	}
	elseif ($gametime >= 30 && $gametime < 50) {
		$t_points += rcalc(get_dp($t_points), -0.80, false); // < 50 mins
	}
	elseif ($gametime >= 50 && $gametime < 100) {
		$t_points += rcalc(get_dp($t_points), -0.5, false); // < 100 mins
	}
	elseif ($gametime >= 100 && $gametime < 200) {
		$t_points += rcalc(get_dp($t_points), -0.3, false); // < 200 mins
	}
	elseif ($gametime >= 200 && $gametime < 300) {
		$t_points += rcalc(get_dp($t_points), -0.15, false); // < 300 mins
	}

	$rank_nrank = $t_points; 
	
	$rank_gametime = $gametime;
	$rank_matches = $r_cnt['matches'];

	// Select rank record
	$r_rankp = small_query("SELECT id, time, rank, matches FROM uts_rank WHERE pid = '$pid' AND gid = '$gid' AND year = '".$rank_year."'");
	$rank_id = $r_rankp[id];
	$rank_crank = $r_rankp[rank];


	if ($rank_id == NULL) {
		// Add new rank record if one does not exist
		mysql_query("INSERT INTO uts_rank SET time = '$r_gametime', pid = '$pid', gid = '$gid', rank = '0', matches = '0', year = '".$rank_year."';") or die(mysql_error());
		$rank_id = mysql_insert_id();
		$rank_crank = 0;
	} 

	// Work out effective rank given
	$eff_rank = $rank_nrank-$rank_crank;
	if ($rank_year <= 0)
	{
		$sql = "UPDATE uts_player SET rank = '".$eff_rank."' WHERE id = '$playerecordid';";
		if (isset($results['debugpid']) && $results['debugpid'] == $pid)
			$s_debug = $s_debug."-----\r\ntotals-3-add_eff_rank_pts:\r\n".$sql."\r\n";

		mysql_query($sql) or die(mysql_error());
	}
	// Update the rank
	$sql = "UPDATE uts_rank SET time = '".$rank_gametime."', rank = '".$rank_nrank."', prevrank = '".$rank_crank."', matches = '".$rank_matches."' WHERE id = '".$rank_id."' and year = '".$rank_year."';";
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."-----\r\ntotals-4-update_rank:\r\n".$sql."\r\n";

	mysql_query($sql) or die(mysql_error());
	ob_end_flush();
	flush();	
	ob_flush();
}

echo 'Done</td>
</tr>';
if (isset($s_debug)) {
	echo '
<tr>
	<td class="smheading">Debug output:</td><td class="grey"><pre>'.$s_debug.'</pre></td>
</tr>';
}

echo '
<tr>
	<td class="smheading" align="center" colspan="2">Rankings recalculated - <a href="./admin.php?key='.$_REQUEST[key].'">Go Back To Admin Page</a></td>
</tr></table>';


?>
