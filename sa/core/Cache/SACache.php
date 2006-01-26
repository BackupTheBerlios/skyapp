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
   
   $Id: SACache.php,v 1.2 2006/01/26 22:50:25 trinculescu Exp $
  */

include_once('Cache/Function.php');

class SACache {
    function &singleton() {
        static $cache;

        if (is_null($cache)) {
            if (USE_DB_CACHE) {
                $cache = new Cache_Function('db', array('dsn' => DSN, 'cache_table' => 'cache'), CACHE_EXPIRES);
            }
            else {
                $cache = new Cache_Function('file', array('cache_dir' => '/tmp'), CACHE_EXPIRES);
            }
            //change the varible name flushCache to something else
            if ($_GET['flushCache'] || $_POST['flushCache']) {
                $cache->flush('function_cache');
            }
        }
        return $cache;
    }
} //end class SACache