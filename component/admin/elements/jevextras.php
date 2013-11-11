<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevextras.php 1196 2010-09-27 08:26:32Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport("joomla.html.parameter.element");

class JElementJevextras extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevextras';

	var $data = null;
	var $labeldata = null;


	/**
	 * Constructor
	 *
	 * @access protected
	 */
	function __construct($parent = null) {

		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		parent::__construct($parent	);
		$this->data = array();
		$this->labeldata = array();

	}


	function render(&$xmlElement, $value, $control_name = 'params')
	{
		JHTMLBehavior::modal();
		$name	= $xmlElement->attributes('name');
		$label	= $xmlElement->attributes('label');
		$description = $xmlElement->attributes('description');
		
		// load any custom fields
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin("jevents");
		$id = intval(str_replace("extras","",$name));
		$res = $dispatcher->trigger( 'onEditMenuItem' , array(&$this->data, &$value, $control_name, $name, $id, $this->_parent));
		
		//make sure we have a valid label
		$label = $label ? $label : $name;
		$result[0] = $this->fetchTooltip($label, $description, $xmlElement, $control_name, $name);
		$result[1] = $this->fetchElement($name, $value, $xmlElement, $control_name);
		$result[2] = $description;
		$result[3] = $label;
		$result[4] = $value;
		$result[5] = $name;

		return $result;
	}
	
	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='')
	{	
		$id = intval(str_replace("extras","",$name));
		if (array_key_exists($id,$this->data)){
			$item = $this->data[$id];
			$label = $item->label;
			$description = $item->description;
			$output = '<label id="'.$control_name.$name.'-lbl" for="'.$control_name.$name.'"';
			if ($description) {
				$output .= ' class="hasTip" title="'.JText::_($label).'::'.JText::_($description).'">';
			} else {
				$output .= '>';
			}
			$output .= JText::_( $label ).'</label>';

			return $output;
		}
		else return "";
	}

	function fetchElement($name, $value, &$node, $control_name)
	{
		// load any custom fields
		$dispatcher	= JDispatcher::getInstance();
		JPluginHelper::importPlugin("jevents");
		$id = intval(str_replace("extras","",$name));

		if (array_key_exists($id,$this->data)){
			$item = $this->data[$id];
			if (isset($item->html) && $item->html!="") return $item->html;
			return JHTML::_('select.radiolist', $item->options, ''.$control_name.'['.$name.']', '', 'value', 'text', $value, $control_name.$name );

		}
		else return "";


	}
}