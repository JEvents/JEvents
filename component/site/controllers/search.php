<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: search.php 1763 2010-05-18 10:04:45Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class SearchController extends JController   {

	function __construct($config = array())
	{
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'search' );
		//		$this->registerTask( 'show',  'showContent' );

		// Load abstract "view" class
		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/$theme/abstract/abstract.php");
	}

	function search() {

		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		$document =& JFactory::getDocument();
		$viewType	= $document->getType();

		$db	= & JFactory::getDBO();
		$keyword = $db->getEscaped(JRequest::getVar( 'keyword', '' ));

		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "search";
		$this->addViewPath($this->_basePath.DS."views".DS.$theme);
		$this->view = & $this->getView($view,$viewType, $theme,
		array( 'base_path'=>$this->_basePath,
		"template_path"=>$this->_basePath.DS."views".DS.$theme.DS.$view.DS.'tmpl',
		"name"=>$theme.DS.$view));

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
		$cfg	 = & JEVConfig::getInstance();
		$useCache = intval($cfg->get('com_cache', 0));
		$user = &JFactory::getUser();
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache =& JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'display');
		}
	}

	function results(){

		list($year,$month,$day) = JEVHelper::getYMD();
		$Itemid	= JEVHelper::getItemid();

		$db	= & JFactory::getDBO();
		$keyword = $db->getEscaped(JRequest::getVar( 'keyword', '' ));

		// Joomla unhelpfully switched limitstart to start when sef is enabled!  includes/router.php line 390
		$limitstart = intval( JRequest::getVar( 	'start', 	 JRequest::getVar( 	'limitstart', 	0 ) ) );
		global $mainframe;
		$params =& JComponentHelper::getParams( JEV_COM_COMPONENT );
		$limit = intval($mainframe->getUserStateFromRequest( 'jevlistlimit','limit', $params->getValue("com_calEventListRowsPpg",15)));

		$document =& JFactory::getDocument();
		$viewType	= $document->getType();

		$cfg = & JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "search";
		$this->addViewPath($this->_basePath.DS."views".DS.$theme);
		$this->view = & $this->getView($view,$viewType, $theme,
		array( 'base_path'=>$this->_basePath,
		"template_path"=>$this->_basePath.DS."views".DS.$theme.DS.$view.DS.'tmpl',
		"name"=>$theme.DS.$view));

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
		$cfg	 = & JEVConfig::getInstance();
		$useCache = intval($cfg->get('com_cache', 0));
		$user = &JFactory::getUser();
		if ($user->get('id') || !$useCache) {
			$this->view->display();
		} else {
			$cache =& JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$uri = JURI::getInstance();
			$url = $uri->toString();
			$cache->get($this->view, 'display', base64_encode($keyword.$Itemid.$limit.$limitstart.$month.$day.$year.$url));
		}
	}


}

