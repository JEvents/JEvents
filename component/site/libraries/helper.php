<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: helper.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.access.access');
JLoader::register('JevJoomlaVersion', JPATH_ADMINISTRATOR . "/components/com_jevents/libraries/version.php");

/**
 * Helper class with common functions for the component and modules
 *
 * @author     Thomas Stahl
 * @since      1.4
 */
class JEVHelper
{

	/**
	 * load language file
	 *
	 * @static
	 * @access public
	 * @since 1.4
	 */
	public static
			function loadLanguage($type = 'default', $lang = '')
	{

		// to be enhanced in future : load by $type (com, modcal, modlatest) [tstahl]

		$option = JRequest::getCmd("option");
		$cfg = JEVConfig::getInstance();
		$lang = JFactory::getLanguage();

		static $isloaded = array();

		$typemap = array(
			'default' => 'front',
			'front' => 'front',
			'admin' => 'admin',
			'modcal' => 'front',
			'modlatest' => 'front',
			'modfeatured' => 'front'
		);
		$type = (isset($typemap[$type])) ? $typemap[$type] : $typemap['default'];

		// load language defines only once
		if (isset($isloaded[$type]))
		{
			return;
		}

		$cfg = JEVConfig::getInstance();
		$isloaded[$type] = true;

		switch ($type) {
			case 'front':
				// load new style language
				// if loading from another component or is admin then force the load of the site language file - otherwite done automatically
				if ($option != JEV_COM_COMPONENT || JFactory::getApplication()->isAdmin())
				{
					// force load of installed language pack
					$lang->load(JEV_COM_COMPONENT, JPATH_SITE);
				}
				// overload language with components language directory if available
				//$inibase = JPATH_SITE . '/components/' . JEV_COM_COMPONENT;
				//$lang->load(JEV_COM_COMPONENT, $inibase);
				// Load Site specific language overrides
				$lang->load(JEV_COM_COMPONENT, JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate());

				break;

			case 'admin':
				// load new style language
				// if loading from another component or is frontend then force the load of the admin language file - otherwite done automatically
				if ($option != JEV_COM_COMPONENT || !JFactory::getApplication()->isAdmin())
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
		$lang = JFactory::getLanguage();
		return $lang->load(strtolower($extension), $basePath, null, false, true);

	}

	/**
	 * load iCal instance for filename
	 *
	 * @static
	 * @access public
	 * @since 1.5
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
			$import = new iCalImport();
			$instances[$index] = $import->import($filename, $rawtext);

			return $instances[$index];
		}

	}

	/**
	 * Returns the Max year to display from Config
	 * 
	 * @static
	 * @access public
	 * @return	string				integer with the max year to show in the calendar
	 */
	public static
			function getMaxYear()
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$maxyear = $params->get("com_latestyear", 2150);
		$maxyear = JEVHelper::getYearNumber($maxyear);

		//Just in case we got text here.
		if (!is_numeric($maxyear))
		{
			$maxyear = "2150";
		}

		return $maxyear;

	}

	/**
	 * Returns the Max year to display from Config
	 * 
	 * @static
	 * @access public
	 * @return	string				integer with the max year to show in the calendar
	 */
	public static
			function getMinYear()
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
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
	 * @param	string	$month		numeric month
	 * @return	string				localised long month name
	 */
	public static
			function getMonthName($month = 12)
	{

		switch (intval($month)) {

			case 1: return JText::_('JEV_JANUARY');
			case 2: return JText::_('JEV_FEBRUARY');
			case 3: return JText::_('JEV_MARCH');
			case 4: return JText::_('JEV_APRIL');
			case 5: return JText::_('JEV_MAY');
			case 6: return JText::_('JEV_JUNE');
			case 7: return JText::_('JEV_JULY');
			case 8: return JText::_('JEV_AUGUST');
			case 9: return JText::_('JEV_SEPTEMBER');
			case 10: return JText::_('JEV_OCTOBER');
			case 11: return JText::_('JEV_NOVEMBER');
			case 12: return JText::_('JEV_DECEMBER');
		}

	}

	/**
	 * Return the short month name
	 * 
	 * @static
	 * @access public
	 * @param	string	$month		numeric month
	 * @return	string				localised short month name
	 */
	public static
			function getShortMonthName($month = 12)
	{

		switch (intval($month)) {

			// Use Joomla translation
			case 1: return JText::_('JANUARY_SHORT');
			case 2: return JText::_('FEBRUARY_SHORT');
			case 3: return JText::_('MARCH_SHORT');
			case 4: return JText::_('APRIL_SHORT');
			case 5: return JText::_('MAY_SHORT');
			case 6: return JText::_('JUNE_SHORT');
			case 7: return JText::_('JULY_SHORT');
			case 8: return JText::_('AUGUST_SHORT');
			case 9: return JText::_('SEPTEMBER_SHORT');
			case 10: return JText::_('OCTOBER_SHORT');
			case 11: return JText::_('NOVEMBER_SHORT');
			case 12: return JText::_('DECEMBER_SHORT');
		}

	}

