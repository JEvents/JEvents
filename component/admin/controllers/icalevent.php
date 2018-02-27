<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icalevent.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;
use Joomla\String\StringHelper;

class AdminIcaleventController extends JControllerAdmin
{

	var $_debug = false;
	var $queryModel = null;
	var $dataModel = null;
	var $editCopy = false;
	var $_largetDataSet = false;

	/**
	 * Controler for the Ical Functions
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		$config["name"]="icalevent";
		//$config["default_view"]="AdminIcalevent";
		parent::__construct($config);
		$this->registerTask('list', 'overview');
		$this->registerTask('unpublish', 'unpublish');
		$this->registerDefaultTask("overview");

		$cfg = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
		$this->queryModel = new JEventsDBModel($this->dataModel);

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');

	}

	/**
	 * List Ical Events
	 *
	 */
	function overview()
	{
		// get the view
		$this->view = $this->getView("icalevent", "html","AdminIcaleventView");

		$this->_checkValidCategories();

		$showUnpublishedICS = true;
		$showUnpublishedCategories = true;

		$jinput = JFactory::getApplication()->input;

		$db = JFactory::getDbo();

		$icsFile = intval(JFactory::getApplication()->getUserStateFromRequest("icsFile", "icsFile", 0));

		$catid = intval(JFactory::getApplication()->getUserStateFromRequest("catidIcalEvents", 'catid', 0));
		$catidtop = $catid;

		$state = intval(JFactory::getApplication()->getUserStateFromRequest("stateIcalEvents", 'state', 3));

		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', JFactory::getApplication()->getCfg('list_limit', 10)));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("view{" . JEV_COM_COMPONENT . "}limitstart", 'limitstart', 0));
		$search = JFactory::getApplication()->getUserStateFromRequest("search{" . JEV_COM_COMPONENT . "}", 'search', '');
		$search = $db->escape(trim(strtolower($search)));

		$created_by = JFactory::getApplication()->getUserStateFromRequest("createdbyIcalEvents", 'created_by', "-1");

		// Is this a large dataset ?
		$query = "SELECT count(rpt.rp_id) from #__jevents_repetition as rpt ";
		$db->setQuery($query);
		$totalrepeats = $db->loadResult();
		$this->_largeDataSet = 0;
		$cfg = JEVConfig::getInstance();
		if ($totalrepeats > $cfg->get('largeDataSetLimit', 100000))
		{
			$this->_largeDataSet = 1;
		}
		$cfg = JEVConfig::getInstance();
		$cfg->set('largeDataSet', $this->_largeDataSet);

		$where = array();
		$join = array();

		if ($search)
		{
			$searchwhere = array();
			$searchwhere[] = "LOWER(detail.summary) LIKE '%$search%'";
			$searchwhere[] = "LOWER(detail.location) LIKE '%$search%'";
			jimport("joomla.filesystem.folder");
			if (JFolder::exists(JPATH_ADMINISTRATOR . "/components/com_jevlocations") && JComponentHelper::getComponent("com_jevlocations", true)->enabled)
			{
				$join[] = "\n #__jev_locations as loc ON loc.loc_id=detail.location";
				$searchwhere[] = "LOWER(loc.title) LIKE '%$search%'";
				$searchwhere[] = "LOWER(loc.city) LIKE '%$search%'";
				$searchwhere[] = "LOWER(loc.postcode) LIKE '%$search%'";
			}
			$where[] = "(" . implode(" OR ", $searchwhere) . " )";
		}

		$user = JFactory::getUser();

		// keep this incase we use filters in category lists
		$catwhere = "\n ev.catid IN(" . $this->queryModel->accessibleCategoryList() . ")";
		$params = JComponentHelper::getParams($jinput->getCmd("option"));
		if ($params->get("multicategory", 0))
		{
			$join[] =  "\n #__jevents_catmap as catmap ON catmap.evid = ev.ev_id" ;
			$join[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$where[] = " catmapcat.access " . ' IN (' . JEVHelper::getAid($user) . ')' ;
			$where[] = " catmap.catid IN(" . $this->queryModel->accessibleCategoryList() . ")";
			$needsgroup = true;
			$catwhere = " 1";
		}
		$where[] = $catwhere;

		// category filter!
		$db->setQuery("SELECT * FROM #__categories where id = " . intval($catid));
		$filtercat = $db->loadObject();

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		$cats = $user->getAuthorisedCategories('com_jevents', 'core.create');
		if (isset($user->id) && !$user->authorise('core.create', 'com_jevents') && !$authorisedonly)
		{
			if (count($cats) > 0 && $catid < 1)
			{
				for ($i = 0; $i < count($cats); $i++)
				{
					if ($params->get("multicategory", 0))
					{
						$whereCats[$i] = "catmap.catid='$cats[$i]'";
					}
					else
					{
						$whereCats[$i] = "ev.catid='$cats[$i]'";
					}
				}
				$where[] = '(' . implode(" OR ", $whereCats) . ')';
			}
			else if (count($cats) > 0 && $catid > 0 && in_array($catid, $cats))
			{
				if ($params->get("multicategory", 0))
				{
					if ($filtercat)
					{
						$where[] = "catmapcat.lft BETWEEN $filtercat->lft AND $filtercat->rgt";
					}
					else
					{
						$where[] = "catmap.catid='$catid'";
					}
				}
				else
				{
					$where[] = "ev.catid='$catid'";
				}
			}
			else
			{
				if ($params->get("multicategory", 0))
				{
					$where[] = "catmap.catid=''";
				}
				else
				{
					$where[] = "ev.catid=''";
				}
			}
		}
		else
		{
			if ($catid > 0)
			{
				if ($params->get("multicategory", 0))
				{
					if ($filtercat)
					{
						$where[] = "catmapcat.lft BETWEEN $filtercat->lft AND $filtercat->rgt";
					}
					else
					{
						$where[] = "catmap.catid='$catid'";
					}
				}
				else
				{
					$where[] = "ev.catid='$catid'";
				}
			}
		}

		if ($created_by >=0)
		{
			$cby = intval($created_by);
			if ($cby >= 0 && $created_by!="")
				$where[] = "ev.created_by=" . $db->Quote($cby);
		}

		if ($icsFile > 0)
		{
			$join[] = " #__jevents_icsfile as icsf ON icsf.ics_id = ev.icsid";
			$where[] = "\n icsf.ics_id = $icsFile";
			if (!$showUnpublishedICS)
			{
				$where[] = "\n icsf.state=1";
			}
		}
		else
		{
			if (!$showUnpublishedICS)
			{
				$join[] = " #__jevents_icsfile as icsf ON icsf.ics_id = ev.icsid";
				$where[] = "\n icsf.state=1";
			}
			else
			{
				$icsFrom = "";
			}
		}

		$hidepast = intval(JFactory::getApplication()->getUserStateFromRequest("hidepast", "hidepast", 1));
		if ($hidepast)
		{
			$datenow = JevDate::getDate("-1 day");
			if (!$this->_largeDataSet)
			{
				$where[] = "\n rpt.endrepeat>'" . $datenow->toSql() . "'";
			}
		}
		if ($state == 1)
		{
			$where[] = "\n ev.state=1";
		}
		else if ($state == 2)
		{
			$where[] = "\n ev.state=0";
		}
		else if ($state == -1)
		{
			$where[] = "\n ev.state=-1";
		} 
                else if ($state == 3){
                    $where[] = "\n (ev.state=1 OR ev.state=0)";
                }

		// get the total number of records
		if ($this->_largeDataSet){
			$query = "SELECT count(distinct ev.ev_id)"
				. "\n FROM #__jevents_vevent AS ev "
				. "\n LEFT JOIN #__jevents_vevdetail as detail ON ev.detail_id=detail.evdet_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				//. "\n LEFT JOIN #__groups AS g ON g.id = ev.access"
				. ( count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '' )
				. ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' );
		}
		else {
			$query = "SELECT count(distinct rpt.eventid)"
				. "\n FROM #__jevents_vevent AS ev "
				. "\n LEFT JOIN #__jevents_vevdetail as detail ON ev.detail_id=detail.evdet_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				//. "\n LEFT JOIN #__groups AS g ON g.id = ev.access"
				. ( count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '' )
				. ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' );
		}
		$db->setQuery($query);
		//echo $db->getQuery()."<br/><br/>";
		$total = $db->loadResult();
		echo $db->getErrorMsg();
		if ($limit > $total)
		{
			$limitstart = 0;
		}

		// if anon user plugin enabled then include this information
		$anonfields = "";
		$anonjoin = "";
		if (JPluginHelper::importPlugin("jevents", "jevanonuser"))
		{

			$anonfields = ", ac.name as anonname, ac.email as anonemail";
			$anonjoin = "\n LEFT JOIN #__jev_anoncreator as ac on ac.ev_id = ev.ev_id";
		}

		$orderdir = JFactory::getApplication()->getUserStateFromRequest("eventsorderdir", "filter_order_Dir", 'asc');
		$order = JFactory::getApplication()->getUserStateFromRequest("eventsorder", "filter_order", 'start');

		if ($params->get("multicategory", 0))
		{
			$anonfields .= ", GROUP_CONCAT(DISTINCT catmap.catid SEPARATOR ',') as catids";
		}

		$dir = $orderdir == "asc" ? "asc" : "desc";

		if ($order == 'start' || $order == 'starttime')
		{
			$order = ($this->_largeDataSet ? "\n GROUP BY  ev.ev_id ORDER BY detail.dtstart $dir" : "\n GROUP BY  ev.ev_id ORDER BY rpt.startrepeat $dir");
		}
		else if ($order == 'created')
		{
			$order = ($this->_largeDataSet ? "\n GROUP BY  ev.ev_id ORDER BY ev.created $dir" : "\n GROUP BY  ev.ev_id ORDER BY ev.created $dir");
		}
		else if ($order == 'modified')
		{
			$order = ($this->_largeDataSet ? "\n GROUP BY  ev.ev_id ORDER BY modified $dir" : "\n GROUP BY  ev.ev_id ORDER BY modified $dir");
		}
		else
		{
			$order = ($this->_largeDataSet ? "\n GROUP BY  ev.ev_id ORDER BY detail.summary $dir" : "\n GROUP BY  ev.ev_id ORDER BY detail.summary $dir");
		}
		// only include repeat id since we need it if we call plugins on the resultant data
		$query = "SELECT ev.* " .  ($this->_largeDataSet ? "" :", rpt.rp_id") . ", ev.state as evstate, detail.*, ev.created as created, max(detail.modified) as modified,  a.title as _groupname " . $anonfields
				. "\n , rr.rr_id, rr.freq,rr.rinterval"//,rr.until,rr.untilraw,rr.count,rr.bysecond,rr.byminute,rr.byhour,rr.byday,rr.bymonthday"
				. ($this->_largeDataSet ? "" : "\n ,MAX(rpt.endrepeat) as endrepeat ,MIN(rpt.startrepeat) as startrepeat"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn")
				. "\n FROM #__jevents_vevent as ev "
				. ($this->_largeDataSet ? "" : "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id")
				. $anonjoin
				. "\n LEFT JOIN #__jevents_vevdetail as detail ON ev.detail_id=detail.evdet_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__viewlevels AS a ON a.id = ev.access"
				. ( count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '' )
				. ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
				. $order;

		if ($limit > 0)
		{
			$query .= "\n LIMIT $limitstart, $limit";
		}
		$db->setQuery($query);

		//echo $db->explain();
		$rows = $db->loadObjectList();
		//echo $db->getQuery()."<br/>";
		foreach ($rows as $key => $val)
		{
			// set state variable to the event value not the event detail value
			$rows[$key]->state = $rows[$key]->evstate;
			$groupname = $rows[$key]->_groupname;
			$rows[$key] = new jIcalEventRepeat($rows[$key]);
			$rows[$key]->_groupname = $groupname;
		}
		if ($this->_debug)
		{
			echo '[DEBUG]<br />';
			echo 'query:';
			echo '<pre>';
			echo $query;
			echo '-----------<br />';
			echo 'option "' . JEV_COM_COMPONENT . '"<br />';
			echo '</pre>';
			//die( 'userbreak - mic ' );
		}

		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

		// get list of categories
		$attribs = 'class="inputbox" size="1" onchange="document.adminForm.submit();"';
		$clist = JEventsHTML::buildCategorySelect($catid, $attribs, null, $showUnpublishedCategories, false, $catidtop, "catid");
		// if there is only one category then do not show the filter
		if (strpos( $clist, "<select") === false)
		{
			$clist = "";
		}
		$options[] = JHTML::_('select.option', '0', JText::_('JEV_NO'));
		$options[] = JHTML::_('select.option', '1', JText::_('JEV_YES'));
		$plist = JHTML::_('select.genericlist', $options, 'hidepast', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $hidepast);


		$catData = JEV_CommonFunctions::getCategoryData();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		// Get/Create the model
		if ($model =  $this->getModel("icalevent", "icaleventsModel")) {
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		// Set the layout
		$this->view->setLayout('overview');

		$this->view->assign('rows', $rows);

		$this->view->assign('largeDataSet', $this->_largeDataSet);
		$this->view->assign('clist', $clist);
		$this->view->assign('plist', $plist);
		$this->view->assign('search', $search);
		$this->view->assign('pageNav', $pageNav);

		$this->view->display();

	}

	function editcopy()
	{
		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor)
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}
		$this->editCopy = true;
		$this->edit();

	}

	function edit($key = NULL, $urlVar = NULL)
	{
		$jinput = JFactory::getApplication()->input;

		// get the view
		if (JFactory::getApplication()->isAdmin()){
			$this->view = $this->getView("icalevent", "html", "AdminIcaleventView");
		}
		else {
			$this->view = $this->getView("icalevent", "html");
		}

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "icaleventsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$cid = $jinput->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);
		if (is_array($cid) && count($cid) > 0)
			$id = $cid[0];
		else
			$id = 0;

		// front end passes the id as evid
		if ($id == 0)
		{
			$id = $jinput->getInt("evid", 0);
		}

		// we check if user can edit specific event in about 30 lines time
		if (!JEVHelper::isEventCreator() && !JEVHelper::isEventEditor())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$repeatId = 0;

		$db = JFactory::getDbo();

		// iCal agid uses GUID or UUID as identifier
		if ($id > 0)
		{
			if ($repeatId == 0)
			{
				// this version gives us a repeat not an event so
				//$row = $this->queryModel->getEventById($id, true, "icaldb");
				$vevent = $this->dataModel->queryModel->getVEventById($id);
				if (!$vevent)
				{
					$Itemid = $jinput->getInt("Itemid");
					JFactory::getApplication()->redirect(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$Itemid", false), JText::_("JEV_SORRY_UPDATED"));
				}

				$row = new jIcalEventDB($vevent);

				$row->fixDtstart();
			}
			else
			{
				$row = $this->queryModel->listEventsById($repeatId, true, "icaldb");
			}
			// for some reason frequency is not always set for imported events !
			if (!$row->freq())
			{
				$row->freq('none');
			}

			if (!JEVHelper::canEditEvent($row))
			{
				throw new Exception( JText::_('ALERTNOTAUTH'), 403);
				return false;
			}
		}
		else
		{
			$vevent = new iCalEvent($db);
			$vevent->set("freq", "NONE");
			$vevent->set("description", "");
			$vevent->set("summary", "");
			$vevent->set("access", "1");
			list($year, $month, $day) = JEVHelper::getYMD();

			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$defaultstarttime = $params->get("defaultstarttime", "08:00");
			$defaultendtime = $params->get("defaultendtime", "17:00");
			list($starthour, $startmin) = explode(":", $defaultstarttime);
			list($endhour, $endmin) = explode(":", $defaultendtime);

			$vevent->set("dtstart", JevDate::mktime((int)$starthour, $startmin, 0, $month, $day, $year));
			$vevent->set("dtend", JevDate::mktime((int)$endhour, $endmin, 0, $month, $day, $year));
			$row = new jIcalEventDB($vevent);
			if ($params->get('default_alldayevent', 0) == 1) {
				$row->_alldayevent = 1;
			}
			if ($params->get('default_noendtime', 0) == 1) {
				$row->_noendtime = 1;
			}
			// TODO - move this to class!!
			// populate with meaningful initial values
			$row->starttime($defaultstarttime);
			$row->endtime($defaultendtime);
		}

		$glist = JEventsHTML::buildAccessSelect(intval($row->access()), 'class="inputbox" size="1"');

		// get all the raw native calendars
		$nativeCals = $this->dataModel->queryModel->getNativeIcalendars();

		// Strip this list down based on user permissions
		$jevuser =  JEVHelper::getAuthorisedUser();
		if ($jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all")
		{
			$cals = array_keys($nativeCals);
			$allowedcals = explode("|", $jevuser->calendars);
			foreach ($cals as $calid)
			{
				if (!in_array($calid, $allowedcals))
					unset($nativeCals[$calid]);
			}
		}

		// Are we allowed to edit events within a URL based iCal
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("allowedit", 0) && $row->icsid() > 0)
		{
			$calsql = 'SELECT * FROM #__jevents_icsfile WHERE ics_id=' . intval($row->icsid());
			$db->setQuery($calsql);
			$cal = $db->loadObject();
			if ($cal && $cal->icaltype == 0)
			{
				$nativeCals[$cal->ics_id] = $cal;
				$this->view->assign("offerlock", 1);
			}
		}

		$excats = "0";
		if ($jevuser && $jevuser->categories != "" && $jevuser->categories != "all")
		{
			// Find which categories to exclude
			$catsql = 'SELECT id  FROM #__categories WHERE id NOT IN (' . str_replace("|", ",", $jevuser->categories) . ') AND extension="com_jevents"';

			$db->setQuery($catsql);
			$excats = implode(",", $db->loadColumn());
		}

		// only offer a choice of native calendars if it exists!
		if (count($nativeCals) > 1)
		{
			$icalList = array();
			$icalList[] = JHTML::_('select.option', '0', JText::_('JEV_EVENT_CHOOSE_ICAL'), 'ics_id', 'label');
			$icalList = array_merge($icalList, $nativeCals);

			$row_icsid = $row->icsid();

			if ($params->get('defaultcal', 0) && !$row_icsid) {
				$row_icsid = count($nativeCals) > 0 ? current($nativeCals)->ics_id : 0;
			}
			$clist = JHTML::_('select.genericlist', $icalList, 'ics_id', " onchange='preselectCategory(this);'", 'ics_id', 'label', $row_icsid);

			$this->view->assign('clistChoice', true);
			$this->view->assign('defaultCat', 0);
		}
		else
		{
			if (count($nativeCals) == 0 || !is_array($nativeCals))
			{

				JFactory::getApplication()->enqueueMessage('870 -' . JText::_('INVALID_CALENDAR_STRUCTURE'), 'warning');

			}

			$icsid = $row->icsid() > 0 ? $row->icsid() : (count($nativeCals) > 0 ? current($nativeCals)->ics_id : 0);

			$clist = '<input type="hidden" name="ics_id" value="' . $icsid . '" />';
			$this->view->assign('clistChoice', false);
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("defaultcat", false))
			{
				$this->view->assign('defaultCat', current($nativeCals)->catid);
			}
			else
			{
				$this->view->assign('defaultCat', 0);
			}
		}

		// Set the layout
		$this->view->setLayout('edit');

		$this->view->assign('editCopy', $this->editCopy);
		$this->view->assign('id', $id);
		$this->view->assign('row', $row);
		$this->view->assign('excats', $excats);
		$this->view->assign('nativeCals', $nativeCals);
		$this->view->assign('clist', $clist);
		$this->view->assign('repeatId', $repeatId);
		$this->view->assign('glist', $glist);
		
		// for Admin interface only
		$this->view->assign('with_unpublished_cat', JFactory::getApplication()->isAdmin());
		$this->view->assignRef('dataModel', $this->dataModel);
		
		// Keep following fields for backwards compataibility only
		// only those who can publish globally can set priority field
		if (JEVHelper::isEventPublisher(true))
		{
			$list = array();
			for ($i = 0; $i < 10; $i++)
			{
				$list[] = JHTML::_('select.option', $i, $i, 'val', 'text');
			}
			$priorities = JHTML::_('select.genericlist', $list, 'priority', "", 'val', 'text', $row->priority());
			$this->view->assign('setPriority', true);
			$this->view->assign('priority', $priorities);
		}
		else
		{
			$this->view->assign('setPriority', false);
		}

		$this->view->display();

	}

	function translate()
	{
		$jinput = JFactory::getApplication()->input;

		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor)
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		// get the view
		if (JFactory::getApplication()->isAdmin()){
			$this->view = $this->getView("icalevent", "html", "AdminIcaleventView");
		}
		else {
			$this->view = $this->getView("icalevent", "html");
		}

		$ev_id = $jinput->getInt("ev_id", 0);
		$evdet_id = $jinput->getInt("evdet_id",  $jinput->getInt("trans_evdet_id",0));

		// check editing permission
		if ($ev_id > 0 && $evdet_id >0)
		{
			// this version gives us a repeat not an event so
			$vevent = $this->dataModel->queryModel->getVEventById($ev_id);
			if (!$vevent)
			{
				$Itemid = $jinput->getInt("Itemid");
				JFactory::getApplication()->redirect(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$Itemid", false), JText::_("JEV_SORRY_UPDATED"));
			}

			$row = new jIcalEventDB($vevent);

			if (!JEVHelper::canEditEvent($row))
			{
				throw new Exception( JText::_('ALERTNOTAUTH'), 403);
				return false;
			}
                        $this->view->assign("row",$row);

		}
		else {
			throw new Exception(  "No event details passed in to translation script", 403);
			return false;
		}

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "icaleventsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		// Set the layout
		$this->view->setLayout('translate');

		$this->view->display();

	}

	function savetranslation ()
	{
		JSession::checkToken('request') or jexit('Invalid Token');

		$jinput = JFactory::getApplication()->input;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$ev_id = $jinput->getInt("ev_id", 0);
		$evdet_id = $jinput->getInt("evdet_id",  $jinput->getInt("trans_evdet_id",0));

		// check editing permission
		if ($ev_id > 0 && $evdet_id >0)
		{
			// this version gives us a repeat not an event so
			$vevent = $this->dataModel->queryModel->getVEventById($ev_id);
			if (!$vevent)
			{
				$Itemid = $jinput->getInt("Itemid");
				JFactory::getApplication()->redirect(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$Itemid", false), JText::_("JEV_SORRY_UPDATED"));
			}

			$row = new jIcalEventDB($vevent);

			if (!JEVHelper::canEditEvent($row))
			{
				throw new Exception( JText::_('ALERTNOTAUTH'), 403);
				return false;
			}
		}
		else {
			throw new Exception( "No event details passed in to translation script", 500);
			return false;
		}

		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "icaleventsModel"))
		{
			$model->saveTranslation();
		}

		ob_end_clean();
		if (!headers_sent())
		{
			header('Content-Type:text/html;charset=utf-8');
		}
		$link = JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.list", false);
		?>
		<script type="text/javascript">
			window.parent.location="<?php echo $link; ?>";
		</script>
		<?php
		exit();

	}

