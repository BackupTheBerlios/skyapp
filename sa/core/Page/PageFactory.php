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

   $Id: PageFactory.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
  */

define('PAGE_FACTORY_OK', 1);
define('PAGE_FACTORY_ERROR', -1);
define('PAGE_FACTORY_ERROR_NOT_FOUND', -2);
define('PAGE_FACTORY_SEARCH_DIR', APPLICATION_HOME . '/Pages/');

class PageFactory extends PEAR {

    function &factory($name) {
        $name = empty($name) ? APPLICATION_DEFAULT_PAGE : $name;
        include_once($file = PAGE_FACTORY_SEARCH_DIR . str_replace("-", "/", $name) . '.php');

        if (!class_exists($classname = 'Page_' . basename($file, '.php'))) {
            if ($name == APPLICATION_404_PAGE) {
                return PEAR::raiseError(null, PAGE_FACTORY_ERROR_NOT_FOUND,
                                        null, null, null, 'PageFactory_Error', true);
            }
            else {
                HTTP_Header::redirect(SAURL::url(APPLICATION_404_PAGE));
                exit;
            }
        }

        $obj =& new $classname;

        return $obj;
    }

    function isError($value)
    {
        return (is_object($value) &&
                (get_class($value) == 'pagefactory_error' ||
                 is_subclass_of($value, 'pagefactory_error')));
    }

    function errorMessage($value)
    {
        static $errorMessages;
        if (!isset($errorMessages)) {
            $errorMessages = array(
                PAGE_FACTORY_ERROR              => 'unknown error',
                PAGE_FACTORY_ERROR_NOT_FOUND    => 'page not found');
        }
        if (PageFactory::isError($value)) {
            $value = $value->getCode();
        }

        return isset($errorMessages[$value]) ? $errorMessages[$value] : $errorMessages[PAGE_FACTORY_ERROR];
    }
}//end class PageFactory

class PageFactory_Error extends PEAR_Error
{
    function PageFactory_Error($code = PAGE_FACTORY_ERROR, $mode = PEAR_ERROR_RETURN,
              $level = E_USER_NOTICE, $debuginfo = null)
    {
        if (is_int($code)) {
            $this->PEAR_Error('PageFactory Error: ' . PageFactory::errorMessage($code), $code, $mode, $level, $debuginfo);
        } else {
            $this->PEAR_Error("PageFactory Error: $code", PAGE_FACTORY_ERROR, $mode, $level, $debuginfo);
        }
    }
}//end class PageFactory_Error
