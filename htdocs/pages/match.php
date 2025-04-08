<?php 
$mid = $_GET['mid'];
$pid = $_GET['pid'];

if ($pid != "") {
	include("match_player.php");
} else {
	include("match_info.php");
}
?>
