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
	public
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
                
                // Adjust date/time for timezones!
                if ($event->_tzid && !isset($event->tzid_adjusted)){
                    // They are stored in system timezone - we need them in event timezone
                    $testdate = DateTime::createFromFormat('Y-m-d H:i:s', $event->publish_up(), new DateTimeZone(@date_default_timezone_get()));
                    $offset1 = $testdate->getOffset();
                    $testdate->setTimezone(new DateTimeZone($event->tzid));
                    $offset2 = $testdate->getOffset();

		    // Fix for timezone specified repeating events breaks backwards compatability so we must do special case handling here
		    if ($event->_modified < "2017-08-26 00:00:00")
		    {
			//USE OFFSETS FOR unix time stamps!!                       
			$event->dtstart($testdate->format("U") - $offset2 + $offset1);
			$event->_publish_up = $testdate->format('Y-m-d H:i:s');
			$event->_unixstartdate = $event->dtstart();
			$event->_unixstarttime= $event->dtstart();

			$testdate = DateTime::createFromFormat('Y-m-d H:i:s', $event->publish_down(), new DateTimeZone(@date_default_timezone_get()));
			$offset1 = $testdate->getOffset();
			$testdate->setTimezone(new DateTimeZone($event->tzid));
			$offset2 = $testdate->getOffset();

			$event->dtend($testdate->format("U") - $offset2 + $offset1);
			$event->_publish_down = $testdate->format('Y-m-d H:i:s');
			$event->_unixenddate = $event->dtend();
			$event->_unixendtime= $event->dtend();
		    }
		    else {
			//USE OFFSETS FOR unix time stamps!!                       
			$event->dtstart($testdate->format("U") - $offset1 + $offset2);
			$event->_publish_up = $testdate->format('Y-m-d H:i:s');
			$event->_unixstartdate = $event->dtstart();
			$event->_unixstarttime= $event->dtstart();

			$testdate = DateTime::createFromFormat('Y-m-d H:i:s', $event->publish_down(), new DateTimeZone(@date_default_timezone_get()));
			$offset1 = $testdate->getOffset();
			$testdate->setTimezone(new DateTimeZone($event->tzid));
			$offset2 = $testdate->getOffset();

			$event->dtend($testdate->format("U") - $offset1 + $offset2);
			$event->_publish_down = $testdate->format('Y-m-d H:i:s');
			$event->_unixenddate = $event->dtend();
			$event->_unixendtime= $event->dtend();
		    }
		    
                    $event->tzid_adjusted = true;
                }

		$inputdateformat = $params->get("com_editdateformat", "d.m.Y");
		static $firsttime = true;
		if ($firsttime){
			$document = JFactory::getDocument();
			$js = "\n eventEditDateFormat='$inputdateformat';//Date.defineParser(eventEditDateFormat.replace('d','%d').replace('m','%m').replace('Y','%Y'));";
			$document->addScriptDeclaration($js);
			$firsttime = false;
		}
		$cal = JEVHelper::loadElectricCalendar($this->name, $this->name, $event->$eventfield(), $minyear, $maxyear, 'var elem =jevjq(this);'.$this->element['onhidestart'], "var elem = jevjq(this);".$this->element['onchange'], $inputdateformat);
		echo $cal;
		?>
		<input type="hidden"  name="<?php echo $this->name;?>2" id="<?php echo $this->name;?>2" value="" />
		<?php

		$html = ob_get_clean();

		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields( $this->element,$this->form->getName());

		return $html;

	}

}