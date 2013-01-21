<?php

defined('_JEXEC') or die('Restricted access');

function DefaultRepeatSummary($view, $event)
{
	$sum = "";

	if (!isset($event->start_date))
	{
		$event_up = new JEventDate($event->publish_up());
		// NB If you set language file date/time formatting then you can use a specific format string here (using strftime formats) e.g.
		// $event->start_date = JEventsHTML::getDateFormat($event_up->year, $event_up->month, $event_up->day, "%Y-%m-%d");
		$event->start_date = JEventsHTML::getDateFormat($event_up->year, $event_up->month, $event_up->day, 0);
		$event->start_time = JEVHelper::getTime($event->getUnixStartTime());

		$event_down = new JEventDate($event->publish_down());
		$event->stop_date = JEventsHTML::getDateFormat($event_down->year, $event_down->month, $event_down->day, 0);
		$event->stop_time = JEVHelper::getTime($event->getUnixEndTime());
		$event->stop_time_midnightFix = $event->stop_time;
		$event->stop_date_midnightFix = $event->stop_date;
		if ($event_down->second == 59)
		{
			$event->stop_time_midnightFix = JEVHelper::getTime($event->getUnixEndTime() + 1);
			$event->stop_date_midnightFix = JEventsHTML::getDateFormat($event_down->year, $event_down->month, $event_down->day + 1, 0);
		}
	}
	if ($event->alldayevent())
	{
		if ($event->start_date == $event->stop_date)
		{
			$sum.= $event->start_date;
		}
		else
		{
			$sum.= JText::_('JEV_FROM') . '&nbsp;' . $event->start_date . '<br />'
					. JText::_('JEV_TO') . '&nbsp;' . $event->stop_date . '<br/>';
		}
	}
	// if starttime and end time the same then show no times!
	else if ($event->start_date == $event->stop_date)
	{
		if ($event->noendtime())
		{
			$sum.= $event->start_date . ',&nbsp;' . $event->start_time . '<br/>';
		}
		else if (($event->start_time != $event->stop_time) && !($event->alldayevent()))
		{
			$sum.= $event->start_date . ',&nbsp;' . $event->start_time
					. '&nbsp;-&nbsp;' . $event->stop_time_midnightFix . '<br/>';
		}
		else if (($event->start_time == $event->stop_time) && !($event->alldayevent()))
		{
			$sum.= $event->start_date . ',&nbsp;' . $event->start_time . '<br/>';
		}
		else
		{
			$sum.= $event->start_date . '<br/>';
		}
	}
	else
	{
		// recurring events should have time related to recurrance not range of dates
		if ($event->noendtime() && !($event->reccurtype() > 0))
		{
			$sum.= $event->start_date . ',&nbsp;' . $event->start_time . '<br/>'
					. JText::_('JEV_TO') . '&nbsp;' . $event->stop_date . '<br/>';
		}
		else if ($event->start_time != $event->stop_time && !($event->reccurtype() > 0))
		{
			$sum.= JText::_('JEV_FROM') . '&nbsp;' . $event->start_date . '&nbsp;-&nbsp; '
					. $event->start_time . '<br />'
					. JText::_('JEV_TO') . '&nbsp;' . $event->stop_date . '&nbsp;-&nbsp;'
					. $event->stop_time_midnightFix . '<br/>';
		}
		else
		{
			$sum.= JText::_('JEV_FROM') . '&nbsp;' . $event->start_date . '<br />'
					. JText::_('JEV_TO') . '&nbsp;' . $event->stop_date . '<br/>';
		}
	}
	if ($event->_freq == "none")
	{
		return $sum;
	}

	if ($event->_eventdetail_id != $event->_detail_id)
	{
		$sum .= "<div class='ev_repeatexception'>" . JText::_('JEV_REPEATEXCEPTION') . "</div>";
	}

	return $sum;

}

