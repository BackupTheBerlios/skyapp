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

$Id: SmartyPage.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
*/

include_once(SMARTY_DIR . 'Smarty.class.php');

class SmartyPage extends Page {
	var $_smarty;
	var $_template;

	function SmartyPage() {
		parent::Page();
		$this->_smarty = & new Smarty();
		$this->_smarty->template_dir = APPLICATION_HOME . '/Templates';
		$this->_smarty->compile_dir = APPLICATION_HOME . '/Templates_c';
		$this->_smarty->use_sub_dirs = true;

		$pageName = $this->getName();
		$this->setTempalte(str_replace("-", "/", $pageName) . '.tpl');
	}

	function setTempalte($template) {
		$this->_template = $template;
	}

	function setAttribute($name, $value) {
		$this->_smarty->assign($name, $value);
	}

	function display() {
		$this->setContents(PAGE_DEFAULT_REGION, $this->_smarty->fetch($this->_template));
		parent::display();
	}
}