<?php
/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevview.php 3493 2012-04-08 09:41:27Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldJevview extends ListField
{

	protected $type = 'jevview';

	public function getOptions()
	{

		$input = Factory::getApplication()->input;

		// Must load admin language files
		$lang = Factory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$views = array();
		include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");

		$exceptions_values = (string) $this->element['except'] ? (string) $this->element['except'] : "";
		$exceptions        = array();
		$exceptions        = explode(',', $exceptions_values);

		foreach (JEV_CommonFunctions::getJEventsViewList((string) $this->element["viewtype"]) as $viewfile)
		{
			if (in_array($viewfile, $exceptions))
			{
				continue;
			}
			$views[] = HTMLHelper::_('select.option', $viewfile, $viewfile);
		}
		sort($views);
		if ($this->menu != 'hide')
		{
			$task = $input->get('task');
			if ($task == "params.edit")
			{
				unset($views['global']);
			}
			else
			{
				array_unshift($views, HTMLHelper::_('select.option', 'global', Text::_('USE_GLOBAL')));
			}
		}

		return $views;

	}

	protected
	function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput();
	}
}
