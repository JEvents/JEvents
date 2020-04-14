<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3543 2012-04-20 08:17:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * HTML View class for the component
 *
 * @static
 */
class CustomcssViewCustomcss extends JEventsAbstractView
{

	function display($cachable = false)
	{

		jimport('joomla.html.pane');

		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_JEVENTS') . ': ' . Text::_('JEV_CUSTOM_CSS'));

		$bar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(Text::_('COM_JEVENTS') . ': ' . Text::_('JEV_CUSTOM_CSS'), 'jevents');

		ToolbarHelper::apply('customcss.apply');
		ToolbarHelper::save('customcss.save');
		ToolbarHelper::cancel('customcss.cancel');
		ToolbarHelper::divider();


		//Check if the Customcss file already exists, if not load the .new version
		$filepath = JPATH_ROOT . '/components/com_jevents/assets/css/jevcustom.css';

		if (!File::exists($filepath))
		{
			//Whoops doesn't exist yet, lets add the .new to it.
			$filepath = JPATH_ROOT . $filepath . '.new';
		}

		$this->file = $filepath;
		$this->form = $this->get('Form');
		$this->form->setFieldAttribute('source', 'syntax', 'css');
		$this->source = $this->get('Customcss');
		$this->type   = 'file';



		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		return parent::display();

	}
}

