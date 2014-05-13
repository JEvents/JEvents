<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevunlock.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementJevunlock extends JElementText
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevunlock';

	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$params = $this->_parent;
		$showcopy = $params->get("com_copyright",0);
		$style=' class="jev_none" ';
		if (!$showcopy){
			$style=' class="jev_block" ';
		}
		return '<div '.$style.'>'.JText::_("Please visit the <a href='http://www.jevents.net/hidecopyright?tmpl=component&template=beez' rel=”nofollow” title='get hide copyright code' class='modal' rel='{handler: \"iframe\", size: {x: 650, y: 450}}'>JEvents website</a> for your free code to hide the copyright message")."<br/>".parent::fetchElement($name, $value, $node, $control_name).'</div>';
	}
	
	function fetchTooltip($label, $description, &$xmlElement, $control_name='', $name='') {
		$params = $this->_parent;
		$showcopy = $params->get("com_copyright",0);
		$style=' class="jev_none" ';
		if (!$showcopy){
			$style=' class="jev_block" ';
		}
		return '<div '.$style.'>'.parent::fetchTooltip($label, $description, $xmlElement, $control_name, $name).'</div>';

	}


}