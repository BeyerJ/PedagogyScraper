<?php

class UserInterface {

protected $OS = PHP_OS;

// prompts go here so that we only have to change them once
const CATALOG_URL_PROMPT = "Please enter the university course catalog URL:\n";
const SELECT_SCRAPER_PROMPT = "Which scraper would you like to use?\n";
const FIRST_ENTRY = "The first entry of the scraper is:\n";
const ANOTHER_ENTRY_PROMPT = "Would you like to see another entry?";
const REPLY_TO_YES = "Awesome! Let me do the thing!\n";
const SCRAPER_REPORT_PROMPT = "The following scrapers are available:\n";
const SAVE_RESULTS_PROMPT = "Would you like to save the results of this scrape?";
const WILL_SAVE = "I shall do that then\n";
const YES_NO_PROMPT = "Please answer yes or no \n";
const NEW_SCRAPE = "Would you like to start a scrape?";
const EDIT_DATA = "Would you like to edit a property?";
const EDIT_CHOOSE = "Which property would you like to edit?\n";
const UNI_INFO_PROMPT = "Would you like to enter the information for each university property?";
const UNI_INFO = "Please enter the information for each property\n";
const WRONG_OPTION = "Please choose one of the options or type 'exit' to quit.\n";
const NULL_ANSWER = "Please enter a response or type 'exit' to quit.\n";
const CHOOSE_CSV_FILE = "Choose one of the available csv files to load into the database.\n";
const CHOOSE_OPTION = "Choose one of the options above.\n";
const CHOOSE_OPTION_REMINDER = "Choose one of the options below.\n";
const ANY_KEY = "Press Enter to continue.\n";


/* Texts for GeneralScraper::grabTexts() */
const GRAB_TEXTS_HELP = "The manual scraper has grabbed all of the text nodes from its current position on the page. Now it will ask you to assess every one of those nodes and reply if those nodes contain any of the course information properties.\n\nIf the same text node contains data for several different course properties, you can push 'a' and attempt to add regular expressions to separate the different pieces of data.\n\nIf you're not adding regular expressions, then several text nodes can be assigned to the same course property (for example, if decide to put several different text parts to the 'note' field).\n\nIf the data in the text has already been scraped using the automatic scraper, there's no need to add it again (just reply with '0' -- 'none' in those cases)\n";
const ASSIGN_COURSE_OPTION_TO_TEXT = "Which course property that has not been scraped yet does this text correspond to?\n";
const ASSIGN_COURSE_OPTION_TO_REGEX = "Which course property are you adding RegEx for?\n";
const ASSIGN_REGEX = "Enter the regular expression below, encased in slashes. The RegEx should have the property you're looking for encased in brakets and labeled with ?<name_of_property>. Example: to match '2015-2016' as a property 'year', you can use /(?<year>[0-9]{4}-[0-9]{4})/\n";
const DOES_THE_REGEX_WORK = "Does this regex work like you want it to?\n";


const MAIN_MENU = "Enter your command:\n";
const BYE = "\n\n*************************\nThank you for using Pedagogy Scraper! See you some other time!\n";
const GREETING = "\n\nThis is Pedagogy Scraper 1.0.\n\n";
const NO_SCRAPER = "The scrapers I have don't work for this URL. Unable to proceed with scraping.\n";
const NO_URL = "No URL input to work with.\n";
const NO_COURSES = "There are currently no scraped courses in the application's memory.\n";
const NO_CSV = "No csv files available.\n";

const ECHO_COURSE = "How would you like to echo the course out?\n";
const OUTPUT_PARTICULAR_COURSE = "Which course should be echoed? Input a number from 1 to how many scraped courses there currently are.\n";

const SAVE_DATA_PROMPT = "Would you like to save the data to a CSV so you can edit it later?[yes/no]\n";
const PULL_EDITED_DATA = "Would you like to retrieve edited data from a CSV?[yes/no]\n";
const SELECT_CSV_FILE = "Please indicate the file you wish to open using the index []\n";

protected static $YESNO = array(
	"y" => true,
	"yes" => true,
	"1" => true,
	"true" => true,
	"t" => true,
	"n" => false,
	"no" => false,
	"0" => false,
	"false" => false,
	"f" => false
	);
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
public function displayResults($results) { // this should basically just get an array and print_r it to the screen or something
	echo self::FIRST_ENTRY;
	$i = 0;
	echo $results[$i] . "\n";
	if (self::questionYN(self::ANOTHER_ENTRY_PROMPT, self::REPLY_TO_YES) == true) {
		
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
public function questionYN($prompt, $acknowledge="OK") {
	$answer = strtolower(self::userPrompt($prompt . " (y/n)\n"));
	while (!array_key_exists($answer, self::$YESNO)) {
		echo self::YES_NO_PROMPT;
		$answer = strtolower(self::userPrompt($prompt . " (y/n)\n"));
	}
	return self::$YESNO[$answer];
}


public function editEntry() {
	$answer = self::questionYN(self::EDIT_DATA, self::REPLY_TO_YES);
	if ($answer == true) {
		$property = self::userPrompt(self::EDIT_CHOOSE);
		echo $property . " is the thing you chose to edit\n";
		return $property;
	}
}


public static function askForSetReply($prompt, $options, $output_options=true) {
	echo "\n";
	if ($output_options) {
		echo self::CHOOSE_OPTION_REMINDER;
		$opt = print_r($options, true);
		echo substr($opt, 6);
	}
	$answer = strtolower(self::userPrompt(constant("self::$prompt")));
	while ($answer != "exit" AND !array_key_exists($answer, $options)) {
		echo "\n";
		echo "Options:\n";
		$opt = print_r($options, true);
		echo substr($opt, 6);
		$answer = self::userPrompt(self::WRONG_OPTION . "\n" . constant("self::$prompt"));
	}
	echo "--------------------------\n";
	if ($answer == "exit") {
		exit;
	} elseif (array_key_exists($answer, $options)) {
		return $answer;	
	}

}


public static function askForInput($prompt, $custom_prompt=null) {
	echo "\n";
	if($custom_prompt) {
			$p = $custom_prompt;
	} else {
		$p = constant("self::$prompt");
	} 
	$answer = self::userPrompt($p);
	while (!$answer) {
		echo "\n";
		$answer = self::userPrompt(self::NULL_ANSWER . "\n" . $p);
	}
	echo "--------------------------\n";
	if ($answer == "exit") {
		exit;
	} else {
		return $answer;	
	}
}

public static function pressEnter($prompt=null) {
	if (!$prompt) {
		$prompt = self::ANY_KEY;
	}
	echo "\n\n";
	self::userPrompt($prompt);
	return true;
}




}


?>