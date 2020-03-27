<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevent.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Factory;

jimport('joomla.application.component.controller');

class JEventController extends Joomla\CMS\MVC\Controller\BaseController
{

	function __construct($config = array())
	{

		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask('detail');
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

	function detail()
	{

		$input = Factory::getApplication()->input;

		$evid = $input->getInt("evid", 0);
		$pop  = $input->getInt('pop', 0);
		list($year, $month, $day) = JEVHelper::getYMD();
		$Itemid = JEVHelper::getItemid();

		// get the view

		$document = Factory::getDocument();
		$viewType = $document->getType();

		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "jevent";
		$this->addViewPath($this->_basePath . '/' . "views" . '/' . $theme);
		$this->view = $this->getView($view, $viewType, $theme . "View",
			array('base_path'     => $this->_basePath,
			      "template_path" => $this->_basePath . '/' . "views" . '/' . $theme . '/' . $view . '/' . 'tmpl',
			      "name"          => $theme . '/' . $view));

		// Set the layout
		$this->view->setLayout("detail");

		$this->view->Itemid     = $Itemid;
		$this->view->month      = $month;
		$this->view->day        = $day;
		$this->view->year       = $year;
		$this->view->task       = $this->_task;
		$this->view->pop        = $pop;
		$this->view->evid       = $evid;
		$this->view->jevtype    = "jevent";

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

