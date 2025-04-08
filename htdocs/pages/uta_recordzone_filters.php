<?php 
// Get game & type and add record conditions if necessary --// Timo 25/07/05
$game = isset($_GET['game']) ? my_addslashes($_GET['game']) : "";
$type = isset($_GET['type']) ? my_addslashes($_GET['type']) : "";

$record_condition_gametypes = "";
$record_condition_teamsize = "";

$recordType = "All";

if (!empty($type))
{
	$matchmode = intval($type)-1;
	if ($matchmode > -1)
	{
		$record_condition_gametypes .= " AND uts_match.matchmode='".$matchmode."'";
		$recordType = "Public"; // Default to Public...
	}

	if ($matchmode==1)
		$recordType = "League"; // Check if actually League.
}

if (!empty($game) && $game > 0)
{	
	$gameid = -1;

	for ($i=0;$i<count($asclasses);$i++)
	{
		if ($game==$asclasses[$i]["id"])
			$gameid = $i;
	}
	debugprint($gameid,"Valid Game ID (>-1)","32");

	if ($gameid > -1)
	{
		$gameFilter = "(".$asclasses[$gameid]["short"].")";
		debugprint($gameFilter,"Picked GameType","38");
		if (isset($asclasses[$gameid]["insta"]))
			$record_condition_gametypes .= " AND uts_match.insta='".$asclasses[$gameid]["insta"]."'";
		
		if (isset($asclasses[$gameid]["friendlyfirescale"]))
			$record_condition_gametypes .= " AND uts_match.friendlyfirescale='".$asclasses[$gameid]["friendlyfirescale"]."'";
			
		if (isset($asclasses[$gameid]["min_teamsize_rec"]))
			$record_condition_teamsize .= " AND att_teamsize>='".$asclasses[$gameid]["min_teamsize_rec"]."' AND def_teamsize >= '".$asclasses[$gameid]["min_teamsize_rec"]."'";
	}
	debugprint($record_condition_gametypes,"Filters GT","48");
}
else
{
	$record_condition_teamsize = " AND att_teamsize >= 4 AND def_teamsize >= 4";	
}


// End Game/Type filtering --// Timo.

function uta_rz_FilterForm()
{
	// Added search filters --// Timo: 25/07/05

	global $type, $asclasses, $game;
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
	if (isset($_REQUEST['p']))
		echo '<input type="hidden" name="p" value="'.$_REQUEST['p'].'">';
	if (isset($_REQUEST['map']))
		echo '<input type="hidden" name="map" value="'.$_REQUEST['map'].'">';
	echo '<table width="600" class="searchform" border="0" cellpadding="1" cellspacing="1">';
	echo '<tr><td><strong>Filter:</strong></td>';
	echo '<td>Type:</td>';
	echo '<td><select class="searchform" name="type">';
	        echo '  <option value="0">*</option>';
	        echo '  <option value="1"';
	        if ($type=="1")
	                echo ' SELECTED';
	        echo '>Public Matches</option>';
	        echo '  <option value="2"';
	        if ($type=="2")
	                echo ' SELECTED';
	        echo '>League Matches</option>';
	echo '</select>';
	echo '<td>GameType: </td>';
	echo '<td><select class="searchform" name="game">';
	        echo '  <option value="-1">*</option>';
	        for ($i=0;$i<count($asclasses);$i++)
	        {
	                echo '  <option value="'.$asclasses[$i]["id"].'"';
	                if ($game==$asclasses[$i]["id"])
	                        echo ' SELECTED';
	                echo '>'.$asclasses[$i]["desc"].'</option>';
	        }
	echo '</select>';
	echo '&nbsp;';
	echo '</td>';
	echo '<td><input class="searchform" type="Submit" value="Apply" /></td>';
	echo '</tr></table></form>';
	// End search filters --// Timo.
}

function uta_rz_FilterFormMini()
{
	// Added search filters --// Timo: 25/07/05

	global $type, $asclasses, $game;
	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="GET">';
	echo '<input type="hidden" name="p" value="'.$_REQUEST['p'].'">';
	echo '<input type="hidden" name="map" value="'.$_REQUEST['map'].'">';
	echo '<input type="hidden" name="team" value="'.$_REQUEST['team'].'">';
	echo '<table width="600" class="searchform" border="0" cellpadding="1" cellspacing="1">';
	echo '<tr><td><strong>Filter:</strong></td>';
/* 	echo '<td>Type:</td>';
 	echo '<td><select class="searchform" name="type">';
 	        echo '  <option value="0">*</option>';
 	        echo '  <option value="1"';
 	        if ($type=="1")
 	                echo ' SELECTED';
 	        echo '>Public Matches</option>';
 	        echo '  <option value="2"';
 	        if ($type=="2")
 	                echo ' SELECTED';
 	        echo '>League Matches</option>';
 	echo '</select>';
*/
	echo '<td>GameType: </td>';
	echo '<td><select class="searchform" name="game">';
	        echo '  <option value="-1">*</option>';
	        for ($i=0;$i<count($asclasses);$i++)
	        {
	                echo '  <option value="'.$asclasses[$i]["id"].'"';
	                if ($game==$asclasses[$i]["id"])
	                        echo ' SELECTED';
	                echo '>'.$asclasses[$i]["desc"].'</option>';
	        }
	echo '</select>';
	echo '&nbsp;';
	echo '</td>';
	echo '<td><input class="searchform" type="Submit" value="Apply" /></td>';
	echo '</tr></table></form>';
	// End search filters --// Timo.
}
