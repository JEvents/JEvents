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
                $row = $this->form->jevdata[$this->name]["event"];
                $glist = JEventsHTML::buildAccessSelect(intval($row->access()), 'class="inputbox" size="1"');
                
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
                $row = $this->form->jevdata[$this->name]["event"];
                $glist = JEventsHTML::buildAccessSelect(intval($row->access()), 'class="inputbox" size="1"');
                
		if ($this->getInput() && $glist && strpos($glist, "<input ")===false)
		{
			return parent::getLabel();
		}
		return "";
	}

}