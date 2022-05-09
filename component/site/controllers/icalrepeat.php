<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icalrepeat.php 3549 2012-04-20 09:26:21Z geraintedwards $
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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

include_once(JEV_ADMINPATH . "/controllers/icalrepeat.php");

class ICalRepeatController extends AdminIcalrepeatController
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

		//JLoader::register('JevRegistry', JPATH_SITE . "/components/com_jevents/libraries/registry.php");
		//$registry = JevRegistry::getInstance("jevents");
		//$registry->set('eventdetails', false);

		// Do we have to be logged in to see this event
		$user = Factory::getUser();

		$cfg        = JEVConfig::getInstance();
		$joomlaconf = Factory::getConfig();
		$useCache   = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);

		$input = Factory::getApplication()->input;

		if ($input->getInt("login", 0) && $user->id == 0)
		{
			$uri     = Uri::getInstance();
			$link    = $uri->toString();
			$comuser = version_compare(JVERSION, '1.6.0', '>=') ? "com_users" : "com_user";
			$link    = 'index.php?option=' . $comuser . '&view=login&return=' . base64_encode($link);
			$link    = Route::_($link, false);
			$this->setRedirect($link, Text::_('JEV_LOGIN_TO_VIEW_EVENT'));
			$this->redirect();

			return;
		}

		$evid = $input->getInt("rp_id", 0);
		if ($evid == 0)
		{
			$evid = $input->getInt("evid", 0);
			// In this case I do not have a repeat id so I 
		}

		// special case where loading a direct menu item to an event with nextrepeat specified
		/*
		 * This is problematic since it will affect direct links to a specific repeat e.g. from latest events module on this menu item
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$Itemid = JRequest::getInt("Itemid");
		if ($params->get("nextrepeat", 0) && $Itemid>0 )
		{
			$menu = Factory::getApplication()->getMenu();
			$menuitem = $menu->getItem($Itemid);
			if (!is_null($menuitem) && isset($menuitem->query["layout"]) && isset($menuitem->query["view"]) && isset($menuitem->query["rp_id"]))
			{
				// we put the xml file in the wrong folder - stupid.  Hard to move now!
				if ($menuitem->query["view"] == "icalrepeat" || $menuitem->query["view"] == "icalevent") {
					if (intval($menuitem->query["rp_id"]) == $evid ){
						$this->datamodel  = new JEventsDataModel();
						$this->datamodel->setupComponentCatids();
						list($year,$month,$day) = JEVHelper::getYMD();
						$uid = urldecode((JRequest::getVar( 'uid', "" )));
						$eventdata = $this->datamodel->getEventData( $evid, "icaldb", $year, $month, $day, $uid );
						if ($eventdata && isset($eventdata["row"])){
							$nextrepeat = $eventdata["row"]->getNextRepeat();
							if ($nextrepeat){
								//$evid = $nextrepeat->rp_id();
							}
						}
					}
				}
			}			
		}
		 *
		 */

		// If cancelling edit in popup then stay in popup
		$popupdetail = PluginHelper::getPlugin("jevents", "jevpopupdetail");
		if ($popupdetail)
		{
			$popuppluginparams = new JevRegistry($popupdetail->params);
			$popupdetail       = $popuppluginparams->get("detailinpopup", 1);
			if ($popupdetail)
			{
				$input->set("pop", 1);
				$input->set("tmpl", "component");
			}
		}

		// if cancelling from save of copy and edit use the old event id
		if ($evid == 0)
		{
			$evid = $input->getInt("old_evid", 0);
		}
		$pop = intval($input->getInt('pop', 0));
		list($year, $month, $day) = JEVHelper::getYMD();
		$Itemid = JEVHelper::getItemid();

		$uid = urldecode(($input->getString('uid', "")));

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

		if ($user->get('id') || !$useCache)
		{
			$this->view->display();
		}
		else
		{
			$cache = Factory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}

		if( $pop && intval($input->get('print')) == 1 && $cfg->get("autoprint", 0))
		{
			$document = Factory::getDocument();

			// auto print and close
			if ($input->getInt("pop", 0))
			{
				$autoPrint = <<< SCRIPT
window.addEventListener('load', function() {
	window.addEventListener("afterprint", function(event)  { 
	    window.close();
	});
	window.print();
});
SCRIPT;

				$document->addScriptDeclaration($autoPrint);
			}


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
			$user = Factory::getUser();
			if ($user->id)
			{
				$this->setRedirect(Uri::root(), Text::_('JEV_NOTAUTH_CREATE_EVENT', 'error'));
				$this->redirect();
				//throw new Exception( Text::_('ALERTNOTAUTH'), 403);
			}
			else
			{
				$this->setRedirect(Route::_("index.php?option=com_users&view=login"), Text::_('JEV_NOTAUTH_CREATE_EVENT', 'error'));
				$this->redirect();
			}

			return;
		}

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup", 0)) $input->set("tmpl", "component");

		parent::edit();
	}

	function save($key = null, $urlVar = null)
	{

		$is_event_editor = JEVHelper::isEventCreator();
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
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}
		parent::apply();
	}

	function delete()
	{

		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}
		parent::delete();
	}

	function deletefuture()
	{

		$is_event_editor = JEVHelper::isEventDeletor();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}
		parent::deletefuture();
	}

	function select()
	{

		HTMLHelper::_('stylesheet', 'system/adminlist.css', array(), true);
		parent::select();
	}

	protected function toggleICalEventPublish($cid, $newstate)
	{

		$is_event_editor = JEVHelper::isEventPublisher();
		if (!$is_event_editor)
		{
			throw new Exception(Text::_('ALERTNOTAUTH'), 403);

			return false;
		}
		parent::toggleICalEventPublish($cid, $newstate);
	}

}

