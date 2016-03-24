<?php
require_once 'autoload.php';

//This script is a template to help create xpath queries for a site


//To see how example queries work, just uncomment lines have "example" in their comments

// Build your own template with queries to scrape a page:
// The url of the site you are looking at:
$url = 'http://www.unb.ca/academics/calendar/undergraduate/current/frederictoncourses/chemistry/index.html';

$xpath = URLPrompt::buildDOM($url); // This builds an Xpath object that you can use to send XPath queries to the DOM structure

//First, try to write a query that creates a list of all the sections that correspond specifically to one course each. For example, on a page that contains a table with all courses you could look specifically for cells that each contains a course title, course code, course description etc.

$course_query = '//table[tbody/tr/td/course_description/p]'; //enter your general query here

$results_num = 0; //this variable will track the number of results you got with your query
foreach ($xpath->query($course_query) as $course) {

	$results_num++;
	echo "Element No " . $results_num . "\n";
	echo "----\n";
	
	//SEVERAL ECHO STATEMENTS FOR DEBUGGING:
	/*
	echo "NodeName: " . $course->nodeName . "\n";
	if ($course->nodeType == 1) echo "NodeType: tag" . "\n";
	if ($course->nodeName == 2) echo "NodeType: attribute" . "\n";
	foreach ($xpath->query('./@*') as $attr) {
		echo "Has attribute:" . $attr->NodeName . "\n";
	}
	echo "-------------\n";
	*/



	//Now that you've gotten a list of elements corresponding to courses, you can write specific queries to grab course data that we need for the database

	$course_title_query = './/*[contains(@abbr, "Course Dscription")]'; //query to find the course title

	if ($course_title_query != './') {
		foreach ($xpath->query($course_title_query, $course) as $title) {
			echo "Course title: " . $title->nodeValue . "\n";
		}
	}

	$course_subtitle_query = './'; //query to find the course subtitle
	if ($course_subtitle_query != './') {
		foreach ($xpath->query($course_subtitle_query, $course) as $subtitle) {
			echo "Course subtitle: " . $subtitle->nodeValue . "\n";
		}
	}

	$course_faculty_query = './/h1'; //query to find the course faculty

	if ($course_faculty_query != './') {
		foreach ($xpath->query($course_faculty_query, $course) as $faculty) {
			echo "Course faculty: " . $faculty->nodeValue . "\n";
		}
	}

	$course_lettercode_query = './/*[contains(@abbr, "Course Code")]'; //query to find the course lettercode

	if ($course_lettercode_query != './') {
		foreach ($xpath->query($course_lettercode_query, $course) as $lettercode) {
			echo "Course lettercode: " . $lettercode->nodeValue . "\n";
		}
	}

	$course_number_query = './/*[contains(@abbr, "Course Code")]'; //query to find the course number

	if ($course_number_query != './') {
		foreach ($xpath->query($course_number_query, $course) as $number) {
			echo "Course number: " . $number->nodeValue . "\n";
		}
	}

	$course_code_query = './'; //query to find the course code
	if ($course_code_query != './') {
		foreach ($xpath->query($course_code_query, $course) as $code) {
			echo "Course code: " . $code->nodeValue . "\n";
		}
	}

	$course_term_query = './'; //query to find the course term
	if ($course_term_query != './') {
		foreach ($xpath->query($course_term_query, $course) as $term) {
			echo "Course term: " . $term->nodeValue . "\n";
		}
	}

	$course_year_query = './'; //query to find the course year
	if ($course_year_query != './') {
		foreach ($xpath->query($course_year_query, $course) as $year) {
			echo "Course year: " . $year->nodeValue . "\n";
		}
	}

	$course_campus_query = './'; //query to find the course campus
	if ($course_campus_query != './') {
		foreach ($xpath->query($course_campus_query, $course) as $campus) {
			echo "Course campus: " . $campus->nodeValue . "\n";
		}
	}

	$course_description_query = './/course_description/p'; //query to find the course description


	if ($course_description_query != './') {
		foreach ($xpath->query($course_description_query, $course) as $description) {
			echo "Course description: " . $description->nodeValue . "\n";
		}
	}

	echo "-----------------\n-----------------\n";

}


//TODO:

// Finally, try to find the following about the program

	/*
			'program_title' =
			'degree_type' =
			'facaulty_name' =
			'department_name' =
			'graduation_requirements' =
			'year_required' =
			'year_max' =


	*/



?>