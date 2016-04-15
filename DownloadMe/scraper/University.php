<?php

require_once 'autoload.php';


class University extends CalendarObject {
	protected $table = "university";
	public $properties = array(
			'id' => null,
			'host' => null,
			'university_title' => null,
			'country' => null,
			'province_state' => null,
			'type' => null,
			'note' => null,
			'created' => null,
			'updated' => null
			);

	public function __toString () {
		$uni_info = "University information:\n";
		$uni_info .= "     Title: " . $this->university_title . "\n";
		$uni_info .= "     Host: " . $this->host . "\n";
		$uni_info .= "     Country: " . $this->country . "\n";
		$uni_info .= "     State/Province: " . $this->province_state . "\n";
		$uni_info .= "     Type: " . $this->type . "\n";
		return $uni_info;
	}


/// whois command thing

// output object to console
	
// ask user if ok

// if no let user edit field by field
	// I did this in Application (I think)

// once done ask to push to database

// check if database already has that university

// if university exists retrieve from database and ask user if it should just use this

// use new record or create record

// if the university is not in database create an entry with appropriate queries

// return id of university

// save id to university objects's properties





}



?>