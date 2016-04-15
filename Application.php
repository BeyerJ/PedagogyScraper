<?php

require_once 'autoload.php';

class Application {
	public $on;
	protected $url;
	protected $xpath;
	protected $university;
	protected $courses = array();
	protected $programs = array();
	protected $available_scrapers = ['AcalogScraper', 'UofCalgaryScraper'];
	protected $scrapers = array();
	protected $chosen_scraper;
	protected $mysql;
	protected $menu_items = array(
		"0" => "Show status",
		"1" => "Input a URL",
		"2" => "Select university",
		"3" => "Run a test scrape",
		"4" => "Attempt a manual scrape",
		"5" => "Reset scraper queries to default values",
		"6" => "Run a scrape with current queries",
		"7" => "Echo course from already scraped",
		"8" => "Output courses to CSV",
		"9" => "Load CSV to Database",
		"e" => "Exit"
	);


	public function __construct () {
		$this->on = true;
		foreach ($this->available_scrapers as $scraper_class) {
			$scr = new $scraper_class;
			$this->scrapers[$scr->id] = $scr;
		}
		$this->getDBConn();
	}


	public function __toString () {
		$string = "";
		foreach ($this as $key=>$value) {
			$string .= $key . ": " . $value . "\n";
		}
		return $string;
	}


	/*************************************
	METHODS FOR CONNECTING TO THE DATABASE
	**************************************/

	public function getDBConn() {
		if (!$this->mysql) {
			$this->mysql = DBConnection::getConnection();
		}
	}

	// takes an array and a table name, creates 
	public function pushInsert($table, $value_array) {
		$table_keys = self::makeTableKeys($value_array);
		$table_values = self::makeTableValues($value_array);
		$query = "INSERT INTO $table ($table_keys) VALUES ($table_values) ";
		//$this->mysql->query($query);
		echo $query . "\n";
	}


	// ?????
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


	//check if the url is valid
	public function urlIsValid ($url) {
		//TODO!
		$well_formed = filter_var($url, FILTER_VALIDATE_URL);
		if (!$well_formed) {
			echo "Badly-formed URL!\n";
			return false;
		}
		return true;
	}



	/************************************
	METHODS FOR BUILDING XPATH OUT OF URL
	*************************************/

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

	//call tidy on an html string
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


	//takes a url, calls getDataFromURL to curl data from it
	//then runs it through tidy using tidyHtml
	//then creates a dom and xpath object for it, using buildDomFromHtml
	public function takeUrlReturnXpath ($url) {
		$dirty_html = self::getDataFromURL($url);
		$tidy_html = self::tidyHtml($dirty_html);
		$xpath = self::buildDomFromHtml($tidy_html);
		return $xpath;
	}


	/*******************************************
	METHODS FOR SETTING UP THE UNIVERSITY OBJECT
	********************************************/




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

	public function randomCourse($courses) { // fix this 
		$e = count($courses); //
		if ($e) {
			$i = rand(1 ,$e - 1);
			return $courses[$i];
		} else {
			return null;
		}
	}


	public function addInfo($ui) {
		$start = $ui->questionYN(UserInterface::UNI_INFO_PROMPT, "Good Choice\n");
		if ($start) {
			foreach ($this->properties as $key => $value) {
				echo UserInterface::UNI_INFO;
				$prompt = 'Enter info for ' . $key . "\n";
				$answer = $ui->userPrompt($prompt);
				$this->properties[$key] = $answer;

			}
		//return $this->properties;
		}
		
	}


	/*********************
	METHODS FOR MAIN MENU
	*********************/

	public function showStatus() {
		if ($this->url) {
			echo "-- Current URL: " . $this->url . "\n";
		} else {
			echo "-- No URL chosen.\n";
		}
		if ($this->chosen_scraper) {
			echo "-- Chosen scraper: '" . $this->chosen_scraper->name . "' (class: " . get_class($this->chosen_scraper) . ")\n";
		} else {
			echo "-- No scraper chosen.\n";
		}
		if ($this->university) {
			echo "-- University information: \n";
			echo $this->university;
		} else {
			echo "-- No university set up.\n";
		}

	}



