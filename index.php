<?php
	$myfile = fopen("cache.txt", "r") or die("Unable to open file!");
	echo fread($myfile,filesize("cache.txt"));
	fclose($myfile); 
?>