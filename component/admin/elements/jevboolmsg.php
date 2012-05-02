<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolmsg.php 2256 2011-06-29 08:29:20Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport("joomla.html.parameter.element");

class JElementJevboolmsg extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevboolmsg';
	
	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang =& JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		if ($value=="") {
			$value = 0;
		}
		$value = intval($value);

		$style=' style="display:none" ';
		// if not showing copyright show message
		if (!$value){
			$style=' style="display:block" ';
		}
		
		$options = array ();
		$options[] = JHTML::_('select.option', 0, JText::_( 'JEV_NO' ));
		$options[] = JHTML::_('select.option', 1, JText::_( 'JEV_YES' ));

		$radio =  JHTML::_('select.radiolist', $options, ''.$control_name.'['.$name.']', ' onclick="if(this.value==0) document.getElementById(\'jevcopymsg\').style.display=\'block\';  else document.getElementById(\'jevcopymsg\').style.display=\'none\';" ', 'value', 'text', $value, $control_name.$name );
		
		return $radio.'<div id="jevcopymsg" '.$style.'>'.JText::_("Before removing the copyright footer please read this important message at the <a href='http://www.jevents.net/hidecopyright?tmpl=component&template=beez' title='get hide copyright code' class='modal' rel='{handler: \"iframe\", size: {x: 650, y: 450}}'>JEvents website</a>.").'</div>';
	}
}