<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icalrepeat.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd,2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

use Joomla\Utilities\ArrayHelper;
use Joomla\String\StringHelper;

class AdminIcalrepeatController extends JControllerLegacy
{

	var $_debug = false;
	var $queryModel = null;

	/**
	 * Controler for the Ical Functions
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('list', 'overview');
		$this->registerDefaultTask("overview");

		$cfg = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
		$this->queryModel = new JEventsDBModel($this->dataModel);

		$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('finder');		
		
	}

	/**
	 * List Ical Repeats
	 *
	 */
	function overview()
	{
		$jinput = JFactory::getApplication()->input;

		$db = JFactory::getDbo();
		$publishedOnly = false;
		$cid = $jinput->get('cid', array(0),"array");
		$cid = ArrayHelper::toInteger($cid);

		if (is_array($cid) && count($cid) > 0)
			$id = $cid[0];
		else
			$id = $cid;

		// if cancelling a repeat edit then I get the event id a different way
		$evid = $jinput->getInt("evid", 0);
		if ($evid > 0)
		{
			$id = $evid;
		}


		$limit = intval(JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', JFactory::getApplication()->getCfg('list_limit',10)));
		$limitstart = intval(JFactory::getApplication()->getUserStateFromRequest("view{" . JEV_COM_COMPONENT . "}limitstart", 'limitstart', 0));

		$query = "SELECT count( DISTINCT rpt.rp_id)"
				. "\n FROM #__jevents_vevent as ev"
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n WHERE ev.ev_id=" . $id
				. "\n AND icsf.state=1"
				. ($publishedOnly ? "\n AND ev.state=1" : "");
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($limit > $total)
		{
			$limitstart = 0;
		}

		$query = "SELECT ev.*, rpt.*, rr.*, det.*"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_vevent as ev"
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n WHERE ev.ev_id=" . $id
				. "\n AND icsf.state=1"
				. ($publishedOnly ? "\n AND ev.state=1" : "")
				. "\n GROUP BY rpt.rp_id"
				. "\n ORDER BY rpt.startrepeat";
		if ($limit > 0)
		{
			$query .= "\n LIMIT $limitstart, $limit";
		}

		$db->setQuery($query);
		$icalrows = $db->loadObjectList();
		$icalcount = count($icalrows);
		for ($i = 0; $i < $icalcount; $i++)
		{
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}


		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		// get the view
		$this->view = $this->getView("icalrepeat", "html");

		// Set the layout
		$this->view->setLayout('overview');

		$this->view->assign('icalrows', $icalrows);
		$this->view->assign('pageNav', $pageNav);
		$this->view->assign('evid', $id);

		$this->view->display();

	}

	function edit($key = NULL, $urlVar = NULL)
	{
		// get the view
		$this->view = $this->getView("icalrepeat", "html");
 
                // Get/Create the model
		if ($model = $this->getModel("icalevent", "icaleventsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$app    = JFactory::getApplication();
		$jinput = $app->input;

		$db = JFactory::getDbo();
		$cid = $jinput->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);
		if (is_array($cid) && count($cid) > 0)
			$id = $cid[0];
		else
			$id = $cid;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		// front end passes the id as evid
		if ($id == 0)
		{
			$id = $jinput->getInt("evid", 0);
		}

		$db = JFactory::getDbo();
		$query = "SELECT rpt.eventid"
				. "\n FROM (#__jevents_vevent as ev, #__jevents_icsfile as icsf)"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n WHERE rpt.rp_id=" . $id
				. "\n AND icsf.ics_id=ev.icsid AND icsf.state=1";
		$db->setQuery($query);
		$ev_id = $db->loadResult();
		if ($ev_id == 0 || $id == 0)
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $ev_id, "ICal repeat does not exist");
			$this->redirect();
		}

		$repeatId = $id;

		$row = $this->queryModel->listEventsById($repeatId, true, "icaldb");

		if (!JEVHelper::canEditEvent($row))
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		//$glist = JEventsHTML::buildAccessSelect(intval($row->access()), 'class="inputbox" size="1"');

		// For repeats don't offer choice of ical or category
		// get all the raw native calendars
		$nativeCals = $this->dataModel->queryModel->getNativeIcalendars();

		$icsid = $row->icsid() > 0 ? $row->icsid() : current($nativeCals)->ics_id;
		$clist = '<input type="hidden" name="ics_id" value="' . $icsid . '" />';

		$this->view->assign('clistChoice', false);
		$this->view->assign('defaultCat', 0);

		// Set the layout
		$this->view->setLayout('edit');

		$this->view->assign('ev_id', $ev_id);
		$this->view->assign('rp_id', $repeatId);
		$this->view->assign('row', $row);
		$this->view->assign('nativeCals', $nativeCals);
		$this->view->assign('clist', $clist);
		$this->view->assign('repeatId', $repeatId);
		//$this->view->assign('glist', $glist);
		$this->view->assignRef('dataModel', $this->dataModel);
		$this->view->assign('editCopy', false);

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

		// for Admin interface only

		$this->view->assign('with_unpublished_cat', JFactory::getApplication()->isAdmin());

		$this->view->display();

	}