	/**
	 * Returns name of the day longversion
	 *
	 * @static
	 * @param	int		daynb	# of day
	 * @param	int		array, 0 return single day, 1 return array of all days
	 * @return	mixed	localised short day letter or array of names
	 * */
	public static
			function getDayName($daynb = 0, $array = 0)
	{

		static $days = null;

		if ($days === null)
		{
			$days = array();

			$days[0] = JText::_('JEV_SUNDAY');
			$days[1] = JText::_('JEV_MONDAY');
			$days[2] = JText::_('JEV_TUESDAY');
			$days[3] = JText::_('JEV_WEDNESDAY');
			$days[4] = JText::_('JEV_THURSDAY');
			$days[5] = JText::_('JEV_FRIDAY');
			$days[6] = JText::_('JEV_SATURDAY');
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
	 * @param	int		daynb	# of day
	 * @param	int		array, 0 return single day, 1 return array of all days
	 * @return	mixed	localised short day letter or array of names
	 * */
	public static
			function getShortDayName($daynb = 0, $array = 0)
	{

		static $days = null;

		if ($days === null)
		{
			$days = array();

			$days[0] = JText::_('JEV_SUN');
			$days[1] = JText::_('JEV_MON');
			$days[2] = JText::_('JEV_TUE');
			$days[3] = JText::_('JEV_WED');
			$days[4] = JText::_('JEV_THU');
			$days[5] = JText::_('JEV_FRI');
			$days[6] = JText::_('JEV_SAT');
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
			$cfg = JEVConfig::getInstance();
			$format_type = $cfg->get('com_dateformat');
		}

		// if date format is from langauge file then do this first
		if ($format_type == 3)
		{
			if ($h >= 0 && $m >= 0)
			{
				$time = JevDate::mktime($h, $m);
				return JEV_CommonFunctions::jev_strftime(JText::_("TIME_FORMAT"), $time);
			}
			else
			{
				return JEV_CommonFunctions::jev_strftime(JText::_("TIME_FORMAT"), $date);
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
	 * @param	i
	 * @staticnt		daynb	# of day
	 * @param	int		array, 0 return single day, 1 return array of all days
	 * @return	mixed	localised short day letter or array of letters
	 * */
	public static
			function getWeekdayLetter($daynb = 0, $array = 0)
	{

		static $days = null;

		if ($days === null)
		{
			$days = array();
			$days[0] = JText::_('JEV_SUNDAY_CHR');
			$days[1] = JText::_('JEV_MONDAY_CHR');
			$days[2] = JText::_('JEV_TUESDAY_CHR');
			$days[3] = JText::_('JEV_WEDNESDAY_CHR');
			$days[4] = JText::_('JEV_THURSDAY_CHR');
			$days[5] = JText::_('JEV_FRIDAY_CHR');
			$days[6] = JText::_('JEV_SATURDAY_CHR');
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
	 * @param string $name - metatag name
	 * @param string $content - metatag value
	 */
	public static
			function checkRobotsMetaTag($name = "robots", $content = "index, follow")
	{

		// force robots metatag
		$cfg = JEVConfig::getInstance();
		$document = JFactory::getDocument();
		if ($cfg->get('com_blockRobots', 0) >= 1)
		{			
			// Allow on content pages
			if ($cfg->get('com_blockRobots', 0) == 3)
			{
				if (strpos(JRequest::getString("jevtask", ""), ".detail") > 0)
				{
					$document->setMetaData($name, "nofollow");
					return;
				}
				$document->setMetaData($name, $content);
				return;
			}
			if ($cfg->get('com_blockRobots', 0) == 1)
			{
				$document->setMetaData($name, $content);
				return;
			}
			list($cyear, $cmonth, $cday) = JEVHelper::getYMD();
			$cdate = JevDate::mktime(0, 0, 0, $cmonth, $cday, $cyear);
			$prior = JevDate::strtotime($cfg->get('robotprior', "-1 day"));
			if ($cdate < $prior && $cfg->get('com_blockRobots', 0))
			{
				$document->setMetaData($name, $content);
				return;
			}
			$post = JevDate::strtotime($cfg->get('robotpost', "-1 day"));
			if ($cdate > $post && $cfg->get('com_blockRobots', 0))
			{
				$document->setMetaData($name, $content);
				return;
			}
		}
		//If JEvents is not blocking robots we use menu item configuration
		else
		{
			$document->setMetaData($name, $cfg->get('robots', $content));
		}

	}

	//New MetaSet Function, to set the meta tags if they exist in the Menu Item

	static public
			function SetMetaTags()
	{
		//Get Document to set the Meta Tags to.
		$document = JFactory::getDocument();

		//Get the Params.
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

		if ($params->get('menu-meta_description'))
		{
			$document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			$document->setMetaData('keywords', $params->get('menu-meta_keywords'));
		}

	}

	public static
			function forceIntegerArray(&$cid, $asString = true)
	{
		for ($c = 0; $c < count($cid); $c++)
		{
			$cid[$c] = intval($cid[$c]);
		}
		if ($asString)
		{
			$id_string = implode(",", $cid);
			return $id_string;
		}
		else
		{
			return "";
		}

	}

	/**
	 * Loads all necessary files for and creats popup calendar link
	 * 
	 * @static
	 */
	public static
			function loadCalendar($fieldname, $fieldid, $value, $minyear, $maxyear, $onhidestart = "", $onchange = "", $format = 'Y-m-d')
	{
		$document = JFactory::getDocument();
		$component = "com_jevents";
		$params = JComponentHelper::getParams($component);
		$forcepopupcalendar = $params->get("forcepopupcalendar", 1);
		$offset = $params->get("com_starday", 1);

		list ($yearpart, $monthpart, $daypart) = explode("-", $value);
		$value = str_replace(array("Y", "m", "d"), array($yearpart, $monthpart, $daypart), $format);

		$calendar = (JevJoomlaVersion::isCompatible("3.0")) ? 'calendar14.js' : 'calendar12.js';
		JEVHelper::script($calendar, "components/" . $component . "/assets/js/", true);
		JEVHelper::stylesheet("dashboard.css", "components/" . $component . "/assets/css/", true);
		$script = '
				var field' . $fieldid . '=false;
				window.addEvent(\'domready\', function() {
				if (field' . $fieldid . ') return;
				field' . $fieldid . '=true;
				new NewCalendar(
					{ ' . $fieldid . ' :  "' . $format . '"},
					{
					direction:0, 
					classes: ["dashboard"],
					draggable:true,
					navigation:2,
					tweak:{x:0,y:-75},
					offset:' . $offset . ',
					range:{min:' . $minyear . ',max:' . $maxyear . '},
					readonly:' . $forcepopupcalendar . ',
					months:["' . JText::_("JEV_JANUARY") . '",
					"' . JText::_("JEV_FEBRUARY") . '",
					"' . JText::_("JEV_MARCH") . '",
					"' . JText::_("JEV_APRIL") . '",
					"' . JText::_("JEV_MAY") . '",
					"' . JText::_("JEV_JUNE") . '",
					"' . JText::_("JEV_JULY") . '",
					"' . JText::_("JEV_AUGUST") . '",
					"' . JText::_("JEV_SEPTEMBER") . '",
					"' . JText::_("JEV_OCTOBER") . '",
					"' . JText::_("JEV_NOVEMBER") . '",
					"' . JText::_("JEV_DECEMBER") . '"
					],
					days :["' . JText::_("JEV_SUNDAY") . '",
					"' . JText::_("JEV_MONDAY") . '",
					"' . JText::_("JEV_TUESDAY") . '",
					"' . JText::_("JEV_WEDNESDAY") . '",
					"' . JText::_("JEV_THURSDAY") . '",
					"' . JText::_("JEV_FRIDAY") . '",
					"' . JText::_("JEV_SATURDAY") . '"
					]
					';
		if ($onhidestart != "")
		{
			$script.=',
					onHideStart : function () { ' . $onhidestart . '; },
					onHideComplete :function () { ' . $onchange . '; }';
		}
		$script.='}
				);
			});';

		// stop same field script being loaded multiple times
		static $processedfields = array();
		if (!in_array($fieldname, $processedfields))
		{
			$document->addScriptDeclaration($script);
		}
		$processedfields[] = $fieldname;

		if ($onchange != "")
		{
			$onchange = 'onchange="' . $onchange . '"';
		}
		echo '<input type="text" name="' . $fieldname . '" id="' . $fieldid . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" maxlength="10" ' . $onchange . ' size="12"  />';

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
			if ($cfg->get("com_enableToolTip", 1) || JFactory::getApplication()->isAdmin())
			{
				$document = JFactory::getDocument();
				// RSH 10/11/10 - Check location of overlib files - j!1.6 doesn't include them!
				JHTML::script('components/' . JEV_COM_COMPONENT . '/assets/js/overlib_mini.js');
				JHTML::script('components/' . JEV_COM_COMPONENT . '/assets/js/overlib_hideform_mini.js');

				// change state so it isnt loaded a second time
				$cfg->set('loadOverlib', true);

				if ($cfg->get("com_calTTShadow", 1) && !JFactory::getApplication()->isAdmin())
				{
					JHTML::script('components/' . JEV_COM_COMPONENT . '/assets/js/overlib_shadow.js');
				}
				if (!JFactory::getApplication()->isAdmin())
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

	/**
	 * find suitable menu item for displaying an event
	 *
	 * @param mixed $forcecheck - false = no check.  jIcalEventRepeat = should we check the access for the event.  Only checks categories at present.
	 * @return integer - menu item id
	 */
	public static
			function getItemid($forcecheck = false, $skipbackend = true)
	{
		if (JFactory::getApplication()->isAdmin() && $skipbackend)
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
			$menu = JFactory::getApplication()->getMenu();
			$active = $menu->getActive();
			$Itemid = JRequest::getInt("Itemid");
			if (is_null($active))
			{
				// wierd bug in Joomla when SEF is disabled but with xhtml urls sometimes &amp;Itemid is misinterpretted !!!
				$Itemid = JRequest::getInt("Itemid");
				if ($Itemid > 0 && $jevitemid[$evid] != $Itemid)
				{
					$active = $menu->getItem($Itemid);
				}
			}
			$option = JRequest::getCmd("option");
			// wierd bug in Joomla when SEF is disabled but with xhtml urls sometimes &amp;Itemid is misinterpretted !!!
			if ($Itemid == 0)
				$Itemid = JRequest::getInt("amp;Itemid", 0);
			if ($option == JEV_COM_COMPONENT && $Itemid > 0 && JRequest::getCmd("task") != "crawler.listevents" && JRequest::getCmd("jevtask") != "crawler.listevents")
			{
				$jevitemid[$evid] = $Itemid;
				return $jevitemid[$evid];
			}
			else if (!is_null($active) && $active->component == JEV_COM_COMPONENT && strpos($active->link, "admin") === false && strpos($active->link, "crawler") === false)
			{
				$jevitemid[$evid] = $active->id;
				return $jevitemid[$evid];
			}
			else
			{
				$jevitems = $menu->getItems("component", JEV_COM_COMPONENT);
				// TODO second level Check on enclosing categories and other constraints
				if (count($jevitems) > 0)
				{
					$user = JFactory::getUser();
					foreach ($jevitems as $jevitem)
					{
						if (version_compare(JVERSION, '1.6.0', '>=') ? in_array($jevitem->access, JEVHelper::getAid($user, 'array')) : JEVHelper::getAid($user) >= $jevitem->access)
						{
							$jevitemid[$evid] = $jevitem->id;

							if ($forcecheck)
							{
								$mparams = is_string($jevitem->params) ? new JRegistry($jevitem->params) : $jevitem->params;
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

	public static
			function getAdminItemid()
	{
		static $jevitemid;
		if (!isset($jevitemid))
		{
			$jevitemid = 0;
			$menu = JFactory::getApplication()->getMenu();
			$active = $menu->getActive();
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
					$user = JFactory::getUser();
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
		$firstpos = substr($year, 0, 1);

		if ($firstpos == "+")
		{
			$year = substr($year, 1);
			$year = $yearnow + $year;
		}
		else if ($firstpos == "-")
		{
			$year = substr($year, 1);
			$year = $yearnow - $year;
		}
		//If we do not get a 4 digit number and no sign we assume it's +$year
		else if (strlen($year) < 4)
		{
			$cuenta = count($year);
			$year = $yearnow + $year;
		}

		return $year;

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

		if (!isset($data))
		{
			$datenow = JEVHelper::getNow();
			list($yearnow, $monthnow, $daynow) = explode('-', $datenow->toFormat('%Y-%m-%d'));

			$year = min(2100, abs(intval(JRequest::getVar('year', $yearnow))));
			$month = min(99, abs(intval(JRequest::getVar('month', $monthnow))));
			$day = min(3650, abs(intval(JRequest::getVar('day', $daynow))));
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
			$lastDayOfMonth = intval(strftime("%d", mktime(6, 0, 0, $month + 1, 1, $year) - 86400));
			$day = $lastDayOfMonth < $day ? $lastDayOfMonth : $day;

			$data = array();
			$data[] = $year;
			$data[] = $month;
			$data[] = $day;
		}
		return $data;

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
			$compparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
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
			$user = JEVHelper::getAuthorisedUser();
			if (is_null($user))
			{
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					$juser = JFactory::getUser();
					$isEventCreator = $juser->authorise('core.create', 'com_jevents');
					// this is too heavy on database queries - keep this in the file so that sites that want to use this approach can uncomment this block
					if (false)
					{
						if (!$isEventCreator)
						{
							$cats = JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.create');
							if (count($cats) > 0)
							{
								$isEventCreator = true;
							}
						}
					}
					else
					{
						if ($isEventCreator)
						{
							$okcats = JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.create');
							if (count($okcats) > 0)
							{
								$juser = JFactory::getUser();
								$dataModel = new JEventsDataModel();
								$dataModel->setupComponentCatids();

								$allowedcats = explode(",", $dataModel->accessibleCategoryList());
								$intersect = array_intersect($okcats, $allowedcats);

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
			}
			else if ($user->cancreate)
			{
				// Check maxevent count
				if ($user->eventslimit > 0)
				{
					$db = JFactory::getDBO();
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

					$juser = JFactory::getUser();
					$dataModel = new JEventsDataModel();
					$dataModel->setupComponentCatids();

					$allowedcats = explode(",", $dataModel->accessibleCategoryList());
					$intersect = array_intersect($okcats, $allowedcats);

					if (count($intersect) == 0)
					{
						$isEventCreator = false;
					}
				}
			}

			JPluginHelper::importPlugin("jevents");
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('isEventCreator', array(& $isEventCreator));
		}
		return $isEventCreator;

	}

	/**
	 * Test to see if user can create event within the specified category
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
	 * @return unknown
	 */
	public static
			function canCreateEvent($row, $user = null)
	{
		// TODO make this call a plugin
		if ($user == null)
		{
			$user = JFactory::getUser();
		}
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		if (!$authorisedonly)
		{
			if ($user->authorise('core.create', 'com_jevents'))
				return true;
			$allowedcats = JEVHelper::getAuthorisedCategories($user, 'com_jevents', 'core.create');
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

	// is the user an event editor - i.e. can edit own and other events
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
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					$juser = JFactory::getUser();
					// Never allow unlogged in users to edit events - just in case someone tries to allow this
					if ($juser->id == 0)
					{
						return false;
					}
					//$isEventEditor = JAccess::check($juser->id, "core.edit","com_jevents");
					$isEventEditor = $juser->authorise('core.edit', 'com_jevents');
				}
			}
			/*
			  $user = JEVHelper::getAuthorisedUser();
			  if (is_null($user)){
			  $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			  $editorLevel= $params->get("jeveditor_level",20);
			  $juser = JFactory::getUser();
			  if (JEVHelper::getGid($user)>=$editorLevel){
			  $isEventEditor = true;
			  }
			  }
			 */
			else if ($user->canedit)
			{
				$isEventEditor = true;
			}
		}
		return $isEventEditor;

	}

	/**
	 * Test to see if user can edit event
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
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
			$user = JFactory::getUser();
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
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		if ($authorisedonly)
		{
			if ($jevuser && $jevuser->published)
			{
				// creator can edit their own event
				if ($jevuser->cancreate && $row->created_by() == $user->id)
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
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					$juser = JFactory::getUser();
					//$isEventPublisher[$type]  = JAccess::check($juser->id, "core.edit.state","com_jevents");
					$isEventPublisher[$type] = $juser->authorise('core.edit.state', 'com_jevents');
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

			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('isEventPublisher', array($type, & $isEventPublisher[$type]));
		}


		return $isEventPublisher[$type];

	}

	// Fall back test to see if user can publish their own events based on config setting
	public static
			function canPublishOwnEvents($evid)
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 1);
		$publishown = $params->get("jevpublishown", 0);

		$jevuser = JEVHelper::getAuthorisedUser();
		$user = JFactory::getUser();

		if (!$authorisedonly && $publishown)
		{

			// can publish all?
			if (JEVHelper::isEventPublisher(true))
			{
				return true;
			}
			else if ($evid == 0)
			{
				return true;
			}
			$dataModel = new JEventsDataModel("JEventsAdminDBModel");
			$queryModel = new JEventsDBModel($dataModel);

			$evid = intval($evid);
			$testevent = $queryModel->getEventById($evid, 1, "icaldb");
			if ($testevent->ev_id() == $evid && $testevent->created_by() == $user->id)
			{
				return true;
			}
		}

		if ($authorisedonly && $jevuser && $jevuser->canpublishown)
		{
			$dataModel = new JEventsDataModel("JEventsAdminDBModel");
			$queryModel = new JEventsDBModel($dataModel);

			$evid = intval($evid);
			$testevent = $queryModel->getEventById($evid, 1, "icaldb");
			if ($testevent->ev_id() == $evid && $testevent->created_by() == $user->id)
			{
				return true;
			}
		}
		return false;

	}

	// gets a list of categories for which this user is the admin
	public static
			function categoryAdmin()
	{
		if (!JEVHelper::isEventPublisher())
			return false;
		$juser = JFactory::getUser();

		$db = JFactory::getDBO();
		$sql = "SELECT id FROM #__categories WHERE extension='com_jevents' AND params like ('%\"admin\":\"" . $juser->id . "\"%')";
		$db->setQuery($sql);
		$catids = $db->loadColumn();
		if (count($catids) > 0)
			return $catids;
		return false;

	}

	/**
	 * Test to see if user can publish event
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
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
			$user = JFactory::getUser();
		}
		// are we authorised to do anything with this category or calendar
		$jevuser = JEVHelper::getAuthorisedUser();
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		if ($authorisedonly)
		{
			if (!$jevuser)
			{
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
			$key = $row->catids() ? json_encode($row->catids()) : json_encode(intval($row->catid()));
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

			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 1);
			$publishown = $params->get("jevpublishown", 0);
			if (!$authorisedonly && $publishown)
			{
				return true;
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

	// is the user an event publisher - i.e. can publish own OR other events
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
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					$juser = JFactory::getUser();
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
	 * Test to see if user can delete event
	 *
	 * @param unknown_type $row
	 * @param unknown_type $user
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
			$user = JFactory::getUser();
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
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
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
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 1);
			$publishown = $params->get("jevpublishown", 0);
			if (!$authorisedonly && ($publishown || JEVHelper::canPublishEvent($row, $user)))
			{
				return true;
			}
		}
		return false;

	}

	/**
	 * Returns contact details or user details as fall back
	 *
	 * @param int id		key of user
	 * @param string attrib	Requested attribute of the user object
	 * @return mixed row	Attribute or row object
	 */
	public static
			function getContact($id, $attrib = 'Object')
	{

		$db = JFactory::getDBO();

		static $rows = array();

		if ($id <= 0)
		{
			return null;
		}

		if (!isset($rows[$id]))
		{
			$user = JFactory::getUser();
			$rows[$id] = null;
			$query = "SELECT ju.id, ju.name, ju.username, ju.sendEmail, ju.email, cd.name as contactname, "
					. ' CASE WHEN CHAR_LENGTH(cd.alias) THEN CONCAT_WS(\':\', cd.id, cd.alias) ELSE cd.id END as slug, '
					. ' CASE WHEN CHAR_LENGTH(cat.alias) THEN CONCAT_WS(\':\', cat.id, cat.alias) ELSE cat.id END AS catslug '
					. " \n FROM #__users AS ju"
					. "\n LEFT JOIN #__contact_details AS cd ON cd.user_id = ju.id "
					. "\n LEFT JOIN #__categories AS cat ON cat.id = cd.catid "
					. "\n WHERE block ='0'"
					. "\n AND cd.published =1 "
					. "\n AND cd.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user))
					. "\n AND cat.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user))
					. "\n AND ju.id = " . $id;

			$db->setQuery($query);
			$rows[$id] = $db->loadObject();
			if (is_null($rows[$id]))
			{
				// if the user has been deleted then try to suppress the warning
				// this causes a problem in Joomla 2.5.1 on some servers
				if (version_compare(JVERSION, '2.5', '>='))
				{
					$rows[$id] = JEVHelper::getUser($id);
				}
				else
				{
					$handlers = JError::getErrorHandling(2);
					JError::setErrorHandling(2, "ignore");
					$rows[$id] = JEVHelper::getUser($id);
					foreach ($handlers as $handler)
					{
						if (!is_array($handler))
							JError::setErrorHandling(2, $handler);
					}
					if ($rows[$id])
					{
						$error = JError::getError(true);
					}
				}
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
	 * Get user details for authorisation testing
	 *
	 * @param int $id Joomla user id
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
			$juser = JFactory::getUser();
			$id = $juser->id;
		}
		if (!array_key_exists($id, $userarray))
		{
			JLoader::import("jevuser", JPATH_ADMINISTRATOR . "/components/" . JEV_COM_COMPONENT . "/tables/");

			$user = new TableUser();

			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
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

	/*
	 * Our own version that caches the results - the Joomla one doesn't!!!
	 */

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

	static public
			function isAdminUser($user = null)
	{
		if (is_null($user))
		{
			$user = JFactory::getUser();
		}
		//$access = JAccess::check($user->id, "core.admin","com_jevents");
		$access = $user->authorise('core.admin', 'com_jevents');
		return $access;

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

		if (file_exists(JPATH_BASE . '/' . 'templates' . '/' . JFactory::getApplication()->getTemplate() . '/' . 'html' . '/' . JEV_COM_COMPONENT . '/' . $view->jevlayout . '/' . "assets" . '/' . "css" . '/' . $filename))
		{
			JEVHelper::stylesheet($filename, 'templates/' . JFactory::getApplication()->getTemplate() . '/html/' . JEV_COM_COMPONENT . '/' . $view->jevlayout . "/assets/css/");
		}
		else
		{
			JEVHelper::stylesheet($filename, 'components/' . JEV_COM_COMPONENT . "/views/" . $view->jevlayout . "/assets/css/");
		}

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
			$user = JFactory::getUser();
		}
		return max(JAccess::getGroupsByUser($user->id));  // RSH trying to get a gid for J!1.6

	}

	static public
			function getAid($user = null, $type = 'string')
	{
		if (is_null($user) || !$user)
		{
			$user = JFactory::getUser();
		}
		$root = $user->get("isRoot");
		if ($root)
		{
			static $rootlevels = false;
			if (!$rootlevels)
			{
				// Get a database object.
				$db = JFactory::getDBO();

				// Build the base query.
				$query = $db->getQuery(true);
				$query->select('id, rules');
				$query->from($query->qn('#__viewlevels'));

				// Set the query for execution.
				$db->setQuery((string) $query);
				$rootlevels = $db->loadColumn();
				JArrayHelper::toInteger($rootlevels);
			}
			$levels = $rootlevels;
		}
		else
		{
			$levels = $user->getAuthorisedViewLevels();
			if (JEVHelper::isAdminUser($user) && JFactory::getApplication()->isAdmin())
			{
				// Make sure admin users can see public events
				$levels = array_merge($levels, JAccess::getAuthorisedViewLevels(0));
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
			function getUserType($user = null)
	{
		if (is_null($user))
		{
			$user = JFactory::getUser();
		}
		$groups = $user->groups;  // RSH 10/17/10 - Get groups, sort them, get the last one, return the value
		asort($groups);
		$last_group = end($groups);
		return ($last_group == 'Super Users') ? "Super Administrator" : $last_group;

	}

	static public
			function stylesheet($file, $path)
	{
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!

		JHTML::stylesheet($path . $file);

	}

	static public
			function script($file, $path)
	{

		if (JComponentHelper::getParams(JEV_COM_COMPONENT)->get("usejquery",0)) {
			// load jQuery versions
			if (strpos($file, "JQ.js")==false) {
				$file = str_replace(".js", "JQ.js", $file);
				// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
				JHTML::script($path . $file);
			}
		}
		else {
			// Include mootools framework
			JHtml::_('behavior.framework', true);

			// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
			JHTML::script($path . $file);
		}

	}

	static public
			function setupJoomla160()
	{

	}

	static public
			function getBaseAccess()
	{
		// Store the ical in the registry so we can retrieve the access level
		$registry = JRegistry::getInstance("jevents");
		$icsfile = $registry->get("jevents.icsfile", false);
		if ($icsfile)
		{
			return $icsfile->access;
		}
		static $base;
		if (!isset($base))
		{
			// NB this method is no use if you delete the public access level - it assumes that 1 always exists!!!
			//$levels = JAccess::getAuthorisedViewLevels(0);
			$levels = array();
			if (count($levels) > 0)
			{
				$base = $levels[0];
			}
			else
			{
				// Get a database object.
				$db = JFactory::getDBO();

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
		return JHTML::_('image', 'system/' . $img, $text, NULL, true);

	}

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
		JArrayHelper::toInteger($catids);
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
			JArrayHelper::toInteger($catids);
			$row->_catidsarray = $catids;
			return $catids;
		}
		return false;

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
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		// only unlogged in users and not logged in OR all visitors grouped by access level 
		if (($params->get("com_cache", 1) == 1 && $user->id == 0) || $params->get("com_cache", 1) == 2)
		{

			$cachecontroller = JFactory::getCache(JEV_COM_COMPONENT);
			$oldcaching = $cachecontroller->cache->getCaching();
			$cachecontroller->cache->setCaching(true);

			// if grouped by access level caching then add this to the cache id
			$cachegroups = ($params->get("com_cache", 1) == 2) ? implode(',', $user->getAuthorisedViewLevels()) : "";
			$lang = JFactory::getLanguage()->getTag();

			$rows = array();
			$indexmap = array();
			foreach ($icalrows as $index => & $row)
			{
				$indexmap[$row->rp_id()] = $index;
				$id = md5($row->rp_id() . " onDisplayCustomFieldsMultiRow " . $row->uid() . " " . $row->title() . "-" . $cachegroups . $lang);
				$data = $cachecontroller->cache->get($id);
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
				JPluginHelper::importPlugin('jevents');
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger('onDisplayCustomFieldsMultiRow', array(&$rows));
				foreach ($rows as $k => $row)
				{
					$id = md5($row->rp_id() . " onDisplayCustomFieldsMultiRow " . $row->uid() . " " . $row->title() . "-" . $cachegroups . $lang);
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
					$index = $indexmap[$row->rp_id()];
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
			JPluginHelper::importPlugin('jevents');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onDisplayCustomFieldsMultiRow', array(&$icalrows));
		}

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
		$condlabel = $element['label'];
		if ($conditional == "creator")
		{
			$conditional = "jev_creatorid";
		}
		if ($conditional == "location")
		{
			$conditional = "evlocation";
		}
		if (strpos("@", $conditional) >= 0)
		{
			$conditional = str_replace("@", "_", $conditional);
		}
		$condarray = (string) $element['conditions'];
		$condtype = (string) $element['type'];
		$fielddefault = (string) $element['default'];
		$multi = (string) $element['multiple'];
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

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$conditionarray = explode(",", $condarray);
		if (in_array($params->get($conditions, "default"), $conditionarray) == TRUE && $component != "com_config.component")
		{
			$conditionarray[] = "global";
		}
		$condarray = "'" . (string) implode("','", $conditionarray) . "'";
		$fielddefaultarray = "'" . (string) str_replace(",", "','", $fielddefault) . "'";

		$script = <<<SCRIPT
			var jevConditional = {
					setupJevConditions: function(conditional,fielddefault,condlabel ,condparam, conditions, fieldparam,condarray,fielddefaultarray) {
						var condition = $(condparam+ conditions);
						var radioElements = condition.getElements('input[type=radio]');
						if (radioElements.length > 0) {
							for (var i = 0; i < radioElements.length; i++) {
								// Need both for Chosen replacements
								radioElements[i].addEvent("click", function() {
									jevConditional.jevCondition(conditional,fielddefault,condlabel ,condparam, conditions, fieldparam,condarray,fielddefaultarray)
								});
								radioElements[i].addEvent("change", function() {
									jevConditional.jevCondition(conditional,fielddefault,condlabel ,condparam, conditions, fieldparam,condarray,fielddefaultarray)
								});
							}
						}
						else if (condition.tagName == "SELECT") {
							if ($(condition.id + "_chzn")) {
								var condition_chzn = $(condition.id + "_chzn");
								for (var i = 0; i < condition_chzn.childNodes.length; i++) {
									condition_chzn.childNodes[i].addEvent("click", function() {
										jevConditional.jevCondition(conditional,fielddefault,condlabel ,condparam, conditions, fieldparam,condarray,fielddefaultarray)
									});
								}
							}
							else
								condition.addEvent("change", function() {
									jevConditional.jevCondition(conditional,fielddefault,condlabel ,condparam, conditions, fieldparam,condarray,fielddefaultarray)
								});
						}
						else {
							condition.addEvent("change", function() {
								jevConditional.jevCondition(conditional,fielddefault,condlabel ,condparam, conditions, fieldparam,condarray,fielddefaultarray)
							});
						}
						jevConditional.jevCondition(conditional,fielddefault,condlabel ,condparam, conditions, fieldparam,condarray,fielddefaultarray)
					},
					jevCondition: function(conditional,fielddefault,condlabel ,condparam, conditions, fieldparam,condarray,fielddefaultarray) {
						var condition = $(condparam + conditions);
						var eventsno = $(fieldparam+conditional);
						if (!condition || !eventsno){
							return;
						}
						// Joomla 3.x named element is inside control and also control-group elements
						var hiddencontrol = eventsno.parentNode.parentNode;
						// Joomla 2.5
						if (hiddencontrol.tagName=="UL"){
							hiddencontrol = eventsno.parentNode;
						}
						var conditionsarray = new Array(condarray);
						if (condition.type == "checkbox") {
							condition.value = condition.checked;
						}
						var radioElements = condition.getElements('input[type=radio]');
						if (radioElements.length>0) {
							for (var i = 0; i < radioElements.length; i++) {
								if (radioElements[i].checked)
									condition.value = radioElements[i].value;
							}
						}
						if (condition.multiple && condition.tagName == "SELECT") {
							for (var i = 0; i < condition.options.length; i++) {
								if (condition.options[i].selected && conditionsarray.indexOf(condition.options[i].value) >= 0)
									conditionsarray[0] = condition.value;
							}
						}
						
						var checkboxElements = condition.type=="checkbox"? new Array(condition) : condition.getElements('input[type=checkbox]');				
						if (checkboxElements.length>0) {
							condition.value = new Array();
							for (var i = 0; i < checkboxElements.length; i++) {
								if (checkboxElements[i].checked && conditionsarray.indexOf(checkboxElements[i].value) >= 0) {
									condition.value = conditionsarray[0];
								}
							}
						}
						// If condition is valid then show the row
						if (conditionsarray.indexOf(condition.value) >= 0) {
							if (hiddencontrol.tagName == "TR") {
								hiddencontrol.style.display = "table-row";
							}
							else if (hiddencontrol.tagName == "SPAN"){
								hiddencontrol.style.display = "inline";
							}
							else
								hiddencontrol.style.display = "block";
						}
						// else hide the row and revert the value to its default
						else {
							// Is the dependent field is a select list, or radio list
							if (eventsno && eventsno.options){
								var defaultarray = new Array(fielddefaultarray);
								for (var i = 0; i < eventsno.options.length; i++) {
									if (defaultarray.indexOf(eventsno.options[i].value) >= 0) {
										eventsno.options[i].selected = true;
									}
									else
										eventsno.options[i].selected = false;
								}
							}
							else {
								eventsno.value = fielddefault;
							}
							hiddencontrol.style.display = "none";
						}
						try {
							jQuery(eventsno).trigger("liszt:updated");
						}
						catch (e) {
						}
					}
				}
SCRIPT;
		$document = JFactory::getDocument();
		static $loadedScript = false;
		if (!$loadedScript)
		{
			$document->addScriptDeclaration($script);
			$loadedScript = true;
		}

		$script = <<<SCRIPT
				window.addEvent('load', function() {
					jevConditional.setupJevConditions('$conditional','$fielddefault', '$condlabel' ,'$condparam', '$conditions', '$fieldparam', $condarray, $fielddefaultarray);
				});
SCRIPT;

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);

	}

	public static
			function processLiveBookmmarks()
	{

		$cfg = JEVConfig::getInstance();
		if ($cfg->get('com_rss_live_bookmarks'))
		{
			$Itemid = JRequest::getInt('Itemid', 0);
			$rssmodid = $cfg->get('com_rss_modid', 0);
			// do not use JRoute since this creates .rss link which normal sef can't deal with
			$rssLink = 'index.php?option=' . JEV_COM_COMPONENT . '&amp;task=modlatest.rss&amp;format=feed&amp;type=rss&amp;Itemid=' . $Itemid . '&amp;modid=' . $rssmodid;
			$rssLink = JUri::root() . $rssLink;

			if (method_exists(JFactory::getDocument(), "addHeadLink"))
			{
				$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
				JFactory::getDocument()->addHeadLink($rssLink, 'alternate', 'rel', $attribs);
			}

			$rssLink = 'index.php?option=' . JEV_COM_COMPONENT . '&amp;task=modlatest.rss&amp;format=feed&amp;type=atom&amp;Itemid=' . $Itemid . '&amp;modid=' . $rssmodid;
			$rssLink = JUri::root() . $rssLink;
			//$rssLink = JRoute::_($rssLink);
			if (method_exists(JFactory::getDocument(), "addHeadLink"))
			{
				$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
				JFactory::getDocument()->addHeadLink($rssLink, 'alternate', 'rel', $attribs);
			}
		}

	}

	/**
	 * Get filter values from database based on URL
	 */
	public static
			function getFilterValues()
	{
		// This is new experimental code that is disabled for the time being
		return;
		$fid = JFactory::getApplication()->input->getInt("jfilter", 0);
		if ($fid > 0)
		{
			$db = JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__jevents_filtermap where fid = " . $fid);
			$filter = $db->loadObject();

			// does this filter belong to this user (needed ??)
			$user = JFactory::getUser();
			if ($filter)
			{

				$filtervars = json_decode($filter->filters);
				if (is_object($filtervars)){
					$filtervars = get_object_vars($filtervars);
				}
				var_dump($filtervars);
				if (is_array($filtervars))
				{
					foreach ($filtervars as $fvk => $fvv)
					{
						if (strpos($fvk, "_fv") > 0)
						{
							JRequest::setVar($fvk , $fvv);
						}
					}
				}

			}
		}

		else {
			JEVHelper::setFilterValues();
		}

	}

	/**
	 * Set filter values in database based on URL and redirect
	 */
	public static
			function setFilterValues()
	{

		$input = JRequest::get();

		$filtervars = array();

		$input = JRequest::get();
		if (is_array($input))
		{
			foreach ($input as $fvk => $fvv)
			{
				if (strpos($fvk, "_fv") > 0)
				{
					$filtervars[$fvk] = $fvv;
				}
			}
		}

		if (count($filtervars)>0){
			ksort($filtervars);
			var_dump($filtervars);
			$filtervars = json_encode($filtervars);

			$db = JFactory::getDbo();
			// check for any matching filters first
			$md5 = md5($filtervars);

			$db->setQuery("SELECT fid, filters  FROM #__jevents_filtermap where md5 = " . $db->quote($md5));
			$filters = $db->loadAssocList("fid","filters");

			if (!in_array($filtervars, $filters)){
				$db->setQuery("INSERT INTO #__jevents_filtermap (filters, md5) VALUES (" . $db->quote($filtervars).",".$db->quote($md5).")");
				$db->query();
			}
		}

	}

	public static
			function parameteriseJoomlaCache()
	{

// If Joomla caching is enabled then we have to manage progressive caching and ensure that session data is taken into account.
		$conf = JFactory::getConfig();
		if ($conf->get('caching', 1))
		{
			// Joomla  3.0 safe cache parameters
			$safeurlparams = array('catids' => 'STRING', 'Itemid' => 'STRING', 'task' => 'STRING', 'jevtask' => 'STRING', 'jevcmd' => 'STRING', 'view' => 'STRING', 'layout' => 'STRING', 'evid' => 'INT', 'modid' => 'INT', 'year' => 'INT', 'month' => 'INT', 'day' => 'INT', 'limit' => 'UINT', 'limitstart' => 'UINT', 'jfilter' => 'STRING');
			$app = JFactory::getApplication();

			$filtervars = JRequest::get();
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
							//echo $fvk."= ".$fvv."<br/>";;
						}
					}
				}
			}

			$session = JFactory::getSession();
			$sessionregistry = $session->get('registry');
			$sessionArray = isset($sessionregistry) ? $sessionregistry->toArray() : false;
			$sessionArrayData = array();
			if (is_array($sessionArray))
			{
				$specialcount = 0;
				foreach ($sessionArray as $sak => $sav)
				{
					if (strpos($sak, "_fv_ses") > 0)
					{
						$sessionArrayData[$sak] = $sav;
						$specialcount += (($sak == "published_fv_ses" || $sak == "justmine_fv_ses") && $sav == 0) ? 1 : 0;
					}
				}
				// special case when published and justmine the only filters and these are the default values
				if (count($sessionArrayData) == 2 && $specialcount == 2)
				{
					$sessionArrayData = array();
				}
			}
			if ($sessionArrayData > 0)
			{
				$safeurlparams["sessionArray"] = "STRING";
				//var_dump($sessionArrayData);
				JRequest::setVar("sessionArray", md5(serialize($sessionArrayData)));

				// if we have session data then stock progressive caching
				if ($conf->get('caching', 1) == 2)
				{
					$conf->set('caching', 1);
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
				// Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
				$registeredurlparams->$key = $value;
			}

			$app->registeredurlparams = $registeredurlparams;
		}

	}

	/**
	 * Get an user object.
	 *
	 * JEvents version that doesn't throw error message when user doesn't exist
	 * 
	 * Returns the global {@link JUser} object, only creating it if it doesn't already exist.
	 *
	 * @param   integer  $id  The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 *
	 * @return  JUser object
	 *
	 * @see     JUser
	 * @since   11.1
	 */
	public static function getUser($id = null)
	{
		if (is_null($id) || $id==0)
		{
			return JFactory::getUser($id);
		}
		else
		{
			static $tested = array();
			if (!isset($tested[$id])){
				// Initialise some variables
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__users'));
				$query->where($db->quoteName('id') . ' = ' . $db->quote($id));
				$db->setQuery($query, 0, 1);
				$tested[$id] = $db->loadResult();
			}
			if (!$tested[$id]) {
				return false;
			}
			return JFactory::getUser($id);
		}

	}
        // We use this for RSVP Pro Invites with iCal mail and New & Event change notifcations at present to avoid code duplication.
        public static function iCalMailGenerator($row, $params, $ics_method = "PUBLISH" ) {        
                        if ($ics_method == "CANCEL") {
                                $status = "CANCELLED";
                        }
                        if (JFile::exists(JPATH_SITE."/plugins/jevents/jevnotify/")) {
                            //If using JEvents notify plugin we need to load it for the processing of data.
                                JLoader::register('JEVNotifyHelper',JPATH_SITE."/plugins/jevents/jevnotify/helper.php");
                        }
                        
			$icalEvents = array($row);
			if (ob_get_contents()) ob_end_clean();
			$html = "";
                        $params = JComponentHelper::getParams("com_jevents");
                        
			if ($params->get('outlook2003icalexport'))
				$html .= "BEGIN:VCALENDAR\r\nPRODID:JEvents 3.1 for Joomla//EN\r\n";
			else
				$html .= "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:JEvents 3.1 for Joomla//EN\r\n";

			$html .= "CALSCALE:GREGORIAN\r\nMETHOD:" . $ics_method . "\r\n";
                        if (isset($status)) {
                            $html .= "STATUS:" . $status . "\r\n";
                            
                        }
			if (!empty($icalEvents))
			{

				ob_start();
				$tzid = self::vtimezone($icalEvents);
				$html .= ob_get_clean();

				// Build Exceptions dataset - all done in big batches to save multiple queries
				$exceptiondata = array();
				$ids = array();
				foreach ($icalEvents as $a)
				{
					$ids[] = $a->ev_id();
					if (count($ids) > 100)
					{
						$db = JFactory::getDBO();
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
					$db = JFactory::getDBO();
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
				$dispatcher =  JDispatcher::getInstance();
				ob_start();
				JEVHelper::onDisplayCustomFieldsMultiRow($icalEvents);
				ob_end_clean();

				foreach ($icalEvents as $a)
				{
					// if event has repetitions I must find the first one to confirm the dates
					if ($a->hasrepetition())
					{
						$a = $a->getOriginalFirstRepeat();
					}
					if (!$a)
						continue;
					$html .= "BEGIN:VEVENT\r\n";
					$html .= "UID:" . $a->uid() . "\r\n";
					$html .= "CATEGORIES:" . $a->catname() . "\r\n";
					if (!empty($a->_class))
						$html .= "CLASS:" . $a->_class . "\r\n";
					$html .= "SUMMARY:" . $a->title() . "\r\n";
					if ($a->location() != "")
					{
						if (!is_numeric($a->location()))
						{
							$html .= "LOCATION:" . self::wraplines(self::replacetags($a->location())) . "\r\n";
						}
						else if (isset($a->_loc_title))
						{
							$html .= "LOCATION:" . self::wraplines(self::replacetags($a->_loc_title)) . "\r\n";
						}
						else
						{
							$html .= "LOCATION:" . self::wraplines(self::replacetags($a->location())) . "\r\n";
						}
					}
					// We Need to wrap this according to the specs
					/* $html .= "DESCRIPTION:".preg_replace("'<[\/\!]*?[^<>]*?>'si","",preg_replace("/\n|\r\n|\r$/","",$a->content()))."\n"; */
					$html .= self::setDescription(strip_tags($a->content())) . "\r\n";

					if ($a->hasContactInfo())
						$html .= "CONTACT:" . self::replacetags($a->contact_info()) . "\r\n";
					if ($a->hasExtraInfo())
						$html .= "X-EXTRAINFO:" . self::wraplines(self::replacetags($a->_extra_info)) . "\r\n";
                                        $user = JFactory::getUser($a->created_by());
                                                        
                                        $html .= "ORGANIZER;CN=" . $user->name . ":MAILTO:" . $user->email . "\r\n";
					$alldayprefix = "";
					// No doing true timezones!
					if ($tzid == "" && is_callable("date_default_timezone_set"))
					{
						// UTC!
						$start = $a->getUnixStartTime();
						$end = $a->getUnixEndTime();

						// in case the first repeat has been changed
						if (array_key_exists($a->_eventid, $exceptiondata) && array_key_exists($a->rp_id(), $exceptiondata[$a->_eventid]))
						{
							$start = JevDate::strtotime($exceptiondata[$a->_eventid][$a->rp_id()]->oldstartrepeat);
						}

						// Change timezone to UTC
						$current_timezone = date_default_timezone_get();

						// If all day event then don't show the start time or end time either
						if ($a->alldayevent())
						{
							$alldayprefix = ";VALUE=DATE";
							$startformat = "%Y%m%d";
							$endformat = "%Y%m%d";

							// add 10 seconds to make sure its not midnight the previous night
							$start += 10;
							$end += 10;
						}
						else
						{
							date_default_timezone_set("UTC");

							$startformat = "%Y%m%dT%H%M%SZ";
							$endformat = "%Y%m%dT%H%M%SZ";
						}

						// Do not use JevDate version since this sets timezone to config value!
						$start = strftime($startformat, $start);
						$end = strftime($endformat, $end);

						$stamptime = strftime("%Y%m%dT%H%M%SZ", time());

						// Change back
						date_default_timezone_set($current_timezone);
					}
					else
					{
						$start = $a->getUnixStartTime();
						$end = $a->getUnixEndTime();

						// If all day event then don't show the start time or end time either
						if ($a->alldayevent())
						{
							$alldayprefix = ";VALUE=DATE";
							$startformat = "%Y%m%d";
							$endformat = "%Y%m%d";

							// add 10 seconds to make sure its not midnight the previous night
							$start += 10;
							$end += 10;
						}
						else
						{
							$startformat = "%Y%m%dT%H%M%S";
							$endformat = "%Y%m%dT%H%M%S";
						}

						$start = JevDate::strftime($startformat, $start);
						$end = JevDate::strftime($endformat, $end);

						if (is_callable("date_default_timezone_set"))
						{
							date_default_timezone_set("UTC");
							$stamptime = JevDate::strftime("%Y%m%dT%H%M%SZ", time());
							// Change back
							date_default_timezone_set($current_timezone);
						}
						else
						{
							$stamptime = JevDate::strftime("%Y%m%dT%H%M%SZ", time());
						}

						// in case the first repeat is changed
						if (array_key_exists($a->_eventid, $exceptiondata) && array_key_exists($a->rp_id(), $exceptiondata[$a->_eventid]))
						{
							$start = JevDate::strftime($startformat, JevDate::strtotime($exceptiondata[$a->_eventid][$a->rp_id()]->oldstartrepeat));
						}
					}

					$html .= "DTSTAMP:" . $stamptime . "\r\n";
					$html .= "DTSTART$tzid$alldayprefix:" . $start . "\r\n";
					// events with no end time don't give a DTEND
					if (!$a->noendtime())
					{
						$html .= "DTEND$tzid$alldayprefix:" . $end . "\r\n";
					}
					$html .= "SEQUENCE:" . $a->_sequence . "\r\n";
					if ($a->hasrepetition())
					{
						$html .= 'RRULE:';

						// TODO MAKE SURE COMPAIBLE COMBINATIONS
						$html .= 'FREQ=' . $a->_freq;
						if ($a->_until != "" && $a->_until != 0)
						{
							// Do not use JevDate version since this sets timezone to config value!
							// GOOGLE HAS A PROBLEM WITH 235959!!!
							//$html .= ';UNTIL=' . strftime("%Y%m%dT235959Z", $a->_until);
							$html .= ';UNTIL=' . strftime("%Y%m%dT000000Z", $a->_until + 86400);
						}
						else if ($a->_count != "")
						{
							$html .= ';COUNT=' . $a->_count;
						}
						if ($a->_rinterval != "")
							$html .= ';INTERVAL=' . $a->_rinterval;
						if ($a->_freq == "DAILY")
						{

						}
						else if ($a->_freq == "WEEKLY")
						{
							if ($a->_byday != "")
								$html .= ';BYDAY=' . $a->_byday;
						}
						else if ($a->_freq == "MONTHLY")
						{
							if ($a->_bymonthday != "")
							{
								$html .= ';BYMONTHDAY=' . $a->_bymonthday;
								if ($a->_byweekno != "")
									$html .= ';BYWEEKNO=' . $a->_byweekno;
							}
							else if ($a->_byday != "")
							{
								$html .= ';BYDAY=' . $a->_byday;
								if ($a->_byweekno != "")
									$html .= ';BYWEEKNO=' . $a->_byweekno;
							}
						}
						else if ($a->_freq == "YEARLY")
						{
							if ($a->_byyearday != "")
								$html .= ';BYYEARDAY=' . $a->_byyearday;
						}
						$html .= "\r\n";
					}

					// Now handle Exceptions
					$exceptions = array();
					if (array_key_exists($a->ev_id(), $exceptiondata))
					{
						$exceptions = $exceptiondata[$a->ev_id()];
					}

					$deletes = array();
					$changed = array();
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
									$deletes[] = strftime("%Y%m%dT%H%M%SZ", $exceptiondate);

									// Change back
									date_default_timezone_set($current_timezone);
								}
								else
								{
									$deletes[] = JevDate::strftime("%Y%m%dT%H%M%S", $exceptiondate);
								}
							}
							else
							{
								$changed[] = $exception->rp_id;
								$changedexceptions[$exception->rp_id] = $exception;
							}
						}
						if (count($deletes) > 0)
						{
							$html .= "EXDATE$tzid:" . self::wraplines(implode(",", $deletes)) . "\r\n";
						}
					}

					$html .= "TRANSP:OPAQUE\r\n";
					$html .= "END:VEVENT\r\n";

					$changedrows = array();

					if (count($changed) > 0 && $changed[0] != 0)
					{
						foreach ($changed as $rpid)
						{
                                                    if (JPATH_SITE."/plugins/jevents/jevnotify/") {
                                                            $a = JEVNotifyHelper::getEventData($rpid, "icaldb", 0, 0, 0);
                                                    } else {
                                                            // No usage yet. 
                                                            // Likely to update helper function when moving over RSVP Pro Generated iCals.
                                                            $a = $dataModal->getEventData($rpid, "icaldb", 0, 0, 0);
                                                    }

							if ($a && isset($a["row"]))
							{
								$a = $a["row"];
								$changedrows[] = $a;
							}
						}

						ob_start();
						$dispatcher->trigger('onDisplayCustomFieldsMultiRow', array(&$changedrows));
						ob_end_clean();

						foreach ($changedrows as $a)
						{
							$html .= "BEGIN:VEVENT\r\n";
							$html .= "UID:" . $a->uid() . "\r\n";
							$html .= "CATEGORIES:" . $a->catname() . "\r\n";
							if (!empty($a->_class))
								$html .= "CLASS:" . $a->_class . "\r\n";
							$html .= "SUMMARY:" . $a->title() . "\r\n";
							if ($a->location() != "")
								$html .= "LOCATION:" . self::wraplines(self::replacetags($a->location())) . "\r\n";
							// We Need to wrap this according to the specs
							$html .= self::setDescription(strip_tags($a->content())) . "\r\n";

							if ($a->hasContactInfo())
								$html .= "CONTACT:" . self::replacetags($a->contact_info()) . "\r\n";
                                                                
							if ($a->hasExtraInfo())
								$html .= "X-EXTRAINFO:" . self::wraplines(self::replacetags($a->_extra_info)); $html .= "\r\n";
                                                        $user = JFactory::getUser($a->created_by());
                                                        
                                                        $html .= "ORGANIZER;CN=" . $user->name . ":MAILTO:" . $user->email . "\r\n";
							$exception = $changedexceptions[$rpid];
							$originalstart = JevDate::strtotime($exception->oldstartrepeat);
							$chstart = $a->getUnixStartTime();
							$chend = $a->getUnixEndTime();

							// No doing true timezones!
							if ($tzid == "" && is_callable("date_default_timezone_set"))
							{
								// UTC!
								// Change timezone to UTC
								$current_timezone = date_default_timezone_get();
								date_default_timezone_set("UTC");

								// Do not use JevDate version since this sets timezone to config value!
								$chstart = strftime("%Y%m%dT%H%M%SZ", $chstart);
								$chend = strftime("%Y%m%dT%H%M%SZ", $chend);
								$stamptime = strftime("%Y%m%dT%H%M%SZ", time());
								$originalstart = strftime("%Y%m%dT%H%M%SZ", $originalstart);
								// Change back
								date_default_timezone_set($current_timezone);
							}
							else
							{
								$chstart = JevDate::strftime("%Y%m%dT%H%M%S", $chstart);
								$chend = JevDate::strftime("%Y%m%dT%H%M%S", $chend);
								$stamptime = JevDate::strftime("%Y%m%dT%H%M%S", time());
								$originalstart = JevDate::strftime("%Y%m%dT%H%M%S", $originalstart);
							}
							$html .= "DTSTAMP$tzid:" . $stamptime . "\r\n";
							$html .= "DTSTART$tzid:" . $chstart . "\r\n";
							$html .= "DTEND$tzid:" . $chend . "\r\n";
							$html .= "RECURRENCE-ID$tzid:" . $originalstart . "\r\n";
							$html .= "SEQUENCE:" . $a->_sequence . "\r\n";
							$html .= "TRANSP:OPAQUE\r\n";
							$html .= "END:VEVENT\r\n";
						}
					}
				}
			}


			$html .= "END:VCALENDAR\r\n";
                        return $html;
        }
        protected function vtimezone($icalEvents)
	{
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$tzid = "";
		if (is_callable("date_default_timezone_set"))
		{
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$tz = $params->get("icaltimezonelive", "");
			if ($tz == "")
			{
				return "";
			}

			$current_timezone = $tz;

			// Do the Timezone definition
			// replace any spaces with _ underscores
			$current_timezone = str_replace(" ", "_", $current_timezone);
			$tzid = ";TZID=$current_timezone";
			// find the earliest start date
			$firststart = false;
			foreach ($icalEvents as $a)
			{
				if (!$firststart || $a->getUnixStartTime() < $firststart)
					$firststart = $a->getUnixStartTime();
			}
			// Subtract 1 leap year to make sure we have enough transitions
			$firststart -= 31622400;
			$timezone = new DateTimeZone($current_timezone);

			if (version_compare(PHP_VERSION, "5.3.0") >= 0)
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
				$lastyear = $params->get("com_latestyear", 2020);
				echo "BEGIN:VTIMEZONE\r\n";
				echo "TZID:$current_timezone\r\n";
				for ($t = 0; $t < count($transitions); $t++)
				{
					$transition = $transitions[$t];
					if ($transition['isdst'] == 0)
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
						$prev += ( $t == 0) ? 1 : -1;

						$offset = $transitions[$prev]["offset"];
						$sign = $offset >= 0 ? "+" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETFROM:$offset\r\n";

						$offset = $transitions[$t]["offset"];
						$sign = $offset >= 0 ? "+" : "-";
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
					if ($transition['isdst'] == 1)
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
						$prev += ( $t == 0) ? 1 : -1;

						$offset = $transitions[$prev]["offset"];
						$sign = $offset >= 0 ? "+" : "-";
						$offset = abs($offset);
						$offset = $sign . sprintf("%04s", (floor($offset / 3600) * 100 + $offset % 60));
						echo "TZOFFSETFROM:$offset\r\n";

						$offset = $transitions[$t]["offset"];
						$sign = $offset >= 0 ? "+" : "-";
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
        // Special methods ONLY user for iCal invitations
	protected function setDescription($desc)
	{
		// TODO - run this through plugins first ?

		$icalformatted = JRequest::getInt("icf", 0);
		if (!$icalformatted)
			$description = self::replacetags($desc);
		else
			$description = $desc;

		// wraplines	from vCard class
		$cfg =  JEVConfig::getInstance();
		if ($cfg->get("outlook2003icalexport", 0))
		{
			return "DESCRIPTION:" . self::wraplines($description, 76, false);
		}
		else
		{
			return "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:" . self::wraplines($description);

	}
	}
        	protected function wraplines($input, $line_max = 76, $quotedprintable = false)
	{
		$hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
		$eol 		= "\r\n";

		$input = str_replace($eol, "", $input);

		// new version

		$output = '';
		while (JString::strlen($input)>=$line_max){
			$output .= JString::substr($input,0,$line_max-1);
			$input = JString::substr($input,$line_max-1);
			if (strlen($input)>0){
		  		 $output .= $eol." ";
			}
		}
		if (strlen($input)>0){
			$output .= $input;
		}
		return $output;

		$escape = '=';
		$output = '';
		$outline = "";
		$newline = ' ';

		$linlen = JString::strlen($input);


		for ($i = 0; $i < $linlen; $i++)
		{
			$c = JString::substr($input, $i, 1);

			/*
			$dec = ord($c);
			  if (!$quotedprintable) {
			  if (($dec == 32) && ($i == ($linlen - 1))) { // convert space at eol only
			  $c = '=20';
			  } elseif (($dec == 61) || ($dec < 32 ) || ($dec > 126)) { // always encode "\t", which is *not* required
			  $h2 = floor($dec / 16);
			  $h1 = floor($dec % 16);
			  $c = $escape . $hex["$h2"] . $hex["$h1"];
			  }
			  }
			 */
			if ((strlen($outline) + 1) >= $line_max)
			{ // CRLF is not counted
				$output .= $outline . $eol . $newline; // soft line break; "\r\n" is okay
				$outline = $c;
				//$newline .= " ";
			}
			else
			{
				$outline .= $c;
			}
		} // end of for
		$output .= $outline;

		return trim($output);

	}
        protected function replacetags($description)
	{
		$description = str_replace('<p>', '\n\n', $description);
		$description = str_replace('<P>', '\n\n', $description);
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
		$description = strip_tags($description);
		//$description 	= strtr( $description,	array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
		//$description 	= preg_replace( "/&#([0-9]+);/me","chr('\\1')", $description );
		return $description;

	}


}

/* Keep this - just in case */
/*
 				var jevConditional_$conditional = {
					setupJevConditions: function() {
						var condition = $("$condparam$conditions");
						if (condition.className.indexOf("radio") >= 0) {
							for (var i = 0; i < condition.childNodes.length; i++) {
								if (condition.childNodes[i].type == "radio") {
									condition.childNodes[i].addEvent("click", function() {
										jevConditional_$conditional.jevCondition()
									});
								}
							}
						}
						else if (condition.tagName == "SELECT") {
							if ($(condition.id + "_chzn")) {
								var condition_chzn = $(condition.id + "_chzn");
								for (var i = 0; i < condition_chzn.childNodes.length; i++) {
									condition_chzn.childNodes[i].addEvent("click", function() {
										jevConditional_$conditional.jevCondition()
									});
								}
							}
							else
								condition.addEvent("change", function() {
									jevConditional_$conditional.jevCondition()
								});
						}
						else {
							condition.addEvent("change", function() {
								jevConditional_$conditional.jevCondition()
							});
						}
						jevConditional_$conditional.jevCondition();
					},
					jevCondition: function() {
						var condition = $("$condparam$conditions");
						var eventsno = $("$fieldparam$conditional");
						var hiddencontrol = eventsno.parentNode.parentNode;
						var conditionsarray = new Array($condarray);
						if (condition.type == "checkbox")
							condition.value = condition.checked;
						if (condition.childNodes[0] && condition.childNodes[0].type == "radio") {
							for (var i = 0; i < condition.childNodes.length; i++) {
								if (condition.childNodes[i].checked)
									condition.value = condition.childNodes[i].value;
							}
						}
						if (condition.multiple && condition.tagName == "SELECT") {
							for (var i = 0; i < condition.options.length; i++) {
								if (condition.options[i].selected && conditionsarray.indexOf(condition.options[i].value) >= 0)
									conditionsarray[0] = condition.value;
							}
						}
						if (condition.childNodes[0] && condition.childNodes[0].childNodes[0] && condition.childNodes[0].childNodes[0].childNodes[0] && condition.childNodes[0].childNodes[0].childNodes[0].type == "checkbox") {
							condition.value = new Array();
							for (var i = 0; i < condition.childNodes[0].childNodes.length; i++) {
								if (condition.childNodes[0].childNodes[i].childNodes[0].checked && conditionsarray.indexOf(condition.childNodes[0].childNodes[i].childNodes[0].value) >= 0) {
									condition.value = conditionsarray[0];
								}
							}
						}
						// If condition is valid then show the row
						if (conditionsarray.indexOf(condition.value) >= 0) {
							if (hiddencontrol.tagName == "TR") {
								hiddencontrol.style.display = "table-row";
							}
							else if (hiddencontrol.tagName == "SPAN"){
								hiddencontrol.style.display = "inline";
							}
							else
								hiddencontrol.style.display = "block";
						}
						// else hide the row and revert the value to its default
						else {
							// Is the dependent field is a select list, or radio list
							if (eventsno && eventsno.options){
								var defaultarray = new Array($fielddefaultarray);
								for (var i = 0; i < eventsno.options.length; i++) {
									if (defaultarray.indexOf(eventsno.options[i].value) >= 0) {
										eventsno.options[i].selected = true;
									}
									else
										eventsno.options[i].selected = false;
								}
							}
							// this should be not dependent on language!
							else if ("$condlabel" == "Specified Person?" || "$condlabel" == "Specified Location?")
								$conditional.Delete();
							else {
								eventsno.value = "$fielddefault";
							}
							hiddencontrol.style.display = "none";
						}
						try {
							jQuery(eventsno).trigger("liszt:updated");
						}
						catch (e) {
						}
					}
				}
				window.addEvent('load', function() {
					jevConditional_$conditional.setupJevConditions();
				});

 */
