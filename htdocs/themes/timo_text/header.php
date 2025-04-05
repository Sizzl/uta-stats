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


echo'
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
  <title>Unreal Tournament Stats - Powered by UTStats</title>
  <meta http-equiv="Content-Type" content="text/html; CHARSET=iso-8859-1" />
  <link rel="icon" href="'.$theme.'images/favicon.ico" type="image/ico" />
  <link rel="stylesheet" href="'.$theme.'style.css" />
  <link rel="stylesheet" href="'.$theme.'archive.css" />
  <script type="text/javascript">
  <!--
    var ol_fgclass="dark"; var ol_bgclass="darkbox"; var ol_textfontclass="dark"; var ol_captionfontclass="hlheading";
  -->
  </script>
  <script type="text/javascript" src="includes/overlib_mini.js"><!-- overLIB (c) Erik Bosrup --></script>
</head>
<body>
<div class="pagebody">
<div id="navbar" style="font-weight: bold;">UTA Stats. Last updated: '.date("d/m/Y @ H:i:s",$lastupdate).' </div>
<br /><br />
<hr />
<hr />
<table width="100%" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="120" nowrap="nowrap" valign="top" align="left">
			<div class=post>
				<div class=posttop>
					<div class="username">Navigation:&gt;</div>
					<div class=date>&nbsp;</div>
				</div>
				<div class=posttext>';
	if ($_SESSION["customsidebar"]=="1")
		include("sidebar.php");
	else
		include("./includes/sidebar.php");
echo '				</div>
			</div>
			<hr /></td>
		<td width="10" style="width: 10px;">&nbsp;&nbsp;</td>
		<td valign="top" align="center" width="85%">';
?>
