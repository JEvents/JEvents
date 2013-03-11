<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


include_once(JPATH_SITE."/libraries/joomla/form/fields/textarea.php");

class JFormFieldJevtextarea extends JFormFieldTextarea
{
	protected function getInput()
	{
		$this->value = str_replace('<br />', "\n", JText::_($this->value));
		return parent::getInput();
	}
	
}