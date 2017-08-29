<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: icalevent.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

include_once(JEV_ADMINPATH."/controllers/icalevent.php");

class ICalEventController extends AdminIcaleventController   {

	function __construct($config = array())
	{
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'detail' );

		// Load abstract "view" class
		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/$theme/abstract/abstract.php");
		if (!isset($this->_basePath)){
			$this->_basePath = $this->basePath;
			$this->_task = $this->task;
		}
	}

	function detail() {

		// Do we have to be logged in to see this event
		$user = JFactory::getUser();

		$jinput = JFactory::getApplication()->input;

		if ($jinput->getInt("login", 0) && $user->id==0)
		{
			$uri = JURI::getInstance();
			$link = $uri->toString();
			$comuser= version_compare(JVERSION, '1.6.0', '>=') ? "com_users":"com_user";
			$link = 'index.php?option='.$comuser.'&view=login&return='.base64_encode($link);
			$link = JRoute::_($link, false);
			$this->setRedirect($link,JText::_('JEV_LOGIN_TO_VIEW_EVENT'));
			$this->redirect();
			return;
		}

		$evid = $jinput->getInt("rp_id", 0);
		if ($evid==0){
			$evid = $jinput->getInt("evid", 0);
			// if cancelling from save of copy and edit use the old event id
			if ($evid==0){
				$evid =$jinput->getInt("old_evid", 0);
			}
			// In this case I do not have a repeat id so I find the first one that matches
			$datamodel = new JEventsDataModel("JEventsAdminDBModel");
			$vevent = $datamodel->queryModel->getVEventById( $evid);
			$event = new jIcalEventDB($vevent);
			//$repeat = $event->getFirstRepeat();
			$repeat = $event->getNextRepeat();
			if ($repeat){
				$evid=$repeat->rp_id();
			}
		}
		$pop = intval($jinput->getInt('pop', 0 ));
		$uid = urldecode(($jinput->getString('uid', "")));
		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		// seth month and year to be used by mini-calendar if needed
		if (isset($repeat)) {
			if (!$jinput->getInt("month", 0)) $jinput->set("month", $repeat->mup());
			if (!$jinput->getInt("year", 0))  $jinput->set("year", $repeat->yup());
		}

		$document = JFactory::getDocument();
		$viewType	= $document->getType();

		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "icalevent";
		$this->addViewPath($this->_basePath.'/'."views".'/'.$theme);
		$this->view = $this->getView($view,$viewType, $theme."View",
			array( 'base_path'=>$this->_basePath,
				"template_path"=>$this->_basePath.'/'."views".'/'.$theme.'/'.$view.'/'.'tmpl',
				"name"=>$theme.'/'.$view));

		// Set the layout
		$this->view->setLayout("detail");

		$this->view->assign("Itemid",$Itemid);
		$this->view->assign("month",$month);
		$this->view->assign("day",$day);
		$this->view->assign("year",$year);
		$this->view->assign("task",$this->_task);
		$this->view->assign("pop",$pop);
		$this->view->assign("evid",$evid);
		$this->view->assign("jevtype","icaldb");
		$this->view->assign("uid",$uid);

		// View caching logic -- simple... are we logged in?
		$cfg	 = JEVConfig::getInstance();
		$joomlaconf = JFactory::getConfig();
		$useCache = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);
		$user = JFactory::getUser();
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache = JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}
	}

	function edit($key = NULL, $urlVar = NULL){

		// Must be at least an event creator to edit or create events
		// We check specific event editing permissions in the parent class
		$is_event_creator = JEVHelper::isEventCreator();
		$is_event_editor = JEVHelper::isEventEditor();
		
		$user = JFactory::getUser();
		if ((!$is_event_creator && !$is_event_editor) || ($user->id==0 && JRequest::getInt("evid",0)>0)){
			if ($user->id){
				$this->setRedirect(JURI::root(),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
				$this->redirect();
				//throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			}
			else {
				$uri = JURI::getInstance();
				$link = $uri->toString();
				$this->setRedirect(JRoute::_("index.php?option=com_users&view=login&return=".base64_encode($link)),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
				$this->redirect();
			}
			return;
		}

		// attach data model component catids at this point so it will affect the choice of calendars too
		$this->dataModel->setupComponentCatids();

		parent::edit();
	}

	function editcopy(){
		// Must be at least an event creator to edit or create events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}
		$this->editCopy = true;

		// attach data model component catids at this point so it will affect the choice of calendars too
		$this->dataModel->setupComponentCatids();

		parent::edit();
	}


	function save($key = NULL, $urlVar = NULL){
		// Must be at least an event creator to save events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}
		parent::save();
	}

	function apply(){
		// Must be at least an event creator to save events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}
		parent::apply();
	}

	function select() {
		JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
		parent::select();
	}

	public function edit_cancel() {
		$session = JFactory::getSession();
                $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
-               $fallback = $params->get("editreturnto", "day.listevents");
		$ref = $session->get('jev_referrer',$fallback, 'extref');

		$this->setRedirect($ref);
		$this->redirect();

	}


}

