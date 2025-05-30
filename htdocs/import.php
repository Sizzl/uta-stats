<?php 
function add_info($name, $value) {
	if ($value == '' or $value === NULL) return('');
	return(htmlentities($name) ." ". htmlentities($value) ."<br />");
}

@ignore_user_abort(true);
@set_time_limit(0);
if (isset($_REQUEST['rememberkey'])) setcookie('uts_importkey', $_REQUEST['key'], time()+60*60*24*30*365);
if (isset($_COOKIE['uts_importkey'])) $adminkey = $_COOKIE['uts_importkey'];
global $rank_year;
include_once "includes/mysql-shim/lib/mysql.php";
require "includes/uta_functions.php";
require "includes/functions.php";
require "includes/config.php";

$compatible_actor_versions = array('beta 4.0', 'beta 4.1', 'beta 4.2', '0.4.0', '0.4.1', '0.4.2', '0.4.2a', '0.4.2b', '5.0');

// Get key from web browser
if (isset($_REQUEST['key'])) $adminkey = $_REQUEST['key'];
if (!isset($adminkey)) $adminkey = '';

// Were running from the command line (cron-jobs)
if (php_sapi_name() == 'cli' or !isset($_SERVER['SERVER_PORT']) or !$_SERVER['SERVER_PORT'])
{
	// Running from command line, translate args into REQUEST
	parse_str(implode('&', array_slice($argv, 1)), $_REQUEST);
	// No password needed when in cli mode.
	$adminkey = $import_adminkey;
	// There is no time limit when running the cli. And no page to reload :)
	$import_reload_after = 0;
	// No browser, no HTML
	$html = false;
	// Chdir to our homedir
	if (!empty($import_homedir)) chdir($import_homedir);
} else {
	$html = true;
}

// Debugging mode?
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : false;
$debugpid = isset($_REQUEST['debugpid']) ? $_REQUEST['debugpid'] : 0;
if (is_numeric($debugpid) && $debugpid > 0)
	$results['debugpid'] = $debugpid;

// Output HTML?
$html = isset($_REQUEST['html']) ? $_REQUEST['html'] : $html;

if ($html)
{
	if (!isset($_GET['noheader']))
	{
		if (isset($_SESSION['themelocation'])) // Themed header --// 19/07/05 Timo: Added customisable header (& sidebar !)
		{
			if (file_exists($_SESSION['themelocation']."header.php"))
				include $_SESSION['themelocation']."header.php";
			else
				include "includes/header.php";
		}
		else
			include "includes/header.php";
	}
}
if (isset($_GET['p']))
	$pagehandler = mks($_GET['p']);

if ($html)
{
	echo'<table border="0" cellpadding="1" cellspacing="2" width="720">
	<tr>
		<td class="heading" align="center" colspan="2">Importing Latest Log Files</td>
	</tr>';
}


if (empty($import_adminkey))
{
	if ($html) echo'<tr><td class="smheading" align="left" width="150">Error:</td><td class="grey" align="left">';
	echo "\$import_adminkey not set in config.php!\n";
	if ($html)
	{
		echo '</td></tr></table>';
		include("includes/footer.php");
	}
	return;
}

if (!empty($adminkey) && $adminkey != $import_adminkey)
{
	if ($html) echo'<tr><td class="smheading" align="left" width="150">Error:</td><td class="grey" align="left">';
	echo "Keys do not match\n";
	$adminkey = '';
	if (!$html) return;
}

if (empty($adminkey))
{
	if (!$html) die('Please provide the adminkey' ."\n");
	echo'<tr>
		  <td class="smheading" align="left" width="150">Enter Admin key:</td>
		  <td class="grey" align="left"><form NAME="adminkey" ACTION="import.php">
		  <input TYPE="text" NAME="key" MAXLENGTH="35" SIZE="20" CLASS="searchform">
		  <input TYPE="submit" VALUE="Submit" CLASS="searchformb">
		  <input TYPE="checkbox" NAME="rememberkey"> Remember the key
		  </form></td>
		</tr></table>';
	include("includes/footer.php");
	return;
}

if (!@is_dir('logs'))
{
	if ($html) echo'<tr><td class="smheading" align="left" width="150">Error:</td><td class="grey" align="left">';
	echo "Can't find the logs directory!\n";
	if ($html) echo "<br>";
	echo "Current working directory is: ". getcwd() ."\n";
	if ($html) echo "<br>";
	if (!$html) echo "You forgot to cd to my home directory? Take a look at \$import_homedir in config.php.\n";
	if ($html) {
		echo '</td></tr></table>';
		include("includes/footer.php");
	}
	return;
}

if ($html) echo'</table><br>';
echo "\n";

$start_time = time();
$files = isset($_REQUEST['files']) ? $_REQUEST['files'] : 0;
$elapsed = isset($_REQUEST['elapsed']) ? $_REQUEST['elapsed'] : 0;

if ($ftp_use && !isset($_GET['no_ftp']))
{
	include("includes/ftp.php");
	$elapsed = $elapsed - (time() - $start_time);
}

$logdir = opendir('logs');
$logfiles = array();
echo "Looking for logs...  \n";
while (false !== ($filename = readdir($logdir))) {
        if ($filename != ".." && $filename != ".")
                $logfiles[] = $filename;
}
unset($filename);
echo count($logfiles)." logs found. \n";
sort($logfiles);
echo "Log files sorted alphabetically.\n";

