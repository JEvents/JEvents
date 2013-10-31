<?php
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldJeveventtext extends JFormFieldText
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventtext';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$input = parent::getInput();
		if (strpos($input, "placeholder")===false){
			$placeholder = $this->element['placeholder'] ? ' placeholder="' . htmlspecialchars(JText::_($this->element['placeholder'])) . '"' : '';
			$input = str_replace("/>", " $placeholder />", $input);
		}

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		return $input;

	}

}