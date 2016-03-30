<?php

require_once 'autoload.php';


class University extends CalendarObject {
	protected $table = "university";
	protected $properties = array(
			'id' => null,
			'university_title' => null,
			'country' => null,
			'province_state' => null,
			'type' => null,
			'note' => null,
			'created' => null,
			'updated' => null
			);



// output object to console

// ask user if ok

// if no let user edit field by field

// once done ask to push to database

// check if database already has that university

// if university exists retrieve from database and ask user if it should just use this

// use new record or create record

// if the university is not in database create an entry with appropriate queries

// return id of university

// save id to university objects's properties





}



?>