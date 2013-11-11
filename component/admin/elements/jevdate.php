<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevdate.php 1240 2010-10-05 15:06:25Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport("joomla.html.parameter.element");

class JElementJevdate extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'JEVDate';

	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);
		$lang->load("com_jevents", JPATH_SITE);

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		$option = "com_jevents"; 
		$params = JComponentHelper::getParams( $option );
		$minyear = JEVHelper::getMinYear();
		$maxyear = JEVHelper::getMaxYear();
		ob_start();
		JEVHelper::loadCalendar($control_name.'['.$name.']', $control_name.$name, $value,$minyear, $maxyear, '',"", 'Y-m-d');
		return ob_get_clean();
	}
}