<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevents.php 3551 2012-04-20 09:41:37Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Environment\Browser;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

//header("Content-Security-Policy: script-src 'self' 'unsafe-inline'");

$app    = Factory::getApplication();
$input  = $app->input;

jimport('joomla.filesystem.path');

// For development performance testing only
/**
 * $db = Factory::getDbo();
 * $db->setQuery("SET SESSION query_cache_type = OFF");
 * $db->execute();
 *
 * $cfg = JEVConfig::getInstance();
 * $cfg->set('jev_debug', 1);
 **/

if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "Internet Explorer") !== false))
{
	define ("GSLMSIE10" , 1);
}
else
{
	define ("GSLMSIE10" , 0);
}

require_once JPATH_COMPONENT . '/jevents.defines.php';

$isMobile = false;
jimport("joomla.environment.browser");
$browser = Browser::getInstance();

$registry = JevRegistry::getInstance("jevents");

// Load Joomla Core scripts
HTMLHelper::_('behavior.core', true);

// This loads jQuery too!
JHtml::_('jquery.framework');
// jQnc not only fixes noConflict it creates the jQuery alias
// we use in JEvents "jevqc" so we always need it
JEVHelper::script("components/com_jevents/assets/js/jQnc.js");

$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
$newparams = Factory::getApplication('site')->getParams();

if (strpos($params->get('framework', 'bootstrap'), 'bootstrap') === 0 || $params->get('framework', 'bootstrap') == 'native')
{
	if (version_compare(JVERSION, '4.0', 'lt'))
	{
		JevHtmlBootstrap::framework();
	}
	JevHtmlBootstrap::loadCss();
	HTMLHelper::stylesheet('media/system/css/joomla-fontawesome.min.css');
}
else
{
	$params->set('bootstrapchosen', 0);
	$params->set('bootstrapcss', 0);
	$newparams->set('bootstrapchosen', 0);
	$newparams->set('bootstrapcss', 0);
}

JevModal::modal();

// Because the application sets a default page title,
// we need to get it from the menu item itself
// WP TODO sort out menus!
$menu = $app->getMenu()->getActive();
if ($menu)
{
	$newparams->def('page_heading', $newparams->get('page_title', $menu->title));
}
else
{
	$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
	$newparams->def('page_heading', $params->get('page_title'));
}

// handle global menu item parameter for viewname
$com_calViewName = $newparams->get('com_calViewName', "");
if ($com_calViewName == "global" || $com_calViewName == "")
{
	$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
	$newparams->set('com_calViewName', $params->get('com_calViewName'));
}

// handle global menu item parameter for com_showrepeats
$com_showrepeats = $newparams->get('com_showrepeats', "");
if ($com_showrepeats === "-1" || $com_showrepeats === "")
{
	$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
	$newparams->set('com_showrepeats', $params->get('com_showrepeats'));
}

// handle global menu item parameter for com_startday
$com_startday = $newparams->get('com_starday', "");
if ($com_startday === "-1" || $com_startday === "")
{
	$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
	$newparams->set('com_starday', $params->get('com_starday'));
}

// disable caching for form POSTS
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$newparams->set('com_cache', 0);
}

// Menu item specific parameters
$newParamsArray = $newparams->toArray();
$keys = array_keys($newParamsArray);
$mispecifics = preg_grep("/^mispecific_/", $keys);

if ($mispecifics)
{
	foreach ($mispecifics as $mispecific)
	{
		$pattern = str_replace("mispecific_", "", $mispecific);
		$mispecifics2 = preg_grep("/^mi" . $pattern . "_/", $keys);
		// If this group of menu item specific parameters are enabled then use them
		if ($mispecifics2 && $newparams->get($mispecific, 0))
		{
			foreach ($mispecifics2 as $mispecific2)
			{
				$key = str_replace("mi" . $pattern . "_", "", $mispecific2);
				$newparams->set($key, $newparams->get($mispecific2));
			}
		}
	}
}

$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
$component         = ComponentHelper::getComponent(JEV_COM_COMPONENT);
$component->params = $newparams;

JEVHelper::setupWordpress();

