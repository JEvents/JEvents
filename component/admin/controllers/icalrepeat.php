<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icalrepeat.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd,2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Session\Session;
use Joomla\String\StringHelper;
use Joomla\CMS\Pagination\Pagination;

#[AllowDynamicProperties]
class AdminIcalrepeatController extends Joomla\CMS\MVC\Controller\BaseController
{

	var $_debug = false;
	var $queryModel = null;

	/**
	 * Controler for the Ical Functions
	 *
	 * @param array        configuration
	 */
	function __construct($config = array())
	{

		parent::__construct($config);
		$this->registerTask('list', 'overview');
		$this->registerDefaultTask("overview");

		$cfg          = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		$this->dataModel  = new JEventsDataModel("JEventsAdminDBModel");
		$this->queryModel = new JEventsDBModel($this->dataModel);

		PluginHelper::importPlugin('finder');

	}

	/**
	 * List Ical Repeats
	 *
	 */
	function overview()
	{

		// get the view
		$this->view = $this->getView("icalrepeat", "html");

		// Get/Create the model
		if ($model = $this->getModel("icalrepeat", "jeventsModel"))
		{
			$model->queryModel = $this->queryModel;

			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		// getItems so we populate the state and the active filters
		$total    = $model->getTotal();
		$icalrows = $model->getItems();

		jimport('joomla.html.pagination');
		$limit      = intval($model->getState('list.limit', 10));
		$limitstart = intval($model->getState('list.start', 10));
		$pagination = new Pagination($total, $limitstart, $limit);

		$input = Factory::getApplication()->input;

		$cid           = $input->get('cid', array(0), "array");
		$cid           = ArrayHelper::toInteger($cid);

		if (is_array($cid) && count($cid) > 0)
			$id = $cid[0];
		else
			$id = $cid;
		// if cancelling a repeat edit then I get the event id a different way
		$evid = $input->getInt("evid", 0);
		if ($evid > 0)
		{
			$id = $evid;
		}

		// Set the layout
		$this->view->setLayout('overview');

		$this->view->icalrows   = $icalrows;
		$this->view->pagination = $pagination;
		$this->view->evid       = $id;

		$this->view->display();

	}

	function edit($key = null, $urlVar = null)
	{

		$app    = Factory::getApplication();
		$input = $app->input;

		// get the view
		$this->view = $this->getView("icalrepeat", "html");

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "jeventsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$db  = Factory::getDbo();
		$cid = $input->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);
		if (is_array($cid) && count($cid) > 0)
			$id = $cid[0];
		else
			$id = $cid;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		// front end passes the id as evid
		if ($id == 0)
		{
			$id = $input->getInt("evid", 0);
		}

		$db    = Factory::getDbo();
		$query = "SELECT rpt.eventid"
				. "\n FROM #__jevents_vevent as ev"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			    . "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n WHERE rpt.rp_id=" . $id
				. "\n AND icsf.state=1";
		$db->setQuery($query);
		$ev_id = $db->loadResult();
		if ($ev_id == 0 || $id == 0)
		{
			$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $ev_id), "ICal repeat does not exist");
			$this->redirect();
		}

		$repeatId = $id;

		$row = $this->queryModel->listEventsById($repeatId, true, "icaldb");

		if (!JEVHelper::canEditEvent($row))
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		//$glist = JEventsHTML::buildAccessSelect(intval($row->access()), 'class="inputbox" size="1"');

		// For repeats don't offer choice of ical or category
		// get all the raw native calendars
		$nativeCals = $this->dataModel->queryModel->getNativeIcalendars();

		$icsid = $row->icsid() > 0 ? $row->icsid() : current($nativeCals)->ics_id;
		$clist = '<input type="hidden" name="ics_id" value="' . $icsid . '" />';

		$this->view->clistChoice    = false;
		$this->view->defaultCat     = 0;

		// Set the layout
		$this->view->setLayout('edit');

		$this->view->ev_id      = $ev_id;
		$this->view->rp_id      = $repeatId;
		$this->view->row        = $row;
		$this->view->nativeCals = $nativeCals;
		$this->view->clist      = $clist;
		$this->view->repeatId   = $repeatId;
		$this->view->dataModel  = $this->dataModel;
		$this->view->editCopy   = false;

		// only those who can publish globally can set priority field
		if (JEVHelper::isEventPublisher(true))
		{
			$list = array();
			for ($i = 0; $i < 10; $i++)
			{
				$list[] = HTMLHelper::_('select.option', $i, $i, 'val', 'text');
			}
			$priorities = HTMLHelper::_('select.genericlist', $list, 'priority', "", 'val', 'text', $row->priority());
			$this->view->setPriority    = true;
			$this->view->priority       = $priorities;
		}
		else
		{
			$this->view->setPriority    = false;
		}

		// for Admin interface only

		$this->view->with_unpublished_cat   = $app->isClient('administrator');

		$this->view->display();

	}

	function save($key = null, $urlVar = null)
	{

		$app    = Factory::getApplication();

		$msg = "";
		$rpt = $this->doSave($msg);

		if ($app->isClient('administrator'))
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $rpt->eventid, "" . Text::_("JEV_ICAL_RPT_DETAILS_SAVED") . "");
			$this->redirect();
		}
		else
		{
			$popupdetail = PluginHelper::getPlugin("jevents", "jevpopupdetail");
			if ($popupdetail)
			{
				$pluginparams = new JevRegistry($popupdetail->params);
				$popupdetail  = $pluginparams->get("detailinpopup", 1);
				if ($popupdetail)
				{
					$popupdetail = "&pop=1&tmpl=component";
				}
				else
				{
					$popupdetail = "";
				}
			}
			else
			{
				$popupdetail = "";
			}

			list($year, $month, $day) = JEVHelper::getYMD();
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0) || $popupdetail)
			{
				$link = Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.detail&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid() . "&year=$year&month=$month&day=$day$popupdetail", false);
				$msg  = Text::_("JEV_ICAL_RPT_UPDATED", true);
				if ($popupdetail != "")
				{
					// redirect to event detail page within popup window
					$this->setRedirect($link, $msg);
					$this->redirect();

					return;
				}
				else
				{
					ob_end_clean();
					?>
					<script type="text/javascript">
                        window.parent.alert("<?php echo $msg; ?>");
                        window.parent.location = "<?php echo $link; ?>";
					</script>
					<?php
					exit();
				}
			}
			$url = 'index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.detail&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid() . "&year=$year&month=$month&day=$day";
			$url = Route::_($url);
			$this->setRedirect($url, "" . Text::_("JEV_ICAL_RPT_UPDATED") . "");
			$this->redirect();
		}

	}

	// Simple function to add a repeat to the series.

	function addRepeat() {
		if (!JEVHelper::isEventCreator())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$app    = Factory::getApplication();
		$input  = $app->input;
		$evid   = $input->getInt('evid', 0);

		$dataModel  = new JEventsDataModel("JEventsAdminDBModel");
		$queryModel = new JEventsDBModel($dataModel);

		// First repeat data for inserting a new repeat to end
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('*')
		    ->from($db->quoteName('#__jevents_repetition', 'rp'))
		    ->where($db->quoteName('eventid') . ' = ' . $evid);
		$db->setQuery($query);
		$repeats = $db->loadObjectList();

		$query  = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__jevents_vevent', 'ev'))
			->where($db->quoteName('ev_id') . ' = ' . $evid);
		$db->setQuery($query);
		$event = $db->loadObject();

		$start          = strtotime('now');
		$irregularDates = array();

		foreach ($repeats AS $repeat) {
		    $irregularDates[] = $repeat->startrepeat;
        }

		$irregularsCount = count($irregularDates);

		if($irregularsCount <= 0) {
		    throw new Exception('Error, no repeats could be found?', 500);
		    return false;
        }

		// Add in our new date for additional repeat
		$repeatToInsert = $repeats[count($repeats) -1];
		$start          = strtotime($repeatToInsert->startrepeat . '+1 hour');
		$start          = JevDate::strftime('%Y-%m-%d %H:%M:%S', $start);
		$irregularDates[]   =  $start;

		// Set Irregular Rules
		$rruleVals = new stdClass();
		$rruleVals->eventid         = $evid;
		$rruleVals->freq            = 'IRREGULAR';
		$rruleVals->irregulardates  = implode(',', $irregularDates);
		$rruleVals->count           = count($irregularDates);

		// Update the repeat rule to be irregular
        $db     = Factory::getDbo();
        $result = $db->updateObject('#__jevents_rrule', $rruleVals, 'eventid');

        if($result) {
            // Now to insert the the additional repeat
            $repeatToInsert = $repeats[count($repeats) -1];

            $start          = strtotime($repeatToInsert->startrepeat . '+1 day');
            $end            = strtotime($repeatToInsert->endrepeat . '+2 day');
            $repeatToInsert->rp_id          = '';
            $repeatToInsert->duplicatecheck = md5($repeatToInsert->eventid . $start);
            $repeatToInsert->startrepeat    = JevDate::strftime('%Y-%m-%d %H:%M:%S', $start);
            $repeatToInsert->endrepeat      = JevDate::strftime('%Y-%m-%d %H:%M:%S', $end);
	        $repeatToInsert->eventdetail_id = $event->detail_id;

            $insertResult = Factory::getDbo()->insertObject('#__jevents_repetition', $repeatToInsert);

            if ($app->isClient('administrator'))
            {
                $this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $evid, "" . Text::_("JEVENTS_REPEAT_ADDED") . "", "success");
                $this->redirect();
            }
        }
        else
        {
	        if ($app->isClient('administrator'))
	        {
		        $this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $evid, "" . Text::_("JEVENT_REPEAT_RRULE_ERROR") . "", "success");
		        $this->redirect();
	        }
        }


	}

	private function doSave(& $msg)
	{

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$app    = Factory::getApplication();
		$input  = $app->input;
        $params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$filter = InputFilter::getInstance(array(), array(), 1, 1);

		// clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		$option = JEV_COM_COMPONENT;
		$rp_id  = $input->getInt("rp_id", 0);
		$cid    = $input->get("cid", array());

		if (count($cid) > 0 && $rp_id == 0)
			$rp_id = (int) $cid[0];
		if ($rp_id == 0)
		{
            $url = 'index.php?option=' . $option . '&task=icalrepeat.list&cid[]=' . $rp_id;
            $url = Route::_($url);

            $this->setRedirect($url, "1Cal rpt NOT SAVED");
			$this->redirect();
		}

		// I should be able to do this in one operation but that can come later
		$event = $this->queryModel->listEventsById((int) $rp_id, 1, "icaldb");
		if (!JEVHelper::canEditEvent($event))
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$db  = Factory::getDbo();
		$rpt = new iCalRepetition($db);
		$rpt->load($rp_id);

		$query = "SELECT detail_id FROM #__jevents_vevent WHERE ev_id=$rpt->eventid";
		$db->setQuery($query);
		$eventdetailid = $db->loadResult();

		$data["UID"] = $input->get("uid", md5(uniqid(rand(), true)));

		if ($params->get("allowraw", 0))
		{
			$data["X-EXTRAINFO"] = $input->get("extra_info", '', 'RAW');
			$data["DESCRIPTION"] = $input->get('jevcontent', '', 'RAW');
		} else {
			$data["X-EXTRAINFO"] = $input->getRaw("extra_info", "");
			$data["DESCRIPTION"] = $input->getRaw('jevcontent', '');

			$data["X-EXTRAINFO"] = $filter->clean($data["X-EXTRAINFO"] , 'html');
			$data["DESCRIPTION"] = $filter->clean($data["DESCRIPTION"] , 'html');
		}

		$data["LOCATION"]    = $input->getString("location", "");
		$data["GEOLON"]      = $input->getFloat("geolon", 0);
		$data["GEOLAT"]      = $input->getFloat("geolat", 0);
		$data["allDayEvent"] = $input->get("allDayEvent", "off");
		if ($data["allDayEvent"] == 1)
		{
			$data["allDayEvent"] = "on";
		}
		$data["CONTACT"]      = $input->getString("contact_info", "");
		$data["publish_down"] = $input->getString("publish_down", "2006-12-12");
		$data["publish_up"]   = $input->getString("publish_up", "2006-12-12");

		// Alternative date format handling
		if ($input->get("publish_up2", false))
		{
			$data["publish_up"] = $input->getString("publish_up2", $data["publish_up"]);
		}
		if ($input->get("publish_down2", false))
		{
			$data["publish_down"] = $input->getString("publish_down2", $data["publish_down"]);
		}

		$interval        = $input->get("rinterval", 1); //Not current in use
		$data["SUMMARY"] = $input->getString("title", "");

		$data["MULTIDAY"]  = $input->get("multiday", "1");
		$data["NOENDTIME"] = $input->get("noendtime", 0);

		$ics_id = $input->get("ics_id", 0);

		if ($data["allDayEvent"] == "on")
		{
			$start_time = "00:00";
		}
		else
		{
			$start_time = $input->getString("start_time", "08:00");
		}

		$publishstart    = $data["publish_up"] . ' ' . $start_time . ':00';
		$data["DTSTART"] = JevDate::strtotime($publishstart);

		if ($data["allDayEvent"] == "on")
		{
			$end_time   = "23:59";
			$publishend = $data["publish_down"] . ' ' . $end_time . ':59';
		}
		else
		{
			if (isset($data["NOENDTIME"]) && $data["NOENDTIME"])
			{
				$end_time = '23:59:59';
			}
			else
			{
				$end_time = $input->getString("end_time", "15:00") . ':00';
			}
			$publishend = $data["publish_down"] . ' ' . $end_time;
		}

		$data["DTEND"] = JevDate::strtotime($publishend);
		// iCal for whole day uses 00:00:00 on the next day JEvents uses 23:59:59 on the same day
		list ($h, $m, $s) = explode(":", $end_time . ':00');
		if (($h + $m + $s) == 0 && $data["allDayEvent"] == "on" && $data["DTEND"] > $data["DTSTART"])
		{
			$publishend    = JevDate::strftime('%Y-%m-%d 23:59:59', ($data["DTEND"] - 86400));
			$data["DTEND"] = JevDate::strtotime($publishend);
		}

		$data["X-COLOR"] = $input->get("color", "", 'HTML');

		// Add any custom fields into $data array - allowing HTML (which can be cleaned up later by plugins)
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		// This test is a no-op
        $array = !$params->get('allowraw', 0) ?
            JEVHelper::arrayFiltered($input->getArray(array(), null, 'RAW')) :
            JEVHelper::arrayFiltered($input->getArray(array(), null, 'RAW'));
        // We need to filter for valid HTML
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



		foreach ($array as $key => $value)
		{
			if (strpos($key, "custom_") === 0)
			{
				$data[$key] = $value;
			}
			// convert jform data to data format used before
			if (strpos($key, "jform") === 0 && is_array($value))
			{
				foreach ($value as $cfkey => $cfvalue)
				{
					$data["custom_" . $cfkey] = $cfvalue;
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
		$start            = $data["DTSTART"];
		$end              = $data["DTEND"];
		$rpt->startrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S', $start);
		$rpt->endrepeat   = JevDate::strftime('%Y-%m-%d %H:%M:%S', $end);

		$rpt->duplicatecheck = md5($rpt->eventid . $start);
		$rpt->eventdetail_id = $detail->evdet_id;
		$rpt->rp_id          = $rp_id;

		// Avoid SQL error on duplicate key insert.
		try
		{
			$store = $rpt->store();

			if (!$store)
			{
				throw new RuntimeException(Text::_('JEV_COULD_NOT_SAVE_REPEAT_SAME_START_END'), 101);
			}

		}
		catch (RuntimeException $e)
		{

			if ($app->isClient('administrator'))
			{
				$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $rpt->eventid, "" . Text::_("JEV_COULD_NOT_SAVE_REPEAT_SAME_START_END") . "", "error");
				$this->redirect();
			}
			else
			{
				list($year, $month, $day) = JEVHelper::getYMD();
				$rettask = $input->getString("rettask", "day.listevents");
                $url = 'index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid() . "&year=$year&month=$month&day=$day";
                $url = Route::_($url);

                $this->setRedirect($url, "" . Text::_("JEV_COULD_NOT_SAVE_REPEAT_SAME_START_END") . "", "error");
				$this->redirect();
			}
		}

		// Just in case we don't have JEvents plugins registered yet
		PluginHelper::importPlugin("jevents");

		// I may also need to process repeat changes
		$res = $app->triggerEvent('onStoreCustomRepeat', array(&$rpt));

		$exception = iCalException::loadByRepeatId($rp_id);
		if (!$exception)
		{
			$exception = new iCalException($db);
			$exception->bind(get_object_vars($rpt));
			// ONLY set the old start repeat when first creating the exception
			$exception->oldstartrepeat = $original_start;
		}

		$exception->exception_type = 1; // Modified

		if ($exception->store()) {
			$exceptionData          = $data;
			$exceptionData['RP_ID']  = $rp_id;
			$exceptionData['EX_ID']  = $exception->ex_id;

			$app->triggerEvent('onAfterStoreRepeatException', array(&$exceptionData));
		}

		return $rpt;
	}

	function apply()
	{

		$msg = "";
		$rpt = $this->doSave($msg);

		$msg = Text::_("Event_Saved", true);
		if (Factory::getApplication()->isClient('administrator'))
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.edit&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid(), $msg);
			$this->redirect();
		}
		else
		{
			list($year, $month, $day) = JEVHelper::getYMD();
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0))
			{
				ob_end_clean();
				?>
				<script type="text/javascript">
                    window.alert("<?php echo $msg; ?>");
                    window.location = "<?php echo Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.edit&evid=" . $rpt->rp_id . "&year=$year&month=$month&day=$day&Itemid=" . JEVHelper::getItemid(), false); ?>";
				</script>
				<?php
				exit();
			}
			// return to the event repeat
            $this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalrepeat.edit&evid=" . $rpt->rp_id . "&year=$year&month=$month&day=$day&Itemid=" . JEVHelper::getItemid(), false), $msg);
			$this->redirect();
		}

	}

	function select()
	{

		Session::checkToken('request') or jexit('Invalid Token');

		$app    = Factory::getApplication();
		$input = $app->input;

		$db            = Factory::getDbo();
		$publishedOnly = true;
		$id            = $input->getInt('evid', 0);

		$limit      = (int) Factory::getApplication()->getUserStateFromRequest("viewlistlimit", 'limit', 10);
		$limitstart = (int) Factory::getApplication()->getUserStateFromRequest("view{" . JEV_COM_COMPONENT . "}limitstart", 'limitstart', 0);

		$user    = Factory::getUser();
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
			. (count($where) ? "\n AND " . implode(' AND ', $where) : '')
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
			. (count($where) ? "\n AND " . implode(' AND ', $where) : '')
			. "\n GROUP BY rpt.rp_id"
			. "\n ORDER BY rpt.startrepeat";
		if ($limit > 0)
		{
			$query .= "\n LIMIT $limitstart, $limit";
		}

		$db->setQuery($query);
		$icalrows  = $db->loadObjectList();
		$icalcount = count($icalrows);
		for ($i = 0; $i < $icalcount; $i++)
		{
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}

		$menulist = $this->targetMenu($input->getInt("Itemid"), "Itemid");

		jimport('joomla.html.pagination');
		$pagination = new Pagination($total, $limitstart, $limit);

		// get the view
		$this->view = $this->getView("icalrepeat", "html");

		// Set the layout
		$this->view->setLayout('select');

		$this->view->menulist   = $menulist;
		$this->view->icalrows   = $icalrows;
		$this->view->pagination = $pagination;
		$this->view->evid       = $id;

		$this->view->display();

	}

	// experimentaal code disabled for the time being

	private function targetMenu($itemid = 0, $name = "")
	{

		$db = Factory::getDbo();

		// assemble menu items to the array
		$options   = array();
		$options[] = HTMLHelper::_('select.option', '', '- ' . Text::_('SELECT_ITEM') . ' -');

		// load the list of menu types
		// TODO: move query to model
		$query = 'SELECT menutype, title' .
			' FROM #__menu_types' .
			' ORDER BY title';
		$db->setQuery($query);
		$menuTypes = $db->loadObjectList();

		$menu      = Factory::getApplication()->getMenu('site');
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
				$pt   = 0; //(version_compare(JVERSION, '1.6.0', ">=")) ? $v->parent_id: $v->parent;  // RSH 10/4/10 in J!1.5 - parent was always 0, this changed in J!.16 to a real parent_id, so force id to 0 for compatibility
				$list = @$children[0] ? $children[0] : array();
				array_push($list, $v);
				$children[0] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		// assemble into menutype groups
		$n           = count($list);
		$groupedList = array();
		foreach ($list as $k => $v)
		{
			$groupedList[$v->menutype][] = &$list[$k];
		}

		foreach ($menuTypes as $type)
		{
			$options[] = HTMLHelper::_('select.option', $type->menutype, $type->title, 'value', 'text', true);  // these are disabled! (true)
			if (isset($groupedList[$type->menutype]))
			{
				$n = count($groupedList[$type->menutype]);
				for ($i = 0; $i < $n; $i++)
				{
					$item = &$groupedList[$type->menutype][$i];

					$disable   = false;
					$text      = (version_compare(JVERSION, '1.6.0', ">=")) ? '     ' . html_entity_decode($item->treename) : '&nbsp;&nbsp;&nbsp;' . $item->treename;
					$text      = str_repeat("&nbsp;", (isset($item->level) ? $item->level : $item->sublevel) * 4) . $text;
					$options[] = HTMLHelper::_('select.option', $item->id, $text, 'value', 'text', $disable);
				}
			}
		}

		return HTMLHelper::_('select.genericlist', $options, '' . $name, 'class="inputbox"', 'value', 'text', $itemid, $name);

	}

	function savefuture()
	{

		// Experimentaal code disabled for count (start the time being
		throw new Exception(Text::_('ALERTNOTAUTH'), 403);
		return false;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$app    = Factory::getApplication();
		$input  = $app->input;

		// clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		echo "<pre>";
		$rpid  = $input->getInt("rp_id", 0);
		$event = $this->queryModel->listEventsById($rpid, 1, "icaldb");
		$data  = array();
		foreach (get_object_vars($event) as $key => $val)
		{
			if (strpos($key, "_") == 0)
			{
				$data[StringHelper::substr($key, 1)] = $val;
			}
		}
		echo var_export($data, true);

		echo "\n\n";

		// Change the underlying event repeat rule details  !!
		$query = "SELECT * FROM #__jevents_rrule WHERE rr_id=" . $event->_rr_id;
		$db    = Factory::getDbo();
		$db->setQuery($query);
		$this->rrule = null;
		$this->rrule = $db->loadObject();
		$this->rrule = iCalRRule::iCalRRuleFromDB(get_object_vars($this->rrule));


		echo var_export($this->rrule, true);

		// TODO *** I must save the modified repeat rule
		// Create the copy of the event and reset the repeat rule to the new values
		foreach ($this->rrule->data as $key => $val)
		{
			$key         = "_" . $key;
			$event->$key = $val;
		}
		$event->eventid = 0;
		$event->ev_id   = 0;

		// create copy of rrule by resetting id and saving
		$this->rrule->rr_id   = 0;
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
			$countrepeat        = $db->loadResult();
			$this->rrule->count -= $countrepeat;
		}
		//$this->rrule->store();
		$event->_rr_id = $this->rrule->rr_id;

		//I must copy the event detail and its id too
		$eventDetail = new iCalEventDetail($db);
		$eventDetail->load($event->_eventdetail_id);
		$eventDetail->evdet_id = 0;
		$eventDetail->store();

		$event->_detail_id      = $eventDetail->evdet_id;
		$event->_eventdetail_id = $eventDetail->evdet_id;

		// TODO I must now regenerate the repetitions
		$event->_rp_id = 0;

		$event->store();
		echo "</pre>";

		// TODO I must store a copy of the event id in the rrule table
		exit();

		// now delete exising current and future repeats - this resets the rrule for the truncated event
		$this->_deleteFuture();


		if (Factory::getApplication()->isClient('administrator'))
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalrepeat.list&cid[]=' . $rpt->eventid, "" . Text::_("JEV_ICAL_RPT_UPDATED") . "");
			$this->redirect();
		}
		else
		{
			list($year, $month, $day) = JEVHelper::getYMD();
			$rettask = $input->getString("rettask", "day.listevents");
			$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&evid=" . $rpt->rp_id . "&Itemid=" . JEVHelper::getItemid() . "&year=$year&month=$month&day=$day"), "" . Text::_("JEV_ICAL_RPT_UPDATED") . "");
			$this->redirect();
		}

	}

	function _deleteFuture()
	{

		$app    = Factory::getApplication();
		$input = $app->input;

		$cid = $input->get('cid', array(0), "array");
		if (!is_array($cid))
			$cid = array(intval($cid));
		$cid = ArrayHelper::toInteger($cid);

		$db = Factory::getDbo();
		foreach ($cid as $id)
		{

			// I should be able to do this in one operation but that can come later
			$event = $this->queryModel->listEventsById(intval($id), 1, "icaldb");
			if (!JEVHelper::canDeleteEvent($event))
			{
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}

			$query = "SELECT * FROM #__jevents_repetition WHERE rp_id=$id";
			$db->setQuery($query);
			$repeatdata = null;
			$repeatdata = $db->loadObject();
			if (is_null($repeatdata))
			{
				throw new Exception(Text::_('NO_SUCH_EVENT'), 4777);

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
				// Just in case we don't have jevents plugins registered yet
				PluginHelper::importPlugin("jevents");
				// May want to send notification messages etc.
				$res = $app->triggerEvent('onDeleteEventRepeat', array($rp_id));
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
				$lastrepeat         = $db->loadResult();
				$this->rrule->until = JevDate::strtotime($lastrepeat);
			}
			else
			{
				// Find latest matching repetition
				$query = "SELECT count(startrepeat) FROM #__jevents_repetition WHERE eventid=" . $repeatdata->eventid . " AND startrepeat<'" . $repeatdata->startrepeat . "'";
				$db->setQuery($query);
				$countrepeat        = $db->loadResult();
				$this->rrule->count = $countrepeat;
			}
			$this->rrule->store();

			if (!is_null($detailids) && count($detailids) > 0)
			{
				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN (" . implode(",", $detailids) . ")";
				$db->setQuery($query);
				$db->execute();

				// Just in case we don't have jevents plugins registered yet
				PluginHelper::importPlugin("jevents");
				// I also need to clean out associated custom data
				$res = $app->triggerEvent('onDeleteEventDetails', array(implode(",", $detailids)));
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

	function close()
	{

		ob_end_clean();
		?>
		<script type="text/javascript">
            try {
                window.parent.closeJevModalBySelector('#myEditModal,#myDetailModal,#myTranslationModal');
            }
            catch (e) {
            }
            try {
                window.parent.SqueezeBox.close();
            }
            catch (e) {
            }
            try {
                window.parent.closedialog();
            }
            catch (e) {
            }
		</script>
		<?php
		exit();

	}

	function delete()
	{

		// clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$app    = Factory::getApplication();
		$input = $app->input;
		$cid    = $input->get('cid', array(0), "array");

		if (!is_array($cid))
			$cid = array(intval($cid));
		$cid = ArrayHelper::toInteger($cid);

		$db = Factory::getDbo();
		foreach ($cid as $id)
		{

			if ($id == 0)
				continue;

			// I should be able to do this in one operation but that can come later
			$event = $this->queryModel->listEventsById(intval($id), 1, "icaldb");
			if (!JEVHelper::canDeleteEvent($event))
			{
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}

			// Just in case we don't have jevents plugins registered yet
			PluginHelper::importPlugin("jevents");
			// May want to send notification messages etc.
			$res = $app->triggerEvent('onDeleteEventRepeat', array($id));

			$query = "SELECT * FROM #__jevents_repetition WHERE rp_id=$id";
			$db->setQuery($query);
			$data = null;
			$data = $db->loadObject();

			$query = "SELECT detail_id FROM #__jevents_vevent WHERE ev_id=$data->eventid";
			$db->setQuery($query);
			$eventdetailid = $db->loadResult();

			$query = "SELECT * FROM #__jevents_rrule WHERE eventid=$data->eventid";
			$db->setQuery($query);
			$rrule = $db->loadObject();

			// only remove the detail id if its different for this repetition i.e. not the global one!
			if ($eventdetailid != $data->eventdetail_id)
			{
				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id = " . $data->eventdetail_id;
				$db->setQuery($query);
				$db->execute();

				// Just in case we don't have jevents plugins registered yet
				PluginHelper::importPlugin("jevents");
				// I also need to clean out associated custom data
				$res = $app->triggerEvent('onDeleteEventDetails', array($data->eventdetail_id));
			}

			// create exception based on deleted repetition
			$rp_id     = $id;
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

			$app->triggerEvent('onAfterDeleteEventRepeat', array(&$event));

		}

		if ($app->isClient('administrator'))
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icalrepeat.list&cid[]=" . $data->eventid, Text::_("JEV_ICAL_REPEAT_DELETED"));
			$this->redirect();
		}
		else
		{
			$Itemid = $input->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();
			$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid", false), Text::_("JEV_ICAL_REPEAT_DELETED"));
			$this->redirect();
		}

	}

	function deletefuture()
	{

		// clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		$app    = Factory::getApplication();
		$input  = $app->input;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$this->_deleteFuture();

		if ($app->isClient('administrator'))
		{
			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icalrepeat.list&cid[]=" . $this->rrule->data["eventid"], Text::_("JEV_ICAL_REPEATS_DELETED"));
			$this->redirect();
		}
		else
		{
			$Itemid = $input->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();
			$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid"), Text::_("JEV_ICAL_REPEATS_DELETED"));
			$this->redirect();
		}

	}

}
