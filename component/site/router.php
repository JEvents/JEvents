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

defined('_JEXEC') or die( 'No Direct Access' );

JLoader::register('JEVConfig',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/config.php");
JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");

function JEventsBuildRoute(&$query)
{
	$cfg = & JEVConfig::getInstance();
	$segments = array();
	// We don't need the view - its only used to manipulate parameters
	if (isset($query['view'])){
		unset($query['view']);
	}

	$task = false;
	$task = false;
	if (!isset($query['task'])){
		if (isset($query["Itemid"])){
			$menu = & JSite::getMenu();
			$menuitem = $menu->getItem($query["Itemid"]);
			if (!is_null($menuitem) && isset($menuitem->query["task"])){
				$task = $menuitem->query["task"];
				return $segments;
			}
			else if (!is_null($menuitem) && isset($menuitem->query["layout"]) && isset($menuitem->query["view"]) ){
				$task = $menuitem->query["view"].".".$menuitem->query["layout"];
			}
		}
		if (!$task){
			$task = 'month.calendar';
		}
	}
	else {
		$task=$query['task'];
		unset($query['task']);
	}

	switch ($task) {
		case "year.listevents":
		case "month.calendar":
		case "week.listevents":
		case "day.listevents":
		case "cat.listevents":
		case "jevent.detail":
		case "icalevent.detail":
		case "icalrepeat.detail":
		case "search.form":
		case "search.results":
		case "admin.listevents":
			{
				$segments[]=$task;
				$config	=& JFactory::getConfig();
				$t_datenow = JEVHelper::getNow();

				// if no date in the query then use TODAY not the calendar date
				$nowyear	= strftime( '%Y', $t_datenow->toUnix(true));
				$nowmonth	= strftime( '%m', $t_datenow->toUnix(true));
				$nowday	= strftime( '%d', $t_datenow->toUnix(true));
				/*
				$year	= intval( JRequest::getVar( 'year',	 $nowyear ));
				$month	= intval( JRequest::getVar( 'month', $nowmonth ));
				$day	= intval( JRequest::getVar( 'day',	 $nowday ));
				*/
				if(isset($query['year'])) {
					$segments[] = $query['year'];
					unset($query['year']);
				}
				else {
					// if no date in the query then use TODAY not the calendar date
					$segments[] = $nowyear;
				}
				if(isset($query['month'])) {
					$segments[] = $query['month'];
					unset($query['month']);
				}
				else {
					// if no date in the query then use TODAY not the calendar date
					$segments[] = $nowmonth;
				}
				if(isset($query['day'])) {
					$segments[] = $query['day'];
					unset($query['day']);
				}
				else {
					// if no date in the query then use TODAY not the calendar date
					$segments[] = $nowday;
				}
				switch ($task) {
					case "jevent.detail":
					case "icalevent.detail":
					case "icalrepeat.detail":
						if(isset($query['jevtype'])) {
							unset($query['jevtype']);
						}
						if(isset($query['evid'])) {
							$segments[] = $query['evid'];
							unset($query['evid']);
						}
						else {
							$segments[] = "0";
						}
						/*
						// Can we drop the use of uid?
						if(isset($query['title'])) {
						$segments[] = JFilterOutput::stringURLSafe($query['title']);
						unset($query['title']);
						}
						else {
						$segments[] = "-";
						}
						*/

						break;
					default:
						break;
				}
				if(isset($query['catids']) && strlen($query['catids'])>0) {
					$segments[] = $query['catids'];
					unset($query['catids']);
				}
				else {
					$segments[] = "-";
				}

				switch ($task) {
					case "icalrepeat.detail":
						if(isset($query['uid'])) {
							// Some remote UIDs have @ and other dodgy characters in them so encode them for safety
							//$segments[] = base64_encode($query['uid']);
							unset($query['uid']);
						}
						if(isset($query['title'])) {
							$segments[] = substr(JFilterOutput::stringURLSafe($query['title']),0,150);
							unset($query['title']);
						}
						else {
							$segments[] = "-";
						}

						break;
					default:
						break;
				}

			}
			break;
		case "jevent.edit":
		case "icalevent.edit":
		case "icalevent.publish":
		case "icalevent.unpublish":
		case "icalevent.editcopy":
		case "icalrepeat.edit":
		case "jevent.delete":
		case "icalevent.delete":
		case "icalrepeat.delete":
		case "icalrepeat.deletefuture":
			$segments[]=$task;
			if(isset($query['jevtype'])) {
				unset($query['jevtype']);
			}
			if(isset($query['evid'])) {
				$segments[] = $query['evid'];
				unset($query['evid']);
			}
			else {
				$segments[] = "0";
			}
			break;
		case "modlatest.rss":
			$segments[]=$task;
			// assume implicit feed document
			//unset($query['format']);

			// feed type
			if(isset($query['type'])) {
				$segments[] = $query['type'];
				unset($query['type']);
			} else {
				$segments[] = 'rss';
			}

			// modid
			if(isset($query['modid'])) {
				$segments[] = $query['modid'];
				unset($query['modid']);
			}
			else {
				$segments[] = "0";
			}

			break;
		case "icalrepeat.vcal":
		case "icalevent.vcal":
			$segments[]=$task;
			if(isset($query['evid'])) {
				$segments[] = $query['evid'];
				unset($query['evid']);
			}
			else {
				$segments[] = "0";
			}
			if(isset($query['catids'])) {
				$segments[] = $query['catids'];
				unset($query['catids']);
			}
			else {
				$segments[] = "0";
			}
			break;

		default:
			$segments[]=$task;
			$segments[] = "-";
			break;
	}


	return $segments;
}

function JEventsParseRoute($segments)
{
	$vars = array();

	//Get the active menu item
	$menu =& JSite::getMenu();
	$item =& $menu->getActive();

	// Count route segments
	$count = count($segments);

	if ($count>0){
		// task
		$task = $segments[0];
		$vars["task"]=$task;

		switch 	($task){
			case "year.listevents":
			case "month.calendar":
			case "week.listevents":
			case "day.listevents":
			case "cat.listevents":
			case "jevent.detail":
			case "icalevent.detail":
			case "icalrepeat.detail":
			case "view_cat":
				if (strpos($task,"jevent")===0){
					$vars['jevtype']="jevent";
				}
				else if (strpos($task,"icalevent")===0){
					$vars['jevtype']="icaldb";
				}
				if($count>1) {
					$vars['year'] = $segments[1];
				}
				if($count>2) {
					$vars['month'] = $segments[2];
				}
				if($count>3) {
					$vars['day'] = $segments[3];
				}
				if($count>4) {
					switch ($task) {
						case "jevent.detail":
						case "icalevent.detail":
						case "icalrepeat.detail":
							$vars['evid'] = $segments[4];
							// note that URI decoding swaps /-/ for :
							if (count($segments)>5 && $segments[5]!=":"){
								$vars['catids']= $segments[5];
							}
							break;
						default:
							// note that URI decoding swaps /-/ for :
							if ($segments[4]!=":"){
								$vars['catids']= $segments[4];
							}
							break;
					}
				}
				if ($count>6){
					switch ($task) {
						case "icalrepeat.detail":
							//$vars['uid'] = base64_decode($segments[6]);
							break;
						default:
							break;
					}
				}
				break;
			case "jevent.edit":
			case "icalevent.editcopy":
			case "icalevent.edit":
			case "icalevent.publish":
			case "icalevent.unpublish":
			case "icalrepeat.edit":
			case "icalevent.delete":
			case "icalrepeat.delete":
			case "icalrepeat.deletefuture":
				if($count>1) {
					$vars['evid'] = $segments[1];
				}
				break;
			case "modlatest.rss":
				// URI = /task/feedtype/modid
				// force JDocumentFeed
				$vars['format'] = 'feed';
				//feed type
				if($count>1) {
					$vars['type']= $segments[1];
				}
				else {
					$vars['type'] = "rss";
				}
				// modid
				if($count>2) {
					$vars['modid'] = $segments[2];
				}
				else {
					$vars['modid'] = "0";
				}
				break;

			case "icalrepeat.vcal":
			case "icalevent.vcal":
				if($count>1) {
					$vars['evid']= $segments[1];
				}
				else {
					$vars['evid'] = "0";
				}
				// modid
				if($count>2) {
					$vars['catids'] = $segments[2];
				}
				else {
					$vars['catids'] = "0";
				}
				break;

			default:
				break;
		}


	}
	return $vars;

}
