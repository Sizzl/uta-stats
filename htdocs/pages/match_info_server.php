<?php 
// --// Feb 2022 Sizzl: Dropped into a function to allow multi-round support (e.g. both rounds in Assault)
function server_stats($mid, $deferoutput=false, $mid2=-1)
{
	// Get Summary Info
	$extra = "";
	$matchdate2 = "";
	if ($mid2 > -1)
	{
		$extra = " OR `id` = '".$mid2."'";
		$matchinfo = small_query("SELECT m.time, m.servername, g.name AS gamename, m.gamename AS real_gamename, m.gid, m.mapname, m.mapfile, m.serverinfo, m.gameinfo, m.mutators, m.serverip FROM uts_match AS m, uts_games AS g WHERE m.gid = g.id AND m.id = '".$mid2."';");
		$matchdate2 = mdate($matchinfo[time]);
	}
	$teamscore = small_query("SELECT SUM(t0score + t1score + t2score + t3score) AS `result` FROM `uts_match` WHERE `id` = '".$mid."'".$extra.";");
	$playerscore = small_query("SELECT SUM(gamescore) AS `result` FROM `uts_player` WHERE `matchid` = '".$mid."'".$extra.";");
	$fragcount = small_query("SELECT SUM(frags) AS `result` FROM `uts_match` WHERE `id` = '".$mid."'".$extra.";");
	$killcount = small_query("SELECT SUM(kills) AS `result` FROM `uts_match` WHERE `id` = '".$mid."'".$extra.";");
	$deathcount = small_query("SELECT SUM(deaths) AS `result` FROM `uts_match` WHERE `id` = '".$mid."'".$extra.";");
	$suicidecount = small_query("SELECT SUM(suicides) AS `result` FROM `uts_match` WHERE `id` = '".$mid."'".$extra.";");

	$matchinfo = small_query("SELECT m.time, m.servername, g.name AS gamename, m.gamename AS real_gamename, m.gid, m.mapname, m.mapfile, m.serverinfo, m.gameinfo, m.mutators, m.serverip FROM uts_match AS m, uts_games AS g WHERE m.gid = g.id AND m.id = '".$mid."';");
	$matchdate = mdate($matchinfo[time]);
	$rank_year = substr($matchinfo[time],0,4);
	$gamename = $matchinfo[gamename];
	$real_gamename = $matchinfo[real_gamename];
	$gid = $matchinfo[gid];

	$mapname = un_ut($matchinfo[mapfile]);
	$mappic = strtolower("images/maps/".$mapname.".jpg");
	if (!$deferoutput)
	{
		echo'
<table border="0" cellpadding="1" cellspacing="2" width="720">
  <tbody><tr>
    <td class="heading" align="center">Unreal Tournament '.($mid2 > -1 ? "Match" : "Map").'</td>
  </tr>
</tbody></table>
<br>
<table class="box" border="0" cellpadding="1" cellspacing="2">
  <tbody><tr>
    <td class="heading" colspan="6" align="center">Totals for This '.($mid2 > -1 ? "Match" : "Map").'</td>
  </tr>
    <tr>';
		if (substr($gamename,0,7) != "Assault")
			echo'
    <td class="smheading" align="center" width="65">Team Score</td>
    <td class="smheading" align="center" width="70">Player Score</td>';
		echo'
    <td class="smheading" align="center" width="65">Frags</td>
    <td class="smheading" align="center" width="70">Deaths</td>
    <td class="smheading" align="center" width="80">Suicides</td>
  </tr><tr>';
		if (substr($gamename,0,7) != "Assault")
			echo'
    <td class="smheading" align="center" width="65">'.$teamscore[result].'</td>
    <td class="smheading" align="center" width="70">'.$playerscore[result].'</td>';
		echo '
    <td class="smheading" align="center" width="65">'.$fragcount[result].'</td>
    <td class="smheading" align="center" width="70">'.$deathcount[result].'</td>
    <td class="smheading" align="center" width="80">'.$suicidecount[result].'</td>
  </tr>
</tbody></table>
<br>
<table border="0" cellpadding="1" cellspacing="2" width="720">
  <tbody><tr>
    <td class="heading" colspan="4" align="center">Unreal Tournament '.($mid2 > -1 ? "Match" : "").' Stats</td>
  </tr>';

		if (!file_exists($mappic))
		{
		   $mappic = ("images/maps/blank.jpg");
		}

		$myurl = urlencode($mapname);

		echo'
  <tr>
    <td class="dark" align="center" width="110">Match Date'.(strlen($matchdate2) > 0 ? "s" : "").'</td>
    <td class="grey" align="center">'.$matchdate.(strlen($matchdate2) > 0 ? "\n<br />\n".$matchdate2 : "").'</td>
    <td class="dark" align="center" width="110">Server</td>
    <td class="grey" align="center" width="146"><a class="grey" href="./?p=sinfo&amp;serverip='.$matchinfo[serverip].'">'.$matchinfo[servername].'</a></td>
  </tr>
  <tr>
    <td class="dark" align="center">Match Type</td>
    <td class="grey" align="center">'.$gamename.'</td>
    <td class="dark" align="center">Map Name</td>
    <td class="greyhuman" align="center"><a class="grey" href="./?p=minfo&amp;map='.$myurl.'">'.$matchinfo[mapname].'</a></td>
  </tr>
  <tr>
    <td class="dark" align="center">Server Info</td>
    <td class="grey" align="center">'.$matchinfo[serverinfo].'</td>
    <td class="dark" align="center" rowspan="4" colspan="2"><img border="0" alt="'.$mapname.'" title="'.$mapname.'" src="'.$mappic.'"></td>
  </tr>
  <tr>
    <td class="dark" align="center">Game Info</td>
    <td class="grey" align="center">'.$matchinfo[gameinfo].'</td>
  </tr>
  <tr>
    <td class="dark" align="center">Mutators</td>
    <td class="grey" align="center">'.$matchinfo[mutators].'</td>
  </tr>
</tbody></table>
<br>';
	}
	return $matchinfo;
}

$matchinfo = server_stats($mid,true);
$matchdate = mdate($matchinfo[time]);
$rank_year = substr($matchinfo[time],0,4);
$gamename = $matchinfo[gamename];
$real_gamename = $matchinfo[real_gamename];
$gid = $matchinfo[gid];
?>
