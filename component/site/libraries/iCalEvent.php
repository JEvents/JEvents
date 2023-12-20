<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: iCalEvent.php 3549 2012-04-20 09:26:21Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\CMS\Plugin\PluginHelper;

class iCalEvent extends Joomla\CMS\Table\Table
{

	/** @var int Primary key */
	var $ev_id = null;

	/**
	 * This holds the raw data as an array
	 *
	 * @var array
	 */
	var $data;
	var $rrule = null;

	var $_detail;
	var $vevent;

	var $_start = null;
	var $_end = null;

	// array of exception date via EXDATE tag
	var $_exdate = array();

	// default values
	//var $rinterval = 1;
	//var $freq = "DAILY";
	//var $description = "";
	//var $summary = "";
	//var $dtstart="";
	//var $dtend="";

	var $access = 0;
	var $state = 1;

	/**
	 * Null Constructor
	 */
	function __construct(&$db)
	{

		parent::__construct('#__jevents_vevent', 'ev_id', $db);
		$this->access = JEVHelper::getBaseAccess();
	}

	/**
	 * Pseudo Constructor
	 *
	 * @param iCal Event parsed from ICS file as an array $ice
	 *
	 * @return n/a
	 */
	public static function iCalEventFromData($ice)
	{

		$db         = Factory::getDbo();
		$temp       = new iCalEvent($db);
		$temp->data = $ice;
		if (array_key_exists("RRULE", $temp->data) && !empty($temp->data['RRULE']))
		{
			$temp->rrule = iCalRRule::iCalRRuleFromData($temp->data['RRULE']);
		}
		$temp->convertData();
		$temp->setupEventDetail();

		//		$temp->map_iCal_to_Jevents();
		return $temp;
		//print_r($this->data);
	}

	/**
	 * Converts $data into class values
	 *
	 */
	function convertData()
	{

		$this->processField("uid", 0);
		$this->rawdata = serialize($this->data);
		//$this->processField("catid","");

		/* most of this now goes in the eventdetail
		$this->processField("dtstart",0);
		$this->processField("dtstartraw","");
		$this->processField("duration",0);
		$this->processField("durationraw","");
		$this->processField("dtend",0);
		$this->processField("dtendraw","");
		$this->processField("dtstamp","");
		$this->processField("class","");
		$this->processField("categories","");
		$this->processField("description","");
		$this->processField("geolon","0");
		$this->processField("geolat","0");
		$this->processField("location","");
		$this->processField("priority","");
		$this->processField("status","");
		$this->processField("summary","");
		$this->processField("contact","");
		$this->processField("organizer","");
		$this->processField("url","");
		$this->processField("created","");
		$this->processField("sequence","");
		*/
		$this->processField("recurrence-id", "");
		$this->processField("lockevent", 0);

		// old events access and published state
		$this->processField("x-access", JEVHelper::getBaseAccess(), "access");
		$this->processField("x-state", 1, "state");
		$user = Factory::getUser();
		$this->processField("x-createdby", $user->id, "created_by");
		$this->processField("x-createdbyalias", "", "created_by_alias");
		$this->processField("x-modifiedby", $user->id, "modified_by");

		/*

		if (isset($this->rrule)) $this->trueend = $this->rrule->trueEndDate($this->dtend);
		else $this->trueend = $this->dtend;
		*/

		if (array_key_exists("EXDATE", $this->data) && count($this->data["EXDATE"]) > 0)
		{
			$this->_exdate = $this->data["EXDATE"];
		}
	}

	/**
	 * private function
	 *
	 * @param string $field
	 */
	function processField($field, $default, $targetFieldName = "")
	{

		if ($targetFieldName == "")
		{
			$targetfield = str_replace("-", "_", $field);
		}
		else
		{
			$targetfield = $targetFieldName;
		}
		$this->$targetfield = array_key_exists(strtoupper($field), $this->data) ? $this->data[strtoupper($field)] : $default;
	}

	/**
	 * Create and setup event detail
	 *
	 * @param data array
	 */
	function setupEventDetail()
	{

		//if ($this->recurrence_id==""){
		$this->_detail = iCalEventDetail::iCalEventDetailFromData($this->data);
		/*
}
else $this->_detail = false;
*/
	}

