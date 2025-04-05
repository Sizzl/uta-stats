<?php

if (isset($_GET["debug"]))
	$_SESSION['debug'] = true;

// --// brajan / timo - 10/03/08
function mks($str)
{
        global $notallowed, $spec_chars;
		if (Null != $notallowed && strlen($notallowed))
        	$str = str_replace($notallowed, '', $str);
		if (Null != $spec_chars && strlen($spec_chars))
        	$str = str_replace($spec_chars, '', $str);
        //if (get_magic_quotes_gpc() == 0) { $str = addslashes(trim($str)); };
		if (Null != $str)
        	$str = addslashes(trim($str));;
        return $str;
}

function checkLoadavg()
{

	if (file_exists("/proc/loadavg"))
	{
		$load = file_get_contents("/proc/loadavg");
		list($load1, $load5, $load15) = explode(" ", $load, 3);
		if (round($load5)>=2) {
			echo "<!-- Server Load: ".$load5." -->\r\n";
			return true;
		}
		return false;
	}
	else
		return false;
}

function debugprint($stringinput,$stringtype,$linenumber)
{
	if ($_SESSION['debug'])
		echo "<!-- Debug output [".$linenumber."]: ".$stringtype."- ".$stringinput." -->\r\n";
}

function get_short_servername($servername)
{
	if (!(strpos($servername,"=]DoG[= Pound - Standard Maps") === false))
		return ("=]DoG[= Standard");
	if (!(strpos($servername,"-=([DFP])=-PublicServer #2") === false))
		return ("-=([DFP])=- Public2 (zp)");
	if (!(strpos($servername,"-=([DFP])=-PublicServer") === false))
		return ("-=([DFP])=- Public");
	if (!(strpos($servername,"=]DoG[= Tired of StdAS Maps") === false))
		return ("=]DoG[= Custom");
	if (strpos($servername,"Match Server #") !== false)
		return ("UTA ".substr($servername,strpos($servername,"Match Server #"),15));
	if (strpos($servername,"iCTF Fraghut #1") !== false)
		return ("iCTF Fraghut #1");
		
	return ($servername);
}

function GetHours($seconds)
{
	$timehours = intval($seconds / 3600);
	$timemins = intval(($seconds / 60) % 60);
	$timesecs = ($seconds % 60);

	$Reqlength = 2; //Amount of digits we need
	if ($Reqlength-strlen($timehours) > 0) $timehours = str_repeat("0",($Reqlength-strlen($timehours))) . $timehours;
	if ($Reqlength-strlen($timemins) > 0) $timemins = str_repeat("0",($Reqlength-strlen($timemins))) . $timemins;
	if ($Reqlength-strlen($timesecs) > 0) $timesecs = str_repeat("0",($Reqlength-strlen($timesecs))) . $timesecs;
	return $timehours . ":" . $timemins . ":" . $timesecs;
}

function DateAdd($timestamp, $seconds,$minutes,$hours,$days,$months,$years) {
	$mytime = mktime(1+$hours,0+$minutes,0+$seconds,1+$months,1+$days,1970+$years);
 return $timestamp + $mytime;
}

function uta_ass_objectiveinfo($mid, $att_teamname) {
  global $t_smartass_objs, $t_smartass_objstats, $t_match, $t_pinfo, $t_player, $t_games; // fetch table globals.
  if (is_numeric($mid))
  {
	echo'
	<table border="0" cellpadding="0" cellspacing="2" width="620">
	<tbody><tr>
		<td class="heading" colspan="3" align="center">Assault Objectives - '.$att_teamname.' Team Attacking</td>
	</tr>
	<tr>
		<td class="smheading" align="center" width="200">Objective Name</td>
		<td class="smheading" align="center" width="125">Timestamp</td>
		<td class="smheading" align="center" width="275">Taken by</td>
	</tr>';
	
		
$sql_objs = "SELECT ".(isset($t_smartass_objs) ? $t_smartass_objs : "uts_smartass_objs").".id as objid, objnum, objname, defensepriority 
			FROM ".(isset($t_smartass_objs) ? $t_smartass_objs : "uts_smartass_objs")." inner join ".(isset($t_match) ? $t_match : "uts_match")." on ".(isset($t_smartass_objs) ? $t_smartass_objs : "uts_smartass_objs").".mapfile = ".(isset($t_match) ? $t_match : "uts_match").".mapfile
			WHERE ".(isset($t_match) ? $t_match : "uts_match").".id = ".$mid." ORDER BY defensepriority desc, objname asc;";
$q_objs = mysql_query($sql_objs) or die(mysql_error());

while ($r_objs = mysql_fetch_array($q_objs)) {
	
	// Get Objective Info
	$objid = $r_objs['objid'];
	$objnum = $r_objs['objnum'];
	$objname = $r_objs['objname'];
	
	// Who did take it?	
	$sql_objgone = "SELECT 'broken_inc_functions' AS slowquery, ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats").".timestamp as objtime,
					".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id as pid, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".name as pname, ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".country as pcountry
					from ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats")." inner join ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")." on ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats").".pid = ".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo").".id 
					WHERE ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats").".matchid = '".$mid."' AND ".(isset($t_smartass_objstats) ? $t_smartass_objstats : "uts_smartass_objstats").".objid = '".$objid."';";
	echo "<!-- dbvl: ".$sql_objgone." -->\n";				
	$q_objgone = small_query($sql_objgone);	
	
	if ($q_objgone != Null)
	{
		$timestring = GetMinutes($q_objgone['objtime']); 
		$pid = $q_objgone['pid'];
		$pname = $q_objgone['pname'];
		$pcountry = $q_objgone['pcountry']; 		
	}		
	
	echo '<tr>';
		echo '<td class="darkhuman" align="center">'.$objname.'</td>';
		if ($q_objgone != Null)
		{
			echo '<td nowrap class="grey" align="center">'.$timestring.'</td>';
			echo '<td class="grey" align="center"><a href="./?p=pinfo&amp;pid='.$pid.'">'.FormatPlayerName($pcountry, $pid, $pname).'</a></td>';
		}
		else
		{
			echo '<td class="grey" align="center"></td>';
			echo '<td class="grey" align="center"></td>';
		}
	echo '</tr>';	
	}
	
 }
 echo '</tbody></table>';	
}	
?>
