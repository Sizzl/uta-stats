<?php
error_reporting(0);
header('Content-type: text/plain');
exit; // Remove this for production use

// This requires free GeoLite2 API access from https://www.maxmind.com/en/geoip2-country-database
// It also utilises a basic Country Code map between 2 and 3-letter codes which are needed for UT Stats (source https://gist.githubusercontent.com/tadast/)
// Treat this as a very basic example, build it out for your own needs.

// TO DO - Consider getting db info from UTStats config instead
$dbHost = "localhost"; // database host
$dbUser = "dbuser";  // database username
$dbPass = "dbpass"; // database password
$dbName = "utstats";

$tbIPTC = "ipToCountry2";
$tbPInfo = "uts_pinfo";
$tbPlayer = "uts_player";

// MaxMind key
$maxKey = "replaceme";

// TO DO - consider importing all 3 csvs into temp tables and letting MySQL do the legwork (needs CPU profiling to understand whether it's better here).

function ipRange($cidr) {
   $range = array();
   $cidr = explode('/', $cidr);
   $range[0] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
   $range[1] = long2ip((ip2long($range[0])) + pow(2, (32 - (int)$cidr[1])) - 1);
   return $range;
}

function recursiveRm($str){
  if(is_file($str)){ return @unlink($str); }
  elseif(is_dir($str)){
    $content = glob(rtrim($str,'/').'/*');
    foreach($content as $index=>$path){
      recursiveRm($path);
    }
    return @rmdir($str);
  }
}

// Grab files
// This currently uses file_get_contents which needs allow_url_fopen in php.ini (default is enabled); not checking for this yet, so will fail if not available.
if (file_exists(__DIR__."/iso3166-1.csv")) {
  recursiveRm(__DIR__."/iso3166-1.csv");
}
file_put_contents(__DIR__."/iso3166-1.csv", file_get_contents("https://gist.githubusercontent.com/tadast/8827699/raw/3cd639fa34eec5067080a61c69e3ae25e3076abb/countries_codes_and_coordinates.csv"));

if (file_exists(__DIR__."/geoip.zip")) {
  recursiveRm(__DIR__."/geoip.zip");
}

if (isset($maxKey) && $maxKey != "replaceme") {
  file_put_contents(__DIR__."/geoip.zip", file_get_contents("https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country-CSV&license_key=".$maxKey."&suffix=zip"));
}

// Scan for zips to extract
foreach (scandir(__DIR__) as $value) {
  if (stristr($value,"geoip.zip")) {
    // get the absolute path to $file
    $path = pathinfo(realpath($value), PATHINFO_DIRNAME);

    $zip = new ZipArchive;
    $zipfile = $zip->open(__DIR__."/".$value);
    if ($zipfile === TRUE) {
      $zip->extractTo($path);
      $zip->close();
      echo "[".date('c')."] Extracted ZipArchive.\n";
    } else {
      echo "[".date('c')."] Failed to extract ZipArchive.\n";
    }
  }
}

// Scan for input CSVs
foreach (scandir(__DIR__) as $value) {
    if (is_dir(__DIR__ . "/" . $value)) {
        if (stristr($value,"GeoLite2")) {
      foreach(scandir(__DIR__."/".$value) as $data) {
                if (stristr($data,"Country-Blocks-IPv4.csv"))
                  $cbcsv = __DIR__."/".$value."/".$data;
                if (stristr($data,"Country-Locations-en.csv"))
                  $clcsv = __DIR__."/".$value."/".$data;
            }
        }
    } else {
        if (stristr($value,"iso3166-1.csv")) {
          $cccsv = __DIR__."/".$value;
        }
    }
}

if (isset($cbcsv) || isset($clcsv) || isset($cccsv)) {
  $dbconn = mysql_connect ($dbHost, $dbUser, $dbPass) or die("Error loading database. Unable to connect to MySQL: " . mysql_error());
  mysql_select_db ($dbName,$dbconn) or die("Error during selection.");
  echo "[".date('c')."] Starting import.\n";
}

if (isset($cbcsv) && isset($clcsv)) {
  $sql = "TRUNCATE `".$tbIPTC."`;";
  $rsquery = mysql_query($sql)
    or die("ipToCountry2 Query failed 001: " . mysql_error());

  $fh = fopen($cbcsv, "r");
  $row = 1;
  while (($data = fgetcsv($fh)) !== FALSE) {
    $num = count($data);
    if ($num == 6 && stristr($data[0],"/")) {
      $range = ipRange($data[0]);
      $anon = $sat = "FALSE";
      if (intval($data[4])==1)
        $anon = "TRUE";
      if (intval($data[5])==1)
        $sat = "TRUE";
      $sql = "INSERT INTO `".$tbIPTC."` (`ip_from`,`ip_to`,`ip_cidr`,`geo_id`,`reg_id`,`anon`,`sat`) VALUES (INET_ATON('".$range[0]."'),INET_ATON('".$range[1]."'),'".$data[0]."','".$data[1]."','".$data[2]."','".$anon."','".$sat."');";
      $rsquery = mysql_query($sql)
        or die("ipToCountry2 Query failed 002: " . mysql_error());
    }
          $row++;
  }
  fclose($fh);
  echo "[".date('c')."] - Completed initial import.\n";
  echo "[".date('c')."] - Starting country data update...\n";
  $fh = fopen($clcsv, "r");
  $row = 1;
  while (($data = fgetcsv($fh)) !== FALSE) {
    $num = count($data);
    if ($num == 7 && stristr($data[1],"en")) {
      $sql = "UPDATE `".$tbIPTC."` SET `country_code2` = '".$data[4]."', `country_name` = '".$data[5]."', `continent_code2` = '".$data[2]."', `continent_name` = '".$data[3]."', `in_eu` = '".$data[6]."' WHERE `geo_id` = '".$data[0]."';";
      $rsquery = mysql_query($sql)
        or die("ipToCountry2 Query failed 003: " . mysql_error());
    }
  }
  fclose($fh);
  echo "[".date('c')."] - Completed data update (countries).\n";
}

if (isset($cccsv)) {
  // Update ISO3166-1 alpha-3 codes
  echo "[".date('c')."] - Starting ISO3166-1 alpha-3 data update...\n";
  $fh = fopen($cccsv, "r");
  $row = 1;
  while (($data = fgetcsv($fh)) !== FALSE) {
    $num = count($data);
    if ($num == 6 && (strlen($data[1])==2 || strlen($data[1])==4)) {
      $sql = "UPDATE `".$tbIPTC."` SET `country_code3` = '".$data[2]."', `country_lat` = '".$data[4]."', `country_lon` = '".$data[5]."' WHERE `country_code2` = '".$data[1]."';";
      $rsquery = mysql_query($sql)
        or die("ipToCountry2 Query failed 004: " . mysql_error());
    }
  }
  fclose($fh);
  echo "[".date('c')."] - Completed data update (ISO3166-1 alpha-3).\n";
}

$sql = "UPDATE `".$tbPInfo."` SET `".$tbPInfo."`.`country` = (SELECT LOWER(`country`) FROM `".$tbPlayer."` WHERE `".$tbPlayer."`.`PID` = `".$tbPInfo."`.`id` GROUP BY `".$tbPlayer."`.`PID`)";
$updobj = mysql_query($sql,$dbconn) or die("Pinfo Update: ".mysql_error()."");

mysql_close($dbconn);
echo "[".date('c')."] Complete.\n";
?>
