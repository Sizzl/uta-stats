<?php
	if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] =="totals") {
		include_once "makestatic/totals_makestatic.php";
	}
	else {
		include_once "makestatic/home_makestatic.php";
	}
?>
