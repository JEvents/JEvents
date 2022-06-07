<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevcategory.php 1987 2011-04-28 09:53:46Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.form.formfield');

class JFormFieldJevlist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Jevlist';

	protected function getInput()
	{

		if (is_string($this->value))
		{
			$this->value = explode(",", $this->value);
		}

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		return parent::getInput();
	}


	protected function getOptions()
	{
		$options = parent::getOptions();
		$skip = array();
		foreach ($this->element->xpath('option') as $option)
		{
			$maxJoomlaVersion = (string) $option['maxjoomlaversion'];
			$minJoomlaVersion = (string) $option['minjoomlaversion'];
			if (!empty($maxJoomlaVersion) && version_compare(JVERSION, $maxJoomlaVersion, "gt"))
			{
				$skip[] = (string) $option['value'];
			}
			if (!empty($minJoomlaVersion) && version_compare(JVERSION, $minJoomlaVersion, "lt"))
			{
				$skip[] = (string) $option['value'];
			}
			if (!empty($skip))
			{
				foreach ($options as $i => $option)
				{
					if(isset($option->value) && in_array($option->value, $skip))
					{
						unset($options[$i]);
					}
				}
				$options = array_values($options);
			}
		}
		return $options;
	}
}
