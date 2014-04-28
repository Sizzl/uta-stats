<?
global $t_rank, $t_match, $t_pinfo, $t_player, $t_games; // fetch table globals.
$outerlimit = 25; // Added adjustable record limit --// Timo 20/07/05

$gid = my_addslashes($_GET['gid']);

$r_gamename = small_query("SELECT name FROM ".(isset($t_games) ? $t_games : "uts_games")." WHERE id = '$gid'");
$gamename = $r_gamename['name'];

if ($_GET['cfilter'] && strlen($_GET['cfilter'])==2)
{
	$r_pcount = small_query("SELECT COUNT(*) AS pcount  FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." LEFT JOIN ".(isset($t_player) ? $t_player : "uts_player")." ON ".(isset($t_rank) ? $t_rank : "uts_rank").".pid = ".(isset($t_player) ? $t_player : "uts_player").".id WHERE ".(isset($t_player) ? $t_player : "uts_player").".country = '".$_GET['cfilter']."' AND ".(isset($t_rank) ? $t_rank : "uts_rank").".gid = '$gid'");
	$pcount = $r_pcount['pcount'];
}
else
{
	$r_pcount = small_query("SELECT COUNT(*) as pcount FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." WHERE gid= '$gid'");
	$pcount = $r_pcount['pcount'];
}


$ecount = $pcount/$outerlimit;
$ecount2 = number_format($ecount, 0, '.', '');

IF($ecount > $ecount2) {
	$ecount2 = $ecount2+1;
}

$fpage = 0;
IF($ecount < 1) { $lpage = 0; }
else { $lpage = $ecount2-1; }

$cpage = $_GET["page"];
IF ($cpage == "") { $cpage = "0"; }
$qpage = $cpage*$outerlimit;

$tfpage = $cpage+1;
$tlpage = $lpage+1;

$ppage = $cpage-1;
$ppageurl = "<a class=\"pages\" href=\"./?p=ext_rank&amp;gid=$gid&amp;page=$ppage\">[Previous]</a>";
IF ($ppage < "0") { $ppageurl = "[Previous]"; }

$npage = $cpage+1;
$npageurl = "<a class=\"pages\" href=\"./?p=ext_rank&amp;gid=$gid&amp;page=$npage\">[Next]</a>";
IF ($npage >= "$ecount") { $npageurl = "[Next]"; }

$fpageurl = "<a class=\"pages\" href=\"./?p=ext_rank&amp;gid=$gid&amp;page=$fpage\">[First]</a>";
IF ($cpage == "0") { $fpageurl = "[First]"; }

$lpageurl = "<a class=\"pages\" href=\"./?p=ext_rank&amp;gid=$gid&amp;page=$lpage\">[Last]</a>";
IF ($cpage == "$lpage") { $lpageurl = "[Last]"; }

if ($_GET['cfilter'] && strlen($_GET['cfilter'])==2)
{
	$ppageurl = str_replace("&amp;page=","&amp;cfilter=".$_GET['cfilter']."&amp;page=",$ppageurl);
	$npageurl = str_replace("&amp;page=","&amp;cfilter=".$_GET['cfilter']."&amp;page=",$npageurl);
	$fpageurl = str_replace("&amp;page=","&amp;cfilter=".$_GET['cfilter']."&amp;page=",$fpageurl);
	$lpageurl = str_replace("&amp;page=","&amp;cfilter=".$_GET['cfilter']."&amp;page=",$lpageurl);
}

echo'
<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>
<br>';
// 2006-09-07 brajan 
// if country filter selected display
// a link to switch back to all countries
 if(!empty($_GET['cfilter'])){ 
 	?>
 <table class="box" border="0" cellpadding="1" cellspacing="1" style="margin-bottom:25px">
	 <tbody>
 		<tr>
	  	<td class="smheading"><a href="?p=ext_rank&gid=<?=$_GET['gid']?>">Show players from all countries.</a></td>
	  </tr>
	 </tbody>
	</table>
<?
};
echo '
<table class="box" border="0" cellpadding="1" cellspacing="1">
  <tbody>
  <tr>
	<td class="heading" colspan="4" align="center">'.$gamename.' Ranking Players</td>
  </tr>
  <tr>
	<td class="smheading" align="center" width="75">N°</td>
	<td class="smheading" align="center" width="150">Player Name</td>
	<td class="smheading" align="center" width="75">Rank</td>
	<td class="smheadingx" align="center" width="75">Matches</td>
  </tr>';

	$ranking = $qpage;
	if ($_GET['cfilter'])
	{
		if (strlen($_GET['cfilter'])==2)
			$sql_rplayer = "SELECT pi.name, pi.country, r.rank, r.prevrank, r.matches, r.pid FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." AS r, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." AS pi WHERE r.pid = pi.id AND r.gid = '$gid' AND pi.country = '".$_GET['cfilter']."' AND pi.banned <> 'Y' ORDER BY rank DESC LIMIT $qpage,$outerlimit";
	}
	else
		$sql_rplayer = "SELECT pi.name, pi.country, r.rank, r.prevrank, r.matches, r.pid FROM ".(isset($t_rank) ? $t_rank : "uts_rank")." AS r, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." AS pi WHERE r.pid = pi.id AND r.gid = '$gid' AND pi.banned <> 'Y' ORDER BY rank DESC LIMIT $qpage,$outerlimit";
	$q_rplayer = mysql_query($sql_rplayer) or die(mysql_error());
	while ($r_rplayer = mysql_fetch_array($q_rplayer)) {

		$ranking++;
	  echo'
	  <tr>
		<td class="grey" align="center">'.$ranking.'</td>
		<td nowrap class="dark" align="left">';

		// Modifications to rank by country --// Idea by brajan  20/07/05 : Timo.
		echo '<a href="./?p='.$_GET['p'].'&gid='.$_GET['gid'];
		if (!$_GET['cfilter'])
			echo '&cfilter='.$r_rplayer[country];
		echo '">'.FlagImage($r_rplayer[country]).'</a>';

		echo ' <a class="darkhuman" href="./?p=pinfo&amp;pid='.$r_rplayer['pid'].'">'.htmlspecialchars($r_rplayer[name]) .' '. RankMovement($r_rplayer['rank'] - $r_rplayer['prevrank']) .'</a></td>';
		// End Modifications to rank by country -->
		echo '
		<td class="dark" align="center">'.get_dp($r_rplayer[rank]).'</td>
		<td class="grey" align="center">'.$r_rplayer[matches].'</td>
	  </tr>';

}
echo'
</tbody></table>
<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>';
?>
