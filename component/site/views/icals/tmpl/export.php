<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: modlatest.php 1142 2010-09-08 10:10:52Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

ob_end_clean();

// Define the file as an iCalendar file
header('Content-Type: application/octet-stream; charset=UTF-8');
// Give the file a name and force download
header('Content-Disposition: attachment; filename=calendar.ics');

if ($this->outlook2003icalexport)
	echo "BEGIN:VCALENDAR\nPRODID:-//jEvents 2.0 for Joomla//EN\n";
else
	echo "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//jEvents 2.0 for Joomla//EN\n";

echo "CALSCALE:GREGORIAN\nMETHOD:PUBLISH\n";
if (!empty($this->icalEvents))
{

	$tzid = $this->vtimezone($this->icalEvents);

	// Build Exceptions dataset - all done in big batches to save multiple queries
	$exceptiondata = array();
	$ids = array();
	foreach ($this->icalEvents as $a)
	{
		$ids[] = $a->ev_id();
		if (count($ids) > 100)
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (" . implode(",", $ids) . ")");
			$rows = $db->loadObjectList();
			foreach ($rows as $row)
			{
				if (!isset($exceptiondata[$row->eventid]))
				{
					$exceptiondata[$row->eventid] = array();
				}
				$exceptiondata[$row->eventid][$row->rp_id] = $row;
			}
			$ids = array();
		}
	}
	// mop up the last ones
	if (count($ids) > 0)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (" . implode(",", $ids) . ")");
		$rows = $db->loadObjectList();
		foreach ($rows as $row)
		{
			if (!isset($exceptiondata[$row->eventid]))
			{
				$exceptiondata[$row->eventid] = array();
			}
			$exceptiondata[$row->eventid][$row->rp_id] = $row;
		}
	}
	
	// make sure the array is now reindexed for the sake of the plugins!
	$this->icalEvents = array_values($this->icalEvents);
			
	// Call plugin on each event
	$dispatcher =& JDispatcher::getInstance();
	ob_start();
	$dispatcher->trigger( 'onDisplayCustomFieldsMultiRow', array( &$this->icalEvents) );
	ob_end_clean();
	
	foreach ($this->icalEvents as $a)
	{
		// if event has repetitions I must find the first one to confirm the dates
		if ($a->hasrepetition())
		{
			$a = $a->getOriginalFirstRepeat();
		}
		echo "BEGIN:VEVENT\n";
		echo "UID:" . $a->uid() . "\n";
		echo "CATEGORIES:" . $a->catname() . "\n";
		if (!empty($a->_class))
			echo "CLASS:" . $a->_class . "\n";
		echo "SUMMARY:" . $a->title() . "\n";
		echo "LOCATION:" . $this->wraplines($this->replacetags($a->location())) . "\n";
		// We Need to wrap this according to the specs
		/* echo "DESCRIPTION:".preg_replace("'<[\/\!]*?[^<>]*?>'si","",preg_replace("/\n|\r\n|\r$/","",$a->content()))."\n"; */
		echo $this->setDescription($a->content()) . "\n";

		if ($a->hasContactInfo())
			echo "CONTACT:" . $this->replacetags($a->contact_info()) . "\n";
		if ($a->hasExtraInfo())
			echo "X-EXTRAINFO:" . $this->wraplines($this->replacetags($a->_extra_info)) . "\n";

		$alldayprefix = "";
		// No doing true timezones!
		if ($tzid == "" && is_callable("date_default_timezone_set"))
		{
			// UTC!
			$start = $a->getUnixStartTime();
			$end = $a->getUnixEndTime();

			// in case the first repeat has been changed
			if (array_key_exists($a->_eventid, $exceptiondata) && array_key_exists($a->rp_id(), $exceptiondata[$a->_eventid]))
			{
				$start = JevDate::strtotime($exceptiondata[$a->_eventid][$a->rp_id()]->oldstartrepeat);
			}

			// Change timezone to UTC
			$current_timezone = date_default_timezone_get();

			// If all day event then don't show the start time or end time either
			if ($a->alldayevent())
			{
				$alldayprefix = ";VALUE=DATE";
				$startformat = "%Y%m%d";
				$endformat = "%Y%m%d";

				// add 10 seconds to make sure its not midnight the previous night
				$start += 10;
				$end += 10;
			}
			else
			{
				date_default_timezone_set("UTC");

				$startformat = "%Y%m%dT%H%M%SZ";
				$endformat = "%Y%m%dT%H%M%SZ";
			}

			// Do not use JevDate version since this sets timezone to config value!
			$start = strftime($startformat, $start);
			$end = strftime($endformat, $end);

			$stamptime = strftime("%Y%m%dT%H%M%SZ", time());

			// Change back
			date_default_timezone_set($current_timezone);
		}
		else
		{
			$start = $a->getUnixStartTime();
			$end = $a->getUnixEndTime();

			// If all day event then don't show the start time or end time either
			if ($a->alldayevent())
			{
				$alldayprefix = ";VALUE=DATE";
				$startformat = "%Y%m%d";
				$endformat = ":%Y%m%d";

				// add 10 seconds to make sure its not midnight the previous night
				$start += 10;
				$end += 10;
			}
			else
			{
				$startformat = "%Y%m%dT%H%M%S";
				$endformat = "%Y%m%dT%H%M%S";
			}

			$start = JevDate::strftime($startformat, $start);
			$end = JevDate::strftime($endformat, $end);
			$stamptime = JevDate::strftime("%Y%m%dT%H%M%S", time());

			// in case the first repeat is changed
			if (array_key_exists($a->_eventid, $exceptiondata) && array_key_exists($a->rp_id(), $exceptiondata[$a->_eventid]))
			{
				$start = JevDate::strftime($startformat, JevDate::strtotime($exceptiondata[$a->_eventid][$a->rp_id()]->oldstartrepeat));
			}
		}

		echo "DTSTAMP$tzid$alldayprefix:" . $stamptime . "\n";
		echo "DTSTART$tzid$alldayprefix:" . $start . "\n";
		// events with no end time don't give a DTEND
		if (!$a->noendtime())
		{
			echo "DTEND$tzid$alldayprefix:" . $end . "\n";
		}
		echo "SEQUENCE:" . $a->_sequence . "\n";
		if ($a->hasrepetition())
		{
			echo 'RRULE:';

			// TODO MAKE SURE COMPAIBLE COMBINATIONS
			echo 'FREQ=' . $a->_freq;
			if ($a->_until != "" && $a->_until != 0)
			{
				// Do not use JevDate version since this sets timezone to config value!					
				echo ';UNTIL=' . strftime("%Y%m%dT235959Z", $a->_until);
			}
			else if ($a->_count != "")
			{
				echo ';COUNT=' . $a->_count;
			}
			if ($a->_rinterval != "")
				echo ';INTERVAL=' . $a->_rinterval;
			if ($a->_freq == "DAILY")
			{
				
			}
			else if ($a->_freq == "WEEKLY")
			{
				if ($a->_byday != "")
					echo ';BYDAY=' . $a->_byday;
			}
			else if ($a->_freq == "MONTHLY")
			{
				if ($a->_bymonthday != "")
				{
					echo ';BYMONTHDAY=' . $a->_bymonthday;
					if ($a->_byweekno != "")
						echo ';BYWEEKNO=' . $a->_byweekno;
				}
				else if ($a->_byday != "")
				{
					echo ';BYDAY=' . $a->_byday;
					if ($a->_byweekno != "")
						echo ';BYWEEKNO=' . $a->_byweekno;
				}
			}
			else if ($a->_freq == "YEARLY")
			{
				if ($a->_byyearday != "")
					echo ';BYYEARDAY=' . $a->_byyearday;
			}
			echo "\n";
		}

		// Now handle Exceptions
		$exceptions = array();
		if (array_key_exists($a->ev_id(), $exceptiondata))
		{
			$exceptions = $exceptiondata[$a->ev_id()];
		}

		$deletes = array();
		$changed = array();
		$changedexceptions = array();
		if (count($exceptions) > 0)
		{
			foreach ($exceptions as $exception)
			{
				if ($exception->exception_type == 0)
				{
					$exceptiondate = JevDate::strtotime($exception->startrepeat);

					// No doing true timezones!
					if ($tzid == "" && is_callable("date_default_timezone_set"))
					{

						// Change timezone to UTC
						$current_timezone = date_default_timezone_get();
						date_default_timezone_set("UTC");

						// Do not use JevDate version since this sets timezone to config value!
						$deletes[] = strftime("%Y%m%dT%H%M%SZ", $exceptiondate);

						// Change back
						date_default_timezone_set($current_timezone);
					}
					else
					{
						$deletes[] = JevDate::strftime("%Y%m%dT%H%M%S", $exceptiondate);
					}
				}
				else
				{
					$changed[] = $exception->rp_id;
					$changedexceptions[$exception->rp_id] = $exception;
				}
			}
			if (count($deletes) > 0)
			{
				echo "EXDATE:" . $this->wraplines(implode(",", $deletes)) . "\n";
			}
		}

		echo "TRANSP:OPAQUE\n";
		echo "END:VEVENT\n";


		if (count($changed) > 0)
		{
			foreach ($changed as $rpid)
			{
				$a = $this->dataModel->getEventData($rpid, "icaldb", 0, 0, 0);
				if ($a && isset($a["row"]))
				{
					$a = $a["row"];

					//$dispatcher = & JDispatcher::getInstance();
					//$dispatcher->trigger('onDisplayCustomFields', array(& $a));

					echo "BEGIN:VEVENT\n";
					echo "UID:" . $a->uid() . "\n";
					echo "CATEGORIES:" . $a->catname() . "\n";
					if (!empty($a->_class))
						echo "CLASS:" . $a->_class . "\n";
					echo "SUMMARY:" . $a->title() . "\n";
					echo "LOCATION:" . $this->wraplines($this->replacetags($a->location())) . "\n";
					// We Need to wrap this according to the specs
					echo $this->setDescription($a->content()) . "\n";

					if ($a->hasContactInfo())
						echo "CONTACT:" . $this->replacetags($a->contact_info()) . "\n";
					if ($a->hasExtraInfo())
						echo "X-EXTRAINFO:" . $this->wraplines($this->replacetags($a->_extra_info)); echo "\n";

					$exception = $changedexceptions[$rpid];
					$originalstart = JevDate::strtotime($exception->oldstartrepeat);
					$chstart = $a->getUnixStartTime();
					$chend = $a->getUnixEndTime();

					// No doing true timezones!
					if ($tzid == "" && is_callable("date_default_timezone_set"))
					{
						// UTC!
						// Change timezone to UTC
						$current_timezone = date_default_timezone_get();
						date_default_timezone_set("UTC");

						// Do not use JevDate version since this sets timezone to config value!								
						$chstart = strftime("%Y%m%dT%H%M%SZ", $chstart);
						$chend = strftime("%Y%m%dT%H%M%SZ", $chend);
						$stamptime = strftime("%Y%m%dT%H%M%SZ", time());
						$originalstart = strftime("%Y%m%dT%H%M%SZ", $originalstart);
						// Change back
						date_default_timezone_set($current_timezone);
					}
					else
					{
						$chstart = JevDate::strftime("%Y%m%dT%H%M%S", $chstart);
						$chend = JevDate::strftime("%Y%m%dT%H%M%S", $chend);
						$stamptime = JevDate::strftime("%Y%m%dT%H%M%S", time());
						$originalstart = JevDate::strftime("%Y%m%dT%H%M%S", $originalstart);
					}
					echo "DTSTAMP$tzid:" . $stamptime . "\n";
					echo "DTSTART$tzid:" . $chstart . "\n";
					echo "DTEND$tzid:" . $chend . "\n";
					echo "RECURRENCE-ID:" . $originalstart . "\n";
					echo "SEQUENCE:" . $a->_sequence . "\n";
					echo "TRANSP:OPAQUE\n";
					echo "END:VEVENT\n";
				}
			}
		}
	}
}


echo "END:VCALENDAR";
exit();
