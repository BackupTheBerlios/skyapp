<?php
class Page_index extends DemoPage {
	function Page_index() {
		parent::DemoPage();
	}
	function display() {
		$this->setContents('_TITLE_', 'Welcome to my site');
		$this->setContents(PAGE_DEFAULT_REGION, '<h1>Welcome friend</h1>');
		$this->appendContents(PAGE_DEFAULT_REGION, '<a href="' . SAUrl::Url('nested/index', array(APPLICATION_EVENTS_VAR_NAME => 'update')) . '">Click me</a>');
		parent::display();
	}
}