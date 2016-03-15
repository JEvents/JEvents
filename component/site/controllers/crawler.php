<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: crawler.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2015 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class CrawlerController extends JControllerLegacy   {

	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerDefaultTask( 'listevents' );

		JLoader::register('JEventsDefaultView',JEV_VIEWS."/default/abstract/abstract.php");
		if (!isset($this->_basePath)){
			$this->_basePath = $this->basePath;
			$this->_task = $this->task;
		}
	}

	function listevents() {

		JRequest::setVar("tmpl","component");
		list($year,$month,$day) = JEVHelper::getYMD();

		// Joomla unhelpfully switched limitstart to start when sef is enabled!  includes/router.php line 390
		$limitstart = intval( JRequest::getVar( 	'start', 	 JRequest::getVar( 	'limitstart', 	0 ) ) );
		
		$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
		$limit = $params->get("com_calEventListRowsPpg",15);
		// Crawler should list at least 100 events for efficiency
		$limit = intval($limit)<100 ? 100 : $limit;

		$Itemid	= JEVHelper::getItemid();

		// get the view

		$document = JFactory::getDocument();
		$viewType	= $document->getType();
		
		$cfg = JEVConfig::getInstance();
		$theme = "default";

		$view = "crawler";
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
		$this->view->assign("task",$this->_task);
		
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
	
	
}

