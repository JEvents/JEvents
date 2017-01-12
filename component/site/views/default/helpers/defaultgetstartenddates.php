<?php 
defined('_JEXEC') or die('Restricted access');

function Defaultgetstartenddates($view){

	$params = JComponentHelper::getParams( JEV_COM_COMPONENT );

	// fix to allow start/end date to be preserved during pagination IF filter module before/after dates are used
	$Itemid = JRequest::getInt("Itemid",0);
	// This causes the filter module to reset
	$filters = jevFilterProcessing::getInstance(array());
	$activeFilterMenu = JFactory::getApplication()->getUserState( 'active_filter_menu ',$Itemid);
	if (intval(JRequest::getVar('filter_reset',0)) || ($activeFilterMenu>0 && $activeFilterMenu!=$Itemid)){
                // if actively filtering then do not reset
		if (!JRequest::getString("startdate",0) || intval(JRequest::getVar('filter_reset',0))) {
                    JRequest::setVar( 'startdate', '');
        	    JFactory::getApplication()->setUserState( 'range_startdate'.$Itemid, '');
                }
		if (!JRequest::getString("enddate",0) || intval(JRequest::getVar('filter_reset',0))) {
                    JRequest::setVar( 'enddate', '');
                    JFactory::getApplication()->setUserState( 'range_enddate'.$Itemid, '');
                }
		JFactory::getApplication()->setUserState( 'active_filter_menu ', 0);
	}

	$startdate = JFactory::getApplication()->getUserStateFromRequest( 'range_startdate'.$Itemid, 'startdate', JRequest::getString("startdate"));
	$enddate = JFactory::getApplication()->getUserStateFromRequest( 'range_enddate'.$Itemid, 'enddate', JRequest::getString("enddate"));

        if ($startdate!=""){
            // WE have specified a start date in the URL so we should use it!
            list($startyear,$startmonth,$startday)=explode("-",$startdate);
            $view->assign("month",$startmonth);
            $view->assign("day",$startday);
            $view->assign("year",$startyear);        
        }
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
                        // order is important since "day" has a y in it which would then be matched! 
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
                        // order is important since "day" has a y in it which would then be matched! 
			$value = str_replace(","," ",$value);
			$value = str_replace("y","year",$value);
			$value = str_replace("d","day",$value);
			$value = str_replace("w","week",$value);
			$value = str_replace("m","month",$value);
			$value = new JevDate($value);
			$enddate = $value->toFormat("%Y-%m-%d");
		}
	}
	if ($enddate < $startdate) {
		// default to 1 year when input dates are not valid!
		$value = new JevDate($startdate);
		$value->add(new DateInterval('P1Y'));
		$enddate = $value->toFormat("%Y-%m-%d");
	}
	return array($startdate, $enddate);

}

