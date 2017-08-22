<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jicaleventrepeat.php 2992 2011-11-10 15:15:22Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\String\StringHelper;

class jIcalEventRepeat extends jIcalEventDB
{

	private
			$_nextRepeat = null;
	private
			$_prevRepeat = null;

                
        
	function id()
	{
		if (!isset($this->_rp_id))
			return parent::id();
		return $this->_rp_id;

	}

	function rp_id()
	{
		return $this->_rp_id;

	}

	function ev_id()
	{
		return parent::id();

	}

	/*
	  function dtstart($val=""){
	  if (JString::strlen($val)==0) return $this->getUnixStartTime();
	  else {
	  $this->_dtstart=$val;
	  $this->_unixstarttime=$val;
	  $this->_publish_up = JevDate::strftime( '%Y-%m-%d %H:%M:%S',$this->getUnixStartTime());
	  }
	  }

	  function dtend($val=""){
	  if (JString::strlen($val)==0) return $this->getUnixEndTime();
	  else {
	  $this->_dtend=$val;
	  $this->_unixendtime=$val;
	  $this->_publish_down = JevDate::strftime( '%Y-%m-%d %H:%M:%S',$this->getUnixEndTime());
	  }
	  }
	 */

	function checkRepeatMonth($cellDate, $year, $month)
	{
		// builds and returns array
		if (!isset($this->eventDaysMonth))
		{
			$this->eventDaysMonth = array();
		}

		if (!array_key_exists($cellDate, $this->eventDaysMonth))
		{
			if ($this->eventOnDate($cellDate))
			{
				$this->eventDaysMonth[$cellDate] = true;
			}
			/*
			  // I don't need to do this since eventOnDate checks the multiday condition
			  if ($this->eventOnDate($cellDate) && ($this->_multiday || !isset($this->_alreadyShown) || !$this->_alreadyShown)) {
			  $this->eventDaysMonth[$cellDate]=true;
			  $this->_alreadyShown = true;
			  }
			 */
			else
			{
				$this->eventDaysMonth[$cellDate] = false;
			}
		}

		return $this->eventDaysMonth[$cellDate];

	}

	function eventOnDate($testDate, $multidayTreatment = 0)
	{
		if (!isset($this->_startday))
		{
			$this->_startday = JevDate::mktime(0, 0, 0, $this->mup(), $this->dup(), $this->yup());
			$this->_startday_plus1 = JevDate::mktime(0, 0, 0, $this->mup(), $this->dup()+1, $this->yup());
			$this->_endday = JevDate::mktime(0, 0, 0, $this->mdn(), $this->ddn(), $this->ydn());
			// if ends on midnight then testing day should ignore the second day since no one wants this event to show
			if ($this->hdn() + $this->mindn() + $this->sdn() == 0 && $this->_startday != $this->_endday)
			{
				$this->_endday = JevDate::mktime(0, 0, 0, $this->mdn(), $this->ddn()-1, $this->ydn());
			}
		}                
		if ($this->_startday <= $testDate && $this->_endday >= $testDate)
		{
			// if only show on first day
			if ($multidayTreatment == 2 && $testDate >= $this->_startday_plus1)
			{
				return false;
			}
			// don't show multiday suppressed events after the first day if multiday is not true
			if ($multidayTreatment == 0)
			{
				if (!$this->_multiday && $testDate >= $this->_startday_plus1)
				{
					return false;
				}
			}
			return true;
		}
		else
			return false;

	}

	function isEditable()
	{
		return true;

	}

	function hasrepetition()
	{
		#if (isset($this->_rr_id)  && $this->_rr_id>0 ) return true;
		if (isset($this->_freq) && ($this->_freq != 'none'))
			return true;
		else
			return false;

	}

	function editTask()
	{
		// TODO add methods for editing specific repeats
		return "icalrepeat.edit";

	}

	function detailTask()
	{
		// TODO add methods for editing specific repeats
		return "icalrepeat.detail";

	}

