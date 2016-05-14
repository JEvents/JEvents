<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: search.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class SearchController extends JControllerLegacy   {

	function __construct($config = array())
	{
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'search' );
		//		$this->registerTask( 'show',  'showContent' );

		// Load abstract "view" class
		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/$theme/abstract/abstract.php");
		if (!isset($this->_basePath)){
			$this->_basePath = $this->basePath;
			$this->_task = $this->task;
		}
	}

	function search() {

		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		$jinput = JFactory::getApplication()->input;

		$document = JFactory::getDocument();
		$viewType	= $document->getType();

		$db	= JFactory::getDBO();
		$keyword = $jinput->getString('keyword', '');
		// limit searchword to a maximum of characters
		$upper_limit = 20;
		if (JString::strlen($keyword) > $upper_limit) {
			$keyword	= JString::substr($keyword, 0, $upper_limit - 1);
		}
		$keyword = $db->escape($jinput->getString('keyword', ''));

		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "search";
		$this->addViewPath($this->_basePath.'/'."views".'/'.$theme);
		$this->view = $this->getView($view,$viewType, $theme."View",
		array( 'base_path'=>$this->_basePath,
		"template_path"=>$this->_basePath.'/'."views".'/'.$theme.'/'.$view.'/'.'tmpl',
		"name"=>$theme.'/'.$view));

		// Set the layout
		$this->view->setLayout('form');

		$this->view->assign("Itemid",$Itemid);
		$this->view->assign("month",$month);
		$this->view->assign("day",$day);
		$this->view->assign("year",$year);
		$this->view->assign("task",$this->_task);
		$this->view->assign("task",$this->_task);

		$this->view->assign("keyword",$keyword);

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

	function results(){

		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		$db	= JFactory::getDBO();
		$keyword = JRequest::getString( 'keyword', '' );
		// limit searchword to a maximum of characters
		$upper_limit = 20;
		if (JString::strlen($keyword) > $upper_limit) {
			$keyword	= JString::substr($keyword, 0, $upper_limit - 1);
		}

		// Joomla unhelpfully switched limitstart to start when sef is enabled!  includes/router.php line 390
		$limitstart = intval( JRequest::getVar( 	'start', 	 JRequest::getVar( 	'limitstart', 	0 ) ) );
		
		$params = JComponentHelper::getParams( JEV_COM_COMPONENT );
		$limit = intval(JFactory::getApplication()->getUserStateFromRequest( 'jevlistlimit','limit', $params->get("com_calEventListRowsPpg",15)));

		$document = JFactory::getDocument();
		$viewType	= $document->getType();

		$cfg = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "search";
		$this->addViewPath($this->_basePath.'/'."views".'/'.$theme);
		$this->view = $this->getView($view,$viewType, $theme."View",
		array( 'base_path'=>$this->_basePath,
		"template_path"=>$this->_basePath.'/'."views".'/'.$theme.'/'.$view.'/'.'tmpl',
		"name"=>$theme.'/'.$view));

		// Set the layout
		$this->view->setLayout('results');

		$this->view->assign("Itemid",$Itemid);
		$this->view->assign("month",$month);
		$this->view->assign("day",$day);
		$this->view->assign("year",$year);
		$this->view->assign("task",$this->_task);

		$this->view->assign("keyword",$keyword);
		$this->view->assign("limit",$limit);
		$this->view->assign("limitstart",$limitstart);

		// View caching logic -- simple... are we logged in?
		$cfg	 = JEVConfig::getInstance();
		$joomlaconf = JFactory::getConfig();
		$useCache = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);
		$user = JFactory::getUser();
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache = JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$uri = JURI::getInstance();
			$url = $uri->toString();
			$cache->get($this->view, 'display', base64_encode($keyword.$Itemid.$limit.$limitstart.$month.$day.$year.$url));
		}
	}


}

