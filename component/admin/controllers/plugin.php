<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: cpanel.php 3546 2012-04-20 09:08:44Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Layout\LayoutHelper;

jimport('joomla.application.component.controlleradmin');

class AdminPluginController extends AdminController
{

	/**
	 * Controler for the Control Panel
	 *
	 * @param array        configuration
	 */
	function __construct($config = array())
	{

		parent::__construct($config);
		$this->registerDefaultTask("plugin");
	}

	function plugin()
	{

		// Just in case we don't have jevents plugins registered yet
		PluginHelper::importPlugin("jevents");
		$app    = Factory::getApplication();
		$action = $app->input->get("task", "", "cmd");
		$parts  = explode(".", $action);
		if (count($parts) == 3)
		{
			list($controller, $plugin, $task) = $parts;

			ob_start();
			$res = $app->triggerEvent('onJEventsPluginController', array($plugin, $task));
			$output = ob_get_clean();

			if (!GSLMSIE10)
			{
				echo LayoutHelper::render('gslframework.header');
				echo $output;
				echo LayoutHelper::render('gslframework.footer');
			}
			else
			{
				echo $output;
			}
		}
	}
}
