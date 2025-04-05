<?php 
/* brajan
18-10-2007 # let's cache this bitch :/
*/
$plik = 'uta_pug_totals-'.md5(base64_encode($_SERVER['QUERY_STRING'])); //file name
$out = '';
$home_plik = '/home/utassault/web/pugstats/static/'.$plik.'.cache'; //file path
$lease = 1800; // cache file expire time
global $t_match, $t_pinfo, $t_player, $t_games, $dbversion; // fetch table globals.
if ( ( !file_exists($home_plik)) OR (time() > (int)filemtime($home_plik) + (int)$lease ) ){
	
include_once('includes/teamstats.php');
// include_once('includes/uta_functions.php');
$matchcode = $_GET['matchcode'];
// FINAL SCORE brajan 26082005

//$score0 ='0';
//$score1 ='0';

if (isset($dbversion) && floatval($dbversion) > 5.6) {
  $sql_matchsummary = "SELECT ANY_VALUE(serverip) AS serverip, ANY_VALUE(servername) AS servername, ANY_VALUE(serverinfo) AS serverinfo, ANY_VALUE(gameinfo) AS gameinfo ,SUM(gametime) AS totaltime FROM ".(isset($t_match) ? $t_match : "uts_match")." WHERE servername LIKE '%PUG%' AND matchmode = '1' GROUP BY serverip";	  
} else {
  $sql_matchsummary = "SELECT serverip,servername,serverinfo,gameinfo,SUM(gametime) AS totaltime FROM ".(isset($t_match) ? $t_match : "uts_match")." WHERE servername LIKE '%PUG%' AND matchmode = '1' GROUP BY serverip";	  
}
$q_matchsummary = mysql_query($sql_matchsummary) or die(mysql_error());
$r_matchsummary = mysql_fetch_array($q_matchsummary);
$serverip = $r_matchsummary['serverip'];
$servername = $r_matchsummary['servername'];
$serverinfo = $r_matchsummary['serverinfo'];
$gameinfo = $r_matchsummary['gameinfo'];
$total_time = $total_time + $r_matchsummary['totaltime'];
$server_info = preg_split('/\n | \r/', $serverinfo, -1, PREG_SPLIT_NO_EMPTY);
$out .= '
	<table border="0" cellpadding="3" cellspacing="3" width="720" style="background-color:#0F1D2F">
	  <tbody>
	  <tr><td align="center"><a href="unreal://'.$serverip.'">'. $servername.' - '.$serverip.'</a></td></tr>
	  <tr><td align="center" class="grey">'.$gameinfo.'</td></tr>
	  <tr><td align="center" class="heading"><strong> '.$team0.' '.$score0.' </strong></td></tr>
	  <tr><td align="center" class="grey">Total playing time: '. GetMinutes($total_time).' minutes</td></tr>
	</tbody></table>';

	$out .= '<br />
	<table border="0" cellpadding="0" cellspacing="2" width="720">
	<tbody>
	<tr><td class="hlheading" colspan="15" align="center">PUG Matches Summary</td></tr>';		
	$out .= '
	<tr class="lggrey"><td align="center"><br/>
	<table border="0" cellpadding="0" cellspacing="2" width="690">
	<tbody>';
	$out .= '
	<tr class="smheading" style="height:20px">';
	$out .= '
		<td  align="center" rowspan="2"><a href="?p=utapugsummary&sort=pname">Player</a></td>		
		<td align="center" rowspan="2"><a href="?p=utapugsummary&sort=objs">Objs</a></td>
		<td align="center"rowspan="2"><a href="?p=utapugsummary&sort=ass_assist">Assists</a></td>
		<td align="center" colspan="2">Hammerlaunches</td>
		<td align="center" colspan="2">Rocketlaunches</td>
		<td align="center" rowspan="2"><a href="?p=utapugsummary&sort=ass_h_jump">H-Jumps</td>	
		<td align="center" rowspan="2"><a href="?p=utapugsummary&sort=kills">Kills</a></td>
		<td align="center" rowspan="2"><a href="?p=utapugsummary&sort=deaths">Death</a></td>
		<td align="center" rowspan="2"><a href="?p=utapugsummary&sort=maps">Maps</a></td>
		<td align="center" rowspan="2"><a href="?p=utapugsummary&sort=ping">Ping</a></td>
		</tr><tr class="smheading" style="height:20px">		
		<td align="center"><a href="?p=utapugsummary&sort=ass_h_launch">Launcher</a></td>
		<td align="center"><a href="?p=utapugsummary&sort=ass_h_launched">Pass.</a></td>
		<td align="center"><a href="?p=utapugsummary&sort=ass_r_launch">Launcher</a></td>
		<td align="center"><a href="?p=utapugsummary&sort=ass_r_launched">Pass.</a></td>
	</tr>';
	$sort_allowed = array("pname", "objs", "ass_assist", "kills", "ass_h_launch", "ass_h_launched", "ass_r_launch", "ass_r_launched", "deaths", "maps", "ping", "ass_h_jump");
	$sort_by = ( (!empty($_GET['sort'])) && (in_array($_GET['sort'], $sort_allowed)) ) ? $_GET['sort'] : 'objs';
	
	$sql =  "SELECT SUM(".(isset($t_player) ? $t_player : "uts_player").".frags) as frags, 
			(SUM(".(isset($t_player) ? $t_player : "uts_player").".kills) - SUM(".(isset($t_player) ? $t_player : "uts_player").".teamkills)) as kills, SUM(".(isset($t_player) ? $t_player : "uts_player").".deaths) as deaths, AVG(".(isset($t_player) ? $t_player : "uts_player").".avgping) as ping, 
			COUNT(".(isset($t_player) ? $t_player : "uts_player").".matchid) as maps, AVG(".(isset($t_player) ? $t_player : "uts_player").".team) as team,
			SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launch) as ass_h_launch, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launch) as ass_r_launch,
			SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launched) as ass_h_launched, SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launched) as ass_r_launched,
			SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_assist) as ass_assist, SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_h_jump) as ass_h_jump, SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_obj) as objs,
			".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id as pid, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name as pname, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country as pcountry
			FROM ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." INNER JOIN ".(isset($t_player) ? $t_player : "uts_player")." on ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id = ".(isset($t_player) ? $t_player : "uts_player").".pid
			INNER JOIN ".(isset($t_match) ? $t_match : "uts_match")." on ".(isset($t_player) ? $t_player : "uts_player").".matchid = ".(isset($t_match) ? $t_match : "uts_match").".id AND ".(isset($t_match) ? $t_match : "uts_match").".matchmode = 1 AND ".(isset($t_match) ? $t_match : "uts_match").".servername LIKE '%PUG%'	
			GROUP BY ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country 
			ORDER BY ".$sort_by." DESC";
	$q_sql = mysql_query($sql) or die(mysql_error());
	$out .=  '<!-- debug: '.$sql.' -->';	
