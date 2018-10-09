<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


include_once(JPATH_SITE . "/libraries/joomla/form/fields/checkboxes.php");
// J4 = include_once(JPATH_SITE . "/libraries/src/Form/Field/CheckboxField.php");

class JFormFieldJevcheckboxes extends JFormFieldCheckboxes
{
	protected function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput();
	}

}