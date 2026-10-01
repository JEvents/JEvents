<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;


defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class FormFieldJeveventcategory extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Jeveventcategory';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{
      //  $jevdatamap = isset($_SESSION['jevdatamap']) ? $_SESSION['jevdatamap'] : new WeakMap();

       // $jevdata = $jevdatamap[$this->form];

		if (!isset(JEventsHelper::getJevData($this->form)[$this->name]["excats"]))
		{
			JEventsHelper::getJevData($this->form)[$this->name]["excats"] = false;
		}
		$selectSomeCategories = Text::_("JEV_SELECT_SOME_CATEGORIES", true);
		$input                = JEventsHTML::buildCategorySelect($this->value, 'data-placeholder="' . $selectSomeCategories . '" ', JEventsHelper::getJevData($this->form)[$this->name]["dataModel"]->accessibleCategoryList(),
		JEventsHelper::getJevData($this->form)[$this->name]["with_unpublished_cat"], true, 0, 'catid', JEV_COM_COMPONENT, JEventsHelper::getJevData($this->form)[$this->name]["excats"], "ordering", true);

		if (JEventsHelper::getJevData($this->form)[$this->name]["repeatId"] !== 0)
		{
			return $input;
		}

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());
		return $input;
	}

	protected function getLabel()
	{

		if (JEventsHelper::getJevData($this->form)[$this->name]["repeatId"] == 0)
		{
			return parent::getLabel();
		}

		return "";
	}

}

class_alias("FormFieldJeveventcategory", "JFormFieldJeveventcategory");
