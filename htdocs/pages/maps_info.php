<?php 

// include ("includes/uta_functions.php");

$map = $_GET['map'];
$realmap = my_addslashes($_GET['map']);
$realmap = "".$realmap.".unr";

$map_matches = small_query("SELECT COUNT(id) as matchcount, SUM(t0score+t1score+t2score+t3score) AS gamescore,
SUM(gametime) AS gametime, SUM(kills) AS kills, SUM(suicides) AS suicides FROM uts_match WHERE mapfile = '$realmap'");
$map_last = small_query("SELECT time FROM uts_match WHERE mapfile = '$realmap' ORDER BY time DESC LIMIT 0,1");

$map_tottime = GetMinutes($map_matches['gametime']);
$map_lastmatch = mdate($map_last['time']);

// Map pic code
$mappic = strtolower("images/maps/".$map.".jpg");

if (file_exists($mappic))
{
}
else
{
   $mappic = ("images/maps/blank.jpg");
}

echo'
<table border="0" cellpadding="1" cellspacing="2" width="775">
  <tbody><tr>
    <td class="heading" align="center" colspan="4">Statistics for '.$map.'</td>
  </tr>
  <tr>
    <td class="dark" align="center">Matches</td>
    <td class="grey" align="center">'.$map_matches[matchcount].'</td>
    <td class="grey" align="center" rowspan="8"><img border="0" alt="'.$map.'" title="'.$map.'" src="'.$mappic.'"></td>
  </tr>
  <tr>
    <td class="dark" align="center">Total Score</td>
    <td class="grey" align="center">'.$map_matches[gamescore].'</td>
  </tr>
  <tr>
    <td class="dark" align="center">Total Time</td>
    <td class="grey" align="center">'.$map_tottime.' minutes</td>
  </tr>
  <tr>
    <td class="dark" align="center">Total Kills</td>
    <td class="grey" align="center">'.$map_matches[kills].'</td>
  </tr>
  <tr>
    <td class="dark" align="center">Total Suicides</td>
    <td class="grey" align="center">'.$map_matches[suicides].'</td>
  </tr>
  <tr>
    <td class="dark" align="center">Last Match</td>
    <td class="grey" align="center">'.$map_lastmatch.'</td>
  </tr>
</tbody></table>
<br>';



// ==========================================================
// --// Cratos
if (substr($map,0,3) == "AS-")
{
	include("pages/uta_ass_maps_info.php");
}
// ==========================================================




// Do graph stuff
$bgwhere = "mapfile = '$realmap'";
include("pages/graph_mbreakdown.php");

$mcount = small_count("SELECT id FROM uts_match WHERE mapfile = '$realmap' GROUP BY id");

$ecount = $mcount/25;
$ecount2 = number_format($ecount, 0, '.', '');

if ($ecount > $ecount2)
{
	$ecount2 = $ecount2+1;
}

$fpage = 0;
if ($ecount < 1) { $lpage = 0; }
else { $lpage = $ecount2-1; }

$cpage = $_GET["page"];
$qpage = $cpage*25;

if ($cpage == "") { $cpage = "0"; }

$tfpage = $cpage+1;
$tlpage = $lpage+1;

$ppage = $cpage-1;
$page = my_addslashes($_GET['p']);
$ppageurl = "<a class=\"pages\" href=\"./?p=".$page."&amp;page=$ppage\">[Previous]</a>";
if ($ppage < "0") { $ppageurl = "[Previous]"; }

$npage = $cpage+1;
$npageurl = "<a class=\"pages\" href=\"./?p=".$page."&amp;page=$npage\">[Next]</a>";
if ($npage >= "$ecount") { $npageurl = "[Next]"; }

$fpageurl = "<a class=\"pages\" href=\"./?p=".$page."&amp;page=$fpage\">[First]</a>";
if ($cpage == "0") { $fpageurl = "[First]"; }

$lpageurl = "<a class=\"pages\" href=\"./?p=".$page."&amp;page=$lpage\">[Last]</a>";
if ($cpage == "$lpage") { $lpageurl = "[Last]"; }

echo'
<table class="box" border="0" cellpadding="1" cellspacing="1">
  <tbody><tr>
    <td class="heading" colspan="6" align="center">Recent Matches</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="250">Date</td>
    <td class="smheading" align="center" width="100">Match Type</td>
    <td class="smheading" align="center">Player Count</td>
    <td class="smheading" align="center" width="80">Time</td>
    <td class="smheading" align="center" width="150">Server</td>
  </tr>';

$sql_maps = "SELECT m.id, m.time, g.name AS gamename, m.gametime, m.servername
FROM uts_match AS m, uts_games AS g WHERE m.mapfile = '$realmap' AND m.gid = g.id ORDER BY time DESC LIMIT $qpage,25";
$q_maps = mysql_query($sql_maps) or die(mysql_error());
while ($r_maps = mysql_fetch_array($q_maps))
{

	  $r_mapfile = un_ut($r_maps['mapfile']);
	  $r_matchtime = mdate($r_maps['time']);
	  $r_gametime = GetMinutes($r_maps['gametime']);
	  $r_servername = get_short_servername($r_maps['servername']);

	  $map_pcount = small_count("SELECT id FROM uts_player WHERE matchid = $r_maps['id']");

	  echo'
	  <tr>
		<td class="dark" align="center"><a class="darkhuman" href="./?p=match&amp;mid='.$r_maps['id'].'">'.$r_matchtime.'</a></td>
		<td class="grey" align="center">'.$r_maps['gamename'].'</td>
		<td class="grey" align="center">'.$map_pcount.'</td>
		<td class="grey" align="center">'.$r_gametime.'</td>
		<td nowrap class="grey" align="center">'.$r_servername.'</td>
	  </tr>';
}

echo'
</tbody></table>
<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>';
?>
