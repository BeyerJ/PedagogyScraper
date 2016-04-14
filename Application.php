<?php

require_once 'autoload.php';

class Application {
	public $on;
	protected $url;
	protected $xpath;
	protected $university_id;
	protected $courses = array();
	protected $programs = array();
	protected $available_scrapers = ['AcalogScraper', 'UofCalgaryScraper'];
	protected $scrapers = array();
	protected $chosen_scraper;
	protected $menu_items = array(
		"0" => "Show status",
		"1" => "Input a URL",
		"2" => "Run a test scrape",
		"3" => "Attempt a manual scrape",
		"4" => "Reset scraper queries to default values",
		"e" => "Exit"
	);


	public function __construct () {
		$this->on = true;
		foreach ($this->available_scrapers as $scraper_class) {
			$scr = new $scraper_class;
			$this->scrapers[$scr->id] = $scr;
		}
	}

	protected $mysql;

	//FIX THIS! NO PROPERTIES ARRAY, SO THIS WON'T WORK
	public function __get ($varname) {
		//if the keyname exists in the array, then return it
		//otherwise return null
		//this is sometimes called a WHITELIST
		if ($varname == "url") {
			return $this->properties[$varname];
		} else {
			return null;
		}
	}

	//SETTERS

	//FIX THIS! NO PROPERTIES ARRAY, SO THIS WON'T WORK
	public function __set ($varname, $value) {
		//if the keyword exist in the properties array, set it to be the value
		if ($varname == "url") {
			$this->properties[$varname] = $value;
		}
	}


	public function __toString () {
		$string = "";
		foreach ($this as $key=>$value) {
			$string .= $key . ": " . $value . "\n";
		}
		return $string;
	}

		public function __toArray() {

	}

	public function getDBConn() {
		if (!$this->mysql) {
			$this->mysql = DBConnection::getConnection();
		}
	}

	// takes an array and puts it in the table of your choice
	public function pushInsert($table, $value_array) {
		$table_keys = self::makeTableKeys($value_array);
		$table_values = self::makeTableValues($value_array);
		$query = "INSERT INTO $table ($table_keys) VALUES ($table_values) ";
		//$this->mysql->query($query);
		echo $query . "\n";
	}


	public function pushCourses($table) {
		for ($i=0; $i < count($this->courses); $i++) { 
			$properties = $this->courses[$i]->properties;
			self::pushInsert($table, $properties);
		}
	}

	// looks at the university object and then compare to entries in the database
	public function compareData($table, $value_array) {
		$university_title = $value_array['university_title'];
		$country = $value_array['country'];
		$query = "SELECT id, university_title, country, province_state FROM $table WHERE university_title LIKE '{$university_title}' AND country = '{$country}'";
		//$this->mysql->query($query);
		// $results = ;
		// return $results; // make this into an associative array
		echo $query . "\n";
	}

	public function makeTableKeys($value_array) {
		$table_keys = '';
		foreach ($value_array as $key => $value) {
			$table_keys = $table_keys . $key . ",";
		}
		$table_keys = rtrim($table_keys, ",");
	}

	public function makeTableValues($value_array) {
		$table_values = '';
		foreach ($value_array as $key => $value) {
			$table_values = $table_values . "'" . $value . "'" . ",";
		}
		$table_values = rtrim($table_values, ",");
		return $table_values;
	}


	public function urlIsValid ($url) {
		//TODO!
		$well_formed = filter_var($url, FILTER_VALIDATE_URL);
		if (!$well_formed) {
			echo "Badly-formed URL!\n";
			return false;
		}
		return true;
		//$headers = @get_headers($url);
		//check if the header contains 404, 403 or 500, exit; otherwise return url
	}

