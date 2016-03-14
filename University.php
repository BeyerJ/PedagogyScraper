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






}



?>