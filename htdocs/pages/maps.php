<?
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


// Get filter and set sorting
$filter = my_addslashes($_GET[filter]);
$sort = my_addslashes($_GET[sort]);

IF (empty($filter)) {
	$filter = "mapfile";
}

if (empty($sort) or ($sort != 'ASC' and $sort != 'DESC')) $sort = ($filter == "mapfile") ? "ASC" : "DESC";

// Firstly we need to work out First Last Next Prev pages

$mcount = small_count("SELECT mapfile FROM uts_match GROUP BY mapfile");

$ecount = $mcount/32;
$ecount2 = number_format($ecount, 0, '.', '');

IF($ecount > $ecount2) {
	$ecount2 = $ecount2+1;
}

$fpage = 0;
IF($ecount < 1) { $lpage = 0; }
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

echo' <div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: 
'.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div> <table 
class="box" border="0" cellpadding="1" cellspacing="1"> <tbody><tr> <td 
class="heading" colspan="5" align="center">Unreal Tournament Maps List</td> 
</tr> <tr> <td class="smheading" align="center" width="250"><a class="smheading" 
href="./?p=maps&amp;filter=mapfile&amp;sort='.InvertSort('mapfile', $filter, 
$sort).'">Map Name</a>'.SortPic('mapfile', $filter, $sort).'</td> <td 
class="smheading" align="center" width="150"><a class="smheading" 
href="./?p=maps&amp;filter=matchcount&amp;sort='.InvertSort('matchcount', 
$filter, $sort).'">Matches</a>'.SortPic('matchcount', $filter, $sort).'</td> <td 
class="smheading" align="center"><a class="smheading" 
href="./?p=maps&amp;filter=frags&amp;sort='.InvertSort('frags', $filter, 
$sort).'">Avg. Frags</a>'.SortPic('frags', $filter, $sort).'</td> <td 
class="smheading" align="center" width="100"><a class="smheading" 
href="./?p=maps&amp;filter=matchscore&amp;sort='.InvertSort('matchscore', 
$filter, $sort).'">Avg. Score</a>'.SortPic('matchscore', $filter, $sort).'</td> 
<td class="smheading" align="center" width="100"><a class="smheading" 
href="./?p=maps&amp;filter=gametime&amp;sort='.InvertSort('gametime', $filter, 
$sort).'">Time</a>'.SortPic('gametime', $filter, $sort).'</td> </tr>';

$sql_maps = "SELECT mapfile, COUNT(id) AS matchcount, AVG(frags) AS frags, AVG(t0score+t1score+t2score+t3score) AS matchscore, SUM(gametime) AS gametime
FROM uts_match GROUP BY mapfile ORDER BY $filter $sort LIMIT $qpage,32";
$q_maps = mysql_query($sql_maps) or die(mysql_error());
while ($r_maps = mysql_fetch_array($q_maps)) {

	  $r_mapfile = un_ut($r_maps[mapfile]);
	  $myurl = urlencode($r_mapfile);
	  $r_gametime = GetMinutes($r_maps[gametime]);

	  echo'
	  <tr>
		<td class="dark" align="center"><a class="darkhuman" href="./?p=minfo&amp;map='.$myurl.'">'.$r_mapfile.'</a></td>
		<td class="grey" align="center">'.$r_maps[matchcount].'</td>
		<td class="grey" align="center">'.get_dp($r_maps[frags]).'</td>
		<td class="grey" align="center">'.get_dp($r_maps[matchscore]).'</td>
		<td class="grey" align="center">'.$r_gametime.'</td>
	  </tr>';
}

echo'
</tbody></table>
<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>';
?>