	function deletetranslation ()
	{
		JSession::checkToken('request') or jexit('Invalid Token');

		$jinput = JFactory::getApplication()->input;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$ev_id = $jinput->getInt("ev_id", 0);
		$evdet_id = $jinput->getInt("evdet_id",  $jinput->getInt("trans_evdet_id", 0));

		// check editing permission
		if ($ev_id > 0 && $evdet_id >0)
		{
			// this version gives us a repeat not an event so
			$vevent = $this->dataModel->queryModel->getVEventById($ev_id);
			if (!$vevent)
			{
				$Itemid = $jinput->getInt("Itemid");
				JFactory::getApplication()->redirect(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$Itemid", false), JText::_("JEV_SORRY_UPDATED"));
			}

			$row = new jIcalEventDB($vevent);

			if (!JEVHelper::canEditEvent($row))
			{
				throw new Exception( JText::_('ALERTNOTAUTH'), 403);
				return false;
			}
		}
		else {
			throw new Exception( "No event details passed in to translation script", 500);
			return false;
		}

		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "icaleventsModel"))
		{
			$model->deleteTranslation();
		}

		ob_end_clean();
		if (!headers_sent())
		{
			header('Content-Type:text/html;charset=utf-8');
		}
		$link = JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.list", false);
		?>
		<script type="text/javascript">
			window.parent.location="<?php echo $link; ?>";
		</script>
		<?php
		exit();

	}

	function save($key = NULL, $urlVar = NULL)
	{
		$jinput = JFactory::getApplication()->input;

		$msg = "";
		$event = $this->doSave($msg);

		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.list', $msg);
			$this->redirect();
		}
		else
		{
			$popupdetail = JPluginHelper::getPlugin("jevents", "jevpopupdetail");
			if ($popupdetail) {
				$pluginparams = new JRegistry($popupdetail->params);
				$popupdetail = $pluginparams->get("detailinpopup",1);
				if ($popupdetail) {
					$popupdetail = "&pop=1&tmpl=component";
				}
				else {
					$popupdetail = "";
				}
			}
			else {
				$popupdetail = "";
			}

			$Itemid = $jinput->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();

			// When editing an event from a specific repeat page we want to return to that specific repeat
			if ($event && intval($event->rp_id())==0){
				if ($jinput->getInt("rp_id", 0)){
					$tempevent = $this->dataModel->queryModel->listEventsById($jinput->getInt("rp_id", 0), true);
					if ($tempevent){
						$event = $tempevent;
					}
					else {
						$event = $event->getFirstRepeat();
					}
				}
				else {
					$event = $event->getFirstRepeat();
				}
			}

			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0) || $popupdetail)
			{
				ob_end_clean();
				if (!headers_sent() && $popupdetail=="")
				{
					header('Content-Type:text/html;charset=utf-8');
				}
				if ($event){
					$year = $event->yup();
					$month = $event->mup();
					$day = $event->dup();
					$jinput->set("year", $year);
					$jinput->set("month", $month);
					$jinput->set("day", $day);
				}
				if ($event && $event->state())
				{
					$link = JRoute::_($event->viewDetailLink($year, $month, $day, false , $Itemid)."&published_fv=-1$popupdetail");
				}
				else
				{
					if (JFactory::getUser()->id>0){
						$link = JRoute::_($event->viewDetailLink($year, $month, $day, false , $Itemid)."&published_fv=-1$popupdetail");
					}
					else {
						$link = JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid$popupdetail", false);
					}
				}
				if ($popupdetail!=""){
					// redirect to event detail page within popup window
					$this->setRedirect($link, $msg);
					$this->redirect();
					return;
				}
				else {
				?>
				<script type="text/javascript">
					window.parent.alert("<?php echo $msg; ?>");
					window.parent.location="<?php echo $link; ?>";
				</script>
				<?php
				exit();
				}
			}

			// if the event is published then return to the event
			if ($event && $event->state())
			{
				list($year, $month, $day) = JEVHelper::getYMD();
				$this->setRedirect($event->viewDetailLink($year, $month, $day, false, $Itemid), $msg);
				$this->redirect();
			}
			else
			{
				if (JFactory::getUser()->id>0){
					$this->setRedirect(JRoute::_($event->viewDetailLink($year, $month, $day, false , $Itemid)."&published_fv=-1"), $msg);
					$this->redirect();
				}
				else {
					// I can't go back to the same repetition since its id has been lost
					$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid", false), $msg);
					$this->redirect();
				}
			}
		}

	}

	function savenew()
	{

		$msg = "";
		$event = $this->doSave($msg);

		$jinput = JFactory::getApplication()->input;

		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.edit', $msg);
			$this->redirect();
		}
		else
		{
			$Itemid = $jinput->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();

			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0))
			{
				ob_end_clean();
				if (!headers_sent())
				{
					header('Content-Type:text/html;charset=utf-8');
				}
				if ($event && $event->state())
				{
					$link = $event->viewDetailLink($year, $month, $day, true . $Itemid);
				}
				else
				{
					$link = JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid", false);
				}
				?>
				<script type="text/javascript">
					window.parent.alert("<?php echo $msg; ?>");
					window.parent.location="<?php echo $link; ?>";
				</script>
				<?php
				exit();
			}

			// if the event is published then return to the event
			if ($event && $event->state())
			{
				list($year, $month, $day) = JEVHelper::getYMD();
				$this->setRedirect($event->viewDetailLink($year, $month, $day, false, $Itemid), $msg);
				$this->redirect();
			}
			else
			{
				// I can't go back to the same repetition since its id has been lost
				$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid", false), $msg);
				$this->redirect();
			}
		}

	}

	function apply()
	{
		$jinput = JFactory::getApplication()->input;

		$msg = "";
		$event = $this->doSave($msg);

		// reload the event to get the reptition ids
		$evid = intval($event->ev_id());
		$testevent = $this->queryModel->getEventById($evid, 1, "icaldb");
		$rp_id = $testevent->rp_id();
                if (!$rp_id){
                    JFactory::getApplication()->enqueueMessage(JText::_("JEV_CANNOT_DISPLAY_SAVED_EVENT_ON_THIS_MENU_ITEM", "WARNING"));
                    return;
                }
		list($year, $month, $day) = JEVHelper::getYMD();

		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.edit&evid=$evid&rp_id=$rp_id&year=$year&month=$month&day=$day", $msg);
			$this->redirect();
		}
		else
		{
			$Itemid = $jinput->getInt("Itemid");

			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0))
			{
				ob_end_clean();
				?>
				<script type="text/javascript">				
					window.parent.alert("<?php echo $msg; ?>");
					window.location="<?php echo JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.edit&evid=$evid&rp_id=$rp_id&year=$year&month=$month&day=$day&Itemid=$Itemid", false); ?>";
				</script>
				<?php
				exit();
			}

			// return to the event
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.edit&evid=$evid&rp_id=$rp_id&year=$year&month=$month&day=$day&Itemid=$Itemid", false), $msg);
			$this->redirect();
		}

	}

	function csvimport()
	{

		if (!JFactory::getApplication()->isAdmin())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		// get the view
		$this->view = $this->getView("icalevent", "html");

		// get all the raw native calendars
		$nativeCals = $this->dataModel->queryModel->getNativeIcalendars();

		// Strip this list down based on user permissions
		$jevuser =  JEVHelper::getAuthorisedUser();
		if ($jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all")
		{
			$cals = array_keys($nativeCals);
			$allowedcals = explode("|", $jevuser->calendars);
			foreach ($cals as $calid)
			{
				if (!in_array($calid, $allowedcals))
					unset($nativeCals[$calid]);
			}
		}
		// only offer a choice of native calendars if it exists!
		if (count($nativeCals) > 0)
		{
			$icalList = array();
			$icalList[] = JHTML::_('select.option', '0', JText::_('JEV_EVENT_CHOOSE_ICAL'), 'ics_id', 'label');
			$icalList = array_merge($icalList, $nativeCals);
			$callist = JHTML::_('select.genericlist', $icalList, 'ics_id', " onchange='preselectCategory(this);'", 'ics_id', 'label', 0);
			$this->view->assign('callist', $callist);
		}
		else
		{

			JFactory::getApplication()->enqueueMessage('870 -' . JText::_('INVALID_CALENDAR_STRUCTURE'), 'warning');

		}

		// Set the layout
		$this->view->setLayout('csvimport');

		$this->view->display();

	}

	private function doSave(& $msg)
	{
		if (!JEVHelper::isEventCreator() && !JEVHelper::isEventEditor())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
                
		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);
		$jinput = JFactory::getApplication()->input;
		$array  = $jinput->getArray(array(), null, 'RAW');

		if (version_compare(JVERSION, '3.7.1', '>=') && !$params->get('allowraw', 0))
        {

			$filter = JFilterInput::getInstance(null, null, 1, 1);

			//Joomla! no longer provides HTML allowed in JInput so we need to fetch raw
			//Then filter on through with JFilterInput to HTML

			foreach ($array as $key => $row)
			{
				//Single row check
				if (!is_array($row))
				{
					$array[$key] = $filter->clean($row, 'HTML');
				}
				else
				{
					//1 Deep row check
					foreach ($array[$key] as $key1 => $sub_row)
					{
						//2 Deep row check
						if (!is_array($sub_row))
						{
							$array[$key][$key1] = $filter->clean($sub_row, 'HTML');
						}
						else
						{
							foreach ($sub_row as $key2 => $sub_sub_row)
							{
								$array[$key][$key1][$key2] = $filter->clean($sub_sub_row, 'HTML');
							}
						}
					}
				}
			}
		}

		// Should we allow raw content through unfiltered
		if ($params->get("allowraw", 0))
		{
			$array['jevcontent'] = $jinput->post->get("jevcontent", "", RAW);
			$array['extra_info'] = $jinput->post->get("extra_info", "", RAW);
		}
		// convert nl2br if there is no HTML
		if (strip_tags($array['jevcontent']) == $array['jevcontent'])
		{
			$array['jevcontent'] = nl2br($array['jevcontent']);
		}
		if (strip_tags($array['extra_info']) == $array['extra_info'])
		{
			$array['extra_info'] = nl2br($array['extra_info']);
		}

		// convert event data to objewct so we can test permissions
		$eventobj = new stdClass();
		foreach ($array as $key => $val)
		{
			$newkey = "_" . $key;
			$eventobj->$newkey = $val;
		}
		$eventobj->_icsid = $eventobj->_ics_id;
		if (isset($eventobj->_catid) && is_array($eventobj->_catid))
		{
			$eventobj->_catid = current($eventobj->_catid);
		}

		if (!JEVHelper::canCreateEvent($eventobj) && !JEVHelper::isEventEditor())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$rrule = SaveIcalEvent::generateRRule($array);

		// ensure authorised
		if (isset($array["evid"]) && intval($array["evid"]) > 0)
		{
			$event = $this->queryModel->getEventById(intval($array["evid"]), 1, "icaldb");
			if (!$event || !JEVHelper::canEditEvent($event))
			{
				throw new Exception( JText::_('ALERTNOTAUTH'), 403);
				return false;
			}
		}

		$clearout = false;
		// remove all exceptions since they are no longer needed
		if (isset($array["evid"]) && intval($array["evid"])> 0 && $jinput->getInt("updaterepeats", 1))
		{
			$clearout = true;
		}

		if ($event = SaveIcalEvent::save($array, $this->queryModel, $rrule))
		{
			$row = new jIcalEventRepeat($event);
			if (!JEVHelper::canPublishEvent($row) && !$event->state)
			{
				$msg = JText::_("EVENT_SAVED_UNDER_REVIEW", true);
			}
			else
			{
				$msg = JText::_("Event_Saved", true);
			}
			if ($clearout)
			{
				$db = JFactory::getDbo();
				$query = "DELETE FROM #__jevents_exception WHERE eventid = " . intval($array["evid"]);
				$db->setQuery($query);
				$db->execute();
				// TODO clear out old exception details
			}
		}
		else
		{
			$msg = JText::_("Event Not Saved", true);
			$row = null;
		}

		return $row;

	}

	function close()
	{
		ob_end_clean();
		?>
		<script type="text/javascript">
			try {
				window.parent.jQuery('#myEditModal').modal('hide');
			}
			catch (e){}
			try {
				window.parent.SqueezeBox.close();
			}
			catch (e){}
			try {
				window.parent.closedialog();
			}
			catch (e){}
		</script>
		<?php
		exit();

	}

	function publish()
	{
		$jinput = JFactory::getApplication()->input;

		$cid = $jinput->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleICalEventPublish($cid, 1);

	}

	function unpublish()
	{
		$jinput = JFactory::getApplication()->input;

		$cid = $jinput->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleICalEventPublish($cid, 0);
	}

	function delete()
	{

		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		$jinput = JFactory::getApplication()->input;

		$cid = $jinput->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);

		// front end passes the id as evid
		if (count($cid) == 1 && $cid[0] == 0)
		{
			$cid = array($jinput->getInt("evid", 0));
		}

		$db = JFactory::getDbo();

		foreach ($cid as $key => $id)
		{
			// I should be able to do this in one operation but that can come later
			$event = $this->queryModel->getEventById(intval($id), 1, "icaldb");
			if (is_null($event) || !JEVHelper::canDeleteEvent($event))
			{

				JFactory::getApplication()->enqueueMessage('870 -' . JText::_('JEV_NO_DELETE_ROW'), 'warning');

				unset($cid[$key]);
			}
		}

		$newstate = -1;
		foreach ($cid as $key => $id)
		{
			$sql = "UPDATE #__jevents_vevent SET state=$newstate where ev_id='" . $id . "'";
			$db->setQuery($sql);
			$db->execute();

		}

		// I also need to trigger any onpublish event triggers
		$dispatcher = JEventDispatcher::getInstance();
		// just incase we don't have jevents plugins registered yet
		JPluginHelper::importPlugin("jevents");
		$res = $dispatcher->trigger('onPublishEvent', array($cid, $newstate));

		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.list', JTEXT::_("JEV_EVENT_STATE_CHANGED"));
			$this->redirect();
		}
		else
		{
			$Itemid = $jinput->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();
			$rettask = $jinput->getString("rettask", "day.listevents");
			// Don't return to the event detail since we may be filtering on published state!
			//$this->setRedirect( JRoute::_('index.php?option=' . JEV_COM_COMPONENT. "&task=icalrepeat.detail&evid=$id&year=$year&month=$month&day=$day&Itemid=$Itemid",false),"IcalEvent  : New published state Saved");
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&year=$year&month=$month&day=$day&Itemid=$Itemid", false), JText::_('JEV_EVENT_DELETE_STATE_SAVED'));
			$this->redirect();
		}

	}

	protected function toggleICalEventPublish($cid, $newstate)
	{
	    $jinput = JFactory::getApplication()->input;
		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		// Must be at least an event creator to publish events
		$is_event_editor = JEVHelper::isEventPublisher();
		if (!$is_event_editor)
		{
			if (is_array($cid))
			{
				foreach ($cid as $id)
				{
					if (!JEVHelper::canPublishOwnEvents($id))
					{
						throw new Exception( JText::_('ALERTNOTAUTH'). " 1", 403);
						return false;
					}
				}
			}
			$is_event_editor = true;
		}
		if (!$is_event_editor)
		{
			throw new Exception( JText::_('ALERTNOTAUTH'). " 2", 403);
			return false;
		}

		$db = JFactory::getDbo();
		foreach ($cid as $id)
		{

			// I should be able to do this in one operation but that can come later
			$event = $this->queryModel->getEventById(intval($id), 1, "icaldb");
			if (is_null($event))
			{
                                /*echo $db->getQuery()."<br/>";return;*/
				throw new Exception( JText::_('ALERTNOTAUTH'). " 3", 403);
				return false;
			}
                        else if ( !JEVHelper::canPublishEvent($event))
                        {
				throw new Exception( JText::_('ALERTNOTAUTH'). " 4", 403);
				return false;
                        }
                            
			$sql = "UPDATE #__jevents_vevent SET state=$newstate where ev_id='" . $id . "'";
			$db->setQuery($sql);
			$db->execute();

			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($newstate == 1 && $params->get("com_notifyauthor", 0) && !$event->_author_notified)
			{
				$sql = "UPDATE #__jevents_vevent SET author_notified=1 where ev_id='" . $id . "'";
				$db->setQuery($sql);
				$db->execute();

				JEV_CommonFunctions::notifyAuthorPublished($event);
			}
		}

		// I also need to trigger any onpublish event triggers
		$dispatcher = JEventDispatcher::getInstance();
		// just incase we don't have jevents plugins registered yet
		JPluginHelper::importPlugin("jevents");
		$res = $dispatcher->trigger('onPublishEvent', array($cid, $newstate));
		$pub_filter = $jinput->get('published_fv', 0);

		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.list', JText::_('JEV_EVENT_PUBLISH_STATE_SAVED'));
			$this->redirect();
		}
		else
		{
			$Itemid = JRequest::getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();
			$rettask = JRequest::getString("rettask", "day.listevents");
			// Don't return to the event detail since we may be filtering on published state!
			//$this->setRedirect( JRoute::_('index.php?option=' . JEV_COM_COMPONENT. "&task=icalrepeat.detail&evid=$id&year=$year&month=$month&day=$day&Itemid=$Itemid",false),"IcalEvent  : New published state Saved");
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&year=$year&month=$month&day=$day&Itemid=$Itemid&published_fv=$pub_filter", false), JText::_('JEV_EVENT_PUBLISH_STATE_SAVED'));
			$this->redirect();
		}

	}

	function emptytrash()
	{
		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		/*
		  // This is covered by canDeleteEvent below
		  if (!JEVHelper::isEventDeletor()){
		  throw new Exception( JText::_('ALERTNOTAUTH'), 403);
		 return false;
		  }
		 */

		$app    = JFactory::getApplication();
		$jinput = $app->input;

		$cid = $jinput->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);

		// front end passes the id as evid
		if (count($cid) == 1 && $cid[0] == 0)
		{
			$cid = array($jinput->getInt("evid", 0));
		}

		$db = JFactory::getDbo();

		foreach ($cid as $key => $id)
		{
			// I should be able to do this in one operation but that can come later
			$event = $this->queryModel->getEventById(intval($id), 1, "icaldb");
			if (is_null($event) || !JEVHelper::canDeleteEvent($event))
			{
				$app->enqueueMessage('534 -' . JText::_('JEV_NO_DELETE_ROW'), 'warning');

				unset($cid[$key]);
			}
		}

		if (count($cid) > 0)
		{
			$veventidstring = implode(",", $cid);

			// TODO the ruccurences should take care of all of these??
			// This would fail if all recurrances have been 'adjusted'
			$query = "SELECT DISTINCT (eventdetail_id) FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
			$db->setQuery($query);
			$detailids = $db->loadColumn();
			$detailidstring = implode(",", $detailids);

			$query = "DELETE FROM #__jevents_rrule WHERE eventid IN ($veventidstring)";
			$db->setQuery($query);
			$db->execute();

			$query = "DELETE FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
			$db->setQuery($query);
			$db->execute();

			$query = "DELETE FROM #__jevents_exception WHERE eventid IN ($veventidstring)";
			$db->setQuery($query);
			$db->execute();

			$query = "DELETE FROM #__jevents_catmap WHERE evid IN ($veventidstring)";
			$db->setQuery($query);
			$db->execute();

			if (JString::strlen($detailidstring) > 0)
			{
				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN ($detailidstring)";
				$db->setQuery($query);
				$db->execute();

				// I also need to clean out associated custom data
				$dispatcher = JEventDispatcher::getInstance();
				// just incase we don't have jevents plugins registered yet
				JPluginHelper::importPlugin("jevents");
				$res = $dispatcher->trigger('onDeleteEventDetails', array($detailidstring));
			}

			$query = "DELETE FROM #__jevents_vevent WHERE ev_id IN ($veventidstring)";
			$db->setQuery($query);
			$db->execute();

			// I also need to delete custom data
			$dispatcher = JEventDispatcher::getInstance();
			// just incase we don't have jevents plugins registered yet
			JPluginHelper::importPlugin("jevents");
			$res = $dispatcher->trigger('onDeleteCustomEvent', array(&$veventidstring));

			if (JFactory::getApplication()->isAdmin())
			{
				$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.list", JTEXT::_("ICAL_EVENTS_DELETED"));
				$this->redirect();
			}
			else
			{
				$Itemid = $jinput->getInt("Itemid");
				list($year, $month, $day) = JEVHelper::getYMD();
				$rettask = $jinput->getString("rettask", "day.listevents");
				$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&year=$year&month=$month&day=$day&Itemid=$Itemid", false), JTEXT::_("ICAL_EVENT_DELETED"));
				$this->redirect();
			}
		}
		else
		{
			if ($app->isAdmin())
			{
				$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.list");
				$this->redirect();
			}
			else
			{
				$Itemid = $jinput->getInt("Itemid");
				list($year, $month, $day) = JEVHelper::getYMD();
				$rettask = $jinput-getString("rettask", "day.listevents");
				$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&year=$year&month=$month&day=$day&Itemid=$Itemid", false));
				$this->redirect();
			}
		}

	}

	function _checkValidCategories()
	{
		// TODO switch this after migration
		$component_name = "com_jevents";

		$db = JFactory::getDbo();
		$query = "SELECT COUNT(*) AS count FROM #__categories WHERE extension = '$component_name' AND `published` = 1;";  // RSH 9/28/10 added check for valid published, J!1.6 sets deleted categoris to published = -2
		$db->setQuery($query);
		$count = intval($db->loadResult());
		if ($count <= 0)
		{
			// RSH 9/28/10 - Added check for J!1.6 to use different URL for reroute
			$redirectURL = "index.php?option=com_categories&extension=" . JEV_COM_COMPONENT;
			$this->setRedirect($redirectURL, "You must first create at least one category");
			$this->redirect();
		}

	}

	function select()
	{
		JSession::checkToken('request') or jexit('Invalid Token');

		// get the view
		if (JFactory::getApplication()->isAdmin()){
			$this->view = $this->getView("icalevent", "html", "AdminIcaleventView");
		}
		else {
			$this->view = $this->getView("icalevent", "html");
		}

		$this->_checkValidCategories();

		$showUnpublishedICS = false;
		$showUnpublishedCategories = false;

		$db = JFactory::getDbo();

		$icsFile = intval(JFactory::getApplication()->getUserStateFromRequest("icsFile", "icsFile", 0));

		$catid = intval(JFactory::getApplication()->getUserStateFromRequest("catidIcalEvents", 'catid', 0));
		$catidtop = $catid;

		$state = 1;

		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 10));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("view{" . JEV_COM_COMPONENT . "}limitstart", 'limitstart', 0));
		$search = JFactory::getApplication()->getUserStateFromRequest("search{" . JEV_COM_COMPONENT . "}", 'search', '');
		$search = $db->escape(trim(strtolower($search)));

		$created_by = JFactory::getApplication()->getUserStateFromRequest("createdbyIcalEvents", 'created_by', 0);

		// Is this a large dataset ?
		$query = "SELECT count(rpt.rp_id) from #__jevents_repetition as rpt ";
		$db->setQuery($query);
		$totalrepeats = $db->loadResult();
		$this->_largeDataSet = 0;
		$cfg = JEVConfig::getInstance();
		if ($totalrepeats > $cfg->get('largeDataSetLimit', 100000))
		{
			$this->_largeDataSet = 1;
		}
		$cfg = JEVConfig::getInstance();
		$cfg->set('largeDataSet', $this->_largeDataSet);

		$where = array();
		$join = array();

		if ($search)
		{
			$where[] = "LOWER(detail.summary) LIKE '%$search%'";
		}

		$user = JFactory::getUser();

		// keep this incase we use filters in category lists
		$catwhere = "\n ev.catid IN(" . $this->queryModel->accessibleCategoryList() . ")";
		$params = JComponentHelper::getParams(JRequest::getCmd("option"));
		if ($params->get("multicategory", 0))
		{
			$join[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$join[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$where[] = " catmapcat.access  IN (" . JEVHelper::getAid($user) . ")";
			$where[] = " catmap.catid IN(" . $this->queryModel->accessibleCategoryList() . ")";
			$needsgroup = true;
			$catwhere = " 1";
		}
		$where[] = $catwhere;

		if ($catid > 0)
		{
			if ($params->get("multicategory", 0))
			{
				$where[] = "catmap.catid='$catid'";
			}
			else
			{
				$where[] = "ev.catid='$catid'";
			}
		}

		if ($created_by === "")
		{
			$where[] = "ev.created_by=0";
		}
		else
		{
			$created_by = intval($created_by);
			if ($created_by > 0)
				$where[] = "ev.created_by=" . $db->Quote($created_by);
		}

		if ($icsFile > 0)
		{
			$join[] = " #__jevents_icsfile as icsf ON icsf.ics_id = ev.icsid";
			$where[] = "\n icsf.ics_id = $icsFile";
			if (!$showUnpublishedICS)
			{
				$where[] = "\n icsf.state=1";
			}
		}
		else
		{
			if (!$showUnpublishedICS)
			{
				$join[] = " #__jevents_icsfile as icsf ON icsf.ics_id = ev.icsid";
				$where[] = "\n icsf.state=1";
			}
			else
			{
				$icsFrom = "";
			}
		}

		$user = JFactory::getUser();
		$where[] = "\n ev.access " . ' IN (' . JEVHelper::getAid($user) . ')' ;
		$where[] = "\n icsf.access " . ' IN (' . JEVHelper::getAid($user) . ')' ;

		$hidepast = intval(JFactory::getApplication()->getUserStateFromRequest("hidepast", "hidepast", 1));
		if ($hidepast)
		{
			$datenow = JevDate::getDate("-1 day");
			if (!$this->_largeDataSet)
			{
				$where[] = "\n rpt.endrepeat>'" . $datenow->toSql() . "'";
			}
		}
		if ($state == 1)
		{
			$where[] = "\n ev.state=1";
		}
		else if ($state == 2)
		{
			$where[] = "\n ev.state=0";
		}

		// get the total number of records
		$query = "SELECT count(distinct rpt.eventid)"
				. "\n FROM #__jevents_vevent AS ev "
				. "\n LEFT JOIN #__jevents_vevdetail as detail ON ev.detail_id=detail.evdet_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				//. "\n LEFT JOIN #__groups AS g ON g.id = ev.access"
				. ( count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '' )
				. ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' );
		$db->setQuery($query);
		//echo $db->_sql;
		$total = $db->loadResult();
		echo $db->getErrorMsg();
		if ($limit > $total)
		{
			$limitstart = 0;
		}

		// if anon user plugin enabled then include this information
		$anonfields = "";
		$anonjoin = "";
		if (JPluginHelper::importPlugin("jevents", "jevanonuser"))
		{

			$anonfields = ", ac.name as anonname, ac.email as anonemail";
			$anonjoin = "\n LEFT JOIN #__jev_anoncreator as ac on ac.ev_id = ev.ev_id";
		}

		$orderdir = JRequest::getCmd("filter_order_Dir", 'asc');
		$order = JRequest::getCmd("filter_order", 'start');
		$dir = $orderdir == "asc" ? "asc" : "desc";

		if ($order == 'start' || $order == 'starttime')
		{
			$order = ($this->_largeDataSet ? "\n ORDER BY detail.dtstart $dir" : "\n GROUP BY  ev.ev_id ORDER BY rpt.startrepeat $dir");
		}
		else if ($order == 'created')
		{
			$order = ($this->_largeDataSet ? "\n ORDER BY ev.created $dir" : "\n GROUP BY  ev.ev_id ORDER BY ev.created $dir");
		}
		else
		{
			$order = ($this->_largeDataSet ? "\n ORDER BY detail.summary $dir" : "\n GROUP BY  ev.ev_id ORDER BY detail.summary $dir");
		}
		$query = "SELECT ev.*, ev.state as evstate, detail.*, ev.created as created, a.title as _groupname " . $anonfields
				. "\n , rr.rr_id, rr.freq,rr.rinterval"//,rr.until,rr.untilraw,rr.count,rr.bysecond,rr.byminute,rr.byhour,rr.byday,rr.bymonthday"
				. ($this->_largeDataSet ? "" : "\n ,MAX(rpt.endrepeat) as endrepeat ,MIN(rpt.startrepeat) as startrepeat"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn")
				. "\n FROM #__jevents_vevent as ev "
				. ($this->_largeDataSet ? "" : "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id")
				. $anonjoin
				. "\n LEFT JOIN #__jevents_vevdetail as detail ON ev.detail_id=detail.evdet_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__viewlevels AS a ON a.id = ev.access"
				. ( count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '' )
				. ( count($where) ? "\n WHERE " . implode(' AND ', $where) : '' )
				. $order;
		if ($limit > 0)
		{
			$query .= "\n LIMIT $limitstart, $limit";
		}
		$db->setQuery($query);

		//echo $db->explain();
		$rows = $db->loadObjectList();
		foreach ($rows as $key => $val)
		{
			// set state variable to the event value not the event detail value
			$rows[$key]->state = $rows[$key]->evstate;
			$groupname = $rows[$key]->_groupname;
			$rows[$key] = new jIcalEventRepeat($rows[$key]);
			$rows[$key]->_groupname = $groupname;
		}
		if ($this->_debug)
		{
			echo '[DEBUG]<br />';
			echo 'query:';
			echo '<pre>';
			echo $query;
			echo '-----------<br />';
			echo 'option "' . JEV_COM_COMPONENT . '"<br />';
			echo '</pre>';
			//die( 'userbreak - mic ' );
		}

		if ($db->getErrorNum())
		{
			echo $db->stderr();
			return false;
		}

		// get list of categories
		$attribs = 'class="inputbox" size="1" onchange="document.adminForm.submit();"';
		$clist = JEventsHTML::buildCategorySelect($catid, $attribs, null, $showUnpublishedCategories, false, $catidtop, "catid");

		// get list of ics Files
		$icsfiles = array();
		//$icsfiles[] =  JHTML::_('select.option', '0', "Choose ICS FILE" );
		$icsfiles[] = JHTML::_('select.option', '-1', JText::_('ALL_ICS_FILES'));

		$query = "SELECT ics.ics_id as value, ics.label as text FROM #__jevents_icsfile as ics ";
		if (!$showUnpublishedICS)
		{
			$query .= " WHERE ics.state=1";
		}
		$query .= " ORDER BY ics.isdefault DESC, ics.label ASC";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$icsfiles = array_merge($icsfiles, $result);
		$icslist = JHTML::_('select.genericlist', $icsfiles, 'icsFile', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $icsFile);

		// get list of creators
		$sql = "SELECT distinct u.id, u.name, u.username FROM #__jevents_vevent as jev LEFT JOIN #__users as u on u.id=jev.created_by ORDER BY u.name";
		$db = JFactory::getDbo();
		$db->setQuery($sql);
		$users = $db->loadObjectList();
		$userOptions = array();
		$userOptions[] = JHTML::_('select.option', "0", JText::_("JEV_EVENT_CREATOR"));
		foreach ($users as $user)
		{
			$userOptions[] = JHTML::_('select.option', $user->id, $user->name . " ($user->username)");
		}
		$userlist = JHTML::_('select.genericlist', $userOptions, 'created_by', 'class="inputbox" size="1"  onchange="document.adminForm.submit();"', 'value', 'text', $created_by);

		$options[] = JHTML::_('select.option', '0', JText::_('JEV_NO'));
		$options[] = JHTML::_('select.option', '1', JText::_('JEV_YES'));
		$plist = JHTML::_('select.genericlist', $options, 'hidepast', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $hidepast);

		$menulist = $this->targetMenu(0, "Itemid");

		$catData = JEV_CommonFunctions::getCategoryData();

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		// Set the layout
		$this->view->setLayout('select');

		$this->view->assign('rows', $rows);
		$this->view->assign('userlist', $userlist);
		$this->view->assign('menulist', $menulist);
		$this->view->assign('clist', $clist);
		$this->view->assign('plist', $plist);
		$this->view->assign('search', $search);
		$this->view->assign('icsList', $icslist);
		$this->view->assign('pageNav', $pageNav);

		$this->view->display();

	}

	private function targetMenu($itemid = 0, $name)
	{
		$db = JFactory::getDbo();

		// assemble menu items to the array
		$options = array();
		$options[] = JHTML::_('select.option', '', '- ' . JText::_('SELECT_ITEM') . ' -');

		// load the list of menu types
		// TODO: move query to model
		$query = 'SELECT menutype, title' .
				' FROM #__menu_types' .
				' ORDER BY title';
		$db->setQuery($query);
		$menuTypes = $db->loadObjectList();

		$menu =  JFactory::getApplication()->getMenu('site');
		$menuItems = $menu->getMenu();
		foreach ($menuItems as &$item)
		{

			if ($item->component == "com_jevents")
			{
				if (version_compare(JVERSION, '1.6.0', ">="))
				{
					$item->title = "*** " . $item->title . " ***";
				}
				else
				{
					$item->name = "*** " . $item->name . " ***";
				}
			}
			unset($item);
		}

		// establish the hierarchy of the menu
		$children = array();

		if ($menuItems)
		{
			// first pass - collect children
			foreach ($menuItems as $v)
			{
				$pt = 0; //(version_compare(JVERSION, '1.6.0', ">=")) ? $v->parent_id: $v->parent;  // RSH 10/4/10 in J!1.5 - parent was always 0, this changed in J!.16 to a real parent_id, so force id to 0 for compatibility
				$list = @$children[0] ? $children[0] : array();
				array_push($list, $v);
				$children[0] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		// assemble into menutype groups
		$n = count($list);
		$groupedList = array();
		foreach ($list as $k => $v)
		{
			$groupedList[$v->menutype][] = &$list[$k];
		}

		foreach ($menuTypes as $type)
		{
			$options[] = JHTML::_('select.option', $type->menutype, $type->title, 'value', 'text', true);  // these are disabled! (true)
			if (isset($groupedList[$type->menutype]))
			{
				$n = count($groupedList[$type->menutype]);
				for ($i = 0; $i < $n; $i++)
				{
					$item = &$groupedList[$type->menutype][$i];

					$disable = false;
					$text = (version_compare(JVERSION, '1.6.0', ">=")) ? '     ' . html_entity_decode($item->treename) : '&nbsp;&nbsp;&nbsp;' . $item->treename;
					$text = str_repeat("&nbsp;", (isset($item->level) ? $item->level : $item->sublevel) * 4) . $text;
					$options[] = JHTML::_('select.option', $item->id, $text, 'value', 'text', $disable);
				}
			}
		}

		return JHTML::_('select.genericlist', $options, '' . $name, 'class="inputbox"', 'value', 'text', $itemid, $name);

	}

}
