<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: mod_jevents_cal.php 3143 2011-12-29 10:15:10Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Calendar
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

require_once (dirname ( __FILE__ ) . '/' . 'helper.php');

$jevhelper = new modJeventsCalHelper ();

JPluginHelper::importPlugin ( "jevents" );

// record what is running - used by the filters
$registry = JRegistry::getInstance ( "jevents" );
$registry->set ( "jevents.activeprocess", "mod_jevents_cal" );
$registry->set ( "jevents.moduleid", $module->id );
$registry->set ( "jevents.moduleparams", $params );

// See http://www.php.net/manual/en/timezones.php
$compparams = JComponentHelper::getParams ( JEV_COM_COMPONENT );
$tz = $compparams->get ( "icaltimezonelive", "" );
if ($tz != "" && is_callable ( "date_default_timezone_set" )) {
	$timezone = date_default_timezone_get ();
	// echo "timezone is ".$timezone."<br/>";
	date_default_timezone_set ( $tz );
	$registry->set ( "jevents.timezone", $timezone );
}

$theme = JEV_CommonFunctions::getJEventsViewName ();
$modtheme = $params->get ( "com_calViewName", $theme );
if ($modtheme == "global" || $modtheme == "") {
	$modtheme = $theme;
}
$theme = $modtheme;

$viewclass = $jevhelper->getViewClass ( $theme, 'mod_jevents_cal', $theme . '/' . "calendar", $params );
$modview = new $viewclass ( $params, $module->id );
$modview->jevlayout = $theme;
echo $modview->getCal ();

// Must reset the timezone back!!
if ($tz && is_callable ( "date_default_timezone_set" )) {
	date_default_timezone_set ( $timezone );
}
