<?php

require_once 'autoload.php';


class Course extends CalendarObject {
	protected $table = "course";
	public $properties = array(
			'id' => null,
			'university_id' => null,
			'course_title' => null,
			'course_subtitle' => null,
			//'course_faculty' => null,
			'course_lettercode' => null,
			'course_number' => null,
			'course_code' => null,
			'course_subject' => null,
			'credit' => null,
			'term' => null,
			'year' => null,
			'campus' => null,
			'description' => null,
			'note' => null,
			'created' => null,
			'updated' => null
			);
}



?>