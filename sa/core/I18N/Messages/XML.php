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
   
   $Id: XML.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
  */

include_once('I18N/Messages/Common.php');
include_once('XML/Parser.php');

/**
*
*      Sample language file:
*      <?xml version="1.0" encoding="iso-8859-1"?>
*      <messages>
*          <message id="hello">Salut</message>
*      </messages>
*/

class I18N_Messages_XML extends I18N_Messages_Common {

    // {{ properties

    /**
    * Holds directory information
    *
    * @type  : string        Directory name
    * @access: private
    */
    var $_dir;

    var $_is_loaded = false;

    // }}
    // {{ constructor

    /**
    * Save Lanuguage and the directory name where language file resides.
    * Then load the file.
    *
    * @param : string          Lanuguage Code
    * @param : string          Directory Name
    *
    * @return: void
    * @access: public
    */
    function __construct($lang = 'en', $domain = '',$dir = './')
    {
        parent::__construct();
        $this->setDir($dir);
        $this->bindLanguage($lang);
        $this->bindDomain($domain);
        $this->_load();
    }

    // }}
    // {{ I18N_Messages_XML()

    /**
    * For pre-Zend2 compatibility. Call actual constructor
    *
    * @param : string          Lanuguage Code
    * @param : string          Directory Name
    *
    * @return: void
    * @access: public
    */
    function I18N_Messages_XML($lang = 'en', $domain = '', $dir = './')
    {
        $this->__construct($lang,$domain,$dir);
    }

    /**
    * Load the lanuguage file
    *
    * @param : string      Language code
    *
    * @return: void
    * @access: private
    */
    function _load()
    {
        $xml_file = $this->getDir() . '/' . $this->bindLanguage() . '/' . $this->bindDomain() . '.xml';
        if (is_readable($xml_file)) {
            $xml = & new XML_Messages_Parser($this);
            $xml->setInputFile($xml_file);
            if (!PEAR::isError($xml->parse())) {
                $this->_is_loaded = $xml_file;
            }
        }
    }

    function _isLoaded() {
        return $this->_is_loaded == $this->getDir() . '/' . $this->bindLanguage() . '/' . $this->bindDomain() . '.xml';
    }

    /**
    * Set directory
    *
    * @return: string     Directory name
    * @access: public
    */
    function setDir($dir)
    {
        $this->_dir = $dir;
    }

    /**
    * Return directory name
    *
    * @return: string     Directory name
    * @access: public
    */
    function getDir()
    {
        return $this->_dir;
    }

    function get($messageID)
    {
        // make sure it's loaded. for just after bindDomain() or bindLanuguage() method is called.
        if (!$this->_isLoaded()) {
            $this->_load();
        }
        return ($messageID !== "" && is_array($this->_message) && in_array($messageID, array_keys($this->_message))) ? $this->_message[$messageID] :$messageID;
    }
} //end class I18N_Messages_XML


class XML_Messages_Parser extends XML_Parser {
    var $_valid = false;
    var $_messageID = null;
    var $_i18n = null;

    function XML_Messages_Parser(&$i18n, $srcenc = 'UTF-8', $mode = 'func', $tgtenc = null) {
        parent::XML_Parser($srcenc, $mode, $tgtenc);
        $this->_i18n = &$i18n;
    }

    function xmltag_messages($xp, $elem, $attribs) {
        $this->_valid = true;
    }

    function xmltag_message($xp, $elem, $attribs) {
        $this->_messageID = $attribs['ID'];
        $this->_i18n->_message[$this->_messageID] = null;
    }

    function xmltag_message_($xp, $elem) {
        $this->_i18n->_message[$this->_messageID] = html_entity_decode($this->_i18n->_message[$this->_messageID]);
        $this->_messageID = null;
    }

    function cdataHandler($xp, $cdata) {
        if ($this->_valid && $this->_messageID) {
            $this->_i18n->_message[$this->_messageID] .= $cdata;
        }
    }
} //end class XML_Messages_Parser