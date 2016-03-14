<?php
require_once 'autoload.php';

//This script is a template to help create xpath queries for a site 



/*
//To try running this script, delete comment symbols from line 8 and line 34
//An example query for a page at the University of Calgary:

$page = 'http://www.ucalgary.ca/pubs/calendar/current/accounting.html';

$xpath = URLPrompt::buildDOM($page); 

$query = '(//table[tr/td[@class="myCell"]])'; // this query is designed to find and create a list of tags (table cells in this particular case) that each corresponds to a particular course.

// An Xpath query returns a list of nodes. You can iterate through it like through an array (note, though, that trying to access paricular items using an indexwon't work). Instead, use a foreach loop to iterate through it.

foreach ($xpath->query($query) as $cell) { // this loop is looking through the list of cells, each of which holds information about one course
	//once you have a list of courses, you can use an XPath query to look through that list specifically, and access various fields in each cell, for example:

	$code_query = './/*[@class="course-code"]'; // this query asks to find any tags amongs the descendants that have an attribute called "class" with value "course-code"
	foreach ($xpath->query($code_query, $cell) as $course) { // this is looking through the cell, which holds one course 
		echo $course->nodeValue . "\n"; // echo whatever's the textual content inside the node we found using class "course-code"
	}

	$desc_query = './/*[@class="course-desc"]'; // this is looking at descendants with attribute "class" labeled "course-desc"
	foreach ($xpath->query($desc_query, $cell) as $desc) { // this is looking through the same cell, but echoes the course description
		echo $desc->nodeValue . "\n";
	}
}

*/

// Build your own template qith queries to scrape a page:
// The url of the site you are looking at:
$url = '';

//$url = "http://www.ucalgary.ca/pubs/calendar/current/accounting.html"; //example from the University of Calgary website

$xpath = URLPrompt::buildDOM($url); // This builds an Xpath object that you can use to send XPath queries to the DOM structure

//First, try to write a query that creates a list of all the sections that correspond specifically to one course each. For example, on a page that contains a table with all courses you could look specifically for cells that each contains a course title, course code, course description etc.

$course_query = ''; //enter your general query here
//$course_query = '(//table[tr/td[@class="myCell"]])'; //example for University of Calgary

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



	$course_title_query = './'; //query to find the course title
	//$course_title_query = './/*[contains(@id, "cnTitle")]'; //example for University of Calgary

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

	$course_faculty_query = './'; //query to find the course faculty
	//$course_faculty_query = './/*[contains(@id, "cnCourse")]'; //example for University of Calgary
	if ($course_faculty_query != './') {
		foreach ($xpath->query($course_faculty_query, $course) as $faculty) {
			echo "Course faculty: " . $faculty->nodeValue . "\n";
		}
	}

	$course_lettercode_query = './'; //query to find the course lettercode
	if ($course_lettercode_query != './') {
		foreach ($xpath->query($course_lettercode_query, $course) as $lettercode) {
			echo "Course lettercode: " . $lettercode->nodeValue . "\n";
		}
	}

	$course_number_query = './'; //query to find the course number
	//$course_number_query = './/*[contains(@id, "cnCode")]'; //example for University of Calgary
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

	$course_description_query = './'; //query to find the course description
	//$course_description_query = './/*[contains(@id, "cnDescription")]'; //example for University of Calgary
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