<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.modeladmin');


class IcaleventsModelicalevent extends JModelAdmin
{
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();
	}

		/**
	 * Method to get a form object.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Prepare the data
		// Experiment in the use of JForm and template override for forms and fields
		JForm::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/");
		$template = JFactory::getApplication()->getTemplate();
		JForm::addFormPath(JPATH_THEMES."/$template/html/com_jevents/forms");
		//JForm::addFieldPath(JPATH_THEMES."/$template/html/com_jevents/fields");

		$xpath = false;
		// leave form control blank since we want the fields as ev_id and not jform[ev_id]
		$form = $this->loadForm("jevents.edit.icalevent", 'icalevent', array('control' => '', 'load_data' => false), false, $xpath);
		JForm::addFieldPath(JPATH_THEMES."/$template/html/com_jevents/fields");
		
		if (empty($form)) {
			return false;
		}

		return $form;
	}


		/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 * @since    3.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'jevents')
	{
		// Association content items
		$app = JFactory::getApplication();
		$assoc = false &&  JLanguageAssociations::isEnabled() && JFactory::getApplication()->isAdmin();
		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');
			$addform = new SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_JEVENTS_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add = true;
					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'modal_article');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');
					$field->addAttribute('edit', 'true');
					$field->addAttribute('clear', 'true');
				}
			}
			if ($add)
			{
				$form->load($addform, false);
			}
		}

		parent::preprocessForm($form, $data, $group);
	}

}