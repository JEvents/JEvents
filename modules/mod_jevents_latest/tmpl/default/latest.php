<?php

/**
 * copyright (C) 2008 GWE Systems Ltd - All rights reserved
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the module  frontend
 *
 * @static
 */
class DefaultModLatestView
{

	var $_modid = null;
	var $modparams = null;
	// Note that we encapsulate all this in a class to create
	// an isolated name space from everythng else (I hope).
	var $aid = null;
	var $lang = null;
	var $catid = null;
	var $inccss = null;
	var $maxEvents = null;
	var $dispMode = null;
	var $rangeDays = null;
	var $norepeat = null;
	var $displayLinks = null;
	var $displayYear = null;
	var $disableDateStyle = null;
	var $disableTitleStyle = null;
	var $linkCloaking = null;
	var $customFormatStr = null;
	var $_defaultfFormatStr12 = '${eventDate}[!a: - ${endDate(%l:%M%p)}]<br />${title}';
	var $_defaultfFormatStr24 = '${eventDate}[!a: - ${endDate(%H:%M)}]<br />${title}';
	var $defaultfFormatStr = null;
	var $linkToCal = null; // 0=no, 1=top, 2=bottom
	var $sortReverse = null;
	var $displayRSS = null;
	var $rsslink = null;
	var $com_starday = null;
	var $com_calUseStdTime = null;
	var $datamodel = null;
	var $catout = null;


	function DefaultModLatestView($params, $modid)
	{

		$this->_modid = $modid;
		$this->modparams = & $params;

		$jevents_config = & JEVConfig::getInstance();

		$this->datamodel = new JEventsDataModel();
		// find appropriate Itemid and setup catids for datamodel
		$this->myItemid = $this->datamodel->setupModuleCatids($this->modparams);
		$this->catout = $this->datamodel->getCatidsOutLink(true);

		$user = & JFactory::getUser();
		$this->aid = $user->aid;
		// Can't use getCfg since this cannot be changed by Joomfish etc.
		$tmplang = & JFactory::getLanguage();
		$this->langtag = $tmplang->getTag();

		// get params exclusive to module
		$this->inccss = $params->get('modlatest_inccss', 0);
		if ($this->inccss)
		{
			JEVHelper::componentStylesheet($this, "modstyle.css");
		}

		// get params exclusive to component
		$this->com_starday = intval($jevents_config->get('com_starday', 0));
		$this->com_calUseStdTime = intval($jevents_config->get('com_calUseStdTime', 1));
		if ($this->com_calUseStdTime)
		{
			$this->defaultfFormatStr = $this->_defaultfFormatStr12;
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
		$this->norepeat = intval($myparam->get('modlatest_NoRepeat', 0));
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

		if ($this->dispMode > 6)
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
	function _htmlLinkCloaking($url='', $text='', $class='')
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
			return '<a href="' . $link . '" ' . $class . '>' . $text . '</a>';
		}

	}

