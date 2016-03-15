<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: saveIcalEvent.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class SaveIcalEvent {

	// we can use dry run to create the event data without saving it!
	public static function save($array, &$queryModel, $rrule, $dryrun = false){

		$cfg = JEVConfig::getInstance();
		$db	= JFactory::getDBO();
		$user = JFactory::getUser();

		// Allow plugins to check data validity
		$dispatcher     = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin("jevents");
		$res = $dispatcher->trigger( 'onBeforeSaveEvent' , array(&$array, &$rrule, $dryrun));

		// TODO do error and hack checks here
		$ev_id = intval(JArrayHelper::getValue( $array,  "evid",0));
		$newevent = $ev_id==0;

		$data = array();

		// TODO add UID to edit form
		$data["UID"]				= JArrayHelper::getValue( $array,  "uid",md5(uniqid(rand(),true)));

		$data["X-EXTRAINFO"]	= JArrayHelper::getValue( $array,  "extra_info","");
		$data["LOCATION"]		= JArrayHelper::getValue( $array,  "location","");
		$data["allDayEvent"]	= JArrayHelper::getValue( $array,  "allDayEvent","off");
		// Joomla 3.2 fix !!  The form doesn't respect the checkbox value in the form xml file being "on" instead of 1
		if ($data["allDayEvent"]==1)
		{
			$data["allDayEvent"]="on";
		}
		$data["CONTACT"]		= JArrayHelper::getValue( $array,  "contact_info","");
		$data["DESCRIPTION"]	= JArrayHelper::getValue( $array,  "jevcontent","");
		$data["publish_down"]	= JArrayHelper::getValue( $array,  "publish_down","2006-12-12");
		$data["publish_up"]		= JArrayHelper::getValue( $array,  "publish_up","2006-12-12");
		$data["publish_down2"]	= JArrayHelper::getValue( $array,  "publish_down2",false);
		$data["publish_up2"]		= JArrayHelper::getValue( $array,  "publish_up2",false);
		if ($data["publish_down2"]){
			$data["publish_down"] = $data["publish_down2"];
		}
		if ($data["publish_up2"]){
			$data["publish_up"] = $data["publish_up2"];
		}
		$data["SUMMARY"]		= JArrayHelper::getValue( $array,  "title","");
		$data["URL"]	= JArrayHelper::getValue( $array,  "url","");

		// If user is jevents can deleteall or has backend access then allow them to specify the creator
		$jevuser	= JEVHelper::getAuthorisedUser();
		$creatorid = JRequest::getInt("jev_creatorid",0);
		if ( $creatorid>0){
			$access = $user->authorise('core.admin', 'com_jevents');
		
			if (($jevuser && $jevuser->candeleteall) || $access) {
				$data["X-CREATEDBY"]	= $creatorid;
			}

		}

		$ics_id				= JArrayHelper::getValue( $array,  "ics_id",0);

		if ($data["allDayEvent"]=="on"){
			$start_time="00:00";
		}
		else $start_time			= JArrayHelper::getValue( $array,  "start_time","08:00");
		$publishstart		= $data["publish_up"] . ' ' . $start_time . ':00';
		$data["DTSTART"]	= JevDate::strtotime( $publishstart );

		if ($data["allDayEvent"]=="on"){
			$end_time="00:00";
		}
		else $end_time 			= JArrayHelper::getValue( $array,  "end_time","15:00");
		$publishend		= $data["publish_down"] . ' ' . $end_time . ':00';

		if (isset($array["noendtime"]) && $array["noendtime"]){
			$publishend		= $data["publish_down"] . ' 23:59:59';
		}

		$data["DTEND"]		= JevDate::strtotime( $publishend );
		// iCal for whole day uses 00:00:00 on the next day JEvents uses 23:59:59 on the same day
		list ($h,$m,$s) = explode(":",$end_time . ':00');
		if (($h+$m+$s)==0 && $data["allDayEvent"]=="on" && $data["DTEND"]>=$data["DTSTART"]) {
			$publishend = JevDate::strftime('%Y-%m-%d 23:59:59',($data["DTEND"]));
			$data["DTEND"]		= JevDate::strtotime( $publishend );
		}

		$data["RRULE"]	= $rrule;

		$data["MULTIDAY"]	= JArrayHelper::getValue( $array,  "multiday","1");
		$data["NOENDTIME"]	= JArrayHelper::getValue( $array,  "noendtime","0");
		$data["X-COLOR"]	= JArrayHelper::getValue( $array,  "color","");

		$data["LOCKEVENT"]	= JArrayHelper::getValue( $array,  "lockevent","0");

		// Add any custom fields into $data array
		foreach ($array as $key=>$value) {
			if (strpos($key,"custom_")===0){
				$data[$key]=$value;
			}
			// convert jform data to data format used before
			if (strpos($key,"jform")===0 && is_array($value)){
				foreach ($value as $cfkey => $cfvalue) {
					$data["custom_".$cfkey]=$cfvalue;
				}
			}
		}

		
		$vevent = iCalEvent::iCalEventFromData($data);

		$vevent->catid = JArrayHelper::getValue( $array,  "catid",0);
		if (is_array($vevent->catid)){
			  JArrayHelper::toInteger($vevent->catid);
		}
		// if catid is empty then use the catid of the ical calendar
		if ((is_string($vevent->catid) && $vevent->catid<=0) || (is_array($vevent->catid) && count($vevent->catid)==0)){
			$query = "SELECT catid FROM #__jevents_icsfile WHERE ics_id=$ics_id";
			$db->setQuery( $query);
			$vevent->catid = $db->loadResult();
		}
		// minimum access is 1 in Joomla 2.5+
		$vevent->access = intval(JArrayHelper::getValue( $array,  "access",1));

		$vevent->state =  intval(JArrayHelper::getValue( $array,  "state",0));
		// Shouldn't really do this like this
		$vevent->_detail->priority =  intval(JArrayHelper::getValue( $array,  "priority",0));

		// FRONT END AUTO PUBLISHING CODE
		$frontendPublish = JEVHelper::isEventPublisher();

		if (!$frontendPublish){
			$frontendPublish = JEVHelper::canPublishOwnEvents($ev_id);
		}
		// Always unpublish if no Publisher otherwise publish automatically (for new events)
		// Should we always notify of new events
		$notifyAdmin = $cfg->get("com_notifyallevents",0);
		if (!$frontendPublish){
			$vevent->state = 0;
			// In this case we send a notification email to admin
			$notifyAdmin = true;
		}

		$vevent->icsid = $ics_id;
		if ($ev_id>0){
			$vevent->ev_id=$ev_id;
		}

		$rp_id = intval(JArrayHelper::getValue( $array,  "rp_id",0));
		if ($rp_id>0){
			// I should be able to do this in one operation but that can come later
			$testevent = $queryModel->listEventsById( intval($rp_id), 1, "icaldb" );
			if (!JEVHelper::canEditEvent($testevent)){
				throw new Exception( JText::_('ALERTNOTAUTH'), 403);
				return false;
			}
		}

		$db = JFactory::getDBO();
		$success = true;
		//echo "class = ".get_class($vevent);
		if (!$dryrun){
			if (!$vevent->store()){
				echo $db->getErrorMsg()."<br/>";
				$success = false;
				JError::raiseWarning(101,JText::_( 'COULD_NOT_SAVE_EVENT_' ));
			}
		}
		else {
			// need a value for eventid to pretend we have saved the event so we can get the repetitions
			if (!isset($vevent->ev_id)){
				$vevent->ev_id = 0;
			}
			$vevent->rrule->eventid = $vevent->ev_id;
		}
		
		// Only update the repetitions if the event edit says the reptitions will have changed or a new event or ONLY 1 repetition
		$repetitions = $vevent->getRepetitions(true);
		if ($newevent || JRequest::getInt("updaterepeats",1) || count($repetitions)==1){			
			if (!$dryrun){
				if (!$vevent->storeRepetitions()){
					echo $db->getErrorMsg()."<br/>";
					$success = false;
					JError::raiseWarning(101,JText::_( 'COULD_NOT_SAVE_REPETITIONS' ));
				}
			}
		}
		
		// whilst the DB field is called 'state' we use the variable 'published' in all of JEvents so must set it before the plugin
		$vevent->published =  $vevent->state ;
		$res = $dispatcher->trigger( 'onAfterSaveEvent' , array(&$vevent, $dryrun));
		if ($dryrun) return $vevent;

		
		// If not authorised to publish in the frontend then notify the administrator
		if (!$dryrun && $success && $notifyAdmin && !JFactory::getApplication()->isAdmin()){

			JLoader::register('JEventsCategory',JEV_ADMINPATH."/libraries/categoryClass.php");
			$cat = new JEventsCategory($db);
			$cat->load($vevent->catid);
			$adminuser = $cat->getAdminUser();

			$adminEmail	= $adminuser->email;
			$config = new JConfig();
			$sitename =  $config->sitename;
			$subject	= JText::_('JEV_MAIL_ADDED') . ' ' . $sitename;
			$subject	= ($vevent->state == '1') ? JText::_('COM_JEV_INFO') . $subject : JText::_('COM_JEV_APPROVAL') . $subject;
			$Itemid = JEVHelper::getItemid();
			// reload the event to get the reptition ids
			$evid = intval($vevent->ev_id);
			$testevent = $queryModel->getEventById( $evid, 1, "icaldb" );

			list($year,$month,$day) = JEVHelper::getYMD();
			//http://joomlacode1.5svn/index.php?option=com_jevents&task=icalevent.edit&evid=1&Itemid=68&rp_id=72&year=2008&month=09&day=10&lang=cy
			$uri  = JURI::getInstance(JURI::base());
			$root = $uri->toString( array('scheme', 'host', 'port') );

			if ($testevent){
				$rp_id = $testevent->rp_id();
				$modifylink = '<a href="' . $root . JRoute::_( 'index.php?option=' .JEV_COM_COMPONENT . '&task=icalevent.edit&evid='.$evid.'&rp_id='.$rp_id. '&Itemid=' . $Itemid."&year=$year&month=$month&day=$day" ) . '"><b>' . JText::_('JEV_MODIFY') . '</b></a>' . "\n";
				$viewlink = '<a href="' . $root . JRoute::_( 'index.php?option=' .JEV_COM_COMPONENT . '&task=icalrepeat.detail&evid='.$rp_id. '&Itemid=' . $Itemid."&year=$year&month=$month&day=$day&login=1" ) . '"><b>' . JText::_('JEV_VIEW') . '</b></a>' . "\n";
				$title = $testevent->title();
				$content = $testevent->content();
				$catids = $testevent->catids() ? $testevent->catids() : array();
				if (($key = array_search($testevent->catid() ,  $catids)) !== false) {
					unset($catids[$key]);
				}
				$cc = "";
				if (count($catids)>0){
					$ccs = array();
					foreach ($catids as $catid){
						$cat->load($catid);
						$catadminuser = $cat->getAdminUser();
						if ($catadminuser->email!=$adminEmail && !in_array($catadminuser->email, $ccs)){
							$ccs[]= $catadminuser->email;
						}
					}
					if (count($ccs)>0){
						$cc = implode(",",$ccs);
					}
				}
			}
			else {
				$modifylink = '<a href="' . $root . JRoute::_( 'index.php?option=' .JEV_COM_COMPONENT . '&task=icalevent.edit&evid='.$evid. '&Itemid=' . $Itemid."&year=$year&month=$month&day=$day" ) . '"><b>' . JText::_('JEV_MODIFY') . '</b></a>' . "\n";
				$viewlink = '<a href="' . $root . JRoute::_( 'index.php?option=' .JEV_COM_COMPONENT . '&task=icalevent.detail&evid='.$evid. '&Itemid=' . $Itemid."&year=$year&month=$month&day=$day&login=1" ) . '"><b>' . JText::_('JEV_VIEW') . '</b></a>' . "\n";
				$title = $data["SUMMARY"];
				$content = $data["DESCRIPTION"]	;
				$subject .= " PROBLEMS SAVING THIS EVENT";
				$cc = "";
			}

			$created_by = $user->name;
			if ($created_by==null) {
				$created_by= "Anonymous";
				if (JRequest::getString("custom_anonusername","")!=""){
					$created_by=JRequest::getString("custom_anonusername","")." (".JRequest::getString("custom_anonemail","").")";
				}
			}

			JEV_CommonFunctions::sendAdminMail( $sitename, $adminEmail, $subject, $title, $content, $day, $month, $year, $start_time, $end_time, $created_by, JURI::root(), $modifylink, $viewlink , $testevent, $cc);

		}
		if ($success){
			return $vevent;
		}
		return $success;
}

public static function generateRRule($array){
	//static $weekdayMap=array("SU"=>0,"MO"=>1,"TU"=>2,"WE"=>3,"TH"=>4,"FR"=>5,"SA"=>6);
	static $weekdayReverseMap=array("SU","MO","TU","WE","TH","FR","SA");

	$interval 	= JArrayHelper::getValue( $array,  "rinterval",1);

	$freq = JArrayHelper::getValue( $array,  "freq","NONE");
	if ($freq!="NONE") {
		$rrule = array();
		$rrule["FREQ"]	= $freq;
		$countuntil		= JArrayHelper::getValue( $array,  "countuntil","count");
		if ($countuntil=="count" ){
			$count 			= intval(JArrayHelper::getValue( $array,  "count",1));
			if ($count<=0) $count=1;
			$rrule["COUNT"] = $count;
		}
		else {
			$publish_down	= JArrayHelper::getValue( $array,  "publish_down","2006-12-12");
			$until			= JArrayHelper::getValue( $array,  "until", $publish_down);
			$until2		= JArrayHelper::getValue( $array,  "until2" , false);
			if ($until2){
				$until = $until2;
			}
			$rrule["UNTIL"] = JevDate::strtotime($until." 00:00:00");
			
		}
		$rrule["INTERVAL"] = $interval;
		$rrule["IRREGULARDATES"] =  JArrayHelper::getValue( $array,  "irregularDates",array(),"ARRAY");
		array_walk($rrule["IRREGULARDATES"], function(& $item, $index ){
			$item = JevDate::strtotime($item." 00:00:00");
			});
	}

	$whichby			= JArrayHelper::getValue( $array,  "whichby","bd");

	switch ($whichby){
		case "byd":
			$byd_direction		= JArrayHelper::getValue( $array,  "byd_direction","off")=="off"?"+":"-";
			$byyearday 			= JArrayHelper::getValue( $array,  "byyearday","");
			$rrule["BYYEARDAY"] = $byd_direction.$byyearday;
			break;
		case "bm":
			$bm_direction		= JArrayHelper::getValue( $array,  "bm_direction","off")=="off"?"+":"-";
			$bymonth			= JArrayHelper::getValue( $array,  "bymonth","");
			$rrule["BYMONTH"] 	= $bymonth;
			break;
		case "bwn":
			$bwn_direction		= JArrayHelper::getValue( $array,  "bwn_direction","off")=="off"?"+":"-";
			$byweekno			= JArrayHelper::getValue( $array,  "byweekno","");
			$rrule["BYWEEKNO"] 	= $bwn_direction.$byweekno;
			break;
		case "bmd":
			$bmd_direction		= JArrayHelper::getValue( $array,  "bmd_direction","off")=="off"?"+":"-";
			$bymonthday			= JArrayHelper::getValue( $array,  "bymonthday","");
			$rrule["BYMONTHDAY"]= $bmd_direction.$bymonthday;
			break;
		case "bd":
			$bd_direction		= JArrayHelper::getValue( $array,  "bd_direction","off")=="off"?"+":"-";
			$weekdays			= JArrayHelper::getValue( $array,  "weekdays",array());
			$weeknums			= JArrayHelper::getValue( $array,  "weeknums",array());
			$byday		= "";
			if (count($weeknums)==0){
				// special case for weekly repeats which don't specify eeek of a month
				foreach ($weekdays as $wd) {
					if (JString::strlen($byday)>0) $byday.=",";
					$byday .= $weekdayReverseMap[$wd];
				}
			}
			foreach ($weeknums as $week){
				foreach ($weekdays as $wd) {
					if (JString::strlen($byday)>0) $byday.=",";
					$byday .= $bd_direction.$week.$weekdayReverseMap[$wd];
				}
			}
			$rrule["BYDAY"] = $byday;
			break;
	}
	return $rrule;
}
}
