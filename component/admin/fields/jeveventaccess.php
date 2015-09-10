<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventaccess extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventaccess';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$glist = $this->form->jevdata[$this->name]["glist"];

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		if ($glist)
		{
			return $glist;
		}
		return "";

	}

	protected function getLabel()
	{
		$glist = $this->form->jevdata[$this->name]["glist"];
		if ($this->getInput() && $glist && strpos($glist, "<input ")===false)
		{
			return parent::getLabel();
		}
		return "";
	}

}