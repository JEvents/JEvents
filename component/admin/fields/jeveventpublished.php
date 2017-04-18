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
		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());
		if (JFactory::getApplication()->isAdmin() || JEVHelper::isEventPublisher() || JEVHelper::canPublishOwnEvents($this->form->jevdata[$this->name]["ev_id"]))
		{
			$ev_id= $this->form->jevdata[$this->name]["ev_id"];

			if ($ev_id == 0 && JFactory::getApplication()->input->getCmd("task")!="icalevent.editcopy")
			{
				// published by default
				$this->value = 1;
			}
				$poptions = array();
				$poptions[] = JHTML::_('select.option', 0, JText::_("JUNPUBLISHED"));
				$poptions[] = JHTML::_('select.option', 1, JText::_("JPUBLISHED"));
                                $poptions[] = JHTML::_('select.option', -1, JText::_("JTRASHED"));

				return JHTML::_('select.genericlist', $poptions, 'state', 'class="inputbox" size="1"', 'value', 'text', $this->value);

		}
		else {
			return '<input type="hidden" name="state" id="state" value="' . $this->value . '" />';
		}

	}

	protected function getLabel()
	{
		if (JFactory::getApplication()->isAdmin() || JEVHelper::isEventPublisher() || JEVHelper::canPublishOwnEvents($this->form->jevdata[$this->name]["ev_id"]))
		{
			return parent::getLabel();
		}
		return "";

	}

}