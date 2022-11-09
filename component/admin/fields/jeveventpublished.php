<?php
defined('JPATH_BASE') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class FormFieldJeveventpublished extends FormField
{

	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Jeveventpublished';

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());
		if (Factory::getApplication()->isClient('administrator') || JEVHelper::isEventPublisher() || JEVHelper::canPublishOwnEvents($this->form->jevdata[$this->name]["ev_id"]))
		{
			$ev_id = $this->form->jevdata[$this->name]["ev_id"];

			if ($ev_id == 0 && Factory::getApplication()->input->getCmd("task") != "icalevent.editcopy")
			{
				// published by default
				$this->value = 1;
			}
			$poptions   = array();
			$poptions[] = HTMLHelper::_('select.option', 0, Text::_("JUNPUBLISHED"));
			$poptions[] = HTMLHelper::_('select.option', 1, Text::_("JPUBLISHED"));
			$poptions[] = HTMLHelper::_('select.option', -1, Text::_("JTRASHED"));

			return HTMLHelper::_('select.genericlist', $poptions, 'state', 'class="inputbox " size="1"', 'value', 'text', $this->value);

		}
		else
		{
			return '<input type="hidden" name="state" id="state" value="' . $this->value . '" />';
		}

	}

	protected function getLabel()
	{

		if (Factory::getApplication()->isClient('administrator') || JEVHelper::isEventPublisher() || JEVHelper::canPublishOwnEvents($this->form->jevdata[$this->name]["ev_id"]))
		{
			return parent::getLabel();
		}

		return "";

	}

}
class_alias("FormFieldJeveventpublished", "JFormFieldJeveventpublished");