// while (false !== ($filename = readdir($logdir)))
foreach ($logfiles as $filename)
{
// Our (self set) timelimit exceeded => reload the page to prevent srcipt abort
	if (!empty($import_reload_after) && $start_time + $import_reload_after <= time())
	{
		if (!$html) die('Time limit exceeded - unable to reload page (no HTML output)' ."\n");

		$elapsed = $elapsed + time() - $start_time;
		$target = $PHP_SELF ."?key=". urlencode($adminkey) ."&amp;".str_rand()."=".str_rand()."&amp;no_ftp=1&amp;debug=$debug&amp;files=$files&amp;elapsed=$elapsed";
		echo '<meta http-equiv="refresh" content="2;URL='. $target .'">';

		echo'<br /><table border="0" cellpadding="1" cellspacing="2" width="720">
		  <tr>
			<td class="heading" align="center" colspan="2">Maximum execution time exeeded; restarting ...</td>
		  </tr>
		  </table>';

		include("includes/footer.php");
		return;
	}

	$oldfilename = $filename;
	$filename = 'logs/' . $filename;
	$backupfilename = 'logs/backup/' . $oldfilename;

	// UTDC log: Move to logs/utdc/
	if ($import_utdc_download_enable
		&& substr($filename, strlen($filename) - strlen($import_utdc_log_extension)) == $import_utdc_log_extension
		&& substr($oldfilename, 0, strlen($import_utdc_log_start)) == $import_utdc_log_start)
	{
		if ($import_utdc_log_compress == 'no') $import_utdc_log_compress = 'yes';
		if ($html)
		{
			echo'<table class="box" border="0" cellpadding="1" cellspacing="2">
				<tr>
					<td class="smheading" align="center" height="25" width="550" colspan="2">UTDC log: '.$oldfilename.'</td>
				</tr>
				<tr>
					<td class="smheading" align="left" width="350">';
		}
		else
		{
			echo "UTDC log: $oldfilename:\n";
		}
		echo 'Moving to logs/utdc/: ';
		if ($html) echo '</td><td class="grey" align="left" width="200">';
		echo backup_logfile($import_utdc_log_compress, $filename, 'logs/utdc/'.$oldfilename, true) . "\n";
		if ($html) echo '</td></tr></table><br />';
		echo "\n\n";
		unlink($filename);
		continue;
	}

	if(substr($filename, strlen($filename) - strlen($import_log_extension)) != $import_log_extension) 	continue;
	if(substr($oldfilename, 0, strlen($import_log_start)) != $import_log_start) continue;

	// Create a unique ID
	$uid = str_rand();

	// Check if there are any logs to do ...

	// Create our temp Table

	for (;;)
	{
		$sql = "CREATE ". ($import_use_temporary_tables ? 'TEMPORARY ' : '') ."TABLE `uts_temp_".$uid."` (`id` mediumint(5) NOT NULL, `col0` char(20) NOT NULL default '',";
		for ($c=1; $c < 21; $c++)
		{
			$sql = $sql."\n		`col".$c."` char(120) NULL default '',";
		}
		$sql = $sql."\n		KEY `part1` (`col1` (20),`col2` (20)),\n		KEY `part2` (`col0` (20),`col1` (20),`col2` (20)),\n		KEY `full` (`col0` (20),`col1` (20),`col2` (20),`col3` (20),`col4` (20),`col5` (20))) ENGINE=". ($import_use_heap_tables ? 'HEAP' : 'MyISAM') .";";

		$result = mysql_query($sql);
		if ($result) break;

		if (mysql_errno() == 1044 && $import_use_temporary_tables)
		{
			echo "<br><strong>WARNING: Unable to create temporary table (". mysql_error() .")<br>";
			echo "I'll retry without using MySQL's temporary table feature (see \$import_use_temporary_tables in config.php for details).<br><br></strong>";
			$import_use_temporary_tables = false;
			continue;
		}
		die("<br><strong>Unable to create the temporary table - are you allowed to create tables in this database?<br><br></strong><pre>".$sql."</pre>");
	}
	mysql_query("ALTER TABLE `uts_temp_".$uid."` ADD UNIQUE(`id`);");
	$id = 0;

	if ($html)
	{
		echo'<table class="box" border="0" cellpadding="1" cellspacing="2">
		<tr>
			<td class="smheading" align="center" height="25" width="550" colspan="2">Importing '.$oldfilename.'</td>
		</tr>
		<tr>
			<td class="smheading" align="left" width="350">';
	}
	else
	{
		echo "Importing $oldfilename:\n";
	}
	echo 'Creating Temp MySQL Table: ';
	if ($html) echo '</td><td class="grey" align="left" width="200">';
	echo "uts_temp_".$uid."\n";
	if ($html) echo '</td></tr><tr><td class="smheading" align="left" width="350">';
	echo 'Backing Up Log File: ';
	if ($html) echo '</td><td class="grey" align="left" width="200">';

	// Copy the file to backup folder first
	echo backup_logfile($import_log_backup, $filename, $backupfilename, true) . "\n";

	if ($html) echo '</td><tr><td class="smheading" align="left" width="350">';
	echo 'Player Data Moved to Temp MySQL: ';
	if ($html) echo '</td>';

	// Create sql for NGLog
	$row = 1;
	$handle = fopen("$filename", "r");
	while (($data = my_fgets($handle, 5000)) !== FALSE)
	{
		// if ($debug) debug_output('Raw input         ', $data);
		$data = preg_replace('/[\x00]/', '', $data);
		// if ($debug) debug_output('After preg_replace', $data);
		$data = explode("\t", $data);

		$num = count($data);
		$row++;
		if ($num > 1)
		{
			$ins = "INSERT INTO `uts_temp_".$uid."` (`id`";
			for ($c=0; $c < $num; $c++)
			{
				$ins = $ins.", `col".$c."`";
			}
			$ins = $ins.") VALUES ('".$id."'";
			for ($c=0; $c < $num; $c++)
			{
				$col = addslashes($data[$c]);
				$col = trim($col, " \n\r");
				$ins = $ins.", '".$col."'";
			}
			$ins = $ins.");";
			if ($debug) debug_output('Log2tmp SQL insert: ', $ins, false);
			$id++;
			mysql_query($ins) or die("log2tmp - ".mysql_error());
		}
	}
	while (($data = my_fgets($handle, 5000)) !== FALSE)
	{
		if ($debug) debug_output('Raw input         ', $data);
		$data = preg_replace('/[\x00]/', '', $data);
		if ($debug) debug_output('After preg_replace', $data);
		$data = explode("\t", $data);

		$num = count($data);
		$row++;
		for ($c=0; $c < 1; $c++)
		{

			$col0 = addslashes($data[0]);
			$col1 = addslashes($data[1]);
			$col2 = addslashes($data[2]);
			$col3 = addslashes($data[3]);
			$col4 = addslashes($data[4]);
			$col5 = addslashes($data[5]);

			$col0 = trim($col0, " \n\r");
			$col1 = trim($col1, " \n\r");
			$col2 = trim($col2, " \n\r");
			$col3 = trim($col3, " \n\r");
			$col4 = trim($col4, " \n\r");
			$col5 = trim($col5, " \n\r");

			$id++;
			mysql_query("INSERT INTO `uts_temp_".$uid."` VALUES ($id, '".$col0."', '".$col1."', '".$col2."', '".$col3."', '".$col4."', '".$col5."');") or die("log2tmp - ".mysql_error());
		}
	}
	fclose($handle);
	$files++;

	if ($html) echo'<td class="grey" align="left" width="200">';
	echo "Yes\n";

	$log_incompatible = false;
	$actor_version = 'unknown';
	$qm_logtype = small_query("SELECT `col3` FROM `uts_temp_".$uid."` WHERE `col1` = 'info' AND `col2` = 'Log_Standard';");
	if ($qm_logtype['col3'] == 'UTStats')
	{
		$qm_logversion = small_query("SELECT `col3` FROM `uts_temp_".$uid."` WHERE `col1` = 'info' AND `col2` = 'Log_Version';");
		$actor_version = $qm_logversion['col3'];
	}

	if (!in_array($actor_version, $compatible_actor_versions))
	{
		if ($import_incompatible_logs)
		{
			if ($html) echo '</td></tr><tr><td class="smheading" align="left" width="350" style="background-color: red;">';
			echo "WARNING: ";
			if ($html) echo '</td><td class="grey" align="left" width="200" style="background-color: red;">';
			echo "This logfile was created using an incompatible UTStats server actor version ($actor_version). You may experience strange results and/or bugs!\n";
		}
		else
		{
			$log_incompatible = true;
		}
	}
	// --// Timo @ 2009/02/19 - Filter out all non-league matches if toggle enabled
	if ($ftp_matchesonly==true)
	{
		$qm_matchmode = small_query("SELECT `col2` FROM `uts_temp_".$uid."` WHERE `col1` = 'ass_matchmode';");
		if ($qm_matchmode['col2'] != "True")
		{
			$log_incompatible = true;
			$actor_version = "League Matches Only (see import.php) - result: ".$qm_matchmode['col2'];
		}
		else
		{
			$tournament = 'True';
		}
		// Exception for Easter
		$qm_mutatorex = small_query("SELECT `col3` FROM `uts_temp_".$uid."` WHERE `col1` = 'game' AND `col2` = 'GoodMutator' AND (`col3` LIKE '%Easter Egg Hunt%' OR `col3` LIKE '%Halloween Hunt%')");
		if (isset($qm_mutatorex) && isset($qm_mutatorex['col3']) && strpos($qm_mutatorex['col3'],"Hunt") > 0)
		{
			$log_incompatible = false;
			$allow_solologs = true;
			unset($actor_version);
		}
	}

	if ($html) echo '</td></tr><tr><td class="smheading" align="left" width="350">';
	echo "Match Data Created: ";
	if ($html) echo '</td><td class="grey" align="left" width="200">';

	// Get the match table info
	$qm_time = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Absolute_Time'");
	$qm_zone = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'GMT_Offset'");
	$qm_servername = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Server_ServerName'");
	$qm_serverip = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'True_Server_IP'");
	$qm_serverport = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Server_Port'");
	$qm_gamename = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'GameName'");

	$qm_gamestart = small_query("SELECT col0 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'realstart'");
	$qm_gameend = small_query("SELECT col0 FROM uts_temp_".$uid." WHERE col1 = 'game_end'");

	$qm_insta = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'insta'");
// 	$qm_tournament = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'TournamentMode'");
	$qm_teamgame = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'TeamGame'");
	$qm_mapname = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'map' AND col2 = 'Title'");
	$qm_mapfile = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'map' AND col2 = 'Name'");
	$qm_frags = small_query("SELECT SUM(col4) AS frags FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'frags'");
	$qm_kills = small_query("SELECT SUM(col4) AS kills FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'kills'");
	$qm_suicides = small_query("SELECT SUM(col4) AS suicides FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'suicides'");
	$qm_deaths = small_query("SELECT SUM(col4) AS deaths FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'deaths'");
	$qm_teamkills = small_query("SELECT SUM(col4) AS teamkills FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'teamkills'");

	$qm_playercount = small_count("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'player' AND col2 = 'rename' GROUP BY col3");

	$s_frags = $qm_frags['frags'];
	$s_suicides = $qm_suicides['suicides'];
	$s_deaths = $qm_deaths['deaths'];

	$sql_mutators = "SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'GoodMutator'";
	$qm_mutators = "";
	$q_mutators = mysql_query($sql_mutators);
	while ($r_mutators = mysql_fetch_array($q_mutators))
	{
		$qm_mutators .= "".$r_mutators['col3'].", ";
	}

	// Add teamkills only if its a team game, else add them to kills total
	if ($qm_teamgame['col3'] == "True")
	{
		$s_kills = $qm_kills['kills'];
		$s_teamkills = $qm_teamkills['teamkills'];
	}
	else
	{
		$s_kills = $qm_kills['kills']+$qm_teamkills['teamkills'];
		$s_teamkills = 0;
	}
	// Check if anything happened, if it didnt stop everything now
	
	if ($qm_kills['kills'] == 0 && $qm_deaths['deaths'] == 0 && $s_suicides == -5)
	{  // CRATOS
		echo "No (Empty Match)\n";
		if ($html) echo '</td></tr>';
	}
	elseif ($qm_playercount < 2 && (!isset($allow_solologs) || $allow_solologs==false))
	{
		echo "No (Not Enough Players)\n";
		if ($html) echo '</td></tr>';
	}
	elseif ($log_incompatible)
	{
		echo "No (Logfile incompatible [created by UTStats $actor_version])\n";
		if ($html) echo '</td></tr>';
	}
	elseif ($import_ignore_if_gametime_less_than != 0 && ceil(($qm_gameend['col0'] - $qm_gamestart['col0']) / 60) < $import_ignore_if_gametime_less_than)
	{
		echo "No (game too short [". ceil(($qm_gameend['col0'] - $qm_gamestart['col0']) / 60) ." &lt; $import_ignore_if_gametime_less_than minutes])\n";
		if ($html) echo '</td></tr>';
	}
	else
	{
		$qm_serveran = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Server_AdminName'");
		$qm_serverae = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Server_AdminEmail'");
		$qm_serverm1 = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Server_MOTDLine1'");
		$qm_serverm2 = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Server_MOTDLine2'");
		$qm_serverm3 = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Server_MOTDLine3'");
		$qm_serverm4 = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'info' AND col2 = 'Server_MOTDLine4'");

		$qm_gameinfotl = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'TimeLimit'");
		$qm_gameinfofl = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'FragLimit'");
		$qm_gameinfogt = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'GoalTeamScore'");
		$qm_gameinfomp = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'MaxPlayers'");
		$qm_gameinfoms = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'MaxSpectators'");
		$qm_gameinfogs = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'GameSpeed'");
		$qm_gameinfout = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'UseTranslocator'");
		$qm_gameinfoff = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'FriendlyFireScale' LIMIT 0,1");
		$qm_gameinfows = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'game' AND col2 = 'WeaponsStay'");

		if (isset($qm_time) && isset($qm_time['col3']))
			$gametime = $qm_time['col3'];
		if (isset($qm_zone) && isset($qm_zone['col3']))
			$offset = $qm_zone['col3']; // --// Timo: 18/09/05
		$servername = addslashes($qm_servername['col3']);
		$serverip = $qm_serverip['col3'];
		$serverport = $qm_serverport['col3'];
		$gamename = addslashes($qm_gamename['col3']);
		$servergametime = get_dp($qm_gameend['col0'] - $qm_gamestart['col0']);
		if (isset($qm_tournament))
			$tournament = $qm_tournament['col3'];
		else
			$tournament = 'True'; // assume match mode if not present
		$teamgame = $qm_teamgame['col3'];
		$mapname = addslashes($qm_mapname['col3']);
		$mapfile = addslashes($qm_mapfile['col3']);

		// ************************************************************************************
		// CRATOS: Fix Servername
		// ************************************************************************************
		$q_sname = small_query("SELECT col2 FROM uts_temp_".$uid." WHERE col1 = 'ass_servername'");
		if ($q_sname!=NULL)
		{
			$servername = trim($q_sname['col2']);	
			$servername = addslashes($servername); // --// Timo 2020-08-23 (Yes, still fixing sh!t 15 years later)
		}
		else
		{		
			// try to get existing servername by ip/port
			$srv = small_query("Select servername from uts_match where serverip = '$serverip:$serverport' LIMIT 0,1");
			if ($srv!=Null) $servername = $srv['servername']; 
			else
			{ 
				// this is just for logfiles with an old SmartAS or DM/UTPure
				$servername = str_replace("[PURE]","",$servername);			
				$servername = str_replace("| StdAS |","",$servername);
				$servername = str_replace("| ProAS |","",$servername);
				$servername = str_replace("| iAS |","",$servername);			
				$servername = str_replace("| 2v2AS |","",$servername);
				$servername = str_replace("[LOCKED - PRIVATE]","",$servername);
				$servername = str_replace("[OPEN - PUBLIC]","",$servername);
				$servername = str_replace("[iG+] ","",$servername);
				$servername = trim($servername);
				$servername = addslashes($servername); // --// Timo 2020-08-23 (Yes, still fixing sh!t 15 years later)
			}			
		}		
		// $servername = str_replace("$servername","'","''"); // quick fix ' in server name --pinny
		
		
		// *******************************************************************************************************
		// CRATOS: Fix GameStart/RealGameStart thingy		
		// *******************************************************************************************************
		$qm_gamestartreg = small_query("SELECT col0 FROM uts_temp_".$uid." WHERE col1 = 'game_start'");	
		if ($qm_gamestartreg!=NULL)
			if ($qm_gamestart['col0'] < $qm_gamestartreg['col0'])
				$servergametime = get_dp($qm_gameend['col0'] - $qm_gamestartreg['col0']);
				
				
		// *******************************************************************************************************
		// CRATOS: Fix Timedilation
		// 
		// Mind: The InGameTimeStamp always is exact realtime, but the LogTimeStamp depends on Gamespeed/Timedilation
		// TimeDilation = Gamespeed * 1.1 (for all Multiplayergames except some custommaps?)
		// LogTimeStamp = IngameTimestamp * TimeDilation
		// 
		// For Assault Records this means: ScreenRecord = LogTimeStamp / TimeDilation!
		// *******************************************************************************************************		
		
		// Get Timedilation
		$gamespeed = floatval($qm_gameinfogs['col3']);	// default: 100
		$timedilation = $gamespeed * 1.1 / 100.0;			
				
		// Fix Servergametime
		$servergametime	= $servergametime / $timedilation;

		// ************************************************************************************
		// CRATOS: Fix Mapfile name 
		// ************************************************************************************
		if (substr($mapfile,-4) != ".unr") $mapfile = $mapfile . ".unr";

		// ************************************************************************************
		// Cratos: Check for duplicate logfile import
		// ************************************************************************************
		if (isset($offset) && isset($gametime)) // --// Timo: 18/09/05
			$gametime = offsetutdate($gametime,$offset);
		else
			$gametime = utdate($gametime);

		$duplicate = small_count("SELECT id FROM uts_match WHERE serverip='$serverip:$serverport' AND time='".$gametime."' AND mapfile='".$mapfile."'");
		if ($duplicate > 0 && !isset($processdupes))
		{
			echo "ERROR: DUPLICATE LOGFILE \nServer: $servername\nGame: $gametime \nIgnoring...";
			if ($html) echo '</td></tr>';
			// Delete Temp MySQL Table
			$droptable = "DROP TABLE `uts_temp_".$uid."`;";
			mysql_query($droptable) or die("tmp drop ".mysql_error());
			if ($html) echo'<tr><td class="smheading" align="left" width="350">';
			echo "Deleting Temp MySQL Table: ";
			if ($html) echo '</td><td class="grey" align="left" width="200">';
			echo "uts_temp_".$uid."\n";
			if ($html) echo '</td></tr></table><br />';
			echo "\n\n";
			// Delete log file
			unlink($filename);
			continue;
		}
		elseif ($duplicate > 0 && isset($processdupes))
		{
			$dupe_m = small_query("SELECT id FROM uts_match WHERE serverip='$serverip:$serverport' AND time='".$gametime."' AND mapfile='".$mapfile."'");
			$matchid = $dupe_m["id"];
		}

		// Lazy Hack for unknown gametypes
		$unknowngt = substr("$mapfile", 0, 3);	// Gets first 3 characters

		if ($unknowngt == "JB-")
		{
			$gamename = "JailBreak";
			$teamgame = 'True';
		}

		// Append insta to game if it was an insta game. Reset gid to re-force lookup.
		$gameinsta = 0;
		if ($qm_insta['col3'] == "True") { $gameinsta = 1; $gamename = "$gamename (insta)"; unset($gid); } else { $gameinsta = 0; }

		// CRATOS: Check for PRO matches. Reset gid to re-force lookup.
		if (intval($qm_gameinfoff['col3']) != 0) { $gamename = "$gamename (pro)"; unset($gid); }

		// Check wheter we want to override the gametype for this match
		// (Useful if we want a server to have separate stats for one server or if we want to
		// combine DM and TDM rankings or ...)

		// Read all rules
		if (!isset($overriderules))
		{
			$overriderules = array();
			$sql_overriderules = "SELECT `id`, `serverip`, `gamename`, `mutator`, `gid` FROM `uts_gamestype` ORDER BY `pri` ASC, `id` ASC;";
			$q_overriderules = mysql_query($sql_overriderules);
			while ($r_overriderules = mysql_fetch_array($q_overriderules))
			{
				$overriderules[$r_overriderules['id']]['serverip'] = $r_overriderules['serverip'];
				$overriderules[$r_overriderules['id']]['gamename'] = $r_overriderules['gamename'];
				$overriderules[$r_overriderules['id']]['mutator'] = $r_overriderules['mutator'];
				$overriderules[$r_overriderules['id']]['gid'] = $r_overriderules['gid'];
			}
		}

		// Check if one of our overriderules applies to this match. The first rule to fully match (ordered by `pri`ority) will be used.
		foreach($overriderules as $rule)
		{
			if (isset($rule['serverip']) && strlen($rule['serverip']) > 0)
				if ($rule['serverip'] != '*' && $rule['serverip'] != "$serverip:$serverport") continue;
			if (isset($rule['gamename']) && strlen($rule['gamename']) > 0)
				if ($rule['gamename'] != '*' && $rule['gamename'] != $gamename) continue;
			if (isset($rule['mutator']) && strlen($rule['mutator']) > 0)
				if ($rule['mutator'] != '*' && stristr($qm_mutators, $rule['mutator']) === false) continue;
			$gid = $rule['gid'];
			$r_gid = small_query("SELECT `gamename`, `name` FROM `uts_games` WHERE `id` = '".$gid."'"); // Check it's valid.
			if ($r_gid)
				$gamename = $r_gid['gamename'];
			else
				unset($gid);
			break;
		}

		if (!isset($gid))
		{
			// Get the unique ID of this gametype.
			// Create a new one if it has none yet.
			$r_gid = small_query("SELECT id FROM uts_games WHERE gamename = '".$gamename."'");
			if ($r_gid)
				$gid = $r_gid['id'];
			else
			{
				mysql_query("INSERT INTO `uts_games` SET `gamename` = '".$gamename."', `name` = '".$gamename."';") or die("game ins - ".mysql_error());
				$gid = mysql_insert_id();
			}
		}

		$qm_firstblood = small_query("SELECT col2 FROM uts_temp_".$uid." WHERE col1 = 'first_blood'");
		if (isset($qm_firstblood['col2']))
			$firstblood = addslashes($qm_firstblood['col2']);

		$serverinfo = "";
		if (isset($qm_serveran) && isset($qm_serveran['col3']))
			$serverinfo = $serverinfo."Admin: ".$qm_serveran['col3']."<br />";
		if (isset($qm_serverae) && isset($qm_serverae['col3']))
			$serverinfo = $serverinfo."Email: ".$qm_serverae['col3']." <br /><br />";
		if (isset($qm_serverm1) && isset($qm_serverm1['col3']))
			$serverinfo = $serverinfo."<u>MOTD</u><br />".$qm_serverm1['col3']."<br />";
		if (isset($qm_serverm2) && isset($qm_serverm2['col3']))
			$serverinfo = $serverinfo.$qm_serverm2['col3']."<br />";
		if (isset($qm_serverm3) && isset($qm_serverm3['col3']))
			$serverinfo = $serverinfo.$qm_serverm3['col3']."<br />";
		if (isset($qm_serverm4) && isset($qm_serverm4['col3']))
			$serverinfo = $serverinfo.$qm_serverm4['col3'];
		
		$serverinfo = addslashes($serverinfo);
		$gameinfo = "";
		if (isset($qm_gameinfotl) && isset($qm_gameinfotl['col3']))
			$gameinfo = $gameinfo.add_info('Time Limit:', $qm_gameinfotl['col3']);
		if (isset($qm_gameinfofl) && isset($qm_gameinfofl['col3']))
			$gameinfo = $gameinfo.add_info('Frag Limit:', $qm_gameinfofl['col3']);
		if (isset($qm_gameinfogt) && isset($qm_gameinfogt['col3']))
			$gameinfo = $gameinfo.add_info('Goal Team Score:', $qm_gameinfogt['col3']);
		if (isset($qm_gameinfomp) && isset($qm_gameinfomp['col3']))
			$gameinfo = $gameinfo.add_info('Max Players:', $qm_gameinfomp['col3']);
		if (isset($qm_gameinfoms) && isset($qm_gameinfoms['col3']))
			$gameinfo = $gameinfo.add_info('Max Specs:', $qm_gameinfoms['col3']);
		if (isset($qm_gameinfogs) && isset($qm_gameinfogs['col3']))
			$gameinfo = $gameinfo.add_info('Game Speed:', $qm_gameinfogs['col3']);
		if (isset($qm_gameinfout) && isset($qm_gameinfout['col3']))
			$gameinfo = $gameinfo.add_info('Translocator:', $qm_gameinfout['col3']);
		if (isset($qm_gameinfoff) && isset($qm_gameinfoff['col3']))
			$gameinfo = $gameinfo.add_info('Friendly Fire:', $qm_gameinfoff['col3']);
		if (isset($qm_gameinfows) && isset($qm_gameinfows['col3']))
			$gameinfo = $gameinfo.add_info('Weapon Stay:', $qm_gameinfows['col3']);
		if (isset($actor_version))
			$gameinfo = $gameinfo.add_info('UTStats Actor Version:', $actor_version);
		$gameinfo = addslashes($gameinfo);

		// Tidy Up The Info
		if (strlen($qm_mutators))
		{
			$mutators = substr($qm_mutators, 0, -2);		// remove trailing ,
			$mutators = un_ut($mutators);				// Remove Class and BotPack. etc
			$mutators = addslashes($mutators);
		}
		//$gametime = utdate($gametime);

		// Get Teams Info
		$sql_tinfo = "SELECT `col4` FROM `uts_temp_".$uid."` WHERE `col1` = 'player' AND `col2` = 'TeamName'  GROUP BY `col4` ORDER BY `col4` ASC";
		$q_tinfo = mysql_query($sql_tinfo) or die(mysql_error());

		$t0info = 0;
		$t1info = 0;
		$t2info = 0;
		$t3info = 0;

		while ($r_tinfo = mysql_fetch_array($q_tinfo)) {
		      if ($r_tinfo['col4'] == "Red") { $t0info = 1; }
		      if ($r_tinfo['col4'] == "Blue") { $t1info = 1; }
		      if ($r_tinfo['col4'] == "Green") { $t2info = 1; }
		      if ($r_tinfo['col4'] == "Gold") { $t3info = 1; }
		}

		// Get Teamscores
		$sql_tscore = "SELECT col2 AS team, col3 AS score FROM uts_temp_".$uid." WHERE col1 = 'teamscore'";
		$q_tscore = mysql_query($sql_tscore) or die(mysql_error());

		$t0score = 0;
		$t1score = 0;
		$t2score = 0;
		$t3score = 0;

		while ($r_tscore = mysql_fetch_array($q_tscore))
		{
			if ($r_tscore['team'] == "0") $t0score = $r_tscore['score'];
			if ($r_tscore['team'] == "1") $t1score = $r_tscore['score'];
			if ($r_tscore['team'] == "2") $t2score = $r_tscore['score'];
			if ($r_tscore['team'] == "3") $t3score = $r_tscore['score'];
		}

		if ($duplicate > 0 && isset($processdupes))
		{
			echo "Forced dupe processing (ID: $matchid)\n";

			// Clear weapon stats
			echo " - Clearing weapon stats (ID: $matchid)\n";
			mysql_query("DELETE FROM uts_weaponstats WHERE matchid = '".$matchid."';") or die("Clear WS ".mysql_error());
			// Clear kills matrix
			echo " - Clearing kills matrix (ID: $matchid)\n";
			mysql_query("DELETE FROM uts_killsmatrix WHERE matchid = '".$matchid."';") or die("Clear KM ".mysql_error());
			// Clear player matrix
			echo " - Clearing player matrix (ID: $matchid)\n";
			mysql_query("DELETE FROM uts_player WHERE matchid = '".$matchid."';") or die("Clear PS ".mysql_error());
			// Clear obj stats
			echo " - Clearing AS objective stats (ID: $matchid)\n";
			mysql_query("DELETE FROM uts_smartass_objstats WHERE matchid = '".$matchid."';") or die("Clear OS ".mysql_error());
		}
		else
		{
			// Insert Server Info Into Database
			if (!isset($s_frags))
				$s_frags = 0;
			if (!isset($s_kills))
				$s_kills = 0;
			if (!isset($s_teamkills))
				$s_teamkills = 0;
			if (!isset($s_suicides))
				$s_suicides = 0;
			if (!isset($s_deaths))
				$s_deaths = 0;
			$sql_serverinfo = "INSERT INTO uts_match (time, servername, serverip, gamename, gid, gametime, mutators, insta, tournament,	teamgame, mapname, mapfile, serverinfo, gameinfo, frags, kills, suicides, teamkills, deaths,
				t0, t1, t2, t3, t0score, t1score, t2score, t3score)
				VALUES ('".$gametime."', '".$servername."', '$serverip:$serverport', '".$gamename."', '".$gid."', '".$servergametime."', '".$mutators."', '".$gameinsta."', '".$tournament."',
				'".$teamgame."', '".$mapname."', '".$mapfile."', '".$serverinfo."', '".$gameinfo."', '".$s_frags."', '".$s_kills."', '".$s_suicides."', '".$s_teamkills."', '".$s_deaths."',
				$t0info, $t1info, $t2info, $t3info, $t0score, $t1score, $t2score, $t3score);";

			$q_serverinfo = mysql_query($sql_serverinfo) or die("Match Ins ".mysql_error().";SQL: ".$sql_serverinfo);
			$matchid = mysql_insert_id();			// Get our Match ID
			echo "Yes (ID: $matchid)\n";
		}
		if ($html) echo '</td></tr>';

		// ************************************************************************************
		// Cratos: Add FriendlyFireScale, Timedilation
		// ************************************************************************************
		$friendlyfirescale = intval(floatval($qm_gameinfoff['col3'])*100);
		$sql = "UPDATE uts_match SET 
				friendlyfirescale = '".$friendlyfirescale."',
				timedilation = '".$timedilation."' 
				WHERE id = '".$matchid."';";
		mysql_query($sql) or die("FF/TD update; ".mysql_error());		
		
		// ************************************************************************************
		// Cratos: Get Gametype specific match stuff done
		// ************************************************************************************
		if (substr($gamename,0,7) == "Assault")	
		{
			include("import/uta_import_ass_match.php"); 
		}

		// Process Player Stuff
		$playerid2pid = array(); // set in import_playerstuff.php
		$ignored_players = array();
		$imported_players = array();

		if ($html) echo '<tr><td class="smheading" align="left" width="350">';
		echo "Importing Players: ";
		if ($html) echo '</td><td class="grey" align="left" width="200">';

		// Get List of Player IDs and Process What They Have Done
		$sql_player = "SELECT DISTINCT col4 FROM uts_temp_".$uid." WHERE col1 = 'player' AND col2 = 'rename' AND col4 <> ''";
		$q_player = mysql_query($sql_player) or die(mysql_error());
		while ($r_player = mysql_fetch_array($q_player))
		{
			$playerid = $r_player['col4'];

			// Get players last name used
			$r_player2 = small_query("SELECT col3 FROM uts_temp_".$uid." WHERE col1 = 'player' AND col2 = 'rename' AND col4 = '".$playerid."' ORDER BY id DESC LIMIT 0,1");
			$playername = addslashes($r_player2['col3']);


			// Are they a Bot
			$r_player1 = small_query("SELECT col4 FROM uts_temp_".$uid." WHERE col1 = 'player' AND col2 = 'IsABot' AND col3 = '".$playerid."' ORDER BY id DESC LIMIT 0,1");
			$playertype = isset($r_player1['col4']) ? $r_player1['col4'] : false;
			// This player is a bot
			if ($playertype == 'True' && $import_ignore_bots) {
				$ignored_players[] = $playername;
				// We do not want to know who killed and who was killed by this bot...
				mysql_query("DELETE FROM uts_temp_".$uid." WHERE (col1 = 'kill' OR col1 = 'teamkill') AND (col2 = '".$playerid."' OR col4 = '".$playerid."');") or die(mysql_error());
				if ($html) echo "<span style='text-decoration: line-through;'>";
				echo "Bot:".$playername." ";
				if ($html) echo "</span>";
				continue;
			}

			// Get players last team
			$r_player3 = small_query("SELECT col4 FROM uts_temp_".$uid." WHERE col1 = 'player' AND col2 = 'TeamChange' AND col3 = '".$playerid."' ORDER BY id DESC LIMIT 0,1");
			$playerteam = isset($r_player3['col4']) ? $r_player3['col4'] : 255;

			$qc_kills = small_query("SELECT col4 FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'kills'AND col3 = '".$playerid."';");
			$qc_teamkills = small_query("SELECT col4 FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'teamkills' AND col3 = '".$playerid."';");
			$qc_deaths = small_query("SELECT col4 FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'deaths' AND col3 = '".$playerid."';");
			$qc_objs = small_query("SELECT count(id) AS assobjcount FROM uts_temp_".$uid." WHERE col1 = 'assault_obj' AND col2 = '".$playerid."';");
			
			// Player had no kills, deaths or teamkills, or objectives! => ignore 
			// 
			// CRATOS: only ignore him if his TimeOnServer is < 30sec
			// 
			$qc_time = small_query("SELECT col4 FROM uts_temp_".$uid." WHERE col1 = 'stat_player' AND col2 = 'time_on_server' AND col3 = '".$playerid."' LIMIT 0,1");
			if (isset($qc_time) && $qc_time != NULL) 
				$timeonserver = intval($qc_time['col4']);
			else
				$timeonserver = 0; 

			if (($playerteam > 7 || $playerteam) < 0 || (isset($qc_kills) && isset($qc_kills['col4']) && isset($qc_deaths) && isset($qc_deaths['col4']) && isset($qc_teamkills) && isset($qc_teamkills['col4']) && isset($qc_objs) && isset($qc_objs['assobjcount']) && $qc_kills['col4'] == 0 && $qc_deaths['col4'] == 0 && $qc_teamkills['col4'] == 0 && $qc_objs['assobjcount'] <= 0 && $timeonserver < 30))
			{
				if ($timeonserver < 10 || $servergametime > 60 || $playerteam > 7 || $playerteam < 0) 	
				{			
					$ignored_players[] = $playername;
					continue;
				}
			}

			// ************************************************************************************
			// Cratos: Get Authusers
			// ************************************************************************************
			include("import/uta_import_ass_authplayer.php"); // CRATOS

			// Process all the other player information
			include("import/import_playerstuff.php");

			// Process all the player pickup information
			include("import/import_playerpickups.php");

			if ($playerbanned)
			{
				// Banned players don't have a rank.
				mysql_query("DELETE FROM uts_rank WHERE pid = '".$pid."'");

				if ($import_ban_type == 2)
				{
					// We do not want to know who killed and who was killed by this banned player
					$ignored_players[] = $playername;
					mysql_query("DELETE FROM uts_temp_".$uid." WHERE (col1 = 'kill' OR col1 = 'teamkill') AND (col2 = '".$playerid."' OR col4 = '".$playerid."');") or die(mysql_error());
					if ($html) echo "<span style='text-decoration: line-through;'>";
					echo "Banned:$playername ";
					if ($html) echo "</span>";
					continue;
				}
			}

			// ************************************************************************************
			// Cratos: Get Gametype specific stuff done
			// ************************************************************************************
			if (substr($gamename,0,7) == "Assault")
			{ 
				include("import/import_ass.php");
				include("import/uta_import_ass_player.php");
			}

			if ($gamename == "Capture the Flag" || $gamename == "Capture the Flag (insta)") { include("import/import_ctf.php"); }
			if ($gamename == "Domination" || $gamename == "Domination (insta)") { include("import/import_dom.php"); }
			if ($gamename == "Tournament Team Game" || $gamename == "Tournament Team Game (insta)") { include("import/import_tdm.php"); }
			if ($gamename == "JailBreak" || $gamename == "JailBreak (insta)") { include("import/import_jailbreak.php"); }

			if (!isset($skip_ranking) || $skip_ranking==false)
			{
				// Do the rankings
				unset($rank_year); // all time ranking
				echo "(R: All";
				include("import/import_ranking.php");
				$rank_year = intval(substr($gametime,0,4));
				echo "; ".$rank_year.") ";
				include("import/import_ranking.php"); // repeat just for this year
			}

			if ($playerbanned)
			{
					if ($html) echo "<span style='font-style: italic;'>";
					echo "Banned:";
			}
			echo $playername.' ';
			if ($playerbanned && $html) echo "</span>";
			$imported_players[] = $playername;
		}
		if ($html) echo '</td></tr>';

		if ($html) echo '<tr><td class="smheading" align="left" width="350">';
		echo "\nBuilding damage tracking:";
		if ($html) echo '</td><td class="grey" align="left" width="200">';
		include("import/import_killsdetail.php");
		echo " Done\n";
		if ($html) echo "</td></tr>";

		// Check if theres any players left, if none or one delete the match (its possible ...)
		$final_pcount = small_count("SELECT id FROM uts_player WHERE matchid = $matchid");

		if ($final_pcount == NULL || ($final_pcount == 1 && (!isset($allow_solologs) || $allow_solologs==false)))
		{
			if ($html)
			{
				echo'<tr>
					<td class="smheading" align="left" width="350">Deleting Match:</td>
					<td class="grey" align="left" width="200">0 or 1 Player Entries Left</td>
				</tr>';
			}
			else
			{
				echo "Removing match info; 1 or less valid players remain. Override with \$allow_sololog\n";
			}
			$sql_radjust = "SELECT `pid`, `gid`, `rank` FROM `uts_player` WHERE `matchid` = '".$matchid."';";
			$q_radjust = mysql_query($sql_radjust) or die("Rank Sel ".mysql_error());
			while ($r_radjust = mysql_fetch_array($q_radjust)) {
				$pid = $r_radjust['pid'];
				$gid = $r_radjust['gid'];
				$rank = $r_radjust['rank'];

				$sql_crank = small_query("SELECT `id`, `rank`, `matches` FROM `uts_rank` WHERE `pid` = '".$pid."' AND `gid` = '".$gid."';");
				if (!$sql_crank) continue;

				$rid = $sql_crank['id'];
				$newrank = $sql_crank['rank']-$rank;
				$oldrank = $sql_crank['rank'];
				$matchcount = $sql_crank['matches']-1;

				mysql_query("UPDATE `uts_rank` SET `rank` = '".$newrank."', `prevrank` = '".$oldrank."', `matches` = '".$matchcount."' WHERE `id` = '".$rid."';") or die("Rank Upd ".mysql_error());
			}
			mysql_query("DELETE FROM `uts_rank` WHERE `matches` = '0';") or die(mysql_error());

			$rem_mrecord = "DELETE FROM `uts_match` WHERE `id` = '".$matchid."';";
			mysql_query($rem_mrecord);
			$rem_precord = "DELETE FROM `uts_player` WHERE `matchid` = '".$matchid."';";
			mysql_query($rem_precord);
			// CRATOS
			$rem_objstats = "DELETE FROM `uts_smartass_objstats` WHERE `matchid` = '".$matchid."';";
			mysql_query($rem_objstats);
		}
		else
		{
			// Make our weapons statistics
			if ($html) echo '<tr><td class="smheading" align="left" width="350">';
			echo "Importing weapon statistics: ";
			if ($html) echo '</td><td class="grey" align="left" width="200">';
			include("import/import_weapons.php");
			echo "Done\n";
			if ($html) echo '</td></tr>';

			// Make our kills matrix stuff ...
			if ($html) echo '<tr><td class="smheading" align="left" width="350">';
			echo "Building kills matrix: ";
			if ($html) echo '</td><td class="grey" align="left" width="200">';
			include("import/import_killsmatrix.php");
			echo "Done\n";

			if ($html) echo '</td></tr><tr><td class="smheading" align="left" width="350">';
			echo "Combining Duplicate Player Entries: ";
			if ($html) echo '</td><td class="grey" align="left" width="200">';

			// Combine duplicate player entries ... very intensive :(
			include("import/import_pcleanup.php");

			echo "Done\n";
			if ($html) echo'</td></tr>';


			$updategameinfo = false;
			if (count($ignored_players) > 0) {
				// Maybe we imported the player and ignored another record of him?
				$ignored_players = array_unique($ignored_players);
				foreach($ignored_players as $t_id => $t_name) {
					if (in_array($t_name, $imported_players)) unset($ignored_players[$t_id]);
				}
				if (count($ignored_players) > 0) {
					$gameinfo .= addslashes(add_info('Ignored Players:', implode(', ', $ignored_players)));
					$updategameinfo = true;
				}
			}
			if ($updategameinfo) {
				mysql_query("UPDATE `uts_match` SET `gameinfo` = '".$gameinfo."' WHERE `id` = '".$matchid."';");
				$updategameinfo = false;
			}
		}
	}

	// Delete Temp MySQL Table
	$droptable = "DROP TABLE `uts_temp_".$uid."`;";
	mysql_query($droptable) or die(mysql_error());

	if ($html) echo'<tr><td class="smheading" align="left" width="350">';
	echo "Deleting Temp MySQL Table: ";
	if ($html) echo '</td><td class="grey" align="left" width="200">';
	echo "uts_temp_".$uid."\n";
	if ($html) echo '</td></tr></table><br />';
	echo "\n\n";

  // Clear variables

	$asscode = "";
	$assteam = "";
	$asswin = "";
	$avgping = "";
	$data = "";
	$domplayer = "";
	$droptable = "";
	$firstblood = "";
	$gameinfo = "";
	$gameinsta = "";
	$gamename = "";
	$gametime = "";
	$highping = "";
	$unknowngt = "";
	$lowping = "";
	$mapname = "";
	$mapfile = "";
	$matchid = "";
	$mutators = "";
	$num = "";
	$playerid = "";
	$playerfragscnt = "";
	$playername = "";
	$playerecordid = "";
	$playerteam = "";
	$qm_mutators = "";
	$row = 1;
	$servername = "";
	$serverinfo = "";
	$serverip = "";
	$serverport = "";
	$suicidecnt = "";
	$t0info = "";
	$t1info = "";
	$t2info = "";
	$t3info = "";
	$t0score = "";
	$t1score = "";
	$t2score = "";
	$t3score = "";
	$teamgame = "";
	$tournament = "";

