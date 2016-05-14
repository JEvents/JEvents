<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: helper.php 2347 2011-07-26 08:31:58Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');


jimport("joomla.application.module.helper");

class JevModuleHelper extends JModuleHelper
{

	static public function getVisibleModules()
	{
		return self::_load();

	}

}