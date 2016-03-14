<?php
require_once 'autoload.php';

/*

This script is a template to help create xpath queries for a site 

An example query

	$page = 'http://www.ucalgary.ca/pubs/calendar/current/accounting.html';

	$xpath = URLPrompt::buildDOM($page); 

	$query = '(//table[tr/td[@class="myCell"]])'; // this is looking at the cells in the table belonging to each of the courses
	$query2 = './/*[@class="course-code"]'; // this is looking at the classes in each cell that are labelled course-code
	$query3 = './/*[@class="course-desc"]'; // this is looking at the sections labeled course description

	// Xpath returns a list of nodes, and can iterate like an array. Use a foreach loop to look through it

	foreach ($xpath->query($query) as $key) { // this loop is looking through the list of cells from the table
		foreach ($xpath->query($query2,$key) as $course) { // this is looking through the cell, which holds one course 
			echo $course->nodeValue . "\n"; // this echos the course code
		}
		foreach ($desc = $xpath->query($query3,$key) as $desc) { // this is looking through the same cell, but echos the description
			echo $desc->nodeValue . "\n";
		}
	}


*/

// The url of the site you are looking at

$url = "";

$xpath = URLPrompt::buildDOM($url); // this is the object that you send the query to

// First you will need to get information about the university
/////This info will be put into the database before the course info

// You will need to break the html page into the following data:

/* 
			'university_title' = '';

			'country' = '';

			'province_state' = '';

			'type' = '';
			
*/

// try to write a query that creates a list of the all the sections that corresponds to each part of the courses:
		// eg: './/*[@class="course-code"]' or './/*[@class="course-desc"]'
	/*
			'course_title' 
			'course_subtitle'
			'course_facaulty' 
			'course_lettercode' 
			'course_number'  
			'course_code' 
			'term' 
			'year' 
			'campus'
			'description'	

	*/



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