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
	protected $university;
	protected $courses = array();

	
	// Should I make it so that if the file exists already I add a number or something?
	
	function __construct($university, $courses) {
		$this->filename = self::filenameCSV($university);
		$this->university = $university;
		$this->courses = $courses;
		$greeting = array("This is the raw data from the institution: " . $university->university_title . " please edit as needed");
		if (self::checkExists() == false) {
			echo "I have made a thing for " . $university->university_title . "\n";
			fputcsv(self::openPipeA(), $greeting);
			fputcsv(self::openPipeA(), self::writeKeys());
		}
		
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

	// opens pipeline to append to csv
	public function openPipeA() {
		$apipeline = fopen($this->filename, 'a');
		return $apipeline;
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
		$pipeline = self::openPipeA();
		foreach ($object as $line) {
			fputcsv($pipeline, $line);
		}
	}

	// reads the csv file and shows it to user
	public function readArray($length = 2000) {
		$pipeline = self::openPipeR();
		while ($row = fgetcsv($pipeline, $length)) {
		print_r($row);
			//UserInterface::makeReadable($row);
		}
	}

	// uses the commandline as the file to input, so in same format as what would go into csv and is echoed to user
	public function readList($object) {
		$stdout = fopen('php://stdout', 'w');
		foreach ($object as $row) {
			fputcsv($stdout, $row);
		}
		fclose($stdout);
		}

	// writes the university and course objects into the csv
	public function writeObjects() {
		//self::writeProp($this->university); dont need because uni is in database already
		foreach ($this->courses as $course) {
			self::writeProp($course);
		}
	}

	public function checkExists(){
		if (file_exists($this->filename)) {
			return true;
		} else {
			return false;
		}
	}

	// makes the second row a list of the keys in the course array so that we know what we are looking at for later
	public function writeKeys() {
		for ($i=0; $i < 1; $i++) { 
			$keys = array_keys($this->courses[$i]->properties);
		}
		return $keys;
	}

	// takes the data from the csv and makes it back into arrays to go into the database
	// need to disregard first two rows, and also make into an associative array
	public function getCourses() {
		$pipeline = self::openPipeR();
		$keys = self::writeKeys();
		$edited_courses = array();
		$edited_course = array();
		$e = 0;
		while ($course = fgetcsv($pipeline)) {
			for ($i=0; $i < count($course); $i++) { 
				$key = $keys[$i];
				$edited_course[$key] = $course[$i];
			}
			
			$edited_courses[$e] = $edited_course;
			$e++;
		}
		unset($edited_courses[0]);
		unset($edited_courses[1]);
		return $edited_courses;
	}

}


?>