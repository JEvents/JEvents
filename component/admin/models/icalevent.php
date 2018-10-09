<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Component\ComponentHelper;

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
	 * @param    array   $data     Data for the form.
	 * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return    mixed    A Form object on success, false on failure
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{

		// Prepare the data
		// Experiment in the use of Form and template override for forms and fields
		Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/");
		$template = Factory::getApplication()->getTemplate();
		Form::addFormPath(JPATH_THEMES . "/$template/html/com_jevents/forms");
		//Form::addFieldPath(JPATH_THEMES."/$template/html/com_jevents/fields");

		$xpath = false;
		// leave form control blank since we want the fields as ev_id and not Form[ev_id]
		$form = $this->loadForm("jevents.edit.icalevent", 'icalevent', array('control' => '', 'load_data' => false), false, $xpath);
		Form::addFieldPath(JPATH_THEMES . "/$template/html/com_jevents/fields");

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function getTranslateForm($data = array(), $loadData = true)
	{

		// Prepare the data
		// Experiment in the use of Form and template override for forms and fields
		Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR . "/models/forms/");
		$template = Factory::getApplication()->getTemplate();
		Form::addFormPath(JPATH_THEMES . "/$template/html/com_jevents/forms");

		$xpath = false;
		// leave form control blank since we want the fields as ev_id and not Form[ev_id]
		$form = $this->loadForm("jevents.translate.icalevent", 'translate', array('control' => '', 'load_data' => false), false, $xpath);
		Form::addFieldPath(JPATH_THEMES . "/$template/html/com_jevents/fields");

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function getOriginal()
	{

		$db = Factory::getDbo();
		$input  = Factory::getApplication()->input;

		$evdet_id = $input->getInt("evdet_id", 0);
		$db->setQuery("SELECT * FROM #__jevents_vevdetail where evdet_id = " . $evdet_id);
		$data = $db->loadAssoc();

		return $data;
	}

	public function getTranslation()
	{

		$db = Factory::getDbo();

		$input  = Factory::getApplication()->input;

		$evdet_id = $input->getInt("evdet_id", 0);
		$lang     = $input->getString("lang", "");
		$db->setQuery("SELECT * FROM #__jevents_translation where evdet_id = " . $evdet_id . " AND language = " . $db->quote($lang));
		$tempdata = $db->loadAssoc();
		$data     = array();
		if ($tempdata)
		{
			foreach ($tempdata as $key => $val)
			{
				$data["trans_" . $key] = $val;
			}
		}

		return $data;
	}

	public function saveTranslation()
	{

		$input  = Factory::getApplication()->input;

		$array = JEVHelper::arrayFiltered($input->getArray(array(), null, 'RAW'));

		// Should we allow raw content through unfiltered
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("allowraw", 0))
		{

			$array['trans_description'] = $input->post->get("trans_description", "", 'RAW');
			$array['trans_extra_info']  = $input->post->get("trans_extra_info", "", 'RAW');
		}

		include_once JPATH_COMPONENT . "/tables/translate.php";
		$translation = new TableTranslate();
		$success     = $translation->save($array);

		if ($success)
		{
			Factory::getApplication()->triggerEvent('onSaveTranslation', array($array), true);
		}

		return $success;
	}

	public function deleteTranslation()
	{

		$input  = Factory::getApplication()->input;

		include_once JPATH_COMPONENT . "/tables/translate.php";
		$translation = new TableTranslate();
		$translation->delete($input->getInt("trans_translation_id"));
	}

	function getLanguages()
	{

		static $languages;
		if (!isset($languages))
		{
			$db = Factory::getDbo();

			// get the list of languages first
			$query = $db->getQuery(true);
			$query->select("l.*");
			$query->from("#__languages as l");
			$query->where('l.lang_code <> "xx-XX"');
			$query->order("l.lang_code asc");

			$db->setQuery($query);
			$languages = $db->loadObjectList('lang_code');
		}

		return $languages;
	}

	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 * @since    3.0
	 */
	protected function preprocessForm(Form $form, $data, $group = 'jevents')
	{

		// Association content items
		$app   = Factory::getApplication();
		$assoc = false && JLanguageAssociations::isEnabled() && Factory::getApplication()->isClient('administrator');
		if ($assoc)
		{
			$languages = JLanguageHelper::getLanguages('lang_code');
			$addform   = new SimpleXMLElement('<form />');
			$fields    = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_JEVENTS_ITEM_ASSOCIATIONS_FIELDSET_DESC');
			$add = false;
			foreach ($languages as $tag => $language)
			{
				if (empty($data->language) || $tag != $data->language)
				{
					$add   = true;
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