<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: jevents.php 3552 2012-04-20 09:41:53Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2012 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

if (version_compare(phpversion(), '5.0.0', '<')===true) {
	echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;"><div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;"><h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">'.JText::_("JEV_INVALID_PHP1").'</h3></div>'.JText::_("JEV_INVALID_PHP2").'</div>';
	return;
}
// remove metadata.xml if its there.
jimport('joomla.filesystem.file');
if (JFile::exists(JPATH_COMPONENT_SITE.'/'."metadata.xml")){
	JFile::delete(JPATH_COMPONENT_SITE.'/'."metadata.xml");
}

//error_reporting(E_ALL);

jimport('joomla.filesystem.path');

// Get Joomla version.
$version = new JVersion();
$jver = explode( '.', $version->getShortVersion() );

//version_compare(JVERSION,'1.5.0',">=")
if (!isset($option))  $option = JRequest::getCmd("option"); // 1.6 mod
define("JEV_COM_COMPONENT",$option);
define("JEV_COMPONENT",str_replace("com_","",$option));

include_once(JPATH_COMPONENT_ADMINISTRATOR.'/'.JEV_COMPONENT.".defines.php");

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
	JFactory::getDocument()->addScript("//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js");
	//JFactory::getDocument()->addScript("//www.google.com/jsapi");
	JHTML::script("components/com_jevents/assets/js/jQnc.js");
	//JHTML::script("components/com_jevents/assets/js/bootstrap.min.js");
	//JHTML::stylesheet("components/com_jevents/assets/css/bootstrap.css");
	// this script should come after all the URL based scripts in Joomla so should be a safe place to know that noConflict has been set
	JFactory::getDocument()->addScriptDeclaration( "checkJQ();");
}

$registry	= JRegistry::getInstance("jevents");
/*
 * frontend only!
// In Joomla 1.6 JComponentHelper::getParams(JEV_COM_COMPONENT) is a clone so the menu params do not propagate so we force this here!
if (JevJoomlaVersion::isCompatible("1.6.0")){
	$newparams	= JFactory::getApplication()->getParams();
	$component = JComponentHelper::getComponent(JEV_COM_COMPONENT);
	$component->params =& $newparams;
}
*/
// See http://www.php.net/manual/en/timezones.php

// If progressive caching is enabled then remove the component params from the cache!
/* Bug fixed in Joomla 3.2.1 ?? - not always it appears */
$joomlaconfig = JFactory::getConfig();
if ($joomlaconfig->get("caching",0)){
	$cacheController = JFactory::getCache('_system', 'callback');
	$cacheController->cache->remove("com_jevents");
}

$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("icaltimezonelive","")!="" && is_callable("date_default_timezone_set") && $params->get("icaltimezonelive","")!=""){
	$timezone= date_default_timezone_get();
	date_default_timezone_set($params->get("icaltimezonelive",""));
	$registry->set("jevents.timezone",$timezone);
}

// Thanks to ssobada
$authorisedonly = $params->get("authorisedonly", 0);
$user      = JFactory::getUser();
//Stop if user is not authorised to access JEevents CPanel
if (!$authorisedonly && !$user->authorise('core.manage',      'com_jevents')) {
    return;
}

// Backend of JEvents needs Boostrap and jQuery
/*
if (JevJoomlaVersion::isCompatible("3.0")){
	JHtml::_('jquery.framework');
	JHtml::_('behavior.framework', true);
	JHtml::_('bootstrap.framework');
	JHTML::stylesheet("components/com_jevents/assets/css/bootstrap.css");
}
else {
	// Make loading this conditional on config option
	JFactory::getDocument()->addScript("//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js");
	JHTML::script("components/com_jevents/assets/js/jQnc.js");
	JHTML::script("components/com_jevents/assets/js/bootstrap.min.js");
	JHTML::stylesheet("components/com_jevents/assets/css/bootstrap.css");
}

*/

// Must also load frontend language files
$lang = JFactory::getLanguage();
$lang->load(JEV_COM_COMPONENT, JPATH_SITE);

if (!version_compare(JVERSION,'1.6.0',">=")){
	// Load Site specific language overrides - can't use getTemplate since wer'e in the admin interface
	$db = JFactory::getDBO();
	$query = 'SELECT template'
	. ' FROM #__templates_menu'
	. ' WHERE client_id = 0 AND menuid=0'
	. ' ORDER BY menuid DESC'
	. ' LIMIT 1'
	;
	$db->setQuery($query);
	$template = $db->loadResult();
	$lang->load(JEV_COM_COMPONENT, JPATH_SITE.'/'."templates".'/'.$template);
}
// disable Zend php4 compatability mode
@ini_set("zend.ze1_compatibility_mode","Off");

// Split tasl into command and task
$cmd = JRequest::getCmd('task', 'cpanel.show');

if (strpos($cmd, '.') != false) {
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

	// Define the controller name and path
	$controllerName	= strtolower($controllerName);
	$controllerPath	= JPATH_COMPONENT.'/'.'controllers'.'/'.$controllerName.'.php';
	$controllerName = "Admin".$controllerName;

	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath)) {
		require_once($controllerPath);
	} else {
		JError::raiseError(500, 'Invalid Controller');
	}
} else {
	// Base controller, just set the task
	$controllerName = null;
	$task = $cmd;
}

// Make the task available later
JRequest::setVar("jevtask",$cmd);
JRequest::setVar("jevcmd",$cmd);

JPluginHelper::importPlugin("jevents");

// Make this a config option - should not normally be needed
//$db = JFactory::getDBO();
//$db->setQuery( "SET SQL_BIG_SELECTS=1");
//$db->query();

// Set the name for the controller and instantiate it
$controllerClass = ucfirst($controllerName).'Controller';
if (class_exists($controllerClass)) {
	$controller = new $controllerClass();
} else {
	JError::raiseError(500, 'Invalid Controller Class - '.$controllerClass );
}

// record what is running - used by the filters
$registry	= JRegistry::getInstance("jevents");
$registry->set("jevents.activeprocess","administrator");

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
