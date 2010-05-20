<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: mod_jevents_cal.php 1057 2008-04-21 18:06:33Z tstahl $
 * @package     JEvents
 * @subpackage  Module JEvents Calendar
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */


defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (dirname(__FILE__).DS.'helper.php');

$jevhelper = new modJeventsCalHelper();

JPluginHelper::importPlugin("jevents");

// record what is running - used by the filters
$registry	=& JRegistry::getInstance("jevents");
$registry->setValue("jevents.activeprocess","mod_jevents_cal");
$registry->setValue("jevents.moduleid", $module->id);
$registry->setValue("jevents.moduleparams", $params);

$theme = JEV_CommonFunctions::getJEventsViewName();
require_once(JModuleHelper::getLayoutPath('mod_jevents_cal',$theme.DS."calendar"));
$viewclass = ucfirst($theme)."ModCalView";
$modview = new $viewclass($params, $module->id);
echo $modview->getCal();