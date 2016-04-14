<?php

// they need names or ids
// check if applicable method (all children will have this but add to it)

/**
* General Scraper
*/
class GeneralScraper 
{
	// Properties
	protected $name; //descriptive name of scraper, gets called to refer to the scraper in the command line
	protected $id; //id of scraper, gets added to the database queries

	public $xpath; //xpath object the scraper is currently looking at
	public $url; //the URL the scraper is currently looking at

	protected $current_node; //the node 
	protected $current_links; //an array of urls that lead to CoursePages from the current page
	protected $courses; //the resulting array of course objects the scraper created
	protected $current_course; //the course object the scraper is working on currently --> used by scrapeCoursePage and grabCourseProp
	protected $queries = array(); //an array of xpath queries and regex the scraper uses for looking up particular information on the webpage
	protected $course_options = array(); //an array of options 
	protected $correspondences; //array that stores results of a manual scrape
	protected $stop_list = array(); //stop list of text nodes that should be ignored during manual scraping


	public function __construct() {
		//echo "Initiating scraper of class " . get_class($this) . "\n";
		$course_ex = new Course;
		$this->queries["cell_query"] = null;
		$this->queries["link_query"] = null;
		$this->queries["texts_query"] = null;
		$this->queries["next_page_query"] = null;

		$n = 1;
		foreach ($course_ex->properties as $key => $value) {
			if (!in_array($key, ["id", "university_id", "created", "updated"])) {
				$this->course_options[$n] = $key;
				$this->queries[$key . "_query"] = null;
				$this->queries[$key . "_regex"] = null; 
				$n++;
			}
		}
	}


	//GETTER
	public function __get ($varname) {
		//if the keyname exists in the array QUERIES, then return it
		//otherwise return null
		if (array_key_exists($varname, $this->queries)) {
			return $this->queries[$varname];
		} elseif ($varname=="name" OR $varname=="id") {
			return $this->$varname;
		} else {
			return null;
		}
	}

	//SETTER
	public function __set ($varname, $value) {
		//if the keyword exists in the QUERIES array, set it to be the value
		if (array_key_exists($varname, $this->queries)) {
			$this->queries[$varname] = $value;
		}
	}


	//Force extending classes to define the method for checking if the scraper applies
	//Returns boolean
	public function checkIfApplies() {
		echo "Scraper " . get_class($this) . " doesn't have a checkIfApplies method and therefore can't check if it's applicable to this webpage!";
		return false;
	}


	//Extremely complicated logic for grabbing manual user input regarding the texts in the cell.
	//Gets called when attempting manual scraping.
	public function grabTexts () {
		//1. Find all text() nodes among the descendants of current node, using the "texts_query".
		$texts = $this->findNodes($this->texts_query);
		$correspondences = array();
		echo UserInterface::GRAB_TEXTS_HELP;
		UserInterface::pressEnter();
		//2. For each of these text nodes:
		foreach ($texts as $index => $t) {
			$index++;
			//Make sure that all html entities are encoded in UTF-8
			$text = html_entity_decode(trim($t->nodeValue), ENT_XHTML, "UTF-8");
			//Check that the text is not in the list of stop-words (to ignore "print", "add to favourites" and such)
			if ($text AND !in_array(strtolower($text), $this->stop_list)) {
				echo "\n\nTEXT (" . $index . "): " . $text . "\n";
				//Ask the user if this particular text corresponds to any course values that they would want to grab
				$option = UserInterface::askForSetReply('ASSIGN_COURSE_OPTION_TO_TEXT', ["a" => "add regex", "0" => "none"] + $this->course_options + ["c" => "cancel"], true);
				//Option "a" stands for adding new regular expression queries in addition to xpath queries.
				//This allows to account for such cases when the same piece of text contains several pieces of relevant information, such as:
				//"(term: spring) This is an introductory course to Accounting!"
				//Since "term" and "description" are separate course properties, we might want to write several regular expressions to account for that fact
				//This loop allows the user to create an array of regular expressions, that looks something like this:
				//["term" => '/^(?<term>.*) term/, "description" => '/\) (?<description>.*)$/']
				switch ($option) {
					case 'c':
						break 2;
					case 'a':
						echo "\n\nTEXT (" . $index . "): " . $text . "\n";
						//ask the user which course property they want to add a regex for
						$add = UserInterface::askForSetReply('ASSIGN_COURSE_OPTION_TO_REGEX', ["s" => "stop adding regex"] + $this->course_options, true);
						$regs = array();
						//option "s" is "stop adding regex for this particular piece of text -- either because the user has added all they wanted or because they're sick of trying to make it work"
						while ($add != "s") {
							echo "\n\nTEXT (" . $index . "): " . $text . "\n";
							$regex_works = 0;
							//Asks the user for a regex, then tests it against the string using method checkRegEx
							while (!$regex_works) {
								$regex = UserInterface::askForInput('ASSIGN_REGEX');
								$regex_works = $this->checkRegEx($regex, $text, $this->course_options[$add]);
							}
							//If user was satisfied with how their regex worked, then add this regex to the list of queries
							if ($regex_works == "y") {
								$regs[$this->course_options[$add]] = $regex;
							}
							echo "\n\nTEXT (" . $index . "): " . $text . "\n";
							$add = UserInterface::askForSetReply('ASSIGN_COURSE_OPTION_TO_REGEX', ["s" => "stop adding regex"] + $this->course_options, true);
						} //add the resulting array of regular expressions to the correspondences
						$correspondences[$index] = $regs;
						break;
					case '0':
						$correspondences[$index] = "none";
						break;
					default:
						$correspondences[$index] = $this->course_options[$option];
						break;
				}
				/*
				if ($option == "a") {

				//if user chose something other than "a", then just save that decision to the correspondences
				} else if (!$option) {
					
				} else {
					
				}*/
			}
		}
		//save the resulting array for posteriority
		$this->correspondences = $correspondences;
	}

