<?
require ("includes/config.php");
require ("includes/config_pic.php");
require ("includes/functions.php");

if (!isset($pic_enable) or !$pic_enable) pic_error('err_disabled');
// brajan 04082005
$serv_path = ereg_replace("\\\\","/",__FILE__); 
$serv_path = dirname($serv_path); 

$debugvar = true;
$outputheader = false;

if ($_GET['output'])
{
	$debugvar = false;
	$outputheader = true;
}
function scenedebug($strinput)
{
	global $debugvar,$outputheader;
	if ($outputheader!=true)
	{
		header("content-type: text/plain");
		$outputheader=true;
	}
	if ($debugvar==true)
	{
		echo $strinput;
	}
}

function pic_error($name) {
	header("Content-type: image/png");
	readfile("images/templates/${name}.png");
	exit;
}
function image_create($filename) {
	if (!file_exists($filename)) return(false);
	$infos = getimagesize($filename);
	scenedebug("Image type: ".$infos[2]."\r\n");
	if (!$infos) return(false);
	switch($infos[2]) {
		case 1:
			$im = @imagecreatefromgif($filename);
			break;
		case 2: 
			$im = @imagecreatefromjpeg($filename);
			break;
		case 3:
			$im = @imagecreatefrompng($filename);
			break;
		default:
			die("Unsupported image type");
	}
	if (!$im) die("Unable to load image template");
	
	return($im);
}

function place_text(&$im, $size, $angle, $x, $to_x, $y, $to_y, $color, $font, $align, $text)
{
	$cp = allocate_color($im, $color);
	$box = imagettfbbox($size, $angle, $font, $text);
	$twidth = $box[4] - $box[0];

	switch($align) {
		case 'center':
			$p_x = ($to_x - $x) / 2 - ceil($twidth/2); break;
		case 'right':
			$p_x = $to_x - $twidth; break;
		default: 
			$p_x = $x;
	}
	imagettftext($im, $size, $angle, $p_x, $y, $cp, $font, $text);
}

function allocate_color(&$im, $colstring) {
	static $cache = array();
	
	if (isset($cache[$colstring])) return($cache[$colstring]);
	
	$col = explode(':', substr(chunk_split($colstring, 2, ':'), 0, -1));
	$r = hexdec($col[0]);
	$g = hexdec($col[1]);
	$b = hexdec($col[2]);
	if (isset($col[3])) {
		$alpha = hexdec($col[3]);
		$cp = imagecolorallocatealpha($im, $r, $g, $b, $alpha);	
	} else {
		$cp = imagecolorallocate($im, $r, $g, $b);	
	}
	$cache[$colstring] = $cp;
	return($cp);	
}

function output_image(&$im, $type) {
	switch($type) {
		case 'jpg':
			header("Content-type: image/jpeg");
			imagejpeg($im);
			break;
		
		case 'gif':
			header("Content-type: image/gif");
			imagegif($im);
			break;
			
		default:
			header("Content-type: image/png");
			imagepng($im);
	}
}

if (!function_exists("gd_info")) {
	if (!check_extension('gd2')) pic_error('err_no_gd');
}

$gd_info = gd_info();
if (!$gd_info['FreeType Support']) pic_error('err_no_ft');

if ($_GET['mid'])
	$matchid = intval($_GET['mid']);
else
	$matchid = 2852;

$sql = "SELECT * FROM `uts_match` WHERE `id` = '".$matchid."'";

