<?php

function __autoload($classname) {
	$filename = $classname . '.php';
	require_once $filename;
}


?>