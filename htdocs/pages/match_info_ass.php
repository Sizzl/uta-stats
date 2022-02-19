<?php 


// ******************************************************************************************************
// Cratos / Timo
// ******************************************************************************************************
include_once ("pages/match_info_server.php");

// Get information about this match
$sql_assault = small_query("SELECT `assaultid`, `ass_att`, `gametime`, `ass_win`, `time` FROM `uts_match` WHERE `id` = '".$mid."';");
$ass_id = $sql_assault['assaultid'];
$gametime = $sql_assault['gametime'];
$matchtime = $sql_assault['time'];

// Get information about the other match
$sql_assault2 = small_query("SELECT `id`, `gametime`, `ass_win`, `time` FROM `uts_match` WHERE `assaultid` = '".$ass_id."' AND `id` != '".$mid."' LIMIT 0,1");
if ($sql_assault2)
{
	$mid2 = $sql_assault2['id'];
	$matchtime2 = $sql_assault2['time'];
}
if (!isset($mid2))
{
        // Use alternative way to find an associated round. If this is a "warm-up" map, there won't be one.
        $sql_assault2 = small_query("SELECT `matchcode`, `mapfile`, `mapsleft`, `time` FROM `uts_match` WHERE `id` = '".$mid."';");
        if ($sql_assault2)
        {
		$matchtime2 = $sql_assault2['time'];
                $mid2sql = "SELECT `id` FROM `uts_match` WHERE `id` <> '".$mid."' AND `mapsleft` = '".$sql_assault2['mapsleft']."' AND `matchcode` = '".$sql_assault2['matchcode']."' and `mapfile` = '".$sql_assault2['mapfile']."';";
                $assmatch = small_query($mid2sql);
                if ($assmatch)
                {
                        $mid2 = $assmatch['id'];
                }
        }
}
if (isset($mid2))
{
	if (isset($matchtime2) && $matchtime2 < $matchtime)
	{
		// Swap the matches around to be in chronological order
		$midX = $mid2;
		$mid2 = $mid;
		$mid = $midX;
		unset($midX);
	}
	$matchinfo = server_stats($mid,false,$mid2); // return both rounds
}
else
	$matchinfo = server_stats($mid); // return only this round

// Work out who was attacking which match
$ass_att = $sql_assault['ass_att'];
if ($ass_att == 0)
{
	$ass_att = "Red";
	$ass_att2 = "Blue";
}
else
{
	$ass_att = "Blue";
	$ass_att2 = "Red";
}

// Work out the end result for each match
$asswin = $sql_assault['ass_win'];
$asswin2 = $sql_assault2['ass_win'];
if ($asswin == 0)
{
	$asswin = "$ass_att2 Successfully Defended";
}
else
{
	$asswin = "$ass_att Successfully Attacked";
}

if ($asswin2 == 0)
{
	$asswin2 = "$ass_att Successfully Defended";
}
else
{
	$asswin2 = "$ass_att2 Successfully Attacked";
}

$gametime = sec2min($gametime);
$gametime2 = sec2min($gametime2);


// ******************************************************************************************************
// Cratos
// ******************************************************************************************************
uta_ass_objectiveinfo($mid, $ass_att);
echo '<br />';
// ******************************************************************************************************

teamstats($mid, 'Match Summary - '.$ass_att.' Team Attacking', 'ass_obj', 'Ass Obj');

echo'
<table border="0" cellpadding="0" cellspacing="2" width="720">
  <tbody><tr>
    <td class="hlheading" colspan="15" align="center">'.$asswin.'</td>
  </tr>
</tbody></table>
<br>';


// The Other Game (if it happened)

if (isset($mid2))
{
	
// ******************************************************************************************************
// Cratos
// ******************************************************************************************************
	uta_ass_objectiveinfo($mid2,$ass_att2);
// ******************************************************************************************************
	echo '<br />';
	teamstats($mid2, 'Match Summary - '.$ass_att2.' Team Attacking');
		
	echo'
	<table border="0" cellpadding="0" cellspacing="2" width="720">
	<tbody><tr>
		<td class="hlheading" colspan="15" align="center">'.$asswin2.'</td>
	</tr>
	</tbody></table>
	<br>';
}
?>
