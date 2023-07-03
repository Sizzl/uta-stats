<?php 
 // 2006-09-07 brajan 
 // if country filter selected display
 // a link to switch back to all countries
 if(!empty($_GET['cfilter'])){ 
 	?>
 <table class="box" border="0" cellpadding="1" cellspacing="1" style="margin-bottom:25px">
	 <tbody>
 		<tr>
	  	<td class="smheading"><a href="?p=rank">Switch to all countries rankings.</a></td>
	  </tr>
	 </tbody>
	</table>
<?php 
}
global $t_rank, $t_match, $t_pinfo, $t_player, $t_games;// fetch table globals.
global $htmlcp;

// Adding year filters --// Timo 13/02/21
$rank_year = 0;
if (isset($_GET['year']) && strlen($_GET['year'])==4 && is_numeric($_GET['year']))
	$rank_year = intval(my_addslashes($_GET['year']));

if ($rank_year == 0)
	$sql_rgame = "SELECT DISTINCT(p.gid), g.name FROM ".(isset($t_player) ? $t_player : "uts_player")." AS p, ".(isset($t_games) ? $t_games : "uts_games")." AS g WHERE p.gid = g.id ORDER BY g.name ASC"; // -- original unfiltered by year
else
{
	// Work out what game types were played in the year provided; this is quite a slow query, need to work out a better way to do this... Perhaps ignore uts_player?
	$rank_time_start = $rank_year."0101000000";
	$rank_time_end   = $rank_year."1231235959"; 
 	$sql_rgame = "SELECT DISTINCT(p.gid), ".(isset($t_games) ? $t_games : "uts_games").".name FROM ".(isset($t_match) ? $t_match : "uts_match")." as m INNER JOIN ".(isset($t_player) ? $t_player : "uts_player")." AS p ON p.matchid = m.id, ".(isset($t_games) ? $t_games : "uts_games")." WHERE m.time >= '".$rank_time_start."' AND m.time <= '".$rank_time_end."' AND ".(isset($t_games) ? $t_games : "uts_games").".id = p.gid ORDER BY p.gid"; // slow, need to fix this
// 	$sql_rgame = "SELECT DISTINCT(p.gid), LEFT(m.time,4) AS year, ".(isset($t_games) ? $t_games : "uts_games").".name FROM ".(isset($t_match) ? $t_match : "uts_match")." as m INNER JOIN ".(isset($t_player) ? $t_player : "uts_player")." AS p ON p.matchid = m.id, ".(isset($t_games) ? $t_games : "uts_games")." WHERE year = '".$rank_year."' AND ".(isset($t_games) ? $t_games : "uts_games").".id = p.gid ORDER BY p.gid"; // slow, need to fix this
}
$q_ytest = mysql_query("SHOW COLUMNS FROM `".(isset($t_rank) ? $t_rank : "uts_rank")."` LIKE 'year';");
if (mysql_num_rows($q_ytest))
	$where_year = " r.year = '".$rank_year."' AND";
else
	$where_year = "";
