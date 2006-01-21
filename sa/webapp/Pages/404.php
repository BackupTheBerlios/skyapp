<?php

class Page_404 extends DemoPage {
	function Page_404() {
		parent::DemoPage();
	}
	function display() {
		$this->_header->sendStatusCode('404');
		$this->setContents('_TITLE_', '404 - Page not found');
		$this->setContents(PAGE_DEFAULT_REGION, '<h1 style="color: red">Page not found</h1>');
		parent::display();
	}
}