	//matches a regular expression against the string, looking specifically for a pointed out section with label $propname
	//asks the user if they're satisfied with the result of the match
	public function checkRegEx ($regex, $string, $propname) {
		preg_match($regex, $string, $matches);
		echo "\n\n";
		if ($matches) {
			echo "RESULT of this reg ex match: '" . $matches[$propname] . "'\n";
		} else {
			echo "RESULT: no match\n";
		}
		return UserInterface::askForSetReply('DOES_THE_REGEX_WORK', ["0" => "This doesn't work. I want to enter another regex", "y" => "This regex works, assign it to property", "c" => "I changed my mind, I don't want to assign this property a regex"], true);
	}


	//adds new queries to the scraper after a manual scrape has been conducted
	public function applyCorrespondences() {
		foreach ($this->correspondences as $key => $value) {
			//if the element is an array, e.g. 22 => ["course_title" => "/^Title: (?<course_title>.*)$/"]
			//then add an xpath query "course_title_query" -> "(~query to look for text)[22]"
			//and a regex query "course_title_regex" -> '/^Title: (?<course_title>.*)$/'
			if (is_array($value)) {
				foreach ($value as $add => $regex) {
					$regex_title = $add . "_regex";
					$query_title = $add . "_query";
					$this->$regex_title = $regex;
					$this->$query_title = "(" . $this->texts_query . ")[" . $key . "]";
				}
			//if the element is a string, e.g. 11 => "description"
			//then append the xpath query "(~query to look for text)[11]" to the end of "description_query"
			} elseif ($value != "none") {
		 		$query_title = $value . "_query";
		 		if ($this->$query_title) {
		 			$this->$query_title .= " | ";
		 		}
		 		$this->$query_title .= "(" . $this->texts_query . ")[" . $key . "]";
		 	}
		}
	}


	//find all nodes using the query provided, then return an array of nodes
	public function findNodes ($querytonodes) {
		$nodes = array();
		//if a current_node is set, start looking from it and not form the top
		if ($this->current_node) {
			foreach ($this->xpath->query($querytonodes, $this->current_node) as $node) {
				$nodes[] = $node;
			}
		} else {
			foreach ($this->xpath->query($querytonodes) as $node) {
				$nodes[] = $node;
			}
		}
		return $nodes;
	}


	//take an array of DOM nodes, return an array of values
	public function getNodeValues ($nodes_array) {
		$values = array();
		foreach ($nodes_array as $node) {
			$values[] = $node->nodeValue;
		}
		return $values;
	}


