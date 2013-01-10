<?php

defined('_JEXEC') or die('Restricted access');

function DefaultRedirectDetail($view)
{

	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	$redirect_to_event = $params->get("redirect_detail", 0);

	if ($redirect_to_event == 1)
	{
		$view->data  = $data = $view->datamodel->getDayData($view->year, $view->month, $view->day);
		$activeEvents = array();
		$countevents = count($data['hours']['timeless']['events']);

		for ($h = 0; $h < 24; $h++)
		{
			$countevents += count($data['hours'][$h]['events']);		
			if ($countevents>0){
				$activeEvents=array_merge($activeEvents,$data['hours'][$h]['events']);
			}
		}

		if (count($activeEvents) == 1)
		{
			$row = $activeEvents[0];
			$rowlink = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), false);
			$rowlink = JRoute::_($rowlink . $view->datamodel->getCatidsOutLink());
			$rowlink = str_replace("&", "&", $rowlink);
			JFactory::getApplication()->redirect($rowlink);
		}
	}

}