<?php 

require_once 'autoload.php';

/**
* Asks for a URL and builds a DOM Object from it
*/
class URLPrompt {

	protected static $URL = NULL;
	

	/// METHODS

	public function getURL() {
		//Prompting the user for a URL
		//It turns out, PHP on Windows doesn't support the readline() function :(
		$prompt = "Please enter a URL\n";
		if (PHP_OS == 'WINNT') {
			echo $prompt;
			$input = stream_get_line(STDIN, 1024, PHP_EOL);
		} else {
			$input = readline($prompt);
		}
		self::$URL = $input;
		return self::$URL;
		

	}

	public function buildDOM($url = NULL) {
		if (empty($url)) {
			$url = self::$URL;
		}
		$dom = new DomDocument;
		$dom->preserveWhiteSpace = false;
		$dom->loadHTMLFile($url);

		$xpath = new DomXPath($dom);

		return $xpath;

	}



}



 ?>