	public function mainMenu() {
		echo UserInterface::GREETING;
		$user_command = UserInterface::askForSetReply('MAIN_MENU', $this->menu_items, true);
		switch ($user_command) {
			case "0":
				echo "***************\nSTATUS\n***************\n";
				$this->showStatus();
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
				/*$answer = UserInterface::questionYN("Is this a new University", "OK\n");
				if ($answer) {
						$new_uni = new University;
						$new_uni->addInfo($ui);
						$this->university = $new_uni;
					}*/	
				}
				break;
			case "2":
				echo "***************\nSELECTING UNIVERSITY\n***************\n";
				break;
			case "3":
				echo "***************\nRUNNING TEST SCRAPE\n***************\n";
				if ($this->chosen_scraper) {
					$this->chosen_scraper->testPageScrape();
				} else {
					echo UserInterface::NO_URL;
				}
				break;
			case "4":
				echo "***************\nRUNNING MANUAL SCRAPE\n***************\n";
				if ($this->chosen_scraper) {
					$this->chosen_scraper->manualScrapeCourse();
				} else {
					echo UserInterface::NO_URL;
				}
				break;
			case "5":
				echo "***************\nRESETTING SCRAPER QUERIES\n***************\n";
				if ($this->chosen_scraper) {
					$this->chosen_scraper->resetScraperQueries();
				} else {
					echo UserInterface::NO_URL;
				}
				break;
			case "6":
				echo "***************\nRUNNING SCRAPE\n***************\n";
				$purge = UserInterface::questionYN("Would you link to purge the currently scraped courses from the application's memory?");
				if ($purge) {
					$this->courses = array();
				}
				if ($this->chosen_scraper) {
					$this->chosen_scraper->runScrape();
					$save_result = UserInterface::questionYN("Would you like to add the results of this scrape to the application's memory?");
					if ($save_result) {
						$this->courses = $this->courses + $this->chosen_scraper->courses;
					}
				} else {
					echo UserInterface::NO_URL;
				}
				break;
			case "7":
				echo "***************\nECHOING COURSE FROM ALREADY SCRAPED\n***************\n";
				if ($this->courses) {
					$count = count($this->courses);
					echo "There are currenly " . $count . " scraped courses in the application's memory.\n";
					$random = UserInterface::askForSetReply('ECHO_COURSE', ["0" => "Choose a particular course by number", "1" => "Output a random course"]);
					if (!$random) {
						for($n = 0; $n < $count; $n++) {
							$course_numbers[$n+1] = "Scraped course number $n";
						}
						$output_course = UserInterface::askForSetReply('OUTPUT_PARTICULAR_COURSE', $course_numbers, false);
						print_r($this->courses[$output_course-1]);

					} else {
						$course = self::randomCourse($this->courses);
						print_r($course);
					}
				} else {
					echo UserInterface::NO_COURSES;
				}
				break;
			case "8":
				echo "***************\nOUTPUTTING COURSES TO CSV\n***************\n";
				/*
				if (!$this->university) {
					$new_uni = new University;
					$ui = new UserInterface();
					$new_uni->addInfo($ui);
					$this->university = $new_uni;
				}
				$csv = new CSV;
				$csv->makeCSV($this->university);
				$csv->addCourses($this->courses);
				$csv->writeObjects();
				
				*/
				break;
			case "9":
				echo "***************\nLOAD CSV TO DATABASE\n***************\n";
				$csv = new CSV();
				$saved = $csv->readSavedData();
				if ($saved) {
					$choice = UserInterface::askForSetReply('CHOOSE_CSV_FILE', $saved);
					
					$filename = $saved[$choice];
					$csv->filename = $filename;
					$courses = $csv->getCourses();
					$course = self::randomCourse($courses);
					print_r($course);

					$uni = $csv->getUni();
					print_r($uni);
				} else {
					echo "There are no csv files available.\n";
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