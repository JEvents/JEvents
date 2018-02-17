<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevents.php 3551 2012-04-20 09:41:37Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

jimport('joomla.filesystem.path');

// For development performance testing only
/*
  $db	= JFactory::getDbo();
  $db->setQuery("SET SESSION query_cache_type = OFF");
  $db->execute();

  $cfg = JEVConfig::getInstance();
  $cfg->set('jev_debug', 1);
 */

include_once(JPATH_COMPONENT . '/' . "jevents.defines.php");

$isMobile = false;
jimport("joomla.environment.browser");
$browser = JBrowser::getInstance();

$registry = JRegistry::getInstance("jevents");
// In Joomla 1.6 JComponentHelper::getParams(JEV_COM_COMPONENT) is a clone so the menu params do not propagate so we force this here!

// Load Joomla Core scripts for sites that don't load MooTools;
JHtml::_('behavior.core', true);

// This loads jQuery too!
JevHtmlBootstrap::framework();

// jQnc not only fixes noConflict it creates the jQuery alias we use in JEvents "jevqc" so we always need it
	JEVHelper::script("components/com_jevents/assets/js/jQnc.js");
if ( JComponentHelper::getParams(JEV_COM_COMPONENT)->get("fixjquery",1)){
	// this script should come after all the URL based scripts in Joomla so should be a safe place to know that noConflict has been set
	JFactory::getDocument()->addScriptDeclaration( "checkJQ();");
}

if (JComponentHelper::getParams(JEV_COM_COMPONENT)->get("bootstrapcss", 1)==1)
{
	// This version of bootstrap has maximum compatibility with JEvents due to enhanced namespacing
	JHTML::stylesheet("com_jevents/bootstrap.css", array(), true);
	// Responsive version of bootstrap with maximum compatibility with JEvents due to enhanced namespacing
	JHTML::stylesheet("com_jevents/bootstrap-responsive.css", array(), true);
}
else if (JComponentHelper::getParams(JEV_COM_COMPONENT)->get("bootstrapcss", 1)==2)
{
	JHtmlBootstrap::loadCss();
}


$newparams = JFactory::getApplication('site')->getParams();
// Because the application sets a default page title,
// we need to get it from the menu item itself
// WP TODO sort out menus!
$menu = JFactory::getApplication()->getMenu()->getActive();
if ($menu)
{
	$newparams->def('page_heading', $newparams->get('page_title', $menu->title));
}
else
{
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	$newparams->def('page_heading', $params->get('page_title'));
}

// handle global menu item parameter for viewname
$com_calViewName = $newparams->get('com_calViewName', "");
if ($com_calViewName == "global" || $com_calViewName == "")
{
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	$newparams->set('com_calViewName', $params->get('com_calViewName'));
}

// handle global menu item parameter for com_showrepeats
$com_showrepeats = $newparams->get('com_showrepeats', "");
if ($com_showrepeats === "-1" || $com_showrepeats === "")
{
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	$newparams->set('com_showrepeats', $params->get('com_showrepeats'));
}

// handle global menu item parameter for com_startday
$com_startday = $newparams->get('com_starday', "");
if ($com_startday === "-1" || $com_startday === "")
{
	$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
	$newparams->set('com_starday', $params->get('com_starday'));
}

// disable caching for form POSTS
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$newparams->set('com_cache', 0);
}

$component =  JComponentHelper::getComponent(JEV_COM_COMPONENT);
$component->params =  $newparams;

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

$params = JComponentHelper::getParams(JEV_COM_COMPONENT);

