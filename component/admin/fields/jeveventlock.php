<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class FormFieldJeveventlock extends FormField
{

	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Jeveventlock';

	protected function getLabel()
	{

		$offerlock = $this->form->jevdata[$this->name]["offerlock"];
		if ($this->getInput() && $offerlock)
		{
			return parent::getLabel();
		}

		return "";

	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{

		ob_start();
		$offerlock = $this->form->jevdata[$this->name]["offerlock"];

		$btngroup = "btn-group";
		$btn      = "btn";
		if ($offerlock)
		{
			?>
			<div class="radio <?php echo $btngroup; ?>">
				<label class="radio  <?php echo $btn; ?>" for="lockevent1">
					<?php echo Text::_("JEV_YES"); ?>
					<input type="radio" name="lockevent" id="lockevent1"
					       value="1" <?php echo($this->value ? "checked='checked'" : "") ?> />
				</label>
				<label class="radio   <?php echo $btn; ?>" for="lockevent0">
					<?php echo Text::_("JEV_NO"); ?>
					<input type="radio" name="lockevent" id="lockevent0"
					       value="0" <?php echo(!$this->value ? "checked='checked'" : ""); ?> />
				</label>
			</div>
			<?php
		}
		$input = ob_get_clean();

		return $input;

	}

}
class_alias("FormFieldJeveventlock", "JFormFieldJeveventlock");
