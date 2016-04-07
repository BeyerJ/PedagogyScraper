<?php

require_once 'autoload.php';
//include_once '/scraperscripts/GeneralScraper.php';

//For lack of a better solution this is going to be the main script that calls methods from the various classes
//This is where the majority of function and method calls form the flowchart would go.

// Variables (just so the php notices will go away)
$catalog_url = null;
$UI = null;
$STATUS = null;
$university = null;
$keep_scraping = true;


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
	$ui = new UserInterface();
	$app = new Application();

while ($keep_scraping == true) {

	// I attempted to make it so the user can input all the info for the university to start, but it keeps saying the properties 
	// do not exist. I think it is looking at the values and not the keys, which seems wierd
	// but you can change things which is exciting
	// unless my method changes the name of the key and not the value. Shit
	$university = new University();
	foreach ($university->properties as $property) {
		$app->editProperty($property, $university);
	}
	
	

	$url = $general->newScrape();
	echo "You have given me a URL\n";
	$general->buildDOM($url);
	echo "I build a DOM object\n";
	/* 

	University Object Stuff
$query = '(//@alt)'; 
	echo "I have a query\n";
	$title = $general->scrapeUniversityInfo($query);
	echo "the title is " . $title . "\n";
	echo "Also I scraped a thing\n";
/*
	$query1 = '(//@alt)'; 
	$country = $general->scrapeUniversityInfo($query1);
	echo "the country is " . $country . "\n";

	$query2 = '(//@alt)'; 
	$province = $general->scrapeUniversityInfo($query2);
	echo "the title is " . $province . "\n";

	$query3 = '(//@alt)'; 
	$type = $general->scrapeUniversityInfo($query3);
	echo "the title is " . $type . "\n";
*/

	
	// create university object
	


	//$university = new University();
	$answer = $general->UI->startScrape();
	if ($answer == false) {
		$keep_scraping = false;
		echo "I dont wanna\n";
	}

}






?>