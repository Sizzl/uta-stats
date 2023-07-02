<?php 
	// --// Feb 2022 Sizzl: Introducing damage attribution tracking

	$sql_killdata = "SELECT 
			`col2` AS `victim`,
			`col3` AS `instigator`,
			`col4` AS `weapon`,
			`col5` AS `damage`,
			`col6` AS `damagehp`,
			`col7` AS `htime`,
			`col8` AS `ftime`,
			`col9` AS `varmour`,
			`col10` AS `vhealth`,
			`col11` AS `vparmour`,
			`col12` AS `vphealth`,
			`col13` AS `damagecap`
		FROM `uts_temp_".$uid."`
		WHERE `col1` = 'damage_tracker' ORDER BY `col8`, `col7`;";

	$q_killdata = mysql_query($sql_killdata) or die(mysql_error());
	while ($r_killdata = mysql_fetch_array($q_killdata)) {
		$frag = 0;
		$dcap = $r_killdata['damage'];
		if ($r_killdata['damage'] >= $r_killdata['vphealth'] && $r_killdata['htime'] == $r_killdata['ftime'])
		{
			$frag = 1;
			if (strlen($r_killdata['damagecap']))
				$dcap = $r_killata['damagecap'];
			else
				$dcap = min($r_killdata['vphealth']+$r_killdata['vparmour'],$r_killdata['damage']); // doesn't take into account ArmorAbsorption ratios, but is good enough
		}
		$sql =	"INSERT INTO uts_killsdetail (`matchid`,`victim`,`instigator`,`weapon`,`hit_time`,`hit_time_final`,`damage`,`damage_hp`,`damage_capped`,`victim_armour`,`victim_health`,`frag`)
			VALUES ('".$matchid."',
				'".$r_killdata['victim']."',
				'".$r_killdata['instigator']."',
				'".$r_killdata['weapon']."',
				'".$r_killdata['htime']."',
				'".$r_killdata['ftime']."',
				'".$r_killdata['damage']."',
				'".$r_killdata['damagehp']."',
				'".$dcap."',
				'".$r_killdata['varmour']."',
				'".$r_killdata['vhealth']."',
				'".$frag."');";
		mysql_query($sql) or die("import_kd: ".mysql_error()."\n".$sql);
	}
?>