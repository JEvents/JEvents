<?php

defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class FormFieldJeveventpriorities extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Jeveventpriorities';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{

		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$showpriority   = $params->get("showpriority", 0);
		$showPriorityTo = (int) $params->get('showPriorityACL', 0);

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		$isAuth = JEVHelper::isEventPublisher(true);
		if ($showPriorityTo === 1)
		{
			$isAuth = JEVHelper::isEventCreator(true);
		}
		else if ($showPriorityTo === 2)
		{
			$isAuth = JEVHelper::isEventEditor();
		}

		// only those who can publish globally can set priority field
		if ($showpriority && $isAuth)
		{
			$list = array();
			for ($i = 0; $i < 10; $i++)
			{
				$list[] = HTMLHelper::_('select.option', $i, $i, 'val', 'text');
			}

			return HTMLHelper::_('select.genericlist', $list, 'priority', "style='width:50px'", 'val', 'text', $this->value);
		}
		else
		{
			return "";
		}

	}

}
class_alias("FormFieldJeveventpriorities", "JFormFieldJeveventpriorities");
