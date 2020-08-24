<?php 

// include ("includes/uta_functions.php");
global $t_match, $t_pinfo, $t_player, $t_games; // fetch table globals.

error_reporting(E_ALL^E_NOTICE);
// Firstly we need to work out First Last Next Prev pages
$where = ' ';
$year = !empty($_REQUEST['year']) ? my_addslashes(sprintf("%04d", $_REQUEST['year'])) : 0;
$month = !empty($_REQUEST['month']) ? my_addslashes(sprintf("%02d", $_REQUEST['month'])) : 0;
$day = !empty($_REQUEST['day']) ? my_addslashes(sprintf("%02d", $_REQUEST['day'])) : 0;
$gid  = !empty($_REQUEST['gid']) ?  my_addslashes($_REQUEST['gid']) : 0;

if (!empty($year) and empty($month) and empty($day)) $where .= " AND m.time LIKE '$year%'";
if (!empty($year) and !empty($month) and empty($day)) $where .= " AND m.time LIKE '$year$month%'";
if (!empty($year) and !empty($month) and !empty($day)) $where .= " AND m.time LIKE '$year$month$day%'";
if (!empty($gid)) $where .= " AND m.gid = '$gid'";
 
// Added extra filter for non-specific friendly matches
// 2006-09-05 added some stuff to avoid RED vs BLUE or empty clan names // brajan
// 2012-12-20 removed blue/red filter for Pug games
$where .= " AND (matchcode <> 'HIDDEN') AND (teamname0 <> 'Red Team' AND teamname0 <> 'RED' AND teamname1 <> ''AND teamname1 <> 'Blue Team' AND teamname1 <> 'BLUE' AND teamname1 <> '(None)' AND teamname1 <> '')";
// $where .= " AND (matchcode <> 'HIDDEN') AND (teamname0 <> 'Red Team' AND teamname1 <> '' AND teamname1 <> 'Blue Team' AND teamname1 <> '(None)' AND teamname1 <> '')";

// NEW QUERY
// 2006-09-05 changed query to fix clan tags and paging // brajan 
$r_mcount = mysql_query("SELECT COUNT(*) AS result FROM uts_match m WHERE matchmode = 1 $where GROUP BY teamname0, teamname1, matchcode ORDER BY mapsequence,time");
while($count_row = mysql_fetch_assoc($r_mcount)){
$mcount[] = $count_row['result'];
}
$mcount = count($mcount);
$ecount = $mcount/25;
$ecount2 = number_format($ecount, 0, '.', '');
	if($ecount > $ecount2) {
		$ecount2 = $ecount2+1;
	}
$fpage = 0;
	if($ecount < 1) { $lpage = 0; }
	else { $lpage = $ecount2-1; }
$cpage = $_REQUEST["page"];
if($cpage == "") { $cpage = "0"; }
$qpage = $cpage*25;
$tfpage = $cpage+1;
$tlpage = $lpage+1;

$ppage = $cpage-1;
$ppageurl = "<a class=\"pages\" href=\"./?p=utarecent&amp;year=$year&amp;month=$month&amp;day=$day&amp;gid=$gid&amp;page=$ppage\">[Previous]</a>";
	if($ppage < "0") { $ppageurl = "[Previous]"; }

$npage = $cpage+1;
$npageurl = "<a class=\"pages\" href=\"./?p=utarecent&amp;year=$year&amp;month=$month&amp;day=$day&amp;gid=$gid&amp;page=$npage\">[Next]</a>";
	if($npage >= "$ecount") { $npageurl = "[Next]"; }

$fpageurl = "<a class=\"pages\" href=\"./?p=utarecent&amp;year=$year&amp;month=$month&amp;day=$day&amp;gid=$gid&amp;page=$fpage\">[First]</a>";
	if($cpage == "0") { $fpageurl = "[First]"; }

$lpageurl = "<a class=\"pages\" href=\"./?p=utarecent&amp;year=$year&amp;month=$month&amp;day=$day&amp;gid=$gid&amp;page=$lpage\">[Last]</a>";
	if($cpage == "$lpage") { $lpageurl = "[Last]"; }

echo '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
echo '<input type="hidden" name="p" value="'.$_REQUEST['p'].'">';
echo '<table width="600" class="searchform" border="0" cellpadding="1" cellspacing="1">';
echo '<tr><td><strong>Filter:</strong></td>';
echo '<td><select class="searchform" name="year">';
echo '<option value="0">*</option>';
	for($i = date('Y');$i >= date("Y") - 5; $i--) {
		$selected = ($year == $i) ? 'selected' : '';
		echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
	}
