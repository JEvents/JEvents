<?php
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Component\ComponentHelper;

FormHelper::loadFieldClass('text');

class FormFieldJeveventtext extends JFormFieldText
{

	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Jeveventtext';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{

		$input = parent::getInput();
		if (strpos($input, "placeholder") === false)
		{
			$placeholder = $this->element['placeholder'] ? (string) $this->element['placeholder'] : '';
			if ($this->name === "title" && $placeholder === "JEV_EVENT_TITLE_PLACEHOLDER")
			{
				$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
				$placeholder = $params->get("titleplaceholder", $placeholder);

				$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
				if (!$params->get("enableshowon", 0))
				{
					$placeholder = "JEV_EVENT_TITLE";
				}
			}
			$placeholder = !empty($placeholder) ? ' placeholder="' . htmlspecialchars(Text::_($placeholder)) . '"' : '';
			$input       = str_replace("<input", "<input $placeholder ", $input);
		}

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return $input;

	}

}
class_alias("FormFieldJeveventtext", "JFormFieldJeveventtext");
