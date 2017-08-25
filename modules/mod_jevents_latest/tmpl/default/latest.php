<?php

/**
 * copyright (C) 2008-2017 GWE Systems Ltd - All rights reserved
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\String\StringHelper;

/**
 * HTML View class for the module  frontend
 *
 * @static
 */
class DefaultModLatestView
{

	var
			$_modid = null;
	var
			$modparams = null;
	// Note that we encapsulate all this in a class to create
	// an isolated name space from everythng else (I hope).
	var
			$aid = null;
	var
			$lang = null;
	var
			$catid = null;
	var
			$inccss = null;
	var
			$maxEvents = null;
	var
			$dispMode = null;
	var
			$rangeDays = null;
	var
			$norepeat = null;
	var
			$displayLinks = null;
	var
			$displayYear = null;
	var
			$disableDateStyle = null;
	var
			$disableTitleStyle = null;
	var
			$linkCloaking = null;
	var
			$customFormatStr = null;
	var
			$_defaultfFormatStr12 = '${eventDate}[!a: - ${endDate(%l:%M%p)}]<br />${title}';
	var
			$_defaultfFormatStr12winos = '${eventDate}[!a: - ${endDate(%I:%M%p)}]<br />${title}';
	var
			$_defaultfFormatStr24 = '${eventDate}[!a: - ${endDate(%H:%M)}]<br />${title}';
	var
			$defaultfFormatStr = null;
	var
			$linkToCal = null; // 0=no, 1=top, 2=bottom
	var
			$sortReverse = null;
	var
			$displayRSS = null;
	var
			$rsslink = null;
	var
			$com_starday = null;
	var
			$com_calUseStdTime = null;
	var
			$datamodel = null;
	var
			$catout = null;

	function __construct($params, $modid)
	{
		$this->_modid = $modid;
		$this->modparams = & $params;

		$jevents_config = JEVConfig::getInstance();

		$this->datamodel = new JEventsDataModel();
		// find appropriate Itemid and setup catids for datamodel
		$this->myItemid = $this->datamodel->setupModuleCatids($this->modparams);
		$this->catout = $this->datamodel->getCatidsOutLink(true);

		$user =  JFactory::getUser();

		// Can't use getCfg since this cannot be changed by Joomfish etc.
		$tmplang = JFactory::getLanguage();
		$this->langtag = $tmplang->getTag();

		// get params exclusive to module
		$this->inccss = $params->get('modlatest_inccss', 0);
		if ($this->inccss)
		{
			$modtheme = $params->get("com_calViewName", "");
			if ($modtheme == "" || $modtheme == "global")
			{
				$modtheme = JEV_CommonFunctions::getJEventsViewName();
				;
			}
			$this->jevlayout = $modtheme;

			JEVHelper::componentStylesheet($this, "modstyle.css");
		}

		// get params exclusive to component
		$this->com_starday = intval($jevents_config->get('com_starday', 0));
		$this->com_calUseStdTime = intval($jevents_config->get('com_calUseStdTime', 1));
		if ($this->com_calUseStdTime)
		{
			$this->defaultfFormatStr = IS_WIN ? $this->_defaultfFormatStr12winos : $this->_defaultfFormatStr12;
		}
		else
		{
			$this->defaultfFormatStr = $this->_defaultfFormatStr24;
		}

		// get params depending on switch
		if (intval($params->get('modlatest_useLocalParam', 0)) == 1)
		{
			$myparam = &$params;
		}
		else
		{
			$myparam = &$jevents_config;
		}
		$this->maxEvents = intval($myparam->get('modlatest_MaxEvents', 15));
		$this->dispMode = intval($myparam->get('modlatest_Mode', 0));
		$this->startNow = intval($myparam->get('startnow', 0));
		$this->pastOnly = intval($myparam->get('pastonly', 0));
		$this->rangeDays = intval($myparam->get('modlatest_Days', 30));
		$this->repeatdisplayoptions = intval($myparam->get('modlatest_NoRepeat', 0));
		$this->multiday = intval($myparam->get('modlatest_multiday', 0));
		$this->displayLinks = intval($myparam->get('modlatest_DispLinks', 1));
		$this->displayYear = intval($myparam->get('modlatest_DispYear', 0));
		$this->disableDateStyle = intval($myparam->get('modlatest_DisDateStyle', 0));
		$this->disableTitleStyle = intval($myparam->get('modlatest_DisTitleStyle', 0));
		$this->linkCloaking = intval($myparam->get('modlatest_LinkCloaking', 0));
		$this->linkToCal = intval($myparam->get('modlatest_LinkToCal', 0));
		$this->customFormatStr = $myparam->get('modlatest_CustFmtStr', '');
		$this->displayRSS = intval($myparam->get('modlatest_RSS', 0));
		$this->sortReverse = intval($myparam->get('modlatest_SortReverse', 0));

		if ($myparam->get("bootstrapcss", 1)==1)
		{
			$cfg = JEVConfig::getInstance();
			if ($cfg->get("bootstrapcss", 1)==1)
			{
				// This version of bootstrap has maximum compatability with JEvents due to enhanced namespacing
				JHTML::stylesheet("com_jevents/bootstrap.css", array(), true);
				// Responsive version of bootstrap with maximum compatibility with JEvents due to enhanced namespacing
				JHTML::stylesheet("com_jevents/bootstrap-responsive.css", array(), true);
			}
			else if ($cfg->get("bootstrapcss", 1)==2)
			{
				JHtmlBootstrap::loadCss();
			}
		}
		else if ($myparam->get("bootstrapcss", 1)==2)
		{
			JHtmlBootstrap::loadCss();
		}

		if (JFile::exists(JPATH_SITE . "/components/com_jevents/assets/css/jevcustom.css"))
		{
			$document = JFactory::getDocument();
			JEVHelper::stylesheet('jevcustom.css', 'components/' . JEV_COM_COMPONENT . '/assets/css/');
		}

		if ($myparam->get("modlatest_customcss", false)){
			JFactory::getDocument()->addStyleDeclaration($myparam->get("modlatest_customcss", false));
		}

		if ($this->dispMode > 8)
			$this->dispMode = 0;

		// $maxEvents hardcoded to 105 for now to avoid bad mistakes in params
		if ($this->maxEvents > 150)
			$this->maxEvents = 150;

		if ($this->displayRSS)
		{
			if ($modid > 0)
			{
				// do not use JRoute since this creates .rss link which normal sef can't deal with
				$this->rsslink = JURI::root() . 'index.php?option=' . JEV_COM_COMPONENT . '&amp;task=modlatest.rss&amp;format=feed&amp;type=rss&amp;modid=' . $modid;
			}
			else
			{
				$this->displayRSS = false;
			}
		}

	}

	function getTheme()
	{
		$theme = JEV_CommonFunctions::getJEventsViewName();
		return $theme;

	}

	/**
	 * Cloaks html link whith javascript
	 *
	 * @param string The cloaking URL
	 * @param string The link text
	 * @return string HTML
	 */
	function _htmlLinkCloaking($url = '', $text = '', $class = '')
	{

		//$link = JRoute::_($url);
		// sef already should be already called below
		$link = $url;

		if ($this->linkCloaking)
		{
			return '<a href="#" onclick="window.location.href=\'' . $link . '\'; return false;" ' . $class . ' >' . $text . '</a>';
		}
		else
		{
			if (strpos($link, "tmpl=component")){
				return '<a href="' . $link . '" ' . $class . '  >' . $text . '</a>';
			}
			else {
				return '<a href="' . $link . '" ' . $class . ' target="_top" >' . $text . '</a>';
			}
		}

	}

