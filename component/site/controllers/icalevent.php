<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icalevent.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

include_once(JEV_ADMINPATH . "/controllers/icalevent.php");

#[\AllowDynamicProperties]
class ICalEventController extends AdminIcaleventController
{

	function __construct($config = array())
	{

		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask('detail');

		// Load abstract "view" class
		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents' . ucfirst($theme) . 'View', JEV_VIEWS . "/$theme/abstract/abstract.php");
		if (!isset($this->_basePath))
		{
			$this->_basePath = $this->basePath;
			$this->_task     = $this->task;
		}
	}

	function detail()
	{

		// Do we have to be logged in to see this event
		$user = Factory::getUser();

		$cfg        = JEVConfig::getInstance();
		$joomlaconf = Factory::getConfig();
		$useCache   = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);

		$input = Factory::getApplication()->input;

		if ($input->getInt("login", 0) && $user->id == 0)
		{
			$uri  = Uri::getInstance();
			$link = $uri->toString();
			$link = 'index.php?option=com_users&view=login&return=' . base64_encode($link);
			$link = Route::_($link, false);
			$this->setRedirect($link, Text::_('JEV_LOGIN_TO_VIEW_EVENT'));
			$this->redirect();

			return;
		}

		$evid = $input->getInt("rp_id", 0);

		if ($evid == 0)
		{
			$evid = $input->getInt("evid", 0);
			// if cancelling from save of copy and edit use the old event id
			if ($evid == 0)
			{
				$evid = $input->getInt("old_evid", 0);
			}
			// In this case I do not have a repeat id so I find the first one that matches
			$datamodel = new JEventsDataModel("JEventsAdminDBModel");
			$vevent    = $datamodel->queryModel->getVEventById($evid);
			$event     = new jIcalEventDB($vevent);
			//$repeat = $event->getFirstRepeat();
			$repeat = $event->getNextRepeat();
			if ($repeat)
			{
				$evid = $repeat->rp_id();
			}
		}
		$pop = intval($input->getInt('pop', 0));
		$uid = urldecode(($input->getString('uid', "")));
		list($year, $month, $day) = JEVHelper::getYMD();
		$Itemid = JEVHelper::getItemid();

		// seth month and year to be used by mini-calendar if needed
		if (isset($repeat))
		{
			if (!$input->getInt("month", 0)) $input->set("month", $repeat->mup());
			if (!$input->getInt("year", 0)) $input->set("year", $repeat->yup());
		}

		$document = Factory::getDocument();
		$viewType = $document->getType();

		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "icalevent";

		Factory::getApplication()->triggerEvent('onBeforeLoadView', array($view, $theme, $viewType, 'icalrepeat.detail', $useCache));

		$this->addViewPath($this->_basePath . '/' . "views" . '/' . $theme);
		$this->view = $this->getView($view, $viewType, $theme . "View",
			array('base_path'     => $this->_basePath,
			      "template_path" => $this->_basePath . '/' . "views" . '/' . $theme . '/' . $view . '/' . 'tmpl',
			      "name"          => $theme . '/' . $view));

		// Set the layout
		$this->view->setLayout("detail");

		$this->view->Itemid     = $Itemid;
		$this->view->month      = $month;
		$this->view->day        = $day;
		$this->view->year       = $year;
		$this->view->task       = $this->_task;
		$this->view->pop        = $pop;
		$this->view->evid       = $evid;
		$this->view->jevtype    = "icaldb";
		$this->view->uid        = $uid;

		// View caching logic -- simple... are we logged in?
		$user = Factory::getUser();
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

	function edit($key = null, $urlVar = null)
	{

		$input  = Factory::getApplication()->input;
		// Must be at least an event creator to edit or create events
		// We check specific event editing permissions in the parent class
		$is_event_creator = JEVHelper::isEventCreator();
		$is_event_editor  = JEVHelper::isEventEditor();

		$user = Factory::getUser();
		if ((!$is_event_creator && !$is_event_editor) || ($user->id == 0 && $input->getInt("evid", 0) > 0))
		{
			if ($user->id)
			{
				$this->setRedirect(Uri::root(), Text::_('JEV_NOTAUTH_CREATE_EVENT', 'error'));
				$this->redirect();
				//throw new Exception( Text::_('ALERTNOTAUTH'), 403);
			}
			else
			{
				$uri  = Uri::getInstance();
				$link = $uri->toString();
				$this->setRedirect(Route::_("index.php?option=com_users&view=login&return=" . base64_encode($link)), Text::_('JEV_NOTAUTH_CREATE_EVENT', 'error'));
				$this->redirect();
			}

			return;
		}

		// attach data model component catids at this point so it will affect the choice of calendars too
		$this->dataModel->setupComponentCatids();

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup", 0)) $input->set("tmpl", "component");

		parent::edit();
	}

	function editcopy()
	{
		$input = Factory::getApplication()->input;
		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator() || JEVHelper::isEventEditor();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}
		$this->editCopy = true;

		// attach data model component catids at this point so it will affect the choice of calendars too
		$this->dataModel->setupComponentCatids();

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup", 0)) $input->set("tmpl", "component");

		parent::edit();
	}


	function save($key = null, $urlVar = null)
	{

		// Must be at least an event creator to save events
		$is_event_editor = JEVHelper::isEventCreator() || JEVHelper::isEventEditor();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}
		parent::save();
	}

	function apply()
	{

		// Must be at least an event creator to save events
		$is_event_editor = JEVHelper::isEventCreator() || JEVHelper::isEventEditor();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}
		parent::apply();
	}

	function select()
	{

		HTMLHelper::_('stylesheet', 'system/adminlist.css', array(), true);
		parent::select();
	}

	public function edit_cancel()
	{

		$session = Factory::getSession();
		$params  = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$fallback = $params->get("editreturnto", "day.listevents");
		$ref = $session->get('jev_referer', $fallback, 'extref');

		if ($ref == $fallback)
		{
			$ref = JRoute::_("index.php?option=com_jevents&task=" . $fallback);
		}
		$this->setRedirect($ref);
		$this->redirect();

	}


}