echo '</select>';
echo '&nbsp;';
echo '<select class="searchform" name="month">';
echo '<option value="0">*</option>';
$monthname = array('', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	for($i = 1;$i <= 12; $i++) {
		$selected = ($month == $i) ? 'selected' : '';
		echo '<option '.$selected.' value="'.$i.'">'.$monthname[$i].'</option>';
	}
echo '</select>';
echo '&nbsp;';
echo '<select class="searchform" name="day">';
echo '<option value="0">*</option>';
	for($i = 1;$i <= 31; $i++) {
		$selected = ($day == $i) ? 'selected' : '';
		echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
	}
echo '</select></td>';
echo '<td>Gametype:</td>';
echo '<td><select class="searchform" name="gid">';
echo '<option value="0">*</option>';
//$sql_game = "SELECT DISTINCT(p.gid), g.name FROM uts_player AS p, uts_games AS g WHERE p.gid = g.id ORDER BY g.name ASC";
$sql_game = "SELECT g.id as gid, g.name FROM uts_games AS g ORDER BY g.name ASC";
$q_game = mysql_query($sql_game) or die(mysql_error());
	while ($r_game = mysql_fetch_array($q_game)) {
		$selected = ($r_game['gid'] == $gid) ? 'selected' : '';
		echo '<option '.$selected.' value="'.$r_game['gid'].'">'. $r_game['name'] .'</option>';
	}
echo '</select></td>';
echo '<td><input class="searchform" type="Submit" name="filter" value="Apply"></td>';
echo'</tr></table></form><br>';
echo'
<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>
<table width="720" class="box" border="0" cellpadding="3" cellspacing="1">
  <tbody><tr>
    <td class="heading" colspan="7" align="center">Unreal Tournament League Match List</td>
  </tr>
  <tr>
    <td class="smheading" align="center" width="190">Date/Time</td>
    <td class="smheading" align="center" width="100">Match Type</td>
    <td nowrap class="smheading" align="center">Clans</td>
    <td class="smheading" align="center" width="70">Time</td>
    <td nowrap class="smheading" align="center" width="50">Score</td>
    <td nowrap class="smheading" align="center" width="130">Server</td>
  </tr>';
$sql_recent = "SELECT m.id, m.time, g.name AS gamename, m.serverinfo, m.gametime, m.matchmode, m.teamname0, m.teamname1, m.matchcode FROM uts_match AS m, uts_games AS g WHERE g.id = m.gid AND m.matchmode = 1 $where GROUP BY m.teamname0, m.teamname1, m.matchcode ORDER BY m.time DESC LIMIT $qpage,25";
$q_recent = mysql_query($sql_recent) or die(mysql_error());
while ($r_recent = mysql_fetch_array($q_recent)) {
	  $r_time = mdate($r_recent[time]);
	  $r_mapfile = un_ut($r_recent[mapfile]);
	  $r_gametime = GetMinutes($r_recent[gametime]);
// TOTAL MATCH TIME AND SERVER
		$sql_matchsummary = "SELECT id, gametime, servername, serverip, score0, score1 FROM uts_match WHERE matchmode = 1 AND matchcode='".$r_recent[matchcode]."' ORDER BY mapsequence";	  
		$q_matchsummary = mysql_query($sql_matchsummary) or die(mysql_error());
		$total_time = 0;
			while ($r_matchsummary = mysql_fetch_array($q_matchsummary)) {
				$total_time = $total_time + $r_matchsummary[gametime];
				$servername = $r_matchsummary[servername];
			  	$serverip = $r_matchsummary[serverip];
			  	$score0 = $r_matchsummary[score0];
			  	$score1 = $r_matchsummary[score1];
			}
	$servername = get_short_servername($servername);
	$total_time = GetHours($total_time);
	//if($score0 == '-1' || $score1 == '-1') { continue; }
	echo'
	  <tr>
		<td nowrap class="dark" align="center"><a class="darkhuman" href="./?p=uta_match&amp;matchcode='.$r_recent[matchcode].'">'.$r_time.'</a></td>
		<td nowrap class="grey" align="center">'.$r_recent[gamename].'</td>
		<td nowrap class="grey" align="center"><a class="grey" href="./?p=utateams&amp;team='.urlencode($r_recent[teamname0]).'">'.htmlspecialchars($r_recent[teamname0]).'</a> vs. <a class="grey" href="./?p=utateams&amp;team='.urlencode($r_recent[teamname1]).'">'.htmlspecialchars($r_recent[teamname1]).'</a></td>
		<td class="grey" align="center">'.$total_time.'</td>
    	<td nowrap class="grey" align="center">'.$score0.' - '.$score1.'</td>    	
    	<td class="grey" align="center">'.$servername.'</td>
	  </tr>';
		//<td nowrap class="grey" align="center"><a class="grey" href="./?p=sinfo&serverip='.$serverip.'">'.$servername.'</a></td>
}
echo'</tbody></table><div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>';
?>
