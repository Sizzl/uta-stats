<?php
$dbHost = "localhost"; // database host
$dbUser = "dbuser";  // database username
$dbPass = "dbpass"; // database password
$dbName = "utstats";
$file = 'IpToCountry.csv'; // full path to 'ip-to-country.csv' file on server
header('Content-type: text/plain');


$dbconn = mysql_connect ($dbHost, $dbUser, $dbPass);
mysql_select_db ($dbName,$dbconn) ;

$fobj = fopen($file,"rb") ;

// if file 'ip-to-country.csv' not found
	if ($fobj==FALSE) {
	 echo "File Ip-to-country.csv not found!\r\n";
	 die();
	} 
 
// clear table before update
echo "Deleting old records from table\r\n";
mysql_query("DELETE FROM ipToCountry",$dbconn);


echo "Update in progress.... please wait ...\r\n\r\n";

  $sql= "INSERT INTO ipToCountry (ip_from,ip_to,country_code2,country_code3,country_name) VALUES"." (";
  $i=0;
  while (!feof($fobj)){ 
  $fread = fgets($fobj,1024); 
  if (substr($fread,0,1) != "#") {
    $cparray= explode(',',$fread);
    $cparray=str_replace('"','',$cparray);
    $cparray=str_replace("'",'&#039;',$cparray);

# IP FROM      IP TO        REGISTRY  ASSIGNED   CTRY CNTRY COUNTRY
# "1346797568","1346801663","ripencc","20010601","il","isr","Israel"
    if (count($cparray) > 4) {
      $sql = "INSERT INTO ipToCountry VALUES ('$cparray[0]','$cparray[1]','".strtoupper($cparray[4])."','".strtoupper($cparray[5])."','$cparray[6]')";
      $updobj = mysql_query($sql,$dbconn) or die("BLAD PODCZAS ZAPISU: ".mysql_error()."");
      $sql = "UPDATE uts_player SET country = '".strtolower($cparray[4])."' WHERE ip >= '$cparray[0]' AND ip <= '$cparray[1]'";
      $updobj = mysql_query($sql,$dbconn) or die("BLAD PODCZAS ZAPISU: ".mysql_error()."");

    } else {
      $sql = "INSERT INTO ipToCountry VALUES ('$cparray[0]','$cparray[1]','$cparray[2]','$cparray[3]','$cparray[4]')";
      $updobj = mysql_query($sql,$dbconn) or die("BLAD PODCZAS ZAPISU: ".mysql_error()."");
      $sql = "UPDATE uts_player SET country = '".strtolower($cparray[2])."' WHERE ip >= '".$cparray[0]."' AND ip <= '".$cparray[1]."'";
      $updobj = mysql_query($sql,$dbconn) or die("BLAD PODCZAS ZAPISU: ".mysql_error()."");
    }
    $i++;
  }
}
  $sql = "UPDATE `uts_pinfo` SET uts_pinfo.country = (SELECT LOWER(country) FROM `uts_player` WHERE uts_player.PID = uts_pinfo.id GROUP BY uts_player.PID)";
  $updobj = mysql_query($sql,$dbconn) or die("BLAD PODCZAS ZAPISU: ".mysql_error()."");
echo "Operation complete :)\r\n";
?>
