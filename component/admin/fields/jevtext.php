<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

use Joomla\CMS\Language\Text;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JLoader::register('JFormFieldText', JPATH_SITE . '/libraries/joomla/form/fields/text.php');
//J4 = JLoader::register('JFormFieldText', JPATH_SITE . '/libraries/src/Form/Field/TextField.php');

class JFormFieldJevtext extends JFormFieldText
{
	protected function getInput()
	{

		if (Text::_($this->value) !== $this->value && strtoupper($this->value) === $this->value  && strlen($this->value) > 0 && $this->default === $this->value)
		{
			$this->value = Text::_($this->value);
		}
		if (Text::_($this->default) !== $this->default && strtoupper($this->default) === $this->default  && strlen($this->default) > 0)
		{
			$this->element['default'] = $this->default = Text::_($this->default);
		}
		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput();
	}

}