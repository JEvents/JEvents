<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 2692 2011-10-04 10:33:39Z geraintedwards $
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

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminParamsViewParams extends JEventsAbstractView
{

	function edit()
	{
		$uEditor    = Factory::getUser()->getParam('editor',  Factory::getConfig()->get('editor', 'none'));

		$this->editor = \Joomla\CMS\Editor\Editor::getInstance($uEditor);

		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_JEVENTS_CONFIGURATION'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('COM_JEVENTS_CONFIGURATION'), 'jevents');

		JToolbarHelper::apply('params.apply');
		JToolbarHelper::save('params.save');
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$landingpage = $params->get("landingpage", 'cpanel.cpanel');
		JToolbarHelper::cancel($landingpage);

		$model = $this->getModel();

		jimport('joomla.form.form');

		// Add the search path for the admin component config.xml file.
		\Joomla\CMS\Form\Form::addFormPath(JPATH_ADMINISTRATOR . '/components/' . JEV_COM_COMPONENT);

		// Get the form.
		// Some plugins called by the form can wreck the layout by outputting things here like &#65279; !!
		ob_start();
		$modelForm = $model->getForm();
		$junk = ob_get_clean();

		$component = $this->get('Component');
		// Bind the form to the data.
		if ($modelForm && $component->params)
		{
			$modelForm->bind($component->params);
		}

		$this->form         = $modelForm;
		$this->component    = $component;

		// Set the layout
		$this->setLayout('edit');

	}

	function dbsetup($tpl = null)
	{

		JEVHelper::stylesheet('eventsadmin.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');

		$document = Factory::getDocument();
		$document->setTitle(Text::_('DB_SETUP'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('DB_SETUP'), 'jevents');
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$landingpage = $params->get("landingpage", 'cpanel.cpanel');
		JToolbarHelper::cancel($landingpage);

	}
}
