<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: version.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//class JEvents_Version extends JObject {
class JEventsVersion {
	/** @var string Product */
	var $PRODUCT 	= 'JEvents';
	/** @var string Release Level */
	var $RELEASE 	= '3.4.46';
	/** @var int Sub Release - backwards compatability only for club addons */
	var $DEV_LEVEL 	= '0';
	/** @var string Patch Level  - backwards compatability only for club addons */
	var $PATCH_LEVEL = '0';
	
	/** @var string Development Status */
	var $DEV_STATUS = 'Stable';
	/** @var string Copyright Text */
	var $COPYRIGHT 	= 'Copyright &copy; 2006-2018';
	/** @var string Copyright Text */
	var $COPYRIGHTBY 	= 'GWE Systems Ltd, JEvents Project Group';
	/** @var string LINK */
	var $LINK 		= 'http://www.jevents.net';

	public static function &getInstance() {

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
	public function get($property) {
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
	public function getUrl() {
		return $this->LINK;
	}
	/**
	 * @return string short Copyright
	 */
	public function getShortCopyright() {
		return $this->COPYRIGHT;
	}
	/**
	 * @return string long Copyright
	 */
	public function getLongCopyright() {
		return $this->COPYRIGHT . ' ' . $this->COPYRIGHTBY;
	}
	/**
	 * @return string Long format version
	 */
	public function getLongVersion() {
		return $this->PRODUCT .' '. $this->getShortVersion();
	}

	/**
	 * @return string Short version format
	 */
	public function getShortVersion() {
		return 'v' . $this->RELEASE . ' ' . $this->DEV_STATUS;
	}
	
}

class JevJoomlaVersion {
	
	public static function isCompatible($minimum)
	{
		return version_compare(JVERSION, $minimum, 'ge');
	}

}