	// this could go to a data model class
	// for the time being put it here so the different views can inherit from this 'base' class
	function getLatestEventsData($limit="")
	{

		// RSS situation overrides maxecents
		$limit = intval($limit);
		if ($limit > 0)
		{
			$this->maxEvents = $limit;
		}

		$db = & JFactory::getDBO();

		$t_datenow = JEVHelper::getNow();
		$this->now = $t_datenow->toUnix(true);
		$this->now_Y_m_d = date('Y-m-d', $this->now);
		$this->now_d = date('d', $this->now);
		$this->now_m = date('m', $this->now);
		$this->now_Y = date('Y', $this->now);
		$this->now_w = date('w', $this->now);
		$t_datenowSQL = $t_datenow->toMysql();

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
				// begin of today - $days
				$beginDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d - $this->rangeDays, $this->now_Y)) . " 00:00:00";
				// end of today + $days
				$endDate = date('Y-m-d', JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d + $this->rangeDays, $this->now_Y)) . " 23:59:59";
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

		$periodStart = $beginDate; //substr($beginDate,0,10);
		$periodEnd = $endDate; //substr($endDate,0,10);

		$reg = & JFactory::getConfig();
		$reg->set("jev.modparams", $this->modparams);		
		if ($this->dispMode == 5)
		{
			$this->sortReverse = true;
			$rows = $this->datamodel->queryModel->recentIcalEvents($periodStart, $periodEnd, $this->maxEvents, $this->norepeat);
		}
		else if ($this->dispMode == 6)
		{
			$rows = $this->datamodel->queryModel->popularIcalEvents($periodStart, $periodEnd, $this->maxEvents, $this->norepeat);
		}
		else
		{
			$rows = $this->datamodel->queryModel->listLatestIcalEvents($periodStart, $periodEnd, $this->maxEvents, $this->norepeat, $this->multiday);
		}		
		$reg->set("jev.modparams", false);

		// determine the events that occur each day within our range

		$events = 0;
		// I need the date not the time of day !!
		//$date = $this->now;
		$date = JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d, $this->now_Y);
		$lastDate = JevDate::mktime(0, 0, 0, intval(substr($endDate, 5, 2)), intval(substr($endDate, 8, 2)), intval(substr($endDate, 0, 4)));
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
			else
				usort($rows, array(get_class($this), "_sortEventsByDate"));
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
		else
		{
			if (count($rows))
			{
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


						if (($this->dispMode == 5 && $this->checkCreateDay($date, $row)) || ($this->dispMode != 5 && $row->checkRepeatDay($date, $this->multiday)))
						{
							if (($this->norepeat && $row->hasrepetition())
									// use settings from the event - multi day event only show once
									|| ($this->multiday == 0 && $row->ddn() != $row->dup() && $row->multiday() == 0)
									// override settings from the event - multi day event only show once/on first day
									|| (($this->multiday == 2 || $this->multiday == 3) && $row->ddn() != $row->dup() )
							)
							{
								// make sure this event has not already been used!
								$eventAlreadyAdded = false;
								foreach ($this->eventsByRelDay as $ebrd)
								{
									foreach ($ebrd as $evt)
									{
										// could test on devent detail but would need another config option
										if ($row->ev_id() == $evt->ev_id())
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
						usort($eventsThisDay, array(get_class($this), "_sortEventsByTime"));

						$this->eventsByRelDay[$i] = $eventsThisDay;
						$events += count($this->eventsByRelDay[$i]);
					}
					if ($events >= $this->maxEvents)
					{
						break;
					}
					$date = JevDate::strtotime("+1 day", $date);
					$i++;
				}
			}
			if ($events < $this->maxEvents && ($this->dispMode == 1 || $this->dispMode == 3 || $this->dispMode == 5 || $this->dispMode == 6))
			{

				if (count($rows))
				{

					// start from yesterday
					// I need the date not the time of day !!
					$date = JevDate::mktime(0, 0, 0, $this->now_m, $this->now_d - 1, $this->now_Y);
					$lastDate = JevDate::mktime(0, 0, 0, intval(substr($beginDate, 5, 2)), intval(substr($beginDate, 8, 2)), intval(substr($beginDate, 0, 4)));
					$i = -1;

					while ($date >= $lastDate)
					{
						// get the events for this $date
						$eventsThisDay = array();
						foreach ($rows as $row)
						{
							if (($this->dispMode == 5 && $this->checkCreateDay($date, $row)) || ($this->dispMode != 5 && $row->checkRepeatDay($date, $this->multiday)))
							{
								if (($this->norepeat && $row->hasrepetition())
										// use settings from the event - multi day event only show once
										|| ($this->multiday == 0 && $row->ddn() != $row->dup() && $row->multiday() == 0)
										// override settings from the event - multi day event only show once/on first day
										|| (($this->multiday == 2 || $this->multiday == 3) && $row->ddn() != $row->dup() )
								)
								{
									// make sure this event has not already been used!
									$eventAlreadyAdded = false;
									foreach ($this->eventsByRelDay as $ebrd)
									{
										foreach ($ebrd as $evt)
										{
											// could test on devent detail but would need another config option
											if ($row->ev_id() == $evt->ev_id())
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
									if ($this->dispMode == 5 && !$eventAlreadyAdded)
									{
										foreach ($eventsThisDay as $evt)
										{
											// could test on devent detail but would need another config option
											if ($row->ev_id() == $evt->ev_id())
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
							usort($eventsThisDay, array(get_class($this), "_sortEventsByTime"));
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
		if (isset($this->eventsByRelDay) && count($this->eventsByRelDay))
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

	}

	function checkCreateDay($date, $row)
	{
		return (JevDate::strftime("%Y-%m-%d", $date) == substr($row->created(), 0, 10));

	}

	function _sortEventsByDate(&$a, &$b)
	{
		$adate = $a->_startrepeat;
		$bdate = $b->_startrepeat;
		return strcmp($adate, $bdate);

	}

	function _sortEventsByCreationDate(&$a, &$b)
	{
		$adate = $a->created();
		$bdate = $b->created();
		// reverse created date
		return -strcmp($adate, $bdate);

	}

	function _sortEventsByHits(&$a, &$b)
	{
		$ah = $a->hits();
		$bh = $b->hits();
		if ($ah == $bh)
		{
			return 0;
		}
		return ($ah > $bh) ? -1 : 1;

	}

	function _sortEventsByTime(&$a, &$b)
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
			'countdown','categoryimage'
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
				if (preg_match('/\$\{' . $whsp . '(' . $keywords_or . ')(' . $datefm . ')?' . $whsp . '}/', $customToken, $matches))
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

		$cfg = & JEVConfig::getInstance();
		$compname = JEV_COM_COMPONENT;

		$this->getLatestEventsData();

		$content = "";

		$k = 0;
		if (isset($this->eventsByRelDay) && count($this->eventsByRelDay))
		{
			$content .= '<table class="mod_events_latest_table" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';

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

					if ($firstTime)
						$content .= '<tr class="jevrow'.$k.'"><td class="mod_events_latest_first">';
					else
						$content .= '<tr class="jevrow'.$k.'"><td class="mod_events_latest">';

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
								$content .= $token;
								continue;
							}

							$this->processMatch($content, $match, $dayEvent, $dateParm, $relDay);
						} // end of foreach
					} // end of foreach
					$content .= "</td></tr>\n";
					$firstTime = false;
				} // end of foreach
				$k ++;
				$k %=2;
			} // end of foreach
			$content .="</table>\n";
		}
		else
		{
			$content .= '<table class="mod_events_latest_table" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">';
			$content .= '<tr class="jevrow'.$k.'"><td class="mod_events_latest_noevents">' . JText::_('JEV_NO_EVENTS') . '</td></tr>' . "\n";
			$content .="</table>\n";
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
		return $content;

	}

// end of function

	protected function processMatch(&$content, $match, $dayEvent, $dateParm, $relDay)
	{
		$datenow = JEVHelper::getNow();
		$dispatcher = & JDispatcher::getInstance();

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

				if (!$dayEvent->alldayevent() && $match == "endDate" && ($dayEvent->noendtime() || $dayEvent->getUnixStartTime() == $dayEvent->getUnixEndTime()))
				{
					$time_fmt = "";
				}
				else if (!isset($dateParm) || $dateParm == '')
				{
					if ($this->com_calUseStdTime)
					{
						$time_fmt = $dayEvent->alldayevent() ? '' : ' @%l:%M%p';
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
					if ($match == "endDate" && $dayEvent->sdn()==59){
						$tempEndDate  = $endDate + 1;
						if ($dayEvent->alldayevent()){
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
					else {
						$content .= date($dateParm, $$match);
					}
					if ($match == "tempDndDate" ){
						$match = "endDate";
					}
				}

				if (!$this->disableDateStyle)
					$content .= "</span>";
				break;

			case 'title':
				$title = $dayEvent->title();
				if (!empty ($dateParm)){
					$parts = explode("|",$dateParm);
					if (count($parts)>0 && strlen($title)>  intval($parts[0])){
						$title = substr($title, 0, intval($parts[0]));
						if (count($parts)>1) {
							$title .= $parts[1];
						}
					}
				}
				if (!$this->disableTitleStyle)
					$content .= '<span class="mod_events_latest_content">';
				if ($this->displayLinks)
				{

					$link = $dayEvent->viewDetailLink($ev_year, $ev_month, $ev_day, false, $this->myItemid);
					$link = JRoute::_($link . $this->datamodel->getCatidsOutLink());

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
				$dispatcher->trigger( 'onContentPrepare', array('com_jevents', &$dayEvent, &$this->modparams, 0));
				
				$dayEvent->contact_info($dayEvent->text);
				$content .= $dayEvent->contact_info();
				break;

			case 'content':  // Added by Kaz McCoy 1-10-2004
				$this->modparams->set("image", 1);
				$dayEvent->data->text = $dayEvent->content();
				$dispatcher->trigger( 'onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));
								
				if (!empty ($dateParm)){
					$parts = explode("|",$dateParm);
					if (count($parts)>0 && strlen(strip_tags($dayEvent->data->text)) >  intval($parts[0])){
						$dayEvent->data->text = substr(strip_tags($dayEvent->data->text), 0, intval($parts[0]));
						if (count($parts)>1) {
							$dayEvent->data->text .= $parts[1];
						}
					}
				}
				
				$dayEvent->content($dayEvent->data->text);
				//$content .= substr($dayEvent->content, 0, 150);
				$content .= $dayEvent->content();
				break;

			case 'addressInfo':
			case 'location':
				$this->modparams->set("image", 0);
				$dayEvent->data->text = $dayEvent->location();
				$dispatcher->trigger( 'onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));
				$dayEvent->location($dayEvent->data->text);
				$content .= $dayEvent->location();
				break;

			case 'extraInfo':
				$this->modparams->set("image", 0);
				$dayEvent->data->text = $dayEvent->extra_info();
				$dispatcher->trigger( 'onContentPrepare', array('com_jevents', &$dayEvent->data, &$this->modparams, 0));
				$dayEvent->extra_info($dayEvent->data->text);
				$content .= $dayEvent->extra_info();
				break;

			case 'countdown':
				$timedelta = $dayEvent->getUnixStartTime() - JevDate::mktime();
				$fieldval = $dateParm;
				$shownsign = false;
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
				$catobj = JFactory::getUser($dayEvent->created_by());
				$content .= isset($catobj->username) ? $catobj->username : "";
				break;

			case 'createdByUserEmail':
				// Note that users email address will NOT be available if they don't want to receive email
				$catobj = JFactory::getUser($dayEvent->created_by());
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

								$subparts = explode("}", $part);
								
								if (strpos($subparts[0],"#")>0){
									$formattedparts = explode("#", $subparts[0]);
									$subparts[0] = $formattedparts[0];
								}
								else {
									$formattedparts = array($subparts[0], "%s","");
								}
								$subpart = "_" .$subparts[0];
								
								if (isset($dayEvent->$subpart))
								{
									$temp = $dayEvent->$subpart;
									if ($temp !="") {
										$tempstr .= str_replace("%s",$temp,$formattedparts[1]);
									}
									else {
										$tempstr .= str_replace("%s",$temp,$formattedparts[2]);
									}										
								}		
								else if (isset($dayEvent->customfields[$subparts[0]]['value']))
								{
									$temp = $dayEvent->customfields[$subparts[0]]['value'];
									if ($temp !="") {
										$tempstr .= str_replace("%s",$temp,$formattedparts[1]);
									}
									else {
										$tempstr .= str_replace("%s",$temp,$formattedparts[2]);
									}										
								}
								else {
									
									$layout = "list";
									static $fieldNameArrays = array();
									$jevplugins = JPluginHelper::getPlugin("jevents");
									foreach ($jevplugins as $jevplugin){
										$classname = "plgJevents".ucfirst($jevplugin->name);
										if (is_callable(array($classname,"substitutefield"))){
											 if (!isset($fieldNameArrays[$classname])){
												$fieldNameArrays[$classname] = call_user_func(array($classname,"fieldNameArray"),$layout);
											 }
											if ( isset($fieldNameArrays[$classname]["values"])) {
												if (in_array($subparts[0],$fieldNameArrays[$classname]["values"] )){
													// is the event detail hidden - if so then hide any custom fields too!
													if (!isset($event->_privateevent) || $event->_privateevent!=3){
														$temp = call_user_func(array($classname,"substitutefield"),$dayEvent,$subparts[0]);
														if ($temp !="") {
															$tempstr .= str_replace("%s",$temp,$formattedparts[1]);
														}
														else {
															$tempstr .= str_replace("%s",$temp,$formattedparts[2]);
														}
													}
												}
											}
										}
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
					else if ($match)
						$content .= $match;
				}
				catch (Exception $e) {
					if ($match)
						$content .= $match;
				}
				break;
		} // end of switch

	}

	protected function getCalendarLink()
	{
		$menu = & JApplication::getMenu('site');
		$menuItem = $menu->getItem($this->myItemid);
		if ($menuItem && $menuItem->component == JEV_COM_COMPONENT)
		{
			$task = isset($menuItem->query["task"]) ? $menuItem->query["task"] : ($menuItem->query["view"] . "." . $menuItem->query["layout"]);
		}
		else
		{
			$task = "month.calendar";
		}
		return $this->_htmlLinkCloaking(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=" . $this->myItemid . "&task=" . $task . $this->catout, true), JText::_('JEV_CLICK_TOCOMPONENT'));

	}

}

// end of class



