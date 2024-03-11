<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: helper.php 2347 2011-07-26 08:31:58Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\ModuleHelper;

class JevModuleHelper extends ModuleHelper
{

	static public function getVisibleModules()
	{
        if (PHP_SAPI === "cli")
        {
            return array();
        }
		return self::load();

	}

}
