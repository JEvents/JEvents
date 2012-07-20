<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 2692 2011-10-04 10:33:39Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminParamsViewParams extends JEventsAbstractView
{

	function edit16()
	{
		if (JVersion::isCompatible("1.6.0"))
		{
			JEVHelper::stylesheet('eventsadmin16.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');
		}
		return $this->edit();

	}

	function edit()
	{
		JEVHelper::stylesheet('eventsadmin.css', 'administrator/components/' . JEV_COM_COMPONENT . '/assets/css/');

		$document = & JFactory::getDocument();
		$document->setTitle(JText::_('COM_JEVENTS_CONFIGURATION'));

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JEVENTS_CONFIGURATION'), 'jevents');

		JToolBarHelper::apply('params.apply'); 
		JToolBarHelper::save('params.save');
		JToolBarHelper::cancel('cpanel.cpanel');

		$model = $this->getModel();

		JHTML::_('behavior.tooltip');

		// Get the actions for the asset.
		$actions = JAccess::getActions(JEV_COM_COMPONENT, "component");

		jimport('joomla.form.form');

		// Add the search path for the admin component config.xml file.
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/' . JEV_COM_COMPONENT);

		// Get the form.
		$modelForm = $model->getForm();
		
		$component	= $this->get('Component');
		// Bind the form to the data.
		if ($modelForm && $component->params) {
			$modelForm->bind($component->params);
		}		

		$this->assignRef("form", $modelForm);
		$this->assignRef("component", $component);

	}

}