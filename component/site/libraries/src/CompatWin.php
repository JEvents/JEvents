<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('_JEXEC') or die('Restricted access');

use JEvents\Site\Registry;

/**
 * Compatibility functions for Windows strftime() specifiers unsupported on Windows.
 * Legacy class name: JEV_CompatWin
 */
class CompatWin
{
	public static function win_strftime(string $format = '', ?int $timestamp = null): string
	{
		if (!$timestamp)
		{
			$timestamp = time();
		}

		$registry = Registry::getInstance('jevents');
		$registry->set('jevents.strftime', $timestamp);

		$patterns = ['/%C/', '/%D/', '/%e/', '/%g/', '/%G/',
			'/%h/', '/%n/', '/%r/', '/%R/', '/%t/',
			'/%T/', '/%u/', '/%V/'];

		$format = preg_replace_callback($patterns, [static::class, '_cb_strftime'], $format);

		return \JevDate::strftime($format, $timestamp);
	}

	public static function _cb_strftime(array $pattern): string
	{
		$registry = Registry::getInstance('jevents');
		$ts       = $registry->get('jevents.strftime', time());

		return match ($pattern[0]) {
			'%C'    => sprintf("%02d", date("Y", $ts) / 100),
			'%D'    => '%m/%d/%y',
			'%e'    => sprintf("%' 2d", date("j", $ts)),
			'%g'    => \JevDate::strftime('%y', static::_getThursdayOfWeek($ts)),
			'%G'    => \JevDate::strftime('%Y', static::_getThursdayOfWeek($ts)),
			'%h'    => '%b',
			'%n'    => "\n",
			'%r'    => '%I:%M:%S %p',
			'%R'    => '%H:%M',
			'%t'    => "\t",
			'%T'    => '%H:%M:%S',
			'%u'    => (string) (($w = date("w", $ts)) ? $w : 7),
			'%V'    => (string) static::_getWeekNumberISO8601($ts),
			default => ' unknown specifier! ',
		};
	}

	public static function _getThursdayOfWeek(int $date): int
	{
		$dayofweek = \JevDate::strftime('%w', $date);

		if ($dayofweek == 0)
		{
			$dayofweek = 7;
		}

		if ($dayofweek < 4)
		{
			return \JevDate::strtotime('next thursday', $date);
		}

		if ($dayofweek > 4)
		{
			return \JevDate::strtotime('last thursday', $date);
		}

		return $date;
	}

	public static function _getWeekNumberISO8601(int $date): int
	{
		$thursday   = static::_getThursdayOfWeek($date);
		$thursday_Y = \JevDate::strftime('%Y', $thursday);
		$first_th   = static::_getThursdayOfWeek(\JevDate::strtotime($thursday_Y . '-01-04'));

		return (int) ((\JevDate::strftime('%j', $thursday) - \JevDate::strftime('%j', $first_th)) / 7 + 1);
	}
}