<?php

require_once 'autoload.php';

class Application {

	protected $url;
	protected $university;
	protected $courses = array();
	protected $programs = array();



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


	public function urlIsValid ($url) {
		//TODO!
		$well_formed = filter_var($url, FILTER_VALIDATE_URL);
		if (!$well_formed) {
			echo "Badly-formed URL!\n";
			exit;
		}
		$headers = @get_headers($url);
		//check if the header contains 404, 403 or 500, exit; otherwise return url
	}

	//get text HTML data from a url using cURL
	public function getDataFromURL($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
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


} 

?>