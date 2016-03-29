<?php

require_once 'autoload.php';

//For lack of a better solution this is going to be the main script that calls methods from the various classes
//This is where the majority of function and method calls form the flowchart would go.

// Variables (just so the php notices will go away)
$catalog_url = null;
$UI = null;
$STATUS = null;
$university = null;

/*********************
USER STARTS THE SCRIPT
**********************/

//Create UserInterface
$UI = new UserInterface();
//Create ApplicationStatus
$STATUS = new ApplicationStatus();

//Ask for the general URL, using a method from UserInterface
$catalog_url = $UI->askForURL();


//If the method returned FALSE, stop the script
if(!$catalog_url) {
	exit;
}

//Save the URL to the ApplicationStatus
$STATUS->$url = $catalog_url;

/* 

University Object Stuff

*/


// create university object

$university = new University();

// scrape university info (name and such)

// output object to console

// ask user if ok

// if no let user edit field by field

// once done ask to push to database

// check if database already has that university

// if university exists retrieve from database and ask user if it should just use this

// use new record or create record

// if the university is not in database create an entry with appropriate queries

// return id of university

// save id to university objects's properties




?>