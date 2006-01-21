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
   
   $Id: SASession.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
  */

include_once('HTTP/Session.php');

class SASession extends HTTP_Session {
    function start($name = 'SessionID', $id = null)
    {
        HTTP_Session::name($name);
        if (is_null(SASession::detectID())) {
            HTTP_Session::id($id ? $id : uniqid(dechex(rand())));
        }
        session_start();
        if (!isset($_SESSION['__HTTP_Session_Info'])) {
            $_SESSION['__HTTP_Session_Info'] = HTTP_SESSION_STARTED;
        } else {
            $_SESSION['__HTTP_Session_Info'] = HTTP_SESSION_CONTINUED;
        }
    }

    function detectID()
    {
        if (SASession::useCookies()) {
            if (isset($_COOKIE[HTTP_Session::name()])) {
                return $_COOKIE[HTTP_Session::name()];
            }
        } else {
            if (isset($_GET[HTTP_Session::name()])) {
                return $_GET[HTTP_Session::name()];
            }
            if (isset($_POST[HTTP_Session::name()])) {
                return $_POST[HTTP_Session::name()];
            }
        }
        return null;
    }


    function useCookies() {
        return isset($_COOKIE[HTTP_Session::name()]);
    }
} //end class SASession