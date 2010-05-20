<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @subpackage  Module Latest JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

//
// Parameters:
// ===========
//
// maxEvents = max. no. of events to display in the module (1 to 10, default is 5)
//
// mode:
// = 0  (default) display events for current week and following week only up to 'maxEvents'.
//
// = 1  same as 'mode'=0 except some past events for the current week will also be
//      displayed if num of future events is less than $maxEvents.
//
// = 2  display events for +'days' range relative to current day up to $maxEvents.
//
// = 3  same as mode 2 except if there are < 'maxEvents' in the range,
//      then display past events within -'days' range.
//
// = 4  display events for current month up to 'maxEvents'.
//
// days: (default=7) range of days relative to current day to display events for mode 1 or 3.
//
// displayLinks = 1 (default is 0) display event titles as links to the 'view_detail' com_jevents
//                   task which will display details of the event.
//
// displayYear = 1 (default is 0) display year when displaying dates in the non-customized event's listing.
//
// New for rev 1.1:
//
// disableDateStyle = 1 (default is 0) disables the application of the css style 'mod_events_latest_date' to
//                  the displayed events.  Use this when full customization of the display format is desired.
//                  See customFormat parameter below.
//
// disableTitleStyle = 1 (default is 0) disables the application of the css style 'mod_events_latest_title' to
//                  the displayed event's title.  Use this when full customization of the display format is desired.
//                  See customFormat parameter below.
//
// customFormatStr = string (default is null).  allows a customized specification of the desired event fields and
//                format to be used to display the event in the module.  The string can specify html directly.
//                As well, certain event fields can be specified as ${event_field} in the string.  If desired,
//                the user can even specify overriding inline styles in the event format using <div> or <span>
//                to delineate.  Or the <div>'s or <span>'s can actually reference new css style classes which you
//                can create in the template css file.
//                The ${startDate} and ${endDate} are special event fields which can support further customization
//                of the date and time display by allowing a user to specify exactly how to display the date with
//                identical format control codes to the PHP 'date()' function.
//
//                Event fields available:
//
//                ${startDate}, ${endDate}, ${eventDate}, ${title}, ${category}, ${contact}, ${content}, ${addressInfo}, ${extraInfo},
//                ${createdByAlias}, ${createdByUserName}, ${createdByUserEmail}, ${createdByUserEmailLink},
//                ${eventDetailLink}, ${color}
//
//                ${startDate}, ${eventDate} and ${endDate} can also specify a format in the form of a strftime() format or a
//                date() function format.  If a '%' sign is detected in the format string, strftime() is assumed
//                to be used (supports locale international dates).  An example of a format used:
//                ${startDate('D, M jS, Y, @g:ia')}
//
// Note that the default customFormatStr is '${eventDate}<br />${title}' which will almost display the same information
// and in the same format as in rev 1.11.  ${eventDate} is the actual date of an event within an event's
// start and end publish date ranges.  This more accurately reflects a multi-day event's actual date.


defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (dirname(__FILE__).DS.'helper.php');

$jevhelper = new modJeventsLatestHelper();
$theme = JEV_CommonFunctions::getJEventsViewName();

JPluginHelper::importPlugin("jevents");

// record what is running - used by the filters
$registry	=& JRegistry::getInstance("jevents");
$registry->setValue("jevents.activeprocess","mod_jevents_latest");
$registry->setValue("jevents.moduleid", $module->id);
$registry->setValue("jevents.moduleparams", $params);

$viewclass = $jevhelper->getViewClass($theme, 'mod_jevents_latest',$theme.DS."latest", $params);

$registry	=& JRegistry::getInstance("jevents");
// See http://www.php.net/manual/en/timezones.php
$compparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
$tz=$compparams->get("icaltimezonelive","");
if ($tz!="" && is_callable("date_default_timezone_set")){
	$timezone= date_default_timezone_get();
	//echo "timezone is ".$timezone."<br/>";
	date_default_timezone_set($tz);
	$registry->setValue("jevents.timezone",$timezone);
}

$modview = new $viewclass($params, $module->id);
echo $modview->displayLatestEvents();

// Must reset the timezone back!!
if ($tz && is_callable("date_default_timezone_set")){
	date_default_timezone_set($timezone);
}
