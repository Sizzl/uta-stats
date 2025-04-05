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
  <meta http-equiv="Content-Type" content="text/html; CHARSET=iso-8859-1">
  <link rel="icon" href="images/favicon.ico" type="image/ico">
  <link rel="stylesheet" href="'.$theme.'style.css">
  <script language="JavaScript" type="text/JavaScript" src="includes/loader.js"></script>
  <script type="text/javascript">
  <!--
    var ol_fgclass="dark"; var ol_bgclass="darkbox"; var ol_textfontclass="dark"; var ol_captionfontclass="hlheading";
  -->
  </script>
  <script type="text/javascript" src="includes/overlib_mini.js"><!-- overLIB (c) Erik Bosrup --></script>
</head>
<body onload="remove_loading();">
<div id="loader_container">
	<div id="loader">

		<div align="center">Loading Statistics.<br>
		Please wait ...</div>
		<div id="loader_bg"><div id="progress"> </div></div>
	</div>
</div>
<table class="main" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="top" class="topbar"><img src="'.$theme.'images/top-logo.gif" width="400" height="55" alt="UTA Stats" /></td>
		</tr>
		<tr>
			<td valign="top" class="greybar"><img src="'.$theme.'images/greyxh.gif" width="122" height="2" alt="+" /><img src="'.$theme.'images/spacer.gif" width="678" height="2" alt="-" /></td>
		</tr>
		<tr>
			<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" alt="Top U logo and strapline">
			<tr>
				<td width="19" valign="top"><img src="'.$theme.'images/spacer.gif" width="19" height="1" alt=" " /></td>
				<td width="103" valign="top"><a href="/"><img src="'.$theme.'images/sb_topu.gif" width="103" height="70" border="0" alt="UTAssault.net Home" /></a></td>
				<td class="txtbigtitle" valign="middle">
				<p align="center">&nbsp; &nbsp; Welcome to PUG Stats - <a class="txtbigtitle" href="irc://bacon.utassault.net/utapug" target="_blank"></a>#utapug<br />
				<span class="rangtext">[Stats last updated : '.date("d/m/Y @ H:i:s",$lastupdate).']</span></p>
				</td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" alt="Main body">
			<tr>
				<td width="160"><img src="'.$theme.'images/spacer.gif" width="160" height="2" alt=" " /></td>
				<td><img src="'.$theme.'images/spacer.gif" width="640" height="2" alt=" " /></td>
			</tr>
			<tr>
				<td width="160" valign="top">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" alt="Side Bar">
					<tr>
						<td width="7"><img src="'.$theme.'images/spacer.gif" width="7" height="20" alt=" " /></td>
						<td width="12" valign="top" align="left"><img src="'.$theme.'images/isb_top_l.gif" width="12" height="20" alt=" " /></td>
						<td width="103" valign="top" align="left" class="isb_t">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" alt="Side Bar Title">
							<tr>
								<td width="103"><img src="'.$theme.'images/spacer.gif" width="103" height="1" alt=" " /></td>
							</tr>
							<tr>
								<td width="103" valign="bottom" align="center" class="epicinfo">.: Navigation :.</td>
							</tr>
						</table></td>
						<td width="12" valign="top" align="left"><img src="'.$theme.'images/isb_top_r.gif" width="30" height="20" alt=" " /></td>
						<td width="26"><img src="'.$theme.'images/spacer.gif" width="26" height="20" alt=" " /></td>
					</tr>
					<tr>
						<td width="7"><img src="'.$theme.'images/spacer.gif" width="7" height="20" alt=" " /></td>
						<td colspan="2" valign="top" align="left" class="isb_l">
						<table width="100%" cellpadding="0" cellspacing="0" border="0" alt="Side Bar">
							<tr>
								<td width="5"><img src="'.$theme.'images/spacer.gif" width="5" height="1" alt=" " /></td>
								<td class="sidebar" valign="top"><br />';
if ($_SESSION["customsidebar"]=="1" && file_exists("./".$theme."/sidebar.php"))
	include("./".$theme."/sidebar.php");
else
	include("./includes/sidebar.php");


echo'
</td>
							</tr>
							</table></td>
						<td width="12" valign="top" align="left" class="isb_r"><img src="'.$theme.'images/spacer.gif" width="30" height="1" alt=" " /></td>
						<td width="26"><img src="'.$theme.'images/spacer.gif" width="26" height="20" alt=" " /></td>
					</tr>
					<tr>
						<td width="7"><img src="'.$theme.'images/spacer.gif" width="7" height="20" alt=" " /></td>
						<td width="12" valign="top" align="left"><img src="'.$theme.'images/isb_bottom_l.gif" width="12" height="20" alt=" " /></td>
						<td width="103" valign="top" align="left" class="isb_b"><img src="'.$theme.'images/spacer.gif" width="103" height="20" alt=" " /></td>
						<td width="12" valign="top" align="left"><img src="'.$theme.'images/isb_bottom_r.gif" width="30" height="20" alt=" " /></td>
						<td width="26"><img src="'.$theme.'images/spacer.gif" width="26" height="20" alt=" " /></td>
					</tr>
					<tr>
						<td colspan="2"><img src="'.$theme.'images/spacer.gif" width="19" height="10" alt=" " /></td>
						<td width="103" valign="top" align="left" class="isb_b"><img src="'.$theme.'images/isb_under.gif" width="103" height="10" alt=" " /></td>
						<td colspan="2"><img src="'.$theme.'images/spacer.gif" width="38" height="10" alt=" " /></td>
					</tr>
				</table></td>
				<td valign="top" align="center">
';
