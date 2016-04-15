<?php

require_once 'autoload.php';

class Application {
	public $on;
	protected $url;
	protected $xpath;
	protected $university;
	protected $courses = array();
	protected $programs = array();
	protected $available_scrapers = array('AcalogScraper', 'UofCalgaryScraper');
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
		$this->university = new University;
	}


	public function __toString () {
		$string = "";
		foreach ($this as $key=>$value) {
			$string .= $key . ": " . $value . "\n";
		}
		return $string;
	}


	/*************************************
	METHODS FOR DEALING WITH THE DATABASE
	**************************************/

	public function getDBConn() {
		if (!$this->mysql) {
			$this->mysql = DBConnection::getConnection();
		}
	}

	protected function cleanUp ($string) {
		if ($string == "NOW()") {
			return "NOW()";
		} else if (strlen($string) > 0) {
			return "'" . $this->mysql->real_escape_string($string) . "'";
		} else {
			return "NULL";
		}
	}

	protected function cleanUpOld ($string) {
		if (strlen($string) > 0) {
			return "'" . $string . "'";
		} else {
			return "NULL";
		}
	}

	// takes an array and a table name, creates 
	public function pushInsert($table, $value_array) {
		$table_keys = $this->makeTableKeys($value_array);
		$table_values = $this->makeTableValues($value_array);
		$query = "INSERT INTO $table ($table_keys) VALUES ($table_values)";
		$this->mysql->query($query);
		//echo $query . "\n";
	}


	// push an array consisting of courses (each of which is an array of course properties) to the database
	public function pushCourses($courses) {
		if (!$this->university->id) {
			echo "Can't push courses because university id is not defined.\n";
		} else {
			foreach ($courses as $course) {
				$course["university_id"] = $this->university->id;
				$course["created"] = "NOW()";
				$this->pushInsert("course", $course);
			}
		}
	}


	public function grabUniversityId () {
		if ($this->mysql) {
			$this->lookForUniInDatabase();
			if(!$this->university->id) {
				$uni_props = $this->university->properties;
				$uni_props["created"] = "NOW()";
				$this->pushInsert("university", $uni_props);
				$this->university->id = $this->mysql->insert_id;
			}
		} else {
			echo "Can't grab university id from database because there is no database connection.\n";
		}
	}

	// create the string for a list of columns for an INSERT query
	public function makeTableKeys($value_array) {
		$table_keys = '';
		foreach ($value_array as $key => $value) {
			$table_keys .= $key . ",";
		}
		$table_keys = rtrim($table_keys, ",");
		return $table_keys;
	}

	// create the string for a list of values for an INSERT query
	public function makeTableValues($value_array) {
		$table_values = '';
		foreach ($value_array as $key => $value) {
			$table_values .= $this->cleanUp($value)  . ",";
		}
		$table_values = rtrim($table_values, ",");
		return $table_values;
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

	//Send a select query to database looking for a university with this host
	public function lookForUniInDatabase () {
		echo "Trying to find the university in the database.\n";
		$host = parse_url($this->url, PHP_URL_HOST);
		if (!$host) {
			$host = $this->university->host;
		}
		if ($host) {
			$query = "SELECT * FROM university WHERE host='" . $host . "'";
			//echo $query;
			$result = $this->mysql->query($query);
			$row = $result->fetch_assoc();
			if($row) {
				foreach ($row as $varkey => $varvalue) {
					$this->university->$varkey = $varvalue;
				}
			}
		}
		if ($this->university->id) {
			echo "Initiated university from the database.\n";
		} else {
			echo "Couldn't find the university in the database.\n";
		}
	}

	//Main method for selecting university:
	//1. Create uni object if it doesn't exist
	//2. Try to find the university in the database
	//3. If the university object has a filled-in title, output the object on the screen and ask user if they want to change it
	//4. If yes, or if the university object doesn't have a title, ask user to populate university object manually
	public function selectUniversity() {
		if ($this->mysql) {
			$this->lookForUniInDatabase();
		}
		$redo = true;
		if ($this->university->university_title) {
			echo "\nCurrent " . $this->university;
			$redo = UserInterface::questionYN("Change university properties?");
		}
		if ($redo) {
			$this->manualUniversityInput();
			echo "\n\nCurrent " . $this->university;
		}
	}


	//Populate university object manually:
	//Output university title, country, province/state and type one by one and ask if the user wants to change them
	//If yes, or if they're not set up, ask user for input, save it to the university property
	public function manualUniversityInput() {
		echo "\nPlease input information about the university.\n";
		foreach (array("university_title" => "university title", "country" => "country", "province_state" => "province or state", "type" => "type of institution") as $key => $value) {
			$redo = true;
			if ($this->university->$key) {
				echo "\nCurrent " . $value . ": " . $this->university->$key . "\n";
				$redo = UserInterface::questionYN("Change?");
			}
			if ($redo) {
				$this->university->$key = UserInterface::askForInput("", "Please enter $value:\n");
			}
		}
		echo "Resulting " . $this->university;
		if (!UserInterface::questionYN("Is this the correct university information?")) {
			echo "\nResetting university information.\n\n";
			$this->manualUniversityInput();
		}
	}





	/*********************
	METHODS FOR MAIN MENU
	*********************/

	// choose random item out of an array
	public function randomCourse($courses) {
		$e = count($courses);
		if ($e) {
			$i = mt_rand(1 ,$e - 1);
			return $courses[$i];
		} else {
			return null;
		}
	}


	//Show the application's status:
	//1. The current URL
	//2. The currently chosen scraper
	//3. The university information
	//4. The number of saved courses
	//5. Queries set up in the currently chosen scraper
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
		if ($this->university->university_title) {
			echo "-- ";
			echo $this->university;
		} else {
			echo "-- No university set up.\n";
		}
		if($this->courses) {
			$number_courses = count($this->courses);
			echo "-- There are " . $number_courses . " scraped courses in the application's memory.\n";
		} else {
			echo "-- No courses in the application's memory.\n";
		}
		if ($this->chosen_scraper) {
			echo "-- Queries currently set up for the scraper: \n";
			$this->chosen_scraper->outputQueries();
		}
	}


	//method for the main menu
	//gets called from the main.php script
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
							echo "\nChose " . $scraper->name . "\n";
							$this->chosen_scraper = $scraper;
							$this->university->host = parse_url($this->url, PHP_URL_HOST);
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
				echo "***************\nSELECTING UNIVERSITY\n***************\n";
				$redo = true;
				$this->selectUniversity();
				if ($this->url) {
					$this->university->host = parse_url($this->url, PHP_URL_HOST);
				}
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
					echo UserInterface::NO_SCRAPER;
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
					$random = UserInterface::askForSetReply('ECHO_COURSE', array("0" => "Choose a particular course by number", "1" => "Output a random course"));
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
				if ($this->courses) {
					$this->selectUniversity();
					$csv = new CSV;
					$csv->makeCSV($this->university);
					$csv->addCourses($this->courses);
					$csv->writeObjects();
					echo "Output the courses to a csv file.\n";
				} else {
					echo UserInterface::NO_COURSES;
				}
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
					$uni = $csv->getUni();
					if ($uni) {
						$this->university->initializeFromArray($uni);
					}
					if (!$this->mysql) {
						$this->getDBConn();
					}
					$this->grabUniversityId();
					$this->pushCourses($courses);
				} else {
					echo UserInterface::NO_CSV;
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