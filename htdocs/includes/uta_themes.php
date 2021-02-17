<?php

// Theme handling
global $themeselection;
session_start();
$themeselection = "<form method=\"GET\" action=\"".$_SERVER['SCRIPT_NAME']."\">\r\n";
$currentpage = str_replace("?theme=","?oldtheme=",$_SERVER["QUERY_STRING"]);
$currentpage = str_replace("&theme=","&oldtheme=",$currentpage);
$currentarray = split("&",$currentpage); // added section to keep current display page! --// 23/07/05 Timo.
for ($i=0;$i<count($currentarray);$i++)
{
	$currentvar = split("=",$currentarray[$i]);
	if (count($currentvar) > 1)
		$themeselection .= "<input type=\"hidden\" name=\"".$currentvar[0]."\" value=\"".$currentvar[1]."\" />\r\n";
}
$themeselection .= "Select Theme:&nbsp; &nbsp;<select class=\"searchform\" style=\" font-size: 7pt; \" name=\"theme\">";

$themeselection .= "				<option value=\"0\">  </option>\r\n";
$themesql = "SELECT * FROM x_themes ORDER BY themename";
$themequery = mysql_query($themesql,$dbconnect);
if (mysql_num_rows($themequery))
{	
	if ($_COOKIE['utaTheme'])
		$inputtheme = $_COOKIE['utaTheme'];

	if ($_GET['theme'])
		$inputtheme = $_GET['theme'];

	$lasttheme = 0;
	$majortheme = 0;

        while ($rs = mysql_fetch_object($themequery))
        {
		$lasttheme = $rs->id;
		if ($inputtheme==$lasttheme)
		{
			$_SESSION["themeid"] = $lasttheme;
			$_SESSION["themename"] = $rs->themename;
			$_SESSION["themelocation"] = $rs->themelocation;
			$_SESSION["customsidebar"] = $rs->customsidebar;
			$_SESSION["customchartbars"] = $rs->customchartbars;
			$_SESSION["customsig"] = $rs->customsig;
			if ($rs->weaponimages=="1")
				$_SESSION["weaponimages"] = true;
			else
				$_SESSION["weaponimages"] = false;
		}

		if ($rs->default=="1")
			$majortheme = $lasttheme;

		if ($_SESSION["themeid"]==$lasttheme || (!$_SESSION["themeid"] && $lasttheme==$majortheme))
			$themeselection .= "				<option value=\"".$lasttheme."\" selected=\"SELECTED\"> ".htmlspecialchars($rs->themename)." </option>\r\n";
		else
			if ($rs->enabled=="1")
				$themeselection .= "				<option value=\"".$lasttheme."\"> ".htmlspecialchars($rs->themename)." </option>\r\n";
	}

	if ($majortheme > 0)
		$lasttheme = $majortheme;

	if (!isset($_SESSION["themeid"]) && $lasttheme > 0)
	{
		$themesql = "SELECT * FROM x_themes WHERE id=".$lasttheme.";";
		$themequery = mysql_query($themesql,$dbconnect);
		if (mysql_num_rows($themequery))
		{
			$rs = mysql_fetch_object($themequery);
			
			$_SESSION["themeid"] = $lasttheme;
			$_SESSION["themename"] = $rs->themename;
			$_SESSION["themelocation"] = $rs->themelocation;
			$_SESSION["customsidebar"] = $rs->customsidebar;
			$_SESSION["customchartbars"] = $rs->customchartbars;
			$_SESSION["customsig"] = $rs->customsig;
			if ($rs->weaponimages=="1")
				$_SESSION["weaponimages"] = true;
			else
				$_SESSION["weaponimages"] = false;
		}
	}
}
				
$themeselection .= "
			</select>&nbsp;-&nbsp;<input style=\"background-color: #CCCCCC; font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 7pt; text-align: center; height: auto; border : 1px ridge; border-bottom-color : #FFFFFF; border-top-color : #FFFFFF; border-left-color : #FFFFFF; border-right-color : #FFFFFF;\" type=\"submit\" value=\"Change :&gt;\" />
</form>
				\r\n";

$theme = $_SESSION["themelocation"];
if ($_SESSION["weaponimages"])
	$themeimage = $theme;
else
	$themeimage = "";

$footertext = 	  "		  <tr>
			<td class=\"smheading\" align=\"center\">
			".$themeselection."</td>
		  </tr>
		  <tr><td>&nbsp;</td></tr>
		  <tr>
			<td class=\"smheading\" align=\"center\"><a class=\"mainbody\" href=\"http://utstats.unrealadmin.org/\" target=\"_blank\">
			UTStats Beta 4.2</a> &copy; 2005 azazel, AnthraX and toa</td>
		  </tr>
		  <tr>
			<td class=\"smheading\" align=\"center\">
			UTStats Beta 4.2.29uta modifications by Cratos, brajan and Timo (2005-2007, 2020-2021)</td>
		  </tr>
		  </table>";

$anchorprefix = "?p=";

$defaultsidebar = "  <p><a class=\"sidebar\" href=\"./\">Summary</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."recent".$anchorpostfix."\">Public Games</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."utarecent".$anchorpostfix."\">League Matches</a></p>\r\n";
 $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."utapugrecent".$anchorpostfix."\">PUG Matches</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."utapugsummary".$anchorpostfix."\">PUG Totals</a></p>\r\n";
$defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."utarecordzone".$anchorpostfix."\">Assault Records</a></p>\r\n";
$defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."rank&amp;year=".date("Y").$anchorpostfix."\">Rankings (".date("Y").")</a></p>\r\n";
$defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."rank".$anchorpostfix."\">Rankings (All)</a></p>\r\n";
$defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."servers".$anchorpostfix."\">Servers</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."players".$anchorpostfix."\">Players</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."utateams".$anchorpostfix."\">UTA Teams</a></p>\r\n";
$defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."maps".$anchorpostfix."\">Maps</a></p>\r\n";
$defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."totals".$anchorpostfix."\">Totals</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."watchlist".$anchorpostfix."\">Watchlist</a></p>\r\n";
if ($_COOKIE["uta_uts_Admin"])
	$defaultsidebar .="  <p><a class=\"sidebar\" href=\"./admin.php\">Admin</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."credits".$anchorpostfix."\">Credits</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"./".$anchorprefix."help".$anchorpostfix."\">Help</a></p>\r\n";
// $defaultsidebar .="  <p><a class=\"sidebar\" href=\"http://www.unrealadmin.org/forums/forumdisplay.php?f=173\" target=\"_blank\">UTStats Forums</a></p>\r\n";
$defaultsidebar .="  <p><a class=\"sidebar\" href=\"http://forums.utassault.net/forumdisplay.php?f=350\" target=\"_blank\">PUG Forums</a></p>\r\n";
$defaultsidebar .="  <p><a class=\"sidebar\" href=\"http://forums.utassault.net/\" target=\"_blank\">UTA Forums</a></p>\r\n";

?>
