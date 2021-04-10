<?php 

// Read all available weapons, we'll need them later
if (!isset($pickupnames)) {
	$sql_pickupnames = "SELECT id, name FROM uts_pickups";
	$q_pickupnames = mysql_query($sql_pickupnames);
	if (mysql_num_rows($q_pickupnames)==0) {
		// Add some basic known pickups
		mysql_query("INSERT INTO uts_pickups (name,sequence) VALUES ('Thigh Pads','11'),('Body Armor','12'),('ShieldBelt','13'),('Health Vial','1'),('Health Pack','2'),('Super Health Pack','3'),('Nali Healing Fruit','4');") or die(mysql_error());
		$q_pickupnames = mysql_query($sql_pickupnames);
	}
	while ($r_pickupnames = mysql_fetch_array($q_pickupnames)) {
		$pickupnames[$r_pickupnames['name']] = $r_pickupnames['id'];
	}
}

// Reset time arrays
foreach (array_keys($pickupnames) as &$pickup) {
	$pickupstats[$pickupnames[$pickup]] = [];
}

if (!isset($qm_gamestart)) {
	$qm_gamestart = small_query("SELECT col0 FROM uts_temp_$uid WHERE col1 = 'game' AND col2 = 'realstart'");
}

if (!isset($gametime)) {
	$qm_time = small_query("SELECT col3 FROM uts_temp_$uid WHERE col1 = 'info' AND col2 = 'Absolute_Time'");
	$qm_zone = small_query("SELECT col3 FROM uts_temp_$uid WHERE col1 = 'info' AND col2 = 'GMT_Offset'");
	$gametime = $qm_time[col3];
	$offset = $qm_zone[col3];
	if ($offset)
		$gametime = offsetutdate($gametime,$offset);
	else
		$gametime = utdate($gametime);
}

// Track only known pickups
$sql_pickupstats = "SELECT col0, col2 FROM uts_temp_$uid WHERE col1 = 'item_get' AND col3 = '".$playerid."';";
$q_pickupstats = mysql_query($sql_pickupstats);

while ($r_pickupstats = mysql_fetch_array($q_pickupstats)) {
	// Cycle through pickups, log times against those we are tracking
	if (!(in_array($r_pickupstats[col2],array_keys($pickupnames)))) {
		// Dynamically insert pickups? To-Do
		if (isset($dynamicpickups) && $dynamicpickups==true) {
			mysql_query("INSERT INTO uts_pickups SET name = '".addslashes($r_pickupstats[col2])."';") or die(mysql_error());
			$pickupid = mysql_insert_id();
			$pickupnames[$r_pickupstats['col2']] = $pickupid;
		}
	}

	if (in_array($r_pickupstats[col2],array_keys($pickupnames))) {
		$pickupid = $pickupnames[$r_pickupstats['col2']];

		$pickupact = $r_pickupstats[col0];
		$pickuprel = $pickupact - $qm_gamestart[col0]; // relative time after map started and player spawned, in-game seconds with dilation

		if (isset($timedilation)) {
			$pickuprwt = $gametime + ($pickupact / $timedilation); // gametime is real world time, need to sort dilation here
		} else {
			$pickuprwt = $gametime + $pickupact; // gametime is real world time, need to sort dilation here
		}

		array_push($pickupstats[$pickupid],array('act' => $pickupact, 'rel' => $pickuprel, 'rwt' => $pickuprwt));
	} 
}

if (!isset($pid)) {
	// Get the unique pid of this player
	if (!isset($playerid2pid[$playerid])) {
		continue;
	} else {
		$pid = $playerid2pid[$playerid];
	}
}

// Check rank_year
if (!isset($rank_year) || $rank_year == 0)
{
	// Fetch this from the match gametime
	$r_match = small_query("SELECT LEFT(time,4) AS matchtime FROM uts_match WHERE id = '".$matchid."';");
	if (isset($r_match))
		$rank_year = substr($r_match['matchtime'],0,4);
	else
		$rank_year = date("Y");
}

// Record the pickup statistics for this match
foreach ($pickupnames as $pickupname => $pickupid) {
	if (!empty($pickupstats[$pickupid])) {
		// Loop through every pickup recorded for this player
		foreach ($pickupstats[$pickupid] as &$pickup) {
			// echo "PU: ".$pid."-".$pickupid."-".$pickup['act']."; ";
			// Check for entry before inserting
			$dupepickup = small_count("SELECT matchid, year, pid, pickup, timestamp FROM uts_pickupstats WHERE matchid='".$matchid."' AND year = '".$rank_year."' AND pid = '".$pid."' AND pickup = '".$pickupid."' AND time_logged = '".$pickup['act']."';");
			if ($dupepickup > 0) {
				mysql_query("	UPDATE	uts_pickupstats
						SET	time_logged = '".$pickup['act']."',
							time_relative= '".$pickup['rel']."',
							timestamp= '".$pickup['rwt']."'
						WHERE	matchid = '".$matchid."' AND year = '".$rank_year."' AND pid = '".$pid."' AND pickup = '".$pickupid."' AND time_logged = '".$pickup['act']."';") or die(mysql_error());
			} else {
				mysql_query("	INSERT	
						INTO	uts_pickupstats
						SET	matchid = '$matchid',
							year = '$rank_year',
							pid = '$pid',
							pickup = '$pickupid',
							timestamp = '".$pickup['rwt']."',
							time_logged = '".$pickup['act']."',
							time_relative= '".$pickup['rel']."';") or die(mysql_error());
			}
		}
	}
}	
?>