$isMobile = $browser->isMobile();
// Joomla isMobile method doesn't identify all android phones
if (!$isMobile && isset($_SERVER['HTTP_USER_AGENT']))
{
	if (stripos($_SERVER['HTTP_USER_AGENT'], 'android') > 0 || stripos($_SERVER['HTTP_USER_AGENT'], 'blackberry') > 0)
	{
		$isMobile = true;
	}
	else if (stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') > 0 || stripos($_SERVER['HTTP_USER_AGENT'], 'ipod') > 0)
	{
		$isMobile = true;
	}
}

$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
// Smart phone theme is discontinued so disable it here
$params->set("disablesmartphone", 1);

if ($isMobile || strpos($app->getTemplate(), 'mobile_') === 0 || (class_exists("T3Common") && class_exists("T3Parameter") && T3Common::mobile_device_detect()) || $input->get("jEV", "") == "smartphone")
{
	if (!$params->get("disablesmartphone"))
	{
		$input->set("jevsmartphone", 1);
		if (Folder::exists(JEV_VIEWS . "/smartphone"))
		{
			$input->set("jEV", "smartphone");
		}
		$params->set('iconicwidth', "scalable");
		$params->set('extpluswidth', "scalable");
		$params->set('ruthinwidth', "scalable");
	}
	$params->set("isSmartphone", 1);
}

// See http://www.php.net/manual/en/timezones.php
$tz = $params->get("icaltimezonelive", "");
if ($tz != "" && is_callable("date_default_timezone_set"))
{
	$timezone = date_default_timezone_get();
	date_default_timezone_set($tz);
	$registry->set("jevents.timezone", $timezone);
}

// Must also load backend language files
$lang = Factory::getLanguage();
$lang->load(JEV_COM_COMPONENT, JPATH_ADMINISTRATOR);

// Load Site specific language overrides
$lang->load(JEV_COM_COMPONENT, JPATH_THEMES . '/' . $app->getTemplate());

// Split task into command and task
$cmd = $input->getCmd('task', false);

if (!$cmd || !is_string($cmd) || strpos($cmd, '.') == false)
{
	$view   = $input->getCmd('view', false);
	$layout = $input->getCmd('layout', "show");
	if ($view && $layout)
	{
		$cmd = $view . '.' . $layout;
	}
	else
		$cmd = "month.calendar";
}

PluginHelper::importPlugin("jevents");

// Should the output come from one of the plugins instead?
if (strpos($cmd, "plugin.") === 0)
{
	Factory::getApplication()->triggerEvent('onJEventsPluginOutput');
	return;
}

if (strpos($cmd, '.') != false)
{
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

	// check view input is compatible - can be a problem on some form submissions
	if ($input->getCmd("view", "") != "" && $input->getCmd("view", "") != $controllerName)
	{
		$input->set("view", $controllerName);
	}

	// Define the controller name and path
	$controllerName = strtolower($controllerName);
	$controllerPath = JPATH_COMPONENT . '/' . 'controllers' . '/' . $controllerName . '.php';
	//$controllerName = "Front".$controllerName;
	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath))
	{
		require_once($controllerPath);
	}
	else
	{
		$app->enqueueMessage('404 - ' . Text::sprintf("JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS", $controllerName), 'error');

		//$app->enqueueMessage('Invalid Controller - ' . $controllerName);
		$cmd = "month.calendar";
		list($controllerName, $task) = explode('.', $cmd);
		$controllerPath = JPATH_COMPONENT . '/' . 'controllers' . '/' . $controllerName . '.php';
		require_once($controllerPath);
	}
}
else
{
	// Base controller, just set the task
	$controllerName = null;
	$task           = $cmd;
}

// Make the task available later
$input->set("jevtask", $cmd);
$input->set("jevcmd", $cmd);

// Are all Jevents pages apart from crawler, rss and details pages to be redirected for search engines?
if (in_array($cmd, array("year.listevents", "month.calendar", "week.listevents", "day.listevents", "cat.listevents", "search.form",
	"search.results", "admin.listevents", "jevent.edit", "icalevent.edit", "icalevent.publish", "icalevent.unpublish",
	"icalevent.editcopy", "icalrepeat.edit", "jevent.delete", "icalevent.delete", "icalrepeat.delete", "icalrepeat.deletefuture")))
{
	$browser = Browser::getInstance();
	if ($params->get("redirectrobots", 0) && ($browser->isRobot() || strpos($browser->getAgentString(), "bingbot") !== false))
	{
		// redirect  to crawler menu item
		$Itemid = $params->get("robotmenuitem", 0);
		$app->redirect(Route::_("index.php?option=com_jevents&task=crawler.listevents&Itemid=$Itemid"));
	}
}

