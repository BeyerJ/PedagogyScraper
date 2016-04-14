<?php
class UofCalgaryScraper extends GeneralScraper {
	protected $name = "Scraper for University of Calgary website";
	protected $id = "uofc";

	public function __construct () {
		parent::__construct();
		$this->link_query = '//*[@class="link-text" and not(contains(@href, "course-desc"))]/@href';
		$this->cell_query = '(//table[tr/td[@class="myCell"]])';
		$this->texts_query = './/descendant::text()';

		/*COURSE NUMBER*/
		$this->course_number_query = '(.//*[@class="course-code"])[2]';
		/*COURSE TITLE*/
		$this->course_title_query = '(.//*[@class="course-code"])[3]';

		/*COURSE SUBJECT*/
		$this->course_subject_query = '(.//*[@class="course-code"])[1]';

		/*DESCRIPTION*/
		$this->description_query = './/*[@class="course-desc"]';
	}

	public function checkIfApplies () {
		echo "Checking if " . $this->name . " applies to URL '" . $this->url . "'.\n";
		return (parse_url($this->url, PHP_URL_HOST) == "www.ucalgary.ca"); 
	}
}

?>