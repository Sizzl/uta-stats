<?php 
if (empty($import_adminkey) or isset($_REQUEST['import_adminkey']) or $import_adminkey != $adminkey) die('bla');

// Graph width
$max_width = 150;
function nf($number) {
	return(number_format($number));
}


echo'<table border="0" cellpadding="1" cellspacing="2" width="600">
<tr>
	<td class="smheading" align="center" height="25" colspan="4">Database Statistics</td>
</tr>';

$q_dbsize = mysql_query("SHOW table STATUS") or die(mysql_error());
$tot_size = 0;
$tot_rows = 0;
$max_size = 0;
while ($r_dbsize = mysql_fetch_array($q_dbsize)) {
	if (substr($r_dbsize['Name'], 0, 3) != 'uts') continue;
	$size = $r_dbsize['Data_length'] + $r_dbsize['Index_length'];
	$rows = $r_dbsize['Rows'];
	$tables[] = array	(
							'name'		=>	$r_dbsize['Name'],
							'size'		=>	$size,
							'rows'		=> $rows
							);
	$tot_size += $size;
	$tot_rows += $rows;
	if ($max_size < $size) $max_size = $size;

}

$i = 0;
foreach($tables as $table) {
	$i++;
	$class = ($i%2) ? 'grey' : 'grey2';

	$d_size = file_size_info($table['size']);
	$title = get_dp($table['size'] / $tot_size * 100) .' %';
	echo'<tr>
		<td class="smheading" align="left" width="200">'.$table['name'].'</td>
		<td class="'.$class.'" align="right">'.nf($table['rows']).' rows</td>
		<td class="'.$class.'" align="right">'.$d_size['size'] .' '. $d_size['type'].'</td>
		<td class="'.$class.'" width="'.($max_width + 5).'"><img border="0" src="images/bars/h_bar'. ($i % 16 + 1) .'.png" height="10" width="'.(int)($table['size'] / $max_size * $max_width).'" alt="'. $title .'" title="'. $title .'"></td>
	</tr>';
}

$d_size = file_size_info($tot_size);
echo'<tr>
	<td class="smheading" align="left" width="200">Total Database Size</td>
	<td class="darkgrey" align="right">'.nf($tot_rows).' rows</td>
	<td class="darkgrey" align="right">'.$d_size['size'] .' '. $d_size['type'].'</td>
	<td class="darkgrey" >&nbsp;</td>
</tr>
</table><br>';

echo'<table border="0" cellpadding="1" cellspacing="2" width="600">
	<tr><td width="100%">';

echo '
	<ul>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=mplayers&list=hide">Merge Players</a> &nbsp; (<a href="admin.php?key='. urlencode($adminkey) .'&amp;action=mplayers">Show player list</a>)</li>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=mservers">Merge Servers</a></li>';
		echo '<br>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=dmatch">Delete Match</a></li>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=dpmatch">Delete Player From Match</a></li>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=dplayer&list=hide">Delete Player</a> &nbsp; (<a href="admin.php?key='. urlencode($adminkey) .'&amp;action=dplayer">Show player list</a>)</li>';
		echo '<br>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=pban&amp;saction=ban&list=hide">Ban Player</a> &nbsp; (<a href="admin.php?key='. urlencode($adminkey) .'&amp;action=pban&amp;saction=ban">Show player list</a>)</li>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=pban&amp;saction=unban">Unban Player</a></li>';
		echo '<br>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=pinfo&list=hide">Extended Player Info</a> &nbsp; (<a href="admin.php?key='. urlencode($adminkey) .'&amp;action=pinfo">Show player list</a>)</li>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=ipsearch">Search IP</a></li>';
		echo '<br>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=uta_pautomerge">Suggest auto-merge of duplicate players</a></li>';
		if ($import_utdc_download_enable) {
			echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=utdclog">View UTDC logs</a></li>';
		}
		echo '<br>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=editweapons">Edit Weapons</a></li>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=editgames">Add/Edit Game Names</a></li>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=editgamestype">Add/Edit Game Types</a></li>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=recalcranking">Recalculate Rankings</a> &nbsp; (<a href="admin.php?key='. urlencode($adminkey) .'&amp;action=recalcranking&amp;piddebug=true">Player debugging</a>)</li>';
		echo '<br>';
		echo '<li><a href="admin.php?key='. urlencode($adminkey) .'&amp;action=emptydb">Empty the database</a></li>';
		echo '<li><a href="./import.php?'.str_rand().'='.str_rand().'">Import Logs</a></li>';
echo '
</ul>
';



echo'</td></tr></table>';
?>
