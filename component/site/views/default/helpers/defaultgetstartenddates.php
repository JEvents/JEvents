<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

function Defaultgetstartenddates($view)
{

	$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

	$app        = Factory::getApplication();
	$input      = $app->input;
	// fix to allow start/end date to be preserved during pagination IF filter module before/after dates are used
	$Itemid = $input->getInt("Itemid", 0);
	// This causes the filter module to reset
	$filters          = jevFilterProcessing::getInstance(array());
	$activeFilterMenu = $app->getUserState('active_filter_menu ', $Itemid);

	// When switching between view types then we must reset the date range!
	$oldTask = $app->getUserState('jevents_float_task', false);
	$jevtask = $input->getCmd('jevtask', 'month.calendar');

	//echo "oldTask = $oldTask jevtask = $jevtask<br>";
	$app->setUserState('jevents_float_task', $jevtask);

	if ($input->getInt('filter_reset', 0) || ($activeFilterMenu > 0 && $activeFilterMenu != $Itemid) || ($oldTask && $oldTask !== $jevtask))
	{
		// if actively filtering then do not reset
		if (!$input->getString("startdate", 0) || $input->getInt('filter_reset', 0))
		{
			$input->set('startdate', '');
			$app->setUserState('range_startdate' . $Itemid, '');
		}
		if (!$input->getString("enddate", 0) || $input->getInt('filter_reset', 0))
		{
			$input->get('enddate', '');
			$app->setUserState('range_enddate' . $Itemid, '');
		}

		$app->setUserState('active_filter_menu ', 0);
	}

	$startdate = $app->getUserStateFromRequest('range_startdate' . $Itemid, 'startdate', $input->getString("startdate"));
	$enddate   = $app->getUserStateFromRequest('range_enddate' . $Itemid, 'enddate', $input->getString("enddate"));

	if ($jevtask == "day.listevents" && empty($input->get('enddate', '')) && empty($input->get('range_startdate', '')))
	{
		list($year, $month, $day) = JEVHelper::getYMD();
		$startdate = "$year-$month-$day";
		$enddate = "$year-$month-$day";
	}

	if ($startdate != "")
	{
		// WE have specified a start date in the URL so we should use it!
		list($startyear, $startmonth, $startday) = explode("-", $startdate);
		$view->month    = $startmonth;
		$view->day      = $startday;
		$view->year     = $startyear;
	}
	if ($startdate == "")
	{
		if ($params->get("relative", "rel") == "abs")
		{
			$startdate = $params->get("absstart", "");
			list($startyear, $startmonth, $startday) = explode("-", $startdate);
		}
		else if ($params->get("relative", "rel") == "strtotime")
		{
			$value     = $params->get("strstart", "");
			$value     = new JevDate(JevDate::strtotime($value));
			$startdate = $value->toFormat("%Y-%m-%d");
		}
		else
		{
			$value = $params->get("relstart", "");
			// order is important since "day" has a y in it which would then be matched! 
			$value     = str_replace(",", " ", $value);
			$value     = str_replace("y", "year", $value);
			$value     = str_replace("d", "day", $value);
			$value     = str_replace("w", "week", $value);
			$value     = str_replace("m", "month", $value);
			$value     = new JevDate($value);
			$startdate = $value->toFormat("%Y-%m-%d");
		}
	}
	if ($enddate == "")
	{
		if ($params->get("relative", "rel") == "abs")
		{
			$enddate = $params->get("absend", "");
		}
		else if ($params->get("relative", "rel") == "strtotime")
		{
			$value   = $params->get("strend", "");
			$value   = new JevDate(JevDate::strtotime($value));
			$enddate = $value->toFormat("%Y-%m-%d");
		}
		else
		{
			$value = $params->get("relend", "");
			// order is important since "day" has a y in it which would then be matched! 
			$value   = str_replace(",", " ", $value);
			$value   = str_replace("y", "year", $value);
			$value   = str_replace("d", "day", $value);
			$value   = str_replace("w", "week", $value);
			$value   = str_replace("m", "month", $value);
			$value   = new JevDate($value);
			$enddate = $value->toFormat("%Y-%m-%d");
		}
	}
	if ($enddate < $startdate)
	{
		// default to 1 year when input dates are not valid!
		$value = new JevDate($startdate);
		$value->add(new DateInterval('P1Y'));
		$enddate = $value->toFormat("%Y-%m-%d");
	}

	$usedates = $params->get('usedates', 'both');

	return array($startdate, $enddate);

}

