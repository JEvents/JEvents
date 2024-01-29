<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: dbmodel.php 3575 2012-05-01 14:06:28Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Profiler\Profiler;

// load language constants
JEVHelper::loadLanguage('front');

class JEventsDBModel
{

	var $cfg = null;
	var $datamodel = null;
	var $subquery = false;
	// default multi-day event treatment
	var $daymultiday = 2;

	public function __construct(&$datamodel)
	{

		$this->cfg = JEVConfig::getInstance();

		$this->datamodel = &$datamodel;

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$this->subquery = $params->get("subquery", 0);
	}

	function getCategoryInfo($catids = null, $aid = null)
	{

		$db = Factory::getDbo();
		if (is_null($aid))
		{
			$aid = $this->datamodel->aid;
		}
		if (is_null($catids))
		{
			$catids = $this->datamodel->catids;
		}

		$catids = ArrayHelper::toInteger($catids);
		$catidList = implode(",", $catids);

		$cfg         = JEVConfig::getInstance();
		$sectionname = JEV_COM_COMPONENT;

		static $instances;

		if (!$instances)
		{
			$instances = array();
		}

		// calculate unique index identifier
		$index = $aid . '+' . $catidList;
		$where = null;

		if (!array_key_exists($index, $instances))
		{
			if (count($catids) > 0 && $catidList != "0" && StringHelper::strlen($catidList) != "")
			{
				$where = ' AND c.id IN (' . $catidList . ') ';
			}

			$q_published = Factory::getApplication()->isClient('administrator') ? "\n AND c.published >= 0" : "\n AND c.published = 1";
			$query       = "SELECT c.*"
				. "\n FROM #__categories AS c"
				. "\n WHERE c.access IN (" . $aid . ") "
				. $q_published
				. ' AND c.extension = ' . $db->Quote($sectionname)
				. "\n " . $where;

			$db->setQuery($query);
			$catlist = $db->loadObjectList('id');

			$instances[$index] = $catlist;
		}

		return $instances[$index];

	}

	function getChildCategories($catids = null, $levels = 1, $aid = null)
	{

		$db = Factory::getDbo();
		if (is_null($aid))
		{
			$aid = $this->datamodel->aid;
		}
		if (is_null($catids))
		{
			$catids = $this->datamodel->catids;
		}

		$catidList = implode(",", $catids);

		$cfg         = JEVConfig::getInstance();
		$sectionname = JEV_COM_COMPONENT;

		static $instances;

		if (!$instances)
		{
			$instances = array();
		}

		// calculate unique index identifier
		$index = $aid . '+' . $catidList;
		$where = null;

		if (!array_key_exists($index, $instances))
		{
			if (count($catids) > 0 && $catidList != "0" && StringHelper::strlen($catidList) != "")
			{
				$where = ' AND (p.id IN (' . $catidList . ') ' . ($levels > 1 ? ' OR gp.id IN (' . $catidList . ')' : '') . ($levels > 2 ? ' OR ggp.id IN (' . $catidList . ')' : '') . ')';
			}
			// TODO check if this should also check abncestry based on $levels
			$where .= ' AND p.id IS NOT NULL ';

			$q_published = Factory::getApplication()->isClient('administrator') ? "\n AND c.published >= 0" : "\n AND c.published = 1";
			$query       = "SELECT c.*"
				. "\n FROM #__categories AS c"
				. ' LEFT JOIN #__categories AS p ON p.id=c.parent_id'
				. ($levels > 1 ? ' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id ' : '')
				. ($levels > 2 ? ' LEFT JOIN #__categories AS ggp ON ggp.id=gp.parent_id ' : '')
				. "\n WHERE c.access " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . $aid . ')' : ' <=  ' . $aid)
				. $q_published
				. ' AND c.extension ' . ' = ' . $db->Quote($sectionname)
				. "\n " . $where;


			$db->setQuery($query);
			$catlist = $db->loadObjectList('id');

			$instances[$index] = $catlist;
		}

		return $instances[$index];

	}


	function recentIcalEvents($startdate, $enddate, $limit = 10, $repeatdisplayoptions = 0)
	{

		$user    = Factory::getUser();
		$db      = Factory::getDbo();
		$app     = Factory::getApplication();
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate, "-") === false || is_numeric($startdate))
		{
			$startdate = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$enddate   = JevDate::strftime('%Y-%m-%d 23:59:59', $enddate);
		}

		// Use alternative data source
		$rows        = array();
		$skipJEvents = false;
		$app->triggerEvent('fetchListRecentIcalEvents', array(&$skipJEvents, &$rows, $startdate, $enddate, $limit, $repeatdisplayoptions));
		if ($skipJEvents)
		{
			return $rows;
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup  = false;

		$filterarray = array("published", "justmine", "category", "search", "repeating");

		// If there are extra filters from the module then apply them now
		$reg       = Factory::getConfig();
		$modparams = $reg->get("jev.modparams", false);
		if ($modparams && $modparams->get("extrafilters", false))
		{
			$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
		}

		$filters = jevFilterProcessing::getInstance($filterarray);
		$filters->setWhereJoin($extrawhere, $extrajoin);
		$needsgroup = $filters->needsGroupBy();

		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		// showing NO repeating events - in which case MUST search for events with freq=none
		if ($repeatdisplayoptions == 3)
		{
			$extrawhere[] = "LOWER(rr.freq) = 'none'";
		}
		else if ($repeatdisplayoptions == 4)
		{
			$extrawhere[] = "LOWER(rr.freq) <> 'none'";
		}

		// special case for only showing first repeat (i.e. if the first repeat has passed then show nothing!)
		else if ($repeatdisplayoptions == 2)
		{
			$extrawhere[] = "rpt.startrepeat=(
				SELECT MIN(rpt3.startrepeat) FROM #__jevents_repetition as rpt3 WHERE rpt3.eventid=rpt.eventid
			)";
		}

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		// get the event ids first
		$query = "SELECT  ev.ev_id FROM #__jevents_repetition as rpt"
			. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
			. $extrajoin
			. $catwhere
			. "\n AND ev.created >= '$startdate' AND ev.created <= '$enddate'"
			. $extrawhere
			. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
			. " \n AND icsf.state=1"
			. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
			// published state is now handled by filter
			. "\n GROUP BY ev.ev_id";

		// always in reverse created date order!
		$query .= " ORDER BY ev.created DESC ";

		// This limit will always be enough
		$query .= " LIMIT " . $limit;


		$db = Factory::getDbo();
		$db->setQuery($query);
		$ids = $db->loadColumn();
		array_push($ids, 0);
		$ids = implode(",", $ids);

		$groupby = "\n GROUP BY rpt.rp_id";
		if ($repeatdisplayoptions)
			$groupby = "\n GROUP BY ev.ev_id";

		// This version picks the details from the details table
		// ideally we should check if the event is a repeat but this involves extra queries unfortunately
		$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM #__jevents_repetition as rpt"
			. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
			. $extrajoin
			. $catwhere
			. "\n AND ev.created >= '$startdate' AND ev.created <= '$enddate'"
			. $extrawhere
			. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
			. "  AND icsf.state=1 "
			. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
			. "  AND ev.ev_id IN (" . $ids . ")"
			// published state is now handled by filter
			//. "\n AND ev.state=1"
			. ($needsgroup ? $groupby : "");
		$query .= " ORDER BY ev.created DESC , rpt.startrepeat ASC ";
		//echo str_replace("#__", 'jos_', $query);
		$cache = JEVHelper::getCache(JEV_COM_COMPONENT);
		if (version_compare(JVERSION, '4.0.0', 'ge'))
		{
			$rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
		}
		else
		{
			$rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
		}

		// make sure we have the first repeat in each instance
		// do not use foreach incase time limit plugin removes one of the repeats
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			// no repeating events
			if (strtolower($row->freq()) != "none" && $repeatdisplayoptions == 2)
			{
				continue;
			}
			else if (strtolower($row->freq()) != "none" && $repeatdisplayoptions == 1)
			{
				$repeat = $row->getFirstRepeat();
				if ($repeat->rp_id() != $row->rp_id())
				{
					$row = $this->listEventsById($repeat->rp_id());
					if (is_null($row))
					{
						unset($rows[$i]);
					}
					else
					{
						$rows[$i] = $row;
					}
				}
			}
		}
		$rows = array_values($rows);

		JEventsDBModel::translateEvents($rows);

      //  !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
		$app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		return $rows;

	}

	/**
	 * Fetch recently created events
	 */
	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	function accessibleCategoryList($aid = null, $catids = null, $catidList = null, $allLanguages = false, $checkAccess = true, $includeSubs = 1)
	{

		if (is_null($aid))
		{
			$aid = $this->datamodel->aid;
		}
		if (is_null($catids))
		{
			$catids = $this->datamodel->catids;
		}
		if (is_null($catidList))
		{
			$catidList = $this->datamodel->catidList;
		}

		static $instances;

		$app    = Factory::getApplication();
		$input  = $app->input;

		if (!$instances)
		{
			$instances = array();
		}

		if (is_array($catidList))
		{
			$catidList = ArrayHelper::toInteger($catidList);
			$catidListIndex = implode(", ", $catidList);
		}
		else
		{
			$catidListIndex = $catidList;
		}

		// calculate unique index identifier
		$index = $aid . '+' . $catidListIndex;
		// if catidList = 0 then the result is the same as a blank so slight time saving
		if (is_null($catidList) || $catidListIndex == 0)
		{
			$index = $aid . '+';
		}

        if ($allLanguages)
        {
         //   $index .= "*";
        }
        else
        {
           // $index .= Factory::getLanguage()->getTag();
        }
		$db = Factory::getDbo();

		$where = "";

		if (!array_key_exists($index, $instances))
		{
			static $allcats;
			if (!isset($allcats))
			{
				jimport("joomla.application.categories");
				$allcats = Categories::getInstance("jevents");
				// prepopulate the list internally
				$allcats->get('root');
			}

			// If the menu of module has been constrained then we need to take account of that here!
			$catids = JEVHelper::forceIntegerArray($catids, false);
			$mmcatids    = $this->datamodel->mmcatids;
			$mmcatidList = $this->datamodel->mmcatidList;

			if (isset($this->datamodel->mmcatids) && count($this->datamodel->mmcatids) > 0)
			{
				$this->datamodel->mmcatids = JEVHelper::forceIntegerArray($this->datamodel->mmcatids, false);

				// Take account of inclusion of subcategories here!
				if ($includeSubs && $this->cfg->get("include_subcats", 1))
				{
					$mmcatids = array();
					$mmcats   = Categories::getInstance("jevents");
					foreach ($this->datamodel->mmcatids as $mmcatid)
					{
						$mmcat      = $mmcats->get($mmcatid);
						$mmcatids[] = $mmcatid;
						if ($mmcat && $mmcat->hasChildren())
						{
							$kids = $mmcat->getChildren(true);
							foreach ($kids as $kid)
							{
								$mmcatids[] = $kid->id;
							}
						}
					}

					$specificCatids = array();
					foreach ($catids as $specificCatid)
					{
						$specificCat      = $mmcats->get($specificCatid);
						$specificCatids[] = $specificCatid;
						if ($specificCat && $specificCat->hasChildren())
						{
							$kids = $specificCat->getChildren(true);
							foreach ($kids as $kid)
							{
								$specificCatids[] = $kid->id;
							}
						}
					}

					$catids   = array_intersect($mmcatids, $specificCatids);

				}
				else
				{
					$catids   = array_intersect($mmcatids, $catids);
				}
				$catids   = array_values($catids);
				$catids[] = -1;
				// hardening!
				$catidList = JEVHelper::forceIntegerArray($catids, true);
			}

			if (is_null($catidList))
			{
				$catidList = "";
			}

			$catids   = is_array($catidList) ? $catidList : explode(",", $catidList);
			$catwhere = array();
			$hascatid = false;
			foreach ($catids as $catid)
			{
				// hardening
				$catid = intval($catid);
				if ($catid > 0)
				{
					$hascatid = true;
					$cat      = $allcats->get($catid);
					if ($cat)
					{
						//$catwhere[] = "(c.lft<=" . $cat->rgt . " AND c.rgt>=" . $cat->lft." )";
						$catwhere[] = "(c.lft>=" . $cat->lft . " AND c.rgt<=" . $cat->rgt . " )";
					}
				}
			}

			// trap siguation where we have menu contraint but URL/filter is trying to get categories outside this!
			if (count($catids) > 0 && !$hascatid && isset($mmcatids) && count($mmcatids) > 0)
			{
				$hascatid = true;
			}
			if (count($catwhere) > 0)
			{
				$where = "AND (" . implode(" OR ", $catwhere) . ")";
			}
			// do we have a complete set of inaccessible or unpublished categories - if so then we must block all events
			if ($hascatid && count($catwhere) == 0)
			{
				$where = " AND 0 ";
			}
			// The menu or module may have specified categories but NOT their children
			if (isset($this->datamodel->mmcatids) && count($this->datamodel->mmcatids) > 0 && !$this->cfg->get("include_subcats", 1))
			{
				$where .= " AND c.id in (" . $mmcatidList . ")";
			}
			else
			{
				$reg       = Factory::getConfig();
				$modparams = $reg->get("jev.modparams", false);
				if ($modparams && isset($this->datamodel->mmcatids) && count($this->datamodel->mmcatids) > 0 && !$modparams->get("include_subcats", 1))
				{
					$where .= " AND c.id in (" . $mmcatidList . ")";
				}
			}

			$q_published = $app->isClient('administrator') ? "\n AND c.published >= 0" : "\n AND c.published = 1";
			$jevtask     = $input->getString("jevtask", "");
			$isedit      = false;
			// not only for edit pages but for all backend changes we ignore the language filter on categories
			if (strpos($jevtask, "icalevent.edit") !== false || strpos($jevtask, "icalrepeat.edit") !== false || $app->isClient('administrator') || $allLanguages)
			{
				$isedit = true;
			}

			$whereQuery = ($checkAccess ? "c.access  " . ' IN (' . $aid . ')' : " 1 ")
				. $q_published
				// language filter only applies when not editing
				. ($isedit ? "" : "\n  AND c.language in (" . $db->quote(Factory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')')
				. "\n AND c.extension = 'com_jevents'"
				. "\n " . $where;

			$query = $db->getQuery(true);
			$query->select('c.id')
				->from('#__categories AS c')
				->where($whereQuery)
				->order('c.lft asc');

			$db->setQuery($query);

			$catlist = $db->loadColumn();

			$instances[$index] = implode(',', array_merge(array(-1), $catlist));

			$app->triggerEvent('onGetAccessibleCategories', array(& $instances[$index]));

			if (empty($instances[$index]))
			{
				$instances[$index] = '-1';
			}
		}

		return $instances[$index];

	}

	/**
	 * Fetch recently modified events
	 */
	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	function listEventsById($rpid, $includeUnpublished = 0, $jevtype = "icaldb", $checkAccess = true)
	{

		// special case where the event is outside of JEvents - handled by a plugin
		if ($rpid < 0)
		{
			$rows       = array();
           // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
            Factory::getApplication()->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
          //  !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

			if (count($rows) == 1)
			{
				return $rows[0];
			}

			return array();
		}

		$user            = Factory::getUser();
		$db              = Factory::getDbo();
		$frontendPublish = JEVHelper::isEventPublisher();

		if ($jevtype == "icaldb")
		{
			// process the new plugins
			// get extra data and conditionality from plugins
			$extrafields = "";  // must have comma prefix
			$extratables = "";  // must have comma prefix
			$extrawhere  = array();
			$extrajoin   = array();

			if ($includeUnpublished)
			{
				$filterarray = array("justmine", "search", "repeating");
			}
			else
			{
				$filterarray = array("published", "justmine", "search", "repeating");
			}

			// If there are extra filters from the module then apply them now
			$reg       = Factory::getConfig();
			$modparams = $reg->get("jev.modparams", false);
			if ($modparams && $modparams->get("extrafilters", false))
			{
				$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
			}

			$filters = jevFilterProcessing::getInstance($filterarray);
			$filters->setWhereJoin($extrawhere, $extrajoin);
			$needsgroup = $filters->needsGroupBy();

			Factory::getApplication()->triggerEvent('onListEventsById', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin));

			$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList(null, null, null, false, $checkAccess) . ")";
			$params   = ComponentHelper::getParams("com_jevents");
			if ($params->get("multicategory", 0))
			{
				$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
				$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
				$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
				if ($checkAccess)
				{
					// accessibleCategoryList handles access checks on category
					//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
				}
				$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList(null, null, null, false, $checkAccess) . ")";
				$needsgroup   = true;
				$catwhere     = "\n WHERE 1 ";
			}

			$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
			$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

			$query = "SELECT ev.*, ev.state as published, rpt.*, rr.*, det.* $extrafields, ev.rawdata as evrawdata, ev.created as created "
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. " \n , ev.state as state "
				. "\n FROM (#__jevents_vevent as ev $extratables)"
				. "\n INNER JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
				. $extrajoin
				. $catwhere
				. ($checkAccess ? "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")" : "")
				. ($includeUnpublished ? "" : " AND icsf.state=1")
				. ($checkAccess ? " AND icsf.access IN (" . JEVHelper::getAid($user) . ")" : "")
				. $extrawhere
				. "\n AND rpt.rp_id = '$rpid'";
			$query .= "\n GROUP BY rpt.rp_id";
		}
		else
		{
			die("invalid jevtype in listEventsById - more changes needed");
		}

		$db->setQuery($query);
		//echo (string) $db->getQuery();
		$rows = $db->loadObjectList();

		// iCal agid uses GUID or UUID as identifier
		if ($rows)
		{
			// set multi-category and check access levels
			// done in the above query
			/*
			  if (!$this->setMultiCategory($rows[0],$accessibleCategories)){
			  return null;
			  }
			 *
			 */
			if (strtolower($jevtype) == "icaldb")
			{
				$row = new jIcalEventRepeat($rows[0]);
			}
			else if (strtolower($jevtype) == "jevent")
			{
				$row = new jEventCal($rows[0]);
			}

			JEventsDBModel::translateEvents($row);

		}
		else
		{
            //echo (string) $db->getQuery();
            //exit();

			$row = null;
		}

		return $row;

	}

	/**
	 * Fetch recently created events
	 */
	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	// TODO fix multi-day event handling!
	public static function translateEvents(&$icalrows)
	{

		static $translationCache = array();

		$is_array = true;
		if (!is_array($icalrows))
		{
			$is_array = false;
			$icalrows = array($icalrows);
		}
		$icalcount = count($icalrows);
		// Do we need to translate this data
		$languages      = LanguageHelper::getLanguages('lang_code');
		$translationids = array();
		if (count($languages) > 1)
		{
			$lang    = Factory::getLanguage();
			$langtag = $lang->getTag();
			for ($i = 0; $i < $icalcount; $i++)
			{
				// Check if it's null - Extra catch to avoid issues.
				if (!$icalrows[$i]->_evdet_id)
				{
					continue;
				}
				$translationids[] = $icalrows[$i]->_evdet_id;
			}
		}
		if (count($translationids) > 0)
		{
			$db             = Factory::getDbo();

			if (count($translationids) > 0)
			{
				$translationidsList = array();

				foreach ($translationids as $translationid)
				{
					if (array_key_exists($translationid, $translationCache))
					{
						$translations[$translationid] = $translationCache[$translationid];
					}
					else
					{
						$translationidsList[] = $translationid;
					}
				}

				if (count($translationidsList))
				{
					$translationidsListString = implode(",", $translationidsList);

					$db->setQuery("SELECT *, summary as title, description as content FROM #__jevents_translation WHERE evdet_id IN(" . $translationidsListString . ") AND language=" . $db->quote($langtag));
					$newTranslations = $db->loadObjectList("evdet_id");

					foreach ($translationidsList as $translationidsListItem)
					{
						if (isset($newTranslations[$translationidsListItem]))
						{
							$translationCache[$translationidsListItem] = $newTranslations[$translationidsListItem];
						}
						else
						{
							$translationCache[$translationidsListItem] = false;
						}
						$translations[$translationidsListItem] = $translationCache[$translationidsListItem];
					}
				}

			}
			else
			{
				$translations = false;
			}

			if ($translations)
			{
				for ($i = 0; $i < $icalcount; $i++)
				{
					if (array_key_exists($icalrows[$i]->_evdet_id, $translations) && is_object($translations[$icalrows[$i]->_evdet_id]))
					{
						foreach (get_object_vars($translations[$icalrows[$i]->_evdet_id]) as $k => $v)
						{
							$k = "_" . $k;
							if ($v != "" && isset($icalrows[$i]->$k))
							{
								// hard coded workaround for translated locations overwritng usefuldata
								if ($k == "_location" && is_numeric($v))
								{
									continue;
								}
								$icalrows[$i]->$k = $v;
							}
						}
					}
				}
			}
		}
		if (!$is_array)
		{
			$icalrows = $icalrows[0];
		}

	}

	/* Special version for Latest events module */


	function recentlyModifiedIcalEvents($startdate, $enddate, $limit = 10, $repeatdisplayoptions = 0)
	{

		$user    = Factory::getUser();
		$app     = Factory::getApplication();
		$db      = Factory::getDbo();
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate, "-") === false || is_numeric($startdate))
		{
			$startdate = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$enddate   = JevDate::strftime('%Y-%m-%d 23:59:59', $enddate);
		}

		// Use alternative data source
		$rows        = array();
		$skipJEvents = false;
		$app->triggerEvent('fetchListRecentlyModifiedIcalEvents', array(&$skipJEvents, &$rows, $startdate, $enddate, $limit, $repeatdisplayoptions));
		if ($skipJEvents)
		{
			return $rows;
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup  = false;

		$filterarray = array("published", "justmine", "category", "search", "repeating");

		// If there are extra filters from the module then apply them now
		$reg       = Factory::getConfig();
		$modparams = $reg->get("jev.modparams", false);
		if ($modparams && $modparams->get("extrafilters", false))
		{
			$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
		}

		$filters = jevFilterProcessing::getInstance($filterarray);
		$filters->setWhereJoin($extrawhere, $extrajoin);
		$needsgroup = $filters->needsGroupBy();

		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		// showing NO repeating events - in which case MUST search for events with freq=none
		if ($repeatdisplayoptions == 3)
		{
			$extrawhere[] = "LOWER(rr.freq) = 'none'";
		}
		else if ($repeatdisplayoptions == 4)
		{
			$extrawhere[] = "LOWER(rr.freq) <> 'none'";
		}
		// special case for only showing first repeat (i.e. if the first repeat has passed then show nothing!)
		else if ($repeatdisplayoptions == 2)
		{
			$extrawhere[] = "rpt.startrepeat=(
				SELECT MIN(rpt3.startrepeat) FROM #__jevents_repetition as rpt3 WHERE rpt3.eventid=rpt.eventid
			)";
		}

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		// get the event ids first
		$query = "SELECT  det.evdet_id FROM #__jevents_repetition as rpt"
			. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
			. $extrajoin
			. $catwhere
			. "\n AND det.modified >= '$startdate' AND det.modified <= '$enddate'"
			. $extrawhere
			. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
			. " \n AND icsf.state=1"
			. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
			// published state is now handled by filter
			. "\n GROUP BY det.evdet_id";
		// always in reverse modification date order!
		$query .= " ORDER BY det.modified DESC ";

		// This limit will always be enough
		$query .= " LIMIT " . $limit;


		$db = Factory::getDbo();
		$db->setQuery($query);
