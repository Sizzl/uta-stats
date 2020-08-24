<?php 

include_once ("includes/config.php");
// include_once ("includes/uta_functions.php");
global $t_match, $t_pinfo, $t_player, $t_games, $t_smartass_objs, $t_smartass_objstats; // fetch table globals.

function InvertSort($curr_field, $filter, $sort) {
	if ($curr_field != $filter) return(($curr_field == "mapfile") ? "ASC" : "DESC");
	if ($sort == 'ASC') return('DESC');
	return('ASC');
}

function SortPic($curr_field, $filter, $sort) {
	if ($curr_field != $filter) return;
	$fname = 'images/s_'. strtolower($sort) .'.png';
	if (!file_exists($fname)) return;
	return('&nbsp;<img src="'. $fname .'" border="0" width="11" height="9" alt="" title="('.strtolower($sort).'ending)">');
}

$mapsperpage=100;

// Get filter and set sorting
$filter = my_addslashes($_GET[filter]);
$sort = my_addslashes($_GET[sort]);

include_once("uta_recordzone_filters.php");

if (empty($filter)) {
	$filter = "mapfile";
}

if (empty($sort) or ($sort != 'ASC' and $sort != 'DESC')) $sort = ($filter == "mapfile") ? "ASC" : "DESC";


// Firstly we need to work out First Last Next Prev pages
$mcount = small_count("SELECT mapfile FROM ".(isset($t_match) ? $t_match : "uts_match")." GROUP BY mapfile HAVING mapfile like 'AS-%'");

$ecount = $mcount/$mapsperpage;
$ecount2 = number_format($ecount, 0, '.', '');

IF($ecount > $ecount2) {
	$ecount2 = $ecount2+1;
}

$fpage = 0;
IF($ecount < 1) { $lpage = 0; }
else { $lpage = $ecount2-1; }

$cpage = $_GET["page"];
IF ($cpage == "") { $cpage = "0"; }
$qpage = $cpage*$mapsperpage;

$tfpage = $cpage+1;
$tlpage = $lpage+1;

$ppage = $cpage-1;
$ppageurl = "<a class=\"pages\" href=\"./?p=utarecordzone&amp;type=$type&amp;game=$game&amp;filter=$filter&amp;sort=$sort&amp;page=$ppage\">[Previous]</a>";
IF ($ppage < "0") { $ppageurl = "[Previous]"; }

$npage = $cpage+1;
$npageurl = "<a class=\"pages\" href=\"./?p=utarecordzone&amp;type=$type&amp;game=$game&amp;filter=$filter&amp;sort=$sort&amp;page=$npage\">[Next]</a>";
IF ($npage >= "$ecount") { $npageurl = "[Next]"; }

$fpageurl = "<a class=\"pages\" href=\"./?p=utarecordzone&amp;type=$type&amp;game=$game&amp;filter=$filter&amp;sort=$sort&amp;page=$fpage\">[First]</a>";
IF ($cpage == "0") { $fpageurl = "[First]"; }

$lpageurl = "<a class=\"pages\" href=\"./?p=utarecordzone&amp;type=$type&amp;game=$game&amp;filter=$filter&amp;sort=$sort&amp;page=$lpage\">[Last]</a>";
IF ($cpage == "$lpage") { $lpageurl = "[Last]"; }

	uta_rz_FilterForm(); // Show Filter form

echo' <div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: 
'.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div><br /> <table 
class="box" border="0" cellpadding="2" cellspacing="1"> <tbody><tr> <td 
class="heading" colspan="6" align="center">UTA Assault Record Zone</td></tr><tr>
 
<td class="smheading" align="center" width="180"><a class="smheading"
href="./?p=utarecordzone&amp;filter=mapfile&amp;sort='.InvertSort('mapfile', $filter, 
$sort).'">Map Name</a>'.SortPic('mapfile', $filter, $sort).'</td>
 
<td class="smheading" align="center" width="50"><a class="smheading" 
href="./?p=utarecordzone&amp;filter=matchcount&amp;sort='.InvertSort('matchcount', 
$filter, $sort).'">Played</a>'.SortPic('matchcount', $filter, $sort).'</td>

<td class="smheading" align="center" width="60">Time</td>

