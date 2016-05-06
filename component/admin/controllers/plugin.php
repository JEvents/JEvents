<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: cpanel.php 3546 2012-04-20 09:08:44Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.controlleradmin');

class AdminPluginController extends JControllerAdmin
{

	/**
	 * Controler for the Control Panel
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerDefaultTask("plugin");
	}

	function plugin()
	{
		$dispatcher = JEventDispatcher::getInstance();
		// just incase we don't have jevents plugins registered yet
		JPluginHelper::importPlugin("jevents");
		$action = JFactory::getApplication()->input->get("task", "", "cmd");
		$parts = explode(".", $action);
		if (count($parts)==3){
			list($controller, $plugin, $task) = $parts;
			$res = $dispatcher->trigger('onJEventsPluginController', array($plugin, $task));
		}

	}
	
}
