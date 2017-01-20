<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: vCal.php 1085 2010-07-26 17:07:27Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2017 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

/***************************************************************************
PHP vCal class v0.1
***************************************************************************/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// borrow encoding stuff from bitfolge.vcard

include_once(JPATH_ROOT."/includes/vcard.class.php");

class vEvent// extends JObject
{
	var $properties;
	var $reccurdays= array("SU","MO","TU","WE","TH","FR","SA");
	var $reccurday = "";
	var $migration = false;

	//function __construct($event) {
	public function __construct($event, $migration = false) {
		// to track migration from 1.4 to 1.5 events
		$this->migration = $migration;

		$this->properties = array();

		$this->addProperty("SUMMARY",$event->title);
		$this->setDescription( $event->content);
		$this->addProperty("LOCATION",$event->adresse_info);
		$this->addProperty("CONTACT",$event->contact_info);
		$this->addProperty("CATEGORIES",$event->category);
		$this->addProperty("X-EXTRAINFO",$event->extra_info);

		if (isset($event->created_by)){
			$this->addProperty("X-CREATEDBY",$event->created_by);
			$this->addProperty("X-CREATEDBYALIAS",$event->created_by_alias);
			$this->addProperty("X-MODIFIEDBY",$event->modified_by);
		}
		$this->addProperty("X-COLOR",$event->color_bar);
		$this->addProperty("X-ACCESS",$event->access);
		$this->addProperty("X-STATE",$event->state);

		//recurrence
		if ($event->reccurtype==0){
			$this->addProperty("DTSTART",date("Ymd\THi00",$event->dtstart ));
			$this->addProperty("DTEND",date("Ymd\THi00",$event->dtend ));
			$this->addProperty("UID",time()."evt".$event->id);
		}
		else {
			$rrule = "";
			switch ($event->reccurtype) {
				case 1://each week
				$rrule.="FREQ=WEEKLY;";
				$rrule.="UNTIL=".date("Ymd\THi00", $event->dtend )."Z;";
				if ($event->reccurweeks=="pair") $rrule.="INTERVAL=2;";
				elseif ($event->reccurweeks=="impair") $rrule.="INTERVAL=3;";
				else $rrule.="INTERVAL=1;";
				$rrule.="BYDAY=".$this->reccurdays[$event->reccurday];

				break;
				case 2://more than once a week  or set days per month
				if ($event->reccurweeks=="pair" || $event->reccurweeks=="impair"){
					$rrule.="FREQ=WEEKLY;";
					$rrule.="UNTIL=".date("Ymd\THi00", $event->dtend )."Z;";
					if ($event->reccurweeks=="pair") $rrule.="INTERVAL=2;";
					elseif ($event->reccurweeks=="impair") $rrule.="INTERVAL=3;";
					$bd = explode("|",$event->reccurweekdays);
					foreach ($bd as $key=>$val){
						$bd[$key] = $this->reccurdays[$val];
					}
					$rrule.="BYDAY=".implode(",",$bd);
				}
				else {
					$rrule.="FREQ=MONTHLY;";
					$rrule.="UNTIL=".date("Ymd\THi00", $event->dtend )."Z;";
					$rrule.="INTERVAL=1;";
					//$rrule.="BYWEEKNO=".str_replace("|",",",$event->reccurweeks).";";
					$wn = explode("|",$event->reccurweeks);
					$bd = explode("|",$event->reccurweekdays);
					$bydays = array();
					foreach ($wn as $weeknum){
						foreach ($bd as $dayname){
							$bydays[] = $weeknum.$this->reccurdays[$dayname];
						}
					}
					$rrule.="BYDAY=".implode(",",$bydays);
				}
				break;

				case 3://each month
				$rrule.="FREQ=MONTHLY;";
				$rrule.="UNTIL=".date("Ymd\THi00", $event->dtend )."Z;";
				$rrule.="INTERVAL=1;";
				if ($event->reccurday==-1){
					$rrule.="BYMONTHDAY=".date("d",$event->dtstart );
				}
				else {
					$monthday = date("d",$event->dtstart );
					$days = array();
					for ($d=0;$d<7;$d++){
						if ($monthday+$d>31) break;
						$days[]=$monthday+$d;
					}
					$rrule.="BYMONTHDAY=".implode(",",$days).";";
					$rrule.="BYDAY=".$this->reccurdays[$event->reccurday];
				}

				break;
				case 4://the end of each month
				//$this->reccurday = $event->reccurday_month;
				$rrule.="FREQ=MONTHLY;";
				$rrule.="UNTIL=".date("Ymd\THi00", $event->dtend )."Z;";
				$rrule.="INTERVAL=1;";
				$rrule.="BYMONTHDAY=-1";
				break;
				case 5://each year
				$rrule.="FREQ=YEARLY;";
				$rrule.="UNTIL=".date("Ymd\THi00", $event->dtend )."Z;";
				$rrule.="INTERVAL=1;";
				if ($event->reccurday == -1){
					$rrule.="BYMONTHDAY=".date("d",$event->dtstart );
				}
				else {
					$monthday = date("d",$event->dtstart );
					$days = array();
					for ($d=0;$d<7;$d++){
						if ($monthday+$d>31) break;
						$days[]=$monthday+$d;
					}
					$rrule.="BYMONTHDAY=".implode(",",$days).";";
					$rrule.="BYDAY=".$this->reccurdays[$event->reccurday];
				}
				break;
				default:
					$this->reccurday = "";
			}

			$this->addProperty("DTSTART",date("Ymd\THi00",$event->dtstart ));
			$endtime = $event->dtstart + (($event->dtend - $event->dtstart) % (24*60*60));
			//$event->reccurweekdays
			//$event->reccurweeks
			if ($rrule!="")	$this->addProperty("RRULE",$rrule);
			$this->addProperty("DTEND",date("Ymd\THi00",$endtime ));
			$this->addProperty("UID",time()."evt".$event->id." ".time()."recur");
		}
		$this->addProperty("DTSTAMP",date("Ymd\THi00")."Z");
	}

