<?php 
// Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// always modified
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");

if (isset($_COOKIE['uts_lastvisit'])) {
	if (isset($_COOKIE['utss_lastvisit'])) {
		$s_lastvisit = $_COOKIE['utss_lastvisit'];
	} else {
		setcookie('utss_lastvisit', $_COOKIE['uts_lastvisit'], 0);
		$s_lastvisit = $_COOKIE['uts_lastvisit'];
	}
} else {
	$s_lastvisit = time();
}
setcookie('uts_lastvisit', time(), time()+60*60*24*30*365);
if (isset($_GET['theme'])) setcookie("utaTheme",$_GET['theme'],time()+2419200,"/utstats/",".utassault.net",0);
