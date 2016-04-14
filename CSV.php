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
	public $filename;
	protected $university;
	protected $courses = array();

	const SAVED_COURSE_DATA = 'results/saved_course_data.csv';
	

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
		$keys = array();
		foreach ($this->university->properties as $key => $value) {
			$keys[] = $key;
		}
		fputcsv(self::openPipeA(), $keys);
		self::writeProp($university);
	}


	public function addCourses($courses) {
		$this->courses = $courses;
		fputcsv(self::openPipeA(), self::writeKeys());
	}

	// writes the university and course objects into the csv
	public function writeObjects() {
		
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
	public function readSavedData() {
		if (!file_exists('results/saved_course_data.csv')) {
			$pipeline = fopen('results/saved_course_data.csv', 'w');
			fclose($pipeline);
		}
		$pipeline = fopen('results/saved_course_data.csv', 'r');
		$list = array();
		$i = 0;
		while($row = fgetcsv($pipeline, 2000)) { 
			$list[$i] = $row[0];
			$i++;
		}
		fclose($pipeline);
		return $list;
	}

	public function addToSaved($filename) {
		$array = array($filename);
		$pipeline = fopen('results/saved_course_data.csv', 'a');
		fputcsv($pipeline, $array);
		fclose($pipeline);
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
		$key = $course[3];
		//print_r($key);
		for ($e=4; $e < (count($course) - 1); $e++) { 
			for ($i=0; $i < count($course[4]); $i++) { 
				$new_key = $key[$i];
				$new_array[$new_key] = $course[$e][$i];

			}
		$edited_courses[] = $new_array;
		}
		fclose($pipeline);
		return $edited_courses;
		
	}

	// will retrieve the university information
	public function getUni() {
		$pipeline = self::openPipeR();
		while ($row[] = fgetcsv($pipeline, 2000)) {
		}
		$key = $row[1];
		$uni_data = $row[2];
		for ($i=0; $i < (count($uni_data) - 1); $i++) { 
			$new_key = $key[$i];
			$new_array[$new_key] = $uni_data[$i];
		}
		print_r($new_array);
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

	public function saveData() {
		$pipeline = fopen(self::SAVED_COURSE_DATA, 'a');
		$saved_file = array($this->filename);
		fputcsv($pipeline, $saved_file);
		fclose($pipeline);
		echo "The data from " . $this->university->university_title . " has been paused been saved to " . $this->filename . "\n";
	}

	// check if the application object has a university object with data
	public function checkUniObj() {

	}



}


?>