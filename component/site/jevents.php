<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevents.php 3551 2012-04-20 09:41:37Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

jimport('joomla.filesystem.path');

// For development performance testing only
/*
  $db	= JFactory::getDBO();
  $db->setQuery("SET SESSION query_cache_type = OFF");
  $db->query();

  $cfg = JEVConfig::getInstance();
  $cfg->set('jev_debug', 1);
 */

include_once(JPATH_COMPONENT . '/' . "jevents.defines.php");

$isMobile = false;
jimport("joomla.environment.browser");
$browser = JBrowser::getInstance();

$registry = JRegistry::getInstance("jevents");
// In Joomla 1.6 JComponentHelper::getParams(JEV_COM_COMPONENT) is a clone so the menu params do not propagate so we force this here!

if (JevJoomlaVersion::isCompatible("3.0")){
	JHtml::_('jquery.framework');
	JHtml::_('behavior.framework', true);
	JHtml::_('bootstrap.framework');
	if ( JComponentHelper::getParams(JEV_COM_COMPONENT)->get("fixjquery",1)){
		JHTML::script("components/com_jevents/assets/js/jQnc.js");
		// this script should come after all the URL based scripts in Joomla so should be a safe place to know that noConflict has been set
		JFactory::getDocument()->addScriptDeclaration( "checkJQ();");
	}
}
else if ( JComponentHelper::getParams(JEV_COM_COMPONENT)->get("fixjquery",1)){
	// Make loading this conditional on config option
	JFactory::getDocument()->addScript("//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js");
        //JFactory::getDocument()->addScript("//www.google.com/jsapi");
	JHTML::script("components/com_jevents/assets/js/jQnc.js");
	//JHTML::script("components/com_jevents/assets/js/bootstrap.min.js");
	//JHTML::stylesheet("components/com_jevents/assets/css/bootstrap.css");
        // this script should come after all the URL based scripts in Joomla so should be a safe place to know that noConflict has been set
        JFactory::getDocument()->addScriptDeclaration( "checkJQ();");
}
 /*
 * include_once JPATH_ROOT . '/media/akeeba_strapper/strapper.php';
$jevversion = JEventsVersion::getInstance();
AkeebaStrapper::$tag = $jevversion->getShortVersion();
AkeebaStrapper::bootstrap();
AkeebaStrapper::jQueryUI();
 * 
 */

$newparams = JFactory::getApplication('site')->getParams();
// Because the application sets a default page title,
// we need to get it from the menu item itself
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
// disable caching for form POSTS
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$newparams->set('com_cache', 0);
}

$component =  JComponentHelper::getComponent(JEV_COM_COMPONENT);
$component->params = & $newparams;

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
		$params->set('iconicwidth', 485);
		$params->set('extpluswidth', 485);
		$params->set('ruthinwidth', 485);
	}
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

// disable Zend php4 compatability mode
@ini_set("zend.ze1_compatibility_mode", "Off");

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
		return JError::raiseError(404, 'Invalid Controller - ' . $controllerName);
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

// Stop viewing ALL events - it could take VAST amounts of memory
if ($cfg->get('blockall', 0) && ( JRequest::getInt("limit", -1) == 0 || JRequest::getInt("limit", -1) > 100 ))
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