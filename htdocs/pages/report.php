<?php 
$id = $_GET["id"];
$wid = $_GET["wid"];
$stage = $_GET["stage"];
$oururl = $_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
$oururl = str_replace("index.php", "", $oururl);
$rtype = $_GET["rtype"];

IF ($rtype == "clanbase") {
	include_once("pages/report_cb.php");
}

IF ($rtype == "bbcode") {
	include_once("pages/report/bbcode.php");
}

IF ($rtype == "clanbase" && $stage == "generate") {
	include_once("pages/report/clanbase.php");
}
?>
