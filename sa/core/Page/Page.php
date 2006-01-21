<?php
/*
+-----------------------------------------------------------------------+
| SkyApp, The PHP Application Framework.                                |
| http://developer.berlios.de/projects/skyapp/                          |
+-----------------------------------------------------------------------+
| This source file is released under LGPL license, available through    |
| the world wide web at http://www.gnu.org/copyleft/lesser.html.        |
| This library is distributed WITHOUT ANY WARRANTY. Please see the LGPL |
| for more details.                                                     |
+-----------------------------------------------------------------------+
| Authors: Andi Trînculescu <andi@skyweb.ro>                            |
+-----------------------------------------------------------------------+

$Id: Page.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
*/

define('PAGE_OK', 1);
define('PAGE_ERROR', -1);
define('PAGE_ERROR_NOT_IMPLEMENTED', -2);
define('PAGE_LAYOUTS_DIR', APPLICATION_HOME . '/Layouts/');
define('PAGE_DEFAULT_REGION', '_CONTENTS_');

define('DEFAULT_LANGUAGE', 'en');
define('I18N_DEFAULT_DOMAIN', 'general');
define('I18N_DEFAULT_DIR', APPLICATION_HOME . '/Lang');

include_once('HTTP/Header.php');
include_once('Net/UserAgent/Detect.php');

class Page extends PEAR {
	var $_header = null;
	var $_start_session = true;
	var $_layout = 'DefaultLayout.php';
	var $_regions = array();
	var $_content_type = 'text/html; charset=utf-8';
	var $_accept_languages = array();
	var $_errors = array(
		PAGE_ERROR                   => 'Page Error: unknown error',
		PAGE_ERROR_NOT_IMPLEMENTED   => 'Page Error: method not implemented',
	);

	function Page() {
		parent::PEAR();

		$this->_header = & new HTTP_Header;
		//start the session
		if ($this->_start_session) {
			$this->startSession();
		}
		//set language related session vars
		$this->setLanguage();
		$this->setI18NDomain();
		$this->setI18NDir();
	}

	function _Page() {}
	
	function & getApplicationObject() {
		return PEAR::getStaticProperty('Page', '_sa_app_object_');
	}
	
	function startSession() {
		$app = $this->getApplicationObject();
		$app->startSession();		
	}
	
	function sessionAutostart($auto = true) {
		$this->_start_session = $auto;
	}

	function getName() {
		$name = $_GET[APPLICATION_PAGE_VAR_NAME];
		$name = empty($name) ? APPLICATION_DEFAULT_PAGE : $name;
		return $name;
	}

	function load() {
		return true;
	}

	function unload() {
		unset($_SESSION['flash']);
	}

	function setLayout($layout) {
		$this->_layout = $layout;
	}

	function getLayout() {
		return PAGE_LAYOUTS_DIR . '/' . $this->_layout;
	}

	function setContents($region, $contents) {
		$this->_regions[$region] = $contents;
	}

	function prependContents($region, $contents) {
		$this->_regions[$region] = $contents . $this->_regions[$region];
	}

	function appendContents($region, $contents) {
		$this->_regions[$region] .= $contents;
	}

	function hasLayout()
	{
		$layout = $this->getLayout();
		return is_file($layout) && is_readable($layout);
	}

	function getContents($region = null) {
		$contents = null;

		if ($region) {
			$contents = $this->_regions[$region];
		}
		else {
			if ($this->hasLayout()) {
				ob_start();
				include_once($this->getLayout());
				$contents = ob_get_contents();
				ob_end_clean();
			} else {
				$contents = $this->_regions[PAGE_DEFAULT_REGION];
			}
		}

		return $contents;
	}

	function setAttribute() {
		return false;
	}

	function process() {
		$this->_doEvents();
	}

	function fetch() {
		return $this->getContents();
	}

	function display() {
		$this->setHeader('content-type', $this->_content_type);
		$this->sendHeaders('DUMMY');
		$contents = $this->fetch();
		print (PEAR::isError($contents)) ? $contents->getMessage() : $contents;
	}

	function setHeader($key, $value) {
		$this->_header->setHeader($key, $value);
	}

	function sendHeaders($keys) {
		$this->_header->sendHeaders($keys);
	}

	function setContentType($type) {
		$this->_content_type = $type;
	}

	function setLanguage($lang = null) {		
		$currentLanguage = $_SESSION['_sa_app_language_'];
		$lang = ($lang) ? $lang : $_REQUEST[APPLICATION_LANG_VAR_NAME];
		$lang = ($lang) ? ((in_array($lang, $this->_accept_languages)) ? $lang : DEFAULT_LANGUAGE) : (($currentLanguage) ? $currentLanguage : DEFAULT_LANGUAGE);
		$_SESSION['_sa_app_language_'] = $lang;
	}

	function & getLanguage() {
		return $_SESSION['_sa_app_language_'];
	}

	function setI18NDomain($domain = I18N_DEFAULT_DOMAIN) {
		$i18n = & PEAR::getStaticProperty('Page', '_sa_i18n_domain_');
		$i18n = $domain;
	}

	function & getI18NDomain() {
		return PEAR::getStaticProperty('Page', '_sa_i18n_domain_');
	}

	function setI18NDir($dir = I18N_DEFAULT_DIR) {
		$i18n = & PEAR::getStaticProperty('Page', '_sa_i18n_dir_');
		$i18n = $dir;
	}

	function & getI18NDir() {
		return PEAR::getStaticProperty('Page', '_sa_i18n_dir_');
	}

	function _doEvents() {
		$arr_action = explode(':', $_REQUEST[APPLICATION_EVENTS_VAR_NAME]);
		if (is_array($arr_action)) {
			foreach($arr_action as $ind => $action) {
				$method = 'do' . ucfirst($action);
				if (method_exists($this, $method)) {
					call_user_func_array(array(&$this, $method), null);
				}
			}
		}
	}

	function _triggerError($code = PAGE_ERROR) {
		return $this->throwError(
		$this->_errors[$code],
		$code,
		array(
		'class' => get_class($this),
		'file' => __FILE__,
		'line' => __LINE__
		)
		);
	}
}//end class Page
