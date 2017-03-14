<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3543 2012-04-20 08:17:42Z geraintedwards $
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

use Joomla\String\StringHelper;

class CustomcssViewCustomcss extends JViewLegacy {

	function display($cachable = false)
	{
		jimport('joomla.html.pane');

		$document = JFactory::getDocument();
		$document->setTitle(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'));

		JToolBarHelper::title(JText::_('JEVENTS') . ' :: ' . JText::_('JEVENTS'), 'jevents');

		JToolBarHelper::apply('customcss');
		JToolBarHelper::cancel('customcss');

		//   jimport('joomla.form.form');
		//Setup the file:
		$app            = JFactory::getApplication();
		$file           = 'jevcustom.css';
		$srcfile        = 'jevcustom.css.new';
		$filepath       = JPATH_ROOT . '/components/com_jevents/assets/css/' . $file;
		$srcfilepath    = JPATH_ROOT . '/components/com_jevents/assets/css/' . $srcfile;

		if (!JFile::exists($filepath))
		{
			Jfile::copy($srcfilepath, $filepath);
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

