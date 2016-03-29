<?php

class UserInterface {

protected $OS = PHP_OS;

// prompts go here so that we only have to change them once
const CATALOG_URL_PROMPT = "Please enter the university course catalog URL:\n";
const SELECT_SCRAPER_PROMPT = "Which scraper would you like to use?\n";
const FIRST_ENTRY = "The first entry of the scraper is:\n";
const ANOTHER_ENTRY_PROMPT = "Would you like to see another entry?\n";
const REPLY_TO_YES = "Awesome! Let me do the thing!\n";

/******METHODS******/

/*

// Will need to test with a DB Connection
public function __construct() {
		//building a connection with a database -- the pipeline is an object
		//our DBConnection class creates a static connection and always returns the same one once it's created
		//we're using default $id=-1 because we want our constructor to grab data from the database
		//if the object already exists
		//or create a new one and push it to the database if it doesn't
		
		$this->mysql = DBConnection::getConnection();
		if (empty($this->mysql)) {
			echo "something went wrong\n";
		}

		//add properties to the properties array, pulling them from the database table (the columns)
		$this->initializeFromDatabase();
}

*/

//Prompt user for input using a $prompt string.
//Check if the script is run on a Windows machine (readline doesn't work on Win).
public function userPrompt($prompt, $length = 1024) {
	$OS = PHP_OS;
	if ($OS == 'WINNT') {
		echo $prompt;
		$input = stream_get_line(STDIN, $length, PHP_EOL);
	} else {
		$input = readline($prompt);
	}
	return $input;
}

//Prompt user for catalog URL
public function askForURL() {
		//Prompting the user for a URL
		return self::userPrompt(self::CATALOG_URL_PROMPT, 2048);
	}

// Choose Scraper (asking the user)
public function chooseScraper($scrapers) {
	echo "The following scrapers are available:\n";
	foreach ($scrapers as $scraper) {
		echo $scraper . "\n";
	}
	return self::userPrompt(self::SELECT_SCRAPER_PROMPT, 2048);
}

// make results human readable
public function makeReadable($results) {
	foreach ($results as $result => $value) {
		echo $result . ": " . $value . "\n";
	}
}

// Display results of scraper (first entry)
public function displayResults($results) {
	echo self::FIRST_ENTRY;
	$i = 0;
	echo $results[$i] . "\n";
	self::questionYN(self::ANOTHER_ENTRY_PROMPT, self::REPLY_TO_YES);
	$e = count($results);
	$i = rand(1 ,$e);
	echo $results[$i] . "\n";
}

// save results (this needs work)
public function saveResults() {
	self::questionYN('Would you like to save the results of this scrape?', 'I shall do that then');
	/// send the relevant object to the db stuff?
}

// choose entry to display



// scrape another?



// ask yes or no question
public function questionYN($prompt1, $prompt2) {
	$answer = '';
	while ($answer != 'yes') {
		$answer = self::userPrompt($prompt1);
		if ($answer == 'no') {
			exit;
		} else {
			"Please answer yes or no \n";
		}
	}
	echo $prompt2;
}

}


?>