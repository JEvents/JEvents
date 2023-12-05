<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: adminqueries.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;

// load language constants

use Joomla\CMS\Factory;

JEVHelper::loadLanguage('admin');

class JEventsAdminDBModel extends JEventsDBModel
{

	/**
	 * gets raw vevent (not a rpt) usually for editing purposes
	 *
	 *
	 * @param int $agid vevent id
	 *
	 * @return stdClass details of vevent selected
	 */
	function getVEventById($agid)
	{

		$db   = Factory::getDbo();
		$user = Factory::getUser();

		$app    = Factory::getApplication();
		$input  = $app->input;

		// Force state value to event state!
		$accessibleCategories = $this->accessibleCategoryList();

		$catwhere   = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$extrajoin  = "";
		$extrawhere = "";
		$params     = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin  = "\n LEFT JOIN #__jevents_catmap as catmap ON catmap.evid = ev.ev_id";
			$extrajoin  .= "\n LEFT JOIN  #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrawhere = " AND catmapcat.access " . ' IN (' . JEVHelper::getAid($user) . ')';
			$extrawhere .= " AND catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$catwhere   = "\n WHERE 1 ";
		}

		// in case we have an event with no category set for some reason
		// $accessibleCategories .= ",0";
		$query = "SELECT ev.*,rr.*, det.*, ev.state as state"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = ev.detail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. $extrajoin
			. $catwhere
			. $extrawhere
			. "\n AND ev.ev_id = '$agid'";
		if (!$user->get("isRoot"))
		{
			$query .= "\n AND ev.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user));
		}
		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if (count($rows) > 0)
		{

			// Check multi-category access
			// do not use jev_com_component incase we call this from locations etc.
			$params = ComponentHelper::getParams($input->getCmd("option"));
			if ($params->get("multicategory", 0))
			{
				// get list of categories this event is in - are they all accessible?
				$db->setQuery("SELECT catid FROM #__jevents_catmap WHERE evid=" . $rows[0]->ev_id . " ORDER BY ordering ASC");
				$catids = $db->loadColumn();

				// are there any catids not in list of accessible Categories
				$inaccessiblecats = array_diff($catids, explode(",", $accessibleCategories));
				if (count($inaccessiblecats))
				{
					$inaccessiblecats[] = -1;
					$inaccessiblecats   = implode(",", $inaccessiblecats);

					$jevtask = $input->getString("jevtask", "");
					$isedit  = false;
					// not only for edit pages but for all backend changes we ignore the language filter on categories
					if (strpos($jevtask, "icalevent.edit") !== false || strpos($jevtask, "icalrepeat.edit") !== false || $app->isClient('administrator') || !$user->get("isRoot"))
					{
						$isedit = true;
					}
					if ($isedit)
					{
						$db->setQuery("SELECT id FROM #__categories WHERE extension='com_jevents' and id in($inaccessiblecats)");
						/*
						 * See http://www.jevents.net/forum/viewtopic.php?f=24&t=26928&p=142283#p142283
						$db->setQuery("SELECT id FROM #__categories WHERE extension='com_jevents' and id in($inaccessiblecats)"
								. "\n AND access NOT IN (" . JEVHelper::getAid($user) . ')');
						 */
					}
					else
					{
						$db->setQuery("SELECT id FROM #__categories WHERE extension='com_jevents' and id in($inaccessiblecats)");
					}
					$realcatids = $db->loadColumn();
					if (count($realcatids))
					{
						if ($isedit && $app->isClient('site'))
						{
							$Itemid = $input->getInt("Itemid");
							$app->enqueueMessage(Text::_("JEV_SORRY_CANT_EDIT_FROM_THAT_MENU_ITEM"), 'warning');
							$app->redirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&Itemid=$Itemid", false));
						}

						return null;
					}
					else
					{
						$catids = array_intersect($catids, explode(",", $accessibleCategories));

					}
				}
				$rows[0]->catids = $catids;
			}

			return $rows[0];
		}
		else return null;
	}

	function getVEventRepeatById($rp_id)
	{

		$db                   = Factory::getDbo();
		$user                 = Factory::getUser();
		$accessibleCategories = $this->accessibleCategoryList();
		$app                  = Factory::getApplication();
		$input                = $app->input;
		$query                = "SELECT ev.*, rpt.*, rr.*, det.*"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND rpt.rp_id = '$rp_id'"
			. "\n AND ev.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user));

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if (count($rows) > 0)
		{

			// check multi-category access
			// do not use jev_com_component incase we call this from locations etc.
			$params = ComponentHelper::getParams($input->getCmd("option"));
			if ($params->get("multicategory", 0))
			{
				// get list of categories this event is in - are they all accessible?
				$db->setQuery("SELECT catid FROM #__jevents_catmap WHERE evid=" . $rows[0]->ev_id);
				$catids = $db->loadColumn();

				// are there any catids not in list of accessible Categories
				$inaccessiblecats = array_diff($catids, explode(",", $accessibleCategories));
				if (count($inaccessiblecats))
				{
					return null;
				}
				$rows[0]->catids = $catids;
			}

			return $rows[0];
		}
		else return null;

	}

	// Used in Dashboard
	function getEventCounts()
	{
		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();
		$t_datenow            = JEVHelper::getNow();
		$t_datenowSQL         = $t_datenow->toSql();

		$query  = "SELECT count(rpt.rp_id) as count, rpt.endrepeat > '$t_datenowSQL' as future, rpt.endrepeat < '$t_datenowSQL' as past"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			// . "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND ev.state = 1 "
			. " \n GROUP BY future, past";

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		$data = array(0,0,0);
		$total = 0;
		foreach ($rows as $row)
		{
			if ($row->future)
			{
				$data[1] = (int) $row->count;
			}
			if ($row->past)
			{
				$data[2] = (int)  $row->count;
			}
			$total  += (int) $row->count;
		}
		$data[0] = $total;
		return $data;

	}

	// Used in Dashboard
	function getUnpublishedEventCounts()
	{
		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();
		$t_datenow            = JEVHelper::getNow();
		$t_datenowSQL         = $t_datenow->toSql();

		$query  = "SELECT count(rpt.rp_id) as count"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			// . "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND rpt.endrepeat > '$t_datenowSQL'  "
			. "\n AND ev.state = 0 ";

		$db->setQuery($query);

		$count = $db->loadResult();

		return (int) $count;
	}

	// Used in Dashboard
	function getNewEventCounts()
	{
		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();

		$lastweek             = new JEVDate('-7 days');
		$lastweekSQL          = $lastweek->toSql();

		$lastmonth             = new JEVDate('-1 month');
		$lastmonthSQL         = $lastmonth->toSql();

		$query  = "SELECT count(ev.ev_id) as count, ev.created > '$lastweekSQL' as week,  ev.created > '$lastmonthSQL' as month"
			. "\n FROM #__jevents_vevent as ev"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND ev.created > '$lastmonthSQL' "
			. "\n AND ev.state = 1 "
			. " \n GROUP BY month, week";

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		$data = array(0,0,0);
		$total = 0;
		foreach ($rows as $row)
		{
			if ($row->week)
			{
				$data[1] += (int) $row->count;
			}
			if ($row->month)
			{
				$data[2] += (int)  $row->count;
			}
			$total  += (int) $row->count;
		}
		$data[0] = $total;
		return $data;

	}

	// Used in RSVP Pro Dashboard
	function getSingleSessionCounts()
	{
		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();
		$t_datenow            = JEVHelper::getNow();
		$t_datenowSQL         = $t_datenow->toSql();

		$jevparams =  ComponentHelper::getParams(JEV_COM_COMPONENT);
		$where = array();
		$join = array();

		if ($jevparams->get("multicategory",0)){
			$where[]= "ev.catid IN ("
				. " SELECT catmap.catid FROM #__jevents_catmap as catmap"
				. " LEFT JOIN #__categories AS catmapcat ON catmap.catid = catmapcat.id"
				. " WHERE catmapcat.access IN (" . JEVHelper::getAid($user) . ")"
				. " )";
		}

		$where[] = "(atd.allrepeats=1 and atd.allowregistration>0 )";
		$where[] = "ev.ev_id IS NOT NULL";

		$query  = "SELECT count(distinct ev.ev_id) as count, "
			. "(atd.regclose > ".$db->quote($t_datenowSQL).") as future, "
			. "(atd.regclose <= ".$db->quote($t_datenowSQL).") as past "
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jev_attendance AS atd ON atd.ev_id = ev.ev_id"
			. ( count( $join) ? "\n LEFT JOIN  " . implode( ' LEFT JOIN ', $join) : '' )
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. ( count( $where ) ? "\n AND " . implode( ' AND ', $where ) : '' )
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND ev.state = 1 "
			. " \n GROUP BY future, past";

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		$data = array(0,0,0);
		$total = 0;
		foreach ($rows as $row)
		{
			if ($row->future)
			{
				$data[1] = (int) $row->count;
			}
			if ($row->past)
			{
				$data[2] = (int)  $row->count;
			}
			$total  += (int) $row->count;
		}
		$data[0] = $total;
		return $data;

	}

	// Used in RSVP Pro Dashboard
	function getRepeatingSessionCounts()
	{
		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();
		$t_datenow            = JEVHelper::getNow();
		$t_datenowSQL         = $t_datenow->toSql();

		$jevparams =  ComponentHelper::getParams(JEV_COM_COMPONENT);
		$where = array();
		$join = array();

		if ($jevparams->get("multicategory",0)){
			$where[]= "ev.catid IN ("
				. " SELECT catmap.catid FROM #__jevents_catmap as catmap"
				. " LEFT JOIN #__categories AS catmapcat ON catmap.catid = catmapcat.id"
				. " WHERE catmapcat.access IN (" . JEVHelper::getAid($user) . ")"
				. " )";
		}

		$where[] = "(atd.allrepeats=0 and atd.allowregistration>0 )";
		$where[] = "ev.ev_id IS NOT NULL";

		$query  = "SELECT count(distinct rpt.rp_id) as count, "
			. "(rpt.startrepeat > ".$db->quote($t_datenowSQL).") as future, "
			. "(rpt.startrepeat <= ".$db->quote($t_datenowSQL).") as past "
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jev_attendance AS atd ON atd.ev_id = ev.ev_id"
			. ( count( $join) ? "\n LEFT JOIN  " . implode( ' LEFT JOIN ', $join) : '' )
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. ( count( $where ) ? "\n AND " . implode( ' AND ', $where ) : '' )
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND ev.state = 1 "
			. " \n GROUP BY future, past";

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		$data = array(0,0,0);
		$total = 0;
		foreach ($rows as $row)
		{
			if ($row->future)
			{
				$data[1] = (int) $row->count;
			}
			if ($row->past)
			{
				$data[2] = (int)  $row->count;
			}
			$total  += (int) $row->count;
		}
		$data[0] = $total;
		return $data;

	}

	// Used in Dashboard
	function getNewSessionCounts()
	{
		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();

		$lastweek             = new JEVDate('-7 days');
		$lastweekSQL          = $lastweek->toSql();

		$lastmonth             = new JEVDate('-1 month');
		$lastmonthSQL         = $lastmonth->toSql();

		$query  = "SELECT count(ev.ev_id) as count, ev.created > '$lastweekSQL' as week,  ev.created > '$lastmonthSQL' as month"
			. "\n FROM #__jevents_vevent as ev"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND ev.created > '$lastmonthSQL' "
			. "\n AND ev.state = 1 "
			. " \n GROUP BY month, week";

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		$data = array(0,0,0);
		$total = 0;
		foreach ($rows as $row)
		{
			if ($row->week)
			{
				$data[1] += (int) $row->count;
			}
			if ($row->month)
			{
				$data[2] += (int)  $row->count;
			}
			$total  += (int) $row->count;
		}
		$data[0] = $total;
		return $data;

	}

	// Used in Dashboard
	function getUpcomingEventAttendeesCounts()
	{
		if (! PluginHelper::isEnabled("jevents", "jevrsvppro"))
		{
			return array(0,0,0);
		}
		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();

		$t_datenow            = JEVHelper::getNow();
		$t_datenowSQL         = $t_datenow->toSql();

		$thisweek             = new JEVDate('+7 days');
		$thisweekSQL          = $thisweek->toSql();

		$thismonth             = new JEVDate('1 month');
		$thismonthSQL         = $thismonth->toSql();

		// repeating events first
		$query  = "SELECT sum(atdc.atdcount) as count, rpt.startrepeat <= '$thisweekSQL' as week,  rpt.startrepeat <= '$thismonthSQL' as month"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jev_attendance as atd ON atd.ev_id = ev.ev_id"
			. "\n LEFT JOIN #__jev_attendeecount as atdc ON atd.id = atdc.at_id  AND atdc.rp_id = rpt.rp_id"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND rpt.startrepeat > '$t_datenowSQL' "
			. "\n AND ev.state = 1 "
			. " \n GROUP BY month, week";

		$db->setQuery($query);

		$reprows = $db->loadObjectList();

		// then non-repeating events
		$query  = "SELECT sum(atdc.atdcount) as count, rpt.startrepeat <= '$thisweekSQL' as week,  rpt.startrepeat <= '$thismonthSQL' as month"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jev_attendance as atd ON atd.ev_id = ev.ev_id"
			. "\n LEFT JOIN #__jev_attendeecount as atdc ON atd.id = atdc.at_id"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND atdc.rp_id = 0"
			. "\n AND rpt.startrepeat > '$t_datenowSQL' "
			. "\n AND ev.state = 1 "
			. " \n GROUP BY month, week";

		$db->setQuery($query);

		$nonreprows = $db->loadObjectList();

		$data = array(0,0,0);
		$total = 0;
		foreach ($reprows as $row)
		{
			if ($row->week)
			{
				$data[1] += (int) $row->count;
			}
			if ($row->month)
			{
				$data[2] += (int)  $row->count;
			}
			$total  += (int) $row->count;
		}
		foreach ($nonreprows as $row)
		{
			if ($row->week)
			{
				$data[1] += (int) $row->count;
			}
			if ($row->month)
			{
				$data[2] += (int)  $row->count;
			}
			$total  += (int) $row->count;
		}
		$data[0] = $total;
		return $data;

	}

	// Used in Dashboard
	function getUpcomingEventAttendees($max = 10)
	{
		if (! PluginHelper::isEnabled("jevents", "jevrsvppro"))
		{
            $data = array('start' => array(), 'title' => array(), 'count' => array(), 'link' => array());
            return $data;
		}

		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();

		$t_datenow            = JEVHelper::getNow();
		$t_datenowSQL         = $t_datenow->toSql();

		// repeating events first
		$query  = "SELECT atdc.atdcount, det.summary as title, rpt.startrepeat, atd.id as atd_id, 0 as repeating , rpt.rp_id "
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON rpt.eventdetail_id = det.evdet_id"
			. "\n LEFT JOIN #__jev_attendance as atd ON atd.ev_id = ev.ev_id"
			. "\n INNER JOIN #__jev_attendeecount as atdc ON atd.id = atdc.at_id  AND atdc.rp_id = rpt.rp_id"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND rpt.startrepeat > '$t_datenowSQL' "
			. "\n AND ev.state = 1 "
			. "\n ORDER BY rpt.startrepeat ASC";

		$db->setQuery($query, 0, $max);

		$reprows = $db->loadObjectList();

		// then non-repeating events
		$query  = "SELECT atdc.atdcount, det.summary as title, rpt.startrepeat, atd.id as atd_id, 1 as repeating, 0 as rp_id"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON rpt.eventdetail_id = det.evdet_id"
			. "\n LEFT JOIN #__jev_attendance as atd ON atd.ev_id = ev.ev_id"
			. "\n INNER JOIN #__jev_attendeecount as atdc ON atd.id = atdc.at_id"
			. "\n WHERE ev.catid IN(" . $accessibleCategories . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND atdc.rp_id = 0"
			. "\n AND rpt.startrepeat > '$t_datenowSQL' "
			. "\n AND ev.state = 1 "
			. "\n GROUP BY ev.ev_id"
			. "\n ORDER BY rpt.startrepeat ASC"
		;

		$db->setQuery($query, 0, $max);

		$nonreprows = $db->loadObjectList();

		$rows = array_merge($reprows, $nonreprows);
		usort($rows, function($a, $b) {
			return strcmp($a->startrepeat, $b->startrepeat);
		});

		// Just the first 10
		$rows = array_slice($rows, 0, $max);

		$data = array('start' => array(), 'title' => array(), 'count' => array(), 'link' => array());

		foreach ($rows as $row)
		{
			$data['start'][] = $row->startrepeat;
			$data['title'][] = $row->title; // . '\n' . $row->startrepeat;
			$data['count'][] = $row->atdcount;
			$data['link'][] = JRoute::_("index.php?option=com_rsvppro&task=attendees.overview&atd_id[]=" . $row->atd_id . "|" . $row->rp_id . "&repeating=" . $row->repeating, false);
		}
		return $data;

	}

	// Used in Dashboard
	function getEventCountsByCategory($numberOfCategories = 10)
	{
		$db                   = Factory::getDbo();
		$accessibleCategories = $this->accessibleCategoryList();
		$user                 = Factory::getUser();
		$t_datenow            = JEVHelper::getNow();
		$t_datenowSQL         = $t_datenow->toSql();

		$catwhere = "AND ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$extrajoin = "\n LEFT JOIN #__categories AS catmapcat ON ev.catid = catmapcat.id";
		$catfield = " ev.catid as catid, catmapcat.params, catmapcat.title";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin    = "\nLEFT JOIN #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin   .= "\nLEFT JOIN #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$catwhere   = "\n AND catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$catfield = " catmap.catid as catid , catmapcat.params, catmapcat.title";
		}

		$query  = "SELECT count(rpt.rp_id) as count, $catfield"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. $extrajoin
			. "\n WHERE state = 1 "
			. $catwhere
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND rpt.endrepeat > '$t_datenowSQL'"
			. " \n GROUP BY catid"
			. " \n ORDER BY count desc";

		$db->setQuery($query, 0, $numberOfCategories);

		$rows = $db->loadObjectList();

		return $rows;

	}

	// Used in Dashboard
	function getEventCountsByDay()
	{
		$db                   = Factory::getDbo();
		$user                 = Factory::getUser();
		$lastweek             = new JevDate("-7 days");
		$lastweekSQL          = $lastweek->toSql();

		$query  = "SELECT count(ev.ev_id) as count, DAYOFWEEK(ev.created) as weekday"
			. "\n FROM #__jevents_vevent as ev"
			. "\n WHERE state = 1 "
			. "\n AND ev.catid IN(" . $this->accessibleCategoryList() . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND ev.created > '$lastweekSQL'"
			. " \n GROUP BY weekday"
			. " \n ORDER BY weekday asc";

		$db->setQuery($query, 0, 10);

		$rows = $db->loadObjectList();

		return $rows;

	}

	// Used in Dashboard
	function getEventCountsByWeek()
	{
		$db                   = Factory::getDbo();
		$user                 = Factory::getUser();

		$t_datenow            = JEVHelper::getNow();
		$t_datenowSQL         = $t_datenow->toSql();

		$eightweeks           = new JevDate("+8 weeks");
		$eightweeksSQL        = $eightweeks->toSql();


		// See https://dev.mysql.com/doc/refman/5.7/en/date-and-time-functions.html#function_yearweek
		https://stackoverflow.com/questions/30364141/mysql-convert-yearweek-to-date
		$query  = "SELECT count(rpt.rp_id) as count, YEARWEEK(rpt.startrepeat, 1) as evweeknum, YEARWEEK('$t_datenowSQL') as todayweek"
		//$query  = "SELECT rpt.rp_id, WEEK(rpt.startrepeat, 1) as evweeknum, WEEK('$t_datenowSQL') as todayweek"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")"
			. "\n AND ev.access  IN ( " . JEVHelper::getAid($user) . ") "
			. "\n AND rpt.startrepeat > '$t_datenowSQL'  "
			. "\n AND rpt.startrepeat <= '$eightweeksSQL'  "
			. "\n AND ev.state = 1 "
			. "\n GROUP BY evweeknum "
			. "\n ORDER BY evweeknum ASC";


		$db->setQuery($query, 0, 10);

		$rows = $db->loadObjectList();

		foreach ($rows as & $row)
		{
			$datetime = new DateTime();
			$datetime->setISODate(substr($row->evweeknum, 0, 4),substr($row->evweeknum, 4, 2), 1);
			$row->weekstart = $datetime->format("d/m/Y");
		}
		return $rows;

	}

	/**
	 * get all the native JEvents Icals (i.e. not imported from URL or FILE)
	 *
	 * @return unknown
	 */

	// TODO add more access control e.g. canpublish caneditown etc.

	function getNativeIcalendars()
	{

		$db    = Factory::getDbo();
		$user  = Factory::getUser();
		$query = "SELECT *"
			. "\n FROM #__jevents_icsfile as ical"
			. "\n WHERE ical.icaltype = '2'"
			. "\n AND ical.state = 1"
			. "\n AND ical.access  " . ' IN (' . JEVHelper::getAid($user) . ')';
		$query .= "\n ORDER BY isdefault desc, label asc";

		Factory::getApplication()->triggerEvent('onSelectIcals', array(&$query));

		$db->setQuery($query);
		$rows = $db->loadObjectList("ics_id");

		return $rows;
	}

	function getIcalByIcsid($icsid)
	{

		$db    = Factory::getDbo();
		$user  = Factory::getUser();
		$query = "SELECT *"
			. "\n FROM #__jevents_icsfile as ical"
			/*
			. "\n WHERE ical.catid IN(".$this->accessibleCategoryList().")"
			. "\n AND ical.ics_id = $icsid"
			*/
			. "\n WHERE ical.ics_id = $icsid"
			. "\n AND ical.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user));

		$db->setQuery($query);
		$row = $db->loadObject();

		return $row;
	}

	/**
	 * Get list of module definitions by given name
	 *
	 * @param string $module
	 *
	 * @return array of rows
	 */
	function getModulesByName($module = 'mod_events_latest')
	{

		$db    = Factory::getDbo();
		$query = "select *"
			. "\n from #__modules"
			. "\n where module='" . $module . "'";

		$db->setQuery($query);
		$modules = $db->loadObjectList();

		return $modules;
	}

}
