<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;

/**
 * Convenience wrapper for component params.
 * Legacy class name: JEVConfig
 */
class Config
{
	public static function &getInstance(string $inifile = '')
	{
		$params = ComponentHelper::getParams('com_jevents');

		return $params;
	}
}
