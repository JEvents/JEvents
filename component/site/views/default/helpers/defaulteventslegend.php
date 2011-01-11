<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultEventsLegend($view){
	$cfg = & JEVConfig::getInstance();
	$theme = JEV_CommonFunctions::getJEventsViewName();

	$modpath = JModuleHelper::getLayoutPath('mod_jevents_legend',$theme.DS."legend");
	if (!file_exists($modpath)) return;

	// load the helper class
	require_once (JPATH_SITE.'/modules/mod_jevents_legend/helper.php');
	require_once($modpath);

	$viewclass = ucfirst($theme)."ModLegendView";
	$module = JModuleHelper::getModule("mod_jevents_legend",false);

	$params = new JParameter( $module->params );

	$modview = new $viewclass($params, $module->id);
	echo $modview->displayCalendarLegend("block");

	echo "<br style='clear:both'/>";
}

