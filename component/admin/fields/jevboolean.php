<?php

/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevboolean.php 1331 2010-10-19 12:35:49Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('radio');

/**
 * JEVMenu Field class for the JEvents Component
 *
 * @package		JEvents.fields
 * @subpackage	com_banners
 * @since		1.6
 */
class JFormFieldJEVBoolean extends JFormFieldRadio
{

	/**
	 * The form field type.s
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected
			$type = 'JEVBoolean';

	protected
			function getInput()
	{
		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());
		$params = JComponentHelper::getParams("com_jevents");
		$value = (int) $this->value;
		if ($value==-1){
			if (version_compare(JVERSION, '3.0.0', "<")){
				$default25 = (string)$this->element["default25"];
				if ($default25!=""){
					$this->value = $this->default = intval($default25);
				}
			}
			else if (version_compare(JVERSION, '3.0.0', ">=")){
				$default30 = (string)$this->element["default30"];
				if ($default30!=""){
					$this->value = $this->default = intval($default30);
				}
			}
		}
		if (!$params->get("bootstrapchosen", 1))
		{
			$x = 1;
		}
		return parent::getInput();

	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public
			function getOptions()
	{
		// Must load admin language files
		$lang = JFactory::getLanguage();
		$lang->load("com_jevents", JPATH_ADMINISTRATOR);

		$options = array();
		$options[] = JHTML::_('select.option', 0, JText::_("Jev_No"));
		$options[] = JHTML::_('select.option', 1, JText::_("jev_Yes"));

		return $options;

	}


	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	/*
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		return parent::setup($element, $value, $group);
	}
	 */
}
