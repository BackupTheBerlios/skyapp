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
   
   $Id: SADB.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
  */

include_once('DB.php');

class SADB {
    function &singleton($dsn = DSN, $fetchmode = DB_FETCHMODE_ASSOC) {
		$db = & PEAR::getStaticProperty('SADB', md5($dsn));
		if (is_null($db)) {
			$db = & DB::connect(DSN);
			if (DB::isError($db)) {
				PEAR::raiseError($db->getMessage(), $db->getCode(), PEAR_ERROR_DIE);
			}
			$db->setFetchMode($fetchmode);
		}
		return $db;
    }
} //end class SADB