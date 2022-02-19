<?php 
error_reporting(E_ALL);
$matchcode = $_GET['matchcode'];
$match_ids = array();
$sql = "SELECT id FROM uts_match WHERE matchcode='".$matchcode."'";
$query = mysql_query($sql);
while($row = mysql_fetch_assoc($query)){ $match_ids[] = $row['id']; }
print_r($match_ids);
//$mid = $_GET['mid'];
//$pid = $_GET['pid'];
?>

