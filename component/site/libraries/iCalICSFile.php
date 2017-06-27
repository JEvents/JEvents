<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: iCalICSFile.php 3474 2012-04-03 13:40:53Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\String\StringHelper;

class iCalICSFile extends JTable  {

	/** @var int Primary key */
	var $ics_id					= null;
	var $filename = "";
	var $srcURL = "";
	var $state = 1;
	var $access = 0;
	var $catid = 0;
	var $label ="";
	var $created;
	var $refreshed;
	// is default ical of its type
	var $isdefault = 0;

	var $overlaps = 0;

	var $created_by = 0;

	// if true allows for front end refresh via cronjob
	var $autorefresh = 0;

	// if true imports ignore embedded cateogry info
	var $ignoreembedcat= 0;

	var $icaltype;
	/**
	 * This holds the raw data as an array 
	 *
	 * @var array
	 */
	var $data;
	var $rrule = null;

	var $vevent;

	/**
	 * Null Constructor
	 */
	public function __construct( &$db ) {
		parent::__construct( '#__jevents_icsfile', 'ics_id', $db );
		$this->access = intval(JEVHelper::getBaseAccess());
	}

	public function _setup($icsid,$catid,$access=0,$state=1, $autorefresh=0, $ignoreembedcat=0){
		if ($icsid>0) $this->ics_id = $icsid;
		$this->created = date( 'Y-m-d H:i:s' );
		$this->refreshed = $this->created;
		$this->catid = $catid;
		$this->access = $access;
		$this->state = $state;
		$this->autorefresh = $autorefresh;
		$this->ignoreembedcat = $ignoreembedcat;
	}

	public function editICalendar($icsid,$catid,$access=0,$state=1, $label=""){
		$db	= JFactory::getDBO();
		$temp = new iCalICSFile($db);
		$temp->_setup($icsid,$catid,$access,$state);
		$temp->filename="_from_scratch_";
		$temp->icaltype=2;
		$temp->label = empty($label) ? 'Scratch-'.md5(JevDate::mktime()) : $label;
		$temp->srcURL ="";

		$rawText = <<<RAWTEXT
BEGIN:VCALENDAR
PRODID:-//JEvents Project//JEvents Calendar 1.5.0//EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-CALNAME:$label
X-WR-TIMEZONE:Europe/London
BEGIN:VTIMEZONE
TZID:Europe/London
X-LIC-LOCATION:Europe/London
BEGIN:DAYLIGHT
TZOFFSETFROM:+0000
TZOFFSETTO:+0100
TZNAME:BST
DTSTART:19700329T010000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0100
TZOFFSETTO:+0000
TZNAME:GMT
DTSTART:19701025T020000
RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU
END:STANDARD
END:VTIMEZONE
END:VCALENDAR
		
RAWTEXT;
		$temp->_icalInfo =& JEVHelper::iCalInstance("", $rawText);
		return $temp;
	}

	public static function newICSFileFromURL($uploadURL,$icsid,$catid,$access=0,$state=1, $label="", $autorefresh=0, $ignoreembedcat=0){
		$db	= JFactory::getDBO();
		$temp = new iCalICSFile($db);
		$temp->_setup($icsid,$catid,$access,$state,$autorefresh,$ignoreembedcat);
		if ($access==0){
			$temp->access = intval(JEVHelper::getBaseAccess());
		}
		if (false !== stripos($uploadURL, 'webcal://')){
			$headers = @get_headers( $uploadURL);
			if ($headers && $headers[0] == 'HTTP/1.1 200 OK') {
				$uploadURL = $uploadURL;
			}
			else {
				$headers = get_headers( str_replace('webcal://', 'https://', $uploadURL));
				if ($headers && $headers[0] == 'HTTP/1.1 200 OK') {
					$uploadURL = str_replace('webcal://', 'https://', $uploadURL);
				}
				else {
					$uploadURL = str_replace('webcal://', 'http://', $uploadURL);
				}
			}
		}
		/*
				$urlParts = parse_url($uploadURL);

				$pathParts = pathinfo($urlParts['path']);

				if (isset($pathParts['basename'])) $temp->filename =  $pathParts['basename'];
				else $temp->filename = $uploadURL;
		*/
		$temp->filename = 'Remote-' . md5($uploadURL);
		$temp->icaltype=0;  // i.e. from URL

		if ($label!="") $temp->label = $label;
		else $temp->label = $temp->filename;

		$temp->srcURL =  $uploadURL;

		// Store the ical in the registry so we can retrieve the access level
		$registry = JRegistry::getInstance("jevents");
		$registry->set("jevents.icsfile", $temp);

		if (false === ($temp->_icalInfo = JEVHelper::iCalInstance($uploadURL)) ) {
			return false;
		}

		return $temp;
	}

