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
	public $current_node;
	public $courses;


	public function __construct() {
		//$this->UI = $UserInterface;
	}


	public function goToLink ($url) {
		//find link (for example, to next page), then follow it, return xpath for new page

		return $url_to_new_page;
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

	

	public function echoHello ($hello) {
		echo "Hello " . $hello;
	}


	// Prompts




	// Methods
	/*
	function __construct(argument)
	{
		# code...
	}
	*/

	// Check if applicable (children will modify)


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
	
	// Build DOM Object
	//TRANSFERED THIS METHOD TO THE APPLICATION CLASS
/*
	public function buildDOM($url = NULL) {
		if (empty($url)) {
			$url = $this->STATUS->$url;
		}
		echo $url . "\n";
		$dom = new DomDocument;
		$dom->preserveWhiteSpace = false;
		$dom->loadHTMLFile($url);

		$this->xpath = new DomXPath($dom);

		return $this->xpath;

	}
*/

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