	public function addProperty($key,$prop) {
		$this->properties[$key]=$prop;
	}

	public function setDescription($desc) {
		if ($this->migration){
			$description = "##migration##".base64_encode($desc);
			$this->addProperty("DESCRIPTION",$description);
		}
		else {
			$description = $desc;
			$description 	= str_replace( '<p>', "\n\n", $description );
			$description 	= str_replace( '<P>', "\n\n", $description );
			$description 	= str_replace( '</p>', "\n" ,$description );
			$description 	= str_replace( '</P>', "\n" ,$description );
			$description 	= str_replace( '<p/>', "\n\n", $description );
			$description 	= str_replace( '<P/>', "\n\n", $description );
			$description 	= str_replace( '<br />', "\n", $description );
			$description 	= str_replace( '<br>', "\n" ,$description );
			$description 	= str_replace( '<BR />', "\n", $description );
			$description 	= str_replace( '<BR>', "\n" ,$description );
			$description 	= str_replace( '<li>', "\n - ", $description );
			$description 	= str_replace( '<LI>', "\n - ", $description );
			$description 	= strip_tags( $description );
			$description 	= str_replace( '{mosimage}', '', $description );
			$description 	= str_replace( '{mospagebreak}', '', $description	);
			$description 	= strtr( $description,	array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
			$description 	= preg_replace( "/&#([0-9]+);/me","chr('\\1')", $description );
			// quoted_printable_encode	from vCard class
			$this->addProperty("DESCRIPTION;ENCODING=QUOTED-PRINTABLE",quoted_printable_encode($description));
		}
	}

	public function getEvent() {
		$output = "";
		$output .=  "BEGIN:VEVENT\r\n";
		$showBR = (int) JRequest::getVar('showBR', '0');
		if ($showBR) $output.= "<br/>";

		foreach($this->properties as $key => $value) {
			$output.= "$key:$value\r\n";
			if ($showBR) $output.= "<br/>";
		}

		$output .=  "END:VEVENT\r\n";
		if ($showBR) $output.= "<br/>";

		return $output;
	}

}

class vCal //extends JObject
{
	var $properties;
	var $filename;
	var $events;
	var $migration = false;

	/**
	* @param filename for download
	*/
	//function __construct($vCalFileName) {
	public function __construct($vCalFileName, $migration=false){
		$this->properties = array();
		$this->filename = $vCalFileName;
		$this->events = array();
		// to track migration from 1.4 to 1.5 events
		$this->migration = $migration;
	}


	public function addProperty($key,$prop) {
		$this->properties[$key]=$prop;
	}

	public function addEvent($event){
		$this->events[] = new vEvent($event, $this->migration);
	}

	public function getVCal() {
		$showBR = (int)JRequest::getVar('showBR', '0');

		$output = "";
		$output .=  "BEGIN:VCALENDAR\r\n";
		if ($showBR) $output.= "<br/>";
		$output .=  "PRODID: -//JEvents for Joomla 1.0.x\r\n";
		if ($showBR) $output.= "<br/>";
		$output .=  "VERSION:2.0\r\n";
		if ($showBR) $output.= "<br/>";
		$output .=  "METHOD:PUBLISH\r\n";
		if ($showBR) $output.= "<br/>";

		foreach ($this->events as $evt) {
			$output .= $evt->getEvent() ;
		}

		foreach($this->properties as $key => $value) {
			$output.= "$key:$value\r\n";
		}

		$output .=  "END:VCALENDAR\r\n";

		return $output;
	}

	public function getFileName() {
		return $this->filename;
	}
}
?>