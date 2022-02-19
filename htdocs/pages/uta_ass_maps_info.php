<?php 	// Cratos

include_once ("includes/config.php");
// include ("includes/uta_functions.php");
include_once ("uta_recordzone_filters.php");

global $t_smartass_objs, $t_smartass_objstats, $t_match, $t_pinfo;

echo'
<table class="box" border="0" cellpadding="1" cellspacing="1">
  <tbody><tr>
    <td class="heading" colspan="7" align="center">Assault Objectives / Records';

	uta_rz_FilterForm();

echo' </td>
 </tr>
  <tr>
    <td class="smheading" rowspan="2" align="center" width="170">Objective Name</td>
    <td class="smheading" rowspan="2" align="center" width="50">Rating</td>
    <td class="smheading" rowspan="2" align="center" width="50">Taken</td>
    <td class="smheading" rowspan="2" align="center" width="65">Average Time</td>
    <td class="smheading" colspan="3" align="center">'.$recordType.' Records '.$gameFilter.' '.$toggleFilter.'</td>
  </tr>
  <tr>
  	<td class="smheading" align="center" width="60">Time</td>
    <td class="smheading" align="center" width="150">Team / Player</td>
    <td class="smheading" align="center" width="200">Match</td>   
  </tr>';


$sql_objs = "SELECT id, objnum, objname, objmsg, defensepriority, rating
			FROM ".(isset($t_smartass_objs) ? $t_smartass_objs : "uts_smartass_objs")." WHERE mapfile like '".$realmap."' order by defensepriority desc, objname asc";
$q_objs = mysql_query($sql_objs) or die(mysql_error());

while ($r_objs = mysql_fetch_array($q_objs)) {
	
	// Get Objective Info
	$r_id = $r_objs['id'];
	$r_objnum = $r_objs['objnum'];
	$r_objname = $r_objs['objname'];
	$r_objmsg = $r_objs['objmsg'];
	$r_rating = $r_objs['rating'];
	
	// Get AVG stuff
	$sql_objtaken = "SELECT count(objid) as takencount, avg(timestamp) as avgtime from ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats")." where objid = $r_id";
	$r_taken = small_query($sql_objtaken);
	$takencount = 0;
	$avgtime = "";
	if ($r_taken != Null)
	{
		$takencount = $r_taken['takencount'];
		$avgtime = GetMinutes($r_taken['avgtime']); 
	}

	// Get a Record using teamsize condition
	$condition = " AND def_teamsize >= att_teamsize AND att_teamsize >= 4 ";
	$sql_record = "SELECT objstats.timestamp as rectime, 
			".(isset($t_match) ? $t_match : "uts_match").".teamname0, ".(isset($t_match) ? $t_match : "uts_match").".teamname1, ".(isset($t_match) ? $t_match : "uts_match").".ass_att AS attackingid, ".(isset($t_match) ? $t_match : "uts_match").".matchmode,
						".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name as recplayername,
						".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id as recplayerid,
						".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country as recpcountry,
						objstats.matchid as recmatchid,
						".(isset($t_match) ? $t_match : "uts_match").".time as matchtime	  						
						from ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats")." objstats  
				inner join ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." on objstats.pid = ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id  
				inner join ".(isset($t_match) ? $t_match : "uts_match")." on objstats.matchid = ".(isset($t_match) ? $t_match : "uts_match").".id 
				WHERE objid = $r_id ";
                        if ($record_condition_gametypes!="") $sql_record .= " $record_condition_gametypes ";
                        if ($record_condition_teamsize!="") $sql_record .= " $record_condition_teamsize ";
	$sql_record .= " ORDER BY rectime ASC LIMIT 0,1";
	echo "<!-- Debug: PriSQL- ".$sql_record." -->\r\n\r\n";
	$r_record = small_query($sql_record);
	
	// No record found?
	if ($r_record == Null)
	{
                echo "<!-- Debug: PriSQL returned nil records -->\r\n\r\n";
		// Get a Record without any teamsize condition
		$sql_record = "SELECT objstats.timestamp as rectime, 
			".(isset($t_match) ? $t_match : "uts_match").".teamname0, ".(isset($t_match) ? $t_match : "uts_match").".teamname1, ".(isset($t_match) ? $t_match : "uts_match").".ass_att AS attackingid, ".(isset($t_match) ? $t_match : "uts_match").".matchmode,
						".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name as recplayername,
						".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id as recplayerid,
						".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country as recpcountry,
						objstats.matchid as recmatchid,
						".(isset($t_match) ? $t_match : "uts_match").".time as matchtime	  						
				from ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats")." objstats
				inner join ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." on objstats.pid = ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id  
				inner join ".(isset($t_match) ? $t_match : "uts_match")." on objstats.matchid = ".(isset($t_match) ? $t_match : "uts_match").".id 
				WHERE objid = $r_id
				ORDER BY rectime ASC LIMIT 0,1";	
		$r_record = small_query($sql_record);
                echo "<!-- Debug: BkpSQL- ".$sql_record." -->\r\n\r\n";

	}
	  
	if ($r_record != Null)
	{
		$recordtimestr = GetMinutes($r_record['rectime']);
	  	$playername = $r_record['recplayername'];
	  	$playerid = $r_record['recplayerid'];
	  	$pcountry = $r_record['recpcountry']; 
	  	$matchid = $r_record['recmatchid'];
	  	$matchname = mdate($r_record['matchtime']);	  		
                $matchmode = $r_record['matchmode'];
		unset($matchteam);
                if ($matchmode==1)
                {
                        if ($r_record['attackingid']==1)
                                $matchteam = $r_record['teamname1'];
                        else
                                $matchteam = $r_record['teamname0'];
                }
	}
	
	echo'
	<tr>
		<td class="dark" align="center">'.$r_objname.'</td>
		<td class="dark" align="center">'.$r_rating.'</td>			
		<td class="dark" align="center">'.$takencount.'</td>
		<td class="dark" align="center">'.$avgtime.'</td>';
		
	if ($r_record != Null)
	{
		echo '
		<td class="grey" align="center">'.$recordtimestr.'</td>
		<td nowrap class="grey" align="center">';
                if ($matchteam)
                        echo htmlspecialchars($matchteam).' &nbsp;(';
		echo '<a class="greyn" href="./?p=pinfo&amp;pid='.$playerid.'">'.FormatPlayerName($pcountry, $playerid, $playername).'</a>';
		if ($matchteam)
			echo ')';
		echo '</td>
		<td class="grey" align="center"><a class="grey" href="./?p=match&amp;mid='.$matchid.'">'.$matchname.'</a></td>
		';
	}
	else
	{
		echo '
		<td class="grey" align="center"></td>
		<td class="grey" align="center"></td>
		<td class="grey" align="center"></td>';
	}
	
	echo '</tr>';	
}
echo'</tbody></table><br>';
?>
