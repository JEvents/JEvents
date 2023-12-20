<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icals.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

include_once(JEV_ADMINPATH . "/controllers/icals.php");

use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

#[\AllowDynamicProperties]
class ICalsController extends AdminIcalsController
{

	function __construct($config = array())
	{

		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask('ical');
		//		$this->registerTask( 'show',  'showContent' );
		// Ensure authorised to do this
		$cfg = JEVConfig::getInstance();
		if ($cfg->get("disableicalexport", 0) && !$cfg->get("feimport", 0))
		{
			$query = "SELECT icsf.* FROM #__jevents_icsfile as icsf where icsf.autorefresh=1";
			$db    = Factory::getDbo();
			$db->setQuery($query);
			$allICS = $db->loadObjectList();
			if (count($allICS) == 0)
			{
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}
		}

		// Load abstract "view" class
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents' . ucfirst($theme) . 'View', JEV_VIEWS . "/$theme/abstract/abstract.php");
		if (!isset($this->_basePath))
		{
			$this->_basePath = $this->basePath;
			$this->_task     = $this->task;
		}

	}

	// Thanks to HiFi
	function ical()
	{

		// Ensure authorised to do this
		$cfg = JEVConfig::getInstance();
		if ($cfg->get("disableicalexport", 0))
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}

		list($year, $month, $day) = JEVHelper::getYMD();
		$Itemid = JEVHelper::getItemid();

		// get the view

		$document = Factory::getDocument();
		$viewType = $document->getType();

		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "icals";
		$this->addViewPath($this->_basePath . '/' . "views" . '/' . $theme);
		$this->view = $this->getView($view, $viewType, $theme . "View",
			array('base_path'     => $this->_basePath,
			      "template_path" => $this->_basePath . '/' . "views" . '/' . $theme . '/' . $view . '/' . 'tmpl',
			      "name"          => $theme . '/' . $view));

		// Set the layout
		$this->view->setLayout('ical');

		$this->view->Itemid = $Itemid;
		$this->view->month  = $month;
		$this->view->day    = $day;
		$this->view->year   = $year;
		$this->view->task   = $this->_task;

