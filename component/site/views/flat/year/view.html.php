<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 1406 2009-04-04 09:54:18Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Component\ComponentHelper;


/**
 * HTML View class for the component frontend
 *
 * @static
 */
class flatViewYear extends JEventsflatView
{
	function listevents($tpl = null)
	{

		JEVHelper::componentStylesheet($this);
		$document = Factory::getDocument();
		$params   = ComponentHelper::getParams(JEV_COM_COMPONENT);

	}

	function getPrecedingYear($year, $month, $day)
	{

		return $this->getAdjacentYear($year, $month, $day, -1);
	}

	function getAdjacentYear($year, $month, $day, $direction = 1)
	{

		$input = Factory::getApplication()->input;

		$d1     = JevDate::mktime(0, 0, 0, $month, $day, $year + $direction);
		$day    = JevDate::strftime("%d", $d1);
		$year   = JevDate::strftime("%Y", $d1);
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$cfg    = JEVConfig::getInstance();
		if (method_exists("JEVHelper", "getMinYear"))
		{
			$earliestyear = JEVHelper::getMinYear();
			$latestyear   = JEVHelper::getMaxYear();
		}
		else
		{
			$earliestyear = $params->get("com_earliestyear", 1970);
			$latestyear   = $params->get("com_latestyear", 2150);
		}

		if ($year > $latestyear || $year < $earliestyear)
		{
			return false;
		}

		$month  = JevDate::strftime("%m", $d1);
		$task   = $input->getString('jevtask');
		$Itemid = JEVHelper::getItemid();
		if (isset($Itemid)) $item = "&Itemid=$Itemid";
		else $item = "";

		return Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=$task$item&year=$year&month=$month&day=$day");
	}

	function getFollowingYear($year, $month, $day)
	{

		return $this->getAdjacentYear($year, $month, $day, +1);
	}

}
