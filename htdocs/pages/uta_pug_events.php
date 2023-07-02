<?php 
// echo '<div style="width: 720px; color:red; font-weight:bold" class="heading">EXPERIMENTAL</div><br><br> -->';
global $t_match, $t_pinfo, $t_player, $t_games, $t_match, $t_pickups, $t_pickupstats, $dbversion, $htmlcp; // fetch table globals.
error_reporting(E_ALL^E_NOTICE);

// include ("includes/uta_functions.php");
$rowsperpage = 20;
if (isset($_REQUEST["page"]))
{
	$cpage = $_REQUEST["page"];
	if($cpage == "" || is_numeric($cpage)===FALSE)
		$cpage = "0";
}
else
{
	$cpage = "0";
}

if (isset($_REQUEST["p"]) && $_REQUEST["p"] == "utapugeventhalloween")
{
	$pickup = "Lantern";
	$hunttitle = "Halloween Hunting";
}
else
{
	$pickup = "Easter Egg";
	$hunttitle = "Easter Eggstravaganza Event: The Hunt";
}
// Firstly we need to work out First Last Next Prev pages
$where = ' ';
$year = !empty($_REQUEST['year']) ? my_addslashes(sprintf("%04d", $_REQUEST['year'])) : date("Y");
$gid  = !empty($_REQUEST['gid']) ?  my_addslashes($_REQUEST['gid']) : 0;

echo "
<style>
.spoiler { transition: color 0.5s; position: relative } 
.spoiler:not(:hover), .spoiler:not(:hover) * { color: transparent }
.spoiler * { transition: color 0.5s, background 0.5s }
.spoiler:not(:hover) * { background: transparent }
.spoiler::after {
    position: absolute;
    top: 0; left: 0; right: 0;
    margin-left: auto;
    margin-right: auto;
    content: '< Hover to reveal spoiler >';
    color: transparent;
}
.spoiler:not(:hover)::after {
    color: #cccc99;
    transition: color 0.3s 0.3s;
}
</style>
";

if (!empty($year) and empty($month) and empty($day)) $where .= " AND m.time LIKE '$year%'";
if (!empty($gid)) $where .= " AND m.gid = '$gid'";
 