$result = mysql_query($sql);	
if (!$result || !mysql_num_rows($result)) return;
else
{
	$rs = mysql_fetch_object($result);
	scenedebug("Timo's UTA Stats Scene generator. Use ?&output=0/1 to toggle debug visibility.\r\n");
	scenedebug("--------\r\nValid match id (?&mid=) : ".$matchid."\r\n");
	$assaultid = $rs->assaultid;
	scenedebug("Assault ID: ".$assaultid."\r\n--------\r\n");

	$mapname = $rs->mapfile;

	$scenefile = strtolower(str_replace(".unr",".jpg",$mapname));
	$scene_exists = false;
	if (file_exists("images/maps/scenes/".$scenefile))
	{
		$im = image_create("images/maps/scenes/".$scenefile);
		if ($im)
		{
			$scene_exists = true;
	 		$img_width = imagesx($im);
 			$img_height = imagesy($im);
			scenedebug("Image Dims: ".$img_width."x".$img_height."\r\n");
			scenedebug("--------\r\n");
		}
		else
		{
			if (!$_GET['verbose'])
			{
				scenedebug("Scene does not exist. Use ?&verbose=true to bypass this exit.\r\n");
				exit; // replace this with spacer output at some point!
			}
		}
	}
	else
	{
		if (!$_GET['verbose'])
		{
			scenedebug("Scene does not exist. Use ?&verbose=true to bypass this exit.\r\n");
			exit; // replace this with spacer output at some point!
		}
	}
	// Get map time
	$sql = "SELECT * FROM `uts_smartass_objs` WHERE `mapfile` = '".$mapname."' AND `defensetime` > 0 ORDER BY defensetime DESC LIMIT 0 , 1;";
	$result = mysql_query($sql);	
	if (!$result || !mysql_num_rows($result))
	{
		// hrm! Well, just give it the default time.
		$maptime = 10;
	}
	else
	{
		$rs = mysql_fetch_object($result);
		$maptime = $rs->defensetime;
	}
	$maptime = intval($maptime*60); // minutes->seconds
	// Get both rounds to determine output syntax
	$sql = "SELECT * FROM `uts_match` WHERE `mapfile` = '".$mapname."' AND `assaultid` = '".$assaultid."' ORDER BY mapsequence ASC;";
	$result = mysql_query($sql);
	if (!$result || !mysql_num_rows($result)) return;
	else
	{
		$i = 0;
		while ($rs=mysql_fetch_object($result))
		{
			if ($i==0 && $rs->id==$matchid) // round 0
			{	
				scenedebug("Round: ".$i."\r\n");
				scenedebug("Map: ".$mapname."\r\n");
				scenedebug("Scene: ".$scenefile);
				if ($scene_exists == true)
					scenedebug(" [Exists]");
				else
					scenedebug(" [No Image Available]");
				scenedebug("\r\n");
				$teamname = array("Red"=>$rs->teamname0,"Blue"=>$rs->teamname1);

				scenedebug("Red Team: ".$teamname["Red"]."\r\n");
				scenedebug("Blue Team: ".$teamname["Blue"]."\r\n");

				scenedebug("Attacking team: ");
				if (intval($rs->ass_att)==1)
				{
					scenedebug("Blue");
					$att_team = "Blue";
					$def_team = "Red";
				}
				else
				{
					scenedebug("Red");
					$att_team = "Red";
					$def_team = "Blue";
				}
				scenedebug("\r\n");

				scenedebug("Attacking team won: ");
				if ($rs->ass_win==1)
				{
					scenedebug("True\r\n");
					$itime_elapsed = $rs->gametime;
					$time_elapsed = GetMinutes($itime_elapsed);
					$itime_remaining = intval($maptime - $itime_elapsed);
					$time_remaining = GetMinutes($itime_remaining);					
					scenedebug("Time remaining: ".$time_remaining."\r\n");
					$topline = $teamname[$att_team]." conquered the base in ".$time_elapsed."! ";
					scenedebug("Topline: ".$topline."\r\n");
				}
				else
				{
					scenedebug("False\r\n");
					$time_remaining = "00:00";
					scenedebug("Time remaining: ".$time_remaining."\r\n");
					$topline = $teamname[$def_team]." defended the base! ";
					scenedebug("Topline: ".$topline."\r\n");
				}

			}
			else if ($i==1 && $rs->id==$matchid) // round 1 (determines map lose or win!)
			{
				scenedebug("Round: ".$i."\r\n");
				scenedebug("Map: ".$mapname."\r\n");
				scenedebug("Scene: ".$scenefile);
				if ($scene_exists == true)
					scenedebug(" [Exists]");
				else
					scenedebug(" [No Image Available]");
				scenedebug("\r\n");

				$teamname = array("Red"=>$rs->teamname0,"Blue"=>$rs->teamname1) ;

				scenedebug("Red Team: ".$teamname["Red"]."\r\n");
				scenedebug("Blue Team: ".$teamname["Blue"]."\r\n");

				scenedebug("Attacking team: ");
				if (intval($rs->ass_att)==1)
				{
					scenedebug("Blue");
					$att_team = "Blue";
					$def_team = "Red";
				}
				else
				{
					scenedebug("Red");
					$att_team = "Red";
					$def_team = "Blue";
				}
				scenedebug("\r\n");

				scenedebug("Attacking team won: ");
				if ($rs->ass_win==1)
				{
					scenedebug("True\r\n");

					$itime_elapsed = $rs->gametime;
					$time_elapsed = GetMinutes($itime_elapsed);
					if ($tiemarker)
						$itime_remaining = intval($tiegametime - $itime_elapsed);
					else
						$itime_remaining = intval($maptime - $itime_elapsed);

					$time_remaining = GetMinutes($itime_remaining);					
					scenedebug("Time remaining: ".$time_remaining."\r\n");
					$topline = $teamname[$att_team]." conquered the base in ".$time_elapsed." ";
					// --// Applies only to second record (round 1):
					if ($tiemarker==true)
						$topline .= "and tie! ";
					else
						$topline .= "and win! ";
					if ($rs->matchmode==1) // Show team scores in match mode!
						$topline .= $teamname[0]." ".$rs->score0." - ".$rs->score1." ".$teamname[1];
					// --// End topline additions.
					scenedebug("Topline: ".$topline."\r\n");
				}
				else
				{
					scenedebug("False\r\n");
					$time_remaining = "00:00";
					scenedebug("Time remaining: ".$time_remaining."\r\n");
					$topline = $teamname[$def_team]." defended the base";
					// --// Applies only to second record (round 1):
					if ($tiemarker==true)
						$topline .= " and win! ";
					else
						$topline .= " and tie! ";
					// --// End topline additions.
					if ($rs->matchmode==1) // Show team scores in match mode!
						$topline .= $teamname["Red"]." ".$rs->score0." - ".$rs->score1." ".$teamname["Blue"]; // Show team stats
					scenedebug("Topline: ".$topline."\r\n");
				}
			}
			else
			{
				if ($rs->ass_win==1)
				{
					$tiemarker = true;
					$tiegametime = $rs->gametime;
				}
			}
			$i++;
		} // end match data

		// Get Team Data!
		$sql = "SELECT uts_pinfo . * , uts_player . * FROM uts_player INNER JOIN uts_pinfo ON ( uts_player.pid = uts_pinfo.id ) WHERE uts_player.matchid = '".$matchid."' ORDER BY uts_player.team ASC , uts_player.gamescore DESC;"; // get team data
		$result = mysql_query($sql);
		if (!$result || !mysql_num_rows($result))
		{
			// no team output?
		}
		else
		{
		        while ($rs = mysql_fetch_object($result))
			{
				if ($rs->team==1)
					$teamid = "Blue";
				else
					$teamid = "Red";
				$teamdata[$teamid][] = array("PID"=>$rs->pid,"Name"=>str_replace(chr(174),"",$rs->name),"Score"=>$rs->gamescore,"Ping"=>$rs->avgping,"Netspeed"=>$rs->netspeed,"Time"=>$rs->gametime);
			}
			unset($i);
			scenedebug("\r\nRed team players:\r\n");
			for ($i=0;$i<count($teamdata["Red"]);$i++)
			{
				scenedebug(" - ".$teamdata["Red"][$i]["Name"]." - ".$teamdata["Red"][$i]["Score"]."\r\n");
			}
			scenedebug("\r\nBlue team players:\r\n");
			for ($i=0;$i<count($teamdata["Blue"]);$i++)
			{
				scenedebug(" - ".$teamdata["Blue"][$i]["Name"]." - ".$teamdata["Blue"][$i]["Score"]."\r\n");
			}
		}	

		// Get taken objective data!
		$sql = "SELECT uts_pinfo.name AS plrname, uts_smartass_objstats.*
			FROM uts_smartass_objstats
			INNER JOIN uts_pinfo ON uts_smartass_objstats.pid = uts_pinfo.id
			WHERE matchid = '".$matchid."';";
		$result = mysql_query($sql);
		if (!$result || !mysql_num_rows($result))
		{
			// no objectives taken!
			$objcount = 0;
		}
		else
		{
			while ($rs=mysql_fetch_object($result))
			{
				$takenobjs[] = array("ObjID"=>$rs->objid,"ByPID"=>$rs->pid,"ByPlayer"=>str_replace(chr(174),"",$rs->plrname));
			}
		}
		scenedebug("\r\nObjectives:\r\n");
		$sql = "SELECT * FROM uts_smartass_objs WHERE mapfile = '".$mapname."' ORDER BY defensepriority DESC, objnum ASC;";
		$result = mysql_query($sql);
		if (!$result || !mysql_num_rows($result))
		{
			// no objectives available!
			scenedebug(". No objectives available\r\n");
		}
		else
		{
			while ($rs=mysql_fetch_object($result))
			{
				$istaken = false;
				for ($j=0;$j<count($takenobjs);$j++)
				{
					if ($takenobjs[$j]["ObjID"]==$rs->id)
					{
						$objective[] = array("Name"=>$rs->objname,"Status"=>"Completed!","ByPID"=>$takenobjs[$j]["ByPID"],"ByPlayer"=>$takenobjs[$j]["ByPlayer"]);
						$istaken = true;
					}
				}
				if (!$istaken)
					$objective[] = array("Name"=>$rs->objname,"Status"=>"Not Completed","ByPID"=>"0","ByPlayer"=>"None");

			}
			scenedebug("--\r\n");
			
			for ($i=0;$i<count($objective);$i++)
			{
				scenedebug(". ".$objective[$i]["Name"]." - ");
				if ($objective[$i]["Status"]=="Completed!")
				{
					scenedebug($objective[$i]["Status"]." - By ".$objective[$i]["ByPlayer"]."\r\n");
				}
				else
				{
					scenedebug($objective[$i]["Status"]."\r\n");
				}
				if (intval(count($objective)/2)==$i)
				{
					scenedebug("--\r\n");
				}
			} // end objective printing
		} // objective checking
	}
}	

