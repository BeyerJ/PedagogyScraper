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
	public $current_node;
	public $current_links;
	public $courses;
	public $dir_url;
	public $current_course;
	public $queries = Array (
			'course_title_query' => null,
			'course_subtitle_query' => null,
			'course_faculty_query' => null,
			'course_lettercode_query' => null,
			'course_number_query' => null,
			'course_code_query' => null,
			'subject_query' => null,
			'term_query' => null,
			'year_query' => null,
			'campus_query' => null,
			'description_query' => null,
			'note_query' => null,
			'course_title_regex' => null,
			'course_subtitle_regex' => null,
			'course_faculty_regex' => null,
			'course_lettercode_regex' => null,
			'course_number_regex' => null,
			'course_code_regex' => null,
			'subject_regex' => null,
			'term_regex' => null,
			'year_regex' => null,
			'campus_regex' => null,
			'description_regex' => null,
			'note_regex' => null,
		);
	protected $course_options = array();
	protected $text_indexes = array();
	protected $correspondences;


	public function __construct() {
		echo "Heyyyy!\n";
		$n = 1;
		$course_ex = new Course;
		$this->course_options["m"] = "multiple";
		$this->course_options[0] = "none";
		foreach ($course_ex->properties as $key => $value) {
			if (!in_array($key, ["id", "university_id", "created", "updated"])) {
				$this->course_options[$n] = $key;
				$n++;
			}
		}
	}


	public function __get ($varname) {
		//if the keyname exists in the array, then return it
		//otherwise return null
		//this is sometimes called a WHITELIST
		if (array_key_exists($varname, $this->queries)) {
			return $this->queries[$varname];
		} else {
			return null;
		}
		// return $this->properties[$varname]; This is going to spew out an error if the key doesn't exist in the array.
	}

	//SETTERS


	public function __set ($varname, $value) {
		//if the keyword exist in the properties array, set it to be the value
		if (array_key_exists($varname, $this->queries)) {
			$this->queries[$varname] = $value;
		}
	}


	public function goToLink ($url) {
		//find link (for example, to next page), then follow it, return xpath for new page

		return $url_to_new_page;
	}


	public function grabTexts () {
		$texts = $this->findNodes($this->texts_query);
		$correspondences = array();
		$n = 1;
		foreach ($texts as $t) {
			$text = html_entity_decode(trim($t->nodeValue), ENT_XHTML, "UTF-8");
			if ($text AND !in_array(strtolower($text), $this->stop_list)) {
				echo "result (" . $n . "): " . $text . "\n";
				$option = $this->getReply();
				if ($option == "m") {
					$add = $this->getReply();
					$regs = array();
					while ($add != "n") {
						$regex = $this->getReply("Add RegEx:\n");
						$regs[$this->course_options[$add]] = $regex;
						$add = $this->getReply();
					}
					$correspondences[$n] = $regs;
				} else {
					$correspondences[$n] = $this->course_options[$option];
				}
			}
			$n++;
		}
		$this->correspondences = $correspondences;
	}

	public function getReply($prompt = "Which value?\n") {
		//Prompting the user for a URL
		//It turns out, PHP on Windows doesn't support the readline() function :(
		print_r($this->course_options);
		if (PHP_OS == 'WINNT') {
			echo $prompt;
			$input = stream_get_line(STDIN, 1024, PHP_EOL);
		} else {
			$input = readline($prompt);
		}
		return $input;
	}

	public function applyCorrespondences() {
		foreach ($this->correspondences as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $add => $regex) {
					$regex_title = $add . "_regex";
					$query_title = $add . "_query";
					$this->$regex_title = $regex;
					$this->$query_title = "(" . $this->texts_query . ")[" . $key . "]";
					$this->grabCourseProp($add);
				}
			} elseif ($value != "none") {
		 		$query_title = $value . "_query";
		 		if ($this->$query_title) {
		 			$this->$query_title .= " | ";
		 		}
		 		$this->$query_title .= "(" . $this->texts_query . ")[" . $key . "]";
		 		$this->grabCourseProp($value);
		 	}
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
			$course = new Course();
			$this->current_node = $cell;
			
			foreach ($course->properties as $key => $value) {
				$this->grabCourseProp ($key);
			}

			echo "Finished with provided queries\n";
			echo "Result:\n";
			print_r($this->current_course);
			echo "Attempting manual scraping.\n";
			$this->grabTexts();
			$this->applyCorrespondences();

			$this->courses[] = $this->current_course;
			
		}
	}


	public function testCoursePageScrap () {
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
		echo "Attempting manual scraping.\n";
		$this->grabTexts();
		$this->applyCorrespondences();

		$this->current_course = new Course();
		foreach ($this->current_course->properties as $key => $value) {
			$this->grabCourseProp ($key);
		}

		echo "Final result:\n";
		print_r($this->current_course);	
	}


	public function grabCourseProp ($propname) {
		$query = $propname . '_query';
		$regex = $propname . '_regex';

		if ($propname == "course_number") {
			echo $this->$query;
		}

		if ($this->$query) {
			$results = $this->findNodes($this->$query);
			foreach ($results as $r) {
				$result = html_entity_decode(trim($r->nodeValue), ENT_XHTML, "UTF-8");
				if ($this->$regex) {
					preg_match($this->$regex, $result, $matches);
					if ($matches) {
						$result = $matches[$propname];
					} else {
						echo "Property $propname - no regex match in '" . $result . "'";
					}
				}
				$this->current_course->$propname .= $result . " ";
			}
		} else {
			echo "No query for " . $propname . "\n";
		}	
	}


	public function scrapeCatalogPage () {
		$dir = dirname($this->url);
		$links = $this->findNodes($this->link_query);
		$links = $this->getNodeValues($links);
		$n = 0;

		foreach ($links as $link) {
			if ($n < 1) {
				echo $dir . '/' . $link . "\n";
				$this->xpath = Application::takeUrlReturnXpath($dir . '/' . $link);
				$this->scrapeCoursePage();
			}
			$n++;
		}
	}

	
	public function testPageScrape () {
		$dir = dirname($this->url);
		$links = $this->findNodes($this->link_query);
		$this->current_links = $this->getNodeValues($links);
		$i = mt_rand(0, count($this->current_links)-1);

		$test_url = $dir . '/' . $this->current_links[$i];
		echo "Testing link: " . $test_url . "\n";
		$this->xpath = Application::takeUrlReturnXpath($test_url);
		$this->testCoursePageScrap();
	}




	public function echoHello ($hello) {
		echo "Hello " . $hello;
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