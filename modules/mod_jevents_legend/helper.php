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
// no direct access
defined('_JEXEC') or die('Restricted access');

class modJeventsLegendHelper
{

	function modJeventsLegendHelper()
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

	function getAllCats($modparams,&$catids,&$catidList)
	{
		
		$catidList = "";
		for ($c = 0; $c < 999; $c++) {
			$nextCID = "catid$c";
			//  stop looking for more catids when you reach the last one!
			if (!$modparams->get($nextCID ,false))
				break;
			if ($modparams->get($nextCID) > 0 && !in_array($modparams->get($nextCID), $catids)) {
				$catids[]=  $modparams->get($nextCID);
				$catidList .= ( strlen($catidList) > 0 ? "," : "") . $modparams->get($nextCID);
			}
		}
		
	}

}
