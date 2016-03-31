<?php

class UserInterface {

protected $OS = PHP_OS;

// prompts go here so that we only have to change them once
const CATALOG_URL_PROMPT = "Please enter the university course catalog URL:\n";
const SELECT_SCRAPER_PROMPT = "Which scraper would you like to use?\n";
const FIRST_ENTRY = "The first entry of the scraper is:\n";
const ANOTHER_ENTRY_PROMPT = "Would you like to see another entry?\n";
const REPLY_TO_YES = "Awesome! Let me do the thing!\n";
const SCRAPER_REPORT_PROMPT = "The following scrapers are available:\n";
const SAVE_RESULTS_PROMPT = "Would you like to save the results of this scrape?\n";
const WILL_SAVE = "I shall do that then\n";
const YES_NO_PROMPT = "Please answer yes or no \n";
const NEW_SCRAPE = "Would you like to start a scrape?\n";
//const = "";

/******METHODS******/


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
	echo self::SCRAPER_REPORT_PROMPT;
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
	if (self::questionYN(self::ANOTHER_ENTRY_PROMPT, self::REPLY_TO_YES) == true) {
		$e = count($results);
		$i = rand(1 ,$e);
		echo $results[$i] . "\n";
	}
	
}

// save results (this needs work)
public function saveResults() {
	self::questionYN(self::SAVE_RESULTS_PROMPT, self::WILL_SAVE);
	/// send the relevant object to the db stuff?
}

// choose entry to display



// scrape another?
public function startScrape() {
	$answer = self::questionYN(self::NEW_SCRAPE, self::REPLY_TO_YES);
	if ($answer == true) {
			return true;
		} else {
			return false;
		}
}


// ask yes or no question
public function questionYN($prompt1, $prompt2) {
	$asking = true;
	while ($asking) {
		$answer = self::userPrompt($prompt1);
		if ($answer == 'no') {
			echo "the answer was no\n";
			return false;
		} elseif ($answer == 'yes') {
			echo $prompt2;
			return true;
			
		} else {
			echo self::YES_NO_PROMPT;
		}
	}
	
}

}


?>