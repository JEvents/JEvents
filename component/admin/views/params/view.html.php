<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 2692 2011-10-04 10:33:39Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2017 GWE Systems Ltd
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

	function edit()
	{

		$this->editor =  JFactory::getEditor();
		if ($this->editor->get("_name") == "codemirror")
		{
			$this->editor = JFactory::getEditor("none");
			JFactory::getApplication()->enqueueMessage(JText::_("JEV_CODEMIRROR_NOT_COMPATIBLE_EDITOR", "WARNING"));
		}

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JEVENTS_CONFIGURATION'));

		// Set toolbar items for the page
		JToolbarHelper::title(JText::_('COM_JEVENTS_CONFIGURATION'), 'jevents');

		JToolbarHelper::apply('params.apply');
		JToolbarHelper::save('params.save');
		JToolbarHelper::cancel('cpanel.cpanel');

		$model = $this->getModel();

		JHTML::_('behavior.tooltip');

		// Get the actions for the asset.
		$actions = JAccess::getActions(JEV_COM_COMPONENT, "component");

		jimport('joomla.form.form');

		// Add the search path for the admin component config.xml file.
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/' . JEV_COM_COMPONENT);

		// Get the form.
		$modelForm = $model->getForm();

		//$component = isset($this->component)?$this->component : $this->get('Component');
		
		$component = $this->get('Component');
		// Bind the form to the data.
		if ($modelForm && $component->params)
		{
			$modelForm->bind($component->params);
		}

		$this->assignRef("form", $modelForm);
		$this->assignRef("component", $component);

		// Set the layout
		$this->setLayout('edit');

	}

	function dbsetup($tpl = null)
	{
		
		JEVHelper::stylesheet( 'eventsadmin.css',  'components/'.JEV_COM_COMPONENT.'/assets/css/' );

		$document = JFactory::getDocument();
		$document->setTitle(JText::_( 'DB_SETUP' ));
		
		// Set toolbar items for the page
		JToolbarHelper::title( JText::_( 'DB_SETUP' ), 'jevents' );
		JToolbarHelper::cancel('cpanel.cpanel');
		JEventsHelper::addSubmenu();
	}	
}
