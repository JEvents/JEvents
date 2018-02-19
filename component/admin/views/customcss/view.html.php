<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3543 2012-04-20 08:17:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2018 GWE Systems Ltd
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

use Joomla\String\StringHelper;

class CustomcssViewCustomcss extends JViewLegacy {

	function display($cachable = false)
	{
		jimport('joomla.html.pane');

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JEVENTS') . ': ' . JText::_('JEV_CUSTOM_CSS'));

		$bar = JToolbar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_JEVENTS') . ': ' . JText::_('JEV_CUSTOM_CSS'), 'jevents');

		JToolbarHelper::apply('customcss.apply');
		JToolbarHelper::save('customcss.save');
		JToolbarHelper::cancel('customcss.cancel');
		JToolbarHelper::divider();


		//Check if the Customcss file already exists, if not load the .new version
		$filepath = JPATH_ROOT . '/components/com_jevents/assets/css/jevcustom.css';

		if (!JFile::exists($filepath))
		{
			//Whoops doesn't exist yet, lets add the .new to it.
			$filepath = JPATH_ROOT . $filepath . '.new';
		}

		$this->file     = $filepath;
		$this->form     = $this->get('Form');
		$this->form->setFieldAttribute('source', 'syntax', 'css');
		$this->source   = $this->get('Customcss');
		$this->type     = 'file';

		JEventsHelper::addSubmenu();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		return parent::display();

	}
}

