<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: rssmod.php 1272 2010-10-11 13:11:55Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
jimport("joomla.html.parameter.element");

class JElementRssmod extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'rssmod';
	
	function fetchElement($name, $value, &$node, $control_name)
	{

		// Must load admin language files
		$lang =& JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
		// get list of latest_events modules
		$modules = $this->dataModel->queryModel->getModulesByName("mod_jevents_latest");
		$seloptions = array();
		$seloptions[] = JHTML::_('select.option', 0, JTEXT::_('JEV_RSS_MODID_MAIN'));
		for ($i=0;$i<count($modules);$i++) {
			$seloptions[] = JHTML::_('select.option', $modules[$i]->id, $modules[$i]->title );
		}
		return JHTML::_('select.genericlist',  $seloptions, ''.$control_name.'['.$name.']', '', 'value', 'text', $value, $control_name.$name );

	}
}