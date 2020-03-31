<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('color');

//include_once(JPATH_SITE . "/libraries/joomla/form/fields/color.php");
// J4 => include_once(JPATH_SITE . "/libraries/src/Form/Field/ColorField.php");

class FormFieldJevcolor extends JFormFieldColor
{
	protected function getLabel()
	{

		if ($input = $this->getInput())
		{
			return parent::getLabel();
		}

		return "";

	}

	protected function getInput()
	{

		$cfg = JEVConfig::getInstance();

		$hideColour = false;
		if (($cfg->get('com_calForceCatColorEventForm', 0) == 1) && (!Factory::getApplication()->isClient('administrator')))
		{
			$hideColour = true;
		}
		else if ($cfg->get('com_calForceCatColorEventForm', 0) == 2)
		{
			$hideColour = true;
		}
		else
		{
			$hideColour = false;
		}

		if (!$hideColour)
		{

			JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
			JEVHelper::ConditionalFields($this->element, $this->form->getName());

			$input = parent::getInput();

			// Unswitch the layouts that Joomla has applied!
			// $this->layout = $this->control === 'simple' ? $this->layout . '.simple' : $this->layout . '.advanced';
			$this->layout = str_replace(array(".simple", ".advanced"), "", $this->layout);

			return $input;
		}

		return "";
	}

}

class_alias("FormFieldJevcolor", "JFormFieldJevcolor");
