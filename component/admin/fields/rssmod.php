<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: rssmod.php 1272 2010-10-11 13:11:55Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldRssmod extends JFormFieldList
{

	protected $type = 'jevview';

	public function getOptions()
	{

		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
		// get list of latest_events modules
		$modules = $this->dataModel->queryModel->getModulesByName("mod_jevents_latest");
		$seloptions = array();
		$seloptions[] = JHTML::_('select.option', 0, JTEXT::_('JEV_RSS_MODID_MAIN'));
		for ($i=0;$i<count($modules);$i++) {
			$seloptions[] = JHTML::_('select.option', $modules[$i]->id, $modules[$i]->title );
		}
		return $seloptions;

	}
	
}