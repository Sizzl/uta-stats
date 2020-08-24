<?php 	// Cratos: Importing Authuser

	$qm_auth = small_query("SELECT col2 as pid, col3 as authuser, col4 as vcode 
							FROM uts_temp_$uid WHERE col1 = 'uta_auth_user' AND col2 = $playerid 
							ORDER BY vcode DESC LIMIT 0,1");
	
	$authuser = ""; $authpid=0; $vcode=0;  
	if ($qm_auth!=Null)
	{
		$authuser = addslashes(trim($qm_auth[authuser]));
		$authpid = $qm_auth[pid];
		$vcode = $qm_auth[vcode];
	}
	else
	{
		$authuser = "";
		$vcode = 0;				
	}
		
	// VCode:
	// 0: not verified
	// 1: Player verified, Clan not verified
	// 2: Player verified and Clan verified
	if ($vcode > 0 && $authuser!="")
	{
		// Authuser: Add an '®' at end of playername
		$playername = $authuser . "®";	
	}
	else
	{ 
		// NO Authuser: don't allow '®' at end of playername (replace with 'R')
		if (substr($playername,-1) == "®") $playername = substr($playername,0,-1) . "R"; 
	}																
	
?>
