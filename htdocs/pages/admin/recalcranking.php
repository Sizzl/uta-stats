<?php 
if (empty($import_adminkey) or isset($_REQUEST['import_adminkey']) or $import_adminkey != $adminkey) die('Wrong key!');
// Adding listing toggle --// Timo 01/05/07
$list = false;
$list = ($_GET['list'] == 'hide' && !isset($_POST['list'])) ? false : true;
$options['showlist'] = $list;

$options['title'] = 'Recalculate Rankings';
$options['requireconfirmation'] = false;
$i = 0;
$options['vars'][$i]['name'] = 'reset';
$options['vars'][$i]['type'] = 'static';
$options['vars'][$i]['options'] = 'No|Yes';
$options['vars'][$i]['prompt'] = 'Clear existing rankings';
$options['vars'][$i]['caption'] = 'Clear rankings:';
$i++;
if (isset($_GET['piddebug']) || isset($_POST['values'])) {
	$options['vars'][$i]['name'] = 'piddebug';
	$options['vars'][$i]['type'] = 'static';
	$options['vars'][$i]['options'] = 'True';
	$options['vars'][$i]['prompt'] = 'PID debugging';
	$options['vars'][$i]['caption'] = 'Debugging On:';
	$i++;
	$options['vars'][$i]['name'] = 'debugpid';
	$options['vars'][$i]['type'] = 'player';
	$options['vars'][$i]['options'] = '0';
	$options['vars'][$i]['prompt'] = 'Select player';
	$options['vars'][$i]['caption'] = 'Debug player:';
	$i++;
	if (isset($_POST['values']) && (isset($_GET['piddebug']) != true && isset($_POST['piddebug']) != true)) {
		if (stristr($_POST['values'],"piddebug=>true")===false) {
			$i = $i - 2;
		}
	}

}
$options['vars'][$i]['name'] = 'start';
$options['vars'][$i]['type'] = 'static';
$options['vars'][$i]['options'] = 'No|Yes';
$options['vars'][$i]['exitif'] = 'No';
$options['vars'][$i]['prompt'] = 'Are you sure';
$options['vars'][$i]['caption'] = 'Sure:';
$i++;

$results = adminselect($options);

if ($results['start'] != 'Yes' && !isset($iscli)) {
	include('pages/admin/main.php');
	exit;
}
@ignore_user_abort(true);
@set_time_limit(0);
//ini_set('memory_limit','100M');

echo'<br><table border="0" cellpadding="1" cellspacing="2" width="600">
<tr>
	<td class="smheading" align="center" colspan="2">Recalculating Rankings</td>
</tr>';
if($results['reset'] == 'Yes'){ // truncate table ONLY if selected
echo'<tr>
	<td class="smheading" align="left" width="200">Deleting rankings</td>';
	mysql_query("TRUNCATE uts_rank") or die(mysql_error());	
}
	echo'<td class="grey" align="left" width="400">Done</td>';

echo'</tr>
<tr>
	<td class="smheading" align="left">Recalculating Rankings:</td>';
	echo'<td class="grey" align="left">';
	$playerbanned = false;
	unset($s_debug);
	$q_pm = mysql_query(	"	SELECT 	p.id, 
												p.matchid, 
												p.pid, 
												p.gid,
												m.gamename
									FROM 		uts_player p, 
												uts_pinfo pi,
												uts_match m
									WHERE 	pi.id = p.pid 
										AND 	pi.banned <> 'Y' 
										AND	m.id = p.matchid
									ORDER BY p.matchid ASC, 
												p.playerid ASC");
	$i = 0;
	while ($r_pm = mysql_fetch_array($q_pm)) {
		$i++;
		if ($i%50 == 0) {
			echo $i.'. ';
		}
		ob_start();
		$playerecordid = $r_pm['id'];
		$matchid = $r_pm['matchid'];
		$pid = $r_pm['pid'];
		$gid = $r_pm['gid'];
		$gamename = $r_pm['gamename'];
// brajan 2006-09-08
// skip to another player if current already has a rank when RESUME mode is enabled
// otherwise calculate it
		if($results['reset'] == 'No'){ 
			$r_rankp = small_query("SELECT id FROM uts_rank WHERE pid = '$pid' AND gid = '$gid'");
				if( @mysql_num_rows($r_rankp) > 0){ continue;	}
		}
	include('import/import_ranking.php');
	ob_end_flush();
	flush();	
	ob_flush();
	}
	echo 'Done</td>
</tr>';
if (isset($s_debug)) {
echo '
<tr>
	<td class="smheading">Debug output:</td><td class="grey"><pre>'.$s_debug.'</pre></td>
</tr>';
}
echo '
<tr>
	<td class="smheading" align="center" colspan="2">Rankings recalculated - <a href="./admin.php?key='.$_REQUEST[key].'">Go Back To Admin Page</a></td>
</tr></table>';

?>
