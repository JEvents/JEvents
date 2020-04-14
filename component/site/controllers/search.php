<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: search.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.application.component.controller');

class SearchController extends Joomla\CMS\MVC\Controller\BaseController
{

	function __construct($config = array())
	{

		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask('search');
		//		$this->registerTask( 'show',  'showContent' );

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

	function search()
	{

		list($year, $month, $day) = JEVHelper::getYMD();
		$Itemid = JEVHelper::getItemid();

		$input = Factory::getApplication()->input;

		$document = Factory::getDocument();
		$viewType = $document->getType();

		$db      = Factory::getDbo();
		$keyword = $input->getString('keyword', '');
		// limit searchword to a maximum of characters
		$upper_limit = 20;
		if (StringHelper::strlen($keyword) > $upper_limit)
		{
			$keyword = StringHelper::substr($keyword, 0, $upper_limit - 1);
		}
		$keyword = $db->escape($keyword);

		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "search";
		$this->addViewPath($this->_basePath . '/' . "views" . '/' . $theme);
		$this->view = $this->getView($view, $viewType, $theme . "View",
			array('base_path'     => $this->_basePath,
			      "template_path" => $this->_basePath . '/' . "views" . '/' . $theme . '/' . $view . '/' . 'tmpl',
			      "name"          => $theme . '/' . $view));

		// Set the layout
		$this->view->setLayout('form');

		$this->view->Itemid     = $Itemid;
		$this->view->month      = $month;
		$this->view->day        = $day;
		$this->view->year       = $year;
		$this->view->task       = $this->_task;

		$this->view->keyword    = $keyword;

		// View caching logic -- simple... are we logged in?
		$cfg        = JEVConfig::getInstance();
		$joomlaconf = Factory::getConfig();
		$useCache   = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);
		$user       = Factory::getUser();
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

	function results()
	{

		$app    = Factory::getApplication();
		$input  = $app->input;

		list($year, $month, $day) = JEVHelper::getYMD();
		$Itemid = JEVHelper::getItemid();

		$db      = Factory::getDbo();
		$keyword = $input->getString('keyword', '');

		// Limit search word to a maximum of characters
		$upper_limit = 20;
		if (StringHelper::strlen($keyword) > $upper_limit)
		{
			$keyword = StringHelper::substr($keyword, 0, $upper_limit - 1);
		}

		// Joomla unhelpfully switched limit start to start when sef is enabled!  includes/router.php line 390
		$limitstart = intval($input->getInt('start', $input->getInt('limitstart', 0)));

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$limit  = intval($app->getUserStateFromRequest('jevlistlimit.search', 'limit', $params->get("com_calEventListRowsPpg", 15)));

		$document = Factory::getDocument();
		$viewType = $document->getType();

		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "search";
		$this->addViewPath($this->_basePath . '/' . "views" . '/' . $theme);
		$this->view = $this->getView($view, $viewType, $theme . "View",
			array('base_path'     => $this->_basePath,
			      "template_path" => $this->_basePath . '/' . "views" . '/' . $theme . '/' . $view . '/' . 'tmpl',
			      "name"          => $theme . '/' . $view));

		// Set the layout
		$this->view->setLayout('results');

		$this->view->Itemid     = $Itemid;
		$this->view->month      = $month;
		$this->view->day        = $day;
		$this->view->year       = $year;
		$this->view->task       = $this->_task;

		$this->view->keyword    = $keyword;
		$this->view->limit      = $limit;
		$this->view->limitstart = $limitstart;

		// View caching logic -- simple... are we logged in?
		$cfg        = JEVConfig::getInstance();
		$joomlaconf = Factory::getConfig();
		$useCache   = intval($cfg->get('com_cache', 0)) && $joomlaconf->get('caching', 1);
		$user       = Factory::getUser();
		if ($user->get('id') || !$useCache)
		{
			$this->view->display();
		}
		else
		{
			$cache = Factory::getCache(JEV_COM_COMPONENT, 'view');
			$uri   = Uri::getInstance();
			$url   = $uri->toString();
			$cache->get($this->view, 'display', base64_encode($keyword . $Itemid . $limit . $limitstart . $month . $day . $year . $url));
		}
	}


}