	public static function newICSFileFromFile($file,$icsid,$catid,$access=0,$state=1, $label="", $autorefresh=0, $ignoreembedcat=0){
		$db	= JFactory::getDBO();
		$temp = new iCalICSFile($db);
		$temp->_setup($icsid,$catid,$access,$state,$autorefresh,$ignoreembedcat);
		if ($access==0){
			$temp->access = intval(JEVHelper::getBaseAccess());
		}		
		$temp->srcURL = "";
		$temp->filename = $file['name'];
		$temp->icaltype=1;  // i.e. from file

		if ($label!="") $temp->label = $label;
		else $temp->label = $temp->filename;

		if (false === ($temp->_icalInfo =& JEVHelper::iCalInstance($file['tmp_name']))) {
			return false;
		}

		return $temp;
	}

	/**
	 * Used to create Ical from raw strring
	 */
	public function newICSFileFromString($rawtext,$icsid,$catid,$access=0,$state=1, $label="", $autorefresh=0, $ignoreembedcat=0){
		$db	= JFactory::getDBO();
		$temp = null;
		$temp = new iCalICSFile($db);
		if ($icsid>0){
			$temp->load($icsid);
			$temp->icaltype=2;  // i.e. from file
		}
		else {
			$temp->_setup($icsid,$catid,$access,$state,$autorefresh,$ignoreembedcat);
			$temp->srcURL = "";
			$temp->filename = "_from_events_cat".$catid;
			$temp->icaltype=2;  // i.e. from file
			if ($label!="") $temp->label = $label;
			else $temp->label = $temp->filename;
		}


		$temp->_icalInfo =& JEVHelper::iCalInstance("",$rawtext);

		return $temp;
	}

	/**
	 * Method that updates details about the ical but does not touch the events contained
	 *
	 */
	public function updateDetails(){
		if (parent::store() && $this->isdefault==1 && $this->icaltype==2){
			// set all the others to 0
			$db	= JFactory::getDBO();
			$sql = "UPDATE #__jevents_icsfile SET isdefault=0 WHERE icaltype=2 AND ics_id<>".$this->ics_id;
			$db->setQuery($sql);
			$db->execute();
		}
	}

	/**
	 * override store function to return id to pass to iCalEvent and store the events too!
	 *
	 * @param int $catid - forced category for the underlying events
	 */
	public function store($catid=false , $cleanup=true , $flush =true) {

		$user = JFactory::getUser();
		$guest = $user->get('guest');

		@ini_set("memory_limit","256M");
		@ini_set("max_execution_time","300");
		
		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);
		
		static $categories;
		if (is_null($categories)){
			$sql = "SELECT * FROM #__categories WHERE extension='com_jevents'";
			$db	= JFactory::getDBO();
			$db->setQuery($sql);
			$categories = $db->loadObjectList('title');
		}

		if (!$catid){
			$catid = $this->catid;
		}
		if ($id = $this->isDuplicate()){
			$this->ics_id = $id;
			// TODO return warning about duplicate file name  VERY IMPORTANT TO DECIDE WHAT TO DO
			// UIDs for the vcalendar itself are not compulsory
		}
		// There is a better way to find
		// duplicate key info trap repeated insertions - I should
		if (!parent::store()){
			echo "failed to store icsFile<br/>";
		}
		else if ($this->isdefault==1 && $this->icaltype==2){
			// set all the others to 0
			$db	= JFactory::getDBO();
			$sql = "UPDATE #__jevents_icsfile SET isdefault=0 WHERE icaltype=2 AND ics_id<>".$this->ics_id;
			$db->setQuery($sql);
			$db->execute();
		}

