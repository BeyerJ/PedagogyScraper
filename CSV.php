<?php
require_once 'autoload.php';

/**
* This is the class that deals with the CSV files 
*/
class CSV 
{
	/**
	* Properties 
	*/
	protected $filename;
	protected $university_info;
	protected $courses = array();


	
	function __construct($university)
	{
		$this->filename = self::filenameCSV($university);
		echo "I have made a thing for " . $university->university_title . "\n";
	}


	/**
	* Methods
	*/

	//This will open a pipe line to a file to write things
	public function openPipeW() {
		$wpipeline = fopen($this->filename, 'w');
		return $wpipeline;
	}

	// open pipeline to read things
	public function openPipeR() {
		$rpipeline = fopen($this->filename, 'r');
		return $rpipeline;
	}

	// Takes a university object, finds name property and adds .csv
	public function filenameCSV($university) {
		$string = $university->university_title;
		$string = str_replace(' ', '', $string);
		$string = 'results/' . $string . '.csv';
		return $string;
	}

	// writes the properties of an object into the csv
	public function writeProp($object) {
		$pipeline = self::openPipeW();
		foreach ($object as $line) {
			fputcsv($pipeline, $line);
		}
	}

	public function readArray($length = 2000) {
		$pipeline = self::openPipeR();
		while ($row = fgetcsv($pipeline, $length)) {
		print_r($row);
		}
	}

	public function readList($object) {
		$stdout = fopen('php://stdout', 'w');
		foreach ($object as $row) {
			fputcsv($stdout, $row);
		}
		fclose($stdout);
		}




}


?>