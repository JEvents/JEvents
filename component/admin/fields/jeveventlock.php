<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");

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

		$btngroup = JevJoomlaVersion::isCompatible("3.0")? "btn-group" : "";
		$btn = JevJoomlaVersion::isCompatible("3.0")? "btn" : "";
		if ($offerlock)
		{
			?>
			<div class="radio <?php echo $btngroup;?>">
				<label class="radio  <?php echo $btn;?>"  for="lockevent1">
					<?php echo JText::_("JEV_YES"); ?>
					<input type="radio" name="lockevent" id="lockevent1" value="1" <?php echo ($this->value ? "checked='checked'" : "") ?> />
				</label>
				<label class="radio   <?php echo $btn;?>" for="lockevent0">
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