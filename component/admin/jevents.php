<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevents.php 3552 2012-04-20 09:41:53Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined('JPATH_BASE') or die('No Direct Access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

ob_start();

$input = Factory::getApplication()->input;

if (version_compare(phpversion(), '5.0.0', '<') === true)
{
	echo '<div style="font:12px/1.35em arial, helvetica, sans-serif;"><div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;"><h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">' . Text::_("JEV_INVALID_PHP1") . '</h3></div>' . Text::_("JEV_INVALID_PHP2") . '</div>';

	return;
}

if (!defined('GSLMSIE10') && isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "Internet Explorer") !== false))
{
	define ("GSLMSIE10" , 1);
}
else if(!defined('GSLMSIE10'))
{
	define ("GSLMSIE10" , 0);
}

// remove metadata.xml if its there.
jimport('joomla.filesystem.file');

if (File::exists(JPATH_COMPONENT_SITE . '/' . "metadata.xml"))
{
	File::delete(JPATH_COMPONENT_SITE . '/' . "metadata.xml");
}

//error_reporting(E_ALL);

jimport('joomla.filesystem.path');

// Get Joomla version.
$version = new Version();
$jver    = explode('.', $version->getShortVersion());

//version_compare(JVERSION,'1.5.0',">=")
if (!isset($option)) $option = $input->getCmd("option"); // 1.6 mod
define("JEV_COM_COMPONENT", $option);
define("JEV_COMPONENT", str_replace("com_", "", $option));

include_once(JPATH_COMPONENT_ADMINISTRATOR . '/' . JEV_COMPONENT . ".defines.php");

// Load Joomla Core scripts for sites that don't load MooTools;
HTMLHelper::_('behavior.core', true);

HTMLHelper::_('jquery.framework');
// AIM TO REMOVE THIS - loading of MooTools should not be necessary !!!
// HTMLHelper::_('behavior.framework', true);

JEVHelper::script("jQnc.js", "components/com_jevents/assets/js/");


$registry = JevRegistry::getInstance("jevents");

// See http://www.php.net/manual/en/timezones.php

// If progressive caching is enabled then remove the component params from the cache!
/* Bug fixed in Joomla! 3.2.1 ?? - not always it appears */
$joomlaconfig = Factory::getConfig();
if ($joomlaconfig->get("caching", 0))
{
	$cacheController = Factory::getCache('_system', 'callback');
	$cacheController->cache->remove("com_jevents");
}

$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
if ($params->get("icaltimezonelive", "") != "" && is_callable("date_default_timezone_set") && $params->get("icaltimezonelive", "") != "")
{
	$timezone = date_default_timezone_get();
	date_default_timezone_set($params->get("icaltimezonelive", ""));
	$registry->set("jevents.timezone", $timezone);
}

