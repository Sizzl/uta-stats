<?php 
$mid = (isset($_GET['mid']) ? $_GET['mid'] : "");
$pid = (isset($_GET['pid']) ? $_GET['pid'] : "");

if ($pid != "") {
	include("match_player.php");
} else {
	include("match_info.php");
}
?>
