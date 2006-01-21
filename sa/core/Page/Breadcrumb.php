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

$Id: Breadcrumb.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
*/

class Breadcrumb {

	var $crumbs = array();

	function Breadcrumb(){

		if ($_SESSION['breadcrumb'] != null){
			$this->crumbs = $_SESSION['breadcrumb'];
		}

	}

	function add($label, $url, $level){

		$crumb = array();
		$crumb['label'] = $label;
		$crumb['url'] = $url;

		if ($crumb['label'] != null && $crumb['url'] != null && isset($level)){

			while(count($this->crumbs) > $level){

				array_pop($this->crumbs); //prune until we reach the $level we've allocated to this page

			}

			if (!isset($this->crumbs[0]) && $level > 0){ //If there's no session data yet, assume a homepage link

			$this->crumbs[0]['url'] = "/index.php";
			$this->crumbs[0]['label'] = "Home";

			}

			$this->crumbs[$level] = $crumb;

		}

		$_SESSION['breadcrumb'] = $this->crumbs; //Persist the data
	}

	/*
	* Output a semantic list of links.  See above for sample CSS.  Modify this to suit your design.
	*/
	function html(){
		reset($this->crumbs);
		$html = '<p class="breadcrumb">';
		$length = count($this->crumbs);
		for($i = 0; $i < $length; $i++) {
			if ($i == ($length - 1)) {
				$html .= '<span class="breadcrumbcurrent">' . $this->crumbs[$i]['label'] . '</span>';
			}
			else {
				$html .= '<a href="' . $this->crumbs[$i]['url'] . '">' . $this->crumbs[$i]['label'] . '</a>&nbsp;&raquo;&nbsp;';
			}
		}
		$html .= "</p>\n";

		return $html;
	}
}