// Thanks to ssobada
$authorisedonly = $params->get("authorisedonly", 0);
$user           = Factory::getUser();
//Stop if user is not authorised to access JEvents CPanel
if (!$authorisedonly && !$user->authorise('core.manage', 'com_jevents'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}
$spcolourscheme = $params->get('spcolourscheme', '');
$disablesmartphone = (int) $params->get('disablesmartphone', 1);
if ($spcolourscheme !== '' && $disablesmartphone == 0 && $_SERVER['REQUEST_METHOD'] == 'GET')
{
	Factory::getApplication()->enqueueMessage(Text::_("COM_JEVENTS_SMARTPHONE_THEME_SHOULD_BE_DISABLED"), 'notice');
}

// Must also load frontend language files
$lang = Factory::getLanguage();
$lang->load(JEV_COM_COMPONENT, JPATH_SITE);

if (!version_compare(JVERSION, '1.6.0', ">="))
{
	// Load Site specific language overrides - can't use getTemplate since we are in the admin interface
	$db    = Factory::getDbo();
	$query = 'SELECT template'
		. ' FROM #__templates_menu'
		. ' WHERE client_id = 0 AND menuid=0'
		. ' ORDER BY menuid DESC'
		. ' LIMIT 1';
	$db->setQuery($query);
	$template = $db->loadResult();
	$lang->load(JEV_COM_COMPONENT, JPATH_SITE . '/' . "templates" . '/' . $template);
}

$landingpage = $params->get("landingpage", 'cpanel.cpanel');
// Split task into command and task
$cmd = $input->get('task', $landingpage);
//echo $cmd;die;

PluginHelper::importPlugin("jevents");

// Should the output come from one of the plugins instead?
if (strpos($cmd, "plugin.") === 0 && count(explode(".", $cmd)) == 2)
{
	Factory::getApplication()->triggerEvent('onJEventsPluginOutput');
	return;
}

//Time to handle view switching for our current setup for J3.7
$view = $input->get('view', '');
//Check the view and redirect if any match.
if ($view === 'customcss')
{
//	Factory::getApplication()->redirect('index.php?option=com_jevents&task=cpanel.custom_css');
	if ($cmd === $landingpage || strpos($cmd, '.') === 0)
	{
		$cmd = $view;
	}
	$controllerName = 'CustomCss';
}
else if($view === 'import') {
	if ($cmd === $landingpage || strpos($cmd, '.') === 0)
	{
		$cmd = $view;
	}
	$controllerName = 'Import';
}
else if($view === 'cpanel' && ($cmd === $landingpage || strpos($cmd, '.') === 0))
{
	$cmd = 'cpanel.show';
}

if ($view === 'supportinfo')
{
	Factory::getApplication()->redirect('index.php?option=com_jevents&task=cpanel.support');
}
if ($view === 'config')
{
	Factory::getApplication()->redirect('index.php?option=com_jevents&task=params.edit');
}
if ($view === 'icalevent')
{
	Factory::getApplication()->redirect('index.php?option=com_jevents&task=icalevent.list');
}
if ($view === 'icaleventform')
{
	Factory::getApplication()->redirect('index.php?option=com_jevents&task=icalevent.edit');
}
if ($view === 'categories')
{
	Factory::getApplication()->redirect('index.php?option=com_categories&view=com_categories&extension=com_jevents');
}


if (strpos($cmd, '.') !== false)
{
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

	// Define the controller name and path
	$controllerName = strtolower($controllerName);
	$controllerPath = JPATH_COMPONENT . '/' . 'controllers' . '/' . $controllerName . '.php';
	//Ignore controller names array.
	$ignore = array('customcss', 'import');
	if (!in_array($controllerName, $ignore, false))
	{
		$controllerName = "Admin" . $controllerName;
	}

	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath))
	{
		require_once($controllerPath);
	}
	else
	{
		throw new Exception('Invalid Controller' . $controllerName, 500);

		return false;
	}
}
else
{
	// Base controller, just set the task
	if (isset($controllerName) && $controllerName !== '')
	{
		// Define the controller name and path
		$controllerName = strtolower($controllerName);
		$controllerPath = JPATH_COMPONENT . '/' . 'controllers' . '/' . $controllerName . '.php';
		$controllerName = $controllerName;

		// If the controller file path exists, include it ... else lets die with a 500 error
		if (file_exists($controllerPath))
		{
			require_once($controllerPath);
		}
		else
		{
			throw new Exception('Invalid Controller' . $controllerName, 500);

			return false;
		}
	}
	else
	{
		$controllerName = null;
	}
	$task = $cmd;
}

// Make the task available later
$input->set("jevtask", $cmd);
$input->set("jevcmd", $cmd);


// Make this a config option - should not normally be needed
//$db = Factory::getDbo();
//$db->setQuery( "SET SQL_BIG_SELECTS=1");
//$db->execute();

// Set the name for the controller and instantiate it
$controllerClass = ucfirst($controllerName) . 'Controller';

if (class_exists($controllerClass))
{
	$controller = new $controllerClass();
}
else
{
	throw new Exception('Invalid Controller Class - ' . $controllerClass, 500);

	return false;
}

// record what is running - used by the filters
$registry = JevRegistry::getInstance("jevents");
$registry->set("jevents.activeprocess", "administrator");

// Perform the Request task
$controller->execute($task);

$output = ob_get_clean();
// remove &#65279; non breaking white space and other joiners that may break the layout
$output = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $output);
echo $output;

// I could also try this in the footer file - but it doesn't work there in com_categories even with an event listener:(
/*
?>
	<script defer>
        var bomsearch = document.evaluate( '//*[contains(text(), "\uFEFF")]', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null );
        if (bomsearch.singleNodeValue)
        {
            for (var n=0; n<bomsearch.singleNodeValue.childNodes.length; n++)
            {
                if (bomsearch.singleNodeValue.childNodes[n].nodeType = 3)
                {
                    bomsearch.singleNodeValue.childNodes[n].nodeValue = bomsearch.singleNodeValue.childNodes[n].textContent.replace("\uFEFF", "");
                }
            }
        }
	</script>
<?php
*/
// Redirect if set by the controller
$controller->redirect();
