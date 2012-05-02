<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevmultilist.php 2989 2011-11-10 14:19:26Z geraintedwards $
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

		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );

		$class .= " multiple='multiple' ";
		$options = array ();
		$size = 0;
		foreach ($node->children() as $option)
		{
			$val	= $option->attributes('value');
			$text	= $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
			$size ++; 
		}
		$class .= " size='$size'";

		$html  = JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]', $class, 'value', 'text', $value, $control_name.$name);		
		$html = str_replace("<select ", "<select multiple='multiple' ", $html);
		return $html;
	}

}