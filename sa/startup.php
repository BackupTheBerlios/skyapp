<?php

ob_start();

ini_set('include_path', '.' . PATH_SEPARATOR . 'PEAR' . PATH_SEPARATOR . 'core');
ini_set('session.use_trans_sid', 0);

error_reporting (E_ALL ^ E_NOTICE);

define('DSN', '');
define('ENABLE_PROFILING', false);
define('ENABLE_DEBUG', false);
define('DUMMY_EXTENSION', '.html');
define('ENCRYPT_URLS', false);
define('SECRET_KEY', 'monaco');
define('CHECK_URLS_CRC32', true); //has effect only if ENCRYPT_URLS == true
define('SESSION_NAME', 'SASID');
define('FORCE_SESSION_COOKIE', true);
define('SESSION_EXPIRES', 0);
define('SESSION_IDLE', 0);
define('USE_DB_SESSIONS', false);
define('USE_CACHE', false);
define('CACHE_EXPIRES', 3600); //0 for endless
define('USE_DB_CACHE', false);

define('WEBAPP_DIR', 'webapp/');

include_once(WEBAPP_DIR . 'DemoApplication.php');
include_once(WEBAPP_DIR . 'DemoPage.php');