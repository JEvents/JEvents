<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: compatwin.php 1784 2011-03-14 14:28:13Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

/**
 * Compatibility functions for Windows 
 * 
 */
class JEV_CompatWin {

	/**
	 * Add unsupported parameters for Windows to strftime()
	 *
	 */
	public static function  win_strftime($format='', $timestamp=null) {

		if (!$timestamp) $timestamp = time();

		$registry = JRegistry::getInstance('jevents');
		$registry->set('jevents.strftime', $timestamp);

		$patterns = array('/%C/', '/%D/', '/%e/', '/%g/', '/%G/',
						  '/%h/', '/%n/', '/%r/', '/%R/', '/%t/',
						  '/%T/', '/%u/', '/%V/' );

		//replace unsupported format string specifiers
		$format = preg_replace_callback($patterns,
			array('JEV_CompatWin', '_cb_strftime'),
			$format);

		return JevDate::strftime($format, $timestamp);

	}

	/**
	 * Callback function
	 *
	 * @static
	 * @param mixed $pattern	array() search pattern or int date
	 */
	public static  function  _cb_strftime($pattern) {

		// timestamp used during callback
		$registry = JRegistry::getInstance('jevents');
		$ts = $registry->get('jevents.strftime', time());

		switch ($pattern[0]) {
			case '%C': return sprintf("%02d", date("Y", $ts) / 100); break;
			case '%D': return '%m/%d/%y'; break;
			case '%e': return sprintf("%' 2d", date("j", $ts)); break;
			case '%g': return JevDate::strftime('%y', JEV_CompatWin::_getThursdayOfWeek($ts)); break;
			case '%G': return JevDate::strftime('%Y', JEV_CompatWin::_getThursdayOfWeek($ts)); break;
			case '%h': return '%b'; break;
			case '%n': return "\n"; break;
			case '%r': return '%I:%M:%S %p'; break;
			case '%R': return '%H:%M'; break;
			case '%t': return "\t"; break;
			case '%T': return '%H:%M:%S'; break;
			case '%u': return ($w = date("w", $ts)) ? $w : 7; break;
			case '%V': return JEV_CompatWin::_getWeekNumberISO8601($ts); break;
			default:   return ' unknown specifier! ';
		}
	}

	/**
	 * Calculate thursday in the same week of date
	 *
	 * @static
	 * @param int $date date
	 * @return int date
	 */
	public static function _getThursdayOfWeek($date) {

		$dayofweek = JevDate::strftime('%w', $date);
		if ($dayofweek == 0) $dayofweek =7;
		if ($dayofweek < 4) {
			return JevDate::strtotime('next thursday', $date);
		} elseif ($dayofweek > 4) {
			return JevDate::strtotime('last thursday', $date);
		} else {
			return $date;
		}
	}

	/**
	 * Get week number according ISO 8601
	 *
	 * @static
	 * @param int $date date
	 * @return int weeknumber
	 */
	public static function _getWeekNumberISO8601($date) {

		$thursday	= JEV_CompatWin::_getThursdayOfWeek($date);
		$thursday_Y	= JevDate::strftime('%Y', $thursday);
		$first_th	= JEV_CompatWin::_getThursdayOfWeek(JevDate::strtotime($thursday_Y.'-01-04'));
		return ((JevDate::strftime('%j', $thursday) - JevDate::strftime('%j', $first_th)) / 7 + 1);

	}
}
