<?php
include_once "../../includes/mysql-shim/lib/mysql.php"; 
include_once "../../includes/config.php";
include_once "../../includes/uta_functions.php";
include_once "../../includes/functions.php";
global $format;
$format="json";
$pagehandler = "utapugrecent";
if (checkLoadavg()==true)
	echo "{}";
else 
	include "../../pages/uta_match.php";

?>
