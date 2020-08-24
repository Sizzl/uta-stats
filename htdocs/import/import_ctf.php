<?php 

	// Cratos - EUT stuff ---start---

    $q_matchmode = small_query("SELECT col2 FROM uts_temp_$uid WHERE col1 = 'eut_matchmode' LIMIT 0,1");
	if ($q_matchmode != NULL) $matchmode = ($q_matchmode[col2] == "True") ? 1 : 0;
	else $matchmode = 0; 	
	$q_matchcode = small_query("SELECT col2 FROM uts_temp_$uid WHERE col1 = 'eut_matchcode' LIMIT 0,1");
	if ($q_matchcode != NULL) $matchcode = $q_matchcode[col2];
	else $matchcode = ""; 	
	$q_matchlength = small_query("SELECT col2 FROM uts_temp_$uid WHERE col1 = 'eut_matchlenth' LIMIT 0,1");
	if ($q_matchlength != NULL) $matchlength = $q_matchlength[col2];
	else $matchlength = 3;
	$q_mapsleft = small_query("SELECT col2 FROM uts_temp_$uid WHERE col1 = 'eut_mapsleft' LIMIT 0,1");
	if ($q_mapsleft != NULL) $mapsleft = $q_mapsleft[col2];
	else $mapsleft = 0;
	$q_teamnames = small_query("SELECT col2, col3 FROM uts_temp_$uid WHERE col1 = 'eut_teamnames_end' LIMIT 0,1");
	if ($q_teamnames != NULL) { $teamname0 = $q_teamnames[col2]; $teamname1 = $q_teamnames[col3]; }
	else { $teamname0 = "RED"; $teamname1 = "BLUE"; }
	$q_score = small_query("SELECT col4, col5 FROM uts_temp_$uid WHERE col1 = 'eut_teamscore_end' LIMIT 0,1");
	if ($q_score != NULL) { $score0 = $q_score[col4]; $score1 = $q_score[col5]; }
	else { $score0 = -1; $score1 = -1; }
	
	$teamname0 = addslashes($teamname0);
	$teamname1 = addslashes($teamname1);
	
	$eut_match = "UPDATE uts_match SET 
		matchmode = $matchmode, matchcode = '$matchcode',
		matchlength = $matchlength, mapsleft = $mapsleft, 
		teamname0 = '$teamname0', teamname1 = '$teamname1',
		score0 = $score0, score1 = $score1  
		WHERE id = $matchid";
		
	mysql_query($eut_match) or die(mysql_error());	
	
	// Cratos - EUT stuff ---end---




// Get Player Flag Events Count
	$sql_playerctf = "SELECT col1, COUNT(col1) AS flag_count FROM uts_temp_$uid WHERE col1 LIKE 'flag_%' AND col2 = $playerid GROUP BY col1";
	$q_playerctf = mysql_query($sql_playerctf);

	$flag_taken = 0;
	$flag_dropped = 0;
	$flag_return = 0;
	$flag_capture = 0;
	$flag_cover = 0;
	$flag_seal = 0;
	$flag_assist = 0;
	$flag_kill = 0;
	$flag_pickedup = 0;

	while ($r_playerctf = mysql_fetch_array($q_playerctf)) {

		// Cycle through events and see what the player got

		IF ($r_playerctf[col1] == "flag_taken") { $flag_taken = $r_playerctf[flag_count]; }
		IF ($r_playerctf[col1] == "flag_dropped") { $flag_dropped = $r_playerctf[flag_count]; }
		IF ($r_playerctf[col1] == "flag_returned") { $flag_return = $r_playerctf[flag_count]; }
		IF ($r_playerctf[col1] == "flag_captured") { $flag_capture = $r_playerctf[flag_count]; }
		IF ($r_playerctf[col1] == "flag_cover" or $r_playerctf[col1] == "Flag_cover") { $flag_cover = $r_playerctf[flag_count]; }
		IF ($r_playerctf[col1] == "flag_seal" or $r_playerctf[col1] == "Flag_seal") { $flag_seal = $r_playerctf[flag_count]; }
		IF ($r_playerctf[col1] == "flag_assist" or $r_playerctf[col1] == "Flag_assist") { $flag_assist = $r_playerctf[flag_count]; }
		IF ($r_playerctf[col1] == "flag_kill" or $r_playerctf[col1] == "Flag_kill") { $flag_kill = $r_playerctf[flag_count]; }
		IF ($r_playerctf[col1] == "flag_pickedup" or $r_playerctf[col1] == "flag_pickedup") { $flag_pickedup = $r_playerctf[flag_count]; }
	}

	$sql_playerflags = "	UPDATE 	uts_player
								SET 		flag_taken = $flag_taken,
											flag_dropped = $flag_dropped,
											flag_return = $flag_return,
											flag_capture = $flag_capture,
											flag_cover = $flag_cover,
											flag_seal = $flag_seal,
											flag_assist = $flag_assist,
											flag_kill = $flag_kill,
											flag_pickedup = $flag_pickedup
								WHERE 	id = $playerecordid";
	mysql_query($sql_playerflags) or die(mysql_error());
?>
