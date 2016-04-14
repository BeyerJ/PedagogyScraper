<?php

require_once 'autoload.php';
//include_once '/scraperscripts/GeneralScraper.php';

//For lack of a better solution this is going to be the main script that calls methods from the various classes
//This is where the majority of function and method calls form the flowchart would go.

// Variables (just so the php notices will go away)


/*********************
USER STARTS THE SCRIPT
**********************/

$app = new Application();

while($app->on) {
	$app->mainMenu();
}

?>