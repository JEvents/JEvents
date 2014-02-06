<?php
/**
 * JEvents Component for Joomla
 *
 * @version     $Id: getJSON.php 3549 2013-10-25 09:26:21Z carcam $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2014 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( 'JPATH_BASE' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');

class GetjsonController extends JControllerLegacy   {

	var $datamodel		= null;
	
	function __construct($config = array())
	{
		if (!isset($config['base_path'])){
			$config['base_path']=JEV_PATH;
		}
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask( 'monthEvents' );

		$cfg = JEVConfig::getInstance();
		$theme = ucfirst(JEV_CommonFunctions::getJEventsViewName());
		JLoader::register('JEvents'.ucfirst($theme).'View',JEV_VIEWS."/".$theme."/abstract/abstract.php");

		include_once(JEV_LIBS."/modfunctions.php");
		if (!isset($this->_basePath)){
			$this->_basePath = $this->basePath;
			$this->_task = $this->task;
		}
	}

	function monthEvents()
	{
		$modid = intval((JRequest::getVar('modid', 0)));

		$user = JFactory::getUser();
		$query = "SELECT id, params"
		. "\n FROM #__modules AS m"
		. "\n WHERE m.published = 1"
		. "\n AND m.id = ". $modid
		. "\n AND m.access IN (" .  JEVHelper::getAid($user, 'string') . ")"
		. "\n AND m.client_id != 1";
		$db	= JFactory::getDBO();
		$db->setQuery( $query );
		$modules = $db->loadObjectList();
		if (count($modules)<=0){
			if (!$modid<=0){return  new JResponseJson(array());}
		}
		$params = new JRegistry( $modules[0]->params );

		$reg = JFactory::getConfig();
		$reg->set("jev.modparams",$params);

		$this->datamodel = new JEventsDataModel();
		$myItemid = $this->datamodel->setupModuleCatids($params);

		$year = JRequest::getVar( 'jev_current_year', 0 );
		$month = JRequest::getVar( 'jev_current_month', 0 );

		if($year==0)
		{
			$year = date("Y");
		}
		if($month==0)
		{
			$month = date("m");
		}

		$data = $this->datamodel->getCalendarData($year,$month,1,false, 0);

		$events = array();
		foreach ($data['dates'] as $day_index)
		{
			foreach($day_index['events'] as $event)
			{
				$eventArray['date'] = $day_index['year'] ."-".$day_index['month']."-".$day_index['d0']. " " .date("H:i",$event->getUnixStartTime());
				$eventArray['title'] = $event->title();
				$link = $event->viewDetailLink($day_index['year'], $day_index['month'], $day_index['d0'], false, $myItemid);
				$eventArray['link'] = JRoute::_($link . $this->datamodel->getCatidsOutLink());
				$events[] = $eventArray;
			}
		}

		$result = new JResponseJson($events);

		echo $result;
	}
}