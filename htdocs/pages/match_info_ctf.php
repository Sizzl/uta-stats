<?php 
echo'
<table border="0" cellpadding="0" cellspacing="2" width="720">
  <tbody><tr>
    <td class="heading" colspan="12" align="center">Flag Event Summary</td>
  </tr>
  <tr>
    <td class="hlheading" colspan="12" align="center">Team: Red</td>
  </tr>
    <tr>
    <td class="smheading" rowspan="2" align="center">Player</td>
    <td class="smheading" colspan="2" align="center" width="90">Score</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Taken</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Pickup</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Drop</td>
    <td class="smheading" rowspan="2" align="center" width="60">Flag Assist</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Cover</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Seal</td>
    <td class="smheading" rowspan="2" align="center" width="60">Flag Capture</td>
    <td class="smheading" rowspan="2" align="center" width="60">Flag Kill</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Return</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Team</td>
    <td class="smheading" align="center">Player</td>
  </tr>';

$sql_msred = "SELECT p.playerid, pi.name, pi.banned, p.country, p.pid, p.gamescore, p.flag_taken, p.flag_pickedup, p.flag_dropped, p.flag_assist, p.flag_cover, p.flag_seal, p.flag_capture, p.flag_kill, p.flag_return, p.rank
FROM uts_player AS p, uts_pinfo AS pi WHERE p.pid = pi.id AND matchid = $mid AND team = 0 ORDER BY gamescore DESC";
$q_msred = mysql_query($sql_msred) or die(mysql_error());
$i = 0;
while ($r_msred = zero_out(mysql_fetch_array($q_msred))) {
	  $i++;
	  $class = ($i % 2) ? 'grey' : 'grey2';

	  $redpname = $r_msred['name'];
	  $myurl = urlencode($r_msred['name']);



	echo'<tr>';
	if ($r_msred['banned'] != 'Y') {
		echo '<td nowrap class="darkhuman" align="left"><a class="darkhuman" href="./?p=matchp&amp;mid='.$mid.'&amp;pid='.$r_msred['pid'].'">'.FormatPlayerName($r_msred['country'], $r_msred['pid'], $redpname, $gid, $gamename, true, $r_msred['rank']).'</a></td>';
	} else {
		$r_msred ['gamescore'] = '-';
		$r_msred ['flag_taken'] = '-';
		$r_msred ['flag_pickedup'] = '-';
		$r_msred ['flag_dropped'] = '-';
		$r_msred ['flag_assist'] = '-';
		$r_msred ['flag_cover'] = '-';
		$r_msred ['flag_seal'] = '-';
		$r_msred ['flag_capture'] = '-';
		$r_msred ['flag_kill'] = '-';
		$r_msred ['flag_return'] = '-';
		echo '<td nowrap class="darkhuman" align="left"><span style="text-decoration: line-through;">'.FormatPlayerName($r_msred['country'], $r_msred['pid'], $redpname, $gid, $gamename, true, $r_msred['rank']).'</span></td>';
	}

	echo '
		<td class="'.$class.'" align="center"></td>
		<td class="'.$class.'" align="center">'.$r_msred['gamescore'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_taken'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_pickedup'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_dropped'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_assist'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_cover'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_seal'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_capture'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_kill'].'</td>
		<td class="'.$class.'" align="center">'.$r_msred['flag_return'].'</td>
	  </tr>';
}

