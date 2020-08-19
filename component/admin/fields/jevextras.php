<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevextras.php 1785 2011-03-14 14:28:17Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");

class FormFieldJevextras extends FormField
{

	/**
	 * The form field type.s
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected
		$type = 'JEVExtras';
	protected
		$extra = null;
	protected
		$data = null;
	protected
		$labeldata = null;

	function __construct($form = null)
	{

		// Must load admin language files
		$lang = Factory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		parent::__construct($form);
		$this->data      = array();
		$this->labeldata = array();

		// Some plugins use JSON.encode (a MooTools alias for JSON.stringify) so we need to define it here
        if (version_compare(JVERSION,'3.999.999',"<")) {
            HTMLHelper::_('behavior.framework', true);
            $script = <<< SCRIPT
JSON.encode = function(obj){
    return JSON.stringify(obj);
};
SCRIPT;
//        JFactory::getDocument()->addScriptDeclaration($script);
        }
	}

	public
	function setup(SimpleXMLElement $element, $value, $group = null)
	{

		$success = parent:: setup($element, $value, $group);
		if (!$success)
		{
			return false;
		}

		//echo var_export($this->form);die();
		return true;

		// load any custom fields
		PluginHelper::importPlugin("jevents");
		$id  = intval(str_replace("extras", "", $this->name));
		$res = Factory::getApplication()->triggerEvent('onEditMenuItem', array(&$this->data, &$this->value, $this->type, $this->name, $this->id, $this->form));

		return true;

	}

	protected
	function getLabel()
	{

		// load any custom fields
		PluginHelper::importPlugin("jevents");
		$id = $this->id;
		if (version_compare(JVERSION, '3.3.0', '<'))
		{
			$res = Factory::getApplication()->triggerEvent('onEditMenuItem', array(&$this->data, &$this->value, $this->type, $this->name, $this->id, $this->form));
		}
		if (isset($this->data[$id]))
		{
			$this->element['label'] = $this->data[$id]->label;
			$this->description      = $this->data[$id]->description;
		}
		else
		{
			$this->element['label'] = "";
			$this->description      = "";
		}

		return parent::getLabel();

	}

	protected
	function getInput()
	{

		// load any custom fields
		PluginHelper::importPlugin("jevents");
		$id = $this->id;

		if (version_compare(JVERSION, '3.3.0', '>='))
		{
			$res = Factory::getApplication()->triggerEvent('onEditMenuItem', array(&$this->data, &$this->value, $this->type, $this->name, $this->id, $this->form));
		}

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		if (array_key_exists($id, $this->data))
		{
			$item = $this->data[$id];
			if (isset($item->html) && $item->html != "")
				return $item->html;
		}
		else
			return "";

	}

	private
	function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
	{

		$id = intval(str_replace("extras", "", $name));
		if (array_key_exists($id, $this->data))
		{
			$item        = $this->data[$id];
			$label       = $item->label;
			$description = $item->description;
			$output      = '<label id="' . $control_name . $name . '-lbl" for="' . $control_name . $name . '"';
			if ($description)
			{
				$output .= ' class="hasTip" title="' . Text::_($label) . '::' . Text::_($description) . '">';
			}
			else
			{
				$output .= '>';
			}
			$output .= Text::_($label) . '</label>';

			return $output;
		}
		else
			return "";

	}

}

class_alias("FormFieldJevextras", "JFormFieldJevextras");
