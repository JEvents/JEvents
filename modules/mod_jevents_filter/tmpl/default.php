<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: default.php 3323 2012-03-08 13:37:46Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Filter
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.gwesystems.com
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Component\ComponentHelper;

$datamodel = new JEventsDataModel();

// find appropriate Itemid and setup catids for datamodel
$app     = Factory::getApplication();
$input   = $app->input;
$Itemid  = $input->getInt("Itemid");
$option  = $input->getCmd("option");
$jevtask = false;
if ($option == JEV_COM_COMPONENT)
{
	$myItemid = $Itemid;
	$jevtask  = $input->getCmd("jevtask", "year.listevents");
}
else
{
	$myItemid = $params->get("target_itemid", 0);
}

// if always to target then set it here
if ($params->get("target_itemid", 0) && $params->get("alwaystarget", 0))
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
	$menu     = Factory::getApplication()->getMenu();
	$menuitem = $menu->getItem($myItemid);
	// if on a detail page or not already on a jevents component page then pick up the default task
	if ($menuitem && (!$jevtask || strpos($jevtask, "detail") !== false))
	{
		$form_link = $menuitem->link . "&Itemid=" . $myItemid;
	}
	else if ($menuitem && $params->get("alwaystarget", 0))
	{
		$form_link = $menuitem->link . "&Itemid=" . $myItemid;
	}
}

//$myItemid = JEVHelper::getItemid();
$datamodel->setupComponentCatids();

list($year, $month, $day) = JEVHelper::getYMD();
$evid    = $input->getInt("evid", 0);
$jevtype = $input->getCmd("jevtype", false);
// FORM for filter submission
$tmpCatids = trim($datamodel->catidsOut);

if ($form_link == "")
{
	$form_link = 'index.php?option=' . JEV_COM_COMPONENT . '&task=' . $input->getCmd("jevtask", "cat.listevents") . "&Itemid=" . $myItemid;
}

$form_link .= "&year=$year&month=$month&day=$day";

// category ID gets picked up by POST results!
$form_link = Route::_($form_link
	. ($evid ? '&evid=' . $evid : '')
	. ($jevtype ? '&jevtype=' . $jevtype : '')
	, false);

$filters = $jevhelper->getFilters();

$compparams = ComponentHelper::getParams('com_jevents');
if ($compparams->get('framework', 'native') === 'uikit3')
{
    $params->set("bootstrapchosen", 2);
}

$option = $input->getCmd("option");
if ($params->get("disablenonjeventspages", 0) && $option != "com_jevents" && $option != "com_jevlocations" && $option != "com_jevpeople" && $option != "com_rsvppro" && $option != "com_jevtags")
{
	// display nothing on non-jevents pages - again make this a config option
	return;
}

// Check if in event details
// We never need filters in an edit page, this could cause user issues, so if there remove to.
if (
	(($input->getCmd("task") == "icalrepeat.detail" ||
		$input->getCmd("task") == "icalevent.detail") &&
		$params->get('showindetails', 0) == 0) ||
		$input->getCmd("task") == "icalevent.edit" ||
		$input->getCmd("task") == "icalrepeat.edit" ||
		$input->getCmd("task") == "icalevent.edit")
{
	return;
}

$allowAutoSubmit = true;
$filters->modParams = $params;
$filterHTML      = $filters->getFilterHTML($allowAutoSubmit, true);

// strip out the filters we don't need!
$filterlist = explode(",", str_replace(" ", "", $params->get('filters', "search")));
foreach ($filterHTML as $filterindex => $filter)
{

    if ( isset( $filter["filterType"] ) && ! empty( $filter["filterType"] ) && $filter["filterType"] === "Customfield" )
    {
        $activeFilter = ucfirst( $filter["filterType"] );
        $activeFilter .= ":" . str_replace( "_", "", $filterindex );

        if ( ! in_array( $activeFilter, $filterlist ) )
        {
            unset( $filterHTML[$filterindex] );
        }
        continue;
    }


    if (
        ! in_array( $filterindex, $filterlist )
        && ! ( isset( $filter["filterType"] ) && ! empty( $filter["filterType"] ) && in_array( ucfirst( $filter["filterType"] ), $filterlist ) )
        && ! ( isset( $filter["filterField"] ) && ! empty( $filter["filterField"] ) && in_array( ucfirst( $filter["filterField"] ), $filterlist ) )
    )
    {
        unset( $filterHTML[$filterindex] );
    }
}


if ($params->get("customcss", false))
{
	$css = $params->get("customcss", false);
	Factory::getDocument()->addStyleDeclaration($css);
}

if ($params->get("bootstrapchosen", 1) == 1)
{
	// Load Bootstrap
	HTMLHelper::_('formbehavior.chosen', '.jevfiltermodule select');
	require(ModuleHelper::getLayoutPath('mod_jevents_filter', 'default_chosenlayout'));
}
else if ($params->get("bootstrapchosen", 1) == 2)
{
	// Load Bootstrap
	require(ModuleHelper::getLayoutPath('mod_jevents_filter', 'default_uikitlayout'));
}
else
{
	//Check if creating / editing an event
	require(ModuleHelper::getLayoutPath('mod_jevents_filter', 'default_layout'));
}
