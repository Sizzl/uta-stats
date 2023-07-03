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
  <link rel="icon" href="'.$theme.'images/favicon.ico" type="image/ico">
  <link rel="stylesheet" href="'.$theme.'style.css">
  <script type="text/javascript">
  <!--
    var ol_fgclass="dark"; var ol_bgclass="darkbox"; var ol_textfontclass="dark"; var ol_captionfontclass="hlheading";
  -->
  </script>
  <script type="text/javascript" src="includes/overlib_mini.js"><!-- overLIB (c) Erik Bosrup --></script>
</head>
<body>
<table class="main" border="0" cellpadding="0" cellspacing="0" width="100%" alt=" ">
	<tr>
		<td valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" alt=" ">
				<tr>
					<td width="100%" class="heading" align="left"><img src="'.$theme.'images/plover_logo.jpg" width="348" height="99" alt="UTA Stats" /></td>
				</tr>
				<tr>
					<td width="100%" class="black" align="left"><img src="'.$theme.'images/spacer.gif" width="600" height="1" alt=" " /></td>
				</tr>
				<tr>
					<td width="100%" align="left">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" alt=" ">
							<tr>
								<td width="150" valign="top">
									<table width="100%" border="0" cellpadding="0" cellspacing="0" alt=" ">
										<tr>
											<td width="1" style="background: url(\''.$theme.'images/tgrey.gif\');"><img src="'.$theme.'images/spacer.gif" width="1" height="48" alt=" " /></td>
											<td width="148" align="center" valign="middle" style="background: url(\''.$theme.'images/tsidebg.gif\');">
											<span class="searchformb">
											Stats Last updated:<br />
											'.strftime("%d/%m/%Y @ %H:%M:%S",$lastupdate).'
											</span></td>
											<td width="1" style="background: url(\''.$theme.'images/tgrey.gif\');"><img src="'.$theme.'images/spacer.gif" width="1" height="48" alt=" " /></td>
										</tr>
										<tr>
											<td colspan="3"><img src="'.$theme.'images/tsend.gif" width="150" height="16" alt=" " /></td>
										</tr>
										<tr>
											<td colspan="3">
												<table width="100%" border="0" cellpadding="0" cellspacing="0" alt=" ">
													<tr>
														<td width="30" valign="top"><img src="'.$theme.'images/spacer.gif" width="30" height="1" alt=" " /></td>
														<td alt="links" valign="top">';
									if ($_SESSION["customsidebar"]=="1")
										include("sidebar.php");
									else
										include("./includes/sidebar.php");
									
echo'</td>
														<td width="10" valign="top"><img src="'.$theme.'images/spacer.gif" width="10" height="1" alt=" " /></td>
													</tr>
												</table></td>
										</tr>
									</table></td>
								<td width="10" valign="top">
									<img src="'.$theme.'images/midshadow.gif" width="10" height="64" alt=" " /></td>
								<td alt="Main Body" align="center" valign="top">
								<p align="center" class="txtbigtitle">&nbsp; &nbsp; Welcome to the UTA Statistics System<br /></p>
';
?>
