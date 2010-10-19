<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

// load language constants
JEVHelper::loadLanguage('front');

class JEventsDBModel {
	var $cfg = null;
	var $datamodel = null;
	var $legacyEvents = null;

	function JEventsDBModel(&$datamodel){
		$this->cfg = & JEVConfig::getInstance();
		// TODO - remove legacy code
		$this->legacyEvents = 0;

		$this->datamodel =& $datamodel;

	}

	function accessibleCategoryList($aid=null, $catids=null, $catidList=null) {
		if (is_null($aid)) {
			$aid = $this->datamodel->aid;
		}
		if (is_null($catids)) {
			$catids = $this->datamodel->catids;
		}
		if (is_null($catidList)) {
			$catidList = $this->datamodel->catidList;
		}

		$cfg = & JEVConfig::getInstance();
		$sectionname = JEV_COM_COMPONENT;

		static $instances;

		if (!$instances) {
			$instances = array();
		}
		// calculate unique index identifier
		$index = $aid . '+' . $catidList;
		// if catidList = 0 then the result is the same as a blank so slight time saving
		if (is_null($catidList) || $catidList==0) {
			$index = $aid . '+';
		}

		$db	=& JFactory::getDBO();

		$where = "";

		if (!array_key_exists($index,$instances)) {
			if (JVersion::isCompatible("1.6.0")){
				//jimport("joomla.application.categories");
				//$cats = JCategories::getInstance("jevents");
				//$catlist = $cats->get();

				$catids = explode(",",$catidList);
				$catwhere = array();
				foreach ($catids as $catid){
					$catid = intval($catid);
					if ($catid>0){
						$catwhere[] =	"(c.lft<=".$catid." AND c.rgt>=".$catid.")";
					}
				}
				if (count($catwhere)>0){
					$where = "AND (".implode(" OR ",$catwhere).")";
				}

				$q_published = JFactory::getApplication()->isAdmin() ? "\n AND c.published >= 0" : "\n AND c.published = 1";
				$query = "SELECT c.id"
				. "\n FROM #__categories AS c"
				. "\n WHERE c.access  " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . $aid . ')' : ' <=  ' . $aid)
				. $q_published
				. "\n AND c.extension = '".$sectionname."'"
				. "\n " . $where;
				;

				$db->setQuery($query);
				$catlist =  $db->loadResultArray();

				$instances[$index] = implode(',', array_merge(array(-1), $catlist));


			}
			else {
				if (count($catids)>0 && !is_null($catidList) && $catidList!="0") {
					$where = ' AND (c.id IN (' . $catidList .') OR p.id IN (' . $catidList .')  OR gp.id IN (' . $catidList .') OR ggp.id IN (' . $catidList .'))';
				}

				$q_published = JFactory::getApplication()->isAdmin() ? "\n AND c.published >= 0" : "\n AND c.published = 1";
				$query = "SELECT c.id"
				. "\n FROM #__categories AS c"
				. ' LEFT JOIN #__categories AS p ON p.id=c.parent_id'
				. ' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id '
				. ' LEFT JOIN #__categories AS ggp ON ggp.id=gp.parent_id '
				. "\n WHERE c.access " .( version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . $aid . ')' : ' <=  ' . $aid)
				. $q_published
				. "\n AND c.section = '".$sectionname."'"
				. "\n " . $where;
				;

				$db->setQuery($query);
				$catlist =  $db->loadResultArray();

				$instances[$index] = implode(',', array_merge(array(-1), $catlist));

			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onGetAccessibleCategories', array (& $instances[$index]));
			
			}
		}
		return $instances[$index];
	}

	function getCategoryInfo($catids=null,$aid=null){
		
		$db	=& JFactory::getDBO();
		if (is_null($aid)) {
			$aid = $this->datamodel->aid;
		}
		if (is_null($catids)) {
			$catids = $this->datamodel->catids;
		}

		$catidList = implode(",", $catids);

		$cfg = & JEVConfig::getInstance();
		$sectionname = JEV_COM_COMPONENT;

		static $instances;

		if (!$instances) {
			$instances = array();
		}

		// calculate unique index identifier
		$index = $aid . '+' . $catidList;
		$where = null;

		if (!array_key_exists($index,$instances)) {
			if (count($catids)>0 && $catidList!="0" && strlen($catidList)!="") {
				$where = ' AND c.id IN (' . $catidList .') ';
			}

			$q_published = JFactory::getApplication()->isAdmin() ? "\n AND c.published >= 0" : "\n AND c.published = 1";
			$query = "SELECT c.*"
			. "\n FROM #__categories AS c"
			. "\n WHERE c.access " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . $aid . ')' : ' <=  ' . $aid)
			. $q_published
			. "\n AND c.section = '".$sectionname."'"
			. "\n " . $where;
			;

			$db->setQuery($query);
			$catlist =  $db->loadObjectList('id');

			$instances[$index] =  $catlist;
		}
		return $instances[$index];

	}

	function getChildCategories($catids=null,$levels=1,$aid=null){
		
		$db	=& JFactory::getDBO();
		if (is_null($aid)) {
			$aid = $this->datamodel->aid;
		}
		if (is_null($catids)) {
			$catids = $this->datamodel->catids;
		}

		$catidList = implode(",", $catids);

		$cfg = & JEVConfig::getInstance();
		$sectionname = JEV_COM_COMPONENT;

		static $instances;

		if (!$instances) {
			$instances = array();
		}

		// calculate unique index identifier
		$index = $aid . '+' . $catidList;
		$where = null;

		if (!array_key_exists($index,$instances)) {
			if (count($catids)>0 && $catidList!="0"  && strlen($catidList)!="") {
				$where = ' AND (p.id IN (' . $catidList .') '.($levels>1?' OR gp.id IN (' . $catidList .')':'').($levels>2?' OR ggp.id IN (' . $catidList .')':'').')';
			}
			// TODO check if this should also check abncestry based on $levels
			$where .= ' AND p.id IS NOT NULL ';

			$q_published = JFactory::getApplication()->isAdmin() ? "\n AND c.published >= 0" : "\n AND c.published = 1";
			$query = "SELECT c.*"
			. "\n FROM #__categories AS c"
			. ' LEFT JOIN #__categories AS p ON p.id=c.parent_id'
			. ($levels>1?' LEFT JOIN #__categories AS gp ON gp.id=p.parent_id ':'')
			. ($levels>2?' LEFT JOIN #__categories AS ggp ON ggp.id=gp.parent_id ':'')
			. "\n WHERE c.access " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . $aid . ')' : ' <=  ' . $aid)
			. $q_published
			. "\n AND c.section = '".$sectionname."'"
			. "\n " . $where;
			;


			$db->setQuery($query);
			$catlist =  $db->loadObjectList('id');

			$instances[$index] =  $catlist;
		}
		return $instances[$index];

	}

	function listEvents( $startdate, $enddate, $order=""){
		if (!$this->legacyEvents) {
			return array();
		}
	}

	function _cachedlistEvents($query, $langtag,$count=false){
		$db	=& JFactory::getDBO();
		$db->setQuery( $query );
		if ($count){
			$db->query();
			return $db->getNumRows();
		}

		$rows = $db->loadObjectList();
		$rowcount = count($rows);
		if ($rowcount>0) {
			usort( $rows, array('JEventsDBModel','sortEvents') );
		}

		for( $i = 0; $i < $rowcount; $i++ ){
			$rows[$i] = new jEventCal($rows[$i]);
		}
		return $rows;
	}

	/**
	 * Fetch recently created events
	 */
	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	function recentIcalEvents($startdate,$enddate, $limit=10, $noRepeats=0){
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		$lang =& JFactory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate,"-")===false) {
			$startdate = strftime('%Y-%m-%d 00:00:00',$startdate);
			$enddate = strftime('%Y-%m-%d 23:59:59',$enddate);
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere =array();
		$extrajoin = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup = false;

			$filterarray = array("published","justmine","category","search");

			// If there are extra filters from the module then apply them now
			$reg =& JFactory::getConfig();
			$modparams = $reg->getValue("jev.modparams",false);
			if ($modparams && $modparams->getValue("extrafilters",false)){
				$filterarray  = array_merge($filterarray, explode(",",$modparams->getValue("extrafilters",false)));
			}

			$filters = jevFilterProcessing::getInstance($filterarray);
			$filters->setWhereJoin($extrawhere,$extrajoin );
			$needsgroup = $filters->needsGroupBy();

			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );
		
		// get the event ids first
		$query = "SELECT  ev.ev_id FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		. "\n AND ev.created >= '$startdate' AND ev.created <= '$enddate'"
		. $extrawhere
		. "\n AND ev.access <= ".$user->aid
		. "  AND icsf.state=1 AND icsf.access <= ".$user->aid
		// published state is now handled by filter
		. "\n GROUP BY ev.ev_id";

		// always in reverse created date order!
		$query .= " ORDER BY ev.created DESC ";

		// This limit will always be enough
		$query .= " LIMIT ".$limit;


		$db = JFactory::getDBO();
		$db->setQuery($query);
		$ids = $db->loadResultArray();
		array_push($ids,0);
		$ids =  implode(",",$ids);

		$groupby = "\n GROUP BY rpt.rp_id";
		if ($noRepeats) $groupby = "\n GROUP BY ev.ev_id";
		
		// This version picks the details from the details table
		// ideally we should check if the event is a repeat but this involves extra queries unfortunately
		$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
		. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
		. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
		. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
		. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
		. "\n FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		. "\n AND ev.created >= '$startdate' AND ev.created <= '$enddate'"

		. $extrawhere
		. "\n AND ev.access <= ".$user->aid
		. "  AND icsf.state=1 AND icsf.access <= ".$user->aid
		. "  AND ev.ev_id IN (".$ids.")"
		// published state is now handled by filter
		//. "\n AND ev.state=1"
		. ($needsgroup?$groupby:"");
		$query .= " ORDER BY ev.created DESC ";

		$cache=& JFactory::getCache(JEV_COM_COMPONENT);
		$rows =  $cache->call('JEventsDBModel::_cachedlistIcalEvents', $query, $langtag );

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onDisplayCustomFieldsMultiRowUncached', array( &$rows ));

		return $rows;
		}

	/* Special version for Latest events module */
	function listLatestIcalEvents($startdate,$enddate, $limit=10, $noRepeats=0){
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		$lang =& JFactory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate,"-")===false) {
			$startdate = strftime('%Y-%m-%d 00:00:00',$startdate);
			$enddate = strftime('%Y-%m-%d 23:59:59',$enddate);
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere =array();
		$extrajoin = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup = false;

		$filterarray = array("published","justmine","category","search");

		// If there are extra filters from the module then apply them now
		$reg =& JFactory::getConfig();
		$modparams = $reg->getValue("jev.modparams",false);
		if ($modparams && $modparams->getValue("extrafilters",false)){
			$filterarray  = array_merge($filterarray, explode(",",$modparams->getValue("extrafilters",false)));
		}

		$filters = jevFilterProcessing::getInstance($filterarray);
		$filters->setWhereJoin($extrawhere,$extrajoin );
		$needsgroup = $filters->needsGroupBy();

		$dispatcher	=& JDispatcher::getInstance();
		$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		// What if join multiplies the rows?
		// Useful MySQL link http://forums.mysql.com/read.php?10,228378,228492#msg-228492
		// concat with group
		// http://www.mysqlperformanceblog.com/2006/09/04/group_concat-useful-group-by-extension/

		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		// get the event ids first - split into 2 queries to pick up the ones after now and the ones before 
		$t_datenow = JEVHelper::getNow();
		$t_datenowSQL =  $t_datenow->toMysql();		
		
		/*
		$query = "SELECT  ev.ev_id FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		// New equivalent but simpler test
		. "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'"
		// We only show events on their first day if they are not to be shown on multiple days so also add this condition
		. "\n AND ((rpt.startrepeat >= '$startdate' AND det.multiday=0) OR  det.multiday=1)"		
		. $extrawhere
		. "\n AND ev.access <= ".$user->aid
		. "  AND icsf.state=1 AND icsf.access <= ".$user->aid
		// published state is now handled by filter
		. "\n GROUP BY ev.ev_id";

		// This limit will always be enough
		$query .= " LIMIT ".$limit;
		*/
		
		// Find the ones after now
		$query = "SELECT DISTINCT rpt.eventid FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		// New equivalent but simpler test
		. "\n AND rpt.endrepeat >= '$t_datenowSQL' AND rpt.startrepeat <= '$enddate'"
		// We only show events on their first day if they are not to be shown on multiple days so also add this condition
		. "\n AND ((rpt.startrepeat >= '$t_datenowSQL' AND det.multiday=0) OR  det.multiday=1)"		
		. $extrawhere
		. "\n AND ev.access <= ".$user->aid
		. "  AND icsf.state=1 AND icsf.access <= ".$user->aid
		// published state is now handled by filter
		. "\n AND rpt.startrepeat=(SELECT MIN(startrepeat) FROM #__jevents_repetition as rpt2 WHERE rpt2.eventid=rpt.eventid AND rpt2.startrepeat >= '$t_datenowSQL' AND rpt2.endrepeat <= '$enddate')"
		//. "\n GROUP BY rpt.eventid"
		. "\n ORDER BY rpt.startrepeat ASC"
		;

		// This limit will always be enough
		$query .= " LIMIT ".$limit;		

		$db = JFactory::getDBO();
		$db->setQuery($query);
		//echo $db->explain();
		//echo $db->_sql."<br/>";		
		$ids1 = $db->loadResultArray();
				
		// Before now
		$query = "SELECT rpt.eventid  FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		// New equivalent but simpler test
		. "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$t_datenowSQL'"
		// We only show events on their first day if they are not to be shown on multiple days so also add this condition
		. "\n AND ((rpt.startrepeat >= '$startdate' AND det.multiday=0) OR  det.multiday=1)"		
		. $extrawhere
		. "\n AND ev.access <= ".$user->aid
		. "  AND icsf.state=1 AND icsf.access <= ".$user->aid
		// published state is now handled by filter
		. "\n AND rpt.startrepeat=(SELECT MAX(startrepeat) FROM #__jevents_repetition as rpt2 WHERE rpt2.eventid=rpt.eventid AND rpt2.startrepeat <= '$t_datenowSQL' AND rpt2.startrepeat >= '$startdate')"
 		. "\n GROUP BY rpt.eventid "
		. "\n ORDER BY rpt.startrepeat DESC"
		;

		// This limit will always be enough
		$query .= " LIMIT ".$limit;		

		$db = JFactory::getDBO();
		$db->setQuery($query);
		//echo $db->explain();die();
		//echo $db->_sql;		die();
		$ids2 = $db->loadResultArray();
		
		$ids = array_merge($ids1, $ids2);
		array_push($ids,0);
		$ids = array_unique($ids);
		
		// As an alternative to avoid the temporary table  we could use php array_unique and array_slice to get the list of ids - with no memory issues.
		
		$ids =  implode(",",$ids);

		$groupby = "\n GROUP BY rpt.rp_id";
		if ($noRepeats) $groupby = "\n GROUP BY ev.ev_id";
		
		// This version picks the details from the details table
		// ideally we should check if the event is a repeat but this involves extra queries unfortunately
		$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
		. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
		. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
		. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
		. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
		. "\n FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		// New equivalent but simpler test
		. "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'"

		. $extrawhere
		. "\n AND ev.access  " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' . JEVHelper::getAid($user))
		. "  AND icsf.state=1 AND icsf.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		. "  AND ev.ev_id IN (".$ids.")"
		// published state is now handled by filter
		. ($needsgroup?$groupby:"");
		;

		$cache=& JFactory::getCache(JEV_COM_COMPONENT);

		$rows =  $cache->call('JEventsDBModel::_cachedlistIcalEvents', $query, $langtag );

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onDisplayCustomFieldsMultiRowUncached', array( &$rows ));

		return $rows;
	}

	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	function listIcalEvents($startdate,$enddate, $order="", $filters = false, $extrafields="", $extratables="", $limit=""){
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		$lang =& JFactory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate,"-")===false) {
			$startdate = strftime('%Y-%m-%d 00:00:00',$startdate);
			$enddate = strftime('%Y-%m-%d 23:59:59',$enddate);
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere =array();
		$extrajoin = array();
		//		$extrafields = "";  // must have comma prefix
		//		$extratables = "";  // must have comma prefix
		$needsgroup = false;

		if (!$filters){
			$filterarray = array("published","justmine","category","search");

			// If there are extra filters from the module then apply them now
			$reg =& JFactory::getConfig();
			$modparams = $reg->getValue("jev.modparams",false);
			if ($modparams && $modparams->getValue("extrafilters",false)){
				$filterarray  = array_merge($filterarray, explode(",",$modparams->getValue("extrafilters",false)));
			}

			$filters = jevFilterProcessing::getInstance($filterarray);
			$filters->setWhereJoin($extrawhere,$extrajoin );
			$needsgroup = $filters->needsGroupBy();

			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

			// What if join multiplies the rows?
			// Useful MySQL link http://forums.mysql.com/read.php?10,228378,228492#msg-228492
			// concat with group
			// http://www.mysqlperformanceblog.com/2006/09/04/group_concat-useful-group-by-extension/

		}
		else {
			$filters->setWhereJoin($extrawhere,$extrajoin);
		}

		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		// This version picks the details from the details table
		// ideally we should check if the event is a repeat but this involves extra queries unfortunately
		$query = "SELECT rpt.*, ev.*, rr.*, det.*, ev.state as published, ev.created as created $extrafields"
		. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
		. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
		. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
		. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
		. "\n FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		// New equivalent but simpler test
		. "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'"
		/*
		. "\n AND ((rpt.startrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate')"
		. "\n OR (rpt.endrepeat >= '$startdate' AND rpt.endrepeat <= '$enddate')"
		// This is redundant!!
		//. "\n OR (rpt.startrepeat >= '$startdate' AND rpt.endrepeat <= '$enddate')"
		// This slows the query down
		. "\n OR (rpt.startrepeat <= '$startdate' AND rpt.endrepeat >= '$enddate')"
		. "\n )"
		*/
		// Radical alternative - seems slower though
		/*
		. "\n WHERE rpt.rp_id IN (SELECT  rbd.rp_id
		FROM jos_jevents_repbyday as rbd
		WHERE  rbd.catid IN(".$this->accessibleCategoryList().")
		AND rbd.rptday >= '$startdate' AND rbd.rptday <= '$enddate' )"
		*/

		. $extrawhere
		. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		. "  AND icsf.state=1 AND icsf.access " .(version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		// published state is now handled by filter
		//. "\n AND ev.state=1"
		. ($needsgroup?"\n GROUP BY rpt.rp_id":"")
		;

		if ($order !="") {
			$query .= " ORDER BY ".$order;
		}
		if ($limit !="") {
			$query .= " LIMIT ".$limit;
		}

		$cache=& JFactory::getCache(JEV_COM_COMPONENT);
		$rows =  $cache->call('JEventsDBModel::_cachedlistIcalEvents', $query, $langtag );

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onDisplayCustomFieldsMultiRowUncached', array( &$rows ));

		return $rows;

	}

	function _cachedlistIcalEvents($query, $langtag,$count=false){
		$db	=& JFactory::getDBO();
		$db->setQuery( $query );
		$user = JFactory::getUser();
		if (JEVHelper::isAdminUser($user)){
		//	echo $db->getQuery();
			//echo $db->explain();
		}
		//echo $db->_sql;
		if ($count){
			$db->query();
			return $db->getNumRows();
		}

		$icalrows = $db->loadObjectList();
		if (JEVHelper::isAdminUser($user)){
			echo $db->getErrorMsg();
		}
		$icalcount = count($icalrows);
		for( $i = 0; $i < $icalcount ; $i++ ){
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onDisplayCustomFieldsMultiRow', array( &$icalrows ));

		return $icalrows;
	}

	function listEventsByDateNEW( $select_date ){
		return $this->listEvents($select_date." 00:00:00",$select_date." 23:59:59");
	}

	function listIcalEventsByDay($targetdate){
		// targetdate is midnight at start of day - but just in case
		list ($y,$m,$d) =	explode(":",strftime( '%Y:%m:%d',$targetdate));
		$startdate 	= mktime( 0, 0, 0, $m, $d, $y );
		$enddate 	= mktime( 23, 59, 59, $m, $d, $y );
		return $this->listIcalEvents($startdate,$enddate);
	}

	function listEventsByWeekNEW( $weekstart, $weekend){
		return $this->listEvents($weekstart, $weekend);
	}

	function listIcalEventsByWeek( $weekstart, $weekend){
		return $this->listIcalEvents( $weekstart, $weekend);
	}

	function listEventsByMonthNew( $year, $month, $order){
		$db	=& JFactory::getDBO();

		$month = str_pad($month, 2, '0', STR_PAD_LEFT);
		$select_date 		= $year.'-'.$month.'-01 00:00:00';
		$select_date_fin 	= $year.'-'.$month.'-'.date('t',mktime(0,0,0,($month+1),0,$year)).' 23:59:59';

		return $this->listEvents($select_date,$select_date_fin,$order);
	}

	function listIcalEventsByMonth( $year, $month){
		$startdate 	= mktime( 0, 0, 0,  $month,  1, $year );
		$enddate 	= mktime( 23, 59, 59,  $month, date( 't', $startdate), $year );
		return $this->listIcalEvents($startdate,$enddate,"");
	}

	function listEventsByYearNEW( $year, $limitstart=0, $limit=0 ) {
		if (!$this->legacyEvents) {
			return array();
		}
	}

	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	function listIcalEventsByYear( $year, $limitstart, $limit, $showrepeats = true, $order="", $filters = false, $extrafields="", $extratables="", $count=false) {
		list($xyear,$month,$day) = JEVHelper::getYMD();
		$thisyear = new JDate("+0 seconds");
		list($thisyear,$thismonth,$thisday) = explode("-",$thisyear->toFormat("%Y-%m-%d"));
		if (!$this->cfg->getValue("showyearpast",1) && $year<$thisyear){
			return array();
		}
		$startdate 	= ($this->cfg->getValue("showyearpast",1) || $year>$thisyear)?mktime( 0, 0, 0, 1, 1, $year ):mktime( 0, 0, 0,$thismonth,$thisday, $thisyear );
		$enddate 	= mktime( 23, 59, 59, 12, 31, $year );
		if (!$count){
			$order = "rpt.startrepeat asc";
		}

		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		$lang =& JFactory::getLanguage();
		$langtag = $lang->getTag();

		if (strpos($startdate,"-")===false) {
			$startdate = strftime('%Y-%m-%d 00:00:00',$startdate);
			$enddate = strftime('%Y-%m-%d 23:59:59',$enddate);
		}

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere =array();
		$extrajoin = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup = false;

		if (!$filters){
			$filters = jevFilterProcessing::getInstance(array("published","justmine","category","search"));
			$filters->setWhereJoin($extrawhere,$extrajoin);
			$needsgroup = $filters->needsGroupBy();

			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));
		}
		else {
			$filters->setWhereJoin($extrawhere,$extrajoin);
		}
		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		// This version picks the details from the details table
		if ($count){
			$query = "SELECT rpt.rp_id";
		}
		else {
			$query = "SELECT ev.*, rpt.*, rr.*, det.*, ev.state as published $extrafields"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn";
		}
		$query .= "\n FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		// New equivalent but simpler test
		. "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'"
		/*
		. "\n AND ((rpt.startrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate')"
		. "\n OR (rpt.endrepeat >= '$startdate' AND rpt.endrepeat <= '$enddate')"
		//. "\n OR (rpt.startrepeat >= '$startdate' AND rpt.endrepeat <= '$enddate')"
		. "\n OR (rpt.startrepeat <= '$startdate' AND rpt.endrepeat >= '$enddate'))"
		*/
		. $extrawhere
		. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		. "  AND icsf.state=1 AND icsf.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		// published state is not handled by filter
		//. "\n AND ev.state=1"
		;
		if (!$showrepeats){
			$query .="\n GROUP BY ev.ev_id";
		}
		else if ($needsgroup){
			$query .="\n GROUP BY rpt.rp_id";
		}

		if ($order !="") {
			$query .= " ORDER BY ".$order;
		}
		if ($limit !="" && $limit!=0) {
			$query .= " LIMIT ".($limitstart!=""?$limitstart.",":"").$limit;
		}

		$cache=& JFactory::getCache(JEV_COM_COMPONENT);

		$rows =  $cache->call('JEventsDBModel::_cachedlistIcalEvents', $query, $langtag );

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onDisplayCustomFieldsMultiRowUncached', array( &$rows ));

		return $rows;
	}

	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	function listIcalEventsByRange( $startdate, $enddate, $limitstart, $limit, $showrepeats = true, $order="rpt.startrepeat ASC ", $filters = false, $extrafields="", $extratables="", $count=false) {
		list($year, $month, $day) = explode('-', $startdate);
		list($thisyear, $thismonth, $thisday) = JEVHelper::getYMD();

		//$startdate 	= $this->cfg->getValue("showyearpast",1)?mktime( 0, 0, 0, intval($month),intval($day),intval($year) ):mktime( 0, 0, 0, $thismonth,$thisday, $thisyear );
		$startdate 	=mktime( 0, 0, 0, intval($month),intval($day),intval($year) );

		$startdate = strftime('%Y-%m-%d',$startdate);

		if (strlen($startdate)==10) $startdate.= " 00:00:00";
		if (strlen($enddate)==10) $enddate.= " 23:59:59";

		// This code is used by the iCals code with a spoofed user so check if this is what is happening
		if (JRequest::getString("jevtask","")=="icals.export"){
			$registry	=& JRegistry::getInstance("jevents");
			$user = $registry->getValue("jevents.icaluser",false);
			if (!$user) $user = JFactory::getUser();
		}
		else {
			$user = JFactory::getUser();
		}
		$db	=& JFactory::getDBO();
		$lang =& JFactory::getLanguage();
		$langtag = $lang->getTag();

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere =array();
		$extrajoin = array();
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$needsgroup = false;

		if (!$filters){
			$filters = jevFilterProcessing::getInstance(array("published","justmine","category","search"));
			$filters->setWhereJoin($extrawhere,$extrajoin);
			$needsgroup = $filters->needsGroupBy();

			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));
		}
		else {
			$filters->setWhereJoin($extrawhere,$extrajoin);
		}
		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		// This version picks the details from the details table
		if ($count){
			$query = "SELECT rpt.rp_id";
		}
		else {
			$query = "SELECT ev.*, rpt.*, rr.*, det.*, ev.state as published $extrafields"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn";
		}
		$query .= "\n FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid "
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = rpt.eventid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		. "\n AND rpt.endrepeat >= '$startdate' AND rpt.startrepeat <= '$enddate'"

		// Must suppress multiday events that have already started
		. "\n AND NOT (rpt.startrepeat < '$startdate' AND det.multiday=0) "

		. $extrawhere
		. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		. "  AND icsf.state=1 AND icsf.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		;
		if (!$showrepeats){
			$query .="\n GROUP BY ev.ev_id";
		}
		else if ($needsgroup){
			$query .="\n GROUP BY rpt.rp_id";
		}

		if ($order !="") {
			$query .= " ORDER BY ".$order;
		}
		if ($limit !="" && $limit!=0) {
			$query .= " LIMIT ".($limitstart!=""?$limitstart.",":"").$limit;
		}

		$cache=& JFactory::getCache(JEV_COM_COMPONENT);
		
		$rows =  $cache->call('JEventsDBModel::_cachedlistIcalEvents', $query, $langtag );

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onDisplayCustomFieldsMultiRowUncached', array( &$rows ));

		return $rows;
	}

	function countIcalEventsByYear( $year,$showrepeats = true) {
		$startdate 	= mktime( 0, 0, 0, 1, 1, $year );
		$enddate 	= mktime( 23, 59, 59, 12, 31, $year );
		return $this->listIcalEventsByYear($year,"","",$showrepeats,"",false,"","",true);
	}

	function countIcalEventsByRange( $startdate, $enddate,$showrepeats = true) {
		return $this->listIcalEventsByRange($startdate, $enddate,"","",$showrepeats,"",false,"","",true);
	}

	function listEventsById( $rpid, $includeUnpublished=0, $jevtype="icaldb" ) {
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		$frontendPublish = JEVHelper::isEventPublisher();

		if ($jevtype=="icaldb"){
			// process the new plugins
			// get extra data and conditionality from plugins
			$extrafields = "";  // must have comma prefix
			$extratables = "";  // must have comma prefix
			$extrawhere =array();
			$extrajoin = array();
			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onListEventsById', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin));
			$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ".implode( " \n LEFT JOIN ", $extrajoin ) : '' );
			$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

			$query = "SELECT ev.*, ev.state as published, rpt.*, rr.*, det.* $extrafields, ev.created as created "
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM (#__jevents_vevent as ev $extratables)"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. $extrajoin
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. $extrawhere
			. "\n AND rpt.rp_id = '$rpid'";
			$query .="\n GROUP BY rpt.rp_id";
		}
		else {
			die("invalid jevtype in listEventsById - more changes needed");
		}

		$db->setQuery( $query );
		//echo $db->_sql;
		$rows = $db->loadObjectList();

		// iCal agid uses GUID or UUID as identifier
		if( $rows ){
			if (strtolower($jevtype)=="icaldb"){
				$row = new jIcalEventRepeat($rows[0]);
			}
			else if (strtolower($jevtype)=="jevent"){
				$row = new jEventCal($rows[0]);
			}
		}else{
			$row=null;
		}

		return $row;
	}

	/**
	 * Get Event by ID (not repeat Id) result is based on first repeat
	 *
	 * @param event_id $evid
	 * @param boolean $includeUnpublished
	 * @param string $jevtype
	 * @return jeventcal (or desencent)
	 */
	function getEventById( $evid, $includeUnpublished=0, $jevtype="icaldb" ) {
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();

		$frontendPublish = JEVHelper::isEventPublisher();

		if ($jevtype=="icaldb"){
			// process the new plugins
			// get extra data and conditionality from plugins
			$extrafields = "";  // must have comma prefix
			$extratables = "";  // must have comma prefix
			$extrawhere =array();
			$extrajoin = array();
			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onListEventsById', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin));

			$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ".implode( " \n LEFT JOIN ", $extrajoin ) : '' );
			$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );
			// make sure we pick up the event state
			$query = "SELECT ev.*, rpt.*, rr.*, det.* $extrafields , ev.state as state "
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM (#__jevents_vevent as ev $extratables)"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. $extrajoin
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. $extrawhere
			. "\n AND ev.ev_id = '$evid'"
			. "\n GROUP BY rpt.rp_id"
			. "\n LIMIT 1";
		}
		else {
			die("invalid jevtype in listEventsById - more changes needed");
		}

		$db->setQuery( $query );
		//echo $db->_sql;
		$rows = $db->loadObjectList();

		// iCal agid uses GUID or UUID as identifier
		if( $rows ){
			if (strtolower($jevtype)=="icaldb"){
				$row = new jIcalEventRepeat($rows[0]);
			}
			else if (strtolower($jevtype)=="jevent"){
				$row = new jEventCal($rows[0]);
			}
		}else{
			$row=null;
		}

		return $row;
	}

	function listEventsByCreator( $creator_id, $limitstart, $limit ){
		if (!$this->legacyEvents) {
			return array();
		}
	}

	function listIcalEventsByCreator ( $creator_id, $limitstart, $limit ){
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();

		$cfg = & JEVConfig::getInstance();

		$rows_per_page = $limit;

		if( empty( $limitstart) || !$limitstart ){
			$limitstart = 0;
		}

		$limit = "";
		if ($limitstart>0 || $rows_per_page>0){
			$limit = "LIMIT $limitstart, $rows_per_page";
		}

		$frontendPublish = JEVHelper::isEventPublisher();

		$adminCats = JEVHelper::categoryAdmin();

		$where = '';
		if( $creator_id == 'ADMIN' ){
			$where = "";
		}
		else if ( $adminCats && count($adminCats)>0){
			//$adminCats = " OR (ev.state=0 AND ev.catid IN(".implode(",",$adminCats)."))";
			$adminCats = " OR ev.catid IN(".implode(",",$adminCats).")";
			$where = " AND ( ev.created_by = ".$user->id. $adminCats. ")";
		}
		else {
			$where = " AND ev.created_by = '$creator_id' ";
		}

		// State is manged by plugin
		/*
		$state = "\n AND ev.state=1";
		if ($frontendPublish){
		$state = "";
		}
		*/

		$extrawhere =array();
		$extrajoin = array();
		$filters = jevFilterProcessing::getInstance(array("published","justmine","category","startdate","search"));
		$filters->setWhereJoin($extrawhere,$extrajoin);
		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		$query = "SELECT ev.*, rr.*, det.*, ev.state as published, count(rpt.rp_id) as rptcount"
		. "\n , YEAR(dtstart) as yup, MONTH(dtstart ) as mup, DAYOFMONTH(dtstart ) as dup"
		. "\n , YEAR(dtend  ) as ydn, MONTH(dtend   ) as mdn, DAYOFMONTH(dtend   ) as ddn"
		. "\n , HOUR(dtstart) as hup, MINUTE(dtstart) as minup, SECOND(dtstart   ) as sup"
		. "\n , HOUR(dtend  ) as hdn, MINUTE(dtend  ) as mindn, SECOND(dtend     ) as sdn"
		. "\n FROM #__jevents_vevent as ev"
		. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = ev.detail_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		. $extrawhere
		. $where
		//. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		. "\n AND icsf.state=1"
		. "\n GROUP BY ev.ev_id"
		. "\n ORDER BY dtstart ASC"
		. "\n $limit";

		$db->setQuery( $query );
		//echo $db->explain();
		$icalrows = $db->loadObjectList();
		echo $db->getErrorMsg();
		$icalcount = count($icalrows);
		for( $i = 0; $i < $icalcount ; $i++ ){
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventDB($icalrows[$i]);
		}
		return $icalrows;
	}

	function listIcalEventRepeatsByCreator ( $creator_id, $limitstart, $limit ){
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();

		$cfg = & JEVConfig::getInstance();

		$rows_per_page = $limit;

		if( empty( $limitstart) || !$limitstart ){
			$limitstart = 0;
		}

		$limit = "";
		if ($limitstart>0 || $rows_per_page>0){
			$limit = "LIMIT $limitstart, $rows_per_page";
		}

		$adminCats = JEVHelper::categoryAdmin();
		$where = '';
		if( $creator_id == 'ADMIN' ){
			$where = "";
		}
		else if ( $adminCats && count($adminCats)>0){
			//$adminCats = " OR (ev.state=0 AND ev.catid IN(".implode(",",$adminCats)."))";
			$adminCats = " OR ev.catid IN(".implode(",",$adminCats).")";
			$where = " AND ( ev.created_by = ".$user->id. $adminCats. ")";
		}
		else {
			$where = " AND ev.created_by = '$creator_id' ";
		}

		$frontendPublish = JEVHelper::isEventPublisher();

		$extrawhere =array();
		$extrajoin = array();
		$filters = jevFilterProcessing::getInstance(array("published","justmine","category","startdate","search"));
		$filters->setWhereJoin($extrawhere,$extrajoin);
		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		if( $frontendPublish ){
			// TODO fine a single query way of doing this !!!
			$query = "SELECT rp_id"
			. "\n FROM #__jevents_repetition as rpt "
			. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. $extrajoin
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. $extrawhere
			. $where
			. "\n  AND icsf.state=1"
			//. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. "\n GROUP BY rpt.rp_id"
			. "\n ORDER BY rpt.startrepeat"
			. "\n $limit";
			;

			$db->setQuery( $query );
			$rplist =  $db->loadResultArray();
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
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. $extrawhere
			. $where
			. "\n  AND icsf.state=1"
			//. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. "\n GROUP BY rpt.rp_id"
			. "\n ORDER BY rpt.startrepeat"
			;
		}
		else {
			// TODO fine a single query way of doing this !!!
			$query = "SELECT rp_id"
			. "\n FROM #__jevents_vevent as ev "
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. $extrajoin
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. $extrawhere
			. "\n AND icsf.state=1"
			. $where
			//. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. "\n GROUP BY rpt.rp_id"
			. "\n ORDER BY rpt.startrepeat"
			. "\n $limit";
			;

			$db->setQuery( $query );
			$rplist =  $db->loadResultArray();

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
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. $where
			//. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. "\n AND icsf.state=1"
			. "\n GROUP BY rpt.rp_id"
			. "\n ORDER BY rpt.startrepeat"
			;

		}
		$db->setQuery( $query );
		$icalrows = $db->loadObjectList();
		$icalcount = count($icalrows);
		for( $i = 0; $i < $icalcount ; $i++ ){
			// convert rows to jIcalEvents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}
		return $icalrows;
	}

	function countEventsByCreator($creator_id){
		if (!$this->legacyEvents) {
			return 0;
		}
	}

	function countIcalEventsByCreator($creator_id){
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();

		$adminCats = JEVHelper::categoryAdmin();
		$where = '';
		if( $creator_id == 'ADMIN' ){
			$where = "";
		}
		else if ( $adminCats && count($adminCats)>0){
			//$adminCats = " OR (ev.state=0 AND ev.catid IN(".implode(",",$adminCats)."))";
			$adminCats = " OR ev.catid IN(".implode(",",$adminCats).")";
			$where = " AND ( ev.created_by = ".$user->id. $adminCats. ")";
		}
		else {
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

		$extrawhere =array();
		$extrajoin = array();
		$filters = jevFilterProcessing::getInstance(array("published","justmine","category","startdate","search"));
		$filters->setWhereJoin($extrawhere,$extrajoin);
		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		$query = "SELECT MIN(rpt.rp_id) as rp_id"
		. "\n FROM #__jevents_vevent as ev "
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
		. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		. $extrawhere
		. $where
		. "\n AND icsf.state=1"
		. "\n GROUP BY ev.ev_id";

		$db->setQuery( $query );
		$db->query();
		return $db->getNumRows();

	}

	function countIcalEventRepeatsByCreator($creator_id){
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();

		$adminCats = JEVHelper::categoryAdmin();
		$where = '';
		if( $creator_id == 'ADMIN' ){
			$where = "";
		}
		else if ( $adminCats && count($adminCats)>0){
			//$adminCats = " OR (ev.state=0 AND ev.catid IN(".implode(",",$adminCats)."))";
			$adminCats = " OR ev.catid IN(".implode(",",$adminCats).")";
			$where = " AND ( ev.created_by = ".$user->id. $adminCats. ")";
		}
		else {
			$where = " AND ev.created_by = '$creator_id' ";
		}

		// State is managed by plugin

		$extrawhere =array();
		$extrajoin = array();
		$filters = jevFilterProcessing::getInstance(array("published","justmine","category","startdate","search"));
		$filters->setWhereJoin($extrawhere,$extrajoin);
		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		$query = "SELECT rpt.rp_id, ev.catid"
		. "\n FROM #__jevents_repetition as rpt "
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		. $extrawhere
		. $where
		. "\n AND icsf.state=1"
		. "\n GROUP BY rpt.rp_id"
		;

		$db->setQuery( $query );
		$db->query();
		return $db->getNumRows();
	}

	function listEventsByCat( $catids, $limitstart, $limit ){
		if (!$this->legacyEvents) {
			return array();
		}
	}

	// Allow the passing of filters directly into this function for use in 3rd party extensions etc.
	function listIcalEventsByCat ($catids, $showrepeats = false, $total=0, $limitstart=0, $limit=0, $order=" ORDER BY rpt.startrepeat asc", $filters = false, $extrafields="", $extratables="") {
		$db	=& JFactory::getDBO();

		// Use catid in accessibleCategoryList to pick up offsping too!
		$aid = null;
		$catidlist = implode(",",$catids);

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$extrawhere =array();
		$extrajoin = array();
		$needsgroup = false;

		if (!$this->cfg->getValue("showyearpast",1)){
			list($year,$month,$day) = JEVHelper::getYMD();
			$startdate = mktime( 0, 0, 0, $month,$day, $year );
			$today = strtotime("+0 days");
			if ($startdate<$today) $startdate=$today;
			$startdate = strftime('%Y-%m-%d 00:00:00',$startdate);
			$extrawhere[] = "rpt.endrepeat >=  '$startdate'";
		}

		if (!$filters){
			$filters = jevFilterProcessing::getInstance(array("published","justmine","category","search"));
			$filters->setWhereJoin($extrawhere,$extrajoin);
			$needsgroup = $filters->needsGroupBy();

			$dispatcher	=& JDispatcher::getInstance();
			$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));
		}
		else {
			$filters->setWhereJoin($extrawhere,$extrajoin);
		}
		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		if ($limit>0 || $limitstart>0){
			if( empty( $limitstart) || !$limitstart ){
				$limitstart = 0;
			}

			$rows_per_page = $limit;
			$limit = " LIMIT $limitstart, $rows_per_page";
		}
		else {
			$limit = "";
		}

		$user =& JFactory::getUser();
		if ($showrepeats){
			$query = "SELECT ev.*, rpt.*, rr.*, det.* $extrafields"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM #__jevents_vevent as ev"
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. $extrajoin
			//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. $extrawhere
			//. "\n AND ev.state=1"
			. "\n  AND icsf.state=1"
			. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. "\n GROUP BY rpt.rp_id"
			. $order
			. $limit;
		}
		else {
			// TODO find a single query way of doing this !!!
			$query = "SELECT MIN(rpt.rp_id) as rp_id FROM #__jevents_repetition as rpt "
			. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_icsfile as icsf  ON icsf.ics_id=ev.icsid"
			. $extrajoin
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. $extrawhere
			. "\n  AND icsf.state=1"
			. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. "\n GROUP BY ev.ev_id"
			;

			$db->setQuery( $query );
			//echo $db->explain();

			$rplist =  $db->loadResultArray();

			$rplist = implode(',', array_merge(array(-1), $rplist));

			$query = "SELECT rpt.rp_id,ev.*, rpt.*, rr.*, det.* $extrafields"
			. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
			. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
			. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
			. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
			. "\n FROM #__jevents_repetition as rpt  "
			. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n AND rpt.rp_id IN($rplist)"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. $extrajoin
			//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. $extrawhere
			//. "\n AND ev.state=1"
			. "\n  AND icsf.state=1"
			. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
			. ($needsgroup?"\n GROUP BY rpt.rp_id":"")
			. $order
			. $limit;
		}

		$cache=& JFactory::getCache(JEV_COM_COMPONENT);
		$lang =& JFactory::getLanguage();
		$langtag = $lang->getTag();
		
		$rows =  $cache->call('JEventsDBModel::_cachedlistIcalEvents', $query, $langtag );

		$dispatcher =& JDispatcher::getInstance();
		$dispatcher->trigger( 'onDisplayCustomFieldsMultiRowUncached', array( &$rows ));

		return $rows;

	}

	function countEventsByCat( $catid){
		return 0;
	}

	function countIcalEventsByCat( $catids, $showrepeats = false){
		$db	=& JFactory::getDBO();

		// Use catid in accessibleCategoryList to pick up offsping too!
		$aid = null;
		$catidlist = implode(",",$catids);

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix
		$extrawhere =array();
		$extrajoin = array();
		$needsgroup = false;

		if (!$this->cfg->getValue("showyearpast",1)){
			list($year,$month,$day) = JEVHelper::getYMD();
			$startdate = mktime( 0, 0, 0, $month,$day, $year );
			$startdate = strftime('%Y-%m-%d 00:00:00',$startdate);
			$extrawhere[] = "rpt.endrepeat >=  '$startdate'";
		}

		$filters = jevFilterProcessing::getInstance(array("published","justmine","category","search"));
		$filters->setWhereJoin($extrawhere,$extrajoin);
		$needsgroup = $filters->needsGroupBy();

		$extrafields = "";  // must have comma prefix
		$extratables = "";  // must have comma prefix

		$dispatcher	=& JDispatcher::getInstance();
		$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));

		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		// Get the count
		if ($showrepeats){
			$query = "SELECT count(DISTINCT rpt.rp_id) as cnt"
			. "\n FROM #__jevents_vevent as ev "
			. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. $extrajoin
			//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. "\n AND icsf.state=1"
			. $extrawhere
			//. "\n AND ev.state=1"
			;
		}
		else {
			// TODO fine a single query way of doing this !!!
			$query = "SELECT MIN(rpt.rp_id) as rp_id FROM #__jevents_repetition as rpt "
			. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
			. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
			. "\n LEFT JOIN #__jevents_icsfile as icsf  ON icsf.ics_id=ev.icsid "
			. $extrajoin
			//. "\n WHERE ev.catid IN(".$this->accessibleCategoryList($aid,$catids,$catidlist).")"
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. $extrawhere
			//. "\n AND ev.state=1"
			. "\n AND icsf.state=1"
			. "\n GROUP BY ev.ev_id";

			$db->setQuery( $query );

			$rplist =  $db->loadResultArray();

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
			. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
			. "\n AND icsf.state=1"
			. $extrawhere
			//. "\n AND ev.state=1"
			//. ($needsgroup?"\n GROUP BY rpt.rp_id":"")
			;
		}

		$db->setQuery( $query );
		//echo $db->_sql;
		//echo $db->explain();
		$total =  intval($db->loadResult());
		return $total;
	}

	function listEventsByKeyword( $keyword, $order, &$limit, &$limitstart, &$total, $useRegX=false ){
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();

		$rows_per_page = $limit;
		if( empty( $limitstart ) || !$limitstart ){
			$limitstart = 0;
		}

		$limitstring = "";
		if ($rows_per_page>0){
			$limitstring = "LIMIT $limitstart, $rows_per_page";
		}

		$where = "";
		$having = "";
		if (!JRequest::getInt('showpast',0)){
			$datenow =& JFactory::getDate("-12 hours");
			$having = " AND rpt.endrepeat>'".$datenow->toMysql()."'";
		}

		if( !$order ){
			$order = 'publish_up';
		}

		$order 	= preg_replace( "/[\t ]+/", '', $order );
		$orders = explode( ",", $order );

		// this function adds #__events. to the beginning of each ordering field
		function app_db( $strng ){
			return '#__events.' . $strng;
		}

		$order = implode( ',', array_map( 'app_db', $orders ));

		$total = 0;

		// process the new plugins
		// get extra data and conditionality from plugins
		$extrawhere =array();
		$extrajoin = array();
		$needsgroup = false;

		$filterarray = array("published");
		// If there are extra filters from the module then apply them now
		$reg =& JFactory::getConfig();
		$modparams = $reg->getValue("jev.modparams",false);
		if ($modparams && $modparams->getValue("extrafilters",false)){
			$filterarray  = array_merge($filterarray, explode(",",$modparams->getValue("extrafilters",false)));
		}

		$filters = jevFilterProcessing::getInstance($filterarray);
		$filters->setWhereJoin($extrawhere,$extrajoin );
		$needsgroup = $filters->needsGroupBy();

		JPluginHelper::importPlugin('jevents');
		$dispatcher	=& JDispatcher::getInstance();
		$dispatcher->trigger('onListIcalEvents', array (& $extrafields, & $extratables, & $extrawhere, & $extrajoin, & $needsgroup));
		$extrajoin = ( count( $extrajoin  ) ?  " \n LEFT JOIN ". implode( " \n LEFT JOIN ", $extrajoin ) : '' );
		$extrawhere = ( count( $extrawhere ) ? ' AND '. implode( ' AND ', $extrawhere ) : '' );

		$extrasearchfields = array();
		$dispatcher->trigger('onSearchEvents', array (& $extrasearchfields, & $extrajoin,& $needsgroup));

		if (count($extrasearchfields)>0) {
			$extraor = implode(" OR ",$extrasearchfields);
			$extraor = " OR ".$extraor;
			// replace the ### placeholder with the keyword
			$extraor = str_replace("###",$keyword,$extraor);

			$searchpart =( $useRegX ) ? "(det.summary RLIKE '$keyword' OR det.description RLIKE '$keyword' $extraor)\n" :
		" (MATCH (det.summary, det.description) AGAINST ('$keyword' IN BOOLEAN MODE) $extraor)\n";
		}
		else {
			$searchpart =( $useRegX ) ? "(det.summary RLIKE '$keyword' OR det.description RLIKE '$keyword')\n" :
		"MATCH (det.summary, det.description) AGAINST ('$keyword' IN BOOLEAN MODE)\n";
		}

		// Now Search Icals
		$query = "SELECT count( distinct det.evdet_id) FROM #__jevents_vevent as ev"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
		. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		. "\n AND icsf.state=1 AND icsf.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		. "\n AND ";
		$query .= $searchpart;
		$query .= $extrawhere;
		$query .= $having;
		$db->setQuery( $query );
		//echo $db->explain();
		$total += intval($db->loadResult());

		if ($total<$limitstart){
			$limitstart = 0;
		}

		$rows = array();
		if ($total==0) return $rows;

		// Now Search Icals
		// New version
		$query = "SELECT DISTINCT det.evdet_id FROM  #__jevents_vevdetail as det"
		. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventdetail_id = det.evdet_id"
		. "\n LEFT JOIN #__jevents_vevent as ev ON ev.ev_id = rpt.eventid"
		. "\n LEFT JOIN #__jevents_icsfile as icsf ON icsf.ics_id=ev.icsid"
		. $extrajoin
		. "\n WHERE ev.catid IN(".$this->accessibleCategoryList().")"
		. "\n  AND icsf.state=1 AND icsf.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		. "\n AND ev.access " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' .JEVHelper::getAid($user))
		;
		$query .= " AND ";
		$query .= $searchpart;
		$query .= $extrawhere;
		$query .= $having;
		$query .=  "\n ORDER BY rpt.startrepeat ASC ";
		$query .= "\n $limitstring";

		$db->setQuery( $query );
		if (JEVHelper::isAdminUser($user)){
			//echo $db->_sql;
			//echo $db->explain();
		}
		//echo $db->explain();
		$details = $db->loadResultArray();

		$icalrows = array();
		foreach ($details as $detid) {
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
			. "\n ORDER BY rpt.startrepeat ASC limit 1";
			$db->setQuery( $query2 );
			//echo $db->explain();
			$icalrows[] = $db->loadObject();
		}

		$num_events = count( $icalrows );

		for( $i = 0; $i < $num_events; $i++ ){
			// convert rows to jevents
			$icalrows[$i] = new jIcalEventRepeat($icalrows[$i]);
		}
		return $icalrows;
	}


	function sortEvents( $a, $b ){

		list( $adate, $atime ) = explode( ' ', $a->publish_up );
		list( $bdate, $btime ) = explode( ' ', $b->publish_up );
		return strcmp( $atime, $btime );
	}

	function sortJointEvents( $a, $b ){
		$adatetime = $a->getUnixStartTime();
		$bdatetime = $b->getUnixStartTime();
		if ($adatetime==$bdatetime) return 0;
		return ($adatetime>$bdatetime)?-1:1;
	}

	function findMatchingRepeat($uid, $year, $month, $day){
		$start = $year.'/'.$month.'/'.$day.' 00:00:00';
		$end = $year.'/'.$month.'/'.$day.' 23:59:59';

		$db	=& JFactory::getDBO();
		$query = "SELECT ev.*, rpt.* "
		. "\n FROM #__jevents_vevent as ev"
		. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
		. "\n WHERE ev.uid = ".$db->Quote($uid)
		. "\n AND rpt.startrepeat>=".$db->Quote($start)." AND rpt.startrepeat<=".$db->Quote($end)
		;

		$db->setQuery( $query );
		//echo $db->_sql;
		$rows = $db->loadObjectList();
		if (count($rows)>0){
			return $rows[0]->rp_id;
		}

		// still no match so find the nearest repeat and give a message.
		$db	=& JFactory::getDBO();
		$query = "SELECT ev.*, rpt.*, abs(datediff(rpt.startrepeat,".$db->Quote($start).")) as diff "
		. "\n FROM #__jevents_repetition as rpt"
		. "\n LEFT JOIN #__jevents_vevent as ev ON rpt.eventid = ev.ev_id"
		. "\n WHERE ev.uid = ".$db->Quote($uid)
		. "\n ORDER BY diff asc LIMIT 3"
		;

		$db->setQuery( $query );
		//echo $db->_sql;
		$rows = $db->loadObjectList();
		if (count($rows)>0){
			JError::raiseNotice(1,JText::_("This event has changed - this is occurance is now the closest to the date you searched for"));
			return $rows[0]->rp_id;
		}

	}
}