		// find the full set of ids currently in the calendar so taht we can remove cancelled ones
		$db	= JFactory::getDBO();
		$sql = "SELECT ev_id, uid, lockevent FROM #__jevents_vevent WHERE icsid=".$this->ics_id ;//. " AND catid=".$catid;
		$db->setQuery($sql);
		$existingevents = $db->loadObjectList('ev_id');

		// insert the data - this will need to deal with multiple rrule values
		foreach ($this->_icalInfo->vevents as & $vevent) {

			if (!$vevent->isCancelled() && !$vevent->isRecurrence()){
				// if existing category then use it
				if (!$this->ignoreembedcat && JString::strlen($vevent->_detail->categories)>0){
					$evcat = explode(",",$vevent->_detail->categories);
					if (count($evcat)>0) {
						include_once(JEV_ADMINLIBS."categoryClass.php");
						foreach ($evcat as $ct){
							// if no such category then create it/them
							if(!array_key_exists(trim($ct),$categories)){
								$cat = new JEventsCategory($db);
								$cat->bind(array("title"=>trim($ct)));
								$cat->published=1;
								$cat->check();
								if (!$cat->store()){
									//var_dump($cat->getErrors());
									die(JText::plural('COM_JEVENTS_MANAGE_CALENDARS_ICAL_IMPORT_FAILED_TO_CREATE_CAT', $ct));
								}
							}
						}
						// must reset  the list of categories now
						$sql = "SELECT * FROM #__categories WHERE extension='com_jevents'";
						$db->setQuery($sql);
						$categories = $db->loadObjectList('title');

						$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
						if ($params->get("multicategory",0) ){
							$vevent->catid = array();
							foreach ($evcat as $ct){
								$vevent->catid[] =  $categories[trim($ct)]->id;
							}							
						}
						else {
							$vevent->catid =  $categories[trim($evcat[0])]->id;
						}
					}
				}
				else {
					$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
					if ($params->get("multicategory",0) ){
						$vevent->catid = array($catid);
					}
					else {
						$vevent->catid = $catid;
					}
				}
				// These now gets picked up in the event
				//$vevent->access = $this->access;
				//$vevent->state =  $this->state;
				$vevent->icsid = $this->ics_id;
				// The refreshed field is used to track dropped events on reload
				$vevent->refreshed = $this->refreshed;
				// make sure I don't add the same events more than once
				if ($matchingEvent = $vevent->matchingEventDetails()){
					$vevent->ev_id = $matchingEvent->ev_id;
					$vevent->_detail->evdet_id = $matchingEvent->evdet_id;
					
					unset($existingevents[$vevent->ev_id]);
				}

				// if the event is locked then skip this row
				if ($matchingEvent && $matchingEvent->lockevent){
					$vevent->lockevent = 1;
					continue;
				}
				else {
					$vevent->lockevent = 0;
				}

				// handle events running over midnight
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$icalmultiday = $params->get("icalmultiday",0);
				$icalmultiday24h = $params->get("icalmultiday24h",0);
				// These booleans are the wrong way around.
				$vevent->_detail->multiday = $icalmultiday?0:1;
				if ($vevent->_detail->dtend-$vevent->_detail->dtstart < 86400) {
					$vevent->_detail->multiday = $icalmultiday24h?0:1;
				}

				// force creator if appropriate
				if ($this->created_by>0){
					$vevent->created_by = $this->created_by;
					// force override of creator
					$vevent->store(false, true);
				}
				else {
					$vevent->store();
				}

				// trigger post save plugins e.g. AutoTweet
				$dispatcher     = JEventDispatcher::getInstance();
				JPluginHelper::importPlugin("jevents");
				if ($matchingEvent) {
					JRequest::setVar("evid", $vevent->ev_id);
				}
				else {
					JRequest::setVar("evid", 0);
				}

				$repetitions = $vevent->getRepetitions(true);
				$vevent->storeRepetitions();

				if (isset($vevent->state) && !isset($vevent->published)) {
					$vevent->published =  $vevent->state ;
				}
				// not a dry run of course!
				$res = $dispatcher->trigger( 'onAfterSaveEvent' , array(&$vevent, false));

				// Save memory by clearing out the repetitions we no longer need
				$repetitions = null;
				$vevent->_repetitions = null;
				//$vevent=null;
				//echo "Event Data read in<br/>";
				//echo "memory = ".memory_get_usage()." ".memory_get_usage(true)."<br/>";
				if ($flush){
					ob_flush();
					flush();
				}
			}
		}
		unset($vevent);

