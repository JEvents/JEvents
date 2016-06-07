<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: mod_jevents_latest.php 3309 2012-03-01 10:07:50Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module Latest JEvents
 * @copyright   Copyright (C) 2006-2012 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (dirname(__FILE__).'/'.'helper.php');

$jevhelper = new modJeventsLatestHelper();
$theme = JEV_CommonFunctions::getJEventsViewName();
$modtheme = $params->get("com_calViewName", $theme);
if ($modtheme=="" || $modtheme=="global"){
	$modtheme=$theme;
}
$theme=$modtheme;

JPluginHelper::importPlugin("jevents");

// record what is running - used by the filters
$registry	= JRegistry::getInstance("jevents");
$registry->set("jevents.activeprocess","mod_jevents_latest");
$registry->set("jevents.moduleid", $module->id);
$registry->set("jevents.moduleparams", $params);

$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest',$theme.'/'."latest", $params);

$registry	= JRegistry::getInstance("jevents");
// See http://www.php.net/manual/en/timezones.php
$compparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
$tz=$compparams->get("icaltimezonelive","");
if ($tz!="" && is_callable("date_default_timezone_set")){
	$timezone= date_default_timezone_get();
	//echo "timezone is ".$timezone."<br/>";
	date_default_timezone_set($tz);
	$registry->set("jevents.timezone",$timezone);
}

$modview = new $viewclass($params, $module->id);
$modview->jevlayout = $theme;
echo $modview->displayLatestEvents();

// Must reset the timezone back!!
if ($tz && is_callable("date_default_timezone_set")){
	date_default_timezone_set($timezone);
}

$dispatcher	= JEventDispatcher::getInstance();
$dispatcher->trigger( 'onJEventsLatestFooter');
