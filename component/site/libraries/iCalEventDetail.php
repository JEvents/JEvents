<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: iCalEventDetail.php 1742 2011-03-08 10:53:09Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\CMS\Plugin\PluginHelper;

class iCalEventDetail extends Joomla\CMS\Table\Table
{

	/** @var int Primary key */
	var $evdet_id = null;

	var $dtstart = null;
	var $dtstartraw = null;
	var $duration = null;
	var $durationraw = null;
	var $dtend = null;
	var $dtendraw = null;
	var $dtstamp = null;
	var $class = null;
	var $categories = null;
	var $description = null;
	var $geolon = 0;
	var $geolat = 0;
	var $location = null;
	var $priority = null;
	var $status = null;
	var $summary = null;
	var $contact = null;
	var $organizer = null;
	var $url = null;
	var $created = null;
	var $sequence = null;
	var $extra_info = null;
	var $color = null;
	var $multiday = null;
	var $noendtime = null;
	var $modified = null;
	var $rawdata = "";

	var $_customFields = null;

	/**
	 * This holds the raw data as an array
	 *
	 * @var array
	 */
	var $_data;

	/**
	 * Null Constructor
	 */
	public function __construct(&$db)
	{

		// get default value for multiday from params
		$cfg             = JEVConfig::getInstance();
		$this->_multiday = $cfg->get('multiday', 1);

		parent::__construct('#__jevents_vevdetail', 'evdet_id', $db);

	}

	/**
	 * Pseudo Constructor
	 *
	 * @param iCal Event parsed from ICS file as an array $ice
	 *
	 * @return n/a
	 */
	public static function iCalEventDetailFromData($ice)
	{

		$db          = Factory::getDbo();
		$temp        = new iCalEventDetail($db);
		$temp->_data = $ice;
		$temp->convertData();

		return $temp;
	}

	/**
	 * Converts $data into class values
	 *
	 */
	public function convertData()
	{

		$this->_rawdata = serialize($this->_data);

		$this->processField("dtstart", 0);
		$this->processField("dtstartraw", "");
		$this->processField("duration", 0);
		$this->processField("durationraw", "");
		$this->processField("dtend", 0);
		$this->processField("dtendraw", "");
		$this->processField("dtstamp", "");
		$this->processField("class", "");
		$this->processField("categories", "");
		$this->processField("description", "");
		if (strpos($this->description, "##migration##") === 0)
		{
			$this->description = StringHelper::substr($this->description, StringHelper::strlen("##migration##"));
			$this->description = base64_decode($this->description);
		}
		else
		{
			$this->description = str_replace('\n', "<br/>", $this->description);
			$this->description = stripslashes($this->description);
		}

		$this->processField("geolon", "0");
		$this->processField("geolat", "0");
		$this->processField("location", "");
		if (strpos($this->location, '\n'))
		{
			$this->location = str_replace('\n', '<br>', $this->location);
		}
		$this->loc_id = (int) $this->location;
		$this->processField("priority", "0");
		$this->processField("status", "");
		$this->processField("summary", "");
		$this->processField("contact", "");
		$this->processField("organizer", "");
		$this->processField("url", "");
		$this->processField("created", "");
		$this->processField("sequence", "0");

		// Fix some stupid Microsoft IIS driven calendars which don't encode the data properly!
		// see section 2 of http://www.the-art-of-web.com/html/character-codes/
		// moved to ical import code directly since this can cause problems with some editor based content (remember strange hyphen in MyEarthHour)
		//$this->description =str_replace(array("\205","\221","\222","\223","\224","\225","\226","\227","\240"),array("...","'","'",'"','"',"*","-","--"," "),$this->description);
		//$this->summary =str_replace(array("\205","\221","\222","\223","\224","\225","\226","\227","\240"),array("...","'","'",'"','"',"*","-","--"," "),$this->summary);

		// The description and summary may need escaping !!!
		// But this will be done by the SQL update function as part of the store so don't do it twice
		/*
		$db = Factory::getDbo();
		$this->description = $db->escape($this->description);
		$this->summary = $db->escape($this->summary);
		*/

		// get default value for multiday from params
		$cfg = JEVConfig::getInstance();
		$this->processField("multiday", $cfg->get('multiday', 1));

		$this->processField("noendtime", 0);

		$this->processField("x-extrainfo", "", "extra_info");

		$this->processField("x-color", "", "color");

        $this->processField("x-alt-desc", $this->description, 'description');

		// To make DB searches easier I set the dtend regardless
		if ($this->dtend == 0 && $this->duration > 0)
		{
			$this->dtend = $this->dtstart + $this->duration;
		}
		else if ($this->dtend == 0)
		{
			// if no dtend or duration (e.g. from imported iCal) - set no end time
			$this->noendtime = 1;
			$icimport        = new iCalImport();
			$this->dtend     = $icimport->unixTime($this->dtstartraw);
			// an all day event
			if ($this->dtend == $this->dtstart && StringHelper::strlen($this->dtstartraw) == 8)
			{
				// convert to JEvents all day event mode!
				//$this->allday = 1;
				$this->dtend += 86399;
			}
		}
		if ($this->dtend < $this->dtstart && StringHelper::strlen($this->dtstartraw) == 8)
		{
			// convert to JEvents all day event mode!
			$this->noendtime = 1;
			//$this->allday = 1;
			$this->dtend = $this->dtstart + 86399;
		}
		// All day event midnight to same midnight from iCalImport
		else if ($this->dtstart - $this->dtend == 1 && $this->dtendraw == $this->dtstartraw)
		{
			if (JevDate::strftime('%H:%M:%S', $this->dtstart) == "00:00:00")
			{
				// convert to JEvents all day event mode!
				$this->noendtime = 1;
				$this->dtend     = $this->dtstart + 86399;
			}
		}

		// Process any custom fields
		$this->processCustom();
	}

