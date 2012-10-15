<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: version.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2012 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//class JEvents_Version extends JObject {
class JEventsVersion {
	/** @var string Product */
	var $PRODUCT 	= 'JEvents';
	/** @var int Main Release Level */
	var $RELEASE 	= '2';
	/** @var int Sub Release Level */
	var $DEV_LEVEL 	= '2';
	/** @var string Patch Level */
	var $PATCH_LEVEL = '8';
	/** @var string Development Status */
	var $DEV_STATUS = '';
	/** @var string Copyright Text */
	var $COPYRIGHT 	= 'Copyright &copy; 2006-2012';
	/** @var string Copyright Text */
	var $COPYRIGHTBY 	= 'GWE Systems Ltd, JEvents Project Group';
	/** @var string LINK */
	var $LINK 		= 'http://www.jevents.net';

	function &getInstance() {

		static $instance;

		if ($instance == null) {
			$instance = new JEventsVersion();
		}
		return $instance;
	}

	/**
	 * access instance properties
	 * @var    string		property name
	 * @return mixed		property content
	 */
	function get($property) {
		if(isset($this->$property)) {
			return $this->$property;
		}
		return null;
	}

	/**
	 * Returns a reference to a global EventsVersion object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @access public
	 * @return object  			The EventsVersion object.
	 */

	/**
	 * @return string URL
	 */
	function getUrl() {
		return $this->LINK;
	}
	/**
	 * @return string short Copyright
	 */
	function getShortCopyright() {
		return $this->COPYRIGHT;
	}
	/**
	 * @return string long Copyright
	 */
	function getLongCopyright() {
		return $this->COPYRIGHT . ' ' . $this->COPYRIGHTBY;
	}
	/**
	 * @return string Long format version
	 */
	function getLongVersion() {
		return $this->PRODUCT .' '. $this->getShortVersion();
	}

	/**
	 * @return string Short version format
	 */
	function getShortVersion() {
		return 'v' . $this->RELEASE . '.' . $this->DEV_LEVEL . '.' . $this->PATCH_LEVEL . ' ' . $this->DEV_STATUS;
	}

}
?>