<td class="smheading" align="center" width="150">Team / Player</td>

<td class="smheading" align="center" width="185">Match / Click for Details</td>

<td class="smheading" align="center" width="130">Server</td>';


// Get all maps
$sql_maps = "SELECT mapfile, COUNT(".(isset($t_match) ? $t_match : "uts_match").".id) AS matchcount
			FROM ".(isset($t_match) ? $t_match : "uts_match")." GROUP BY mapfile HAVING mapfile like 'AS-%'
			ORDER BY $filter $sort LIMIT $qpage,$mapsperpage";
					
$q_maps = mysql_query($sql_maps) or die(mysql_error());
while ($r_maps = mysql_fetch_array($q_maps)) {

	$r_mapfile = un_ut($r_maps[mapfile]);
	$r_mapfileunr = $r_mapfile .".unr";
	$myurl = urlencode($r_mapfile);
	$matchcount = $r_maps[matchcount]; 
	  
	// Get Record for this map
	$sql_record = "SELECT ".(isset($t_match) ? $t_match : "uts_match").".id as recmatchid, ostats.timestamp as rectime, ".(isset($t_match) ? $t_match : "uts_match").".servername as servername, 
			".(isset($t_match) ? $t_match : "uts_match").".teamname0, ".(isset($t_match) ? $t_match : "uts_match").".teamname1, ".(isset($t_match) ? $t_match : "uts_match").".ass_att AS attackingid, ".(isset($t_match) ? $t_match : "uts_match").".matchmode,
			pinfo.name as recplayername, pinfo.id as recplayerid, pinfo.country as recpcountry, 
			".(isset($t_match) ? $t_match : "uts_match").".time as matchtime, ".(isset($t_match) ? $t_match : "uts_match").".serverip as serverip
			from ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats")." ostats 
			inner join ".(isset($t_match) ? $t_match : "uts_match")." on ostats.matchid = ".(isset($t_match) ? $t_match : "uts_match").".id
			inner join ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." pinfo on pinfo.id = ostats.pid 
			where ostats.final = 1 AND ".(isset($t_match) ? $t_match : "uts_match").".mapfile like '$r_mapfileunr' AND att_teamsize <= (def_teamsize+1)";
			if ($record_condition_gametypes!="") $sql_record .= " $record_condition_gametypes ";
			if ($record_condition_teamsize!="") $sql_record .= " $record_condition_teamsize ";
			$sql_record .= " ORDER by ostats.timestamp ASC LIMIT 0,1";				    
	debugprint($sql_record,"SQL","117");
	$q_record = small_query($sql_record);
	$regular = 1;
	if ($q_record==NULL)
	{
		echo "<!-- Debug: PriSQL- ".$sql_record." -->\r\n\r\n";
		// No record found. Trying without teamsize limit
		$sql_record = "SELECT ".(isset($t_match) ? $t_match : "uts_match").".id as recmatchid, ostats.timestamp as rectime, ".(isset($t_match) ? $t_match : "uts_match").".servername as servername, 
			".(isset($t_match) ? $t_match : "uts_match").".teamname0, ".(isset($t_match) ? $t_match : "uts_match").".teamname1, ".(isset($t_match) ? $t_match : "uts_match").".ass_att AS attackingid, ".(isset($t_match) ? $t_match : "uts_match").".matchmode,
			pinfo.name as recplayername, pinfo.id as recplayerid, pinfo.country as recpcountry, 
			".(isset($t_match) ? $t_match : "uts_match").".time as matchtime, ".(isset($t_match) ? $t_match : "uts_match").".serverip as serverip
			from ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats")." ostats 
			inner join ".(isset($t_match) ? $t_match : "uts_match")." on ostats.matchid = ".(isset($t_match) ? $t_match : "uts_match").".id
			inner join ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." pinfo on pinfo.id = ostats.pid 
			where ostats.final = 1 AND ".(isset($t_match) ? $t_match : "uts_match").".mapfile like '$r_mapfileunr' ";
			if ($record_condition_gametypes!="") $sql_record .= " $record_condition_gametypes ";
			//if ($record_condition_teamsize!="") $sql_record .= " $record_condition_teamsize ";
			$sql_record .= " ORDER by ostats.timestamp ASC LIMIT 0,1";				 				    
		$q_record = small_query($sql_record);
		$regular = 0;
	 	echo "<!-- Debug: BkpSQL- ".$sql_record." -->\r\n\r\n";	 	
	}

	$rowclass = "grey";
	$r_record = $q_record;
	if ($r_record!=NULL)
	{
		$gametime = GetMinutes($r_record[rectime]);
	  	$playername = $r_record[recplayername];
	  	$playerid = $r_record[recplayerid];
	  	$pcountry = $r_record[recpcountry]; 
	  	$matchid = $r_record[recmatchid];
	  	$matchname = mdate($r_record[matchtime]);
	  	$servername = get_short_servername($r_record[servername]);
	  	$serverip = $r_record[serverip];	  		  
		$matchmode = $r_record[matchmode];
		unset($matchteam);
		if ($matchmode==1)
		{
			if ($r_record[attackingid]==1)
				$matchteam = "<a class=\"grey\" href=\"./?p=utateams&team=".urlencode($r_record[teamname1])."\">".htmlspecialchars($r_record[teamname1])."</a>";
			else
				$matchteam = "<a class=\"grey\" href=\"./?p=utateams&team=".urlencode($r_record[teamname0])."\">".htmlspecialchars($r_record[teamname0])."</a>";
		}	
	}
	  	  
	echo '<tr>	
	<td nowrap class="dark" align="center"><a class="darkhuman" href="./?p=minfo&amp;game='.$game.'&amp;type='.$type.'&amp;map='.$myurl.'">'.$r_mapfile.'</a></td>
	<td class="grey" align="center">'.$matchcount.'</td>';
	
	if ($r_record!=NULL)
	{
		$time_s = $r_record[matchtime]; // 20050722140406
		$time_d = mktime(0,0,0,substr($time_s,4,2),substr($time_s,6,2),substr($time_s,0,4));
		
		if (((time(void) -$time_d) / 86400.0) > 21)
		{
			if ($regular == 1) echo '<td class="grey" align="center">'.$gametime.'</td>';
			else echo '<td class="grey" align="center">'.$gametime.'&sup1</td>';
		}	
		else
		{
			if ($regular == 1) echo '<td class="grey" align="center"><font color="#FF0000">'.$gametime.'</font></td>';
			else echo '<td class="grey" align="center"><font color="#FF0000">'.$gametime.'&sup1</font></td>';	
		}
						
		echo '
		<td nowrap class="grey" align="center">';
		if ($matchteam)
			echo $matchteam.' &nbsp;(';

		echo '<a class="grey" href="./?p=pinfo&amp;pid='.$playerid.'">'.FormatPlayerName($pcountry, $playerid, $playername).'</a>';
		if ($matchteam)
			echo ')';
		echo '</td>';
		echo '
		<td nowrap class="grey" align="center"><a class="grey" href="./?p=match&amp;mid='.$matchid.'">'.$matchname.'</a></td>
		<td nowrap class="grey" align="center"><a class="grey" href="./?p=sinfo&amp;serverip='.$serverip.'">'.$servername.'</a></td>
		';
	}
	else
	{
		echo '
		<td class="grey" align="center"></td>
		<td class="grey" align="center"></td>
		<td class="grey" align="center"></td>
		<td class="grey" align="center"></td>
		';	
	}
	
	echo '</tr>';
}

echo'
</tbody></table>
<div class="pages"><b>Page ['.$tfpage.'/'.$tlpage.'] Selection: '.$fpageurl.' / '.$ppageurl.' / '.$npageurl.' / '.$lpageurl.'</b></div>
<br>
<div><font color="#ff0000">New records (<21 days) are marked RED</font></div>
<div><font color="#00ff00">StdAS:</font> Minimum Teamsizes: 4v4. Maximum Teamsizedifference: 1. </div>
<div><font color="#00ff00">ProAS:</font> Minimum Teamsizes: 3v3. Maximum Teamsizedifference: 1. </div>
<div><font color="#00ff00">iAS:</font>   Minimum Teamsizes: 3v3. Maximum Teamsizedifference: 1. </div>
<div>Records that dont fulfil these requirements are marked with (&sup1)</div>';
?>
