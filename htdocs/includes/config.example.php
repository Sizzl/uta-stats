<?php
// Server Timezone Offset from GMT
$timezoneoffset = 1;
$ftp_add_tz = true; // Add per-ftp offsets to log files
$ftp_matchesonly = true; // Only log league matches (LeagueAS only)
$home_gamesummary = false; // Enable / disable the game summary page.

// Database connection details
$dbname = "utstats";
$hostname = "localhost";
$uname = "sqluser";
$upass = "sqlpass";

$dbconnect = mysql_connect($hostname,$uname,$upass);
mysql_set_charset('latin1',$dbconnect);
mysql_query('SET NAMES \"latin1\"',$dbconnect);
$dbconnect2 = mysql_select_db($dbname);

// Admin options --// 20/07/05 Timo: Added $showAllTables for admin/main.php
// 		 --// 20/07/05 Timo: Added $allowEmptyDB for admin/main.php
$showAllTables = true;
$allowEmptyDB = false;

// Globally ignored players (doesn't affect �egistered players)
$ignored = array('127.0.0.1','192.168.0.1');

// Assault game class list & parameters --// 25/07/05 Timo: Added $asclasses array for record zone filtering (+more).
unset($asclasses);
$asclasses[] = array("id"=>"1",
	"short"=>"StdAS","desc"=>"Standard Assault","mutators"=>"",
	"insta"=>"0","teamsize"=>"6","min_teamsize_rec"=>"4","friendlyfirescale"=>"0");
$asclasses[] = array("id"=>"2",
	"short"=>"ProAS","desc"=>"Pro Assault","mutators"=>"",
	"insta"=>"0","teamsize"=>"4","min_teamsize_rec"=>"3","friendlyfirescale"=>"100");
$asclasses[] = array("id"=>"3",
	"short"=>"iAS","desc"=>"InstaGIB Assault","mutators"=>"InstaGIB Assault",
	"insta"=>"1","teamsize"=>"4","min_teamsize_rec"=>"3","friendlyfirescale"=>"0");
 $asclasses[] = array("id"=>"4",
 	"short"=>"2v2AS","desc"=>"2vs2 Assault","mutators"=>"Impact Hammer Launch Protection","insta"=>"0","teamsize"=>"2","friendlyfirescale"=>"0");
// $asclasses[] = array("id"=>"6","short"=>"turboAS","desc"=>"turbo Assault","mutators"=>"","insta"=>"0","teamsize"=>"5","friendlyfirescale"=>"0");

// New IP 2 Country table Config --// 20/07/05 Timo: Variable for table name of IP->Country data
  $iptc = array("table"=>"ipToCountry",
		"cfield"=>"lower(country_code2)",
		"tfield"=>"ip_to",
		"ffield"=>"ip_from",
		"ifield"=>"ip_cidr");

// Theme location --// 10/07/05 Timo: Started basic theme selection
// 		  --// 19/07/05 Timo: Moved theme information to uta_themes.php
include ("uta_themes.php");
$theme_header = "<p align=\"center\">&nbsp; &nbsp; Welcome to utassault.net's UT Stats<br />";


// Get lastupdate time --// 10/07/05 Timo: Get the last update time from the ftptimestamp.php file
$lastupdate = file_get_contents("includes/ftptimestamp.php");

$tzoffsetcalc = mktime(1-$timezoneoffset,0,0,1,1,1970);
$lastupdate = $lastupdate + $tzoffsetcalc;

// Configure table matrix (for archiving purposes)
$t_prefix = "uts_";
$d_prefix = $t_prefix;
if (isset($_GET['archive'])) $_SESSION['archive'] = $_GET['archive'];

if (isset($_SESSION['archive'])) {
	$a_test = $_SESSION['archive'];
	if (is_numeric($a_test)) {
		if ($a_test >= 2012 && $a_test < intval(date("Y"))) {
			$a_value = date("y",mktime(12,0,0,1,1,$a_test));
			$t_prefix = "uts".$a_value."_";
		} else {
			unset($_SESSION['archive']);
			$a_value = "";
			unset($a_test);
			$t_prefix = $t_prefix;
		}
	} else {
		unset($_SESSION['archive']);
		$a_value = "";
		unset($a_test);
		$t_prefix = $t_prefix;
	} 
}

global $t_discord_players,$t_discord_teams,$t_games,$t_gamestype,$t_killsmatrix,$t_match,$t_pinfo,$t_player,$t_rank,$t_smartass_objs,$t_smartass_objstats,$t_weapons,$t_weaponstats;

$t_discord_players = $d_prefix."discord_players";
$t_discord_teams = $d_prefix."discord_teams";
$t_games = $d_prefix."games";
$t_gamestype = $d_prefix."gamestype";
$t_killsmatrix = $t_prefix."killsmatrix";
$t_match = $t_prefix."match";
$t_pinfo = $t_prefix."pinfo";
$t_player = $t_prefix."player";
$t_rank = $t_prefix."rank";
$t_smartass_objs = $d_prefix."smartass_objs";
$t_smartass_objstats = $d_prefix."smartass_objstats";
$t_weapons = $d_prefix."weapons";
$t_weaponstats = $t_prefix."weaponstats";

// The key needed to run the import script
$import_adminkey = 'adminkey';

// When runnning from the command-line (cron jobs):
// The absolute path to UTStats's home directory.
// Only needed if you're starting the importer from another directory
// Leave emtpy if unsure
$import_homedir = '/home/webuser/web/utstats/';

// Use the MySQL temporary tables feature?
// Available since MySQL 3.23 - requires CREATE TEMPORARY TABLE privilege since 4.0.2
$import_use_temporary_tables = true;		// set to true if available

