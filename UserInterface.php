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
const EDIT_DATA = "Would you like to edit a property?\n";
const EDIT_CHOOSE = "Which property would you like to edit?\n";


const WRONG_OPTION = "Please choose one of the options or type 'exit' to quit.\n";
const NULL_ANSWER = "Please enter a response or type 'exit' to quit.\n";
const CHOOSE_OPTION = "Choose one of the options below.\n";
const ASSIGN_COURSE_OPTION_TO_TEXT = "Which course property does this text correspond to?\n";
const ASSIGN_COURSE_OPTION_TO_REGEX = "Which course property are you adding RegEx for?\n";
const ASSIGN_REGEX = "Enter the regular expression below, encased in slashes. Example: /(?<year>[0-9]{4}-[0-9]{4})/\n";
const DOES_THE_REGEX_WORK = "Does this regex work like you want it to?\n";

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


public function editEntry() {
	$answer = self::questionYN(self::EDIT_DATA, self::REPLY_TO_YES);
	if ($answer == true) {
		$property = self::userPrompt(self::EDIT_CHOOSE);
		echo $property . " is the thing you chose to edit\n";
		return $property;
	}
}


public static function askForSetReply($prompt, $options, $output_options) {
	echo "\n";
	if ($output_options) {
		echo self::CHOOSE_OPTION;
		print_r($options);
	}
	$answer = self::userPrompt(constant("self::$prompt"));
	while ($answer != "exit" AND !array_key_exists($answer, $options)) {
		echo "\n";
		echo "Options:\n";
		print_r($options);
		$answer = self::userPrompt(self::WRONG_OPTION . "\n" . constant("self::$prompt"));
	}
	echo "--------------------------\n";
	if ($answer == "exit") {
		exit;
	} elseif (array_key_exists($answer, $options)) {
		return $answer;	
	}

}


public static function askForInput($prompt) {
	echo "\n";
	$answer = self::userPrompt(constant("self::$prompt"));
	while (!$answer) {
		echo "\n";
		$answer = self::userPrompt(self::NULL_ANSWER . "\n" . constant("self::$prompt"));
	}
	echo "--------------------------\n";
	if ($answer == "exit") {
		exit;
	} else {
		return $answer;	
	}
}




}


?>