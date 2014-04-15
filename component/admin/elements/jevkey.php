<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevkey.php 1196 2010-09-27 08:26:32Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport("joomla.html.parameter.element");

class JElementJevkey extends JElementText
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevkey';

	function fetchElement($name, $value, &$node, $control_name)
	{
		if ($value=="") {
			$value = uniqid("jevkey",1);
		}

		$params = $this->_parent;
		$showcopy = $params->get("com_copyright",0);
		$style=' class="jev_none" ';
		if (!$showcopy){
			$style=' class="jev_block" ';
		}
		return '<div '.$style.'><input type="hidden" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" />'.$value.'</div>';
	}
	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='') {

		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$params = $this->_parent;
		$showcopy = $params->get("com_copyright",0);
		$style=' class="jev_none" ';
		if (!$showcopy){
			$style=' class="jev_block" ';
		}
		return '<div '.$style.'>'.parent::fetchTooltip($label, $description, $xmlElement, $control_name, $name).'</div>';

	}


}