// Delete log file
	unlink($filename);
}
closedir($logdir);

if ($html) echo '<br />';
echo "\n";

// Import stats
if ($files != 0)
{
	$elapsed = $elapsed + time() - $start_time;
	if ($html) echo '<p class="pages">';
	echo "Processed $files ". ($files == 1 ? 'file' : 'files') ." in $elapsed ". ($elapsed == 1 ? 'second' : 'seconds') ." ";
	echo "(". get_dp($elapsed / $files) ." seconds/file)\n";
	if ($html) echo '</p><br />';
}


// Optimise database
if (rand(0, 5) == 0)
{
	if ($html) echo '<p class="pages">';
	echo "Optimizing tables... ";
	mysql_query("OPTIMIZE TABLE `uts_match`, `uts_player`, `uts_rank`, `uts_killsmatrix`, `uts_weaponstats`, `uts_pinfo`;") or die(mysql_error());
	echo "Done\n";
	if ($html) echo '</p>';
}

// Analyze Key distribution
if (rand(0, 10) == 0)
{
	if ($html) echo '<p class="pages">';
	echo "Analyzing tables... ";
	mysql_query("ANALYZE TABLE `uts_match`, `uts_player`, `uts_rank`, `uts_killsmatrix`, `uts_weaponstats`, `uts_pinfo`;") or die(mysql_error());
	echo "Done\n";
	if ($html) echo '</p>';
}


