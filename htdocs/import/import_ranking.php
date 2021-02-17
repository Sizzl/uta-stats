<?php 
if (!isset($gamename))
	die("This shouldn't be run outside of existing code.");

if ($playerbanned)
	return;

if (!isset($rank_year) || $rank_year == 0)
{
	$rank_year = 0;
	$rank_time_start = 0;
	$rank_time_end = date("Y")."1231235959";
}
else
{
	$rank_time_start = $rank_year."0101000000";
	$rank_time_end   = $rank_year."1231235959";
}
if (strpos($gamename, 'Assault') !== false)
{
	// ***************************************************************************************
	// CRATOS: New Ranking for Assault
	// ***************************************************************************************
	$rank_ass = 0;
	$sql = "SELECT
		SUM(p.frags*0.5) AS frags, SUM(p.deaths*1.0/6.0) AS deaths, SUM(p.teamkills*2) AS teamkills,			
		SUM(p.spree_double*0.5) AS spree_double, SUM(p.spree_multi*1) AS spree_multi, SUM(p.spree_ultra*2) AS spree_ultra, SUM(p.spree_monster*4) AS spree_monster,
		SUM(p.spree_kill*0.5) AS spree_kill, SUM(p.spree_rampage*1.0) AS spree_rampage, SUM(p.spree_dom*1.5) AS spree_dom, SUM(p.spree_uns*2) AS spree_uns, SUM(p.spree_god*3) AS spree_god,
		SUM(p.ass_assist*2.0) AS ass_assist, SUM(p.ass_h_launch*3.0) AS ass_h_launch, SUM(p.ass_r_launch*3.0) AS ass_r_launch, 
		SUM(p.ass_h_launched*1.0) AS ass_h_launched, SUM(p.ass_r_launched*1.0) AS ass_r_launched,
		SUM(m.ass_att=p.team) as ass_att, SUM(m.ass_att<>p.team) as ass_def,
		SUM(p.gametime) AS gametime 
		FROM uts_player p inner join uts_match m on p.matchid = m.id
		WHERE p.pid = '$pid' AND p.gid = '$gid' AND p.matchid <= '$matchid' AND m.time >= '$rank_time_start' AND m.time <= '$rank_time_end'";

	$r_cnt = small_query($sql);

	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."-----\r\n".$matchid."-1-r_cnt:\r\n".$sql."\r\n";

	$ass_att = $r_cnt['ass_att']; 
	$ass_def = $r_cnt['ass_def'];

	if ($ass_att+$ass_def == 0)
	{
		echo "<pre>".$sql."</pre>";
		break;
	}
	else
	{
		$ratio_att = intval($ass_att * 100 / ($ass_att + $ass_def)); // add checks
		$ratio_def = 100 - $ratio_att;
	}									
	// Fragging Events
	$rank_fpos = $r_cnt[frags]+$r_cnt[spree_double]+$r_cnt[spree_multi]+$r_cnt[spree_ultra]+$r_cnt[spree_monster]+$r_cnt[spree_kill]+$r_cnt[spree_rampage]+$r_cnt[spree_dom]+$r_cnt[spree_uns]+$r_cnt[spree_god];
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-1b-r_calc:\r\n Frag(pos) Summary: Frags + DKs + MKs + UKs + MKs + Sprees + Ramp + Doms + Unstops = ".$rank_fpos."\r\n";

	$rank_fneg = $r_cnt[deaths]+$r_cnt[teamkills];
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-1c-r_calc:\r\n Frag(neg) Summary: Deaths + TKs = ".$rank_fneg."\r\n";

	$frags_sum = $rank_fpos - $rank_fneg;
	if ($ratio_def > 50)
	{
		$penaltyfactor = ($ass_att + $ass_def) / (2 * $ass_def) - 1; // add checks
		if ($penaltyfactor <= 0)
			$frags_sum += $frags_sum * get_dp($penaltyfactor);	
	}
	
	
	// Objective Events
	$assault_sum = $r_cnt[ass_assist]+$r_cnt[ass_h_launch]+$r_cnt[ass_r_launch]+$r_cnt[ass_h_launched]+$r_cnt[ass_r_launched];
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-1d-r_calc:\r\n Assault Summary: Assists + HLauncher + RLauncher + HLaunched + RLaunched = ".$r_cnt[ass_assist]." + ".$r_cnt[ass_h_launch]." + ".$r_cnt[ass_r_launch]." + ".$r_cnt[ass_h_launched]." + ".$r_cnt[ass_r_launched]." = ".$assault_sum."\r\n";

	// ObjectiveFaktor = def*2 - att				
	$objsql = "SELECT COUNT(stats.id) as objs, SUM(o.rating) as ratedobjs, def_teamsize, att_teamsize
			from uts_smartass_objstats stats 
			inner join uts_match m on stats.matchid = m.id 
			inner join uts_pinfo p on p.id = stats.pid 
			INNER JOIN uts_smartass_objs o ON stats.objid = o.id
			where m.id <= $matchid and p.id = $pid 
			and m.gid = $gid
			and stats.def_teamsize >= 2 
			and stats.att_teamsize >= 2
			m.time >= '$rank_time_start' AND m.time <= '$rank_time_end'
			group by def_teamsize, att_teamsize";			

	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."-----\r\n".$matchid."-2-objsql:\r\n".$objsql."\r\n";

	$q_obj = mysql_query($objsql);
	$rankobj = 0;
	if ($q_obj!=NULL)
	{				
		while ($r_obj = mysql_fetch_array($q_obj)) 
		{					
			$rankobj += ($r_obj[ratedobjs] * ($r_obj[def_teamsize]*2 - $r_obj[att_teamsize]));
		}
	}
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-2a-objresults:\r\n Assault summary: ".$assault_sum." + (Ranked Objectives: ".$rankobj."* 10.0 / 6.0) = ".$assault_sum."+".($rankobj*10.0/6.0)." = ".($assault_sum+($rankobj *  10.0 / 6.0))."\r\n\r\n";

	$assault_sum += $rankobj *  10.0 / 6.0;	
	if ($ratio_att > 50)
	{
		$penaltyfactor = ($ass_att + $ass_def) / (2 * $ass_att) - 1; 
		if (isset($results['debugpid']) && $results['debugpid'] == $pid) $s_debug = $s_debug."--\r\n".$matchid."-2b-objpens:\r\n Penalties: ".$penaltyfactor."\r\n\r\n";
			if ($penaltyfactor <= 0)
			$assault_sum += $assault_sum * get_dp($penaltyfactor); 
	}
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-2c-asstotal:\r\n Assault Summary: ".$assault_sum."\r\n\r\n";

}
else
{
	// Original Ranking:			
	$r_cnt = small_query("SELECT
		SUM(frags*0.5) AS frags, SUM(deaths*0.25) AS deaths, SUM(suicides*0.25) AS suicides, SUM(teamkills*2) AS teamkills,
		SUM(flag_taken*1) AS flag_taken, SUM(flag_pickedup*1) AS flag_pickedup, SUM(flag_return*1) AS flag_return, SUM(flag_capture*10) AS flag_capture, SUM(flag_cover*3) AS flag_cover,
			SUM(flag_seal*2) AS flag_seal, SUM(flag_assist*5) AS flag_assist, SUM(flag_kill*2) AS flag_kill,
			SUM(dom_cp*10) AS dom_cp,
			SUM(spree_double*1) AS spree_double, SUM(spree_multi*1) AS spree_multi, SUM(spree_ultra*1.5) AS spree_ultra, SUM(spree_monster*2) AS spree_monster,
			SUM(spree_kill*1) AS spree_kill, SUM(spree_rampage*1) AS spree_rampage, SUM(spree_dom*1.5) AS spree_dom, SUM(spree_uns*2) AS spree_uns, SUM(spree_god*3) AS spree_god,
			SUM(gametime) AS gametime		
			FROM uts_player p inner join uts_match m on p.matchid = m.id
			FROM uts_player WHERE p.pid = '$pid' AND p.gid = '$gid' AND p.matchid <= '$matchid' AND m.time >= '$rank_time_start' AND m.time <= '$rank_time_end'");
	
	// Work out per game ranking amounts
	$rank_ctf = $r_cnt[flag_taken]+$r_cnt[flag_pickedup]+$r_cnt[flag_return]+$r_cnt[flag_capture]+$r_cnt[flag_cover]+$r_cnt[flag_seal]+$r_cnt[flag_assist]+$r_cnt[flag_kill];
	$rank_dom = $r_cnt[com_cp];
	$rank_jb = $r_cnt[ass_obj] * 0.15;			
	$rank_fpos = $r_cnt[frags]+$r_cnt[spree_double]+$r_cnt[spree_multi]+$r_cnt[spree_ultra]+$r_cnt[spree_monster]+$r_cnt[spree_kill]+$r_cnt[spree_rampage]+$r_cnt[spree_dom]+$r_cnt[spree_uns]+$r_cnt[spree_god];
	$rank_fneg = $r_cnt[deaths]+$r_cnt[suicides]+$r_cnt[teamkills];
}
				
$r_gametime = ceil($r_cnt[gametime]/60);		
		
// Work out initial rank dependant on game, if no game known use DM ranking
if (strpos($gamename, 'Assault') !== false)
	$rank_nrank = $assault_sum + $frags_sum;	
elseif ($gamename == "Capture the Flag" || $gamename == "Capture the Flag (insta)")
	$rank_nrank = $rank_ctf+$rank_fpos-$rank_fneg;
elseif ($gamename == "Domination" || $gamename == "Domination (insta)")
	$rank_nrank = $rank_dom+$rank_fpos-$rank_fneg;
elseif ($gamename == "JailBreak" || $gamename == "JailBreak (insta)")
	$rank_nrank = $rank_jb+$rank_fpos-$rank_fneg;
else
	$rank_nrank = $rank_fpos-$rank_fneg;


// Average the rank over game minutes
if (isset($results['debugpid']) && $results['debugpid'] == $pid)
	$s_debug = $s_debug."--\r\n".$matchid."-2d-rank:\r\n New Rank = (Rank/GameTime)*600 = (".$rank_nrank." / ".$r_gametime.") * 600 = ".(($rank_nrank/$r_gametime)*600)."\r\n\r\n";

$rank_nrank = ($rank_nrank/$r_gametime) * 600; // add checks
		
if ($dbg) echo "Nrank: $rank_nrank ";
		
// Add rank gametime to previous amount
$rank_gametime = $r_gametime;
if (isset($results['debugpid']) && $results['debugpid'] == $pid)
	$s_debug = $s_debug."--\r\n".$matchid."-2e-gametime:\r\n Gametime = ".$rank_gametime."\r\n\r\n";

// Reduce ranking if player hasnt played that much
if ($rank_gametime < 10)
	return;
		
if ($rank_gametime >= 10 && $rank_gametime < 30) {
	$rank_nrank = $rank_nrank*.10;
	if (isset($results['debugpid']) && $results['debugpid'] == $pid) $s_debug = $s_debug."--\r\n".$matchid."-2f-penalty:\r\n Penalty = Rank - 90% = ".$rank_nrank."\r\n\r\n";
}
		
if ($rank_gametime >= 30 && $rank_gametime < 50) {
	$rank_nrank = $rank_nrank*.20;
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-2f-penalty:\r\n Penalty = Rank - 80% = ".$rank_nrank."\r\n\r\n";
}
		
if ($rank_gametime >= 50 && $rank_gametime < 100) {
	$rank_nrank = $rank_nrank*.50;
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-2f-penalty:\r\n Penalty = Rank - 50% = ".$rank_nrank."\r\n\r\n";
}
		
if ($rank_gametime >= 100 && $rank_gametime < 200) {
	$rank_nrank = $rank_nrank*.70;
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-2f-penalty:\r\n Penalty = Rank - 30% = ".$rank_nrank."\r\n\r\n";
}

if ($rank_gametime >= 200 && $rank_gametime < 300) {
	$rank_nrank = $rank_nrank*.85;
	if (isset($results['debugpid']) && $results['debugpid'] == $pid)
		$s_debug = $s_debug."--\r\n".$matchid."-2f-penalty:\r\n Penalty = Rank - 15% = ".$rank_nrank."\r\n\r\n";
}

// Get sums of different events
		
// Select rank record
$r_rankp = small_query("SELECT id, time, rank, matches FROM uts_rank WHERE pid = '$pid' AND gid = '$gid' AND year = '".$rank_year."'");
$rank_id = $r_rankp[id];
$rank_gametime = $r_rankp[time];
$rank_crank = $r_rankp[rank];
$rank_matches = $r_rankp[matches];

if ($rank_id == NULL) {
	// Add new rank record if one does not exist
	mysql_query("INSERT INTO uts_rank SET time = '$r_gametime', pid = '$pid', gid = '$gid', rank = '0', matches = '0', year = '".$rank_year."';") or die(mysql_error());
	$rank_id = mysql_insert_id();
	$rank_gametime = 0;
	$rank_crank = 0;
	$rank_matches = 0;
} // end IF($rank_id == NULL)
// Add number of matches played
$rank_matches = $rank_matches+1;

// Work out effective rank given
$eff_rank = $rank_nrank-$rank_crank;
if ($rank_year <= 0)
{
	// Add effective rank points given to uts_player record --// Timo 13/02/2021 - need to understand whether this needs filtering by year; will be a PITA if so.
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

?>
