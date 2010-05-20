<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1399 2009-03-30 08:31:52Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementJevtimezone extends JElementText
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevtimezone';

	function fetchElement($name, $value, &$node, $control_name)
	{
		if (class_exists("DateTimeZone")){
			$zones = DateTimeZone::listIdentifiers();
			static $options;
			if (!isset($options)){
				$options = array();
				$options[]	= JHTML::_('select.option', '', '- '.JText::_('SELECT TIMEZONE').' -');
				foreach ($zones as $zone) {
					if (strpos($zone,"/")===false) continue;
					if (strpos($zone,"Etc")===0) continue;
					$options[]	= JHTML::_('select.option', $zone, $zone);
				}
			}
			return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
		}
		else {
			$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );
			$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
			/*
			* Required to avoid a cycle of encoding &
			* html_entity_decode was used in place of htmlspecialchars_decode because
			* htmlspecialchars_decode is not compatible with PHP 4
			*/
			$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

			return '<input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';
		}
	}


}