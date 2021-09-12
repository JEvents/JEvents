<?php
/**
 *
 * @copyright   Copyright (C) 2015 - JEVENTS_COPYRIGHT GWE Systems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Utility class for Bootstrap or UIKit Modal Popups especially URL based Modals which bootstrap usually fails on
 *
 */
class JevModal
{
	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static $loaded = array();

	/**
	 * Add javascript support for Bootstrap modal
	 *
	 * @param   string $selector   The selector for the modal element.
	 * @param   array  $params     An array of options for the modal element.
	 *                             Options for the tooltip can be:
	 *                             - size     string,  Values can be "max" or "h,w" for height and width values
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function modal($selector = '.jevmodal', $params = array())
	{

		if (!isset(static::$loaded[__METHOD__][$selector]))
		{

			// Include Modal framework
			static::framework();

			$jsonParams = json_encode($params);

			$script = <<< SCRIPT
document.addEventListener('DOMContentLoaded', function() {
	var targets = document.querySelectorAll('$selector');
	targets.forEach(function(target) {
		target.addEventListener('click', function(evt){
			jevModalSelector(target, $jsonParams, evt);
			return false;
		}, target);
	});
});
SCRIPT;
			Factory::getDocument()->addScriptDeclaration($script);

			// Set static array
			static::$loaded[__METHOD__][$selector] = true;
		}

		return;
	}


	/**
	 * Add javascript support for Bootstrap modal
	 *
	 * @param   string $selector   The selector for the modal element.
	 * @param   array  $params     An array of options for the modal element.
	 *                             Options for the tooltip can be:
	 *                             - size     string,  Values can be "max" or "h,w" for height and width values
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function bootstrapModal($selector = '.jevmodal', $params = array())
	{

		if (!isset(static::$loaded[__METHOD__][$selector]))
		{
			// NEEDS TO BE DIFFERENT FOR EACH SELECTOR TO SUPPORT MULTIPLE INSTANCES ON ONE PAGE!
			// so we also need different javascript variable names
			$jsname = "jevmodal" . md5($selector);

			// Include Modal framework
			static::framework(null, true);

			// Setup options object
			$opt['size'] = isset($params['size']) ? $params['size'] : 'max';

			$jsonParams = json_encode($params);

			$script = <<< SCRIPT
document.addEventListener('DOMContentLoaded', function() {
	var targets = document.querySelectorAll('$selector');
	targets.forEach(function(target) {
		target.addEventListener('click', function(evt){
			jevModalSelector(target, $jsonParams, evt);
			return false;
		}, target);
	});
});
SCRIPT;
			Factory::getDocument()->addScriptDeclaration($script);

			// Set static array
			static::$loaded[__METHOD__][$selector] = true;
		}

		return;
	}

	/**
	 * Method to load the Bootstrap JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   mixed $debug Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($debug = null, $forceBoostrap = false, $forceUIkit = false)
	{

		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		$jevparams = ComponentHelper::getParams('com_jevents');

		// UIKit or Bootstrap
		$jinput = JFactory::getApplication()->input;
		$task = $jinput->getString("task", $jinput->getString("jevtask", ""));
		if (!$forceBoostrap && ($task == "icalevent.edit" || $task == "icalrepeat.edit")
			&& (Factory::getApplication()->isClient('administrator') || $jevparams->get("newfrontendediting", 1))
		)
		{
			HTMLHelper::script('com_jevents/lib_jevmodal/jevmodal_uikit.js', array('framework' => false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
		}
		else if ($forceUIkit)
        {
            HTMLHelper::script('com_jevents/lib_jevmodal/jevmodal_uikit.js', array('framework' => false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
        }
		else
		{
			// Load jQuery
			HTMLHelper::_('jquery.framework');

			HTMLHelper::stylesheet('com_jevents/lib_jevmodal/jevmodal.css', array(), true);
			HTMLHelper::script('com_jevents/lib_jevmodal/jevmodal.js', array('framework' => false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
		}
		static::$loaded[__METHOD__] = true;

		return;
	}
}
