<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: helper.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Cache\Cache;
use Joomla\CMS\User\User;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Profiler\Profiler;

jimport('joomla.access.access');

/** Should already be defined within JEvents, however it does no harm and resolves issue with pop-up details */
include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");

/**
 * Helper class with common functions for the component and modules
 *
 * @author     Thomas Stahl
 * @since      1.4
 */
class JEVHelper
{

	/**
	 * @var    array  Array containing information for loaded files
	 * @since  3.0
	 */
	protected static
		$loaded = array();

	/**
	 * load language file
	 *
	 * @static
	 * @access public
	 * @since  1.4
	 */
	public static
	function loadLanguage($type = 'default', $lang = '')
	{

		// to be enhanced in future : load by $type (com, modcal, modlatest) [tstahl]
		$input = Factory::getApplication()->input;

		$option = $input->getCmd("option");
		$cfg    = JEVConfig::getInstance();
		$lang   = Factory::getLanguage();

		static $isloaded = array();

		$typemap = array(
			'default'     => 'front',
			'front'       => 'front',
			'admin'       => 'admin',
			'modcal'      => 'front',
			'modlatest'   => 'front',
			'modfeatured' => 'front'
		);
		$type    = (isset($typemap[$type])) ? $typemap[$type] : $typemap['default'];

		// load language defines only once
		if (isset($isloaded[$type]))
		{
			return;
		}

		$cfg             = JEVConfig::getInstance();
		$isloaded[$type] = true;

		switch ($type)
		{
			case 'front':
				// load new style language
				// Always load site component language !
				$lang->load(JEV_COM_COMPONENT, JPATH_SITE);

				// overload language with components language directory if available
				//$inibase = JPATH_SITE . '/components/' . JEV_COM_COMPONENT;
				//$lang->load(JEV_COM_COMPONENT, $inibase);
				// Load Site specific language overrides
                if (PHP_SAPI !== "cli")
                {
                    $lang->load( JEV_COM_COMPONENT, JPATH_THEMES . '/' . Factory::getApplication( 'site' )->getTemplate() );
                }
				break;

			case 'admin':
				// load new style language
				// if loading from another component or is frontend then force the load of the admin language file - otherwite done automatically
				if ($option != JEV_COM_COMPONENT || !Factory::getApplication()->isClient('administrator'))
				{
					// force load of installed language pack
					$lang->load(JEV_COM_COMPONENT, JPATH_ADMINISTRATOR);
				}
				// overload language with components language directory if available
				//$inibase = JPATH_ADMINISTRATOR . '/components/' . JEV_COM_COMPONENT;
				//$lang->load(JEV_COM_COMPONENT, $inibase);

				break;
			default:
				break;
		} // switch

	}

	public static
	function loadExtensionLanguage($extension, $basePath = JPATH_ADMINISTRATOR)
	{

		$lang = Factory::getLanguage();

		return $lang->load(strtolower($extension), $basePath, null, false, true);

	}

	/**
	 * load iCal instance for filename
	 *
	 * @static
	 * @access public
	 * @since  1.5
	 */
	public static
	function & iCalInstance($filename, $rawtext = "")
	{

		static $instances = array();
		if (is_array($filename))
		{
			echo "problem";
		}
		$index = md5($filename . $rawtext);
		if (array_key_exists($index, $instances))
		{
			return $instances[$index];
		}
		else
		{
			$import            = new iCalImport();
			$instances[$index] = $import->import($filename, $rawtext);

			return $instances[$index];
		}

	}

	/**
	 * Returns the Max year to display from Config
	 *
	 * @static
	 * @access public
	 * @return    string                integer with the max year to show in the calendar
	 */
	public static
	function getMinYear()
	{

		$params  = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$minyear = $params->get("com_earliestyear", 1970);

		$minyear = JEVHelper::getYearNumber($minyear);

		//Just in case we got text here.
		if (!is_numeric($minyear))
		{
			$minyear = "1970";
		}

		return $minyear;

	}

	/**
	 * Returns the full month name
	 *
	 * @static
	 * @access public
	 *
	 * @param    string $month numeric month
	 *
	 * @return    string                localised long month name
	 */
	public static
	function getMonthName($month = 12)
	{

		switch (intval($month))
		{

			case 1:
				return Text::_('JEV_JANUARY');
			case 2:
				return Text::_('JEV_FEBRUARY');
			case 3:
				return Text::_('JEV_MARCH');
			case 4:
				return Text::_('JEV_APRIL');
			case 5:
				return Text::_('JEV_MAY');
			case 6:
				return Text::_('JEV_JUNE');
			case 7:
				return Text::_('JEV_JULY');
			case 8:
				return Text::_('JEV_AUGUST');
			case 9:
				return Text::_('JEV_SEPTEMBER');
			case 10:
				return Text::_('JEV_OCTOBER');
			case 11:
				return Text::_('JEV_NOVEMBER');
			case 12:
				return Text::_('JEV_DECEMBER');
		}

	}

	/**
	 * Return the short month name
	 *
	 * @static
	 * @access public
	 *
	 * @param    string $month numeric month
	 *
	 * @return    string                localised short month name
	 */
	public static
	function getShortMonthName($month = 12)
	{

		switch (intval($month))
		{

			// Use Joomla translation
			case 1:
				return Text::_('JANUARY_SHORT');
			case 2:
				return Text::_('FEBRUARY_SHORT');
			case 3:
				return Text::_('MARCH_SHORT');
			case 4:
				return Text::_('APRIL_SHORT');
			case 5:
				return Text::_('MAY_SHORT');
			case 6:
				return Text::_('JUNE_SHORT');
			case 7:
				return Text::_('JULY_SHORT');
			case 8:
				return Text::_('AUGUST_SHORT');
			case 9:
				return Text::_('SEPTEMBER_SHORT');
			case 10:
				return Text::_('OCTOBER_SHORT');
			case 11:
				return Text::_('NOVEMBER_SHORT');
			case 12:
				return Text::_('DECEMBER_SHORT');
		}

	}

	/**
	 * Returns name of the day longversion
	 *
	 * @static
	 *
	 * @param    int        daynb    # of day
	 * @param    int        array, 0 return single day, 1 return array of all days
	 *
	 * @return    mixed    localised short day letter or array of names
	 * */
	public static
	function getDayName($daynb = 0, $array = 0)
	{

		static $days = null;

		if ($days === null)
		{
			$days = array();

			$days[0] = Text::_('JEV_SUNDAY');
			$days[1] = Text::_('JEV_MONDAY');
			$days[2] = Text::_('JEV_TUESDAY');
			$days[3] = Text::_('JEV_WEDNESDAY');
			$days[4] = Text::_('JEV_THURSDAY');
			$days[5] = Text::_('JEV_FRIDAY');
			$days[6] = Text::_('JEV_SATURDAY');
		}

		if ($array == 1)
		{
			return $days;
		}

		$i = $daynb % 7; //modulo 7

		return $days[$i];

	}

	/**
	 * Returns the short day name
	 *
	 * @static
	 *
	 * @param    int        daynb    # of day
	 * @param    int        array, 0 return single day, 1 return array of all days
	 *
	 * @return    mixed    localised short day letter or array of names
	 * */
	public static
	function getShortDayName($daynb = 0, $array = 0)
	{

		static $days = null;

		if ($days === null)
		{
			$days = array();

			$days[0] = Text::_('JEV_SUN');
			$days[1] = Text::_('JEV_MON');
			$days[2] = Text::_('JEV_TUE');
			$days[3] = Text::_('JEV_WED');
			$days[4] = Text::_('JEV_THU');
			$days[5] = Text::_('JEV_FRI');
			$days[6] = Text::_('JEV_SAT');
		}

		if ($array == 1)
		{
			return $days;
		}

		$i = $daynb % 7; //modulo 7

		return $days[$i];

	}

	public static
	function getTime($date, $h = -1, $m = -1)
	{

		$cfg = JEVConfig::getInstance();

		static $format_type;
		if (!isset($format_type))
		{
			$cfg         = JEVConfig::getInstance();
			$format_type = $cfg->get('com_dateformat');
		}

		// if date format is from langauge file then do this first
		if ($format_type == 3)
		{
			if ($h >= 0 && $m >= 0)
			{
				$time = JevDate::mktime($h, $m);

				return JEV_CommonFunctions::jev_strftime(Text::_("JEV_TIME_FORMAT"), $time);
			}
			else
			{
				return JEV_CommonFunctions::jev_strftime(Text::_("JEV_TIME_FORMAT"), $date);
			}
		}

		if ($cfg->get('com_calUseStdTime') == '0')
		{
			if ($h >= 0 && $m >= 0)
			{
				return sprintf('%02d:%02d', $h, $m);
			}
			else
			{
				return JevDate::strftime("%H:%M", $date);
			}
		}
		else if (IS_WIN)
		{
			return JevDate::strftime("%#I:%M%p", $date);
		}
		else
		{
			return strtolower(JevDate::strftime("%I:%M%p", $date));
		}

	}

	/**
	 * Returns name of the day letter
	 *
	 * @param    i
	 *
	 * @staticnt        daynb    # of day
	 *
	 * @param    int        array, 0 return single day, 1 return array of all days
	 *
	 * @return    mixed    localised short day letter or array of letters
	 * */
	public static
	function getWeekdayLetter($daynb = 0, $array = 0)
	{

		static $days = null;

		if ($days === null)
		{
			$days    = array();
			$days[0] = Text::_('JEV_SUNDAY_CHR');
			$days[1] = Text::_('JEV_MONDAY_CHR');
			$days[2] = Text::_('JEV_TUESDAY_CHR');
			$days[3] = Text::_('JEV_WEDNESDAY_CHR');
			$days[4] = Text::_('JEV_THURSDAY_CHR');
			$days[5] = Text::_('JEV_FRIDAY_CHR');
			$days[6] = Text::_('JEV_SATURDAY_CHR');
		}

		if ($array == 1)
		{
			return $days;
		}

		$i = $daynb % 7; //modulo 7

		return $days[$i];

	}

	/**
	 * Function that overwrites meta-tags in mainframe!!
	 *
	 * @static
	 *
	 * @param string $name    - metatag name
	 * @param string $content - metatag value
	 */
	public static
	function checkRobotsMetaTag($name = "robots", $content = "index,follow")
	{

		$app    = Factory::getApplication();
		$input  = $app->input;

		// force robots metatag
		$cfg      = JEVConfig::getInstance();
		$document = Factory::getDocument();
		// constrained in some way
		if ($cfg->get('com_blockRobots', 0) >= 1)
		{
			// Allow on detail  pages - block otherwise unless crawler!
			if ($cfg->get('com_blockRobots', 0) == 3)
			{
				if (strpos($input->getString("jevtask", ""), ".detail") > 0)
				{
					$document->setMetaData($name, "index,nofollow");

					return;
				}
				if (strpos($input->getString("jevtask", ""), "crawler") !== false || $content != "index,follow")
				{
					$document->setMetaData($name, $content);
				}
				else
				{
					$document->setMetaData($name, "noindex,nofollow");
				}

				return;
			}
			// Always block Robots
			if ($cfg->get('com_blockRobots', 0) == 1)
			{
				$document->setMetaData($name, "noindex,nofollow");

				return;
			}
			// conditional on date
			list($cyear, $cmonth, $cday) = JEVHelper::getYMD();
			$cdate = JevDate::mktime(0, 0, 0, $cmonth, $cday, $cyear);
			$prior = JevDate::strtotime($cfg->get('robotprior', "-1 day"));
			if ($cdate < $prior && $cfg->get('com_blockRobots', 0))
			{
				$document->setMetaData($name, "noindex,nofollow");

				return;
			}
			$post = JevDate::strtotime($cfg->get('robotpost', "-1 day"));
			if ($cdate > $post && $cfg->get('com_blockRobots', 0))
			{
				$document->setMetaData($name, "noindex,nofollow");

				return;
			}
			//If JEvents is not blocking robots we use menu item configuration
			$document->setMetaData($name, $cfg->get('robots', $content));
		}
		//If JEvents is not blocking robots we use menu item configuration
		else
		{
			$document->setMetaData($name, $cfg->get('robots', $content));
		}

	}

	/**
	 * Get array Year, Month, Day from current Request, fallback to current date
	 *
	 * @return array
	 */
	public static
	function getYMD()
	{

		static $data;

		$input  = Factory::getApplication()->input;
		if (!isset($data))
		{
			$datenow = JEVHelper::getNow();
			list($yearnow, $monthnow, $daynow) = explode('-', $datenow->toFormat('%Y-%m-%d'));

			$year  = min(2100, abs($input->getInt('year', $yearnow)));
			$month = min(99, abs($input->getInt('month', $monthnow)));
			$day   = min(3650, abs($input->getInt('day', $daynow)));
			if ($day <= 0)
			{
				$day = $daynow;
			}
			if ($month <= 0)
			{
				$month = $monthnow;
			}
			if ($year <= 0)
			{
				$year = $yearnow;
			}
			if ($day <= '9')
			{
				$day = '0' . $day;
			}
			if ($month <= '9')
			{
				$month = '0' . $month;
			}

			// Make sure $day is not outside the month
			$lastDayOfMonth = intval(date("d", mktime(6, 0, 0, $month + 1, 1, $year) - 86400));
			$day            = $lastDayOfMonth < $day ? $lastDayOfMonth : $day;

			$data   = array();
			$data[] = $year;
			$data[] = $month;
			$data[] = $day;
		}

		return $data;

	}

	//New MetaSet Function, to set the meta tags if they exist in the Menu Item

