<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");

class JFormFieldJeveventtime extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected
			$type = 'Jeveventtime';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected
			function getInput()
	{
		$event = $this->form->jevdata[$this->name]["event"];
		ob_start();
		$name = $this->name;
		$partname = explode("_",$name);
		$partname = $partname[0];
		$time24function =$partname."time24";

		if ($partname=="start"){
			$test = $event->alldayevent();
		}
		else {
			$test = $event->alldayevent() || $event->noendtime();
		}
		$btngroup = JevJoomlaVersion::isCompatible("3.0")? "btn-group" : "";
		$btn = JevJoomlaVersion::isCompatible("3.0")? "btn" : "";
		?>
		<div id="<?php echo $partname;?>_24h_area" class="jev_inline">
			<input class="inputbox" type="text" name="<?php echo $partname;?>_time" id="<?php echo $name;?>" size="8" <?php echo $test ? "disabled='disabled'" : ""; ?> maxlength="8" value="<?php echo $event->$time24function(); ?>" onchange="checkTime(this);"/>
		</div>
		<div  id="<?php echo $partname;?>_12h_area"  class="jev_inline">
			<input class="inputbox" type="text" name="<?php echo $partname;?>_12h" id="<?php echo $partname;?>_12h" size="8" maxlength="8" <?php echo $test ? "disabled='disabled'" : ""; ?> value="" onchange="check12hTime(this);" />
			<div class="radio <?php echo $btngroup;?> " id="<?php echo $partname;?>_ampm"  style="display:inline;">
				<label for="<?php echo $partname;?>AM" class="radio <?php echo $btn;?>">
					<input type="radio" name="<?php echo $partname;?>_ampm" id="<?php echo $partname;?>AM" value="none" checked="checked" onclick="toggleAMPM('<?php echo $partname;?>AM');" <?php echo $test ? "disabled='disabled'" : ""; ?> />
					<?php echo JText::_('JEV_AM'); ?>
				</label>
				<label for="<?php echo $partname;?>PM" class="radio <?php echo $btn;?>">
					<input type="radio" name="<?php echo $partname;?>_ampm" id="<?php echo $partname;?>PM" value="none" onclick="toggleAMPM('<?php echo $partname;?>PM');" <?php echo $test ? "disabled='disabled'" : ""; ?> />
					<?php echo JText::_('JEV_PM'); ?>
				</label>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		return $html;

	}

}
