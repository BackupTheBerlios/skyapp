<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Container class for storing session data
 *
 * PHP version 4
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTTP
 * @package    HTTP_Session
 * @author     Alexander Radivanovich <info@wwwlab.net>
 * @author     David Costa <gurugeek@php.net>
 * @author     Michael Metz <pear.metz@speedpartner.de>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @author     Torsten Roehr <torsten.roehr@gmx.de>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: Container.php,v 1.1 2006/01/21 11:38:35 trinculescu Exp $
 * @link       http://pear.php.net/package/HTTP_Session
 * @since      File available since Release 0.4.0
 */

/**
 * Container class for storing session data
 *
 * @category   HTTP
 * @package    HTTP_Session
 * @author     David Costa <gurugeek@php.net>
 * @author     Michael Metz <pear.metz@speedpartner.de>
 * @author     Stefan Neufeind <pear.neufeind@speedpartner.de>
 * @author     Torsten Roehr <torsten.roehr@gmx.de>
 * @copyright  1997-2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTTP_Session
 * @since      Class available since Release 0.4.0
 */
class HTTP_Session_Container
{

    /**
     * Additional options for the container object
     *
     * @var array
     * @access private
     */
    var $options = array();

    /**
     * Constrtuctor method
     *
     * @access public
     * @param  array  $options Additional options for the container object
     * @return void
     */
    function HTTP_Session_Container($options = null)
    {
        $this->_setDefaults();
        if (is_array($options)) {
            $this->_parseOptions();
        }
    }

    /**
     * Set some default options
     *
     * @access private
     */
    function _setDefaults()
    {
    }

    /**
     * Parse options passed to the container class
     *
     * @access private
     * @param array Options
     */
    function _parseOptions($options)
    {
        foreach ($options as $option => $value) {
            if (in_array($option, array_keys($this->options))) {
                $this->options[$option] = $value;
            }
        }
    }

    /**
     * This function is called by the session
     * handler to initialize things
     *
     * @access public
     */
    function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * This function is called when the page is finished
     * executing and the session handler needs to close things off
     *
     * Has to be overwritten by each container class
     *
     * @access public
     */
    function close()
    {
        return true;
    }

    /**
     * This function is called by the session handler
     * to read the data associated with a given session ID.
     * This function must retrieve and return the session data
     * for the session identified by $id.
     *
     * Has to be overwritten by each container class
     *
     * @access public
     * @param  mixed  $id ID of the session
     * @return mixed      The data associated with a given session ID
     */
    function read($id)
    {
        return '';
    }

    /**
     * This function is called when the session handler
     * has session data to save, which usually happens
     * at the end of your script
     *
     * Has to be overwritten by each container class
     *
     * @access public
     * @param  mixed   $id   ID of the session
     * @param  mixed   $data The data associated with a given session ID
     * @return boolean Obvious
     */
    function write($id, $data)
    {
        return true;
    }

    /**
     * This function is called when a session is destroyed.
     * It is responsible for deleting the session and cleaning things up.
     *
     * Has to be overwritten by each container class
     *
     * @access public
     * @param  mixed  $id ID of the session
     * @return boolean Obvious
     */
    function destroy($id)
    {
        return true;
    }

    /**
     * This function is responsible for garbage collection.
     * In the case of session handling, it is responsible
     * for deleting old, stale sessions that are hanging around.
     * The session handler will call this every now and then.
     *
     * Has to be overwritten by each container class
     *
     * @access public
     * @param  integer $maxlifetime ???
     * @return boolean Obvious
     */
    function gc($maxlifetime)
    {
        return true;
    }

    /**
     * Set session save handler
     *
     * @access public
     * @return void
     */
    function set()
    {
        $GLOBALS['HTTP_Session_Container'] =& $this;
        session_module_name('user');
        session_set_save_handler(
            'HTTP_Session_Open',
            'HTTP_Session_Close',
            'HTTP_Session_Read',
            'HTTP_Session_Write',
            'HTTP_Session_Destroy',
            'HTTP_Session_GC'
        );
    }

}

// Delegate function calls to the object's methods
/** @ignore */
function HTTP_Session_Open($save_path, $session_name) { return $GLOBALS['HTTP_Session_Container']->open($save_path, $session_name); }
/** @ignore */
function HTTP_Session_Close()                         { return $GLOBALS['HTTP_Session_Container']->close(); }
/** @ignore */
function HTTP_Session_Read($id)                       { return $GLOBALS['HTTP_Session_Container']->read($id); }
/** @ignore */
function HTTP_Session_Write($id, $data)               { return $GLOBALS['HTTP_Session_Container']->write($id, $data); }
/** @ignore */
function HTTP_Session_Destroy($id)                    { return $GLOBALS['HTTP_Session_Container']->destroy($id); }
/** @ignore */
function HTTP_Session_GC($maxlifetime)                { return $GLOBALS['HTTP_Session_Container']->gc($maxlifetime); }

?>