	function editLink($sef = false)
	{
		$Itemid = JEVHelper::getItemid();
		// rp_id is added for return via cancel only
		// I pass in the rp_id so that I can return to the repeat I was viewing before editing
		// I need $year,$month,$day So that I can return to an appropriate date after saving an event (the repetition ids have all changed so I can't go back there!!)
		list($year, $month, $day) = JEVHelper::getYMD();
		$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . parent::editTask() . '&evid=' . parent::id() . '&Itemid=' . $Itemid . '&rp_id=' . $this->rp_id() . "&year=$year&month=$month&day=$day";
		//$link = $sef?JRoute::_( $link ,true ):$link;
		$link .= JRequest::getInt("pop",0)?"&tmpl=component&pop=1":"";
		$link = JRoute::_($link, true);
		return $link;

	}

	function editCopyLink($sef = false)
	{
		$Itemid = JEVHelper::getItemid();
		// rp_id is added for return via cancel only
		// I pass in the rp_id so that I can return to the repeat I was viewing before editing
		// I need $year,$month,$day So that I can return to an appropriate date after saving an event (the repetition ids have all changed so I can't go back there!!)
		list($year, $month, $day) = JEVHelper::getYMD();
		$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . parent::editCopyTask() . '&evid=' . parent::id() . '&Itemid=' . $Itemid . '&rp_id=' . $this->rp_id() . "&year=$year&month=$month&day=$day";
		//$link = $sef?JRoute::_( $link ,true ):$link;
		$link .= JRequest::getInt("pop",0)?"&tmpl=component&pop=1":"";
		$link = JRoute::_($link, true);
		return $link;

	}

	function editRepeatLink($sef = false)
	{
		$Itemid = JEVHelper::getItemid();
		list($year, $month, $day) = JEVHelper::getYMD();
		$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . $this->editTask() . '&evid=' . $this->id() . '&Itemid=' . $Itemid
				. "&year=$year&month=$month&day=$day";
		//$link = $sef?JRoute::_( $link ,true ):$link;
		$link .= JRequest::getInt("pop",0)?"&tmpl=component&pop=1":"";
		$link = JRoute::_($link, true);
		return $link;

	}

	function deleteLink($sef = false)
	{
		$Itemid = JEVHelper::getItemid();
		// I need $year,$month,$day So that I can return to an appropriate date after deleting a repetition!!!
		list($year, $month, $day) = JEVHelper::getYMD();
		$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . parent::deleteTask() . '&evid=' . parent::id() . '&Itemid=' . $Itemid . "&year=$year&month=$month&day=$day";
		//$link = $sef?JRoute::_( $link ,true ):$link;
		$link = JRoute::_($link, true);
		return $link;

	}

	function deleteRepeatLink($sef = false)
	{
		$Itemid = JEVHelper::getItemid();
		// I need $year,$month,$day So that I can return to an appropriate date after deleting a repetition!!!
		list($year, $month, $day) = JEVHelper::getYMD();
		$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . $this->deleteTask() . '&cid=' . $this->id() . '&Itemid=' . $Itemid . "&year=$year&month=$month&day=$day";
		//$link = $sef?JRoute::_( $link ,true ):$link;
		$link = JRoute::_($link, true);
		return $link;

	}

	function deleteFutureLink($sef = false)
	{
		$Itemid = JEVHelper::getItemid();
		// I need $year,$month,$day So that I can return to an appropriate date after deleting a repetition!!!
		list($year, $month, $day) = JEVHelper::getYMD();
		$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . $this->deleteFutureTask() . '&cid=' . $this->id() . '&Itemid=' . $Itemid . "&year=$year&month=$month&day=$day";
		//$link = $sef?JRoute::_( $link ,true ):$link;
		$link = JRoute::_($link, true);
		return $link;

	}

