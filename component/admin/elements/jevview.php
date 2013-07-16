<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevview.php 2256 2011-06-29 08:29:20Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport("joomla.html.parameter.element");

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
		include_once(JPATH_ADMINISTRATOR."/components/com_jevents/jevents.defines.php");

		foreach (JEV_CommonFunctions::getJEventsViewList() as $viewfile) {
			$views[] = JHTML::_('select.option', $viewfile, $viewfile);
			$load = $lang->load("com_jevents", JPATH_SITE."/components/com_jevents/views/".$viewfile."/assets");
		}
		sort( $views );
		if ($node->attributes('menu')!='hide'){
			array_unshift($views , JHTML::_('select.option', '', JText::_( 'USE_GLOBAL' )));
		}
		return JHTML::_('select.genericlist',  $views, ''.$control_name.'['.$name.']', '', 'value', 'text', $value, $control_name.$name );

	}
}


