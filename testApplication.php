<?php

require_once 'autoload.php';

$app = new Application;
$urls = array('http://www.ucalgary.ca/pubs/calendar/current/course-desc-main.html', 'http://www.ucalgary.ca/pubs/calendar/current/accounting.html','http://www.ucalgary.ca/pubs/calendar/current/african-studies.html');

//THIS IS HOW YOU WOULD CALL A METHOD IN THE GENERAL SCRAPER CLASS
//NOTE THAT YOU'RE PASSING A _FUNCTION_ AS A PARAMETER
//GeneralScraper::loopOnNodes(echoCourses, $urls);




$uofc_scr = new UofCalgaryScraper();
$uofc_scr->xpath = $app->takeUrlReturnXpath($urls[0]);
$uofc_scr->url = $urls[0];
$uofc_scr->scrapeCatalogPage();

print_r($uofc_scr->courses);


//THIS IS A FUNCTION TO TEST 
function echoCourses ($url) {
	global $app; //this tells the function that this variable has global scope -- so basically to use the $app object created up there, outside of this function

	//THIS IS HOW YOU BUILD AN XPATH NOW -- NOTE THAT YOU NEED AN APPLICATION OBJECT FOR IT TO WORK (IT GETS CREATED ALL THE WAY ABOVE)
	$xpath = $app->takeUrlReturnXpath($url);



	//This is just the same old UofCalgary scrape we did before
	$query = '(//table[tr/td[@class="myCell"]])';
	foreach ($xpath->query($query) as $cell) { // this loop is looking through the list of cells, each of which holds information about one course
		//once you have a list of courses, you can use an XPath query to look through that list specifically, and access various fields in each cell, for example:

		$code_query = './/*[@class="course-code"]'; // this query asks to find any tags amongs the descendants that have an attribute called "class" with value "course-code"
		foreach ($xpath->query($code_query, $cell) as $course) { // this is looking through the cell, which holds one course 
			echo $course->nodeValue . "\n"; // echo whatever's the textual content inside the node we found using class "course-code"
		}

		$desc_query = './/*[@class="course-desc"]'; // this is looking at descendants with attribute "class" labeled "course-desc"
		foreach ($xpath->query($desc_query, $cell) as $desc) { // this is looking through the same cell, but echoes the course description
			if (!empty($desc->nodeValue)) {
				echo $desc->nodeValue . "\n";
			}
		}
		echo "--------\n";
	}
}

?>