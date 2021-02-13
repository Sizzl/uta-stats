<?php 
//require_once('config.php');
if ($ftp_type == 'php') {
	require(dirname(__FILE__) . '/ftp_class_native.php');
} else {
	require(dirname(__FILE__) . '/ftp_class.php');
}

$g_ftp_error = false;

function ftp_error($message) {
	global $html, $g_ftp_error, $ftp;
	
	$g_ftp_error = true;
	tablerow('ERROR:', $message, true);
	while(($err = $ftp->PopError()) !== false) {
		$fctname = $err['fctname'];
		$msg = $err['msg'];
		$desc = $err['desc'];
		if($desc) $tmp=' ('.$desc.')'; else $tmp='';
		if (strpos($msg, 'socket set') === 0) {
			$tmp .= "\nTry disabling the usage of sockets (set \$ftp_type = 'pure'; in config.php)";
		}
		tablerow('Error details:', $fctname.': '.$msg.$tmp, true);
	}

}

function tablerow($left, $right, $error=false) {
	global $html, $ftp_debug;
	if ($ftp_debug) return;
	$space = ($html) ? '&nbsp;' : ' ';
	$left = (empty($left)) ? $space : (($html) ? htmlentities($left) : $left);
	$right = (empty($right)) ? $space : (($html) ? nl2br(htmlentities($right)) : $right);
	$style = ($error) ? 'style="background-color: red;"' : '';
	if ($html) {
		echo '<tr>';
		echo '<td class="smheading" '. $style .' align="left" width="170">'. $left .'</td>';
		echo '<td class="grey" '. $style .' align="left" width="380">'. $right .'</td>';
		echo '</tr>';
	} else {
		if (strlen($left) < 30) $left .= str_repeat(" ", 30 - strlen($left));
		echo "$left $right\n";
	}
	flush();
}

