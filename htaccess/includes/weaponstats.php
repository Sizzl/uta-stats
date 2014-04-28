<?
function weaponstats($_mid, $_pid, $title = 'Weapons Summary') {
	global $gamename, $gid;

	$sql_weapons = "SELECT	w.matchid,
									w.pid AS playerid,
									w.weapon,
									w.kills,
									w.shots,
									w.hits,
									w.damage,
									w.acc,
									pi.name AS playername,
									pi.country AS country,
									pi.banned AS banned,
									wn.id AS weaponid,
									wn.name AS weaponname,
									wn.image AS weaponimg,
									wn.sequence AS sequence
						FROM
						".(isset($t_weapons) ? $t_weapons : "uts_weapons")." AS wn,
						".(isset($t_weaponstats) ? $t_weaponstats : "uts_weaponstats")." AS w
						LEFT JOIN (".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." AS pi)
							ON		w.pid = pi.id
						WHERE		w.matchid = '$_mid'
							AND	w.pid = '$_pid'
							AND	(wn.id = w.weapon)
							AND wn.hide <> 'Y'";


	if ($_pid == 0 and $_mid != 0) {
		$sql_weapons = "SELECT	w.matchid,
										w.pid AS playerid,
										w.weapon,
										SUM(w.kills) AS kills,
										SUM(w.shots) AS shots,
										SUM(w.hits)  AS hits,
										SUM(w.damage) AS damage,
										AVG(w.acc) AS acc,
										pi.name AS playername,
										pi.country AS country,
										pi.banned AS banned,
										wn.id AS weaponid,
										wn.name AS weaponname,
										wn.image AS weaponimg,
										wn.sequence AS sequence,
										wn.hide AS hideweapon
								FROM
								".(isset($t_weapons) ? $t_weapons : "uts_weapons")." AS wn,
								".(isset($t_weaponstats) ? $t_weaponstats : "uts_weaponstats")." AS w
								LEFT JOIN (".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." AS pi)
								ON		w.pid = pi.id
							WHERE		w.matchid = '$_mid'
								AND	(wn.id = w.weapon)
								AND wn.hide <> 'Y'
							GROUP BY	w.pid,
										w.weapon";
	}

	$q_weapons = mysql_query($sql_weapons) or die(mysql_error()."\r\n<pre>".$sql_weapons."</pre>\r\n");
	while ($r_weapons = zero_out(mysql_fetch_array($q_weapons))) {
		$weaponid = intval($r_weapons['weaponid']);
		$playerid = intval($r_weapons['playerid']);
		// Don't include banned players
		if ($r_weapons['banned'] != 'Y') $psort[$playerid] = strtolower($r_weapons['playername']);

		if ($r_weapons['damage'] > 1000000) $r_weapons['damage'] = round($r_weapons['damage'] / 1000, 0) .'K';
//		if ($r_weapons['damage'] > 1000) $r_weapons['damage'] = round($r_weapons['damage'] / 1000, 0) .'K';

		$wd[$playerid]['playername']			=	$r_weapons['playername'];
		$wd[$playerid]['country']				=	$r_weapons['country'];
		$wd[$playerid]['banned']				=	$r_weapons['banned'];
		$wd[$playerid][$weaponid]['kills']	=	$r_weapons['kills'];
		$wd[$playerid][$weaponid]['shots']	=	$r_weapons['shots'];
		$wd[$playerid][$weaponid]['hits']	=	$r_weapons['hits'];
		$wd[$playerid][$weaponid]['damage']	=	$r_weapons['damage'];
		$wd[$playerid][$weaponid]['acc']		=	((!empty($r_weapons['acc'])) ? get_dp($r_weapons['acc']) : '');

		if (!isset($wsort[$weaponid]) and $r_weapons['hideweapon'] != 'Y') {
			$wsort[$weaponid] = intval($r_weapons['sequence']);
			$weapons[$weaponid]['name'] 		= $r_weapons['weaponname'];
			$weapons[$weaponid]['image']		= $r_weapons['weaponimg'];
			$weapons[$weaponid]['sequence']	= $r_weapons['sequence'];
		}
	}
	if (!isset($psort)) return;

	asort($psort);
	asort($wsort);

	$playercol = 1;
	if (count($wsort) < 3) {
		$one = true;
		$colspan = 5;
		if (count($psort) == 1) {
			$playercol = 0;
		}
	} else {
		$one = false;
		$colspan = 1;
	}

	echo'
	<table border="0" cellpadding="0" cellspacing="2">
	<tbody>
	<tr>
		<td class="heading" colspan="'. ((count($wsort) * $colspan) + $playercol) .'" align="center">'.htmlentities($title).'</td>
	</tr>';


	if ($one) {
		ws_header($wsort, $weapons, $colspan, $one, $playercol);
		echo '<tr>';
		foreach($wsort as $wid => $bar) {
			for ($i = 1; $i <= $colspan; $i++) {
				switch($i) {
					case 1: $extra = 'Kills'; break;
					case 2: $extra = 'Shots'; break;
					case 3: $extra = 'Hits'; break;
					case 4: $extra = 'Acc'; break;
					case 5: $extra = 'Dmg'; break;
				}
				$extra = '<span style="font-size: 100%">'. $extra .'</span>';
				echo '<td class="smheading" align="center" width="35">'.$extra.'</td>';
			}
		}
		echo '</tr>';

		$i = 0;
		foreach($psort as $pid => $foo) {
			$i++;
			echo '<tr>';
			if ($playercol) echo '<td nowrap class="darkhuman" align="left"><a class="darkhuman" href="./?p=matchp&amp;mid='.$_mid.'&amp;pid='.urlencode($pid).'">'.FormatPlayerName($wd[$pid]['country'], $pid,  $wd[$pid]['playername'], $gid, $gamename).'</a></td>';
			foreach($wsort as $wid => $bar) {
				ws_cell($wd, $pid, $wid, 'kills', $i);
				ws_cell($wd, $pid, $wid, 'shots', $i);
				ws_cell($wd, $pid, $wid, 'hits', $i);
				ws_cell($wd, $pid, $wid, 'acc', $i);
				ws_cell($wd, $pid, $wid, 'damage', $i);
			}
			echo '</tr>';
		}
	}

	if (!$one) {
		ws_block($wd, $weapons, $wsort, $psort, $colspan, $playercol, $one, $_mid, $gamename, 'Kills', 'kills');
		ws_block($wd, $weapons, $wsort, $psort, $colspan, $playercol, $one, $_mid, $gamename, 'Shots', 'shots');
		ws_block($wd, $weapons, $wsort, $psort, $colspan, $playercol, $one, $_mid, $gamename, 'Hits', 'hits');
		ws_block($wd, $weapons, $wsort, $psort, $colspan, $playercol, $one, $_mid, $gamename, 'Damage', 'damage');
		ws_block($wd, $weapons, $wsort, $psort, $colspan, $playercol, $one, $_mid, $gamename, 'Accuracy', 'acc');
	}


	echo '</tbody></table>';
}