		// View caching logic -- simple... are we logged in?
		$cfg        = JEVConfig::getInstance();
		$joomlaconf = Factory::getConfig();
		$useCache   = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);
		$user       = Factory::getUser();
		if ($user->get('id') || !$useCache)
		{
			$this->view->display();
		}
		else
		{
			$cache = Factory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}

	}

	function export()
	{

		$app    = Factory::getApplication();
		$input  = $app->input;
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$years = $input->getString('years', '0');
		$years = explode(",", $years);
		$years = JEVHelper::forceIntegerArray($years, true);

		$cats  = $input->getString('catids', '0');
		$cats = explode(",", $cats);
		$cats = JEVHelper::forceIntegerArray($cats, true);

		// validate the key
		$icalkey = $params->get("icalkey", "secret phrase");

		$outlook2003icalexport = $input->getInt("outlook2003", 0) && $params->get("outlook2003icalexport", 1);
		if ($outlook2003icalexport)
		{
			$input->set("icf", 1);
		}
		$privatecalendar = false;
		$k               = $input->getString("k", "NONE");
		$pk              = $input->getString("pk", "NONE");
		$userid          = $input->getInt("i", 0);
		if ($pk != "NONE")
		{
			if (!$userid)
			{
				throw new Exception(Text::_('JEV_ERROR'), 403);

				return false;
			}
			$privatecalendar = true;
			$puser           = User::getInstance($userid);
			$key             = md5($icalkey . $cats . $years . $puser->password . $puser->username . $puser->id);

			if ($key != $pk)
			{
				throw new Exception(Text::_('JEV_ERROR'), 403);

				return false;
			}

			// ensure "user" can access non-public categories etc.
			$this->dataModel->aid        = JEVHelper::getAid($puser);
			$this->dataModel->accessuser = $puser->get('id');

			$registry = JevRegistry::getInstance("jevents");
			$registry->set("jevents.icaluser", $puser);
		}
		else if ($k != "NONE")
		{
			if ($params->get("disableicalexport", 0))
			{
				throw new Exception(Text::_('ALERTNOTAUTH'), 403);

				return false;
			}

			$key = md5($icalkey . $cats . $years);
			if ($key != $k)
			{
				throw new Exception(Text::_('JEV_ERROR'), 403);

				return false;
			}
		}
		else
		{
			throw new Exception(Text::_('JEV_ERROR'), 403);

			return false;
		}

		// Fix the cats
		$cats = explode(',', $cats);
		// hardening!
		$cats = JEVHelper::forceIntegerArray($cats, false);
		if ($cats != array(0))
		{
			$separator = $params->get("catseparator", "|");
			$input->set("catids", implode($separator, $cats));
		}
		else
		{
			$input->set("catids", '');
		}

		//Parsing variables from URL
		//Year
		// All years
		if ($years == 0)
		{
			$years  = array();
			for ($y = JEVHelper::getMinYear(); $y <= JEVHelper::getMaxYear(); $y++)
			{
				if (!in_array($y, $years))
					$years[] = $y;
			}
			$years = ArrayHelper::toInteger($years);
		}
		else if ($years != "NONE")
		{
			// already fetched and sanitised
			$years = explode(",", $years);

			if (!is_array($years) || count($years) == 0)
			{
				list($y, $m, $d) = JEVHelper::getYMD();
				$years = array($y);
			}
			$years = ArrayHelper::toInteger($years);
		}
		else
		{
			list($y, $m, $d) = JEVHelper::getYMD();
			$years = array($y);
		}

		// Lockin hte categories from the URL
		$Itemid = $input->getInt("Itemid", 0);
		if (!$Itemid)
		{
			$input->set("Itemid", 1);
		}
		$this->dataModel->setupComponentCatids();

		// Just in case we don't have jevents plugins registered yet
		PluginHelper::importPlugin("jevents");

		// And then the real work
		// Force all only the one repeat
		$cfg = JEVConfig::getInstance();
		$icalEvents = array();
		foreach ($years as $year)
        {
            $startdate     = $year . "-01-01";
            $enddate       = $year . "-12-31";
            $cfg->set('com_showrepeats', 0);
            $rows          = $this->dataModel->getRangeData( $startdate, $enddate, 0, 0, "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC", '!irregular'  );
            $cfg->set('com_showrepeats', 1);
            $irregularrows = $this->dataModel->getRangeData( $startdate, $enddate, 0, 0, "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC", 'irregular' );
            if ( isset( $rows["rows"] ) )
            {
                foreach ( $rows["rows"] as $row )
                {
                    if ( ! array_key_exists( $row->ev_id(), $icalEvents ) )
                    {

                        $app->triggerEvent( 'onExportRow', array( &$row ) );
                        $icalEvents[$row->ev_id()] = $row;

                        // Parse Content Plugins for Description
                        $row->_description = HTMLHelper::_( 'content.prepare', $row->_content );
                    }
                }
                unset( $rows );
            }
            if ( isset( $irregularrows["rows"] ) )
            {
                foreach ( $irregularrows["rows"] as $row )
                {
                    if ( ! array_key_exists( $row->ev_id() . "_" .  $row->rp_id(), $icalEvents ) )
                    {

                        $app->triggerEvent( 'onExportRow', array( &$row ) );
                        $icalEvents[$row->ev_id() . "_" .  $row->rp_id()] = $row;

                        // Parse Content Plugins for Description
                        $row->_description = HTMLHelper::_( 'content.prepare', $row->_content );
                    }
                }
                unset( $irregularrows );
            }
		}

		if ($userid)
			$user = User::getInstance($userid);

		// get the view
		$this->view = $this->getView("icals", "html");
		$this->view->setLayout("export");
		$this->view->dataModel              = $this->dataModel;
		$this->view->outlook2003icalexport  = $outlook2003icalexport;
		$this->view->icalEvents             = $icalEvents;
		$this->view->withrepeats            = true;

		$this->view->export();

		return;

	}

	function icalevent()
	{

		$this->exportEvent(true);

	}

	private function exportEvent($withrepeats = true)
	{

		$input  = Factory::getApplication()->input;
		$rpid = $input->getInt("evid", 0);
		if (!$rpid)
			return;

		list($year, $month, $day) = JEVHelper::getYMD();
		$repeat = $this->dataModel->getEventData($rpid, "icaldb", $year, $month, $day);

		if ($repeat && is_array($repeat) && isset($repeat["row"]) && $repeat["row"]->rp_id() == $rpid)
		{
			$a = $repeat["row"];
			$this->dataModel->setupComponentCatids();

			if ($withrepeats && $a->hasrepetition())
			{
				$a = $a->getOriginalFirstRepeat();
			}
			if (!$a) return;

			$input->set("tmpl", "component");

			// just incase we don't have jevents plugins registered yet
			//PluginHelper::importPlugin("jevents");
			//Factory::getApplication()->trigger('onExportRow', array(&$row));
			$icalEvents              = array();
			$icalEvents[$a->ev_id()] = $a;

			// get the view
			$this->view = $this->getView("icals", "html");
			$this->view->setLayout("export");
			$this->view->dataModel              = $this->dataModel;
			$this->view->outlook2003icalexport  = false;
			$this->view->icalEvents             = $icalEvents;
			$this->view->withrepeats            = $withrepeats;

			$this->view->export();

			return;
		}

	}

	function icalrepeat()
	{

		$this->exportEvent(false);

	}

	function importform()
	{

		// Can only do this if can add an event
		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor)
		{
			$user = Factory::getUser();
			if ($user->id)
			{
				$this->setRedirect(Uri::root(), Text::_('JEV_NOTAUTH_CREATE_EVENT'));
				$this->redirect();
			}
			else
			{
				$this->setRedirect(Route::_("index.php?option=com_users&view=login"), Text::_('JEV_NOTAUTH_CREATE_EVENT', 'error'));
				$this->redirect();
			}

			return;
		}

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!$params->get("feimport", 0))
		{
			return;
		}

		$document = Factory::getDocument();
		$viewType = $document->getType();

		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "icals";
		$this->addViewPath($this->_basePath . '/' . "views" . '/' . $theme);
		$this->view = $this->getView($view, $viewType, $theme . "View",
			array('base_path'     => $this->_basePath,
			      "template_path" => $this->_basePath . '/' . "views" . '/' . $theme . '/' . $view . '/' . 'tmpl',
			      "name"          => $theme . '/' . $view));

		// Set the layout
		$this->view->setLayout('importform');
		$this->view->task = $this->_task;

		// Get all the raw native calendars
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

		$excats = "0";
		if ($jevuser && $jevuser->categories != "" && $jevuser->categories != "all")
		{
			// Find which categories to exclude
			$db     = Factory::getDbo();
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
			$clist      = HTMLHelper::_('select.genericlist', $icalList, 'icsid', " onchange='preselectCategory(this);'", 'ics_id', 'label', 0);
			$this->view->clistChoice    = true;
			$this->view->defaultCat     = 0;
		}
		else
		{
			if (count($nativeCals) == 0 || !is_array($nativeCals))
			{

				Factory::getApplication()->enqueueMessage(Text::_('INVALID_CALENDAR_STRUCTURE'), 'warning');

			}

			$icsid = current($nativeCals)->ics_id;

			$clist = '<input type="hidden" name="icsid" value="' . $icsid . '" />';
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

		$this->view->excats         = $excats;
		$this->view->nativeCals     = $nativeCals;
		$this->view->clist          = $clist;

		// View caching logic -- simple... are we logged in?
		$cfg        = JEVConfig::getInstance();
		$joomlaconf = Factory::getConfig();
		$useCache   = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);
		$user       = Factory::getUser();
		if ($user->get('id') || !$useCache)
		{
			$this->view->display();
		}
		else
		{
			$cache = Factory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}

	}

	function importdata()
	{

		// Check for request forgeries
		Session::checkToken() or jexit('Invalid Token');

		$app    = Factory::getApplication();
		$input  = $app->input;

		// Can only do this if can add an event
		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor)
		{
			$user = Factory::getUser();
			if ($user->id)
			{
				$this->setRedirect(Uri::root(), Text::_('JEV_NOTAUTH_CREATE_EVENT', 'error'));
				$this->redirect();
			}
			else
			{
				$this->setRedirect(Route::_("index.php?option=com_users&view=login"), Text::_('JEV_NOTAUTH_CREATE_EVENT', 'error'));
				$this->redirect();
			}

			return;
		}

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!$params->get("feimport", 0))
		{
			return;
		}

		$catid          = $input->getInt('catid', 0);
		$ignoreembedcat = $input->getInt('ignoreembedcat', 0);
		$createnewcategories = $input->getInt('createnewcategories', 1);
		// Should come from the form or existing item
		$access      = 0;
		$state       = 1;
		$uploadURL   = $input->getString('uploadURL', '');
		$icsLabel    = uniqid('icsLabel');
		$icsid       = $input->getInt('icsid', 0);
		$icsFile     = false;
		$autorefresh = 0;

		if ($catid == 0)
		{
			// Paranoia, should not be here, validation is done by java script
			// Just load the ical event list on redirect for now.
			$redirect_task = "icalevent.list";
			$app->enqueueMessage(JTExt::_('JEV_FATAL_ERROR') . Text::_('JEV_E_WARNCAT'), 'error');

			$this->setRedirect("index.php?option=" . JEV_COM_COMPONENT . "&task=$redirect_task", Text::_('JEV_E_WARNCAT'));
			$this->redirect();

			return;
		}

		// I need a better check and expiry information etc.
		if (StringHelper::strlen($uploadURL) > 0)
		{
			$icsFile = iCalICSFile::newICSFileFromURL($uploadURL, $icsid, $catid, $access, $state, $icsLabel, $autorefresh, $ignoreembedcat, $createnewcategories);
		}
		else if (isset($_FILES['upload']) && is_array($_FILES['upload']))
		{
			$file = $_FILES['upload'];
			if ($file['size'] == 0)
			{//|| !($file['type']=="text/calendar" || $file['type']=="application/octet-stream")){
				$app->enqueueMessage(Text::_('JEV_EMPTY_FILE_UPLOAD'), 'warning');
				$icsFile = false;
			}
			else
			{
				$icsFile = iCalICSFile::newICSFileFromFile($file, $icsid, $catid, $access, $state, $icsLabel, 0, $ignoreembedcat, $createnewcategories);
			}
		}


		$count = 0;
		if ($icsFile !== false)
		{
			$count = $icsFile->storeEvents();
		}

		list($year, $month, $day) = JEVHelper::getYMD();
		ob_end_clean();
		?>
		<script type="text/javascript">
            window.alert("<?php echo Text::sprintf("JEV_EVENTS_IMPORTED", $count); ?>");
            try {
                window.parent.closeJevModalBySelector('#myImportModal');
            }
            catch (e) {
            }
            try {
                window.parent.SqueezeBox.close();
            }
            catch (e) {
            }
            //window.parent.location.reload();
		</script>
		<?php
		exit();

	}

	protected function setDescription($desc)
	{
		return JEVHelper::setDescription($desc);
	}

	private function replacetags($description)
	{
		return JEVHelper::replacetags($replacetags);
	}

	private function wraplines($input, $line_max = 76, $quotedprintable = false)
	{
		return JEVHelper::wraplines($input, $line_max, $quotedprintable);
	}

	private function vtimezone($icalEvents)
	{

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$tzid = "";
		if (is_callable("date_default_timezone_set"))
		{
			$current_timezone = date_default_timezone_get();
			// Do the Timezone definition
			$tzid = ";TZID=$current_timezone";
			// find the earliest start date
			$firststart = false;
			foreach ($icalEvents as $a)
			{
				if (!$firststart || $a->getUnixStartTime() < $firststart)
					$firststart = $a->getUnixStartTime();
			}
			// Subtract 1 leap year to make sure we have enough transitions
			$firststart -= 31622400;
			$timezone   = new DateTimeZone($current_timezone);

			if (version_compare(PHP_VERSION, "5.3.0", "ge"))
			{
				$transitions = $timezone->getTransitions($firststart);
			}
			else
			{
				$transitions = $timezone->getTransitions();
			}
			$tzindex = 0;
			while (JevDate::strtotime($transitions[$tzindex]['time']) < $firststart)
			{
				$tzindex++;
			}
			$transitions = array_slice($transitions, $tzindex);
			if (count($transitions) >= 2)
			{
				$lastyear = JEVHelper::getMaxYear();
				echo "BEGIN:VTIMEZONE\n";
				echo "TZID:$current_timezone\n";
				for ($t = 0; $t < count($transitions); $t++)
				{
					$transition = $transitions[$t];
					if ((int) $transition['isdst'] == 0)
					{
						if (JevDate::strftime("%Y", $transition['ts']) > $lastyear)
							continue;
						echo "BEGIN:STANDARD\n";
						echo "DTSTART:" . JevDate::strftime("%Y%m%dT%H%M%S\n", $transition['ts']);
						if ($t < count($transitions) - 1)
						{
							echo "RDATE:" . JevDate::strftime("%Y%m%dT%H%M%S\n", $transitions[$t + 1]['ts']);
						}
						// if its the first transition then assume the old setting is the same as the next otherwise use the previous value
						$prev = $t;
						$prev += ($t == 0) ? 1 : -1;

						$offset = $transitions[$prev]["offset"];
						$sign   = $offset >= 0 ? "+" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETFROM:$offset\n";

						$offset = $transitions[$t]["offset"];
						$sign   = $offset >= 0 ? "" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETTO:$offset\n";
						echo "TZNAME:$current_timezone " . $transitions[$t]["abbr"] . "\n";
						echo "END:STANDARD\n";
					}
				}
				for ($t = 0; $t < count($transitions); $t++)
				{
					$transition = $transitions[$t];
					if ((int) $transition['isdst'] == 1)
					{
						if (JevDate::strftime("%Y", $transition['ts']) > $lastyear)
							continue;
						echo "BEGIN:DAYLIGHT\n";
						echo "DTSTART:" . JevDate::strftime("%Y%m%dT%H%M%S\n", $transition['ts']);
						if ($t < count($transitions) - 1)
						{
							echo "RDATE:" . JevDate::strftime("%Y%m%dT%H%M%S\n", $transitions[$t + 1]['ts']);
						}
						// if its the first transition then assume the old setting is the same as the next otherwise use the previous value
						$prev = $t;
						$prev += ($t == 0) ? 1 : -1;

						$offset = $transitions[$prev]["offset"];
						$sign   = $offset >= 0 ? "+" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETFROM:$offset\n";

						$offset = $transitions[$t]["offset"];
						$sign   = $offset >= 0 ? "" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETTO:$offset\n";
						echo "TZNAME:$current_timezone " . $transitions[$t]["abbr"] . "\n";
						echo "END:DAYLIGHT\n";
					}
				}
				echo "END:VTIMEZONE\n";
			}
		}

		return $tzid;

	}

}
