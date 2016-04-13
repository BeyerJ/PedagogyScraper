<?php
class UofCalgaryScraper extends GeneralScraper {
	protected $name = "Scraper for University of Calgary website";
	public $id = "uofc";


	protected $link_query = '//*[@class="link-text" and not(contains(@href, "course-desc"))]/@href';

	protected $cell_query = '(//table[tr/td[@class="myCell"]])';

	protected $code_query = '(.//*[@class="course-code"])[2]';
	protected $subj_query = '(.//*[@class="course-code"])[1]';
	protected $title_query = '(.//*[@class="course-code"])[3]';
	protected $desc_query = './/*[@class="course-desc"]';




}

?>