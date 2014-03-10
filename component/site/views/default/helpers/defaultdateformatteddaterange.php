<?php

defined('_JEXEC') or die('Restricted access');

function DefaultdateFormattedDateRange($view)
{
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	if ($params->get("daterangeformat", "") == "")
	{
		$return = JEventsHTML::getDateFormat($view->startyear, $view->startmonth, $view->startday, 1)
				. "&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;"
				. JEventsHTML::getDateFormat($view->endyear, $view->endmonth, $view->endday, 1);
	}
	else {
		$return = $params->get("daterangeformat", "");
		$startmatches= array();
		preg_match_all('|{START(.*?)}|', $return , $startmatches);
		if (count($startmatches)==2 && count($startmatches[1])==1){
			$replace = str_replace(array("(",")"), "", $startmatches[1][0]);
			$datestp =  JevDate::mktime(0, 0, 0, $view->startmonth, $view->startday, $view->startyear);
			$replace =JEV_CommonFunctions::jev_strftime($replace, $datestp);
			$return = str_replace($startmatches[0][0],  $replace,  $return);
		}
		$endmatches= array();
		preg_match_all('|{END(.*?)}|', $return, $endmatches);
		if (count($endmatches)==2 && count($endmatches[1])==1){
			$replace = str_replace(array("(",")"), "", $endmatches[1][0]);
			$datestp =  JevDate::mktime(0, 0, 0, $view->endmonth, $view->endday, $view->endyear);
			$replace =JEV_CommonFunctions::jev_strftime($replace, $datestp);
			$return = str_replace($endmatches[0][0],  $replace,  $return);
		}
		//$return = $params->get("daterangeformat", "");
	}
	return $return;

}
