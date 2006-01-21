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

   $Id: SAI18N.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
  */

include_once('I18N/Messages/XML.php');

class SAI18N {
    function _($messageID) {
        static $i18n;

        if (is_null($i18n)) {
            $i18n = new I18N_Messages_XML;
        }

		$lang = $_SESSION['_sa_app_language_'];
		$i18nDomain = PEAR::getStaticProperty('Page', '_sa_i18n_domain_');
		$i18nDir = PEAR::getStaticProperty('Page', '_sa_i18n_dir_');
		
        $i18n->bindLanguage($lang);
        $i18n->bindDomain($i18nDomain);
        $i18n->setDir($i18nDir);

        if (USE_CACHE) {
            $cache = & SACache::singleton();
            $save = $GLOBALS['i18n'];
            $GLOBALS['i18n'] = &$i18n;
            $result = $cache->call('i18n->_', $messageID, $lang, $i18nDomain, $i18nDir);
            $GLOBALS['i18n'] = $save;
        }
        else {
            $result = $i18n->_($messageID);
        }
        return $result;
    }
} //end class SAI18n