<?php
class UofCalgaryScraper extends GeneralScraper {
	protected $name = "Scraper for University of Calgary website";
	protected $id = "uofc"; 

	public function scrapeCoursePage () {
		$cell_query = '(//table[tr/td[@class="myCell"]])';

		$code_query = '(.//*[@class="course-code"])[2]';
		$subj_query = '(.//*[@class="course-code"])[1]';
		$title_query = '(.//*[@class="course-code"])[3]';
		$desc_query = './/*[@class="course-desc"]';


		$cells = $this->findNodes($cell_query);
		foreach ($cells as $cell) {
			$course = new Course();
			$this->current_node = $cell;

			$code = $this->findNodes($code_query);
			foreach ($code as $c) {
				$course->course_number = $c->nodeValue;
			}

			$title = $this->findNodes($title_query);
			foreach ($title as $t) {
				$course->course_title = $t->nodeValue;
			}

			$subj = $this->findNodes($subj_query);
			foreach ($subj as $s) {
				$course->subject = $s->nodeValue;
			}

			$desc = $this->findNodes($desc_query);
			foreach ($desc as $d) {
				$course->description = $d->nodeValue;
			}

			$this->courses[] = $course;
		}
	}

	public function scrapeCatalogPage () {
		$dir = dirname($this->url);
		$link_query = '//*[@class="link-text" and not(contains(@href, "course-desc"))]/@href';
		$links = $this->findNodes($link_query);
		$links = $this->getNodeValues($links);
		$n = 0;

		foreach ($links as $link) {
			if ($n < 6) {
				$this->current_node = NULL;
				$this->xpath = Application::takeUrlReturnXpath($dir . '/' . $link);
				echo $dir . '/' . $link . "\n";
				echo $n;
				$this->scrapeCoursePage();
			}
			$n++;
		}
	}
}

?>