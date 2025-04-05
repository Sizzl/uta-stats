<?php 
if(empty($_SERVER['SSH_CONNECTION'])){ exit; } // run this script from SSH only!
include_once ("includes/mysql-shim/lib/mysql.php");
if(!function_exists('adminselect')){
	include ("includes/functions.php");
	include ("includes/config.php");
	include ("includes/functions_admin.php");
}
$results['start'] = 'Yes';
$results['reset'] = 'No'; // Truncate uts_rank table?

// disable importing while runing this script!
mysql_query("UPDATE x_ftpservers SET enabled='2' WHERE enabled='1';");

@ignore_user_abort(true);
@set_time_limit(0);

echo "Recalculating Rankings\n\r";
if($results['reset'] == 'Yes'){ // truncate table ONLY if selected
echo "Deleting current rankings\n\r";
	mysql_query("TRUNCATE uts_rank") or die(mysql_error());	
}
echo'Recalculating Rankings:\n\r';
$playerbanned = false;

// brajan 2006-09-09
// this query needs to be splited into few smaller then 700 000 queries parts!!!!
// it causes mysql cpu hog at that high number - mysql = zombie with 100% CPU usage :(

// find out how many records we have to deal with
/*
SELECT COUNT(*) AS max_records FROM uts_player p, uts_pinfo pi, uts_match m WHERE pi.id = p.pid AND pi.banned <> 'Y' AND m.id = p.matchid
*/

$startfrom = 2187201;
	$q_pm = mysql_query(	"	SELECT 	p.id, 
												p.matchid, 
												p.pid, 
												p.gid,
												m.gamename
									FROM 		uts_player p, 
												uts_pinfo pi,
												uts_match m
									WHERE 	pi.id = p.pid 
										AND 	pi.banned <> 'Y' 
										AND	m.id = p.matchid
									ORDER BY p.matchid ASC, 
												p.playerid ASC LIMIT $startfrom, 700000");
	$i = $startfrom;
	echo mysql_error();
	while ($r_pm = mysql_fetch_array($q_pm)) {
		//if($i < 787200) { continue; }
		$i++;
		if ($i%50 == 0) {
			echo $i.'. ';
		}
		ob_start();
		$playerecordid = $r_pm['id'];
		$matchid = $r_pm['matchid'];
		$pid = $r_pm['pid'];
		$gid = $r_pm['gid'];
		$gamename = $r_pm['gamename'];
// brajan 2006-09-08
// skip to another player if current already has a rank when RESUME mode is enabled
// otherwise calculate it
		if($results['reset'] == 'No'){ 
			$r_rankp = small_query("SELECT id FROM uts_rank WHERE pid = '".$pid."' AND gid = '".$gid."'");
				if( @mysql_num_rows($r_rankp) > 0){ continue;	}
		}
	include('import/import_ranking.php');
	ob_end_flush();
	flush();	
	ob_flush();
	}
	echo "Done\n\r Rankings recalculated \n\r";
// enable import after all is done
mysql_query("UPDATE x_ftpservers SET enabled='1' WHERE enabled='2';");
?>
