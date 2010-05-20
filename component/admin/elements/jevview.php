<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevview.php 1569 2009-09-16 06:22:03Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

include_once(dirname(__FILE__)."/../jevents.defines.php");

class JElementJevview extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'jevview';

	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang =& JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);
		
		$views = array();
		foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile) {
			$views[] = JHTML::_('select.option', $viewfile, $viewfile);
		}
		sort( $views );
		return JHTML::_('select.genericlist',  $views, ''.$control_name.'['.$name.']', '', 'value', 'text', $value, $control_name.$name );

	}
}