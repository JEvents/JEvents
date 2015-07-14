<?php
/**
 *
 * @copyright   Copyright (C) 2015 - 2015 GWE Systems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Utility class for Bootstrap Modal Popups especially URL based Modals which bootstrap usually fails on
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
	 * Method to load the Bootstrap JavaScript framework into the document head
	 *
	 * If debugging mode is on an uncompressed version of Bootstrap is included for easier debugging.
	 *
	 * @param   mixed  $debug  Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($debug = null)
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		// Load jQuery
		JHtml::_('jquery.framework');
		
		JHtml::stylesheet('com_jevents/lib_jevmodal/jevmodal.css',array(),true);
		JHtml::script('com_jevents/lib_jevmodal/jevmodal.js',false,true,false,false,true);
	
		static::$loaded[__METHOD__] = true;

		return;
	}
	
	/**
	 * Add javascript support for Bootstrap modal
	 *
	 * @param   string  $selector  The selector for the modal element.
	 * @param   array   $params    An array of options for the modal element.
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
			// NEEDS TO BE DIFFERENT FOR EACH SELECTOR TO SUPPORT MULTIPLE INSTANCES ON ONE PAGE!
			// so we also need different javascript variable names
			$jsname = "jevmodal".md5($selector);

			// Include Modal framework
			static::framework();

			// Setup options object
			$opt['size']	= isset($params['size']) ? $params['size'] : 'max';

			// Set static array
			static::$loaded[__METHOD__][$selector] = true;
		}

		return;
	}
}
