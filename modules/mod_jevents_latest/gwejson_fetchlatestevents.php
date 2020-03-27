<?php

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * @copyright	Copyright (C) 2015-JEVENTS_COPYRIGHT GWESystems Ltd. All rights reserved.
 * @license		By negotiation with author via http://www.gwesystems.com
 */

function ProcessJsonRequest(&$requestObject, $returnData)
{

//	ini_set("log_errors", 1);
//	ini_set("error_log", "error_log"); // Confirms log file is error_log in Joomla! root.

	ini_set("display_errors", 0);

	if (!isset($requestObject->modid) || (int) $requestObject->modid <= 0)
	{
		PlgSystemGwejson::throwerror("There was an error - no valid module id");
	}

	ob_clean();
	ob_start();
	$app    = Factory::getApplication();
	$db  = Factory::getDbo();

	$query = 'SELECT id, title, module, position, content, showtitle,  params'
		. ' FROM #__modules AS m'
		. ' LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id'
		. ' WHERE m.published = 1'
		. ' AND m.access <= 1 '
		. ' AND m.client_id = 0'
		. ' AND m.id=' . (int) $requestObject->modid
		. ' GROUP BY m.id'
		. ' ORDER BY position, ordering';

	$db->setQuery($query);

	try {
		$module = $db->loadObject();
	} catch (Exception $e) {
		echo $e;
		PlgSystemGwejson::throwerror("There was an error - no valid module id 2");
	}


	require_once(dirname(__FILE__) . '/' . 'helper.php');

	$jevhelper = new modJeventsLatestHelper();
	JEVHelper::loadLanguage('admin');
	$theme    = JEV_CommonFunctions::getJEventsViewName();
	$params   = new JevRegistry($module->params);
	$modtheme = $params->get("com_calViewName", $theme);
	if ($modtheme == "" || $modtheme == "global")
	{
		$modtheme = $theme;
	}
	$theme = $modtheme;

	PluginHelper::importPlugin("jevents");

// record what is running - used by the filters
	$registry = JevRegistry::getInstance("jevents");
	$registry->set("jevents.activeprocess", "mod_jevents_latest");
	$registry->set("jevents.moduleid", $requestObject->modid);
	$registry->set("jevents.moduleparams", $params);
	$registry->set("jevents.fetchlatestevents", 1);

	// Set new constraints on dates for pagination!
	//$firstEventDate = $app->getUserState("jevents.moduleid".$requestObject->modid.".firstEventDate",false);
	//$lastEventDate = $app->getUserState("jevents.moduleid".$requestObject->modid.".lastEventDate",false);
	//$firstEventId = $app->getUserState("jevents.moduleid".$requestObject->modid.".firstEventId",false);
	//$lastEventId = $app->getUserState("jevents.moduleid".$requestObject->modid.".lastEventId",false);
	$page = (int) $app->getUserState("jevents.moduleid" . $requestObject->modid . ".page", 0);
	// Based on direction we are moving change the constraints!
	if ($requestObject->direction == 1)
	{
		$page++;
	}
	else
	{
		$page--;
	}
	//$app->setUserState("jevents.moduleid".$requestObject->modid.".firstEventDate",$firstEventDate);
	//$app->setUserState("jevents.moduleid".$requestObject->modid.".lastEventDate",$lastEventDate);
	//$app->setUserState("jevents.moduleid".$requestObject->modid.".firstEventId",$firstEventId);
	//$app->setUserState("jevents.moduleid".$requestObject->modid.".lastEventId",$lastEventId);
	$app->setUserState("jevents.moduleid" . $requestObject->modid . ".page", $page);
	$app->setUserState("jevents.moduleid" . $requestObject->modid . ".direction", $requestObject->direction);

	$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest', $theme . '/' . "latest", $params);

	$registry = JevRegistry::getInstance("jevents");
// See http://www.php.net/manual/en/timezones.php
	$compparams = ComponentHelper::getParams(JEV_COM_COMPONENT);
	$tz         = $compparams->get("icaltimezonelive", "");
	if ($tz != "" && is_callable("date_default_timezone_set"))
	{
		$timezone = date_default_timezone_get();
		//echo "timezone is ".$timezone."<br/>";
		date_default_timezone_set($tz);
		$registry->set("jevents.timezone", $timezone);
	}

	$modview            = new $viewclass($params, $module->id);
	$modview->jevlayout = $theme;
	echo $modview->displayLatestEvents();

// Must reset the timezone back!!
	if ($tz && is_callable("date_default_timezone_set"))
	{
		date_default_timezone_set($timezone);
	}

	$app->triggerEvent('onJEventsLatestFooter');

	$returnData->html = ob_get_clean();

	return $returnData;


}
