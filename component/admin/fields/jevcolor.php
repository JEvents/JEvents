<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


include_once(JPATH_SITE."/libraries/joomla/form/fields/color.php");

class JFormFieldJevcolor extends JFormFieldColor
{
	protected function getInput()
	{
		$cfg = JEVConfig::getInstance();

		$hideColour = false;
		if (($cfg->get('com_calForceCatColorEventForm', 0) == 1) && (!JFactory::getApplication()->isAdmin()))
		{
			$hideColour = true;
		}
		else if ($cfg->get('com_calForceCatColorEventForm', 0) == 2)
		{
			$hideColour = true;
		}
		else {
			$hideColour = false;
		}

		if (!$hideColour)
		{

			return parent::getInput();
		}
		return "";
	}

	protected function getLabel()
	{
		if ($this->getInput())
		{
			return parent::getLabel();
		}
		return "";

	}

}
