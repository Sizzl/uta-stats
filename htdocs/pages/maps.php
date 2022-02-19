<?php 
function InvertSort($curr_field, $filter, $sort) {
	if ($curr_field != $filter) return(($curr_field == "mapfile") ? "ASC" : "DESC");
	if ($sort == 'ASC') return('DESC');
	return('ASC');
}

function SortPic($curr_field, $filter, $sort) {
	if ($curr_field != $filter) return;
	$fname = 'images/s_'. strtolower($sort) .'.png';
	if (!file_exists($fname)) return;
	return('&nbsp;<img src="'. $fname .'" border="0" width="11" height="9" alt="" title="('.strtolower($sort).'ending)">');
}
function secToHR($seconds) {
  $days = floor($seconds / (3600*24));
  if ($days > 0)
    $hours = floor($seconds / 3600)-(24*$days);
  else
    $hours = floor($seconds / 3600);

  $minutes = floor(($seconds / 60) % 60);
  $seconds = $seconds % 60;
  return $days > 0 ? "$days d, $hours h, $minutes m, $seconds s" : ($hours > 0 ? "$hours h, $minutes m, $seconds s" : ($minutes > 0 ? "$minutes m, $seconds s" : "$seconds s"));
  // return "$hours:$minutes:$seconds";
}

global $dbversion;
// Get filter and set sorting
$filter = my_addslashes($_GET['filter']);
$sort = my_addslashes($_GET['sort']);

// if (empty($filter) || preg_match("/(\')|(;)|(\-)|(\>)|(!)|(\<)|(where)|(drop)|(select)|(from)|(if)/i",$filter)) {
if (empty($filter) || !(preg_match("/(^mapfile$)|(^matchcount$)|(^pick90$)|(^pick60$)|(^pick30$)|(^lastmonth$)|(^lastweek$)|(^frags$)|(^matchscore$)|(^avggametime$)|(^gametime$)/i",$filter))) {
	$filter = "mapfile";
}

if (empty($sort) or ($sort != 'ASC' and $sort != 'DESC')) $sort = ($filter == "mapfile") ? "ASC" : "DESC";

// Firstly we need to work out First Last Next Prev pages

$mcount = small_count("SELECT mapfile FROM uts_match GROUP BY mapfile");

$ecount = $mcount/32;
$ecount2 = number_format($ecount, 0, '.', '');

if ($ecount > $ecount2) {
	$ecount2 = $ecount2+1;
}

$fpage = 0;
if ($ecount < 1) { $lpage = 0; }
else { $lpage = $ecount2-1; }

$cpage = $_GET["page"];
IF ($cpage == "") { $cpage = "0"; }
$qpage = $cpage*32;

$tfpage = $cpage+1;
$tlpage = $lpage+1;

$ppage = $cpage-1;
$ppageurl = "<a class=\"pages\" href=\"./?p=maps&amp;filter=$filter&amp;sort=$sort&amp;page=$ppage\">[Previous]</a>";
IF ($ppage < "0") { $ppageurl = "[Previous]"; }

$npage = $cpage+1;
$npageurl = "<a class=\"pages\" href=\"./?p=maps&amp;filter=$filter&amp;sort=$sort&amp;page=$npage\">[Next]</a>";
IF ($npage >= "$ecount") { $npageurl = "[Next]"; }

$fpageurl = "<a class=\"pages\" href=\"./?p=maps&amp;filter=$filter&amp;sort=$sort&amp;page=$fpage\">[First]</a>";
IF ($cpage == "0") { $fpageurl = "[First]"; }

$lpageurl = "<a class=\"pages\" href=\"./?p=maps&amp;filter=$filter&amp;sort=$sort&amp;page=$lpage\">[Last]</a>";
IF ($cpage == "$lpage") { $lpageurl = "[Last]"; }

