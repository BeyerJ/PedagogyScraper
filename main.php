<? php

//For lack of a better solution this is going to be the main script that calls methods from the various classes
//This is where the majority of function and method calls form the flowchart would go.

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


?>