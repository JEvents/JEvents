<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventcategory extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventcategory';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		if ($this->form->jevdata[$this->name]["repeatId"]==0){
			$input = JEventsHTML::buildCategorySelect($this->value, 'id="catid" ', $this->form->jevdata[$this->name]["dataModel"]->accessibleCategoryList(), 
				$this->form->jevdata[$this->name]["with_unpublished_cat"], true, 0, 'catid', JEV_COM_COMPONENT, $this->form->jevdata[$this->name]["excats"], "ordering", true);
		}
		else {
			$input = "";
		}
		return  $input;
	}
	
	protected function getLabel() {
		if ($this->getInput()){
			return parent::getLabel();
		}
		return "";
	}

}