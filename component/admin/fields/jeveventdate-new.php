<?php

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJeveventdate extends JFormField
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected
			$type = 'Jeveventdate';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected
			function getInput()
	{
		ob_start();
		$event = $this->form->jevdata[$this->name]["event"];
		$eventfield = $this->name=="publish_up"?"startDate":"endDate";
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$minyear = JEVHelper::getMinYear();
		$maxyear = JEVHelper::getMaxYear();
		$inputdateformat = $params->get("com_editdateformat", "d.m.Y");
		static $firsttime;
		if (!defined($firsttime)){
			$document = JFactory::getDocument();
			$js = "\neventEditDateFormat='$inputdateformat';Date.defineParser(eventEditDateFormat.replace('d','%d').replace('m','%m').replace('Y','%Y'));";
			$document->addScriptDeclaration($js);
		}
		JEVHelper::loadCalendar($this->name, $this->name, $event->$eventfield(), $minyear, $maxyear, 'var elem = $("'.$this->name.'");'.$this->element['onhidestart'], "elem = $('".$this->name."');".$this->element['onchange'], $inputdateformat);
		?>
		<input type="hidden"  name="<?php echo $this->name;?>2" id="<?php echo $this->name;?>2" value="" />
		<?php

		$html = ob_get_clean();

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		return $html;

	}

}