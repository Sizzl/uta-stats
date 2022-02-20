<?php 	// Cratos
								
	// ************************************************************************************
	// Headshots
	// ************************************************************************************
	
	$qm_headshots = small_query("SELECT count(col1) as headshots FROM uts_temp_".$uid." WHERE col1 = 'headshot' AND col2 = '".$playerid."';");
	if ($qm_headshots!=Null) 
	{	
		$headshots = $qm_headshots['headshots'];
		if ($headshots > 0)
		{
			$update_hs = "UPDATE uts_player SET	headshots = $headshots WHERE id = $playerecordid";
			mysql_query($update_hs) or die("uta_player UPDATE 1;".mysql_error());
		}
	}
				
				
	// ************************************************************************************
	// Get Assault Objectives
	// ************************************************************************************
	
	$sql_obj = "SELECT col0, col1, col2, col3, col4 FROM uts_temp_".$uid." WHERE col1 LIKE 'assault_obj' AND col2 = '".$playerid."';";
	$q_obj = mysql_query($sql_obj);
	while ($r_obj = mysql_fetch_array($q_obj)) {
				
		// log: timestamp;assault_obj;playerid;lastobj;objnum
		$timestamp = floatval($r_obj['col0'] - $ass_gamestart);
		
		// fix Timedilation
		$timestamp = get_dp($timestamp / $timedilation);
		
		// get objective
		$objnum = $r_obj['col4'];						
		$objid = small_query("SELECT id FROM uts_smartass_objs WHERE mapfile like '".$mapfile."' AND objnum = '".$objnum."';");
		
		// get final obj
		$final = ($r_obj['col3'] == "True") ? 1 : 0; 
	
		// get teamsize
		$objteams = small_query("SELECT col3, col4 FROM uts_temp_".$uid." WHERE col1 = 'assault_obj_teams' AND col2 = '".$objnum."';");
		$att_teamsize = -1; $def_teamsize = -1; 
		if ($objteams!=NULL)
		{
			$att_teamsize = $objteams['col3'];
			$def_teamsize = $objteams['col4'];
		}			
		$objSQL = "INSERT INTO uts_smartass_objstats (matchid,objid,final,pid,playerid,timestamp,att_teamsize,def_teamsize)
					VALUES ('".$matchid."','".$objid['id']."','".$final."','".$pid."','".$playerid."','".$timestamp."','".$att_teamsize."','".$def_teamsize."');";
		mysql_query($objSQL) or die(mysql_error());	
	}		

	
	
	// ************************************************************************************
	// Get Assault events
	// ATTENTION: These Events have to be accumulated in import_cleanup.php!!!
	// ************************************************************************************

	//
	//	Get INSTIGATORS
	//
	$sql_playerass = "SELECT col1, COUNT(col1) AS ass_events FROM uts_temp_".$uid." WHERE 
		(col1 LIKE 'ass_%' AND col2 = '".$playerid."') OR
		(col1 LIKE 'ass_assist' AND col4 = '".$playerid."')
		GROUP BY col1";
	$q_playerass = mysql_query($sql_playerass);

	$ass_suicide_coop = 0;
	$ass_teamchange = 0;
	$ass_h_launch = 0;
	$ass_r_launch = 0;
	$ass_h_jump = 0;
	$ass_assist = 0;
	
	while ($r_playerass = mysql_fetch_array($q_playerass)) {

		IF ($r_playerass['col1'] == "ass_suicide_coop") { $ass_suicide_coop = $r_playerass['ass_events']; }
		IF ($r_playerass['col1'] == "ass_teamchange") { $ass_teamchange = $r_playerass['ass_events']; }
		IF ($r_playerass['col1'] == "ass_h_launch") { $ass_h_launch = $r_playerass['ass_events']; }
		IF ($r_playerass['col1'] == "ass_r_launch") { $ass_r_launch = $r_playerass['ass_events']; }
		IF ($r_playerass['col1'] == "ass_h_jump") { $ass_h_jump = $r_playerass['ass_events']; }
		IF ($r_playerass['col1'] == "ass_assist") { $ass_assist = $r_playerass['ass_events']; }
	}	

	$ass_playerevents = "	UPDATE 	uts_player	SET			
											ass_suicide_coop = $ass_suicide_coop,
											ass_h_launch = $ass_h_launch,
											ass_r_launch = $ass_r_launch,
											ass_h_jump = $ass_h_jump,
											ass_assist = $ass_assist
							WHERE 	id = $playerecordid";
	mysql_query($ass_playerevents) or die("uta_player UPDATE 2; ".mysql_error());
	
	
	//
	//	Get VICTIMS
	//
	$sql_playerass = "SELECT col1, COUNT(col1) AS ass_events FROM uts_temp_".$uid." WHERE col1 LIKE 'ass_%' AND col3 = '".$playerid."' GROUP BY col1";
	$q_playerass = mysql_query($sql_playerass);

	$ass_h_launched = 0;
	$ass_r_launched = 0;	
	
	while ($r_playerass = mysql_fetch_array($q_playerass)) {

		IF ($r_playerass['col1'] == "ass_h_launch") { $ass_h_launched = $r_playerass['ass_events']; }
		IF ($r_playerass['col1'] == "ass_r_launch") { $ass_r_launched = $r_playerass['ass_events']; }
	}
	

	$ass_playerevents = "	UPDATE 	uts_player	SET			
											ass_h_launched = $ass_h_launched,
											ass_r_launched = $ass_r_launched
							WHERE 	id = $playerecordid";
	mysql_query($ass_playerevents) or die("uta_player UPDATE 3; ".mysql_error());
																	
?>
