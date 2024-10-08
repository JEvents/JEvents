<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;

use Joomla\CMS\Form\Field\TextareaField;

//include_once(JPATH_SITE . "/libraries/joomla/form/fields/textarea.php");
//J4 = include_once(JPATH_SITE . "/libraries/src/Form/Field/TextareaField.php");

#[\AllowDynamicProperties]
class FormFieldJevtextarea extends TextareaField
{
	protected function getInput()
	{
        if (is_null($this->value))
        {
            $this->value = "";
        }
		$this->value = str_replace('<br />', "\n", strpos($this->value, " ") > 0 ? $this->value : Text::_($this->value));

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput();
	}

}
class_alias("FormFieldJevtextarea", "JFormFieldJevtextarea");
