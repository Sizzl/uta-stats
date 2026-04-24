<?php 
include_once ("includes/config.php");
include_once ("includes/uta_functions.php");
global $htmlcp, $t_rank, $t_match, $t_pinfo, $t_player, $t_games, $t_discord_players, $t_discord_teams; // fetch table globals.

$rank_year = date("Y"); 
$gid = isset($_GET['gid']) ? my_addslashes($_GET['gid']) : 0;
$gid_filter = ($gid > 0 ? "`r`.`gid` = '".$gid."' AND " : "");
if (isset($_GET['year']) && strlen($_GET['year'])==4 && is_numeric($_GET['year']))
        $rank_year = intval(my_addslashes($_GET['year']));

$r_gamename = small_query("SELECT name FROM ".(isset($t_games) ? $t_games : "uts_games")." WHERE id = '".$gid."';");
$gamename = (isset($r_gamename['name']) ? $r_gamename['name'] : "");
$d_sql = mysql_query("SELECT table_name FROM information_schema.tables WHERE table_name like '%".(isset($t_discord_players) ? $t_discord_players : "discord_players")."%';");
if (mysql_num_rows($d_sql) == 0) {
  if (!isset($format) || (isset($format) && $format != "json")) {
  echo "<h3>Discord player stats are not available.</h3>";
  } else {
    header('Content-Type: application/json; charset=windows-1252');
    echo "{\r\n  \"players\" : []\r\n}";
  }
} else {
  $dp_query = "SELECT dp.*, pi.*, pl.*, r.*
      FROM `".(isset($t_discord_players) ? $t_discord_players : "uts_discord_players")."` AS dp
      LEFT JOIN `".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")."` AS pi ON (pi.id = dp.pid)
      LEFT JOIN `".(isset($t_player) ? $t_player : "uts_player")."` AS pl ON (pl.pid = dp.pid)
      LEFT JOIN `".(isset($t_rank) ? $t_rank : "uts_rank")."` AS r ON (r.pid = dp.pid)
      WHERE (dp.pid > 0 AND pl.id = (SELECT MAX(id)
      FROM `".(isset($t_player) ? $t_player : "uts_player")."`
      WHERE pid = dp.pid) AND r.year = 0) OR dp.pid = 0"; //slow
  $dp_query = "SELECT dp.*, pi.*, r.*
    FROM `".(isset($t_discord_players) ? $t_discord_players : "uts_discord_players")."` AS dp
    LEFT JOIN `".(isset($t_pinfo) ? $t_pinfo : "uts_pinfo")."` AS pi ON (pi.id = dp.pid)
    LEFT JOIN `".(isset($t_rank) ? $t_rank : "uts_rank")."` AS r ON (r.pid = dp.pid)
    WHERE r.year = 0 OR dp.pid = 0";
  $dp_sql = mysql_query($dp_query);

  if (!isset($format) || (isset($format) && $format != "json")) {
    echo'
<table class="box" border="0" cellpadding="1" cellspacing="2" width="710">
  <tbody>
    <tr>
      <td class="heading" colspan="9" align="center">Discord Player Statistics - '.(strlen($gamename) > 0 ? $gamename.' in' : '').$rank_year.'</td>
    </tr>
    <tr>
      <td class="smheading">Player</td>
      <td class="smheading">Last Match</td>
      <td class="smheading">Maps Won</td>
      <td class="smheading">Maps Lost</td>
      <td class="smheading">Matches Won</td>
      <td class="smheading">Matches Lost</td>
      <td class="smheading">Average Frags/Match</td>
      <td class="smheading">Average Objs/Match</td>
      <td class="smheading">Total Time Played</td>
    </tr>';
  } else {
    header('Content-Type: application/json; charset=windows-1252');
    echo "{\r\n  \"players\" : [";
  }
  $first_did = true;
  $last_did = 0;
  while ($r_dp = mysql_fetch_array($dp_sql)) {
    if (!isset($format) || (isset($format) && $format != "json")) {
      if ($last_did != $r_dp['did']) {
        $last_did = $r_dp['did'];
        echo ($first_did ? "" : "\r\n    </tr>");
        $first_did = false;
        echo'
    <tr>
      <td class="dark" align="left">'.(isset($r_dp['name']) ? htmlentities($r_dp['name'], ENT_SUBSTITUTE, $htmlcp) : htmlentities($r_dp['username'], ENT_SUBSTITUTE, $htmlcp)).'</td>
      <td class="dark" align="center">'.(isset($r_dp['last_match']) ? $r_dp['last_match'] : "").'</td>
      <td class="dark" align="center">'.(isset($r_dp['maps_won']) ? $r_dp['maps_won'] : 0).'</td>
      <td class="dark" align="center">'.(isset($r_dp['maps_lost']) ? $r_dp['maps_lost'] : 0).'</td>
      <td class="dark" align="center">'.(isset($r_dp['matches_won']) ? $r_dp['matches_won'] : 0).'</td>
      <td class="dark" align="center">'.(isset($r_dp['matches_lost']) ? $r_dp['matches_lost'] : 0).'</td>
      <td class="dark" align="center">'.(isset($r_dp['avg_frags_match']) ? $r_dp['avg_frags_match'] : 0).'</td>
      <td class="dark" align="center">'.(isset($r_dp['avg_objs_match']) ? $r_dp['avg_objs_match'] : 0).'</td>
      <td class="dark" align="center">'.(isset($r_dp['total_time_played']) ? $r_dp['total_time_played'] : 0).'</td>';
      }
    } else {
      if ($last_did != $r_dp['did']) {
        if ($last_did > 0) {
          echo "\r\n      ]";
          echo "\r\n    }";
        }
        echo ($first_did ? "" : ",")."\r\n    {";
        $first_did = false;
        $first_rank = true;
        $last_did = $r_dp['did'];
        echo "\r\n      \"pid\" : ".(isset($r_dp['pid']) ? $r_dp['pid'] : 0).",";
        echo "\r\n      \"discordid\" : ".(isset($r_dp['did']) ? $r_dp['did'] : 0).",";
        echo "\r\n      \"fid\" : ".(isset($r_dp['fid']) ? $r_dp['fid'] : 0).",";
        echo "\r\n      \"name\" : \"".(isset($r_dp['name']) ? addslashes($r_dp['name']) : addslashes($r_dp['username']))."\",";
        echo "\r\n      \"last_match\" : \"".(isset($r_dp['last_match']) ? $r_dp['last_match'] : "")."\",";
        echo "\r\n      \"maps_won\" : ".(isset($r_dp['maps_won']) ? $r_dp['maps_won'] : 0).",";
        echo "\r\n      \"maps_lost\" : ".(isset($r_dp['maps_lost']) ? $r_dp['maps_lost'] : 0).",";
        echo "\r\n      \"matches_won\" : ".(isset($r_dp['matches_won']) ? $r_dp['matches_won'] : 0).",";
        echo "\r\n      \"matches_lost\" : ".(isset($r_dp['matches_lost']) ? $r_dp['matches_lost'] : 0).",";
        echo "\r\n      \"avg_frags_match\" : ".(isset($r_dp['avg_frags_match']) ? $r_dp['avg_frags_match'] : 0).",";
        echo "\r\n      \"avg_objs_match\" : ".(isset($r_dp['avg_objs_match']) ? $r_dp['avg_objs_match'] : 0).",";
        echo "\r\n      \"total_time_played\" : ".(isset($r_dp['total_time_played']) ? $r_dp['total_time_played'] : 0).",";
        echo "\r\n      \"ranks\" : [";
      } else {
        echo ($first_rank ? "" : ",")."\r\n        {";
        $first_rank = false;
        echo "\r\n          \"gid\" : ".(isset($r_dp['gid']) ? $r_dp['gid'] : 0).",";
        echo "\r\n          \"year\" : ".(isset($r_dp['year']) ? $r_dp['year'] : 0).",";
        echo "\r\n          \"rank\" : ".(isset($r_dp['rank']) ? $r_dp['rank'] : 0).",";
        echo "\r\n          \"percentile\" : ".(isset($r_dp['percentile']) ? $r_dp['percentile'] : 0);
        echo "\r\n        }";
      }
    }
  }

  if (!isset($format) || (isset($format) && $format != "json")) {
    if ($last_did > 0) {
      echo "\r\n    </tr>";
    }
    echo'
  </tbody>
 </table>';
  } else {
  if ($last_did > 0) {
    echo "\r\n      ]";
  }
  if ($first_did == false) {
    echo "\r\n    }";
  }
  echo "\r\n  ],";
  echo "\r\n  \"misc\" : \"\"";
  echo "\r\n}";
  }
}
?>