	/**
	 * Pseudo Constructor
	 *
	 * @param iCal Event parsed from ICS file as an array $ice
	 *
	 * @return n/a
	 */
	public static function iCalEventFromDB($icalrowAsArray)
	{

		$db   = Factory::getDbo();
		$temp = new iCalEvent($db);
		foreach ($icalrowAsArray as $key => $val)
		{
			$temp->$key = $val;
		}
		if ($temp->freq != "")
		{
			$temp->rrule = iCalRRule::iCalRRuleFromDB($icalrowAsArray);
		}
		$temp->setupEventDetailFromDB($icalrowAsArray);

		return $temp;
	}

	function setupEventDetailFromDB($icalrowAsArray)
	{

		//if ($this->recurrence_id==""){
		$this->_detail = iCalEventDetail::iCalEventDetailFromDB($icalrowAsArray);
		/*
}
else $this->_detail = false;
*/
	}

	/**
	 * override store function to force rrule to save too!
	 *
	 * @param unknown_type $updateNulls
	 */
	function store($updateNulls = false, $overwriteCreator = false)
	{

		$app    = Factory::getApplication();
		$user   = Factory::getUser();

		$input       = $app->input;
		$curr_task   = $input->getCmd('task');
		$ical_access = $input->getInt('access');

		$isNew       = 0;
		if ($curr_task == "icals.save")
		{
			$this->access = $ical_access;
		}

		if ((int) $this->ev_id == 0)
		{
			$date          = JevDate::getDate("+0 seconds");
			$this->created = $date->toMySQL();
			$isNew         = 1;
		}

		if (!isset($this->created_by) || is_null($this->created_by) || $this->created_by == 0)
		{
			$this->created_by = $user->id;
		}
		$this->modified_by = $user->id;


		if (!isset($this->created_by_alias) || is_null($this->created_by_alias) || $this->created_by_alias == "")
		{
			$this->created_by_alias = "";
		}

		// make sure I update existing detail
		$matchingDetail = $this->matchingEventDetails();
		if (isset($matchingDetail) && isset($matchingDetail->evdet_id))
		{
			$this->_detail->evdet_id = $matchingDetail->evdet_id;
		}

		// if existing row preserve created by - unless being overwritten by authorised user
		// If user is jevents can deleteall or has backend access then allow them to specify the creator
		$jevuser   = JEVHelper::getAuthorisedUser();
		$creatorid = $input->getInt("jev_creatorid", 0);

		$access = false;
		if ($user->get('id') > 0)
		{
			//$access = Access::check($user->id, "core.deleteall","com_jevents");
			$access = $user->authorise('core.deleteall', 'com_jevents');
		}

		if (!(($jevuser && $jevuser->candeleteall) || $access) || $creatorid == 0)
		{
			if (!is_null($this->ev_id) || $this->ev_id > 0)
			{
				// we can overwrite the creator if refreshing/saving an ical with specified creator
				if (isset($matchingDetail) && $matchingDetail->created_by > 0 && !$overwriteCreator)
				{
					$this->created_by = $matchingDetail->created_by;
				}
			}
		}

		// place private reference to created_by in event detail in case needed by plugins
		$this->_detail->_created_by = $this->created_by;

		$db       = Factory::getDbo();
		try
		{
			$detailid = $this->_detail->store($updateNulls);

		} catch (Exception $e) {

			throw new Exception(Text::_("JEV_PROBLEMS_STORING_EVENT_DETAIL") . $e->getMessage(), 320, $e);
			return false;

		}


		$this->detail_id = $detailid;

		// Keep the multiple catids for storing after this
		$catids = false;
		if (is_array($this->catid))
		{
			$catids      = $this->catid;
			$this->catid = $this->catid[0];
		}

		try {

			parent::store($updateNulls);
			$this->isNew    = $isNew;

		} catch (Exception $e) {

			$app->enqueueMessage(Text::_("PROBLEMS_STORING_EVENT"), 'error');
			throw new Exception(Text::_("PROBLEMS_STORING_EVENT") . $e->getMessage(), 321, $e);
			return false;
		}

		if ($catids)
		{
			$pairs = array();
			$order = 0;
			foreach ($catids as $catid)
			{
				if ($catid == "")
				{
					$catid = -1;
				}
				else
				{
					$pairs[] = "($this->ev_id,$catid, $order)";
					$order++;
				}
			}
			try
			{
				$db->setQuery("DELETE FROM #__jevents_catmap where evid = " . $this->ev_id . " AND catid NOT IN (" . implode(",", $catids) . ")");
				$sql     = $db->getQuery();
				$success = $db->execute();
			}
			catch (Exception $e) {

				$app->enqueueMessage("Problems clearing category mapping entry for imported event", 'warning');
			}

			if (count($pairs) > 0)
			{
				try
				{
					$db->setQuery("Replace into #__jevents_catmap (evid, catid, ordering) VALUES " . implode(",", $pairs));
					$success = $db->execute();
				}
				catch (Exception $e) {

					$app->enqueueMessage("Problems storing category mapping entry for imported event", 'warning');
				}

			}
		}

		// Just in case we don't have jevents plugins registered yet
		PluginHelper::importPlugin("jevents");
		// I also need to store custom data - when we need the event itself and not just the detail
		$res = $app->triggerEvent('onStoreCustomEvent', array(&$this));

		// Some iCal imports do not provide an RRULE entry so create an empty one here
		if (!isset($this->rrule))
		{
			$this->rrule = iCalRRule::iCalRRuleFromData(array("FREQ" => "none"));
		}
		$this->rrule->eventid = $this->ev_id;
		if ($id = $this->rrule->isDuplicate())
		{
			$this->rrule->rr_id = $id;
		}
		try
		{
			$this->rrule->store($updateNulls);
		} catch (Exception $e) {
			throw new Exception(Text::_("JEV_PROBLEMS_STORING_EVENT_DETAIL") . $e->getMessage(), 322, $e);
			return false;

		}

		return true;
	}

