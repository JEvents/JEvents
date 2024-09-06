<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\Field\TextField;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JFormFieldJevtext extends TextField
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