<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: admin.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('JPATH_BASE') or die('No Direct Access.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.application.component.controller');

class AdminController extends Joomla\CMS\MVC\Controller\BaseController
{

	function __construct($config = array())
	{

		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask('listevents');
		//		$this->registerTask( 'show',  'showContent' );

		// Load abstract "view" class
		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();
		JLoader::register('JEvents' . ucfirst($theme) . 'View', JEV_VIEWS . "/$theme/abstract/abstract.php");
		$this->_basePath = $this->basePath;
		$this->_task     = $this->task;
	}

	function listevents()
	{

		$input = Factory::getApplication()->input;

		$is_event_editor = JEVHelper::isEventCreator();

		$Itemid = JEVHelper::getItemid();

		$user = Factory::getUser();
		if (!$is_event_editor)
		{
			$returnlink = Route::_('index.php?option=' . JEV_COM_COMPONENT . '&task=day.listevents&Itemid=' . $Itemid, false);
			$this->setRedirect($returnlink, html_entity_decode(Text::_('JEV_NOPERMISSION')));
			$this->redirect();

			return;
		}

		list($year, $month, $day) = JEVHelper::getYMD();

		// Joomla unhelpfully switched limitstart to start when sef is enabled!  includes/router.php line 390
		$limitstart = intval($input->getInt('start', $input->getInt('limitstart', 0)));

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$limit  = intval(Factory::getApplication()->getUserStateFromRequest('jevlistlimit.admin', 'limit', $params->get("com_calEventListRowsPpg", 15)));

		$Itemid = JEVHelper::getItemid();

		$task = $this->_task;

		$params    = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$adminuser = $params->get("jevadmin", -1);

		if (JEVHelper::isAdminUser($user) || JEVHelper::isEventPublisher(true) || JEVHelper::isEventEditor() || $user->id == $adminuser)
		{
			$creator_id = 'ADMIN';
		}
		else
		{
			$creator_id = $user->id;
		}

		// get the view

		$document = Factory::getDocument();
		$viewType = $document->getType();

		$cfg   = JEVConfig::getInstance();
		$theme = JEV_CommonFunctions::getJEventsViewName();

		$view = "admin";
		$this->addViewPath($this->_basePath . '/' . "views" . '/' . $theme);
		$this->view = $this->getView($view, $viewType, $theme . "View",
			array('base_path'     => $this->_basePath,
			      "template_path" => $this->_basePath . '/' . "views" . '/' . $theme . '/' . $view . '/' . 'tmpl',
			      "name"          => $theme . '/' . $view));

		// Set the layout
		$this->view->setLayout('listevents');

		$this->view->Itemid     = $Itemid;
		$this->view->limitstart = $limitstart;
		$this->view->limit      = $limit;
		$this->view->month      = $month;
		$this->view->day        = $day;
		$this->view->year       = $year;
		$this->view->task       = $task;
		$this->view->creator_id = $creator_id;

		$this->view->display();

	}

}
