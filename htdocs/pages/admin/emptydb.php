<?php 
if (empty($import_adminkey) or isset($_REQUEST['import_adminkey']) or $import_adminkey != $adminkey) die('bla');

$options['title'] = 'Empty the Database';
$i = 0;
$options['vars'][$i]['name'] = 'sure';
$options['vars'][$i]['type'] = 'static';
$options['vars'][$i]['options'] = 'No|Yes';
$options['vars'][$i]['exitif'] = 'No';
$options['vars'][$i]['prompt'] = 'Are you sure (all data will be lost)?';
$options['vars'][$i]['caption'] = 'Sure:';
$i++;
$options['vars'][$i]['name'] = 'really';
$options['vars'][$i]['type'] = 'static';
$options['vars'][$i]['options'] = 'No|Yes';
$options['vars'][$i]['exitif'] = 'No';
$options['vars'][$i]['prompt'] = 'Are you really sure (this can NOT be undone)?';
$options['vars'][$i]['caption'] = 'Really sure (last warning):';
$i++;

$results = adminselect($options);


IF ($results['sure'] == "Yes" and $results['really'] == "Yes") {
	echo'<br><table border="0" cellpadding="1" cellspacing="2" width="600">
	<tr>
		<td class="smheading" align="center" colspan="2">Empty Database</td>
	</tr>
	<tr>
		<td class="smheading" align="left" width="300">Emptying All Tables but uts_ip2country and uts_weapons</td>';
	mysql_query("TRUNCATE uts_games;") or die(mysql_error());
	mysql_query("TRUNCATE uts_gamestype;") or die(mysql_error());
	mysql_query("TRUNCATE uts_killsmatrix;") or die(mysql_error());
	mysql_query("TRUNCATE uts_match;") or die(mysql_error());
	mysql_query("TRUNCATE uts_pinfo;") or die(mysql_error());
	mysql_query("TRUNCATE uts_player;") or die(mysql_error());
	mysql_query("TRUNCATE uts_rank;") or die(mysql_error());
	mysql_query("DELETE FROM uts_weapons WHERE id > 19") or die(mysql_error());
	mysql_query("TRUNCATE uts_weaponstats;") or die(mysql_error());
		
	// CRATOS
	mysql_query("TRUNCATE uts_smartass_objstats;") or die(mysql_error());
	// mysql_query("TRUNCATE uts_smartass_objs;") or die(mysql_error()); NO! Dont delete this one!
	
	mysql_query("ALTER TABLE uts_weapons AUTO_INCREMENT=20") or die(mysql_error());
	
		
	
		echo'<td class="grey" align="left" width="300">Done</td>
	</tr>
	<tr>
		<td class="smheading" align="center" colspan="2">Database Emptied - <a href="./admin.php?key='.$_REQUEST['key'].'">Go Back To Admin Page</a></td>
	</tr></table>';
} else {
	echo'<br><table border="0" cellpadding="1" cellspacing="2" width="600">
	<tr>
		<td class="smheading" align="center" colspan="2">Empty Database</td>
	</tr>
	<tr>
		<td class="smheading" align="left" width="300">Database Not Emptied</td>
		<td class="grey" align="left" width="300">Answer Was No</td>
	</tr>
	<tr>
		<td class="smheading" align="center" colspan="2">Database Not Emptied - <a href="./admin.php?key='.$_REQUEST['key'].'">Go Back To Admin Page</a></td>
	</tr></table>';
}


?>
