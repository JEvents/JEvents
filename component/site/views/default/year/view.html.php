<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 3155 2012-01-05 12:01:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML View class for the component frontend
 *
 * @static
 */
class DefaultViewYear extends JEventsDefaultView
{

	function listevents($tpl = null)
	{
		JEVHelper::componentStylesheet($this);

		$document = JFactory::getDocument();
		// TODO do this properly
		//$document->setTitle(JText::_( 'BROWSER_TITLE' ));

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$this->assign("introduction", $params->get("intro",""));


	}
	function getAdjacentYear($year,$month,$day, $direction=1)
	{
		$jinput = JFactory::getApplication()->input;

		$d1 = JevDate::mktime(0,0,0,$month,$day,$year+$direction);
		$day = JevDate::strftime("%d",$d1);
		$year = JevDate::strftime("%Y",$d1);
		
		$cfg = JEVConfig::getInstance();
		$earliestyear =  JEVHelper::getMinYear();
		$latestyear = JEVHelper::getMaxYear();
		if ($year>$latestyear || $year<$earliestyear){
			return false;
		}
		
		$month = JevDate::strftime("%m",$d1);
		$task = $jinput->getString('jevtask');
		$Itemid = JEVHelper::getItemid();
		if (isset($Itemid)) $item= "&Itemid=$Itemid";
		else $item="";
		return JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=$task$item&year=$year&month=$month&day=$day");
	}
	function getPrecedingYear($year,$month,$day)
	{
		return 	$this->getAdjacentYear($year,$month,$day,-1);
	}
	function getFollowingYear($year,$month,$day)
	{
		return 	$this->getAdjacentYear($year,$month,$day,+1);
	}

}
