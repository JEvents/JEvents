<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jeventdate.php 1784 2011-03-14 14:28:13Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2018 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

// TODO replace with JevDate

class JEventDate {
	var $year	= null;
	var $month	= null;
	var $day	= null;
	var $hour	= null;
	var $minute	= null;
	var $second	= null;
	var $dim	= null;

	function __construct( $datetime='' ) {
		$time = JevDate::strtotime($datetime);
		if ($datetime!="" && $time!==false){
			$this->date = $time;
			$parts = explode(":",date("Y:m:j:G:i:s:t",$this->date));

			$this->year   = intval($parts[0]);
			$this->month  = intval($parts[1]);
			$this->day    = intval($parts[2]);
			$this->hour   = intval($parts[3]);
			$this->minute = intval($parts[4]);
			$this->second = intval($parts[5]);
			$this->dim    = intval($parts[6]);
		}
		else {
			$this->date = time();
			$parts = explode(":",date("Y:m:j:G:i:s:t",$this->date ));

			$this->year   = intval($parts[0]);
			$this->month  = intval($parts[1]);
			$this->day    = intval($parts[2]);
			$this->hour   = 0;
			$this->minute = 0;
			$this->second = 0;
			$this->dim    = intval($parts[6]);

		}
	}

	function setDate( $year=0, $month=0, $day=0 ) {
		$this->date = JevDate::mktime(0,0,0,$month,$day,$year);
		$parts = explode(":",date("Y:m:j:G:i:s:t",$this->date));

		$this->year   = intval($parts[0]);
		$this->month  = intval($parts[1]);
		$this->day    = intval($parts[2]);
		$this->hour   = intval($parts[3]);
		$this->minute = intval($parts[4]);
		$this->second = intval($parts[5]);
		$this->dim    = intval($parts[6]);
	}

	function getYear( $asString=false ) {
		return $asString ? sprintf( '%04d', $this->year ) : $this->year;
	}

	function getMonth( $asString=false ) {
		return $asString ? sprintf( '%02d', $this->month ) : $this->month;
	}

	function getDay( $asString=false ) {
		return $asString ? sprintf( '%02d', $this->day ) : $this->day;
	}

	function get12hrTime( ){
		return date("g:ia",$this->date);
	}

	function get24hrTime( ){
		return sprintf( '%02d:%02d', $this->hour, $this->minute);
	}

	function toDateURL() {
		return( 'year=' . $this->getYear( 1 )
		. '&month=' . $this->getMonth( 1 )
		. '&day=' . $this->getDay( 1 )
		);
	}

	/**
    * Utility function for calculating the days in the month
    *
    * If no parameters are supplied then it uses the current date
    * if 'this' object does not exist
    * @param int The month
    * @param int The year
    */
	function daysInMonth( $month=0, $year=0 ) {
		$month	= intval( $month );
		$year	= intval( $year );

		if ( !$month ){
			if( isset( $this )) {
				$month = $this->month;
			} else {
				$month = date( 'm' );
			}
		}

		if( !$year ){
			if( isset( $this )) {
				$year = $this->year;
			}else{
				$year = date( 'Y' );
			}
		}
		;
		return intval(date("t",JevDate::mktime(0,0,0,$month,1,$year)));
	}

	/**
    * Adds (+/-) a number of months to the current date.
    * @param int Positive or negative number of months
    */
	function addMonths( $n=0 ) {
		// correct for months where number of days is shorter than source month)
		$dim = intval(date("t",JevDate::mktime(0,0,0,$this->month+$n,1,$this->year)));
		$this->date = JevDate::mktime($this->hour,$this->minute,$this->second,$this->month+$n,min($this->day,$dim),$this->year);
		$parts = explode(":",date("Y:m:j:G:i:s:t",$this->date));

		$this->year   = intval($parts[0]);
		$this->month  = intval($parts[1]);
		$this->day    = intval($parts[2]);
		$this->dim    = intval($parts[6]);

	}

	function addDays( $n=0 ) {
		$this->date = JevDate::mktime($this->hour,$this->minute,$this->second,$this->month,$this->day+$n,$this->year);
		$parts = explode(":",date("Y:m:j:G:i:s:t",$this->date));

		$this->year   = intval($parts[0]);
		$this->month  = intval($parts[1]);
		$this->day    = intval($parts[2]);
		$this->dim    = intval($parts[6]);
	}

	//function toDays( $day=0, $month=0, $year=0)  is no longer needed

	// function fromDays( $days )  is no longer needed
} // end class
