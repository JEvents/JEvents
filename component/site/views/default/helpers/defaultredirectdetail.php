<?php

defined('_JEXEC') or die('Restricted access');

function DefaultRedirectDetail($view)
{

	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	$redirect_to_event = $params->get("redirect_detail", 0);

	if ($redirect_to_event == 1)
	{
		$activeEvents = array();
		$countevents = count($view->data['hours']['timeless']['events']);
		if ($countevents >1) return;
		if ($countevents==1){
			$activeEvents=$view->data['hours']['timeless']['events'];
		}
		
		for ($h = 0; $h < 24; $h++)
		{
			$countevents += count($view->data['hours'][$h]['events']);		
			if ($countevents >1) return;
			if ($countevents==1 && count($activeEvents)==0){
				$activeEvents=$view->data['hours'][$h]['events'];
			}
		}

		if ($countevents == 1)
		{
			$row = $activeEvents[0];
			$rowlink = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), false);
			$rowlink = JRoute::_($rowlink . $view->datamodel->getCatidsOutLink());
			$rowlink = str_replace("&", "&", $rowlink);
			JFactory::getApplication()->redirect($rowlink);
		}
	}

}