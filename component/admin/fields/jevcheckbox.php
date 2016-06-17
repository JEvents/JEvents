<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


include_once(JPATH_SITE."/libraries/joomla/form/fields/checkbox.php");

class JFormFieldJevcheckbox extends JFormFieldCheckbox
{
	protected function getInput()
	{
		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		$input =  parent::getInput();
		return $input;
	}
	
}