	//get text HTML data from a url using cURL
	public function getDataFromURL($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, 'PedagogyScraperbot/1.0 (+http://github.com/BeyerJ/PedagogyScraper)');
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}


	//build a DOM object from HTML text using DomDocument
	//then build an XPath object off of this DOM object, using DomXPath
	public function buildDomFromHtml($html) {
		$dom = new DomDocument;
		$dom->preserveWhiteSpace = false;
		@$dom->loadHTML($html);

		$xpath = new DomXPath($dom);
		return $xpath;
	}


	public function tidyHtml($html) {
		$config = array(
           'indent'         => true,
           'output-xhtml'   => true,
           //'clean'			=> true,
           'bare'			=> true,
           'wrap'           => 0,
           'anchor-as-name' => false,
           'preserve-entities'	=> true,
           'drop-empty-paras' => false,
           'char-encoding' => 'utf8',
           'input-encoding' => 'utf8',
           'output-encoding' => 'utf8',
           );

		$tidy = new tidy;
		$tidy->parseString($html, $config, 'utf8');
		$tidy->cleanRepair();

		// Output
		return $tidy;
	}


	public function takeUrlReturnXpath ($url) {
		$dirty_html = self::getDataFromURL($url);
		$tidy_html = self::tidyHtml($dirty_html);
		$xpath = self::buildDomFromHtml($tidy_html);
		return $xpath;
	}


	// Lets the user change one property of a university object, though I bet it could do any child of the CalendarObject, needs testing
	public function editProperty($property, $university) {
		echo $property . " is equal to " . $university->$property . "\n";
		$new_value = UserInterface::userPrompt("What would you like to change " . $property . " to?\n");
		$check = "You chose " . $new_value . ", is this ok?\n";
		$editing = true;
		while ($editing) {
			$answer = UserInterface::questionYN($check, "I'll change it then\n");
			if ($answer == true) {
				$university->$property = $new_value;
				echo "I have changed " . $property . " to " . $university->$property . "\n";
				$editing = false;
			} else {
				$new_value = UserInterface::userPrompt("What would you like to change " . $property . " to?\n");
				$answer = UserInterface::questionYN($check, "I'll change it then\n");
			}	
		}
	}

	// choose random result (has class property - courses or course objects)
	public function randomCourse() { // fix this 
		$e = count($this->courses); //  
		$i = rand(1 ,$e);
		return $this->courses[$i];
	}


	public function mainMenu() {
		echo UserInterface::GREETING;
		$user_command = UserInterface::askForSetReply('MAIN_MENU', $this->menu_items, true);
		switch ($user_command) {
			case "0":
				echo "***************\nSTATUS\n***************\n";
				echo "I'm scraper, hello.\n\n\n";
				break;
			case "1":
				echo "***************\nENTERING NEW URL\n***************\n";
				$url = UserInterface::askForInput('CATALOG_URL_PROMPT');
				if ($this->urlIsValid($url)) {
					$this->url = $url;
					$xpath = $this->takeUrlReturnXpath($url);
					foreach ($this->scrapers as $scraper) {
						$scraper->url = $url;
						$scraper->xpath = $xpath;
						$applies = $scraper->checkIfApplies();
						if($applies) {
							echo "\n\nChose " . $scraper->name . "\n";
							$this->chosen_scraper = $scraper;
							break;
						}
					}
					if (!$applies) {
						echo UserInterface::NO_SCRAPER;
						$this->url = null;
					}
				}
				break;
			case "2":
				echo "***************\nRUNNING TEST SCRAPE\n***************\n";
				if ($this->chosen_scraper) {
					$this->chosen_scraper->testPageScrape();
				} else {
					echo UserInterface::NO_URL;
				}
				break;
			case "3":
				echo "***************\nRUNNING MANUAL SCRAPE\n***************\n";
				if ($this->chosen_scraper) {
					$this->chosen_scraper->manualScrapeCourse();
				} else {
					echo UserInterface::NO_URL;
				}
				break;
			case "4":
				echo "***************\nRESETTING SCRAPER QUERIES\n***************\n";
				if ($this->chosen_scraper) {
					$this->chosen_scraper->resetScraperQueries ();
				} else {
					echo UserInterface::NO_URL;
				}
				break;
			case "e":
				echo UserInterface::BYE;
				$this->on = false;
				break;
		}

	}

} 

?>