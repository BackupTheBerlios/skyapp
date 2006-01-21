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

   $Id: SApplication.php,v 1.1 2006/01/21 11:38:36 trinculescu Exp $
  */

define('APPLICATION_HOME', dirname(__FILE__));
define('APPLICATION_OK', 1);
define('APPLICATION_ERROR', -1);
define('APPLICATION_ERROR_NOT_IMPLEMENTED', -2);
define('APPLICATION_PAGE_VAR_NAME', 'p');
define('APPLICATION_EVENTS_VAR_NAME', 'e');
define('APPLICATION_LANG_VAR_NAME', 'l');
define('APPLICATION_DEFAULT_PAGE', 'index');
define('APPLICATION_401_PAGE', '401');
define('APPLICATION_404_PAGE', '404');

include_once('PEAR.php');
include_once('SADebug.php');
include_once('SAUrl.php');
include_once('Cache/SACache.php');
include_once('DB/SADB.php');
include_once('I18N/SAI18N.php');
include_once('Page/Breadcrumb.php');
include_once('Page/PageFactory.php');
include_once('Page/Page.php');
include_once('Session/SASession.php');

class SApplication extends PEAR {
    var $_page;
    var $_timer;
    var $_errors = array(
        APPLICATION_ERROR                   => 'Application Error: unknown error',
        APPLICATION_ERROR_NOT_IMPLEMENTED   => 'Application Error: method not implemented',
        );

    function SApplication() {
		$this->_updateMeStatic();
        if (ENABLE_PROFILING) {
            include_once('Benchmark/Timer.php');
            $this->_timer = & new Benchmark_Timer();
            $this->_timer->start();
        }

        parent::PEAR();

        $u = SAURL::restore();

        if (PEAR::isError($u)) {
            if ($u->getCode() == URL_MANIPULATION) {
                HTTP_Header::redirect(SAUrl::Url(APPLICATION_401_PAGE));
                exit;
            }
        }
    }
	
	function startSession() {
		if (USE_DB_SESSIONS) {
			SASession::setContainer('DB', DSN);
		}
		SASession::start(SESSION_NAME);
		SASession::setExpire(time() + SESSION_EXPIRES);
		SASession::setIdle(SESSION_IDLE);
		SASession::updateIdle();		
	}

    function run() {		
        $this->_page = & PageFactory::factory($_GET[APPLICATION_PAGE_VAR_NAME]);
		$this->_updateMeStatic();
        if (!PEAR::isError($this->_page)) {
            if (is_object($this->_page) && is_a($this->_page, 'page')) {
            	$this->_page->load();
                $this->_page->process();
                $this->_page->display();
                $this->_page->unload();
            }
        }
        else {
            print $this->_page->getMessage();
        }
    }
	
	function _updateMeStatic() {
		$app = & PEAR::getStaticProperty('Page', '_sa_app_object_');
		$app = $this;		
	}	

    function _triggerError($code = APPLICATION_ERROR) {
        return $this->throwError(
            $this->_errors[$code],
            $code,
            array(
                'class' => get_class($this),
                'file' => __FILE__,
                'line' => __LINE__
                )
            );
    }

    function _SApplication() {
        if (eregi('text/html', $this->_page->_content_type)) {
?>
<p style="font-size: x-small">Powered by <a href="http://developer.berlios.de/projects/skyapp/" target="_blank">SkyApp</a></p>
<?php
            if (ENABLE_PROFILING) {
?>

<p style="font-size: x-small">Execution time: <? $this->_timer->stop(); print $this->_timer->timeElapsed() ?> sec.</p>
<?php
            }
        }
    }
}//end class Application
