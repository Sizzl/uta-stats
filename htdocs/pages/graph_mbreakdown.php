<?php 
$max_height = 100;

// Hourly Breakdown
$sql_ghours = "SELECT HOUR(time) AS res_hour, COUNT(*) AS res_count
FROM uts_match WHERE $bgwhere GROUP by res_hour";
$q_ghours = mysql_query($sql_ghours) or die(mysql_error());
$hour_max = 0;
$hour_sum = 0;
while ($r_ghours = mysql_fetch_array($q_ghours)) {
		$gb_hour[$r_ghours['res_hour']] = $r_ghours['res_count'];
		if ($r_ghours['res_count'] > $hour_max) $hour_max = $r_ghours['res_count'];
		$hour_sum += $r_ghours['res_count'];
}
if ($hour_max == 0) return;

// Monthly Breakdown
$sql_gmonths = "SELECT MONTH(time) AS res_month, COUNT(*) AS res_count
FROM uts_match WHERE $bgwhere GROUP by res_month";
$q_gmonths = mysql_query($sql_gmonths) or die(mysql_error());
$month_max = 0;
$month_sum = 0;
while ($r_gmonths = mysql_fetch_array($q_gmonths)) {
		$gb_month[$r_gmonths['res_month']] = $r_gmonths['res_count'];
		if ($r_gmonths['res_count'] > $month_max) $month_max = $r_gmonths['res_count'];
		$month_sum += $r_gmonths['res_count'];
}

// Yearly Breakdown
$sql_gyears = "SELECT YEAR(time) AS res_year, COUNT(DISTINCT matchcode) AS res_count
FROM uts_match WHERE $bgwhere GROUP by res_year ORDER BY res_year";
$q_gyears = mysql_query($sql_gyears) or die(mysql_error());
$year_max = 0;
$year_sum = 0;
$year_first = 0;
$year_last = 0;
while ($r_gyears = mysql_fetch_array($q_gyears)) {
	$year_last = $r_gyears['res_year'];
	if ($year_first == 0)
		$year_first = $year_last;
	$gb_year[$r_gyears['res_year']] = $r_gyears['res_count'];
	if ($r_gyears['res_count'] > $year_max)
		$year_max = $r_gyears['res_count'];
	$year_sum += $r_gyears['res_count'];
}

echo'<table border="0" cellpadding="0" cellspacing="0">
  <tbody>
  <tr>
    <td class="heading" align="center" colspan="39">Hourly and Monthly Activity '.($gtitle ?: '').'</td>
  </tr>
  <tr>
    <td class="dark" align="center" colspan="39" height="10"></td>
  </tr>
  <tr>
	<td class="dark" align="center" width="15"></td>';

// Hourly
for ($i = 0; $i <= 23; $i++) {
	if (!isset($gb_hour[$i])) $gb_hour[$i] = 0;
	$title = $gb_hour[$i] .' ('. get_dp($gb_hour[$i] / $hour_sum * 100) .' %)';
	echo '<td class="dark" align="center" valign="bottom" width="15"><img border="0" src="images/bars/v_bar'. ($i % 16 + 1) .'.png" width="18" height="'.(int)($gb_hour[$i] / $hour_max * $max_height).'" alt="'. $title .'" title="'. $title .'"></td>';
}

echo '<td class="dark" align="center" valign="bottom" width="15" width="10"></td>';

// Monthly
for ($i = 1; $i <= 12; $i++) {
	if (!isset($gb_month[$i])) $gb_month[$i] = 0;
	$title = $gb_month[$i] .' ('. get_dp($gb_month[$i] / $month_sum * 100) .' %)';
	echo '<td class="dark" align="center" valign="bottom" width="15"><img border="0" src="images/bars/v_bar'. (($i + 8) % 16 + 1) .'.png" width="18" height="'.(int)($gb_month[$i] / $month_max * $max_height).'" alt="'. $title .'" title="'. $title .'"></td>';
}
echo '<td class="dark" align="center" valign="bottom" width="15" width="10"></td>';
echo'</tr><tr>
	<td class="grey" align="center" width="15"></td>
	<td class="grey" align="center">0</td>
	<td class="grey" align="center">1</td>
	<td class="grey" align="center">2</td>
	<td class="grey" align="center">3</td>
	<td class="grey" align="center">4</td>
	<td class="grey" align="center">5</td>
	<td class="grey" align="center">6</td>
	<td class="grey" align="center">7</td>
	<td class="grey" align="center">8</td>
	<td class="grey" align="center">9</td>
	<td class="grey" align="center">10</td>
	<td class="grey" align="center">11</td>
	<td class="grey" align="center">12</td>
	<td class="grey" align="center">13</td>
	<td class="grey" align="center">14</td>
	<td class="grey" align="center">15</td>
	<td class="grey" align="center">16</td>
	<td class="grey" align="center">17</td>
	<td class="grey" align="center">18</td>
	<td class="grey" align="center">19</td>
	<td class="grey" align="center">20</td>
	<td class="grey" align="center">21</td>
	<td class="grey" align="center">22</td>
	<td class="grey" align="center">23</td>
	<td class="grey" align="center" width="10"></td>
	<td class="grey" align="center">J</td>
	<td class="grey" align="center">F</td>
	<td class="grey" align="center">M</td>
	<td class="grey" align="center">A</td>
	<td class="grey" align="center">M</td>
	<td class="grey" align="center">J</td>
	<td class="grey" align="center">J</td>
	<td class="grey" align="center">A</td>
	<td class="grey" align="center">S</td>
	<td class="grey" align="center">O</td>
	<td class="grey" align="center">N</td>
	<td class="grey" align="center">D</td>
	<td class="grey" align="center" width="15"></td>
</tr>
</tbody></table>
<br>';

$total_years = intval(($year_last)-($year_first));
echo'<table border="0" cellpadding="0" cellspacing="0">
  <tbody>
  <tr>
    <td class="heading" align="center" colspan="'.intval(3+$total_years).'">Yearly Activity '.($gtitle ?: '').'</td>
  </tr>
  <tr>
    <td class="dark" align="center" colspan="'.intval(3+$total_years).'" height="10"></td>
  </tr>
  <tr>
	<td class="dark" align="center" width="15"></td>';
// Yearly
for ($i = $year_first; $i <= $year_last; $i++) {
	if (!isset($gb_year[$i])) $gb_year[$i] = 0;
	$title = $gb_year[$i] .' ('. get_dp($gb_year[$i] / $year_sum * 100) .' %)';
	echo '<td class="dark" align="center" valign="bottom" width="25"><img border="0" src="images/bars/v_bar'. (($i + 8) % 16 + 1) .'.png" width="30" height="'.(int)($gb_year[$i] / $year_max * $max_height).'" alt="'. $title .'" title="'. $title .'"></td>';
}
echo '
	<td class="dark" align="center" width="15"></td>
   </tr>
   <tr>
	<td class="grey" align="center" width="25"></td>';
for ($i = $year_first; $i <= $year_last; $i++) {
	echo '
	<td class="grey" align="center">'.$i.'</td>
';
}
echo'
	<td class="grey" align="center" width="15"></td>
  </tr>
 </tbody>
</table><br />';


?>
