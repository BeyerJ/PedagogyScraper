<?php

require_once 'autoload.php';
include_once '/scraperscripts/GeneralScraper.php';

//For lack of a better solution this is going to be the main script that calls methods from the various classes
//This is where the majority of function and method calls form the flowchart would go.

// Variables (just so the php notices will go away)
$catalog_url = null;
$UI = null;
$STATUS = null;
$university = null;

$endscrape = true;

/*********************
USER STARTS THE SCRIPT
**********************/

// Jocelyn -> I am moving this to the General Scraper
/* 
		//Create UserInterface
		$UI = new UserInterface();
		//Create ApplicationStatus
		$STATUS = new ApplicationStatus();
*/

// Make a General Scraper to begin
	$general = new GeneralScraper();

while ($endscrape == true) {
	$endscrape = null;
	$url = $general->newScrape();
	$general->buildDOM($url);

	/* 

	University Object Stuff

	*/
	$query = '(//@alt)'; 
	$title = $general->scrapeUniversityInfo($query);
	echo "the title is " . $title . "\n";

	$query1 = '(//@alt)'; 
	$country = $general->scrapeUniversityInfo($query1);
	echo "the country is " . $country . "\n";

	$query2 = '(//@alt)'; 
	$province = $general->scrapeUniversityInfo($query2);
	echo "the title is " . $province . "\n";

	$query3 = '(//@alt)'; 
	$type = $general->scrapeUniversityInfo($query3);
	echo "the title is " . $type . "\n";

	// create university object

	//$university = new University();

	$endscrape = $general->UI->startScrape();

}






?>