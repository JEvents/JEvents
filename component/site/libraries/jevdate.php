<?php

// No direct access
defined('JPATH_BASE') or die;

// Class to fix Joomla 1.6 date class bugs
jimport("joomla.utilities.date");

if (JVersion::isCompatible("1.6.0"))
{
	class JevDate extends JDate
	{

		/**
		 * Constructor.
		 *
		 * @param	string	String in a format accepted by strtotime(), defaults to "now".
		 * @param	mixed	Time zone to be used for the date.
		 * @return	void
		 * @since	1.5
		 *
		 * @throws	JException
		 */
		public function __construct($date = 'now', $tz = null)
		{
			// Create the base GMT and server time zone objects.
			if (empty(self::$gmt))
			{ //|| empty(self::$stz)) {
				self::$gmt = new DateTimeZone('GMT');
				//self::$stz = new DateTimeZone(@date_default_timezone_get());
			}
			// Must get this each time otherwise modules can't set their own timezone
			$compparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$tz=$compparams->get("icaltimezonelive","");
			echo "JEvDate Timezone is $tz<br/>";
			if ($tz!=""){
				self::$stz = new DateTimeZone($tz);
			}
			else {
				self::$stz = new DateTimeZone(@date_default_timezone_get());
			}

			// If the time zone object is not set, attempt to build it.
			if (!($tz instanceof DateTimeZone))
			{
				if ($tz === null)
				{
					$tz = self::$gmt;
				}
				elseif (is_numeric($tz))
				{
					// Translate from offset.
					$tz = new DateTimeZone(self::$offsets[(string) $tz]);
				}
				elseif (is_string($tz))
				{
					$tz = new DateTimeZone($tz);
				}
			}

			// If the date is numeric assume a unix timestamp and convert it.
			date_default_timezone_set('UTC');
			$date = is_numeric($date) ? date('c', $date) : $date;

			// Call the DateTime constructor.
			parent::__construct($date, $tz);

			// reset the timezone !!
			date_default_timezone_set(self::$stz->getName());

			// Set the timezone object for access later.
			$this->_tz = $tz;

		}

		/**
		 * Gets the date in a specific format
		 *
		 * Returns a string formatted according to the given format. Month and weekday names and
		 * other language dependent strings respect the current locale
		 *
		 * @deprecated	Deprecated since 1.6, use JDate::format() instead.
		 *
		 * @param	string	The date format specification string (see {@link PHP_MANUAL#strftime})
		 * @param	boolean	True to return the date string in the local time zone, false to return it in GMT.
		 * @return	string	The date as a formatted string.
		 * @since	1.5
		 */
		public function toFormat($format = '%Y-%m-%d %H:%M:%S', $local = false)
		{
			// Set time zone to GMT as strftime formats according locale setting.
			date_default_timezone_set('GMT');

			// Generate the timestamp.
			$time = (int) parent::format('U');

			// If the returned time should be local add the GMT offset.
			if ($local)
			{
				$time += $this->getOffsetFromGMT();
			}

			// Manually modify the month and day strings in the format.
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
			if (strpos($format, '%B') !== false)
			{
				$format = str_replace('%B', $this->monthToString(date('n', $time)), $format);
			}

			// Generate the formatted string.
			$date = strftime($format, $time);

			// reset the timezone !!
			date_default_timezone_set(self::$stz->getName());

			return $date;

		}

			/**
	 * Return the {@link JDate} object
	 *
	 * @param mixed $time     The initial time for the JDate object
	 * @param mixed $tzOffset The timezone offset.
	 *
	 * @return JDate object
	 * @since 1.5
	 */
	public static function getDate($time = 'now', $tzOffset = null)
	{
			jimport('joomla.utilities.date');
			static $instances;
			static $classname;
			static $mainLocale;

			if (!isset($instances))
			{
				$instances = array();
			}

			$classname = 'JevDate';
			$key = $time . '-' . $tzOffset;

			//		if (!isset($instances[$classname][$key])) {
			$tmp = new $classname($time, $tzOffset);
			return $tmp;

		}

	}

}
else
{
	class JevDate extends JDate
	{
		public function getDate($time = 'now', $tzOffset = null) {
			return JFactory::getDate($time,$tzOffset);
		}
	}
}