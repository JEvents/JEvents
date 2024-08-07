<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: modlatest.php 1142 2010-09-08 10:10:52Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Profiler\Profiler;

$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
$app    = Factory::getApplication();

@ob_end_clean();
@ob_end_clean();

// Define the file as an iCalendar file
header('Content-Type: text/calendar; charset=UTF-8');
// Give the file a name and force download
header('Content-Disposition: attachment; filename=calendar.ics');

$html = "";
if ($this->outlook2003icalexport)
	$html .= "BEGIN:VCALENDAR\r\nPRODID:-//jEvents 2.0 for Joomla//EN\r\n";
else
	$html .= "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//jEvents 2.0 for Joomla//EN\r\n";

$html .= "CALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\n";
if (!empty($this->icalEvents))
{

	ob_start();
	$tzid = $this->vtimezone($this->icalEvents);
	$html .= ob_get_clean();

	// Build Exceptions dataset - all done in big batches to save multiple queries
	$exceptiondata = array();
	$ids           = array();
	foreach ($this->icalEvents as $a)
	{
		$ids[] = $a->ev_id();
		if (count($ids) > 100)
		{
			$db = Factory::getDbo();
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
		$db = Factory::getDbo();
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
	ob_start();
	JEVHelper::onDisplayCustomFieldsMultiRow($this->icalEvents);
	ob_end_clean();

	foreach ($this->icalEvents as $a)
	{
		// if event has repetitions I must find the first one to confirm the dates
		if ($a->hasrepetition() && $this->withrepeats)
		{
			$a = $a->getOriginalFirstRepeat();
		}
		if (!$a) continue;

		// if an irregular repeat then skip it :(
		// TODO create it as a daily repeat using the irregular values as exceptions
		if (isset($a->_freq) && $a->_freq == "IRREGULAR")
		{
			continue;
		}

		// Fix for end time of first repeat if its an exception
		if (array_key_exists($a->ev_id(), $exceptiondata) && array_key_exists($a->rp_id(), $exceptiondata[$a->ev_id()]))
		{
			$exception = $exceptiondata[$a->ev_id()][$a->rp_id()];
			// if its the first repeat that has had its end time changes we have not stored this data so need to determine it again
			if ($exception->startrepeat == $exception->oldstartrepeat && $exception->exception_type == 1)
			{
				// look for repeats that are not exceptions
				$testrepeat = $a->getFirstRepeat(false);
				if ($testrepeat)
				{
					$enddatetime     = $a->getUnixStartTime() + ($testrepeat->getUnixEndTime() - $testrepeat->getUnixStartTime());
					$a->_endrepeat   = date("Y-m-d H:i:s", $enddatetime);
					$a->_dtend       = $enddatetime;
					$a->_unixendtime = $enddatetime;
				}
			}
			// If start AND end date/times have changed
			elseif ($exception->exception_type == 1)
			{
				// look for repeats that are not exceptions
				$testrepeat = $a->getFirstRepeat(false);
				if ($testrepeat)
				{
					$oldstart        = strtotime($exception->oldstartrepeat);
					$enddatetime     = $oldstart + ($testrepeat->getUnixEndTime() - $testrepeat->getUnixStartTime());
					$a->_endrepeat   = date("Y-m-d H:i:s", $enddatetime);
					$a->_dtend       = $enddatetime;
					$a->_unixendtime = $enddatetime;
				}
			}
		}

		$html .= "BEGIN:VEVENT\r\n";
		$html .= "UID:" . $a->uid() . "\r\n";
		$html .= "CATEGORIES:" . $a->catname() . "\r\n";
		if (!empty($a->_class))
			$html .= "CLASS:" . $a->_class . "\r\n";
		$html .= "CREATED:" . date("Ymd\THis", strtotime($a->_created)) . "\r\n";
		$html .= "SUMMARY:" . JEVHelper::iCalTitlePrefix($a) . $a->title() . "\r\n";
		if ($a->location() != "")
		{
			if (!is_numeric($a->location()))
			{
				$html .= "LOCATION:" . $this->wraplines(str_replace(array(","), array("\,"), $this->replacetags($a->location()))) . "\r\n";
			}
			else if (isset($a->_loc_title))
			{
				$html .= "LOCATION:" . $this->wraplines(str_replace(array(","), array("\,"), $this->replacetags($a->_loc_title))) . "\r\n";
			}
			else
			{
				$html .= "LOCATION:" . $this->wraplines(str_replace(array(","), array("\,"), $this->replacetags($a->location()))) . "\r\n";
			}
		}
		// We Need to wrap this according to the specs
		/* $html .= "DESCRIPTION:".preg_replace("'<[\/\!]*?[^<>]*?>'si","",preg_replace("/\n|\r\n|\r$/","",$a->content()))."\n"; */

		//Check if we should include the link to the event
		if ($params->get('source_url', 0) == 1)
		{
			$link = $a->viewDetailLink($a->yup(), $a->mup(), $a->dup(), true, $params->get('default_itemid', 0));
			$uri  = Uri::getInstance(Uri::base());
			$root = $uri->toString(array('scheme', 'host', 'port'));
			$html .= "URL;VALUE=URI:" . $this->wraplines($root . Route::_($link, true, -1)) . "\r\n";
			$html .= $this->setDescription($a->content() . ' <a href="'. $root . Route::_($link, true, -1) . '">' . Text::_('JEV_EVENT_IMPORTED_FROM') . $root . Route::_($link, true, -1). "</a>\r\n");
		}
        else {
            $html .= $this->setDescription($a->content()) . "\r\n";
        }

		if ($a->hasContactInfo())
		{
			$html .= "CONTACT:" . $this->replacetags($a->contact_info()) . "\r\n";
		}

		if ($a->hasExtraInfo())
		{
			$html .= "X-EXTRAINFO:" . $this->wraplines($this->replacetags($a->_extra_info)) . "\r\n";
		}

		if ($a->hasColor())
		{
			$html .= "X-COLOR:" . $this->wraplines(strip_tags($a->_color)) . "\r\n";
		}

		$alldayprefix = "";
		// No doing true timezones!
		if ($tzid == "" && is_callable("date_default_timezone_set"))
		{
			// UTC!
			$start = $a->getUnixStartTime();
			$end   = $a->getUnixEndTime();

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
				$startformat  = "Ymd";
				$endformat    = "Ymd";

				// add 10 seconds to make sure its not midnight the previous night
				$start += 10;
				$end   += 10;
			}
			else
			{
				date_default_timezone_set("UTC");

				$startformat = "Ymd\THis";
				$endformat   = "Ymd\THis";
			}

			// Do not use JevDate version since this sets timezone to config value!
			$start = date($startformat, $start);
			$end   = date($endformat, $end);

			$stamptime = date("Ymd\THis", time());

			// Change back
			date_default_timezone_set($current_timezone);
		}
		else
		{
			$start = $a->getUnixStartTime();
			$end   = $a->getUnixEndTime();

			// If all day event then don't show the start time or end time either
			if ($a->alldayevent())
			{
				$alldayprefix = ";VALUE=DATE";
				$startformat  = "Ymd";
				$endformat    = "Ymd";

				// add 10 seconds to make sure its not midnight the previous night
				$start += 10;
				$end   += 10;
			}
			else
			{
				$startformat = "Ymd\THis";
				$endformat   = "Ymd\THis";
			}

			$start = date($startformat, $start);
			$end   = date($endformat, $end);

			if (is_callable("date_default_timezone_set"))
			{
				// Change timezone to UTC
				$current_timezone = date_default_timezone_get();
				date_default_timezone_set("UTC");
				$stamptime = date("Ymd\THis", time());
				// Change back
				date_default_timezone_set($current_timezone);
			}
			else
			{
				$stamptime = date("Ymd\THis", time());
			}

			// in case the first repeat is changed
			if (array_key_exists($a->_eventid, $exceptiondata) && array_key_exists($a->rp_id(), $exceptiondata[$a->_eventid]))
			{
				$start = date($startformat, JevDate::strtotime($exceptiondata[$a->_eventid][$a->rp_id()]->oldstartrepeat));
                $this->withrepeats = true;
			}
		}

		$html .= "DTSTAMP:" . $stamptime . "\r\n";
		$html .= "DTSTART$tzid$alldayprefix:" . $start . "\r\n";
		// events with no end time don't give a DTEND
		if ($a->noendtime())
		{
			// special case for no-end time over multiple days
			if ($a->start_date != $a->stop_date)
			{
				$alldayprefix = ";VALUE=DATE";
				$endformat    = "%Y%m%d";
				// add 10 seconds to make sure its not midnight the previous night
				$end  = JevDate::strftime($endformat, $a->getUnixEndTime() + 10);
				$html .= "DTEND$tzid$alldayprefix:" . $end . "\r\n";
			}
		}
		else
		{
			$html .= "DTEND$tzid$alldayprefix:" . $end . "\r\n";
		}

		$html              .= "SEQUENCE:" . $a->_sequence . "\r\n";
		$deletes           = array();
		$changed           = array();
		$changedexceptions = array();
		if ($a->hasrepetition() && $this->withrepeats)
		{
			$html .= 'RRULE:';

			// TODO MAKE SURE COMPAIBLE COMBINATIONS
			$html .= 'FREQ=' . $a->_freq;
			if ($a->_until != "" && $a->_until != 0)
			{
				// Do not use JevDate version since this sets timezone to config value!
				// GOOGLE HAS A PROBLEM WITH 235959!!!
				if ($a->alldayevent())
				{
					$html .= ';UNTIL=' . date("Ymd\T000000\Z", $a->_until );
				}
				else
				{
					$html .= ';UNTIL=' . date("Ymd\T000000\Z", $a->_until + 86400);
				}
			}
			else if ($a->_count != "")
			{
				$html .= ';COUNT=' . $a->_count;
			}
			if ($a->_rinterval != "")
				$html .= ';INTERVAL=' . $a->_rinterval;
			if ($a->_freq == "DAILY")
			{

			}
			else if ($a->_freq == "WEEKLY")
			{
				if ($a->_byday != "")
				{
					// must remove an extraneuous +/- and numbers
					$a->_byday = str_replace(array("+", "-", "0", "1", "2", "3", "4", "5", "6"), "", $a->_byday);
					$html .= ';BYDAY=' . $a->_byday;
				}
			}
			else if ($a->_freq == "MONTHLY")
			{
				if ($a->_bymonthday != "")
				{
					$html .= ';BYMONTHDAY=' . $a->_bymonthday;
					if ($a->_byweekno != "")
						$html .= ';BYWEEKNO=' . $a->_byweekno;
				}
				else if ($a->_byday != "")
				{
					$html .= ';BYDAY=' . $a->_byday;
					if ($a->_byweekno != "")
						$html .= ';BYWEEKNO=' . $a->_byweekno;
				}
			}
			else if ($a->_freq == "YEARLY")
			{
				if ($a->_byyearday != "")
					$html .= ';BYYEARDAY=' . $a->_byyearday;
			}
			$html .= "\r\n";

			// Now handle Exceptions
			$exceptions = array();
			if (array_key_exists($a->ev_id(), $exceptiondata))
			{
				$exceptions = $exceptiondata[$a->ev_id()];
			}

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
							$deletes[] = date("Ymd\THis", $exceptiondate);

							// Change back
							date_default_timezone_set($current_timezone);
						}
						else
						{
							$deletes[] = date("Ymd\THis", $exceptiondate);
						}
					}
					else
					{
						$changed[]                            = $exception->rp_id;
						$changedexceptions[$exception->rp_id] = $exception;
					}
				}
				if (count($deletes) > 0)
				{
					$html .= "EXDATE$tzid:" . $this->wraplines(implode(",", $deletes)) . "\r\n";
				}
			}
		}

		$html .= "TRANSP:OPAQUE\r\n";
		$html .= "END:VEVENT\r\n";

		$changedrows = array();

		if (isset($changed) && count($changed) > 0 && $changed[0] != 0)
		{
			foreach ($changed as $rpid)
			{
				$a = $this->dataModel->getEventData($rpid, "icaldb", 0, 0, 0);

				if ($a && isset($a["row"]))
				{
					$a             = $a["row"];
					$changedrows[] = $a;
				}
			}

			ob_start();
           // !JDEBUG ?: Profiler::getInstance('Application')->mark('before onDisplayCustomFieldsMultiRow');
			$app->triggerEvent('onDisplayCustomFieldsMultiRow', array(&$changedrows));
           // !JDEBUG ?: Profiler::getInstance('Application')->mark('after onDisplayCustomFieldsMultiRow');
			ob_end_clean();

			foreach ($changedrows as $a)
			{
				$html .= "BEGIN:VEVENT\r\n";
				$html .= "UID:" . $a->uid() . "\r\n";
				$html .= "CATEGORIES:" . $a->catname() . "\r\n";
				if (!empty($a->_class))
					$html .= "CLASS:" . $a->_class . "\r\n";
				$html .= "CREATED:" . date("Ymd\THis", strtotime($a->_created)) . "\r\n";
				$html .= "SUMMARY:" . $a->title() . "\r\n";
				if ($a->location() != "") $html .= "LOCATION:" . $this->wraplines($this->replacetags($a->location())) . "\r\n";
				// We Need to wrap this according to the specs
				$html .= $this->setDescription($a->content()) . "\r\n";

				$ilink = $a->viewDetailLink($a->yup(), $a->mup(), $a->dup(), true, $params->get('default_itemid', 0));
				$iuri  = Uri::getInstance(Uri::base());
				$iroot = $iuri->toString(array('scheme', 'host', 'port'));
				$html .= "URL;VALUE=URI:" . $this->wraplines($iroot . Route::_($ilink, true, -1)) . "\r\n";


				if ($a->hasContactInfo())
					$html .= "CONTACT:" . $this->replacetags($a->contact_info()) . "\r\n";
				if ($a->hasExtraInfo())
					$html .= "X-EXTRAINFO:" . $this->wraplines($this->replacetags($a->_extra_info)); $html .= "\r\n";
				if ($a->hasColor())
				{
					$html .= "X-COLOR:" . $this->wraplines($this->replacetags($a->_color)) . "\r\n";
				}

				$exception     = $changedexceptions[$a->rp_id()];
				$originalstart = JevDate::strtotime($exception->oldstartrepeat);
				$chstart       = $a->getUnixStartTime();
				$chend         = $a->getUnixEndTime();

				// No doing true timezones!
				if ($tzid == "" && is_callable("date_default_timezone_set"))
				{
					// UTC!
					// Change timezone to UTC
					$current_timezone = date_default_timezone_get();
					date_default_timezone_set("UTC");

					// Do not use JevDate version since this sets timezone to config value!
					$chstart       = date("Ymd\THis", $chstart);
					$chend         = date("Ymd\THis", $chend);
					$originalstart = date("Ymd\THis", $originalstart);
					// Change back
					date_default_timezone_set($current_timezone);
				}
				else
				{
					$chstart       = date("Ymd\THis", $chstart);
					$chend         = date("Ymd\THis", $chend);
					$originalstart = date("Ymd\THis", $originalstart);
				}

				if (is_callable("date_default_timezone_set"))
				{
					// Change timezone to UTC
					$current_timezone = date_default_timezone_get();
					date_default_timezone_set("UTC");
					$stamptime = date("Ymd\THis", time());
					// Change back
					date_default_timezone_set($current_timezone);
				}
				else
				{
					$stamptime = date("Ymd\THis", time());
				}

				$html .= "DTSTAMP:" . $stamptime . "\r\n";
				$html .= "DTSTART$tzid:" . $chstart . "\r\n";
				$html .= "DTEND$tzid:" . $chend . "\r\n";
				$html .= "RECURRENCE-ID$tzid:" . $originalstart . "\r\n";
				$html .= "SEQUENCE:" . $a->_sequence . "\r\n";
				$html .= "TRANSP:OPAQUE\r\n";
				$html .= "END:VEVENT\r\n";

			}
		}
	}

    // Now handle the irregular repeats as a series of one off events
    foreach ($this->icalEvents as $a)
    {
        // if NOT an irregular repeat then skip it :(
        if (!isset($a->_freq) || $a->_freq !== "IRREGULAR")
        {
            continue;
        }

        $html .= "BEGIN:VEVENT\r\n";
        $html .= "UID:" . $a->uid() . "RR" . $a->rp_id() . "\r\n";
        $html .= "CATEGORIES:" . $a->catname() . "\r\n";
        if (!empty($a->_class))
            $html .= "CLASS:" . $a->_class . "\r\n";
        $html .= "CREATED:" . date("Ymd\THis", strtotime($a->_created)) . "\r\n";
        $html .= "SUMMARY:" . JEVHelper::iCalTitlePrefix($a) . $a->title() . "\r\n";
        if ($a->location() != "")
        {
            if (!is_numeric($a->location()))
            {
                $html .= "LOCATION:" . $this->wraplines(str_replace(array(","), array("\,"), $this->replacetags($a->location()))) . "\r\n";
            }
            else if (isset($a->_loc_title))
            {
                $html .= "LOCATION:" . $this->wraplines(str_replace(array(","), array("\,"), $this->replacetags($a->_loc_title))) . "\r\n";
            }
            else
            {
                $html .= "LOCATION:" . $this->wraplines(str_replace(array(","), array("\,"), $this->replacetags($a->location()))) . "\r\n";
            }
        }
        // We Need to wrap this according to the specs
        /* $html .= "DESCRIPTION:".preg_replace("'<[\/\!]*?[^<>]*?>'si","",preg_replace("/\n|\r\n|\r$/","",$a->content()))."\n"; */

        //Check if we should include the link to the event
        if ($params->get('source_url', 0) == 1)
        {
            $link = $a->viewDetailLink($a->yup(), $a->mup(), $a->dup(), true, $params->get('default_itemid', 0));
            $uri  = Uri::getInstance(Uri::base());
            $root = $uri->toString(array('scheme', 'host', 'port'));
            $html .= "URL;VALUE=URI:" . $this->wraplines($root . Route::_($link, true, -1)) . "\r\n";
            //$html .= $this->setDescription($a->content() . ' ' . Text::_('JEV_EVENT_IMPORTED_FROM') . $root . Route::_($link, true, -1)) . "\r\n";
        }
        $html .= $this->setDescription($a->content()) . "\r\n";

        if ($a->hasContactInfo())
        {
            $html .= "CONTACT:" . $this->replacetags($a->contact_info()) . "\r\n";
        }

        if ($a->hasExtraInfo())
        {
            $html .= "X-EXTRAINFO:" . $this->wraplines($this->replacetags($a->_extra_info)) . "\r\n";
        }

        if ($a->hasColor())
        {
            $html .= "X-COLOR:" . $this->wraplines(strip_tags($a->_color)) . "\r\n";
        }

        $alldayprefix = "";
        // No doing true timezones!
        if ($tzid == "" && is_callable("date_default_timezone_set"))
        {
            // UTC!
            $start = $a->getUnixStartTime();
            $end   = $a->getUnixEndTime();

            // Change timezone to UTC
            $current_timezone = date_default_timezone_get();

            // If all day event then don't show the start time or end time either
            if ($a->alldayevent())
            {
                $alldayprefix = ";VALUE=DATE";
                $startformat  = "Ymd";
                $endformat    = "Ymd";

                // add 10 seconds to make sure its not midnight the previous night
                $start += 10;
                $end   += 10;
            }
            else
            {
                date_default_timezone_set("UTC");

                $startformat = "Ymd\THis";
                $endformat   = "Ymd\THis";
            }

            // Do not use JevDate version since this sets timezone to config value!
            $start = date($startformat, $start);
            $end   = date($endformat, $end);

            $stamptime = date("Ymd\THis", time());

            // Change back
            date_default_timezone_set($current_timezone);
        }
        else
        {
            $start = $a->getUnixStartTime();
            $end   = $a->getUnixEndTime();

            // If all day event then don't show the start time or end time either
            if ($a->alldayevent())
            {
                $alldayprefix = ";VALUE=DATE";
                $startformat  = "Ymd";
                $endformat    = "Ymd";

                // add 10 seconds to make sure its not midnight the previous night
                $start += 10;
                $end   += 10;
            }
            else
            {
                $startformat = "Ymd\THis";
                $endformat   = "Ymd\THis";
            }

            $start = date($startformat, $start);
            $end   = date($endformat, $end);

            if (is_callable("date_default_timezone_set"))
            {
                // Change timezone to UTC
                $current_timezone = date_default_timezone_get();
                date_default_timezone_set("UTC");
                $stamptime = date("Ymd\THis", time());
                // Change back
                date_default_timezone_set($current_timezone);
            }
            else
            {
                $stamptime = date("Ymd\THis", time());
            }

        }

        $html .= "DTSTAMP:" . $stamptime . "\r\n";
        $html .= "DTSTART$tzid$alldayprefix:" . $start . "\r\n";
        // events with no end time don't give a DTEND
        if ($a->noendtime())
        {
            // special case for no-end time over multiple days
            if ($a->start_date != $a->stop_date)
            {
                $alldayprefix = ";VALUE=DATE";
                $endformat    = "%Y%m%d";
                // add 10 seconds to make sure its not midnight the previous night
                $end  = JevDate::strftime($endformat, $a->getUnixEndTime() + 10);
                $html .= "DTEND$tzid$alldayprefix:" . $end . "\r\n";
            }
        }
        else
        {
            $html .= "DTEND$tzid$alldayprefix:" . $end . "\r\n";
        }

        $html              .= "SEQUENCE:" . $a->_sequence . "\r\n";

        // TREAT AS NOT REPEATING

        $html .= "TRANSP:OPAQUE\r\n";
        $html .= "END:VEVENT\r\n";

    }

}


$html .= "END:VCALENDAR";

// clear out any rubbish
@ob_end_clean();
echo $html;

exit();
