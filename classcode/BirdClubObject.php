<?php

class BirdClubObject {

	protected $properties = array();
	const DEFAULT_ID_NUMBER = -1;

	//mySQLi connection object
	PROTECTED $mysql;

	PUBLIC function __construct ($id = self::DEFAULT_ID_NUMBER) {
		//building a connection with a database -- the pipeline is an object
		//our DBConnection class creates a static connection and always returns the same one once it's created
		//we're using default $id=-1 because we want our constructor to grab data from the database
		//if the object already exists
		//or create a new one and push it to the database if it doesn't
		//
		$this->mysql = DBConnection::getConnection();

		//add properties to the properties array, pulling them from the database table (the columns)
		$this->initializeFromDatabase();

		if(empty($id)) $id = self::DEFAULT_ID_NUMBER;
		//save the id number to use later
		$this->id = $id;
	}


	//GETTERS

	public function __get ($varname) {
		//if the keyname exists in the array, then return it
		//otherwise return null
		//this is sometimes called a WHITELIST
		if (array_key_exists($varname, $this->properties)) {
			return $this->properties[$varname];
		} else {
			return null;
		}
		// return $this->properties[$varname]; This is going to spew out an error if the key doesn't exist in the array.
	}

	//SETTERS


	public function __set ($varname, $value) {
		//if the keyword exist in the properties array, set it to be the value
		if (array_key_exists($varname, $this->properties)) {
			$this->properties[$varname] = $value;
		}
	}


	public function __toString () {
		$string = "";
		foreach ($this->properties as $key=>$value) {
			$string .= $key . ": " . $value . "\n";
		}
		return $string;
	}


	//function that creates the "properties" array by making a query to the database and copying all of the properties
	//from the table description into the properties array
	//note that this just initializes the array, it doesn't set the properties, they remain NULL
	protected function initializeFromDatabase() {
		//Metadata about the table is used to create a WHITELIST
		$query = "DESCRIBE {$this->table}";
		$result = $this->mysql->query($query);
		while ($row = $result->fetch_assoc()) {
			$this->properties[$row['Field']] = null;
		}
	}


	//DATABASE METHODS

	PUBLIC function loadFromDatabase () {
		if($this->id < 1) {
			echo "This object doesn't exist in the database.\n";
			return;
		}
	
		//query the database for the member
		$query = 'SELECT * FROM ' . $this->table . ' WHERE pid=' . $this->id;
		$person = $this->mysql->query($query);

		//exit if there are errors
		//give feedback on what happened and where
		if($this->mysql->errno != 0) {
			$message = 'MySQL Error: ' . $this->mysql->errno . ' (' . $this->mysql->error . ") while trying to run query |$query|";
			$exc = new QueryException($message);
			$exc->query = $query;
			throw $exc;


			/* SELF REPORTING -- changed it for an Exception
			************************************************
			echo __CLASS__ . ' has an error in ' . __METHOD__ . ' at line ' . __LINE__ . ' in file ' . __FILE__ . "\n";
			echo "Error! " . $this->mysql->error . "\n";
			exit;
			*/
		}

		//grab the values and stick them into the properties array
		$row = $person->fetch_assoc();
		foreach ($row as $varkey => $varvalue) {
			$this->properties[$varkey] = $varvalue;
			//echo "Got property $varkey with value '$varvalue'";
		}
	}


	protected function cleanUp ($string) {
		if (strlen($string) > 0) {
			return "'" . $this->mysql->real_escape_string($string) . "'";
		} else {
			return "NULL";
		}
	}


	public function loadFromHTML() {
		foreach ($_POST as $key => $value) $this->$key = $value;
	} 

}


?>