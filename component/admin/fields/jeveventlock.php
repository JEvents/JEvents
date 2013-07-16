<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventlock extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventlock';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		ob_start();
		$offerlock = $this->form->jevdata[$this->name]["offerlock"];

		if ($offerlock)
		{
			?>
			<div class="radio btn-group">
				<label class="radio btn"  for="lockevent1">
					<?php echo JText::_("JEV_YES"); ?>
					<input type="radio" name="lockevent" id="lockevent1" value="1" <?php echo ($this->value ? "checked='checked'" : "") ?> />
				</label>
				<label class="radio btn" for="lockevent0">
					<?php echo JText::_("JEV_NO"); ?>
					<input type="radio" name="lockevent" id="lockevent0" value="0" <?php echo (!$this->value ? "checked='checked'" : ""); ?> />
				</label>
			</div>
			<?php
		}
		$input = ob_get_clean();
		return $input;

	}

	protected function getLabel()
	{
		$offerlock = $this->form->jevdata[$this->name]["offerlock"];
		if ($this->getInput() && $offerlock)
		{
			return parent::getLabel();
		}
		return "";

	}

}