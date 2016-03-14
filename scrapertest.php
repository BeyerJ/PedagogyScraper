<?php

require_once 'autoload.php';

$page = 'http://www.ucalgary.ca/pubs/calendar/current/accounting.html';

$htmlpage = new simple_html_dom();

$htmlpage->load_file($page);

/*

$links = $htmlpage->find('span[class="course-code"]');

$links2 = $htmlpage->find('span[class="course-desc"]');


foreach ($links as $key => $value) {
	echo $key . ' ' . $value->innertext . "\n";
}

foreach ($links as $key => $value) {
	echo $key . ' ' . $value->innertext . "\n";
}



$table = $htmlpage->find('table');

foreach ($table as $key => $value) {
	if ($value->find('*[class="course-desc"]')) {
		echo "$key\n";
	}
}
[last()-1]

echo $key->nodeValue . "\n";
*/

$dom = new DomDocument;
$dom->preserveWhiteSpace = false;
$dom->loadHTMLFile($page);

$xpath = new DomXPath($dom);

$query = '(//table[tr/td[@class="myCell"]])';
$query2 = './/*[@class="course-code"]';
$query3 = './/*[@class="course-desc"]';

foreach ($xpath->query($query) as $key) {
	foreach ($xpath->query($query2,$key) as $course) {
		echo $course->nodeValue . "\n";
	}
	foreach ($desc = $xpath->query($query3,$key) as $desc) {
		echo $desc->nodeValue . "\n";
	}
}



?>