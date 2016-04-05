<?php

// they need names or ids
// check if applicable method (all children will have this but add to it)

/**
* General Scraper
*/
class GeneralScraper 
{
	// Properties
	public $name; 
	public $id;
	public $UI;
	public $STATUS;
	public $xpath;




	public function goToLink ($xpath, $querytolink) {
		//find link (for example, to next page), then follow it, return xpath for new page
	}


	public function findLinks ($xpath, $querytolinks) {
		//find all links using the query provided, then return an array of links
	}


	public function loopOnLinks ($func, $linksarray) {
		foreach ($linksarray as $link) {
			$func($link);
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