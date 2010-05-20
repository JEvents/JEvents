<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: view.html.php 1620 2009-10-20 16:04:05Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
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
class DefaultYear extends JEventsDefaultView
{

	function listevents($tpl = null)
	{
		JEVHelper::componentStylesheet($this);

		$document =& JFactory::getDocument();
		// TODO do this properly
		//$document->setTitle(JText::_("BROWSER TITLE"));

		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		//$this->assign("introduction", $params->get("intro",""));


	}
	function getAdjacentYear($year,$month,$day, $direction=1)
	{
		$d1 = mktime(0,0,0,$month,$day,$year+1);
		$day = strftime("%d",$d1);
		$year = strftime("%Y",$d1);
		$month = strftime("%m",$d1);
		$task = JRequest::getString('jevtask');
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