$i = 0;
	while ($p_sql = mysql_fetch_assoc($q_sql)) 
	{	
		if(substr($p_sql['pname'], -1) != '�'){ continue; }
		$tr_color = ($i % 2)? "#8F8F8F" : "";
	
		$out .=  '<tr class="grey" style="background-color:'.$tr_color.'; height:20px; vertical-align:middle">';
		$out .=  '<td nowrap align="left"><b>'.FormatPlayerName($p_sql['pcountry'], $p_sql['pid'], $p_sql['pname']).'</b></td>';
		$out .=  '<td nowrap align="center">'.$p_sql['objs'].'</td>';
		$out .=  '<td nowrap align="center">'.$p_sql['ass_assist'].'</td>';
		$out .=  '<td nowrap align="center">'.$p_sql['ass_h_launch'].'</td>';
		$out .=  '<td nowrap align="center">'.$p_sql['ass_h_launched'].'</td>';
		$out .=  '<td nowrap align="center">'.$p_sql['ass_r_launch'].'</td>';
		$out .=  '<td nowrap align="center">'.$p_sql['ass_r_launched'].'</td>';
		$out .=  '<td nowrap align="center">'.$p_sql['ass_h_jump'].'</td>';
		$out .=  '<td nowrap align="center">'.$p_sql['kills'].'</td>';
		$out .=  '<td nowrap align="center">'.$p_sql['deaths'].'</td>';
		$out .=  '<td nowrap align="center">'.intval($p_sql['maps'] / 2).'</td>';
		$out .=  '<td nowrap align="center">'.intval($p_sql['ping']).'</td>';
		$out .=  '</tr>';
		$i++;
	}
	
	// Team Summary
	$out .= '<tr class="grey"><td align="center" colspan="12">Match Team Totals</td></tr>';	
	$sql =  "SELECT SUM(".(isset($t_player) ? $t_player : "uts_player").".frags) as frags, 
			(SUM(".(isset($t_player) ? $t_player : "uts_player").".kills) - SUM(".(isset($t_player) ? $t_player : "uts_player").".teamkills)) as kills, sum(".(isset($t_player) ? $t_player : "uts_player").".deaths) as deaths, avg(".(isset($t_player) ? $t_player : "uts_player").".avgping) as ping, 
			COUNT(".(isset($t_player) ? $t_player : "uts_player").".matchid) as maps, ".(isset($t_player) ? $t_player : "uts_player").".team as team,
			SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launch) as ass_h_launch, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launch) as ass_r_launch,
			SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_h_launched) as ass_h_launched, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_r_launched) as ass_r_launched,
			SUM(".(isset($t_player) ? $t_player : "uts_player").".ass_assist) as ass_assist, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_h_jump) as ass_h_jump, sum(".(isset($t_player) ? $t_player : "uts_player").".ass_obj) as objs,
			".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id as pid, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name as pname, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country as pcountry
			FROM ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." inner join ".(isset($t_player) ? $t_player : "uts_player")." on ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id = ".(isset($t_player) ? $t_player : "uts_player").".pid
			INNER JOIN ".(isset($t_match) ? $t_match : "uts_match")." on ".(isset($t_player) ? $t_player : "uts_player").".matchid = ".(isset($t_match) ? $t_match : "uts_match").".id AND ".(isset($t_match) ? $t_match : "uts_match").".matchmode = 1 AND ".(isset($t_match) ? $t_match : "uts_match").".servername LIKE '%PUG%'	
			GROUP BY ".(isset($t_player) ? $t_player : "uts_player").".team
			ORDER BY ".(isset($t_player) ? $t_player : "uts_player").".team";