	function matchingEventDetails()
	{

		// no need to look more than once if I've already changed the data
		if (isset($this->_matched))
		{
			return;
		}
		$uid = str_replace("'", "", $this->uid);
		$sql = "SELECT *  from #__jevents_vevent as vev,#__jevents_vevdetail as det"
			. "\n WHERE vev.uid = '" . $uid . "'"
			. "\n AND vev.detail_id = det.evdet_id";
		$this->_db->setQuery($sql);
		$matches = $this->_db->loadObjectList();
		if (count($matches) > 0 && isset($matches[0]->ev_id))
		{
			$this->_matched = true;
			if ($matches[0]->icsid != $this->icsid)
			{
				echo "Matched duplicate uid $this->uid to find " . count($matches) . " icsid = " . $matches[0]->icsid . "<br/>";
			}

			return $matches[0];
		}
		$this->_matched = false;

		return false;
	}

	function isDuplicate()
	{

		$uid = str_replace("'", '', $this->uid);
		$sql = "SELECT ev_id from #__jevents_vevent as vev WHERE vev.uid = '" . $uid . "'";
		$this->_db->setQuery($sql);
		$matches = $this->_db->loadObjectList();
		if (count($matches) > 0 && isset($matches[0]->ev_id))
		{
			return $matches[0]->ev_id;
		}

		return false;
	}

	function hasRepetition()
	{

		return isset($this->rrule);
	}

	/**
	 * function that removed cancelled instances from repetitions table
	 *
	 */
	function cancelRepetition()
	{

		// TODO - rather than deleting the repetition I should save the new detail and report it as cancelled
		// this would make subsequent export easier
		$eventid = $this->ev_id;
        $icimport        = new iCalImport();
		$start   = $icimport->unixTime($this->recurrence_id);

		// TODO CHECK THIS logic - an make it more abstract since a few functions do the same !!!

		// TODO if I implement this outsite of upload I need to clean the detail table too
		$duplicatecheck = md5($eventid . $start);
		$db             = Factory::getDbo();
		$sql            = "DELETE FROM #__jevents_repetition WHERE duplicatecheck='" . $duplicatecheck . "'";
		$db->setQuery($sql);

		return $db->execute();
	}

