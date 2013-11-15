<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: adminqueries.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */ 

defined( '_JEXEC' ) or die( 'Restricted access' );

// load language constants
JEVHelper::loadLanguage('admin');

class JEventsAdminDBModel extends JEventsDBModel {

	/**
 * gets raw vevent (not a rpt) usually for editing purposes
 * 
 *
 * @param int $agid vevent id
 * @return stdClass details of vevent selected
 */
	function getVEventById( $agid) {
		$db	= JFactory::getDBO();
		$user = JFactory::getUser();
		// force state value to event state!
		$accessibleCategories = $this->accessibleCategoryList();
		
		
		$catwhere = "\n WHERE ev.catid IN(" . $this->accessibleCategoryList() . ")";
		$extrajoin = "";
		$extrawhere = "";
		$params = JComponentHelper::getParams("com_jevents");
		if ($params->get("multicategory", 0))
		{
			$extrajoin = "\n LEFT JOIN #__jevents_catmap as catmap ON catmap.evid = ev.ev_id";
			$extrajoin .= "\n LEFT JOIN  #__categories AS catmapcat ON catmap.catid = catmapcat.id";
			$extrawhere = " AND  catmapcat.access " . (version_compare(JVERSION, '1.6.0', '>=') ? ' IN (' . JEVHelper::getAid($user) . ')' : ' <=  ' . JEVHelper::getAid($user));
			$extrawhere .= " AND catmap.catid IN(" . $this->accessibleCategoryList() . ")";
			$catwhere = "\n WHERE 1 ";
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
		if (!$user->get("isRoot")){
			$query .= "\n AND ev.access  " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' . JEVHelper::getAid($user));
		}
		$db->setQuery( $query );

		$rows = $db->loadObjectList();
		
		if (count($rows)>0){
			
			// check multi-category access
			// do not use jev_com_component incase we call this from locations etc.
			$params = JComponentHelper::getParams(JRequest::getCmd("option"));
			if ($params->get("multicategory",0)){
				// get list of categories this event is in - are they all accessible?
				$db->setQuery("SELECT catid FROM #__jevents_catmap WHERE evid=".$rows[0]->ev_id);
				$catids = $db->loadColumn();

				// are there any catids not in list of accessible Categories 
				$inaccessiblecats = array_diff($catids, explode(",",$accessibleCategories));
				if (count($inaccessiblecats )){
					$inaccessiblecats[] = -1;
					$inaccessiblecats = implode(",",$inaccessiblecats);
					$db->setQuery("SELECT id FROM #__categories WHERE extension='com_jevents' and id in($inaccessiblecats)");
					$realcatids = $db->loadColumn();
					if (count ($realcatids) ){
						return null;						
					}
					else {
						$catids = array_intersect($catids, explode(",",$accessibleCategories));

					}
				}
				$rows[0]->catids = $catids;
			}
			
			return $rows[0];
		}
		else return null;
	}

	function getVEventRepeatById( $rp_id) {
		$db	= JFactory::getDBO();
		$user = JFactory::getUser();
		$accessibleCategories = $this->accessibleCategoryList();
		$query = "SELECT ev.*, rpt.*, rr.*, det.*"
		. "\n , YEAR(rpt.startrepeat) as yup, MONTH(rpt.startrepeat ) as mup, DAYOFMONTH(rpt.startrepeat ) as dup"
		. "\n , YEAR(rpt.endrepeat  ) as ydn, MONTH(rpt.endrepeat   ) as mdn, DAYOFMONTH(rpt.endrepeat   ) as ddn"
		. "\n , HOUR(rpt.startrepeat) as hup, MINUTE(rpt.startrepeat ) as minup, SECOND(rpt.startrepeat ) as sup"
		. "\n , HOUR(rpt.endrepeat  ) as hdn, MINUTE(rpt.endrepeat   ) as mindn, SECOND(rpt.endrepeat   ) as sdn"
		. "\n FROM #__jevents_vevent as ev"
		. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventid = ev.ev_id"
		. "\n LEFT JOIN #__jevents_vevdetail as det ON det.evdet_id = rpt.eventdetail_id"
		. "\n LEFT JOIN #__jevents_rrule as rr ON rr.eventid = ev.ev_id"
		. "\n WHERE ev.catid IN(".$accessibleCategories.")"
		. "\n AND rpt.rp_id = '$rp_id'"
		. "\n AND ev.access  " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' . JEVHelper::getAid($user));

		$db->setQuery( $query );

		$rows = $db->loadObjectList();
		
		if (count($rows)>0){
			
			// check multi-category access
			// do not use jev_com_component incase we call this from locations etc.
			$params = JComponentHelper::getParams(JRequest::getCmd("option"));
			if ($params->get("multicategory",0)){
				// get list of categories this event is in - are they all accessible?
				$db->setQuery("SELECT catid FROM #__jevents_catmap WHERE evid=".$rows[0]->ev_id);
				$catids = $db->loadColumn();

				// are there any catids not in list of accessible Categories 
				$inaccessiblecats = array_diff($catids, explode(",",$accessibleCategories));
				if (count($inaccessiblecats )){
					return null;
				}
				$rows[0]->catids = $catids;
			}
			
			return $rows[0];
		}
		else return null;

	}

	/**
 * get all the native JEvents Icals (i.e. not imported from URL or FILE)
 *
 * @return unknown
 */

	// TODO add more access control e.g. canpublish caneditown etc.

	function getNativeIcalendars() {
		$db	= JFactory::getDBO();
		$user = JFactory::getUser();
		$query = "SELECT *"
		. "\n FROM #__jevents_icsfile as ical"
		. "\n WHERE ical.icaltype = '2'"
		. "\n AND ical.state = 1"		
		. "\n AND ical.access  " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' . JEVHelper::getAid($user));

		$dispatcher	= JDispatcher::getInstance();
		$dispatcher->trigger( 'onSelectIcals', array( &$query) );		
		
		$db->setQuery( $query );
		$rows = $db->loadObjectList("ics_id");

		return $rows;
	}

	function getIcalByIcsid($icsid) {
		$db	= JFactory::getDBO();
		$user = JFactory::getUser();
		$query = "SELECT *"
		. "\n FROM #__jevents_icsfile as ical"
		/*
		. "\n WHERE ical.catid IN(".$this->accessibleCategoryList().")"
		. "\n AND ical.ics_id = $icsid"
		*/
		. "\n WHERE ical.ics_id = $icsid"
		. "\n AND ical.access  " . (version_compare(JVERSION, '1.6.0', '>=') ?  ' IN (' . JEVHelper::getAid($user) . ')'  :  ' <=  ' . JEVHelper::getAid($user));

		$db->setQuery( $query );
		$row = $db->loadObject();

		return $row;
	}

	/**
	 * Get list of module definitions by given name
	 *
	 * @param string $module
	 * @return array of rows
	 */
	function getModulesByName($module='mod_events_latest') {

		$db	= JFactory::getDBO();
		$query = "select *"
		. "\n from #__modules"
		. "\n where module='" . $module . "'";

		$db->setQuery( $query );
		$modules = $db->loadObjectList();

		return $modules;
	}

}