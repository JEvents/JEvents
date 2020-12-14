<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: mod_jevents_filter.php 941 2010-05-20 13:21:57Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Filter
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.gwesystems.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/' . 'helper.php');

// Reset filters when viewed on non-JEvents page - make this a configurable option

$jevhelper = new modJeventsFilterHelper($params);

$app    = Factory::getApplication();
$input  = $app->input;

// record what is running - used by the filters
$registry = JevRegistry::getInstance("jevents");
$registry->set("jevents.activeprocess", "mod_jevents_filter");
$registry->set("jevents.moduleid", $module->id);
$registry->set("jevents.moduleparams", $params);
$option = $input->getCmd("option");
if ($params->get("alwaystarget", 0) && $params->get("target_itemid", 0) > 0)
{
	$app->setUserState("jevents.filtermenuitem", $params->get("target_itemid", 0));
}
else if ($option == "com_jevents")
{
	$menu   = $app->getMenu();
	$active = $menu->getActive();
	if ($active)
	{
		$app->setUserState("jevents.filtermenuitem", $active->id);
	}
}
if ($input->getCmd("task") == "icalrepeat.detail" && $params->get('showindetails', 0) == 0)
{
	return;
}

$app->activeModule = $module;
require(ModuleHelper::getLayoutPath('mod_jevents_filter'));


