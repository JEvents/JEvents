<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: icalrepeat.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

include_once(JEV_ADMINPATH."/controllers/icalrepeat.php");

class ICalRepeatController extends AdminIcalrepeatController   {

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

		if ($jinput->getInt("login", 0) && $user->id == 0)
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
			$evid =$jinput->getInt("evid", 0);
			// In this case I do not have a repeat id so I 
		}

		// special case where loading a direct menu item to an event with nextrepeat specified
		/*
		 * This is problematic since it will affect direct links to a specific repeat e.g. from latest events module on this menu item
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$Itemid = JRequest::getInt("Itemid");
		if ($params->get("nextrepeat", 0) && $Itemid>0 )
		{
			$menu = JFactory::getApplication()->getMenu();
			$menuitem = $menu->getItem($Itemid);
			if (!is_null($menuitem) && isset($menuitem->query["layout"]) && isset($menuitem->query["view"]) && isset($menuitem->query["rp_id"]))
			{
				// we put the xml file in the wrong folder - stupid.  Hard to move now!
				if ($menuitem->query["view"] == "icalrepeat" || $menuitem->query["view"] == "icalevent") {
					if (intval($menuitem->query["rp_id"]) == $evid ){
						$this->datamodel  =  new JEventsDataModel();
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

		// if cancelling from save of copy and edit use the old event id
		if ($evid==0){
			$evid =$jinput->getInt("old_evid", 0);
		}
		$pop = intval($jinput->getInt( 'pop', 0));
		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		$uid = urldecode(($jinput->getString('uid', "")));

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
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache = JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}
	}

	function edit($key = NULL, $urlVar = NULL){
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			$user = JFactory::getUser();
			if ($user->id){
				$this->setRedirect(JURI::root(),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
				$this->redirect();
				//throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			}
			else {
				$comuser= version_compare(JVERSION, '1.6.0', '>=') ? "com_users":"com_user";
				$this->setRedirect(JRoute::_("index.php?option=$comuser&view=login"),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
				$this->redirect();
			}
			return;
		}
		
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup",0)) JRequest::setVar("tmpl","component");
		
		parent::edit();
	}
	
	function save($key = NULL, $urlVar = NULL){
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
	
	function delete(){
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}
		parent::delete();		
	}
	
	function deletefuture(){
		$is_event_editor = JEVHelper::isEventDeletor();
		if (!$is_event_editor){
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}
		parent::deletefuture();		
	}

	function select() {
		JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
		parent::select();
	}

	protected  function toggleICalEventPublish($cid,$newstate) {
		$is_event_editor = JEVHelper::isEventPublisher();
		if (!$is_event_editor){
			throw new Exception( JText::_('ALERTNOTAUTH'), 403);
			return false;
		}
		parent::toggleICalEventPublish($cid,$newstate);		
	}

}