function ftpupdate()
{
	global  $html, $ftp, $ftp_uname, $ftp_upass, $ftp_hostname, $ftp_port, $g_ftp_error, $ftp_debug, $ftp_log_extension, $ftp_offset, $ftp_add_tz,
		$ftp_delete, $ftp_movedir, $ftp_dir, $ftp_passive, $import_log_start, $import_log_extension,
		$import_utdc_download_enable, $import_utdc_log_start, $import_utdc_log_extension;

	if (!$ftp_debug) {
		if ($html) echo'<table class="box" border="0" cellpadding="1" cellspacing="2" style="table-layout:fixed"><tr><td class="smheading" align="center" height="25" width="550" colspan="2">';
		echo "FTP Transferring Log Files...\n";
		if ($html) echo '</td></tr>';
	}

	// Update, from here on were going to be doing multiple FTP sessions.
	for ($i = 0; $i < count($ftp_hostname); $i++) {
		$import_log_extension = $ftp_log_extension[$i];
		$timezone_offset = $ftp_offset[$i];
		if ($i != 0) {
			if ($html) echo '<tr><td class="" align="center" height="25" width="550" colspan="2"></td></tr>';
			echo "\n";
		}
		if ($ftp_port[$i]<1 && ($ftp_hostname[$i]=="localhost" || $ftp_hostname[$i]=="127.0.0.1"))
		{
			tablerow('Connecting to server:', "Local host file copy/move.");
			foreach($ftp_dir[$i] as $dir)
			{
				if (!empty($dir) && is_dir($dir))
				{
					$dl_start = time();
					$dl_files = 0;
					$dl_bytes = 0;
					$error = false;
					tablerow("Current directory is:", $dir);
					if ($dh = opendir($dir))
					{
						while (($file = readdir($dh)) !== false) {
							if($file == "." || $file == ".." || filetype($dir."/".$file)!="file"){continue;}
							// <-- Begin bz2 decompression routine -->
							if (substr($file,-4)==".bz2") // decompress log data now
							{
								$outputfile = "logs/".substr($file, strlen($file) - 4).".log";
								$bz = bzopen($dir."/".$file, "r") or ftp_error("bz2 Decompression failed on $filename");
								$decompressed_file = '';
								while (!feof($bz))
								{
									$decompressed_file .= bzread($bz, 4096);
								}
								bzclose($bz);
								$fhandle = fopen($outputfile,"w");
								if ($fhandle)
								{
									fwrite($fhandle,$decompressed_file);
									fclose($fhandle);
								}
								tablerow("Decompressing bz2 file...", "done!");
								$filename = $outputfile;
								if ($ftp_delete[$i]==1)
									unlink($dir."/".$file);
								else
									rename($dir."/".$file,$dir."/".$file.".processed");

								if ($ftp_add_tz)
								{
									tablerow('Adding TZ Offset', $timezone_offset." hour".($timezone_offset > 1 || $timezone_offset < -1 ? "(s)" : "")."...");
									$fhandle = fopen('logs/'.$file,"a");
									if ($fhandle)
									{
										fwrite($fhandle,"0.00	info	GMT_Offset	".$timezone_offset);
										fclose($fhandle);
									}
								}

							}
							// <-- End bz2 decompression routine --> --// Timo @ 05/07/05
							else if (substr($file,-4)==".log")
							{
								$dl_files++;
								$size = filesize($dir."/".$file);
								$dl_bytes += $size; 
								$rhandle = fopen($dir.'/'.$file,"r");
								$fhandle = fopen('logs/'.$file,"x+");
								if ($rhandle && $fhandle)
								{
									tablerow('Transferring...', "Contents from \$rhandle to \$fhandle + adding TZ.");
									$contents = fread($rhandle, filesize($dir."/".$file));
									fwrite($fhandle,$contents);
									fwrite($fhandle,"0.00	info	GMT_Offset	".$timezone_offset);
									fclose($fhandle);
									fclose($rhandle);
								}
								if ($ftp_delete[$i]==1)
									unlink($dir."/".$file);
								else
									rename($dir."/".$file,$dir."/".$file.".processed");
	
								$result = "OK [".$import_log_extension." file] (". number_format(round(($size / 1024), 0)) ." KB)";
	
								tablerow('Copied...', "$file -> $result");
							}
						}
						$dl_kb = number_format(round(($dl_bytes / 1024), 0));
						$dl_time = time() - $dl_start;
						tablerow("Copied:", "$dl_files ". ((count($dl_files) == 1) ? 'file' : 'files') ." ($dl_kb KB) in $dl_time seconds");
						closedir($dh);
					}
				}
			}
		}
		else
		{
			tablerow('Connecting to server:', $ftp_hostname[$i] .':'. $ftp_port[$i]);
			if (!$ftp->SetServer($ftp_hostname[$i], $ftp_port[$i])) {
				ftp_error("Unable to set server: ". $ftp->lastmsg); $ftp->quit(true); continue;
			}
			if (!$ftp->connect()) {
				ftp_error("Unable to connect to server: ". $ftp->lastmsg); $ftp->quit(true); continue;
			}
	
			tablerow('', "Connected, now logging in...");
			if (!$ftp->login($ftp_uname[$i], $ftp_upass[$i])) {
				ftp_error("Login failed!\nBad username/password?"); $ftp->quit(true); continue;
			}
			tablerow('', "Logged in!");
	
			if (!$ftp->SetType(FTP_BINARY)) {
				ftp_error("Could not set type: ". $ftp->lastmsg); $ftp->quit(true); continue;
			}
			if (!isset($ftp_passive[$i]) or $ftp_passive[$i]) {
			tablerow("", "Setting passive mode");
				if(!$ftp->Passive(true)) {
					ftp_error("Could not set passive mode: ". $ftp->lastmsg); $ftp->quit(true); continue;
				}
			} else {
				tablerow("", "Setting active mode");
				if(!$ftp->Passive(false)) {
					ftp_error("Could not set active mode: ". $ftp->lastmsg); $ftp->quit(true); continue;
				}
			}
			if (($pwd = $ftp->pwd()) === false) {
				ftp_error("Unable to retrieve current working directory"); $ftp->quit(true); continue;
			}
			tablerow("Current directory is:", $pwd);
			$dl_start = time();
			$dl_files = 0;
			$dl_bytes = 0;
			$error = false;
			foreach($ftp_dir[$i] as $dir) {
				if (!empty($dir)) {
					if (!$ftp->chdir($dir)) {
						ftp_error("Unable to change directory to: $dir"); $ftp->quit(true); continue;
					}
					tablerow('', "Changing directory to: $dir");
					if (($pwd = $ftp->pwd()) === false) {
						ftp_error("Unable to retrieve current working directory"); $ftp->quit(true); continue;
					}
					tablerow("New directory is:", $pwd);
				}
				
				if (($filelist = $ftp->nlist()) === false) {
					ftp_error("Unable to retrieve file list"); continue;
				}
				if (count($filelist) == 0) {
					continue;
				}
				foreach ($filelist as $filename) {
					if (stristr($filename, 'Unreal.ngLog') === FALSE) {
						tablerow('','Ignoring '.$filename.' - not Unreal.ngLog format');
						continue;
					}
					if ((substr($filename, strlen($filename) - strlen($import_log_extension)) == $import_log_extension)
					or ($import_utdc_download_enable and substr($filename, strlen($filename) - strlen($import_utdc_log_extension)) == $import_utdc_log_extension)) {
					} else {
						continue;
					}
					if ((substr($filename, 0, strlen($import_log_start)) == $import_log_start)	
					or ($import_utdc_download_enable and substr($filename, 0, strlen($import_utdc_log_start)) == $import_utdc_log_start)) {
					} else {
						continue;
					}
					$size = $ftp->get($filename, 'logs/' . $filename);
					if ($size === FALSE) {
						$result = 'ERROR!';
						$error = true;
					} else {
						// $result line changed to include extension debug output --// Timo @ 07/07/05
						$result = "OK [".$import_log_extension." file] (". number_format(round(($size / 1024), 0)) ." KB)";
						// <-- Begin bz2 decompression routine -->
						if ($import_log_extension==".bz2") // decompress log data now
						{
							$outputfile = "logs/".substr($filename, strlen($filename) - strlen($import_log_extension)).".log";
							$bz = bzopen("logs/".$filename, "r") or ftp_error("bz2 Decompression failed on $filename");
							$decompressed_file = '';
							while (!feof($bz))
							{
								$decompressed_file .= bzread($bz, 4096);
							}
							bzclose($bz);
							$fhandle = fopen($outputfile,"w");
							if ($fhandle)
							{
								fwrite($fhandle,$decompressed_file);
								fclose($fhandle);
							}
	// 						file_put_contents($outputfile,$decompressed_file); // --// Supported in PHP5 only :(
							tablerow("Decompressing bz2 file...", "done!");
							$filename = $outputfile;
						}
						// <-- End bz2 decompression routine --> --// Timo @ 05/07/05
						// <-- Begin Timezone log append --> --// Timo @ 16/09/05
						if ($ftp_add_tz)
						{
							$fhandle = fopen('logs/'.$filename,"a");
							if ($fhandle)
							{
								fwrite($fhandle,"0.00	info	GMT_Offset	".$timezone_offset);
								fclose($fhandle);
							}
	// 						file_put_contents('logs/'.$filename,"0.00	info	GMT_Offset	".$timezone_offset,FILE_APPEND); // --// Supported in PHP5 only :(
						}
						// <-- End Timezone log append --> --// Timo @ 16/09/05
						$dl_files++;
						$dl_bytes += $size;
					}
					tablerow(($dl_files == 1) ? 'Downloading...' : '', "$filename -> $result");
					if (!isset($ftp_delete[$i]) or $ftp_delete[$i]) $ftp->delete($filename);
				}
			}	
			$dl_kb = number_format(round(($dl_bytes / 1024), 0));
			$dl_time = time() - $dl_start;
			tablerow("Downloaded:", "$dl_files ". ((count($filelist) == 1) ? 'file' : 'files') ." ($dl_kb KB) in $dl_time seconds");
	
			if ($error) {
				ftp_error('There were errors when downoading (some) files!');
			}
	
			tablerow("Disconnecting...", "done!");
			$ftp->quit(true);
		} // check for local host
	}

	// update timestamp --// Updated 06/08/05 Timo
	if ($html) echo '<tr><td class="" align="center" height="25" width="550" colspan="2"></td></tr>';
	echo "\n";
	
	if (!$ug_ftp_error) {
		$file = fopen('./includes/ftptimestamp.php', 'wb+', 1);
		if ($file)
		{
			$timestamp = time();
			tablerow("Writing timestamp....","[".$timestamp."]",false);
			fwrite($file, $timestamp) or tablerow("Error writing timestamp!"," ",true);
			tablerow("Timestamp written."," ",false);
			fclose($file);
		}
	}

	if (!$ftp_debug and $html) echo '</table><br />';
	echo "\n\n";
	// --// End timestamp update.

}


