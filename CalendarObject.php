<?php 

require_once 'autoload.php';

class CalendarObject {

	protected $properties = array();
	PROTECTED $mysql;

	PUBLIC function __construct () {
		//building a connection with a database -- the pipeline is an object
		//our DBConnection class creates a static connection and always returns the same one once it's created
		//we're using default $id=-1 because we want our constructor to grab data from the database
		//if the object already exists
		//or create a new one and push it to the database if it doesn't
		//
		$this->mysql = DBConnection::getConnection();
		if (empty($this->mysql)) {
			echo "something went wrong\n";
		}

		//add properties to the properties array, pulling them from the database table (the columns)
		$this->initializeFromDatabase();

		
	}

	protected function initializeFromDatabase() {
		//Metadata about the table is used to create a WHITELIST
		$query = "DESCRIBE {$this->table}";
		$result = $this->mysql->query($query);
		while ($row = $result->fetch_assoc()) {
			$this->properties[$row['Field']] = null;
		}
	}


}










 ?>