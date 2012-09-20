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

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.filesystem.path');
/*
// JevDate test
jimport("joomla.utilities.date");
$date = new JevDate("1.30pm 12 March 2011", new DateTimeZone('America/New_York'));
echo $date->format("Y-m-d H:i:s")."<br/>";
echo "<hr/>";
$date->add(new DateInterval('P1D'));
echo $date->format("Y-m-d H:i:s")."<br/>";
echo "<hr/>";
$date = new JevDate("1.30pm 12 March 2011", new DateTimeZone('UTC'));
echo $date->format("Y-m-d H:i:s")."<br/>";
echo "<hr/>";
$date->add(new DateInterval('P1D'));
echo $date->format("Y-m-d H:i:s")."<br/>";
echo "<hr/>";

$date = new JevDate("1.30pm 12 March 2011", new DateTimeZone('America/New_York'));
echo $date->format("Y-m-d H:i:s")."<br/>";
echo "<hr/>";
$date->modify("+1 day");
echo $date->format("Y-m-d H:i:s")."<br/>";
echo "<hr/>";
*/
// For development performance testing only
/*
$db	=& JFactory::getDBO();
$db->setQuery("SET SESSION query_cache_type = OFF");
$db->query();

$cfg = & JEVConfig::getInstance();
$cfg->set('jev_debug', 1);
*/

include_once(JPATH_COMPONENT.DS."jevents.defines.php");

$isMobile = false;
jimport("joomla.environment.browser");
$browser = JBrowser::getInstance();

$registry	=& JRegistry::getInstance("jevents");
// In Joomla 1.6 JComponentHelper::getParams(JEV_COM_COMPONENT) is a clone so the menu params do not propagate so we force this here!
if (JVersion::isCompatible("1.6.0")){
	$newparams	= JFactory::getApplication('site')->getParams();
	// Because the application sets a default page title,
	// we need to get it from the menu item itself
	$menu = JFactory::getApplication()->getMenu()->getActive();
	if ($menu) {
		$newparams->def('page_heading', $newparams->get('page_title', $menu->title));
	}
	else {
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$newparams->def('page_heading', $params->get('page_title')) ;
	}
	
	// handle global menu item parameter for viewname
	$com_calViewName = $newparams->get('com_calViewName',"");
	if ($com_calViewName == "global" || $com_calViewName == ""){
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$newparams->set('com_calViewName',$params->get('com_calViewName'));
	}

	$component =& JComponentHelper::getComponent(JEV_COM_COMPONENT);
	$component->params =& $newparams;
	
	$isMobile = $browser->isMobile();
	// Joomla isMobile method doesn't identify all android phones
	if (!$isMobile && isset($_SERVER['HTTP_USER_AGENT'])){
		if (stripos($_SERVER['HTTP_USER_AGENT'], 'android') >0) {
			$isMobile = true;
		}
		else 	if (stripos($_SERVER['HTTP_USER_AGENT'], 'iphone')>0 || stripos($_SERVER['HTTP_USER_AGENT'], 'ipod')>0)  {
			$isMobile = true;
		}
	}
}
else {
	$isMobile = $browser->_mobile;
}
$params =& JComponentHelper::getParams(JEV_COM_COMPONENT);

if ($isMobile || strpos(JFactory::getApplication()->getTemplate(), 'mobile_')===0 || (class_exists("T3Common") && class_exists("T3Parameter") && T3Common::mobile_device_detect()) || JRequest::getVar("jEV","")=="smartphone"){
	JRequest::setVar("jevsmartphone",1);
	if (JFolder::exists(JEV_VIEWS."/smartphone")){
		JRequest::setVar("jEV","smartphone");
	}
	$params->set('iconicwidth',485);
	$params->set('extpluswidth',485);
	$params->set('ruthinwidth',485);
}

// See http://www.php.net/manual/en/timezones.php
$tz=$params->get("icaltimezonelive","");
if ($tz!="" && is_callable("date_default_timezone_set")){
	$timezone= date_default_timezone_get();
	date_default_timezone_set($tz);
	$registry->setValue("jevents.timezone",$timezone);
}

if (!JVersion::isCompatible("1.6.0")){
	// Multi-category events only supported in Joomla 2.5 + so disable elsewhere
	$params->set('multicategory',0);
}
// Must also load backend language files
$lang =& JFactory::getLanguage();
$lang->load(JEV_COM_COMPONENT, JPATH_ADMINISTRATOR);

// Load Site specific language overrides
$lang->load(JEV_COM_COMPONENT, JPATH_THEMES.DS.JFactory::getApplication()->getTemplate());

// disable Zend php4 compatability mode
@ini_set("zend.ze1_compatibility_mode","Off");

// Split task into command and task
$cmd = JRequest::getCmd('task', false);

if (!$cmd || !is_string($cmd) || strpos($cmd, '.') == false) {
	$view =	JRequest::getCmd('view', false);
	$layout = JRequest::getCmd('layout', "show");
	if ($view && $layout){
		$cmd = $view.'.'.$layout;
	}
	else $cmd = "month.calendar";
}

