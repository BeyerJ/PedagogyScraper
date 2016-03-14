<?php 

require_once 'autoload.php';



//$url = URLPrompt::getURL();
//echo "The URL you entered is " . $url . "\n";
$xpath = URLPrompt::buildDOM("http://www.ucalgary.ca/pubs/calendar/current/accounting.html");

		$query = '//head/title';

		foreach ($xpath->query($query) as $key) {
			echo "The title is " . $key->nodeValue . "\n";
		}

		$query2 = '//*[@id="uc-banner-logo"]/*/*[@alt]/@alt';

		foreach ($xpath->query($query2) as $key) {
			echo "The alt title is " . $key->firstChild->nodeName . "\n";
		}








 ?>