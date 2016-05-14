<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: modlatest.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class ModLatestController extends JControllerLegacy   {


	function __construct($config = array())
	{
		if (!isset($config['base_path'])){
			$config['base_path']=JEV_PATH;
		}
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'calendar' );

		$cfg = JEVConfig::getInstance();
		$theme = ucfirst(JEV_CommonFunctions::getJEventsViewName());
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/".$theme."/abstract/abstract.php");

		include_once(JEV_LIBS."/modfunctions.php");
		if (!isset($this->_basePath)){
			$this->_basePath = $this->basePath;
			$this->_task = $this->task;
		}
	}

	function rss() {
		$jinput = JFactory::getApplication()->input;

		$jinput->setVar("tmpl", "component");

		// get the view
		$this->view = $this->getView("modlatest","feed");

		// Set the layout
		$this->view->setLayout('rss');
	
		// View caching logic -- simple... are we logged in?
		$cfg	 = JEVConfig::getInstance();
		$joomlaconf = JFactory::getConfig();
		$useCache = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);
		$user = JFactory::getUser();
		// Stupid Joomla 3.1 problem where its not possible to use the view cache on RSS feed output!
		if (JevJoomlaVersion::isCompatible("3.1") || $user->get('id') || !$useCache) {
			$this->view->rss();
		} else {
			$cache = JFactory::getCache(JEV_COM_COMPONENT, 'view');
			$cache->get($this->view, 'rss');
		}
	}
	

}