	static public
	function SetMetaTags()
	{

		// Get Global Config
		$jConfig = Factory::getConfig();

		//Get Document to set the Meta Tags to.
		$document = Factory::getDocument();

		//Get the Params.
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		if ($params->get('menu-meta_description') && (string) $jConfig->get('MetaDesc', '') === (string) $document->getDescription())
		{
			$document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords') && $jConfig->get('MetaKeys', '') === $document->getMetaData("keywords"))
		{
			$document->setMetaData('keywords', $params->get('menu-meta_keywords'));
		}

	}

	public static
	function forceIntegerArray($cid = null, $asString = true)
	{

		$cid = is_null($cid) ? array() : $cid;

		$arraykeys = array_keys($cid);
		foreach ($arraykeys as $c)
		{
			if (!isset($cid[$c]))
			{
				$x = 1;
			}
			$cid[$c] = intval($cid[$c]);
		}
		if ($asString)
		{
			$id_string = implode(",", $cid);

			return $id_string;
		}
		else
		{
			return $cid;
		}

	}

	/**
	 * Loads all necessary files for and creats popup calendar link
	 *
	 * @static
	 */
	public static
	function loadCalendar($fieldname, $fieldid, $value, $minyear, $maxyear, $onhidestart = "", $onchange = "", $format = 'Y-m-d', $attributes = array())
	{

		$document           = Factory::getDocument();
		$component          = "com_jevents";
		$params             = ComponentHelper::getParams($component);
		$forcepopupcalendar = $params->get("forcepopupcalendar", 1);
		$offset             = $params->get("com_starday", 1);

		if ($value == "")
		{
			$value = date("Y-m-d");
		}

		list ($yearpart, $monthpart, $daypart) = explode("-", $value);
		$value = str_replace(array("Y", "m", "d"), array($yearpart, $monthpart, $daypart), $format);

		// Build the attributes array.
		empty($onchange) ? null : $attributes['onchange'] = $onchange;
		//$attributes['onselect']="function{this.hide();}";
		/*
		empty($this->size)      ? null : $attributes['size'] = $this->size;
		empty($this->maxlength) ? null : $attributes['maxlength'] = $this->maxlength;
		empty($this->class)     ? null : $attributes['class'] = $this->class;
		!$this->readonly        ? null : $attributes['readonly'] = 'readonly';
		!$this->disabled        ? null : $attributes['disabled'] = 'disabled';
		empty($hint)            ? null : $attributes['placeholder'] = $hint;
		$this->autocomplete     ? null : $attributes['autocomplete'] = 'off';
		!$this->autofocus       ? null : $attributes['autofocus'] = '';

		if ($this->required)
		{
			$attributes['required'] = '';
			$attributes['aria-required'] = 'true';
		}
*/
		// switch back to strftime format to use Joomla calendar tool
		$format = str_replace(array("Y", "m", "d"), array("%Y", "%m", "%d"), $format);

		echo HTMLHelper::_('calendar', $yearpart . "-" . $monthpart . "-" . $daypart, $fieldname, $fieldid, $format, $attributes);

	}

	/**
	 * Loads all necessary files for and creats popup calendar link
	 *
	 * @static
	 */
	public static
	function loadElectricCalendar($fieldname, $fieldid, $value, $minyear, $maxyear, $onhidestart = "", $onchange = "", $format = 'Y-m-d', $attribs = array(), $showtime = false, $showDefaultDateValue = true)
	{

		if (version_compare(JVERSION, '4.0.0', '>='))
		{
            // make sure not an strftime format
            $informat = $format;
            $invalue  = $value;

            $format = JEVHelper::mapStrftimeFormatToDateFormat($format);

            $formatHasTime = preg_match("#a|A|g|h|G|H|i|s|v|u|U#", $format);
            $showtime = $showtime && $formatHasTime;

			$document           = Factory::getDocument();
			$component          = "com_jevents";
			$params             = ComponentHelper::getParams($component);
			$forcepopupcalendar = $params->get("forcepopupcalendar", 1);
			$offset             = $params->get("com_starday", 1);

			if ($value == "")
			{
				$value = date("Y-m-d");
			}
			else
			{
                $dcff_format = $format;
				$datetime = date_create_from_format($dcff_format, $value);
                // leading spaces!
                $spaceMapping = array();
                $spaceMapping[] = array("g", " g");
                $spaceMapping[] = array("d", " d");
                $spaceMapping[] = array(array('g', 'd'),   array(" g", "d"));
                $spaceMapping[] = array(array('g', 'd'),   array("g", " d"));
                $spaceMapping[] = array(array('g', 'd'), array("  g", " d"));
                if (!$datetime && (strpos($dcff_format, 'g') !== false || strpos($dcff_format, 'd') !== false))
                {
                    foreach ($spaceMapping as $spaceMap)
                    {
                        $dcff_format = str_replace( $spaceMap[0], $spaceMap[1], $format );
                        $datetime = date_create_from_format( $dcff_format, $value );
                        if ($datetime)
                        {
                            break;
                        }
                    }
                }

				// This is probably because we have mysql formatted value
				if (!$datetime)
				{
					$datetime = date_create_from_format('Y-m-d', $value);
					if (!$datetime)
					{
						$value    = date($dcff_format);
						$datetime = date_create_from_format($dcff_format, $value);
					}
				}
                if ($showtime) {
                    $value = $datetime->format("Y-m-d H:i");
                }
                else {
                    $value = $datetime->format("Y-m-d");
                }
			}


			list ($yearpart, $monthpart, $daypart) = explode("-", $value);
            $hourpart = '00';
            $minpart = '00';
            if ($showtime && strpos($daypart, " ") !== false)
            {
                list($daypart, $timepart) = explode(" ", $daypart);
                if ($timepart)
                {
                    list($hourpart, $minpart) = explode(":", $timepart);
                }
            }

            $value = str_replace(
                array("Y",
                "m", "M", "n", "F",
                "d", "D", "j",
                "h", "H",
                "i"),
                array($yearpart,
                $monthpart, $monthpart, $monthpart, $monthpart,
                $daypart, $daypart,$daypart,
                $hourpart, $hourpart,
                $minpart),
                $format);

			$attributes = $attribs;
			// Build the attributes array.
			empty($onchange) ? null : $attributes['onChange'] = $onchange;
            empty($onchange) ? null : $attributes['onchange'] = $onchange;

            $attributes["showTime"] = $showtime;

			// Remove text based format for Joomla calendar tool
            $xformat = str_replace(
                array( "D", "l", "F", "M"),
                array( "N", "w", "m", 'm'),
                $format);

            // Switch back to strftime formats for javascript!
            // Year
            $format = str_replace(
                array( 'o',  'y',  'Y'),
                array('%G', '%y', '%Y'),
                $format);
            // Not supporting %c %g

            // Month
            $format = str_replace(
                array( 'M',  'F',  'M',  'm'),
                array('%b', '%B', '%h', '%m'),
                $format);
            // Not supporting

            // Day
            $format = str_replace(
                array( 'D',  'l',  'd',  'j'),
                array('%a', '%A', '%d', '%e' ),
                $format);
            // Not supporting %u %w %j

            // AM/PM
            $format = str_replace(
                array( 'a',  'A'),
                array('%P', '%p'),
                $format);
            // Not supporting %u %w %j

            // Hour
            $format = str_replace(
                array( 'H',  'G',  'h',  'g'),
                array('%H', '%k', '%I', '%l' ),
                $format);
            // Not supporting %u %w %j

            // Minute
            $format = str_replace(
                array( 'i'),
                array('%M'),
                $format);
            // Not supporting

            // Second
            $format = str_replace(
                array( 's'),
                array('%s'),
                $format);
            // Not supporting

            if ($showtime)
            {
                $value = "$yearpart-$monthpart-$daypart $hourpart:$minpart";
            }
            else
            {
                $value = "$yearpart-$monthpart-$daypart";
            }
            //echo HTMLHelper::_('calendar', $value, $fieldname, $fieldid, $format, $attributes);
            echo JEVHelper::j4calendar($value, $fieldname, $fieldid, $format, $attributes);

            return;
		}
		$document           = Factory::getDocument();
		$component          = "com_jevents";
		$params             = ComponentHelper::getParams($component);
		$forcepopupcalendar = $params->get("forcepopupcalendar", 1);
		$offset             = $params->get("com_starday", 1);

        $format = JEVHelper::mapStrftimeFormatToDateFormat($format);

        $formatHasTime = preg_match("#a|A|g|h|G|H|i|s|v|u|U#", $format);
        $showtime = $showtime && $formatHasTime;

		$app    = Factory::getApplication();

		if ($showtime)
		{
			if ($showDefaultDateValue)
			{
				if (empty($value))
				{
					$value = date($format);
				}

				$datetime = date_create_from_format($format, $value);
				// This is probably because we have mysql formatted value
				if (!$datetime)
				{
					$datetime = date_create_from_format('Y-m-d', $value);
					if (!$datetime)
					{
						$value    = date($format);
						$datetime = date_create_from_format($format, $value);
					}
				}

				$value = $datetime->format("Y-m-d H:i");
			}
			// switch back to strftime format to use Joomla calendar tool
			$format = str_replace(array("Y", "m", "d", "H", "h", "i", "a"), array("%Y", "%m", "%d", "%H", "%I", "%M", "%P"), $format);

		}
		else
		{
			if ($showDefaultDateValue)
			{

				if ($value == "")
				{
					$value = date("Y-m-d");
				}
				list ($yearpart, $monthpart, $daypart) = explode("-", $value);
				$value = str_replace(array("Y", "m", "d"), array($yearpart, $monthpart, $daypart), $format);
				$value  = $yearpart . "-" . $monthpart . "-" . $daypart;
			}
			// switch back to strftime format to use Joomla calendar tool
			$format = str_replace(array("Y", "m", "d"), array("%Y", "%m", "%d"), $format);
		}


		if (!empty($onchange))
		{
			Factory::getDocument()->addScriptDeclaration("document.addEventListener('DOMContentLoaded',function (){if(document.getElementById('" . $fieldid . "')) document.getElementById('" . $fieldid . "').addEventListener('change', function(){" . $onchange . "});});");
			$onchange = "";

		}
		// Build the attributes array.
		//empty($onchange) ? null : $attribs['onchange'] = $onchange;

		$name = $fieldname;

		static $done;

		if ($done === null)
		{
			$done = array();
		}

		// new script is disabled if readonly is set so set it on an onload event instead
		if ((isset($attribs['readonly']) && $attribs['readonly'] == 'readonly')
			|| (isset($attribs[' readonly']) && $attribs[' readonly'] == 'readonly'))
		{
			$readonly = true;
		}
		else
		{
			$readonly = false;
		}

		$disabled   = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';
		$showtime   = (isset($attribs['showtime']) && $attribs['showtime'] == 'showtime') || $showtime;
		$timeformat = "24";

		if ($showtime && $params->get("com_calUseStdTime", 1) == 0)
		{
			// $timeformat = "12";
		}

		if ($showtime && strpos($format, "%P"))
		{
			$timeformat = "12";
		}
        $showtime = $showtime? 1 : 0;

		if (is_array($attribs))
		{
			$attribs['class'] = isset($attribs['class']) ? $attribs['class'] : 'input-medium';
			$attribs['class'] = trim($attribs['class'] . ' hasTooltip');

			$attribs = ArrayHelper::toString($attribs);
		}

		HTMLHelper::_('bootstrap.tooltip');

		// Format value when not nulldate ('0000-00-00 00:00:00'), otherwise blank it as it would result in 1970-01-01.
		if ((int) $value && $value != Factory::getDbo()->getNullDate())
		{
			$tz = date_default_timezone_get();
			date_default_timezone_set('UTC');
			$inputvalue = JevDate::rawStrftime($format, strtotime($value));
			date_default_timezone_set($tz);
		}
		else
		{
			$inputvalue = '';
		}

		// Load the calendar behavior
		//HTMLHelper::_('behavior.calendar');
		// TODO remove these Joomla 3.7.0 bug workarounds when fixed in Joomla
		//$tag      = Factory::getLanguage()->getTag();
		//HTMLHelper::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
		//HTMLHelper::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);

		$tag = Factory::getLanguage()->getTag();

		if (version_compare(JVERSION, '3.7.0', '>='))
		{
			if (is_array($attribs))
			{
				// Joomla readonly workaround
				unset($attribs['readonly']);
				unset($attribs[' readonly']);
			}

			$calendar  = Factory::getLanguage()->getCalendar();
			$direction = strtolower(Factory::getDocument()->getDirection());

			// Get the appropriate file for the current language date helper
			$helperPath = 'system/fields/calendar-locales/date/gregorian/date-helper.min.js';

			if (!empty($calendar) && is_dir(JPATH_ROOT . '/media/system/js/fields/calendar-locales/date/' . strtolower($calendar)))
			{
				$helperPath = 'system/fields/calendar-locales/date/' . strtolower($calendar) . '/date-helper.min.js';
			}

			// Get the appropriate locale file for the current language
			$localesPath = 'system/fields/calendar-locales/en.js';

			if (is_file(JPATH_ROOT . '/media/system/js/fields/calendar-locales/' . strtolower($tag) . '.js'))
			{
				$localesPath = 'system/fields/calendar-locales/' . strtolower($tag) . '.js';
			}
			elseif (is_file(JPATH_ROOT . '/media/system/js/fields/calendar-locales/' . strtolower(substr($tag, 0, -3)) . '.js'))
			{
				$localesPath = 'system/fields/calendar-locales/' . strtolower(substr($tag, 0, -3)) . '.js';
			}

			$direction  = strtolower(Factory::getDocument()->getDirection());
			$cssFileExt = ($direction === 'rtl') ? '-rtl.css' : '.css';

			// Load polyfills for older IE
			HTMLHelper::_('behavior.polyfill', array('event', 'classlist', 'map'), 'lte IE 11');

			HTMLHelper::_('script', $localesPath, array('framework' =>  false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
			HTMLHelper::_('script', $helperPath, array('framework' =>  false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
			HTMLHelper::_('script', 'system/fields/calendar.js', array('framework' =>  false, 'relative' => true, 'pathOnly' => false, 'detectBrowser' => false, 'detectDebug' => true));
			HTMLHelper::_('stylesheet', 'system/fields/calendar' . $cssFileExt, array(), true);

			// Hide button using inline styles for readonly/disabled fields
			//$btn_style	= ($readonly || $disabled) ? ' style="display:none;"' : '';
			//$div_class	= (!$readonly && !$disabled) ? ' class="input-append"' : '';
			$btn_style = $disabled ? ' style="display:none;"' : '';
			$div_class = !$disabled ? ' class="input-group"' : '';

			$jevtask = Factory::getApplication()->input->getString("jevtask", "");
			$isedit = (strpos($jevtask, "icalevent.edit") !== false || strpos($jevtask, "icalrepeat.edit") !== false );

			echo '<div class=" field-calendar">'
				. '<div' . $div_class . '>'
				. '<input type="text" title="' . ($inputvalue ? HTMLHelper::_('date', $value, null, null) : '')
				. '" name="' . $name . '" id="' . $fieldid . '" '
				. 'value="' . htmlspecialchars($inputvalue, ENT_COMPAT, 'UTF-8') . '" '
				. 'data-alt-value="' . htmlspecialchars($inputvalue, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />'
				. '<span class="input-group-append">'
				. '<button type="button" class="btn btn-primary ' . $btn_style . '"
			id="' . $fieldid . '_btn"
			data-inputfield="' . $fieldid . '"
			data-dayformat="' . $format . '"
			data-button="' . $fieldid . '_btn"
			data-firstday="' . $offset . '"
			data-weekend="' . Factory::getLanguage()->getWeekEnd() . '"
			data-today-btn="1"
			data-week-numbers="0"
			data-show-time="' . $showtime . '"
			data-show-others="1"
			data-only-months-nav="0"
			data-time-24="' . $timeformat . '" 
			' . (!empty($minYear) ? ' data-min-year="' . $minYear . '"' : "") . '
			' . (!empty($maxYear) ? ' data-max-year="' . $maxYear . '"' : "") . ' >'
				. (($app->isClient('administrator') || ($params->get("newfrontendediting", 1) && $isedit)) ? '<span class="gsl-icon" gsl-icon="icon: calendar"></span>' : '<span class="icon-calendar"></span>')
				. '</button>'
				. '</span>'
				. '</div>'
				. '</div>';

			if ($readonly)
			{
				Factory::getDocument()->addScriptDeclaration("jQuery(window).on('load', function(){jQuery('#" . $fieldid . "').prop('readonly', true);})");
			}

		}
		else
		{
			HTMLHelper::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
			HTMLHelper::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);

			// Only display the triggers once for each control.
			if (!in_array($fieldid, $done))
			{
				$document = Factory::getDocument();
				$document
					->addScriptDeclaration(
						'jQuery(document).ready(function($) {
					if (!jQuery("#' . $fieldid . '").length) {
						alert("' . Text::sprintf("JEV_MISSING_CALENDAR_FIELD_IN_PAGE", true) . '\n\n" + "' . $fieldid . '"  );
						return;
					}
			Calendar.setup({
			// Id of the input field
			inputField: "' . $fieldid . '",
			// Format of the input field
			ifFormat: "' . $format . '",
			// Trigger for the calendar (button ID)
			button: "' . $fieldid . '_img",
			// Alignment (defaults to "Bl")
			align: "Tl",
                        // firstDay   numeric: 0 to 6.  "0" means display Sunday first, "1" means display Monday first, etc.
                        firstDay: ' . $offset . ',
			// Allowable date range for picker
			range:[' . $minyear . ',' . $maxyear . '],
			// electric false means field update ONLY when a day cell is clicked
			electric:false,
			singleClick: true,
                        showsTime:' . $showtime . ',
                        timeFormat:' . $timeformat . ',
			});});'
					);
				$done[] = $fieldid;
			}

			// Hide button using inline styles for readonly/disabled fields
			$btn_style = ($readonly || $disabled) ? ' style="display:none;"' : '';
			$div_class = (!$readonly && !$disabled) ? ' class="input-append"' : '';

			echo '<div' . $div_class . '>'
				. '<input type="text" title="' . ($inputvalue ? HTMLHelper::_('date', $value, null, null) : '')
				. '" name="' . $name . '" id="' . $fieldid . '" value="' . htmlspecialchars($inputvalue, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />'
				. '<button type="button" class="btn" id="' . $fieldid . '_img"' . $btn_style . '><span class="icon-calendar"></span></button>'
				. '</div>';

		}

	}

    /**
     * Displays a calendar control field
     *
     * @param   string  $value    The date value
     * @param   string  $name     The name of the text field
     * @param   string  $id       The id of the text field
     * @param   string  $format   The date format
     * @param   mixed   $attribs  Additional HTML attributes
     *                            The array can have the following keys:
     *                            readonly      Sets the readonly parameter for the input tag
     *                            disabled      Sets the disabled parameter for the input tag
     *                            autofocus     Sets the autofocus parameter for the input tag
     *                            autocomplete  Sets the autocomplete parameter for the input tag
     *                            filter        Sets the filter for the input tag
     *
     * @return  string  HTML markup for a calendar field
     *
     * @since   1.5
     *
     */
    public static function j4calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = [])
    {
        $app       = Factory::getApplication();
        $lang      = $app->getLanguage();
        $tag       = $lang->getTag();
        $calendar  = $lang->getCalendar();
        $direction = strtolower($app->getDocument()->getDirection());

        // Get the appropriate file for the current language date helper
        $helperPath = 'system/fields/calendar-locales/date/gregorian/date-helper.min.js';

        if ($calendar && is_dir(JPATH_ROOT . '/media/system/js/fields/calendar-locales/date/' . strtolower($calendar))) {
            $helperPath = 'system/fields/calendar-locales/date/' . strtolower($calendar) . '/date-helper.min.js';
        }

        $readonly     = isset($attribs['readonly']) && $attribs['readonly'] === 'readonly';
        $disabled     = isset($attribs['disabled']) && $attribs['disabled'] === 'disabled';
        $autocomplete = isset($attribs['autocomplete']) && $attribs['autocomplete'] === '';
        $autofocus    = isset($attribs['autofocus']) && $attribs['autofocus'] === '';
        $required     = isset($attribs['required']) && $attribs['required'] === '';
        $filter       = isset($attribs['filter']) && $attribs['filter'] === '';
        $todayBtn     = $attribs['todayBtn'] ?? true;
        $weekNumbers  = $attribs['weekNumbers'] ?? true;
        $showTime     = $attribs['showTime'] ?? false;
        $fillTable    = $attribs['fillTable'] ?? true;
        $timeFormat   = $attribs['timeFormat'] ?? 24;
        $singleHeader = $attribs['singleHeader'] ?? false;
        $hint         = $attribs['placeholder'] ?? '';
        $class        = $attribs['class'] ?? '';
        $onchange     = $attribs['onChange'] ?? '';
        $minYear      = $attribs['minYear'] ?? null;
        $maxYear      = $attribs['maxYear'] ?? null;

        $showTime     = ($showTime) ? "1" : "0";
        $todayBtn     = ($todayBtn) ? "1" : "0";
        $weekNumbers  = ($weekNumbers) ? "1" : "0";
        $fillTable    = ($fillTable) ? "1" : "0";
        $singleHeader = ($singleHeader) ? "1" : "0";

        // Format value when not nulldate ('0000-00-00 00:00:00'), otherwise blank it as it would result in 1970-01-01.
        if ($value && $value !== Factory::getDbo()->getNullDate() && strtotime($value) !== false) {
            $tz = date_default_timezone_get();
            date_default_timezone_set('UTC');

            /**
             * Try to convert strftime format to date format, if success, use DateTimeImmutable to format
             * the passed datetime to avoid deprecated warnings on PHP 8.1. We only support converting most
             * common used format here.
             */
            //$dateFormat = self::strftimeFormatToDateFormat($format);
            $dateFormat = self::mapStrftimeFormatToDateFormat($format);

            if ($dateFormat !== false) {
                $date       = \DateTimeImmutable::createFromFormat('U', strtotime($value));
                $inputValue = $date->format($dateFormat);
            } else {
                $inputValue = strftime($format, strtotime($value));
            }

            date_default_timezone_set($tz);
        } else {
            $inputValue = '';
        }

        $data = [
            'id'             => $id,
            'name'           => $name,
            'class'          => $class,
            'value'          => $inputValue,
            'format'         => $format,
            'filter'         => $filter,
            'required'       => $required,
            'readonly'       => $readonly,
            'disabled'       => $disabled,
            'hint'           => $hint,
            'autofocus'      => $autofocus,
            'autocomplete'   => $autocomplete,
            'todaybutton'    => $todayBtn,
            'weeknumbers'    => $weekNumbers,
            'showtime'       => $showTime,
            'filltable'      => $fillTable,
            'timeformat'     => $timeFormat,
            'singleheader'   => $singleHeader,
            'tag'            => $tag,
            'helperPath'     => $helperPath,
            'direction'      => $direction,
            'onchange'       => $onchange,
            'minYear'        => $minYear,
            'maxYear'        => $maxYear,
            'dataAttribute'  => '',
            'dataAttributes' => '',
            'calendar'       => $calendar,
            'firstday'       => $lang->getFirstDay(),
            'weekend'        => explode(',', $lang->getWeekEnd()),
        ];

        return LayoutHelper::render('joomla.form.field.calendar', $data, null, null);
    }


	/**
	 * Loads all necessary files for JS Overlib tooltips
	 *
	 * @static
	 */
	public static
	function loadOverlib()
	{

		$cfg = JEVConfig::getInstance();

		// check if this function is already loaded
		if (!$cfg->get('loadOverlib'))
		{
			if ($cfg->get("com_enableToolTip", 1) || Factory::getApplication()->isClient('administrator'))
			{
				$document = Factory::getDocument();
				// RSH 10/11/10 - Check location of overlib files - j!1.6 doesn't include them!
				HTMLHelper::script('components/' . JEV_COM_COMPONENT . '/assets/js/overlib_mini.js');
				HTMLHelper::script('components/' . JEV_COM_COMPONENT . '/assets/js/overlib_hideform_mini.js');

				// change state so it isnt loaded a second time
				$cfg->set('loadOverlib', true);

				if ($cfg->get("com_calTTShadow", 1) && !Factory::getApplication()->isClient('administrator'))
				{
					HTMLHelper::script('components/' . JEV_COM_COMPONENT . '/assets/js/overlib_shadow.js');
				}
				if (!Factory::getApplication()->isClient('administrator'))
				{
					// Override Joomla class definitions for overlib decoration - only affects logged in users
					$ol_script = "  /* <![CDATA[ */\n";
					$ol_script .= "  // inserted by JEvents\n";
					$ol_script .= "  ol_fgclass='';\n";
					$ol_script .= "  ol_bgclass='';\n";
					$ol_script .= "  ol_textfontclass='';\n";
					$ol_script .= "  ol_captionfontclass='';\n";
					$ol_script .= "  ol_closefontclass='';\n";
					$ol_script .= "  /* ]]> */";
					$document->addScriptDeclaration($ol_script);
				}
			}
		}

	}

	public static
	function getAdminItemid()
	{

		static $jevitemid;
		if (!isset($jevitemid))
		{
			$jevitemid = 0;
			$menu      = Factory::getApplication()->getMenu();
			$active    = $menu->getActive();
			if (!is_null($active) && $active->component == JEV_COM_COMPONENT && strpos($active->link, "admin.listevents") > 0)
			{
				$jevitemid = $active->id;

				return $jevitemid;
			}
			else
			{
				$jevitems = $menu->getItems("component", JEV_COM_COMPONENT);
				// TODO Check enclosing categories
				if (count($jevitems) > 0)
				{
					$user = Factory::getUser();
					foreach ($jevitems as $jevitem)
					{
						if (in_array($jevitem->access, JEVHelper::getAid($user, 'array')))
						{
							if (strpos($jevitem->link, "admin.listevents") > 0)
							{
								$jevitemid = $jevitem->id;

								return $jevitemid;
							}
						}
					}
				}
			}
			$jevitemid = JEVHelper::getItemid();
		}

		return $jevitemid;

	}

	static public
	function getAid($user = null, $type = 'string')
	{

		if (is_null($user) || !$user)
		{
			$user = Factory::getUser();
		}
		$registry  = JevRegistry::getInstance("jevents");
		$adminuser = $registry->get("jevents.icaluser", false);
		if ($adminuser)
		{
			$user = $adminuser;
		}

		$root = $user->get("isRoot");
		if ($root)
		{
			static $rootlevels = false;
			if (!$rootlevels)
			{
				// Get a database object.
				$db = Factory::getDbo();

				// Build the base query.
				$query = $db->getQuery(true);
				$query->select('id, rules');
				$query->from($query->qn('#__viewlevels'));

				// Set the query for execution.
				$db->setQuery((string) $query);
				$rootlevels = $db->loadColumn();
				$rootlevels = ArrayHelper::toInteger($rootlevels);
			}
			$levels = $rootlevels;
		}
		else
		{
			$levels = $user->getAuthorisedViewLevels();
			if (JEVHelper::isAdminUser($user) && Factory::getApplication()->isClient('administrator'))
			{
				// Make sure admin users can see public events
				$levels = array_merge($levels, Access::getAuthorisedViewLevels(0));
			}
		}


		if ($type == 'string')
		{
			return implode(',', $levels);
		}
		elseif ($type == 'array')
		{
			return $levels;
		}
		elseif ($type = 'max')
		{
			return max($levels);
		}
		else
		{
			// not sure!
			return false; //  ??
		}

	}

	static public
	function isAdminUser($user = null)
	{

		if (is_null($user))
		{
			$user = Factory::getUser();
		}
		//$access = Access::check($user->id, "core.admin","com_jevents");
		// Add a second check incase the getuser failed.
		if (!$user)
		{
			return false;
		}
		$access = $user->authorise('core.admin', 'com_jevents');

		return $access;

	}

	/**
	 * find suitable menu item for displaying an event
	 *
	 * @param mixed $forcecheck - false = no check.  jIcalEventRepeat = should we check the access for the event.  Only checks categories at present.
	 *
	 * @return integer - menu item id
	 */
	public static
	function getItemid($forcecheck = false, $skipbackend = true)
	{

		$app    = Factory::getApplication();
		$input  = $app->input;

		if ($app->isClient('administrator') && $skipbackend)
			return 0;
		static $jevitemid;
		$evid = $forcecheck ? $forcecheck->ev_id() : 0;
		if (!isset($jevitemid))
		{
			$jevitemid = array();
		}
		if (!isset($jevitemid[$evid]))
		{
			$jevitemid[$evid] = 0;
			$menu             = $app->getMenu();
			$active           = $menu->getActive();
			$Itemid           = $input-> getInt("Itemid");
			if (is_null($active))
			{
				// wierd bug in Joomla when SEF is disabled but with xhtml urls sometimes &amp;Itemid is misinterpretted !!!
				$Itemid = $input->getInt("Itemid");
				if ($Itemid > 0 && $jevitemid[$evid] != $Itemid)
				{
					$active = $menu->getItem($Itemid);
				}
			}
			$option = $input->getCmd("option");
			// wierd bug in Joomla when SEF is disabled but with xhtml urls sometimes &amp;Itemid is misinterpretted !!!
			if ($Itemid == 0)
				$Itemid = $input->getInt("amp;Itemid", 0);
			if ($option == JEV_COM_COMPONENT && $Itemid > 0 && $input->getCmd("task") != "crawler.listevents" && $input->getCmd("jevtask", "") != "crawler.listevents")
			{
				$jevitemid[$evid] = $Itemid;

				return $jevitemid[$evid];
			}
			else if (!is_null($active) && $active->component == JEV_COM_COMPONENT && strpos($active->link, "admin") === false && strpos($active->link, "edit") === false && strpos($active->link, "crawler") === false)
			{
				$jevitemid[$evid] = $active->id;

				return $jevitemid[$evid];
			}
			else
			{
				$registry = JevRegistry::getInstance("jevents");
				$user     = $registry->get("jevents.icaluser", false);
				if (!$user)
				{
					$user = Factory::getUser();
				}
				$accesslevels = $user->getAuthorisedViewLevels();
				$jevitems     = $menu->getItems(array("component", "access"), array(JEV_COM_COMPONENT, $accesslevels));
				// TODO second level Check on enclosing categories and other constraints
				if (count($jevitems) > 0)
				{
					foreach ($jevitems as $jevitem)
					{
						// skip manage events and edit events menu items unless we really need them
						if (strpos($jevitem->link, "edit") > 0 || strpos($jevitem->link, "admin") > 0)
						{
							continue;
						}
						if (in_array($jevitem->access, JEVHelper::getAid($user, 'array')))
						{
							$jevitemid[$evid] = $jevitem->id;

							if ($forcecheck)
							{
								$mparams = is_string($jevitem->getParams()) ? new JevRegistry($jevitem->getParams()) : $jevitem->getParams();
								$mcatids = array();
								// New system
								$newcats = $mparams->get("catidnew", false);
								if ($newcats && is_array($newcats))
								{
									foreach ($newcats as $newcat)
									{
										if ($forcecheck->catid() == $newcat)
										{
											return $jevitemid[$evid];
										}

										if (!in_array($newcat, $mcatids))
										{
											$mcatids[] = $newcat;
										}
									}
								}
								else
								{
									for ($c = 0; $c < 999; $c++)
									{
										$nextCID = "catid$c";
										//  stop looking for more catids when you reach the last one!
										if (!$nextCatId = $mparams->get($nextCID, null))
										{
											break;
										}
										if ($forcecheck->catid() == $mparams->get($nextCID, null))
										{
											return $jevitemid[$evid];
										}

										if (!in_array($nextCatId, $mcatids))
										{
											$mcatids[] = $nextCatId;
										}
									}
								}
								// if no restrictions then can use this
								if (count($mcatids) == 0)
								{
									return $jevitemid[$evid];
								}
								continue;
							}

							return $jevitemid[$evid];
						}

					}
					// we didn't find them amongst the other menu item so checn the edit and admin ones
					foreach ($jevitems as $jevitem)
					{
						if (strpos($jevitem->link, "edit") === false && strpos($jevitem->link, "admin") === false)
						{
							continue;
						}
						if (in_array($jevitem->access, JEVHelper::getAid($user, 'array')))
						{
							$jevitemid[$evid] = $jevitem->id;

							if ($forcecheck)
							{
								$mparams = is_string($jevitem->params) ? new JevRegistry($jevitem->params) : $jevitem->params;
								$mcatids = array();
								// New system
								$newcats = $mparams->get("catidnew", false);
								if ($newcats && is_array($newcats))
								{
									foreach ($newcats as $newcat)
									{
										if ($forcecheck->catid() == $newcat)
										{
											return $jevitemid[$evid];
										}

										if (!in_array($newcat, $mcatids))
										{
											$mcatids[] = $newcat;
										}
									}
								}
								else
								{
									for ($c = 0; $c < 999; $c++)
									{
										$nextCID = "catid$c";
										//  stop looking for more catids when you reach the last one!
										if (!$nextCatId = $mparams->get($nextCID, null))
										{
											break;
										}
										if ($forcecheck->catid() == $mparams->get($nextCID, null))
										{
											return $jevitemid[$evid];
										}

										if (!in_array($nextCatId, $mcatids))
										{
											$mcatids[] = $nextCatId;
										}
									}
								}
								// if no restrictions then can use this
								if (count($mcatids) == 0)
								{
									return $jevitemid[$evid];
								}
								continue;
							}

							return $jevitemid[$evid];
						}
					}
				}
			}
		}

		return $jevitemid[$evid];

	}

	/**
	 * Get current year number
	 * @param   string  $year     Year reference or exact number of the year
	 * @return int
	 */
	public static
			function getYearNumber($year)
	{
		$datenow = JEVHelper::getNow();
		$yearnow = $datenow->toFormat('%Y');
		$firstpos = StringHelper::substr($year, 0, 1);

		if ($firstpos == "+")
		{
			$year = StringHelper::substr($year, 1);
			$year = $yearnow + $year;
		}
		else if ($firstpos == "-")
		{
			$year = StringHelper::substr($year, 1);
			$year = $yearnow - $year;
		}
		// If we do not get a 4 digit number and no sign we assume it's +$year
		else if (StringHelper::strlen($year) < 4)
		{
			$year = $yearnow + $year;
		}

		return $year;

	}

	/**
	 * Get JevDate object of current time
	 *
	 * @return object JevDate
	 */
	public static
			function getNow()
	{

		/* JevDate object of current time */
		static $datenow = null;

		if (!isset($datenow))
		{
			include_once(JPATH_SITE . "/components/com_jevents/jevents.defines.php");
			$compparams = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$tz = $compparams->get("icaltimezonelive", "");
			// Now in the set timezone!
			$datenow = JevDate::getDate("+0 seconds");
		}
		return $datenow;

	}

	/**
	 * Test to see if user can add events from the front end
	 *
	 * @return boolean
	 */
	public static
	function isEventCreator()
	{

		static $isEventCreator;
		if (!isset($isEventCreator))
		{
			$isEventCreator = false;
			$user           = JEVHelper::getAuthorisedUser();
			if (is_null($user))
			{
				$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
				$juser          = Factory::getUser();
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{

					if ($params->get("category_allow_deny", 1) == 0)
					{
						// this is too heavy on database queries - keep this in the file so that sites that want to use this approach can uncomment this block
						list($usec, $sec) = explode(" ", microtime());
						$time_start = (float) $usec + (float) $sec;
						if ($juser->get("id"))
						{
							$okcats = JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.create');
							$juser  = Factory::getUser();
							if (count($okcats))
							{
								$dataModel = new JEventsDataModel();
								$dataModel->setupComponentCatids();

								$allowedcats = explode(",", $dataModel->accessibleCategoryList());
								$intersect   = array_intersect($okcats, $allowedcats);

								if (count($intersect) > 0)
								{
									$isEventCreator = true;
								}
							}
						}
						list ($usec, $sec) = explode(" ", microtime());
						$time_end = (float) $usec + (float) $sec;
						//echo "time taken = ". round($time_end -  $time_start, 4)."<Br/>";
						//if ($isEventCreator) return $isEventCreator;
					}
					else
					{
						$isEventCreator = $juser->authorise('core.create', 'com_jevents');
						if ($isEventCreator)
						{
							$okcats = JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.create');
							if (count($okcats) > 0)
							{
								$juser     = Factory::getUser();
								$dataModel = new JEventsDataModel();
								$dataModel->setupComponentCatids();

								$allowedcats = explode(",", $dataModel->accessibleCategoryList());
								$intersect   = array_intersect($okcats, $allowedcats);

								if (count($intersect) == 0)
								{
									$isEventCreator = false;
								}
							}
							else
							{
								$isEventCreator = false;
							}
						}
					}
				}
				else if ($juser->id > 0 && JEVHelper::isAdminUser($juser))
				{
					Factory::getApplication()->enqueueMessage(Text::_("JEV_AUTHORISED_USER_MODE_ENABLED_BUT_NO_ENTRY_FOR_SUPER_USER"), 'warning');

				}
			}
			else if ($user->cancreate)
			{
				// Check maxevent count
				if ($user->eventslimit > 0)
				{
					$db = Factory::getDbo();
					$db->setQuery("SELECT count(*) FROM #__jevents_vevent where created_by=" . $user->user_id);
					$eventcount = intval($db->loadResult());
					if ($eventcount < $user->eventslimit)
					{
						$isEventCreator = true;
					}
					else
					{
						$isEventCreator = false;
					}
				}
				else
				{
					$isEventCreator = true;
				}
				// are we blocked by category or calendar constraints
				if ($isEventCreator && $user->categories != "" && $user->categories != "all")
				{
					$okcats = explode("|", $user->categories);

					$juser     = Factory::getUser();
					$dataModel = new JEventsDataModel();
					$dataModel->setupComponentCatids();

					$allowedcats = explode(",", $dataModel->accessibleCategoryList());
					$intersect   = array_intersect($okcats, $allowedcats);

					if (count($intersect) == 0)
					{
						$isEventCreator = false;
					}
				}
			}

			PluginHelper::importPlugin("jevents");

			Factory::getApplication()->triggerEvent('onIsEventCreator', array(& $isEventCreator));
		}
		if (is_null($isEventCreator)) $isEventCreator = false;

		return $isEventCreator;

	}

	/**
	 * Get user details for authorisation testing
	 *
	 * @param int $id Joomla user id
	 *
	 * @return array TableUser
	 */
	public static
	function getAuthorisedUser($id = null)
	{

		static $userarray;
		if (!isset($userarray))
		{
			$userarray = array();
		}
		if (is_null($id))
		{
			$juser = Factory::getUser();
			$id    = $juser->id;
		}
		if (!array_key_exists($id, $userarray))
		{
			JLoader::import("jevuser", JPATH_ADMINISTRATOR . "/components/" . JEV_COM_COMPONENT . "/tables/");

			$user = new TableUser();

			$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 0);
			// if authorised only then load from database
			if ($authorisedonly)
			{
				$users = $user->getUsersByUserid($id);
				if (count($users) > 0)
				{
					$userarray[$id] = current($users);
					// user must also be enabled!
					if (!$userarray[$id]->published)
					{
						$userarray[$id] = null;
					}
				}
				else
				{
					$userarray[$id] = null;
				}
			}
			else
			{
				$userarray[$id] = null;
			}
		}

		return $userarray[$id];

	}

	public static
	function getAuthorisedCategories($user, $component, $action)
	{

		static $results = array();
		$key = $user->id . ":component:" . $action;
		if (!isset($results[$key]))
		{
			$results[$key] = $user->getAuthorisedCategories($component, $action);
		}

		return $results[$key];

	}

	// is the user an event editor - i.e. can edit own and other events

	/**
	 * Test to see if user can create event within the specified category
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
	 *
	 * @return unknown
	 */
	public static
	function canCreateEvent($row, $user = null)
	{

		// TODO make this call a plugin
		if ($user == null)
		{
			$user = Factory::getUser();
		}

		$app    = Factory::getApplication();
		$input  = $app->input;

		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		if (!$authorisedonly)
		{
			if ($user->authorise('core.create', 'com_jevents'))
				return true;
			$allowedcats = JEVHelper::getAuthorisedCategories($user, 'com_jevents', 'core.create');

			// anon user event creation
			if ($user->id == 0 && count($allowedcats) == 0)
			{
				$jevtask = $input->getString("task");
				// This allows savenew and savecopy through too!
				if (strpos($jevtask, "icalevent.save") !== false || strpos($jevtask, "icalevent.apply") !== false)
				{
					$input->set("task", "icalevent.edit");
					$catids     = JEVHelper::rowCatids($row) ? JEVHelper::rowCatids($row) : array(intval($row->_catid));
					$catids     = implode(",", $catids);
					$app->triggerEvent('onGetAccessibleCategories', array(& $catids));
					$allowedcats = explode(",", $catids);
					$input->set("task", $jevtask);
				}
			}

			if (!in_array($row->_catid, $allowedcats))
			{
				return false;
			}
			// check multi cats too
			if (JEVHelper::rowCatids($row))
			{
				if (count(array_diff(JEVHelper::rowCatids($row), $allowedcats)))
				{
					return false;
				}
			}
		}
		else
		{
			// are we authorised to do anything with this category or calendar
			$jevuser = JEVHelper::getAuthorisedUser();
			if ($row->_icsid > 0 && $jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all")
			{
				$allowedcals = explode("|", $jevuser->calendars);
				if (!in_array($row->_icsid, $allowedcals))
					return false;
			}

			if ($row->_catid > 0 && $jevuser && $jevuser->categories != "" && $jevuser->categories != "all")
			{
				$allowedcats = explode("|", $jevuser->categories);
				if (!in_array($row->_catid, $allowedcats))
					return false;
				// check multi cats too
				if (JEVHelper::rowCatids($row))
				{
					if (count(array_diff(JEVHelper::rowCatids($row), $allowedcats)))
					{
						return false;
					}
				}
			}
		}

		return true;

	}

	static public
	function rowCatids(&$row)
	{

		if (isset($row->_catids))
		{
			if (isset($row->_catidsarray))
			{
				return $row->_catidsarray;
			}
			$catids = $row->_catids;
			if (is_string($catids) && strpos($catids, ",") > 0)
			{
				$catids = str_replace('"', '', $catids);
				$catids = explode(",", $catids);
			}
			if (!is_array($catids))
			{
				$catids = array($catids);
			}
			$catids            = ArrayHelper::toInteger($catids);
			$row->_catidsarray = $catids;

			return $catids;
		}

		return false;

	}

	// is the user an event publisher - i.e. can publish own OR other events

	/**
	 * Test to see if user can edit event
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
	 *
	 * @return unknown
	 */
	public static
	function canEditEvent($row, $user = null)
	{

		// store in static to save repeated database calls
		static $authdata_coreedit = array();
		static $authdata_editown = array();

		// TODO make this call a plugin
		if ($user == null)
		{
			$user = Factory::getUser();
		}

		if ($user->id == 0)
		{
			return false;
		}

		// are we authorised to do anything with this category or calendar
		$jevuser = JEVHelper::getAuthorisedUser();
		if ($row->_icsid > 0 && $jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all")
		{
			$allowedcals = explode("|", $jevuser->calendars);
			if (!in_array($row->_icsid, $allowedcals))
				return false;
		}

		if ($row->_catid > 0 && $jevuser && $jevuser->categories != "" && $jevuser->categories != "all")
		{
			$allowedcats = explode("|", $jevuser->categories);
			if (!in_array($row->_catid, $allowedcats))
				return false;
			// check multi cats too
			if (JEVHelper::rowCatids($row))
			{
				if (count(array_diff(JEVHelper::rowCatids($row), $allowedcats)))
				{
					return false;
				}
			}
		}
		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		if ($authorisedonly)
		{
			if ($jevuser && $jevuser->published)
			{
				// creator can edit their own event
				if ($jevuser->cancreate && $row->_created_by == $user->id)
				{
					return true;
				}
				else if ($jevuser->canedit)
				{
					return true;
				}
			}

			return false;
		}


		if (JEVHelper::isEventEditor())
		{
			// any category restrictions on this?
			// This involes TOO many database queries in Joomla - one per category which can be a LOT
			/*
			  $cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit');
			  $cats_own = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit.own');
			  if (in_array($row->_catid, $cats))
			  return true;
			  else if (in_array($row->_catid, $cats_own))
			  return true;
			  else return false;
			 */
			$key = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
			if (!isset($authdata_coreedit[$key]))
			{
				$authdata_coreedit[$key] = JEVHelper::authoriseCategories('core.edit', $key, $user);
			}
			if ($authdata_coreedit[$key])
			{
				return true;
			}
			else if ($user->id > 0 && $row->created_by() == $user->id)
			{
				if (!isset($authdata_editown[$key]))
				{
					$authdata_editown[$key] = JEVHelper::authoriseCategories('core.edit.own', $key, $user);
				}

				return $authdata_editown[$key];
			}

			// category settings trumps overall setting
			return false;

			return true;
		}
		// must stop anon users from editing any events
		else if ($user->id > 0 && $row->created_by() == $user->id)
		{

			if ($authorisedonly)
			{
				if ($jevuser)
				{
					if ($jevuser->published && $jevuser->cancreate)
					{
						return true;
					}
				}
				else
				{
					return false;
				}
			}


			// other users can always edit their own unless blocked by category
			// This involes TOO many database queries in Joomla - one per category which can be a LOT
			/*
			  $cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit');
			  $cats_own = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit.own');
			  if (in_array($row->_catid, $cats))
			  return true;
			  else if (in_array($row->_catid, $cats_own))
			  return true;
			 */
			$key = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
			if (!isset($authdata_coreedit[$key]))
			{
				$authdata_coreedit[$key] = JEVHelper::authoriseCategories('core.edit', $key, $user);
			}
			if ($authdata_coreedit[$key])
			{
				return true;
			}
			else
			{
				if (!isset($authdata_editown[$key]))
				{
					$authdata_editown[$key] = JEVHelper::authoriseCategories('core.edit.own', $key, $user);
				}

				return $authdata_editown[$key];
			}

			return false;
		}

		if ($user->id > 0 && $row->catid() > 0)
		{
			$key = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
			if (!isset($authdata_coreedit[$key]))
			{
				$authdata_coreedit[$key] = JEVHelper::authoriseCategories('core.edit', $key, $user);
			}

			return $authdata_coreedit[$key];
		}

		return false;

	}

	/**
	 * Test to see if user can edit own events
	 *
	 * @return unknown
	 */
	public static
	function canEditOwnEventNewEventOnlyCheck()
	{
		$params     = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!$params->get("authorisedonly", 0))
		{
			$juser      = Factory::getUser();
			$canEditOwn = $juser->authorise('core.edit.own', 'com_jevents');
		}
		else
		{
			// are we an authorised user
			$jevuser = JEVHelper::getAuthorisedUser();
			if ($jevuser)
			{
				$canEditOwn = $jevuser->cancreate;
			}
			else
			{
				$canEditOwn = false;
			}
		}


		return $canEditOwn;

	}

	// Fall back test to see if user can publish their own events based on config setting

	public static
	function isEventEditor()
	{

		static $isEventEditor;
		if (!isset($isEventEditor))
		{
			$isEventEditor = false;

			$user = JEVHelper::getAuthorisedUser();

			if (is_null($user))
			{
				$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					$juser = Factory::getUser();
					// Never allow unlogged in users to edit events - just in case someone tries to allow this
					if ($juser->id == 0)
					{
						return false;
					}
					//$isEventEditor = $juser->authorise('core.edit', 'com_jevents');

					if ($params->get("category_allow_deny", 1) == 0)
					{
						// this is too heavy on database queries - keep this in the file so that sites that want to use this approach can uncomment this block
						list($usec, $sec) = explode(" ", microtime());
						$time_start = (float) $usec + (float) $sec;
						if ($juser->get("id"))
						{
							$okcats = JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.edit');
							$juser  = Factory::getUser();
							if (count($okcats))
							{
								$dataModel = new JEventsDataModel();
								$dataModel->setupComponentCatids();

								$allowedcats = explode(",", $dataModel->accessibleCategoryList());
								$intersect   = array_intersect($okcats, $allowedcats);

								if (count($intersect) > 0)
								{
									$isEventEditor = true;
								}
							}
						}
						list ($usec, $sec) = explode(" ", microtime());
						$time_end = (float) $usec + (float) $sec;
					}
					else
					{
						$isEventEditor = $juser->authorise('core.edit', 'com_jevents');
						if ($isEventEditor)
						{
							$okcats = JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.edit');
							if (count($okcats) > 0)
							{
								$juser     = Factory::getUser();
								$dataModel = new JEventsDataModel();
								$dataModel->setupComponentCatids();

								$allowedcats = explode(",", $dataModel->accessibleCategoryList());
								$intersect   = array_intersect($okcats, $allowedcats);

								if (count($intersect) == 0)
								{
									$isEventEditor = false;
								}
							}
							else
							{
								$isEventEditor = false;
							}
						}
					}

				}
			}

			/*
			  $user = JEVHelper::getAuthorisedUser();
			  if (is_null($user)){
			  $params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			  $editorLevel= $params->get("jeveditor_level",20);
			  $juser = Factory::getUser();
			  if (JEVHelper::getGid($user)>=$editorLevel){
			  $isEventEditor = true;
			  }
			  }
			 */
			else if ($user->canedit)
			{
				$isEventEditor = true;
			}
			else if ($user->cancreate)
			{
				// User can create, lets check the DB for the Creator ID.
				$input = Factory::getApplication()->input;
				$ev_id  = $input->getInt('evid', 0);
				if ($ev_id > 0)
				{
					// Get the creator ID:
					$db = Factory::getDbo();
					$db->setQuery("SELECT created_by FROM #__jevents_vevent WHERE ev_id = " . $ev_id);
					$result = $db->loadResult();
					if ($result === $user->user_id)
					{
						$isEventEditor = true;
					}

				}
			}
		}

		return $isEventEditor;

	}

	// gets a list of categories for which this user is the admin

	static public
	function authoriseCategories($action, $catids, $user)
	{

		if (is_string($catids) && (strpos($catids, "[") === 0 || strpos($catids, '"') === 0))
		{
			$catids = json_decode($catids);
		}
		else if (is_string($catids) && strpos($catids, ",") > 0)
		{
			$catids = str_replace('"', '', $catids);
			$catids = explode(",", $catids);
		}
		if (!is_array($catids))
		{
			$catids = array(intval($catids));
		}
		$catids = ArrayHelper::toInteger($catids);
		$result = false; //count($catids)>0;
		foreach ($catids as $catid)
		{
			// this is an invalid category so skip it!
			if ($catid == 0)
				continue;
			$result = $user->authorise($action, 'com_jevents.category.' . $catid) ? true : false;
			if (!$result)
				return false;
		}

		return $result;

	}

	public static
	function categoryAdmin()
	{

		if (!JEVHelper::isEventPublisher())
			return false;
		$juser = Factory::getUser();

		$db = Factory::getDbo();
		// TODO make this query tighter to stop uers with ids starting with $juser->id from matching -
		// try using word boundaries RLIKE [[:<:]] and [[;>:]]  see http://dev.mysql.com/doc/refman/5.7/en/regexp.html
		$sql = "SELECT id FROM #__categories WHERE extension='com_jevents' AND params like ('%\"admin\":\"" . $juser->id . "\"%')";
		$db->setQuery($sql);
		$catids = $db->loadColumn();
		if (count($catids) > 0)
			return $catids;

		return false;

	}

	// is the user an event publisher - i.e. can publish own OR other events

	public static
	function isEventPublisher($strict = false)
	{

		static $isEventPublisher;
		if (!isset($isEventPublisher))
		{
			$isEventPublisher = array();
		}
		$type = $strict ? "strict" : "notstrict";
		if (!isset($isEventPublisher[$type]))
		{
			$isEventPublisher[$type] = false;

			$user = JEVHelper::getAuthorisedUser();
			if (is_null($user))
			{
				$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					$juser = Factory::getUser();

					if ($params->get("category_allow_deny", 1) == 0)
					{
						// this is too heavy on database queries - keep this in the file so that sites that want to use this approach can uncomment this block
						list($usec, $sec) = explode(" ", microtime());
						$time_start = (float) $usec + (float) $sec;
						if ($juser->get("id"))
						{
							$okcats = JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.edit.state');
							$juser  = Factory::getUser();
							if (count($okcats))
							{
								$dataModel = new JEventsDataModel();
								$dataModel->setupComponentCatids();

								$allowedcats = explode(",", $dataModel->accessibleCategoryList());
								$intersect   = array_intersect($okcats, $allowedcats);

								if (count($intersect) > 0)
								{
									$isEventPublisher[$type] = true;
								}
							}
						}
						list ($usec, $sec) = explode(" ", microtime());
						$time_end = (float) $usec + (float) $sec;
					}
					else
					{
						$isEventPublisher[$type] = $juser->authorise('core.edit.state', 'com_jevents');
						if ($isEventPublisher[$type])
						{
							$okcats = JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.edit.state');
							if (count($okcats) > 0)
							{
								$juser     = Factory::getUser();
								$dataModel = new JEventsDataModel();
								$dataModel->setupComponentCatids();

								$allowedcats = explode(",", $dataModel->accessibleCategoryList());
								$intersect   = array_intersect($okcats, $allowedcats);

								if (count($intersect) == 0)
								{
									$isEventPublisher[$type] = false;
								}
							}
							else
							{
								$isEventPublisher[$type] = false;
							}
						}
					}


				}
			}
			else if ($user->canpublishall)
			{
				$isEventPublisher[$type] = true;
			}
			else if (!$strict && $user->canpublishown)
			{
				$isEventPublisher[$type] = true;
			}

			Factory::getApplication()->triggerEvent('onIsEventPublisher', array($type, & $isEventPublisher[$type]));
		}


		return $isEventPublisher[$type];

	}

	/**
	 * Test to see if user can delete event
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
	 *
	 * @return unknown
	 */
	public static
	function canDeleteEvent($row, $user = null)
	{

		// store in static to save repeated database calls
		static $authdata_coredeleteall = array();

		// TODO make this call a plugin
		if ($user == null)
		{
			$user = Factory::getUser();
		}

		// are we authorised to do anything with this category or calendar
		$jevuser = JEVHelper::getAuthorisedUser();
		if ($row->_icsid > 0 && $jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all")
		{
			$allowedcals = explode("|", $jevuser->calendars);
			if (!in_array($row->_icsid, $allowedcals))
				return false;
		}

		if ($row->_catid > 0 && $jevuser && $jevuser->categories != "" && $jevuser->categories != "all")
		{
			$allowedcats = explode("|", $jevuser->categories);
			if (!in_array($row->_catid, $allowedcats))
				return false;
			// check multi cats too
			if (JEVHelper::rowCatids($row))
			{
				if (count(array_diff(JEVHelper::rowCatids($row), $allowedcats)))
				{
					return false;
				}
			}
		}
		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 1);
		if ($authorisedonly)
		{
			if (!$jevuser)
			{
				return false;
			}

			if (!is_null($jevuser) && $jevuser->candeleteall)
			{
				return true;
			}
			else if (!is_null($jevuser) && $jevuser->candeleteown && $row->created_by() == $user->id)
			{
				return true;
			}

			return false;
		}

		// This involes TOO many database queries in Joomla - one per category which can be a LOT
		/*
		  $cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.deleteall');
		  if (in_array($row->_catid, $cats))
		  return true;
		 */
		$key = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
		if (!isset($authdata_coredeleteall[$key]))
		{
			$authdata_coredeleteall[$key] = JEVHelper::authoriseCategories('core.deleteall', $key, $user);
		}
		if ($authdata_coredeleteall[$key])
		{
			return $authdata_coredeleteall[$key];
		}

		// can delete all?
		if (JEVHelper::isEventDeletor(true))
		{
			// any category restrictions on this?
			// This involes TOO many database queries in Joomla - one per category which can be a LOT
			/*
			  $cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.deleteall');
			  if (in_array($row->_catid, $cats))
			  return true;
			 */
			$key = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
			if (!isset($authdata_coredeleteall[$key]))
			{
				$authdata_coredeleteall[$key] = JEVHelper::authoriseCategories('core.deleteall', $key, $user);
			}
			if ($authdata_coredeleteall[$key])
			{
				return $authdata_coredeleteall[$key];
			}
		}

		// There seems to be a problem with category permissions - sometimes Joomla ACL set to yes in category but result is false!
		// fall back to being able to delete own events if a publisher
		if ($row->created_by() == $user->id)
		{
			$jevuser = JEVHelper::getAuthorisedUser();
			if (!is_null($jevuser))
			{
				return $jevuser->candeleteown;
			}
			// if a user can publish their own then cal delete their own too
			$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 1);
			$publishown     = $params->get("jevpublishown", 0);
			if (!$authorisedonly && ($publishown == 1 || JEVHelper::canPublishEvent($row, $user)))
			{
				return true;
			}
		}

		return false;

	}