//echo "<pre>".$db->getQuery()."</pre>";exit();
		$detids = $db->loadColumn();
		array_push($detids, 0);
		$detids = implode(",", $detids);

		$groupby = "\n GROUP BY det.evdet_id";

		// This version picks the details from the details table
		// ideally we should check if the event is a repeat but this involves extra queries unfortunately
		$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM #__jevents_repetition as rpt"
			. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
			. $extrajoin
			. $catwhere
			. "\n AND det.modified >= '$startdate' AND det.modified <= '$enddate'"
			. $extrawhere
			. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
			. "  AND icsf.state=1 "
			. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
			. "  AND det.evdet_id IN (" . $detids . ")"
			// published state is now handled by filter
			//. "\n AND ev.state=1"
			. $groupby;
		$query .= " ORDER BY det.modified DESC , rpt.startrepeat ASC ";
		//echo str_replace("#__", 'jos_', $query);
		$cache = JEVHelper::getCache(JEV_COM_COMPONENT);
		if (version_compare(JVERSION, '4.0.0', 'ge'))
		{
			$rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
		}
		else
		{
			$rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
		}

		// make sure we have the first repeat in each instance
		// do not use foreach incase time limit plugin removes one of the repeats
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			// no repeating events
			if (strtolower($row->freq()) != "none" && $repeatdisplayoptions == 2)
			{
				continue;
			}
			if (strtolower($row->freq()) != "none" && $repeatdisplayoptions == 1)
			{
				$repeat = $row->getFirstRepeat();
				if ($repeat->rp_id() != $row->rp_id())
				{
					$row = $this->listEventsById($repeat->rp_id());
					if (is_null($row))
					{
						unset($rows[$i]);
					}
					else
					{
						$rows[$i] = $row;
					}
				}
			}
		}
		$rows = array_values($rows);

		JEventsDBModel::translateEvents($rows);

        //!JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
        $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		return $rows;

	}


	function popularIcalEvents($startdate, $enddate, $limit = 10, $repeatdisplayoptions = 0, $multidayTreatment = 0)
	{

		$user    = Factory::getUser();
		$app     = Factory::getApplication();
		$db      = Factory::getDbo();
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate, "-") === false || is_numeric($startdate))
		{
			$startdate = date('Y-m-d 00:00:00', $startdate);
			$enddate   = date('Y-m-d 23:59:59', $enddate);
		}

		// Use alternative data source
		$rows        = array();
		$skipJEvents = false;
		$app->triggerEvent('fetchListPopularIcalEvents', array(&$skipJEvents, &$rows, $startdate, $enddate, $limit, $repeatdisplayoptions));
		if ($skipJEvents)
		{
			return $rows;
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup  = false;

		$filterarray = array("published", "justmine", "category", "search", "repeating");

		// If there are extra filters from the module then apply them now
		$reg       = Factory::getConfig();
		$modparams = $reg->get("jev.modparams", false);
		if ($modparams && $modparams->get("extrafilters", false))
		{
			$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
		}

		$filters = jevFilterProcessing::getInstance($filterarray);
		$filters->setWhereJoin($extrawhere, $extrajoin);
		$needsgroup = $filters->needsGroupBy();

		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		// showing NO repeating events - in which case MUST search for events with freq=none
		if ($repeatdisplayoptions == 3)
		{
			$extrawhere[] = "LOWER(rr.freq) = 'none'";
		}
		else if ($repeatdisplayoptions == 4)
		{
			$extrawhere[] = "LOWER(rr.freq) <> 'none'";
		}
		// special case for only showing first repeat (i.e. if the first repeat has passed then show nothing!)
		else if ($repeatdisplayoptions == 2)
		{
			$extrawhere[] = "rpt.startrepeat=(
				SELECT MIN(rpt3.startrepeat) FROM #__jevents_repetition as rpt3 WHERE rpt3.eventid=rpt.eventid
			)";
		}

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		// get the event ids first - split into 2 queries to pick up the ones after now and the ones before
		$t_datenow    = JEVHelper::getNow();
		$t_datenowSQL = $t_datenow->toSql();

		// multiday condition
		if ($multidayTreatment == 3)
		{
			// We only show events once regardless of multiday setting of event so we allow them all through here!
			$multiday  = "";
			$multiday2 = "";
			$multiday3 = "";
		}
		else if ($multidayTreatment == 2)
		{
			// We only show events on their first day only regardless of multiday setting of event so we allow them all through here!
			$multiday  = "";
			$multiday2 = "";
			$multiday3 = "";
		}
		else if ($multidayTreatment == 1)
		{
			// We only show events on all days regardless of multiday setting of event so we allow them all through here!
			$multiday  = "";
			$multiday2 = "";
			$multiday3 = "";
		}
		else
		{
			// We only show events on their first day if they are not to be shown on multiple days so also add this condition
			// i.e. the event settings are used
			// This is the true version of these conditions
			//$multiday = "\n AND ((rpt.startrepeat >= '$startdate' AND det.multiday=0) OR  det.multiday=1)";
			//$multiday2 = "\n AND ((rpt.startrepeat <= '$startdate' AND det.multiday=0) OR  det.multiday=1)";
			// BUT this is logically equivalent and appears much faster  on some databases
			$multiday  = "\n AND (rpt.startrepeat >= '$startdate' OR  det.multiday=1)";
			$multiday2 = "\n AND (rpt.startrepeat <= '$startdate'OR  det.multiday=1)";
			$multiday3 = "AND det.multiday=1";
		}

		// TODO fix this
		// Disable multiday checking
		$multiday  = "";
		$multiday2 = "";
		$multiday3 = "";

		$daterange  = "\n AND rpt.endrepeat >= '$t_datenowSQL' AND rpt.startrepeat <= '$enddate'";
		$multidate  = "\n AND ((rpt.startrepeat >= '$t_datenowSQL' AND det.multiday=0) OR  det.multiday=1)";

		$daterange2 = "\n rptx.endrepeat >= '$t_datenowSQL' AND rptx.startrepeat <= '$enddate'";
		$multidate2 = "\n AND ((rptx.startrepeat >= '$t_datenowSQL' AND det2.multiday=0) OR  det2.multiday=1)";
		$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
			. "\n INNER JOIN #__jevents_vevdetail as det2  ON det2.evdet_id = rptx.eventdetail_id"
			. "\n WHERE  $daterange2 "
			. $multidate2
			. ")";

		// get the event ids first
		$query = "SELECT  ev.ev_id FROM #__jevents_repetition as rpt"
			. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
			. $extrajoin
			. $catwhere
			// New equivalent but simpler test
			. ($this->subquery ? $daterange2 : $daterange)
			// We only show events on their first day if they are not to be shown on multiple days so also add this condition
			. ($this->subquery ? "" : $multidate)
			. $extrawhere
			. $multiday
			. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
			. " \n AND icsf.state=1"
			. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
			// published state is now handled by filter
			. "\n AND rpt.startrepeat=(SELECT MIN(rptq.startrepeat) FROM #__jevents_repetition as rptq WHERE rptq.eventid=ev.ev_id AND rptq.startrepeat >= '$t_datenowSQL' AND rptq.startrepeat <= '$enddate' $multiday3)"
			. "\n GROUP BY ev.ev_id";

		// always in reverse hits  order!
		$query .= " ORDER BY det.hits DESC ";

		// This limit will always be enough
		$query .= " LIMIT " . $limit;


		$db = Factory::getDbo();
		$db->setQuery($query);
		$ids = $db->loadColumn();
		array_push($ids, 0);
		$ids = implode(",", $ids);

		$groupby = "\n GROUP BY rpt.rp_id";
		if ($repeatdisplayoptions)
			$groupby = "\n GROUP BY ev.ev_id";

		$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'";

		$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$enddate'";
		$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
			. "\n WHERE  $daterange2 )";

		// This version picks the details from the details table
		// ideally we should check if the event is a repeat but this involves extra queries unfortunately
		$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM #__jevents_repetition as rpt"
			. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
			. $extrajoin
			. $catwhere
			// New equivalent but simpler test
			. ($this->subquery ? $daterange2 : $daterange)
			. $extrawhere
			. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
			. "  AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")"
			. "  AND ev.ev_id IN (" . $ids . ")"
			// published state is now handled by filter
			. ($needsgroup ? $groupby : "");
		$query .= " ORDER BY det.hits DESC ";
		$query .= " LIMIT " . $limit;

		$cache = JEVHelper::getCache(JEV_COM_COMPONENT);
		if (version_compare(JVERSION, '4.0.0', 'ge'))
		{
			$rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
		}
		else
		{
			$rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
		}

       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
        $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
      //  !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		return $rows;

	}

	function listLatestIcalEvents($startdate, $enddate, $limit = 10, $repeatdisplayoptions = 0, $multidayTreatment = 0, $includeSubs = 1)
	{
		//list($usec, $sec) = explode(" ", microtime());
		//$starttime = (float) $usec + (float) $sec;

		$debugLatest = false;
		if ($debugLatest)
		{
			echo "listLatestIcalEvents<br>";
		}

		$app    = Factory::getApplication();
		$input  = $app->input;
		$userid = $input->get('jev_userid', "0");

		if ($userid == "0")
		{
			$user = Factory::getUser();
		}
		else
		{
			$user = JEVHelper::getUser($userid);
		}
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate, "-") === false || is_numeric($startdate))
		{
			$startdate = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$enddate   = JevDate::strftime('%Y-%m-%d 23:59:59', $enddate);
		}

		// Use alternative data source
		$rows        = array();
		$skipJEvents = false;
		$app->triggerEvent('fetchListLatestIcalEvents', array(&$skipJEvents, &$rows, $startdate, $enddate, $limit, $repeatdisplayoptions, $multidayTreatment));
		if ($skipJEvents)
		{
			return $rows;
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$rptwhere    = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup  = false;

		$reg =  Factory::getConfig();
		$modparams = $reg->get("jev.modparams", false);

		if ($modparams && $modparams->get('ignorecatfilter',0) == 2 )
		{
			$filterarray = array("published", "justmine", "search", "repeating");
		}
		else
		{
			$filterarray = array("published", "justmine", "category", "search", "repeating");
		}

		// If there are extra filters from the module then apply them now
		$reg       = Factory::getConfig();
		$modparams = $reg->get("jev.modparams", false);
		if ($modparams && $modparams->get("extrafilters", false))
		{
			$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
		}

		$filters = jevFilterProcessing::getInstance($filterarray);
		$filters->setWhereJoin($extrawhere, $extrajoin);
		$needsgroup = $filters->needsGroupBy();

		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup, & $rptwhere));

		//list ($usec, $sec) = explode(" ", microtime());
		//$time_end = (float) $usec + (float) $sec;
		//echo  "post onListIcalEvents= ".round($time_end - $starttime, 4)."<br/>";

		// showing NO repeating events - in which case MUST search for events with freq=none
		if ($repeatdisplayoptions == 3)
		{
			$extrawhere[] = "LOWER(rr.freq) = 'none'";
		}
		else if ($repeatdisplayoptions == 4)
		{
			$extrawhere[] = "LOWER(rr.freq) <> 'none'";
		}

		// special case for only showing first repeat (i.e. if the first repeat has passed then show nothing!)
		else if ($repeatdisplayoptions == 2)
		{
			$extrawhere[] = "rpt.startrepeat=(
				SELECT MIN(rpt3.startrepeat) FROM #__jevents_repetition as rpt3 WHERE rpt3.eventid=rpt.eventid
			)";
		}

		// What if join multiplies the rows?
		// Useful MySQL link http://forums.mysql.com/read.php?10,228378,228492#msg-228492
		// concat with group
		// http://www.mysqlperformanceblog.com/2006/09/04/group_concat-useful-group-by-extension/
		// did any of the plugins adjust the range of dateds allowed eg. timelimit plugin - is so then we need to use this information otherwise we  get problems
		$regex = "#(rpt.endrepeat>='[0-9:\- ]*' AND rpt.startrepeat<='[0-9:\- ]*')#";
		foreach ($extrawhere as $exwhere)
		{
			if (preg_match($regex, $exwhere))
			{
				$rptwhere[] = str_replace("rpt.", "rptq.", $exwhere);
			}
		}
		$rptwhere = (count($rptwhere) ? ' AND ' . implode(' AND ', $rptwhere) : '');
		$rptwhere = str_replace("rpt2.", "rptq.", $rptwhere);
		$rptwherex = str_replace("rptq.", "rptx.", $rptwhere);

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList(JEVHelper::getAid($user), null, null, false, true, $includeSubs) . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		$cathaving = "";
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";

			if ($modparams->get("categoryAllOrAny", 0) == 1 && $modparams->get("include_subcats", 0) == 0)
			{
				$catidnew = $modparams->get("catidnew", array());
				if ($catidnew && is_array($catidnew))
				{
					$extrafields .= ", COUNT(DISTINCT catmapcat.id ) as catidcount";
					$cathaving = " HAVING catidcount = " . count($catidnew);
				}
			}

			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access  IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}


		// Are we responding to pagination request  - if so ignore the repeats already shown
		$ignoreRepeatIds = $this->getIgnoreRepeatIds();
		if ($ignoreRepeatIds)
		{
			$extrawhere[] = $ignoreRepeatIds;
		}

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		// get the event ids first - split into 2 queries to pick up the ones after now and the ones before
		$t_datenow    = JEVHelper::getNow();
		$t_datenowSQL = $t_datenow->toSql();

		// multiday condition
		if ($multidayTreatment == 3)
		{
			// We only show events once regardless of multiday setting of event so we allow them all through here!
			$multiday  = "";
			$multiday2 = "";
			$multiday3 = "";
		}
		else if ($multidayTreatment == 2)
		{
			// We only show events on their first day only regardless of multiday setting of event so we allow them all through here!
			// BUT NOT events where the startrepeat is on a date previous to the start date
			$multiday  = "\n AND rpt.startrepeat >= '$startdate' ";
			$multiday2 = "\n AND rpt.startrepeat >= '$startdate' ";
			$multiday3 = "";
		}
		else if ($multidayTreatment == 1)
		{
			// We only show events on all days regardless of multiday setting of event so we allow them all through here!
			$multiday  = "";
			$multiday2 = "";
			$multiday3 = "";
		}
		else
		{
			// We only show events on their first day if they are not to be shown on multiple days so also add this condition
			// i.e. the event settings are used
			// This is the true version of these conditions
			//$multiday = "\n AND ((rpt.startrepeat >= '$startdate' AND det.multiday=0) OR  det.multiday=1)";
			//$multiday2 = "\n AND ((rpt.startrepeat <= '$startdate' AND det.multiday=0) OR  det.multiday=1)";
			// BUT this is logically equivalent and appears much faster  on some databases
			$multiday  = "\n AND (rpt.startrepeat >= '$startdate' OR  det.multiday=1)";
			$multiday2 = "\n AND (rpt.startrepeat <= '$startdate' OR  det.multiday=1)";
			$multiday3 = "AND det.multiday=1";
		}

		if ($repeatdisplayoptions)
		{
			if ($debugLatest)
			{
				echo "repeatdisplayoptions = true<br>";
			}
			// Display a repeating event ONCE we group by event id selecting the most appropriate repeat for each one
			// Find the ones after now (only if not past only)
			$rows1 = array();
			if ($enddate >= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) != 1)
			{
				$daterange  = "\n AND rpt.endrepeat >= '$t_datenowSQL' AND rpt.startrepeat <= '$enddate'";

				$daterange2 = "\n rptx.endrepeat >= '$t_datenowSQL' AND rptx.startrepeat <= '$enddate'";
				$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
					. "\n WHERE  $daterange2 )";

				$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
					. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
					. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
					. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
					. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
					. "\n FROM #__jevents_repetition as rpt"
					. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n INNER JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
					. $extrajoin
					. $catwhere
					// New equivalent but simpler test
					// For large sites sub-query is a LOT faster
					. ($this->subquery ? $daterange2 : $daterange)
					. $multiday
					//xxxxx
					. $extrawhere
					. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
					. "  AND icsf.state=1 "
					. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
					// published state is now handled by filter
					. "\n AND rpt.startrepeat=(
				SELECT MIN(rptq.startrepeat) FROM #__jevents_repetition as rptq
				WHERE rptq.eventid=ev.ev_id
				AND  (
					(rptq.startrepeat >= '$t_datenowSQL' AND rptq.startrepeat <= '$enddate')
					OR (rptq.startrepeat <= '$t_datenowSQL' AND rptq.endrepeat  > '$t_datenowSQL'  $multiday3)
					)
				$rptwhere
			) 
			GROUP BY ev.ev_id
			$cathaving
			ORDER BY rpt.startrepeat";

				// This limit will always be enough
				$query .= " LIMIT " . $limit;

				$cache = JEVHelper::getCache(JEV_COM_COMPONENT);
				if (version_compare(JVERSION, '4.0.0', 'ge'))
				{
					$rows1 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
				}
				else
				{
					$rows1 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
				}

				if ($debugLatest)
				{
					echo "rows 1<br>";
					echo $query . "<br>";
					echo count($rows1) . "<br>";
				}
			}

			// Before now (only if not past only == future events)
			$rows2 = array();
			if ($startdate <= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) < 2)
			{
				$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

				$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
				$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
					. "\n WHERE  $daterange2 )";

				// note the order is the ones nearest today
				$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
					. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
					. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
					. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
					. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
					. "\n FROM #__jevents_repetition as rpt"
					. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
					. $extrajoin
					. $catwhere
					// New equivalent but simpler test
					. ($this->subquery ? $daterange2 : $daterange)
					. $multiday
					//xxxx
					. $extrawhere
					. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
					. "  AND icsf.state=1 "
					. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
					// published state is now handled by filter
					. "\n AND rpt.startrepeat=(
					SELECT MAX(rptq.startrepeat) FROM #__jevents_repetition as rptq
					 WHERE rptq.eventid=ev.ev_id
					AND rptq.startrepeat <= '$t_datenowSQL' AND rptq.startrepeat >= '$startdate'
					$rptwhere
				)
				GROUP BY ev.ev_id
				$cathaving
				ORDER BY rpt.startrepeat desc";

				// This limit will always be enough
				$query .= " LIMIT " . $limit;

				$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

				if (version_compare(JVERSION, '4.0.0', 'ge'))
				{
					$rows2 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
				}
				else
				{
					$rows2 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
				}

				if ($debugLatest)
				{
					echo "rows 2<br>";
					echo $query . "<br>";
					echo count($rows2) . "<br>";
				}
			}

			$rows3 = array();
			if ($multidayTreatment != 2 && $multidayTreatment != 3)
			{
				$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

				$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
				$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
					. "\n WHERE  $daterange2 )";

				// Mutli day events
				$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
					. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
					. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
					. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
					. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
					. "\n FROM #__jevents_repetition as rpt"
					. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
					. $extrajoin
					. $catwhere
					// Must be starting before NOW otherwise would already be picked up
					. ($this->subquery ? $daterange2 : $daterange)
					. $multiday2
					//xxxx
					. $extrawhere
					. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
					. "  AND icsf.state=1 "
					. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
					// published state is now handled by filter
					// This is the correct approach but can be slow
					/*
					  . "\n AND rpt.startrepeat=(
					  SELECT MAX(rptq.startrepeat) FROM #__jevents_repetition as rptq
					  WHERE rptq.eventid=ev.ev_id
					  AND rptq.startrepeat <= '$t_datenowSQL' AND rptq.endrepeat >= '$t_datenowSQL'
					  $rptwherex
					  )"
					 */
					// This is the alternative - it could produce unexpected results if you have overlapping repeats over 'now' but this is  low risk
					. "\n AND rpt.startrepeat <= '$t_datenowSQL' AND rpt.endrepeat >= '$t_datenowSQL'"
					. " \n GROUP BY ev.ev_id
						$cathaving
						ORDER BY rpt.startrepeat";

				// This limit will always be enough
				$query .= " LIMIT " . $limit;

				$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

				if (version_compare(JVERSION, '4.0.0', 'ge'))
				{
					$rows3 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
				}
				else
				{
					$rows3 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
				}

				if ($debugLatest)
				{
					echo "rows 3<br>";
					echo $query . "<br>";
					echo count($rows3) . "<br>";
				}
			}

			// ensure specific event is not used more than once
			$events = array();
			$rows   = array();
			// future events
			foreach ($rows1 as $val)
			{
				if (!in_array($val->ev_id(), $events))
				{
					if ($debugLatest)
					{
						echo $val->_startrepeat . " " . $val->_endrepeat . " " . $val->ev_id() . " " . $val->title() . "<br/>";
					}
					$events[] = $val->ev_id();
					$rows[]   = $val;
				}
			}
			// straddling multi-day event
			foreach ($rows3 as $val)
			{
				if (!in_array($val->ev_id(), $events))
				{
					if ($debugLatest)
					{
						echo $val->_startrepeat . " " . $val->_endrepeat . " " . $val->ev_id() . " " . $val->title() . "<br/>";
					}
					$events[] = $val->ev_id();
					$rows[]   = $val;
				}
			}
			// past events
			foreach ($rows2 as $val)
			{
				if (!in_array($val->ev_id(), $events))
				{
					if ($debugLatest)
					{
						echo $val->_startrepeat . " " . $val->_endrepeat . " " . $val->ev_id() . " " . $val->title() . "<br/>";
					}
					$events[] = $val->ev_id();
					$rows[]   = $val;
				}
			}
			if ($debugLatest)
			{
				echo "count rows " . count($rows1) . " " . count($rows2) . " " . count($rows3) . " " . count($rows) . "<br/>";
			}
			unset($rows1);
			unset($rows2);
			unset($rows3);
		}
		else
		{
			if ($debugLatest)
			{
				echo "repeatdisplayoptions = true<br>";
			}
			//list ($usec, $sec) = explode(" ", microtime());
			//$time_end = (float) $usec + (float) $sec;
			//echo  "pre version= ".round($time_end - $starttime, 4)."<br/>";

			$version = $input->getCmd("version", "old");

			if ($version == "new")
			{
				// new approach that gets the IDs first
				// Display a repeating event for EACH repeat
				// We therefore fetch 3 sets of possible repeats if necessary i.e. not over the limit!
				// Find the ones after now (only if not past only)
				$ids1 = array();
				if ($enddate >= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) != 1)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$t_datenowSQL' AND rpt.startrepeat <= '$enddate'";

					$daterange2 = "\n rptx.endrepeat >= '$t_datenowSQL' AND rptx.startrepeat <= '$enddate'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					$query = "SELECT DISTINCT rpt.rp_id"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. $extrajoin
						. $catwhere
						// New equivalent but simpler test
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						. "\n GROUP BY rpt.rp_id
						$cathaving
						ORDER BY rpt.startrepeat ASC";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					// TODO cache this!
					$db = Factory::getDbo();
					$db->setQuery($query);
					$ids1 = $db->loadColumn();
				}

				// Before now (only if not past only == future events)
				$ids2 = array();
				if ($startdate <= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) < 2)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

					$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					// note the order is the ones nearest today
					$query = "SELECT  DISTINCT rpt.rp_id"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						// New equivalent but simpler test
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						. "\n GROUP BY rpt.rp_id
							$cathaving
							ORDER BY rpt.startrepeat desc";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					// TODO cache this!
					$db = Factory::getDbo();
					$db->setQuery($query);
					$ids2 = $db->loadColumn();
				}

				$ids3 = array();
				if ($multidayTreatment != 2 && $multidayTreatment != 3)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

					$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					// Mutli day events
					$query = "SELECT  DISTINCT rpt.rp_id"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						// Must be starting before NOW otherwise would already be picked up
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday2
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						. "\n GROUP BY rpt.rp_id
							$cathaving
							ORDER BY rpt.startrepeat asc";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					// TODO cache this!
					$db = Factory::getDbo();
					$db->setQuery($query);
					$ids3 = $db->loadColumn();
				}

				//list ($usec, $sec) = explode(" ", microtime());
				//$time_end = (float) $usec + (float) $sec;
				//echo  "after ids = ".round($time_end - $starttime, 4)."<br/>";

				$ids = array_merge($ids1, $ids2, $ids3);
				if (count($ids) == 0)
				{
					$rows = array();
				}
				else
				{

					$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. " \n WHERE rpt.rp_id IN (" . implode(",", $ids) . ")"
						. " \n $cathaving"
						. "\n GROUP BY rpt.rp_id";

					// This limit will always be enough
					//$query .= " LIMIT " . $limit;
					$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

					if (version_compare(JVERSION, '4.0.0', 'ge'))
					{
						$rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
					}
					else
					{
						$rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
					}
				}
				//list ($usec, $sec) = explode(" ", microtime());
				//$time_end = (float) $usec + (float) $sec;
				//echo  "after rows = ".round($time_end - $starttime, 4)."<br/>";

			}
			else
			{
				if ($debugLatest)
				{
					echo "version = false<br>";
				}
				// Display a repeating event for EACH repeat
				// We therefore fetch 3 sets of possible repeats if necessary i.e. not over the limit!
				// Find the ones after now (only if not past only)
				$rows1 = array();
				if ($enddate >= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) != 1)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$t_datenowSQL' AND rpt.startrepeat <= '$enddate'";

					$daterange2 = "\n rptx.endrepeat >= '$t_datenowSQL' AND rptx.startrepeat <= '$enddate'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						// New equivalent but simpler test
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						// duplicating the sort in the group statements improves MySQL performance
						. "\n GROUP BY rpt.startrepeat , rpt.rp_id
						$cathaving
						ORDER BY rpt.startrepeat ASC";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					$cache = JEVHelper::getCache(JEV_COM_COMPONENT);
					if (version_compare(JVERSION, '4.0.0', 'ge'))
					{
						$rows1 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
					}
					else
					{
						$rows1 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
					}
					if ($debugLatest)
					{
						echo "rows 1<br>";
						echo $query . "<br>";
						echo count($rows1) . "<br>";
					}
				}

				// Before now (only if not past only == future events)
				$rows2 = array();
				if ($startdate <= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) < 2)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

					$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					// note the order is the ones nearest today
					$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						// New equivalent but simpler test
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						// duplicating the sort in the group statements improves MySQL performance
						. "\n GROUP BY rpt.startrepeat , rpt.rp_id
							$cathaving
							ORDER BY rpt.startrepeat desc";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

					if (version_compare(JVERSION, '4.0.0', 'ge'))
					{
						$rows2 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
					}
					else
					{
						$rows2 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
					}
					if ($debugLatest)
					{
						echo "rows 2<br>";
						echo $query . "<br>";
						echo count($rows2) . "<br>";
					}
				}

				$rows3 = array();
				if ($multidayTreatment != 2 && $multidayTreatment != 3)
				{
					// Must be starting before NOW otherwise would already be picked up
					$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

					$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					// Mutli day events
					$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday2
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						// duplicating the sort in the group statements improves MySQL performance
						. "\n GROUP BY rpt.startrepeat , rpt.rp_id
							$cathaving
							ORDER BY rpt.startrepeat asc";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

					if (version_compare(JVERSION, '4.0.0', 'ge'))
					{
						$rows3 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
					}
					else
					{
						$rows3 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
					}
					if ($debugLatest)
					{
						echo "rows 3<br>";
						echo $query . "<br>";
						echo count($rows3) . "<br>";
					}
				}


				// ensure specific repeat is not used more than once
				$repeats = array();
				$rows    = array();
				// future events
				foreach ($rows1 as $val)
				{
					if (!is_bool($val) && !in_array($val->rp_id(), $repeats))
					{
						if ($debugLatest)
						{
							echo $val->_startrepeat . " " . $val->rp_id() . " " . $val->title() . "<br/>";
						}

						$repeats[] = $val->rp_id();
						$rows[]    = $val;
					}
				}
				// straddling multi-day event
				foreach ($rows3 as $val)
				{
					if (!in_array($val->rp_id(), $repeats))
					{
						if ($debugLatest)
						{
							echo $val->_startrepeat . " " . $val->rp_id() . " " . $val->title() . "<br/>";
						}

						$repeats[] = $val->rp_id();
						$rows[]    = $val;
					}
				}
				// past events
				foreach ($rows2 as $val)
				{
					if (!in_array($val->rp_id(), $repeats))
					{
						if ($debugLatest)
						{
							echo $val->_startrepeat . " " . $val->rp_id() . " " . $val->title() . "<br/>";
						}

						$repeats[] = $val->rp_id();
						$rows[]    = $val;
					}
				}
				if ($debugLatest)
				{
					echo "count rows " . count($rows1) . " " . count($rows2) . " " . count($rows3) . " " . count($rows) . "<br/>";
				}
				unset($rows1);
				unset($rows2);
				unset($rows3);
			}
		}
		if ($debugLatest)
		{
			echo "count rows = " . count($rows) . "<Br/>";
		}

		JEventsDBModel::translateEvents($rows);

        //!JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
        $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		//list ($usec, $sec) = explode(" ", microtime());
		//$time_end = (float) $usec + (float) $sec;
		//echo  "listLatestIcalEvents  = ".round($time_end - $starttime, 4)."<br/>";

		return $rows;

	}

	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.

	private function getIgnoreRepeatIds()
	{

		$registry = JevRegistry::getInstance("jevents");
		if (!$registry->get("jevents.fetchlatestevents", 0))
		{
			return "";
		}
		$modid = $registry->get("jevents.moduleid", 0);

		$shownEventIds = Factory::getApplication()->getUserState("jevents.moduleid" . $modid . ".shownEventIds", array());
		$page          = (int) Factory::getApplication()->getUserState("jevents.moduleid" . $modid . ".page", 0);

		$direction      = Factory::getApplication()->getUserState("jevents.moduleid" . $modid . ".direction", 1);
		$firstEventDate = Factory::getApplication()->getUserState("jevents.moduleid" . $modid . ".firstEventDate", false);
		$lastEventDate  = Factory::getApplication()->getUserState("jevents.moduleid" . $modid . ".lastEventDate", false);

		$ignoreRepeatIds = "";
		if (count($shownEventIds) > 0)
		{
			// Already done this page so only pick up these repeat ids!
			if (isset($shownEventIds[$page]) && count($shownEventIds[$page]) > 0)
			{
				// To be clean it should be in a different method by hei ho!
				$ignoreRepeatIds = " rpt.rp_id IN (" . implode(",", $shownEventIds[$page]) . ")";
			}
			else
			{
				$ignoreRepeatIds = array();
				foreach ($shownEventIds as $shownpage => $shownids)
				{
					if ($shownpage == $page)
					{
						continue;
					}
					$ignoreRepeatIds = array_merge($ignoreRepeatIds, $shownids);
				}
				if (!count($ignoreRepeatIds))
				{
					$ignoreRepeatIds = "";
				}
				else
				{
					$ignoreRepeatIds = " rpt.rp_id NOT IN (" . implode(",", $ignoreRepeatIds) . ")";
				}
			}
		}

		// Do we supplement the repeat ids to ignore with date constrains for mode 3
		$registry = JevRegistry::getInstance("jevents");
		$params   = $registry->get("jevents.moduleparams", new JevRegistry);
		if ($params->get("modlatest_Mode", 0) != 2)
		{
			$db = Factory::getDbo();
			if ($direction == 1 && $lastEventDate)
			{
				$extra = " rpt.startrepeat >= " . $db->quote($lastEventDate);
			}
			else if ($firstEventDate)
			{
				$extra = " rpt.startrepeat <= " . $db->quote($firstEventDate);
			}
			else
			{
				return $ignoreRepeatIds;
			}
			$ignoreRepeatIds .= ($ignoreRepeatIds != "" ? " AND " : "") . $extra;
		}

		return $ignoreRepeatIds;

	}

	function randomIcalEvents($startdate, $enddate, $limit = 10, $repeatdisplayoptions = 0, $multidayTreatment = 0)
	{

		//list($usec, $sec) = explode(" ", microtime());
		//$starttime = (float) $usec + (float) $sec;

		$app     = Factory::getApplication();
		$input   = $app->input;
		$user    = Factory::getUser();
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate, "-") === false || is_numeric($startdate))
		{
			$startdate = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$enddate   = JevDate::strftime('%Y-%m-%d 23:59:59', $enddate);
		}

		// Use alternative data source
		$rows        = array();
		$skipJEvents = false;
		$app->triggerEvent('fetchListRandomIcalEvents', array(&$skipJEvents, &$rows, $startdate, $enddate, $limit, $repeatdisplayoptions, $multidayTreatment));
		if ($skipJEvents)
		{
			return $rows;
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$rptwhere    = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup  = false;

		$filterarray = array("published", "justmine", "category", "search");

		// If there are extra filters from the module then apply them now
		$reg       = Factory::getConfig();
		$modparams = $reg->get("jev.modparams", false);
		if ($modparams && $modparams->get("extrafilters", false))
		{
			$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
		}

		$filters = jevFilterProcessing::getInstance($filterarray);
		$filters->setWhereJoin($extrawhere, $extrajoin);
		$needsgroup = $filters->needsGroupBy();

		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup, & $rptwhere));

		// showing NO repeating events - in which case MUST search for events with freq=none
		if ($repeatdisplayoptions == 3)
		{
			$extrawhere[] = "LOWER(rr.freq) = 'none'";
		}
		else if ($repeatdisplayoptions == 4)
		{
			$extrawhere[] = "LOWER(rr.freq) <> 'none'";
		}
		// special case for only showing first repeat (i.e. if the first repeat has passed then show nothing!)
		else if ($repeatdisplayoptions == 2)
		{
			$extrawhere[] = "rpt.startrepeat=(
				SELECT MIN(rpt3.startrepeat) FROM #__jevents_repetition as rpt3 WHERE rpt3.eventid=rpt.eventid
			)";
		}

		//list ($usec, $sec) = explode(" ", microtime());
		//$time_end = (float) $usec + (float) $sec;
		//echo  "post onListIcalEvents= ".round($time_end - $starttime, 4)."<br/>";

		// What if join multiplies the rows?
		// Useful MySQL link http://forums.mysql.com/read.php?10,228378,228492#msg-228492
		// concat with group
		// http://www.mysqlperformanceblog.com/2006/09/04/group_concat-useful-group-by-extension/
		// did any of the plugins adjust the range of dateds allowed eg. timelimit plugin - is so then we need to use this information otherwise we  get problems
		$regex = "#(rpt.endrepeat>='[0-9:\- ]*' AND rpt.startrepeat<='[0-9:\- ]*')#";
		foreach ($extrawhere as $exwhere)
		{
			if (preg_match($regex, $exwhere))
			{
				$rptwhere[] = str_replace("rpt.", "rptq.", $exwhere);
			}
		}
		$rptwhere = (count($rptwhere) ? ' AND ' . implode(' AND ', $rptwhere) : '');
		$rptwhere = str_replace("rpt2.", "rptq.", $rptwhere);
		$rptwherex = str_replace("rptq.", "rptx.", $rptwhere);

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		// get the event ids first - split into 2 queries to pick up the ones after now and the ones before
		$t_datenow    = JEVHelper::getNow();
		$t_datenowSQL = $t_datenow->toSql();

		// multiday condition
		if ($multidayTreatment == 3)
		{
			// We only show events once regardless of multiday setting of event so we allow them all through here!
			$multiday  = "";
			$multiday2 = "";
			$multiday3 = "";
		}
		else if ($multidayTreatment == 2)
		{
			// We only show events on their first day only regardless of multiday setting of event so we allow them all through here!
			$multiday  = "";
			$multiday2 = "";
			$multiday3 = "";
		}
		else if ($multidayTreatment == 1)
		{
			// We only show events on all days regardless of multiday setting of event so we allow them all through here!
			$multiday  = "";
			$multiday2 = "";
			$multiday3 = "";
		}
		else
		{
			// We only show events on their first day if they are not to be shown on multiple days so also add this condition
			// i.e. the event settings are used
			// This is the true version of these conditions
			//$multiday = "\n AND ((rpt.startrepeat >= '$startdate' AND det.multiday=0) OR  det.multiday=1)";
			//$multiday2 = "\n AND ((rpt.startrepeat <= '$startdate' AND det.multiday=0) OR  det.multiday=1)";
			// BUT this is logically equivalent and appears much faster  on some databases
			$multiday  = "\n AND (rpt.startrepeat >= '$startdate' OR  det.multiday=1)";
			$multiday2 = "\n AND (rpt.startrepeat <= '$startdate'OR  det.multiday=1)";
			$multiday3 = "AND det.multiday=1";
		}

		if ($repeatdisplayoptions == 1)
		{
			// Display a repeating event ONCE we group by event id selecting the most appropriate repeat for each one
			// Find the ones after now (only if not past only)
			$rows1 = array();
			if ($enddate >= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) != 1)
			{
				$daterange  = "\n AND rpt.endrepeat >= '$t_datenowSQL' AND rpt.startrepeat <= '$enddate'";

				$daterange2 = "\n rptx.endrepeat >= '$t_datenowSQL' AND rptx.startrepeat <= '$enddate'";
				$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
					. "\n WHERE  $daterange2 )";

				$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
					. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
					. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
					. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
					. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
					. "\n FROM #__jevents_repetition as rpt"
					. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
					. $extrajoin
					. $catwhere
					// New equivalent but simpler test
					. ($this->subquery ? $daterange2 : $daterange)
					//xxx
					. $multiday
					. $extrawhere
					. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
					. "  AND icsf.state=1 "
					. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
					// published state is now handled by filter
					. "\n AND rpt.startrepeat=(
				SELECT MIN(rptq.startrepeat) FROM #__jevents_repetition as rptq
				WHERE rptq.eventid=ev.ev_id
				AND  (
					(rptq.startrepeat >= '$t_datenowSQL' AND rptq.startrepeat <= '$enddate')
					OR (rptq.startrepeat <= '$t_datenowSQL' AND rptq.endrepeat  > '$t_datenowSQL'  $multiday3)
					)
				$rptwhere
			) 
                      
                        GROUP BY ev.ev_id
			ORDER BY RAND()";

				// This limit will always be enough

				$query .= " LIMIT " . $limit;

				$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

				if (version_compare(JVERSION, '4.0.0', 'ge'))
				{
					$rows1 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
				}
				else
				{
					$rows1 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
				}
			}

			// Before now (only if not past only == future events)
			$rows2 = array();
			if ($startdate <= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) < 2)
			{
				$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

				$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
				$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
					. "\n WHERE  $daterange2 )";

				// note the order is the ones nearest today
				$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
					. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
					. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
					. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
					. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
					. "\n FROM #__jevents_repetition as rpt"
					. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
					. $extrajoin
					. $catwhere
					// New equivalent but simpler test
					. ($this->subquery ? $daterange2 : $daterange)
					//xxx
					. $multiday
					. $extrawhere
					. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
					. "  AND icsf.state=1 "
					. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
					// published state is now handled by filter
					. "\n AND rpt.startrepeat=(
					SELECT MAX(rptq.startrepeat) FROM #__jevents_repetition as rptq
					 WHERE rptq.eventid=ev.ev_id
					AND rptq.startrepeat <= '$t_datenowSQL' AND rptq.startrepeat >= '$startdate'
					$rptwhere
				)
				GROUP BY ev.ev_id
				ORDER BY RAND()";

				// This limit will always be enough
				$query .= " LIMIT " . $limit;

				$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

				if (version_compare(JVERSION, '4.0.0', 'ge'))
				{
					$rows2 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
				}
				else
				{
					$rows2 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
				}
			}

			$rows3 = array();
			if ($multidayTreatment != 2 && $multidayTreatment != 3)
			{
				// Must be starting before NOW otherwise would already be picked up
				$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

				$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
				$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
					. "\n WHERE  $daterange2 )";

				// Mutli day events
				$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
					. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
					. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
					. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
					. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
					. "\n FROM #__jevents_repetition as rpt"
					. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
					. $extrajoin
					. $catwhere
					. ($this->subquery ? $daterange2 : $daterange)
					//xxx
					. $multiday2
					. $extrawhere
					. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
					. "  AND icsf.state=1 "
					. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
					// published state is now handled by filter
					// This is the correct approach but can be slow
					/*
					  . "\n AND rpt.startrepeat=(
					  SELECT MAX(rptq.startrepeat) FROM #__jevents_repetition as rptq
					  WHERE rptq.eventid=ev.ev_id
					  AND rptq.startrepeat <= '$t_datenowSQL' AND rptq.endrepeat >= '$t_datenowSQL'
					  $rptwherex
					  )"
					 */
					// This is the alternative - it could produce unexpected results if you have overlapping repeats over 'now' but this is  low risk
					. "\n AND rpt.startrepeat <= '$t_datenowSQL' AND rpt.endrepeat >= '$t_datenowSQL'"
					. " \n GROUP BY ev.ev_id
						ORDER BY RAND()";

				// This limit will always be enough
				$query .= " LIMIT " . $limit;

				$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

				if (version_compare(JVERSION, '4.0.0', 'ge'))
				{
					$rows3 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
				}
				else
				{
					$rows3 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
				}
			}

			// ensure specific event is not used more than once
			$events = array();
			$rows   = array();
			// future events
			foreach ($rows1 as $val)
			{
				if (!in_array($val->ev_id(), $events))
				{
					//echo $val->_startrepeat." ".$val->ev_id()." ".$val->title()."<br/>";
					$events[] = $val->ev_id();
					$rows[]   = $val;
				}
			}
			// straddling multi-day event
			foreach ($rows3 as $val)
			{
				if (!in_array($val->ev_id(), $events))
				{
					//echo $val->_startrepeat." ".$val->ev_id()." ".$val->title()."<br/>";
					$events[] = $val->ev_id();
					$rows[]   = $val;
				}
			}
			// past events
			foreach ($rows2 as $val)
			{
				if (!in_array($val->ev_id(), $events))
				{
					//echo $val->_startrepeat." ".$val->ev_id()." ".$val->title()."<br/>";
					$events[] = $val->ev_id();
					$rows[]   = $val;
				}
			}
			//echo "count rows ".count($rows1)." ".count($rows2)." ".count($rows3)." ".count($rows)."<br/>";
			unset($rows1);
			unset($rows2);
			unset($rows3);
		}
		else
		{

			//list ($usec, $sec) = explode(" ", microtime());
			//$time_end = (float) $usec + (float) $sec;
			//echo  "pre version= ".round($time_end - $starttime, 4)."<br/>";

			$version = $input->getCmd("version", "old");

			if ($version == "new")
			{
				// new approach that gets the IDs first
				// Display a repeating event for EACH repeat
				// We therefore fetch 3 sets of possible repeats if necessary i.e. not over the limit!
				// Find the ones after now (only if not past only)
				$ids1 = array();
				if ($enddate >= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) != 1)
				{
					// New equivalent but simpler test
					$daterange  = "\n AND rpt.endrepeat >= '$t_datenowSQL' AND rpt.startrepeat <= '$enddate'";

					$daterange2 = "\n rptx.endrepeat >= '$t_datenowSQL' AND rptx.startrepeat <= '$enddate'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					$query = "SELECT DISTINCT rpt.rp_id"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. $extrajoin
						. $catwhere
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						. "\n GROUP BY rpt.rp_id
						ORDER BY RAND()";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					// TODO cache this!
					$db = Factory::getDbo();
					$db->setQuery($query);
					$ids1 = $db->loadColumn();
				}

				// Before now (only if not past only == future events)
				$ids2 = array();
				if ($startdate <= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) < 2)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

					$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					// note the order is the ones nearest today
					$query = "SELECT  DISTINCT rpt.rp_id"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						// New equivalent but simpler test
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						. "\n GROUP BY rpt.rp_id
							ORDER BY RAND()";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					// TODO cache this!
					$db = Factory::getDbo();
					$db->setQuery($query);
					$ids2 = $db->loadColumn();
				}

				$ids3 = array();
				if ($multidayTreatment != 2 && $multidayTreatment != 3)
				{
					// Must be starting before NOW otherwise would already be picked up
					$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

					$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";
					// Mutli day events
					$query = "SELECT  DISTINCT rpt.rp_id"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday2
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						. "\n GROUP BY rpt.rp_id
							ORDER BY RAND()";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					// TODO cache this!
					$db = Factory::getDbo();
					$db->setQuery($query);
					$ids3 = $db->loadColumn();
				}

				//list ($usec, $sec) = explode(" ", microtime());
				//$time_end = (float) $usec + (float) $sec;
				//echo  "after ids = ".round($time_end - $starttime, 4)."<br/>";

				$ids = array_merge($ids1, $ids2, $ids3);
				if (count($ids) == 0)
				{
					$rows = array();
				}
				else
				{

					$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. " \n WHERE rpt.rp_id IN (" . implode(",", $ids) . ")"
						. "\n GROUP BY rpt.rp_id";

					// This limit will always be enough
					//$query .= " LIMIT " . $limit;
					$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

					if (version_compare(JVERSION, '4.0.0', 'ge'))
					{
						$rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
					}
					else
					{
						$rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
					}
				}
				//list ($usec, $sec) = explode(" ", microtime());
				//$time_end = (float) $usec + (float) $sec;
				//echo  "after rows = ".round($time_end - $starttime, 4)."<br/>";

			}
			else
			{

				// Display a repeating event for EACH repeat
				// We therefore fetch 3 sets of possible repeats if necessary i.e. not over the limit!
				// Find the ones after now (only if not past only)
				$rows1 = array();
				if ($enddate >= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) != 1)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$t_datenowSQL' AND rpt.startrepeat <= '$enddate'";

					$daterange2 = "\n rptx.endrepeat >= '$t_datenowSQL' AND rptx.startrepeat <= '$enddate'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						// New equivalent but simpler test
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						// duplicating the sort in the group statements improves MySQL performance
						. "\n GROUP BY rpt.startrepeat , rpt.rp_id
						ORDER BY RAND()";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

					if (version_compare(JVERSION, '4.0.0', 'ge'))
					{
						$rows1 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
					}
					else
					{
						$rows1 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
					}
				}

				// Before now (only if not past only == future events)
				$rows2 = array();
				if ($startdate <= $t_datenowSQL && $modparams && $modparams->get("pastonly", 0) < 2)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

					$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					// note the order is the ones nearest today
					$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						// New equivalent but simpler test
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						// duplicating the sort in the group statements improves MySQL performance
						. "\n GROUP BY rpt.startrepeat , rpt.rp_id
							ORDER BY RAND()";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

					if (version_compare(JVERSION, '4.0.0', 'ge'))
					{
						$rows2 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
					}
					else
					{
						$rows2 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
					}
				}

				$rows3 = array();
				if ($multidayTreatment != 2 && $multidayTreatment != 3)
				{
					$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'";

					$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$t_datenowSQL'";
					$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
						. "\n WHERE  $daterange2 )";

					// Mutli day events
					$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
						. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
						. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
						. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
						. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
						. "\n FROM #__jevents_repetition as rpt"
						. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
						. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
						. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
						. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
						. $extrajoin
						. $catwhere
						// Must be starting before NOW otherwise would already be picked up
						. ($this->subquery ? $daterange2 : $daterange)
						//xxx
						. $multiday2
						. $extrawhere
						. "\n AND ev.access  IN (" . JEVHelper::getAid($user) . ")"
						. "  AND icsf.state=1 "
						. "\n AND icsf.access  IN (" . JEVHelper::getAid($user) . ")"
						// published state is now handled by filter
						// duplicating the sort in the group statements improves MySQL performance
						. "\n GROUP BY rpt.startrepeat , rpt.rp_id
							ORDER BY RAND()";

					// This limit will always be enough
					$query .= " LIMIT " . $limit;

					$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

					if (version_compare(JVERSION, '4.0.0', 'ge'))
					{
						$rows3 = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
					}
					else
					{
						$rows3 = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
					}
				}


				// ensure specific repeat is not used more than once
				$repeats = array();
				$rows    = array();
				// future events
				foreach ($rows1 as $val)
				{
					if (!in_array($val->rp_id(), $repeats))
					{
						$repeats[] = $val->rp_id();
						$rows[]    = $val;
					}
				}
				// straddling multi-day event
				foreach ($rows3 as $val)
				{
					if (!in_array($val->rp_id(), $repeats))
					{
						$repeats[] = $val->rp_id();
						$rows[]    = $val;
					}
				}
				// past events
				foreach ($rows2 as $val)
				{
					if (!in_array($val->rp_id(), $repeats))
					{
						$repeats[] = $val->rp_id();
						$rows[]    = $val;
					}
				}

				unset($rows1);
				unset($rows2);
				unset($rows3);
			}
		}

      //  !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
        $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		return $rows;

	}

	function listIcalEventsByDay($targetdate)
	{

		// targetdate is midnight at start of day - but just in case
		list ($y, $m, $d) = explode(":", JevDate::strftime('%Y:%m:%d', $targetdate));
		$startdate = JevDate::mktime(0, 0, 0, $m, $d, $y);
		$enddate   = JevDate::mktime(23, 59, 59, $m, $d, $y);

		// timezone offset (3 hours as a test)
		//$startdate = JevDate::strftime('%Y-%m-%d %H:%M:%S', $startdate+10800);
		//$enddate = JevDate::strftime('%Y-%m-%d %H:%M:%S', $enddate+10800);

		$params   = ComponentHelper::getParams("com_jevents");
		$this->daymultiday             = intval($params->get('daymultiday', 0));

		return $this->listIcalEvents($startdate, $enddate);

	}

	function listIcalEvents($startdate, $enddate, $order = "", $filters = false, $extrafields = "", $extratables = "", $limit = "")
	{

		$debuginfo = false;
		list($usec, $sec) = explode(" ", microtime());
		$starttime = (float) $usec + (float) $sec;

		$app     = Factory::getApplication();
		$input   = $app->input;
		$user    = Factory::getUser();
		$db      = Factory::getDbo();
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate, "-") === false || is_numeric($startdate))
		{
			$startdate = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$enddate   = JevDate::strftime('%Y-%m-%d 23:59:59', $enddate);
		}

		// Use alternative data source
		$rows        = array();
		$skipJEvents = false;
		$app->triggerEvent('fetchListIcalEvents', array(&$skipJEvents, &$rows, $startdate, $enddate, $order, $filters, $extrafields, $extratables, $limit));
		if ($skipJEvents)
		{
			return $rows;
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";  // must have comma prefix
		// $extratables = "";  // must have comma prefix
		$needsgroup = false;

		if (!$filters)
		{
			$filterarray = array("published", "justmine", "category", "search", "repeating");

			// If there are extra filters from the module then apply them now
			$reg       = Factory::getConfig();
			$modparams = $reg->get("jev.modparams", false);
			if ($modparams && $modparams->get("extrafilters", false))
			{
				$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
			}

			$filters = jevFilterProcessing::getInstance($filterarray);
			$filters->setWhereJoin($extrawhere, $extrajoin);
			$needsgroup = $filters->needsGroupBy();

			$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

			// What if join multiplies the rows?
			// Useful MySQL link http://forums.mysql.com/read.php?10,228378,228492#msg-228492
			// concat with group
			// http://www.mysqlperformanceblog.com/2006/09/04/group_concat-useful-group-by-extension/
		}
		else
		{
			$filters->setWhereJoin($extrawhere, $extrajoin);
		}

		// Do we only show multiday events on the first day?
		if ($this->daymultiday == 2)
		{

			$extrawhere[] = "rpt.startrepeat >= '$startdate' ";

		}

		if ($debuginfo)
		{
			list ($usec, $sec) = explode(" ", microtime());
			$time_end = (float) $usec + (float) $sec;
			echo "after setup filters = " . round($time_end - $starttime, 4) . "<br/>";
		}

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		$version = $input->getCmd("version", "old");

		if ($version == "new")
		{
			$newextrajoin = $extrajoin;
			// no need to joine images or agenda/minutes
			for ($i = 0; $i < count($newextrajoin); $i++)
			{
				if (strpos($newextrajoin[$i], "#__jev_files") || strpos($newextrajoin[$i], "#__jev_agendaminutes"))
				{
					unset($newextrajoin[$i]);
				}
			}
			$newextrajoin = (count($newextrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $newextrajoin) : '');
			$extrawhere   = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

			$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'";

			$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$enddate'";
			$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
				. "\n INNER JOIN #__jevents_vevdetail as det2 ON det2.evdet_id = rptx.eventdetail_id"
				. "\n WHERE  $daterange2 "
				. "\n AND NOT (rptx.startrepeat < '$startdate' AND det2.multiday=0) "
				. ")";

			$query = "SELECT DISTINCT rpt.rp_id "
				. "\n FROM #__jevents_repetition as rpt"
				. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $newextrajoin
				. $catwhere
				// New equivalent but simpler test
				. ($this->subquery ? $daterange2 : $daterange)
				. ($this->subquery ? "" : "\n AND NOT (rpt.startrepeat < '$startdate' AND det.multiday=0) ")
				. $extrawhere
				. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
				. "  AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")"
				. "\n GROUP BY rpt.rp_id";

			if ($order != "")
			{
				$query .= " ORDER BY " . $order;
			}
			if ($limit != "")
			{
				$query .= " LIMIT " . $limit;
			}

			$db = Factory::getDbo();
			$db->setQuery($query);
			$rptids = $db->loadColumn();

			if ($debuginfo)
			{
				list ($usec, $sec) = explode(" ", microtime());
				$time_end = (float) $usec + (float) $sec;
				echo "after rptids  = " . round($time_end - $starttime, 4) . "<br/>";
			}

			if (count($rptids) > 0)
			{

				$extrajoin = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');

				$query = "SELECT det.evdet_id as detailid, rpt.*, ev.*, rr.*, det.* , ev.rawdata as evrawdata,  ev.state as published, ev.created as created $extrafields"
					. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
					. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
					. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
					. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
					. "\n FROM #__jevents_repetition as rpt"
					. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
					. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
					. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
					. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
					. $extrajoin
					. "\n WHERE rpt.rp_id in (" . implode(",", $rptids) . ") "
					. "\n GROUP BY rpt.rp_id";

				if ($order != "")
				{
					$query .= " ORDER BY " . $order;
				}
				if ($limit != "")
				{
					$query .= " LIMIT " . $limit;
				}

				// skip this cache now we have the onDisplayCustomFieldsMultiRow cache
				$rows = $this->_cachedlistIcalEvents($query, $langtag);
				//$cache = JEVHelper::getCache(JEV_COM_COMPONENT);
				/*
				if (version_compare(JVERSION, '4.0.0', 'ge'))
				{
					$rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
				}
				else
				{
					$rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
				}
				*/
			}
			else
			{
				$rows = array();
			}
		}
		else
		{
			$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
			$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

			$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'";

			$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$enddate'";
			$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
				. "\n INNER JOIN #__jevents_vevdetail as det2 ON det2.evdet_id = rptx.eventdetail_id"
				. "\n WHERE  $daterange2 "
				. "\n AND NOT (rptx.startrepeat < '$startdate' AND det2.multiday=0) "
				. ")";

			// This version picks the details from the details table
			// ideally we should check if the event is a repeat but this involves extra queries unfortunately
			$query = "SELECT det.evdet_id as detailid, rpt.*, ev.*, rr.*, det.* , ev.rawdata as evrawdata,  ev.state as published, ev.created as created $extrafields"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_repetition as rpt"
				. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
				. $extrajoin
				. $catwhere
				// New equivalent but simpler test
				. ($this->subquery ? $daterange2 : $daterange)
				// Should be blocking multi-day events started before the time window
				. ($this->subquery ? "" : "\n AND NOT (rpt.startrepeat < '$startdate' AND det.multiday=0) ")
				. $extrawhere
				. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
				. "  AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")"
				// published state is now handled by filter
				//. "\n AND ev.state=1"
				. ($needsgroup ? "\n GROUP BY rpt.rp_id" : "");

			if ($order != "")
			{
				$query .= " ORDER BY " . $order;
			}
			if ($limit != "")
			{
				$query .= " LIMIT " . $limit;
			}

			if ($debuginfo)
			{
				$db = Factory::getDbo();
				$db->setQuery($query);
				echo "<pre>" .  $db->replacePrefix((string) $db->getQuery()) . "</pre>";
				$rows = $db->loadObjectList();
				list ($usec, $sec) = explode(" ", microtime());
				$time_end = (float) $usec + (float) $sec;
				echo "pre convert rows (" . count($rows) . ") = " . round($time_end - $starttime, 4) . "<br/>";

				$icalcount = count($rows);
				for ($i = 0; $i < $icalcount; $i++)
				{
					// convert rows to jIcalEvents
					$rows[$i] = new jIcalEventRepeat($rows[$i]);
				}

				JEventsDBModel::translateEvents($rows);
			}
			else
			{

				// skip this cache now we have the onDisplayCustomFieldsMultiRow cache
				$rows = $this->_cachedlistIcalEvents($query, $langtag);
			}

		}

        //!JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
        $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		if ($debuginfo)
		{
			list ($usec, $sec) = explode(" ", microtime());
			$time_end = (float) $usec + (float) $sec;
			echo "listIcalEvents  = " . round($time_end - $starttime, 4) . "<br/>";
		}

		$secretmodule = JevRegistry::getInstance("secretmodule");
		if ($secretmodule->get("storedata", 0))
		{
			$secretmodule->set("storeddata", $rows);
		}

		return $rows;

	}

	function _cachedlistIcalEvents($query, $langtag = "", $count = false)
	{

		$debuginfo = false;
		list($usec, $sec) = explode(" ", microtime());
		$starttime = (float) $usec + (float) $sec;

		$user      = Factory::getUser();
		$db        = Factory::getDbo();
		$adminuser = JEVHelper::isAdminUser($user);
		$db->setQuery($query);
		if ($adminuser)
		{
			//echo $db->getQuery()."<br/>";
			//echo $db->explain();
			//exit();
		}

		/*
		SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
		select @@sql_mode;
		SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode,',ONLY_FULL_GROUP_BY'));
		select @@sql_mode;
		 */
		$error      = '';
		$icalrows   = 0;

		if ($count)
		{
			try {
				$db->execute();

				return $db->getNumRows();
			} catch (Throwable $e) {
                $error .= "Database error  " . $e->getCode() . "<br>";
                $error .= $e->getMessage() . "<br>";
                $error .= "<pre>" . (string) $db->getQuery() . "</pre><br>";

			}
		}

		try
		{
			$icalrows = $db->loadObjectList();
		} catch (Throwable $e) {
            $error .= "Database error  " . $e->getCode() . "<br>";
            $error .= $e->getMessage() . "<br>";
			$error .= "<pre>" . (string) $db->getQuery() . "</pre><br>";
			$icalrows = [];
		}

		if ($adminuser)
		{
			echo $error;
		}

		$icalcount = count($icalrows);

		if ($debuginfo)
		{
			list ($usec, $sec) = explode(" ", microtime());
			$time_end = (float) $usec + (float) $sec;
			echo "pre converting (" . $icalcount . ")= " . round($time_end - $starttime, 4) . "<br/>";
		}

		$valid = true;
		for ($i = 0; $i < $icalcount; $i++)
		{
			// only convert rows when necessary
			if ($i == 0 && count(get_object_vars($icalrows[$i])) < 5)
			{
				$valid = false;
				break;
			}
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}

		if (!$valid)
			return $icalrows;

		JEventsDBModel::translateEvents($icalrows);

		JEVHelper::onDisplayCustomFieldsMultiRow($icalrows);

		if ($debuginfo)
		{
			list ($usec, $sec) = explode(" ", microtime());
			$time_end = (float) $usec + (float) $sec;
			echo "after converting (" . $icalcount . ")= " . round($time_end - $starttime, 4) . "<br/>";
		}

		return $icalrows;

	}

	function listIcalEventsByWeek($weekstart, $weekend)
	{
		$params   = ComponentHelper::getParams("com_jevents");
		$this->daymultiday             = intval($params->get('daymultiday', 0));

		return $this->listIcalEvents($weekstart, $weekend);

	}

	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.

	function listIcalEventsByMonth($year, $month)
	{

		$startdate = JevDate::mktime(0, 0, 0, $month, 1, $year);
		$enddate   = JevDate::mktime(23, 59, 59, $month, date('t', $startdate), $year);

		//$startdate -= 604800;
		//$enddate   += 604800;

//		$cfg = JEVConfig::getInstance();
//		var_dump($this->countIcalEventsByRangebyDay($startdate, $enddate,  $cfg->get('com_showrepeats')));

		$params   = ComponentHelper::getParams("com_jevents");
		$this->daymultiday             = intval($params->get('daymultiday', 0));

		return $this->listIcalEvents($startdate, $enddate, "");

	}

	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.

	function countIcalEventsByRangebyDay($startdate, $enddate, $showrepeats = true)
	{

		if (strpos($startdate, "-") === false || is_numeric($startdate))
		{
			$startdate = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$enddate   = JevDate::strftime('%Y-%m-%d 23:59:59', $enddate);
		}

		list($syear, $smonth, $sday) = explode('-', $startdate);
		list($eyear, $emonth, $eday) = explode('-', $enddate);

		$startdate .= " 00:00:00";
		$enddate   .= " 23:59:59";

		$user    = Factory::getUser();

		$app     = Factory::getApplication();
		$db      = Factory::getDbo();
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrawhere2 = array();
		$extrajoin   = array();
		$extrafields = '';  // Must have comma prefix
		$extratables = '';
		$needsgroup  = false;

		$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "search", "repeating"));
		$filters->setWhereJoin($extrawhere, $extrajoin);
		$needsgroup = $filters->needsGroupBy();

		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[]  = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[]  = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields  .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			if ($this->subquery)
			{
				$extrawhere2[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			}
			else
			{
				$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			}
			$needsgroup = true;
			$catwhere   = "\n WHERE 1 ";
		}

		$extrajoin   = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere  = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');
		$extrawhere2 = (count($extrawhere2) ? ' AND ' . implode(' AND ', $extrawhere2) : '');

		$daterange = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'";
		// Must suppress multiday events that have already started
		$multidate  = "\n AND NOT (rpt.startrepeat < '$startdate' AND det.multiday=0) ";

		$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$enddate'";
		$multidate2 = "\n AND NOT (rptx.startrepeat < '$startdate' AND det2.multiday=0) ";
		$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
			. "\n INNER JOIN #__jevents_vevdetail as det2  ON det2.evdet_id = rptx.eventdetail_id"
			. "\n WHERE  $daterange2 "
			. $multidate2
			. ")";

		if (!$showrepeats)
		{
			$query = "SELECT count(distinct ev.ev_id), DATE(rpt.startrepeat)";
		}
		else
		{
			$query = "SELECT count(distinct rpt.rp_id), DATE(rpt.startrepeat)";
		}
		$query .= "\n FROM #__jevents_repetition as rpt ";

		$query .= "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
			. $extrajoin
			. $catwhere
			. ($this->subquery ? $daterange2 : $daterange)
			. ($this->subquery ? "" : $multidate)
			. $extrawhere
			. $extrawhere2
			. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
			. "  AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")"
			. "\n Group by DATE(rpt.startrepeat)";

		$db = Factory::getDbo();
		$db->setQuery($query);
		$res = $db->loadObjectList();

		return $res;

	}

	function countIcalEventsByYear($year, $showrepeats = true)
	{

		$startdate = JevDate::mktime(0, 0, 0, 1, 1, $year);
		$enddate   = JevDate::mktime(23, 59, 59, 12, 31, $year);

		return $this->listIcalEventsByYear($year, "", "", $showrepeats, "", false, "", "", true);

	}

	function listIcalEventsByYear($year, $limitstart, $limit, $showrepeats = true, $order = "", $filters = false, $extrafields = "", $extratables = "", $count = false)
	{

		list($xyear, $month, $day) = JEVHelper::getYMD();
		$app      = Factory::getApplication();
		$thisyear = new JevDate("+0 seconds");
		list($thisyear, $thismonth, $thisday) = explode("-", $thisyear->toFormat("%Y-%m-%d"));
		if (!$this->cfg->get("showyearpast", 1) && $year < $thisyear)
		{
			return array();
		}
		$params   = ComponentHelper::getParams("com_jevents");
		$this->daymultiday             = intval($params->get('daymultiday', 0));

		$startdate = ($this->cfg->get("showyearpast", 1) || $year > $thisyear) ? JevDate::mktime(0, 0, 0, 1, 1, $year) : JevDate::mktime(0, 0, 0, $thismonth, $thisday, $thisyear);
		$enddate   = JevDate::mktime(23, 59, 59, 12, 31, $year);
		if (!$count)
		{
			$order = "rpt.startrepeat asc";
		}

		$user    = Factory::getUser();
		$db      = Factory::getDbo();
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate, "-") === false || is_numeric($startdate))
		{
			$startdate = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$enddate   = JevDate::strftime('%Y-%m-%d 23:59:59', $enddate);
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup  = false;

		if (!$filters)
		{
			$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "search", "repeating"));
			$filters->setWhereJoin($extrawhere, $extrajoin);
			$needsgroup = $filters->needsGroupBy();

			$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));
		}
		else
		{
			$filters->setWhereJoin($extrawhere, $extrajoin);
		}

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		// This version picks the details from the details table
		if ($count)
		{
			$query = "SELECT rpt.rp_id";
		}
		else
		{
			$query = "SELECT ev.*, rpt.*, rr.*, det.*, ev.state as published $extrafields"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn";
		}
		if (!$showrepeats && !$count)
		{
			// suggest an index to ensure the group by gets the correct row
			$query .= "\n FROM #__jevents_repetition as rpt  use INDEX (eventstart)";
		}
		else
		{
			$query .= "\n FROM #__jevents_repetition as rpt ";
		}
		$daterange  = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'";

		$daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$enddate'";
		$daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
			. "\n WHERE  $daterange2 )";

		$query .= "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
			. $extrajoin
			. $catwhere
			// New equivalent but simpler test
			. ($this->subquery ? $daterange2 : $daterange)
			. $extrawhere
			. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
			. "  AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")"
			// published state is not handled by filter
			//. "\n AND ev.state=1"
		;
		if (!$showrepeats)
		{
			$query .= "\n GROUP BY ev.ev_id";
		}
		else if ($needsgroup)
		{
			$query .= "\n GROUP BY rpt.rp_id";
		}

		if ($order != "")
		{
			$query .= " ORDER BY " . $order;
		}
		if ($limit != "" && $limit != 0)
		{
			$query .= " LIMIT " . ($limitstart != "" ? $limitstart . "," : "") . $limit;
		}

		$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

		if (version_compare(JVERSION, '4.0.0', 'ge'))
		{
			$rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag, $count));
		}
		else
		{
			$rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag, $count);
		}

		if (!$count)
		{
           // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
            $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
           // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');
		}

		return $rows;

	}

	function countIcalEventsByRange($startdate, $enddate, $showrepeats = true, $repeatType = 'any')
	{

		return $this->listIcalEventsByRange($startdate, $enddate, "", "", $showrepeats, "", false, "", "", true, $repeatType);
	}

	function listIcalEventsByRange($startdate, $enddate, $limitstart, $limit, $showrepeats = true, $order = "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC", $filters = false, $extrafields = "", $extratables = "", $count = false, $repeatType = 'any')
    {

	    $params   = ComponentHelper::getParams("com_jevents");
	    $this->daymultiday             = intval($params->get('daymultiday', 0));

	    $app = Factory::getApplication();
        $input = $app->input;
        list($year, $month, $day) = explode('-', $startdate);
        list($thisyear, $thismonth, $thisday) = JEVHelper::getYMD();

        //$startdate 	= $this->cfg->get("showyearpast",1)?JevDate::mktime( 0, 0, 0, intval($month),intval($day),intval($year) ):JevDate::mktime( 0, 0, 0, $thismonth,$thisday, $thisyear );
        $startdate = JevDate::mktime(0, 0, 0, intval($month), intval($day), intval($year));

        $startdate = JevDate::strftime('%Y-%m-%d', $startdate);

        if (StringHelper::strlen($startdate) == 10)
            $startdate .= " 00:00:00";
        if (StringHelper::strlen($enddate) == 10)
            $enddate .= " 23:59:59";

        // This code is used by the iCals code with a spoofed user so check if this is what is happening
        if ($input->getString("jevtask", "") == "icals.export") {
            $registry = JevRegistry::getInstance("jevents");
            $user = $registry->get("jevents.icaluser", false);
            if (!$user)
                $user = Factory::getUser();
        } // remote module loader uses this too
        else if ($input->getInt("rau")) {
            $registry = JevRegistry::getInstance("jevents");
            $user = $registry->get("jevents.icaluser", false);
            if (!$user)
                $user = Factory::getUser();
        } else {
            $user = Factory::getUser();
        }
        $db = Factory::getDbo();
        $lang = Factory::getLanguage();
        $langtag = $lang->getTag();

        // process the new plugins
        // get extra data and conditionality from plugins
        $extrawhere = array();
        $extrawhere2 = array();
        $extrajoin = array();
        $extrafields = "";  // must have comma prefix
        $needsgroup = false;

        if (!$filters) {
            $filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "search", "repeating"));
            $filters->setWhereJoin($extrawhere, $extrajoin);
            $needsgroup = $filters->needsGroupBy();

            $app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));
        } else {
            $filters->setWhereJoin($extrawhere, $extrajoin);
        }

        $catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
        $params = ComponentHelper::getParams("com_jevents");
        if ($params->get("multicategory", 0)) {
            $extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
            $extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
            $extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
            // accessibleCategoryList handles access checks on category
            //$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
            if ($this->subquery) {
                $extrawhere2[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
            } else {
                $extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
            }
            $needsgroup = true;
            $catwhere = "\n WHERE 1 ";
        }

	    // Do we only show multiday events on the first day?
	    if ($this->daymultiday == 2)
	    {

		    $extrawhere[] = "rpt.startrepeat >= '$startdate' ";

	    }

        if ($repeatType !== 'any')
        {
            if (strpos($repeatType, "!") === false)
            {
                $extrawhere[] = " rr.freq = '" . $repeatType . "'";
            }
            else
            {
                $extrawhere[] = " rr.freq <> '" . str_replace("!", "", $repeatType) . "'";
            }
        }

	    $extrajoin = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
        $extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');
        $extrawhere2 = (count($extrawhere2) ? ' AND ' . implode(' AND ', $extrawhere2) : '');

        // Do we want to only use start or end dates in the range?
        $usedates = $params->get("usedates", "both");
        if ($usedates == "both") {
            $daterange = "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'";
            // Must suppress multiday events that have already started
            $multidate = "\n AND NOT (rpt.startrepeat < '$startdate' AND det.multiday=0) ";

            $daterange2 = "\n rptx.endrepeat >= '$startdate' AND rptx.startrepeat <= '$enddate'";
            $multidate2 = "\n AND NOT (rptx.startrepeat < '$startdate' AND det2.multiday=0) ";
            $daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
                . "\n INNER JOIN #__jevents_vevdetail as det2  ON det2.evdet_id = rptx.eventdetail_id"
                . "\n WHERE  $daterange2 "
                . $multidate2
                . ")";
        } else if ($usedates == "start") {
            $daterange = "\n AND rpt.endrepeat >= '$startdate' ";
            // Must suppress multiday events that have already started
            $multidate = "\n AND NOT (rpt.startrepeat < '$startdate' AND det.multiday=0) ";

            $daterange2 = "\n rptx.endrepeat >= '$startdate' ";
            $multidate2 = "\n AND NOT (rptx.startrepeat < '$startdate' AND det2.multiday=0) ";
            $daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
                . "\n INNER JOIN #__jevents_vevdetail as det2  ON det2.evdet_id = rptx.eventdetail_id"
                . "\n WHERE  $daterange2 "
                . $multidate2
                . ")";
        } else if ($usedates == "end") {
            $daterange = "\n AND rpt.startrepeat <= '$enddate' ";
            // Must suppress multiday events that haven't already ended
            $multidate = "\n AND NOT (rpt.endrepeat > '$enddate' AND det.multiday=0) ";

            $daterange2 = "\n rptx.startrepeat <= '$enddate' ";
            $multidate2 = "\n AND NOT (rptx.endrepeat > '$enddate' AND det2.multiday=0) ";
            $daterange2 = "\n AND rpt.rp_id in (SELECT rptx.rp_id FROM #__jevents_repetition as rptx "
                . "\n INNER JOIN #__jevents_vevdetail as det2  ON det2.evdet_id = rptx.eventdetail_id"
                . "\n WHERE  $daterange2 "
                . $multidate2
                . ")";
        }
        // This version picks the details from the details table
        if ($count) {
            if (!$showrepeats) {
                $query = "SELECT count(distinct ev.ev_id)";
            } else {
                $query = "SELECT count(distinct rpt.rp_id)";
            }
        } else {
            $query = "SELECT ev.*, rpt.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
                . "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
                . "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
                . "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
                . "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn";
        }
        if (!$showrepeats && !$count) {
            $fullselect = $query;

            // suggest an index to ensure the group by gets the correct row
            $query .= "\n FROM #__jevents_repetition as rpt  use INDEX (eventstart)";
        } else {
            $query .= "\n FROM #__jevents_repetition as rpt ";
        }

        $query .= "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
            . "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
            . "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
            . "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
            . $extrajoin
            . $catwhere
            . ($this->subquery ? $daterange2 : $daterange)
            . ($this->subquery ? "" : $multidate)
            . $extrawhere
            . $extrawhere2
            . "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
            . "  AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")";

        if (!$showrepeats && !$count) {
//            $minselect = "SELECT MIN(rpt.rp_id) as minrpt ";
//           $subquery = str_replace($fullselect, $minselect, $query);

            // Need to do it this way - the previous way didn't work for irregular repeats where the repeat id was not in sequence!
            $minselect = "SELECT rpt2.rp_id as minrpt , MIN(rpt.startrepeat)";
            $newjoin = "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid\n LEFT JOIN #__jevents_repetition as rpt2 ON rpt.rp_id = rpt2.rp_id";
            $subquery = str_replace("\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid", $newjoin, $query);

            $subquery = str_replace($fullselect, $minselect, $subquery);

            $db->setQuery("$subquery GROUP BY ev.ev_id");
            $rpids = $db->loadColumn(0);
            $rpids = is_array($rpids) ? $rpids : array(0);
            $rpids[] = -1;
	        if ($input->get('task') == 'icals.export')
	        {
		        //echo (string) $db->getQuery() . "<br>";
				//var_dump($rpids);
		        //exit();
	        }

            // Reconstitute the query with the additional conditionality

            // This version picks the details from the details table
            if ($count) {
                if (!$showrepeats) {
                    $query = "SELECT count(distinct ev.ev_id)";
                } else {
                    $query = "SELECT count(distinct rpt.rp_id)";
                }
            } else {
                $query = "SELECT ev.*, rpt.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
                         . "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
                         . "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
                         . "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
                         . "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn";
            }
            if (!$showrepeats && !$count) {
                $fullselect = $query;

                // suggest an index to ensure the group by gets the correct row
                $query .= "\n FROM #__jevents_repetition as rpt  use INDEX (eventstart)";
            } else {
                $query .= "\n FROM #__jevents_repetition as rpt ";
            }

            $query .= "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
                      . "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
                      . "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
                      . "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
                      . $extrajoin
                      . $catwhere

                      // new conditionality
                      . "\n AND rpt.rp_id IN (" . implode(",", $rpids) . " ) "

                      . ($this->subquery ? $daterange2 : $daterange)
                      . ($this->subquery ? "" : $multidate)
                      . $extrawhere
                      . $extrawhere2
                      . "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
                      . "  AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")";

            $query .= "\n GROUP BY rpt.rp_id";

            // This is FAR to simplistic!
            /*
            $query = substr($query, 0, strpos($query, "WHERE "));
            $query .= "\n WHERE rpt.rp_id IN (" . implode(",", $rpids) . " ) GROUP BY rpt.rp_id";
            */

        } else if ($needsgroup && !$count) {
            $query .= "\n GROUP BY rpt.rp_id";
        }

        if ($order != "")
		{
			$query .= " ORDER BY " . $order;
		}
		if ($limit != "" && $limit != 0)
		{
			$query .= " LIMIT " . ($limitstart != "" ? $limitstart . "," : "") . $limit;
		}

		if ($count)
		{
			$db = Factory::getDbo();
			$db->setQuery($query);
			$res = $db->loadResult();

			return $res;
		}


		$cache = JEVHelper::getCache(JEV_COM_COMPONENT);

	    if (version_compare(JVERSION, '4.0.0', 'ge'))
	    {
		    $rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag, $count));
	    }
	    else
	    {
		    $rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag, $count);
	    }

	    if ($input->get('task') == 'icals.export')
	    {
		 //   echo $query . "<br>";
		//	var_dump($rows);
		 //   exit();
	    }

        //!JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
        $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
        //!JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		$secretmodule = JevRegistry::getInstance("secretmodule");
		if ($secretmodule->get("storedata", 0))
		{
			$secretmodule->set("storeddata", $rows);
		}

		return $rows;

	}

	/**
	 * Get Event by ID (not repeat Id) result is based on first repeat
	 *
	 * @param event_id $evid
	 * @param boolean  $includeUnpublished (which also means trashed too!)
	 * @param string   $jevtype
	 *
	 * @return jeventcal (or desencent)
	 */
	function getEventById($evid, $includeUnpublished = 0, $jevtype = "icaldb", $checkAccess = true, $skipFilters = false)
	{

		$user = Factory::getUser();
		$db   = Factory::getDbo();

		$app  = Factory::getApplication();

		$frontendPublish = JEVHelper::isEventPublisher();

		if ($jevtype == "icaldb")
		{
			// process the new plugins
			// get extra data and conditionality from plugins
			$extrafields = "";  // must have comma prefix
			$extratables = "";  // must have comma prefix
			$extrawhere  = array();
			$extrajoin   = array();

			if ($includeUnpublished)
			{
				$filterarray = array("justmine", "search", "repeating");
			}
			else
			{
				$filterarray = array("published", "justmine", "search", "repeating");
			}

			if ($skipFilters)
			{
				$filterarray = array();
			}

			// If there are extra filters from the module then apply them now
			$reg       = Factory::getConfig();
			$modparams = $reg->get("jev.modparams", false);
			if ($modparams && $modparams->get("extrafilters", false))
			{
				$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
			}

			$filters = jevFilterProcessing::getInstance($filterarray);
			$filters->setWhereJoin($extrawhere, $extrajoin);
			$needsgroup = $filters->needsGroupBy();

			if (!$skipFilters)
			{
				$app->triggerEvent('onListEventsById', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin));
			}

			$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList(null, null, null, false, $checkAccess) . ")";
			$params   = ComponentHelper::getParams("com_jevents");
			if ($params->get("multicategory", 0))
			{
				$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
				$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
				$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
				if ($checkAccess)
				{
					// accessibleCategoryList handles access checks on category
					//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
				}
				$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList(null, null, null, false, $checkAccess) . ")";
				$needsgroup   = true;
				$catwhere     = "\n WHERE 1 ";
			}

			$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
			$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

			// make sure we pick up the event state
			$query = "SELECT ev.*, rpt.*, rr.*, det.* $extrafields , ev.state as state,  ev.state as published "
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. (empty($extratables) ? "\n FROM #__jevents_vevent as ev" : "\n FROM (#__jevents_vevent as ev $extratables)")
				. "\n INNER JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
				. $extrajoin
				. $catwhere
				. ($checkAccess ? "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")" : "")
				. ($includeUnpublished ? "" : " AND icsf.state=1")
				. ($checkAccess ? "\n AND icsf.access IN (" . JEVHelper::getAid($user) . ")" : "")
				. $extrawhere
				. "\n AND ev.ev_id = '$evid'"
				. "\n GROUP BY rpt.rp_id"
				. "\n LIMIT 1";
		}
		else
		{
			die("invalid jevtype in listEventsById - more changes needed");
		}

		$db->setQuery($query);
		//echo $db->_sql;
		$rows = $db->loadObjectList();

		// iCal agid uses GUID or UUID as identifier
		if ($rows)
		{
			// set multi-category and check access levels
			// done in the above query
			/*
			  if (!$this->setMultiCategory($rows[0],$accessibleCategories)){
			  return null;
			  }
			 *
			 */

			if (strtolower($jevtype) == "icaldb")
			{
				$row = new jIcalEventRepeat($rows[0]);
			}
			else if (strtolower($jevtype) == "jevent")
			{
				$row = new jEventCal($rows[0]);
			}

			JEventsDBModel::translateEvents($row);

		}
		else
		{
			//$app    = Factory::getApplication();
			//$app->enqueueMessage($query, 'warning');
			$row = null;
		}

		return $row;

	}

	function listIcalEventsByCreator($creator_id, $limitstart, $limit, $orderby = 'dtstart ASC')
	{

		$user = Factory::getUser();
		$db   = Factory::getDbo();

		$app  = Factory::getApplication();
		$cfg  = JEVConfig::getInstance();

		$params   = ComponentHelper::getParams("com_jevents");
		$this->daymultiday             = intval($params->get('daymultiday', 0));

		$rows_per_page = $limit;

		if (empty($limitstart) || !$limitstart)
		{
			$limitstart = 0;
		}

		$limit = "";
		if ($limitstart > 0 || $rows_per_page > 0)
		{
			$limit = "LIMIT $limitstart, $rows_per_page";
		}

		$frontendPublish = JEVHelper::isEventPublisher();

		$adminCats = JEVHelper::categoryAdmin();

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup  = false;

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		$where = '';
		if ($creator_id == 'ADMIN' || JEVHelper::isEventEditor() || JEVHelper::isEventPublisher(true))
		{
			$where = "";
		}
		else if ($adminCats && count($adminCats) > 0)
		{
			//$adminCats = " OR (ev.state=0 AND ev.catid IN(".implode(",",$adminCats)."))";
			if ($params->get("multicategory", 0))
			{
				$adminCats = " OR catmap.catid IN(" . implode(",", $adminCats) . ")";
			}
			else
			{
				$adminCats = " OR ev.catid IN(" . implode(",", $adminCats) . ")";
			}
			$where = " AND ( ev.created_by = " . $user->id . $adminCats . ")";
		}
		else
		{
			$where = " AND ev.created_by = '$creator_id' ";
		}

		$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "startdate", "search", "repeating"));
		$filters->setWhereJoin($extrawhere, $extrajoin);

		$needsgroup = false;
		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		$query = "SELECT ev.*, rr.*, det.*, ev.state as published, count(rpt.rp_id) as rptcount $extrafields"
			. "\n , YEAR(dtstart) as yup, MONTH(dtstart ) as mup, DAYOFMONTH(dtstart ) as dup"
			. "\n , YEAR(dtend  ) as ydn, MONTH(dtend   ) as mdn, DAYOFMONTH(dtend   ) as ddn"
			. "\n , HOUR(dtstart) as hup, MINUTE(dtstart) as minup, SECOND(dtstart   ) as sup"
			. "\n , HOUR(dtend  ) as hdn, MINUTE(dtend  ) as mindn, SECOND(dtend     ) as sdn"
			. "\n FROM #__jevents_vevent as ev"
			. "\n INNER JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = ev.detail_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. $extrajoin
			. $catwhere
			. $extrawhere
			. $where
			. "\n AND icsf.state=1"
			. "\n GROUP BY ev.ev_id"
			. "\n ORDER BY " . ($orderby != "" ? $orderby : "dtstart ASC")
			. "\n $limit";

		$db->setQuery($query);

		$icalrows   = 0;
		try
		{
			$icalrows = $db->loadObjectList();
		} catch (Throwable $e) {
			echo $e;
		}

		$icalcount = count($icalrows);
		for ($i = 0; $i < $icalcount; $i++)
		{
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}

		JEVHelper::onDisplayCustomFieldsMultiRow($icalrows);

		return $icalrows;

	}

	function listIcalEventRepeatsByCreator($creator_id, $limitstart, $limit, $orderby = "rpt.startrepeat")
	{

		// Use alternative data source
		$rows        = array();
		$skipJEvents = false;
		$app         = Factory::getApplication();
		$app->triggerEvent('fetchListIcalEventRepeatsByCreator', array(&$skipJEvents, &$rows, $creator_id, $limitstart, $limit, $orderby));

		if ($skipJEvents)
		{
			return $rows;
		}

		$params   = ComponentHelper::getParams("com_jevents");
		$this->daymultiday             = intval($params->get('daymultiday', 0));

		$user = Factory::getUser();
		$db   = Factory::getDbo();

		$cfg = JEVConfig::getInstance();

		$rows_per_page = $limit;

		if (empty($limitstart) || !$limitstart)
		{
			$limitstart = 0;
		}

		$limit = "";
		if ($limitstart > 0 || $rows_per_page > 0)
		{
			$limit = "LIMIT $limitstart, $rows_per_page";
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup  = false;

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		$adminCats = JEVHelper::categoryAdmin();
		$where     = '';
		if ($creator_id == 'ADMIN' || JEVHelper::isEventEditor() || JEVHelper::isEventPublisher(true))
		{
			$where = "";
		}
		else if ($adminCats && count($adminCats) > 0)
		{
			if ($params->get("multicategory", 0))
			{
				$adminCats = " OR catmap.catid IN(" . implode(",", $adminCats) . ")";
			}
			else
			{
				$adminCats = " OR ev.catid IN(" . implode(",", $adminCats) . ")";
			}
			$where = " AND ( ev.created_by = " . $user->id . $adminCats . ")";
		}
		else
		{
			$where = " AND ev.created_by = '$creator_id' ";
		}

		$frontendPublish = JEVHelper::isEventPublisher();

		$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "startdate", "search", "repeating"));
		$filters->setWhereJoin($extrawhere, $extrajoin);

		$needsgroup = false;
		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		$needsgroup = false;
		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		if ($frontendPublish)
		{
			// TODO fine a single query way of doing this !!!
			$query = "SELECT rp_id"
				. "\n FROM #__jevents_repetition as rpt "
				. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $extrajoin
				. $catwhere
				. $extrawhere
				. $where
				. "\n  AND icsf.state=1"
				. "\n GROUP BY rpt.rp_id"
				. "\n ORDER BY " . ($orderby != "" ? $orderby : "rpt.startrepeat ASC")
				. "\n $limit";

			$db->setQuery($query);
			$rplist = $db->loadColumn();
			//echo $db->explain();

			$rplist = implode(',', array_merge(array(-1), $rplist));

			$query = "SELECT ev.*, rpt.*, rr.*, det.*, ev.state as published"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_vevent as ev "
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n AND rpt.eventid = ev.ev_id"
				. "\n AND rpt.rp_id IN($rplist)"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $extrajoin
				. $catwhere
				. $extrawhere
				. $where
				. "\n  AND icsf.state=1"
				. "\n GROUP BY rpt.rp_id"
				. "\n ORDER BY " . ($orderby != "" ? $orderby : "rpt.startrepeat ASC");
		}
		else
		{
			// TODO fine a single query way of doing this !!!
			$query = "SELECT rp_id"
				. "\n FROM #__jevents_vevent as ev "
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $extrajoin
				. $catwhere
				. $extrawhere
				. "\n AND icsf.state=1"
				. $where
				. "\n GROUP BY rpt.rp_id"
				. "\n ORDER BY " . ($orderby != "" ? $orderby : "rpt.startrepeat ASC")
				. "\n $limit";

			$db->setQuery($query);
			$rplist = $db->loadColumn();

			$rplist = implode(',', array_merge(array(-1), $rplist));

			$query = "SELECT ev.*, rpt.*, rr.*, det.*, ev.state as published"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_vevent as ev "
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n AND rpt.rp_id IN($rplist)"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $extrajoin
				. $catwhere
				. $where
				. "\n AND icsf.state=1"
				. $extrawhere
				. "\n GROUP BY rpt.rp_id"
				. "\n ORDER BY " . ($orderby != "" ? $orderby : "rpt.startrepeat ASC");
		}
		$db->setQuery($query);
		$icalrows  = $db->loadObjectList();
		$icalcount = count($icalrows);
		for ($i = 0; $i < $icalcount; $i++)
		{
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}

		JEventsDBModel::translateEvents($icalrows);

		JEVHelper::onDisplayCustomFieldsMultiRow($icalrows);

		return $icalrows;

	}

	function countIcalEventsByCreator($creator_id)
	{

		$user = Factory::getUser();
		$db   = Factory::getDbo();
		$app  = Factory::getApplication();

		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}


		$adminCats = JEVHelper::categoryAdmin();
		$where     = '';
		if ($creator_id == 'ADMIN' || JEVHelper::isEventEditor() || JEVHelper::isEventPublisher(true))
		{
			$where = "";
		}
		else if ($adminCats && count($adminCats) > 0)
		{
			if ($params->get("multicategory", 0))
			{
				$adminCats = " OR catmap.catid IN(" . implode(",", $adminCats) . ")";
			}
			else
			{
				$adminCats = " OR ev.catid IN(" . implode(",", $adminCats) . ")";
			}
			$where = " AND ( ev.created_by = " . $user->id . $adminCats . ")";
		}
		else
		{
			$where = " AND ev.created_by = '$creator_id' ";
		}

		// State is managed by plugin
		/*
		  $frontendPublish = JEVHelper::isEventPublisher();
		  $state = "\n AND ev.state=1";
		  if ($frontendPublish){
		  $state = "";
		  }
		 */

		$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "startdate", "search", "repeating"));
		$filters->setWhereJoin($extrawhere, $extrajoin);

		$needsgroup = false;
		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		$query = "SELECT MIN(rpt.rp_id) as rp_id"
			. "\n FROM #__jevents_vevent as ev "
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. $extrajoin
			. $catwhere
			. $extrawhere
			. $where
			. "\n AND icsf.state=1"
			. "\n GROUP BY ev.ev_id";

		$db->setQuery($query);
		$db->execute();

		return $db->getNumRows();

	}

	function countIcalEventRepeatsByCreator($creator_id)
	{

		$user = Factory::getUser();
		$db   = Factory::getDbo();

		$extrawhere  = array();
		$extrajoin   = array();
		$extrafields = "";  // must have comma prefix

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}


		$adminCats = JEVHelper::categoryAdmin();
		$where     = '';
		if ($creator_id == 'ADMIN')
		{
			$where = "";
		}
		else if ($adminCats && count($adminCats) > 0)
		{
			if ($params->get("multicategory", 0))
			{
				$adminCats = " OR catmap.catid IN(" . implode(",", $adminCats) . ")";
			}
			else
			{
				$adminCats = " OR ev.catid IN(" . implode(",", $adminCats) . ")";
			}
			$where = " AND ( ev.created_by = " . $user->id . $adminCats . ")";
		}
		else
		{
			$where = " AND ev.created_by = '$creator_id' ";
		}

		// State is managed by plugin

		$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "startdate", "search", "repeating"));
		$filters->setWhereJoin($extrawhere, $extrajoin);

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		$query = "SELECT rpt.rp_id, ev.catid"
			. "\n FROM #__jevents_repetition as rpt "
			. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. $extrajoin
			. $catwhere
			. $extrawhere
			. $where
			. "\n AND icsf.state=1"
			. "\n GROUP BY rpt.rp_id";

		$db->setQuery($query);
		$db->execute();

		return $db->getNumRows();

	}

	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	public function listIcalEventsByCat($catids, $showrepeats = false, $total = 0, $limitstart = 0, $limit = 0, $order = "rpt.startrepeat asc, rpt.endrepeat ASC, det.summary ASC", $filters = false, $extrafields = "", $extratables = "")
	{

		$db   = Factory::getDbo();
		$user = Factory::getUser();
		$app  = Factory::getApplication();

		$params   = ComponentHelper::getParams("com_jevents");
		$this->daymultiday             = intval($params->get('daymultiday', 0));

		// Use catid in accessibleCategoryList to pick up offsping too!
		$aid       = null;
		$catidlist = implode(",", $catids);

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$extrawhere  = array();
		$extrajoin   = array();
		$needsgroup  = false;

		if (!$this->cfg->get("showyearpast", 1))
		{
			list($year, $month, $day) = JEVHelper::getYMD();
			$startdate = JevDate::mktime(0, 0, 0, $month, $day, $year);
			$today     = JevDate::strtotime("+0 days");
			if ($startdate < $today)
				$startdate = $today;
			$startdate    = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$extrawhere[] = "rpt.endrepeat >=  '$startdate'";
		}

		if ($limit == 0 && $this->cfg->get("maxevents", 10) > 0)
		{
			$limit = $this->cfg->get("maxevents", 10);
		}
		else if ($this->cfg->get("maxevents", 10) > $limit)
		{
			$limit = $this->cfg->get("maxevents", 10);
		}

		if (!$filters)
		{
			$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "search", "repeating"));
			$filters->setWhereJoin($extrawhere, $extrajoin);
			$needsgroup = $filters->needsGroupBy();

			$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));
		}
		else
		{
			$filters->setWhereJoin($extrawhere, $extrajoin);
		}

		$accessibleCategories = $this->accessibleCategoryList();
		$catwhere             = "\n WHERE ev.catid IN(" . $accessibleCategories . ")";
		$params               = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $accessibleCategories . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		if ($limit > 0 || $limitstart > 0)
		{
			if (empty($limitstart) || !$limitstart)
			{
				$limitstart = 0;
			}

			$rows_per_page = $limit;
			$limit         = " LIMIT $limitstart, $rows_per_page";
		}
		else
		{
			$limit = "";
		}

		if ($order != "")
		{
			$order = (strpos($order, 'ORDER BY') === false ? " ORDER BY " : " ") . $order;
		}

		$user = Factory::getUser();
		if ($showrepeats)
		{
			$query = "SELECT ev.*, rpt.*, rr.*, det.* $extrafields"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_vevent as ev"
				. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n INNER JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $extrajoin
				//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
				. $catwhere
				. $extrawhere
				//. "\n AND ev.state=1"
				. "\n  AND icsf.state=1"
				. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
				. "\n GROUP BY rpt.rp_id"
				. $order
				. $limit;
		}
		else
		{
			// TODO find a single query way of doing this !!!
			$query = "SELECT MIN(rpt.rp_id) as rp_id FROM #__jevents_repetition as rpt "
				. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n INNER JOIN #__jevents_icsfile as icsf  ON icsf.ics_id=ev.icsid"
				. $extrajoin
				. $catwhere
				. $extrawhere
				. "\n  AND icsf.state=1"
				. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
				. "\n GROUP BY ev.ev_id";

			$db->setQuery($query);
			//echo $db->explain();

			$rplist = $db->loadColumn();

			$rplist = implode(',', array_merge(array(-1), $rplist));

			$query = "SELECT rpt.rp_id,ev.*, rpt.*, rr.*, det.* $extrafields"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_repetition as rpt  "
				. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $extrajoin
				//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
				. $catwhere
				. $extrawhere
				//. "\n AND ev.state=1"
				. "\n AND rpt.rp_id IN($rplist)"
				. "\n  AND icsf.state=1"
				. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
				. ($needsgroup ? "\n GROUP BY rpt.rp_id" : "")
				. $order
				. $limit;
		}

		$cache   = JEVHelper::getCache(JEV_COM_COMPONENT);
		$lang    = Factory::getLanguage();
		$langtag = $lang->getTag();

		if (version_compare(JVERSION, '4.0.0', 'ge'))
		{
			$rows = $cache->get(array($this, '_cachedlistIcalEvents'), array($query, $langtag));
		}
		else
		{
			$rows = $cache->call(array($this, '_cachedlistIcalEvents'), $query, $langtag);
		}

       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
        $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$rows));
       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		return $rows;

	}

	function countIcalEventsByCat($catids, $showrepeats = false)
	{

		$db   = Factory::getDbo();
		$user = Factory::getUser();
		$app  = Factory::getApplication();

		// Use catid in accessibleCategoryList to pick up offsping too!
		$aid       = null;
		$catidlist = implode(",", $catids);

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$extrawhere  = array();
		$extrajoin   = array();
		$needsgroup  = false;

		if (!$this->cfg->get("showyearpast", 1))
		{
			list($year, $month, $day) = JEVHelper::getYMD();
			$startdate    = JevDate::mktime(0, 0, 0, $month, $day, $year);
			$startdate    = JevDate::strftime('%Y-%m-%d 00:00:00', $startdate);
			$extrawhere[] = "rpt.endrepeat >=  '$startdate'";
		}

		$filters = jevFilterProcessing::getInstance(array("published", "justmine", "category", "search", "repeating"));
		$filters->setWhereJoin($extrawhere, $extrajoin);
		$needsgroup = $filters->needsGroupBy();

		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix

		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$accessibleCategories = $this->accessibleCategoryList();
		$catwhere             = "\n WHERE ev.catid IN(" . $accessibleCategories . ")";
		$params               = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $accessibleCategories . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		// Get the count
		if ($showrepeats)
		{
			$query = "SELECT count(DISTINCT rpt.rp_id) as cnt"
				. "\n FROM #__jevents_vevent as ev "
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $extrajoin
				//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
				. $catwhere
				. "\n AND icsf.state=1"
				. $extrawhere//. "\n AND ev.state=1"
			;
		}
		else
		{
			// TODO fine a single query way of doing this !!!
			$query = "SELECT MIN(rpt.rp_id) as rp_id FROM #__jevents_repetition as rpt "
				. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n INNER JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n INNER JOIN #__jevents_icsfile as icsf  ON icsf.ics_id=ev.icsid "
				. $extrajoin
				//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
				. $catwhere
				. $extrawhere
				//. "\n AND ev.state=1"
				. "\n AND icsf.state=1"
				. "\n GROUP BY ev.ev_id";

			$db->setQuery($query);

			$rplist = $db->loadColumn();

			$rplist = implode(',', array_merge(array(-1), $rplist));

			$query = "SELECT count(DISTINCT det.evdet_id) as cnt"
				. "\n FROM #__jevents_vevent as ev "
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n AND rpt.rp_id IN($rplist)"
				. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. $extrajoin
				//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
				. $catwhere
				. "\n AND icsf.state=1"
				. $extrawhere
				//. "\n AND ev.state=1"
				//. ($needsgroup?"\n GROUP BY rpt.rp_id":"")
			;
		}

		try
		{
			$db->setQuery($query);
		}
		catch (Throwable $e)
		{
			if (JDEBUG)
			{
				echo "Problems in countIcalEventsByCat line 4735<br>";
				echo (string) $query . "<br>";
				exit();
			}
		}
		//echo $db->_sql;
		//echo $db->explain();
		$total = intval($db->loadResult());

		if ($this->cfg->get("maxevents", 0) > 0)
		{
			$total = $total > $this->cfg->get("maxevents", 0) ? $this->cfg->get("maxevents", 0) : $total;
		}

		return $total;

	}

	// NB $order is no longer used
	function listEventsByKeyword($keyword, $order, &$limit, &$limitstart, &$total, $useRegX = false)
	{

		$app       = Factory::getApplication();
		$input     = $app->input;
		$user      = Factory::getUser();
		$adminuser = JEVHelper::isAdminUser($user);
		$db        = Factory::getDbo();

		$params   = ComponentHelper::getParams("com_jevents");
		$this->daymultiday             = intval($params->get('daymultiday', 0));

        $lang    = Factory::getLanguage();
        $langtag = $lang->getTag();

		$keyword = $db->escape($keyword, true);

		// Use alternative data source
		$rows        = array();
		$skipJEvents = false;
		$app->triggerEvent('fetchListEventsByKeyword', array(&$skipJEvents, &$rows, $keyword, $order, &$limit, &$limitstart, &$total, $useRegX));

		if ($skipJEvents)
		{
			return $rows;
		}

		$rows_per_page = $limit;
		if (empty($limitstart) || !$limitstart)
		{
			$limitstart = 0;
		}

		$limitstring = "";
		if ($rows_per_page > 0)
		{
			$limitstring = "LIMIT $limitstart, $rows_per_page";
		}

		$where  = "";
		$having = "";
		if (!$input->getInt('showpast', 0))
		{
			$datenow = JevDate::getDate("-12 hours");
			$having  = " AND rpt.endrepeat>'" . $datenow->toSql() . "'";
		}

		$total = 0;

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere  = array();
		$extrajoin   = array();
		$extratables = array();
		$extrafields = "";  // must have comma prefix
		$needsgroup  = false;

		$filterarray = array("published");
		// If there are extra filters from the module then apply them now
		$reg       = Factory::getConfig();
		$modparams = $reg->get("jev.modparams", false);
		if ($modparams && $modparams->get("extrafilters", false))
		{
			$filterarray = array_merge($filterarray, explode(",", $modparams->get("extrafilters", false)));
		}

		$filters = jevFilterProcessing::getInstance($filterarray);
		$filters->setWhereJoin($extrawhere, $extrajoin);
		$needsgroup = $filters->needsGroupBy();

		PluginHelper::importPlugin('jevents');

		$app->triggerEvent('onListIcalEvents', array(& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$params   = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin[] = "\n #__jevents_catmap as catmap ON catmap.evid = rpt.eventid";
			$extrajoin[] = "\n #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrafields .= ", GROUP_CONCAT(DISTINCT catmapcat.id ORDER BY catmapcat.lft ASC SEPARATOR ',' ) as catids";
			// accessibleCategoryList handles access checks on category
			//$extrawhere[] = " catmapcat.access IN (" . JEVHelper::getAid($user) . ")";
			$extrawhere[] = " catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$needsgroup   = true;
			$catwhere     = "\n WHERE 1 ";
		}

        $extrajoin[] = "\n #__jevents_translation as trans ON trans.evdet_id = det.evdet_id AND trans.language=" . $db->quote($langtag);

		$extrajoin  = (count($extrajoin) ? " \n LEFT JOIN " . implode(" \n LEFT JOIN ", $extrajoin) : '');
		$extrawhere = (count($extrawhere) ? ' AND ' . implode(' AND ', $extrawhere) : '');

		// NB extrajoin is a string from now on
		$extrasearchfields = array();
		$app->triggerEvent('onSearchEvents', array(& $extrasearchfields, & $extrajoin, & $needsgroup));

        if ($useRegX)
        {
            $extrasearchfields[] = "trans.summary RLIKE '$keyword' OR trans.description RLIKE '$keyword' OR trans.extra_info RLIKE '$keyword'";
        }
        else
        {
            $extrasearchfields[] = " MATCH (trans.summary, trans.description, trans.extra_info) AGAINST ('$keyword' IN BOOLEAN MODE)";
        }

		if (count($extrasearchfields) > 0)
		{
			$extraor = implode(" OR ", $extrasearchfields);
			$extraor = " OR " . $extraor;
			// replace the ### placeholder with the keyword
			$extraor = str_replace("###", $keyword, $extraor);

			$searchpart = ($useRegX) ? "(det.summary RLIKE '$keyword' OR det.description RLIKE '$keyword' OR det.location RLIKE '$keyword' OR det.extra_info RLIKE '$keyword' $extraor)\n" :
				" (MATCH (det.summary, det.description, det.extra_info) AGAINST ('$keyword' IN BOOLEAN MODE) $extraor)\n";
		}
		else
		{
			$searchpart = ($useRegX) ? "(det.summary RLIKE '$keyword' OR det.description RLIKE '$keyword'  OR det.location RLIKE '$keyword'  OR det.extra_info RLIKE '$keyword')\n" :
				"MATCH (det.summary, det.description, det.extra_info) AGAINST ('$keyword' IN BOOLEAN MODE)\n";
		}

		// Now Search Icals
		$query = "SELECT count( distinct det.evdet_id) FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. $extrajoin
			. $catwhere
			. "\n AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")"
			. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")"
			. "\n AND ";
		$query .= $searchpart;
		$query .= $extrawhere;
		$query .= $having;
		$db->setQuery($query);
		//echo (string) $db->getQuery();
		$total += intval($db->loadResult());

		if ($total < $limitstart)
		{
			$limitstart = 0;
		}

		$rows = array();
		if ($total == 0)
			return $rows;

		// Now Search Icals
		// New version
		$query = "SELECT DISTINCT det.evdet_id FROM  #__jevents_vevdetail as det"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventdetail_id = det.evdet_id"
			. "\n LEFT JOIN #__jevents_vevent as ev ON ev.ev_id = rpt.eventid"
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. $extrajoin
			. $catwhere
			. "\n  AND icsf.state=1 AND icsf.access IN (" . JEVHelper::getAid($user) . ")"
			. "\n AND ev.access IN (" . JEVHelper::getAid($user) . ")";
		$query .= " AND ";
		$query .= $searchpart;
		$query .= $extrawhere;
		$query .= $having;
		$query .= "\n ORDER BY rpt.startrepeat ASC ";
		$query .= "\n $limitstring";

		$db->setQuery($query);
		if ($adminuser)
		{
			//echo $db->_sql;
			//echo $db->explain();
		}
		//echo $db->explain();

		$details = $db->loadColumn();

		$icalrows = array();
		foreach ($details as $detid)
		{
			$query2 = "SELECT ev.*, rpt.*, det.* $extrafields"
				. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
				. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
				. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
				. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
				. "\n FROM #__jevents_vevent as ev"
				. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
				. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
				. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
				. $extrajoin
				. "\n WHERE rpt.eventdetail_id = $detid"
				. $extrawhere
				. $having
				. ($needsgroup ? "\n GROUP BY rpt.rp_id" : "")
				. "\n ORDER BY rpt.startrepeat ASC limit 1";
			$db->setQuery($query2);
			//echo $db->explain();
			$data = $db->loadObject();
			// belts and braces - some servers have a MYSQLK bug on the  user of DISTINCT!
			if (!$data->ev_id)
				continue;
			$icalrows[] = $data;
		}

		$num_events = count($icalrows);

		for ($i = 0; $i < $num_events; $i++)
		{
			// convert rows to jevents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}

		JEventsDBModel::translateEvents($icalrows);

		JEVHelper::onDisplayCustomFieldsMultiRow($icalrows);

       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel before onDisplayCustomFieldsMultiRowUncached');
        $app->triggerEvent('onDisplayCustomFieldsMultiRowUncached', array(&$icalrows));
       // !JDEBUG ?: Profiler::getInstance('Application')->mark('dbmodel after onDisplayCustomFieldsMultiRowUncached');

		return $icalrows;

	}

	function sortEvents($a, $b)
	{

		list($adate, $atime) = explode(' ', $a->publish_up);
		list($bdate, $btime) = explode(' ', $b->publish_up);

		return strcmp($atime, $btime);

	}

	function sortJointEvents($a, $b)
	{

		$adatetime = $a->getUnixStartTime();
		$bdatetime = $b->getUnixStartTime();
		if ($adatetime == $bdatetime)
			return 0;

		return ($adatetime > $bdatetime) ? -1 : 1;

	}

	function findMatchingRepeat($uid, $year, $month, $day)
	{

		$start = $year . '/' . $month . '/' . $day . ' 00:00:00';
		$end   = $year . '/' . $month . '/' . $day . ' 23:59:59';

		$db    = Factory::getDbo();
		$query = "SELECT ev.*, rpt.* "
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n WHERE ev.uid = " . $db->Quote($uid)
			. "\n AND rpt.startrepeat>=" . $db->Quote($start) . " AND rpt.startrepeat<=" . $db->Quote($end);

		$db->setQuery($query);
		//echo $db->_sql;
		$rows = $db->loadObjectList();
		if (count($rows) > 0)
		{
			return $rows[0]->rp_id;
		}

		// still no match so find the nearest repeat and give a message.
		$db    = Factory::getDbo();
		$query = "SELECT ev.*, rpt.*, abs(datediff(rpt.startrepeat," . $db->Quote($start) . ")) as diff "
			. "\n FROM #__jevents_repetition as rpt"
			. "\n INNER JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n WHERE ev.uid = " . $db->Quote($uid)
			. "\n ORDER BY diff asc LIMIT 3";

		$db->setQuery($query);
		//echo $db->_sql;
		$rows = $db->loadObjectList();
		if (count($rows) > 0)
		{
			Factory::getApplication()->enqueueMessage(Text::_('THIS_EVENT_HAS_CHANGED_THIS_OCCURANCE_IS_NOW_THE_CLOSEST_TO_THE_DATE_YOU_SEARCHED_FOR'), 'notice');

			return $rows[0]->rp_id;
		}

	}

	function setMultiCategory(&$row, $accessibleCategories)
	{

		// check multi-category access
		// do not use jev_com_component incase we call this from locations etc.
		$params = ComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$db = Factory::getDbo();
			// get list of categories this event is in - are they all accessible?
			$db->setQuery("SELECT catid FROM #__jevents_catmap WHERE evid=" . $row->ev_id);
			$catids = $db->loadColumn();
			// backward compatbile
			if (!$catids)
			{
				return true;
			}

			// are there any catids not in list of accessible Categories
			$inaccessiblecats = array_diff($catids, explode(",", $accessibleCategories));
			if (count($inaccessiblecats))
			{
				return null;
			}
			$row->catids = $catids;
		}

		return true;

	}

}
