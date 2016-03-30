<?php

	
		if (PHP_OS == 'WINNT') {
			require_once("/../autoload.php");
		} else {
			require_once '../autoload.php';
		}
	

//include_once("../");

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

// scrape university info (name and such) (because every one should do this) (maybe, fuck I don't know)
	public function scrapeUniversityInfo($query) {
		$uni = new University();
		$info = '';
		$alt = $this->xpath->query($query);

		foreach ($alt as $value) {
			$info = $value->nodeValue; 
			}
		return $info;
	}

}

?>