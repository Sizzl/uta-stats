<?php 
function killstats($mid, $pid=-1, $rcount=false, $title = 'Kill Attribution')
{
	global $gamename, $gid, $rank_year, $t_width;

	if (!isset($rank_year))
		$rank_year = 0;

	if ($rcount)
	{
		// Very quick check to see if we have damage data
		$sql_damage = "SELECT COUNT(`matchid`) AS `mcount` FROM `uts_killsdetail` WHERE `matchid` = '".$mid."';";
		$q_damage = small_query($sql_damage);
		if ($q_damage && $q_damage['mcount'] > 0)
			return true;
		else
			return false;
	}
	else
	{
		$q_damage = mysql_query($sql_damage) or die("ks: ".mysql_error()."\r\n<pre>".$sql_damage."</pre>\r\n");
		while ($r_damage = zero_out(mysql_fetch_array($q_damage)))
		{

		}
	}
}
?>
