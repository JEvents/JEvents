<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventpriorities extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventpriorities';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{	
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$showpriority = $params->get("showpriority", 0);

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		// only those who can publish globally can set priority field
		if ($showpriority && JEVHelper::isEventPublisher(true))
		{
			$list = array();
			for ($i = 0; $i < 10; $i++)
			{
				$list[] = JHTML::_('select.option', $i, $i, 'val', 'text');
			}
			return  JHTML::_('select.genericlist', $list, 'priority', "style='width:50px'", 'val', 'text', $this->value);
		}
		else {
			return "";
		}

	}

}