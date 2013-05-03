<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: default.php 3323 2012-03-08 13:37:46Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Filter
 * @copyright   Copyright (C) 2008 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.gwesystems.com
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

$datamodel = new JEventsDataModel();
// find appropriate Itemid and setup catids for datamodel
$Itemid = JRequest::getInt("Itemid");
$option = JRequest::getCmd("option");
$jevtask = false;
if ($option == JEV_COM_COMPONENT)
{
	$myItemid = $Itemid;
	$jevtask = JRequest::getVar("jevtask", "year.listevents");
}
else
{
	$myItemid = $params->get("target_itemid", 0);
}
if ($myItemid == 0)
{
	$myItemid = $datamodel->setupModuleCatids($params);
}

$form_link = "";
if ($myItemid > 0)
{
	$menu = & JSite::getMenu();
	$menuitem = $menu->getItem($myItemid);
	// if on a detail page or not already on a jevents component page then pick up the default task
	if ($menuitem && (!$jevtask || strpos($jevtask, "detail") !== false))
	{
		$form_link = $menuitem->link . "&Itemid=" . $myItemid;
	}
}

//$myItemid = JEVHelper::getItemid();
$datamodel->setupComponentCatids();

list($year, $month, $day) = JEVHelper::getYMD();
$evid = JRequest::getVar("evid", false);
$jevtype = JRequest::getVar("jevtype", false);
// FORM for filter submission
$tmpCatids = trim($datamodel->catidsOut);

if ($form_link == "")
{
	$form_link = 'index.php?option=' . JEV_COM_COMPONENT . '&task=' . JRequest::getVar("jevtask", "cat.listevents") . "&Itemid=" . $myItemid;
}

// category ID gets picked up by POST results!
$form_link = JRoute::_($form_link
				. ($evid ? '&evid=' . $evid : '')
				. ($jevtype ? '&jevtype=' . $jevtype : '')
				. ($year ? '&year=' . $year : '')
				. ($month ? '&month=' . $month : '')
				. ($day ? '&day=' . $day : '')
				, false);

$filters = $jevhelper->getFilters();

$option = JRequest::getCmd("option");
if ($params->get("disablenonjeventspages",0) && $option!="com_jevents" && $option!="com_jevlocations" && $option!="com_jevpeople" && $option!="com_rsvppro"  && $option!="com_jevtags") {
	// display nothing on non-jevents pages - again make this a config option
	return ;
}

$filterHTML = $filters->getFilterHTML();

//Check if in event details
//We never need filters in an edit page, this could cause user issues, so if there remove to.
if (JRequest::getCmd("task") == "icalrepeat.detail" && $params->get('showindetails', 0) == 0 || JRequest::getCmd("task") == "icalevent.edit" || JRequest::getCmd("task") == "icalrepeat.edit")
{
	return;
}

//Check if creating / editing an event
require(JModuleHelper::getLayoutPath('mod_jevents_filter', 'default_layout'));