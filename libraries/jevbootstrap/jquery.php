<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Utility class for jQuery JavaScript behaviors
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.0
 */
class JevHtmlJquery
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();


	/**
	 * Method to load the jQuery JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of jQuery is included for easier debugging.
	 *
	 * @param   boolean $noConflict True to load jQuery in noConflict mode [optional]
	 * @param   mixed   $debug      Is debugging mode on? [optional]
	 * @param   boolean $migrate    True to enable the jQuery Migrate plugin
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($noConflict = true, $debug = null, $migrate = true)
	{

		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		// If no debugging value is set, use the configuration setting
		if ($debug === null)
		{
			$config = Factory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}

		//We try to load Joomla's version first (will make it faster if caching)

		if (!HTMLHelper::_('script', 'jui/jquery.min.js', false, true, true, false, $debug))
		{
			HTMLHelper::_('script', 'libraries/jevents/bootstrap/js/jquery.min.js', false, false, false, false, $debug);
		}
		else
		{
			HTMLHelper::_('script', 'jui/jquery.min.js', false, true, false, false, $debug);
		}

		// Check if we are loading in noConflict
		if ($noConflict)
		{
			if (!HTMLHelper::_('script', 'jui/jquery-noconflict.js', false, true, true, false, false))
			{
				HTMLHelper::_('script', 'libraries/jevents/bootstrap/js/jquery-noconflict.js', false, false, false, false, false);
			}
			else
			{
				HTMLHelper::_('script', 'jui/jquery-noconflict.js', false, true, false, false, false);
			}
		}

		// Check if we are loading Migrate
		if ($migrate)
		{
			if (!HTMLHelper::_('script', 'jui/jquery-migrate.min.js', false, true, true, false, $debug))
			{
				HTMLHelper::_('script', 'libraries/jevents/bootstrap/js/jquery-migrate.min.js', false, false, false, false, $debug);
			}
			else
			{
				HTMLHelper::_('script', 'jui/jquery-migrate.min.js', false, true, false, false, $debug);
			}
		}

		static::$loaded[__METHOD__] = true;

		return;
	}
}
