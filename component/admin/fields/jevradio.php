<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


use Joomla\CMS\Form\Field\RadioField;

if(file_exists(JPATH_SITE . "/libraries/joomla/form/fields/radio.php")) {
    include_once(JPATH_SITE . "/libraries/joomla/form/fields/radio.php");
}

class JFormFieldJevradio extends JFormFieldRadio
{
	protected function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput();
	}

}