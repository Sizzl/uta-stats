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
<?
}
global $t_rank, $t_match, $t_pinfo, $t_player, $t_games; // fetch table globals.

$sql_rgame = "SELECT DISTINCT(p.gid), g.name FROM ".(isset($t_player) ? $t_player : "uts_player")." AS p, ".(isset($t_games) ? $t_games : "uts_games")." AS g WHERE p.gid = g.id ORDER BY g.name ASC";
$q_rgame = mysql_query($sql_rgame) or die(mysql_error());
while ($r_rgame = mysql_fetch_array($q_rgame)) {

	  echo'
	  <table class="box" border="0" cellpadding="1" cellspacing="1">
	  <tbody>
	  <tr>
		<td class="heading" colspan="4" align="center">Top 10 '.$r_rgame['name'].' Players</td>
	  </tr>
	  <tr>
		<td class="smheading" align="center" width="75">N°</td>
		<td class="smheading" align="center" width="150">Player Name</td>
		<td class="smheading" align="center" width="75">Rank</td>
		<td class="smheadingx" align="center" width="75">Matches</td>
	  </tr>
	  ';

		$ranking = 0;

		// Modifications to rank by country --// Idea by brajan  20/07/05 : Timo. <--
		if ($_GET['cfilter'])
		{
			if (strlen($_GET['cfilter'])==2)
			  	$sql_rplayer = "SELECT pi.id AS pid, pi.name, pi.country, r.rank, r.prevrank, r.matches FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." AS r, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." AS pi WHERE r.pid = pi.id AND r.gid =  '$r_rgame[gid]' AND pi.country = '".$_GET['cfilter']."' AND pi.banned <> 'Y' ORDER BY r.rank DESC LIMIT 0,10";
		}
		else	
		  	$sql_rplayer = "SELECT pi.id AS pid, pi.name, pi.country, r.rank, r.prevrank, r.matches FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." AS r, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." AS pi WHERE r.pid = pi.id AND r.gid =  '$r_rgame[gid]' AND pi.banned <> 'Y' ORDER BY r.rank DESC LIMIT 0,10";
		// end modifications -->
		$q_rplayer = mysql_query($sql_rplayer) or die(mysql_error());
		while ($r_rplayer = mysql_fetch_array($q_rplayer)) {

			$ranking++;
			$myurl = urlencode($r_rplayer[name]);

		  echo'
		  <tr>
			<td class="grey" align="center">'.$ranking.'</td>
			<td nowrap class="dark" align="left">';
		// Modifications to rank by country --// Idea by brajan  20/07/05 : Timo.
		// hmm this wasn't working :/
		// now it is :) // brajan 2006-09-07
		echo '<a class="darkhuman" href="./?p='.$_GET['p'];
		echo '&cfilter='.$r_rplayer[country];
		echo '">'.FlagImage($r_rplayer[country]).'</a> &nbsp; ';
		echo '<a class="darkhuman" href="./?p=pinfo&amp;pid='.$r_rplayer[pid].'">';
		echo htmlspecialchars($r_rplayer[name]) .' '. RankMovement($r_rplayer['rank'] - $r_rplayer['prevrank']).'</a></td>';
		// end modifications -->
		echo '
			<td class="dark" align="center">'.get_dp($r_rplayer[rank]).'</td>
			<td class="grey" align="center">'.$r_rplayer[matches].'</td>
		  </tr>';
ob_flush();
	}
	echo'
	  <tr>
		<td class="smheading" align="center" colspan="4"><a href="./?p=ext_rank&amp;gid='.$r_rgame[gid].'&cfilter='.addslashes($_GET['cfilter']).'">Click Here To See All The Rankings<a/></td>
	  </tr>
	  </tbody></table><br>';
}
?>
