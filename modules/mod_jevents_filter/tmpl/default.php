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

$datamodel	= new JEventsDataModel();
// find appropriate Itemid and setup catids for datamodel
$Itemid = JRequest::getInt("Itemid");
$option = JRequest::getCmd("option");
if ($option==JEV_COM_COMPONENT){
	$myItemid = $Itemid;
}
else {
	$myItemid = $params->get("target_itemid",0);
}
if ($myItemid ==0){
	$myItemid = $datamodel->setupModuleCatids($params);
}

$form_link = "";
if ($myItemid>0){
	$menu = & JSite::getMenu();
	$menuitem = $menu->getItem($myItemid);
        if ($menuitem){
            $form_link = $menuitem->link. "&Itemid=".$myItemid;
        }
}

//$myItemid = JEVHelper::getItemid();
$datamodel->setupComponentCatids();

list($year,$month,$day) = JEVHelper::getYMD();
$evid = JRequest::getVar("evid",false);
$jevtype = JRequest::getVar("jevtype",false);
// FORM for filter submission
$tmpCatids = trim($datamodel->catidsOut);

if ($form_link==""){
	$form_link = 'index.php?option=' . JEV_COM_COMPONENT . '&task=' . JRequest::getVar("jevtask", "cat.listevents"). "&Itemid=".$myItemid;
}

// category ID gets picked up by POST results!
$form_link = JRoute::_($form_link
. ($evid ? '&evid=' . $evid : '')
. ($jevtype ? '&jevtype=' . $jevtype : '')
. ($year ? '&year=' . $year : '')
. ($month ? '&month=' . $month : '')
. ($day ? '&day=' . $day : '')
,false);

$filters = $jevhelper->getFilters();
$filterHTML = $filters->getFilterHTML();

require(JModuleHelper::getLayoutPath('mod_jevents_filter', 'default_layout'));
