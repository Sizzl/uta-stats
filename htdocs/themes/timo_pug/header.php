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
  <link rel="stylesheet" href="'.$theme.'main.css">
  <link rel="stylesheet" href="'.$theme.'quickstats.css">
  <script language="JavaScript" type="text/JavaScript" src="includes/loader.js"></script>
  <script language="JavaScript" type="text/JavaScript" src="'.$theme.'table_hl.js"></script>
  <script type="text/javascript">
  <!--
    var ol_fgclass="dark"; var ol_bgclass="darkbox"; var ol_textfontclass="dark"; var ol_captionfontclass="hlheading";
  -->
  </script>
  <script type="text/javascript" src="includes/overlib_mini.js"><!-- overLIB (c) Erik Bosrup --></script>
</head>
<body bgcolor="#000033" onload="remove_loading();">
<div id="loader_container">
	<div id="loader">

		<div align="center">Loading Statistics.<br>
		Please wait ...</div>
		<div id="loader_bg"><div id="progress"> </div></div>
	</div>
</div>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <TR> 
    <TD width="100%" align="center" valign="top"> 
  
						<TABLE width="80%" cellpadding="0" cellspacing="0">
							<TBODY align="left">
							  <TR> 
								<TD rowspan="3"><a href="index.php"><IMG src="'.$theme.'images/ladderheaderleft.gif" border="0" alt="ladder header left"></a></TD>
								<TD background="'.$theme.'images/ladderheadertop.gif">&nbsp;</TD>
								<TD><IMG src="'.$theme.'images/ladderheadertoprightcorner.gif"></TD>
							  </TR>
							  <TR> 
								<TD height="54" bgcolor="#2B3455" align="center" width="100%">
				<p align="center"><font size="5" face="Verdana">Welcome to PUG Stats '.(isset($a_test) ? " (archived ".$a_test." season) " : "").'- <a class="txtbigtitle" href="irc://bacon.utassault.net/utapug" target="_blank"></a>#utapug</font><br />
				<span class="rangtext">[Stats last updated : '.strftime("%d/%m/%Y @ %H:%M:%S",$lastupdate).']</span></p>

</TD>
								<TD background="'.$theme.'images/ladderheader-right.gif"></TD>
							  </TR>
							  <TR> 
								<TD height="15" background="'.$theme.'images/ladderheaderbottom.gif">&nbsp;</TD>
								<TD><IMG src="'.$theme.'images/ladderheaderbottomrightcorn.gif"></TD>
							  </TR>
							</TBODY>
						  </TABLE>
						  
	  </TD>
  </TR>
  <TR> 
    <TD width="100%" align="center" valign="top">';
if ($_SESSION["customsidebar"]=="1" && file_exists("./".$theme."/sidebar.php"))
	include("./".$theme."/sidebar.php");
else
	include("./includes/sidebar.php");


echo'
    </TD>
  </TR>
<TR> 
    <TD width="100%" align="center" valign="top"> 
     
						<TABLE width="80%" align="center" cellpadding="0" cellspacing="0">
							<TBODY>
							  <TR> 
								<TD><IMG src="'.$theme.'images/ladderheadertopleftcorner.gif"></TD>
								<TD background="'.$theme.'images/ladderheadertop.gif" width="100"> </TD>
								<TD><IMG src="'.$theme.'images/ladderheadertoprightcorner.gif"></TD>
							  </TR>
							  <TR> 
								<TD background="'.$theme.'images/ladderheaderleft2.gif"> 
								<TD height="50" align="center" valign="middle" bgcolor="#2B3455" width="100%"> 
								  <!--end of header-->';
