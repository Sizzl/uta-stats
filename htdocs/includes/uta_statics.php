<?php
function checkStaticFile($strFilename = 'totals')
{
	$staticFile = "/static/".$strFilename.".html";
	if (file_exists($staticFile))
	{
		if (filemtime($staticFile)+1800 < time()) // file is older than 30 mins
		{
			$result = exec("./makestatics.sh");
			if (file_exists($staticFile)) // still there!
			{
				return $staticFile;
			}
			else
				return false; // error creating file
		}
		else
			return $staticFile;
	}
	else
	{
		return false;
	}
}

?>
