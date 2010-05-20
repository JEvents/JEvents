<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: config.php 1399 2009-03-30 08:31:52Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * convenience wrapper for config - to ensure backwards compatability
 */
class JEVConfig extends JParameter
{
	function &getInstance($inifile='') {
		return JComponentHelper::getParams("com_jevents");
	}

}