		// Having stored all the repetitions - remove the cancelled instances
		// this should be done as a batch but for now I'll do them one at a time
		foreach ($this->_icalInfo->vevents as $vevent) {
			// if the event is locked then skip this row
			if (!is_null($vevent) && $vevent->lockevent) continue;

			if (!is_null($vevent) && ($vevent->isCancelled() || $vevent->isRecurrence())){
				// if existing category then use it
				if (JString::strlen($vevent->_detail->categories)>0){
					if (count($evcat)>0) {
						include_once(JEV_ADMINLIBS."categoryClass.php");
						foreach ($evcat as $ct){
							// if no such category then create it/them
							if(!array_key_exists($ct,$categories)){
								$cat = new JEventsCategory($db);
								$cat->bind(array("title"=>$ct));
								$cat->published=1;
								$cat->check();
								$cat->store();
							}
						}
						// must reset  the list of categories now
						$sql = "SELECT * FROM #__categories WHERE extension='com_jevents'";
						$db->setQuery($sql);
						$categories = $db->loadObjectList('title');

						$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
						if ($params->get("multicategory",0) && count($evcat)>1){
							$vevent->catid = array();
							foreach ($evcat as $ct){
								$vevent->catid[] =  $categories[$ct]->id;
							}							
						}
						else {
							$vevent->catid =  $categories[$evcat[0]]->id;							
						}
					}
				}
				else {
					$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
					if ($params->get("multicategory",0) ){
						$vevent->catid = array($catid);
					}
					else {
						$vevent->catid = $catid;
					}
				}
				$vevent->access = $this->access;
				$vevent->state =  $this->state;
				$vevent->icsid = $this->ics_id;
				// make sure I don't add the same events more than once
				if ($matchingEvent = $vevent->matchingEventDetails()){
					$vevent->ev_id = $matchingEvent->ev_id;
				}
				if ($vevent->isCancelled()) {
					$vevent->cancelRepetition();
				}
				else {
					// replace event that is only 'adjusted' with the correct settings
					$vevent->adjustRepetition($matchingEvent);
				}
			}
		}