// Use temporary heap tables?
// This will (at any cost) keep the entire table in RAM and may speed and/or fuck things up
$import_use_heap_tables = false;				// DON'T USE IF YOU DON'T KNOW WHAT YOU'RE DOING!

// Log files start with...
$import_log_start = "Unreal.ngLog";

// Log files end with...
$import_log_extension = ".log"; // --// 05/07/05 - Timo: Defunct; now uses ftp_log_extension[$array] for compressed logs

// How to backup logfiles?
// Possible values: yes      - move logfiles to the backup directory
//                  no       - don't make a backup. The file will be lost after it was imported
//                  compress - will compress the logfile and move it to the backup directory
//                             It'll first try bzip2 compression, then gzip (your php must be
//                             compiled to support these)
//                             If both fail, it will backup the uncompressed log
//                  gzip     - same as compress but will only try to gzip the file
$import_log_backup = "compress";

// Purge old logs after x days. 0 to disable.
$import_log_backups_purge_after = 0;


// After how many seconds should we reload the import page?
// This is to prevent the 'maximum execution time exeeded' error. It will reload
// the page after the amount of seconds you specify in order to bypass php's time limit.
// Set to 0 to disable (f.e. if your php does not run in safe mode)
$import_reload_after = 22;

// Ignore bots and bot kills/deaths?
$import_ignore_bots = true;

// How to deal with banned players?
// 1 - (recommended) import the player and display him/her on matchpages (without values :D)
//     but don't include him/her in rankings and don't allow to show individual player stats
//     You may unban a player banned with this setting and all stuff will display again
// 2 - don't import at all
//     will lead to 'strange' results on matchpages because kills of and against this player
//     won't be shown; efficiency etc. will be calculated including these kills though.
$import_ban_type = 1;

// Try to import logs from previous versions of UTStats
// Set this to true and you'll probably some strange results - You've been warned ;)
$import_incompatible_logs = false;

// Don't import if the gametime was less than x minutes. Set to 0 to import all logs.
$import_ignore_if_gametime_less_than = 0;

// UTStats can download and manage your UTDC logs
// Enable downloading of UTDC logs?
$import_utdc_download_enable = false;

// Log files start with...
$import_utdc_log_start = "[UTDC]";

// Log files end with...
$import_utdc_log_extension = ".log";

// Compress UTDC logfiles after download? [compress/gzip/no]
// (see $import_log_backup for available options)
$import_utdc_log_compress = "compress";

// Purge old UTDC logs after x days. 0 to disable.
$import_utdc_log_purge_after = 0;

// Enable the creation of pictures? (Signature pictures for users where they can see their current ranking and stuff)
// Requires GD- and FreeType support.
// see config_pic.php for picture configuration options
$pic_enable = true;


// FTP Connection Details --// Altered for dynamic use Timo 01/07/05
$ftp_interval = 0;			    	// How often in minutes to allow stats update
$ftp_type = 'sockets';				// Which FTP module do you want to use?
						// sockets - (recommended)
						//           Use PHP's socket extension to connect to the FTP server
						//           will fallback to 'pure' if no sockets available
						// pure    - Use fsockopen() to connnect to the FTP server
						//           should work with any php version
						// php     - Use PHP's FTP extension (must be compiled in)
						//           Debugging will not be available with this module and
						//           error handling may not be as good as with the other modules

$ftp_debug = false;				// Debugging output that may help you to resolve ftp problems

$ftp_use = false;			      	// Whether to auto get the log files

// Dynamic FTP Details via MySQL --// Timo @ 01/07/05
$i = 0;
$ftpsql = "SELECT * FROM x_ftpservers WHERE enabled = '1' ORDER BY id";
$ftpquery = mysql_query($ftpsql,$dbconnect);
if (mysql_num_rows($ftpquery))
{
	$ftp_use = true;		      	// Whether to auto get the log files
	while ($rs = mysql_fetch_object($ftpquery))
	{
		$ftp_servername[$i]	= $rs->servername;	// Timo: Server Name
		$ftp_utaid[$i]		= $rs->utaid;		// Timo: UTA Server ID (if known)

		$ftp_hostname[$i]	= $rs->ftp_hostname;	// FTP server location here
		$ftp_port[$i] 		= $rs->ftp_port;	// FTP Port - do not remove this even if you do not use ftp
								// Do not add '' around the port either
		$ftp_uname[$i] 	= $rs->ftp_uname;		// FTP Username
		$ftp_upass[$i] 	= $rs->ftp_upass;		// FTP Password

		unset($allftpdir);
		$allftpdir = $rs->ftp_dir;

		unset($splitdir);
		$splitdir = explode("\r\n",$allftpdir);
		for ($j=0;$j<count($splitdir);$j++)
		{
			$ftp_dir[$i][] 	= $splitdir[$j];	// Directory of the log files - MUST NOT end with a /
		}
		if ($rs->ftp_passive==1)
			$ftp_passive[$i] 	= true;		// Use passive transfer mode for this connection?
		else
			$ftp_passive[$i] 	= false;
		if ($rs->ftp_delete==1)
			$ftp_delete[$i] 	= true;		// Delete logs after download?
		else
			$ftp_delete[$i] 	= false;		// Delete logs after download?
		if ($rs->ftp_logext!=".log")
			$ftp_log_extension[$i] = $rs->ftp_logext;
		else
			$ftp_log_extension[$i] = ".log";

		if ($rs->gmt_offset)
			$ftp_offset[$i]		= $rs->gmt_offset;
		else
			$ftp_offset[$i]		= 0;
			
		$i++;
	}
	$i--;
}
