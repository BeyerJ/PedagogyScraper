<?php

$path = array('.\scraperscripts', '.\results');

for ($i=0; $i < count($path); $i++) { 
	set_include_path(get_include_path() . PATH_SEPARATOR . $path[$i]);
}


function __autoload($classname) {
	$filename = $classname . '.php';
	require_once $filename;
	// had to look 
	//$filename = '/scraperscripts' . $classname. '.php'; 
	//require_once $filename;
}

/*
if (PHP_OS == 'WINNT') {
			require_once("/../autoload.php");
		} else {
			require_once '../autoload.php';
		}
*/
?>