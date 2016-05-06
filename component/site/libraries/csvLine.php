<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: csvLine.php 3285 2012-02-21 14:56:25Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Class for objects containing lines of converted CSV file to iCal format
 *
 * Part of the CSV to iCal conversion mechanism
 */
class CsvLine {

    var $uid;
    var $categories;
    var $summary;
    var $location;
    var $description;
    var $contact;
    var $extraInfo;
    var $dtstamp;
    var $dtstart;
    var $dtend;
    var $timezone;
    var $rrule;
    var $noendtime;
    var $multiday;
    var $cf;
    /**
     * default constructor with manatory parameters
     *
     * @param categories category of the event
     * @param summary title (name) of the event
     * @param dtstart start datetime of the event
     * @param dtend end datetime of the event
     */
    public function CsvLine($categories, $summary, $dtstart, $dtend) {
        $this->categories = $categories;
        $this->summary = $summary;
        $this->dtstart = $dtstart;
        $this->dtend = $dtend;
        // default timezone
        $this->timezone = date_default_timezone_get();
		
	$this->cf = array();
    }

    /**
     * Getters and setters
     */
    public function getCategories() {
        return $this->categories;
    }

    public function setCategories($categories) {
        $this->categories = $categories;
    }

    public function getSummary() {
        return $this->summary;
    }

    public function setSummary($summary) {
        $this->summary = $summary;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($location) {
        $this->location = $location;
    }

    public function getDescription() {
        return trim($this->description);
    }

    public function setDescription($description) {
        $this->description = trim($description);
    }

    public function getContact() {
        return $this->contact;
    }

    public function setContact($contact) {
        $this->contact = trim($contact);
    }

    public function getRRule() {
        return $this->rrule;
    }

    public function setRrule($rrule) {
        $this->rrule = trim($rrule);
    }

    public function getNoendtime() {
        return $this->noendtime;
    }

    public function setNoendtime($noendtime) {
        $this->noendtime = intval($noendtime);
    }

    public function getMultiday() {
        return $this->multiday;
    }

    public function setMultiday($multiday) {
        $this->multiday = intval($multiday);
    }

	public function Customfield($cf, $col) {
        $this->cf[$col] = $cf;
    }
	
	public function getExtraInfo() {
        return $this->extraInfo;
    }

    public function setExtraInfo($extraInfo) {
        $this->extraInfo = $extraInfo;
    }

    public function getDtstamp() {
        return $this->dtstamp;
    }

    public function setDtstamp($dtstamp) {
		if (trim($dtstamp)=="") return;
        $this->dtstamp = JevDate::strtotime($dtstamp);
    }


    public function getDtend() {
        return $this->dtend;
    }

    public function setDtend($dtend) {
        $this->dtend = JevDate::strtotime($dtend);
    }

    public function getUid() {
		if (isset($this->uid) && $this->uid!=""){
			return $this->uid;
		}
		else return $this->generateUid ();
    }

    public function setUID($uid) {
        $this->uid = $uid;
    }

	public function getTimezone() {
        return $this->timezone;
    }

    public function setTimezone($timezone) {
        $this->timezone = $timezone;
    }

    /**
     * function prepares event in iCal format
     *
     * @return this object in iCal format
     */
    public function getInICalFormat() {
        $prevTimezone = date_default_timezone_get();
        date_default_timezone_set($this->timezone);

        $ical = "BEGIN:VEVENT\n";
        $ical .= "UID:".$this->getUid()."\n"
               ."CATEGORIES:".$this->categories."\n"
               ."SUMMARY:".$this->summary."\n"
               ."DTSTART".$this->timezoneoutput().":".$this->datetimeToIcsFormat($this->dtstart)."\n";

	if($this->dtend != "") $ical .= "DTEND".$this->timezoneoutput().":".$this->datetimeToIcsFormat($this->dtend)."\n";
        if($this->dtstamp != "") $ical .= "DTSTAMP:".$this->datetimeToUtcIcsFormat($this->dtstamp)."\n";
        if($this->location != "") $ical .= "LOCATION:".$this->location."\n";
        if($this->description != "") $ical .= "DESCRIPTION:".$this->description."\n";
        if($this->contact != "") $ical .= "CONTACT:".$this->contact."\n";
        if($this->extraInfo != "") $ical .= "X-EXTRAINFO:".$this->extraInfo."\n";
        if($this->rrule != "") $ical .= "RRULE:".$this->rrule."\n";
        if($this->noendtime!= "") $ical .= "NOENDTIME:".$this->noendtime."\n";
        if($this->multiday!= "") $ical .= "MULTIDAY:".$this->multiday."\n";

	if (count($this->cf)>0){
		foreach($this->cf as $key => $cf){
			$ical .= "custom_".$key.":".$cf."\n";
		}
	}
        $ical .= "SEQUENCE:0\n";
        $ical .= "TRANSP:OPAQUE\n";
        $ical .= "END:VEVENT\n";

        date_default_timezone_set($prevTimezone); // set timezone back
        return $ical;
    }

    /**
     * Function generates unique UID of the event
     *
     * @return generated uid of the event
     */
    private function generateUid() {
        return md5(uniqid(rand(),true));
    }

    /**
     * Function converts datetime to iCal format
     *
     * @param datetime Datetime of the event
     * @return converted datetime in iCal format
     */
    private function datetimeToUtcIcsFormat($datetime) {
		$datetime = JevDate::strtotime($datetime);
        return gmdate("Ymd", $datetime)."T".gmdate("His", $datetime)."Z";
    }
	
    /**
     * Function converts datetime to iCal format
     *
     * @param datetime Datetime of the event
     * @return converted datetime in iCal format
     */
    private function datetimeToIcsFormat($datetime) {
		$newdatetime = JevDate::strtotime($datetime);
		$tempdate = new JevDate($newdatetime);
	if (JString::strlen($datetime)<=10 && $tempdate->toFormat("%H:%M:%S")=="00:00:00"){
		// in this case we have not time element so don't set it otherwise iCal import will think a time is actually set and not process all day or no end time events correctly
		return date("Ymd", $newdatetime);
	}
        return date("Ymd", $newdatetime)."T".date("His", $newdatetime);
    }

	private function timezoneoutput(){
		if ($this->timezone!=""){
			return ";TZID=".$this->timezone;
		}
		else {
			return "";
		}
	}
}