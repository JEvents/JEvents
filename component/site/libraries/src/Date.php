<?php
/**
 * @package     JEvents
 * @copyright   Copyright (C) GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace JEvents;

defined('JPATH_BASE') or die;

use Joomla\CMS\Date\Date as JoomlaDate;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseDriver;

/**
 * JEvents timezone-aware date class.
 * Targets Joomla 5.4+ (J5 removed $gmt/$stz from the parent Date class).
 * Legacy class name: JevDate
 */
#[\AllowDynamicProperties]
class Date extends JoomlaDate
{
	public $mytz;

	protected static $gmt;
	protected static $stz;

	public function __construct(string $date = 'now', mixed $tz = null)
	{
		if (empty(self::$gmt))
		{
			self::$gmt = new \DateTimeZone('GMT');
		}

		$compparams = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$jtz        = $compparams->get('icaltimezonelive', '');

		self::$stz  = $jtz !== '' ? new \DateTimeZone($jtz) : new \DateTimeZone(@date_default_timezone_get());
		$this->mytz = self::$stz;

		if (!($tz instanceof \DateTimeZone))
		{
			if ($tz === null)
			{
				$tz = self::$gmt;
			}
			elseif (is_numeric($tz))
			{
				static $offsets = [
					'-12' => 'Etc/GMT-12',    '-11' => 'Pacific/Midway',
					'-10' => 'Pacific/Honolulu', '-9.5' => 'Pacific/Marquesas',
					'-9'  => 'US/Alaska',       '-8'  => 'US/Pacific',
					'-7'  => 'US/Mountain',     '-6'  => 'US/Central',
					'-5'  => 'US/Eastern',      '-4.5' => 'America/Caracas',
					'-4'  => 'America/Barbados', '-3.5' => 'Canada/Newfoundland',
					'-3'  => 'America/Buenos_Aires', '-2' => 'Atlantic/South_Georgia',
					'-1'  => 'Atlantic/Azores', '0'   => 'Europe/London',
					'1'   => 'Europe/Amsterdam', '2'  => 'Europe/Istanbul',
					'3'   => 'Asia/Riyadh',     '3.5' => 'Asia/Tehran',
					'4'   => 'Asia/Muscat',     '4.5' => 'Asia/Kabul',
					'5'   => 'Asia/Karachi',    '5.5' => 'Asia/Calcutta',
					'5.75' => 'Asia/Katmandu',  '6'  => 'Asia/Dhaka',
					'6.5' => 'Indian/Cocos',    '7'  => 'Asia/Bangkok',
					'8'   => 'Australia/Perth',  '8.75' => 'Australia/West',
					'9'   => 'Asia/Tokyo',       '9.5' => 'Australia/Adelaide',
					'10'  => 'Australia/Brisbane', '10.5' => 'Australia/Lord_Howe',
					'11'  => 'Pacific/Kosrae',   '11.5' => 'Pacific/Norfolk',
					'12'  => 'Pacific/Auckland', '12.75' => 'Pacific/Chatham',
					'13'  => 'Pacific/Tongatapu', '14' => 'Pacific/Kiritimati',
				];
				$tz = new \DateTimeZone($offsets[(string) $tz]);
			}
			elseif (is_string($tz))
			{
				$tz = new \DateTimeZone($tz);
			}
		}

		date_default_timezone_set('UTC');
		$date = is_numeric($date) ? date('c', $date) : $date;

		if (strpos($date, ':') > 0 && strpos($date, '-') === false)
		{
			$date = str_replace(':', '', $date);
		}

		parent::__construct($date, $tz);

		date_default_timezone_set(self::$stz->getName());
		$this->_tz = $tz;
	}

	public function toFormat(string $format = '%Y-%m-%d %H:%M:%S', bool $local = false): string
	{
		$time = (int) parent::format('U', true);

		if ($local)
		{
			$time += $this->getOffsetFromGMT();
		}

		if (strpos($format, '%a') !== false)
		{
			$format = str_replace('%a', $this->dayToString(date('w', $time), true), $format);
		}

		if (strpos($format, '%A') !== false)
		{
			$format = str_replace('%A', $this->dayToString(date('w', $time)), $format);
		}

		if (strpos($format, '%b') !== false)
		{
			$format = str_replace('%b', $this->monthToString(date('n', $time), true), $format);
		}

		if (strpos($format, '%h') !== false)
		{
			$format = str_replace('%h', $this->monthToString(date('n', $time), true), $format);
		}

		if (strpos($format, '%B') !== false)
		{
			$format = str_replace('%B', $this->monthToString(date('n', $time)), $format);
		}

		$date = self::strftime($format, $time);

		date_default_timezone_set(self::$stz->getName());

		return $date;
	}

	public static function getDate(mixed $time = 'now', mixed $tzOffset = null): static
	{
		return new static($time, $tzOffset);
	}

	public function toMySQL(bool $local = false): string
	{
		return $this->toSql($local);
	}

	// Timezone-aware override of parent toSql
	public function toSql($local = false, ?DatabaseDriver $db = null): string
	{
		return $this->toFormat('%Y-%m-%d %H:%M:%S', $local);
	}

	public static function strtotime(string $time, ?int $now = null): int|false
	{
		static $date;

		if (!isset($date))
		{
			$date = new self();
		}

		date_default_timezone_set($date->mytz->getName());

		return $now !== null ? strtotime($time, $now) : strtotime($time);
	}

	public static function mktime(): int
	{
		static $date;

		if (!isset($date))
		{
			$date = new self();
		}

		date_default_timezone_set($date->mytz->getName());
		$args = func_get_args();
		$name = 'mktime';

		if (is_callable($name) && count($args) > 0)
		{
			try
			{
				return call_user_func_array($name, $args);
			}
			catch (\Throwable $e)
			{
				echo $e->getMessage() . '<br>';
				var_dump($args);
				exit(0);
			}
		}

		return call_user_func_array('time', $args);
	}

	public static function strftime(string $format, mixed $timestamp = 'time()', bool|string $tzid = false): string
	{
		static $date;

		if (!isset($date))
		{
			$date = new self();
		}

		$oldtz = date_default_timezone_get();
		date_default_timezone_set($tzid !== false ? $tzid : $date->mytz->getName());

		include_once JPATH_ADMINISTRATOR . '/components/com_jevents/libraries/strftime.php';

		try
		{
			$return = \PHP81_BC\strftime($format, $timestamp);
		}
		catch (\Throwable $e)
		{
			$return = \PHP81_BC\strftime($format, $timestamp);
		}

		date_default_timezone_set($oldtz);

		return $return;
	}

	public static function rawStrftime(string $format, int|false $timestamp = false): string
	{
		$timestamp = $timestamp !== false ? $timestamp : time();

		include_once JPATH_ADMINISTRATOR . '/components/com_jevents/libraries/strftime.php';

		try
		{
			$return = \PHP81_BC\strftime($format, $timestamp);
		}
		catch (\Throwable $e)
		{
			$return = \PHP81_BC\strftime($format, $timestamp);
		}

		return $return;
	}

	public function __call(string $name, array $arguments): mixed
	{
		static $date;

		if (!isset($date))
		{
			$date = new self();
		}

		date_default_timezone_set($date->mytz->getName());
		array_unshift($arguments, $this);

		if (is_callable($name))
		{
			return call_user_func_array($name, $arguments);
		}

		return null;
	}
}
