<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: helper.php 3141 2011-12-29 10:13:17Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Calendar
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\String\StringHelper;

class modJeventsLegendHelper
{

	public function __construct()
	{
		// setup for all required function and classes
		$file = JPATH_SITE . '/components/com_jevents/mod.defines.php';
		if (file_exists($file)) {
			include_once($file);
			include_once(JEV_LIBS . "/modfunctions.php");
		} else {
			die("JEvents Calendar\n<br />This module needs the JEvents component");
		}

		// load language constants
		JEVHelper::loadLanguage('modcal');
	}

	public static function getAllCats($modparams,&$catids,&$catidList)
	{
		
		$catidList = "";
		// New system
		$newcats = $modparams->get( "catidnew", false);
		if ($newcats && is_array($newcats )){
			foreach ($newcats as $newcat){
				if ( !in_array( $newcat,$catids )){
					$catids[]=$newcat;
					$catidList .= (JString::strlen($catidList)>0?",":"").$newcat;
				}
			}				
		}
		else {			
			for ($c = 0; $c < 999; $c++) {
				$nextCID = "catid$c";
				//  stop looking for more catids when you reach the last one!
				if (!$modparams->get($nextCID ,false))
					break;
				if ($modparams->get($nextCID) > 0 && !in_array($modparams->get($nextCID), $catids)) {
					$catids[]=  $modparams->get($nextCID);
					$catidList .= ( JString::strlen($catidList) > 0 ? "," : "") . $modparams->get($nextCID);
				}
			}
		}
		
	}

	function getViewClass($theme, $module, $layout, $params=false){

		// If we have a specified over ride then use it here
		if ($params && JString::strlen($params->get("layout",""))>0){
			$speciallayout = strtolower($params->get("layout",""));
			// Build the template and base path for the layout
			$tPath = JPATH_SITE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.$module.'/'.$theme.'/'.$speciallayout.'.php';

			// If the template has a layout override use it
			if (file_exists($tPath)) {
				$viewclass = "Override".ucfirst($theme)."ModLegendView".ucfirst($speciallayout);
				require_once($tPath);
				if (class_exists($viewclass)){
					return $viewclass;
				}
			}
		}
		if ($layout=="" || $layout=="global"){
			$layout=JEV_CommonFunctions::getJEventsViewName();;
		}
		
		// Build the template and base path for the layout
		$tPath = JPATH_SITE.'/'.'templates'.'/'.JFactory::getApplication()->getTemplate().'/'.'html'.'/'.$module.'/'.$layout.'.php';
		$bPath = JPATH_SITE.'/'.'modules'.'/'.$module.'/'.'tmpl'.'/'.$layout.'.php';

		jimport('joomla.filesystem.file');
		// If the template has a layout override use it
		if (JFile::exists($tPath)) {
			require_once($tPath);
			$viewclass = "Override".ucfirst($theme)."ModLegendView";
			if (class_exists($viewclass)){
				return $viewclass;
			}
			else {
				$viewclass = ucfirst($theme)."ModLegendView";
				return $viewclass;				
			}
		}
		else if (JFile::exists($bPath)) {
			require_once($bPath);
			$viewclass = ucfirst($theme)."ModLegendView";
			return $viewclass;
		}
		else {
			echo "<strong>".JText::sprintf("JEV_PLEASE_REINSTALL_LAYOUT",$theme)."</strong>";
			$bPath = JPATH_SITE.'/'.'modules'.'/'.$module.'/'.'tmpl'.'/'.'default'.'/'.'legend.php';
			require_once($bPath);
			$viewclass = "DefaultModLegendView";
			return $viewclass;

		}
	}		
}
