<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icalevent.php 3576 2012-05-01 14:11:04Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\TagsHelper;

#[\AllowDynamicProperties]
class AdminIcaleventController extends Joomla\CMS\MVC\Controller\AdminController
{

	var $_debug = false;
	var $queryModel = null;
	var $dataModel = null;
	var $editCopy = false;
	var $_largetDataSet = false;

	/**
	 * Controler for the Ical Functions
	 *
	 * @param array        configuration
	 */
	function __construct($config = array())
	{

		$config["name"] = "icalevent";
		//$config["default_view"]="AdminIcalevent";
		parent::__construct($config);
		$this->registerTask('list', 'overview');
		$this->registerTask('unpublish', 'unpublish');
		$this->registerDefaultTask("overview");

		$cfg          = JEVConfig::getInstance();
		$this->_debug = $cfg->get('jev_debug', 0);

		$this->dataModel  = new JEventsDataModel("JEventsAdminDBModel");
		$this->queryModel = new JEventsDBModel($this->dataModel);

		PluginHelper::importPlugin('finder');

	}

	/**
	 * List Ical Events
	 *
	 */
	function overview()
	{
		$app   = Factory::getApplication();
		$db = Factory::getDbo();

		// get the view
		$this->view = $this->getView("icalevent", "html", "AdminIcaleventView");

		$this->_checkValidCategories();

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "jeventsModel"))
		{
			$model->queryModel = $this->queryModel;

			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		// Set the layout
		$this->view->setLayout('overview');

		$this->view->display();

	}

	function _checkValidCategories()
	{

		// TODO switch this after migration
		$component_name = "com_jevents";

		$db    = Factory::getDbo();
		$query = "SELECT COUNT(*) AS count FROM #__categories WHERE extension = '$component_name' AND `published` = 1;";  // RSH 9/28/10 added check for valid published, J!1.6 sets deleted categoris to published = -2
		$db->setQuery($query);
		$count = intval($db->loadResult());
		if ($count <= 0)
		{
			// RSH 9/28/10 - Added check for J!1.6 to use different URL for reroute
			$redirectURL = "index.php?option=com_categories&view=categories&extension=" . JEV_COM_COMPONENT;
			$this->setRedirect($redirectURL, "You must first create at least one category");
			$this->redirect();
		}

	}

	function editcopy()
	{

		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}
		$this->editCopy = true;
		$this->edit();

	}

	function edit($key = null, $urlVar = null)
	{

		$app    = Factory::getApplication();
		$input = $app->input;

		// get the view
		if ($app->isClient('administrator'))
		{
			$this->view = $this->getView("icalevent", "html", "AdminIcaleventView");
		}
		else
		{
			$this->view = $this->getView("icalevent", "html");
		}

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "jeventsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		$cid = $input->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);
		if (is_array($cid) && count($cid) > 0)
			$id = $cid[0];
		else
			$id = 0;

		// front end passes the id as evid
		if ($id == 0)
		{
			$id = $input->getInt("evid", 0);
		}

		// we check if user can edit specific event in about 30 lines time
		if (!JEVHelper::isEventCreator() && !JEVHelper::isEventEditor())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$repeatId = 0;

		$db = Factory::getDbo();

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
					$Itemid = $input->getInt("Itemid");
					Factory::getApplication()->enqueueMessage(Text::_("JEV_SORRY_UPDATED"), 'warning');
					Factory::getApplication()->redirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$this->Itemid", false));
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
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}


		}
		else if ($input->getInt('directCreate', 0))
		{
			$vevent = new iCalEvent($db);

			$vevent->set("freq", "NONE");
			$vevent->set("description", "");
			$vevent->set("summary", "");
			$vevent->set("access", "1");
			list($year, $month, $day) = JEVHelper::getYMD();

			$params           = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$defaultstarttime = $params->get("defaultstarttime", "08:00");
			$defaultendtime   = $params->get("defaultendtime", "17:00");
			list($starthour, $startmin) = explode(":", $defaultstarttime);
			list($endhour, $endmin) = explode(":", $defaultendtime);

			$vevent->set("dtstart", JevDate::mktime((int) $starthour, $startmin, 0, $month, $day, $year));
			$vevent->set("dtend", JevDate::mktime((int) $endhour, $endmin, 0, $month, $day, $year));

			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$input = Factory::getApplication()->input;
			$array  = !$params->get('allowraw', 0) ? JEVHelper::arrayFiltered($input->getArray(array(), null, 'RAW')) : $input->getArray(array(), null, 'RAW');

			if (!isset($array['extra_info']))
			{
				$array['extra_info'] = "";
			}
			if (!isset($array['jevcontent']))
			{
				$array['jevcontent'] = "";
			}
			// Should we allow raw content through unfiltered
			if ($params->get("allowraw", 0))
			{
				$array['jevcontent'] = $input->post->get("jevcontent", "", 'RAW');
				$array['extra_info'] = $input->post->get("extra_info", "", 'RAW');
			}
			// Convert nl2br if there is no HTML
			if (strip_tags($array['jevcontent']) == $array['jevcontent'])
			{
				$array['jevcontent'] = nl2br($array['jevcontent']);
			}
			if (strip_tags($array['extra_info']) == $array['extra_info'])
			{
				$array['extra_info'] = nl2br($array['extra_info']);
			}

			if (!isset($array['freq']))
			{
				$array['freq']      = "none";
				$array['count']     = 1;
				$array['rinterval'] = 1;
			}
			$rrule = SaveIcalEvent::generateRRule($array);

			// Use dry run code to generate data
			$vevent = SaveIcalEvent::save($array, $this->queryModel, $rrule, true);
			foreach (get_object_vars($vevent->_detail) as $key => $val)
			{
				if (strpos($key, "_") !== 0)
				{
					$vevent->$key = $val;
				}
			}
			foreach (get_object_vars($vevent->rrule) as $key => $val)
			{
				if (strpos($key, "_") !== 0)
				{
					$vevent->$key = $val;
				}
			}
			$row = new jIcalEventDB($vevent);
			// add in a reference to the custom field data so that the plugins can use it
			if (isset($vevent->_detail->_customFields))
			{
				$row->_customFields = $vevent->_detail->_customFields;
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

			$params           = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$defaultstarttime = $params->get("defaultstarttime", "08:00");
			$defaultendtime   = $params->get("defaultendtime", "17:00");
			list($starthour, $startmin) = explode(":", $defaultstarttime);
			list($endhour, $endmin) = explode(":", $defaultendtime);

			$vevent->set("dtstart", JevDate::mktime((int) $starthour, $startmin, 0, $month, $day, $year));
			$vevent->set("dtend", JevDate::mktime((int) $endhour, $endmin, 0, $month, $day, $year));
			$row = new jIcalEventDB($vevent);
			if ($params->get('default_alldayevent', 0) == 1)
			{
				$row->_alldayevent = 1;
			}
			if ($params->get('default_noendtime', 0) == 1)
			{
				$row->_noendtime = 1;
			}
			// TODO - move this to class!!
			// populate with meaningful initial values
			$row->starttime($defaultstarttime);
			$row->endtime($defaultendtime);
		}

		/*
		 * Make sure content_type is registered!
		 * This is to support Joomla tags - buts its complicated since we need to manually modify the ucm_content table because key data is spread across 3 tables
		 * So its disabled for now
        */
		/*
        $query = $db->getQuery(true);
		$query->select('type_title')
            ->from ("#__content_types")
            ->where ('type_alias = "com_jevents.event"');
		$db->setQuery($query);
		if (!$db->loadResult())
        {
            $sql = <<< SQL
		INSERT INTO #__content_types
	(
		type_title,
		type_alias,
		`table`,
		rules,
		field_mappings
	)
VALUES (
	'JEvents Event',
	'com_jevents.event',
	'{"special":{"dbtable":"#__jevent_vevent","key":"ev_id","type":"Event","prefix":"JEventsTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"Table","config":"array()"}}',
	'',
	'{
	"common": {
		"core_content_item_id": "ev_id",
		"core_title": "(SELECT summary FROM #__jevents_vevdetail as det WHERE det.evdet_id = #__jevent_vevent.detail_id)",
		"core_state": "(SELECT state FROM #__jevents_vevdetail as det WHERE det.evdet_id = #__jevent_vevent.detail_id)",
		"core_alias": "null",
		"core_created_time": "null",
		"core_modified_time": "null",
		"core_body": "null",
		"core_hits": "null",
		"core_publish_up": "null",
		"core_publish_down": "null",
		"core_access": "null",
		"core_params": "null",
		"core_featured": "null",
		"core_metadata": "null",
		"core_language": "null",
		"core_images": "null",
		"core_urls": "null",
		"core_version": "null",
		"core_ordering": "null",
		"core_metakey": "null",
		"core_metadesc": "null",
		"core_catid": "null",
		"core_xreference": "null",
		"asset_id": "asset_id"
	},
	"special": {
	}
}' )
SQL;
	        $db->setQuery($sql);
	        $db->execute();
        }
		*/

		/*
		// Load item tags
		if (!empty($row->evdet_id()))
		{
			$row->tags = new TagsHelper;
			$row->tags->getTagIds($row->evdet_id(), 'com_jevents.event');
		}
		*/

		$glist = JEventsHTML::buildAccessSelect(intval($row->access()), 'class="inputbox" size="1"');

		// get all the raw native calendars
		$nativeCals = $this->dataModel->queryModel->getNativeIcalendars();

		// Strip this list down based on user permissions
		$jevuser = JEVHelper::getAuthorisedUser();
		if ($jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all")
		{
			$cals        = array_keys($nativeCals);
			$allowedcals = explode("|", $jevuser->calendars);
			foreach ($cals as $calid)
			{
				if (!in_array($calid, $allowedcals))
					unset($nativeCals[$calid]);
			}
		}

		// Are we allowed to edit events within a URL based iCal
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("allowedit", 0) && $row->icsid() > 0)
		{
			$calsql = 'SELECT * FROM #__jevents_icsfile WHERE ics_id=' . intval($row->icsid());
			$db->setQuery($calsql);
			$cal = $db->loadObject();
			if ($cal && $cal->icaltype == 0)
			{
				$nativeCals[$cal->ics_id] = $cal;
				$this->view->offerlock = 1;
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
			$icalList   = array();
			$icalList[] = HTMLHelper::_('select.option', '0', Text::_('JEV_EVENT_CHOOSE_ICAL'), 'ics_id', 'label');
			$icalList   = array_merge($icalList, $nativeCals);

			$row_icsid = $row->icsid();

			if ($params->get('defaultcal', 0) && !$row_icsid)
			{
				$row_icsid = count($nativeCals) > 0 ? current($nativeCals)->ics_id : 0;
			}
			$clist = HTMLHelper::_('select.genericlist', $icalList, 'ics_id', " onchange='preselectCategory(this);'", 'ics_id', 'label', $row_icsid);

			$this->view->clistChoice = true;
			$this->view->defaultCat = 0;
		}
		else
		{
			if (count($nativeCals) == 0 || !is_array($nativeCals))
			{

				$app->enqueueMessage('870 -' . Text::_('INVALID_CALENDAR_STRUCTURE'), 'warning');

			}

			$icsid = $row->icsid() > 0 ? $row->icsid() : (count($nativeCals) > 0 ? current($nativeCals)->ics_id : 0);

			$clist = '<input type="hidden" name="ics_id" value="' . $icsid . '" />';
			$this->view->clistChoice = false;
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("defaultcat", false))
			{
				$this->view->defaultCat = current($nativeCals)->catid;
			}
			else
			{
				$this->view->defaultCat = 0;
			}
		}

		// Set the layout
		$this->view->setLayout('edit');

		$this->view->editCopy = $this->editCopy;
		$this->view->id = $id;
		$this->view->row = $row;
		$this->view->excats = $excats;
		$this->view->nativeCals = $nativeCals;
		$this->view->clist  = $clist;
		$this->view->repeatId = $repeatId;
		$this->view->glist = $glist;

		// for Admin interface only
		$this->view->with_unpublished_cat = $app->isClient('administrator');
		$this->view->dataModel = $this->dataModel;

		// Keep following fields for backwards compataibility only
		// only those who can publish globally can set priority field
		if (JEVHelper::isEventPublisher(true))
		{
			$list = array();
			for ($i = 0; $i < 10; $i++)
			{
				$list[] = HTMLHelper::_('select.option', $i, $i, 'val', 'text');
			}
			$priorities = HTMLHelper::_('select.genericlist', $list, 'priority', "", 'val', 'text', $row->priority());
			$this->view->setPriority = true;
			$this->view->priority = $priorities;
		}
		else
		{
			$this->view->setPriority = false;
		}

		$this->view->display();

	}

	function translate()
	{

		$app    = Factory::getApplication();
		$input = $app->input;

		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		// get the view
		if ($app->isClient('administrator'))
		{
			$this->view = $this->getView("icalevent", "html", "AdminIcaleventView");
		}
		else
		{
			$this->view = $this->getView("icalevent", "html");
		}

		$ev_id    = $input->getInt("ev_id", 0);
		$evdet_id = $input->getInt("evdet_id", $input->getInt("trans_evdet_id", 0));

		// check editing permission
		if ($ev_id > 0 && $evdet_id > 0)
		{
			// this version gives us a repeat not an event so
			$vevent = $this->dataModel->queryModel->getVEventById($ev_id);
			if (!$vevent)
			{
				$Itemid = $input->getInt("Itemid");
				Factory::getApplication()->enqueueMessage(Text::_("JEV_SORRY_UPDATED"), 'warning');
				Factory::getApplication()->redirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$this->Itemid", false));
			}

			$row = new jIcalEventDB($vevent);

			if (!JEVHelper::canEditEvent($row))
			{
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}
			$this->view->row = $row;

		}
		else
		{
			throw new Exception("No event details passed in to translation script", 403);

			return false;
		}

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "jeventsModel"))
		{
			// Push the model into the view (as default)
			$this->view->setModel($model, true);
		}

		// Set the layout
		$this->view->setLayout('translate');

		$this->view->display();

	}

	function savetranslation()
	{

		Session::checkToken('request') or jexit('Invalid Token');

		$app   = Factory::getApplication();
		$input = $app->input;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$ev_id    = $input->getInt("ev_id", 0);
		$evdet_id = $input->getInt("evdet_id", $input->getInt("trans_evdet_id", 0));

		// check editing permission
		if ($ev_id > 0 && $evdet_id > 0)
		{
			// this version gives us a repeat not an event so
			$vevent = $this->dataModel->queryModel->getVEventById($ev_id);
			if (!$vevent)
			{
				$Itemid = $input->getInt("Itemid");
				Factory::getApplication()->enqueueMessage(Text::_("JEV_SORRY_UPDATED"), 'warning');
				Factory::getApplication()->redirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$this->Itemid", false));
			}

			$row = new jIcalEventDB($vevent);

			if (!JEVHelper::canEditEvent($row))
			{
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}
		}
		else
		{
			throw new Exception("No event details passed in to translation script", 500);

			return false;
		}

		// clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "jeventsModel"))
		{
			$model->saveTranslation();
		}

		ob_end_clean();
		if (!headers_sent())
		{
			header('Content-Type:text/html;charset=utf-8');
		}
		$link = Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.list", false);
		?>
		<script type="text/javascript">
            window.parent.location = "<?php echo $link; ?>";
		</script>
		<?php
		exit();

	}

	function deletetranslation()
	{

		Session::checkToken('request') or jexit('Invalid Token');

		$app   = Factory::getApplication();
		$input = $app->input;

		if (!JEVHelper::isEventCreator())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$ev_id    = $input->getInt("ev_id", 0);
		$evdet_id = $input->getInt("evdet_id", $input->getInt("trans_evdet_id", 0));

		// check editing permission
		if ($ev_id > 0 && $evdet_id > 0)
		{
			// this version gives us a repeat not an event so
			$vevent = $this->dataModel->queryModel->getVEventById($ev_id);
			if (!$vevent)
			{
				$Itemid = $input->getInt("Itemid");
				Factory::getApplication()->enqueueMessage(Text::_("JEV_SORRY_UPDATED"), 'warning');
				Factory::getApplication()->redirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$this->Itemid", false));

			}

			$row = new jIcalEventDB($vevent);

			if (!JEVHelper::canEditEvent($row))
			{
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}
		}
		else
		{
			throw new Exception("No event details passed in to translation script", 500);

			return false;
		}

		// clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		// Get/Create the model
		if ($model = $this->getModel("icalevent", "jeventsModel"))
		{
			$model->deleteTranslation();
		}

		ob_end_clean();
		if (!headers_sent())
		{
			header('Content-Type:text/html;charset=utf-8');
		}
		$link = Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.list", false);
		?>
		<script type="text/javascript">
            window.parent.location = "<?php echo $link; ?>";
		</script>
		<?php
		exit();

	}

	function savecopy($key = null, $urlVar = null)
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$input->set("old_evid", $input->getInt("evid", 0));
		$input->set("evid", 0);
		$input->set("rp_id", 0);
		$input->set("uid", md5(uniqid(rand(), true)));
		$this->save($key, $urlVar);
	}

	function save($key = null, $urlVar = null)
	{

		$app   = Factory::getApplication();
		$input = $app->input;

		$msg   = "";
		$event = $this->doSave($msg);

		if ($app->isClient('administrator'))
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.list', $msg);
			$this->redirect();
		}
		else
		{
			JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
			JEVHelper::setUpdateUrls();

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

			$Itemid = $input->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();

			// When editing an event from a specific repeat page we want to return to that specific repeat
			if ($event && intval($event->rp_id()) == 0)
			{
				if ($input->getInt("rp_id", 0))
				{
					$tempevent = $this->dataModel->queryModel->listEventsById($input->getInt("rp_id", 0), true);
					if ($tempevent)
					{
						$event = $tempevent;
					}
					else
					{
						$event = $event->getFirstRepeat();
					}
				}
				else
				{
					$event = $event->getFirstRepeat();
				}
			}

			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0) || $popupdetail)
			{
				ob_end_clean();
				if (!headers_sent() && $popupdetail == "")
				{
					header('Content-Type:text/html;charset=utf-8');
				}
				if ($event)
				{
					$year  = $event->yup();
					$month = $event->mup();
					$day   = $event->dup();
					$input->set("year", $year);
					$input->set("month", $month);
					$input->set("day", $day);
				}
				if ($event && $event->state())
				{
					$link = Route::_($event->viewDetailLink($year, $month, $day, false, $Itemid) . "&published_fv=-1$popupdetail");
				}
				else
				{
					if (Factory::getUser()->id > 0)
					{
						$link = Route::_($event->viewDetailLink($year, $month, $day, false, $Itemid) . "&published_fv=-1$popupdetail");
					}
					else
					{
						$link = Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid$popupdetail", false);
					}
				}
				if ($popupdetail != "")
				{
					// redirect to event detail page within popup window
					$this->setRedirect($link, $msg);
					$this->redirect();

					return;
				}
				else
				{
					?>
					<script type="text/javascript">
                        window.parent.alert("<?php echo $msg; ?>");
                        window.parent.location = "<?php echo $link; ?>";
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
				if (Factory::getUser()->id > 0)
				{
					$this->setRedirect(Route::_($event->viewDetailLink($year, $month, $day, false, $Itemid) . "&published_fv=-1"), $msg);
					$this->redirect();
				}
				else
				{
					// I can't go back to the same repetition since its id has been lost
					$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid", false), $msg);
					$this->redirect();
				}
			}
		}

	}

	private function doSave(& $msg)
	{

		if (!JEVHelper::isEventCreator() && !JEVHelper::isEventEditor())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		// Clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);
		$input = Factory::getApplication()->input;
		$array  = !$params->get('allowraw', 0) ? JEVHelper::arrayFiltered($input->getArray(array(), null, 'RAW')) : $input->getArray(array(), null, 'RAW');

		if (!isset($array['extra_info']))
        {
	        $array['extra_info'] = "";
        }
		if (!isset($array['jevcontent']))
		{
			$array['jevcontent'] = "";
		}
		// Should we allow raw content through unfiltered
		if ($params->get("allowraw", 0))
		{
			$array['jevcontent'] = $input->post->get("jevcontent", "", 'RAW');
			$array['extra_info'] = $input->post->get("extra_info", "", 'RAW');
		}
		// Convert nl2br if there is no HTML
		if (strip_tags($array['jevcontent']) == $array['jevcontent'])
		{
			$array['jevcontent'] = nl2br($array['jevcontent']);
		}
		if (strip_tags($array['extra_info']) == $array['extra_info'])
		{
			$array['extra_info'] = nl2br($array['extra_info']);
		}

		// Convert event data to objewct so we can test permissions
		$eventobj = new stdClass();
		foreach ($array as $key => $val)
		{
			$newkey            = "_" . $key;
			$eventobj->$newkey = $val;
		}
		$eventobj->_icsid = $eventobj->_ics_id;
		if (isset($eventobj->_catid) && is_array($eventobj->_catid))
		{
			$eventobj->_catid = current($eventobj->_catid);
		}

		if (!JEVHelper::canCreateEvent($eventobj) && !JEVHelper::isEventEditor())
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		$rrule = SaveIcalEvent::generateRRule($array);

		// ensure authorised
		if (isset($array["evid"]) && intval($array["evid"]) > 0)
		{
			$event = $this->queryModel->getEventById(intval($array["evid"]), 1, "icaldb");
			if (!$event || !JEVHelper::canEditEvent($event))
			{
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}
		}

		$clearout = false;
		// remove all exceptions since they are no longer needed
		if (isset($array["evid"]) && intval($array["evid"]) > 0 && $input->getInt("updaterepeats", 1))
		{
			$clearout = true;
		}

		if ($event = SaveIcalEvent::save($array, $this->queryModel, $rrule))
		{
			$row = new jIcalEventRepeat($event);
			if (!JEVHelper::canPublishEvent($row) && !$event->state)
			{
				$msg = Text::_("EVENT_SAVED_UNDER_REVIEW", true);
			}
			else
			{
				$msg = Text::_("Event_Saved", true);
			}
			if ($clearout)
			{
				$db    = Factory::getDbo();
				$query = "DELETE FROM #__jevents_exception WHERE eventid = " . intval($array["evid"]);
				$db->setQuery($query);
				$db->execute();
				// TODO clear out old exception details
			}

			/*
			$tagsHelper = new TagsHelper();
			$tagsHelper->typeAlias = "com_jevents.event";
            //$ucmId, TableInterface $table, $tags = array(), $replace = true
			$tagged = $tagsHelper->tagItem($event->detail_id, $event->_detail, $array['tags'], true);

			// I need to manually set the #__ucm_content #__ucm_base and table entries since they are joins and the UCM doesn't deal with it properlu
			*/
		}
		else
		{
			$msg = Text::_("Event Not Saved", true);
			$row = null;
		}

		return $row;

	}

	function savenew()
	{

		$msg   = "";
		$event = $this->doSave($msg);

		$app   = Factory::getApplication();
		$input = $app->input;

		if ($app->isClient('administrator'))
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.edit', $msg);
			$this->redirect();
		}
		else
		{
			$Itemid = $input->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();

			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
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
					$link = Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid", false);
				}
				?>
				<script type="text/javascript">
                    window.parent.alert("<?php echo $msg; ?>");
                    window.parent.location = "<?php echo $link; ?>";
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
				$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=day.listevents&year=$year&month=$month&day=$day&Itemid=$Itemid", false), $msg);
				$this->redirect();
			}
		}

	}

	function apply()
	{

		$app   = Factory::getApplication();
		$input = $app->input;
		$Itemid = $input->getInt("Itemid");

		$msg   = "";
		$event = $this->doSave($msg);

		// reload the event to get the reptition ids
		$evid      = intval($event->ev_id());
		$testevent = $this->queryModel->getEventById($evid, 1, "icaldb");
		$rp_id     = !is_null($testevent) ? $testevent->rp_id() : null;

		if (!$rp_id)
		{
			// See if a plugin can find our missing event - maybe on another menu item
			PluginHelper::importPlugin('jevents');
			$app->triggerEvent('onMissingEventById', array(& $testevent, $evid, & $Itemid));
			$rp_id     = !is_null($testevent) ? $testevent->rp_id() : null;

			if (!$rp_id)
			{
				$app->enqueueMessage(Text::_("JEV_CANNOT_DISPLAY_SAVED_EVENT_ON_THIS_MENU_ITEM", "WARNING"));

				return;
			}
		}
		list($year, $month, $day) = JEVHelper::getYMD();

		if ($app->isClient('administrator'))
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.edit&evid=$evid&rp_id=$rp_id&year=$year&month=$month&day=$day", $msg);
			$this->redirect();
		}
		else
		{

			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("editpopup", 0))
			{
				ob_end_clean();
				?>
				<script type="text/javascript">
                    window.parent.alert("<?php echo $msg; ?>");
                    window.location = "<?php echo Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.edit&evid=$evid&rp_id=$rp_id&year=$year&month=$month&day=$day&Itemid=$Itemid", false); ?>";
				</script>
				<?php
				exit();
			}

			// return to the event
			$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=icalevent.edit&evid=$evid&rp_id=$rp_id&year=$year&month=$month&day=$day&Itemid=$Itemid", false), $msg);
			$this->redirect();
		}

	}

	function csvimport()
	{

		$app    = Factory::getApplication();

		if (!$app->isClient('administrator'))
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		// get the view
		$this->view = $this->getView("icalevent", "html");

		// get all the raw native calendars
		$nativeCals = $this->dataModel->queryModel->getNativeIcalendars();

		// Strip this list down based on user permissions
		$jevuser = JEVHelper::getAuthorisedUser();
		if ($jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all")
		{
			$cals        = array_keys($nativeCals);
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
			$icalList   = array();
			$icalList[] = HTMLHelper::_('select.option', '0', Text::_('JEV_EVENT_CHOOSE_ICAL'), 'ics_id', 'label');
			$icalList   = array_merge($icalList, $nativeCals);
			$callist    = HTMLHelper::_('select.genericlist', $icalList, 'ics_id', " onchange='preselectCategory(this);'", 'ics_id', 'label', 0);
			$this->view->callist = $callist;
		}
		else
		{

			$app->enqueueMessage('870 -' . Text::_('INVALID_CALENDAR_STRUCTURE'), 'warning');

		}

		// Set the layout
		$this->view->setLayout('csvimport');

		$this->view->display();

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

	function publish()
	{

		$input = Factory::getApplication()->input;

		$cid = $input->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleICalEventPublish($cid, 1);

	}

	protected function toggleICalEventPublish($cid, $newstate)
	{

		$app   = Factory::getApplication();
		$input = $app->input;
		// clean out the cache
		$cache = Factory::getCache('com_jevents');
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
						throw new Exception(Text::_('ALERTNOTAUTH') . " 1", 403);

						return false;
					}
				}
			}
			$is_event_editor = true;
		}
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH') . " 2", 403);

			return false;
		}

		$db = Factory::getDbo();
		foreach ($cid as $id)
		{

			// I should be able to do this in one operation but that can come later
			$event = $this->queryModel->getEventById(intval($id), 1, "icaldb");
			if (is_null($event))
			{
				/*echo $db->getQuery()."<br/>";return;*/
				throw new Exception(Text::_('ALERTNOTAUTH') . " 3", 403);

				return false;
			}
			else if (!JEVHelper::canPublishEvent($event))
			{
				throw new Exception(Text::_('ALERTNOTAUTH') . " 4", 403);

				return false;
			}

			$sql = "UPDATE #__jevents_vevent SET state=$newstate where ev_id='" . $id . "'";
			$db->setQuery($sql);
			$db->execute();

			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($newstate == 1 && $params->get("com_notifyauthor", 0) && !$event->_author_notified)
			{
				$sql = "UPDATE #__jevents_vevent SET author_notified=1 where ev_id='" . $id . "'";
				$db->setQuery($sql);
				$db->execute();

				JEV_CommonFunctions::notifyAuthorPublished($event);
			}
		}

		// Just in case we don't have jevents plugins registered yet
		PluginHelper::importPlugin("jevents");

		// I also need to trigger any onpublish event triggers
		$res        = $app->triggerEvent('onPublishEvent', array($cid, $newstate));
		$pub_filter = $input->get('published_fv', 0);

		if ($app->isClient('administrator'))
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.list', Text::_('JEV_EVENT_PUBLISH_STATE_SAVED'));
			$this->redirect();
		}
		else
		{
			$Itemid = $input->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();
			$rettask = $input->getString("rettask", "day.listevents");
			// Don't return to the event detail since we may be filtering on published state!
			//$this->setRedirect( Route::_('index.php?option=' . JEV_COM_COMPONENT. "&task=icalrepeat.detail&evid=$id&year=$year&month=$month&day=$day&Itemid=$Itemid",false),"IcalEvent  : New published state Saved");
			$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&year=$year&month=$month&day=$day&Itemid=$Itemid&published_fv=$pub_filter", false), Text::_('JEV_EVENT_PUBLISH_STATE_SAVED'));
			$this->redirect();
		}

	}

	function unpublish()
	{

		$input = Factory::getApplication()->input;

		$cid = $input->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);
		$this->toggleICalEventPublish($cid, 0);
	}

	function delete()
	{

		// Clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		$app    = Factory::getApplication();
		$input  = $app->input;

		$cid = $input->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);

		// front end passes the id as evid
		if (count($cid) == 1 && $cid[0] == 0)
		{
			$cid = array($input->getInt("evid", 0));
		}

		$db = Factory::getDbo();

		foreach ($cid as $key => $id)
		{
			// I should be able to do this in one operation but that can come later
			// Do not check access as we are checking canDeleteEvent
			$event = $this->queryModel->getEventById(intval($id), 1, "icaldb", false);
			if (is_null($event) || !JEVHelper::canDeleteEvent($event))
			{
				// check for corrupted repeats table
				if (is_null($event))
				{
					$db->setQuery("SELECT * FROM #__jevents_repetition WHERE eventid = " . intval($id) . " LIMIT 1");
					if (!$db->loadObject())
					{
						continue;
					}
				}

				unset($cid[$key]);
				if (count($cid) == 0)
				{
					$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.list', JTEXT::_("JEV_NO_DELETE_ROW") . " : " . (is_null($event) ? $id : 0));
					$this->redirect();
				}
				else
				{
					$app->enqueueMessage('870 -' . Text::_('JEV_NO_DELETE_ROW'), 'warning');
				}
			}
		}

		$newstate = -1;
		foreach ($cid as $key => $id)
		{
			$sql = "UPDATE #__jevents_vevent SET state=$newstate where ev_id='" . $id . "'";
			$db->setQuery($sql);
			$db->execute();

		}

		// Just incase we don't have JEvents plugins registered yet
		PluginHelper::importPlugin("jevents");

		// I also need to trigger any onpublish event triggers

		$res = $app->triggerEvent('onPublishEvent', array($cid, $newstate));

		if ($app->isClient('administrator'))
		{
			$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=icalevent.list', JTEXT::_("JEV_EVENT_STATE_CHANGED"));
			$this->redirect();
		}
		else
		{
			$Itemid = $input->getInt("Itemid");
			list($year, $month, $day) = JEVHelper::getYMD();
			$rettask = $input->getString("rettask", "day.listevents");

			// Don't return to the event detail since we may be filtering on published state!
			//$this->setRedirect( Route::_('index.php?option=' . JEV_COM_COMPONENT. "&task=icalrepeat.detail&evid=$id&year=$year&month=$month&day=$day&Itemid=$Itemid",false),"IcalEvent  : New published state Saved");
			$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&year=$year&month=$month&day=$day&Itemid=$Itemid", false), Text::_('JEV_EVENT_DELETE_STATE_SAVED'));
			$this->redirect();
		}

	}

	function emptytrash()
	{

		// clean out the cache
		$cache = Factory::getCache('com_jevents');
		$cache->clean(JEV_COM_COMPONENT);

		/*
		  // This is covered by canDeleteEvent below
		  if (!JEVHelper::isEventDeletor()){
		  throw new Exception( Text::_('ALERTNOTAUTH'), 403);
		 return false;
		  }
		 */

		$app    = Factory::getApplication();
		$input = $app->input;

		$cid = $input->get('cid', array(0), "array");
		$cid = ArrayHelper::toInteger($cid);

		// front end passes the id as evid
		if (count($cid) == 1 && $cid[0] == 0)
		{
			$cid = array($input->getInt("evid", 0));
		}

		$db = Factory::getDbo();

		foreach ($cid as $key => $id)
		{
			// I should be able to do this in one operation but that can come later
			$events     = array();
			$event      = $this->queryModel->getEventById(intval($id), 1, "icaldb");

			if (is_null($event) || !JEVHelper::canDeleteEvent($event))
			{
				// check for corrupted repeats table
				if (is_null($event))
				{
					$db->setQuery("SELECT * FROM #__jevents_repetition WHERE eventid = " . intval($id) . " LIMIT 1");
					if (!$db->loadObject())
					{
						continue;
					}
				}

				$app->enqueueMessage('534 -' . Text::_('JEV_NO_DELETE_ROW'), 'warning');

				unset($cid[$key]);
			} else {
				$events[$key]['title']      = $event->title();
				$events[$key]['id']         = $event->id();
				$events[$key]['startDate']  = $event->publish_up();

			}
		}

		if (count($cid) > 0)
		{
			$veventidstring = implode(",", $cid);

			// TODO the ruccurences should take care of all of these??
			// This would fail if all recurrances have been 'adjusted'
			$query = "SELECT DISTINCT (eventdetail_id) FROM #__jevents_repetition WHERE eventid IN ($veventidstring)";
			$db->setQuery($query);
			$detailids      = $db->loadColumn();
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

			if (\Joomla\String\StringHelper::strlen($detailidstring) > 0)
			{
				$query = "DELETE FROM #__jevents_vevdetail WHERE evdet_id IN ($detailidstring)";
				$db->setQuery($query);
				$db->execute();

				// just incase we don't have jevents plugins registered yet
				PluginHelper::importPlugin("jevents");
				// I also need to clean out associated custom data
				$res = $app->triggerEvent('onDeleteEventDetails', array($detailidstring));
			}

			$query = "DELETE FROM #__jevents_vevent WHERE ev_id IN ($veventidstring)";
			$db->setQuery($query);
			$db->execute();

			// just incase we don't have jevents plugins registered yet
			PluginHelper::importPlugin("jevents");
			// I also need to delete custom data

			$res = $app->triggerEvent('onDeleteCustomEvent', array(&$veventidstring));

			$app->triggerEvent('onAfterDeleteEvent', array(&$events));

			if ($app->isClient('administrator'))
			{
				$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.list", JTEXT::_("ICAL_EVENTS_DELETED"));
				$this->redirect();
			}
			else
			{
				$Itemid = $input->getInt("Itemid");
				list($year, $month, $day) = JEVHelper::getYMD();
				$rettask = $input->getString("rettask", "day.listevents");
				$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&year=$year&month=$month&day=$day&Itemid=$Itemid", false), JTEXT::_("ICAL_EVENT_DELETED"));
				$this->redirect();
			}
		}
		else
		{
			if ($app->isClient('administrator'))
			{
				$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.list");
				$this->redirect();
			}
			else
			{
				$Itemid = $input->getInt("Itemid");
				list($year, $month, $day) = JEVHelper::getYMD();
				$rettask = $input - getString("rettask", "day.listevents");
				$this->setRedirect(Route::_('index.php?option=' . JEV_COM_COMPONENT . "&task=$rettask&year=$year&month=$month&day=$day&Itemid=$Itemid", false));
				$this->redirect();
			}
		}

	}

	function select()
	{

		Session::checkToken('request') or jexit('Invalid Token');
		$app    = Factory::getApplication();
		$input  = $app->input;

		// Get the view
		if ($app->isClient('administrator'))
		{
			$this->view = $this->getView("icalevent", "html", "AdminIcaleventView");
		}
		else
		{
			$this->view = $this->getView("icalevent", "html");
		}

		$this->_checkValidCategories();

		$showUnpublishedICS        = false;
		$showUnpublishedCategories = false;

		$db = Factory::getDbo();

		$icsFile = intval($app->getUserStateFromRequest("icsFile", "icsFile", 0));

		$catid    = intval($app->getUserStateFromRequest("catidIcalEvents", 'catid', 0));
		$catidtop = $catid;

		$state = 1;

		$limit      = intval($app->getUserStateFromRequest("viewlistlimit", 'limit', 10));
		$limitstart = intval($app->getUserStateFromRequest("view{" . JEV_COM_COMPONENT . "}limitstart", 'limitstart', 0));
		$search     = $app->getUserStateFromRequest("search{" . JEV_COM_COMPONENT . "}", 'search', '');
		$search     = $db->escape(trim(strtolower($search)));

		$created_by = $app->getUserStateFromRequest("createdbyIcalEvents", 'created_by', 0);

		// Is this a large dataset ?
		$query = "SELECT count(rpt.rp_id) from #__jevents_repetition as rpt ";
		$db->setQuery($query);
		$totalrepeats        = $db->loadResult();
		$this->_largeDataSet = 0;
		$cfg                 = JEVConfig::getInstance();
		if ($totalrepeats > $cfg->get('largeDataSetLimit', 100000))
		{
			$this->_largeDataSet = 1;
		}
		$cfg = JEVConfig::getInstance();
		$cfg->set('largeDataSet', $this->_largeDataSet);

		$where = array();
		$join  = array();

		if ($search)
		{
			$where[] = "LOWER(detail.summary) LIKE '%$search%'";
		}

		$user = Factory::getUser();

		// keep this incase we use filters in category lists
		$catwhere = "\n ev.catid IN(" . $this->queryModel->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams($input->getCmd("option"));

		if ($params->get("multicategory", 0))
		{
			$join[]     = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$join[]     = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$where[]    = " catmapcat.access  IN (" . JEVHelper::getAid($user) . ")";
			$where[]    = " catmap.catid IN(" . $this->queryModel->accessibleCategoryList() . ")";
			$needsgroup = true;
			$catwhere   = " 1";
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
			$join[]  = " #__jevents_icsfile as icsf ON icsf.ics_id = ev.icsid";
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
				$join[]  = " #__jevents_icsfile as icsf ON icsf.ics_id = ev.icsid";
				$where[] = "\n icsf.state=1";
			}
			else
			{
				$icsFrom = "";
			}
		}

		$user    = Factory::getUser();
		$where[] = "\n ev.access " . ' IN (' . JEVHelper::getAid($user) . ')';
		$where[] = "\n icsf.access " . ' IN (' . JEVHelper::getAid($user) . ')';

		$showpast = intval($app->getUserStateFromRequest("showpast", "showpast", 0));
		if (!$showpast)
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
			. (count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '')
			. (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');
		$db->setQuery($query);
		//echo $db->_sql;
		$total = 0;

		try
		{
			$total = $db->loadResult();
		} catch (Exception $e) {
			echo $e;
		}

		if ($limit > $total)
		{
			$limitstart = 0;
		}

		// if anon user plugin enabled then include this information
		$anonfields = "";
		$anonjoin   = "";
		if (PluginHelper::importPlugin("jevents", "jevanonuser"))
		{

			$anonfields = ", ac.name as anonname, ac.email as anonemail";
			$anonjoin   = "\n LEFT JOIN #__jev_anoncreator as ac on ac.ev_id = ev.ev_id";
		}

		$orderdir = $input->getCmd("filter_order_Dir", 'asc');
		$order    = $input->getCmd("filter_order", 'start');
		$dir      = $orderdir == "asc" ? "asc" : "desc";

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
			. (count($join) ? "\n LEFT JOIN  " . implode(' LEFT JOIN ', $join) : '')
			. (count($where) ? "\n WHERE " . implode(' AND ', $where) : '')
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
			$rows[$key]->state      = $rows[$key]->evstate;
			$groupname              = $rows[$key]->_groupname;
			$rows[$key]             = new jIcalEventRepeat($rows[$key]);
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

		// get list of categories
		$attribs = 'class="inputbox" size="1" onchange="document.adminForm.submit();"';
		$clist   = JEventsHTML::buildCategorySelect($catid, $attribs, null, $showUnpublishedCategories, false, $catidtop, "catid");

		// get list of ics Files
		$icsfiles = array();
		//$icsfiles[] = HTMLHelper::_('select.option', '0', "Choose ICS FILE" );
		$icsfiles[] = HTMLHelper::_('select.option', '-1', Text::_('ALL_ICS_FILES'));

		$query = "SELECT ics.ics_id as value, ics.label as text FROM #__jevents_icsfile as ics ";
		if (!$showUnpublishedICS)
		{
			$query .= " WHERE ics.state=1";
		}
		$query .= " ORDER BY ics.isdefault DESC, ics.label ASC";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$icsfiles = array_merge($icsfiles, $result);
		$icslist  = HTMLHelper::_('select.genericlist', $icsfiles, 'icsFile', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $icsFile);

		// get list of creators
		$sql = "SELECT distinct u.id, u.name, u.username FROM #__jevents_vevent as jev LEFT JOIN #__users as u on u.id=jev.created_by ORDER BY u.name";
		$db  = Factory::getDbo();
		$db->setQuery($sql);
		$users         = $db->loadObjectList();
		$userOptions   = array();
		$userOptions[] = HTMLHelper::_('select.option', "0", Text::_("JEV_EVENT_CREATOR"));
		foreach ($users as $user)
		{
			$userOptions[] = HTMLHelper::_('select.option', $user->id, $user->name . " ($user->username)");
		}
		$userlist = HTMLHelper::_('select.genericlist', $userOptions, 'created_by', 'class="inputbox" size="1"  onchange="document.adminForm.submit();"', 'value', 'text', $created_by);

		$options[] = HTMLHelper::_('select.option', '0', JText::_('JEV_NO'));
		$options[] = HTMLHelper::_('select.option', '1', JText::_('JEV_YES'));
		$plist     = HTMLHelper::_('select.genericlist', $options, 'showpast', 'class="gsl-select"  onchange="document.adminForm.submit();"', 'value', 'text', $showpast);

		$menulist = $this->targetMenu(0, "Itemid");

		$catData = JEV_CommonFunctions::getCategoryData();

		jimport('joomla.html.pagination');
		$pagination = new \Joomla\CMS\Pagination\Pagination($total, $limitstart, $limit);

		// Set the layout
		$this->view->setLayout('select');

		$this->view->rows       = $rows;
		$this->view->userlist   = $userlist;
		$this->view->menulist   = $menulist;
		$this->view->clist      = $clist;
		$this->view->plist      = $plist;
		$this->view->search     = $search;
		$this->view->icsList    = $icslist;
		$this->view->pagination    = $pagination;

		$this->view->display();

	}

	private function targetMenu($itemid = 0, $name = '')
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
				$item->title = "*** " . $item->title . " ***";
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
				$pt   = 0;
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
					$text      = '     ' . html_entity_decode($item->treename);
					$text      = str_repeat("&nbsp;", (isset($item->level) ? $item->level : $item->sublevel) * 4) . $text;
					$options[] = HTMLHelper::_('select.option', $item->id, $text, 'value', 'text', $disable);
				}
			}
		}

		return HTMLHelper::_('select.genericlist', $options, '' . $name, 'class="inputbox"', 'value', 'text', $itemid, $name);

	}

	public function cancel()
	{

		$app    = Factory::getApplication();
		$input = $app->input;
		$Itemid = $input->getInt("Itemid");

		// Clear the post, so event vars are not passed into the filters.
		$input->post = '';

		return $app->redirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.list&Itemid=$Itemid", false));
	}

}
