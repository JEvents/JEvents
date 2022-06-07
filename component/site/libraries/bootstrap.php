<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Utility class for Bootstrap elements.
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       3.0
 */
class JevHtmlBootstrap
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
	 * @param   mixed $debug Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function framework($debug = null)
	{
		HTMLHelper::_('bootstrap.framework', $debug);
		return;
	}

	/**
	 * Add javascript support for Bootstrap modals
	 *
	 * @param   string $selector   The ID selector for the modal.
	 * @param   array  $params     An array of options for the modal.
	 *                             Options for the modal can be:
	 *                             - backdrop  boolean  Includes a modal-backdrop element.
	 *                             - keyboard  boolean  Closes the modal when escape key is pressed.
	 *                             - show      boolean  Shows the modal when initialized.
	 *                             - remote    string   An optional remote URL to load
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function modal($selector = 'modal', $params = array())
	{
		JevModal::modal($selector, $params);
		return;

	}

	/**
	 * Add javascript support for Bootstrap popovers
	 *
	 * Use element's Title as popover content
	 *
	 * @param   string $selector                        Selector for the popover
	 * @param   array  $params                          An array of options for the popover.
	 *                                                  Options for the popover can be:
	 *                                                  animation  boolean          apply a css fade transition to the popover
	 *                                                  html       boolean          Insert HTML into the popover. If false, jQuery's text method will be used to insert
	 *                                                  content into the dom.
	 *                                                  placement  string|function  how to position the popover - top | bottom | left | right
	 *                                                  selector   string           If a selector is provided, popover objects will be delegated to the specified targets.
	 *                                                  trigger    string           how popover is triggered - hover | focus | manual
	 *                                                  title      string|function  default title value if `title` tag isn't present
	 *                                                  content    string|function  default content value if `data-content` attribute isn't present
	 *                                                  delay      number|object    delay showing and hiding the popover (ms) - does not apply to manual trigger type
	 *                                                  If a number is supplied, delay is applied to both hide/show
	 *                                                  Object structure is: delay: { show: 500, hide: 100 }
	 *                                                  container  string|boolean   Appends the popover to a specific element: { container: 'body' }
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function popover($selector = '.hasPopover', $params = array())
	{
		JevModal::popover($selector, $params);
		return;
	}

	/**
	 * Add javascript support for Bootstrap tooltips
	 *
	 * Add a title attribute to any element in the form
	 * title="title::text"
	 *
	 * @param   string $selector                                 The ID selector for the tooltip.
	 * @param   array  $params                                   An array of options for the tooltip.
	 *                                                           Options for the tooltip can be:
	 *                                                           - animation  boolean          Apply a CSS fade transition to the tooltip
	 *                                                           - html       boolean          Insert HTML into the tooltip. If false, jQuery's text method will be used to insert
	 *                                                           content into the dom.
	 *                                                           - placement  string|function  How to position the tooltip - top | bottom | left | right
	 *                                                           - selector   string           If a selector is provided, tooltip objects will be delegated to the specified targets.
	 *                                                           - title      string|function  Default title value if `title` tag isn't present
	 *                                                           - trigger    string           How tooltip is triggered - hover | focus | manual
	 *                                                           - delay      integer          Delay showing and hiding the tooltip (ms) - does not apply to manual trigger type
	 *                                                           If a number is supplied, delay is applied to both hide/show
	 *                                                           Object structure is: delay: { show: 500, hide: 100 }
	 *                                                           - container  string|boolean   Appends the popover to a specific element: { container: 'body' }
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function tooltip($selector = '.hasTooltip', $params = array())
	{
		// Only load once
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		JevModal::tooltip($selector, $params);
		static::$loaded[__METHOD__][$selector] = true;

		return;

	}

	/**
	 * Loads CSS files needed by Bootstrap
	 *
	 * @param   boolean $includeMainCss If true, main bootstrap.css files are loaded
	 * @param   string  $direction      rtl or ltr direction. If empty, ltr is assumed
	 * @param   array   $attribs        Optional array of attributes to be passed to HTMLHelper::_('stylesheet')
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function loadCss($includeMainCss = true, $direction = 'ltr', $attribs = array())
	{

		$params = ComponentHelper::getParams('com_jevents');#
		if (strpos($params->get('framework', 'native'),  "uikit") !== false)
		{
			return;
		}
		// Load Bootstrap main CSS
		if ($includeMainCss)
		{
			switch ($params->get("bootstrapcss", 1))
			{
				case 1:
					HTMLHelper::_('stylesheet', 'com_jevents/bootstrap.css', $attribs, true);
					HTMLHelper::_('stylesheet', 'com_jevents/bootstrap-responsive.css', $attribs, true);
					break;
				case 2:
					JHtmlBootstrap::loadCss();
					break;
			}
			//HTMLHelper::_('stylesheet', 'com_jevents/jevbootstrap/bootstrap-extended.css', $attribs, true);
		}

		// Load Bootstrap RTL CSS
		if ($direction === 'rtl' && $params->get("bootstrapcss", 1) > 0)
		{
			HTMLHelper::_('stylesheet', 'jui/bootstrap-rtl.css', $attribs, true);
		}
	}
}
