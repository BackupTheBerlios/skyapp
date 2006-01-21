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

$Id: SAUrl.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
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
		$url = (($fullpath) ? SAURL::baseHref($secure) : '') . ((CLEAN_URLS) ? '' : basename($_SERVER['SCRIPT_NAME'])) . ((SEARCH_ENGINE_FRIENDLY_URLS) ? ((CLEAN_URLS) ? '' : '/') : '?') . SAUrl::build($page, $params);
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
		if (SEARCH_ENGINE_FRIENDLY_URLS) {
			$arr_path_info = explode('/', $_SERVER['PATH_INFO']);
			$length = count($arr_path_info);
			for($i = 1; $i < $length - 2; $i += 2) {
				$var = $arr_path_info[$i];
				$_REQUEST[$var] = $_GET[$var] = urldecode(html_entity_decode($arr_path_info[$i + 1]));
			}
			$page = $arr_path_info[$i];
			$_REQUEST[APPLICATION_PAGE_VAR_NAME] = $_GET[APPLICATION_PAGE_VAR_NAME] = substr($page, 0, strpos($page, DUMMY_EXTENSION));
		}
	}

	function restoreEncrypted() {
		$encoded = (SEARCH_ENGINE_FRIENDLY_URLS) ? substr($_SERVER['PATH_INFO'], 1) : $_SERVER['QUERY_STRING'];
		$rc4 = & new Crypt_RC4(SECRET_KEY);
		$encoded = base64_decode($encoded);
		$rc4->decrypt($encoded);
		$params = unserialize($encoded);
		if (is_array($params)) {
			foreach($params as $name => $value) {
				$_REQUEST[$name] = $_GET[$name] = $value;
			}
			unset($params['crc32_chk']);
		}
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
		if (is_array($params)) {
			$keys = array_keys($params);
			$len = count($keys);
			for($i = 0; $i < $len; $i++) {
				$value = $params[$keys[$i]];
				if (empty($value)) continue;
				$params[$keys[$i]] = htmlentities(urlencode($value));
				$url .= (SEARCH_ENGINE_FRIENDLY_URLS) ? $keys[$i] . '/' . $params[$keys[$i]] : $keys[$i] . '=' . $params[$keys[$i]];
				$url .= (SEARCH_ENGINE_FRIENDLY_URLS) ? '/' : '&';
			}
		}
		$url .= (SEARCH_ENGINE_FRIENDLY_URLS) ? "$page" . DUMMY_EXTENSION : APPLICATION_PAGE_VAR_NAME . "=$page";
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