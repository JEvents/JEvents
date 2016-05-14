<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: admin.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class AdminController extends JControllerLegacy   {

	function __construct($config = array())
	{
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'listevents' );
		//		$this->registerTask( 'show',  'showContent' );

		// Load abstract "view" class
		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/$theme/abstract/abstract.php");
		$this->_basePath = $this->basePath;
		$this->_task = $this->task;
	}

	function listevents() {
		$jinput = JFactory::getApplication()->input;

		$is_event_editor = JEVHelper::isEventCreator();

		$Itemid	= JEVHelper::getItemid();

		$user = JFactory::getUser();
		if( !$is_event_editor ){
			$returnlink = JRoute::_( 'index.php?option=' . JEV_COM_COMPONENT . '&task=month.calendar&Itemid=' . $Itemid, false );
			$this->setRedirect( $returnlink, html_entity_decode( JText::_('JEV_NOPERMISSION') ));
			$this->redirect();
			return;
		}

		list($year,$month,$day) = JEVHelper::getYMD();

		// Joomla unhelpfully switched limitstart to start when sef is enabled!  includes/router.php line 390
		$limitstart = intval( $jinput->getInt('start', $jinput->getInt('limitstart', 	0)));
		
		$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest( 'jevlistlimit','limit', $params->get("com_calEventListRowsPpg",15)));

		$Itemid	= JEVHelper::getItemid();

		$task=$this->_task;

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$adminuser = $params->get("jevadmin",-1);
		
		if(JEVHelper::isAdminUser($user) || $user->id==$adminuser) {
			$creator_id = 'ADMIN';
		}else{
			$creator_id = $user->id;
		}

		// get the view

		$document = JFactory::getDocument();
		$viewType	= $document->getType();

		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "admin";
		$this->addViewPath($this->_basePath.'/'."views".'/'.$theme);
		$this->view = $this->getView($view,$viewType, $theme."View",
		array( 'base_path'=>$this->_basePath,
		"template_path"=>$this->_basePath.'/'."views".'/'.$theme.'/'.$view.'/'.'tmpl',
		"name"=>$theme.'/'.$view));

		// Set the layout
		$this->view->setLayout('listevents');

		$this->view->assign("Itemid",$Itemid);
		$this->view->assign("limitstart",$limitstart);
		$this->view->assign("limit",$limit);
		$this->view->assign("month",$month);
		$this->view->assign("day",$day);
		$this->view->assign("year",$year);
		$this->view->assign("task",$task);
		$this->view->assign("creator_id",$creator_id);

		$this->view->display();

	}

}
