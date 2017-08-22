<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevextras.php 1785 2011-03-14 14:28:17Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');

include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");

class JFormFieldJevextras extends JFormField
{

	/**
	 * The form field type.s
	 *
	 * @var		string
	 * @since	1.6
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
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		parent::__construct($form);
		$this->data = array();
		$this->labeldata = array();

	}

	protected
			function getLabel()
	{

		// load any custom fields
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin("jevents");
		$id = $this->id;
		if (version_compare(JVERSION, '3.3.0', '<')){
			$res = $dispatcher->trigger('onEditMenuItem', array(&$this->data, &$this->value, $this->type, $this->name, $this->id, $this->form));
		}
		if (isset($this->data[$id]))
		{
			$this->element['label'] = $this->data[$id]->label;
			$this->description = $this->data[$id]->description;
		}
		else
		{
			$this->element['label'] = "";
			$this->description = "";
		}
		return parent::getLabel();

	}

	protected
			function getInput()
	{
		// load any custom fields
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin("jevents");
		$id = $this->id;

		if (version_compare(JVERSION, '3.3.0', '>=')){
			$res = $dispatcher->trigger('onEditMenuItem', array(&$this->data, &$this->value, $this->type, $this->name, $this->id, $this->form));
		}

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		if (array_key_exists($id, $this->data))
		{
			$item = $this->data[$id];
			if (isset($item->html) && $item->html != "")
				return $item->html;
		}
		else
			return "";

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
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin("jevents");
		$id = intval(str_replace("extras", "", $this->name));
		$res = $dispatcher->trigger('onEditMenuItem', array(&$this->data, &$this->value, $this->type, $this->name, $this->id, $this->form));

		return true;

	}

	private
			function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
	{
		$id = intval(str_replace("extras", "", $name));
		if (array_key_exists($id, $this->data))
		{
			$item = $this->data[$id];
			$label = $item->label;
			$description = $item->description;
			$output = '<label id="' . $control_name . $name . '-lbl" for="' . $control_name . $name . '"';
			if ($description)
			{
				$output .= ' class="hasTip" title="' . JText::_($label) . '::' . JText::_($description) . '">';
			}
			else
			{
				$output .= '>';
			}
			$output .= JText::_($label) . '</label>';

			return $output;
		}
		else
			return "";

	}

}