	function viewDetailLink($year, $month, $day, $sef = true, $Itemid = 0)
	{
                $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
                if ($params->get("permatarget",0)){
                    $Itemid = (int) $params->get("permatarget",0);
                }
                else {
                    $Itemid = $Itemid > 0 ? $Itemid : JEVHelper::getItemid($this);
                }
		// uid = event series unique id i.e. the actual event
		$title = JApplicationHelper::stringURLSafe($this->title());
                if ($this->rp_id()){
                    $link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . $this->detailTask() . "&evid=" . $this->rp_id() . '&Itemid=' . $Itemid
				. "&year=$year&month=$month&day=$day&title=" . $title . "&uid=" . urlencode($this->uid());
                }
                else {
                    $link = "index.php?option=" . JEV_COM_COMPONENT . "&task=icalevent.detail&evid=" . $this->ev_id() . '&Itemid=' . $Itemid
				. "&year=$year&month=$month&day=$day&title=" . $title . "&uid=" . urlencode($this->uid());
                }
		if (JRequest::getCmd("tmpl", "") == "component" && JRequest::getCmd('task', 'selectfunction') != 'icalevent.select' && JRequest::getCmd("option", "") != "com_acymailing" && JRequest::getCmd("option", "") != "com_jnews" && JRequest::getCmd("option", "") != "com_search" && JRequest::getCmd("jevtask", "") != "crawler.listevents" && JRequest::getCmd("jevtask", "") != "modcal.ajax")
		{
			$link .= "&tmpl=component";
		}
		// SEF is applied later
		$link = $sef ? JRoute::_($link, true) : $link;
		return $link;

	}

	function deleteTask()
	{
		return "icalrepeat.delete";

	}

	function deleteFutureTask()
	{
		return "icalrepeat.deletefuture";

	}

	function checkRepeatWeek($this_currentdate, $week_start, $week_end)
	{
		//TODO fix this
		//if ($this->vevent->eventOnDate($this_currentdate)) return true;
		if ($this->eventOnDate($this_currentdate) && ($this->_multiday || !isset($this->_alreadyShown) || !$this->_alreadyShown))
		{
			$this->_alreadyShown = true;
			return true;
		}
		return false;

	}

	function checkRepeatDay($this_currentdate, $multidayTreatment = 0)
	{
		//if ($this->vevent->eventOnDate($this_currentdate)) return true;
		if ($this->eventOnDate($this_currentdate, $multidayTreatment))
			return true;
		return false;

	}

	function repeatSummary()
	{
		$result = parent::repeatSummary();
		if ($this->_eventdetail_id != $this->_detail_id)
		{
			$result .= "<div class='ev_repeatexception'>" . JText::_('JEV_REPEATEXCEPTION') . "</div>";
		}

		//$result .= "<div style='font-weight:bold;color:black;background-color:yellow'>Repeat Summary needs more work still!</div>";
		return $result;

	}

	function previousnextLinks()
	{
		$cfg = JEVConfig::getInstance();
		$result = parent::previousnextLinks();
		if ($this->prevRepeat() || $this->nextRepeat())
		{
			if ($this->prevRepeat())
			{
				$result .= "<div class='ev_prevrepeat'>";
				$result .= "<a href='" . $this->prevRepeat() . "' title='" . JText::_('JEV_PREVIOUSREPEAT') . "' class='" . $cfg->get('com_navbarcolor') . "'>" . JText::_('JEV_PREVIOUSREPEAT') . "</a>";
				$result .= "</div>";
			}
			if ($this->nextRepeat())
			{
				$result .= "<div class='ev_nextrepeat'>";
				$result .= "<a href='" . $this->nextRepeat() . "' title='" . JText::_('JEV_NEXTREPEAT') . "' class='" . $cfg->get('com_navbarcolor') . "'>" . JText::_('JEV_NEXTREPEAT') . "</a>";
				$result .= "</div>";
			}
		}
		return $result;

	}

	function previousLink()
	{
		$cfg = JEVConfig::getInstance();
		$result = parent::previousnextLinks();
		if ($this->prevRepeat())
		{
			$result .= "<div class='ev_prevrepeat'>";
			$result .= "<a href='" . $this->prevRepeat() . "' title='" . JText::_('JEV_PREVIOUSREPEAT') . "' class='" . $cfg->get('com_navbarcolor') . "'>" . JText::_('JEV_PREVIOUSREPEAT') . "</a>";
			$result .= "</div>";
		}
		return $result;

	}

