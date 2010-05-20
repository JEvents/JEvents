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
// no direct access
defined('_JEXEC') or die('Restricted access');

class modJeventsLatestHelper
{

	function modJeventsLatestHelper(){
		// setup for all required function and classes
		$file = JPATH_SITE . '/components/com_jevents/mod.defines.php';
		if (file_exists($file) ) {
			include_once($file);
			include_once(JEV_LIBS."/modfunctions.php");

		} else {
			die ("JEvents Calendar\n<br />This module needs the JEvents component");
		}

		// load language constants
		JEVHelper::loadLanguage('modlatest');
	}

	function getViewClass($theme, $module, $layout, $params=false){
		global $mainframe;

		// If we have a specified over ride then use it here
		if ($params && strlen($params->get("layout",""))>0){
			$speciallayout = strtolower($params->get("layout",""));
			// Build the template and base path for the layout
			$tPath = JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$module.DS.$theme.DS.$speciallayout.'.php';

			// If the template has a layout override use it
			if (file_exists($tPath)) {
				$viewclass = "Override".ucfirst($theme)."ModLatestView".ucfirst($speciallayout);
				require_once($tPath);
				if (class_exists($viewclass)){
					return $viewclass;
				}
			}
		}
		// Build the template and base path for the layout
		$tPath = JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$module.DS.$layout.'.php';
		$bPath = JPATH_SITE.DS.'modules'.DS.$module.DS.'tmpl'.DS.$layout.'.php';

		// If the template has a layout override use it
		if (file_exists($tPath)) {
			require_once($tPath);
			$viewclass = "Override".ucfirst($theme)."ModLatestView";
			if (class_exists($viewclass)){
				return $viewclass;
			}
		}
		require_once($bPath);
		$viewclass = ucfirst($theme)."ModLatestView";
		return $viewclass;
	}
}