$teamscore = small_query("SELECT t0score AS teamscore FROM uts_match WHERE id = $mid");
$msredtot = small_query("SELECT SUM(gamescore) AS gamescore, SUM(flag_taken) AS flag_taken, SUM(flag_pickedup) AS flag_pickedup, SUM(flag_dropped) AS flag_dropped, SUM(flag_assist) AS flag_assist, SUM(flag_cover) AS flag_cover, SUM(flag_seal) AS flag_seal, SUM(flag_capture) AS flag_capture, SUM(flag_kill)as flag_kill, SUM(flag_return) AS flag_return
FROM uts_player WHERE matchid = $mid AND team = 0 ORDER BY gamescore DESC");


echo'
  <tr>
    <td class="dark" align="center">Totals</td>
		<td class="darkgrey" align="center">'.$teamscore['teamscore'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['gamescore'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_taken'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_pickedup'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_dropped'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_assist'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_cover'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_seal'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_capture'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_kill'].'</td>
		<td class="darkgrey" align="center">'.$msredtot['flag_return'].'</td>
  </tr>


  <tr>
    <td class="hlheading" colspan="12" align="center">Team: Blue</td>
  </tr>
    <tr>
    <td class="smheading" rowspan="2" align="center">Player</td>
    <td class="smheading" colspan="2" align="center" width="90">Score</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Taken</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Pickup</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Drop</td>
    <td class="smheading" rowspan="2" align="center" width="60">Flag Assist</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Cover</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Seal</td>
    <td class="smheading" rowspan="2" align="center" width="60">Flag Capture</td>
    <td class="smheading" rowspan="2" align="center" width="60">Flag Kill</td>
    <td class="smheading" rowspan="2" align="center" width="50">Flag Return</td>
  </tr>
  <tr>
    <td class="smheading" align="center">Team</td>
    <td class="smheading" align="center">Player</td>
  </tr>';

$sql_msblue = "SELECT p.playerid, pi.name, pi.banned, p.country, p.pid, p.gamescore, p.flag_taken, p.flag_pickedup, p.flag_dropped, p.flag_assist, p.flag_cover, p.flag_seal, p.flag_capture, p.flag_kill, p.flag_return, p.rank
FROM uts_player AS p, uts_pinfo AS pi WHERE p.pid = pi.id AND matchid = $mid AND team = 1 ORDER BY gamescore DESC";
$q_msblue = mysql_query($sql_msblue) or die(mysql_error());
$i = 0;
while ($r_msblue = zero_out(mysql_fetch_array($q_msblue))) {
	  $i++;
	  $class = ($i % 2) ? 'grey' : 'grey2';

	  $bluepname = $r_msblue['name'];
	  $myurl = urlencode($r_msblue['name']);

	echo'<tr>';
	if ($r_msblue['banned'] != 'Y') {
		echo '<td nowrap class="darkhuman" align="left"><a class="darkhuman" href="./?p=matchp&amp;mid='.$mid.'&amp;pid='.$r_msblue['pid'].'">'.FormatPlayerName($r_msblue['country'], $r_msblue['pid'], $bluepname, $gid, $gamename, true, $r_msblue['rank']).'</a></td>';
	} else {
		$r_msblue ['gamescore'] = '-';
		$r_msblue ['flag_taken'] = '-';
		$r_msblue ['flag_pickedup'] = '-';
		$r_msblue ['flag_dropped'] = '-';
		$r_msblue ['flag_assist'] = '-';
		$r_msblue ['flag_cover'] = '-';
		$r_msblue ['flag_seal'] = '-';
		$r_msblue ['flag_capture'] = '-';
		$r_msblue ['flag_kill'] = '-';
		$r_msblue ['flag_return'] = '-';
		echo '<td nowrap class="darkhuman" align="left"><span style="text-decoration: line-through;">'.FormatPlayerName($r_msblue['country'], $r_msblue['pid'], $bluepname, $gid, $gamename, true, $r_msblue['rank']).'</span></td>';
	}

	echo '
		<td class="'.$class.'" align="center"></td>
		<td class="'.$class.'" align="center">'.$r_msblue['gamescore'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_taken'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_pickedup'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_dropped'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_assist'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_cover'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_seal'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_capture'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_kill'].'</td>
		<td class="'.$class.'" align="center">'.$r_msblue['flag_return'].'</td>
	  </tr>';
}

$teamscore = small_query("SELECT t1score AS teamscore FROM uts_match WHERE id = $mid");
$msbluetot = small_query("SELECT SUM(gamescore) AS gamescore, SUM(flag_taken) AS flag_taken, SUM(flag_pickedup) AS flag_pickedup, SUM(flag_dropped) AS flag_dropped, SUM(flag_assist) AS flag_assist, SUM(flag_cover) AS flag_cover, SUM(flag_seal) AS flag_seal, SUM(flag_capture) AS flag_capture, SUM(flag_kill)as flag_kill, SUM(flag_return) AS flag_return
FROM uts_player WHERE matchid = $mid AND team = 1 ORDER BY gamescore DESC");

echo'
  <tr>
    <td class="dark" align="center">Totals</td>
		<td class="darkgrey" align="center">'.$teamscore['teamscore'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['gamescore'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_taken'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_pickedup'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_dropped'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_assist'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_cover'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_seal'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_capture'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_kill'].'</td>
		<td class="darkgrey" align="center">'.$msbluetot['flag_return'].'</td>
  </tr>';

// Check If Green Team Were Used (CTF4 Compatibility)

$greencheck = small_count("SELECT * FROM uts_player WHERE matchid = $mid AND team = 2");
if ($greencheck > 0) {

	  echo'<tr>
		<td class="hlheading" colspan="12" align="center">Team: Green</td>
	  </tr>
		<tr>
		<td class="smheading" rowspan="2" align="center">Player</td>
		<td class="smheading" colspan="2" align="center" width="90">Score</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Taken</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Pickup</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Drop</td>
		<td class="smheading" rowspan="2" align="center" width="60">Flag Assist</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Cover</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Seal</td>
		<td class="smheading" rowspan="2" align="center" width="60">Flag Capture</td>
		<td class="smheading" rowspan="2" align="center" width="60">Flag Kill</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Return</td>
	  </tr>
	  <tr>
		<td class="smheading" align="center">Team</td>
		<td class="smheading" align="center">Player</td>
	  </tr>';

	$sql_msgreen = "SELECT p.playerid, pi.name, pi.banned, p.country, p.pid, p.gamescore, p.flag_taken, p.flag_pickedup, p.flag_dropped, p.flag_assist, p.flag_cover, p.flag_seal, p.flag_capture, p.flag_kill, p.flag_return, p.rank
	FROM uts_player AS p, uts_pinfo AS pi WHERE p.pid = pi.id AND matchid = $mid AND team = 2 ORDER BY gamescore DESC";
	$q_msgreen = mysql_query($sql_msgreen) or die(mysql_error());
	$i = 0;
	while ($r_msgreen = zero_out(mysql_fetch_array($q_msgreen))) {
		  $i++;
		  $class = ($i % 2) ? 'grey' : 'grey2';

		  $greenpname = $r_msgreen['name'];
		  $myurl = urlencode($r_msgreen['name']);

		echo'<tr>';
		if ($r_msgreen['banned'] != 'Y') {
			echo '<td nowrap class="darkhuman" align="left"><a class="darkhuman" href="./?p=matchp&amp;mid='.$mid.'&amp;pid='.$r_msgreen['pid'].'">'.FormatPlayerName($r_msgreen['country'], $r_msgreen['pid'], $greenpname, $gid, $gamename, true, $r_msgreen['rank']).'</a></td>';
		} else {
			$r_msgreen ['gamescore'] = '-';
			$r_msgreen ['flag_taken'] = '-';
			$r_msgreen ['flag_pickedup'] = '-';
			$r_msgreen ['flag_dropped'] = '-';
			$r_msgreen ['flag_assist'] = '-';
			$r_msgreen ['flag_cover'] = '-';
			$r_msgreen ['flag_seal'] = '-';
			$r_msgreen ['flag_capture'] = '-';
			$r_msgreen ['flag_kill'] = '-';
			$r_msgreen ['flag_return'] = '-';
			echo '<td nowrap class="darkhuman" align="left"><span style="text-decoration: line-through;">'.FormatPlayerName($r_msgreen['country'], $r_msgreen['pid'], $greenpname, $gid, $gamename, true, $r_msgreen['rank']).'</span></td>';
		}

		echo '
			<td class="'.$class.'" align="center"></td>
			<td class="'.$class.'" align="center">'.$r_msgreen['gamescore'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_taken'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_pickedup'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_dropped'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_assist'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_cover'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_seal'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_capture'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_kill'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgreen['flag_return'].'</td>
		  </tr>';
	}

	$teamscore = small_query("SELECT t1score AS teamscore FROM uts_match WHERE id = $mid");
	$msgreentot = small_query("SELECT SUM(gamescore) AS gamescore, SUM(flag_taken) AS flag_taken, SUM(flag_pickedup) AS flag_pickedup, SUM(flag_dropped) AS flag_dropped, SUM(flag_assist) AS flag_assist, SUM(flag_cover) AS flag_cover, SUM(flag_seal) AS flag_seal, SUM(flag_capture) AS flag_capture, SUM(flag_kill)as flag_kill, SUM(flag_return) AS flag_return
	FROM uts_player WHERE matchid = $mid AND team = 2 ORDER BY gamescore DESC");

	echo'
	  <tr>
		<td class="dark" align="center">Totals</td>
			<td class="darkgrey" align="center">'.$teamscore['teamscore'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['gamescore'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_taken'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_pickedup'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_dropped'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_assist'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_cover'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_seal'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_capture'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_kill'].'</td>
			<td class="darkgrey" align="center">'.$msgreentot['flag_return'].'</td>
	  </tr>';
}

// Check If Gold Team Were Used (CTF4 Compatibility)

$goldcheck = small_count("SELECT * FROM uts_player WHERE matchid = $mid AND team = 3");
if ($goldcheck > 0) {

	  echo'<tr>
		<td class="hlheading" colspan="12" align="center">Team: Gold</td>
	  </tr>
		<tr>
		<td class="smheading" rowspan="2" align="center">Player</td>
		<td class="smheading" colspan="2" align="center" width="90">Score</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Taken</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Pickup</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Drop</td>
		<td class="smheading" rowspan="2" align="center" width="60">Flag Assist</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Cover</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Seal</td>
		<td class="smheading" rowspan="2" align="center" width="60">Flag Capture</td>
		<td class="smheading" rowspan="2" align="center" width="60">Flag Kill</td>
		<td class="smheading" rowspan="2" align="center" width="50">Flag Return</td>
	  </tr>
	  <tr>
		<td class="smheading" align="center">Team</td>
		<td class="smheading" align="center">Player</td>
	  </tr>';

	$sql_msgold = "SELECT p.playerid, pi.name, pi.banned, p.country, p.pid, p.gamescore, p.flag_taken, p.flag_pickedup, p.flag_dropped, p.flag_assist, p.flag_cover, p.flag_seal, p.flag_capture, p.flag_kill, p.flag_return, p.rank
	FROM uts_player AS p, uts_pinfo AS pi WHERE p.pid = pi.id AND matchid = $mid AND team = 3 ORDER BY gamescore DESC";
	$q_msgold = mysql_query($sql_msgold) or die(mysql_error());
	$i = 0;
	while ($r_msgold = zero_out(mysql_fetch_array($q_msgold))) {
		  $i++;
		  $class = ($i % 2) ? 'grey' : 'grey2';

		  $goldpname = $r_msgold['name'];
		  $myurl = urlencode($r_msgold['name']);

		echo'<tr>';
		if ($r_msgold['banned'] != 'Y') {
			echo '<td nowrap class="darkhuman" align="left"><a class="darkhuman" href="./?p=matchp&amp;mid='.$mid.'&amp;pid='.$r_msgold['pid'].'">'.FormatPlayerName($r_msgold['country'], $r_msgold['pid'], $goldpname, $gid, $gamename, true, $r_msgold['rank']).'</a></td>';
		} else {
			$r_msgold ['gamescore'] = '-';
			$r_msgold ['flag_taken'] = '-';
			$r_msgold ['flag_pickedup'] = '-';
			$r_msgold ['flag_dropped'] = '-';
			$r_msgold ['flag_assist'] = '-';
			$r_msgold ['flag_cover'] = '-';
			$r_msgold ['flag_seal'] = '-';
			$r_msgold ['flag_capture'] = '-';
			$r_msgold ['flag_kill'] = '-';
			$r_msgold ['flag_return'] = '-';
			echo '<td nowrap class="darkhuman" align="left"><span style="text-decoration: line-through;">'.FormatPlayerName($r_msgold['country'], $r_msgold['pid'], $goldpname, $gid, $gamename, true, $r_msgold['rank']).'</span></td>';
		}

		echo '
			<td class="'.$class.'" align="center"></td>
			<td class="'.$class.'" align="center">'.$r_msgold['gamescore'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_taken'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_pickedup'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_dropped'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_assist'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_cover'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_seal'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_capture'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_kill'].'</td>
			<td class="'.$class.'" align="center">'.$r_msgold['flag_return'].'</td>
		  </tr>';
	}

	$teamscore = small_query("SELECT t1score AS teamscore FROM uts_match WHERE id = $mid");
	$msgoldtot = small_query("SELECT SUM(gamescore) AS gamescore, SUM(flag_taken) AS flag_taken, SUM(flag_pickedup) AS flag_pickedup, SUM(flag_dropped) AS flag_dropped, SUM(flag_assist) AS flag_assist, SUM(flag_cover) AS flag_cover, SUM(flag_seal) AS flag_seal, SUM(flag_capture) AS flag_capture, SUM(flag_kill)as flag_kill, SUM(flag_return) AS flag_return
	FROM uts_player WHERE matchid = $mid AND team = 3 ORDER BY gamescore DESC");

	echo'
	  <tr>
		<td class="dark" align="center">Totals</td>
			<td class="darkgrey" align="center">'.$teamscore['teamscore'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['gamescore'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_taken'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_pickedup'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_dropped'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_assist'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_cover'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_seal'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_capture'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_kill'].'</td>
			<td class="darkgrey" align="center">'.$msgoldtot['flag_return'].'</td>
	  </tr>';
}

echo'</tbody></table>
<br>';

?>