if (!$debugvar && $im) // no debug output & image exists! Add the text variables and output final image! :)
{
	// --// Colour: UT Red:    #FF0000; (R)
	// --// Colour: UT Green:  #00FF00; (G)
	// --// Colour: UT Blue:   #00AAFF; (B)
	// --// Colour: UT Yellow: #FFFF00; (Y)
	// --// Colour: White:     #FFFFFF; (W)
	// --// Colour: Black:     #000000; (X)

	$utcol =  array("R"=>"FF0000",
			"G"=>"00FF00",
			"B"=>"00AAFF",
			"Y"=>"FFFF00",
			"W"=>"FFFFFF",
			"X"=>"000000");

	// To-do: Add function to ensure $im is 800x600 (i.e. resize if different).

	$utfont = "images/maps/scenes/arial.ttf";
	$digfont = "images/maps/scenes/Digirtu_.ttf";
	$defangle = "0";

/* 
	*****************************************************
	*** Attribute        (x, w, y, h)     - Alignment ***
	*****************************************************

	Red Digital Display  (*,*,30,*)       - Center
	Yellow top line      (*,*,60,*)       - Center

	Red team name        (100,300,120,20) - Left
	Blue team name       (500,300,120,20) - Left
	
	Red player ping box  (50,50,140,20)   - Left
	Red player name      (100,160,140,20) - Left
	Red player score     (260,45,140,20)  - Right

	Blue player ping box (450,50,140,20)  - Left
	Blue player name     (500,160,140,20) - Left
	Blue player score    (660,45,140,20)  - Right
	
*/
	place_text($im,"24",$defangle,"0","800","30","80",$utcol["R"],$digfont,"center",$time_remaining);
	place_text($im,"16",$defangle,"0","800","60","80",$utcol["Y"],$utfont,"center",$topline);

	place_text($im,"14",$defangle,"100","400","120","160",$utcol["R"],$utfont,"left",$teamname["Red"]);

	$i = 147;	
	for ($j=0;$j<count($teamdata["Red"]);$j++)
	{
		place_text($im,"14",$defangle,"100","260",$i,"27",$utcol["R"],$utfont,"left",$teamdata["Red"][$j]["Name"]);
		place_text($im,"14",$defangle,"260","305",$i,"27",$utcol["R"],$utfont,"right",$teamdata["Red"][$j]["Score"]);
		$i = $i + 27;
	}

	place_text($im,"14",$defangle,"500","800","120","160",$utcol["B"],$utfont,"left",$teamname["Blue"]);

	$i = 147;	
	for ($j=0;$j<count($teamdata["Blue"]);$j++)
	{
		place_text($im,"14",$defangle,"500","660",$i,"27",$utcol["B"],$utfont,"left",$teamdata["Blue"][$j]["Name"]);
		place_text($im,"14",$defangle,"660","705",$i,"27",$utcol["B"],$utfont,"right",$teamdata["Blue"][$j]["Score"]);
		$i = $i + 27;
	}
	$i = 0;


	output_image($im,'jpg');
}











?>
