<?php

define('APPLICATION_HOME', dirname(__FILE__));

include_once('SApplication.php');

class DemoApplication extends SApplication {
	function DemoApplication() {
		parent::SApplication();
	}
}