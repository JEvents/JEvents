<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventpublished extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventpublished';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		if (JFactory::getApplication()->isAdmin() || JEVHelper::isEventPublisher())
		{
			$ev_id= $this->form->jevdata[$this->name]["ev_id"];
			
			if ($ev_id == 0)
			{
				// published by default	
				$this->value = 1;
			}
				$poptions = array();
				$poptions[] = JHTML::_('select.option', 0, JText::_("JUNPUBLISHED"));
				$poptions[] = JHTML::_('select.option', 1, JText::_("JPUBLISHED"));
			
				return JHTML::_('select.genericlist', $poptions, 'state', 'class="inputbox" size="1"', 'value', 'text', $this->value);
			
		}
		else {
			return '<input type="hidden" name="state" id="state" value="' . $this->value . '" />';	
		}		

	}

	protected function getLabel()
	{
		if (JFactory::getApplication()->isAdmin() || JEVHelper::isEventPublisher())
		{
			return parent::getLabel();
		}
		return "";

	}

}