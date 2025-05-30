<?php 
	// Get the unique ID of this player.
	// Create a new one if he has none yet.
	$r_pid = small_query("SELECT id, country, banned FROM uts_pinfo WHERE name = '".$playername."';");
	if ($r_pid) {
		$pid = $r_pid['id'];
		$pid_country = $r_pid['country'];
		$playerbanned = ($r_pid['banned'] == 'Y') ? true : false;
	} else {
		mysql_query("INSERT INTO uts_pinfo SET name = '".$playername."';") or die("import_playerstuff pinfo INSERT; ".mysql_error());
		$pid = mysql_insert_id();
		$pid_country = false;
		$playerbanned = false;
	}
	$playerid2pid[$playerid] = $pid;

	// Do we import banned players?
	if ($playerbanned and $import_ban_type == 2) return;


	// Did the player do first blood?
	if ($playerid == $firstblood) {
		$upd_firstblood = "UPDATE uts_match SET firstblood = '".$pid."' WHERE id = '".$matchid."';";
		mysql_query($upd_firstblood) or die("import_playerstuff FB; ".mysql_error());
	}

	// Get player's IP
	$q_playerip = small_query("SELECT INET_ATON(col4) AS ip FROM uts_temp_".$uid." WHERE col1 = 'player' AND col2 = 'IP' and col3 = '".$playerid."' ORDER BY id ASC LIMIT 0,1;");
	$playerip = $q_playerip['ip'];

	// Check if player is in $ignored array (excludes �egistered players [pug/league]) --// Added 29/04/07 Timo.
	if (in_array($playerip,$ignored) && ord(substr($playername,-1,1))<>174)
		$playerbanned = true;

	// Map the IP to a country --// Modified 20/07/05 Timo: Added iptc fields (configurable via config.php)
	// $q_playercountry = small_query("SELECT ".$iptc["cfield"]." AS country FROM ".$iptc["table"]." WHERE ".$playerip." BETWEEN ".$iptc["ffield"]." AND ".$iptc["tfield"].";");
	// Added index to ip_cidr and modified query for performance gains -- Timo 2021/06
	$sql_playercountry = "SELECT ".$iptc["cfield"]." AS country
			FROM 
			( SELECT `".$iptc["ifield"]."` FROM `".$iptc["table"]."` WHERE `".$iptc["ffield"]."` <= ".$playerip." ORDER BY `".$iptc["ffield"]."` DESC LIMIT 1 ) `limit_ip`
			INNER JOIN `".$iptc["table"]."` ON limit_ip.`".$iptc["ifield"]."` = `".$iptc["table"]."`.`".$iptc["ifield"]."`
			WHERE `".$iptc["table"]."`.`".$iptc["tfield"]."` >= ".$playerip.";";
	$q_playercountry = small_query($sql_playercountry);

	if ($q_playercountry) {
		$playercountry = strtolower($q_playercountry['country']);
	} else {
		$playercountry = "xx";
	}

	if ($playercountry != $pid_country) {
		mysql_query("UPDATE uts_pinfo SET country = '".$playercountry."', banned = '".($playerbanned==true ? 'Y' : 'N')."' WHERE id = '".$pid."';") or die(mysql_error());
	}

	// Do we import banned players? --// recheck by Timo 29/04/07
	if ($playerbanned and $import_ban_type == 2) return;

	// Get Sprees
	$q_spree_dbl = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_dbl' AND col3 = '".$playerid."';");
	$q_spree_mult = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_mult' AND col3 = '".$playerid."';");
	$q_spree_ult = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_ult' AND col3 = '".$playerid."';");
	$q_spree_mon = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_mon' AND col3 = '".$playerid."';");

	$q_spree_kill = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_kill' AND col3 = '".$playerid."';");
	$q_spree_rampage = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_rampage' AND col3 = '".$playerid."';");
	$q_spree_dom = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_dom' AND col3 = '".$playerid."';");
	$q_spree_uns = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_uns' AND col3 = '".$playerid."';");
	$q_spree_god = small_count("SELECT id FROM uts_temp_".$uid." WHERE col1 = 'spree' AND col2 = 'spree_god' AND col3 = '".$playerid."';");


	// Get Count of Pickups
	$sql_player7 = "SELECT col2, COUNT(col2) AS pu_count FROM uts_temp_".$uid." WHERE col1 = 'item_get' AND col3 = '".$playerid."' GROUP BY col2;";
	$q_player7 = mysql_query($sql_player7);

	$pu_pads = 0;
	$pu_armour = 0;
	$pu_keg = 0;
	$pu_belt = 0;
	$pu_amp = 0;
	$pu_invis = 0;

	while ($r_player7 = mysql_fetch_array($q_player7)) {
		// Cycle through pickups and see what the player got
		if ($r_player7['col2'] == "Thigh Pads") { $pu_pads = $r_player7['pu_count']; }
		if ($r_player7['col2'] == "Body Armor") { $pu_armour = $r_player7['pu_count']; }
		if ($r_player7['col2'] == "Super Health Pack") { $pu_keg = $r_player7['pu_count']; }
		if ($r_player7['col2'] == "ShieldBelt") { $pu_belt = $r_player7['pu_count']; }
		if ($r_player7['col2'] == "Damage Amplifier") { $pu_amp = $r_player7['pu_count']; }
		if ($r_player7['col2'] == "Invisibility") { $pu_invis = $r_player7['pu_count']; }
	}

	// Get ping information
	$r_player9 = small_query("SELECT MIN(col4 * 1) AS lowping, MAX(col4 * 1) AS highping, AVG(col4 * 1) AS avgping FROM uts_temp_".$uid." WHERE col1 = 'Player' AND col2 = 'Ping' AND col3 = '".$playerid."' AND col4 > 0");
	$lowping = min($r_player9['lowping'],30000);
	$highping = min($r_player9['highping'],30000);
	$avgping = min($r_player9['avgping'],30000);

	// People who join at the end error the import, this stops it
	if ($lowping == NULL) { $lowping = 0; }
	if ($highping == NULL) { $highping = 0; }
	if ($avgping == NULL) { $avgping = 0; }

	// Get accuracy, ttl etc
	$r_acc = 0;
	$r_deaths = 0;
	$r_efficiency = 0;
	$r_frags = 0;
	$r_kills = 0;
	$r_teamkills = 0;
	$r_suicides = 0;
	$r_tos = 0;
	$r_ttl = 0;
	$r_score = 0;

	$q_acc = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'accuracy' AND col3 = '".$playerid."';");
	$q_deaths = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'deaths' AND col3 = '".$playerid."';");
	$q_kills = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'kills' AND col3 = '".$playerid."';");
	$q_teamkills = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'teamkills' AND col3 = '".$playerid."';");
	$q_efficiency = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'efficiency' AND col3 = '".$playerid."';");
	$q_suicides = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'suicides' AND col3 = '".$playerid."';");
	$q_tos = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'time_on_server' AND col3 = '".$playerid."';");
	$q_ttl = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'ttl' AND col3 = '".$playerid."';");
	$q_score = small_query("SELECT `col4` FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'score' AND col3 = '".$playerid."';");

	if (isset($q_kills) && isset($q_kills['col4']) && strlen($q_kills['col4']))
    	$r_kills = $q_kills['col4'];
	else
	{
		$q_kills = small_query("SELECT COUNT(`id`) AS `col4` FROM `uts_temp_".$uid."` WHERE `col1` = 'kill' AND `col2` = '".$playerid."';");
		if (strlen($q_kills['col4']))
			$r_kills = $q_kills['col4'];
	}
	if (isset($q_teamkills) && isset($q_teamkills['col4']) && strlen($q_teamkills['col4']))
		$r_teamkills = $q_teamkills['col4'];
	else
	{
		$q_teamkills = small_query("SELECT COUNT(`id`) AS `col4` FROM `uts_temp_".$uid."` WHERE `col1` = 'teamkill' AND `col2` = '".$playerid."';");
		if (strlen($q_teamkills['col4']))
			$r_teamkills = $q_teamkills['col4'];
	}
	if ($teamgame == "False") {
		$r_kills = $r_kills + $r_teamkills;
		$r_teamkills = 0;
	}

	if (isset($q_deaths) && isset($q_deaths['col4']) && strlen($q_deaths['col4']))
		$r_deaths = $q_deaths['col4'];
	else
	{
		$q_deaths = small_query("SELECT COUNT(`id`) AS `col4` FROM `uts_temp_".$uid."` WHERE (`col1` = 'kill' OR `col1` = 'teamkill') AND `uts_temp_".$uid."`.`col4` = '".$playerid."';");
		if (strlen($q_deaths['col4']))
			$r_deaths = $q_deaths['col4'];
	}

	if (isset($q_suicides) && isset($q_suicides['col4']) && strlen($q_suicides['col4']))
		$r_suicides = $q_suicides['col4'];
	else
	{
		$q_suicides = small_query("SELECT COUNT(`id`) AS `col4` FROM `uts_temp_".$uid."` WHERE `col1` = 'suicide' AND `col2` = '".$playerid."';");
		if (strlen($q_suicides['col4']))
			$r_suicides = $q_suicides['col4'];
	}
	if (Null != $r_kills)
		$r_frags = $r_kills;
	if (Null != $r_suicides)
		$r_frags = $r_frags - $r_suicides;
	if (Null != $r_teamkills)
		$r_frags = $r_frags - $r_teamkills;

	if (isset($q_acc) && isset($q_acc['col4']) && strlen($q_acc['col4']))
		$r_acc = get_dp($q_acc['col4']);
	else
	{
		$q_acc = small_query("SELECT AVG(`eff`) AS `eff`, AVG(`accuracy`) AS `accuracy`, `pid` FROM `uts_player` WHERE `pid` = '".$playerid2pid[$playerid]."' ORDER BY `matchid` DESC LIMIT 0, 10;");
		if (isset($q_acc) && isset($q_acc['accuracy']) && strlen($q_acc['accuracy']))
			$r_acc = get_dp($q_acc['accuracy']);
		else
			$r_acc = 10;
		if (isset($q_acc) && isset($q_acc['eff']) && strlen($q_acc['eff']))
			$r_efficiency = get_dp($q_acc['eff']);
		else
			$r_efficiency = 40;
	}
	if (isset($q_efficiency) && isset($q_efficiency['col4']) && strlen($q_efficiency['col4']))
		$r_efficiency = get_dp($q_efficiency['col4']);

	if (isset($q_tos) && isset($q_tos['col4']) && strlen($q_tos['col4']))
		$r_tos = get_dp($q_tos['col4']);
	else
	{
		$r_tos = 0;
		$q_tos = array('pcon'=>'','pdis'=>'','gstrt'=>'','gmend'=>''); // Build this from other data we have

		$q_nfo = small_query("SELECT `col0` AS `pcon` FROM `uts_temp_".$uid."` WHERE `col1` = 'player' AND `col2` = 'Connect' AND `col4` = '".$playerid."';");
		if (isset($q_nfo) && isset($q_nfo['pcon']) && strlen($q_nfo['pcon']))
			$q_tos['pcon'] = $q_nfo['pcon'];

		$q_nfo = small_query("SELECT `col0` AS `pdis` FROM `uts_temp_".$uid."` WHERE `col1` = 'player' AND `col2` = 'Disconnect' AND `col4` = '".$playerid."';");
		if (isset($q_nfo) && isset($q_nfo['pdis']) && strlen($q_nfo['pdis']))
			$q_tos['pdis'] = $q_nfo['pdis'];

		$q_nfo = small_query("SELECT `col0` AS `gstrt` FROM `uts_temp_".$uid."` WHERE `col1` = 'game_start';");
		if (isset($q_nfo) && isset($q_nfo['gstrt']) && strlen($q_nfo['gstrt']))
			$q_tos['gstrt'] = $q_nfo['gstrt'];

		$q_nfo = small_query("SELECT `col0` AS `gmend` FROM `uts_temp_".$uid."` WHERE `col1` = 'game_end';");
		if (isset($q_nfo) && isset($q_nfo['gmend']) && strlen($q_nfo['gmend']))
			$q_tos['gmend'] = $q_nfo['gmend'];

		if (isset($q_tos) && isset($q_tos['pdis']) && isset($q_tos['pcon']) && strlen($q_tos['pdis']) && strlen($q_tos['pcon']))
		{
			$r_tos = get_dp($q_tos['pdis']-$q_tos['pcon']);
		}
		elseif (isset($q_tos) && isset($q_tos['gmend']) && isset($q_tos['pcon']) && strlen($q_tos['pcon']) && strlen($q_tos['gmend']))
		{
			if ($q_tos['gmend']-$q_tos['pcon'] > 0)
				$r_tos = get_dp($q_tos['gmend']-$q_tos['pcon']);
		}
		if (isset($q_tos) && isset($q_tos['pcon']) && isset($q_tos['gstrt']) && strlen($q_tos['pcon']) && strlen($q_tos['gstrt']))
		{
			if (($q_tos['pcon'] > $q_tos['gstrt']) || (isset($q_tos['pdis']) && strlen($q_tos['pdis'])))
				$r_ttl = $r_tos;
			elseif ($q_tos['pcon'] < $q_tos['gstrt'] && strlen($q_tos['gmend']))
				$r_ttl = get_dp($q_tos['gmend']-$q_tos['gstrt']);
		}
	}

	if (isset($q_ttl) && isset($q_ttl['col4']) && strlen($q_ttl['col4']))
		$r_ttl = get_dp($q_ttl['col4']);

	if (isset($q_score) && isset($q_score['col4']) && strlen($q_score['col4']))
		$r_score = $q_score['col4'];
	else
		$r_score = $r_kills;

	// Generate player record
	$sql_playerid = "	INSERT 
							INTO		`uts_player`
							SET		`matchid` = '".$matchid."',
										`playerid` = '".$playerid."',
										`pid` = '".$pid."',
										`team` = '".$playerteam."',
										`gid` = '".$gid."',
										`insta` = '".$gameinsta."',
										`country` = '".$playercountry."',
										`ip` = '".$playerip."',

										`spree_double` = '".$q_spree_dbl."',
										`spree_multi` = '".$q_spree_mult."',
										`spree_ultra` = '".$q_spree_ult."',
										`spree_monster` = '".$q_spree_mon."',
										`spree_kill` = '".$q_spree_kill."',
										`spree_rampage` = '".$q_spree_rampage."',
										`spree_dom` = '".$q_spree_dom."',
										`spree_uns` = '".$q_spree_uns."',
										`spree_god` = '".$q_spree_god."',

										`pu_pads` = '".$pu_pads."',
										`pu_armour` = '".$pu_armour."',
										`pu_keg` = '".$pu_keg."',
										`pu_belt` = '".$pu_belt."',
										`pu_amp` = '".$pu_amp."',
										`pu_invis` = '".$pu_invis."',

										`lowping` = '".$lowping."',
										`highping` = '".$highping."',
										`avgping` = '".$avgping."',

										`accuracy` = '".$r_acc."',
										`frags` = '".$r_frags."',
										`deaths` = '".$r_deaths."',
										`kills` = '".$r_kills."',
										`suicides` = '".$r_suicides."',
										`teamkills` = '".$r_teamkills."',
										`eff` = '".$r_efficiency."',
										`gametime` = '".$r_tos."',
										`ttl` = '".$r_ttl."',
										`gamescore` = '".$r_score."';";

	$q_playerid = mysql_query($sql_playerid) or die("import_playerstuff query; (pt=".$playerteam.";f=".$r_frags.";sc=".$r_score."; s=\n".$sql_playerid.")\n".mysql_error()."\n");
	$playerecordid = mysql_insert_id() or die("import_playerstuff insert; (pt=".$playerteam.";f=".$r_frags.";sc=".$r_score."; s=\n".$sql_playerid.")\n".mysql_error()."\n");;


?>