//list($usec, $sec) = explode(" ", microtime());
//$starttime = (float) $usec + (float) $sec;

//list ($usec, $sec) = explode(" ", microtime());
//$time_end = (float) $usec + (float) $sec;
//echo  "JEvents before importPlugin = ".round($time_end - $starttime, 4)."<br/>";

PluginHelper::importPlugin("jevents");

//list ($usec, $sec) = explode(" ", microtime());
//$time_end = (float) $usec + (float) $sec;
//echo  "JEvents after importPlugin = ".round($time_end - $starttime, 4)."<br/>";

// Make sure the view specific language file is loaded
JEV_CommonFunctions::loadJEventsViewLang();

// Set the name for the controller and instantiate it
$controllerClass = ucfirst($controllerName) . 'Controller';
if (class_exists($controllerClass))
{
	$controller = new $controllerClass();
}
else
{
	$app->enqueueMessage('Invalid Controller Class - ' . $controllerClass);
	$cmd = "month.calendar";
	list($controllerName, $task) = explode('.', $cmd);
	$input->set("jevtask", $cmd);
	$input->set("jevcmd", $cmd);
	$controllerClass = ucfirst($controllerName) . 'Controller';
	$controllerPath  = JPATH_COMPONENT . '/' . 'controllers' . '/' . $controllerName . '.php';
	require_once($controllerPath);
	$controller = new $controllerClass();
}

// create live bookmark if requested
JEVHelper::processLiveBookmmarks();

$cfg = JEVConfig::getInstance();

// Add reference for constructor in registry - unfortunately there is no add by reference method
// we rely on php efficiency to not create a copy
$registry = JevRegistry::getInstance("jevents");
$registry->set("jevents.controller", $controller);
// record what is running - used by the filters
$registry->set("jevents.activeprocess", "component");

// Stop viewing ALL events - it could take VAST amounts of memory.  But allow for CSV export
if ($cfg->get('blockall', 0) && !$cfg->get("csvexport", 0) && ($input->getInt("limit", -1) == 0 || $input->getInt("limit", -1) > 100))
{
	$input->set("limit", 100);
	$app->setUserState("limit", 100);
}

// Must reset the timezone back!!
if ($tz && is_callable("date_default_timezone_set"))
{
	date_default_timezone_set($timezone);
}

JEVHelper::getFilterValues();

// If Joomla caching is enabled then we have to manage progressive caching and ensure that session data is taken into account.
JEVHelper::parameteriseJoomlaCache();

//list ($usec, $sec) = explode(" ", microtime());
//$time_end = (float) $usec + (float) $sec;
//echo  "JEvents component pre task = ".round($time_end - $starttime, 4)."<br/>";

//HTMLHelper::_('bootstrap.popover', '.hasjevtip');
// focus event is causing problems on Apple devices!!
JevModal::popover('.hasjevtip' , array("trigger"=>"hover", "placement"=>"top", "container"=>"#jevents_body",  "html" => true,  "delay"=> array( "show"=> 150, "hide"=> 150 )));

// Perform the Request task
$controller->execute($task);

//list ($usec, $sec) = explode(" ", microtime());
//$time_end = (float) $usec + (float) $sec;
//echo  "JEvents component post task   = ".round($time_end - $starttime, 4)."<br/>";

// Set the browser title to include site name if required
$title = Factory::getDocument()->GetTitle();
$app   = $app;
if (empty($title))
{
	$title = $app->getCfg('sitename');
}
elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
{
	$title = Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
}
elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
{
	$title = Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
}
if ($input->getCmd("format") != "feed")
{
	Factory::getDocument()->SetTitle($title);
}

// Redirect if set by the controller
$controller->redirect();

/*
 // Experimental code for capturing out of memory problems
ini_set('display_errors', false);
error_reporting(-1);

register_shutdown_function(function() {
	$error = error_get_last();
	if (null !== $error)
	{
		if (isset($error["message"]) && strpos($error["message"], "bytes exhausted") > 0)
		{
			echo "ran out of memory";
		}
		else
		{
			echo 'Caught at shutdown';
		}
	}
	else
		echo "normal shutdown";
});

	// Simulate memory overload
   // while(true)
    //{
     //   $data .= str_repeat('#', PHP_INT_MAX);
   // }

*/
