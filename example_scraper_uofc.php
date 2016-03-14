<?php
require_once 'autoload.php';

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

	echo "--------\n";
}


?>