// Purge old logs
if ($purged = (purge_backups('logs/backup', $import_log_backups_purge_after)))
{
	if ($html) echo '<p class="pages">';
	echo "Purged $purged old logfiles\n";
	if ($html) echo '</p>';
}

// Purge old utdc logs
if ($import_utdc_download_enable)
{
	if ($purged = (purge_backups('logs/utdc', $import_utdc_log_purge_after)))
	{
		if ($html) echo '<p class="pages">';
		echo "Purged $purged old UTDC logfiles\n";
		if ($html) echo '</p>';
	}
}

echo "\n\n";

// Debugging output
//  Use $results['debugpid'] to define a player ID to debug output for
if ($debug) {
	if ($html) echo '<pre>';
	echo $s_debug;
	if ($html) echo '</pre>';
}
if ($html) echo '<br /><table border="0" cellpadding="1" cellspacing="2" width="720"><tr><td class="heading" align="center" colspan="2">';
echo "Import Script Completed\n";
if ($html) echo '</td></tr></table>';

if ($html)
{
	if (isset($_SESSION['themelocation'])) // Themed footer --// 19/07/05 Timo: Added customisable footer
	{
		if (file_exists($_SESSION['themelocation']."footer.php"))
			include $_SESSION['themelocation']."footer.php";
		else
			include "includes/footer.php";
	}
	else
		include "includes/footer.php";
}
