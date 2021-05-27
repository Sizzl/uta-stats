<?php 
if (empty($import_adminkey) or isset($_REQUEST['import_adminkey']) or $import_adminkey != $adminkey) die('bla');
global $dbversion;
	
$options['title'] = 'Player record auto-merge suggestions';
$options['requireconfirmation'] = false;

$safemode = false;

$sql_iplist = "SELECT COUNT(pid) as pidCount, ip FROM uts_player WHERE ip <> '3588675586' AND ip <> '3104445278' GROUP BY ip ORDER BY pidCount DESC";// LIMIT 0,100";
$q_iplist = mysql_query($sql_iplist) or die(mysql_error());
while ($r_iplist = mysql_fetch_array($q_iplist)) {
  if (isset($dbversion) && floatval($dbversion) > 5.6) {
    $sql_dfind = "SELECT ANY_VALUE(`uts_pinfo`.`name`) AS `name`, `uts_player`.`pid`, COUNT(`uts_player`.`matchid`) AS `matchCount`, ANY_VALUE(`uts_player`.`ip`) AS `ip` FROM `uts_player` INNER JOIN `uts_pinfo` ON (`uts_pinfo`.`id` = `uts_player`.`pid`) WHERE `uts_player`.`ip` = '".$r_iplist['ip']."' GROUP BY `pid` ORDER BY `matchCount` DESC";
  } else {
    $sql_dfind = "SELECT uts_pinfo.name, uts_player.pid, COUNT(uts_player.matchid) AS matchCount, uts_player.ip FROM uts_player INNER JOIN uts_pinfo ON (uts_pinfo.id = uts_player.pid) WHERE uts_player.ip = '".$r_iplist['ip']."' GROUP BY pid ORDER BY matchCount DESC";
  }
  $q_dfind = mysql_query($sql_dfind) or die(mysql_error());
  if (mysql_num_rows($q_dfind) > 1) {
    $regs = array();
    while ($r_dlist = mysql_fetch_array($q_dfind)) {
      // first run through, filter for multiple registered users
      if (strpos($r_dlist['name'],chr(174)) > 0) {
        array_push($regs,$r_dlist['pid']);
      }
    }
    if (count($regs) > 0) {
      // shortlist the registered accounts to find most used
      if (isset($dbversion) && floatval($dbversion) > 5.6) { 
        $sql_rfind = "SELECT ANY_VALUE(`uts_pinfo`.`name`) AS `name`, `uts_player`.`pid`, COUNT(`uts_player`.`matchid`) AS `matchCount` FROM `uts_player` INNER JOIN `uts_pinfo` ON (`uts_pinfo`.`id` = `uts_player`.`pid`) WHERE `uts_player`.`pid` IN (".implode(',', $regs).") GROUP BY `uts_player`.`pid` ORDER BY `matchCount` DESC;";
      } else {
        $sql_rfind = "SELECT uts_pinfo.name, uts_player.pid, COUNT(uts_player.matchid) AS matchCount FROM uts_player INNER JOIN uts_pinfo ON (uts_pinfo.id = uts_player.pid) WHERE uts_player.pid IN (".implode(',', $regs).") GROUP BY uts_player.pid ORDER BY matchCount DESC;";
      }
      $q_rfind = mysql_query($sql_rfind) or die(mysql_error());
      $regs = array(); 
      while ($r_rlist = mysql_fetch_array($q_rfind)) {
        array_push($regs,$r_rlist);
      }
    } else {
      $regs = array();
    }
    // re-query
    echo "<fieldset><dl><dt style=\"text-align: left;\">IP: ".long2ip($r_iplist['ip'])." [".$r_iplist['ip']."]</dt><dd style=\"text-align: left;\"><ul>";
    $mplayer1 = $mplayer2 = 0;
    $q_dfind = mysql_query($sql_dfind) or die(mysql_error());
    while ($r_dlist = mysql_fetch_array($q_dfind)) {
      if ($mplayer1 == 0) {
        $mplayer1 = $r_dlist['pid'];
        $mplayer1name = utf8_encode($r_dlist['name']);
        $mplayer1count = $r_dlist['matchCount'];
      } else {
        $mplayer2 = $r_dlist['pid'];
        $mplayer2name = utf8_encode($r_dlist['name']);
        $mplayer2count = $r_dlist['matchCount'];
      }
      if (($mplayer1 > 0) && ($mplayer2 > 0) && ($mplayer2 <> $mplayer1)) {
        echo "<li>";
        if ($safemode)
        {
          echo "[<a href=\"admin.php?key=".addslashes($_GET['key'])."&list=hide&action=mplayers&step=2&values=mplayer1=%3E".$mplayer1.",mplayer2=%3E".$mplayer2."\">MERGE</a>] &nbsp;";
          echo $mplayer2name." (match count (this IP)=".$mplayer2count.") into ".$mplayer1name." (match count (this IP)=".$mplayer1count.") &nbsp;</li> ";
        }
        else {
          echo "[<a href=\"admin.php?key=".addslashes($_GET['key'])."&list=hide&action=mplayers&step=3&values=mplayer1=%3E".$mplayer1.",mplayer2=%3E".$mplayer2."&submit=Finish\">MERGE</a>] &nbsp;";
          echo $mplayer2name." (match count (this IP)=".$mplayer2count.") into ".$mplayer1name." (match count (this IP)=".$mplayer1count.") &nbsp;</li> ";
        }
      }
      if (count($regs)) {
        foreach ($regs as &$reg) {
          if ($reg['pid'] <> $mplayer2 && $mplayer2 > 0) {
            echo "<li>[<a href=\"admin.php?key=".addslashes($_GET['key'])."&list=hide&action=mplayers&step=3&values=mplayer1=%3E".$reg['pid'].",mplayer2=%3E".$mplayer2."&submit=Finish\">MERGE</a>] &nbsp;";
            echo $mplayer2name." (match count=".$mplayer2count.") into <b>".utf8_encode($reg['name'])."</b> (total match count=".$reg['matchCount'].") &nbsp;</li> ";
          }
          if ($reg['pid'] <> $mplayer1 && $mplayer2 == 0) {
            echo "<li>[<a href=\"admin.php?key=".addslashes($_GET['key'])."&list=hide&action=mplayers&step=3&values=mplayer1=%3E".$reg['pid'].",mplayer2=%3E".$mplayer1."&submit=Finish\">MERGE</a>] &nbsp;";
            echo $mplayer1name." (match count=".$mplayer1count.") into <b>".utf8_encode($reg['name'])."</b> (total match count=".$reg['matchCount'].") &nbsp;</li> ";
          }
        }
      }
      $mplayer2 = 0;

    }
    $regs = false;
    $mplayer1 = $mplayer2 = 0;
    echo "</ul></dd></dl></fieldset>\n";
  }
}

$is_admin = true;
	
?>