	/**
	 * function that adjusts instances in the repetitions table
	 *
	 */
	function adjustRepetition($matchingEvent)
	{

		$eventid = $this->ev_id;

		$tz = false;
		if (StringHelper::stristr($this->recurrence_id, "TZID"))
		{
			list($tz, $this->recurrence_id) = explode(";", $this->recurrence_id);
			$tz = str_replace("TZID=", "", $tz);
			$tz = iCalImport::convertWindowsTzid($tz);
		}

		$icalImport     = new iCalImport();
		$start          = $icalImport->unixTime($this->recurrence_id, $tz);
		$duplicatecheck = md5($eventid . $start);

		// find the existing repetition in order to get the detailid
		$db  = Factory::getDbo();
		$sql = "SELECT * FROM #__jevents_repetition WHERE duplicatecheck='$duplicatecheck'";
		$db->setQuery($sql);
		$matchingRepetition = $db->loadObject();
		if (!isset($matchingRepetition))
		{
			return false;
		}
		// I now create a new evdetail instance
		$newDetail = iCalEventDetail::iCalEventDetailFromData($this->data);
		if (isset($matchingRepetition) && isset($matchingRepetition->eventdetail_id))
		{
			// This traps the first time through since the 'global id has not been overwritten
			if ($matchingEvent->detail_id != $matchingRepetition->eventdetail_id)
			{
				//$newDetail->evdet_id = $matchingRepetition->eventdetail_id;
			}
		}
		if (!$newDetail->store())
		{
			return false;
		}

		// clumsy - add in the new version with the correct times (does not deal with modified descriptions and sequence)
		$start = JevDate::strftime('%Y-%m-%d %H:%M:%S', $newDetail->dtstart);
		if ($newDetail->dtend != 0)
		{
			$end = $newDetail->dtend;
		}
		else
		{
			$end = $start + $newDetail->duration;
		}
		// iCal for whole day uses 00:00:00 on the next day JEvents uses 23:59:59 on the same day
		list ($h, $m, $s) = explode(":", JevDate::strftime("%H:%M:%S", $end));
		if (($h + $m + $s) == 0)
		{
			$end = JevDate::strftime('%Y-%m-%d 23:59:59', ($end - 86400));
		}
		else
		{
			$end = JevDate::strftime('%Y-%m-%d %H:%M:%S', $end);
		}

		$duplicatecheck = md5($eventid . $start);
		$db             = Factory::getDbo();
		$sql            = "UPDATE #__jevents_repetition SET eventdetail_id=" . $newDetail->evdet_id
			. ", startrepeat='" . $start . "'"
			. ", endrepeat='" . $end . "'"
			. ", duplicatecheck='" . $duplicatecheck . "'"
			. " WHERE rp_id=" . $matchingRepetition->rp_id;
		$db->setQuery($sql);

		return $db->execute();

	}

