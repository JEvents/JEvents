<?php
/**
* @copyright	Copyright (C) 2015-2017 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/

function ProcessJsonRequest(&$requestObject, $returnData){

	//$file4 = JPATH_SITE . '/components/com_jevents/libraries/checkconflict.php';
	//if (JFile::exists($file4)) JFile::delete($file4);

        // Some SEF addons leave Itemid blank here so force the active menu!
	$ttItemid = 	JRequest::getVar("ttItemid", 0);
	if ($ttItemid>0 && JRequest::getVar("Itemid", 0)==0){
		$menu = JFactory::getApplication()->getMenu();
		JRequest::setVar("Itemid", $ttItemid);
		$menu->setActive($ttItemid);
	}
    
	$returnData->allclear = 1;

	ini_set("display_errors", 0);

	$lang = JFactory::getLanguage();
	$lang->load("com_jevents", JPATH_SITE);
	$lang->load("com_jevents", JPATH_ADMINISTRATOR);

	include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");

	$params = JComponentHelper::getParams("com_jevents");

	if (!$params->get("checkconflicts", 0))
		return $returnData;

	// Do we ignore overlaps
	if (JEVHelper::isEventDeletor(true) && isset($requestObject->formdata->overlapoverride) && $requestObject->formdata->overlapoverride==1){
		return $returnData;
	}
	
	// Enforce referrer
	if (!$params->get("skipreferrer", 0))
	{
		if (!array_key_exists("HTTP_REFERER", $_SERVER))
		{
			PlgSystemGwejson::throwerror("There was an error - no referrer info available");
		}

		$live_site = $_SERVER['HTTP_HOST'];
		$ref_parts = parse_url($_SERVER["HTTP_REFERER"]);

		if (!isset($ref_parts["host"]) || ($ref_parts["host"] . (isset($ref_parts["port"]) ? ':' . $ref_parts["port"] : '')) != $live_site)
		{
			PlgSystemGwejson::throwerror("There was an error - missing host in referrer");
		}
	}

	if ($params->get("icaltimezonelive", "") != "" && is_callable("date_default_timezone_set") && $params->get("icaltimezonelive", "") != "")
	{
		$timezone = date_default_timezone_get();
		$tz = $params->get("icaltimezonelive", "");
		date_default_timezone_set($tz);
		$registry = JRegistry::getInstance("jevents");
		$registry->set("jevents.timezone", $timezone);
	}

	$token = JSession::getFormToken();
	if (!isset($requestObject->token) || strcmp($requestObject->token, $token)!==0)
	{
		PlgSystemGwejson::throwerror("There was an error - bad token.  Please refresh the page and try again.");
	}

	$user = JFactory::getUser();
	if (!JEVHelper::isEventCreator())
	{
		PlgSystemGwejson::throwerror("There was an error - not an event creator");
	}

	if (intval($requestObject->formdata->evid) > 0)
	{
		$db = JFactory::getDBO();
		$dataModel = new JEventsDataModel("JEventsAdminDBModel");
		$queryModel = new JEventsDBModel($dataModel);
		$event = $queryModel->getEventById(intval($requestObject->formdata->evid), 1, "icaldb");
		//$db->setQuery("SELECT * FROM #__jevents_vevent where ev_id=".intval($requestObject->formdata->evid));
		//	$event = $db->loadObject();
		if (!$event || (!JEVHelper::canEditEvent($event) ))
		{
			PlgSystemGwejson::throwerror("There was an error - cannot edit this event");
		}
	}

	$returnData->overlaps = array();
	if ($requestObject->pressbutton == "icalrepeat.apply" || $requestObject->pressbutton == "icalrepeat.save")
	{
		$testrepeat = simulateSaveRepeat($requestObject);

		// now we have out event and its repetitions we now check to see for overlapping events
		$overlaps = checkRepeatOverlaps($testrepeat, $returnData, intval($requestObject->formdata->evid), $requestObject);
	}
	else
	{
		$testevent = simulateSaveEvent($requestObject);

		// now we have out event and its repetitions we now check to see for overlapping events
		$overlaps = checkEventOverlaps($testevent, $returnData, intval($requestObject->formdata->evid), $requestObject);
	}


	if (count($overlaps) > 0)
	{
		$returnData->allclear = 0;
		foreach ($overlaps as $olp)
		{
			$overlap = new stdClass();
			$overlap->event_id = $olp->eventid;
			$overlap->eventdetail_id = $olp->eventdetail_id;
			$overlap->summary = $olp->summary;
			$overlap->rp_id = $olp->rp_id;
			$overlap->startrepeat = $olp->startrepeat;
			$overlap->endrepeat = $olp->endrepeat;

			list($y, $m, $d, $h, $m, $d) = sscanf($olp->startrepeat, "%d-%d-%d %d:%d:%d");

			$tstring = JText::_("JEV_OVERLAP_MESSAGE");
			$overlap->conflictMessage = sprintf($tstring, $olp->summary, JEV_CommonFunctions::jev_strftime(JText::_("DATE_FORMAT_4"), JevDate::strtotime($olp->startrepeat)), JEV_CommonFunctions::jev_strftime(JText::_("DATE_FORMAT_4"), JevDate::strtotime($olp->endrepeat)), $olp->conflictCause);
			$overlap->conflictMessage = addslashes($overlap->conflictMessage);
			$overlap->url = JURI::root() . "index.php?option=com_jevents&task=icalrepeat.detail&evid=" . $olp->rp_id . "&year=$y&month=$m&day=$d";
			$overlap->url = str_replace("components/com_jevents/libraries/", "", $overlap->url);
			$returnData->overlaps[] = $overlap;
		}
	}


	if ($requestObject->error)
	{
		$returnData->allclear = 0;
		return "Error";
	}

	return $returnData;

}

function simulateSaveEvent($requestObject)
{

	if (!JEVHelper::isEventCreator())
	{
		PlgSystemGwejson::throwerror(JText::_('ALERTNOTAUTH'));
	}

	// Convert formdata to array
	$formdata = array();
	foreach (get_object_vars($requestObject->formdata) as $k => $v)
	{
		$k = str_replace("[]", "", $k);
		$formdata[$k] = $v;
	}
	
	// If the allow HTML flag is set, apply a safe HTML filter to the variable
	//	$array = JRequest::_cleanVar($formdata, JREQUEST_ALLOWHTML);
	$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
	$array = $safeHtmlFilter->clean($formdata, null);
		

	$dataModel = new JEventsDataModel("JEventsAdminDBModel");
	$queryModel = new JEventsDBModel($dataModel);

	$rrule = SaveIcalEvent::generateRRule($array);

	// ensure authorised
	if (isset($array["evid"]) && $array["evid"] > 0)
	{
		$event = $queryModel->getEventById(intval($array["evid"]), 1, "icaldb");
		if (!JEVHelper::canEditEvent($event))
		{
			PlgSystemGwejson::throwerror(JText::_('ALERTNOTAUTH'));
		}
	}
	$row = false;

	// do dry run of event saving!
	ob_start();
	$event = SaveIcalEvent::save($array, $queryModel, $rrule, true);
	ob_end_clean();
	if ($event)
	{

		$row = new jIcalEventDB($event);
		$row->repetitions = $event->_repetitions;
		if (is_array($row->_catid)){
			$row->_catids = $row->_catid;
			$row->_catid = $row->_catid[0];
		}
	}
	else
	{
		PlgSystemGwejson::throwerror(JText::_('EVENT_NOT_SAVED'));
	}


	return $row;

}

function simulateSaveRepeat($requestObject)
{
	include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");

	if (!JEVHelper::isEventCreator())
	{
		PlgSystemGwejson::throwerror(JText::_('ALERTNOTAUTH'));
	}

	// Convert formdata to array
	$formdata = array();
	foreach (get_object_vars($requestObject->formdata) as $k => $v)
	{
		$k = str_replace("[]", "", $k);
		$formdata[$k] = $v;
	}
	//$array = JRequest::_cleanVar($formdata, JREQUEST_ALLOWHTML);
	$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
	$array = $safeHtmlFilter->clean($formdata, null);

	if (!array_key_exists("rp_id", $array) || intval($array["rp_id"]) <= 0)
	{
		PlgSystemGwejson::throwerror(JText::_("Not a repeat", true));
	}

	$rp_id = intval($array["rp_id"]);

	$dataModel = new JEventsDataModel("JEventsAdminDBModel");
	$queryModel = new JEventsDBModel($dataModel);

	// I should be able to do this in one operation but that can come later
	$event = $queryModel->listEventsById(intval($rp_id), 1, "icaldb");
	if (!JEVHelper::canEditEvent($event))
	{
		PlgSystemGwejson::throwerror(JText::_('ALERTNOTAUTH'));
	}

	$db = JFactory::getDBO();
	$rpt = new iCalRepetition($db);
	$rpt->load($rp_id);

	$query = "SELECT detail_id FROM #__jevents_vevent WHERE ev_id=$rpt->eventid";
	$db->setQuery($query);
	$eventdetailid = $db->loadResult();

	$data["UID"] = valueIfExists($array, "uid", md5(uniqid(rand(), true)));

	$data["X-EXTRAINFO"] = valueIfExists($array, "extra_info", "");
	$data["LOCATION"] = valueIfExists($array, "location", "");
	$data["allDayEvent"] = valueIfExists($array, "allDayEvent", "off");
	$data["CONTACT"] = valueIfExists($array, "contact_info", "");
	// allow raw HTML (mask =2)
	$data["DESCRIPTION"] = valueIfExists($array, "jevcontent", "", 'request', 'html', 2);
	$data["publish_down"] = valueIfExists($array, "publish_down", "2006-12-12");
	$data["publish_up"] = valueIfExists($array, "publish_up", "2006-12-12");
	
	if (isset($array["publish_down2"]) && $array["publish_down2"]){
		$data["publish_down"] = $array["publish_down2"];
	}
	if (isset($array["publish_up2"]) && $array["publish_up2"]){
		$data["publish_up"] = $array["publish_up2"];
	}
	
	$interval = valueIfExists($array, "rinterval", 1);
	$data["SUMMARY"] = valueIfExists($array, "title", "");

	$data["MULTIDAY"] = intval(valueIfExists($array, "multiday", "1"));
	$data["NOENDTIME"] = intval(valueIfExists($array, "noendtime", 0));

	$ics_id = valueIfExists($array, "ics_id", 0);

	if ($data["allDayEvent"] == "on")
	{
		$start_time = "00:00";
	}
	else
		$start_time = valueIfExists($array, "start_time", "08:00");
	$publishstart = $data["publish_up"] . ' ' . $start_time . ':00';
	$data["DTSTART"] = JevDate::strtotime($publishstart);

	if ($data["allDayEvent"] == "on")
	{
		$end_time = "23:59";
		$publishend = $data["publish_down"] . ' ' . $end_time . ':59';
	}
	else
	{
		$end_time = valueIfExists($array, "end_time", "15:00");
		$publishend = $data["publish_down"] . ' ' . $end_time . ':00';
	}

	$data["DTEND"] = JevDate::strtotime($publishend);
	// iCal for whole day uses 00:00:00 on the next day JEvents uses 23:59:59 on the same day
	list ($h, $m, $s) = explode(":", $end_time . ':00');
	if (($h + $m + $s) == 0 && $data["allDayEvent"] == "on" && $data["DTEND"] > $data["DTSTART"])
	{
		$publishend = JevDate::strftime('%Y-%m-%d 23:59:59', ($data["DTEND"] - 86400));
		$data["DTEND"] = JevDate::strtotime($publishend);
	}

	$data["X-COLOR"] = valueIfExists($array, "color", "");

	// Add any custom fields into $data array
	foreach ($array as $key => $value)
	{
		if (strpos($key, "custom_") === 0)
		{
			$data[$key] = $value;
		}
	}

	// populate rpt with data
	$start = $data["DTSTART"];
	$end = $data["DTEND"];
	$rpt->startrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S', $start);
	$rpt->endrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S', $end);

	$rpt->duplicatecheck = md5($rpt->eventid . $start);
	$rpt->rp_id = $rp_id;

	$rpt->event = $event;
	return $rpt;

}

function valueIfExists($array, $key, $default)
{
	if (!array_key_exists($key, $array))
		return $default;
	return $array[$key];

}

function checkEventOverlaps($testevent, & $returnData, $eventid, $requestObject)
{
	$params = JComponentHelper::getParams("com_jevents");
	$db = JFactory::getDBO();
	$overlaps = array();



	if ( $params->get("checkconflicts", 0)==2 )
	{
		foreach ($testevent->repetitions as $repeat)
		{

			$sql = "SELECT *, ev.state FROM #__jevents_repetition as rpt ";
			$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
			$sql .= " LEFT JOIN #__jevents_vevent as ev ON ev.ev_id=rpt.eventid ";
			$sql .= " WHERE rpt.eventid<>" . intval($eventid) . " AND rpt.startrepeat<" . $db->Quote($repeat->endrepeat) . " AND rpt.endrepeat>" . $db->Quote($repeat->startrepeat);
			$sql .= " AND ev.state=1";
			$sql .= " LIMIT 100";
			$db->setQuery($sql);
			$conflicts = $db->loadObjectList();
			if ($conflicts && count($conflicts) > 0)
			{
				foreach ($conflicts as &$conflict)
				{
					$conflict->conflictCause = JText::_("JEV_GENERAL_OVERLAP");
				}
				unset($conflict);
				$overlaps = array_merge($overlaps, $conflicts);
			}
		}
	}
	else if ( ($params->get("checkconflicts", 0)==1) )
	{
		$dataModel = new JEventsDataModel();
		$dbModel = new JEventsDBModel($dataModel);

		// First of all check for Category overlaps
		$catids = $testevent->catids() ? $testevent->catids() : array($testevent->catid());
		$skipCatTest = false;
		$catinfo = $dbModel->getCategoryInfo( $catids );
		if ($catinfo && count($catinfo) >0)
		{
			foreach ($catids as $c => $specificCatid){
				if (isset($catinfo[$catids[$c]])){
					$cinfo = $catinfo[$catids[$c]];
					$catparams = json_decode($cinfo->params);
					if (!$catparams->overlaps)
					{
						unset($catids[$c]);
					}
				}
			}
			if (count($catids)==0){
				$skipCatTest = true;
			}
		}
		else {
			$skipCatTest = true;
		}
		
		if (!$skipCatTest)
		{
			foreach ($testevent->repetitions as $repeat)
			{

				$sql = "SELECT *, evt.catid , evt.state";
				if ($params->get("multicategory", 0))
				{
					$sql .= ", GROUP_CONCAT(DISTINCT catmap.catid SEPARATOR ',') as catids";
				}
				$sql .= " FROM #__jevents_repetition as rpt ";
				$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
				$sql .= " LEFT JOIN #__jevents_vevent as evt ON evt.ev_id=rpt.eventid ";

				if ($params->get("multicategory", 0))
				{
					$sql .= " LEFT JOIN #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
					$sql .= " LEFT JOIN #__categories AS catmapcat ON catmap.catid = catmapcat.id";
				}

				$sql .= " WHERE rpt.eventid<>" . intval($eventid) . " AND rpt.startrepeat<" . $db->Quote($repeat->endrepeat) . " AND rpt.endrepeat>" . $db->Quote($repeat->startrepeat);
				$sql .= " AND evt.state=1";
				if ($params->get("multicategory", 0))
				{
					 $sql .= " AND  catmap.catid IN(" . implode(",",$catids) . ") GROUP BY rpt.rp_id";
					
				}
				else {
					$sql .= " AND (evt.catid=" . $testevent->catid() . ") GROUP BY rpt.rp_id";
				}

				$sql .= " LIMIT 100";
				$db->setQuery($sql);
				$conflicts = $db->loadObjectList();
				if ($conflicts && count($conflicts) > 0)
				{
					foreach ($conflicts as &$conflict)
					{
						$conflictCats = isset($conflict->catids) ? explode(",",$conflict->catids) : array($conflict->catid);
						$catname = array();
						foreach ($conflictCats as $cc){
							if (isset($catinfo[$cc])){
								$catname[] = $catinfo[$cc]->title;
							}
						}
						$cat = count($catname)>0 ? implode(", ",$catname) : $testevent->getCategoryName();
						$conflict->conflictCause = JText::sprintf("JEV_CATEGORY_CLASH", $cat);
					}
					unset($conflict);
					$overlaps = array_merge($overlaps, $conflicts);
				}
			}
		}
		
		// Next check for Calendar overlaps
		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__jevents_icsfile WHERE ics_id = ".$testevent->icsid());		
		$calinfo = $db->loadObject();
		if ($calinfo && intval($calinfo->overlaps)==1)
		{
			foreach ($testevent->repetitions as $repeat)
			{
				$sql = "SELECT *, evt.state FROM #__jevents_repetition as rpt ";
				$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
				$sql .= " LEFT JOIN #__jevents_vevent as evt ON evt.ev_id=rpt.eventid ";
				$sql .= " WHERE rpt.eventid<>" . intval($eventid) . " AND rpt.startrepeat<" . $db->Quote($repeat->endrepeat) . " AND rpt.endrepeat>" . $db->Quote($repeat->startrepeat);
				$sql .= " AND evt.state=1";
				$sql .= " AND evt.icsid=" . $testevent->icsid() . " GROUP BY rpt.rp_id";
				$sql .= " LIMIT 100";
				$db->setQuery($sql);
				$conflicts = $db->loadObjectList();
				if ($conflicts && count($conflicts) > 0)
				{
					foreach ($conflicts as &$conflict)
					{
						$conflict->conflictCause = JText::sprintf("JEV_CALENDAR_CLASH", $calinfo->label);
					}
					unset($conflict);
					$overlaps = array_merge($overlaps, $conflicts);
				}
			}
		}
		
	}

	$dispatcher = JEventDispatcher::getInstance();
	$dispatcher->trigger('onCheckEventOverlaps', array(&$testevent, &$overlaps, $eventid, $requestObject));

	return $overlaps;

}

function checkRepeatOverlaps($repeat, & $returnData, $eventid, $requestObject)
{
	$params = JComponentHelper::getParams("com_jevents");
	$db = JFactory::getDBO();
	$overlaps = array();
	if ( $params->get("checkconflicts", 0) == 2 )
	{
		$sql = "SELECT *, ev.state  FROM #__jevents_repetition as rpt ";
		$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
		$sql .= " LEFT JOIN #__jevents_vevent as ev ON ev.ev_id=rpt.eventid ";
		$sql .= " WHERE rpt.rp_id<>" . intval($repeat->rp_id) . " AND rpt.startrepeat<" . $db->Quote($repeat->endrepeat) . " AND rpt.endrepeat>" . $db->Quote($repeat->startrepeat);
		$sql .= " AND ev.state=1";
		$sql .= " LIMIT 100";

		$db->setQuery($sql);
		$conflicts = $db->loadObjectList();
		if ($conflicts && count($conflicts) > 0)
		{
			foreach ($conflicts as &$conflict)
			{
				$conflict->conflictCause = JText::_("JEV_GENERAL_OVERLAP");
			}
			unset($conflict);
			$overlaps = array_merge($overlaps, $conflicts);
		}
	}
	else if ( $params->get("checkconflicts", 0) == 1 )
	{
		$dataModel = new JEventsDataModel();
		$dbModel = new JEventsDBModel($dataModel);

		$catids =$repeat->event->catids() ;
		if (!$catids){
			$catids = array($repeat->event->catid());
		}
		
		$skipCatTest = false;
		$catinfo = $dbModel->getCategoryInfo($catids);
		if ($catinfo && count($catinfo) >0)
		{
			for ($c=0;$c<count($catids);$c++){
				if (isset($catinfo[$catids[$c]])){
					$cinfo = $catinfo[$catids[$c]];
					$catparams = json_decode($cinfo->params);
					if (!$catparams->overlaps)
					{
						unset($catids[$c]);
					}
				}
			}
			if (count($catids)==0){
				$skipCatTest = true;
			}
		}
		else {
			$skipCatTest = true;
		}

		if (!$skipCatTest)
		{
			$sql = "SELECT *, evt.catid, evt.state ";
			if ($params->get("multicategory", 0))
			{
				$sql .= ", GROUP_CONCAT(DISTINCT catmap.catid SEPARATOR ',') as catids";
			}
			$sql .= " FROM #__jevents_repetition as rpt ";
			$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
			$sql .= " LEFT JOIN #__jevents_vevent as evt ON evt.ev_id=rpt.eventid ";
			if ($params->get("multicategory", 0))
			{
				$sql .= " LEFT JOIN #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
				$sql .= " LEFT JOIN #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			}
			$sql .= " WHERE rpt.rp_id<>" . intval($repeat->rp_id) . " AND rpt.startrepeat<" . $db->Quote($repeat->endrepeat) . " AND rpt.endrepeat>" . $db->Quote($repeat->startrepeat);
			$sql .= " AND evt.state=1";
			if ($params->get("multicategory", 0))
			{
				$sql .= " AND  catmap.catid IN(" . implode(",",$catids) . ") GROUP BY rpt.rp_id";
			}
			else {
				$sql .= " AND (evt.catid=" . $repeat->event->catid() . ") GROUP BY rpt.rp_id";
			}
			$sql .= " LIMIT 100";
			
			$db->setQuery($sql);
			$conflicts = $db->loadObjectList();
			if ($conflicts && count($conflicts) > 0)
			{
				foreach ($conflicts as &$conflict)
				{
					$conflictCats = isset($conflict->catids) ? explode(",",$conflict->catids) : array($conflict->catid);
					$catname = array();
					foreach ($conflictCats as $cc){
						if (isset($catinfo[$cc])){
							$catname[] = $catinfo[$cc]->title;
						}
					}
					//TODO $testevent is not set? We need to look at actually setting it as it is pointless at present.
					$cat = count($catname)>0 ? implode(", ",$catname) : $testevent->getCategoryName();
					$conflict->conflictCause = JText::sprintf("JEV_CATEGORY_CLASH", $cat);
				}
				unset($conflict);
				$overlaps = array_merge($overlaps, $conflicts);
			}
		}
	}

	$dispatcher = JEventDispatcher::getInstance();
	$dispatcher->trigger('onCheckRepeatOverlaps', array(&$repeat, &$overlaps, $eventid, $requestObject));

	return $overlaps;

}