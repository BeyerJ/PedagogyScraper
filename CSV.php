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

	const PAUSED_SCRAPES = 'results/paused_scrapes.csv';
	
	// Should I make it so that if the file exists already I add a number or something?
	
	function __construct() {
		
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

	//
	public function makeCSV($university) {
		$this->university = $university;
		$this->filename = self::filenameCSV($university);
		$greeting = array("This is the raw data from the institution: " . $university->university_title . " please edit as needed");
		if (self::checkExists() == false) {
			echo "I have made a thing for " . $university->university_title . "\n";
			fputcsv(self::openPipeA(), $greeting);
			
		}
	}


	public function addCourses($courses) {
		$this->courses = $courses;
		fputcsv(self::openPipeA(), self::writeKeys());
	}

	// writes the university and course objects into the csv
	public function writeObjects() {
		//self::writeProp($this->university); dont need because uni is in database already
		foreach ($this->courses as $course) {
			self::writeProp($course);
		}
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
		fclose($pipeline);
	}

	// uses the commandline as the file to input, so in same format as what would go into csv and is echoed to user
	public function readList($object) {
		$stdout = fopen('php://stdout', 'w');
		foreach ($object as $row) {
			fputcsv($stdout, $row);
		}
		fclose($stdout);
		}

	// gets an assoc array of the saved scrapes so the UI can echo them and the user can select one to pull and push into the database after they have edited the information
	public function readPaused() {
		$pipeline = fopen('results/paused_scrapes.csv', 'r');
		$list = array();
		$i = 0;
		while($row = fgetcsv($pipeline, 2000)) {
			$list[$i] = $row[0];
			$i++;
		}
		fclose($pipeline);
		return $list;
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
		while ($course[] = fgetcsv($pipeline, 2000)) {	
		}
		$key = $course[1];
		//print_r($key);
		for ($e=2; $e < (count($course) - 1); $e++) { 
			for ($i=0; $i < count($course[2]); $i++) { 
				$new_key = $key[$i];
				$new_array[$new_key] = $course[$e][$i];

			}
		$edited_courses[] = $new_array;
		}
		return $edited_courses;
		
	}

	// I realized that the functions I have made are super specific to each object and I don't know if that is at all helpful
	public function getCoursesGen() {
		$pipeline = self::openPipeR();
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
		fclose($pipeline);
		return $edited_courses;
	}

	// will check if there is a file for saved course info, from a paused scrape. if not it will make it
	// this might be a useless function oops 
	public function checkFile($file) {
		if (file_exists($file)) {
			return true;
		} else {
			$pipeline = fopen($file , 'w');
		}
	}

	public function pauseScrape() {
		$pipeline = fopen(self::PAUSED_SCRAPES, 'a');
		$saved_file = array($this->filename);
		fputcsv($pipeline, $saved_file);
		fclose($pipeline);
		echo "The scrape of " . $this->university->university_title . " has been paused and the data has been saved to " . $this->filename . "\n";
	}


}


?>