if (strpos($cmd, '.') != false) {
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

	// Define the controller name and path
	$controllerName	= strtolower($controllerName);
	$controllerPath	= JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php';
	//$controllerName = "Front".$controllerName;

	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath)) {
		require_once($controllerPath);
	} else {
		JFactory::getApplication()->enqueueMessage('Invalid Controller - '.$controllerName );
		$cmd = "month.calendar";
		list($controllerName, $task) = explode('.', $cmd);
		$controllerPath	= JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php';
		require_once($controllerPath);
	}
} else {
	// Base controller, just set the task
	$controllerName = null;
	$task = $cmd;
}
// Make the task available later
JRequest::setVar("jevtask",$cmd);
JRequest::setVar("jevcmd",$cmd);

// Are all Jevents pages apart from crawler, rss and details pages to be redirected for search engines?
if (in_array($cmd, array("year.listevents","month.calendar","week.listevents","day.listevents","cat.listevents","search.form",
			"search.results","admin.listevents","jevent.edit","icalevent.edit","icalevent.publish","icalevent.unpublish",
			"icalevent.editcopy","icalrepeat.edit","jevent.delete","icalevent.delete","icalrepeat.delete","icalrepeat.deletefuture")))
{
	$browser = JBrowser::getInstance();
	if ($params->get("redirectrobots", 0) && ($browser->isRobot() || strpos ($browser->getAgentString(), "bingbot")!==false))
	{
		// redirect  to crawler menu item
		$Itemid = $params->get("robotmenuitem", 0);
		JFactory::getApplication()->redirect(JRoute::_("index.php?option=com_jevents&task=crawler.listevents&Itemid=$Itemid"));
	}
}

JPluginHelper::importPlugin("jevents");

// Make sure the view specific language file is loaded
JEV_CommonFunctions::loadJEventsViewLang();

// Set the name for the controller and instantiate it
$controllerClass = ucfirst($controllerName).'Controller';
if (class_exists($controllerClass)) {
	$controller = new $controllerClass();
} else {
	JFactory::getApplication()->enqueueMessage('Invalid Controller Class - '.$controllerClass );
	$cmd = "month.calendar";
	list($controllerName, $task) = explode('.', $cmd);
	JRequest::setVar("jevtask",$cmd);
	JRequest::setVar("jevcmd",$cmd);
	$controllerClass = ucfirst($controllerName).'Controller';
	$controllerPath	= JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php';
	require_once($controllerPath);
	$controller = new $controllerClass();
}

// create live bookmark if requested
$cfg = & JEVConfig::getInstance();
if ($cfg->get('com_rss_live_bookmarks')) {
	$Itemid = JRequest::getInt('Itemid', 0);
	$rssmodid = $cfg->get('com_rss_modid', 0);
	// do not use JRoute since this creates .rss link which normal sef can't deal with
	$rssLink = 'index.php?option='.JEV_COM_COMPONENT.'&amp;task=modlatest.rss&amp;format=feed&amp;type=rss&amp;Itemid='.$Itemid.'&amp;modid='.$rssmodid;
	$rssLink = JUri::root().$rssLink;
	
	if (JVersion::isCompatible("1.6.0")){
		if (method_exists(JFactory::getDocument(),"addHeadLink")){
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			JFactory::getDocument()->addHeadLink($rssLink, 'alternate', 'rel', $attribs);
		}
	}
	else {
		$rss = '<link href="' .$rssLink .'"  rel="alternate"  type="application/rss+xml" title="JEvents - RSS 2.0 Feed" />'. "\n";
		JFactory::getApplication()->addCustomHeadTag( $rss );
	}

	$rssLink =  'index.php?option='.JEV_COM_COMPONENT.'&amp;task=modlatest.rss&amp;format=feed&amp;type=atom&amp;Itemid='.$Itemid.'&amp;modid='.$rssmodid;
	$rssLink = JUri::root().$rssLink;
	//$rssLink = JRoute::_($rssLink);
	if (JVersion::isCompatible("1.6.0")){
		if (method_exists(JFactory::getDocument(),"addHeadLink")){
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			JFactory::getDocument()->addHeadLink($rssLink, 'alternate', 'rel', $attribs);
		}
	}
	else {
		$rss = '<link href="' .$rssLink .'"  rel="alternate"  type="application/rss+xml" title="JEvents - Atom Feed" />'. "\n";
		JFactory::getApplication()->addCustomHeadTag( $rss );
	}

}

// Add reference for constructor in registry - unfortunately there is no add by reference method
// we rely on php efficiency to not create a copy
$registry	=& JRegistry::getInstance("jevents");
$registry->setValue("jevents.controller",$controller);
// record what is running - used by the filters
$registry->setValue("jevents.activeprocess","component");

// Stop viewing ALL events - it could take VAST amounts of memory
if ($cfg->get('blockall', 0) && ( JRequest::getInt("limit" , -1) == 0 || JRequest::getInt("limit" , -1) >100 )){
	JRequest::setVar("limit",100);
}
// Perform the Request task
$controller->execute($task);

// Must reset the timezone back!!
if ($tz && is_callable("date_default_timezone_set")){
	date_default_timezone_set($timezone);
}

// Redirect if set by the controller
$controller->redirect();
