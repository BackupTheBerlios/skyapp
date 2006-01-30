<?php

class Page_index extends DemoPage {
	function load() {
		$this->setContents('_TITLE_', 'Nested page');
		$this->setContents(PAGE_DEFAULT_REGION, '<a href="' . SAUrl::Url('index') . '">Back</a>');
		$this->appendContents(PAGE_DEFAULT_REGION, '<h1>This is a nested page</h1>');				
	}
	
	function doUpdate() {
		$this->appendContents(PAGE_DEFAULT_REGION, '<p>Executed update event</p>');
	}
}