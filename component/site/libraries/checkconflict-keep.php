<?php
/**
* @copyright	Copyright (C) 2008 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/
ini_set("display_errors",0);

require 'jsonwrapper.php';

list($usec, $sec) = explode(" ", microtime());
define('_SC_START', ((float)$usec + (float)$sec));

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__).DS ."..".DS. "..".DS. "..".DS) ;
if (!file_exists($x.DS."configuration.php") && isset($_SERVER['SCRIPT_FILENAME'])){
	$x = str_replace(DS.'components'.DS.'com_jevents'.DS.'libraries'.DS.'checkconflict.php','',$_SERVER['SCRIPT_FILENAME']);
}
define( 'JPATH_BASE', $x );

// create the mainframe object
$_REQUEST['tmpl'] = 'component';

// Create JSON data structure
$data = new stdClass();
$data->error = 0;
$data->result = "ERROR";
$data->user = "";

// Enforce referrer
if (!array_key_exists("HTTP_REFERER",$_SERVER) ){
	throwerror("There was an error");
}

$live_site = $_SERVER['HTTP_HOST'];
$ref_parts = parse_url($_SERVER["HTTP_REFERER"]);

if (!isset($ref_parts["host"]) || $ref_parts["host"] != $live_site ){
	throwerror("There was an error - missing host in referrer");
}

// Get JSON data
if (!array_key_exists("json",$_REQUEST)){
	throwerror("There was an error - no request data");
}
else {
	$requestData = $_REQUEST["json"];

	if (isset($requestData)){
		try {
			if (ini_get("magic_quotes_gpc")){
				$requestData= stripslashes($requestData);
			}

			$requestObject = json_decode($requestData, 0);
			if (!$requestObject){
				$requestObject = json_decode(utf8_encode($requestData), 0);
			}
		}
		catch (Exception $e) {
			throwerror("There was an exception");
		}

		if (!$requestObject){
			file_put_contents(dirname(__FILE__)."/error.txt", var_export($requestData,true));
			throwerror("There was an error - no request object ");
		}
		else if ($requestObject->error){
			throwerror("There was an error - Request object error ".$requestObject->error);
		}
		else {

			try {
				$data = ProcessRequest($requestObject, $data);
			}
			catch (Exception $e){
				throwerror("There was an exception ".$e->getMessage());
			}
		}
	}
	else {
		throwerror("Invalid Input");
	}
}

header("Content-Type: application/x-javascript; charset=utf-8");

list ($usec,$sec) = explode(" ", microtime());
$time_end = (float)$usec + (float)$sec;
$data->timing = round($time_end - _SC_START,4);

// Must suppress any error messages
@ob_end_clean();
echo json_encode($data);

function ProcessRequest(&$requestObject, $returnData){

	define("REQUESTOBJECT",serialize($requestObject));
	define("RETURNDATA",serialize($returnData));

	require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
	require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

	$requestObject = unserialize(REQUESTOBJECT);
	$returnData = unserialize(RETURNDATA);
	$returnData->allclear=1;

	ini_set("display_errors",0);

	global $option;
	$client = "site";
	if (isset($requestObject->client) && in_array($requestObject->client,array("site","administrator"))){
		$client = $requestObject->client;
	}
	$mainframe = JFactory::getApplication($client);
	JFactory::getApplication()->initialise();
	$option = "com_jevents";
	// Not sure why this is needed but it is if (use use $mainframe =& JFactory::getApplication($client); )!!!
	//$GLOBALS['mainframe']=$mainframe;

	$params =& JComponentHelper::getParams( "com_jevents" );
	if (!$params->get("checkclashes",0) && !$params->get("noclashes",0))  return $returnData;

	if ($params->get("icaltimezonelive","")!="" && is_callable("date_default_timezone_set")){
		$timezone= date_default_timezone_get();
		date_default_timezone_set($params->get("icaltimezonelive",""));
	}

	$token = JUtility::getToken();
	if (!isset($requestObject->token)  || $requestObject->token!=$token){
		throwerror("There was an error - bad token.  Please refresh the page and try again.");
	}

	$user = JFactory::getUser();
	if ($user->id==0){
		throwerror("There was an error");
	}

	if (intval($requestObject->formdata->evid)>0){
		$db=JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__jevents_vevent where ev_id=".intval($requestObject->formdata->evid));
		$event = $db->loadObject();
		if (!$event || ($event->created_by!=$user->id && $user->usertype!="Super Administrator")){
			throwerror("There was an error");
		}
	}

	$returnData->overlaps = array();
	if ($requestObject->pressbutton=="icalrepeat.apply" || $requestObject->pressbutton=="icalrepeat.save"){
		$testrepeat = simulateSaveRepeat($requestObject);

		// now we have out event and its repetitions we now check to see for overlapping events
		$overlaps = checkRepeatOverlaps($testrepeat, $returnData, intval($requestObject->formdata->evid));


	}
	else {
		$testevent = simulateSaveEvent($requestObject);

		// now we have out event and its repetitions we now check to see for overlapping events
		$overlaps = checkEventOverlaps($testevent, $returnData, intval($requestObject->formdata->evid));

	}

	if (count($overlaps)>0) {
		$returnData->allclear=0;
		foreach ($overlaps as $olp){
			$overlap = new stdClass();
			$overlap->event_id = $olp->eventid;
			$overlap->eventdetail_id = $olp->eventdetail_id;
			$overlap->summary = $olp->summary;
			$overlap->rp_id = $olp->rp_id;
			$overlap->startrepeat = $olp->startrepeat;			
			$overlap->endrepeat = $olp->endrepeat;			
			
			list($y, $m, $d, $h, $m, $d) = sscanf($olp->startrepeat, "%d-%d-%d %d:%d:%d");
			
			$overlap->url = JURI::root()."index.php?option=com_jevents&task=icalrepeat.detail&evid=".$olp->rp_id."&year=$y&month=$m&day=$d";
			$overlap->url = str_replace("components/com_jevents/libraries/","",$overlap->url);
			$returnData->overlaps[] = $overlap;
		}
	}


	if ($requestObject->error){
		return "Error";
		$returnData->allclear=0;
	}

	return $returnData;

}



function throwerror ($msg){
	$data = new stdClass();
	//"document.getElementById('products').innerHTML='There was an error - no valid argument'");
	$data->error = "alert('".$msg."')";
	$data->result = "ERROR";
	$data->user = "";

	header("Content-Type: application/x-javascript");
	require 'jsonwrapper.php';
	// Must suppress any error messages
	@ob_end_clean();
	echo json_encode($data);
	exit();
}


function simulateSaveEvent($requestObject){
	include_once(JPATH_SITE."/components/com_jevents/jevents.defines.php");

	if (!JEVHelper::isEventCreator()){
		throwerror(JText::_( 'ALERTNOTAUTH' ) );
	}

	// Convert formdata to array
	$formdata = array();
	foreach (get_object_vars($requestObject->formdata) as $k=>$v){
		$k = str_replace("[]","",$k);
		$formdata[$k] = $v;
	}
	$array = JRequest::_cleanVar($formdata, JREQUEST_ALLOWHTML);
	
	
		$rrule = SaveIcalEvent::generateRRule($array);

		// ensure authorised
		if (isset($array["evid"]) &&  $array["evid"]>0){
			$event = $this->queryModel->getEventById( intval($array["evid"]), 1, "icaldb" );
			if (!JEVHelper::canEditEvent($event)){
				JError::raiseError( 403, JText::_( 'ALERTNOTAUTH' ) );
			}
		}

		$clearout = false;
		// remove all exceptions since they are no longer needed
		if (isset($array["evid"]) &&  $array["evid"]>0){
			$clearout = true;
		}

		if ($event = SaveIcalEvent::save($array, $this->queryModel, $rrule)){

			$row = new jIcalEventDB($event);
			if (JEVHelper::canPublishEvent($row)){
				$msg = JText::_("Event Saved", true);
			}
			else {
				$msg = JText::_("Event Saved Under Review", true);
			}
			if ($clearout) {
				$db = JFactory::getDBO();
				$query = "DELETE FROM #__jevents_exception WHERE eventid = ".$array["evid"];
				$db->setQuery( $query);
				$db->query();
				// TODO clear out old exception details
			}
		}
		else {
			$msg = JText::_("Event Not Saved", true);
		}
	

}

function simulateSaveRepeat($requestObject){
	include_once(JPATH_SITE."/components/com_jevents/jevents.defines.php");

	if (!JEVHelper::isEventCreator()){
		throwerror(JText::_( 'ALERTNOTAUTH' ) );
	}

	// Convert formdata to array
	$formdata = array();
	foreach (get_object_vars($requestObject->formdata) as $k=>$v){
		$k = str_replace("[]","",$k);
		$formdata[$k] = $v;
	}
	$array = JRequest::_cleanVar($formdata, JREQUEST_ALLOWHTML);

	if (!array_key_exists("rp_id",$array) || intval($array["rp_id"])<=0){
		throwerror( JText::_("Not a repeat", true));
	}

	$rp_id = intval($array["rp_id"]);

	$dataModel = new JEventsDataModel("JEventsAdminDBModel");
	$queryModel = new JEventsDBModel($dataModel);

	// I should be able to do this in one operation but that can come later
	$event = $queryModel->listEventsById( intval($rp_id), 1, "icaldb" );
	if (!JEVHelper::canEditEvent($event)){
		throwerror(JText::_( 'ALERTNOTAUTH' ) );
	}

	$db	=& JFactory::getDBO();
	$rpt = new iCalRepetition($db);
	$rpt->load($rp_id);

	$query = "SELECT detail_id FROM #__jevents_vevent WHERE ev_id=$rpt->eventid";
	$db->setQuery( $query);
	$eventdetailid = $db->loadResult();

	$data["UID"]				= valueIfExists($array,  "uid",md5(uniqid(rand(),true)));

	$data["X-EXTRAINFO"]	= valueIfExists($array,  "extra_info","");
	$data["LOCATION"]		= valueIfExists($array,  "location","");
	$data["allDayEvent"]	= valueIfExists($array,  "allDayEvent","off");
	$data["CONTACT"]		= valueIfExists($array,  "contact_info","");
	// allow raw HTML (mask =2)
	$data["DESCRIPTION"]	= valueIfExists($array,  "jevcontent","", 'request',  'html', 2);
	$data["publish_down"]	= valueIfExists($array,  "publish_down","2006-12-12");
	$data["publish_up"]		= valueIfExists($array,  "publish_up","2006-12-12");
	$interval 				= valueIfExists($array,  "rinterval",1);
	$data["SUMMARY"]		= valueIfExists($array,  "title","");

	$data["MULTIDAY"]		=  intval(valueIfExists($array, "multiday","1"));
	$data["NOENDTIME"]		= intval(valueIfExists($array,  "noendtime",0));

	$ics_id					= valueIfExists($array,  "ics_id",0);

	if ($data["allDayEvent"]=="on"){
		$start_time="00:00";
	}
	else $start_time			=valueIfExists($array,  "start_time","08:00");
	$publishstart		= $data["publish_up"] . ' ' . $start_time . ':00';
	$data["DTSTART"]	= JevDate::strtotime( $publishstart );

	if ($data["allDayEvent"]=="on"){
		$end_time="23:59";
		$publishend		= $data["publish_down"] . ' ' . $end_time . ':59';
	}
	else {
		$end_time 			= valueIfExists($array,  "end_time","15:00");
		$publishend		= $data["publish_down"] . ' ' . $end_time . ':00';
	}

	$data["DTEND"]		= JevDate::strtotime( $publishend );
	// iCal for whole day uses 00:00:00 on the next day JEvents uses 23:59:59 on the same day
	list ($h,$m,$s) = explode(":",$end_time . ':00');
	if (($h+$m+$s)==0 && $data["allDayEvent"]=="on" && $data["DTEND"]>$data["DTSTART"]) {
		$publishend = JevDate::strftime('%Y-%m-%d 23:59:59',($data["DTEND"]-86400));
		$data["DTEND"]		= JevDate::strtotime( $publishend );
	}

	$data["X-COLOR"]	= valueIfExists($array,   "color","");

	// Add any custom fields into $data array
	foreach ($array as $key=>$value) {
		if (strpos($key,"custom_")===0){
			$data[$key]=$value;
		}
	}

	// populate rpt with data
	$start = $data["DTSTART"];
	$end = $data["DTEND"];
	$rpt->startrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S',$start);
	$rpt->endrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S',$end);

	$rpt->duplicatecheck = md5($rpt->eventid . $start );
	$rpt->rp_id = $rp_id;

	$rpt->event = $event;
	return $rpt;

}

function valueIfExists($array, $key,$default){
	if (!array_key_exists($key,$array))	return $default;
	return $array[$key];
}

function checkEventOverlaps($testevent, & $returnData, $eventid) {
	$params =& JComponentHelper::getParams( "com_jevents" );
	$db = JFactory::getDBO();
	$overlaps = array();
	if ($params->get("noclashes",0)) {
		foreach ($testevent->repetitions as $repeat){
			$x = 1;
			$sql =  "SELECT * FROM #__jevents_repetition as rpt ";
			$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
			$sql .= " WHERE rpt.eventid<>".intval($eventid)." AND rpt.startrepeat<=".$db->Quote($repeat->endrepeat)." AND rpt.endrepeat>=".$db->Quote($repeat->startrepeat);
			$db->setQuery($sql);
			$conflicts = $db->loadObjectList();
			if ($conflicts && count($conflicts)>0){
				$overlaps = array_merge($overlaps, $conflicts);
			}
		}
	}
	else if ($params->get("checkclashes",0)) {
		foreach ($testevent->repetitions as $repeat){
			$x = 1;
			$sql =  "SELECT * FROM #__jevents_repetition as rpt ";
			$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
			$sql .= " LEFT JOIN #__jevents_vevent as evt ON evt.ev_id=rpt.eventid ";
			$sql .= " WHERE rpt.eventid<>".intval($eventid)." AND rpt.startrepeat<=".$db->Quote($repeat->endrepeat)." AND rpt.endrepeat>=".$db->Quote($repeat->startrepeat);
			$sql .= " AND (evt.catid=".$repeat->event->catid()." OR evt.icsid=".$repeat->event->icsid().") GROUP BY rpt.rp_id";
			$db->setQuery($sql);
			$conflicts = $db->loadObjectList();
			if ($conflicts && count($conflicts)>0){
				$overlaps = array_merge($overlaps, $conflicts);
			}
		}
	}

	return $overlaps;

}

function checkRepeatOverlaps($repeat, & $returnData, $eventid) {
	$params =& JComponentHelper::getParams( "com_jevents" );
	$db = JFactory::getDBO();
	$overlaps = array();
	if ($params->get("noclashes",0)) {
		$sql =  "SELECT * FROM #__jevents_repetition as rpt ";
		$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
		$sql .= " WHERE rpt.rp_id<>".intval($repeat->rp_id)." AND rpt.startrepeat<=".$db->Quote($repeat->endrepeat)." AND rpt.endrepeat>=".$db->Quote($repeat->startrepeat);

		$db->setQuery($sql);
		$conflicts = $db->loadObjectList();
		if ($conflicts && count($conflicts)>0){
			$overlaps = array_merge($overlaps, $conflicts);
		}
	}
	else if ($params->get("checkclashes",0)) {
		$sql =  "SELECT * FROM #__jevents_repetition as rpt ";
		$sql .= " LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id=rpt.eventdetail_id ";
		$sql .= " LEFT JOIN #__jevents_vevent as evt ON evt.ev_id=rpt.eventid ";
		$sql .= " WHERE rpt.rp_id<>".intval($repeat->rp_id)." AND rpt.startrepeat<=".$db->Quote($repeat->endrepeat)." AND rpt.endrepeat>=".$db->Quote($repeat->startrepeat);
		$sql .= " AND (evt.catid=".$repeat->event->catid()." OR evt.icsid=".$repeat->event->icsid().") GROUP BY rpt.rp_id";
		$db->setQuery($sql);
		$conflicts = $db->loadObjectList();
		if ($conflicts && count($conflicts)>0){
			$overlaps = array_merge($overlaps, $conflicts);
		}		
	}
	return $overlaps;

}