	function save($key = NULL, $urlVar = NULL)
	{

		$msg = "";
		$rpt = $this->doSave($msg);

		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $rpt->eventid, "".JText::_("JEV_ICAL_RPT_DETAILS_SAVED")."");
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
                    
			list($year, $month, $day) = JEVHelper::getYMD();
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0) || $popupdetail )
			{
                            $link = JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.detail&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid() . "&year=$year&month=$month&day=$day$popupdetail", false);
                            $msg = JText::_("JEV_ICAL_RPT_UPDATED",true );    
                            if ($popupdetail!=""){
                                    // redirect to event detail page within popup window
                                    $this->setRedirect($link, $msg);
				$this->redirect();
                                    return;
                            }
                            else {
                                ob_end_clean();
                                ?>
                                <script type="text/javascript">
                                        window.parent.alert("<?php echo $msg; ?>");
                                        window.parent.location="<?php echo $link; ?>";
                                </script>
                                <?php
                                exit();
                            }
			}
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.detail&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid() . "&year=$year&month=$month&day=$day", "".JText::_("JEV_ICAL_RPT_UPDATED")."");
			$this->redirect();
		}

	}

	function apply()
	{
		$msg = "";
		$rpt = $this->doSave($msg);

		$msg = JText::_("Event_Saved", true);
		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.edit&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid(), $msg);
			$this->redirect();
		}
		else
		{
			list($year, $month, $day) = JEVHelper::getYMD();
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0))
			{
				ob_end_clean();
				?>
				<script type="text/javascript">
					window.alert("<?php echo $msg; ?>");
					window.location="<?php echo JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.edit&evid=" . $rpt->rp_id . "&year=$year&month=$month&day=$day&Itemid=" . JEVHelper::getItemid(), false); ?>";
				</script>
				<?php
				exit();
			}
			// return to the event repeat
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.edit&evid=" . $rpt->rp_id . "&year=$year&month=$month&day=$day&Itemid=" . JEVHelper::getItemid(), false), $msg);
			$this->redirect();
		}

	}

	function select()
	{
		JSession::checkToken('request') or jexit('Invalid Token');

		$app    = JFactory::getApplication();
		$jinput = $app->input;

		$db = JFactory::getDbo();
		$publishedOnly = true;
		$id = $jinput->getInt('evid', 0);

		$limit = (int) JFactory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 10);
		$limitstart = (int) JFactory::getApplication()->getUserStateFromRequest("view{" . JEV_COM_COMPONENT . "}limitstart", 'limitstart', 0);

		$user = JFactory::getUser();
		$where[] = "\n ev.access IN ('" . JEVHelper::getAid($user) . "')";
		$where[] = "\n icsf.state=1 AND icsf.access IN ('" . JEVHelper::getAid($user) . "')";

		$query = "SELECT count( DISTINCT rpt.rp_id)"
				. "\n FROM #__jevents_vevent as ev"
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n WHERE ev.ev_id=" . $id
				. "\n AND icsf.state=1"
				. ( count($where) ? "\n AND " . implode(' AND ', $where) : '' )
				. ($publishedOnly ? "\n AND ev.state=1" : "");
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($limit > $total)
		{
			$limitstart = 0;
		}

		$query = "SELECT ev.*, rpt.*, rr.*, det.*"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_vevent as ev"
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n WHERE ev.ev_id=" . $id
				. "\n AND icsf.state=1"
				. ($publishedOnly ? "\n AND ev.state=1" : "")
				. ( count($where) ? "\n AND " . implode(' AND ', $where) : '' )
				. "\n GROUP BY rpt.rp_id"
				. "\n ORDER BY rpt.startrepeat";
		if ($limit > 0)
		{
			$query .= "\n LIMIT $limitstart, $limit";
		}

		$db->setQuery($query);
		$icalrows = $db->loadObjectList();
		$icalcount = count($icalrows);
		for ($i = 0; $i < $icalcount; $i++)
		{
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}

		$menulist = $this->targetMenu($jinput->getInt("Itemid"), "Itemid");

		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);

		// get the view
		$this->view = $this->getView("icalrepeat", "html");

		// Set the layout
		$this->view->setLayout('select');

		$this->view->assign('menulist', $menulist);
		$this->view->assign('icalrows', $icalrows);
		$this->view->assign('pageNav', $pageNav);
		$this->view->assign('evid', $id);

		$this->view->display();

	}

	private function doSave(& $msg)
	{
		if (!JEVHelper::isEventCreator())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		$option = JEV_COM_COMPONENT;
		$rp_id  = $jinput->getInt("rp_id", 0);
		$cid    = $jinput->get("cid", array());

		if (count($cid) > 0 && $rp_id == 0)
			$rp_id = (int) $cid[0];
		if ($rp_id == 0)
		{
			$this->setRedirect('index.php?option=' . $option . '&task=icalrepeat.list&cid[]=' . $rp_id, "1Cal rpt NOT SAVED");
			$this->redirect();
		}

		// I should be able to do this in one operation but that can come later
		$event = $this->queryModel->listEventsById((int) $rp_id, 1, "icaldb");
		if (!JEVHelper::canEditEvent($event))
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$db = JFactory::getDbo();
		$rpt = new iCalRepetition($db);
		$rpt->load($rp_id);

		$query = "SELECT detail_id FROM #__jevents_vevent WHERE ev_id=$rpt->eventid";
		$db->setQuery($query);
		$eventdetailid = $db->loadResult();

		$data["UID"] = $jinput->get("uid", md5(uniqid(rand(), true)));

		$data["X-EXTRAINFO"]    = $jinput->getString("extra_info", "");
		$data["LOCATION"]       = $jinput->getString("location", "");
		$data["allDayEvent"]    = $jinput->get("allDayEvent", "off");
                if ($data["allDayEvent"] == 1){
                    $data["allDayEvent"] = "on";
                }
		$data["CONTACT"]        = $jinput->getString("contact_info", "");
		// allow raw HTML (mask =2)
		$data["DESCRIPTION"]    = $jinput->get('jevcontent', '', 'RAW'); //JRequest::getVar("jevcontent", "", 'request', 'html', 2); No option for raw html in JInput, so just RAW the jinput instead.
		$data["publish_down"]   = $jinput->getString("publish_down", "2006-12-12");
		$data["publish_up"]     = $jinput->getString("publish_up", "2006-12-12");

		// Alternative date format handling
		if ($jinput->get("publish_up2", false)){
			$data["publish_up"]  = $jinput->getString("publish_up2",$data["publish_up"]);
		}
		if ($jinput->get("publish_down2",false)){
			$data["publish_down"] = $jinput->getString("publish_down2",$data["publish_down"]);
		}

		$interval = $jinput->get("rinterval", 1); //Not current in use
		$data["SUMMARY"] = $jinput->getString("title", "");

		$data["MULTIDAY"] = $jinput->get("multiday", "1");
		$data["NOENDTIME"] = $jinput->get("noendtime", 0);

		$ics_id = $jinput->get("ics_id", 0);

		if ($data["allDayEvent"] == "on")
		{
			$start_time = "00:00";
		}
		else
		{
			$start_time = $jinput->getString("start_time", "08:00");
		}

		$publishstart = $data["publish_up"] . ' ' . $start_time . ':00';
		$data["DTSTART"] = JevDate::strtotime($publishstart);

		if ($data["allDayEvent"] == "on")
		{
			$end_time = "23:59";
			$publishend = $data["publish_down"] . ' ' . $end_time . ':59';
		}
		else
		{
			if (isset($data["NOENDTIME"]) && $data["NOENDTIME"]){
				$end_time = '23:59:59';
			}
			else {
				$end_time = $jinput->getString("end_time", "15:00"). ':00';
			}
			$publishend = $data["publish_down"] . ' ' . $end_time ;
		}

		$data["DTEND"] = JevDate::strtotime($publishend);
		// iCal for whole day uses 00:00:00 on the next day JEvents uses 23:59:59 on the same day
		list ($h, $m, $s) = explode(":", $end_time . ':00');
		if (($h + $m + $s) == 0 && $data["allDayEvent"] == "on" && $data["DTEND"] > $data["DTSTART"])
		{
			$publishend = JevDate::strftime('%Y-%m-%d 23:59:59', ($data["DTEND"] - 86400));
			$data["DTEND"] = JevDate::strtotime($publishend);
		}

		$data["X-COLOR"] = $jinput->get("color", "");

		// Add any custom fields into $data array - allowing HTML (which can be cleaned up later by plugins)
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

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

		foreach ($array as $key => $value)
		{
			if (strpos($key, "custom_") === 0)
			{
				$data[$key] = $value;
			}
			// convert jform data to data format used before
			if (strpos($key,"jform")===0 && is_array($value)){
				foreach ($value as $cfkey => $cfvalue) {
					$data["custom_".$cfkey]=$cfvalue;
				}
			}			
		}

		$detail = iCalEventDetail::iCalEventDetailFromData($data);

		// if we already havea unique event detail then edit this one!
		if ($eventdetailid != $rpt->eventdetail_id)
		{
			$detail->evdet_id = $rpt->eventdetail_id;
		}

		$detail->priority = (int) ArrayHelper::getValue($array, "priority", 0);

		$detail->store();

		// KEEP THE ORIGINAL START REPEAT FOR THE EXCEPTION HANDLING
		$original_start = $rpt->startrepeat;

		// populate rpt with data
		//$start = JevDate::strtotime($data["publish_up"] . ' ' . $start_time . ':00');
		//$end = JevDate::strtotime($data["publish_down"] . ' ' . $end_time . ':00');
		$start = $data["DTSTART"];
		$end = $data["DTEND"];
		$rpt->startrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S', $start);
		$rpt->endrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S', $end);

		$rpt->duplicatecheck = md5($rpt->eventid . $start);
		$rpt->eventdetail_id = $detail->evdet_id;
		$rpt->rp_id = $rp_id;

		// Avoid SQL error on duplicate key insert.
		try
		{
			$store = $rpt->store();

			if (!$store){
				throw new RuntimeException(JText::_('JEV_COULD_NOT_SAVE_REPEAT_SAME_START_END'), 101);
			}

		} catch (RuntimeException $e) {

			if (JFactory::getApplication()->isAdmin())
			{
				$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $rpt->eventid, "" . JText::_("JEV_COULD_NOT_SAVE_REPEAT_SAME_START_END")."", "error");
				$this->redirect();
			}
			else
			{
				list($year, $month, $day) = JEVHelper::getYMD();
				$rettask = $jinput->getString("rettask", "day.listevents");
				$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid() . "&year=$year&month=$month&day=$day", "" . JText::_("JEV_COULD_NOT_SAVE_REPEAT_SAME_START_END")."", "error");
				$this->redirect();
			}
        }

		// I may also need to process repeat changes
		$dispatcher	= JEventDispatcher::getInstance();
		// just in case we don't have JEvents plugins registered yet
		JPluginHelper::importPlugin("jevents");
		$res = $dispatcher->trigger( 'onStoreCustomRepeat' , array(&$rpt));
		
		$exception = iCalException::loadByRepeatId($rp_id);
		if (!$exception)
		{
			$exception = new iCalException($db);
			$exception->bind(get_object_vars($rpt));
			// ONLY set the old start repeat when first creating the exception
			$exception->oldstartrepeat = $original_start;
		}
		$exception->exception_type = 1; // modified
		$exception->store();

		return $rpt;

	}

	// experimentaal code disabled for the time being
	function savefuture()
	{
		// experimentaal code disabled for count (startthe time being
		throw new Exception( JText::_('ALERTNOTAUTH'), 403);
		return false;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		echo "<pre>";
		$rpid = JRequest::getInt("rp_id", 0);
		$event = $this->queryModel->listEventsById($rpid, 1, "icaldb");
		$data = array();
		foreach (get_object_vars($event) as $key => $val)
		{
			if (strpos($key, "_") == 0)
			{
				$data[JString::substr($key, 1)] = $val;
			}
		}
		echo var_export($data, true);

		echo "\n\n";

		// Change the underlying event repeat rule details  !!
		$query = "SELECT * FROM #__jevents_rrule WHERE rr_id=" . $event->_rr_id;
		$db = JFactory::getDbo();
		$db->setQuery($query);
		$this->rrule = null;
		$this->rrule = $db->loadObject();
		$this->rrule = iCalRRule::iCalRRuleFromDB(get_object_vars($this->rrule));


		echo var_export($this->rrule, true);

		// TODO *** I must save the modified repeat rule
		// Create the copy of the event and reset the repeat rule to the new values
		foreach ($this->rrule->data as $key => $val)
		{
			$key = "_" . $key;
			$event->$key = $val;
		}
		$event->eventid = 0;
		$event->ev_id = 0;

		// create copy of rrule by resetting id and saving
		$this->rrule->rr_id = 0;
		$this->rrule->eventid = 0;
		if (intval($this->rrule->until) > 0)
		{
			// The until date is in the future so no need to do anything here
		}
		else
		{
			// count prior matching repetition
			$query = "SELECT count(startrepeat) FROM #__jevents_repetition WHERE eventid=" . $repeatdata->eventid . " AND startrepeat<'" . $repeatdata->startrepeat;
			$db->setQuery($query);
			$countrepeat = $db->loadResult();
			$this->rrule->count -= $countrepeat;
		}
		//$this->rrule->store();
		$event->_rr_id = $this->rrule->rr_id;

		//I must copy the event detail and its id too
		$eventDetail = new iCalEventDetail($db);
		$eventDetail->load($event->_eventdetail_id);
		$eventDetail->evdet_id = 0;
		$eventDetail->store();

		$event->_detail_id = $eventDetail->evdet_id;
		$event->_eventdetail_id = $eventDetail->evdet_id;

		// TODO I must now regenerate the repetitions
		$event->_rp_id = 0;

		$event->store();
		echo "</pre>";

		// TODO I must store a copy of the event id in the rrule table
		exit();

		// now delete exising current and future repeats - this resets the rrule for the truncated event
		$this->_deleteFuture();


		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $rpt->eventid, "".JText::_("JEV_ICAL_RPT_UPDATED")."");
			$this->redirect();
		}
		else
		{
			list($year, $month, $day) = JEVHelper::getYMD();
			$rettask = JRequest::getString("rettask", "day.listevents");
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid() . "&year=$year&month=$month&day=$day", "".JText::_("JEV_ICAL_RPT_UPDATED")."");
			$this->redirect();
		}

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

	function delete()
	{
		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$app    = JFactory::getApplication();
		$jinput = $app->input;
		$cid    = $jinput->get('cid', array(0), "array");

		if (!is_array($cid))
			$cid = array(intval($cid));
			$cid = ArrayHelper::toInteger($cid);

		$db = JFactory::getDbo();
		foreach ($cid as $id)
		{

			if ($id == 0)
				continue;

			// I should be able to do this in one operation but that can come later
			$event = $this->queryModel->listEventsById(intval($id), 1, "icaldb");
			if (!JEVHelper::canDeleteEvent($event))
			{
				throw new Exception( JText::_('ALERTNOTAUTH'), 403);
				return false;
			}

			// May want to send notification messages etc.
			$dispatcher = JEventDispatcher::getInstance();
			// just incase we don't have jevents plugins registered yet
			JPluginHelper::importPlugin("jevents");
			$res = $dispatcher->trigger('onDeleteEventRepeat', $id);

			$query = "SELECT * FROM #__jevents_repetition WHERE rp_id=$id";
			$db->setQuery($query);
			$data = null;
			$data = $db->loadObject();

			$query = "SELECT detail_id FROM #__jevents_vevent WHERE ev_id=$data->eventid";
			$db->setQuery($query);
			$eventdetailid = $db->loadResult();

			// only remove the detail id if its different for this repetition i.e. not the global one!
			if ($eventdetailid != $data->eventdetail_id)
			{
				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id = " . $data->eventdetail_id;
				$db->setQuery($query);
				$db->execute();

				// I also need to clean out associated custom data
				$dispatcher = JEventDispatcher::getInstance();
				// just incase we don't have jevents plugins registered yet
				JPluginHelper::importPlugin("jevents");
				$res = $dispatcher->trigger('onDeleteEventDetails', array($data->eventdetail_id));
			}

			// create exception based on deleted repetition
			$rp_id = $id;
			$exception = iCalException::loadByRepeatId($rp_id);
			if (!$exception)
			{
				$exception = new iCalException($db);
				$exception->bind(get_object_vars($data));
			}
			$exception->exception_type = 0; // deleted
			$exception->store();

			$query = "DELETE FROM #__jevents_repetition WHERE rp_id=$id";
			$db->setQuery($query);
			$db->execute();
		}

		if ($app->isAdmin())
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icalrepeat.list&cid[]=" . $data->eventid, JText::_("JEV_ICAL_REPEAT_DELETED"));
			$this->redirect();
		}
		else
		{
			$Itemid = $jinput->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid", false), JText::_("JEV_ICAL_REPEAT_DELETED"));
			$this->redirect();
		}

	}

	function deletefuture()
	{

		// clean out the cache
		$cache = JFactory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}

		$this->_deleteFuture();

		if (JFactory::getApplication()->isAdmin())
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icalrepeat.list&cid[]=" . $this->rrule->data["eventid"], JText::_("JEV_ICAL_REPEATS_DELETED"));
			$this->redirect();
		}
		else
		{
			$Itemid = JRequest::getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid"), JText::_("JEV_ICAL_REPEATS_DELETED"));
			$this->redirect();
		}

	}

	function _deleteFuture()
	{

	    $app    = JFactory::getApplication();
	    $jinput = $app->input;

		$cid = $jinput->getVar('cid', array(0));
		if (!is_array($cid))
			$cid = array(intval($cid));
		$cid = ArrayHelper::toInteger($cid);

		$db = JFactory::getDbo();
		foreach ($cid as $id)
		{

			// I should be able to do this in one operation but that can come later
			$event = $this->queryModel->listEventsById(intval($id), 1, "icaldb");
			if (!JEVHelper::canDeleteEvent($event))
			{
				throw new Exception( JText::_('ALERTNOTAUTH'), 403);
				return false;
			}

			$query = "SELECT * FROM #__jevents_repetition WHERE rp_id=$id";
			$db->setQuery($query);
			$repeatdata = null;
			$repeatdata = $db->loadObject();
			if (is_null($repeatdata))
			{
				throw new Exception( JText::_('NO_SUCH_EVENT'), 4777);
				return;
			}

			$query = "SELECT detail_id FROM #__jevents_vevent WHERE ev_id=$repeatdata->eventid";
			$db->setQuery($query);
			$eventdetailid = $db->loadResult();

			// Find detail ids for future repetitions that don't match the global event detail
			$query = "SELECT eventdetail_id FROM #__jevents_repetition WHERE eventid=" . $repeatdata->eventid . " AND startrepeat>='" . $repeatdata->startrepeat . "' AND eventdetail_id<>" . $eventdetailid;
			$db->setQuery($query);
			$detailids = $db->loadColumn();

			// Find repeat ids future repetitions 
			$query = "SELECT rp_id FROM #__jevents_repetition WHERE eventid=" . $repeatdata->eventid . " AND startrepeat>='" . $repeatdata->startrepeat . "'";
			$db->setQuery($query);
			$rp_ids = $db->loadColumn();


			foreach ($rp_ids as $rp_id)
			{
				// May want to send notification messages etc.
				$dispatcher = JEventDispatcher::getInstance();
				// just incase we don't have jevents plugins registered yet
				JPluginHelper::importPlugin("jevents");
				$res = $dispatcher->trigger('onDeleteEventRepeat', $rp_id);
			}


			// Change the underlying event repeat rule details  !!
			$query = "SELECT * FROM #__jevents_rrule WHERE eventid=$repeatdata->eventid";
			$db->setQuery($query);
			$this->rrule = null;
			$this->rrule = $db->loadObject();
			$this->rrule = iCalRRule::iCalRRuleFromDB(get_object_vars($this->rrule));
			if (intval($this->rrule->until) > 0)
			{
				// Find latest matching repetition
				$query = "SELECT max(startrepeat) FROM #__jevents_repetition WHERE eventid=" . $repeatdata->eventid . " AND startrepeat<'" . $repeatdata->startrepeat . "'";
				$db->setQuery($query);
				$lastrepeat = $db->loadResult();
				$this->rrule->until = JevDate::strtotime($lastrepeat);
			}
			else
			{
				// Find latest matching repetition
				$query = "SELECT count(startrepeat) FROM #__jevents_repetition WHERE eventid=" . $repeatdata->eventid . " AND startrepeat<'" . $repeatdata->startrepeat . "'";
				$db->setQuery($query);
				$countrepeat = $db->loadResult();
				$this->rrule->count = $countrepeat;
			}
			$this->rrule->store();

			if (!is_null($detailids) && count($detailids) > 0)
			{
				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN (" . implode(",", $detailids) . ")";
				$db->setQuery($query);
				$db->execute();

				// I also need to clean out associated custom data
				$dispatcher = JEventDispatcher::getInstance();
				// just incase we don't have jevents plugins registered yet
				JPluginHelper::importPlugin("jevents");
				$res = $dispatcher->trigger('onDeleteEventDetails', array(implode(",", $detailids)));
			}

			// setup exception data
			foreach ($rp_ids as $rp_id)
			{
				$query = "SELECT * FROM #__jevents_repetition WHERE rp_id=$rp_id";
				$db->setQuery($query);
				$data = null;
				$data = $db->loadObject();

				$exception = iCalException::loadByRepeatId($rp_id);
				if (!$exception)
				{
					$exception = new iCalException($db);
					$exception->bind(get_object_vars($data));
				}
				$exception->exception_type = 0; // deleted
                $exception->store();
			}
			$query = "DELETE FROM #__jevents_repetition WHERE eventid=" . $repeatdata->eventid . " AND startrepeat>='" . $repeatdata->startrepeat . "'";
			$db->setQuery($query);
			$db->execute();

			// Also clear out defunct exceptions
			$query = "DELETE FROM #__jevents_exception WHERE eventid=" . $repeatdata->eventid . " AND startrepeat>='" . $repeatdata->startrepeat . "' and exception_type=1 ";
			$db->setQuery($query);
			$db->execute();
		}

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
