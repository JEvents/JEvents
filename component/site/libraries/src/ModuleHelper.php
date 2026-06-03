<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\ModuleHelper as JoomlaModuleHelper;

/**
 * JEvents wrapper for Joomla's ModuleHelper, adding CLI safety.
 * Legacy class name: JevModuleHelper
 */
class ModuleHelper extends JoomlaModuleHelper
{
	public static function getVisibleModules(): array
	{
		if (PHP_SAPI === 'cli')
		{
			return [];
		}

		return self::load();
	}
}