$fname = 'includes/ftptimestamp.php';
$timestamp = 0;

if(file_exists($fname))
{
	$file = fopen($fname, 'rb');
	$timestamp = trim(my_fgets($file));
	fclose($file);
}
else // Added debug check for timestamp file --// Timo 05/07/05
{
	if ($html)
		echo "<pre>Could not find timestamp file</pre>";
}
if(!$timestamp || (time() - $timestamp) > $ftp_interval*60) {
	if ($timestamp) {
		if ($html) echo '<p class="pages">';
		echo "Last FTP update more than $ftp_interval minutes ago [stamp:$timestamp], starting update ($ftp_type): \n";
		if ($html) echo '</p>';
	}
	if ($ftp_debug) {
		if ($html) echo '<table class="box" border="0"><tr><td class="smheading" width="550">';
		echo "FTP Debugging Output:\n";
		if ($html) echo '</td></tr><tr><td width="550" align="left"><pre>';
	}
	$ftp = new ftp($ftp_debug, $ftp_debug);
	ftpupdate();
	if ($ftp_debug and $html) echo '</pre></td></tr></table><br />';
} else {
	if ($html) echo '<p class="pages">';
	echo "Last FTP update was ". round(((time() - $timestamp) / 60), 0) ." minutes ago, no update necessary\n";
	if ($html) echo '</p>';
}

?>
