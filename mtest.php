<?php
@ignore_user_abort(true);
@set_time_limit(0);
require ("includes/functions.php");
require ("includes/config.php");

// Debugging mode?
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : false;

// Output HTML?
$html = isset($_REQUEST['html']) ? $_REQUEST['html'] : true;

// Running from CLI?
if (php_sapi_name() == 'cli' or !isset($_SERVER['SERVER_PORT']) or !$_SERVER['SERVER_PORT'])
	$html = false;

if ($html) include ("includes/header.php");

$start_time = time();
if ($html) echo "<pre align=\"left\" style=\"text-align: left;\">";
echo "Start: ".$start_time."\r\n\r\n";
$sql = "DROP TABLE IF EXISTS `tut_players_per_match`;";
mysql_query($sql);
echo "---\r\n".$sql;

$sql = "CREATE TABLE `tut_players_per_match` (
		`players_id` INT NOT NULL ,
		`playerid` INT NOT NULL ,
		`pid` INT NOT NULL ,
		`matchid` INT NOT NULL ,
		`matchmode` TINYINT NOT NULL
	) TYPE = MYISAM ;";
mysql_query($sql);
echo "\r\n---\r\n".$sql;

// $sql = "INSERT INTO `tut_players_per_match` (`players_id`,`playerid`,`pid`,`matchid`,`matchmode`) VALUES (
$sql = "	SELECT `uts_player`.`id` AS players_id, `uts_player`.`playerid`, `uts_player`.`pid`,
		`uts_match`.`id` AS `matchid`, `uts_match`.`matchmode`
		FROM `uts_player` INNER JOIN `uts_match` ON (
			`uts_player`.`matchid` = `uts_match`.`id`
			);";
// 		);";
// 	WHERE `uts_match`.matchmode = 1;";
$query = mysql_query($sql);
while ($rs = mysql_fetch_object($query))
{
	$insql = "INSERT INTO `tut_players_per_match` (`players_id`,`playerid`,`pid`,`matchid`,`matchmode`) VALUES (".$rs->players_id.",".$rs->playerid.",".$rs->pid.",".$rs->matchid.",".$rs->matchmode.");";
	mysql_query($insql);
	// echo "\r\n- ".$insql;
}
echo "\r\n---\r\n".$sql;

$sql = "SELECT * FROM `tut_players_per_match` WHERE `matchmode` = 0;";
$query = mysql_query($sql);
echo "\r\n---\r\n".$sql;
while ($rs = mysql_fetch_object($query))
{
	$pid = $rs->pid;
	$sql = "DELETE FROM uts_killsmatrix WHERE matchid = ".$rs->matchid." AND killer = ".$rs->playerid.";";
	mysql_query($sql);
	$sql = "DELETE FROM uts_player WHERE pid = ".$pid.";";
	mysql_query($sql);
	$sql = "DELETE FROM uts_rank WHERE pid = ".$pid.";";
	mysql_query($sql);
	$sql = "DELETE FROM uts_pinfo WHERE id = ".$pid.";";
	mysql_query($sql);
	$sql = "DELETE FROM uts_weaponstats WHERE pid = ".$pid.";";
	mysql_query($sql);
}

$end_time = time();
echo "\r\nEnd: ".$end_time;
if ($html) "</pre>\r\n";
if ($html) include("includes/footer.php");
?>