	/**
	 * private function
	 *
	 * @param string $field
	 */
	public function processField($field, $default, $targetFieldName = "")
	{

		if ($targetFieldName == "")
		{
			$targetfield = str_replace("-", "_", $field);
		}
		else
		{
			$targetfield = $targetFieldName;
		}
		$this->$targetfield = array_key_exists(strtoupper($field), $this->_data) ? $this->_data[strtoupper($field)] : $default;
	}

	public function processCustom()
	{

		if (!isset($this->_customFields))
		{
			$this->_customFields = array();
		}
		foreach ($this->_data as $key => $value)
		{
			if (strpos($key, "custom_") === 0)
			{
				$field                       = StringHelper::substr($key, 7);
				$this->_customFields[$field] = $value;
			}
			else if (strtoupper($key) === $key && strpos($key, "X-") === 0)
			{
				$field                       = strtolower(StringHelper::substr($key, 2));
				$this->_customFields[$field] = $value;
			}
		}
	}

	/**
	 * Pseudo Constructor
	 *
	 * @param iCal Event parsed from ICS file as an array $ice
	 *
	 * @return n/a
	 */
	public static function iCalEventDetailFromDB($icalrowAsArray)
	{

		$db          = Factory::getDbo();
		$temp        = new iCalEventDetail($db);
		$temp->_data = $icalrowAsArray;
		$temp->convertData();

		return $temp;
	}

	/**
	 * override store function to force rrule to save too!
	 *
	 * @param unknown_type $updateNulls
	 */
	public function store($updateNulls = false)
	{

		$date           = JevDate::getDate();
		$this->modified = $date->toMySQL();

        $sql = "SHOW COLUMNS FROM #__jevents_vevdetail";
        $db  = Factory::getDbo();
        $db->setQuery($sql);
        $cols = @$db->loadObjectList("Field");

        if (array_key_exists("dtstart", $cols) && strpos(strtoupper($cols["dtstart"]->Type), "BIGINT") === false)
        {
            $sql = "ALTER TABLE #__jevents_vevdetail MODIFY dtstart BIGINT NOT NULL";
            $db->setQuery( $sql );
            @$db->execute();
        }
        if (array_key_exists("dtend", $cols) && strpos(strtoupper($cols["dtend"]->Type), "BIGINT") === false)
        {
            $sql = "ALTER TABLE #__jevents_vevdetail MODIFY dtend BIGINT NOT NULL";
            $db->setQuery($sql);
            @$db->execute();
        }

		try {

			$success = parent::store($updateNulls);
			if (!$success)
			{
				throw new Exception("Problem saving event (" . $this->dtstart. ") : " . $this->getError(), 321);
			}
			// just in case we don't have jevents plugins registered yet
			PluginHelper::importPlugin("jevents");
			// I also need to store custom data
			$res = Factory::getApplication()->triggerEvent('onStoreCustomDetails', array(&$this));

		} catch (Exception $e) {
			throw new Exception("Problem saving event " . $e, 321);
		}

		return $this->evdet_id;
	}

	public function isCancelled()
	{

		return $this->status == "CANCELLED";
	}

	public function dumpData()
	{

		echo "starting : " . $this->dtstart . "<br/>";
		echo "ending : " . $this->dtend . "<br/>";
		if (isset($this->rrule))
		{
			$this->rrule->dumpData();
		}
		print_r($this->_data);
		echo "<hr/>";
	}
}

