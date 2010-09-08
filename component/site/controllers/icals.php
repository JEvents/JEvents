<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

include_once(JEV_ADMINPATH."/controllers/icals.php");

class ICalsController extends AdminIcalsController   {

	function __construct($config = array())
	{
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'ical' );
		//		$this->registerTask( 'show',  'showContent' );

		// Ensure authorised to do this
		$cfg = & JEVConfig::getInstance();
		if ($cfg->get("disableicalexport",0)) {
			JError::raiseError(403, JText::_('ALERTNOTAUTH'));
		}

		// Load abstract "view" class
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/$theme/abstract/abstract.php");
		if (!isset($this->_basePath) && JVersion::isCompatible("1.6.0")){
			$this->_basePath = $this->basePath;
			$this->_task = $this->task;
		}
	}

	// Thanks to HiFi
	function ical() {

		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		// get the view

		$document =& JFactory::getDocument();
		$viewType	= $document->getType();

		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "icals";
		$this->addViewPath($this->_basePath.DS."views".DS.$theme);
		$this->view = & $this->getView($view,$viewType, $theme,
		array( 'base_path'=>$this->_basePath,
		"template_path"=>$this->_basePath.DS."views".DS.$theme.DS.$view.DS.'tmpl',
		"name"=>$theme.DS.$view));

		// Set the layout
		$this->view->setLayout('ical');

		$this->view->assign("Itemid",$Itemid);
		$this->view->assign("month",$month);
		$this->view->assign("day",$day);
		$this->view->assign("year",$year);
		$this->view->assign("task",$this->_task);

		// View caching logic -- simple... are we logged in?
		$cfg	 = & JEVConfig::getInstance();
		$useCache = intval($cfg->get('com_cache', 0));
		$user = &JFactory::getUser();
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache =& JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}
	}

	function export() {

		$years = JRequest::getVar(	'years','NONE' );
		$cats = JRequest::getVar(	'catids','NONE' );

		// validate the key
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$icalkey = $params->get("icalkey","secret phrase");

		$outlook2003icalexport = $params->get("outlook2003icalexport",0);
		if ($outlook2003icalexport) JRequest::setVar("icf",1);
		
		$privatecalendar = false;
		$k = JRequest::getString("k","NONE");
		$pk = JRequest::getString("pk","NONE");
		$userid = JRequest::getInt("i",0);
		if ($pk!="NONE"){
			if (!$userid) JError::raiseError(403,"JEV ERROR");
			$privatecalendar = true;
			$puser = JUser::getInstance($userid);
			$key = md5($icalkey . $cats . $years . $puser->password . $puser->username . $puser->id);

			if ($key != $pk) JError::raiseError(403,"JEV ERROR");

			// Get an ACL object
			$acl =& JFactory::getACL();

			// Get the user group from the ACL
			$grp = $acl->getAroGroup($puser->get('id'));

			//Mark the user as logged in
			$puser->set( 'guest', 0);
			$puser->set('aid', 1);
			
			// Fudge Authors, Editors, Publishers and Super Administrators into the special access group
			if ($acl->is_group_child_of($grp->name, 'Registered') || $acl->is_group_child_of($grp->name, 'Public Backend'))    {
				$puser->set('aid', 2);
			}

			// ensure "user" can access non-public categories etc.
			$this->dataModel->aid = $puser->aid;

			//Don'y need to set the usertype based on the ACL group name
			// $puser->set('usertype', $grp->name);

			$registry	=& JRegistry::getInstance("jevents");
			$registry->setValue("jevents.icaluser",$puser);

		}
		else if ($k!="NONE"){
			$key = md5($icalkey . $cats . $years );
			if ($key != $k) JError::raiseError(403,"JEV ERROR");
		}
		else {
			JError::raiseError(403,"JEV ERROR");
		}

		// Fix the cats
		$cats = explode( ',', $cats );
		// hardening!
		JEVHelper::forceIntegerArray($cats, false);
		JRequest::setVar("catids", implode("|",$cats));

		//Parsing variables from URL
		//Year
		if ($years != "NONE") {
			$years = explode(",",JRequest::getVar('years'));
			if (!is_array($years) || count($years)==0){
				list($y,$m,$d) = JEVHelper::getYMD();
				$years = array($y);
			}
			JArrayHelper::toInteger($years);
		}
		else {
			list($y,$m,$d) = JEVHelper::getYMD();
			$years = array($y);
		}

		// Lockin hte categories from the URL
		$this->dataModel->setupComponentCatids();
		
		$dispatcher	=& JDispatcher::getInstance();
		// just incase we don't have jevents plugins registered yet
		JPluginHelper::importPlugin("jevents");

		//And then the real work
		// Force all only the one repeat
		$cfg = & JEVConfig::getInstance();
		$cfg->set('com_showrepeats',0);
		$icalEvents = array();
		foreach ($years as $year) {
			$startdate = $year."-01-01";
			$enddate = $year."-12-31";
			$rows = $this->dataModel->getRangeData($startdate, $enddate,0,0);
			if (!isset($rows["rows"])) continue;
			foreach ($rows["rows"] as $row) {
				if (!array_key_exists($row->ev_id(),$icalEvents)){

					$dispatcher->trigger( 'onExportRow', array( &$row) );
					$icalEvents[$row->ev_id()] = $row;
				}

			}
			unset($rows);
		}
		if ($userid) $user = JUser::getInstance($userid);

		$mainframe = JFactory::getApplication();

		ob_end_clean();

		// Define the file as an iCalendar file
		//header('Content-Type: text/calendar; method=request; charset=UTF-8');
		header('Content-Type: application/octet-stream; charset=UTF-8');
		// Give the file a name and force download
		header('Content-Disposition: attachment; filename=calendar.ics'     );

		if ($outlook2003icalexport) echo "BEGIN:VCALENDAR\nPRODID:-//jEvents 1.5 for Joomla//EN\n";
		else  echo "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//jEvents 1.5 for Joomla//EN\n";
		
		echo "CALSCALE:GREGORIAN\nMETHOD:PUBLISH\n";
		if(!empty($icalEvents)) {

			// Build Exceptions dataset - all done in big batches to save multiple queries
			$exceptiondata = array();
			$ids = array();
			foreach($icalEvents as $a) {
				$ids[]=	$a->ev_id();
				if (count($ids)>50){
					$db = JFactory::getDBO();
					$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (".implode(",",$ids).")");
					$rows = $db->loadObjectList();
					foreach ($rows as $row) {
						if (isset($exceptiondata[$row->eventid])){
							$exceptiondata[$row->eventid] = array();
						}
						$exceptiondata[$row->eventid][]=$row;
					}
					$ids = array();
				}
			}
			// mop up the last ones
			if (count($ids)>0){
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (".implode(",",$ids).")");
				$rows = $db->loadObjectList();
				foreach ($rows as $row) {
					if (isset($exceptiondata[$row->eventid])){
						$exceptiondata[$row->eventid] = array();
					}
					$exceptiondata[$row->eventid][]=$row;
				}
			}

			foreach($icalEvents as $a) {
				// if event has repetitions I must find the first one to confirm the dates
				if ($a->hasrepetition()){
					$a = $a->getFirstRepeat();
				}
				echo "BEGIN:VEVENT";
				echo "\nUID:".$a->uid();
				echo "\nCATEGORIES:".$a->catname();
				if (!empty($a->_class)) echo "\nCLASS:".$a->_class;
				echo "\n"."SUMMARY:".$a->title()."\n";
				echo "LOCATION:".$this->wraplines($this->replacetags($a->location()))."\n";
				// We Need to wrap this according to the specs
				/*echo "DESCRIPTION:".preg_replace("'<[\/\!]*?[^<>]*?>'si","",preg_replace("/\n|\r\n|\r$/","",$a->content()))."\n";*/
				echo $this->setDescription($a->content())."\n";

				if ($a->hasContactInfo()) echo "CONTACT:".$this->replacetags($a->contact_info())."\n";
				if ($a->hasExtraInfo()) echo "X-EXTRAINFO:".$this->wraplines($this->replacetags($a->_extra_info)); echo "\n";

				if (is_callable("date_default_timezone_set")){
					// UTC!
					$start = $a->getUnixStartTime();
					$end = $a->getUnixEndTime();

					// Change timezone to UTC
					$current_timezone= date_default_timezone_get();
					date_default_timezone_set("UTC");

					// If all day event then don't show the start time or end time either
					if ($a->alldayevent()){
						$startformat = "%Y%m%d";
						$endformat = "%Y%m%d";
					}
					else {
						$startformat = "%Y%m%dT%H%M%SZ";
						$endformat = "%Y%m%dT%H%M%SZ";
					}

					$start = strftime($startformat,$start);
					$end = strftime($endformat,$end);

					$stamptime = strftime("%Y%m%dT%H%M%SZ",time());

					// Change back
					date_default_timezone_set($current_timezone);
				}
				else {
					// If all day event then don't show the start time or end time either
					if ($a->alldayevent()){
						$startformat = "%Y%m%d";
						$endformat = "%Y%m%d";
					}
					else {
						$startformat = "%Y%m%dT%H%M%S";
						$endformat = "%Y%m%dT%H%M%S";
					}

					$start = strftime($startformat,$a->getUnixStartTime());
					$end = strftime($endformat,$a->getUnixEndTime());
					$stamptime = strftime("%Y%m%dT%H%M%S",time());
				}

				echo "DTSTAMP:".$stamptime."\n";
				echo "DTSTART:".$start."\n";
				// events with no end time don't give a DTEND
				if (!$a->noendtime()){
					echo "DTEND:".$end."\n";
				}
				echo "SEQUENCE:".$a->_sequence."\n";
				if($a->hasrepetition()){
					echo 'RRULE:';

					// TODO MAKE SURE COMPAIBLE COMBINATIONS
					echo 'FREQ='.$a->_freq;
					if($a->_until != "" && $a->_until != 0) {
						echo ';UNTIL='.strftime("%Y%m%dT235959Z",$a->_until);
					}
					else if($a->_count != "") {
						echo ';COUNT='.$a->_count;
					}
					if($a->_rinterval != "") echo ';INTERVAL='.$a->_rinterval;
					if ($a->_freq=="DAILY"){

					}
					else if ($a->_freq=="WEEKLY"){
						if($a->_byday != "") echo ';BYDAY='.$a->_byday;
					}
					else if ($a->_freq=="MONTHLY"){
						if($a->_bymonthday != "") {
							echo ';BYMONTHDAY='.$a->_bymonthday;
							if($a->_byweekno != "") echo ';BYWEEKNO='.$a->_byweekno;
						}
						else if($a->_byday != "") {
							echo ';BYDAY='.$a->_byday;
							if($a->_byweekno != "") echo ';BYWEEKNO='.$a->_byweekno;
						}
					}
					else if ($a->_freq=="YEARLY"){
						if($a->_byyearday != "") echo ';BYYEARDAY='.$a->_byyearday;
					}
					echo "\n";
				}

				// Now handle Exceptions
				$exceptions = array();
				if (array_key_exists($a->ev_id(),$exceptiondata)){
					$exceptions = $exceptiondata[$a->ev_id()];
				}

				$deletes = array();
				$changed = array();
				$changedexceptions = array();
				if (count($exceptions)>0){
					foreach ($exceptions as $exception) {
						if ($exception->exception_type == 0){
							$exceptiondate = strtotime($exception->startrepeat);
							if (is_callable("date_default_timezone_set")){

								// Change timezone to UTC
								$current_timezone= date_default_timezone_get();
								date_default_timezone_set("UTC");

								$deletes[] = strftime("%Y%m%dT%H%M%SZ",$exceptiondate);

								// Change back
								date_default_timezone_set($current_timezone);

							}
							else {
								$deletes[] = strftime("%Y%m%dT%H%M%S",$exceptiondate);
							}
						}
						else {
							$changed[] = $exception->rp_id;
							$changedexceptions[$exception->rp_id] = $exception;
						}
					}
					if (count($deletes)>0){
						echo "EXDATE:".$this->wraplines(implode(",",$deletes))."\n";
					}
				}

				echo "TRANSP:OPAQUE\n";
				echo "END:VEVENT\n";


				if (count($changed)>0){
					foreach ($changed as $rpid) {
						$a = $this->dataModel->getEventData( $rpid, "icaldb", 0,0,0);
						if ($a && isset($a["row"])){
							$a = $a["row"];
							echo "BEGIN:VEVENT";
							echo "\nUID:".$a->uid();
							echo "\nCATEGORIES:".$a->catname();
							if (!empty($a->_class)) echo "\nCLASS:".$a->_class;
							echo "\n"."SUMMARY:".$a->title()."\n";
							echo "LOCATION:".$this->wraplines($this->replacetags($a->location()))."\n";
							// We Need to wrap this according to the specs
							echo $this->setDescription($a->content())."\n";

							if ($a->hasContactInfo()) echo "CONTACT:".$this->replacetags($a->contact_info())."\n";
							if ($a->hasExtraInfo()) echo "X-EXTRAINFO:".$this->wraplines($this->replacetags($a->_extra_info)); echo "\n";

							$exception = $changedexceptions[$rpid];
							$originalstart = strtotime($exception->oldstartrepeat);
							if (is_callable("date_default_timezone_set")){
								// UTC!
								$chstart = $a->getUnixStartTime();
								$end = $a->getUnixEndTime();

								// Change timezone to UTC
								$current_timezone= date_default_timezone_get();
								date_default_timezone_set("UTC");
								$chstart = strftime("%Y%m%dT%H%M%SZ",$chstart);
								$end = strftime("%Y%m%dT%H%M%SZ",$end);
								$stamptime = strftime("%Y%m%dT%H%M%SZ",time());

								$originalstart = strftime("%Y%m%dT%H%M%SZ",$originalstart);
								
								// Change back
								date_default_timezone_set($current_timezone);
							}
							else {
								$chstart = strftime("%Y%m%dT%H%M%S",$a->getUnixStartTime());
								$end = strftime("%Y%m%dT%H%M%S",$a->getUnixEndTime());
								$stamptime = strftime("%Y%m%dT%H%M%S",time());
								$originalstart = strftime("%Y%m%dT%H%M%S",$originalstart);								
							}
							echo "DTSTAMP:".$stamptime."\n";
							echo "DTSTART:".$chstart."\n";
							echo "DTEND:".$end."\n";
							echo "RECURRENCE-ID:".$originalstart."\n";
							echo "SEQUENCE:".$a->_sequence."\n";
							echo "TRANSP:OPAQUE\n";
							echo "END:VEVENT\n";
						}
					}
				}

			}

		}


		echo "END:VCALENDAR";
		exit();
	}

	function icalevent(){
		$this->exportEvent(true);
	}

	function icalrepeat(){
		$this->exportEvent(false);
	}

	function importform() {
		// Can only do this if can add an event
		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			$user = &JFactory::getUser();
			if ($user->id){
				$this->setRedirect(JURI::root(),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
			}
			else {
				$this->setRedirect(JRoute::_("index.php?option=com_user&view=login"),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
			}
			return;
		}

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!$params->get("feimport",0)){
			return;
		}

		$document =& JFactory::getDocument();
		$viewType	= $document->getType();

		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "icals";
		$this->addViewPath($this->_basePath.DS."views".DS.$theme);
		$this->view = & $this->getView($view,$viewType, $theme,
		array( 'base_path'=>$this->_basePath,
		"template_path"=>$this->_basePath.DS."views".DS.$theme.DS.$view.DS.'tmpl',
		"name"=>$theme.DS.$view));

		// Set the layout
		$this->view->setLayout('importform');

		$this->view->assign("task",$this->_task);


		// get all the raw native calendars
		$nativeCals = $this->dataModel->queryModel->getNativeIcalendars();

		// Strip this list down based on user permissions
		$jevuser =& JEVHelper::getAuthorisedUser();
		if ($jevuser && $jevuser->calendars!="" && $jevuser->calendars!="all"){
			$cals = array_keys($nativeCals);
			$allowedcals = explode("|",$jevuser->calendars);
			foreach ( $cals as $calid) {
				if (!in_array($calid,$allowedcals)) unset($nativeCals[$calid]);
			}
		}

		$excats = "0";
		if ($jevuser && $jevuser->categories!="" && $jevuser->categories!="all"){
			// Find which categories to exclude
			$catsql = 'SELECT id  FROM #__categories WHERE id NOT IN ('.str_replace("|",",",$jevuser->categories).') AND section="com_jevents"';
			$db->setQuery( $catsql );
			$excats = implode(",",$db->loadResultArray());
		}


		// only offer a choice of native calendars if it exists!
		if (count($nativeCals)>1){
			$icalList = array();
			$icalList[] = JHTML::_('select.option', '0', JText::_('JEV_EVENT_CHOOSE_ICAL'), 'ics_id', 'label' );
			$icalList = array_merge( $icalList, $nativeCals );
			$clist = JHTML::_('select.genericlist', $icalList, 'ics_id', " onchange='preselectCategory(this);'", 'ics_id', 'label', 0 );
			$this->view->assign('clistChoice',true);
			$this->view->assign('defaultCat',0);
		}
		else {
			if (count($nativeCals)==0 || !is_array($nativeCals)){
				JError::raiseWarning(870, JText::_("INVALID CALENDAR STRUCTURE") );
			}

			$icsid = current($nativeCals)->ics_id;

			$clist = '<input type="hidden" name="ics_id" value="'.$icsid.'" />';
			$this->view->assign('clistChoice',false);
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("defaultcat",false)){
				$this->view->assign('defaultCat',current($nativeCals)->catid);
			}
			else {
				$this->view->assign('defaultCat',0);
			}
		}

		$this->view->assign('excats',$excats);
		$this->view->assign('nativeCals',$nativeCals);
		$this->view->assign('clist',$clist);

		// View caching logic -- simple... are we logged in?
		$cfg	 = & JEVConfig::getInstance();
		$useCache = intval($cfg->get('com_cache', 0));
		$user = &JFactory::getUser();
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache =& JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}

	}

	function importdata() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Can only do this if can add an event
		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			$user = &JFactory::getUser();
			if ($user->id){
				$this->setRedirect(JURI::root(),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
			}
			else {
				$this->setRedirect(JRoute::_("index.php?option=com_user&view=login"),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
			}
			return;
		}

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!$params->get("feimport",0)){
			return;
		}

		$catid = JRequest::getInt('catid',0);
		$ignoreembedcat = JRequest::getInt('ignoreembedcat',0);
		// Should come from the form or existing item
		$access = 0;
		$state = 1;
		$uploadURL = JRequest::getVar('uploadURL','' );
		$icsLabel = uniqid('icsLabel');
		$icsid = JRequest::getInt('ics_id',0);
		$autorefresh = 0;

		if ($catid==0){
			// Paranoia, should not be here, validation is done by java script
			JError::raiseError('Fatal error', JText::_('JEV_E_WARNCAT') );
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=$redirect_task",  JText::_('JEV_E_WARNCAT'));
			$this->redirect();
			return;
		}

		// I need a better check and expiry information etc.
		if (strlen($uploadURL)>0){
			$icsFile = iCalICSFile::newICSFileFromURL($uploadURL,$icsid,$catid,$access,$state,$icsLabel, $autorefresh, $ignoreembedcat);
		}
		else if (isset($_FILES['upload']) && is_array($_FILES['upload']) ) {
			$file 	= $_FILES['upload'];
			if ($file['size']==0 ){//|| !($file['type']=="text/calendar" || $file['type']=="application/octet-stream")){
				JError::raiseWarning(0, 'empty upload file');
				$icsFile = false;
			}
			else {
				$icsFile = iCalICSFile::newICSFileFromFile($file,$icsid,$catid,$access,$state,$icsLabel);
			}
		}


		$count = 0;
		if ($icsFile !== false) {
			$count = $icsFile->storeEvents();
		}

		list($year,$month,$day) = JEVHelper::getYMD();
		ob_end_clean();
		?>
		<script type="text/javascript">
		window.parent.SqueezeBox.close();
		window.alert("<?php echo JText::sprintf("JEV EVENTS IMPORTED",$count);?>");
		//window.parent.location.reload();
		</script>
		<?php
		exit();
	}

	private function exportEvent($withrepeats = true){
		$rpid = JRequest::getInt("evid",0);
		if (!$rpid) return;

		JRequest::setVar("tmpl","component");
		// Define the file as an iCalendar file
		header('Content-Type: text/calendar; method=request; charset=UTF-8');
		// Give the file a name and force download
		header('Content-Disposition: attachment; filename=calendar.ics' );

		list($year,$month,$day) = JEVHelper::getYMD();
		$repeat = $this->dataModel-> getEventData( $rpid, "icaldb", $year, $month, $day);
		if ($repeat && is_array($repeat) && isset($repeat["row"]) && $repeat["row"]->rp_id()==$rpid){
			$a = $repeat["row"];
			$this->dataModel->setupComponentCatids();

			if ($withrepeats && $a->hasrepetition()){
				$a = $a->getFirstRepeat();
			}

			if ($withrepeats){
				// Build Exceptions dataset - all done in big batches to save multiple queries
				$exceptiondata = array();
				$ids = array();
				$ids[]=	$a->ev_id();
				$db = JFactory::getDBO();
				$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (".implode(",",$ids).")");
				$rows = $db->loadObjectList();
				foreach ($rows as $row) {
					if (isset($exceptiondata[$row->eventid])){
						$exceptiondata[$row->eventid] = array();
					}
					$exceptiondata[$row->eventid][]=$row;
				}
			}

			echo "BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//jEvents 1.5 for Joomla//EN\n";
			echo "BEGIN:VEVENT";
			echo "\nUID:".$a->uid();
			echo "\nCATEGORIES:".$a->catname();
			if (!empty($a->_class)) echo "\nCLASS:".$a->_class;
			echo "\n"."SUMMARY:".$a->title()."\n";
			echo "LOCATION:".$this->wraplines($this->replacetags($a->location()))."\n";
			// We Need to wrap this according to the specs
			/*echo "DESCRIPTION:".preg_replace("'<[\/\!]*?[^<>]*?>'si","",preg_replace("/\n|\r\n|\r$/","",$a->content()))."\n";*/
			echo $this->setDescription($a->content())."\n";

			if ($a->hasContactInfo()) echo "CONTACT:".$this->replacetags($a->contact_info())."\n";
			if ($a->hasExtraInfo()) echo "X-EXTRAINFO:".$this->wraplines($this->replacetags($a->_extra_info)); echo "\n";

			if (is_callable("date_default_timezone_set")){
				// UTC!
				$start = strftime("%Y%m%dT%H%M%SZ",$a->getUnixStartTime());
				$end = strftime("%Y%m%dT%H%M%SZ",$a->getUnixEndTime());
				$stamptime = strftime("%Y%m%dT%H%M%SZ",time());
			}
			else {
				$start = strftime("%Y%m%dT%H%M%S",$a->getUnixStartTime());
				$end = strftime("%Y%m%dT%H%M%S",$a->getUnixEndTime());
				$stamptime = strftime("%Y%m%dT%H%M%S",time());
			}

			echo "DTSTAMP:".$stamptime."\n";
			echo "DTSTART:".$start."\n";
			echo "DTEND:".$end."\n";
			echo "SEQUENCE:".$a->_sequence."\n";
			if($withrepeats && $a->hasrepetition()){
				echo 'RRULE:';

				// TODO MAKE SURE COMPAIBLE COMBINATIONS
				echo 'FREQ='.$a->_freq;
				if($a->_until != "" && $a->_until != 0) {
					echo ';UNTIL='.strftime("%Y%m%dT235959Z",$a->_until);
				}
				else if($a->_count != "") {
					echo ';COUNT='.$a->_count;
				}
				if($a->_rinterval != "") echo ';INTERVAL='.$a->_rinterval;
				if ($a->_freq=="DAILY"){

				}
				else if ($a->_freq=="WEEKLY"){
					if($a->_byday != "") echo ';BYDAY='.$a->_byday;
				}
				else if ($a->_freq=="MONTHLY"){
					if($a->_bymonthday != "") {
						echo ';BYMONTHDAY='.$a->_bymonthday;
						if($a->_byweekno != "") echo ';BYWEEKNO='.$a->_byweekno;
					}
					else if($a->_byday != "") {
						echo ';BYDAY='.$a->_byday;
						if($a->_byweekno != "") echo ';BYWEEKNO='.$a->_byweekno;
					}
				}
				else if ($a->_freq=="YEARLY"){
					if($a->_byyearday != "") echo ';BYYEARDAY='.$a->_byyearday;
				}
				echo "\n";
			}

			if($withrepeats) {
				// Now handle Exceptions
				$exceptions = array();
				if (array_key_exists($a->ev_id(),$exceptiondata)){
					$exceptions = $exceptiondata[$a->ev_id()];
				}

				$deletes = array();
				$changed = array();
				$changedexceptions = array();
				if (count($exceptions)>0){
					foreach ($exceptions as $exception) {
						if ($exception->exception_type == 0){
							$exceptiondate = strtotime($exception->startrepeat);
							if (is_callable("date_default_timezone_set")){
								// Change timezone to UTC
								$current_timezone= date_default_timezone_get();
								date_default_timezone_set("UTC");

								$deletes[] = strftime("%Y%m%dT%H%M%SZ",$exceptiondate);

								// Change back
								date_default_timezone_set($current_timezone);
							}
							else {
								$deletes[] = strftime("%Y%m%dT%H%M%S",$exceptiondate);
							}
						}
						else {
							$changed[] = $exception->rp_id;
							$changedexceptions[$exception->rp_id] = $exception;
						}
					}
					if (count($deletes)>0){
						echo "EXDATE:".$this->wraplines(implode(",",$deletes))."\n";
					}
				}
			}
			echo "TRANSP:OPAQUE\n";
			echo "END:VEVENT\n";

			if($withrepeats) {

				if (count($changed)>0){
					foreach ($changed as $rpid) {
						$a = $this->dataModel->getEventData( $rpid, "icaldb", 0,0,0);
						if ($a && isset($a["row"])){
							$a = $a["row"];
							echo "BEGIN:VEVENT";
							echo "\nUID:".$a->uid();
							echo "\nCATEGORIES:".$a->catname();
							if (!empty($a->_class)) echo "\nCLASS:".$a->_class;
							echo "\n"."SUMMARY:".$a->title()."\n";
							echo "LOCATION:".$this->wraplines($this->replacetags($a->location()))."\n";
							// We Need to wrap this according to the specs
							echo $this->setDescription($a->content())."\n";

							if ($a->hasContactInfo()) echo "CONTACT:".$this->replacetags($a->contact_info())."\n";
							if ($a->hasExtraInfo()) echo "X-EXTRAINFO:".$this->wraplines($this->replacetags($a->_extra_info)); echo "\n";

							$exception = $changedexceptions[$rpid];
							$originalstart = strtotime($exception->oldstartrepeat);
							
							if (is_callable("date_default_timezone_set")){
								// UTC!
								$chstart = strftime("%Y%m%dT%H%M%SZ",$a->getUnixStartTime());
								$end = strftime("%Y%m%dT%H%M%SZ",$a->getUnixEndTime());
								$stamptime = strftime("%Y%m%dT%H%M%SZ",time());
								$originalstart = strftime("%Y%m%dT%H%M%SZ",$originalstart);
							}
							else {
								$chstart = strftime("%Y%m%dT%H%M%S",$a->getUnixStartTime());
								$end = strftime("%Y%m%dT%H%M%S",$a->getUnixEndTime());
								$stamptime = strftime("%Y%m%dT%H%M%S",time());
								$originalstart = strftime("%Y%m%dT%H%M%S",$originalstart);
							}
							echo "DTSTAMP:".$stamptime."\n";
							echo "DTSTART:".$start."\n";
							echo "DTEND:".$end."\n";
							echo "RECURRENCE-ID:".$originalstart."\n";
							echo "SEQUENCE:".$a->_sequence."\n";
							echo "TRANSP:OPAQUE\n";
							echo "END:VEVENT\n";
						}
					}
				}
			}

			echo "END:VCALENDAR";

			exit();

		}

	}

	private function setDescription($desc) {
		// TODO - run this through plugins first ?

		$icalformatted =JRequest::getInt("icf",0);
		if (!$icalformatted) $description = $this->replacetags($desc);
		else $description = $desc;

		// wraplines	from vCard class
		return "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:".$this->wraplines($description);
	}

	private function replacetags($description){
		$description 	= str_replace( '<p>', '\n\n', $description );
		$description 	= str_replace( '<P>', '\n\n', $description );
		$description 	= str_replace( '</p>', '\n' ,$description );
		$description 	= str_replace( '</P>', '\n' ,$description );
		$description 	= str_replace( '<p/>', '\n\n', $description );
		$description 	= str_replace( '<P/>', '\n\n', $description );
		$description 	= str_replace( '<br />', '\n', $description );
		$description 	= str_replace( '<br/>', '\n', $description );
		$description 	= str_replace( '<br>', '\n' ,$description );
		$description 	= str_replace( '<BR />', '\n', $description );
		$description 	= str_replace( '<BR/>', '\n', $description );
		$description 	= str_replace( '<BR>', '\n' ,$description );
		$description 	= str_replace( '<li>', '\n - ', $description );
		$description 	= str_replace( '<LI>', '\n - ', $description );
		$description 	= strip_tags( $description );
		$description 	= strtr( $description,	array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
		$description 	= preg_replace( "/&#([0-9]+);/me","chr('\\1')", $description );
		return $description;
	}

	private function wraplines($input, $line_max = 76, $quotedprintable = true) {
		$hex 		= array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$eol 		= "\r\n";
		$input 		= str_replace($eol,"", $input);
		$escape 	= '=';
		$output 	= '';
		$outline 	= "";
		$newline 	= ' ';

		$linlen 	= strlen($input);

		for($i = 0; $i < $linlen; $i++) {
			$c 		= substr($input, $i, 1);
			$dec 	= ord($c);

			if (!$quotedprintable){
				if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
					$c = '=20';
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
					$h2 = floor($dec/16);
					$h1 = floor($dec%16);
					$c 	= $escape.$hex["$h2"] . $hex["$h1"];
				}
			}
			if ( (strlen($outline) + strlen($c)) >= $line_max ) { // CRLF is not counted
				$output .= $outline. $eol. $newline ; // soft line break; "\r\n" is okay
				$outline = $c;
				//$newline .= " ";
			}
			else {
				$outline .= $c;
			}
		} // end of for
		$output .= $outline;

		return trim($output);
	}

}
