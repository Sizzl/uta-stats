<?php 
$id = $_GET["id"];
$wid = $_GET["wid"];
$stage = $_GET["stage"];
$oururl = $_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
$oururl = str_replace("index.php", "", $oururl);
$rtype = $_GET["rtype"];

if ($rtype == "clanbase") {
	include_once("pages/report_cb.php");
}

if ($rtype == "bbcode") {
	include_once("pages/report/bbcode.php");
}

if ($rtype == "clanbase" && $stage == "generate") {
	include_once("pages/report/clanbase.php");
}
?>
