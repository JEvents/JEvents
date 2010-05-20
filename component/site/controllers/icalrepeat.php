<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: icalrepeat.php 1689 2010-02-03 07:51:04Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
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
		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/$theme/abstract/abstract.php");
		
	}

	function detail() {

		// Do we have to be logged in to see this event
		$user = &JFactory::getUser();
		if (JRequest::getInt("login",0) && $user->id==0)
		{			
			$uri = JURI::getInstance();
			$link = $uri->toString();
			$link = 'index.php?option=com_user&view=login&return='.base64_encode($link);
			$link = JRoute::_($link);
			$this->setRedirect($link,JText::_('JEV LOGIN TO VIEW EVENT'));
			return;
		}
		
		$evid =JRequest::getInt("rp_id",0);
		if ($evid==0){
			$evid =JRequest::getInt("evid",0);
			// In this case I do not have a repeat id so I 
		}
		// if cancelling from save of copy and edit use the old event id
		if ($evid==0){
			$evid =JRequest::getInt("old_evid",0);
		}
		$pop = intval(JRequest::getVar( 'pop', 0 ));
		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		$uid = urldecode((JRequest::getVar( 'uid', "" )));
		
		$document =& JFactory::getDocument();
		$viewType	= $document->getType();
		
		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "icalevent";
		$this->addViewPath($this->_basePath.DS."views".DS.$theme);
		$this->view = & $this->getView($view,$viewType, $theme, 
			array( 'base_path'=>$this->_basePath, 
				"template_path"=>$this->_basePath.DS."views".DS.$theme.DS.$view.DS.'tmpl',
				"name"=>$theme.DS.$view));

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
		$cfg	 = & JEVConfig::getInstance();
		$useCache = intval($cfg->get('com_cache', 0));
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache =& JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}
	}

	function edit(){
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			$user = &JFactory::getUser();
			if ($user->id){
				$this->setRedirect(JURI::root(),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
				//JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
			}
			else {
				$this->setRedirect(JRoute::_("index.php?option=com_user&view=login"),JText::_('JEV_NOTAUTH_CREATE_EVENT'));
			}
			return;
		}
		
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("editpopup",0)) JRequest::setVar("tmpl","component");
		
		parent::edit();
	}
	
	function save(){
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
		}
		parent::save();
	}
	
	function apply(){
		// Must be at least an event creator to save events
		$is_event_editor = JEVHelper::isEventCreator();
		if (!$is_event_editor){
			JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
		}
		parent::apply();
	}
	
	function delete(){
		$is_event_editor = JEVHelper::isEventDeletor();
		if (!$is_event_editor){
			JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
		}
		parent::delete();		
	}
	
	function deletefuture(){
		$is_event_editor = JEVHelper::isEventDeletor();
		if (!$is_event_editor){
			JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
		}
		parent::deletefuture();		
	}

	
	protected  function toggleICalEventPublish($cid,$newstate) {
		$is_event_editor = JEVHelper::isEventPublisher();
		if (!$is_event_editor){
			JError::raiseError( 403, JText::_("ALERTNOTAUTH") );
		}
		parent::toggleICalEventPublish($cid,$newstate);		
	}

}

