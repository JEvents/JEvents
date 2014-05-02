<?php
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");
include_once(JEV_ADMINLIBS . "/colorMap.php");

class JFormFieldJeveventcolourpicker extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Jeveventcolourpicker';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
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
			JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
			JEVHelper::ConditionalFields( $this->element,$this->form->getName());

			ob_start();
			?>
			<table id="pick1064797275" style="background-color:<?php echo $this->value . ';color:' . JevMapColor($this->value); ?>;border:solid 1px black;">
				<tr>	
					<td  nowrap="nowrap">
						<input type="hidden" id="pick1064797275field" name="color" value="<?php echo $this->value; ?>"/>
						<a id="colorPickButton" href="javascript:void(0)"  onclick="document.getElementById('fred').style.visibility='visible';"	  style="visibility:visible;color:<?php echo JevMapColor($this->value); ?>;font-weight:bold;"><?php echo JText::_('JEV_COLOR_PICKER'); ?></a>
					</td>
					<td>
						<div style="position:relative;z-index:9999;">
							<iframe id="fred" src="<?php echo JURI::root() . "components/" . JEV_COM_COMPONENT . "/libraries/colours.html?id=fred"; ?>" class="jev_ev_colour_picker_i"></iframe>
						</div>
					</td>
				</tr>
			</table>
			<?php
			return ob_get_clean();
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