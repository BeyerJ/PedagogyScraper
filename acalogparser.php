<?php

require_once 'acalogURL.php';

//	the URL we're scraping
$url = 'http://calendar.ualberta.ca/content.php?catoid=6&navoid=971';

//	tell the system that we'll handle all the errors.
//	(We won't, but the system doesn't need to know!)
libxml_use_internal_errors(true);

//	create a DOM document
$d = new DOMDocument();

//	load it from the file (or the URL)
$d->loadHTMLFile($url);

//	clear errors from the buffer so we don't
//	have any memory problems. This might not be
//	necessary, but we'll do it anyway.
libxml_clear_errors();


//	build an XPath object
$xpath = new DOMXpath($d);

//	find all the <a> tags
$a_nodes = $xpath->query('//a');

//	iterate that list. Copy each node into
//	a variable, $a. Access that node's 'href'
//	attribute. Skip ones that we don't care about.

foreach ($a_nodes as $a) {
	$href = $a->getAttribute('href');
	
	//	skip if empty
	if ( empty($href) ) continue;
	
	//	skip if it's a link to an anchor on this page:
	//	example: href="#goofus"
	if ( substr($href, 0, 1) == '#' ) continue;

	//	must be OK; let's echo it; use the attached `URL` class
	//	to reconcile relative links
	$links = URL::absolutize($url, $href);

	//	let's change specially encoded characters (e.g.: "%20")
	//	back into real characters.
	$links = urldecode($links) . "\n";

		if (strpos($href, 'preview_course_nopop') !== false ) {	
	
			$link = file_get_contents($links);

			$dom = new DOMDocument();
			$dom->loadHTML($link);
			$newHTML = $dom->saveHTML();

			$xpath = new DOMXPath($dom);

			$descriptions = $xpath->query('.//*[contains(@class, "block_content")]');

				foreach($descriptions as $index => $description){
    				echo $description->textContent . "\n";
					}

	}
}

?>