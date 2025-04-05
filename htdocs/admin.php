<?php 
if (isset($_REQUEST['rememberkey'])) setcookie('uts_adminkey', $_REQUEST['key'], time()+60*60*24*30*365);
if (isset($_COOKIE['uts_adminkey'])) $adminkey = $_COOKIE['uts_adminkey'];
if (isset($adminkey)) setcookie('uta_uts_Admin', 'utaTrue', time()+60*60*24*30*365);
include_once ("includes/mysql-shim/lib/mysql.php");
include ("includes/functions.php");
include ("includes/config.php");
include ("includes/functions_admin.php");

if ($_SESSION["themelocation"]) // Themed header --// 19/07/05 Timo: Added customisable header (& sidebar !)
{
        if (file_exists($_SESSION["themelocation"]."header.php"))
                include($_SESSION["themelocation"]."header.php");
        else
                include ("includes/header.php");
}
else
        include ("includes/header.php");

// Get key from web browser
if (isset($_REQUEST['key'])) $adminkey = $_REQUEST['key'];
if (!isset($adminkey)) $adminkey = '';
$action = (!empty($_REQUEST['action'])) ? $_REQUEST['action'] : 'main';

echo'<table border="0" cellpadding="1" cellspacing="2" width="720">
<tr>
	<td class="heading" align="center" colspan="2">UTStats Administration</td>
</tr>';

IF (empty($import_adminkey)) {
	echo'<tr><td class="smheading" align="left" width="150">Error:</td>
	<td class="grey" align="left">No key set in config.php</td>
	</tr></table>';
	include("includes/footer.php");
	return;
}


IF (!empty($adminkey) && $adminkey != $import_adminkey) {
	echo'<tr><td class="smheading" align="left" width="150">Error:</td>
	<td class="grey" align="left">Keys do not match</td>
	</tr>';
	$adminkey = '';
}

IF (empty($adminkey)) {
	echo'<tr>
		  <td class="smheading" align="left" width="150">Enter Admin key:</td>
		  <td class="grey" align="left"><form NAME="adminkey" ACTION="admin.php">
		  <input TYPE="text" NAME="key" MAXLENGTH="35" SIZE="20" CLASS="searchform">
		  <input TYPE="submit" VALUE="Submit" CLASS="searchformb">
		  <input TYPE="checkbox" NAME="rememberkey"> Remember the key
		  </form></td>
		</tr></table>';
	include("includes/footer.php");
	return;
}


echo'</table>
	<br>';

$action = str_replace(array('.', '/', '<', ':'), array(), $action);
$fn = "pages/admin/$action.php";
if (!file_exists($fn) or !is_file($fn))
	echo('Administrative page does not exist.<p>&nbsp;</p>(<a href="javascript:window.history.back();">Go back</a>)');
else
	require($fn);
if ($_SESSION["themelocation"]) // Themed footer --// 19/07/05 Timo: Added customisable footer
{
        if (file_exists($_SESSION["themelocation"]."footer.php"))
                include($_SESSION["themelocation"]."footer.php");
        else
                include("includes/footer.php");
}
else
        include("includes/footer.php");

?>
