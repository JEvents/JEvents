<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: iCalRRule.php 3467 2012-04-03 09:36:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class iCalRRule extends JTable  {

	/** @var int Primary key */
	var $rr_id					= null;

	/**
	 * This holds the raw data as an array 
	 *
	 * @var array
	 */
	var $data;
	var $freq;

	// array of exception dates
	var $_exdate = array();

	/**
	 * Null Constructor
	 */
	function iCalRRule( &$db ) {
		parent::__construct( '#__jevents_rrule', 'rr_id', $db );
	}

	function store($updateNulls = false) {
		return parent::store($updateNulls);
	}
	/**
	 * Pseudo Constructor
	 *
	 * @param iCal Entry parsed from ICS file as an array $ice
	 * @return n/a
	 */
	function iCalRRuleFromDB($icalrowAsArray){
		$db	= JFactory::getDBO();
		$temp = new iCalRRule($db);

		$temp->data = $icalrowAsArray;

		// Should really test count
		$temp->processField2("freq","YEARLY");
		$temp->processField2("count",999);
		$temp->processField2("rinterval",1);
		//interval ios a mysql reserved word
		$temp->_interval = $temp->rinterval;
		if ($temp->freq=="none"){
			$temp->processField2("until","");
		}
		else {
			$cfg = JEVConfig::getInstance();
			// cap indefinate repeats if count is blank as well as until
			if (array_key_exists("COUNT",$temp->data)){
				$temp->processField2("until","");
			}
			else {
				$temp->processField2("until",JevDate::mktime(23,59,59,12,12,$cfg->get("com_latestyear",2020)));
			}
		}
		$temp->processField2("untilraw","");
		$temp->processField2("bysecond","");
		$temp->processField2("byminute","");
		$temp->processField2("byhour","");
		$temp->processField2("byday","");
		$temp->processField2("bymonthday","");
		$temp->processField2("byyearday","");
		$temp->processField2("byweekno","");
		$temp->processField2("bymonth","");
		$temp->processField2("bysetpos","");
		$temp->processField2("irregulardates","");
		$temp->processField2("wkst","");
		return $temp;
	}
	function processField2($field,$default){
		$this->$field = array_key_exists(strtolower($field),$this->data)?$this->data[strtolower($field)]:$default;
	}


	/**
	 * Pseudo Constructor
	 *
	 * @param iCal Entry parsed from ICS file as an array $ice
	 * @return n/a
	 */
	public static  function iCalRRuleFromData($rrule){
		$db	= JFactory::getDBO();
		$temp = new iCalRRule($db);

		$temp->data = $rrule;
		$temp->freq = $temp->data['FREQ'];

		// Should really test count
		$temp->processField("count",999);
		$temp->processField("interval",1);
		//interval ios a mysql reserved word
		$temp->rinterval = $temp->interval;
		$temp->_interval = $temp->interval;
		unset($temp->interval);
		if ($temp->freq=="none"){
			$temp->processField("until","");
		}
		else {
			// cap indefinate repeats if count is blank as well as until
			if (array_key_exists("COUNT",$temp->data)){
				$temp->processField("until","");
			}
			else {
				$cfg = JEVConfig::getInstance();
				$temp->processField("until",JevDate::mktime(23,59,59,12,12,$cfg->get("com_latestyear",2020)));
				$temp->processField("count",9999);
			}
		}
		$temp->processField("untilraw","");
		$temp->processField("bysecond","");
		$temp->processField("byminute","");
		$temp->processField("byhour","");
		$temp->processField("byday","");
		$temp->processField("bymonthday","");
		$temp->processField("byyearday","");
		$temp->processField("byweekno","");
		$temp->processField("bymonth","");
		$temp->processField("bysetpos","");
		$temp->processField("irregulardates","");
		$temp->irregulardates = json_encode($temp->irregulardates );
		$temp->processField("wkst","");
		return $temp;
	}

	function processField($field,$default){
		$this->$field = array_key_exists(strtoupper($field),$this->data)?$this->data[strtoupper($field)]:$default;
	}
	/**
	 * Creates a repeat if not an exception date returns 1 anyway
	 *
	 * @param unknown_type $start
	 * @param unknown_type $end
	 * @return unknown
	 */
	function _makeRepeat($start,$end){
		if (!isset($this->_repetitions)) $this->_repetitions = array();
		$db	= JFactory::getDBO();
		$repeat = new iCalRepetition($db);
		$repeat->eventid = $this->eventid;
		// TODO CHECK THIS logic
		$repeat->startrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S',$start);
		// iCal for whole day uses 00:00:00 on the next day JEvents uses 23:59:59 on the same day
		list ($h,$m,$s) = explode(":",JevDate::strftime("%H:%M:%S",$end));
		if (($h+$m+$s)==0) {
			//			$repeat->endrepeat = JevDate::strftime('%Y-%m-%d 23:59:59',($end-86400));
			$duration = $end-$start;
			$repeat->endrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S',$start + $duration-1);
			//$repeat->endrepeat = JevDate::strftime('%Y-%m-%d 23:59:59',$end);
		}
		else {
			$repeat->endrepeat = JevDate::strftime('%Y-%m-%d %H:%M:%S',$end);
		}

		$repeat->duplicatecheck = md5($repeat->eventid . $start );

		// Double check its not in the list of exception dates
		foreach ($this->_exdate as $exdate) {
			// compare based on YYYYMMDD since exceptions are(normally) whole day, "EXDATE;VALUE=DATE"
			if (JevDate::strftime('%Y%m%d', $exdate) == JevDate::strftime('%Y%m%d', $start))	{
				// return 0;
				// exceptions count
				return 1;
			}
		}
		$this->_repetitions[] = $repeat;
		return 1;
	}

	function _afterUntil($testDate){
		if (JString::strlen($this->until)==0) return false;
		if (!isset($this->_untilMidnight)) {
			list ($d,$m,$y) = explode(":",JevDate::strftime("%d:%m:%Y",$this->until));
			$this->_untilMidnight = JevDate::mktime(23,59,59,$m,$d,$y);
		}
		if (JString::strlen($this->until)>0 && $testDate>intval($this->_untilMidnight)) {
			return true;
		}
		else return false;
	}


	/**
	 * sort the by days string for negative values so that we start at the beginning of the month/start date
	 *
	 * @param unknown_type $days
	 * @param unknown_type $currentMonthStart
	 * @param unknown_type $dtstart
	 */
	function sortByDays(&$days,$currentMonthStart,$dtstart){
		if (count($days)==0) return;
		// only sort negative values
		if (strpos($days[0],"-")===false) return;

		static $weekdayMap=array("SU"=>0,"MO"=>1,"TU"=>2,"WE"=>3,"TH"=>4,"FR"=>5,"SA"=>6);
		static $weekdayReverseMap=array("SU","MO","TU","WE","TH","FR","SA");

		list ($currentMonth,$currentYear) = explode(":",JevDate::strftime("%m:%Y",$currentMonthStart));
		list ($startMonth,$startYear) = explode(":",JevDate::strftime("%m:%Y",$dtstart));
		if ($startMonth==$currentMonth && $startYear==$currentYear){
			$startdate = $dtstart;
		}
		else {
			$startdate = $currentMonthStart;
		}
		$startWD = JevDate::strftime("%w",$startdate);

		$sorteddays = array();
		// start from week -6 and go forward (overkill I know)
		for ($w=-6;$w<0;$w++){
			// now loop over the week starting at the appropriate day fo the week
			for ($i=0;$i<7;$i++){
				$wd = ($startWD+$i)%7;
				$check = strval($w).$weekdayReverseMap[$wd];
				if (in_array($check,$days)) $sorteddays[]=$check;
			}
		}
		$days = $sorteddays;

	}

	/**
	 * Technically this is very complicated 
	 * see http://www.w3.org/2002/12/cal/rfc2445 ad search for "BYxxx rule parts modify the recurrence in some manner"
	 * 
	 * Priority to 'analysis' should therefore be (??)
	 * 
	 * FREQ=YEARLY
	 * BYMONTH
	 * BYWEEKNO
	 * BYYEARDAY
	 * FREQ=MONTHLY ??
	 * BYMONTHDAY
	 * FREQ=WEEKLY ??
	 * BYDAY
	 * FREQ=DAILY ??
	 * BYHOUR, BYMINUTE, BYSECOND
	 * BYSETPOS
	 * 
	 * INTERVAL always applies to FREQ
	 * 
	 * So if I go over step in freq units adding the BYMONTH to YEARLY etc.
	 * then restricting DAILY with BYMONTH (do an excessive loop in this situation and test if the rules hols
	 * until I get some better logic)
	 */


	/**
	 * Generates repetition from vevent & rrule data from scratch
	 * The result can then be saved to the database
	 */
	function getRepetitions($dtstart,$dtend,$duration,$recreate=false,$exdate=array()) {
		// put exdate into somewhere that I can get from _makerepeat
		$this->_exdate = $exdate;
		// TODO  "getRepetitions doesnt yet deal with Short months and 31st or leap years/week 53<br/>";
		if ($dtend==0 && $duration>0){
			$dtend=$dtstart + $duration;
		}
		if (!$recreate && isset($this->_repetitions)) return $this->_repetitions;
		$this->_repetitions = array();
		if (!isset($this->eventid)) {
			echo "no eventid set in generateRepetitions<br/>";
			return $this->_repetitions;
		}

		if ($this->count==1 && $this->freq!="IRREGULAR"){
			//echo "count=1 returing<br/>";
			$this->_makeRepeat($dtstart,$dtend);
			return $this->_repetitions;
		}
		//list ($h,$min,$s,$d,$m,$y) = explode(":",JevDate::strftime("%H:%M:%S:%d:%m:%Y",$end));

		list ($startHour,$startMin,$startSecond,$startDay,$startMonth,$startYear,$startWD)
		= explode(":",JevDate::strftime("0%H:0%M:0%S:%d:%m:%Y:%w",$dtstart));
		//echo "$startHour,$startMin,$startSecond,$startDay,$startMonth,$startYear,$startWD,$dtstart<br/>";
		$dtstartMidnight = JevDate::mktime(0,0,0,$startMonth,$startDay,$startYear);
		list ($endHour,$endMin,$endSecond,$endDay,$endMonth,$endYear,$endWD) = explode(":",JevDate::strftime("0%H:0%M:0%S:%d:%m:%Y:%w",$dtend));
		$duration = $dtend-$dtstart;
                // duration in days (work in the middle part of the day in case the clocks change and make the end time a couple of hours later too)
                $jevStart = JevDate::mktime(12,0,0,$startMonth,$startDay,$startYear);
                $jevstart = new JevDate($jevStart);
                $jevEnd= JevDate::mktime(15,0,0,$endMonth,$endDay,$endYear);
                $jevend = new JevDate($jevEnd);
                $durationdays=$jevstart->diff($jevend)->days;
		static $weekdayMap=array("SU"=>0,"MO"=>1,"TU"=>2,"WE"=>3,"TH"=>4,"FR"=>5,"SA"=>6);
		static $weekdayReverseMap=array("SU","MO","TU","WE","TH","FR","SA");
		static $dailySecs = 86400;
		static $weeklySecs = 604800;
		// TODO implement byyearday
		// TODO do full leap year audit e.g. YEARLY repeats
		//echo "freq = ".$this->freq."<br/>";
		switch ($this->freq) {
			case "YEARLY":
				// TODO the code doesn't yet deal with multiple bymonths
				if ($this->bymonth=="") $this->bymonth=$startMonth;
				//if ($this->byday=="") $this->byday=$weekdayReverseMap[$startWD];

				// If I have byday and bymonthday then the two considions must be met
				$weekdays = array();
				if ($this->byday!=""){
					foreach (explode(",",$this->byday) as $bday) {
						if (array_key_exists($bday, $weekdayMap)){
							$weekdays[]=$weekdayMap[$bday];
						}
					}
				}

				if ($this->byyearday!=""){
					echo "byyearday <br/>";
					$days = explode(",",$this->byyearday);

					$start = $dtstart;
					$end = $dtend;
					$countRepeats = 0;
					$currentYearStart = JevDate::mktime(0,0,0,1,1,$startYear);
					// do the current month first
					while  ($countRepeats < $this->count  && !$this->_afterUntil($currentYearStart)) {
						$currentYear = JevDate::strftime("%Y",$currentYearStart);
						$currentYearDays = date("L",$currentYearStart)?366:365;
						foreach ($days as $day) {
							if ($countRepeats >= $this->count || $this->_afterUntil($start)) return  $this->_repetitions;

							// TODO I am assuming all + or all -ve
							$details=array();
							preg_match("/(\+|-?)(\d*)/",$day,$details);
							list($temp,$plusminus,$daynumber) = $details;
							if (JString::strlen($plusminus)==0) $plusminus="+";

							// do not go over year end
							if ($daynumber>$currentYearDays) continue;
							if ($plusminus=="+"){
								$targetStart = JevDate::mktime($startHour,$startMin,$startSecond,12,31,$currentYear-1);
								$targetStart = JevDate::strtotime("+$daynumber days",$targetStart);
							}
							else {
								$targetStart = JevDate::mktime($startHour,$startMin,$startSecond,1,1,$currentYear+1);
								$targetStart = JevDate::strtotime("-$daynumber days",$targetStart);
							}
                                                        // TODO - Fix situation where summer time starts or ends for all day event here!!!
							$targetEnd = $targetStart + $duration;
                                                        
							if ($countRepeats >= $this->count) {
								return  $this->_repetitions;
							}
							if ($targetStart>=$dtstartMidnight && !$this->_afterUntil($targetStart)){
								// double check for byday constraints
								if ($this->byday!=""){
									if (!in_array(JevDate::strftime("%w",$targetStart),$weekdays)){
										continue;
									}
								}
								$countRepeats+=$this->_makeRepeat($targetStart,$targetEnd);
							}
						}
						// now ago to the start of next year
						if ($currentYear+$this->rinterval>2099) return  $this->_repetitions;
						$currentYearStart = JevDate::mktime(0,0,0,1,1,$currentYear+$this->rinterval);
					}

				}

				// assume for now that its an anniversary of the start month only!
				// TODO relzs this assumption !!
				else if ($this->bymonthday!="") {
					echo "bymonthday".$this->bymonthday." <br/>";
					$days = explode(",",$this->bymonthday);


					$start = $dtstart;
					$end = $dtend;
					$countRepeats = 0;

					$currentMonthStart = JevDate::mktime(0,0,0,$startMonth,1,$startYear);
					// do the current month first
					while  ($countRepeats < $this->count  && !$this->_afterUntil($currentMonthStart)) {
						list ($currentMonth,$currentYear) = explode(":",JevDate::strftime("%m:%Y",$currentMonthStart));
						$currentMonthDays = date("t",$currentMonthStart);
						foreach ($days as $day) {
							if ($countRepeats >= $this->count || $this->_afterUntil($start)) return  $this->_repetitions;

							// Assume no negative bymonthday values
							// TODO relax this assumption

							// do not go over month end
							if ($day>$currentMonthDays) continue;

							$targetStart = JevDate::mktime($startHour,$startMin,$startSecond,$currentMonth,$day,$currentYear);
							$targetEnd = $targetStart + $duration;
							if ($countRepeats >= $this->count) {
								return  $this->_repetitions;
							}
							if ($targetStart>=$dtstartMidnight && !$this->_afterUntil($targetStart)){
								// double check for byday constraints
								if ($this->byday!=""){
									if (!in_array(JevDate::strftime("%w",$targetStart),$weekdays)){
										continue;
									}
								}
								$countRepeats+=$this->_makeRepeat($targetStart,$targetEnd);
							}
						}
						// now ago to the start of next month
						if ($currentYear+$this->rinterval>2099) return  $this->_repetitions;
						$currentMonthStart = JevDate::mktime(0,0,0,$currentMonth,1,$currentYear+$this->rinterval);
					}

				}
				// see ical RFC 2445 page 42
				/*
   Each BYDAY value can also be preceded by a positive (+n) or negative
   (-n) integer. If present, this indicates the nth occurrence of the
   specific day within the MONTHLY or YEARLY RRULE. For example, within
   a MONTHLY rule, +1MO (or simply 1MO) represents the first Monday
   within the month, whereas -1MO represents the last Monday of the
   month. If an integer modifier is not present, it means all days of
   this type within the specified frequency. For example, within a
   MONTHLY rule, MO represents all Mondays within the month.							 
				 */
				
				// annual repeats of the start date - TODO check this
				else if ($this->byday=="") {
					$start = $dtstart;
					$end = $dtend;
					$countRepeats = 0;
					while ($countRepeats < $this->count && !$this->_afterUntil($start)) {
						$countRepeats+=$this->_makeRepeat($start,$end);

						$currentYear = JevDate::strftime("%Y",$start);
						list ($h,$min,$s,$d,$m,$y) = explode(":",JevDate::strftime("%H:%M:%S:%d:%m:%Y",$start));
						$maxyear  = (PHP_INT_SIZE === 8) ? 2999 : 2037;
						if (($currentYear+$this->rinterval)>=$maxyear) break;
						$start = JevDate::strtotime("+".$this->rinterval." years",$start);
						$end = JevDate::strtotime("+".$this->rinterval." years",$end);
					}
				}
				else {
					$days = explode(",",$this->byday);
					// duplicate where necessary 
					$extradays = array();
					foreach ($days as $day) {
						if (strpos($day, "+")===false && strpos($day, "-")===false ) {
							for ($i=2;$i<=52;$i++){
								$extradays[] = "+".$i.$day;
							}
						}
					}
					$days = array_merge($days, $extradays);
					
					$start = $dtstart;
					$end = $dtend;
					$countRepeats = 0;

					// do the current month first
					while  ($countRepeats < $this->count  && !$this->_afterUntil($start)) {
						$currentMonth = JevDate::strftime("%m",$start);
						foreach ($days as $day) {
							if ($countRepeats >= $this->count || $this->_afterUntil($start)) {
								return  $this->_repetitions;
							}

							$details=array();
							if (strpos($day, "+")===false && strpos($day, "-")===false ) {
								$day = "+1".$day;
							}
							preg_match("/(\+|-?)(\d+)(.{2})/",$day,$details);
							if (count($details)!=4) {
								echo "<br/><br/><b>PROBLEMS with $day</b><br/><br/>";
								return  $this->_repetitions;
							}
							else {
								list($temp,$plusminus,$weeknumber,$dayname) = $details;
								if (JString::strlen($plusminus)==0) $plusminus="+";
								if (JString::strlen($weeknumber)==0) $weeknumber=1;

								// always check for dtstart (nothing is allowed earlier)
								if ($plusminus=="-") {
									//echo "count back $weeknumber weeks on $dayname<br/>";
									list ($startDay,$startMonth,$startYear,$startWD) = explode(":",JevDate::strftime("%d:%m:%Y:%w",$start));
									$startLast = date("t",$start);
									$monthEnd = JevDate::mktime(0,0,0,$startMonth,$startLast,$startYear);
									$meWD = JevDate::strftime("%w",$monthEnd );
									$adjustment = $startLast - (7+$meWD-$weekdayMap[$dayname])%7;

									$targetstartDay = $adjustment - ($weeknumber-1)*7;
									$targetendDay = $targetstartDay + $endDay-$startDay;
									list ($h,$min,$s,$d,$m,$y) = explode(":",JevDate::strftime("%H:%M:%S:%d:%m:%Y",$start));

									$testStart = JevDate::mktime($h,$min,$s,$m,$targetstartDay,$y);
									if ($currentMonth==JevDate::strftime("%m",$testStart)){
										$currentYear = JevDate::strftime("%Y",$start);
										$targetStart = $testStart;
                                                                                // WE can't just add the duration since if summer time starts/ends within the event length then the end and possibly the date could be wrong
                                                                                $targetEnd = $targetStart + $duration;
                                                                                $targetEnd = JevDate::mktime($endHour,$endMin,$endSecond,$currentMonth,$targetstartDay+$durationdays,$currentYear);
                                                                                
										if ($countRepeats >= $this->count) {
											return  $this->_repetitions;
										}
										if ($targetStart>=$dtstartMidnight && !$this->_afterUntil($targetStart)){
											$countRepeats+=$this->_makeRepeat($targetStart,$targetEnd);
										}
									}
								}
								else {
									//echo "count forward $weeknumber weeks on $dayname<br/>";
									list ($startDay,$startMonth,$startYear,$startWD) = explode(":",JevDate::strftime("%d:%m:%Y:%w",$start));
									$monthStart = JevDate::mktime(0,0,0,$startMonth,1,$startYear);
									$msWD = JevDate::strftime("%w",$monthStart );
									if (!isset($weekdayMap[$dayname])){
										$x = 1;
									}
									$adjustment = 1 + (7+$weekdayMap[$dayname]-$msWD)%7;

									$targetstartDay = $adjustment+($weeknumber-1)*7;
									$targetendDay = $targetstartDay + $endDay-$startDay;
									list ($h,$min,$s,$d,$m,$y) = explode(":",JevDate::strftime("%H:%M:%S:%d:%m:%Y",$start));

									$testStart = JevDate::mktime($h,$min,$s,$m,$targetstartDay,$y);
									if ($currentMonth==JevDate::strftime("%m",$testStart)){
										$targetStart = $testStart;
                                                                                // WE can't just add the duration since if summer time starts/ends within the event length then the end and possibly the date could be wrong
                                                                                $targetEnd = $targetStart + $duration;
                                                                                $targetEnd = JevDate::mktime($endHour,$endMin,$endSecond,$currentMonth,$targetstartDay+$durationdays,$currentYear);
										if ($countRepeats >= $this->count) {
											return  $this->_repetitions;
										}
										if ($targetStart>=$dtstartMidnight && !$this->_afterUntil($targetStart)){
											$countRepeats+=$this->_makeRepeat($targetStart,$targetEnd);
										}
									}
								}
							}
						}
						// now ago to the start of the next month
						$start = $targetStart;
						$end = $targetEnd;
						list ($h,$min,$s,$d,$m,$y) = explode(":",JevDate::strftime("%H:%M:%S:%d:%m:%Y",$start));
						if (($y+$this->rinterval+$m/12)>2099) return  $this->_repetitions;
						$start = JevDate::mktime($h,$min,$s,$m,1,$y+$this->rinterval);
						$end = $start + $duration;
					}

				}
				return $this->_repetitions;
				break;
			case "MONTHLY":
				if ($this->bymonthday=="" && $this->byday==""){
					$this->bymonthday=$startDay;
				}
				if ($this->bymonthday!="") {
					echo "bymonthday".$this->bymonthday." <br/>";
					// if not byday then by monthday
					$days = explode(",",$this->bymonthday);
					// If I have byday and bymonthday then the two considions must be met
					$weekdays = array();
					if ($this->byday!=""){
						foreach (explode(",",$this->byday) as $bday) {
							$weekdays[]=$weekdayMap[$bday];
						}
					}

					$start = $dtstart;
					$end = $dtend;
					$countRepeats = 0;
					$currentMonthStart = JevDate::mktime(0,0,0,$startMonth,1,$startYear);

					// do the current month first
					while  ($countRepeats < $this->count  && !$this->_afterUntil($currentMonthStart)) {
						//echo $countRepeats ." ".$this->count." ".$currentMonthStart."<br/>";
						list ($currentMonth,$currentYear) = explode(":",JevDate::strftime("%m:%Y",$currentMonthStart));
						$currentMonthDays = date("t",$currentMonthStart);
						foreach ($days as $day) {
							if ($countRepeats >= $this->count || $this->_afterUntil($start)) return  $this->_repetitions;

							$details=array();
							preg_match("/(\+|-?)(\d+)/",$day,$details);
							if (count($details)!=3) {
								echo "<br/><br/><b>PROBLEMS with $day</b><br/><br/>";
								return  $this->_repetitions;
							}
							else {
								list($temp,$plusminus,$daynumber) = $details;
								if (JString::strlen($plusminus)==0) $plusminus="+";
								if (JString::strlen($daynumber)==0) $daynumber=$startDay;

								// always check for dtstart (nothing is allowed earlier)
								if ($plusminus=="-") {
									// must not go before start of month etc.
									if ($daynumber>$currentMonthDays) continue;

									echo "I need to check negative bymonth days <br/>";
									$targetStart = JevDate::mktime($startHour,$startMin,$startSecond,$currentMonth,$currentMonthDays+1-$daynumber,$currentYear);
									$targetEnd = $targetStart + $duration;
									if ($countRepeats >= $this->count) {
										return  $this->_repetitions;
									}
									if ($targetStart>=$dtstartMidnight && !$this->_afterUntil($targetStart)){
										$countRepeats+=$this->_makeRepeat($targetStart,$targetEnd);
									}
								}
								else {
									//echo "$daynumber $currentMonthDays bd=".$this->byday." <br/>";
									// must not go over end month etc.
									if ($daynumber>$currentMonthDays) continue;
									//echo "$startHour,$startMin,$startSecond,$currentMonth,$daynumber,$currentYear<br/>";
									$targetStart = JevDate::mktime($startHour,$startMin,$startSecond,$currentMonth,$daynumber,$currentYear);
									$targetEnd = $targetStart + $duration;
                                                                        // WE can't just add the duration since if summer time starts/ends within the event length then the end and possibly the date could be wrong
                                                                        $targetEnd = JevDate::mktime($endHour,$endMin,$endSecond,$currentMonth,$daynumber+$durationdays,$currentYear);
									//echo "$targetStart $targetEnd $dtstartMidnight<br/>";
									if ($countRepeats >= $this->count) {
										return  $this->_repetitions;
									}
									if ($targetStart>=$dtstartMidnight && !$this->_afterUntil($targetStart)){
										// double check for byday constraints
										if ($this->byday!=""){
											if (!in_array(JevDate::strftime("%w",$targetStart),$weekdays)){
												continue;
											}
										}
										$countRepeats+=$this->_makeRepeat($targetStart,$targetEnd);
										//echo "countrepeats = $countRepeats<br/>";
									}
								}
							}
						}
						// now ago to the start of next month
						if (($currentYear+($currentMonth+$this->rinterval)/12)>2099) return  $this->_repetitions;
						$currentMonthStart = JevDate::mktime(0,0,0,$currentMonth+$this->rinterval,1,$currentYear);
					}

				}
				// This is byday
				else {
					$days = explode(",",$this->byday);
					// TODO I should also iterate over week number if this is used
					//$weeknumbers = explode(",",$this->byweekno);

					if ($this->bysetpos!=""){
						$newdays = array();
						$setpositions = explode(",",$this->bysetpos);
						foreach($setpositions as  $setposition){
							foreach ($days as $day) {
								if (strpos($setposition, "+")===false && strpos($setposition, "-")===false){
									$setposition = "+".$setposition;
								}
								$newdays[] = $setposition.$day;
							}
						}
						$days = $newdays ;
						$this->byday = implode(",",$days);
					}					
					
					$start = $dtstart;
					$end = $dtend;
					$countRepeats = 0;
					$currentMonthStart = JevDate::mktime(0,0,0,$startMonth,1,$startYear);

					// do the current month first
					while  ($countRepeats < $this->count  && !$this->_afterUntil($currentMonthStart)) {
						list ($currentMonth,$currentYear,$currentMonthStartWD) = explode(":",JevDate::strftime("%m:%Y:%w",$currentMonthStart));
						$currentMonthDays = date("t",$currentMonthStart);
						$this->sortByDays($days,$currentMonthStart,$dtstart);

						foreach ($days as $day) {
							if ($countRepeats >= $this->count || $this->_afterUntil($start)) {
								return  $this->_repetitions;
							}

							$details=array();
							preg_match("/(\+|-?)(\d?)(.+)/",$day,$details);
							if (count($details)!=4) {
								echo "<br/><br/><b>PROBLEMS with $day</b><br/><br/>";
								return  $this->_repetitions;
							}
							else {
								list($temp,$plusminus,$weeknumber,$dayname) = $details;
								if (JString::strlen($plusminus)==0) $plusminus="+";
								if (JString::strlen($weeknumber)==0) $weeknumber=1;

								$multiplier = $plusminus=="+"?1:-1;
								// always check for dtstart (nothing is allowed earlier)
								if ($plusminus=="-") {
									//echo "count back $weeknumber weeks on $dayname<br/>";
									$startLast = date("t",$currentMonthStart);
									$currentMonthEndWD = ($startLast - 1 + $currentMonthStartWD)%7;

									$adjustment = $startLast - (7+$currentMonthEndWD-$weekdayMap[$dayname])%7;

									$targetstartDay = $adjustment - ($weeknumber-1)*7;
								}
								else {
									//echo "count forward $weeknumber weeks on $dayname<br/>";
									$adjustment = 1 + (7+$weekdayMap[$dayname]-$currentMonthStartWD)%7;

									$targetstartDay = $adjustment+($weeknumber-1)*7;

								}
								$targetStart = JevDate::mktime($startHour,$startMin,$startSecond,$currentMonth,$targetstartDay,$currentYear);

								if ($currentMonth==JevDate::strftime("%m",$targetStart)){
                                                                        // WE can't just add the duration since if summer time starts/ends within the event length then the end and possibly the date could be wrong
                                                                        $targetEnd = $targetStart + $duration;
                                                                        $targetEnd = JevDate::mktime($endHour,$endMin,$endSecond,$currentMonth,$targetstartDay+$durationdays,$currentYear);
									if ($countRepeats >= $this->count) {
										return  $this->_repetitions;
									}
									if ($targetStart>=$dtstartMidnight && !$this->_afterUntil($targetStart)){
										$countRepeats+=$this->_makeRepeat($targetStart,$targetEnd);
									}
								}

							}
						}
						// now go to the start of next month
						if (($currentYear+($currentMonth+$this->rinterval)/12)>2099) return  $this->_repetitions;
						$currentMonthStart = JevDate::mktime(0,0,0,$currentMonth+$this->rinterval,1,$currentYear);
					}
				}
				return  $this->_repetitions;

				break;
			case "WEEKLY":
				$days = explode(",",$this->byday);
				$start = $dtstart;
				$end = $dtend;
				$countRepeats = 0;
				$currentWeekDay = JevDate::strftime("%w",$start);
				// Go to the zero day of the first week (even if this is in the past)
				// this will be the base from which we count the weeks and weekdays
				$currentWeekStart = JevDate::strtotime("-$currentWeekDay days",$start);

				// no BYDAY specified
				if ($this->byday==""){
					$daynames = array("SU","MO","TU","WE","TH","FR","SA","SU");
					$this->byday = "+".$daynames[$currentWeekDay];
					$days = array($this->byday) ;
				}
				while  ($countRepeats < $this->count  && !$this->_afterUntil($currentWeekStart)) {
					list ($currentDay,$currentMonth,$currentYear) = explode(":",JevDate::strftime("%d:%m:%Y",$currentWeekStart));

					foreach ($days as $day) {
						if ($countRepeats >= $this->count || $this->_afterUntil($start)) {
							return  $this->_repetitions;
						}
						$details=array();
						preg_match("/(\+|-?)(\d?)(.+)/",$day,$details);
						if (count($details)!=4) {
							continue;
							echo "<br/><br/><b>PROBLEMS with $day</b><br/><br/>";
							return  $this->_repetitions;
						}
						else {
							list($temp,$plusminus,$daynumber,$dayname) = $details;
							if (JString::strlen($plusminus)==0) $plusminus="+";
							// this is not relevant for weekly recurrence ?!?!?
							//if (JString::strlen($daynumber)==0) $daynumber=1;
							$multiplier = $plusminus=="+"?1:-1;
							if ($plusminus=="-") {
								// TODO find out if I ever have this situation?
								// It would seem meaningless
							}
							else {
								//echo "count forward $daynumber days on $dayname<br/>";
								$targetstartDay = $currentDay+$weekdayMap[$dayname];
							}

							$targetStart = JevDate::mktime($startHour,$startMin,$startSecond,$currentMonth,$targetstartDay,$currentYear);

                                                        // WE can't just add the duration since if summer time starts/ends within the event length then the end and possibly the date could be wrong
							$targetEnd = $targetStart + $duration;
                                                        $targetEnd = JevDate::mktime($endHour,$endMin,$endSecond,$currentMonth,$targetstartDay+$durationdays,$currentYear);
							if ($countRepeats >= $this->count) {
								return  $this->_repetitions;
							}
							if ($targetStart>=$dtstartMidnight && !$this->_afterUntil($targetStart)){
								$countRepeats+=$this->_makeRepeat($targetStart,$targetEnd);
							}

						}
					}

					// now go to the start of next week
					if ($currentYear+($currentMonth/12)>2099) return  $this->_repetitions;
					$currentWeekStart = JevDate::strtotime("+".($this->rinterval)." weeks",$currentWeekStart);

				}
				return $this->_repetitions;
				break;
			case "DAILY":
				$start = $dtstart;
				$end = $dtend;
				$countRepeats = 0;

				$startYear = JevDate::strftime("%Y",$start);
				while ($startYear<2027 && $countRepeats < $this->count && !$this->_afterUntil($start)) {
				//while ($startYear<5027 && $countRepeats < $this->count && !$this->_afterUntil($start)) {
					$countRepeats+=$this->_makeRepeat($start,$end);
					$start = JevDate::strtotime("+".$this->rinterval." days",$start);
					$end = JevDate::strtotime("+".$this->rinterval." days",$end);
					$startYear = JevDate::strftime("%Y",$start);
				}
				return $this->_repetitions;
				break;
			case "IRREGULAR":
				$processedDates = array();
				// current date is ALWAYS a repeat
				$processedDates[] = $dtstart;
				$this->_makeRepeat($dtstart,$dtend);
				if (is_string($this->irregulardates) && $this->irregulardates!=""){
					$this->irregulardates = @json_decode($this->irregulardates);
				}
				if (!is_array($this->irregulardates)){
					$this->irregulardates = array();
				}
				sort($this->irregulardates);
				foreach ($this->irregulardates as $irregulardate){
					// avoid duplicate values
					if (in_array($irregulardate,$processedDates)){
						continue;
					}
					$processedDates[] = $irregulardate;
					// find the start and end times of the initial event
					$irregulardate += ($dtstart - $dtstartMidnight);
					$this->_makeRepeat($irregulardate,$irregulardate + $duration);
				}

				return $this->_repetitions;
				break;
			default:
				echo "UNKNOWN TYPE<br/>";
				return $this->_repetitions;
				break;
		}
	}

	function dumpData(){
		echo "Freq : $this->freq <br/>";
		echo "Interval : ".$this->data['INTERVAL']."<br/>";
		switch ($this->freq) {
			case "YEARLY":
				echo "By Month : ".$this->data['BYMONTH']."<br/>";
				break;
			case "MONTHLY":
				echo "By Day : ".$this->data['BYDAY']."<br/>";
				$days = explode(",",$this->data['BYDAY']);
				foreach ($days as $day) {
					$details=array();
					preg_match("/(\+|-?)(\d?)(.+)/",$day,$details);
					if (count($details)!=4) echo "<br/><br/><b>PROBLEMS with $day</b><br/><br/>";
					else {
						if (JString::strlen($details[1])==0) $details[1]="+";
						echo "Event repeat details<br/>";
						if ($details[1]=="-") echo "count back $details[2] weeks on $details[3]<br/>";
						else echo "count forward $details[2] weeks on $details[3]<br/>";

						// Note if no number given then EVERY specified day!!
					}
				}
				break;
			case "WEEKLY":
				echo "By Day : ".$this->data['BYDAY']."<br/>";
				$days = explode(",",$this->data['BYDAY']);
				foreach ($days as $day) {
					$details=array();
					preg_match("/(\+|-?)(\d?)(.+)/",$day,$details);
					if (count($details)!=4) echo "<br/><br/><b>PROBLEMS with $day</b><br/><br/>";
					else {
						if (JString::strlen($details[1])==0) $details[1]="+";
						echo "Event repeat details<br/>";
						if ($details[1]=="-") echo "count back $details[2] weeks on $details[3]<br/>";
						else echo "count forward $details[2] weeks on $details[3]<br/>";

						// Note if no number given then EVERY specified day!!
					}
				}
				break;
			default:
				echo "UNKNOWN TYPE<br/>";
				break;
		}

		// Doesnt yet deal with INTERVAL, UNTIL or COUNT
		print_r($this->data);
		echo "<hr/>";
	}

	function checkDate($test, $start, $end){
		if ($test>=$start && $test<=$end) return true;
		else return false;
	}

	function eventInPeriod($startDate,$endDate, $start, $end){
		// stupid verison to start that scans through EVERY single day!!!
		$checkDate = $startDate;
		while ($checkDate<=$endDate){
			if ($this->checkDate($checkDate,$start,$end)) return true;
			$checkDate+=24*60*60;
		}
		return false;
	}

	function isDuplicate(){
		$sql = "SELECT rr_id from #__jevents_rrule as rr WHERE rr.eventid = '".$this->eventid."'";
		$this->_db->setQuery($sql);
		$matches = $this->_db->loadObjectList();
		if (count($matches)>0 && isset($matches[0]->rr_id)) {
			return $matches[0]->rr_id;
		}
		return false;
	}
}