if ($isMobile || strpos(JFactory::getApplication()->getTemplate(), 'mobile_') === 0 || (class_exists("T3Common") && class_exists("T3Parameter") && T3Common::mobile_device_detect()) || JRequest::getVar("jEV", "") == "smartphone")
{
	if (!$params->get("disablesmartphone"))
	{
		JRequest::setVar("jevsmartphone", 1);
		if (JFolder::exists(JEV_VIEWS . "/smartphone"))
		{
                    JRequest::setVar("jEV", "smartphone");
		}
		$params->set('iconicwidth', "scalable");
		$params->set('extpluswidth', "scalable");
		$params->set('ruthinwidth', "scalable");
	}
        $params->set("isSmartphone",1);
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
$lang = JFactory::getLanguage();
$lang->load(JEV_COM_COMPONENT, JPATH_ADMINISTRATOR);

// Load Site specific language overrides
$lang->load(JEV_COM_COMPONENT, JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate());

// Split task into command and task
$cmd = JRequest::getCmd('task', false);

if (!$cmd || !is_string($cmd) || strpos($cmd, '.') == false)
{
	$view = JRequest::getCmd('view', false);
	$layout = JRequest::getCmd('layout', "show");
	if ($view && $layout)
	{
		$cmd = $view . '.' . $layout;
	}
	else
		$cmd = "month.calendar";
}

if (strpos($cmd, '.') != false)
{
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

        // check view input is compatible - can be a problem on some form submissions
        if (JRequest::getCmd("view","")!="" &&  JRequest::getCmd("view","")!=$controllerName){
            JRequest::setVar("view",$controllerName);
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
		JFactory::getApplication()->enqueueMessage('404 - '.  JText::sprintf("JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS", $controllerName), 'error');

		//JFactory::getApplication()->enqueueMessage('Invalid Controller - ' . $controllerName);
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
	$task = $cmd;
}

// Make the task available later
JRequest::setVar("jevtask", $cmd);
JRequest::setVar("jevcmd", $cmd);

// Are all Jevents pages apart from crawler, rss and details pages to be redirected for search engines?
if (in_array($cmd, array("year.listevents", "month.calendar", "week.listevents", "day.listevents", "cat.listevents", "search.form",
			"search.results", "admin.listevents", "jevent.edit", "icalevent.edit", "icalevent.publish", "icalevent.unpublish",
			"icalevent.editcopy", "icalrepeat.edit", "jevent.delete", "icalevent.delete", "icalrepeat.delete", "icalrepeat.deletefuture")))
{
	$browser = JBrowser::getInstance();
	if ($params->get("redirectrobots", 0) && ($browser->isRobot() || strpos($browser->getAgentString(), "bingbot") !== false))
	{
		// redirect  to crawler menu item
		$Itemid = $params->get("robotmenuitem", 0);
		JFactory::getApplication()->redirect(JRoute::_("index.php?option=com_jevents&task=crawler.listevents&Itemid=$Itemid"));
	}
}

//list($usec, $sec) = explode(" ", microtime());
//$starttime = (float) $usec + (float) $sec;

//list ($usec, $sec) = explode(" ", microtime());
//$time_end = (float) $usec + (float) $sec;
//echo  "JEvents before importPlugin = ".round($time_end - $starttime, 4)."<br/>";

JPluginHelper::importPlugin("jevents");

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
	JFactory::getApplication()->enqueueMessage('Invalid Controller Class - ' . $controllerClass);
	$cmd = "month.calendar";
	list($controllerName, $task) = explode('.', $cmd);
	JRequest::setVar("jevtask", $cmd);
	JRequest::setVar("jevcmd", $cmd);
	$controllerClass = ucfirst($controllerName) . 'Controller';
	$controllerPath = JPATH_COMPONENT . '/' . 'controllers' . '/' . $controllerName . '.php';
	require_once($controllerPath);
	$controller = new $controllerClass();
}

// create live bookmark if requested
JEVHelper::processLiveBookmmarks();

$cfg = JEVConfig::getInstance();

// Add reference for constructor in registry - unfortunately there is no add by reference method
// we rely on php efficiency to not create a copy
$registry = JRegistry::getInstance("jevents");
$registry->set("jevents.controller", $controller);
// record what is running - used by the filters
$registry->set("jevents.activeprocess", "component");

// Stop viewing ALL events - it could take VAST amounts of memory.  But allow for CSV export
if ($cfg->get('blockall', 0) && !$cfg->get("csvexport",0) && ( JRequest::getInt("limit", -1) == 0 || JRequest::getInt("limit", -1) > 100 ))
{
	JRequest::setVar("limit", 100);
	JFactory::getApplication()->setUserState("limit", 100);
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

// Perform the Request task
$controller->execute($task);

//list ($usec, $sec) = explode(" ", microtime());
//$time_end = (float) $usec + (float) $sec;
//echo  "JEvents component post task   = ".round($time_end - $starttime, 4)."<br/>";

// Set the browser title to include site name if required
$title =  JFactory::getDocument()->GetTitle();
$app = JFactory::getApplication();
if (empty($title)) {
	$title = $app->getCfg('sitename');
}
elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
	$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
}
elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
	$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
}
if (JRequest::getCmd("format")!="feed"){
	JFactory::getDocument()->SetTitle($title);
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