function ws_header(&$wsort, &$weapons, $colspan, $one, $playercol) {
	global $themeimage;
	echo '<tr>';
	if ($playercol and $playercol != -1) echo '<td class="smheading" align="center" width="150" '.(($one) ? 'rowspan="2"' : '') .'>Player</td>';
	if ($playercol == -1) echo '<td class="smheading" align="center" width="150">&nbsp;</td>';
	foreach($wsort as $wid => $bar) {
		if (!empty($weapons[$wid]['image'])) {
			// Added $themeimage for custom weapon images --// Timo: 20/07/05
			if ($themeimage)
				$content = '<img border="0" src="'.$themeimage.'images/weapons/'.$weapons[$wid]['image'].'" alt="'.$weapons[$wid]['name'].'" title="'.$weapons[$wid]['name'].'">';
			else
				$content = '<img border="0" src="images/weapons/'.$weapons[$wid]['image'].'" alt="'.$weapons[$wid]['name'].'" title="'.$weapons[$wid]['name'].'">';
		} else {
			$content = '<span style="font-size: 60%;">'.$weapons[$wid]['name'].'</span>';
		}
		echo '<td class="smheading" align="center" '. (($one) ? 'colspan="'.$colspan.'"' : 'width="35"') .'>'.$content.'</td>';

	}
	echo '</tr>';
}


function ws_cell(&$wd, $pid, $wid, $field, $i) {
	$content = '';
	if (isset($wd[$pid][$wid][$field])) $content = $wd[$pid][$wid][$field];
	$class = ($i % 2) ? 'grey' : 'grey2';
	echo '<td class="'.$class.'" align="center">'.$content.'</td>';
}




function ws_block(&$wd, &$weapons, &$wsort, &$psort, &$colspan, $playercol, $one,$_mid, $gamename, $caption, $field) {
	global $gamename, $gid;
	if (count($psort) != 1) {
		echo '
		<tr>
			<td height="5" colspan="'. ((count($wsort) * $colspan) + $playercol) .'" align="center"></td>
		</tr>
		<tr>
			<td class="smheading" height="20" colspan="'. ((count($wsort) * $colspan) + $playercol) .'"  align="center">'.$caption.'</td>
		</tr>';
		ws_header($wsort, $weapons, $colspan, $one, $playercol);
	}
	if (count($psort) == 1) {
		$playercol = -1;
		if ($field == 'kills') ws_header($wsort, $weapons, $colspan, $one, $playercol);
	}

	$i = 0;
	foreach($psort as $pid => $foo) {
		$i++;
		echo '<tr>';
		if ($playercol and $playercol != -1) echo '<td nowrap class="darkhuman" align="left"><a class="darkhuman" href="./?p=matchp&amp;mid='.$_mid.'&amp;pid='.urlencode($pid).'">'.FormatPlayerName($wd[$pid]['country'], $pid, $wd[$pid]['playername'], $gid, $gamename).'</a></td>';
		if ($playercol == -1) echo '<td nowrap class="dark" align="center">'.$caption.'</a></td>';
		foreach($wsort as $wid => $bar) {
			ws_cell($wd, $pid, $wid, $field, $i);
		}
		echo '</tr>';
	}
}
?>
