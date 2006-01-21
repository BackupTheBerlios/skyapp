<?php
class Page_index extends DemoPage {
	function Page_index() {
		parent::DemoPage();
	}
	function display() {
		$this->setContents('_TITLE_', 'Welcome to my site');
		$this->setContents(PAGE_DEFAULT_REGION, '<h1>Welcome friend</h1>');
		parent::display();
	}
}