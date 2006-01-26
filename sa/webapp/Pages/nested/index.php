<?php

class Page_index extends DemoPage {
	function display() {
		$this->setContents('_TITLE_', 'Nested page');
		$this->setContents(PAGE_DEFAULT_REGION, '<a href="' . SAUrl::Url('index') . '">Back</a>');
		$this->appendContents(PAGE_DEFAULT_REGION, '<h1>This is a nested page</h1>');
		parent::display();
	}
}