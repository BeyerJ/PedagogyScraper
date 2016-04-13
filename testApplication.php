<?php

require_once 'autoload.php';

$app = new Application;
$urls = array('http://www.ucalgary.ca/pubs/calendar/current/course-desc-main.html', 'http://www.ucalgary.ca/pubs/calendar/current/accounting.html','http://www.ucalgary.ca/pubs/calendar/current/african-studies.html');

//THIS IS HOW YOU WOULD CALL A METHOD IN THE GENERAL SCRAPER CLASS
//NOTE THAT YOU'RE PASSING A _FUNCTION_ AS A PARAMETER
//GeneralScraper::loopOnNodes(echoCourses, $urls);



/*
$uofc_scr = new UofCalgaryScraper();
$uofc_scr->xpath = $app->takeUrlReturnXpath($urls[0]);
$uofc_scr->url = $urls[0];
$uofc_scr->scrapeCatalogPage();

print_r($uofc_scr->courses);
*/
$scraperyay = "AcalogScraper";

$uofc_scr = new $scraperyay();
$uofc_scr->xpath = $app->takeUrlReturnXpath("http://calendar.ualberta.ca/content.php?catoid=6&navoid=971");
$uofc_scr->url = "http://calendar.ualberta.ca/content.php?catoid=6&navoid=971";

//$uofc_scr->xpath = $app->takeUrlReturnXpath("http://courses.cornell.edu/content.php?catoid=26&navoid=6782");
//$uofc_scr->url = "http://courses.cornell.edu/content.php?catoid=26&navoid=6782";


//$uofc_scr->xpath = $app->takeUrlReturnXpath("http://catalog.kennesaw.edu/content.php?catoid=24&navoid=2024");
//$uofc_scr->url = "http://catalog.kennesaw.edu/content.php?catoid=24&navoid=2024";



$uofc_scr->testPageScrape();

//print_r($uofc_scr->courses);
//var_dump(parse_url($uofc_scr->url, PHP_URL_HOST));



?>