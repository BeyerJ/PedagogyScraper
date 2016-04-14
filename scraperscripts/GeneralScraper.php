<?php

// they need names or ids
// check if applicable method (all children will have this but add to it)

/**
* General Scraper
*/
class GeneralScraper 
{
	// Properties
	protected $name; 
	protected $id;
	public $UI;
	public $STATUS;
	public $xpath;
	public $url;
	protected $current_node; //the node 
	protected $current_links; //an array of urls that lead to CoursePages from the current page
	protected $courses; //the resulting array of course objects the scraper created
	protected $current_course; //the course object the scraper is working on currently --> used by scrapeCoursePage and grabCourseProp
	protected $queries = array(); //an array of xpath queries and regex the scraper uses for looking up particular information on the webpage
	protected $course_options = array(); //an array of options 
	protected $correspondences;
	protected $stop_list = array();


	public function __construct() {
		echo "Initiating scraper of class " . get_class($this) . "\n";
		$n = 1;
		$course_ex = new Course;
		$this->queries["cell_query"] = null;
		$this->queries["link_query"] = null;
		$this->queries["texts_query"] = null;
		$this->queries["next_page_query"] = null;
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
		echo "The manual scraper has grabbed all of the text nodes from its current position on the page. Now it will ask you to assess every one of those nodes and reply if those nodes contain any of the course information properties.\n\nIf the same text node contains data for several different course properties, you can push 'a' and attempt to add regular expressions to separate the different pieces of data.\n\nIf you're not adding regular expressions, then several text nodes can be assigned to the same course property (for example, if decide to put several different text parts to the 'note' field).\n\nIf the data in the text has already been scraped using the automatic scraper, there's no need to add it again (just reply with '0' -- 'none' in those cases)\n";
		//2. For each of these text nodes:
		foreach ($texts as $index => $t) {
			$index++;
			//Make sure that all html entities are encoded in UTF-8
			$text = html_entity_decode(trim($t->nodeValue), ENT_XHTML, "UTF-8");
			//Check that the text is not in the list of stop-words (to ignore "print", "add to favourites" and such)
			if ($text AND !in_array(strtolower($text), $this->stop_list)) {
				echo "\n\nTEXT (" . $index . "): " . $text . "\n";
				//Ask the user if this particular text corresponds to any course values that they would want to grab
				$option = UserInterface::askForSetReply('ASSIGN_COURSE_OPTION_TO_TEXT', ["a" => "add regex", "0" => "none"] + $this->course_options, true);
				//Option "a" stands for adding new regular expression queries in addition to xpath queries.
				//This allows to account for such cases when the same piece of text contains several pieces of relevant information, such as:
				//"(term: spring) This is an introductory course to Accounting!"
				//Since "term" and "description" are separate course properties, we might want to write several regular expressions to account for that fact
				//This loop allows the user to create an array of regular expressions, that looks something like this:
				//["term" => '/^(?<term>.*) term/, "description" => '/\) (?<description>.*)$/']
				if ($option == "a") {
					echo "\n\nTEXT (" . $index . "): " . $text . "\n";
					//ask the user which course property they want to add a regex for
					$add = UserInterface::askForSetReply('ASSIGN_COURSE_OPTION_TO_REGEX', ["s" => "stop adding regex"] + $this->course_options, true);
					$regs = array();
					//option "s" is "stop adding regex for this particular piece of text -- either because the user has added all they wanted or because they're sick of trying to make it work"
					while ($add != "s") {
						echo "\n\nTEXT (" . $index . "): " . $text . "\n";
						$regex_works = 0;
						while (!$regex_works) {
							$regex = UserInterface::askForInput('ASSIGN_REGEX');
							$regex_works = $this->checkRegEx($regex, $text, $this->course_options[$add]);
						}
						if ($regex_works == "y") {
							$regs[$this->course_options[$add]] = $regex;
						}
						echo "\n\nTEXT (" . $index . "): " . $text . "\n";
						$add = UserInterface::askForSetReply('ASSIGN_COURSE_OPTION_TO_REGEX', ["s" => "stop adding regex"] + $this->course_options, true);
					}
					$correspondences[$index] = $regs;
				} else if (!$option) {
					$correspondences[$index] = "none";
				} else {
					$correspondences[$index] = $this->course_options[$option];
				}
			}
		}
		$this->correspondences = $correspondences;
		print_r($this->queries);
	}


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

	public function getReply($prompt = "Which value?\n", $show_options=true) {
		//Prompting the user for a URL
		//It turns out, PHP on Windows doesn't support the readline() function :(
		if ($show_options) {
			print_r($this->course_options);	
		}
		if (PHP_OS == 'WINNT') {
			echo $prompt;
			$input = stream_get_line(STDIN, 1024, PHP_EOL);
		} else {
			$input = readline($prompt);
		}
		return $input;
	}

	public function applyCorrespondences() {
		print_r($this->correspondences);
		foreach ($this->correspondences as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $add => $regex) {
					$regex_title = $add . "_regex";
					$query_title = $add . "_query";
					$this->$regex_title = $regex;
					$this->$query_title = "(" . $this->texts_query . ")[" . $key . "]";
				}
			} elseif ($value != "none") {
		 		$query_title = $value . "_query";
		 		if ($this->$query_title) {
		 			$this->$query_title .= " | ";
		 		}
		 		$this->$query_title .= "(" . $this->texts_query . ")[" . $key . "]";
		 	}
		}
		foreach ($this->queries as $key => $value) {
			echo "$key => " . $this->$key;
		}
	}



	public function findNodes ($querytonodes) {
		//find all nodes using the query provided, then return an array of nodes
		$nodes = array();

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


	public function getNodeValues ($nodes_array) {
		//take an array of DOM nodes, return an array of values
		$values = array();
		foreach ($nodes_array as $node) {
			$values[] = $node->nodeValue;
		}
		return $values;
	}


	public function loopOnNodes ($func, $nodesarray) {
		//take an array of nodes and a function, call this function on each node in a loop
		foreach ($nodesarray as $node) {
			$func($node);
		}
	}



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
		} else {
			echo "No query for " . $propname . "\n";
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
		echo "Testing cell number " . $i;

		$this->current_course = new Course();
		$this->current_node = $cells[$i];
		
		foreach ($this->current_course->properties as $key => $value) {
			$this->grabCourseProp ($key);
		}

		echo "Finished with provided queries\n";
		echo "Result:\n";
		print_r($this->current_course);
		if ($this->getReply("Do a manual scrape?\n", false)) {
			echo "Attempting manual scraping.\n";
			$this->manualScrapeCourse();
		}
		
			
	}

	public function manualScrapeCourse () {
		$this->grabTexts();
		$this->applyCorrespondences();

		$this->current_course = new Course();
		foreach ($this->current_course->properties as $key => $value) {
			$this->grabCourseProp ($key);
		}

		echo "Final result:\n";
		print_r($this->current_course);

		print_r($this->queries);

		if ($this->getReply("Reset scraper queries?\n", false)) {
			$this->resetScraperQueries();
		}
	}


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



	// Scrape one site or url
	public function newScrape(){
		if (!$this->UI) {
			$this->UI = new UserInterface();
		}
		$this->STATUS = new ApplicationStatus();
		//Ask for the general URL, using a method from UserInterface
		$catalog_url = $this->UI->askForURL();

		//Check if URL is valid
		//$this->STATUS->urlIsValid($catalog_url);

		//If the method returned FALSE, stop the script

/*
		if(!$catalog_url) {
			exit;
		}
*/
		return $catalog_url;
	}


	
	

// scrape university info (name and such) (because every one should do this) (maybe, fuck I don't know)
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