	function storeRepetitions()
	{

		if (!isset($this->_repetitions)) $this->getRepetitions(true);
		if (count($this->_repetitions) == 0) return false;
		$db = Factory::getDbo();
		// I must delete the eventdetails for repetitions not matching the global event detail
		// these will be recreated later to match the new adjusted repetitions

		$sql = "SELECT evdet_id FROM #__jevents_vevdetail as detail"
			. "\n LEFT JOIN #__jevents_repetition as rpt ON rpt.eventdetail_id=detail.evdet_id"
			. "\n WHERE eventid=" . $this->ev_id
			. "\n AND rpt.eventdetail_id != " . $this->_detail->evdet_id;
		$db->setQuery($sql);
		$ids = $db->loadColumn();
		if (count($ids) > 0)
		{
			$idlist = implode(",", $ids);
			$sql    = "DELETE FROM #__jevents_vevdetail  WHERE evdet_id IN(" . $idlist . ")";
			$db->setQuery($sql);
			$db->execute();

			// Just in case we don't have jevents plugins registered yet
			PluginHelper::importPlugin("jevents");
			// I also need to clean out associated custom data
			$res = Factory::getApplication()->triggerEvent('onDeleteEventDetails', array($idlist));

		}

		// attempt to find repetitions that match so I can replace them
		$sql = "select * FROM #__jevents_repetition  WHERE eventid=" . $this->ev_id . " ORDER BY startrepeat ASC";
		$db->setQuery($sql);
		$oldrepeats     = $db->loadObjectList();
		$oldrepeatcount = count($oldrepeats);
		foreach ($oldrepeats as &$oldrepeat)
		{
			// find matching day
			$oldrepeat->startday = StringHelper::substr($oldrepeat->startrepeat, 0, 10);
			// free the reference
			unset($oldrepeat);
		}

		// First I delete all existing repetitions - I can't do an update or replace
		if (count($this->_repetitions) > 1 || $oldrepeatcount > 1)
		{
			// since the repeat may have been= adjusted
			$sql = "DELETE FROM #__jevents_repetition  WHERE eventid=" . $this->ev_id;
			$db->setQuery($sql);
			$db->execute();
		}

		// Now attempt to replace repetitions using the old repeat ids
		for ($r = 0; $r < count($this->_repetitions); $r++)
		{
			$repeat =& $this->_repetitions[$r];
			// find matching day and only one!!
			$repeat->startday = StringHelper::substr($repeat->startrepeat, 0, 10);
			$matched          = false;
			foreach ($oldrepeats as & $oldrepeat)
			{
				if ($oldrepeat->startday == $repeat->startday && !isset($repeat->old_rpid) && !isset($oldrepeat->matched))
				{
					$matched            = true;
					$repeat->old_rpid   = $oldrepeat->rp_id;
					$oldrepeat->matched = true;
					if (is_null($repeat->rp_id))
					{
						$repeat->rp_id = $repeat->old_rpid;
					}
					break;
				}
				if ($oldrepeat->startday > $repeat->startday)
				{
					break;
				}
				unset($oldrepeat);
			}
			if (!$matched) $repeat->old_rpid = 0;
			// free the reference
			unset($repeat);
		}

		// if only one repeat in the past and in the future then reuse the same id
		if (count($this->_repetitions) == 1 && $oldrepeatcount == 1)
		{
			$this->_repetitions[0]->old_rpid = $oldrepeats[0]->rp_id;
			if (is_null($this->_repetitions[0]->rp_id))
			{
				$this->_repetitions[0]->rp_id = $this->_repetitions[0]->old_rpid;
			}
		}

		$sql = "REPLACE INTO #__jevents_repetition (rp_id,eventid,eventdetail_id,startrepeat,endrepeat,duplicatecheck) VALUES ";
		for ($r = 0; $r < count($this->_repetitions); $r++)
		{
			$repeat = $this->_repetitions[$r];
			if (!isset($repeat->duplicatecheck))
			{
				echo "fred";
			}
			// for now use the 'global' detail id - I really should override this
			$sql .= "\n ($repeat->old_rpid, $repeat->eventid," . $this->detail_id . ",'" . $repeat->startrepeat . "','" . $repeat->endrepeat . "','" . $repeat->duplicatecheck . "')";
			if ($r + 1 < count($this->_repetitions)) $sql .= ",";
		}
		$db->setQuery($sql);

		return $db->execute();
	}