	function nextLink()
	{
		$cfg = JEVConfig::getInstance();
		$result = parent::previousnextLinks();
		if ($this->nextRepeat())
		{
			$result .= "<div class='ev_nextrepeat'>";
			$result .= "<a href='" . $this->nextRepeat() . "' title='" . JText::_('JEV_NEXTREPEAT') . "' class='" . $cfg->get('com_navbarcolor') . "'>" . JText::_('JEV_NEXTREPEAT') . "</a>";
			$result .= "</div>";
		}
		return $result;

	}

	function prevRepeat()
	{
		if (is_null($this->_prevRepeat))
		{
			$this->getAdjacentRepeats();
		}
		return $this->_prevRepeat;

	}

	function nextRepeat()
	{
		if (is_null($this->_nextRepeat))
		{
			$this->getAdjacentRepeats();
		}
		return $this->_nextRepeat;

	}

	private
			function getAdjacentRepeats()
	{

		$jinput = JFactory::getApplication()->input;
		$pop = $jinput->getInt('pop', 0);
		$tmpl ='';
		$popc =  "&pop=" . $pop;

		if ($pop == 1) {
			$tmpl = "&tmpl=component";
		}

		$Itemid = JEVHelper::getItemid();
		list($year, $month, $day) = JEVHelper::getYMD();

		$db = JFactory::getDBO();

		$sql = "SELECT rpt.*,det.summary as title , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup FROM #__jevents_repetition  as rpt
			 LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id WHERE rpt.eventid=" . $this->ev_id() . " AND rpt.rp_id <> " . $this->rp_id() . " AND rpt.startrepeat<='" . $this->_startrepeat . "' ORDER BY rpt.startrepeat DESC limit 1";
		$db->setQuery($sql);
		$prior = $db->loadObject();
		if (!is_null($prior))
		{
			$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . $this->detailTask() . "&evid=" . $prior->rp_id . '&Itemid=' . $Itemid
					. "&year=$prior->yup&month=$prior->mup&day=$prior->dup&uid=" . urlencode($this->uid()) . "&title=" . JApplicationHelper::stringURLSafe($prior->title) . $tmpl . $popc;
			$link = JRoute::_($link);
			$this->_prevRepeat = $link;
		}
		else
		{
			$this->_prevRepeat = false;
		}

		$sql = "SELECT rpt.*,det.summary as title, YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup FROM #__jevents_repetition  as rpt
			 LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id WHERE rpt.eventid=" . $this->ev_id() . " AND rpt.rp_id <> " . $this->rp_id() . " AND rpt.startrepeat>='" . $this->_startrepeat . "' ORDER BY rpt.startrepeat ASC limit 1";
		$db->setQuery($sql);
		$post = $db->loadObject();
		if (!is_null($post))
		{
			$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . $this->detailTask() . "&evid=" . $post->rp_id . '&Itemid=' . $Itemid
					. "&year=$post->yup&month=$post->mup&day=$post->dup&uid=" . urlencode($this->uid()) . "&title=" . JApplicationHelper::stringURLSafe($post->title) . $tmpl . $popc;
			$link = JRoute::_($link);
			$this->_nextRepeat = $link;
		}
		else
		{
			$this->_nextRepeat = false;
		}

	}

	function previousnextEventLinks()
	{
		$cfg = JEVConfig::getInstance();
		$result = "";
		if ($this->prevEvent() || $this->nextEvent())
		{
			if ($this->prevEvent())
			{
				$result .= "<div class='ev_prevrepeat'>";
				$result .= "<a href='" . $this->prevEvent() . "' title='" . JText::_('JEV_PREVIOUSEVENT') . "' class='" . $cfg->get('com_navbarcolor') . "'>" . JText::_('JEV_PREVIOUSEVENT') . "</a>";
				$result .= "</div>";
			}
			if ($this->nextEvent())
			{
				$result .= "<div class='ev_nextrepeat'>";
				$result .= "<a href='" . $this->nextEvent() . "' title='" . JText::_('JEV_NEXTEVENT') . "' class='" . $cfg->get('com_navbarcolor') . "'>" . JText::_('JEV_NEXTEVENT') . "</a>";
				$result .= "</div>";
			}
		}
		return $result;

	}

