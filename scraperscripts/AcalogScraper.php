<?php
class AcalogScraper extends GeneralScraper {
	protected $name = "Scraper for Acalog-based websites";
	protected $id = "acalog";
	protected $stop_list = ["help", "back to top", "print-friendly page", "[", "]", "|", "add to", "favorites", "add to favorites"];

	public function __construct () {
		parent::__construct();
		$this->link_query = '//*[contains(@onclick, "showCourse")]/@href';
		$this->cell_query = '(//*[@class="block_content_outer"])';
		$this->texts_query = './/descendant::text()';
		$this->next_page_query = "//td[a[contains(@href, 'cpage')]]/strong/following-sibling::a[1]/@href";

		/*COURSE NUMBER*/
		$this->course_number_query = '*//h1';
		$this->course_number_regex = '/ (?<course_number>[0-9]{3,4}) /';
		/*COURSE TITLE*/
		$this->course_title_query = '*//h1';
		$this->course_title_regex = '/ - (?<course_title>[A-Za-z ,\t\[\]]+)$/';
		/*COURSE LETTERCODE*/
		$this->course_lettercode_query = '*//h1';
		$this->course_lettercode_regex = '/^(?<course_lettercode>[A-Z]{2,5}) /';
	}


	public function checkIfApplies () {
		$query = "//a[contains(@href, 'http://www.acalog.com') and contains(text(), 'Acalog')]";
		$nodes = $this->findNodes($query);
		return !(empty($nodes));
	}

	/* POSSIBLE REGEXPS
	**********************

	//YEAR: '/(?<year>[0-9]{4}-[0-9]{4})/' -- four digits, then dash, then four digits
	//TERM: '/^(?<term>[A-Za-z]+)\./' -- start of string, then text using latin characters, then dot
	//TERM: '/^\((?<term>.*), [0-9]{1}-[0-9]{1}-[0-9]{1}\)/' -- start of string, open bracket, then text, then space and the word "term"
	//CREDIT: '/ (?<credit>[0-9]{1})$/' -- space then a signle digit at the end of the string
	//DESCRIPTION: '/[0-9]{1}-[0-9]{1}-[0-9]{1}\) (?<description>.*)$/' -- closing bracket, then anything, then end of string

	***********************/




}

?>