	// this could go to a data model class
	// for the time being put it here so the different views can inherit from this 'base' class
	function getLatestEventsData($limit = "")
	{
                // Find the repeat ids to ignore because of pagination
                // when not loading data using JSON we need to reset the shownEventIds array and the page variable in the session
                $registry = JRegistry::getInstance("jevents");
                if (!$registry->get("jevents.fetchlatestevents", 0))
                {
                    JFactory::getApplication()->setUserState("jevents.moduleid".$this->_modid.".shownEventIds",array());
                    JFactory::getApplication()->setUserState("jevents.moduleid".$this->_modid.".page",0);
                }
                $shownEventIds = JFactory::getApplication()->getUserState("jevents.moduleid".$this->_modid.".shownEventIds",array());
                $page = (int)JFactory::getApplication()->getUserState("jevents.moduleid".$this->_modid.".page",0);

		// RSS situation overrides maxecents
		$limit = intval($limit);
		if ($limit > 0)
		{
			$this->maxEvents = $limit;
		}

		$db = JFactory::getDBO();

		$t_datenow = JEVHelper::getNow();
		$this->now = $t_datenow->toUnix(true);
		$this->now_Y_m_d = date('Y-m-d', $this->now);
		$this->now_d = date('d', $this->now);
		$this->now_m = date('m', $this->now);
		$this->now_Y = date('Y', $this->now);
		$this->now_w = date('w', $this->now);
		$t_datenowSQL = $t_datenow->toMysql();

                // To pick up date from URL use this
                /*
                $ymd = JEVHelper::getYMD();
                $t_datenow->setDate($ymd[0],$ymd[1],$ymd[2]);
		$this->now = $t_datenow->toUnix(true);
		$this->now_Y_m_d = date('Y-m-d', $this->now);
		$this->now_d = date('d', $this->now);
		$this->now_m = date('m', $this->now);
		$this->now_Y = date('Y', $this->now);
		$this->now_w = date('w', $this->now);
		$t_datenowSQL = $t_datenow->toMysql();
                */

		// derive the event date range we want based on current date and
		// form the db query.

		$todayBegin = $this->now_Y_m_d . " 00:00:00";
		$yesterdayEnd = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d - 1, $this->now_Y)) . " 23:59:59";

		switch ($this->dispMode) {
			case 0:
			case 1:

				// week start (ie. Sun or Mon) is according to what has been selected in the events
				// component configuration thru the events admin interface.

				$numDay = ($this->now_w - $this->com_starday + 7) % 7;
				// begin of this week
				$beginDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d - $numDay, $this->now_Y)) . " 00:00:00";
				//$thisWeekEnd = date('Y-m-d', JevDate::mktime(0,0,0,$this->now_m,$this->now_d - $this->now_w+6, $this->now_Y)." 23:59:59";
				// end of next week
				$endDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d - $numDay + 13, $this->now_Y)) . " 23:59:59";
				break;

			case 2:
				if ($this->startNow)
				{
					$beginDate = $t_datenowSQL;
					// end of today + $days
					$endDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d + $this->rangeDays, $this->now_Y)) . " 23:59:59";
				}
				else
				{
					// begin of today - $days
					$beginDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d, $this->now_Y)) . " 00:00:00";
					// end of today + $days
					$endDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d + $this->rangeDays, $this->now_Y)) . " 23:59:59";
				}
				break;
			case 3:
			case 5:
			case 6:
			case 8:
				// begin of today - $days
				$beginDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d - $this->rangeDays, $this->now_Y)) . " 00:00:00";
				// end of today + $days
				$endDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d + $this->rangeDays, $this->now_Y)) . " 23:59:59";
				break;
			case 7:
				$beginDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d - $this->rangeDays, $this->now_Y)) . " 00:00:00";
				// end of this month
				$endDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d + $this->rangeDays, $this->now_Y)) . " 23:59:59";
				if ($this->maxEvents)
					$this->maxEvents = $this->maxEvents * 2;
				break;
			case 4:
			default:
				// beginning of this month
				$beginDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, 1, $this->now_Y)) . " 00:00:00";
				// end of this month
				$endDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m + 1, 0, $this->now_Y)) . " 23:59:59";
				break;
		}

		// only past events
		if ($this->pastOnly == 1)
		{
			if ($this->startNow)
			{
				$endDate = $t_datenowSQL;
			}
			else
			{
				$endDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d, $this->now_Y)) . " 00:00:00";
			}
		}
		// only future events
		else if ($this->pastOnly == 2)
		{
			if ($this->startNow)
			{
				$startDate = $t_datenowSQL;
			}
			else
			{
				$startDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d, $this->now_Y)) . " 00:00:00";
			}
		}

		$periodStart = $beginDate; //JString::substr($beginDate,0,10);
		$periodEnd = $endDate; //JString::substr($endDate,0,10);

		$reg =  JFactory::getConfig();
		$reg->set("jev.modparams", $this->modparams);

		//We get filter value to set it up again after getting the module data adn set the published_fv value to 0
		$filter_value = JFactory::getApplication()->getUserStateFromRequest('published_fv_ses', 'published_fv', "0");
		JRequest::setVar('published_fv', "0");
		if ($this->dispMode == 5)
		{
			$rows = $this->datamodel->queryModel->recentIcalEvents($periodStart, $periodEnd, $this->maxEvents, $this->repeatdisplayoptions);
		}
		else if ($this->dispMode == 6)
		{
			$rows = $this->datamodel->queryModel->popularIcalEvents($periodStart, $periodEnd, $this->maxEvents, $this->repeatdisplayoptions, $this->multiday);
		}
		else if ($this->dispMode == 7)
		{
			$rows = $this->datamodel->queryModel->randomIcalEvents($periodStart, $periodEnd, $this->maxEvents, $this->repeatdisplayoptions);
			shuffle($rows);
		}
		else if ($this->dispMode == 8)
		{
			$rows = $this->datamodel->queryModel->recentlyModifiedIcalEvents($periodStart, $periodEnd, $this->maxEvents, $this->repeatdisplayoptions);
		}
		else
		{
			$rows = $this->datamodel->queryModel->listLatestIcalEvents($periodStart, $periodEnd, $this->maxEvents, $this->repeatdisplayoptions, $this->multiday);
		}
		JRequest::setVar('published_fv', $filter_value);
		$reg->set("jev.modparams", false);

		// Time limit plugin constraints
		$reg =  JFactory::getConfig();
		$pastdate = $reg->get("jev.timelimit.past", false);
		$futuredate = $reg->get("jev.timelimit.future", false);
		if ($pastdate)
		{
			$beginDate = $pastdate > $beginDate ? $pastdate : $beginDate;
		}
		if ($futuredate)
		{
			$endDate = $futuredate < $endDate ? $futuredate : $endDate;
		}
		$timeLimitNow = $todayBegin < $beginDate ? $beginDate : $todayBegin;
		$timeLimitNow = JevDate::mktime(0, 0, 0, intval(JString::substr($timeLimitNow, 5, 2)), intval(JString::substr($timeLimitNow, 8, 2)), intval(JString::substr($timeLimitNow, 0, 4)));

		// determine the events that occur each day within our range

		$events = 0;
		// I need the date not the time of day !!
		//$date = $this->now;
		$date = JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d, $this->now_Y);
		$lastDate = JevDate::mktime(0, 0, 0, intval(JString::substr($endDate, 5, 2)), intval(JString::substr($endDate, 8, 2)), intval(JString::substr($endDate, 0, 4)));
		$i = 0;

		$seenThisEvent = array();
		$this->eventsByRelDay = array();

		if (count($rows))
		{

			// sort combined array by date
			if ($this->dispMode == 5)
				usort($rows, array(get_class($this), "_sortEventsByCreationDate"));
			else if ($this->dispMode == 6)
				usort($rows, array(get_class($this), "_sortEventsByHits"));
			else if ($this->dispMode == 7)
				usort($rows, array(get_class($this), "_sortEventsByDate"));
			else if ($this->dispMode == 8)
				usort($rows, array(get_class($this), "_sortEventsByModificationDate"));
		}

		if ($this->dispMode == 6)
		{
			if (count($rows))
			{
				$eventsThisDay = array();
				foreach ($rows as $row)
				{
					$eventsThisDay[] = clone $row;
				}

				if (count($eventsThisDay))
				{
					$this->eventsByRelDay[$i] = $eventsThisDay;
				}
			}
		}
		else if ($this->dispMode == 7)
		{
			if (count($rows))
			{
				$eventsThisDay = array();
				foreach ($rows as $row)
				{
					if ($i * 2 < $this->maxEvents)
					{
						$eventsThisDay[] = clone $row;
						$i = $i + 1;
					}
				}
				$i = 0;
				if (count($eventsThisDay))
				{
					$this->eventsByRelDay[$i] = $eventsThisDay;
				}
			}
		}
		else
		{
			if (count($rows))
			{
				// Timelimit plugin constraints
				while ($date < $timeLimitNow && $this->dispMode != 5 && $this->dispMode != 8)
				{
					$this->eventsByRelDay[$i] = array();
					$date = JevDate::strtotime("+1 day", $date);
					$i++;
				}

				while ($date <= $lastDate)
				{
					// get the events for this $date
					$eventsThisDay = array();
					foreach ($rows as $row)
					{

						if ($this->dispMode == 2 && $this->startNow)
						{
							if ($row->_endrepeat < $t_datenowSQL)
								continue;
						}


						if (($this->dispMode == 5 && $this->checkCreateDay($date, $row))
                                                        || ($this->dispMode == 8 && $this->checkModificationDay($date, $row)) 
                                                        || ($this->dispMode != 5 && $this->dispMode != 8 && $row->checkRepeatDay($date, $this->multiday)))
						{
							if (($this->repeatdisplayoptions && $row->hasrepetition())
									// use settings from the event - multi day event only show once
									|| ($this->multiday == 0 && ($row->ddn() != $row->dup() || $row->mdn() != $row->mup() || $row->ydn() != $row->yup()) && $row->multiday() == 0)
									// override settings from the event - multi day event only show once/on first day
									|| (($this->multiday == 2 || $this->multiday == 3) && ($row->ddn() != $row->dup() || $row->mdn() != $row->mup() || $row->ydn() != $row->yup()) )
							)
							{
								// make sure this event has not already been used!
								$eventAlreadyAdded = false;
								foreach ($this->eventsByRelDay as $ebrd)
								{
									foreach ($ebrd as $evt)
									{
										// could test on devent detail but would need another config option
										if ($row->ev_id() == $evt->ev_id() && $this->repeatdisplayoptions)
										{
											$eventAlreadyAdded = true;
											break;
										}
										else if ($row->rp_id() == $evt->rp_id() && !$this->repeatdisplayoptions)
										{
											$eventAlreadyAdded = true;
											break;
										}
									}
									if ($eventAlreadyAdded)
									{
										break;
									}
								}
								if (!$eventAlreadyAdded)
								{
									$row->moddate = $date;
									$eventsThisDay[] = clone $row;
								}
							}
							else
							{
								$row->moddate = $date;
								$eventsThisDay[] = clone $row;
							}
						}
						if ($events + count($eventsThisDay) >= $this->maxEvents)
						{
							break;
						}
					}
					if (count($eventsThisDay))
					{
						// dmcd May 7/04  bug fix to not exceed maxEvents
						$eventsToAdd = min($this->maxEvents - $events, count($eventsThisDay));
						$eventsThisDay = array_slice($eventsThisDay, 0, $eventsToAdd);
						//sort by time on this day
						if ($this->dispMode !== 5 && $this->dispMode !== 8) 
						{
							usort($eventsThisDay, array(get_class($this), "_sortEventsByTime"));
						}

						$this->eventsByRelDay[$i] = $eventsThisDay;
						$events += count($this->eventsByRelDay[$i]);
					}
					if ($events >= $this->maxEvents)
					{
						break;
					}

                                        // Attempt to handle Brazil timezone changes which happen at midnight - go figure !!!
                                        list($yy,$mm,$dd) = explode("-", strftime("%Y-%m-%d", $date));
                                        $date = JevDate::mktime(0, 0, 0,$mm, $dd+1, $yy);
                                        //echo strftime("%Y-%m-%d %H:%M<br/>", $date);
					$i++;
				}
			}
			if ($events < $this->maxEvents && ($this->dispMode == 1 || $this->dispMode == 3 || $this->dispMode == 5 || $this->dispMode == 6 || $this->dispMode == 8))
			{

				if (count($rows))
				{

					// start from yesterday
					// I need the date not the time of day !!
					$date = JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d - 1, $this->now_Y);
					$lastDate = JevDate::mktime(0, 0, 0, intval(JString::substr($beginDate, 5, 2)), intval(JString::substr($beginDate, 8, 2)), intval(JString::substr($beginDate, 0, 4)));
					$i = -1;

					// Timelimit plugin constraints
					while ($date > $timeLimitNow && $this->dispMode != 5 && $this->dispMode != 8)
					{
						$this->eventsByRelDay[$i] = array();
						$date = JevDate::strtotime("-1 day", $date);
						$i--;
					}

					while ($date >= $lastDate)
					{
						// get the events for this $date
						$eventsThisDay = array();
						foreach ($rows as $row)
						{
							if (($this->dispMode == 5 && $this->checkCreateDay($date, $row)) 
                                                                || ($this->dispMode == 8 && $this->checkModificationDay($date, $row)) 
                                                                || ($this->dispMode != 5 && $this->dispMode != 8 && $row->checkRepeatDay($date, $this->multiday)))
							{
								if (($this->repeatdisplayoptions && $row->hasrepetition())
										// use settings from the event - multi day event only show once
										|| ($this->multiday == 0 && ($row->ddn() != $row->dup() || $row->mdn() != $row->mup() || $row->ydn() != $row->yup()) && $row->multiday() == 0)
										// override settings from the event - multi day event only show once/on first day
										|| (($this->multiday == 2 || $this->multiday == 3) && ($row->ddn() != $row->dup() || $row->mdn() != $row->mup() || $row->ydn() != $row->yup()) )
								)
								{
									// make sure this event has not already been used!
									$eventAlreadyAdded = false;
									foreach ($this->eventsByRelDay as $ebrd)
									{
										foreach ($ebrd as $evt)
										{
											// could test on devent detail but would need another config option
											if ($row->ev_id() == $evt->ev_id() && $this->repeatdisplayoptions)
											{
												$eventAlreadyAdded = true;
												break;
											}
											else if ($row->rp_id() == $evt->rp_id() && !$this->repeatdisplayoptions)
											{
												$eventAlreadyAdded = true;
												break;
											}
										}
										if ($eventAlreadyAdded)
										{
											break;
										}
									}
									if (($this->dispMode == 5 || $this->dispMode == 8 ) && !$eventAlreadyAdded)
									{
										foreach ($eventsThisDay as $evt)
										{
											// could test on devent detail but would need another config option
											if ($row->ev_id() == $evt->ev_id() && $this->repeatdisplayoptions)
											{
												$eventAlreadyAdded = true;
												break;
											}
											else if ($row->rp_id() == $evt->rp_id() && !$this->repeatdisplayoptions)
											{
												$eventAlreadyAdded = true;
												break;
											}
										}
										if ($eventAlreadyAdded)
										{
											break;
										}
									}
									if (!$eventAlreadyAdded)
									{
										$row->moddate = $date;
										$eventsThisDay[] = clone $row;
									}
								}
								else
								{
									$row->moddate = $date;
									$eventsThisDay[] = clone $row;
								}
							}
							if ($events + count($eventsThisDay) >= $this->maxEvents)
							{
								break;
							}
						}
						if (count($eventsThisDay))
						{
							//sort by time on this day
							if ($this->dispMode !== 5 && $this->dispMode !== 8) 
							{							
								usort($eventsThisDay, array(get_class($this), "_sortEventsByTime"));
							}
							$this->eventsByRelDay[$i] = $eventsThisDay;
							$events += count($this->eventsByRelDay[$i]);
						}
						if ($events >= $this->maxEvents)
						{
							break;
						}
						$date = JevDate::strtotime("-1 day", $date);
						$i--;
					}
				}
			}
		}
		if (isset($this->eventsByRelDay) && count($this->eventsByRelDay) && $this->dispMode !== 5 && $this->dispMode !== 8 )
		{

			// When we display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up so sort the data first

			ksort($this->eventsByRelDay, SORT_NUMERIC);
			reset($this->eventsByRelDay);
		}
		if ($this->sortReverse)
		{
			$this->eventsByRelDay = array_reverse($this->eventsByRelDay, true);

			foreach ($this->eventsByRelDay as $relDay => $daysEvents)
			{
				$this->eventsByRelDay[$relDay] = array_reverse($daysEvents, true);
			}
		}

                $page = (int)JFactory::getApplication()->getUserState("jevents.moduleid".$this->_modid.".page",0);
                $direction  = (int)JFactory::getApplication()->getUserState("jevents.moduleid".$this->_modid.".direction",1);
                
		if (isset($this->eventsByRelDay) && count($this->eventsByRelDay))
		{
                        $lastEventDate = false;
                        $firstEventDate = false;
                        if (!isset($shownEventIds[$page])){
                            $shownEventIds[$page] = array();
                        }
			foreach($this->eventsByRelDay as $relDay => $daysEvents){

				reset($daysEvents);

				// get all of the events for this day
				foreach($daysEvents as $dayEvent){
                                        if (!$firstEventDate) {
                                            $firstEventDate = $dayEvent->startrepeat;
                                            $firstEventId = $dayEvent->rp_id;
                                        }
                                        if (!in_array($dayEvent->rp_id, $shownEventIds)){
                                            $shownEventIds[$page][] = $dayEvent->rp_id;
                                        }
                                        if (!isset($lastEventDate)){
                                            $lastEventDate = $dayEvent->startrepeat;
                                            $lastEventId = $dayEvent->rp_id;
                                        }
                                        if ($dayEvent->startrepeat > $lastEventDate) {
                                            $lastEventDate = $dayEvent->startrepeat;
                                            $lastEventId = $dayEvent->rp_id;
                                        }
                                }
                        }

                        JFactory::getApplication()->setUserState("jevents.moduleid".$this->_modid.".shownEventIds",$shownEventIds);
                        JFactory::getApplication()->setUserState("jevents.moduleid".$this->_modid.".firstEventDate",$firstEventDate);
                        JFactory::getApplication()->setUserState("jevents.moduleid".$this->_modid.".lastEventDate",$lastEventDate);

                        // Navigation
                        static $scriptloaded = false;
                        if (!$scriptloaded ){
                            $root = JURI::root();
                            $token= JSession::getFormToken();
                            $script = <<<SCRIPT
function fetchMoreLatestEvents(modid, direction)
{        
        jQuery.ajax({
                    type : 'POST',
                    dataType : 'json',
                    url : "{$root}index.php?option=com_jevents&ttoption=com_jevents&typeaheadtask=gwejson&file=fetchlatestevents&path=module&folder=mod_jevents_latest&token={$token}",
                    data : {'json':JSON.stringify({'modid':modid, 'direction':direction})},
                    contentType: "application/x-www-form-urlencoded; charset=utf-8",
                    scriptCharset: "utf-8"
            })                        
                .done(function( data ){                    
                    jQuery("#mod_events_latest_"+modid+"_data").replaceWith(data.html);
                    try {
                        document.getElementById("mod_events_latest_"+modid+"_data").parentNode.scrollIntoView({block: "start", behavior: "smooth"});
                    }
                    catch (e) {
                    }
                })
                .fail(function(x) {
        alert('fail '+x);
                });
}
SCRIPT;
                            JFactory::getDocument()->addScriptDeclaration($script);
                        }
                }
                else {
                    $firstEventDate = JFactory::getApplication()->getUserState("jevents.moduleid".$this->_modid.".firstEventDate",false);
                    $lastEventDate = JFactory::getApplication()->getUserState("jevents.moduleid".$this->_modid.".lastEventDate",false);

                    if ($direction == 1){
                        // fix the start and end dates for navigation
                        JFactory::getApplication()->setUserState("jevents.moduleid".$this->_modid.".firstEventDate",$lastEventDate);
                    }
                    else if ($direction == -1){
                        JFactory::getApplication()->setUserState("jevents.moduleid".$this->_modid.".lastEventDate",$firstEventDate);
                    }

                }

	}

	function checkCreateDay($date, $row)
	{
		return (JevDate::strftime("%Y-%m-%d", $date) == JString::substr($row->created(), 0, 10));

	}

	function checkModificationDay($date, $row)
	{
		return (JevDate::strftime("%Y-%m-%d", $date) == JString::substr($row->modified(), 0, 10));

	}
        
	public static function _sortEventsByDate(&$a, &$b)
	{
		$adate = $a->_startrepeat;
		$bdate = $b->_startrepeat;
		if ($adate === $bdate) {
			return strcmp($a->_title, $b->_title);
		}
		return strcmp($adate, $bdate);
	}

	public static function _sortEventsByCreationDate(&$a, &$b)
	{
		$adate = $a->created();
		$bdate = $b->created();
		// reverse created date
		return -strcmp($adate, $bdate);

	}

	public static function _sortEventsByModificationDate(&$a, &$b)
	{
		$adate = $a->modified();
		$bdate = $b->modified();
		// reverse created date
		return -strcmp($adate, $bdate);

	}

	public static function _sortEventsByHits(&$a, &$b)
	{
		$ah = $a->hits();
		$bh = $b->hits();
		if ($ah == $bh)
		{
			return 0;
		}
		return ($ah > $bh) ? -1 : 1;

	}

	public static function _sortEventsByTime(&$a, &$b)
	{
		// this custom sort compare function compares the start times of events that are referenced by the a & b vars
		//if ($a->publish_up() == $b->publish_up()) return 0;

		list( $adate, $atime ) = explode(' ', $a->publish_up());
		list( $bdate, $btime ) = explode(' ', $b->publish_up());

		// if allday event, sort by title first on day
		if ($a->alldayevent())
			$atime = '00:00' . $a->title();
		if ($b->alldayevent())
			$btime = '00:00' . $b->title();
		return strcmp($atime, $btime);

	}

	function processFormatString()
	{
		// see if $customFormatStr has been specified.  If not, set it to the default format
		// of date followed by event title.
		if ($this->customFormatStr == NULL)
			$this->customFormatStr = $this->defaultfFormatStr;
		else
		{
			$this->customFormatStr = preg_replace('/^"(.*)"$/', "\$1", $this->customFormatStr);
			$this->customFormatStr = preg_replace("/^'(.*)'$/", "\$1", $this->customFormatStr);
			// escape all " within the string
			// $customFormatStr = preg_replace('/"/','\"', $customFormatStr);
		}

		// strip out event variables and run the string thru an html checker to make sure
		// it is legal html.  If not, we will not use the custom format and print an error
		// message in the module output.  This functionality is not here for now.
		// parse the event variables and reformat them into php syntax with special handling
		// for the startDate and endDate fields.
		//asdbg_break();
		// interpret linefeed as <br />
		$customFormat = nl2br($this->customFormatStr);

		$keywords = array(
			'content', 'eventDetailLink', 'createdByAlias', 'color',
			'createdByUserName', 'createdByUserEmail', 'createdByUserEmailLink',
			'eventDate', 'endDate', 'startDate', 'title', 'category', 'calendar',
			'contact', 'addressInfo', 'location', 'extraInfo',
			'countdown', 'categoryimage', 'duration', 'siteroot', 'sitebase'
		);
		$keywords_or = implode('|', $keywords);
		$whsp = '[\t ]*'; // white space
		$datefm = '\([^\)]*\)'; // date formats
		//$modifiers	= '(?::[[:alnum:]]*)';

		$pattern = '/(\$\{' . $whsp . '(?:' . $keywords_or . ')(?:' . $datefm . ')?' . $whsp . '\})/'; // keyword pattern
		$cond_pattern = '/(\[!?[[:alnum:]]+:[^\]]*])/'; // conditional string pattern e.g. [!a: blabla ${endDate(%a)}]
		// tokenize conditional strings
		$splitTerm = preg_split($cond_pattern, $customFormat, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		$this->splitCustomFormat = array();
		foreach ($splitTerm as $key => $value)
		{
			if (preg_match('/^\[(.*)\]$/', $value, $matches))
			{
				// remove outer []
				$this->splitCustomFormat[$key]['data'] = $matches[1];
				// split condition
				preg_match('/^([^:]*):(.*)$/', $this->splitCustomFormat[$key]['data'], $matches);
				$this->splitCustomFormat[$key]['cond'] = $matches[1];
				$this->splitCustomFormat[$key]['data'] = $matches[2];
			}
			else
			{
				$this->splitCustomFormat[$key]['data'] = $value;
			}
			// tokenize into array
			$this->splitCustomFormat[$key]['data'] = preg_split($pattern, $this->splitCustomFormat[$key]['data'], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		}

		// cleanup, remove white spaces from key words, seperate date parm string and modifier into array;
		// e.g.  ${ keyword ( 'aaaa' ) } => array('keyword', 'aaa',)
		foreach ($this->splitCustomFormat as $ix => $yy)
		{
			foreach ($this->splitCustomFormat[$ix]['data'] as $keyToken => $customToken)
			{
				if (preg_match('/\$\{' . $whsp . '(' . $keywords_or . ')(' . $datefm . ')?' . $whsp . '}/', trim($customToken), $matches))
				{
					$this->splitCustomFormat[$ix]['data'][$keyToken] = array();
					$this->splitCustomFormat[$ix]['data'][$keyToken]['keyword'] = stripslashes($matches[1]);
					if (isset($matches[2]))
					{
						// ('aaa') => aaa
						$this->splitCustomFormat[$ix]['data'][$keyToken]['dateParm'] = preg_replace('/^\(["\']?(.*)["\']?\)$/', "\$1", stripslashes($matches[2]));
					}
				}
				else
				{
					$this->splitCustomFormat[$ix]['data'][$keyToken] = stripslashes($customToken);
				}
			}
		}

	}

	function displayLatestEvents()
	{

		// this will get the viewname based on which classes have been implemented
		$viewname = $this->getTheme();

		$cfg = JEVConfig::getInstance();

		// override global start now setting so that timelimit plugin can use it!
		$compparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$startnow = $compparams->get("startnow",0);
		$compparams->set("startnow",$this->modparams->get("startnow",0));
		$this->getLatestEventsData();
		$compparams->set("startnow",$startnow);

		$content = "";

		$k = 0;
		if (isset($this->eventsByRelDay) && count($this->eventsByRelDay))
		{
			$content .= $this->modparams->get("modlatest_templatetop") || $this->modparams->get("modlatest_templatebottom")? $this->modparams->get("modlatest_templatetop") : '<table class="mod_events_latest_table jevbootstrap" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';

			// Now to display these events, we just start at the smallest index of the $this->eventsByRelDay array
			// and work our way up.

			$firstTime = true;

			// initialize name of com_jevents module and task defined to view
			// event detail.  Note that these could change in future com_event
			// component revisions!!  Note that the '$this->itemId' can be left out in
			// the link parameters for event details below since the event.php
			// component handler will fetch its own id from the db menu table
			// anyways as far as I understand it.

			$this->processFormatString();

			foreach ($this->eventsByRelDay as $relDay => $daysEvents)
			{

				reset($daysEvents);

				// get all of the events for this day
				foreach ($daysEvents as $dayEvent)
				{

					$eventcontent = "";

					// generate output according custom string
					foreach ($this->splitCustomFormat as $condtoken)
					{

						if (isset($condtoken['cond']))
						{
							if ($condtoken['cond'] == 'a' && !$dayEvent->alldayevent())
								continue;
							else if ($condtoken['cond'] == '!a' && $dayEvent->alldayevent())
								continue;
							else if ($condtoken['cond'] == 'e' && !($dayEvent->noendtime() || $dayEvent->alldayevent()))
								continue;
							else if ($condtoken['cond'] == '!e' && ($dayEvent->noendtime() || $dayEvent->alldayevent()))
								continue;
							else if ($condtoken['cond'] == '!m' && $dayEvent->getUnixStartDate() != $dayEvent->getUnixEndDate())
								continue;
							else if ($condtoken['cond'] == 'm' && $dayEvent->getUnixStartDate() == $dayEvent->getUnixEndDate())
								continue;
						}
						foreach ($condtoken['data'] as $token)
						{
							unset($match);
							unset($dateParm);
							$dateParm = "";
							$match = '';
							if (is_array($token))
							{
								$match = $token['keyword'];
								$dateParm = isset($token['dateParm']) ? trim($token['dateParm']) : "";
							}
							else if (strpos($token, '${') !== false)
							{
								$match = $token;
							}
							else
							{
								$eventcontent .= $token;
								continue;
							}

							$this->processMatch($eventcontent, $match, $dayEvent, $dateParm, $relDay);
						} // end of foreach
					} // end of foreach

					if ($firstTime)
						$eventrow = '<tr class="jevrow' . $k . '"><td class="mod_events_latest_first">%s'."</td></tr>\n";
					else
						$eventrow = '<tr class="jevrow' . $k . '"><td class="mod_events_latest">%s'."</td></tr>\n";

					$templaterow = $this->modparams->get("modlatest_templaterow") ? $this->modparams->get("modlatest_templaterow")  : $eventrow;
					$content .= str_replace("%s", $eventcontent , $templaterow);

					$firstTime = false;
				} // end of foreach
				$k++;
				$k %=2;
			} // end of foreach
			$content .=$this->modparams->get("modlatest_templatebottom") ? $this->modparams->get("modlatest_templatebottom") : "</table>\n";
		}
		else if ($this->modparams->get("modlatest_NoEvents", 1))
		{
			$content .= $this->modparams->get("modlatest_templatetop") || $this->modparams->get("modlatest_templatetop") ? $this->modparams->get("modlatest_templatetop") : '<table class="mod_events_latest_table jevbootstrap" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';
			$templaterow = $this->modparams->get("modlatest_templaterow") ? $this->modparams->get("modlatest_templaterow")  : '<tr><td class="mod_events_latest_noevents">%s</td></tr>' . "\n";
			$content .= str_replace("%s", JText::_('JEV_NO_EVENTS') , $templaterow);
			$content .=$this->modparams->get("modlatest_templatebottom") ? $this->modparams->get("modlatest_templatebottom") : "</table>\n";
		}

		$callink_HTML = '<div class="mod_events_latest_callink">'
				. $this->getCalendarLink()
				. '</div>';

		if ($this->linkToCal == 1)
			$content = $callink_HTML . $content;
		if ($this->linkToCal == 2)
			$content .= $callink_HTML;

		if ($this->displayRSS)
		{
			$rssimg = JURI::root() . "media/system/images/livemarks.png";
			$callink_HTML = '<div class="mod_events_latest_rsslink">'
					. '<a href="' . $this->rsslink . '" title="' . JText::_("RSS_FEED") . '" target="_blank">'
					. '<img src="' . $rssimg . '" alt="' . JText::_("RSS_FEED") . '" />'
					. JText::_("SUBSCRIBE_TO_RSS_FEED")
					. '</a>'
					. '</div>';
			$content .= $callink_HTML;
		}

		if ($this->modparams->get("contentplugins", 0)){
			$dispatcher = JEventDispatcher::getInstance();
			$eventdata = new stdClass();
			//$eventdata->text = str_replace("{/toggle","{/toggle}",$content);
			$eventdata->text = $content;
			$dispatcher->trigger('onContentPrepare', array('com_jevents', &$eventdata, &$this->modparams, 0));
			 $content = $eventdata->text;
		}

		return $content;

	}

// end of function

	protected
			function processMatch(&$content, $match, $dayEvent, $dateParm, $relDay)
	{
		$datenow = JEVHelper::getNow();
		$dispatcher = JEventDispatcher::getInstance();
		$compname = JEV_COM_COMPONENT;

		// get the title and start time
		$startDate = JevDate::strtotime($dayEvent->publish_up());
		if ($relDay > 0)
		{
			$eventDate = JevDate::strtotime($datenow->toFormat('%Y-%m-%d ') . JevDate::strftime('%H:%M', $startDate) . " +$relDay days");
		}
		else
		{
			$eventDate = JevDate::strtotime($datenow->toFormat('%Y-%m-%d ') . JevDate::strftime('%H:%M', $startDate) . " $relDay days");
		}
		$endDate = JevDate::strtotime($dayEvent->publish_down());

		list($st_year, $st_month, $st_day) = explode('-', JevDate::strftime('%Y-%m-%d', $startDate));
		list($ev_year, $ev_month, $ev_day) = explode('-', JevDate::strftime('%Y-%m-%d', $startDate));

		$task_events = 'icalrepeat.detail';
		switch ($match) {

			case 'endDate':
			case 'startDate':
			case 'eventDate':
				// Note we need to examine the date specifiers used to determine if language translation will be
				// necessary.  Do this later when script is debugged.

				if (!$this->disableDateStyle)
					$content .= '<span class="mod_events_latest_date">';

				if (!$dayEvent->alldayevent() && $match == "endDate" && (($dayEvent->noendtime() && ($dayEvent->getUnixStartDate() == $dayEvent->getUnixEndDate())) || $dayEvent->getUnixStartTime() == $dayEvent->getUnixEndTime()))
				{
					$time_fmt = "";
				}
				else if (!isset($dateParm) || $dateParm == '')
				{
					if ($this->com_calUseStdTime)
					{
						$time_fmt = $dayEvent->alldayevent() ? '' : IS_WIN ? ' @%I:%M%p' : ' @%l:%M%p';
					}
					else
					{
						$time_fmt = $dayEvent->alldayevent() ? '' : ' @%H:%M';
					}
					$dateFormat = $this->displayYear ? '%a %b %d, %Y' . $time_fmt : '%a %b %d' . $time_fmt;
					$jmatch = new JevDate($$match);
					$content .= $jmatch->toFormat($dateFormat);
					//$content .= JEV_CommonFunctions::jev_strftime($dateFormat, $$match);
				}
				else
				{
					// format endDate when midnight to show midnight!
					if ($match == "endDate" && $dayEvent->sdn() == 59)
					{
						$tempEndDate = $endDate + 1;
						if ($dayEvent->alldayevent() || $dayEvent->noendtime())
						{
							// if an all day event then we don't want to roll to the next day
							$tempEndDate -= 86400;
						}
						$match = "tempEndDate";
					}
					// if a '%' sign detected in date format string, we assume JevDate::strftime() is to be used,
					if (preg_match("/\%/", $dateParm))
					{
						$jmatch = new JevDate($$match);
						$content .= $jmatch->toFormat($dateParm);
					}
					// otherwise the date() function is assumed.
					else
					{
						$content .= date($dateParm, $$match);
					}
					if ($match == "tempEndDate")
					{
						$match = "endDate";
					}
				}

				if (!$this->disableDateStyle)
					$content .= "</span>";
				break;

			case 'title':
				$title = $dayEvent->title();
				if (!empty($dateParm))
				{
					$parts = explode("|", $dateParm);
					if (count($parts) > 0 && JString::strlen($title) > intval($parts[0]))
					{
						$title = JString::substr($title, 0, intval($parts[0]));
						if (count($parts) > 1)
						{
							$title .= $parts[1];
						}
					}
				}
				if (!$this->disableTitleStyle)
					$content .= '<span class="mod_events_latest_content">';
				if ($this->displayLinks)
				{
					$link = $dayEvent->viewDetailLink($ev_year, $ev_month, $ev_day, false, $this->myItemid);
					if ($this->modparams->get("ignorefiltermodule", 0))
					{
						$link = JRoute::_($link . $this->datamodel->getCatidsOutLink() . "&filter_reset=1", false);
					}
					else
					{
						$link = JRoute::_($link . $this->datamodel->getCatidsOutLink());
					}
					$content .= $this->_htmlLinkCloaking($link, JEventsHTML::special($title));
				}
				else
				{
					$content .= JEventsHTML::special($title);
				}
				if (!$this->disableTitleStyle)
					$content .= '</span>';
				break;

			case 'category':
				$catobj = $dayEvent->getCategoryName();
				$content .= JEventsHTML::special($catobj);
				break;

			case 'categoryimage':
				$catobj = $dayEvent->getCategoryImage();
				$content .= $catobj;
				break;

			case 'calendar':
				$catobj = $dayEvent->getCalendarName();
				$content .= JEventsHTML::special($catobj);
				break;

			case 'contact':
				// Also want to cloak contact details so
				$this->modparams->set("image", 1);
				$dayEvent->text = $dayEvent->contact_info();
				$dispatcher->trigger('onContentPrepare', array('com_jevents', &$dayEvent, &$this->modparams, 0));

				if (!empty($dateParm))
				{
					$parts = explode("|", $dateParm);
					if (count($parts) > 0 && JString::strlen(strip_tags($dayEvent->text)) > intval($parts[0]))
					{
						$dayEvent->text = JString::substr(strip_tags($dayEvent->text), 0, intval($parts[0]));
						if (count($parts) > 1)
						{
							$dayEvent->text .= $parts[1];
						}
					}
				}
				
				$dayEvent->contact_info($dayEvent->text);
				$content .= $dayEvent->contact_info();
				break;

			case 'content':  // Added by Kaz McCoy 1-10-2004
				$this->modparams->set("image", 1);
				$dayEvent->data->text = $dayEvent->content();
				$dispatcher->trigger('onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));

				if (!empty($dateParm))
				{
					$parts = explode("|", $dateParm);
					if (count($parts) > 0 && JString::strlen(strip_tags($dayEvent->data->text)) > intval($parts[0]))
					{
						$dayEvent->data->text = JString::substr(strip_tags($dayEvent->data->text), 0, intval($parts[0]));
						if (count($parts) > 1)
						{
							$dayEvent->data->text .= $parts[1];
						}
					}
				}

				$dayEvent->content($dayEvent->data->text);
				//$content .= JString::substr($dayEvent->content, 0, 150);
				$content .= $dayEvent->content();
				break;

			case 'addressInfo':
			case 'location':
				$this->modparams->set("image", 0);
				$dayEvent->data->text = $dayEvent->location();
				$dispatcher->trigger('onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));
				$dayEvent->location($dayEvent->data->text);
				$content .= $dayEvent->location();
				break;

			case 'duration':
				$timedelta = ($dayEvent->noendtime() || $dayEvent->alldayevent()) ? "" : $dayEvent->getUnixEndTime() - $dayEvent->getUnixStartTime();
				if ($timedelta == "")
				{
					break;
				}
				$fieldval = (isset($dateParm) && $dateParm != '') ? $dateParm : JText::_("JEV_DURATION_FORMAT");
				$shownsign = false;
				// whole days!
				if (stripos($fieldval, "%wd") !== false)
				{
					$days = intval($timedelta / (60 * 60 * 24));
					$timedelta -= $days * 60 * 60 * 24;

					if ($timedelta > 3610)
					{
						//if more than 1 hour and 10 seconds over a day then round up the day output
						$days +=1;
					}

					$fieldval = str_ireplace("%wd", $days, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%d") !== false)
				{
					$days = intval($timedelta / (60 * 60 * 24));
					$timedelta -= $days * 60 * 60 * 24;
					/*
					  if ($timedelta>3610){
					  //if more than 1 hour and 10 seconds over a day then round up the day output
					  $days +=1;
					  }
					 */
					$fieldval = str_ireplace("%d", $days, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%h") !== false)
				{
					$hours = intval($timedelta / (60 * 60));
					$timedelta -= $hours * 60 * 60;
					if ($shownsign)
						$hours = abs($hours);
					$hours = sprintf("%02d", $hours);
					$fieldval = str_ireplace("%h", $hours, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%k") !== false)
				{
					$hours = intval($timedelta / (60 * 60));
					$timedelta -= $hours * 60 * 60;
					if ($shownsign)
						$hours = abs($hours);
					$fieldval = str_ireplace("%kgi", $hours, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%m") !== false)
				{
					$mins = intval($timedelta / 60);
					$timedelta -= $hours * 60;
					if ($mins)
						$mins = abs($mins);
					$mins = sprintf("%02d", $mins);
					$fieldval = str_ireplace("%m", $mins, $fieldval);
				}
				$content .= $fieldval;
				break;

			case 'extraInfo':
				$this->modparams->set("image", 0);
				$dayEvent->data->text = $dayEvent->extra_info();
				$dispatcher->trigger('onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));
				$dayEvent->extra_info($dayEvent->data->text);
				$content .= $dayEvent->extra_info();
				break;

			case 'countdown':
                                $timedelta = $dayEvent->getUnixStartTime() - JevDate::mktime();
                            	$now = new JevDate("+0 seconds");
				$now = $now->toFormat("%Y-%m-%d %H:%M:%S");

				$eventStarted = $dayEvent->publish_up() < $now ? 1 : 0 ;
				$eventEnded   = $dayEvent->publish_down() < $now ? 1 : 0 ;

				$fieldval = $dateParm;
				$shownsign = false;
				if (stripos($fieldval, "%nopast") !== false)
				{
					if (!$eventStarted)
					{
						$fieldval = str_ireplace("%nopast", "", $fieldval);
					}
                                        else if (!$eventEnded)
                                        {
                                                $fieldval = JText::_('JEV_EVENT_STARTED');
                                        }
					else
					{
						$fieldval = JText::_('JEV_EVENT_FINISHED');
					}
				}
				if (stripos($fieldval, "%d") !== false)
				{
					$days = intval($timedelta / (60 * 60 * 24));
					$timedelta -= $days * 60 * 60 * 24;
					$fieldval = str_ireplace("%d", $days, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%h") !== false)
				{
					$hours = intval($timedelta / (60 * 60));
					$timedelta -= $hours * 60 * 60;
					if ($shownsign)
						$hours = abs($hours);
					$hours = sprintf("%02d", $hours);
					$fieldval = str_ireplace("%h", $hours, $fieldval);
					$shownsign = true;
				}
				if (stripos($fieldval, "%m") !== false)
				{
					$mins = intval($timedelta / 60);
					$timedelta -= $hours * 60;
					if ($mins)
						$mins = abs($mins);
					$mins = sprintf("%02d", $mins);
					$fieldval = str_ireplace("%m", $mins, $fieldval);
				}

				$content .= $fieldval;
				break;

			case 'createdByAlias':
				$content .= $dayEvent->created_by_alias();
				break;

			case 'createdByUserName':
				$catobj = JEVHelper::getUser($dayEvent->created_by());
				$content .= isset($catobj->username) ? $catobj->username : "";
				break;

			case 'createdByUserEmail':
				// Note that users email address will NOT be available if they don't want to receive email
				$catobj = JEVHelper::getUser($dayEvent->created_by());
				$content .= $catobj->sendEmail ? $catobj->email : '';
				break;

			case 'createdByUserEmailLink':
				// Note that users email address will NOT be available if they don't want to receive email
				$content .= JRoute::_("index.php?option="
								. $compname
								. "&task=" . $task_events
								. "&agid=" . $dayEvent->id()
								. "&year=" . $st_year
								. "&month=" . $st_month
								. "&day=" . $st_day
								. "&Itemid=" . $this->myItemid . $this->catout);
				break;

			case 'color':
				$content .= $dayEvent->bgcolor();
				break;

			case 'eventDetailLink':
				$link = $dayEvent->viewDetailLink($st_year, $st_month, $st_day, false, $this->myItemid);
				$link = JRoute::_($link . $this->datamodel->getCatidsOutLink());
				$content .= $link;

				/*
				  $content .= JRoute::_("index.php?option="
				  . $compname
				  . "&task=".$task_events
				  . "&agid=".$dayEvent->id()
				  . "&year=".$st_year
				  . "&month=".$st_month
				  . "&day=".$st_day
				  . "&Itemid=".$this->myItemid . $this->catout);
				 */
				break;

			case 'siteroot':
				$content .= JUri::root();
				break;
			case 'sitebase':
				$content .= Juri::base();
				break;

			default:
				try {
					if (strpos($match, '${') !== false)
					{
						$parts = explode('${', $match);
						$tempstr = "";
						foreach ($parts as $part)
						{
							if (strpos($part, "}") !== false)
							{
								// limit to 2 because we may be using joomla content plugins
								$subparts = explode("}", $part,2);

								if (strpos($subparts[0], "#") > 0)
								{
									$formattedparts = explode("#", $subparts[0]);
									$subparts[0] = $formattedparts[0];
								}
								else
								{
									$formattedparts = array($subparts[0], "%s", "");
								}
								$subpart = "_" . $subparts[0];

								if (isset($dayEvent->$subpart))
								{
									$temp = $dayEvent->$subpart;
									if ($temp != "")
									{
										$tempstr .= str_replace("%s", $temp, $formattedparts[1]);
									}
									else if (isset($formattedparts[2]))
									{
										$tempstr .= str_replace("%s", $temp, $formattedparts[2]);
									}
								}
								else if (isset($dayEvent->customfields[$subparts[0]]['value']))
								{
									$temp = $dayEvent->customfields[$subparts[0]]['value'];
									if ($temp != "")
									{
										$tempstr .= str_replace("%s", $temp, $formattedparts[1]);
									}
									else if (isset($formattedparts[2]))
									{
										$tempstr .= str_replace("%s", $temp, $formattedparts[2]);
									}
								}
								else
								{
									$matchedByPlugin = false;
									$layout = "list";
									static $fieldNameArrays = array();
									$jevplugins = JPluginHelper::getPlugin("jevents");
									foreach ($jevplugins as $jevplugin)
									{
										$classname = "plgJevents" . ucfirst($jevplugin->name);
										if (is_callable(array($classname, "substitutefield")))
										{
											if (!isset($fieldNameArrays[$classname]))
											{
												$fieldNameArrays[$classname] = call_user_func(array($classname, "fieldNameArray"), $layout);
                                                                                                
                                                                                                if (isset($fieldNameArrays[$classname]["values"]) && is_array($fieldNameArrays[$classname]["values"]))
                                                                                                {
                                                                                                    // Special case where $fieldname has option value in it e.g. sizedimages 
                                                                                                    foreach($fieldNameArrays[$classname]["values"] as $idx => $fieldname){
                                                                                                        if (strpos($fieldname, ";")>0){
                                                                                                            $temp = explode(";", $fieldname);
                                                                                                            $fn = $temp[0];
                                                                                                            if (!in_array($fn,$fieldNameArrays[$classname]["values"])){
                                                                                                                $fieldNameArrays[$classname]["values"][] = $fn;
                                                                                                                $fieldNameArrays[$classname]["labels"][] = $fieldNameArrays[$classname]["labels"][$idx] ;
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                                
											}
											if (isset($fieldNameArrays[$classname]["values"]))
											{
                                                                                                $strippedSubPart = $subparts[0];
                                                                                                if (strpos($subparts[0], ";")){
                                                                                                    $temp = explode(";", $subparts[0]);
                                                                                                    $strippedSubPart = $temp[0];
                                                                                                }
												if (in_array($subparts[0], $fieldNameArrays[$classname]["values"]) || in_array($strippedSubPart, $fieldNameArrays[$classname]["values"]))
												{
													$matchedByPlugin = true;
													// is the event detail hidden - if so then hide any custom fields too!
													if (!isset($dayEvent->_privateevent) || $dayEvent->_privateevent != 3)
													{
														$temp = call_user_func(array($classname, "substitutefield"), $dayEvent, $subparts[0]);
														if ($temp != "")
														{
															$tempstr .= str_replace("%s", $temp, $formattedparts[1]);
														}
														else if (isset($formattedparts[2]))
														{
															$tempstr .= str_replace("%s", $temp, $formattedparts[2]);
														}
													}
												}
											}
										}
									}
									if (!$matchedByPlugin) {
										// Layout editor code
										include_once(JEV_PATH . "/views/default/helpers/defaultloadedfromtemplate.php");
										ob_start();
										// false at the end to stop it running through the plugins
										$part = "{{Dummy Label:".implode("#", $formattedparts)."}}";
										DefaultLoadedFromTemplate(false, false, $dayEvent, 0, $part,  false);
										$newpart = ob_get_clean();
										if ($newpart != $part) {
											$tempstr .= $newpart;
											$matchedByPlugin = true;
										}
									}
									// none of the plugins has replaced the output so we now replace the blank formatted part!
									if (!$matchedByPlugin && isset($formattedparts[2]))
									{
										$tempstr .= str_replace("%s", "", $formattedparts[2]);
									}
									//$dispatcher->trigger( 'onLatestEventsField', array( &$dayEvent, $subparts[0], &$tempstr));
								}
								$tempstr .= $subparts[1];
							}
							else
							{
								$tempstr .= $part;
							}
						}
						$content .= $tempstr;
					}
					else if ($match) {
						$content .= $match;
					}
				}
				catch (Exception $e) {
					if ($match)
						$content .= $match;
				}
				break;
		} // end of switch

	}

	protected
			function getCalendarLink()
	{
		$menu =  JFactory::getApplication()->getMenu('site');
		$menuItem = $menu->getItem($this->myItemid);
		if ($menuItem && $menuItem->component == JEV_COM_COMPONENT)
		{
			$viewlayout = isset($menuItem->query["view"]) ? ($menuItem->query["view"] . "." . $menuItem->query["layout"]) : "calendar.month";
			$task = isset($menuItem->query["task"]) ? $menuItem->query["task"] : ($menuItem->query["view"] . "." . $menuItem->query["layout"]);
		}
		else
		{
			$task = "month.calendar";
		}
		return $this->_htmlLinkCloaking(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . $this->myItemid . "&task=" . $task . $this->catout, true), JText::_('JEV_CLICK_TOCOMPONENT'));

	}

        protected function getNavigationIcons() {
                $registry = JRegistry::getInstance("jevents");
                $params = $registry->get("jevents.moduleparams", new JRegistry);
                $content = "";
                if ($params->get("showNavigation",0)){
                    $content .= '<div class="mod_events_latest_navigation">';
                    $page = (int)JFactory::getApplication()->getUserState("jevents.moduleid".$this->_modid.".page",0);
                    if ($page>0 || $params->get("modlatest_Mode",0)!=2) {
                        $content .= '<a class="btn btn-default" href="#" onclick="fetchMoreLatestEvents('.$this->_modid.',-1);return false;">'.JText::_('JEV_PRIOR_EVENTS').'</a>';
                    }
                    $content .= '<a class="btn btn-default" href="#" onclick="fetchMoreLatestEvents('.$this->_modid.',1);return false;">'.JText::_('JEV_NEXT_EVENTS').'</a>';
                    $content .= '</div>';
                }
                return $content;
        }

}

// end of class



