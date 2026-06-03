<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\Helpers\Bootstrap;

/**
 * Utility class for Bootstrap/UIKit elements used by JEvents.
 * Legacy class name: JevHtmlBootstrap
 */
#[\AllowDynamicProperties]
class HtmlBootstrap
{
	protected static array $loaded = [];

	public static function framework(mixed $debug = null): void
	{
		HTMLHelper::_('bootstrap.framework', $debug);
	}

	public static function modal(string $selector = 'modal', array $params = []): void
	{
		\JevModal::modal($selector, $params);
	}

	public static function popover(string $selector = '.hasPopover', array $params = []): void
	{
		\JevModal::popover($selector, $params);
	}

	public static function tooltip(string $selector = '.hasTooltip', array $params = []): void
	{
		if (isset(static::$loaded[__METHOD__][$selector]))
		{
			return;
		}

		\JevModal::tooltip($selector, $params);
		static::$loaded[__METHOD__][$selector] = true;
	}

	public static function loadCss(bool $includeMainCss = true, string $direction = 'ltr', array $attribs = []): void
	{
		$params = ComponentHelper::getParams('com_jevents');

		if (strpos($params->get('framework', 'native'), 'uikit') !== false)
		{
			return;
		}

		if ($includeMainCss)
		{
			switch ($params->get('bootstrapcss', 1))
			{
				case 1:
					HTMLHelper::_('stylesheet', 'com_jevents/bootstrap.css', $attribs, true);
					HTMLHelper::_('stylesheet', 'com_jevents/bootstrap-responsive.css', $attribs, true);
					break;
				case 2:
					Bootstrap::loadCss();
					break;
			}
		}

		if ($direction === 'rtl' && $params->get('bootstrapcss', 1) > 0)
		{
			HTMLHelper::_('stylesheet', 'jui/bootstrap-rtl.css', $attribs, true);
		}
	}
}
