<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


use Joomla\CMS\Form\Field\CheckboxField;

class JFormFieldJevcheckbox extends CheckboxField
{
	protected function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		$input = parent::getInput();

		return $input;
	}

}