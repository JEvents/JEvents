<?php

/**
 * JEvents Component for Joomla
 *
 * @version     $Id: getJSON.php 3549 2013-10-25 09:26:21Z carcam $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

jimport('joomla.application.component.controller');

class GetjsonController extends Joomla\CMS\MVC\Controller\BaseController
{

	var
		$datamodel = null;

	function __construct($config = array())
	{

		if (!isset($config['base_path']))
		{
			$config['base_path'] = JEV_PATH;
		}
		parent::__construct($config);
		// TODO get this from config
		$this->registerDefaultTask('monthEvents');

		$cfg   = JEVConfig::getInstance();
		$theme = ucfirst(JEV_CommonFunctions::getJEventsViewName());
		JLoader::register('JEvents' . ucfirst($theme) . 'View', JEV_VIEWS . "/" . $theme . "/abstract/abstract.php");

		include_once(JEV_LIBS . "/modfunctions.php");
		if (!isset($this->_basePath))
		{
			$this->_basePath = $this->basePath;
			$this->_task     = $this->task;
		}

	}

	function eventdata()
	{

		$input = Factory::getApplication()->input;

		$this->datamodel = new JEventsDataModel();

		list($year, $month, $day) = JEVHelper::getYMD();
		$start      = $input->getString('start', "$year-$month-$day");
		$end        = $input->getString('end', "$year-$month-$day");
		$limitstart = 0;
		$limit      = 0;

		$myItemid = JEVHelper::getItemid();

		// Force repeats to show
		$cfg = JEVConfig::getInstance();
		$cfg->set("com_showrepeats", true);

		// TODO Check for sanity of $start and $end
		$this->datamodel = new JEventsDataModel();
		$data            = $this->datamodel->getRangeData($start, $end, $limitstart, $limit);

		$events = array();
		foreach ($data['rows'] as $event)
		{
			$eventArray                    = array();
			$eventArray['title']           = $event->title();
			$eventArray['start']           = $event->yup() . "-" . $event->mup() . "-" . $event->dup() . " " . date("H:i", $event->getUnixStartTime());
			$eventArray['end']             = $event->yup() . "-" . $event->mup() . "-" . $event->dup() . " " . date("H:i", $event->getUnixStartTime());
			$eventArray['textcolor']       = $event->fgcolor();
			$eventArray['backgroundColor'] = $event->bgcolor();
			$link                          = $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), false, $myItemid);
			$eventArray['url']             = Route::_($link . $this->datamodel->getCatidsOutLink());
			if ($event->hasrepetition())
			{
				$eventArray['id'] = $event->ev_id();
			}
			$events[] = $eventArray;
		}

		// Get the document object.
		$document = Factory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
        Factory::getApplication()->setHeader('Content-Disposition', 'attachment;filename="eventdata.json"');

		// Output the JSON data.
		echo json_encode($events);

		exit();

	}

	function monthEvents()
	{

		$input  = Factory::getApplication()->input;

		$modid = intval(($input->getInt('modid', 0)));

		$user  = Factory::getUser();
		$query = "SELECT id, params"
			. "\n FROM #__modules AS m"
			. "\n WHERE m.published = 1"
			. "\n AND m.id = " . $modid
			. "\n AND m.access IN (" . JEVHelper::getAid($user, 'string') . ")"
			. "\n AND m.client_id != 1";
		$db    = Factory::getDbo();
		$db->setQuery($query);
		$modules = $db->loadObjectList();
		if (count($modules) <= 0)
		{
			if (!$modid <= 0)
			{
				return new JsonResponse(array());
			}
		}
		$params = new JevRegistry(isset($modules[0]->params) ? $modules[0]->params : null);

		$reg = Factory::getConfig();
		$reg->set("jev.modparams", $params);

		$this->datamodel = new JEventsDataModel();
		$myItemid        = $this->datamodel->setupModuleCatids($params);

		$year  = $input->getInt('jev_current_year', 0);
		$month = $input->getInt('jev_current_month', 0);

		if ($year == 0)
		{
			$year = date("Y");
		}
		if ($month == 0)
		{
			$month = date("m");
		}

		$data = $this->datamodel->getCalendarData($year, $month, 1, false, 0);

		$events = array();
		foreach ($data['dates'] as $day_index)
		{
			foreach ($day_index['events'] as $event)
			{
				$eventArray['date']  = $day_index['year'] . "-" . $day_index['month'] . "-" . $day_index['d0'] . " " . date("H:i", $event->getUnixStartTime());
                $eventArray['enddate'] = $day_index['year'] ."-".$day_index['month']."-".$day_index['d0']. " " .date("H:i",$event->getUnixEndTime());
                $eventArray['alldayevent'] = $event->alldayevent();
                $eventArray['noendtime'] = $event->noendtime();

                $title = str_replace("'", "&#39;", $event->title());
                $eventArray['title'] = str_replace('"', "&#34;", $title);
                $eventArray['safeTitle'] = addslashes($event->title());

                $link                = $event->viewDetailLink($day_index['year'], $day_index['month'], $day_index['d0'], false, $myItemid) . $this->datamodel->getCatidsOutLink();
                $eventArray['link']  = Route::_($link );
                $eventArray['linkInPopup'] = Route::_($link . "&tmpl=component");
				$events[]            = $eventArray;
			}
		}

		$result = new JsonResponse($events);

		echo $result;

	}

	/**
	 * function to fetch event data into json format
	 */

	function eventRangeData()
	{

		$app   = Factory::getApplication();
		$input = $app->input;

		$this->datamodel = new JEventsDataModel();

		list($year, $month, $day) = JEVHelper::getYMD();
		$start      = $input->getString('start', "$year-$month-$day");
		$end        = $input->getString('end', "$year-$month-$day");
		$limitstart = 0;
		$limit      = 0;

		$myItemid = JEVHelper::getItemid();

		// Force repeats to show
//		$cfg    = JEVConfig::getInstance();
//		$cfg->set("com_showrepeats", true);

		// TODO Check for sanity of $start and $end
		$reg             = JevRegistry::getInstanceWithReferences("jevents");
		$this->datamodel = $reg->getReference("jevents.datamodel", false);

		if (!$this->datamodel)
		{
			$this->datamodel = new JEventsDataModel();
			$this->datamodel->setupComponentCatids();
		}

		$data = $this->datamodel->queryModel->listIcalEventsByRange($start, $end, $limitstart, $limit);

		$events = array();
		foreach ($data as $event)
		{
			$eventArray          = array();
			$eventArray['title'] = $event->title();
			// TODO get the UNIX start/end time to be formatted as below
			if ($event->alldayevent() === 1) :
				$eventArray['start'] = $event->yup() . "-" . $event->mup() . "-" . $event->dup();
				$eventArray['end']   = $event->ydn() . "-" . $event->mdn() . "-" . $event->ddn();
			else :
				$eventArray['start'] = $event->yup() . "-" . $event->mup() . "-" . $event->dup() . "T" . date("H:i:s", $event->getUnixStartTime()) . '+00:00';
				$eventArray['end']   = $event->ydn() . "-" . $event->mdn() . "-" . $event->ddn() . "T" . date("H:i:s", $event->getUnixEndTime()) . '+00:00';
			endif;

			// TODO make event colouring conditional
			$eventArray['textColor']   = $event->fgcolor();
			$eventArray['tooltipBody'] = $event->title();
			$eventArray['color']       = $event->bgcolor();
			$link                      = $event->viewDetailLink($event->yup(), $event->mup(), $event->dup(), false, $myItemid);
			$eventArray['url']         = Route::_($link . $this->datamodel->getCatidsOutLink());
			$eventArray['allDay']      = $event->alldayevent();

			//var_dump($eventArray);die;

			if ($event->hasrepetition())
			{
				$eventArray['id'] = $event->ev_id();
			}

			$events[] = $eventArray;
		}

		// Get the document object.
		$document = Factory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition', 'attachment;filename="eventdata.json"');

		// Output the JSON data.
		echo json_encode($events);
		exit();
	}

}
