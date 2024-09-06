<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 2749 2011-10-13 08:54:34Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminIcalrepeatViewIcalrepeat extends JEventsAbstractView
{

	function overview($tpl = null)
	{

		$document = Factory::getDocument();
		$document->setTitle(Text::_('ICAL_EVENT_REPEATS'));

		// Set toolbar items for the page
		ToolbarHelper::title(Text::_('ICAL_EVENT_REPEATS'), 'jevents');

		ToolbarHelper::addNew('icalrepeat.addRepeat', 'Add repeat');
		ToolbarHelper::editList('icalrepeat.edit');
		ToolbarHelper::deleteList('Delete this repeat?', 'icalrepeat.delete');
		ToolbarHelper::cancel('icalevent.list');

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// This is actually HIDE PAST so switch boolean values
		$showpast = intval($this->getModel()->getState("filter.showpast", 0));
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '0', Text::_('JEV_NO'));
		$options[] = HTMLHelper::_('select.option', '1', Text::_('JEV_YES'));

		$this->filters =
			array('showpast' =>
				       Text::_('JEV_SHOW_PAST') . " " .  HTMLHelper::_('select.genericlist', $options, 'filter[showpast]', 'class="gsl-select"  onchange="document.adminForm.submit();"', 'value', 'text', $showpast)
			);

	}

	function edit($tpl = null)
	{

		$document    = Factory::getDocument();
		$editStrings = '';

		include(JEV_ADMINLIBS . "editStrings.php");

		$document->addScriptDeclaration($editStrings);

		JEVHelper::script('editicalJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		if (!GSLMSIE10)
		{
			JEVHelper::script('editicalGSL.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		}
		JEVHelper::script('view_detailJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		JEVHelper::script('JevStdRequiredFieldsJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');

		$document->setTitle(Text::_('EDIT_ICAL_REPEAT'));

		// Set toolbar items for the page
		ToolbarHelper::title(Text::_('EDIT_ICAL_REPEAT'), 'jevents');

		ToolbarHelper::apply('icalrepeat.apply', "JEV_SAVE");
		ToolbarHelper::apply('icalrepeat.save', "JEV_SAVE_CLOSE");


		ToolbarHelper::cancel('icalrepeat.list');
		//ToolbarHelper::help( 'screen.icalrepeat.edit', true);

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->get("section",0);



		// load Joomla javascript classes
		HTMLHelper::_('behavior.core');
		$this->setLayout("edit");

		$this->setupEditForm();

	}



}
