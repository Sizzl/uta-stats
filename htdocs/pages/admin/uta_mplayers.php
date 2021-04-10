<?php 	// Cratos: Merge Players


// Update Objective stats Tables
echo '
<tr>
	<td class="smheading" align="left" width="200">Updating Objective stats</td>';
mysql_query("UPDATE `uts_smartass_objstats` SET `pid` = '$mplayer1' WHERE `pid` = '$mplayer2'") or die(mysql_error());
mysql_query("DELETE FROM `uts_smartass_objstats` WHERE `pid` = '$mplayer2'") or die(mysql_error());
	echo'<td class="grey" align="left" width="400">Done</td>
</tr>';

echo '
<tr>
	<td class="smheading" align="left" width="200">Updating Pickup stats</td>';
mysql_query("UPDATE `uts_pickupstats` SET `pid` = '$mplayer1' WHERE `pid` = '$mplayer2';") or die(mysql_error());
mysql_query("DELETE FROM `uts_pickupstats` WHERE `pid` = '$mplayer2'") or die(mysql_error());
	echo'<td class="grey" align="left" width="400">Done</td>
</tr>';

?>
