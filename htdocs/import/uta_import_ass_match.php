<?php 
	// ************************************************************************************
	// Import SmartAS matchdata: Brajan / Cratos
	// ************************************************************************************	
		
	// Get real gamestart
	$qm_ass_gamestart = small_query("SELECT `col0` FROM `uts_temp_".$uid."` WHERE `col1` = 'game_start';");
	$ass_gamestart = (isset($qm_ass_gamestart[col0]) ? $qm_ass_gamestart[col0] : 0);
	
	// Get real gamestop
	$qm_ass_gameend = small_query("SELECT `col0` FROM `uts_temp_".$uid."` WHERE `col1` = 'game_end';");
	$ass_gameend = (isset($qm_ass_gameend[col0]) ? $qm_ass_gameend[col0] : 0);
	
	// Get real gameduration
	$ass_gameduration = $ass_gameend - $ass_gamestart;
		
	// Get SmartAS data			
	$q_matchmode = small_query("SELECT `col2` FROM `uts_temp_".$uid."` WHERE `col1` = 'ass_matchmode' LIMIT 0,1;");
	if ($q_matchmode != NULL) $matchmode = ($q_matchmode[col2] == "True") ? 1 : 0;
	else $matchmode = 0; 	
	$q_matchcode = small_query("SELECT `col2` FROM `uts_temp_".$uid."` WHERE `col1` = 'ass_matchcode' LIMIT 0,1;");
	if ($q_matchcode != NULL) $matchcode = $q_matchcode[col2];
	else $matchcode = ""; 
	$q_mapsequence = small_query("SELECT `col2` FROM `uts_temp_".$uid."` WHERE `col1` = 'ass_mapsequence' LIMIT 0,1;");
	if ($q_mapsequence != NULL) $mapsequence = $q_mapsequence[col2];
	else $mapsequence = 0;
	$q_matchlength = small_query("SELECT `col2` FROM `uts_temp_".$uid."` WHERE `col1` = 'ass_matchlenth' LIMIT 0,1;");
	if ($q_matchlength != NULL) $matchlength = $q_matchlength[col2];
	else $matchlength = 14;
	$q_mapsleft = small_query("SELECT `col2` FROM `uts_temp_".$uid."` WHERE `col1` = 'ass_mapsleft' LIMIT 0,1;");
	if ($q_mapsleft != NULL) $mapsleft = $q_mapsleft[col2];
	else $mapsleft = 0;
	$q_teamnames = small_query("SELECT `col2`, `col3` FROM `uts_temp_".$uid."` WHERE `col1` = 'ass_teamnames_end' LIMIT 0,1;");
	if ($q_teamnames != NULL) { $teamname0 = $q_teamnames[col2]; $teamname1 = $q_teamnames[col3]; }
	else { $teamname0 = ""; $teamname1 = ""; }
	$q_score = small_query("SELECT `col2`, `col3` FROM `uts_temp_".$uid."` WHERE `col1` = 'ass_teamscore_end' LIMIT 0,1;");
	if ($q_score != NULL) { $score0 = $q_score[col2]; $score1 = $q_score[col3]; }
	else { $score0 = -1; $score1 = -1; }
	
	$q_teamsizead = small_query("SELECT `col2`, `col3` FROM `uts_temp_".$uid."` WHERE `col1` = 'avg_teamsize_ad' LIMIT 0,1;");
	if ($q_teamsizead != NULL) { $size_a = round($q_teamsizead[col2]); $size_d = round($q_teamsizead[col3]); }
	else { $size_a = -1; $size_d = -1; }
	
	$r_assteam = small_query("SELECT `col2` FROM `uts_temp_".$uid."` WHERE `col1` = 'assault_attacker' LIMIT 0,1;");
	$assteam = $r_assteam[col2];
	$updateassteam = "UPDATE `uts_match` SET `ass_att` = '".$assteam."' WHERE `id` = '".$matchid."';";
	mysql_query($updateassteam) or die("uta_match UPDATE team; ".mysql_error());
	
	
	$teamname0 = addslashes($teamname0);
	$teamname1 = addslashes($teamname1);
	
	$uta_match = "UPDATE `uts_match` SET
		`startstamp` = '".$ass_gamestart."', `matchtime` = '".$ass_gameduration."', 
		`matchmode` = '".$matchmode."', `matchcode` = '".$matchcode."',
		`mapsequence` = '".$mapsequence."', `matchlength` = '".$matchlength."', `mapsleft` = '".$mapsleft."', 
		`teamname0` = '".$teamname0."', `teamname1` = '".$teamname1."',
		`score0` = '".$score0."', `score1` = '".$score1."',
		`att_teamsize_avg` = '".$size_a."', `def_teamsize_avg` = '".$size_d."'
		WHERE `id` = '".$matchid."';";
		

	mysql_query($uta_match) or die("uta_match UPDATE all;".mysql_error()."\n".$uta_match);
	
		
	
	
	// ************************************************************************************
	// Import Objectives list
	// ************************************************************************************	
	$sql_objectivelist = "SELECT col2 as objnum, col3 as objname, col4 as objmsg FROM uts_temp_$uid WHERE col1 LIKE 'assault_objname'";	
	$q_objective = mysql_query($sql_objectivelist);
		
	// Fix mapfilename
	if (substr($mapfile,-4) != ".unr") $mapfile = $mapfile . ".unr";
	
	while ($r_objective = mysql_fetch_array($q_objective)) {
		
		// Get Objective Info
		$objnum = $r_objective[objnum];	
	 	$objname = addslashes($r_objective[objname]);
	 	$objmsg = addslashes($r_objective[objmsg]);				
		
		// Get Extended Info
		$obj_ext_info = small_query("SELECT col2 as objnum, col3 as defpri, col4 as deftime FROM uts_temp_$uid WHERE col1 LIKE 'assault_objinfo' AND col2 = $objnum");
	 	if ($obj_ext_info != Null)
	 	{
			$deftime = $obj_ext_info[deftime];	
	 		$defpri = $obj_ext_info[defpri];
	 		// Check old logfile format
	 		if (strlen($deftime) > 2) $deftime = 0;
	 		else $deftime = intval($deftime);	
	 	}
	 	else
	 	{
	 		$deftime = 0;
	 		$defpri = 0;	
	 	}		
	 	
	 	// Get Existing Info
	 	$q_id = small_query("SELECT id, defensepriority, objmsg, defensetime FROM uts_smartass_objs WHERE mapfile like '$mapfile' AND objnum = $objnum");		
						
		// If Objective doesnt exist yet: INSERT
		if ($q_id == NULL)
		{					 	
		 	$obj_sql = "INSERT INTO uts_smartass_objs (mapfile,objnum,objname,objmsg,defensepriority,defensetime) 
		 					   values ('$mapfile',$objnum,'$objname','$objmsg',$defpri,$deftime)";
		 	mysql_query($obj_sql) or die(mysql_error());			
		}	
		else
		{						
			// If Objective exixts: maybe UPDATE
			$existid = $q_id[id];
			$existpri = $q_id[defensepriority];
			$existmsg = $q_id[objmsg];
			$existtime = $q_id[defensetime];
			
			// Update if defensepriority is 0		
			if (($defpri>0 && $existpri<=0) || ($existmsg=="" && $objmsg!="") || ($existtime<=0 && $deftime >0))
			{
			 	mysql_query("Update uts_smartass_objs set defensepriority = $defpri, objmsg = '$objmsg', defensetime = $deftime WHERE id = $existid") or die(mysql_error());							
			}
		}	
	}	
?>