	/**
	 * Generates repetition from vevent & rrule data from scratch
	 * The result can then be saved to the database
	 */
	function getRepetitions($recreate = false)
	{

		if (!$recreate && isset($this->_repetitions)) return $this->_repetitions;
		$this->_repetitions = array();
		if (!isset($this->ev_id))
		{
			echo "no id set in getRepetitions<br/>";

			return $this->_repetitions;
		}
		// if no rrule then only one instance
		if (!isset($this->rrule) || strtolower($this->rrule->freq) == "none")
		{
			$db              = Factory::getDbo();
			$repeat          = new iCalRepetition($db);
			$repeat->eventid = $this->ev_id;
			// is it in a non-default timezone?
			// $this->_detail->dtstart assumed that the time was in our default timezone via jevdate::strtotime
			$repeat->startrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S', $this->_detail->dtstart);
			$repeat->endrepeat   = JevDate::strftime('%Y-%m-%d %H:%M:%S', $this->_detail->dtend);

			// If it is in a non-default timezone then we must change the start and end repeats
			// so that the stored times are in our default timezone!
			if ($this->tzid)
			{
				$testdate = DateTime::createFromFormat('Y-m-d H:i:s', $repeat->startrepeat, new DateTimeZone($this->tzid));
				$testdate->setTimezone(new DateTimeZone(@date_default_timezone_get()));
				$repeat->startrepeat = $testdate->format('Y-m-d H:i:s');

				$testdate = DateTime::createFromFormat('Y-m-d H:i:s', $repeat->endrepeat, new DateTimeZone($this->tzid));
				$testdate->setTimezone(new DateTimeZone(@date_default_timezone_get()));
				$repeat->endrepeat = $testdate->format('Y-m-d H:i:s');
			}

			$repeat->duplicatecheck = md5($repeat->eventid . $this->_detail->dtstart);
			$this->_repetitions[]   = $repeat;

			return $this->_repetitions;
		}
		else
		{
			$this->_repetitions = $this->rrule->getRepetitions($this->_detail->dtstart, $this->_detail->dtend, $this->_detail->duration, $recreate, $this->_exdate);

			// is it in a non-default timezone
			if ($this->tzid)
			{
				foreach ($this->_repetitions as &$repeat)
				{
					$testdate = DateTime::createFromFormat('Y-m-d H:i:s', $repeat->startrepeat, new DateTimeZone($this->tzid));
					$testdate->setTimezone(new DateTimeZone(@date_default_timezone_get()));
					$repeat->startrepeat = $testdate->format('Y-m-d H:i:s');

					$testdate = DateTime::createFromFormat('Y-m-d H:i:s', $repeat->endrepeat, new DateTimeZone($this->tzid));
					$testdate->setTimezone(new DateTimeZone(@date_default_timezone_get()));
					$repeat->endrepeat = $testdate->format('Y-m-d H:i:s');
					unset($repeat);
				}
			}

			return $this->_repetitions;
		}
	}

	function eventOnDate($testDate)
	{

		if (!isset($this->_start))
		{
			$this->_start = JevDate::mktime(0, 0, 0, $this->mup, $this->dup, $this->yup);
			$this->_end   = JevDate::mktime(0, 0, 0, $this->mup, $this->dup, $this->yup);
		}
		if (!isset($this->rrule))
		{
			if ($this->_start <= $testDate && $this->_end >= $testDate)
			{
				return true;
			}
			else return false;
		}
		else
		{
			if (isset($this->rrule))
			{
				//				if ($testDate>$this->trueend) return false;
				return $this->rrule->checkDate($testDate, $this->_start, $this->_end);
			}
		}

		return false;

	}

	function eventInPeriod($startDate, $endDate)
	{

		if (!isset($this->_start))
		{
			$this->_start = JevDate::mktime(0, 0, 0, $this->mup, $this->dup, $this->yup);
			$this->_end   = JevDate::mktime(0, 0, 0, $this->mdn, $this->ddn, $this->ydn);
		}
		if (!isset($this->rrule))
		{
			if ($this->_start <= $endDate && $this->_end >= $startDate)
			{
				return true;
			}
			else return false;
		}
		else
		{
			if (isset($this->rrule))
			{
				return $this->rrule->eventInPeriod($startDate, $endDate, $this->_start, $this->_end);
			}
		}

		return false;

	}

	function isCancelled()
	{

		if ($this->_detail)
		{
			return $this->_detail->isCancelled();
		}

		return false;
	}

	function isRecurrence()
	{

		return $this->recurrence_id != "";
	}

	/**
	 * Utility function since DTEND really only tells me the end time in repeated events
	 *
	 */
	/*
	function getEndDate(){
	if (isset($this->rrule)) {
	return $this->rrule->getEndDate($this->dtstart,$this->dtend);
	}
	else return $this->dtend;
	}
	*/

	function dumpData()
	{

		echo "starting : " . $this->dtstart . "<br/>";
		echo "ending : " . $this->dtend . "<br/>";
		if (isset($this->rrule))
		{
			$this->rrule->dumpData();
		}
		print_r($this->data);
		echo "<hr/>";
	}
}