		// Now remove existing events that have been deleted
		if ($cleanup){
			if(count($existingevents)>0){								
				$todelete = array();
				foreach ($existingevents as $event) {
					$todelete[]= $event->ev_id;
				}
				$veventidstring = implode(",",$todelete);

				$query = "SELECT DISTINCT (eventdetail_id) FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
				$db->setQuery( $query);
				$detailids = $db->loadColumn();
				$detailidstring = implode(",",$detailids);

				$query = "DELETE FROM #__jevents_rrule WHERE eventid IN ($veventidstring)";
				$db->setQuery( $query);
				$db->execute();

				$query = "DELETE FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
				$db->setQuery( $query);
				$db->execute();

				$query = "DELETE FROM #__jevents_exception WHERE eventid IN ($veventidstring)";
				$db->setQuery( $query);
				$db->execute();

				if (JString::strlen($detailidstring)>0){
					$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN ($detailidstring)";
					$db->setQuery( $query);
					$db->execute();
				}

				$query = "DELETE FROM #__jevents_vevent WHERE ev_id IN ($veventidstring)";
				$db->setQuery( $query);
				$db->execute();

				$ex_count = count($existingevents);
				if($guest === 1)
				{
					JFactory::getApplication()->enqueueMessage(JText::plural('COM_JEVENTS_MANAGE_CALENDARS_ICAL_IMPORT_DELETED_EVENTS', $ex_count));
				}
			}
		}
		$count = count($this->_icalInfo->vevents) ;
		unset($this->_icalInfo->vevents);
		if($guest === 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::plural('COM_JEVENTS_MANAGE_CALENDARS_ICAL_IMPORT_N_EVENTS_PROCESSED', $count));
		}
	}

	// find if icsFile already imported
	public function isDuplicate(){
		$sql = "SELECT ics_id from #__jevents_icsfile as ics WHERE ics.label = " . $this->_db->quote($this->label) ;
		$this->_db->setQuery($sql);
		$matches = $this->_db->loadObjectList();
		if (count($matches)>0 && isset($matches[0]->ics_id)) {
			return $matches[0]->ics_id;
		}
		return false;

	}

	// Method to store the events WITHOUT storing  the calendar itself - used in frontend imports
	public function storeEvents($catid=false , $flush =true) {

		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		
		static $categories;
		if (is_null($categories)){
			$db	= JFactory::getDBO();
			$sql = "SELECT * FROM #__categories WHERE extension='com_jevents'";
			$db->setQuery($sql);
			$categories = $db->loadObjectList('title');
		}

		if (!$catid){
			$catid = $this->catid;
		}

		// insert the data - this will need to deal with multiple rrule values
		foreach ($this->_icalInfo->vevents as & $vevent) {

			// Do I need to remove a duplicate event?
			$sql = "SELECT * FROM #__jevents_vevent WHERE uid=".$db->Quote($vevent->uid);
			$db->setQuery($sql);
			$duplicate = $db->loadObject();

			if ($duplicate && !$vevent->isCancelled() && !$vevent->isRecurrence()){
				$veventidstring = $duplicate->ev_id;

				// TODO the ruccurences should take care of all of these??
				// This would fail if all recurrances have been 'adjusted'
				$query = "SELECT DISTINCT (eventdetail_id) FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
				$db->setQuery( $query);
				$detailids = $db->loadColumn();
				$detailidstring = implode(",",$detailids);

				$query = "DELETE FROM #__jevents_rrule WHERE eventid IN ($veventidstring)";
				$db->setQuery( $query);
				$db->execute();

				$query = "DELETE FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
				$db->setQuery( $query);
				$db->execute();

				$query = "DELETE FROM #__jevents_exception WHERE eventid IN ($veventidstring)";
				$db->setQuery( $query);
				$db->execute();

				if (JString::strlen($detailidstring)>0){
					$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN ($detailidstring)";
					$db->setQuery( $query);
					$db->execute();

					// I also need to clean out associated custom data
					$dispatcher	= JEventDispatcher::getInstance();
					// just incase we don't have jevents plugins registered yet
					JPluginHelper::importPlugin("jevents");
					$res = $dispatcher->trigger( 'onDeleteEventDetails' , array($detailidstring));
				}

				$query = "DELETE FROM #__jevents_vevent WHERE ev_id IN ($veventidstring)";
				$db->setQuery( $query);
				$db->execute();

				// I also need to delete custom data
				$res = $dispatcher->trigger( 'onDeleteCustomEvent' , array(&$veventidstring));

			}


			if (!$vevent->isCancelled() && !$vevent->isRecurrence()){
				// if existing category then use it
				if (!$this->ignoreembedcat && JString::strlen($vevent->_detail->categories)>0){
					$evcat = explode(",",$vevent->_detail->categories);
					if (count($evcat)>0 && array_key_exists($evcat[0],$categories)){
						if ($params->get("multicategory",0) && count($evcat)>1){
							$vevent->catid = array();
							foreach ($evcat as $ct){
                                                            $vevent->catid[] =  $categories[trim($ct)]->id;
							}							
						}
						if ($params->get("multicategory",0) && count($evcat)==1){
							$vevent->catid = array();
							$vevent->catid[] =  $categories[$evcat[0]]->id;
						}
						else {
							$vevent->catid =  $categories[$evcat[0]]->id;							
						}

					}
					// if no such category then create it
					else if (count($evcat)>0) {
						include_once(JEV_ADMINLIBS."categoryClass.php");
						$cat = new JEventsCategory($db);
						$cat->bind(array("title"=>$evcat[0]));
						$cat->published=1;
						$cat->store();
						if ($params->get("multicategory",0)){
							$vevent->catid[] = $cat->id;
						}
						else {
							$vevent->catid = $cat->id;
						}
						// must reset  the list of categories now
						$sql = "SELECT * FROM #__categories WHERE extension='com_jevents'";
						$db->setQuery($sql);
						$categories = $db->loadObjectList('title');
					}
				}
				else {
					if ($params->get("multicategory",0)){
						$vevent->catid[] = $catid;
					}
					else {
						$vevent->catid = $catid;
					}
				}
				$vevent->icsid = $this->ics_id;
				// The refreshed field is used to track dropped events on reload
				$vevent->refreshed = $this->refreshed;

				// handle events running over midnight
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$icalmultiday = $params->get("icalmultiday",0);
				$icalmultiday24h = $params->get("icalmultiday24h",0);
				// These booleans are the wrong way around.
				$vevent->_detail->multiday = $icalmultiday?0:1;
				if ($vevent->_detail->dtend-$vevent->_detail->dtstart < 86400) {
					$vevent->_detail->multiday = $icalmultiday24h?0:1;
				}

				// force creator if appropriate
				if ($this->created_by>0){
					$vevent->created_by = $this->created_by;
					// force override of creator
					$vevent->store(false, true);
				}
				else {
					$vevent->store();
				}
				
				$repetitions = $vevent->getRepetitions(true);
				$vevent->storeRepetitions();

				// Save memory by clearing out the repetitions we no longer need
				$vevent->_repetitions = null;
				$repetitions = null;
				echo "Event Data read in<br/>";
				//echo "memory = ".memory_get_usage()." ".memory_get_usage(true)."<br/>";
				if ($flush){
					ob_flush();
					flush();
				}
			}
		}
		unset($vevent);

		// Having stored all the repetitions - remove the cancelled instances
		// this should be done as a batch but for now I'll do them one at a time
		foreach ($this->_icalInfo->vevents as $vevent) {
			if (!is_null($vevent) && ($vevent->isCancelled() || $vevent->isRecurrence())){
				// if existing category then use it
				if (JString::strlen($vevent->_detail->categories)>0){
					$evcat = explode(",",$vevent->_detail->categories);
					if (count($evcat)>0 && array_key_exists($evcat[0],$categories)){
						if ($params->get("multicategory",0) && count($evcat)>1){
							$vevent->catid = array();
							foreach ($evcat as $ct){
								$vevent->catid[] =  $categories[$ct]->id;
							}							
						}
						else {
							$vevent->catid =  $categories[$evcat[0]]->id;							
						}
					}
					// if no such category then create it
					else if (count($evcat)>0) {
						include_once(JEV_ADMINLIBS."categoryClass.php");
						$cat = new JEventsCategory($db);
						$cat->bind(array("title"=>$evcat[0]));
						$cat->published=1;
						$cat->check();
						$cat->store();
						if ($params->get("multicategory",0)){
							$vevent->catid[] = $cat->id;
						}
						else {
							$vevent->catid = $cat->id;
						}
						// must reset  the list of categories now
						$sql = "SELECT * FROM #__categories WHERE extension='com_jevents'";
						$db->setQuery($sql);
						$categories = $db->loadObjectList('title');
					}
				}
				else {
					if ($params->get("multicategory",0)){
						$vevent->catid[] = $catid;
					}
					else {
						$vevent->catid = $catid;
					}
				}
				$vevent->access = $this->access;
				$vevent->state =  $this->state;
				$vevent->icsid = $this->ics_id;
				// make sure I don't add the same events more than once
				if ($matchingEvent = $vevent->matchingEventDetails()){
					$vevent->ev_id = $matchingEvent->ev_id;
				}
				if ($vevent->isCancelled()) {
					$vevent->cancelRepetition();
				}
				else {
					// replace event that is only 'adjusted' with the correct settings
					$vevent->adjustRepetition($matchingEvent);
				}
			}
		}

		return count($this->_icalInfo->vevents);
	}

}
