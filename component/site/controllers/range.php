<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: range.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.application.component.controller');

#[\AllowDynamicProperties]
class RangeController extends Joomla\CMS\MVC\Controller\BaseController
{

	function __construct($config = array())
	{

		parent::__construct($config);

		// TODO get this from config
		$this->registerDefaultTask('listevents');

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

	function listevents()
	{

		$app    = Factory::getApplication();

		list($year, $month, $day) = JEVHelper::getYMD();

		$input = $app->input;

		$document = Factory::getDocument();
		$viewType = $document->getType();

		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "range";
		$this->addViewPath($this->_basePath . '/' . "views" . '/' . $theme);
		$this->view = $this->getView($view, $viewType, $theme . "View",
			array('base_path'     => $this->_basePath,
			      "template_path" => $this->_basePath . '/' . "views" . '/' . $theme . '/' . $view . '/' . 'tmpl',
			      "name"          => $theme . '/' . $view));

		// Joomla unhelpfully switched limitstart to start when sef is enabled!  includes/router.php line 390
		$limitstart = intval($input->getInt('start', $input->getInt('limitstart', 0)));

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$limit  = intval($app->getUserStateFromRequest('jevlistlimit.range', 'limit', $params->get("com_calEventListRowsPpg", 15)));

		$Itemid = JEVHelper::getItemid();

		// Set the layout
		$this->view->setLayout('listevents');

		$this->view->Itemid         = $Itemid;
		$this->view->limitstart     = $limitstart;
		$this->view->limit          = $limit;
		$this->view->month          = $month;
		$this->view->day            = $day;
		$this->view->year           = $year;
		$this->view->task           = $this->_task;

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


}