$q_rgame = mysql_query($sql_rgame) or die(mysql_error());
while ($r_rgame = mysql_fetch_array($q_rgame))
{

	echo'
	  <table class="box" border="0" cellpadding="1" cellspacing="1">
	  <tbody>
	  <tr>
		<td class="heading" colspan="4" align="center">Top 10 '.$r_rgame['name'].' Players'.($rank_year == 0 ? "" : " [".$rank_year."]").'</td>
	  </tr>
	  <tr>
		<td class="smheading" align="center" width="75">'.htmlentities("N�",ENT_SUBSTITUTE,$htmlcp).'</td>
		<td class="smheading" align="center" width="150">Player Name</td>
		<td class="smheading" align="center" width="75">Rank</td>
		<td class="smheadingx" align="center" width="75">Maps/Rounds</td>
		<!-- <td class="smheadingx" align="center" width="75">Matches</td> -->
	  </tr>
	  ';

	$ranking = 0;
	// Modifications to rank by country --// Idea by brajan  20/07/05 : Timo. <--
	if (isset($_GET['cfilter']))
	{
		if (strlen($_GET['cfilter'])==2)
		  	$sql_rplayer = "SELECT pi.id AS pid, pi.name, pi.country, r.rank, r.prevrank, r.matches FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." AS r, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." AS pi WHERE".$where_year." r.pid = pi.id AND r.gid =  '".$r_rgame['gid']."' AND pi.country = '".$_GET['cfilter']."' AND pi.banned <> 'Y' ORDER BY r.rank DESC LIMIT 0,10";
	}
	else	
		$sql_rplayer = "SELECT pi.id AS pid, pi.name, pi.country, r.rank, r.prevrank, r.matches FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." AS r, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." AS pi WHERE".$where_year." r.pid = pi.id AND r.gid =  '".$r_rgame['gid']."' AND pi.banned <> 'Y' ORDER BY r.rank DESC LIMIT 0,10";
	// end modifications -->

	$q_rplayer = mysql_query($sql_rplayer) or die(mysql_error());
	while ($r_rplayer = mysql_fetch_array($q_rplayer))
	{
		$ranking++;
		$myurl = urlencode($r_rplayer['name']);
	  echo'
		  <tr>
			<td class="grey" align="center">'.$ranking.'</td>
			<td nowrap class="dark" align="left">';
	// Modifications to rank by country --// Idea by brajan  20/07/05 : Timo.
	// hmm this wasn't working :/
	// now it is :) // brajan 2006-09-07
	echo '<a class="darkhuman" href="./?p='.$_GET['p'];
	echo '&cfilter='.$r_rplayer['country'];
	echo '">'.FlagImage($r_rplayer['country']).'</a> &nbsp; ';
	echo '<a class="darkhuman" href="./?p=pinfo&amp;pid='.$r_rplayer['pid'].($rank_year > 0 ? "&amp;year=".$rank_year : "").'">';
	echo htmlentities($r_rplayer['name'],ENT_SUBSTITUTE,$htmlcp) .' '. RankMovement($r_rplayer['rank'] - $r_rplayer['prevrank']).'</a></td>';
	// end modifications -->
	echo '
			<td class="dark" align="center">'.get_dp($r_rplayer['rank']).'</td>
			<td class="grey" align="center">'.$r_rplayer['matches'].'</td>
			<!-- <td class="grey" align="center">'.$r_rplayer['matches'].'</td> -->
		  </tr>';
		ob_flush();
	}
	echo'
	  <tr>
		<td class="smheading" align="center" colspan="4"><a href="./?p=ext_rank&amp;gid='.$r_rgame['gid'].(isset($_GET['cfilter']) ? '&cfilter='.addslashes($_GET['cfilter']) : '').($rank_year == 0 ? '' : '&year='.$rank_year).'">Click Here To See All The Rankings<a/></td>
	  </tr>
	  </tbody></table><br>';
}
// $q_ryears = mysql_query("SELECT LEFT(time,4) AS year FROM `".(isset($t_match) ? $t_match : "uts_match")."` GROUP BY year;") or die (mysql_error());
if (strlen($where_year) > 0) {
	$q_ryears = mysql_query("SELECT year FROM `".(isset($t_rank) ? $t_rank : "uts_rank")."` WHERE year > '0' GROUP BY year ORDER BY year ASC;") or die(mysql_error());
	echo "<div class=\"pages\"><b>Filter Rankings:<br /><br />[<a class=\"pages\" href=\"?p=rank&amp\">All-Time</a>";
	$i = 0;
	while ($r_ryear = mysql_Fetch_array($q_ryears))
	{
		$i++;
		if ($i==6)
		{
			echo "]<br />[";
			echo "<a class=\"pages\" href=\"?p=rank&amp;year=".$r_ryear['year']."\">".$r_ryear['year']."</a>";
			$i = 0;
		}
		else
			echo " / <a class=\"pages\" href=\"?p=rank&amp;year=".$r_ryear['year']."\">".$r_ryear['year']."</a>";
	}
	echo "]</b></div><br />";
} else {
	echo "<div class=\"pages\"><b>Filter Rankings:<br /><br />[<a class=\"pages\" href=\"?p=rank&amp\">All-Time</a>]";
}
?>