	public static
	function isEventDeletor($strict = false)
	{

		static $isEventDeletor;
		if (!isset($isEventDeletor))
		{
			$isEventDeletor = array();
		}
		$type = $strict ? "strict" : "notstrict";
		if (!isset($isEventDeletor[$type]))
		{
			$isEventDeletor[$type] = false;

			$user = JEVHelper::getAuthorisedUser();
			if (is_null($user))
			{
				$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					$juser                 = Factory::getUser();
					$isEventDeletor[$type] = $juser->authorise('core.deleteall', 'com_jevents');
				}
			}
			else if ($user->candeleteall)
			{
				$isEventDeletor[$type] = true;
			}
			else if (!$strict && $user->candeleteown)
			{
				$isEventDeletor[$type] = true;
			}
		}

		return $isEventDeletor[$type];

	}

	/**
	 * Test to see if user can publish event
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
	 *
	 * @return unknown
	 */
	public static
	function canPublishEvent($row, $user = null)
	{

		// store in static to save repeated database calls
		static $authdata_editstate = array();

		// TODO make this call a plugin
		if ($user == null)
		{
			$user = Factory::getUser();
		}
		// are we authorised to do anything with this category or calendar
		$jevuser        = JEVHelper::getAuthorisedUser();
		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		if ($authorisedonly)
		{
			if (!$jevuser)
			{
				// paid subs plugin may override this
				if ($row->created_by() == $user->id && $user->id > 0)
				{
					$frontendPublish = JEVHelper::isEventPublisher(false);

					return $frontendPublish;
				}

				return false;
			}

			if ($row->_icsid > 0 && $jevuser && $jevuser->calendars != "" && $jevuser->calendars != "all")
			{
				$allowedcals = explode("|", $jevuser->calendars);
				if (!in_array($row->_icsid, $allowedcals))
					return false;
			}

			if ($row->_catid > 0 && $jevuser && $jevuser->categories != "" && $jevuser->categories != "all")
			{
				$allowedcats = explode("|", $jevuser->categories);
				if (!in_array($row->_catid, $allowedcats))
					return false;
				// check multi cats too
				if (JEVHelper::rowCatids($row))
				{
					if (count(array_diff(JEVHelper::rowCatids($row), $allowedcats)))
					{
						return false;
					}
				}
			}
			if ($jevuser->canpublishall)
			{
				return true;
			}
			if ($row->created_by() == $user->id && $jevuser->canpublishown)
			{
				return true;
			}

			return false;
		}

		// can publish all?
		if (JEVHelper::isEventPublisher(true))
		{
			// This involes TOO many database queries in Joomla - one per category which can be a LOT
			/*
			  $cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit.state');
			  if (in_array($row->_catid, $cats))
			  return true;
			 */
			// allow multi-categories
			$key                      = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
			$authdata_editstate[$key] = JEVHelper::authoriseCategories('core.edit.state', $key, $user);

			return $authdata_editstate[$key];

			return true;
		}
		else if ($row->created_by() == $user->id)
		{

			// Use generic helper method that can call the plugin to see if user can publish any events
			$isEventPublisher = JEVHelper::isEventPublisher();
			if ($isEventPublisher)
				return true;

			$jevuser = JEVHelper::getAuthorisedUser();
			if (!is_null($jevuser))
			{
				return $jevuser->canpublishown;
			}

			$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 1);
			$publishown     = $params->get("jevpublishown", 0);
			if (!$authorisedonly && $publishown == 1)
			{
				return true;
			}
			else if (!$authorisedonly && $publishown == 2)
			{
				$publishown = JEVHelper::canPublishOwnEvents($row->ev_id());
				if ($publishown)
				{
					return true;
				}
			}

			// This involes TOO many database queries in Joomla - one per category which can be a LOT
			/*
			  $cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit.state');
			  if (in_array($row->_catid, $cats))
			  return true;
			 */
			$key = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
			if (!isset($authdata_editstate[$key]))
			{
				$authdata_editstate[$key] = JEVHelper::authoriseCategories('core.edit.state', $key, $user);
			}

			return $authdata_editstate[$key];
		}
		if ($user->id > 0 && $row->catid() > 0)
		{
			$key = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
			if (!isset($authdata_editstate[$key]))
			{
				$authdata_editstate[$key] = JEVHelper::authoriseCategories('core.edit.state', $key, $user);
			}

			return $authdata_editstate[$key];
		}

		return false;

	}

	/*
	 * Our own version that caches the results - the Joomla one doesn't!!!
	 */

	public static
	function canPublishOwnEvents($evid, $vevent = false)
	{

		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 1);
		$publishown     = $params->get("jevpublishown", 0);
		$canPublishOwn  = false;

		$jevuser = JEVHelper::getAuthorisedUser();
		$user    = Factory::getUser();

		if (!$authorisedonly && $publishown)
		{

			// can publish all?
			if (JEVHelper::isEventPublisher(true))
			{
				return true;
			}
			else if ($evid == 0 && $publishown == 1)
			{
				return true;
			}

			if ($evid == 0 && $publishown == 2)
			{
				if ($params->get("category_allow_deny", 1) == 0)
				{
					$okcats = JEVHelper::getAuthorisedCategories($user, 'com_jevents', 'core.edit.state.own');
					if (isset($vevent->catid))
					{
						$catids = is_array($vevent->catid) ? $vevent->catid : array($vevent->catid);
						$catids = array_intersect($catids, $okcats);

						return count($catids) > 0;
					}
				}
				else
				{
					$canPublishOwn = $user->authorise('core.edit.state.own', 'com_jevents');
					if ($canPublishOwn)
					{
						$okcats = JEVHelper::getAuthorisedCategories($user, 'com_jevents', 'core.edit.state.own');
						if (isset($vevent->catid))
						{
							$catids = is_array($vevent->catid) ? $vevent->catid : array($vevent->catid);
							$catids = array_intersect($catids, $okcats);

							return count($catids) > 0;
						}
					}
				}
			}
			else
			{
				$dataModel  = new JEventsDataModel("JEventsAdminDBModel");
				$queryModel = new JEventsDBModel($dataModel);

				$evid      = intval($evid);
				$testevent = $queryModel->getEventById($evid, 1, "icaldb");
				if ($testevent->ev_id() == $evid && $testevent->created_by() == $user->id)
				{
					if ($publishown == 2)
					{
						if ($params->get("category_allow_deny", 1) == 0)
						{
							$okcats = JEVHelper::getAuthorisedCategories($user, 'com_jevents', 'core.edit.state.own');
							$catids = $testevent->catids();
							if (!is_array($catids))
							{
								$catids = array($testevent->catid());
							}
							$catids = array_intersect($catids, $okcats);

							return count($catids) > 0;
						}
						else
						{
							$canPublishOwn = $user->authorise('core.edit.state.own', 'com_jevents');
							if ($canPublishOwn)
							{
								$okcats = JEVHelper::getAuthorisedCategories($user, 'com_jevents', 'core.edit.state.own');
								$catids = $testevent->catids();
								if (!is_array($catids))
								{
									$catids = array($testevent->catid());
								}
								$catids = array_intersect($catids, $okcats);

								return count($catids) > 0;
							}

							return false;
						}
					}
					else
					{
						return true;
					}

				}
			}
		}

		if ($authorisedonly && $jevuser && $jevuser->canpublishown)
		{
			if ($evid == 0)
			{
				return true;
			}
			$dataModel  = new JEventsDataModel("JEventsAdminDBModel");
			$queryModel = new JEventsDBModel($dataModel);

			$evid      = intval($evid);
			$testevent = $queryModel->getEventById($evid, 1, "icaldb");
			if ($testevent->ev_id() == $evid && $testevent->created_by() == $user->id)
			{
				return true;
			}
		}
		elseif ($canPublishOwn)
		{
			return true;
		}

		return false;

	}

	/**
	 * Returns contact details or user details as fall back
	 *
	 * @param int id        key of user
	 * @param string attrib    Requested attribute of the user object
	 *
	 * @return mixed row    Attribute or row object
	 */
	public static
	function getContact($id, $attrib = 'Object')
	{

		$db = Factory::getDbo();

		static $rows = array();

		if ($id <= 0)
		{
			return null;
		}

		if (!isset($rows[$id]))
		{
			$user      = Factory::getUser();
			$rows[$id] = null;
			$query     = "SELECT ju.id, ju.name, ju.username, ju.sendEmail, ju.email, cd.name as contactname, "
				. ' CASE WHEN CHAR_LENGTH(cd.alias) THEN CONCAT_WS(\':\', cd.id, cd.alias) ELSE cd.id END as slug, '
				. ' CASE WHEN CHAR_LENGTH(cat.alias) THEN CONCAT_WS(\':\', cat.id, cat.alias) ELSE cat.id END AS catslug '
				. " \n FROM #__users AS ju"
				. "\n LEFT JOIN #__contact_details AS cd ON cd.user_id = ju.id "
				. "\n LEFT JOIN #__categories AS cat ON cat.id = cd.catid "
				. "\n WHERE block ='0'"
				. "\n AND cd.published =1 "
				. "\n AND cd.access  " . ' IN (' . JEVHelper::getAid($user) . ')'
				. "\n AND cat.access  " . ' IN (' . JEVHelper::getAid($user) . ')'
				. "\n AND ju.id = " . $id;

			$db->setQuery($query);
			$rows[$id] = $db->loadObject();
			if (is_null($rows[$id]))
			{
				// if the user has been deleted then try to suppress the warning
				// this causes a problem in Joomla 2.5.1 on some servers
				$rows[$id] = JEVHelper::getUser($id);
			}
		}

		if ($attrib == 'Object')
		{
			return $rows[$id];
		}
		elseif (isset($rows[$id]->$attrib))
		{
			return $rows[$id]->$attrib;
		}
		else
		{
			return null;
		}

	}

	/**
	 * Get an user object.
	 *
	 * JEvents version that doesn't throw error message when user doesn't exist
	 *
	 * Returns the global {@link User} object, only creating it if it doesn't already exist.
	 *
	 * @param   integer $id The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 *
	 * @return  User object
	 *
	 * @see     User
	 * @since   11.1
	 */
	public static
	function getUser($id = null)
	{

		if (is_null($id) || $id == 0)
		{
			// CB sometimes messes up with the session data when logging out - so this is a safe workaround!
			return User::getInstance();
		}
		else
		{
			static $tested = array();
			if (!isset($tested[$id]))
			{
				// Initialise some variables
				$db    = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__users'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($id));
				$db->setQuery($query, 0, 1);
				$tested[$id] = $db->loadResult();
			}
			if (!$tested[$id])
			{
				return false;
			}

			return Factory::getUser($id);
		}

	}

	public static
	function componentStylesheet($view, $filename = 'events_css.css')
	{


		if (!isset($view->jevlayout))
		{
			if (method_exists($view, "getViewName"))
				$view->jevlayout = $view->getViewName();
			else if (method_exists($view, "getTheme"))
				$view->jevlayout = $view->getTheme();
		}

		if (file_exists(JPATH_BASE . '/' . 'templates' . '/' . Factory::getApplication()->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT . '/' . $view->jevlayout . '/' . "assets" . '/' . "css" . '/' . $filename))
		{
			JEVHelper::stylesheet($filename, 'templates/' . Factory::getApplication()->getTemplate() . '/html/' . JEV_COM_COMPONENT . '/' . $view->jevlayout . "/assets/css/");
		}
		else
		{
			JEVHelper::stylesheet($filename, 'components/' . JEV_COM_COMPONENT . "/views/" . $view->jevlayout . "/assets/css/");
		}

	}

	static public
	function stylesheet($file, $path = "")
	{

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		// HTMLHelper::stylesheet($path . $file);
		//stylesheet($file, $attribs = array(), $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
		// no need to find browser specific versions
		// $includes = HTMLHelper::stylesheet($path . $file, array(), false, true, false);

		$document = Factory::getDocument();
		// No need for CSS files in XML file
		if ($document->getType() == 'feed')
		{
			return;
		}

		$version = JEventsVersion::getInstance();
		$release = $version->get("RELEASE", "1.0.0");
		$includes = HTMLHelper::stylesheet($path . $file,
			array(
				'relative'      => false,
				'pathOnly'      => false,
				'detectBrowser' => true,
				'detectDebug'   => true,
				'version'       => 'v=' . $release
			)
		);

	}

	/**
	 *
	 * Joomla 1.6 compatability functions
	 *
	 */
	static public
	function getGid($user = null)
	{

		if (is_null($user))
		{
			$user = Factory::getUser();
		}

		return max(Access::getGroupsByUser($user->id));  // RSH trying to get a gid for J!1.6

	}

	static public
	function getUserType($user = null)
	{

		if (is_null($user))
		{
			$user = Factory::getUser();
		}
		$groups = $user->groups;  // RSH 10/17/10 - Get groups, sort them, get the last one, return the value
		asort($groups);
		$last_group = end($groups);

		return ($last_group == 'Super Users') ? "Super Administrator" : $last_group;

	}

	//Custom CSS File Helper file - Single place to define location, preparing to move to media folder

	static public function CustomCSSFile()
	{

		$filePath = JPATH_ROOT . '/components/com_jevents/assets/css/jevcustom.css';

		return $filePath;

	}

	/*
	 * Load JEvents Custom CSS file if any
	 */
	static public
	function loadCustomCSS()
	{

		//Check for JEvents Custom CSS file
		if (File::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
		{
			JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		}
	}

	static public
	function script($file, $path = "", $framework = false, $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
	{

		$includes = null;
		// load jQuery versions if present
		if (strpos($file, "JQ.js") == false)
		{
			$jqfile = str_replace(".js", "JQ.js", $file);
			if (HTMLHelper::script($path . $jqfile,
					array(
						'relative'      => false,
						'pathOnly'      => true
					)
			))
			{
				$file = $jqfile;
			}
		}

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		//HTMLHelper::script($path . $file);
		//public static function script($file, $framework = false, $relative = false, $path_only = false, $detect_browser = true, $detect_debug = true)
		// no need to find browser specific versions
		//$includes = HTMLHelper::script($path . $file, $framework, $relative, true, $detect_browser, $detect_debug);

		$version = JEventsVersion::getInstance();
		$release = $version->get("RELEASE", "1.0.0");

		// New version that must be used for Joomla 4.0 but works in Joomla 3.9
		$includes = HTMLHelper::script($path . $file,
			array(
				'relative'      => $relative,
			    'pathOnly'      => $path_only,
				'detectBrowser' => $detect_browser,
				'detectDebug'   => $detect_debug,
				'version'       => 'v=' . $release
			)
		);
	}

	static public
	function setupJoomla160()
	{

	}

	static public
	function getBaseAccess()
	{

		// Store the ical in the registry so we can retrieve the access level
		$registry = JevRegistry::getInstance("jevents");
		$icsfile  = $registry->get("jevents.icsfile", false);
		if ($icsfile)
		{
			return $icsfile->access;
		}
		static $base;
		if (!isset($base))
		{
			// NB this method is no use if you delete the public access level - it assumes that 1 always exists!!!
			//$levels = Access::getAuthorisedViewLevels(0);
			$levels = array();
			if (count($levels) > 0)
			{
				$base = $levels[0];
			}
			else
			{
				// Get a database object.
				$db = Factory::getDbo();

				// Set the query for execution.
				$db->setQuery("SELECT id FROM #__viewlevels order by ordering limit 1");
				$base = $db->loadResult();
			}
		}

		return $base;

	}

	static public
	function imagesite($img, $text)
	{

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		return HTMLHelper::_('image', 'system/' . $img, $text, null, true);

	}

	public static
	function ConditionalFields($element, $component)
	{

		$conditions = (string) $element["conditional"];
		if (!$conditions)
		{
			return;
		}
		$conditional = (string) $element['name'];
		$condlabel   = $element['label'];
		if ($conditional == "creator")
		{
			$conditional = "jev_creatorid";
		}
		if ($conditional == "location")
		{
			$conditional = "evlocation";
		}
		if (strpos("@", $conditional) !== false)
		{
			$conditional = str_replace("@", "_", $conditional);
		}
		$condarray    = (string) $element['conditions'];
		$condtype     = (string) $element['type'];
		$fielddefault = (string) $element['default'];
		$multi        = (string) $element['multiple'];
		if ($component == "jevents.edit.icalevent")
		{
			$condparam = "";
		}
		elseif ($component == "com_config.component" || strpos($component, "com_jevent.config") !== false)
		{
			$condparam = "jform_";
		}
		else
		{
			$condparam = "jform_params_";
		}
		$fieldparam = ($condtype == "jevcf") ? "" : $condparam;

		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$conditionarray = explode(",", $condarray);
		if (in_array($params->get($conditions, "default"), $conditionarray) == true && $component != "com_config.component")
		{
			$conditionarray[] = "global";
		}
		$condarray         = "'" . (string) implode("','", $conditionarray) . "'";
		$fielddefaultarray = "'" . (string) str_replace(",", "','", $fielddefault) . "'";

		HTMLHelper::script('components/' . JEV_COM_COMPONENT . '/assets/js/conditionalfields.js');

		$script = <<<SCRIPT
	jQuery(document).on('ready', function() {
		jevConditional.setupJevConditions('$conditional','$fielddefault', '$condlabel' ,'$condparam', '$conditions', '$fieldparam', Array($condarray), Array($fielddefaultarray));
	});
SCRIPT;

		$document = Factory::getDocument();
		$document->addScriptDeclaration($script);

	}

	public static
	function processLiveBookmmarks()
	{

		$cfg = JEVConfig::getInstance();
		if ($cfg->get('com_rss_live_bookmarks'))
		{
			$Itemid   =Factory::getApplication()->input->getInt('Itemid', 0);
			$rssmodid = $cfg->get('com_rss_modid', 0);
			// do not use Route since this creates .rss link which normal sef can't deal with
			$rssLink = 'index.php?option=' . JEV_COM_COMPONENT . '&amp;task=modlatest.rss&amp;format=feed&amp;type=rss&amp;Itemid=' . $Itemid . '&amp;modid=' . $rssmodid;
			$rssLink = Uri::root() . $rssLink;

			if (method_exists(Factory::getDocument(), "addHeadLink"))
			{
				$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
				Factory::getDocument()->addHeadLink($rssLink, 'alternate', 'rel', $attribs);
			}

			$rssLink = 'index.php?option=' . JEV_COM_COMPONENT . '&amp;task=modlatest.rss&amp;format=feed&amp;type=atom&amp;Itemid=' . $Itemid . '&amp;modid=' . $rssmodid;
			$rssLink = Uri::root() . $rssLink;
			//$rssLink = Route::_($rssLink);
			if (method_exists(Factory::getDocument(), "addHeadLink"))
			{
				$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
				Factory::getDocument()->addHeadLink($rssLink, 'alternate', 'rel', $attribs);
			}
		}

	}

	/**
	 * Get filter values from database based on URL
	 */
	public static
	function getFilterValues()
	{

		//  $session = Factory::getSession();
		// $session->set('name', "value");

		$session = Factory::getSession();
		echo $session->get('name');


		// Only save/delete filters for non-guests
		if (Factory::getUser()->id > 0)
		{
			$deletefilter = Factory::getApplication()->input->getInt("deletefilter", 0);
			if ($deletefilter)
			{
				$db = Factory::getDbo();
				$db->setQuery("DELETE FROM #__jevents_filtermap where fid = " . $db->quote($deletefilter) . " AND userid=" . intval(Factory::getUser()->id));
				$db->execute();

				return;
			}

			// This is new experimental code
			$fid = Factory::getApplication()->input->getString("jfilter", '');
			if ($fid != "")
			{
				// This isn't high security but best to be safe to make sure filter belongs to this user
				$db = Factory::getDbo();
				$db->setQuery("SELECT * FROM #__jevents_filtermap where fid = " . $db->quote($fid) . " AND userid=" . intval(Factory::getUser()->id));
				$filter = $db->loadObject();

				if ($filter)
				{
					$filtervars = json_decode($filter->filters);
					if (is_object($filtervars))
					{
						$filtervars = get_object_vars($filtervars);
					}
					//var_dump($filtervars);
					if (is_array($filtervars))
					{
						foreach ($filtervars as $fvk => $fvv)
						{
							if (strpos($fvk, "_fv") > 0)
							{
								Factory::getApplication()->input->set($fvk, $fvv);
							}
						}
					}

					// Also set the saved filter results
					Factory::getApplication()->input->set('filtername', $filter->name);
				}
			}
			else
			{
				JEVHelper::setFilterValues();
			}
		}
		else
		{
			JEVHelper::setFilterValues();
		}

	}

	/**
	 * Set filter values in database based on URL and redirect
	 */
	public static
	function setFilterValues()
	{

		// Only save filters for non-guests
		if (!Factory::getUser()->id)
		{
			return;
		}

		$filtername = Factory::getApplication()->input->getString("filtername", '');
		$modid      = Factory::getApplication()->input->getInt("modid", 0);

		if ($filtername == "")
		{
			return;
		}

		$filtervars = array();

		$input = Factory::getApplication()->input->getArray();
		if (is_array($input) && count($input) > 0)
		{
			foreach ($input as $fvk => $fvv)
			{
				if (strpos($fvk, "_fv") > 0)
				{
					$filtervars[$fvk] = $fvv;
				}
			}
		}

		if (count($filtervars) > 0 && Factory::getApplication()->input->getMethod() == "POST")
		{
			ksort($filtervars);
			$filtervars = json_encode($filtervars);

			$db = Factory::getDbo();
			// check for any matching filters first
			$md5 = md5($filtervars);

			$db->setQuery("SELECT fid, filters  FROM #__jevents_filtermap where md5 = " . $db->quote($md5));
			$filters = $db->loadAssocList("fid", "filters");

			$db->setQuery("SELECT fid  FROM #__jevents_filtermap where name = " . $db->quote($filtername));
			$fid = intval($db->loadResult("fid"));

			if ($fid)
			{
				$db->setQuery("REPLACE INTO #__jevents_filtermap (fid, filters, md5, userid, name, andor, modid) VALUES ($fid," . $db->quote($filtervars) . "," . $db->quote($md5) . "," . Factory::getUser()->id . "," . $db->quote($filtername) . ",0," . $modid . ")");
				$db->execute();
			}
			else if (!in_array($filtervars, $filters))
			{
				$db->setQuery("INSERT INTO #__jevents_filtermap (filters, md5, userid, name, andor, modid) VALUES (" . $db->quote($filtervars) . "," . $db->quote($md5) . "," . Factory::getUser()->id . "," . $db->quote($filtername) . ",0," . $modid . ")");
				$db->execute();
			}
			else
			{
				// has name changed!
			}
		}

	}

	public static
	function parameteriseJoomlaCache()
	{

		// If Joomla! caching is enabled then we have to manage progressive caching
		// and ensure that session data is taken into account.
		$conf = Factory::getConfig();
		if ($conf->get('caching', 1))
		{
			// Joomla  3.0 safe cache parameters
			$safeurlparams = array('catids'  => 'STRING', 'Itemid' => 'STRING', 'task' => 'STRING',
			                       'jevtask' => 'STRING', 'jevcmd' => 'STRING', 'view' => 'STRING', 'layout' => 'STRING',
			                       'evid'    => 'INT', 'modid' => 'INT', 'year' => 'INT', 'month' => 'INT', 'day' => 'INT',
			                       'limit'   => 'UINT', 'limitstart' => 'UINT', 'jfilter' => 'STRING', 'em' => 'STRING',
			                       'em2'     => 'STRING', 'pop' => 'UINT');

			$app    = Factory::getApplication();
			$input  = $app->input;

			$filtervars = $input->getArray(array());

			if (is_array($filtervars))
			{
				foreach ($filtervars as $fvk => $fvv)
				{
					if (strpos($fvk, "_fv") > 0)
					{
						if (is_array($fvv))
						{
							$safeurlparams[$fvk] = "ARRAY";
						}
						else
						{
							$safeurlparams[$fvk] = "STRING";
							//echo $fvk."= ".$fvv."<br/>";
						}
					}
				}
			}

			$session          = Factory::getSession();
			$sessionregistry  = $session->get('registry');
			$sessionArray     = isset($sessionregistry) ? $sessionregistry->toArray() : false;
			$sessionArrayData = array();
			if (is_array($sessionArray))
			{
				$specialcount = 0;
				foreach ($sessionArray as $sak => $sav)
				{
					if (strpos($sak, "_fv_ses") > 0)
					{
						$sessionArrayData[$sak] = $sav;
						$specialcount           += (($sak == "published_fv_ses" || $sak == "justmine_fv_ses") && $sav == 0) ? 1 : 0;
					}
				}
				// special case when published and justmine the only filters and these are the default values
				if (count($sessionArrayData) == 2 && $specialcount == 2)
				{
					$sessionArrayData = array();
				}
			}
			if (count($sessionArrayData) > 0)
			{
				$safeurlparams["sessionArray"] = "STRING";
				//var_dump($sessionArrayData);
				$input->set("sessionArray", md5(serialize($sessionArrayData)));

				// if we have session data then stop progressive caching
				if ($conf->get('caching', 1) == 2)
				{
					$conf->set('caching', 1);
				}

				// If we have session data then need to block page caching too!!
				// Cache::getInstance('page', $options); doesn't give an instance its always a NEW copy
				$cache_plg  = PluginHelper::getPlugin('system', 'cache');

				$observers  = @$app->get("_observers");
				if ($observers && is_array($observers))
				{
					foreach ($observers as $observer)
					{
						if (is_object($observer) && get_class($observer) == "plgSystemCache")
						{
							$pagecache = @$observer->get("_cache");
							if ($pagecache)
							{
								$pagecache->setCaching(false);
							}
							break;
						}
					}
				}
			}

			if ($input->getCmd("em") || $input->getCmd("em2"))
			{
				// If we have RSVP PRo data then need to block page caching too!!
				// Cache::getInstance('page', $options); doesn't give an instance its always a NEW copy
				$cache_plg  = PluginHelper::getPlugin('system', 'cache');
				$observers  = @$app->get("_observers");
				if ($observers && is_array($observers))
				{
					foreach ($observers as $observer)
					{
						if (is_object($observer) && get_class($observer) == "plgSystemCache")
						{
							$pagecache = @$observer->get("_cache");
							if ($pagecache)
							{
								$pagecache->setCaching(false);
							}
							break;
						}
					}
				}
			}

			if (!empty($app->registeredurlparams))
			{
				$registeredurlparams = $app->registeredurlparams;
			}
			else
			{
				$registeredurlparams = new stdClass;
			}

			foreach ($safeurlparams as $key => $value)
			{
				// Add your safe url parameters with variable type as value {@see InputFilter::clean()}.
				$registeredurlparams->$key = $value;
			}

			$app->registeredurlparams = $registeredurlparams;
		}

	}

	public static
	function iCalMailGenerator($row, $n_extras, $ics_method = "PUBLISH")
	{

		$m_ev = $n_extras["m_ev"];

		if ($ics_method == "CANCEL")
		{
			$status = "CANCELLED";
		}
		if (File::exists(JPATH_SITE . "/plugins/jevents/jevnotify/"))
		{
			//If using JEvents notify plugin we need to load it for the processing of data.
			JLoader::register('JEVNotifyHelper', JPATH_SITE . "/plugins/jevents/jevnotify/helper.php");
		}

		$icalEvents = array($row);
		if (ob_get_contents())
			ob_end_clean();
		$html   = "";
		$params = ComponentHelper::getParams("com_jevents");

		if ($params->get('outlook2003icalexport'))
			$html .= "BEGIN:VCALENDAR\r\nPRODID:JEvents 3.1 for Joomla//EN\r\n";
		else
			$html .= "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:JEvents 3.1 for Joomla//EN\r\n";

		$html .= "CALSCALE:GREGORIAN\r\nMETHOD:" . $ics_method . "\r\n";
		if (isset($status))
		{
			$html .= "STATUS:" . $status . "\r\n";
		}
		if (!empty($icalEvents))
		{

			ob_start();
			$tzid = self::vtimezone($icalEvents);
			$html .= ob_get_clean();

			// Build Exceptions dataset - all done in big batches to save multiple queries
			$exceptiondata = array();
			$ids           = array();
			foreach ($icalEvents as $a)
			{
				$ids[] = $a->ev_id();
				if (count($ids) > 100)
				{
					$db = Factory::getDbo();
					$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (" . implode(",", $ids) . ")");
					$rows = $db->loadObjectList();
					foreach ($rows as $row)
					{
						if (!isset($exceptiondata[$row->eventid]))
						{
							$exceptiondata[$row->eventid] = array();
						}
						$exceptiondata[$row->eventid][$row->rp_id] = $row;
					}
					$ids = array();
				}
			}
			// mop up the last ones
			if (count($ids) > 0)
			{
				$db = Factory::getDbo();
				$db->setQuery("SELECT * FROM #__jevents_exception where eventid IN (" . implode(",", $ids) . ")");
				$rows = $db->loadObjectList();
				foreach ($rows as $row)
				{
					if (!isset($exceptiondata[$row->eventid]))
					{
						$exceptiondata[$row->eventid] = array();
					}
					$exceptiondata[$row->eventid][$row->rp_id] = $row;
				}
			}

			// make sure the array is now reindexed for the sake of the plugins!
			$icalEvents = array_values($icalEvents);

			// Call plugin on each event
			ob_start();
			JEVHelper::onDisplayCustomFieldsMultiRow($icalEvents);
			ob_end_clean();

			foreach ($icalEvents as $a)
			{
				//See if we are a master event?
				// if event has repetitions I must find the first one to confirm the dates
				if ($a->hasrepetition())
				{
					$a = $a->getOriginalFirstRepeat();
				}
				if (!$a)
					continue;
				if ($m_ev != 0)
				{
					if (!isset($row->uid))
					{
						$row = $a;
					}

					$html .= "BEGIN:VEVENT\r\n";
					$html .= "UID:" . $row->uid() . "\r\n";
					$html .= "CATEGORIES:" . $row->catname() . "\r\n";
					if (!empty($row->_class))
						$html .= "CLASS:" . $row->_class . "\r\n";
					$html .= "SUMMARY:" . $row->title() . "\r\n";
					if ($a->location() != "")
					{
						if (!is_numeric($row->location()))
						{
							$html .= "LOCATION:" . self::wraplines(self::replacetags($row->location())) . "\r\n";
						}
						else if (isset($row->_loc_title))
						{
							$html .= "LOCATION:" . self::wraplines(self::replacetags($row->_loc_title)) . "\r\n";
						}
						else
						{
							$html .= "LOCATION:" . self::wraplines(self::replacetags($row->location())) . "\r\n";
						}
					}

					$ilink = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), true, $params->get('default_itemid', 0));
					$iuri  = Uri::getInstance(Uri::base());
					$iroot = $iuri->toString(array('scheme', 'host', 'port'));
					$html .= "URL;VALUE=URI:" . self::wraplines($iroot . Route::_($ilink, true, -1)) . "\r\n";

					// We Need to wrap this according to the specs
					$html .= self::setDescription($row->content()) . "\r\n";

					if ($a->hasContactInfo())
						$html .= "CONTACT:" . self::replacetags($row->contact_info()) . "\r\n";
					if ($a->hasExtraInfo())
						$html .= "X-EXTRAINFO:" . self::wraplines(self::replacetags($row->_extra_info)) . "\r\n";
					$user = Factory::getUser($row->created_by());

					$html         .= "ORGANIZER;CN=" . $user->name . ":MAILTO:" . $user->email . "\r\n";
					$alldayprefix = "";
					// No doing true timezones!
					if ($tzid == "" && is_callable("date_default_timezone_set"))
					{
						// UTC!
						$start = $row->getUnixStartTime();
						$end   = $row->getUnixEndTime();

						// in case the first repeat has been changed
						if (array_key_exists($row->_eventid, $exceptiondata) && array_key_exists($row->rp_id(), $exceptiondata[$row->_eventid]))
						{
							$start = JevDate::strtotime($exceptiondata[$row->_eventid][$a->rp_id()]->oldstartrepeat);
						}

						// Change timezone to UTC
						$current_timezone = date_default_timezone_get();

						// If all day event then don't show the start time or end time either
						if ($row->alldayevent())
						{
							$alldayprefix = ";VALUE=DATE";
							$startformat  = "Ymd";
							$endformat    = "Ymd";

							// add 10 seconds to make sure its not midnight the previous night
							$start += 10;
							$end   += 10;
						}
						else
						{
							date_default_timezone_set("UTC");

							$startformat = "Ymd\THis";
							$endformat   = "Ymd\THis";
						}

						// Do not use JevDate version since this sets timezone to config value!
						$start = date($startformat, $start);
						$end   = date($endformat, $end);

						$stamptime = date("Ymd\THis", time());

						// Change back
						date_default_timezone_set($current_timezone);
					}
					else
					{
						$start = $row->getUnixStartTime();
						$end   = $row->getUnixEndTime();

						// If all day event then don't show the start time or end time either
						if ($row->alldayevent())
						{
							$alldayprefix = ";VALUE=DATE";
							$startformat  = "%Y%m%d";
							$endformat    = "%Y%m%d";

							// add 10 seconds to make sure its not midnight the previous night
							$start += 10;
							$end   += 10;
						}
						else
						{
							$startformat = "Ymd\THis";
							$endformat   = "Ymd\THis";
						}

						$start = JevDate::strftime($startformat, $start);
						$end   = JevDate::strftime($endformat, $end);

						if (is_callable("date_default_timezone_set"))
						{
							// Change timezone to UTC
							$current_timezone = date_default_timezone_get();
							date_default_timezone_set("UTC");
							$stamptime = date("Ymd\THis", time());
							// Change back
							date_default_timezone_set($current_timezone);
						}
						else
						{
							$stamptime = date("Ymd\THis", time());
						}

						// in case the first repeat is changed
						if (array_key_exists($row->_eventid, $exceptiondata) && array_key_exists($row->rp_id(), $exceptiondata[$a->_eventid]))
						{
							$start = JevDate::strftime($startformat, JevDate::strtotime($exceptiondata[$a->_eventid][$a->rp_id()]->oldstartrepeat));
						}
					}

					$html .= "DTSTAMP:" . $stamptime . "\r\n";
					if ($row->alldayevent())
					{
						$html .= "DTSTART$alldayprefix:" . $start . "\r\n";
					}
					else
					{
						$html .= "DTSTART$tzid$alldayprefix:" . $start . "\r\n";
					}
					// events with no end time don't give a DTEND
					if (!$a->noendtime())
					{
						$html .= "DTEND$tzid$alldayprefix:" . $end . "\r\n";
					}
					$html .= "SEQUENCE:" . $row->_sequence . "\r\n";
					if ($row->hasrepetition())
					{
						$html .= 'RRULE:';


						// TODO MAKE SURE COMPAIBLE COMBINATIONS
						$html .= 'FREQ=' . $row->_freq;
						if ($row->_until != "" && $row->_until != 0)
						{
							// Do not use JevDate version since this sets timezone to config value!
							// GOOGLE HAS A PROBLEM WITH 235959!!!
							$html .= ';UNTIL=' . date("Ymd\T000000\Z", $a->_until + 86400);
						}
						else if ($row->_count != "")
						{
							$html .= ';COUNT=' . $row->_count;
						}
						if ($row->_rinterval != "")
							$html .= ';INTERVAL=' . $row->_rinterval;
						if ($row->_freq == "DAILY")
						{

						}
						else if ($row->_freq == "WEEKLY")
						{
							if ($row->_byday != "")
								$html .= ';BYDAY=' . $row->_byday;
						}
						else if ($row->_freq == "MONTHLY")
						{
							if ($row->_bymonthday != "")
							{
								$html .= ';BYMONTHDAY=' . $row->_bymonthday;
								if ($row->_byweekno != "")
									$html .= ';BYWEEKNO=' . $row->_byweekno;
							}
							else if ($row->_byday != "")
							{
								$html .= ';BYDAY=' . $row->_byday;
								if ($row->_byweekno != "")
									$html .= ';BYWEEKNO=' . $row->_byweekno;
							}
						}
						else if ($row->_freq == "YEARLY")
						{
							if ($row->_byyearday != "")
								$html .= ';BYYEARDAY=' . $row->_byyearday;
						}
						$html .= "\r\n";
					}
				}
				// Now handle Exceptions
				$exceptions = array();
				if (array_key_exists($a->ev_id(), $exceptiondata))
				{
					$exceptions = $exceptiondata[$a->ev_id()];
				}

				$deletes           = array();
				$changed           = array();
				$changedexceptions = array();
				if (count($exceptions) > 0)
				{
					foreach ($exceptions as $exception)
					{
						if ($exception->exception_type == 0)
						{
							$exceptiondate = JevDate::strtotime($exception->startrepeat);

							// No doing true timezones!
							if ($tzid == "" && is_callable("date_default_timezone_set"))
							{

								// Change timezone to UTC
								$current_timezone = date_default_timezone_get();
								date_default_timezone_set("UTC");

								// Do not use JevDate version since this sets timezone to config value!
								$deletes[] = date("Ymd\THis", $exceptiondate);

								// Change back
								date_default_timezone_set($current_timezone);
							}
							else
							{
								$deletes[] = date("Ymd\THis", $exceptiondate);
							}
						}
						else
						{
							$changed[]                            = $exception->rp_id;
							$changedexceptions[$exception->rp_id] = $exception;
						}
					}
					if (count($deletes) > 0)
					{
						$html .= "EXDATE$tzid:" . self::wraplines(implode(",", $deletes)) . "\r\n";
					}
				}

				// Ok if it's a request and not the master event then it's a change. No need the include the master event for the iCal emails Let see about removing it:
				if (($ics_method == "REQUEST" || $ics_method == "CANCEL") && ($a->hasrepetition() && $m_ev == 0))
				{
					// Simple lets, clear her.
					$html = "";
					//Now re-add standard params.
					if ($params->get('outlook2003icalexport'))
					{
						$html .= "BEGIN:VCALENDAR\r\nPRODID:JEvents 3.1 for Joomla//EN\r\n";
					}
					else
					{
						$html .= "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:JEvents 3.1 for Joomla//EN\r\n";
					}

					$html .= "CALSCALE:GREGORIAN\r\nMETHOD:" . $ics_method . "\r\n";

					if (isset($status))
					{
						$html .= "STATUS:" . $status . "\r\n";
					}
				}
				//Lets get the changes
				$changedrows = array();

				if (count($changed) > 0 && $changed[0] != 0 && $ics_method != "CANCEL")
				{
					foreach ($changed as $rpid)
					{
						$helper = new JEVNotifyHelper;
						if (JPATH_SITE . "/plugins/jevents/jevnotify/")
						{
							$a = $helper->getEventData($rpid, "icaldb", 0, 0, 0, $a->uid());
						}
						else
						{
							// No usage yet.
							// Likely to update helper function when moving over RSVP Pro Generated iCals.
							$a = $helper->getEventData($rpid, "icaldb", 0, 0, 0, $a->uid());
						}

						if ($a && isset($a["row"]))
						{
							$a             = $a["row"];
							$changedrows[] = $a;
						}
					}


					ob_start();
                   // !JDEBUG ?: Profiler::getInstance('Application')->mark('before onDisplayCustomFieldsMultiRow');
                    Factory::getApplication()->triggerEvent('onDisplayCustomFieldsMultiRow', array(&$changedrows));
                   // !JDEBUG ?: Profiler::getInstance('Application')->mark('after onDisplayCustomFieldsMultiRow');
					ob_end_clean();

					// TODO look at removing events as array as we will only handle ONE event in mail generation.
					$changedevent = $icalEvents[0]->rp_id();

					foreach ($changedrows as $a)
					{
						//Ok we only need to get the repeat for the one event. So lets just continue past the repeats that don't match up.
						if (($ics_method == "REQUEST" || $ics_method == "CANCEL") && ($a->hasrepetition() && $m_ev == 0 && $a->rp_id() != $changedevent))
						{
							continue;
						}

						$html .= "BEGIN:VEVENT\r\n";
						$html .= "UID:" . $a->uid() . "\r\n";
						$html .= "CATEGORIES:" . $a->catname() . "\r\n";
						if (!empty($a->_class))
							$html .= "CLASS:" . $a->_class . "\r\n";
						$html .= "SUMMARY:" . $a->title() . "\r\n";
						if ($a->location() != "")
							$html .= "LOCATION:" . self::wraplines(self::replacetags($a->location())) . "\r\n";
						// We Need to wrap this according to the specs
						$html .= self::setDescription($a->content()) . "\r\n";

						$ilink = $a->viewDetailLink($a->yup(), $a->mup(), $a->dup(), true, $params->get('default_itemid', 0));
						$iuri  = Uri::getInstance(Uri::base());
						$iroot = $iuri->toString(array('scheme', 'host', 'port'));
						$html .= "URL;VALUE=URI:" . self::wraplines($iroot . Route::_($ilink, true, -1)) . "\r\n";

						if ($a->hasContactInfo())
							$html .= "CONTACT:" . self::replacetags($a->contact_info()) . "\r\n";

						if ($a->hasExtraInfo())
							$html .= "X-EXTRAINFO:" . self::wraplines(self::replacetags($a->_extra_info));
						$html .= "\r\n";
						$user = Factory::getUser($a->created_by());

						$html          .= "ORGANIZER;CN=" . $user->name . ":MAILTO:" . $user->email . "\r\n";
						$exception     = $changedexceptions[$rpid];
						$originalstart = JevDate::strtotime($exception->oldstartrepeat);
						$chstart       = $a->getUnixStartTime();
						$chend         = $a->getUnixEndTime();

						// No doing true timezones!
						if ($tzid == "" && is_callable("date_default_timezone_set"))
						{
							// UTC!
							// Change timezone to UTC
							$current_timezone = date_default_timezone_get();
							date_default_timezone_set("UTC");

							// Do not use JevDate version since this sets timezone to config value!
							$chstart       = date("Ymd\THis", $chstart);
							$chend         = date("Ymd\THis", $chend);
							$stamptime     = date("Ymd\THis", time());
							$originalstart = date("Ymd\THis", $originalstart);
							// Change back
							date_default_timezone_set($current_timezone);
						}
						else
						{
							$chstart       = date("Ymd\THis", $chstart);
							$chend         = date("Ymd\THis", $chend);
							$stamptime     = date("Ymd\THis", time());
							$originalstart = date("Ymd\THis", $originalstart);
						}
						$html .= "DTSTAMP:" . $stamptime . "\r\n";
						$html .= "DTSTART$tzid:" . $chstart . "\r\n";
						$html .= "DTEND$tzid:" . $chend . "\r\n";
						$html .= "RECURRENCE-ID$tzid:" . $originalstart . "\r\n";
						$html .= "SEQUENCE:" . $a->_sequence . "\r\n";
						$html .= "TRANSP:OPAQUE\r\n";
						$html .= "END:VEVENT\r\n";
					}
				}
				else if ($m_ev == 0 && $ics_method == "CANCEL")
				{

					//Crud and means duplicating Code
					//TODO create a new universal iCalMailer. Ideally, one which stores the emails and run's it's own loop finding iCAL events as MS is a bugger and requires individual mails.

					$a = $icalEvents[0];
					//Lets get the repeat data now
					$html .= "BEGIN:VEVENT\r\n";
					$html .= "UID:" . $a->uid() . "\r\n";
					$html .= "CATEGORIES:" . $a->catname() . "\r\n";
					if (!empty($a->_class))
						$html .= "CLASS:" . $a->_class . "\r\n";
					$html .= "SUMMARY:" . $a->title() . "\r\n";
					if ($a->location() != "")
						$html .= "LOCATION:" . self::wraplines(self::replacetags($a->location())) . "\r\n";
					// We Need to wrap this according to the specs
					$html .= self::setDescription($a->content()) . "\r\n";

					$ilink = $a->viewDetailLink($a->yup(), $a->mup(), $a->dup(), true, $params->get('default_itemid', 0));
					$iuri  = Uri::getInstance(Uri::base());
					$iroot = $iuri->toString(array('scheme', 'host', 'port'));
					$html .= "URL;VALUE=URI:" . self::wraplines($iroot . Route::_($ilink, true, -1)) . "\r\n";

					if ($a->hasContactInfo())
						$html .= "CONTACT:" . self::replacetags($a->contact_info()) . "\r\n";

					if ($a->hasExtraInfo())
						$html .= "X-EXTRAINFO:" . self::wraplines(self::replacetags($a->_extra_info));
					$html .= "\r\n";
					$user = Factory::getUser($a->created_by());

					$html          .= "ORGANIZER;CN=" . $user->name . ":MAILTO:" . $user->email . "\r\n";
					$originalstart = JevDate::strtotime($a->_startrepeat);
					$chstart       = $a->getUnixStartTime();
					$chend         = $a->getUnixEndTime();

					// No doing true timezones!
					if ($tzid == "" && is_callable("date_default_timezone_set"))
					{
						// UTC!
						// Change timezone to UTC
						$current_timezone = date_default_timezone_get();
						date_default_timezone_set("UTC");

						// Do not use JevDate version since this sets timezone to config value!
						$chstart       = date("Ymd\THis", $chstart);
						$chend         = date("Ymd\THis", $chend);
						$stamptime     = date("Ymd\THis", time());
						$originalstart = date("Ymd\THis", $originalstart);
						// Change back
						date_default_timezone_set($current_timezone);
					}
					else
					{
						$chstart       = date("Ymd\THis", $chstart);
						$chend         = date("Ymd\THis", $chend);
						$stamptime     = date("Ymd\THis", time());
						$originalstart = date("Ymd\THis", $originalstart);
					}
					$html .= "DTSTAMP$tzid:" . $stamptime . "\r\n";
					$html .= "DTSTART$tzid:" . $chstart . "\r\n";
					$html .= "DTEND$tzid:" . $chend . "\r\n";
					$html .= "RECURRENCE-ID$tzid:" . $originalstart . "\r\n";
					$html .= "SEQUENCE:" . $a->_sequence . "\r\n";
					$html .= "TRANSP:OPAQUE\r\n";
					$html .= "END:VEVENT\r\n";
				}
				else
				{
					$html .= "TRANSP:OPAQUE\r\n";
					$html .= "END:VEVENT\r\n";
				}
			}
		}

		$html .= "END:VCALENDAR\r\n";

		return $html;

	}

	public static function iCalTitlePrefix($row)
	{
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$prefix = $params->get("icalexportprefix", 0);
		switch ($prefix)
		{
			default :
			case  0:
				return "";
			case  1:
				return $row->getCategoryName() . " - ";
			case  2:
				return $row->getCalendarName() . " - ";
			case  3:
				$config   = new JConfig();
				$sitename = $config->sitename;

				return $sitename . " - ";
		}
		return "";
	}

	protected static
	function vtimezone($icalEvents)
	{

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$tzid   = "";
		if (is_callable("date_default_timezone_set"))
		{
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			$tz     = $params->get("icaltimezonelive", "");
			if ($tz == "")
			{
				return "";
			}

			$current_timezone = $tz;

			// Do the Timezone definition
			// replace any spaces with _ underscores
			$current_timezone = str_replace(" ", "_", $current_timezone);
			$tzid             = ";TZID=$current_timezone";
			// find the earliest start date
			$firststart = false;
			foreach ($icalEvents as $a)
			{
				if (!$firststart || $a->getUnixStartTime() < $firststart)
					$firststart = $a->getUnixStartTime();
			}
			// Subtract 1 leap year to make sure we have enough transitions
			$firststart -= 31622400;
			$timezone   = new DateTimeZone($current_timezone);

			if (version_compare(PHP_VERSION, "5.3.0", "ge"))
			{
				$transitions = $timezone->getTransitions($firststart);
			}
			else
			{
				$transitions = $timezone->getTransitions();
			}
			$tzindex = 0;
			while (isset($transitions[$tzindex]) && JevDate::strtotime($transitions[$tzindex]['time']) < $firststart)
			{
				$tzindex++;
			}
			$transitions = array_slice($transitions, $tzindex);
			if (count($transitions) >= 2)
			{
				$lastyear = JEVHelper::getMaxYear();
				echo "BEGIN:VTIMEZONE\r\n";
				echo "TZID:$current_timezone\r\n";
				for ($t = 0; $t < count($transitions); $t++)
				{
					$transition = $transitions[$t];
					if ((int) $transition['isdst'] == 0)
					{
						if (JevDate::strftime("%Y", $transition['ts']) > $lastyear)
							continue;
						echo "BEGIN:STANDARD\r\n";
						echo "DTSTART:" . JevDate::strftime("%Y%m%dT%H%M%S\r\n", $transition['ts']);
						if ($t < count($transitions) - 1)
						{
							echo "RDATE:" . JevDate::strftime("%Y%m%dT%H%M%S\r\n", $transitions[$t + 1]['ts']);
						}
						// if its the first transition then assume the old setting is the same as the next otherwise use the previous value
						$prev = $t;
						$prev += ($t == 0) ? 1 : -1;

						$offset = $transitions[$prev]["offset"];
						$sign   = $offset >= 0 ? "+" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETFROM:$offset\r\n";

						$offset = $transitions[$t]["offset"];
						$sign   = $offset >= 0 ? "+" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETTO:$offset\r\n";
						echo "TZNAME:$current_timezone " . $transitions[$t]["abbr"] . "\r\n";
						echo "END:STANDARD\r\n";
					}
				}
				for ($t = 0; $t < count($transitions); $t++)
				{
					$transition = $transitions[$t];
					if ((int) $transition['isdst'] == 1)
					{
						if (JevDate::strftime("%Y", $transition['ts']) > $lastyear)
							continue;
						echo "BEGIN:DAYLIGHT\r\n";
						echo "DTSTART:" . JevDate::strftime("%Y%m%dT%H%M%S\r\n", $transition['ts']);
						if ($t < count($transitions) - 1)
						{
							echo "RDATE:" . JevDate::strftime("%Y%m%dT%H%M%S\r\n", $transitions[$t + 1]['ts']);
						}
						// if its the first transition then assume the old setting is the same as the next otherwise use the previous value
						$prev = $t;
						$prev += ($t == 0) ? 1 : -1;

						$offset = $transitions[$prev]["offset"];
						$sign   = $offset >= 0 ? "+" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETFROM:$offset\r\n";

						$offset = $transitions[$t]["offset"];
						$sign   = $offset >= 0 ? "+" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETTO:$offset\r\n";
						echo "TZNAME:$current_timezone " . $transitions[$t]["abbr"] . "\r\n";
						echo "END:DAYLIGHT\r\n";
					}
				}
				echo "END:VTIMEZONE\r\n";
			}
		}

		return $tzid;

	}

	/**
	 * Returns the Max year to display from Config
	 *
	 * @static
	 * @access public
	 * @return    string                integer with the max year to show in the calendar
	 */
	public static
	function getMaxYear()
	{

		$params  = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$maxyear = $params->get("com_latestyear", 2150);
		$maxyear = JEVHelper::getYearNumber($maxyear);

		//Just in case we got text here.
		if (!is_numeric($maxyear))
		{
			$maxyear = "2150";
		}

		return $maxyear;

	}

	static public
	function onDisplayCustomFieldsMultiRow(& $icalrows)
	{

		//list($usec, $sec) = explode(" ", microtime());
		//$starttime = (float) $usec + (float) $sec;

		if (!$icalrows || count($icalrows) == 0)
		{
			return;
		}
		$user   = Factory::getUser();
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		// only unlogged in users and not logged in OR all visitors grouped by access level
		if (($params->get("com_cache", 1) == 1 && $user->id == 0) || $params->get("com_cache", 1) == 2)
		{

			$cachecontroller = Factory::getCache(JEV_COM_COMPONENT);
			$oldcaching      = $cachecontroller->cache->getCaching();
			$cachecontroller->cache->setCaching(true);

			// if grouped by access level caching then add this to the cache id
			$cachegroups = ($params->get("com_cache", 1) == 2) ? implode(',', $user->getAuthorisedViewLevels()) : "";
			$lang        = Factory::getLanguage()->getTag();

			$rows     = array();
			$indexmap = array();
			foreach ($icalrows as $index => & $row)
			{
				$indexmap[$row->rp_id()] = $index;
				$id                      = md5($row->rp_id() . " onDisplayCustomFieldsMultiRow " . $row->uid() . " " . $row->title() . "-" . $cachegroups . $lang);
				$data                    = $cachecontroller->cache->get($id);
				if ($data)
				{
					if (is_callable("gzcompress"))
					{
						$data = gzuncompress($data);
					}
					$row = unserialize($data);
				}
				else
				{
					//echo "failed to get $id<br/>";
					$rows[] = $row;
				}
			}
			unset($row);

			if (count($rows))
			{
				PluginHelper::importPlugin('jevents');
               // !JDEBUG ?: Profiler::getInstance('Application')->mark('before onDisplayCustomFieldsMultiRow');
				Factory::getApplication()->triggerEvent('onDisplayCustomFieldsMultiRow', array(&$rows));
              //  !JDEBUG ?: Profiler::getInstance('Application')->mark('after onDisplayCustomFieldsMultiRow');
				foreach ($rows as $k => $row)
				{
					$id   = md5($row->rp_id() . " onDisplayCustomFieldsMultiRow " . $row->uid() . " " . $row->title() . "-" . $cachegroups . $lang);
					$data = serialize($row);
					if (is_callable("gzcompress"))
					{
						// 2 seems a good balance between compression and performance
						$data = gzcompress($data, 2);
					}
					$cached = $cachecontroller->cache->store($data, $id);
					if ($cached)
					{
						//echo "stored $id<br/>";
					}
					$index            = $indexmap[$row->rp_id()];
					$icalrows[$index] = $row;
				}
			}
			//list ($usec, $sec) = explode(" ", microtime());
			//$time_end = (float) $usec + (float) $sec;
			//echo  "onDisplayCustomFieldsMultiRow  = ".round($time_end - $starttime, 4)."<br/>";

			$cachecontroller->cache->setCaching($oldcaching);
		}
		else
		{
			PluginHelper::importPlugin('jevents');
           // !JDEBUG ?: Profiler::getInstance('Application')->mark('before onDisplayCustomFieldsMultiRow');
			Factory::getApplication()->triggerEvent('onDisplayCustomFieldsMultiRow', array(&$icalrows));
           // !JDEBUG ?: Profiler::getInstance('Application')->mark('after onDisplayCustomFieldsMultiRow');
		}

	}

	// Special methods ONLY user for iCal invitations

	public static
	function wraplines($input, $line_max = 76, $quotedprintable = false)
	{

		$eol = "\r\n";

		$input = str_replace($eol, "", $input);

		// new version
		$output = '';
		while (StringHelper::strlen($input) >= $line_max)
		{
			$output .= StringHelper::substr($input, 0, $line_max - 1);
			$input  = StringHelper::substr($input, $line_max - 1);
			if (StringHelper::strlen($input) > 0)
			{
				$output .= $eol . " ";
			}
		}
		if (StringHelper::strlen($input) > 0)
		{
			$output .= $input;
		}

		return $output;
	}

	public static
	function replacetags($description)
	{

		$description = str_replace('<p>', '', $description);
		$description = str_replace('<P>', '', $description);
		$description = str_replace('</p>', '\n', $description);
		$description = str_replace('</P>', '\n', $description);
		$description = str_replace('<p/>', '\n\n', $description);
		$description = str_replace('<P/>', '\n\n', $description);
		$description = str_replace('<br />', '\n', $description);
		$description = str_replace('<br/>', '\n', $description);
		$description = str_replace('<br>', '\n', $description);
		$description = str_replace('<BR />', '\n', $description);
		$description = str_replace('<BR/>', '\n', $description);
		$description = str_replace('<BR>', '\n', $description);
		$description = str_replace('<li>', '\n - ', $description);
		$description = str_replace('<LI>', '\n - ', $description);

		try
		{
			$dom = new DOMDocument();
			// see http://php.net/manual/en/domdocument.savehtml.php cathexis dot de ¶
			@$dom->loadHTML('<html><head><meta content="text/html; charset=utf-8" http-equiv="Content-Type"></head><body>' . $description . '</body>');

			$links = $dom->getElementsByTagName('a');
			foreach ($links as $link)
			{
				$fragment = $dom->createDocumentFragment();
				$href = $link->getAttribute('href');
				$text = $link->textContent;
				if ($text == $href || empty($href))
				{
					$fragment->appendXML( htmlspecialchars($link->textContent) );
				}
				else
				{
					$fragment->appendXML( htmlspecialchars($link->textContent . " (" . $href . ")") );
				}

				$link->parentNode->replaceChild($fragment, $link);
			}
			//$description = $dom->saveHTML($dom->getElementsByTagName('body')[0]);
			$body = $dom->getElementsByTagName('body')[0];
			$newdescription= '';
			$children = $body->childNodes;
			foreach ($children as $child) {
				$newdescription .= $child->ownerDocument->saveHTML( $child );
			}
			if (!empty($newdescription))
			{
				$description = $newdescription;
			}

		}
		catch (Exception $exception)
		{
			$x = 1;
		}
		$description = strip_tags($description, '<a>');
		//$description 	= strtr( $description,	array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
		//$description 	= preg_replace( "/&#([0-9]+);/me","chr('\\1')", $description );
		return $description;

	}

	public static function setDescription($desc)
	{

		// TODO - run this through plugins first ?

		// See http://www.jevents.net/forum/viewtopic.php?f=23&t=21939&p=115231#wrap
		// can we use 	X-ALT-DESC;FMTTYPE=text/html: as well as DESCRIPTION
		$input = Factory::getApplication()->input;

		$icalformatted = $input->getInt("icf", 0);
		if (!$icalformatted)
		{
			$htmlDesc    = $desc;
			$description = self::replacetags($desc);
		}
		else
		{
			$htmlDesc    = $desc;
			$description = $desc;
		}

        // convert relative to absolute URLs
        $htmlDesc = preg_replace('#(href|src|action|background)[ ]*=[ ]*\"(?!(https?://|\#|mailto:|/))(?:\.\./|\./)?#', '$1="' . JURI::root(), $htmlDesc);
        $htmlDesc = preg_replace('#(href|src|action|background)[ ]*=[ ]*\"(?!(https?://|\#|mailto:))/#', '$1="' . JURI::root(), $htmlDesc);

        $htmlDesc = preg_replace("#(href|src|action|background)[ ]*=[ ]*\'(?!(https?://|\#|mailto:|/))(?:\.\./|\./)?#", "$1='" . JURI::root(), $htmlDesc);
        $htmlDesc = preg_replace("#(href|src|action|background)[ ]*=[ ]*\'(?!(https?://|\#|mailto:))/#", "$1='" . JURI::root(), $htmlDesc);

        // wraplines	from vCard class
		$cfg = JEVConfig::getInstance();
		$return = "";
		if ($cfg->get("outlook2003icalexport", 1))
		{
			$return =  "DESCRIPTION:" . self::wraplines($description, 76, false);
		}
		else
		{
			// ENCODING=QUOTED-PRINTABLE is deprecated
			//return "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:" . $this->wraplines($description);
			$return = "DESCRIPTION:" . self::wraplines($description, 76, false);
		}
		if ($htmlDesc !== $description)
		{
			$return .= "\r\nX-ALT-DESC;FMTTYPE=text/html:" . self::wraplines($htmlDesc, 76, false);
		}
		return $return;
	}

	public static
	function modal($selector = 'a.modal', $params = array())
	{

		JevModal::modal($selector, $params);
		return;
	}

	public static function getCache($option)
	{

		$user   = Factory::getUser();
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		// only unlogged in users and not logged in OR all visitors grouped by access level
		if ($params->get("com_cache", 1) && $user->id == 0)
		{
			return Factory::getCache($option);
		}
		else
		{
			include_once("jevCache.php");

			return new jevCache();
		}
	}

	/*
	 * Fix config etc. to run in WP with minimal code changes!
	 */
	public static function setupWordpress()
	{

		if (defined("WPJEVENTS"))
		{
			$cfg = JEVConfig::getInstance();
			$cfg->set('com_email_icon_view', 0);
		}
	}

	public static function setMenuFilter($key, $value)
	{

		$menufilters       = JEVHelper::getMenuFilters();
		$menufilters->$key = $value;
	}

	public static function & getMenuFilters()
	{

		$registry    = JevRegistry::getInstance("jevents");
		$menufilters = $registry->get("jevents.menufilters", false);
		if (!$menufilters)
		{
			$menufilters = new stdClass();
			$registry->set("jevents.menufilters", $menufilters);
		}

		return $menufilters;
	}

	public static function getMenuFilter($key, $default = null)
	{

		$menufilters = JEVHelper::getMenuFilters();

		return isset($menufilters->$key) ? $menufilters->$key : $default;
	}

	// TODO - create a stack of previous values and use reset rather than clear to allow recursive type calls
	public static function clearMenuFilter($key)
	{

		$menufilters = JEVHelper::getMenuFilters();
		unset($menufilters->$key);
	}

	public static function arrayFiltered($array = array()) {

		if (version_compare(JVERSION, '3.7.1', '>='))
		{
			$filter = InputFilter::getInstance(array(), array(), 1, 1);

			//Joomla! no longer provides HTML allowed in input so we need to fetch raw
			//Then filter on through with InputFilter to HTML

            // Ensure it is an array for filtering.
            $array	= is_string($array) ? array($array) : $array;

            foreach ($array as $key => $row)
			{
				//Single row check
				if (!is_array($row))
				{
					$array[$key] = $filter->clean($row, 'HTML');
				}
				else
				{
					//1 Deep row check
					foreach ($array[$key] as $key1 => $sub_row)
					{
						//2 Deep row check
						if (!is_array($sub_row))
						{
							$array[$key][$key1] = $filter->clean($sub_row, 'HTML');
						}
						else
						{
							foreach ($sub_row as $key2 => $sub_sub_row)
							{
								//3 Deep row check
								if (!is_array($sub_sub_row))
								{
									$array[$key][$key1][$key2] = $filter->clean($sub_sub_row, 'HTML');
								}
								else
								{
									foreach ($sub_sub_row as $key3 => $sub_sub_sub_row)
									{
										//4 Deep row check
										if (!is_array($sub_sub_sub_row))
										{
											$array[$key][$key1][$key2][$key3] = $filter->clean($sub_sub_sub_row, 'HTML');
										}
										else
										{
											foreach ($sub_sub_sub_row as $key4 => $sub_sub_sub_sub_row)
											{
												$array[$key][$key1][$key2][$key3][$key4] = $filter->clean($sub_sub_sub_sub_row, 'HTML');
											}
										}

									}
								}

							}
						}
					}
				}
			}
		}

		return $array;
	}

    public static function setUpdateUrls()
    {
        // Only do this once every 30 minutes
        $options = array(
            'defaultgroup' => 'jevents_updateurls',
            'cachebase'    => JPATH_ADMINISTRATOR . '/cache',
            'lifetime'     => 30
        );
        $cache   = JCache::getInstance('', $options);
        $cache->setCaching(true);

        $alreadyUpdated = $cache->get('alreadyUpdated', 'jevents_updateurls');
        if ($alreadyUpdated)
        {
            return;
        }
        $cache->store(1, 'alreadyUpdated', 'jevents_updateurls');

        $starttime = microtime(true);

        $db = Factory::getDbo();

        $params = ComponentHelper::getParams(JEV_COM_COMPONENT);

        $updates = array(
            array("element" => "pkg_jeventsplus", "name" => "com_jeventsplus", "type" => "package"),

            array("element" => "pkg_jevents", "name" => "com_jevents", "type" => "package"),
            array("element" => "pkg_jevlocations", "name" => "com_jevlocations", "type" => "package"),
            array("element" => "pkg_jevpeople", "name" => "com_jevpeople", "type" => "package"),
            array("element" => "pkg_rsvppro", "name" => "com_rsvppro", "type" => "package"),
            array("element" => "pkg_jeventstags", "name" => "com_jeventstags", "type" => "package"),

            // Silver - AnonUsers
            array("element" => "jevanonuser", "name" => "jevanonuser", "folder" => "jevents", "type" => "plugin"),
            // Silver - AutoTweet
            array("element" => "jevsendfb", "name" => "jevsendfb", "folder" => "jevents", "type" => "plugin"),
            array("element" => "autotweetjevents", "name" => "autotweetjevents", "folder" => "system", "type" => "plugin"),
            // Silver - MatchingEvents
            array("element" => "jevmatchingevents", "name" => "jevmatchingevents", "folder" => "jevents", "type" => "plugin"),
            // Silver - StandardImage
            array("element" => "jevfiles", "name" => "jevfiles", "folder" => "jevents", "type" => "plugin"),
            // Silver - agendaminutes
            array("element" => "agendaminutes", "name" => "agendaminutes", "folder" => "jevents", "type" => "plugin"),
            array("element" => "jevent_embed", "name" => "jevent_embed", "folder" => "content", "type" => "plugin"),
            // Silver - authorisedusers
            array("element" => "jevuser", "name" => "jevuser", "folder" => "user", "type" => "plugin"),
            // Silver - calendar
            array("element" => "jevcalendar", "name" => "jevcalendar", "folder" => "jevents", "type" => "plugin"),
            // Silver - catcal
            array("element" => "jevcatcal", "name" => "jevcatcal", "folder" => "jevents", "type" => "plugin"),
            // Silver - cck
            array("element" => "jevcck", "name" => "jevcck", "folder" => "jevents", "type" => "plugin"),
            array("element" => "k2embedded", "name" => "k2embedded", "folder" => "k2", "type" => "plugin"),
            // Silver - creator
            array("element" => "jevcreator", "name" => "jevcreator", "folder" => "content", "type" => "plugin"),
            // Silver - customfields
            array("element" => "jevcustomfields", "name" => "jevcustomfields", "folder" => "jevents", "type" => "plugin"),
            // Silver - Dynamic legend
            array("element" => "mod_jevents_dynamiclegend", "name" => "mod_jevents_dynamiclegend", "type" => "module"),
            // Silver - Calendar Plus
            array("element" => "mod_jevents_calendarplus", "name" => "mod_jevents_calendarplus", "type" => "module"),
            // Silver - Slideshow Module
            array("element" => "mod_jevents_slideshow", "name" => "mod_jevents_slideshow", "type" => "module"),
            // Silver - facebook
            array("element" => "jevfacebook", "name" => "jevfacebook", "folder" => "jevents", "type" => "plugin"),
            // Silver - facebook social
            array("element" => "jevfacebooksocial", "name" => "jevfacebooksocial", "folder" => "jevents", "type" => "plugin"),
            // Silver - featured
            array("element" => "jevfeatured", "name" => "jevfeatured", "folder" => "jevents", "type" => "plugin"),
            // Silver - hiddendetail
            array("element" => "jevhiddendetail", "name" => "jevhiddendetail", "folder" => "jevents", "type" => "plugin"),
            // Silver - jomsocial -  TODO
            array("element" => "jevjsstream", "name" => "jevjsstream", "folder" => "jevents", "type" => "plugin"),
            array("element" => "jevents", "name" => "jevents", "folder" => "community", "type" => "plugin"),
            // Silver - layouts
            array("element" => "extplus", "name" => "extplus", "type" => "file"),
            array("element" => "ruthin", "name" => "ruthin", "type" => "file"),
            array("element" => "flatplus", "name" => "flatplus", "type" => "file"),
            array("element" => "iconic", "name" => "iconic", "type" => "file"),
            array("element" => "map", "name" => "map", "type" => "file"),
            array("element" => "smartphone", "name" => "smartphone", "type" => "file"),
            array("element" => "zim", "name" => "zim", "type" => "file"),
            array("element" => "float", "name" => "float", "type" => "file"),

            // These have been renamed in the XML file - need to be careful doing that!!!
            array("element" => "JEventsExtplusLayout", "name" => "extplus", "type" => "file"),
            array("element" => "JEventsRuthinLayout", "name" => "ruthin", "type" => "file"),
            array("element" => "JEventsFlatplusLayout", "name" => "flatplus", "type" => "file"),
            array("element" => "JEventsIconicLayout", "name" => "iconic", "type" => "file"),
            array("element" => "JEventsMapLayout", "name" => "map", "type" => "file"),
            array("element" => "JEventsSmartphoneLayout", "name" => "smartphone", "type" => "file"),
            array("element" => "JEventsZimLayout", "name" => "zim", "type" => "file"),
            array("element" => "JEventsFloatLayout", "name" => "float", "type" => "file"),

            // Silver - Jevents Categories
            array("element" => "mod_jevents_categories", "name" => "mod_jevents_categories", "type" => "module"),
            // Silver - Newsletters - some TODO
            array("element" => "tagjevents_jevents", "name" => "tagjevents_jevents", "folder" => "acymailing", "type" => "plugin"),
	        array("element" => "jev_latestevents", "name" => "jev_latestevents", "folder" => "emailalerts", "type" => "plugin"),
	        array("element" => "jnewsjevents", "name" => "jnewsjevents", "folder" => "jnews", "type" => "plugin"),
            // Silver - Nnotifications
            array("element" => "jevnotify", "name" => "jevnotify", "folder" => "jevents", "type" => "plugin"),
            array("element" => "mod_jevents_notify", "name" => "mod_jevents_notify", "type" => "module"),
            // Silver - simpleattend
            array("element" => "jevrsvp", "name" => "jevrsvp", "folder" => "jevents", "type" => "plugin"),
            // Silver - tabbed modules
            array("element" => "mod_tabbedmodules", "name" => "mod_tabbedmodules", "type" => "module"),
            // Silver - time Limit
            array("element" => "jevtimelimit", "name" => "jevtimelimit", "folder" => "jevents", "type" => "plugin"),
            // Silver - User Events
            array("element" => "jevusers", "name" => "jevusers", "folder" => "jevents", "type" => "plugin"),
            // Silver - Week Days
            array("element" => "jevweekdays", "name" => "jevweekdays", "folder" => "jevents", "type" => "plugin"),

            // GOLD addons - PaidSubs - TODO check Virtuemart for Joomla 3.0 is available
            array("element" => "jevpaidsubs", "name" => "jevpaidsubs", "folder" => "jevents", "type" => "plugin"),
            array("element" => "mod_jevents_paidsubs", "name" => "mod_jevents_paidsubs", "type" => "module"),

            // Translations - TODO club translations.  Normal JEvents translations handled below!

            // Bronze - editor button
            array("element" => "jevents", "name" => "jevents", "folder" => "editors-xtd", "type" => "plugin"),

	        // Bronze - Remote Module Loaded
	        array("element" => "mod_remoteloader", "name" => "mod_remoteloader", "type" => "module"),

            // Bronze - Meta tags
            array("element" => "jevmetatags", "name" => "jevmetatags", "folder" => "jevents", "type" => "plugin"),

            // Bronze - Missing Events
            array("element" => "jevmissingevent", "name" => "jevmissingevent", "folder" => "jevents", "type" => "plugin"),

            // Bronze - Popups
            array("element" => "jevpopupdetail", "name" => "jevpopupdetail", "folder" => "jevents", "type" => "plugin"),

            // Bronze - sh404sef - TODO

        );

        //JFactory::getApplication()->enqueueMessage("CHANGE UPDATESERVER", 'warning');
        $debug = "XDEBUG_SESSION_START=PHPSTORM&";
        //$updateDomain = "http://ubu.j33jq.com";
        $debug = "";
        $updateDomain = "https://www.jevents.net";

        // Do the language files for Joomla
        $db = Factory::getDbo();
        $db->setQuery("SELECT * FROM #__extensions where type='file' AND element LIKE '%_JEvents' AND element NOT LIKE '%_JEvents_Addons' and element NOT LIKE '%_JEventsAddons' ");
        $translations = $db->loadObjectList();
        foreach ($translations as $translation)
        {
            if ($translation->name == "")
            {
                $translation->name = "JEvents Translation - " . $translation->element;
            }
            //	array("element"=>"ar-AA_JEvents","name"=>"Arabic translation for JEvents","type"=>"file"),
            $updates[] = array("element" => $translation->element, "name" => $translation->name, "type" => "file");
        }

        $db->setQuery("SELECT * FROM #__extensions where type='file' AND (element LIKE '%_JEvents_Addons' OR element LIKE '%_JEventsAddons') ");
        $translations = $db->loadObjectList();
        foreach ($translations as $translation)
        {
            //	array("element"=>"ar-AA_JEvents","name"=>"Arabic translation for JEvents","type"=>"file"),
	        $updates[] = array("element" => $translation->element, "name" => $translation->name, "type" => "file");
        }

        // Eliminate JEvents duplicates
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__update_sites')
            ->where('location LIKE ' . $db->quote("%newupdates/jevents_package.xml"));
        $db->setQuery($query);
        $updatesites = $db->loadObjectList();

        if ($updatesites && count($updatesites) > 1)
        {
            // clean up duplicate records
            $query = $db->getQuery(true);
            $query->delete('#__update_sites')
                ->where('location LIKE ' . $db->quote("%newupdates/jevents_package.xml"));
            $db->setQuery($query);
            $db->execute();
            $updateJeventsSite = false;
        }
        else
        {
            $updateJeventsSite = isset($updatesites[0]) ? $updatesites[0] : false;
        }

        if (!$updateJeventsSite)
        {
            $query = $db->getQuery(true);
            $query->insert('#__update_sites')
                ->columns(array($db->qn('name'),
                        $db->qn('type'),
                        $db->qn('location'),
                        $db->qn('enabled'))
                )
                ->values(
                    $db->q('JEvents Addon Updates') . ', ' .
                    $db->q('extension') . ', ' .
                    $db->q("$updateDomain/newupdates/jevents_package.xml") . ', ' .
                    $db->q(1)
                );

            $db->setQuery($query);
            $db->execute();

            $query = $db->getQuery(true);
            $query->select('*')
                ->from('#__update_sites')
                ->where('location = ' . $db->quote("$updateDomain/newupdates/jevents_package.xml"));

            $db->setQuery($query);
            $updateJeventsSite = $db->loadObject();

        }

        // Eliminate duplicates
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')
            ->from('#__update_sites')
            ->where('location LIKE ' . $db->quote("%newupdates/jevents_addon_updates.xml"));
        $db->setQuery($query);
        $updatesites = $db->loadObjectList();

        if ($updatesites && count($updatesites) > 1)
        {
            // clean up duplicate records
            $query = $db->getQuery(true);
            $query->delete('#__update_sites')
                ->where('location LIKE ' . $db->quote("%newupdates/jevents_addon_updates.xml"));
            $db->setQuery($query);
            $db->execute();
            $updatesite = false;
        }
        else
        {
            $updatesite = isset($updatesites[0]) ? $updatesites[0] : false;
        }

        if (!$updatesite)
        {
            $query = $db->getQuery(true);
            $query->insert('#__update_sites')
                ->columns(array($db->qn('name'),
                        $db->qn('type'),
                        $db->qn('location'),
                        $db->qn('enabled'))
                )
                ->values(
                    $db->q('JEvents Addon Updates') . ', ' .
                    $db->q('collection') . ', ' .
                    $db->q("$updateDomain/newupdates/jevents_addon_updates.xml") . ', ' .
                    $db->q(1)
                );

            $db->setQuery($query);
            $db->execute();

            $query = $db->getQuery(true);
            $query->select('*')
                ->from('#__update_sites')
                ->where('location = ' . $db->quote("$updateDomain/newupdates/jevents_addon_updates.xml"));

            $db->setQuery($query);
            $updatesite = $db->loadObject();

        }

        if ($updatesite)  //&& empty($updatesite->extra_query))
        {
            $sitedomain = rtrim(str_replace(array('https://', 'http://'), "", Uri::root()), '/');

            $params   = ComponentHelper::getParams(JEV_COM_COMPONENT);
            $clubcode = $params->get("clubcode", "");
            $filter   = new InputFilter();
            $clubcode = $filter->clean($clubcode, "CMD");
            $clubcode = $clubcode . "-" . base64_encode($sitedomain);
            $query = $db->getQuery(true);
            $query->update('#__update_sites')
                ->set('extra_query = '. $db->quote($debug . 'dlid=' . $clubcode))
                ->set('name = '. $db->quote('JEvents Addon Updates'))
                ->where('location LIKE ' . $db->quote("%newupdates/jevents_addon_updates.xml"));
            $db->setQuery($query);
            $db->execute();
        }

        // clean up old style records
        $query = $db->getQuery(true);
        $query->delete('#__update_sites')
            ->where('(location NOT LIKE ' . $db->quote("%newupdates/jevents_addon_updates.xml")
                . ' AND location NOT LIKE ' . $db->quote("%newupdates/jevents_package.xml") .')')
            ->where('( location LIKE  ' . $db->quote("https://www.jevents.net%")
                . ' OR '
                . 'location LIKE ' . $db->quote("http://ubu.j33jq.com%") .')'
            );
        $db->setQuery($query);
        $db->execute();

        $query = $db->getQuery(true);
        $query->update('#__update_sites')
            ->set('location = ' . $db->q("$updateDomain/newupdates/jevents_addon_updates.xml"))
            ->where('location LIKE ' . $db->quote("%newupdates/jevents_addon_updates.xml"))
            ->where(' location NOT LIKE ' . $db->q("$updateDomain/newupdates/jevents_addon_updates.xml"));
        $db->setQuery($query);
        $db->execute();

        foreach ($updates as $package)
        {
            JEVHelper::setUpdateUrlsByPackage($package, $updatesite, $updateJeventsSite);
        }

        $time_end = microtime(true);
        // echo  "JEVHelper::setUpdateUrls = ".round($time_end - $starttime, 4)."<br/>";
    }

    private static function setUpdateUrlsByPackage($package, $updatesite, $updateJeventsSite)
    {
        $db = Factory::getDbo();

        $pkg    = $package["element"];
        $com    = $package["name"];
        $folder = isset($package["folder"]) ? $package["folder"] : "";
        $type   = $package["type"];

        static $extensiondata = false;
        if (!$extensiondata)
        {
            $db->setQuery("select map.update_site_id, exn.extension_id as extension_id , exn.type as extension_type, exn.element as extension_element, exn.folder as extension_folder, exn.package_id as extension_package_id  from #__extensions as exn
	LEFT JOIN #__update_sites_extensions as map on map.extension_id=exn.extension_id
	LEFT JOIN #__update_sites as us on us.update_site_id=map.update_site_id");
            $extensiondata = $db->loadObjectList('extension_id');
        }

        // Now check and setup the package update URL
        $pkgupdate = false;
        foreach ($extensiondata as $ed)
        {
            if ($ed->extension_type == $type && $ed->extension_element == $pkg && $ed->extension_folder == $folder)
            {
                $pkgupdate = $ed;
                break;
            }
        }

        if ($pkg == 'pkg_jevents')
        {
            // we have a package and an update record
            if ($pkgupdate && $pkgupdate->update_site_id !== $updateJeventsSite->update_site_id) {
                // Now update package update URL
                JEVHelper::setPackageUpdateUrl($pkgupdate, $updateJeventsSite);
            } // we have a package but not an update record
            else if ($pkgupdate && $pkgupdate->extension_id && !$pkgupdate->update_site_id) {
                // Now set package update URL
                JEVHelper::setPackageUpdateUrl($pkgupdate, $updateJeventsSite);
            }
        }
        else {
            // we have a package and an update record
            if ($pkgupdate && $pkgupdate->update_site_id !== $updatesite->update_site_id) {
                // Now update package update URL
                JEVHelper::setPackageUpdateUrl($pkgupdate, $updatesite);
            } // we have a package but not an update record
            else if ($pkgupdate && $pkgupdate->extension_id && !$pkgupdate->update_site_id) {
                // Now set package update URL
                JEVHelper::setPackageUpdateUrl($pkgupdate, $updatesite);
            }
        }
    }

	private static function setPackageUpdateUrl($pkgupdate, $updatesite)
	{

		$db = Factory::getDbo();

		// Save DB queries!
		static $extensiondata = false;
		if (!$extensiondata)
		{
			$db->setQuery("Select * from #__extensions");
			$extensiondata = $db->loadObjectList('extension_id');
		}

		$extension = isset($extensiondata[$pkgupdate->extension_id]) ? $extensiondata[$pkgupdate->extension_id] : null;

		// Only do top level package updates!
		if ($extension && $extension->package_id == 0)
		{
			$query = $db->getQuery(true);
			$query->delete('#__update_sites_extensions')
				->where('extension_id = ' . $db->quote($pkgupdate->extension_id));
			$db->setQuery($query);
			$db->execute();

			$db->setQuery("INSERT INTO #__update_sites_extensions (update_site_id, extension_id) VALUES ($updatesite->update_site_id, $pkgupdate->extension_id)");
			$db->execute();
		}

	}

    public static
    function dateFromStrftimeFormat($strftime, $datetime = null)
    {
        if (is_null($datetime))
        {
            $datetime = time();
        }
        $format = JEVHelper::mapStrftimeFormatToDateFormat($strftime);
        return date($format, $datetime);
    }

    public static
    function mapStrftimeFormatToDateFormat($strftime)
    {

        $informat = $format = $strftime;

        // Escape Timezone!
        $format = str_replace(
            array(  '%dT',  '%SZ'),
            array( '%d\T', '%S\Z'),
            $format);

        // Aggregates
        $format = str_replace(
            array(          '%r',     '%R',        '%T',       '%D' ,      '%F'),
            array( '%I:%M:%S %p',  '%H:%M',  '%H:%M:%S', '%m/%d/%y', '%Y-%m-%d'),
            $format);
        // Not supporting

        // ToDo Trim double spaces in value if using %k etc. which have preceeding spaces
        // Year
        $format = str_replace(
            array('%G', '%y', '%Y'),
            array( 'o',  'y',  'Y'),
            $format);
        // Not supporting %c %g

        // Month
        $format = str_replace(
            array('%b', '%B', '%h', '%m'),
            array( 'M',  'F',  'M',  'm'),
            $format);
        // Not supporting

        // Day
        $format = str_replace(
            array('%a', '%A', '%d', '%e' ),
            array( 'D',  'l',  'd',  'j'),
            $format);
        // Not supporting %u %w %j

        // AM/PM
        $format = str_replace(
            array('%P', '%p'),
            array( 'a',  'A'),
            $format);
        // Not supporting %u %w %j

        // Hour
        $format = str_replace(
            array('%H', '%k', '%I', '%l' ),
            array( 'H',  'G',  'h',  'g'),
            $format);
        // Not supporting %u %w %j

        // Minute
        $format = str_replace(
            array('%M'),
            array( 'i'),
            $format);
        // Not supporting

        // Second
        $format = str_replace(
            array('%S'),
            array( 's'),
            $format);
        // Not supporting

        return $format;
    }


    public static function redirectMissingEvent($Itemid)
    {
        $params  = ComponentHelper::getParams(JEV_COM_COMPONENT);
        $missingmenu = (int) $params->get("missingmenu", 0);
        if ($missingmenu)
        {
            $Itemid = $missingmenu;
        }
        Factory::getApplication()->enqueueMessage(Text::_("JEV_SORRY_UPDATED"), 'warning');
        Factory::getApplication()->redirect(Route::_("index.php?Itemid=$Itemid", false));
    }
}
