<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport("joomla.html.parameter.element");
jimport("joomla.html.parameter.element/list");

class JElementJevmultilist extends JElementList
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevmultilist';
	
	function fetchElement($name, $value, &$node, $control_name, $raw = false)
	{
		if (is_string($value)){
			$value = explode(",",$value);
		}
		return parent::fetchElement($name, $value, &$node, $control_name, $raw);
	}

}