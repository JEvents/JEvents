<?php 
defined('_JEXEC') or die('Restricted access');

function Defaultgetstartenddates($view){

	$params = JComponentHelper::getParams( JEV_COM_COMPONENT );

	$startdate = JRequest::getString("startdate","");
	$enddate = JRequest::getString("enddate","");

	if ($startdate==""){
		if ($params->get("relative","rel")=="abs"){
			$startdate = $params->get("absstart","");
			list($startyear,$startmonth,$startday)=explode("-",$startdate);
		}
		else if ($params->get("relative","rel")=="strtotime"){
			$value = $params->get("strstart","");
			$value = new JevDate(JevDate::strtotime($value));
			$startdate = $value->toFormat("%Y-%m-%d");
		}
		else {
			$value = $params->get("relstart","");
			$value = str_replace(","," ",$value);
			$value = str_replace("y","year",$value);
			$value = str_replace("d","day",$value);
			$value = str_replace("w","week",$value);
			$value = str_replace("m","month",$value);
			$value = new JevDate($value);
			$startdate = $value->toFormat("%Y-%m-%d");
		}
	}
	if ($enddate==""){
		if ($params->get("relative","rel")=="abs"){
			$enddate = $params->get("absend","");
		}
		else if ($params->get("relative","rel")=="strtotime"){
			$value = $params->get("strend","");
			$value = new JevDate(JevDate::strtotime($value));
			$enddate = $value->toFormat("%Y-%m-%d");
		}
		else {
			$value = $params->get("relend","");
			$value = str_replace(","," ",$value);
			$value = str_replace("y","year",$value);
			$value = str_replace("d","day",$value);
			$value = str_replace("w","week",$value);
			$value = str_replace("m","month",$value);
			$value = new JevDate($value);
			$enddate = $value->toFormat("%Y-%m-%d");
		}
	}
	return array($startdate, $enddate);

}

