<?php

require_once 'autoload.php';


class Program extends CalendarObject {
	protected $table = "program";
	protected $properties = array(
				'id' => null,
				'university_id' => null,
				'program_title' => null,
				'degree_type' => null,
				'facaulty_name' => null,
				'department_name' => null,
				'graduation_requirements' => null,
				'year_required' => null,
				'year_max' => null,
				'note' => null,
				'created' => null,
				'updated' => null
				);





}



?>