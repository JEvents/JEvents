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
	function loadLanguage($type='default', $lang='')
	{

		// to be enhanced in future : load by $type (com, modcal, modlatest) [tstahl]

		$option = JRequest::getCmd("option");
		$cfg = & JEVConfig::getInstance();
		$lang = & JFactory::getLanguage();

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
				$lang->load(JEV_COM_COMPONENT, JPATH_THEMES . DS . JFactory::getApplication()->getTemplate());

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

	/**
	 * load iCal instance for filename
	 *
	 * @static
	 * @access public
	 * @since 1.5
	 */
	function & iCalInstance($filename, $rawtext="")
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
			$instances[$index] = & $import->import($filename, $rawtext);

			return $instances[$index];
		}

	}

	/**
	 * Returns the full month name
	 * 
	 * @static
	 * @access public
	 * @param	string	$month		numeric month
	 * @return	string				localised long month name
	 */
	function getMonthName($month=12)
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
	function getShortMonthName($month=12)
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
	function getDayName($daynb=0, $array=0)
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
	function getShortDayName($daynb=0, $array=0)
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

	function getTime($date, $h=-1, $m=-1)
	{
		$cfg = & JEVConfig::getInstance();

		static $format_type;
		if (!isset($format_type))
		{
			$cfg = & JEVConfig::getInstance();
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
		else if (JUtility::isWinOS())
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
	function getWeekdayLetter($daynb=0, $array=0)
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
	function checkRobotsMetaTag($name="robots", $content="noindex, nofollow")
	{

		// force robots metatag
		$cfg = & JEVConfig::getInstance();
		if ($cfg->get('com_blockRobots', 0) >= 1)
		{
			$document = & JFactory::getDocument();
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
			if ($cdate < $prior)
			{
				$document->setMetaData($name, $content);
				return;
			}
			$post = JevDate::strtotime($cfg->get('robotpost', "-1 day"));
			if ($cdate > $post)
			{
				$document->setMetaData($name, $content);
				return;
			}
		}

	}

	function forceIntegerArray(&$cid, $asString=true)
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
	static function loadCalendar($fieldname, $fieldid, $value, $minyear, $maxyear, $onhidestart="", $onchange="", $format='Y-m-d')
	{
		$document = & JFactory::getDocument();
		$component = "com_jevents";
		$params = & JComponentHelper::getParams($component);
		$forcepopupcalendar = $params->get("forcepopupcalendar", 1);
		$offset = $params->get("com_starday", 1);

		$calendar = (JVersion::isCompatible("1.6.0")) ? 'calendar12.js' : 'calendar11.js'; // RSH 9/28/10 - need to make the calendar a variable to be compatible with both mootools1.1 and 1.2
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
		$document->addScriptDeclaration($script);
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
	function loadOverlib()
	{

		$cfg = & JEVConfig::getInstance();

		// check if this function is already loaded
		if (!JFactory::getApplication()->get('loadOverlib'))
		{
			if ($cfg->get("com_enableToolTip", 1) || JFactory::getApplication()->isAdmin())
			{
				$document = & JFactory::getDocument();
				// RSH 10/11/10 - Check location of overlib files - j!1.6 doesn't include them!
				if (JVersion::isCompatible("1.6.0"))
				{
					$document->addScript(JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/js/overlib_mini.js');
					$document->addScript(JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/js/overlib_hideform_mini.js');
				}
				else
				{
					$document->addScript(JURI::root() . 'includes/js/overlib_mini.js');
					$document->addScript(JURI::root() . 'includes/js/overlib_hideform_mini.js');
				}
				// change state so it isnt loaded a second time
				JFactory::getApplication()->set('loadOverlib', true);

				if ($cfg->get("com_calTTShadow", 1) && !JFactory::getApplication()->isAdmin())
				{
					$document->addScript(JURI::root() . 'components/' . JEV_COM_COMPONENT . '/assets/js/overlib_shadow.js');
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
	function getItemid($forcecheck = false, $skipbackend= true)
	{
		if (JFactory::getApplication()->isAdmin() && $skipbackend)
			return 0;
		static $jevitemid;
		if (!isset($jevitemid))
		{
			$jevitemid = 0;
			$menu = & JSite::getMenu();
			$active = $menu->getActive();
			$Itemid = JRequest::getInt("Itemid");
			if (is_null($active))
			{
				// wierd bug in Joomla when SEF is disabled but with xhtml urls sometimes &amp;Itemid is misinterpretted !!!
				$Itemid = JRequest::getInt("Itemid");
				if ($Itemid > 0 && $jevitemid != $Itemid)
				{
					$active = $menu->getItem($Itemid);
				}
			}
			$option = JRequest::getCmd("option");
			// wierd bug in Joomla when SEF is disabled but with xhtml urls sometimes &amp;Itemid is misinterpretted !!!
			if ($Itemid == 0)
				$Itemid = JRequest::getInt("amp;Itemid", 0);
			if ($option == JEV_COM_COMPONENT && $Itemid > 0)
			{
				$jevitemid = $Itemid;
				return $jevitemid;
			}
			else if (!is_null($active) && $active->component == JEV_COM_COMPONENT)
			{
				$jevitemid = $active->id;
				return $jevitemid;
			}
			else
			{
				$jevitems = $menu->getItems("component", JEV_COM_COMPONENT);
				// TODO second level Check on enclosing categories and other constraints
				if (count($jevitems) > 0)
				{
					$user =  JFactory::getUser();
					foreach ($jevitems as $jevitem)
					{
						if (version_compare(JVERSION, '1.6.0', '>=') ? in_array($jevitem->access, JEVHelper::getAid($user, 'array')) : JEVHelper::getAid($user) >= $jevitem->access)
						{
							$jevitemid = $jevitem->id;

							if ($forcecheck)
							{
								$mparams = new JParameter($jevitem->params);
								$mcatids = array();
								// New system
								$newcats = $mparams->get( "catidnew", false);
								if ($newcats && is_array($newcats )){
									foreach ($newcats as $newcat){
										if ($forcecheck->catid() == $newcat)
										{
											return $jevitemid;
										}

										if ( !in_array( $newcat, $mcatids )){
											$mcatids[]	= $newcat;
										}
									}				
								}
								else {
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
											return $jevitemid;
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
									return $jevitemid;
								}
								continue;
							}

							return $jevitemid;
						}
					}
				}
			}
		}
		return $jevitemid;

	}

	function getAdminItemid()
	{
		static $jevitemid;
		if (!isset($jevitemid))
		{
			$jevitemid = 0;
			$menu = & JSite::getMenu();
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
					$user =  JFactory::getUser();
					foreach ($jevitems as $jevitem)
					{
						if (version_compare(JVERSION, '1.6.0', '>=') ? in_array($jevitem->access, JEVHelper::getAid($user, 'array')) : JEVHelper::getAid($user) >= $jevitem->access)
						{
							if (strpos($active->link, "admin.listevents") > 0)
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
	 * Get array Year, Month, Day from current Request, fallback to current date
	 *
	 * @return array
	 */
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
			if ($day <= 0){
				$day = $daynow;
			}
			if ($month <= 0){
				$month = $monthnow;
			}
			if ($year <= 0){
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
			$datenow = & JevDate::getDate("+0 seconds");
		}
		return $datenow;

	}

	/**
	 * Test to see if user can add events from the front end
	 *
	 * @return boolean
	 */
	function isEventCreator()
	{
		static $isEventCreator;
		if (!isset($isEventCreator))
		{
			$isEventCreator = false;
			/*
			  // experiment in alternative approval mechanism
			  // just incase we don't have jevents plugins registered yet
			  JPluginHelper::importPlugin("jevents");
			  $dispatcher	=& JDispatcher::getInstance();
			  $set = $dispatcher->trigger('isEventCreator', array (& $isEventCreator));
			  if (count($set)>0) return $isEventCreator;
			 */
			$user = & JEVHelper::getAuthorisedUser();
			if (is_null($user))
			{
				$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					if (JVersion::isCompatible("1.6.0"))
					{
						$juser = JFactory::getUser();
						$isEventCreator = $juser->authorise('core.create', 'com_jevents');
						// this is too heavy on database queries
						/*
						if (!$isEventCreator){
							$cats =  JEVHelper::getAuthorisedCategories($juser, 'com_jevents', 'core.create');
							if (count($cats) > 0)
							{
								$isEventCreator = true;
							}
						}
						 */
					}
					else
					{
						$creatorlevel = $params->get("jevcreator_level", 20);
						$juser =  JFactory::getUser();
						if (JEVHelper::getGid($user) >= $creatorlevel)
						{
							$isEventCreator = true;
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
			}

			JPluginHelper::importPlugin("jevents");
			$dispatcher = & JDispatcher::getInstance();
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
	function canCreateEvent($row, $user=null)
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
			if (JVersion::isCompatible("1.6.0"))
			{
				if ($user->authorise('core.create', 'com_jevents'))
					return true;
				$allowedcats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.create');
				if (!in_array($row->_catid, $allowedcats))
				{
					return false;
				}
				// check multi cats too
				if (JEVHelper::rowCatids($row)){
					if (count( array_diff(JEVHelper::rowCatids($row), $allowedcats))){
						return false;
					}
				}

			}
		}
		else {
			// are we authorised to do anything with this category or calendar
			$jevuser = & JEVHelper::getAuthorisedUser();
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
				if (JEVHelper::rowCatids($row)){
					if (count( array_diff(JEVHelper::rowCatids($row), $allowedcats))){
						return false;
					}
				}
			}			
		}
		return true;

	}

	// is the user an event editor - i.e. can edit own and other events
	function isEventEditor()
	{
		static $isEventEditor;
		if (!isset($isEventEditor))
		{
			$isEventEditor = false;

			$user = & JEVHelper::getAuthorisedUser();
			if (is_null($user))
			{
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					if (JVersion::isCompatible("1.6.0"))
					{
						$juser =  JFactory::getUser();
                                                // Never allow unlogged in users to edit events - just in case someone tries to allow this
                                                if ($juser->id==0) {
                                                    return false;
                                                }
						//$isEventEditor = JAccess::check($juser->id, "core.edit","com_jevents");
						$isEventEditor = $juser->authorise('core.edit', 'com_jevents');
					}
					else
					{
						$publishlevel = $params->get("jeveditor_level", 20);
						$juser =  JFactory::getUser();
						if (JEVHelper::getGid($juser) >= $publishlevel)
						{
							$isEventEditor = true;
						}
					}
				}
			}
			/*
			  $user =& JEVHelper::getAuthorisedUser();
			  if (is_null($user)){
			  $params =& JComponentHelper::getParams(JEV_COM_COMPONENT);
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
	function canEditEvent($row, $user=null)
	{
		// store in static to save repeated database calls
		static $authdata_coreedit = array();
		static $authdata_editown = array();

		// TODO make this call a plugin
		if ($user == null)
		{
			$user =  JFactory::getUser();
		}

		if ($user->id==0) {
			return false;
		}
		
		// are we authorised to do anything with this category or calendar
		$jevuser = & JEVHelper::getAuthorisedUser();
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
			if (JEVHelper::rowCatids($row)){
				if (count( array_diff(JEVHelper::rowCatids($row), $allowedcats))){
					return false;
				}
			}
		}
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		if ($authorisedonly){
			if ($jevuser && $jevuser->published){
				// creator can edit their own event
				if ($jevuser->cancreate && $row->created_by() == $user->id ){
					return true;
				}
				else if ($jevuser->canedit){
					return true;
				}
			}
			return false;
		}


		if (JEVHelper::isEventEditor())
		{
			// any category restrictions on this?
			if (JVersion::isCompatible("1.6.0"))
			{
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
				$key = $row->catids()?json_encode($row->catids()):json_encode(intval($row->catid()));
				if (!isset($authdata_coreedit[$key])){
					$authdata_coreedit[$key] = JEVHelper::authoriseCategories('core.edit', $key, $user);
				}
				if ($authdata_coreedit[$key]) {
					return true;
				}
				else if ($user->id > 0 && $row->created_by() == $user->id) {
					if (!isset($authdata_editown[$key])){
						$authdata_editown[$key] =JEVHelper::authoriseCategories('core.edit.own', $key, $user);
					}
					return $authdata_editown[$key];
				}
				// category settings trumps overall setting
				return false;
			}						
			return true;
		}
		// must stop anon users from editing any events
		else if ($user->id > 0 && $row->created_by() == $user->id)
		{
			
			if ($authorisedonly){
				if ($jevuser){
					if ($jevuser->published && $jevuser->cancreate){
						return true;
					}
				}
				else {
					return false;
				}
			}

			
			// other users can always edit their own unless blocked by category
			if (JVersion::isCompatible("1.6.0"))
			{
				 // This involes TOO many database queries in Joomla - one per category which can be a LOT
				/*
				$cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit');
				$cats_own = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit.own');
				if (in_array($row->_catid, $cats))
					return true;
				else if (in_array($row->_catid, $cats_own))
					return true;
				 */
				$key = $row->catids()?json_encode($row->catids()):json_encode(intval($row->catid()));
				if (!isset($authdata_coreedit[$key])){
					$authdata_coreedit[$key] =JEVHelper::authoriseCategories('core.edit', $key, $user);
				}
				if ($authdata_coreedit[$key]) {
					return true;
				}
				else {
					if (!isset($authdata_editown[$key])){
						$authdata_editown[$key] =JEVHelper::authoriseCategories('core.edit.own', $key, $user);
					}
					return $authdata_editown[$key];
				}
				return false;
				
			}
			else
			{
				return true;
			}
		}
		if (JVersion::isCompatible("1.6.0"))
		{
			if ($user->id > 0 && $row->catid()>0){
				$key = $row->catids()?json_encode($row->catids()):json_encode(intval($row->catid()));
				if (!isset($authdata_coreedit[$key])){
					$authdata_coreedit[$key] =JEVHelper::authoriseCategories('core.edit', $key, $user);
				}
				return $authdata_coreedit[$key];
			}
		}
			
	 
		return false;

	}

	// is the user an event publisher - i.e. can publish own OR other events
	function isEventPublisher($strict=false)
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

			$user = & JEVHelper::getAuthorisedUser();
			if (is_null($user))
			{
				$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{
					if (JVersion::isCompatible("1.6.0"))
					{
						$juser =  JFactory::getUser();
						//$isEventPublisher[$type]  = JAccess::check($juser->id, "core.edit.state","com_jevents");
						$isEventPublisher[$type] = $juser->authorise('core.edit.state', 'com_jevents');
					}
					else
					{
						$publishlevel = $params->get("jevpublish_level", 20);
						$juser =  JFactory::getUser();
						if (JEVHelper::getGid($user) >= $publishlevel)
						{
							$isEventPublisher[$type] = true;
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

			$dispatcher = & JDispatcher::getInstance();
			$dispatcher->trigger('isEventPublisher', array($type, & $isEventPublisher[$type]));
		}


		return $isEventPublisher[$type];

	}

	// Fall back test to see if user can publish their own events based on config setting
	function canPublishOwnEvents($evid)
	{
		$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 1);
		$publishown = $params->get("jevpublishown", 0);

		$jevuser = & JEVHelper::getAuthorisedUser();
		$user =  JFactory::getUser();

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
		 
		if ($authorisedonly && $jevuser && $jevuser->canpublishown){
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
	function categoryAdmin()
	{
		if (!JEVHelper::isEventPublisher())
			return false;
		$juser =  JFactory::getUser();

		$db = & JFactory::getDBO();
		$sql = "SELECT id FROM #__jevents_categories WHERE admin=" . $juser->id;
		$db->setQuery($sql);
		$catids = $db->loadResultArray();
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
	function canPublishEvent($row, $user=null)
	{
		// store in static to save repeated database calls
		static $authdata_editstate = array();
		
		// TODO make this call a plugin
		if ($user == null)
		{
			$user =  JFactory::getUser();
		}
		// are we authorised to do anything with this category or calendar
		$jevuser = & JEVHelper::getAuthorisedUser();
		$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 0);
		if ($authorisedonly)
		{
			if (!$jevuser ) {
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
				if (JEVHelper::rowCatids($row)){
					if (count( array_diff(JEVHelper::rowCatids($row), $allowedcats))){
						return false;
					}
				}
			}
			if ($jevuser->canpublishall )
			{
				return true;
			}
			if ($row->created_by() == $user->id && $jevuser->canpublishown){
				return true;
 			}
			return false;

		}

		// can publish all?
		if (JEVHelper::isEventPublisher(true))
		{
			if (JVersion::isCompatible("1.6.0"))
			{
				// This involes TOO many database queries in Joomla - one per category which can be a LOT
				/*
				$cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit.state');
				if (in_array($row->_catid, $cats))
					return true;
				*/
				// allow multi-categories
				$key = $row->catids()?json_encode($row->catids()):json_encode(intval($row->catid()));
				$authdata_editstate[$key] = JEVHelper::authoriseCategories('core.edit.state', $key, $user);
				return $authdata_editstate[$key];
			}
			return true;
			
		}
		else if ($row->created_by() == $user->id)
		{

			// Use generic helper method that can call the plugin to see if user can publish any events
			$isEventPublisher = JEVHelper::isEventPublisher();
			if ($isEventPublisher)
				return true;

			$jevuser = & JEVHelper::getAuthorisedUser();
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
			if (JVersion::isCompatible("1.6.0"))
			{
				// This involes TOO many database queries in Joomla - one per category which can be a LOT
				/*
				$cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.edit.state');
				if (in_array($row->_catid, $cats))
					return true;
				*/
				$key = $row->catids()?json_encode($row->catids()):json_encode(intval($row->catid()));
				if (!isset($authdata_editstate[$key])){
					$authdata_editstate[$key] = JEVHelper::authoriseCategories('core.edit.state', $key, $user);
				}
				return $authdata_editstate[$key];
			}
		}
		if (JVersion::isCompatible("1.6.0"))
		{
			if ($user->id > 0 && $row->catid()>0){
				$key = $row->catids()?json_encode($row->catids()):json_encode(intval($row->catid()));
				if (!isset($authdata_editstate[$key])){
					$authdata_editstate[$key] = JEVHelper::authoriseCategories('core.edit.state', $key, $user);
				}
				return $authdata_editstate[$key];
			}
		}
		
		return false;

	}

	// is the user an event publisher - i.e. can publish own OR other events
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

			$user = & JEVHelper::getAuthorisedUser();
			if (is_null($user))
			{
				$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
				$authorisedonly = $params->get("authorisedonly", 0);
				if (!$authorisedonly)
				{

					if (JVersion::isCompatible("1.6.0"))
					{
						$juser =  JFactory::getUser();
						$isEventDeletor[$type] = $juser->authorise('core.deleteall', 'com_jevents');
					}
					else
					{
						$publishlevel = $params->get("jevpublish_level", 20);
						$juser =  JFactory::getUser();
						if (JEVHelper::getGid($user) >= $publishlevel)
						{
							$isEventDeletor[$type] = true;
						}
					}
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
	function canDeleteEvent($row, $user=null)
	{
		// store in static to save repeated database calls
		static $authdata_coredeleteall = array();
		
		// TODO make this call a plugin
		if ($user == null)
		{
			$user =  JFactory::getUser();
		}

		// are we authorised to do anything with this category or calendar
		$jevuser = & JEVHelper::getAuthorisedUser();
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
			if (JEVHelper::rowCatids($row)){
				if (count( array_diff(JEVHelper::rowCatids($row), $allowedcats))){
					return false;
				}
			}
		}
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$authorisedonly = $params->get("authorisedonly", 1);
		if ($authorisedonly) {
			if (!$jevuser ) {
				return false;
			}

			if (!is_null($jevuser) && $jevuser->candeleteall) 
			{
				return true;
			}			
			else if (!is_null($jevuser) && $jevuser->candeleteown && $row->created_by() == $user->id){ 
				return true;
			}
			return false;
		}
		
		if (JVersion::isCompatible("1.6.0"))
		{
			 // This involes TOO many database queries in Joomla - one per category which can be a LOT
			/*
			$cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.deleteall');
			if (in_array($row->_catid, $cats))
				return true;
			*/
			$key = $row->catids()?json_encode($row->catids()):json_encode(intval($row->catid()));
			if (!isset($authdata_coredeleteall[$key])){
				$authdata_coredeleteall[$key] =JEVHelper::authoriseCategories('core.deleteall', $key, $user);
			}
			if ($authdata_coredeleteall[$key]) {
				return $authdata_coredeleteall[$key];
			}
		}

		// can delete all?
		if (JEVHelper::isEventDeletor(true))
		{
			// any category restrictions on this?
			if (JVersion::isCompatible("1.6.0"))
			{
				// This involes TOO many database queries in Joomla - one per category which can be a LOT
				/*
				$cats = JEVHelper::getAuthorisedCategories($user,'com_jevents', 'core.deleteall');
				if (in_array($row->_catid, $cats))
					return true;
				*/
				$key = $row->catids()?json_encode($row->catids()):json_encode(intval($row->catid()));
				if (!isset($authdata_coredeleteall[$key])){
					$authdata_coredeleteall[$key] =JEVHelper::authoriseCategories('core.deleteall', $key, $user);
				}
				if ($authdata_coredeleteall[$key]) {
					return $authdata_coredeleteall[$key];
				}
			}	
			else {
				// in Joomla 1.5 this is enough
				return true;
			}			
		}
		
		// There seems to be a problem with category permissions - sometimes Joomla ACL set to yes in category but result is false!
		
		// fall back to being able to delete own events if a publisher
		if ($row->created_by() == $user->id)
		{
			$jevuser = & JEVHelper::getAuthorisedUser();
			if (!is_null($jevuser))
			{
				return $jevuser->candeleteown;
			}
			// if a user can publish their own then cal delete their own too
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$authorisedonly = $params->get("authorisedonly", 1);
			$publishown = $params->get("jevpublishown", 0);
			if (!$authorisedonly && ($publishown ||  JEVHelper::canPublishEvent($row, $user)))
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
	function getContact($id, $attrib='Object')
	{

		$db = & JFactory::getDBO();

		static $rows = array();

		if ($id <= 0)
		{
			return null;
		}

		if (!isset($rows[$id]))
		{
			$user =  JFactory::getUser();
			$rows[$id] = null;
			$query = "SELECT ju.id, ju.name, ju.username, ju.usertype, ju.sendEmail, ju.email, cd.name as contactname, "
					. ' CASE WHEN CHAR_LENGTH(cd.alias) THEN CONCAT_WS(\':\', cd.id, cd.alias) ELSE cd.id END as slug, '
					. ' CASE WHEN CHAR_LENGTH(cat.alias) THEN CONCAT_WS(\':\', cat.id, cat.alias) ELSE cat.id END AS catslug '
					. " \n FROM #__users AS ju"
					. "\n LEFT JOIN #__contact_details AS cd ON cd.user_id = ju.id "
					. "\n LEFT JOIN #__categories AS cat ON cat.id = cd.catid "
					. "\n WHERE block ='0'"
					. "\n AND cd.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user))
					. "\n AND cat.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user))
					. "\n AND ju.id = " . $id;

			$db->setQuery($query);
			$rows[$id] = $db->loadObject();
			if (is_null($rows[$id]))
			{
				// if the user has been deleted then try to suppress the warning
				// this causes a problem in Joomla 2.5.1 on some servers
				if (version_compare(JVERSION, '2.5', '>=') ){
					$rows[$id] = JFactory::getUser($id);
				}
				else {
					$handlers = JError::getErrorHandling(2);
					JError::setErrorHandling(2, "ignore");
					$rows[$id] = JFactory::getUser($id);
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
	function getAuthorisedUser($id=null)
	{
		static $userarray;
		if (!isset($userarray))
		{
			$userarray = array();
		}
		if (is_null($id))
		{
			$juser =  JFactory::getUser();
			$id = $juser->id;
		}
		if (!array_key_exists($id, $userarray))
		{
			JLoader::import("jevuser", JPATH_ADMINISTRATOR . "/components/" . JEV_COM_COMPONENT . "/tables/");

			$user = new TableUser();

			$params = & JComponentHelper::getParams(JEV_COM_COMPONENT);
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
	function getAuthorisedCategories($user, $component, $action){
		static $results = array();
		$key = $user->id.":component:".$action;
		if (!isset ($results[$key])){
			$results[$key] = $user->getAuthorisedCategories($component, $action);
		}
		return $results[$key];
	}
	
	static public function isAdminUser($user = null)
	{
		if (is_null($user))
		{
			$user = JFactory::getUser();
		}
		if (JVersion::isCompatible("1.6.0"))
		{
			//$access = JAccess::check($user->id, "core.admin","com_jevents");
			$access = $user->authorise('core.admin', 'com_jevents');
			return $access;
		}
		else
		{
			if (strtolower(JEVHelper::getUserType($user)) != "super administrator" && strtolower(JEVHelper::getUserType($user)) != "administrator")
			{
				return false;
			}
			return true;
		}

	}

	function componentStylesheet($view, $filename='events_css.css')
	{


		if (!isset($view->jevlayout))
		{
			if (method_exists($view, "getViewName"))
				$view->jevlayout = $view->getViewName();
			else if (method_exists($view, "getTheme"))
				$view->jevlayout = $view->getTheme();
		}

		if (file_exists(JPATH_BASE . DS . 'templates' . DS . JFactory::getApplication()->getTemplate() . DS . 'html' . DS . JEV_COM_COMPONENT . DS . $view->jevlayout . DS . "assets" . DS . "css" . DS . $filename))
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
	static public function getGid($user = null)
	{
		if (is_null($user))
		{
			$user = JFactory::getUser();
		}
		if (JVersion::isCompatible("1.6.0"))
		{
			return max(JAccess::getGroupsByUser($user->id));  // RSH trying to get a gid for J!1.6
		}
		else
		{
			return $user->gid;
		}

	}

	static public function getAid($user = null, $type = 'string')
	{
		if (is_null($user))
		{
			$user = JFactory::getUser();
		}
		if (JVersion::isCompatible("1.6.0"))
		{
			$levels = $user->authorisedLevels();

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
		else
		{
			return intval($user->aid);
		}

	}

	static public function getUserType($user = null)
	{
		if (is_null($user))
		{
			$user = JFactory::getUser();
		}
		if (JVersion::isCompatible("1.6.0"))
		{
			$groups = $user->groups;  // RSH 10/17/10 - Get groups, sort them, get the last one, return the value
			asort($groups);
			$last_group = end($groups);
			return ($last_group == 'Super Users') ? "Super Administrator" : $last_group;
		}
		else
		{
			return $user->usertype;
		}

	}

	static public function stylesheet($file, $path)
	{
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0"))
			JHTML::stylesheet($path . $file);
		else
			JHTML::stylesheet($file, $path);

	}

	static public function script($file, $path)
	{

		// Include mootools framework
		JHtml::_('behavior.mootools', true);

		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0"))
		{
			JHTML::script($path . $file);
			/*
			  $document = JFactory::getDocument();
			  if (strpos($path, '/')!==0 && strpos($path, 'http')!==0){
			  $path = "/".$path;
			  }
			  $document->addScript($path.$file);
			 */
		}
		else
			JHTML::script($file, $path);

	}

	static public function setupJoomla160()
	{

	}

	static public function getBaseAccess()
	{
		if (JVersion::isCompatible("1.6.0"))
		{
			// Store the ical in the registry so we can retrieve the access level
			$registry = & JRegistry::getInstance("jevents");
			$icsfile = $registry->getValue("jevents.icsfile", false);
			if ($icsfile) {
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
		else
		{
			return 0;
		}

	}

	static public function imagesite($img, $text)
	{
		// WHY THE HELL DO THEY BREAK PUBLIC FUNCTIONS !!!
		if (JVersion::isCompatible("1.6.0"))
		{
			return JHTML::_('image', 'system/' . $img, $text, NULL, true);
		}
		else
		{
			return JHTML::_('image.site', $img, '/images/M_images/', NULL, NULL, $text);
		}

	}

	static public function authoriseCategories($action, $catids, $user){
		if (is_string($catids) && (strpos( $catids, "[")===0 || strpos( $catids,'"')===0)){
			$catids = json_decode($catids);
		}
		else if (is_string($catids) && strpos( $catids, ",")>0){
			$catids = str_replace('"','', $catids);
			$catids = explode(",",$catids);
		}
		if (!is_array($catids)){
			$catids = array(intval($catids));
		}
		JArrayHelper::toInteger($catids);
		$result = false;//count($catids)>0;
		foreach ($catids as $catid){
			// this is an invalid category so skip it!
			if ($catid==0) continue;
			$result = $user->authorise($action, 'com_jevents.category.'.$catid) ? true : false;
			if (!$result) return false;
		}
		return $result;
	}
	
	static public function rowCatids(&$row){
		if (isset($row->_catids)){
			if (isset($row->_catidsarray)){
				return $row->_catidsarray;
			}
			$catids = $row->_catids;
			if (is_string($catids) && strpos( $catids, ",")>0){
				$catids = str_replace('"','', $catids);
				$catids = explode(",",$catids);
			}
			if (!is_array($catids)){
				$catids = array($catids);
			}
			JArrayHelper::toInteger($catids);
			$row->_catidsarray= $catids;
			return $catids;
		}
		return false;
		
	}
}
