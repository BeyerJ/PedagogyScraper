<?php

require_once 'autoload.php';

class ApplicationStatus {

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
		$well_formed = filter_var($url, FILTER_VALIDATE_URL);
		if (!$well_formed) {
			echo "Badly-formed URL!\n";
			exit;
		}
		$headers = @get_headers($url);
		//check if the header contains 404, 403 or 500, exit; otherwise return url
	}


} 

?>