// $sql_maps = "SELECT mapfile, COUNT(id) AS matchcount, AVG(frags) AS frags, AVG(t0score+t1score+t2score+t3score) AS matchscore, SUM(gametime) AS gametime
// FROM uts_match GROUP BY mapfile ORDER BY $filter $sort LIMIT $qpage,32";
$lastmonth = date('Ymdhis', strtotime("-1 month"));
$lastweek = date('Ymdhis', strtotime("-1 week"));
if (isset($dbversion) && floatval($dbversion) > 5.6) {
  $sql_maps = "SELECT mapfile as mfile, mapfile, COUNT(id) AS matchcount, AVG(frags) AS frags,
    AVG(t0score+t1score+t2score+t3score) AS matchscore, SUM(gametime) AS gametime, AVG(gametime) AS avggametime,
    (SELECT ANY_VALUE(`uts_match`.`time`) AS `time` FROM `uts_match` GROUP BY matchcode ORDER BY `time` DESC LIMIT 90,1) AS last90time,
    (SELECT ANY_VALUE(`uts_match`.`time`) AS `time` FROM `uts_match` GROUP BY matchcode ORDER BY `time` DESC LIMIT 60,1) AS last60time,
    (SELECT ANY_VALUE(`uts_match`.`time`) AS `time` FROM `uts_match` GROUP BY matchcode ORDER BY `time` DESC LIMIT 30,1) AS last30time,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= last90time) AS pick90,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= last60time) AS pick60,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= last30time) AS pick30,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= $lastmonth) AS lastmonth,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= $lastweek) AS lastweek
    FROM uts_match GROUP BY mapfile ORDER BY $filter $sort LIMIT $qpage,32";
} else {
  $sql_maps = "SELECT mapfile as mfile, mapfile, COUNT(id) AS matchcount, AVG(frags) AS frags,
    AVG(t0score+t1score+t2score+t3score) AS matchscore, SUM(gametime) AS gametime, AVG(gametime) AS avggametime,
    (SELECT time FROM `uts_match` GROUP By matchcode ORDER BY `uts_match`.`time` DESC LIMIT 90,1) AS last90time,
    (SELECT time FROM `uts_match` GROUP By matchcode ORDER BY `uts_match`.`time` DESC LIMIT 60,1) AS last60time,
    (SELECT time FROM `uts_match` GROUP By matchcode ORDER BY `uts_match`.`time` DESC LIMIT 30,1) AS last30time,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= last90time) AS pick90,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= last60time) AS pick60,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= last30time) AS pick30,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= $lastmonth) AS lastmonth,
    (SELECT COUNT(id) FROM uts_match WHERE uts_match.mapfile = mfile AND uts_match.time >= $lastweek) AS lastweek
    FROM uts_match GROUP BY mapfile ORDER BY $filter $sort LIMIT $qpage,32";
}
// echo "<!-- ".$sql_maps." -->";
echo' <div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: 
'.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div> <table 
class="box" border="0" cellpadding="1" cellspacing="1"> <tbody><tr> <td 
class="heading" colspan="11" align="center">Unreal Tournament Maps List</td> 
</tr> <tr> <td class="smheading" align="center" width="250"><a class="smheading" 
href="./?p=maps&amp;filter=mapfile&amp;sort='.InvertSort('mapfile', $filter, 
$sort).'">Map Name</a>'.SortPic('mapfile', $filter, $sort).'</td> <td 
class="smheading" align="center" width="100"><a class="smheading" 
href="./?p=maps&amp;filter=matchcount&amp;sort='.InvertSort('matchcount', 
$filter, $sort).'">Matches</a>'.SortPic('matchcount', $filter, $sort).'</td> <td 
class="smheading" align="center"><a class="smheading" 
href="./?p=maps&amp;filter=pick90&amp;sort='.InvertSort('pick90', $filter, 
$sort).'">Last 90 games</a>'.SortPic('pick90', $filter, $sort).'</td> <td 
class="smheading" align="center"><a class="smheading" 
href="./?p=maps&amp;filter=pick60&amp;sort='.InvertSort('pick60', $filter, 
$sort).'">Last 60 games</a>'.SortPic('pick60', $filter, $sort).'</td> <td 
class="smheading" align="center"><a class="smheading" 
href="./?p=maps&amp;filter=pick30&amp;sort='.InvertSort('pick30', $filter, 
$sort).'">Last 30 games</a>'.SortPic('pick30', $filter, $sort).'</td> <td 
class="smheading" align="center"><a class="smheading" 
href="./?p=maps&amp;filter=lastmonth&amp;sort='.InvertSort('lastmonth', $filter, 
$sort).'">Last 30 days</a>'.SortPic('lastmonth', $filter, $sort).'</td> <td 
class="smheading" align="center"><a class="smheading" 
href="./?p=maps&amp;filter=lastweek&amp;sort='.InvertSort('lastweek', $filter, 
$sort).'">Last 7 days</a>'.SortPic('lastweek', $filter, $sort).'</td> <td 
class="smheading" align="center"><a class="smheading" 
href="./?p=maps&amp;filter=frags&amp;sort='.InvertSort('frags', $filter, 
$sort).'">Avg. Frags</a>'.SortPic('frags', $filter, $sort).'</td> <td 
class="smheading" align="center" width="100"><a class="smheading" 
href="./?p=maps&amp;filter=matchscore&amp;sort='.InvertSort('matchscore', 
$filter, $sort).'">Avg. Score</a>'.SortPic('matchscore', $filter, $sort).'</td> <td 
class="smheading" align="center" width="100"><a class="smheading" 
href="./?p=maps&amp;filter=avggametime&amp;sort='.InvertSort('avggametime', 
$filter, $sort).'">Avg. Time</a>'.SortPic('avggametimee', $filter, $sort).'</td> 
<td class="smheading" align="center" width="100"><a class="smheading" 
href="./?p=maps&amp;filter=gametime&amp;sort='.InvertSort('gametime', $filter, 
$sort).'">Total Gametime</a>'.SortPic('gametime', $filter, $sort).'</td> </tr>';

$q_maps = mysql_query($sql_maps) or die(mysql_error());
while ($r_maps = mysql_fetch_array($q_maps)) {

	  $r_mapfile = un_ut($r_maps['mapfile']);
	  $myurl = urlencode($r_mapfile);
	  // $r_gametime = GetMinutes($r_maps['gametime']);
	  $r_gametime = secToHR($r_maps['gametime']);
	  $r_avggametime = secToHR($r_maps['avggametime']);

	  echo'
	  <tr>
		<td class="dark" align="center"><a class="darkhuman" href="./?p=minfo&amp;map='.$myurl.'">'.$r_mapfile.'</a></td>
		<td class="grey" align="center">'.$r_maps['matchcount'].'</td>
		<td class="grey" align="center">'.$r_maps['pick90'].'</td>
		<td class="grey" align="center">'.$r_maps['pick60'].'</td>
		<td class="grey" align="center">'.$r_maps['pick30'].'</td>
		<td class="grey" align="center">'.$r_maps['lastmonth'].'</td>
		<td class="grey" align="center">'.$r_maps['lastweek'].'</td>
		<td class="grey" align="center">'.get_dp($r_maps['frags']).'</td>
		<td class="grey" align="center">'.get_dp($r_maps['matchscore']).'</td>
		<td class="grey" align="center">'.$r_avggametime.'</td>
		<td class="grey" align="center">'.$r_gametime.'</td>
	  </tr>';
}

echo'
</tbody></table>
<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>';
?>
