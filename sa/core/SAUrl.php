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

$Id: SAUrl.php,v 1.4 2006/01/26 23:33:34 trinculescu Exp $
*/


define('URL_OK', 1);
define('URL_MANIPULATION', -1);

include_once('Crypt/Rc4.php');

class SAUrl {
	function Url($page = APPLICATION_DEFAULT_PAGE, $params = array(), $fullpath = true, $secure = false) {
		unset($params[APPLICATION_PAGE_VAR_NAME]);
		if (!FORCE_SESSION_COOKIE && SID && SASession::id()) {
			$params[SASession::name()] = SASession::id();
		}
		$filename = basename($page);
		$dir = dirname($page);
		$dir = ($dir == '.') ? '' : $dir . '/';
		$url = (($fullpath) ? SAURL::baseHref($secure) : '') . $dir . SAUrl::build($filename, $params);
		return $url;
	}

	function baseHref($secure = false) {
		$path = dirname($_SERVER['SCRIPT_NAME']);
		return 'http' . (($secure) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/' . substr($path, 1) . ((strlen($path) > 1) ? '/' : '');
	}

	function restore() {
		if (ENCRYPT_URLS) {
			return SAUrl::restoreEncrypted();
		} else {
			return SAUrl::restoreSimple();
		}
	}

	function restoreSimple() {		
		$vars = explode(',', basename($_SERVER['PATH_INFO']));
		$len = count($vars);
		for($i = 0; $i < $len - 2; $i += 2) {
			$_REQUEST[$vars[$i]] = $_GET[$vars[$i]] = $vars[$i + 1];
		}
		$dir = (empty($_SERVER['PATH_INFO']) || dirname($_SERVER['PATH_INFO']) == '/') ? '' : substr(dirname($_SERVER['PATH_INFO']), 1) . '/';		
		$_REQUEST[APPLICATION_PAGE_VAR_NAME] = $_GET[APPLICATION_PAGE_VAR_NAME] = $dir . substr($vars[$len - 1], 0, strpos($vars[$len - 1], DUMMY_EXTENSION));
	}

	function restoreEncrypted() {
		$encoded = substr($_SERVER['PATH_INFO'], 1);
		$rc4 = & new Crypt_RC4(SECRET_KEY);
		$encoded = base64_decode($encoded);
		$rc4->decrypt($encoded);
		$params = unserialize($encoded);
		if (is_array($params)) {
			foreach($params as $name => $value) {
				$_REQUEST[$name] = $_GET[$name] = $value;
			}
			unset($params['crc32_chk']);
			if (CHECK_URLS_CRC32) {
				if (!isset($_GET['crc32_chk']) || (crc32(serialize($params)) != $_GET['crc32_chk'])) {
					return PEAR::throwError(
						'URL Error: URL Manipulation',
						URL_MANIPULATION,
						array(
							'class' => 'SAUrl',
							'file' => __FILE__,
							'line' => __LINE__
						)
					);
				}
			}
		}
		return URL_OK;
	}

	function build($page, $params) {
		if (ENCRYPT_URLS) {
			$url = SAUrl::buildEncrypted($page, $params);
		} else {
			$url = SAUrl::buildSimple($page, $params);
		}
		return $url;
	}

	function buildSimple($page, $params) {
		$url = '';
		if (is_array($params) && count($params)) {
			$keys = array_keys($params);
			$len = count($keys);
			for($i = 0; $i < $len; $i++) {
				$value = $params[$keys[$i]];
				if (empty($value)) continue;
				$params[$keys[$i]] = htmlentities(urlencode($value));
				$url .= $keys[$i] . ',' . $value . ',';
			}
		}
		$url .= $page . DUMMY_EXTENSION;
		return $url;
	}

	function buildEncrypted($page, $params) {
		$params[APPLICATION_PAGE_VAR_NAME] = $page;
		$params['crc32_chk'] = crc32(serialize($params));
		$rc4 = & new Crypt_RC4(SECRET_KEY);
		$encoded = serialize($params);
		$rc4->crypt($encoded);
		$encoded = base64_encode($encoded);
		return $encoded;
	}
} //end class SAUrl