<?php
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\ModuleHelper;

function DefaultEventsLegend($view)
{

	$cfg = JEVConfig::getInstance();

	if ($cfg->get('com_calShowLegend', 1) == 1)
	{

		$theme = JEV_CommonFunctions::getJEventsViewName();

		$modpath = ModuleHelper::getLayoutPath('mod_jevents_legend', $theme . '/' . "legend");
		if (!file_exists($modpath) || !file_exists(JPATH_SITE . '/modules/mod_jevents_legend/helper.php')) return;


		// load the helper class
		require_once(JPATH_SITE . '/modules/mod_jevents_legend/helper.php');
		require_once($modpath);

		$viewclass = ucfirst($theme) . "ModLegendView";

		$module = ModuleHelper::getModule("mod_jevents_legend", false);
		$params = new JevRegistry($module->params);

		$modview = new $viewclass($params, $module->id);
		echo $modview->displayCalendarLegend("block");

		echo "<br style='clear:both;height:0px;line-height:0px;'/>";
	}
}

