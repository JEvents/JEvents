<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: xconfig.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Handler of the components configuration parameter
 * configuration parameters are stored in a INI style file
 *
 * The INI file is loaded into a Parameter object
 * 
 * @author     Thomas Stahl
 * @since      1.4
 */
class JEVXConfig extends JParameter
{

	/** @var string			full path name of current inifile */
	var $_inifile_path			= null;

	/**
	 * Constructor
	 *
	 */
	function __construct($data='') {

		parent::__construct($data);
	}


	/**
	 * get path name of the default INI file
	 *
	 * @static
	 * @access private
	 * @since 1.4
	 */
	function _getDefaultINIfilePath() {

		return dirname(dirname(__FILE__)) . '/' . 'events_config.ini.php';
	}

	/**
	 * Save a  registry into INI file 
	 *
	 * @access public
	 * @since 1.4
	 */
	function saveEventsINI($inifile='') {

		if (!$inifile) {
			$inifile = ($this->_inifile_path) ? $this->_inifile_path : JEVXConfig::_getDefaultINIfilePath();
		}
		$writable = false;
		$errmsg   = null;
		$oldperm  = null;

		$errmsg  =  JText::_('JEV_MSG_WARNING') . ' ' . JText::_('JEV_MSG_CHMOD_CONFIG') . '(' . $inifile . ')';

		if (is_file($inifile)) {
			$oldperm = fileperms ( $inifile );
			@chmod ($inifile, 0766);
			$writable = is_writable($inifile);
		}

		if ($writable == false) {
			return $errmsg;
		}

		$f = fopen($inifile, 'wb');
		fwrite($f, '<?php die( \'Restricted access\' ); ?>'."\n;\n");
		fwrite($f, '; created by JEV_Config at '. date('r')."\n");
		fwrite($f, '; Please do not edit'."\n;\n");

		foreach ($this->toArray() as $key => $value) {
			fwrite($f, $key . '=' . preg_replace('/(\r)*\n/', '\n', $value) . "\n");
		}

		fclose($f);
		if ($oldperm) {
			@chmod ($inifile, $oldperm);
		}
		return true;
	}

	/**
	 * Returns a reference to a global JEV_Config object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @access public
	 * @return object  			The JEV_Config object.
	 * @since 1.4
	 */
	function &getInstance($inifile='') {

		static $instances;

		if (!$instances) {
			$instances = array();
		}

		if (!$inifile) {
			$inifile = JEVXConfig::_getDefaultINIfilePath();
		}

		if (!array_key_exists($inifile,$instances)) {
			$instances[$inifile] = new JEVXConfig(@file_get_contents($inifile));
			$instances[$inifile]->_inifile_path = $inifile;
		}
		return $instances[$inifile];
	}

}
