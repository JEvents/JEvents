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
			if (!isset( $this->form->jevdata[$this->name]["excats"])){
				 $this->form->jevdata[$this->name]["excats"] = false;
			}
			$selectSomeCategories = JText::_("JEV_SELECT_SOME_CATEGORIES", true);
			$input = JEventsHTML::buildCategorySelect($this->value, 'data-placeholder="'.$selectSomeCategories.'" ', $this->form->jevdata[$this->name]["dataModel"]->accessibleCategoryList(),
				$this->form->jevdata[$this->name]["with_unpublished_cat"], true, 0, 'catid', JEV_COM_COMPONENT, $this->form->jevdata[$this->name]["excats"], "ordering", true);
		}
		else {
			$input = "";
		}

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		return  $input;
	}
	
	protected function getLabel() {
		if ($this->form->jevdata[$this->name]["repeatId"]==0){
			return parent::getLabel();
		}
		return "";
	}

}