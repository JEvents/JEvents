<?php
/**
 * IncludeCivi
 * JEvents Plugin to include CiviCRM events in views
 */

defined('_JEXEC') or die;

class PlgJeventsIncludeCivi extends JPlugin
{

  public function __construct(&$subject, $config = array())
  {
    static::registerClasses();
    static::bootstrapCivi();
    parent::__construct($subject,$config);

  }

  /*
    Loads events from CiviCRM based on 1st day of event being
    between $startdate and $enddate
  */
  private static function _loadCiviEvents($startdate=NULL, $enddate=NULL)
  {
    // get the events
    $rows = CRM_Event_BAO_Event::getCompleteInfo($startdate, NULL, NULL, $enddate);
    return $rows;
  }

  /*
    Ensures CiviCRM autoloader and config are loaded
  */
  public static function bootstrapCivi()
  {
    require_once JPATH_BASE.'/components/com_civicrm/civicrm.settings.php';
    $config = CRM_Core_Config::singleton();
  }

  public static function getCiviEvents($year = 0, $month = 0) {
    // date defaults to today
    list($tyear,$tmonth,$tday) = JEVHelper::getYMD();
    if ((int)$year) {
      $tyear = (int)$year;
    }
    if ((int)$month) {
      $year = (int)$month;
    }
    // standardize the date formats for Civi's search function
    $tstart = JevDate::mktime(0, 0, 0, $tmonth, 1, $tyear);
		$startdate = date("Ym01",$tstart);
		$enddate = date("Ymt",$tstart);
		// get the rows
		// We only pass the start date because of the weird Civi search logic.
		// Passing the end date would require that the date range include any
		// selected event's start AND end date.
		$rows = static::_loadCiviEvents($startdate);
		return $rows;
  }

  //public function onLoadEventsByMonth( &$list, $year = 0, $month = 0 )
  public function onDisplayCustomFieldsMultiRowUncached( &$list, $year = 0, $month = 0 )
  {
    $rows = $this->getCiviEvents($year,$month);
    $rows = $this->translateCiviEvents($rows);
    $list = array_merge($list,$rows);
  }

  /*
    Registers any supporting classes
  */
  public static function registerClasses()
  {
    JLoader::register('jIcalEventRepeatCivi',dirname(__FILE__)."/jicaleventrepeatcivi.php");
  }

  /*
    Wrapper to translate an array of events
  */
  public function translateCiviEvents($rows) {
    $list = array();
		// try to convert each row received
		if (is_array($rows) && count($rows)) {
		  foreach ($rows as $key=>$val) {
		    $convert = $this->translateCiviToJEvent($val);
		    // if it's good, add it to the list
		    if ($convert) {
		      $list[] = $convert;
		    }
		  }
		}
		return $list;
  }

  /*
    Translate a CiviEvent database record into a jIcalEventRepeatCivi object.
    The translation is far from complete, and leaves a number of functionality
    holes to be resolved.  For now, this should only be used for calendar
    display.
  */
  public function translateCiviToJEvent($row) {
    // attempt to verify we have a CiviEvent record.  if we don't leave early returning NULL
    if (!(is_array($row) && substr(CRM_Utils_Array::value('uid',$row,''),0,15)=='CiviCRM_EventID')) {
      return NULL;
    }
    // some easy references
    $start_time = strtotime($row['start_date']);
    $end_time = strtotime($row['end_date']);
    $ismulti = (date('Ymd',$start_time)==date('Ymd',$end_time));
    $converted = new JObject(array('civicrm'=>$row,
                       'detailid' => '',
                       'rp_id' => '',
                       'eventid' => $row['event_id'],
                       'eventdetail_id' => '',
                       'duplicatecheck' => md5($row['uid']),
                       'startrepeat' => $row['start_date'],
                       'endrepeat' => $row['end_date'],
                       'ev_id' => '',
                       'icsid' => '',
                       'catid' => (string)((int)CRM_Utils_Array::value('catid',$this->params->toArray(),0)),
                       'uid' => $row['uid'],
                       'refreshed' => '0000-00-00 00:00:00',
                       'created' => '0000-00-00 00:00:00',
                       'created_by' => '',
                       'created_by_alias' => '',
                       'modified_by' => '',
                       'rawdata' => '',
                       'recurrence_id' => '',
                       'detail_id' => '',
                       'state' => '',
                       'lockevent' => '',
                       'author_notified' => '',
                       'access' => '',
                       'rr_id' => '',
                       'freq' => 'NONE',
                       'until' => '',
                       'untilraw' => '',
                       'count' => '1',
                       'rinterval' => '',
                       'bysecond' => '',
                       'byminute' => '',
                       'byhour' => '',
                       'byday' => '',
                       'bymonthday' => '',
                       'byyearday' => '',
                       'byweekno' => '',
                       'bymonth' => '',
                       'bysetpos' => '',
                       'wkst' => '',
                       'dtstart' => time($row['start_date']),
                       'dtstartraw' => '',
                       'duration' => '',
                       'durationraw' => '',
                       'dtend' => time($row['end_date']),
                       'dtendraw' => '',
                       'dtstamp' => '',
                       'class' => '',
                       'categories' => '',
                       'color' => '',
                       'description' => $row['description'],
                       'geolon' => '0',
                       'geolat' => '0',
                       'location' => '',
                       'priority' => '0',
                       'status' => '',
                       'summary' => $row['summary'],
                       'contact' => '',
                       'organizer' => '',
                       'url' => $row['url'],
                       'extra_info' => '',
                       'sequence' => '0',
                       'modified' => '0000-00-00 00:00:00',
                       'multiday' => (string)((int)$ismulti),
                       'hits' => '0',
                       'noendtime' => '0',
                       'published' => '1',
                       'yup' =>   (int)date('Y',$start_time),
                       'mup' =>   (int)date('n',$start_time),
                       'dup' =>   (int)date('j',$start_time),
                       'ydn' =>   (int)date('Y',$end_time),
                       'mdn' =>   (int)date('n',$end_time),
                       'ddn' =>   (int)date('j',$end_time),
                       'hup' =>   (int)date('G',$start_time),
                       'minup' => (int)date('i',$start_time),
                       'sup' =>   (int)date('s',$start_time),
                       'hdn' =>   (int)date('G',$end_time),
                       'mindn' => (int)date('i',$end_time),
                       'sdn' =>   (int)date('s',$end_time),
                       ));
    $ret = new jIcalEventRepeatCivi($converted);
    return $ret;
  }
}
