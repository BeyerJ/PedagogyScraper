<?php 

require_once 'autoload.php';

class CalendarObject {

	protected $properties = array();
	PROTECTED $mysql;

	protected function initializeFromDatabase() {
		//Metadata about the table is used to create a WHITELIST
		$query = "DESCRIBE {$this->table}";
		$result = $this->mysql->query($query);
		while ($row = $result->fetch_assoc()) {
			$this->properties[$row['Field']] = null;
		}
	}


	//GETTERS

	public function __get ($varname) {
		//if the keyname exists in the array, then return it
		//otherwise return null
		//this is sometimes called a WHITELIST
		if (array_key_exists($varname, $this->properties)) {
			return $this->properties[$varname];
		} else {
			echo $varname . " does not exist\n";
			return null;
		}
	}

	//SETTERS


	public function __set ($varname, $value) {
		//if the keyword exist in the properties array, set it to be the value
		if (array_key_exists($varname, $this->properties)) {
			$this->properties[$varname] = $value;
		} else {
			echo "The property " . $varname . " does not exist\n";
		}
	}

	// Displays the properties of the object
	public function showProperties() {
		foreach($this->properties as $property => $value) {
			echo $property . ": " . $value . "\n";
		}
	}

	public function initializeFromArray ($array) {
		foreach ($array as $key => $value) {
			$this->$key = $value;
		}
	}




}










 ?>