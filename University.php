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

	public function addInfo($ui) {
		$start = $ui->questionYN(UserInterface::UNI_INFO_PROMPT, "Good Choice\n");
		if ($start) {
			foreach ($this->properties as $key => $value) {
				echo UserInterface::UNI_INFO;
				$prompt = 'Enter info for ' . $key . "\n";
				$answer = $ui->userPrompt($prompt);
				$this->properties[$key] = $answer;

			}
		//return $this->properties;
		}
		
	}



}



?>