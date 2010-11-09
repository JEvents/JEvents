<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id$
 * @package     JEvents
 * @subpackage  Module JEvents Calendar
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */


defined( '_JEXEC' ) or die( 'Restricted access' );

// record what is running - used by the filters
$registry	=& JRegistry::getInstance("jevents");
$registry->setValue("jevents.activeprocess","mod_jevents_legend");
$registry->setValue("jevents.moduleid", $module->id);
$registry->setValue("jevents.moduleparams", $params);

require_once (dirname(__FILE__).DS.'helper.php');

$jevhelper = new modJeventsLegendHelper();

$theme = JEV_CommonFunctions::getJEventsViewName();
require_once(JModuleHelper::getLayoutPath('mod_jevents_legend',$theme.DS."legend"));

$viewclass = ucfirst($theme)."ModLegendView";
$modview = new $viewclass($params, $module->id);
echo $modview->displayCalendarLegend();