$sql = "SELECT 
		DISTINCT CONCAT(REPLACE(`".(isset($t_match) ? $t_match : "uts_match")."`.`mapname`,'AS-',''),';', `".(isset($t_pickups) ? $t_pickups : "uts_pickups")."`.`name`),
		REPLACE(`".(isset($t_match) ? $t_match : "uts_match")."`.`mapname`,'AS-','') AS `mapdisplay`,
		SUBSTRING_INDEX(SUBSTRING_INDEX(`uts_pickups`.`name`,\"/\",1),\"(\",1) AS pickuptype,
		CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`uts_pickups`.`name`,\"/\",1),\"(\",-1) AS unsigned) AS pickuporder,
		`".(isset($t_match) ? $t_match : "uts_match")."`.`mapname` AS `mapname`,
		`".(isset($t_pickups) ? $t_pickups : "uts_pickups")."`.`name`, `".(isset($t_pickupstats) ? $t_pickupstats : "uts_pickupstats")."`.`pickup`

	FROM `".(isset($t_pickupstats) ? $t_pickupstats : "uts_pickupstats")."`
	INNER JOIN `".(isset($t_pickups) ? $t_pickups : "uts_pickups")."` ON (`".(isset($t_pickups) ? $t_pickups : "uts_pickups")."`.`id` = `".(isset($t_pickupstats) ? $t_pickupstats : "uts_pickupstats")."`.`pickup`)
	INNER JOIN `".(isset($t_match) ? $t_match : "uts_match")."` ON (`".(isset($t_match) ? $t_match : "uts_match")."`.`id` = `".(isset($t_pickupstats) ? $t_pickupstats : "uts_pickupstats")."`.`matchid`)
	WHERE `".(isset($t_pickups) ? $t_pickups : "uts_pickups")."`.`name` LIKE '%".$pickup."%' AND year = '".$year."'
	ORDER BY `mapdisplay`, `pickuptype`, `pickuporder` ASC";

$started = microtime(true);
$r_pucount = mysql_query($sql);
while($count_row = mysql_fetch_assoc($r_pucount))
{
	$pucount[] = $count_row['mapname'];
	$puids[] = $count_row['pickup'];
}
echo "<!-- SQL [".number_format(microtime(true)-$started,4)."] - ".$sql." -->\n";

$mcount = count($pucount);
$ecount = $mcount/$rowsperpage;
$ecount2 = number_format($ecount, 0, '.', '');

if($ecount > $ecount2)
{
	$ecount2 = $ecount2+1;
}
$fpage = 0;
if($ecount < 1)
{
	$lpage = 0;
}
else
{
	$lpage = $ecount2; // without -1 due to first page being different
}
$qpage = ($cpage - 1)*$rowsperpage;
$tfpage = $cpage + 1;
$tlpage = $lpage + 1;
$ppage = $cpage - 1;
$vpage = my_addslashes($_REQUEST['p']);

$ppageurl = "<a class=\"pages\" href=\"./?p=".$vpage."&amp;year=$year&amp;page=".$ppage."\">[Previous]</a>";
if($ppage < "0") { $ppageurl = "[Previous]"; }

$npage = $cpage+1;
$npageurl = "<a class=\"pages\" href=\"./?p=".$vpage."&amp;year=$year&amp;page=".$npage."\">[Next]</a>";
if($npage > "$ecount") { $npageurl = "[Next]"; }

$fpageurl = "<a class=\"pages\" href=\"./?p=".$vpage."&amp;year=$year&amp;page=".$fpage."\">[First]</a>";
if($cpage == "0") { $fpageurl = "[First]"; }

$lpageurl = "<a class=\"pages\" href=\"./?p=".$vpage."&amp;year=$year&amp;page=".$lpage."\">[Last]</a>";
if($cpage == "$lpage") { $lpageurl = "[Last]"; }

echo '<div style="width: 720px;" class="heading">'.$hunttitle.' '.$year;
if (date("Y") > 2021) // started in 2021
{
	echo '<br /><br /><form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
	echo '<input type="hidden" name="p" value="'.$vpage.'">';
	echo '<table width="210" class="searchform" border="0" cellpadding="1" cellspacing="1">';
	echo '<tr><td width="70"><strong>Filter:</strong></td>';
	echo '<td width="70"><select class="searchform" name="year">';
	echo '<option value="0"> </option>';
	for($i = date('Y');$i >= date("Y") - (date("Y")-2021); $i--) {
		$selected = ($year == $i) ? 'selected' : '';
		echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
	}
	echo '</select></td>';
	echo '<td width="40"><input class="searchform" type="Submit" name="filter" value="Apply"></td>';
	echo'</tr></table></form>';
}
echo'</div><br /><br />
<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div><br />';

if ($cpage > 0)
{
	$sql_recent = $sql." LIMIT ".$qpage.",".$rowsperpage.";";
	$started = microtime(true);
	$q_recent = mysql_query($sql_recent) or die(mysql_error());
	echo "<!-- SQL_RECENT [".number_format(microtime(true)-$started,4)."] - ".$sql_recent." -->\n";

	while ($r_recent = mysql_fetch_array($q_recent))
	{
		$r_mapname = un_ut($r_recent[mapname]);
		$r_mapfile = un_ut($r_recent[mapdisplay]);
		$r_pickup = $r_recent[name];
		$r_pickupid = $r_recent[pickup];

		if (isset($lastmap) && $lastmap != $r_mapfile || !isset($lastmap))
		{
			if (isset($lastmap) && strlen($lastmap) > 0)
			{
				echo '</tbody></table><br />';
			}
			echo '<table width="720" class="box" border="0" cellpadding="3" cellspacing="1">
	<tbody>
		<tr>
			<td class="heading" colspan="7" align="center"><blockquote class="spoiler">'.$r_mapfile.'</blockquote></td>
		</tr>
		<tr>
			<td class="smheading" align="center" width="200">Special Item</td>
			<td nowrap class="smheading" align="center">Successful Hunters</td>
		</tr>';

		$lastmap = $r_mapfile;
		}
		echo'
	  <tr>
		<td nowrap class="dark" align="center">'.$r_pickup.'</td>
		<td class="grey" align="center">';
		$sql_pu = "SELECT DISTINCT
			`uts_pinfo`.`name`, `uts_pickupstats`.`pid`
			FROM `uts_pickupstats`
			INNER JOIN `uts_match` ON (`uts_match`.`id` = `uts_pickupstats`.`matchid`)
			INNER JOIN `uts_pinfo` ON (`uts_pinfo`.`id` = `uts_pickupstats`.`pid`)
			WHERE `uts_pickupstats`.`pickup` = '".$r_pickupid."' AND `uts_match`.`mapname` = '".$r_mapname."'
			ORDER BY `name`;";
		$started = microtime(true);
		$q_hunters = mysql_query($sql_pu) or die(mysql_error());
		echo "<!-- SQL_PU [".number_format(microtime(true)-$started,4)."] - ".$sql_pu." -->\n";
		while ($r_hunters = mysql_fetch_array($q_hunters))
		{
			$playername = $r_hunters[name];
			echo htmlentities($playername,ENT_SUBSTITUTE,$htmlcp)."; ";
		}
		echo '</td>
	  </tr>';

	}
	if (isset($lastmap))
	{
		echo '</tbody></table><br />';
	}
}
else
{
	echo '<table width="420" class="box" border="0" cellpadding="3" cellspacing="1">
	<tbody>
		<tr>
			<td class="heading" colspan="7" align="center">Hunter Leaderboard</td>
		</tr>
		<tr>
			<td class="smheading" align="center">Player Name</td>
			<td nowrap class="smheading" align="center" width="200">Items Found</td>
		</tr>';
	$leaders = [];
	$pinfo = [];
	if (isset($dbversion) && floatval($dbversion) > 5.6)
	{
		$sql_leaders = "SELECT DISTINCT(`uts_pinfo`.`name`) AS playername,ANY_VALUE(`uts_pinfo`.`country`) AS `country`, COUNT(DISTINCT(CONCAT(`uts_pickups`.`name`,`uts_match`.`mapname`))) AS pucount,
			                ANY_VALUE(`uts_pickupstats`.`pid`) AS `pid`
				FROM `uts_pickupstats`
				INNER JOIN `uts_pickups` ON (`uts_pickups`.`id` = `uts_pickupstats`.`pickup`)
				INNER JOIN `uts_match` ON (`uts_match`.`id` = `uts_pickupstats`.`matchid`)
				INNER JOIN `uts_pinfo` ON (`uts_pinfo`.`id` = `uts_pickupstats`.`pid`)
				WHERE `pickup` IN (".join(',',array_unique($puids)).") AND year = '".$year."'
				GROUP BY `uts_pinfo`.`name`;";
	}
	else
	{
		$sql_leaders = "SELECT DISTINCT(`uts_pinfo`.`name`) AS playername,`uts_pinfo`.`country`, COUNT(DISTINCT(CONCAT(`uts_pickups`.`name`,`uts_match`.`mapname`))) AS pucount,
			                `uts_pickupstats`.`pid`
				FROM `uts_pickupstats`
				INNER JOIN `uts_pickups` ON (`uts_pickups`.`id` = `uts_pickupstats`.`pickup`)
				INNER JOIN `uts_match` ON (`uts_match`.`id` = `uts_pickupstats`.`matchid`)
				INNER JOIN `uts_pinfo` ON (`uts_pinfo`.`id` = `uts_pickupstats`.`pid`)
				WHERE `pickup` IN (".join(',',array_unique($puids)).") AND year = '".$year."'
				GROUP BY `uts_pinfo`.`name`;";

	}
	$started = microtime(true);
	$q_leaders = mysql_query($sql_leaders) or die(mysql_error());
	// echo "<!-- SQL_Leaders [".number_format(microtime(true)-$started,4)."] - ".$sql_leaders." -->\n";
	while ($r_leaders = mysql_fetch_array($q_leaders))
	{
		if (array_key_exists($r_leaders['pid'],$leaders))
		{
			$leaders[$r_leaders['pid']] += $r_leaders['pucount'];
		}
		else
		{
			$leaders[$r_leaders['pid']] = $r_leaders['pucount'];
			$pinfo[$r_leaders['pid']]['name'] = $r_leaders['playername'];
			$pinfo[$r_leaders['pid']]['country'] = $r_leaders['country'];
		}
	}
	arsort($leaders);
	foreach ($leaders as $pid => $score)
	{
		echo '	  <tr>
		<td nowrap class="dark" align="center">
		<img src="images/flags/'.$pinfo[$pid]['country'].'.png" width="15" height="10" style="border:0;" alt="'.$pinfo[$pid]['country'].'" />
		<a class="darkhuman" href="?p=pinfo&pid='.$pid.'&year='.$year.'">'.htmlentities($pinfo[$pid]['name'],ENT_SUBSTITUTE,$htmlcp).'</a></td>
		<td class="grey" align="center">'.$score.'</td>';	
	}
	echo '</tbody></table><br />';
}
echo'<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>';
?>