	function prevEvent()
	{
		if (!isset($this->_prevEvent))
		{
			$this->getAdjacentEvents();
		}
		return $this->_prevEvent;

	}

	function nextEvent()
	{
		if (is_null($this->_nextEvent))
		{
			$this->getAdjacentEvents();
		}
		return $this->_nextEvent;

	}

	private
			function getAdjacentEvents()
	{

		$Itemid = JEVHelper::getItemid();
		list($year, $month, $day) = JEVHelper::getYMD();
		$this->datamodel = new JEventsDataModel();

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		if (method_exists("JEVHelper", "getMinYear"))
		{
			$minyear = JEVHelper::getMinYear();
			$maxyear = JEVHelper::getMaxYear();
		}
		else
		{
			$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
			$minyear = $params->get("com_earliestyear", 1970);
			$maxyear = $params->get("com_latestyear", 2150);
		}

		$pastev = 0;
		$limit = 10;
		while ($pastev == 0)
		{
			$prev = $this->datamodel->queryModel->listIcalEvents($minyear.'-01-01 00:00:00', $this->_startrepeat, "rpt.startrepeat DESC, rpt.rp_id DESC", false, "", "", $limit);
			for ($i = 0; $i < count($prev); $i++)
			{
				if ($this->_startrepeat > $prev[$i]->_startrepeat)
				{
					$prior = $prev[$i];
					$pastev = 1;
					break;
				}
				else if ($prev[$i]->_rp_id < $this->_rp_id && $this->_startrepeat == $prev[$i]->_startrepeat)
				{
					$prior = $prev[$i];
					$pastev = 1;
					break;
				}
			}
			if (count($prev) < $limit)
			{
				$pastev = 1;
			}
			$limit = $limit * 2;
		}
		if (isset($prior) && !is_null($prior))
		{
			$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . $this->detailTask() . "&evid=" . $prior->_rp_id . '&Itemid=' . $Itemid
					. "&year=" . $prior->_yup . "&month=" . $prior->_mup . "&day=" . $prior->_dup . "&uid=" . urlencode($prior->_uid) . "&title=" . JApplicationHelper::stringURLSafe($prior->_title);
			$link = JRoute::_($link);
			$this->_prevEvent = $link;
		}
		else
		{
			$this->_prevEvent = false;
		}

		$pastevpost = 0;
                $post = null;
		$limit = 10;
		while ($pastevpost == 0) {
			$next = $this->datamodel->queryModel->listIcalEvents($this->_startrepeat, $maxyear.'-12-31 00:00:00', "rpt.startrepeat ASC, rpt.rp_id ASC", false, "", "", $limit);
			for ($i = 0; $i < count($next); $i++)
			{
				if ($this->_startrepeat < $next[$i]->_startrepeat)
				{
					$post = $next[$i];
					$pastevpost = 1;
					break;
				}
				else if ($next[$i]->_rp_id > $this->_rp_id && $this->_startrepeat == $next[$i]->_startrepeat)
				{
					$post = $next[$i];
					$pastevpost = 1;
					break;
				}
			}
			if (count($next) < $limit)
			{
				$pastevpost = 1;
			}
			$limit = $limit * 2;
		}

		if (!is_null($post))
		{
			$link = "index.php?option=" . JEV_COM_COMPONENT . "&task=" . $this->detailTask() . "&evid=" . $post->rp_id . '&Itemid=' . $Itemid
					. "&year=$post->yup&month=$post->mup&day=$post->dup&uid=" . urlencode($this->uid()) . "&title=" . JApplicationHelper::stringURLSafe($post->title);
			$link = JRoute::_($link);
			$this->_nextEvent = $link;
		}
		else
		{
			$this->_nextEvent = false;
		}

	}

}