$i = 0;
	$q_sql = mysql_query($sql) or die(mysql_error());
	while ($p_sql = mysql_fetch_assoc($q_sql)) {
	$tr_color = ($i % 2)? "#8F8F8F" : "";
	$team = ($p_sql['team'] == '1') ? "Red" : "Blue";
		$out .=  '<tr class="grey" style="background-color:'.$tr_color.'; height:20px; vertical-align:middle">
							<td nowrap align="left"><b>'.$team.'</b></td>
							<td nowrap align="center">'.$p_sql['objs'].'</td>
							<td nowrap align="center">'.$p_sql['ass_assist'].'</td>
							<td nowrap align="center">'.$p_sql['ass_h_launch'].'</td>
							<td nowrap align="center">'.$p_sql['ass_h_launched'].'</td>
							<td nowrap align="center">'.$p_sql['ass_r_launch'].'</td>
							<td nowrap align="center">'.$p_sql['ass_r_launched'].'</td>
							<td nowrap align="center">'.$p_sql['ass_h_jump'].'</td>
							<td nowrap align="center">'.$p_sql['kills'].'</td>
							<td nowrap align="center">'.$p_sql['deaths'].'</td>
							<td nowrap align="center"> -- </td>
							<td nowrap align="center">'.intval($p_sql['ping']).'</td>
					</tr>';
		$i++;
	}
	
	
	$out .= '</td>
					</tr></tbody></table>
					<br/></tbody></table>';
		
	// MATCHSTATS - END
$handle = fopen($home_plik, 'w+');
	 if (fwrite($handle, $out) === FALSE) {
		 $out .= "ERROR: There seems to be a problem with a cache file: ".  $plik;
	   }
	fclose($handle);
}
else{
	$out .= file_get_contents($home_plik);
}
echo $out;
 // END FUNCTION
?>