	//Do a scrape of the course page, using the current $xpath
	//Add the result to the courses[] array
	//Applies the cell_query to find all cells for specific courses, then loops through them
	//For each cell creates a new current_course, then loops on its properties
	//Calls grabCourseProp on each property
	public function scrapeCoursePage () {
		$this->current_node = NULL;

		$cells = $this->findNodes($this->cell_query);
		foreach ($cells as $cell) {
			$this->current_course = new Course();
			$this->current_node = $cell;
			foreach ($this->current_course->properties as $key => $value) {
				$this->grabCourseProp ($key);
			}
			echo "Result:\n";
			print_r($this->current_course);
			$this->courses[] = $this->current_course;
			
		}
	}


	//While being in a cell dedicated to a particular course, looks for data on a specific course property
	//First applies $propname_query to find the node dedicated to specific property
	//Then applies $propname_regex to match a string inside the text value of the node
	public function grabCourseProp ($propname) {
		$query = $propname . '_query';
		$regex = $propname . '_regex';

		if ($this->$query) {
			$results = $this->findNodes($this->$query);
			foreach ($results as $r) {
				$result = html_entity_decode(trim($r->nodeValue), ENT_XHTML, "UTF-8");
				if ($this->$regex) {
					preg_match($this->$regex, $result, $matches);
					if ($matches) {
						$result = $matches[$propname];
					} else {
						echo "Property $propname - no regex match in '" . $result . "'\n";
					}
				}
				$this->current_course->$propname .= $result . " ";
			}
		} 
	}

	//If $this->links array is empty, grabs all links on the page, puts them in the $this->links array
	//For each link on the page creates Xpath object and calls scrapeCoursePage.
	public function scrapeCatalogPage () {
		$dir = dirname($this->url);
		if (empty($this->current_links)) {
			$links = $this->findNodes($this->link_query);
			$this->current_links = $this->getNodeValues($links);
		}
		$n = 0;

		foreach ($this->current_links as $link) {
			if ($n < 1) {
				echo $dir . '/' . $link . "\n";
				$this->xpath = Application::takeUrlReturnXpath($dir . '/' . $link);
				$this->scrapeCoursePage();
			}
			$n++;
		}
	}

	
	//If $this->links array is empty, grabs all links on the page, puts them in the $this->links array
	//Chooses one at random and calls testCoursePageScrape on it
	public function testPageScrape () {
		$dir = dirname($this->url);
		if (empty($this->current_links)) {
			$links = $this->findNodes($this->link_query);
			$this->current_links = $this->getNodeValues($links);
		}
		if ($this->current_links) {
			$i = mt_rand(0, count($this->current_links)-1);

			$test_url = $dir . '/' . $this->current_links[$i];
			echo "Testing link: " . $test_url . "\n";
			$this->xpath = Application::takeUrlReturnXpath($test_url);
			$this->testCoursePageScrape();
		} else {
			echo "No links found using query '" . $this->link_query . "'\n";
		}
	}


	public function testCoursePageScrape () {
		$this->current_node = NULL;

		$cells = $this->findNodes($this->cell_query);

		$i = mt_rand(0, count($cells)-1);
		echo "Testing cell number " . $i . "\n";

		$this->current_course = new Course();
		$this->current_node = $cells[$i];
		
		foreach ($this->current_course->properties as $key => $value) {
			$this->grabCourseProp ($key);
		}

		echo "Finished with provided queries\n";
		echo "Result:\n";
		print_r($this->current_course);			
	}

	public function manualScrapeCourse () {
		$this->testPageScrape();

		$this->grabTexts();
		$this->applyCorrespondences();

		$this->current_course = new Course();
		foreach ($this->current_course->properties as $key => $value) {
			$this->grabCourseProp ($key);
		}

		echo "Final result:\n";
		print_r($this->current_course);
	}


	//Resets scraper queries to their default state (makes sense to call it after a manual scrape).
	public function resetScraperQueries () {
		echo "Resetting Scraper queries and regex to default values.\n";
		$class = get_class($this);
		$clean_scraper = new $class;
		foreach ($this->queries as $key => $value) {
			$this->$key = $clean_scraper->$key;
		}
		print_r($this->queries);
	}


	public function goToNextPage () {
		if ($this->next_page_query) {
			$nextpageurl = $this->findNodes($this->next_page_query);
			$nextpageurl = getNodeValues($nextpageurl);
			//if ()
		}
	}



	

// scrape university info (name and such) (because every one should do this) 
	public function scrapeUniversityInfo($query) {
		$uni = new University();
		$alt = $this->xpath->query($query);

		foreach ($alt as $value) {
			$info = $value->nodeValue; 
			}
		return $info;
	}
}

?>