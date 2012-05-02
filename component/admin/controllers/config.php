<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: config.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class AdminConfigController extends JController {

	/**
	 * Controler for the Config Management
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask( 'edit',  'edit' );
		$this->registerDefaultTask("dbsetup");

	}

	/**
	 * Converts old style events into new iCal events
	 *
	 */
	function convert(){

		if (!JEVHelper::isAdminUser()){
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=cpanel.cpanel", JText::_( 'NOT_AUTHORISED_MUST_BE_ADMIN' ));
			return;
		}

		$cfg = & JEVConfig::getInstance();
		$option = JEV_COM_COMPONENT;
		$db	=& JFactory::getDBO();

		JLoader::register('vCal',JEV_PATH."/libraries/vCal.php");

		$sql = "SHOW COLUMNS FROM #__events_categories";
		$db->setQuery( $sql );
		$cols = $db->loadObjectList();
		if (is_null($cols) ){
			$this->setRedirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=cpanel.show",false),JText::_( 'OLD_JEVENTS_CATEGORY_TABLE_MISSING' ));
			$this->redirect();
		}
		$uptodate = false;
		foreach ($cols as $col) {
			if ($col->Field=="migrated"){
				$uptodate = true;
				break;
			}
		}
		if (!$uptodate){
			$sql = "ALTER TABLE #__events_categories ADD migrated  tinyint(3) NOT NULL default 0";
			$db->setQuery( $sql );
			@$db->query();

			$sql = "ALTER TABLE #__events_categories ADD newid  int(12) NOT NULL default 0";
			$db->setQuery( $sql );
			@$db->query();

		}

		$sql = "SHOW COLUMNS FROM #__events";
		$db->setQuery( $sql );
		$cols = $db->loadObjectList();
		if (is_null($cols) ){
			$this->setRedirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=cpanel.show",false),JText::_( 'OLD_JEVENTS_EVENTS_TABLE_MISSING' ));
			$this->redirect();
		}
		$uptodate = false;
		foreach ($cols as $col) {
			if ($col->Field=="migrated"){
				$uptodate = true;
				break;
			}
		}
		if (!$uptodate){
			$sql = "ALTER TABLE #__events ADD migrated  tinyint(3) NOT NULL default 0";
			$db->setQuery( $sql );
			@$db->query();
		}

		// Check to see if a fresh migration
		if (!JRequest::getInt("ongoing",0)){
			$sql = "UPDATE  #__events_categories set migrated=0";
			$db->setQuery( $sql );
			@$db->query();

			$sql = "UPDATE  #__events set migrated=0";
			$db->setQuery( $sql );
			@$db->query();
		}

		/**
		 * find the categories first
		 */		
		$query = "SELECT cc.*, ec.color FROM #__categories AS cc LEFT JOIN #__events_categories AS ec ON ec.id=cc.id WHERE cc.section='com_events' and ec.migrated=0";
		$db->setQuery( $query );
		$cats = $db->loadObjectList('id');

		if (count($cats)>0){
			// Create new categories with section com_jevents"
			JLoader::register('JEventsCategory',JEV_ADMINPATH."/libraries/categoryClass.php");

			$query = "SELECT cc.*, ec.color FROM #__categories AS cc LEFT JOIN #__jevents_categories AS ec ON ec.id=cc.id WHERE cc.section='com_jevents'";
			$db->setQuery( $query );
			$rows = $db->loadObjectList('id');
			$jcats = array();
			foreach ($rows as $jcat) {
				$newcat = new JEventsCategory($db);
				$newcat->bind(get_object_vars($jcat));
				$jcats[$jcat->id]=$newcat;
			}

			foreach ($cats as $cat) {
				// check not already mapped
				$mapped = false;
				foreach ($jcats as $jcat) {
					if ($jcat->alias==$cat->alias && $jcat->description==$cat->description && $jcat->title==$cat->title){
						$cat->newid = $jcat->id;
						$mapped=true;
						break;
					}
				}
				if (!$mapped){
					$newcat = new JEventsCategory($db);
					$newcat->bind(get_object_vars($cat));
					$newcat->id=null;
					$newcat->store();
					$cat->newid =  $newcat->id;

					$query = "UPDATE #__events_categories set migrated=1, newid=".$cat->newid." WHERE id=".$cat->id;
					$db->setQuery( $query );
					$db->query();

					$jcats[$newcat->id] = $newcat;
				}
			}
			// make sure parent field is correct
			foreach ($jcats as $key => $jcat) {
				if ($jcat->parent_id>0 && array_key_exists($jcat->parent_id,$cats)){
					$jcat->parent_id = $cats[$jcat->parent_id]->newid;
					$jcat->store();

					$jcats[$key]=$jcat;
				}
			}

		}

		$query = "SELECT cc.*, ec.color, ec.newid FROM #__categories AS cc LEFT JOIN #__events_categories AS ec ON ec.id=cc.id WHERE cc.section='com_events'";
		$db->setQuery( $query );
		$cats = $db->loadObjectList('id');

		$remainingevents = 0;
		ob_start();
		foreach ($cats as $cat) {
			// Break this into blocks of 20 events - in case of memory problems
			$remainingevents += $this->processCat($cat);
		}
		ob_end_clean();

		// get the view
		$this->view = & $this->getView("config","html");

		// Set the layout
		$this->view->setLayout('convert');
		$this->view->assign("remaining",$remainingevents);
		$this->view->display();

		//$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=cpanel.cpanel", "Events Migrated");

	}

	private function processCat($cat){
		$blocksize = 10;
		$db = JFactory::getDBO();

		$query = "SELECT ev.*, cc.name AS category, "
		."\n UNIX_TIMESTAMP(ev.publish_up) AS dtstart ,"
		."\n UNIX_TIMESTAMP(ev.publish_down) AS dtend "
		."\n FROM  #__events AS ev"
		. "\n LEFT JOIN #__categories AS cc ON  ev.catid = cc.id"
		."\n WHERE cc.id = $cat->id"
		."\n AND migrated = 0"
		."\n LIMIT $blocksize";
		$db->setQuery( $query );
		$detevents=null;
		$detevents = $db->loadObjectList();

		if (count($detevents)==0){
			return 0;
		}
		$showBR = intval( JRequest::getVar( 'showBR', '0'));
		// get vCal with HTML encoded descriptions
		global $cal;
		$cal = new vCal("", true);

		if (count($detevents)>0){
			foreach ($detevents as $event) {
				$cal->addEvent($event);

				// mark as migrated
				$query = "UPDATE  #__events set migrated=1 WHERE id=".$event->id;
				$db->setQuery( $query );
				$db->query();
			}
			$detevents = null;
			global $output;
			$output = $cal->getVCal();
			$cal = null;
			if ($showBR){
				echo "Processing cat $cat->title<br/>";
				echo $output;
				echo "<hr/>";
			}

			// Map them to the new cat id
			$catid = $cat->newid;
			$access = $cat->access;
			$state = $cat->published;
			// find the default icsfile - if none then create a new one
			$sql = "SELECT * FROM #__jevents_icsfile WHERE icaltype=2 AND isdefault=1";
			$db->setQuery($sql);
			$ics = $db->loadObject();
			if(!$ics || is_null($ics) ){
				$icsid = 0; // new
				$icsLabel = "$cat->title (imp)";
			}
			else {
				$icsid = $ics->ics_id;
				$icsLabel = $ics->label;
				if ($ics->catid==0){
					$sql = "UPDATE #__jevents_icsfile SET catid=".$cat->newid." WHERE ics_id=".$icsid;
					$db->setQuery($sql);
					$db->query();
				}
			}
			$icsFile = iCalICSFile::newICSFileFromString($output,$icsid,$catid,$access,$state,$icsLabel);
			// DO NOT CLEAN OUT EXISTING EVENTS
			$icsFileid = $icsFile->store($catid, false);
			$icsFile = null;

		}
		$query = "SELECT count(ev.id)"
		."\n FROM  #__events AS ev"
		. "\n LEFT JOIN #__categories AS cc ON  ev.catid = cc.id"
		."\n WHERE cc.id = $cat->id"
		."\n AND migrated = 0";
		$db->setQuery( $query );
		$detevents=null;
		$eventsleft = $db->loadResult();

		return $eventsleft;
	}

	/*
	* Utility functiond during development and migration
	* TODO CHECK WHICH OF THESE MUST BE REMOVED BEFORE RELEASE!!!
	*/
	/**
	 * Drops Ical Tables
	 *
	 */
	function droptables() {
		// disabled unless really needed
		return;
		$user = JFactory::getUser();

		if (!JEVHelper::isAdminUser()){
			$this->setRedirect( "index.php?option=".JEV_COM_COMPONENT."&task=cpanel.cpanel", "Not Authorised - must be super admin" );
			return;
		}

		$db	=& JFactory::getDBO();
		$sql="DROP TABLE #__jevents_icsfile";
		$db->setQuery($sql);
		$db->query();

		$sql="DROP TABLE #__jevents_rrule";
		$db->setQuery($sql);
		$db->query();

		$sql="DROP TABLE #__jevents_vevdetail";
		$db->setQuery($sql);
		$db->query();

		$sql="DROP TABLE #__jevents_vevent";
		$db->setQuery($sql);
		$db->query();

		$sql="DROP TABLE #__jevents_repetition";
		$db->setQuery($sql);
		$db->query();

		$sql="DROP TABLE #__jevents_exception";
		$db->setQuery($sql);
		$db->query();

		$sql="DROP TABLE #__jevents_categories";
		$db->setQuery($sql);
		$db->query();

		$sql="DELETE FROM  #__categories where section='com_jevents'";
		$db->setQuery($sql);
		$db->query();


		$this->setMessage( "Tables Dropped and recreated" );

		$this->dbsetup();
	}


	function dbsetup(){
		$db	=& JFactory::getDBO();
		if (JVersion::isCompatible("1.6")){
			$db->setDebug(0);
		}
		else {
			$db->debug(0);
		}
		
		$charset = ($db->hasUTF()) ? 'DEFAULT CHARACTER SET `utf8`' : '';


		/**
	 * create table if it doesn't exit
	 * 
	 * For now : 
	 * 
	 * I'm ignoring attach,comment, resources, transp, attendee, related to, rdate, request-status
	 * 
	 * Separate tables for rrule and exrule
	 */
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_vevent(
	ev_id int(12) NOT NULL auto_increment,
	icsid int(12) NOT NULL default 0,
	catid int(11) NOT NULL default 1,
	uid varchar(255) NOT NULL UNIQUE default "",
	refreshed datetime  NOT NULL default '0000-00-00 00:00:00',
	created datetime  NOT NULL default '0000-00-00 00:00:00',
	created_by int(11) unsigned NOT NULL default '0',
	created_by_alias varchar(100) NOT NULL default '',
	modified_by int(11) unsigned NOT NULL default '0',

	rawdata longtext NOT NULL default "",
	recurrence_id varchar(30) NOT NULL default "",
	
	detail_id int(12) NOT NULL default 0,
	
	state tinyint(3) NOT NULL default 1,
	lockevent tinyint(3) NOT NULL default 0,
	author_notified tinyint(3) NOT NULL default 0,
	access int(11) unsigned NOT NULL default 0,
	
	PRIMARY KEY  (ev_id),
	INDEX (icsid),
	INDEX stateidx (state)
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = "alter table #__jevents_vevent add column lockevent tinyint(3) NOT NULL default 0";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "alter table #__jevents_vevent add column author_notified tinyint(3) NOT NULL default 0";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_vevent ADD created datetime  NOT NULL default '0000-00-00 00:00:00'";
		$db->setQuery( $sql );
		@$db->query();
		
		$sql = "alter table #__jevents_vevent add index stateidx (state)";
		$db->setQuery( $sql );
		@$db->query();


		/**
	 * create table if it doesn't exit
	 * 
	 * For now : 
	 * 
	 * I'm ignoring attach,comment, resources, transp, attendee, related to, rdate, request-status
	 * 
	 * Separate tables for rrule and exrule
	 */
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_vevdetail(
	evdet_id int(12) NOT NULL auto_increment,

	rawdata longtext NOT NULL default "",
	dtstart int(11) NOT NULL default 0,
	dtstartraw varchar(30) NOT NULL default "",
	duration int(11) NOT NULL default 0,
	durationraw varchar(30) NOT NULL default "",
	dtend int(11) NOT NULL default 0,
	dtendraw varchar(30) NOT NULL default "",
	dtstamp varchar(30) NOT NULL default "",
	class  varchar(10) NOT NULL default "",
	categories varchar(120) NOT NULL default "",
	color varchar(20) NOT NULL default "",
	description longtext NOT NULL default "",
	geolon float NOT NULL default 0,
	geolat float NOT NULL default 0,
	location VARCHAR(120) NOT NULL default "",
	priority tinyint unsigned NOT NULL default 0,
	status varchar(20) NOT NULL default "",
	summary longtext NOT NULL default "",
	contact VARCHAR(120) NOT NULL default "",
	organizer VARCHAR(120) NOT NULL default "",
	url text NOT NULL default "",
	extra_info VARCHAR(240) NOT NULL DEFAULT '',
	created varchar(30) NOT NULL default "",
	sequence int(11) NOT NULL default 1,
	state tinyint(3) NOT NULL default 1,
	modified datetime  NOT NULL default '0000-00-00 00:00:00',

	multiday tinyint(3) NOT NULL default 1,
	hits int(11) NOT NULL default 0,
	noendtime tinyint(3) NOT NULL default 0,
		
	PRIMARY KEY  (evdet_id), 
	FULLTEXT searchIdx (summary,description)
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = "ALTER TABLE #__jevents_vevdetail MODIFY COLUMN url text NOT NULL default ''";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_vevdetail ADD modified datetime  NOT NULL default '0000-00-00 00:00:00' ";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_vevdetail ADD color varchar(20) NOT NULL default ''";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_vevdetail ADD multiday tinyint(3) NOT NULL default 1";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_vevdetail ADD noendtime tinyint(3) NOT NULL default 0";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_vevdetail ADD hits int(11) NOT NULL default 0";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_vevdetail ADD FULLTEXT searchIdx (summary,description)"	;
		$db->setQuery( $sql );
		@$db->query();

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_rrule (
	rr_id int(12) NOT NULL auto_increment,
	eventid int(12) NOT NULL default 1,
	freq varchar(30) NOT NULL default "",
	until int(12) NOT NULL default 1,
	untilraw varchar(30) NOT NULL default "",
	count int(6) NOT NULL default 1,
	rinterval int(6) NOT NULL default 1,
	bysecond  varchar(50) NOT NULL default "",
	byminute  varchar(50) NOT NULL default "",
	byhour  varchar(50) NOT NULL default "",
	byday  varchar(50) NOT NULL default "",
	bymonthday  varchar(50) NOT NULL default "",
	byyearday  varchar(50) NOT NULL default "",
	byweekno  varchar(50) NOT NULL default "",
	bymonth  varchar(50) NOT NULL default "",
	bysetpos  varchar(50) NOT NULL default "",
	wkst  varchar(50) NOT NULL default "",
	PRIMARY KEY  (rr_id),
	INDEX (eventid)
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = "ALTER TABLE #__jevents_rrule ADD INDEX eventid (eventid)";
		$db->setQuery( $sql );
		@$db->query();

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_repetition (
	rp_id int(12) NOT NULL auto_increment,
	eventid int(12) NOT NULL default 1,
	eventdetail_id int(12) NOT NULL default 0,	
	duplicatecheck varchar(32) NOT NULL UNIQUE default "",
	startrepeat datetime  NOT NULL default '0000-00-00 00:00:00',
	endrepeat datetime  NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (rp_id),
	INDEX (eventid),
	INDEX eventstart ( eventid , startrepeat ),
	INDEX eventend ( eventid , endrepeat ),
	INDEX eventdetail ( eventdetail_id ),
	INDEX startrepeat ( startrepeat ),
	INDEX startend ( startrepeat,endrepeat ),
	INDEX endrepeat (  endrepeat )
	
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventstart ( eventid , startrepeat )";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventend ( eventid , endrepeat )";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventdetail ( eventdetail_id  )";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "alter table #__jevents_repetition add index startrepeat (startrepeat)";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "alter table #__jevents_repetition add index endrepeat (endrepeat)";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "alter table #__jevents_repetition add index startend (startrepeat,endrepeat)";
		$db->setQuery( $sql );
		@$db->query();


		// exception_type 0=delete, 1=other exception
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_exception (
	ex_id int(12) NOT NULL auto_increment,
	rp_id int(12) NOT NULL default 0,
	eventid int(12) NOT NULL default 1,
	eventdetail_id int(12) NOT NULL default 0,	
	exception_type int(2) NOT NULL default 0,
	startrepeat datetime  NOT NULL default '0000-00-00 00:00:00',
	oldstartrepeat datetime  NOT NULL default '0000-00-00 00:00:00',
	tempfield datetime  NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (ex_id),
	KEY (eventid),
	KEY (rp_id)
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = "ALTER TABLE #__jevents_exception add column startrepeat datetime  NOT NULL default '0000-00-00 00:00:00'";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_exception add column oldstartrepeat datetime  NOT NULL default '0000-00-00 00:00:00'";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_exception add column tempfield datetime  NOT NULL default '0000-00-00 00:00:00'";
		$db->setQuery( $sql );
		@$db->query();
		
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_categories (
	id int(12) NOT NULL default 0 PRIMARY KEY,
	color VARCHAR(8) NOT NULL default '',
	overlaps tinyint(3) NOT NULL default 0,
	admin int(12) NOT NULL default 0
) ENGINE=MyISAM $charset;
SQL;

		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = "ALTER TABLE #__jevents_categories add column admin int(12) NOT NULL default 0";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jevents_categories add column overlaps tinyint(3) NOT NULL default 0";
		$db->setQuery( $sql );
		@$db->query();

		
		// Add one category by default if none exist already
		$sql = "SELECT count(id) from #__jevents_categories";
		$db->setQuery($sql);
		$count = $db->loadResult();

		if($count==0){
			JLoader::register('JEventsCategory',JEV_ADMINPATH."/libraries/categoryClass.php");
			$cat = new JEventsCategory($db);
			if (JVersion::isCompatible("1.6.0"))  {
				$cat->bind(array("title"=>JText::_( 'DEFAULT' ), "published"=>1, "color"=>"#CCCCFF", "access"=>1));
			}
			else {
				$cat->bind(array("title"=>JText::_( 'DEFAULT' ), "published"=>1, "color"=>"#CCCCFF", "access"=>0));
			}
			$cat->store();
			$catid=$cat->id;
		}
		else {
			$catid= 0;
		}

		/**
	 * create table if it doesn't exit
	 * 
	 * For now : 
	 * 
	 * I'm ignoring attach,comment, resources, transp, attendee, related to, rdate, request-status
	 * 
	 * note that icaltype = 0 for imported from URL, 1 for imported from file, 2 for created natively
	 * Separate tables for rrule and exrule
	 * 
	 */
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_icsfile(
	ics_id int(12) NOT NULL auto_increment,
	srcURL VARCHAR(255) NOT NULL default "",
	label varchar(30) NOT NULL UNIQUE default "",

	filename VARCHAR(120) NOT NULL default "",
	icaltype tinyint(3) NOT NULL default 0,
	isdefault tinyint(3) NOT NULL default 0,
	ignoreembedcat  tinyint(3) NOT NULL default 0,
	state tinyint(3) NOT NULL default 1,
	access int(11) unsigned NOT NULL default 0,
	catid int(11) NOT NULL default 1,
	created datetime  NOT NULL default '0000-00-00 00:00:00',
	created_by int(11) unsigned NOT NULL default '0',
	created_by_alias varchar(100) NOT NULL default '',
	modified_by int(11) unsigned NOT NULL default '0',
	refreshed datetime  NOT NULL default '0000-00-00 00:00:00',
	autorefresh tinyint(3) NOT NULL default 0,
	overlaps tinyint(3) NOT NULL default 0,
		
	PRIMARY KEY  (ics_id),
	INDEX stateidx (state)
	
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = "ALTER TABLE #__jevents_icsfile ADD overlaps tinyint(3) NOT NULL default 0";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "alter table #__jevents_icsfile add index stateidx (state)";
		$db->setQuery( $sql );
		@$db->query();

		// Alter table
		$sql = "Alter table #__jevents_icsfile ADD COLUMN isdefault tinyint(3) NOT NULL default 0";
		$db->setQuery($sql);
		@$db->query();

		$sql = "Alter table #__jevents_icsfile ADD COLUMN ignoreembedcat tinyint(3) NOT NULL default 0";
		$db->setQuery($sql);
		@$db->query();

		$sql = "Alter table #__jevents_icsfile ADD COLUMN autorefresh tinyint(3) NOT NULL default 0";
		$db->setQuery($sql);
		@$db->query();

		$sql = "Alter table #__jevents_icsfile MODIFY COLUMN srcURL varchar(255) NOT NULL default '' ";
		$db->setQuery($sql);
		$db->query();

		// Add one native calendar by default if none exist already
		$sql = "SELECT ics_id from #__jevents_icsfile WHERE icaltype=2";
		$db->setQuery($sql);
		$ics = $db->loadResult();

		if(!$ics || is_null($ics) || $ics==0 ){
			if (JVersion::isCompatible("1.6.0"))  {
				$sql = "INSERT INTO #__jevents_icsfile (label,filename,	icaltype,state,	access,	catid, isdefault) VALUES ('Default','Initial ICS File',2,1,1,$catid,1)";
			}
			else {
				$sql = "INSERT INTO #__jevents_icsfile (label,filename,	icaltype,state,	access,	catid, isdefault) VALUES ('Default','Initial ICS File',2,1,0,$catid,1)";
			}
			$db->setQuery($sql);
			$db->query();
			echo $db->getErrorMsg();
		}

		// 1. Make sure users table exists
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_users (
	id int( 11 ) unsigned NOT NULL AUTO_INCREMENT ,
	user_id int( 11 ) NOT NULL default '0',
	published tinyint( 2 ) NOT NULL default '0',

	canuploadimages tinyint( 2 ) NOT NULL default '0',
	canuploadmovies tinyint( 2 ) NOT NULL default '0',

	cancreate tinyint( 2 ) NOT NULL default '0',
	canedit tinyint( 2 ) NOT NULL default '0',

	canpublishown tinyint( 2 ) NOT NULL default '0',
	candeleteown tinyint( 2 ) NOT NULL default '0',

	canpublishall tinyint( 2 ) NOT NULL default '0',
	candeleteall tinyint( 2 ) NOT NULL default '0',

	cancreateown tinyint( 2 ) NOT NULL default '0',
	cancreateglobal tinyint( 2 ) NOT NULL default '0',
	eventslimit int( 11 ) NOT NULL default '0',
	extraslimit int( 11 ) NOT NULL default '0',
	
	categories varchar(255) NOT NULL default '',
	calendars varchar(255) NOT NULL default '',
	
	created datetime  NOT NULL default '0000-00-00 00:00:00',	
	
	PRIMARY KEY ( id ),
	KEY user (user_id  )
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery( $sql );
		if (!$db->query()){
			echo $db->getErrorMsg();
		}

		$sql = "ALTER TABLE #__jev_users ADD categories varchar(255) NOT NULL default ''";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jev_users ADD calendars varchar(255) NOT NULL default ''";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jev_users ADD created datetime  NOT NULL default '0000-00-00 00:00:00'";
		$db->setQuery( $sql );
		@$db->query();


		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_repbyday (
	rptday DATE  NOT NULL default '0000-00-00',
	rp_id int(12) NOT NULL default 0,
	catid int(11) NOT NULL default 1,
	INDEX (rptday),
	INDEX daycat ( rptday , catid )	
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_defaults (
	id int( 11 ) unsigned NOT NULL AUTO_INCREMENT ,
	title varchar(100) NOT NULL default "",
	name varchar(50) NOT NULL default "",
	subject text NOT NULL default "",
	value text NOT NULL default "",
	state tinyint(3) NOT NULL default 1,
	params text NOT NULL default "",
	PRIMARY KEY  (id),
	INDEX (name)
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();

		$sql = "ALTER TABLE #__jev_defaults ADD params text NOT NULL default ''";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "SHOW COLUMNS FROM #__jev_defaults";
		$db->setQuery( $sql );
		$cols = @$db->loadObjectList();
		foreach ($cols as $col){
			if ($col->Field=='name' && $col->Key=='PRI'){
				$sql = "ALTER TABLE #__jev_defaults DROP PRIMARY KEY";
				$db->setQuery( $sql );
				@$db->query();
			}
		}

		$sql = "ALTER TABLE #__jev_defaults ADD id int( 11 ) unsigned NOT NULL AUTO_INCREMENT , add key (id) ";
		$db->setQuery( $sql );
		@$db->query();

		$sql = "ALTER TABLE #__jev_defaults ADD PRIMARY KEY id  (id)";
		$db->setQuery( $sql );
		@$db->query();

		// Multi-category Mapping table
		$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_catmap(
	evid int(12) NOT NULL auto_increment,
	catid int(11) NOT NULL default 1,
	ordering int(5) unsigned NOT NULL default '0',
	UNIQUE KEY `key_event_category` (`evid`,`catid`)
) ENGINE=MyISAM $charset;
SQL;
		$db->setQuery($sql);
		$db->query();
		echo $db->getErrorMsg();
		
		// fill this table if upgrading !
		$sql = "SELECT count(evid) from #__jevents_catmap";
		$db->setQuery($sql);
		$catmaps = $db->loadResult();
		if ($catmaps <5 ){
			$sql = "REPLACE INTO #__jevents_catmap (evid, catid) SELECT ev_id, catid from #__jevents_vevent";
			$db->setQuery($sql);
			$db->query();
			
		}
		
		// 
		// get the view
		$this->view = & $this->getView("config","html");

		// Set the layout
		$this->view->setLayout('dbsetup');

		$this->view->display();
	}

	function maprpts(){
		$db	=& JFactory::getDBO();
		$db->setQuery("delete from jos_repbyday");
		$db->query();

		$db->setQuery("SELECT rpt.rp_id, rpt.startrepeat, rpt.endrepeat, evt.catid FROM #__jevents_repetition as rpt LEFT JOIN #__jevents_vevent as evt ON rpt.eventid=evt.ev_id");
		$rpts = $db->loadObjectList();
		jimport("joomla.utilities.date");
		foreach ($rpts as $rpt) {
			$startday = new JevDate(substr($rpt->startrepeat,0,10));
			$endday = new JevDate(substr($rpt->endrepeat,0,10));
			while ($endday->toUnix()>=$startday->toUnix()){
				$db->setQuery("replace into jos_jevents_repbyday (rptday,rp_id,catid) values('".$startday->toFormat(('%Y-%m-%d'))."',".$rpt->rp_id.",".$rpt->catid.")");
				$db->query();

				$startday = new JevDate($startday->toUnix()+86400);
			}

		}
	}

	// work in progress - needs a sponsor
	function convertjcal() {

		global $task;

		$cfg = & JEVConfig::getInstance();
		$option = $cfg->get("com_componentname", "com_events");
		
		$db	=& JFactory::getDBO();

		/**
		 * Categories first 
		 **/
		$query = "SELECT * FROM #__jcalpro_categories";
		$db->setQuery($query);
		$cats = $db->loadObjectList();

		foreach ($cats as $ec){
			// Remove identical category first !!
			// First remove the extra jevents category information
			$query="SELECT id FROM #__categories"
			."\n WHERE title='$ec->cat_name (jcal)' and section='com_events'";
			$db->setQuery($query);
			$ids = $db->loadResultArray();
			$idlist = implode(",",$ids);

			if (count($ids)>0){
				$idlist = implode(",",$ids);
				$query="DELETE FROM #__events_categories WHERE id IN ($idlist)";
				$db->setQuery($query);
				if( !$db->query() ) {
					$error = array( $db->getErrorMsg(), $query );
					echo "Error in - ".$error[1]."<br/>";
					echo "Error message is ".$error[0]."<hr/>";
				}

				$query="DELETE FROM #__categories"
				."\n WHERE id IN ($idlist)";
				$db->setQuery($query);
				if( !$db->query() ) {
					$error = array( $db->getErrorMsg(), $query );
					echo "Error in - ".$error[1]."<br/>";
					echo "Error message is ".$error[0]."<hr/>";
				}

			}


			// Assume for time being all parents = 0!!
			$query="INSERT INTO #__categories"
			."\n (parent_id, title, name, image, section, image_position, description, published, checked_out, checked_out_time, editor, ordering, access, count, params)"
			."\n VALUES (0, '$ec->cat_name (jcal)', '$ec->cat_name (jcal)', '', 'com_events' ,'left', '$ec->description', $ec->published, $ec->checked_out, '$ec->checked_out_time','',0,0,0,'') ";
			$db->setQuery($query);
			if( !$db->query() ) {
				$error = array( $db->getErrorMsg(), $query );
				echo "Error in - ".$error[1]."<br/>";
				echo "Error message is ".$error[0]."<hr/>";
			}

			// Now set the extra jevents category information
			$query="SELECT id FROM #__categories"
			."\n WHERE title='$ec->cat_name (jcal)'";
			$db->setQuery($query);
			$id = $db->loadResult();

			if ($id>0){
				$query="INSERT INTO #__events_categories"
				."\n (id, color)"
				."\n VALUES ($id, '$ec->color')";
				$db->setQuery($query);
				if( !$db->query() ) {
					$error = array( $db->getErrorMsg(), $query );
					echo "Error in - ".$error[1]."<br/>";
					echo "Error message is ".$error[0]."<hr/>";
				}
			}

		}

		/**
		 * Now to convert the events put them in a special series of icals from scratch called by their category names
		 */
		include_once(JPATH_SITE."/components/".JEV_COM_COMPONENT."/libraries/iCalImport.php");
		foreach ($cats as $ec) {
			// clean out any aborter migration attempts
			$query="DELETE FROM #__jevents_icsfile"
			."\n WHERE label='$ec->cat_name (jcal)'"
			."\n AND icaltype=2";
			$db->setQuery($query);
			if( !$db->query() ) {
				$error = array( $db->getErrorMsg(), $query );
				echo "Error in - ".$error[1]."<br/>";
				echo "Error message is ".$error[0]."<hr/>";
			}

			$query="SELECT id FROM #__categories"
			."\n WHERE title='$ec->cat_name (jcal)' and section='com_events'";
			$db->setQuery($query);
			$catid = $db->loadResult();
			if (is_null($catid) || 	$catid==0){
				echo "missing category selection<br/>";
				return;
			}
			// Should come from the form or existing item
			$access = 0;
			$state = 1;
			$icsLabel = "$ec->cat_name (jcal)";
			$icsid = 0;
			$icsFile = iCalICSFile::editICalendar($icsid,$catid,$access,$state,$icsLabel);
			$icsFileid = $icsFile->store();


			$query = "SELECT * FROM #__jcalpro_events"
			."\n WHERE cat=$ec->cat_id";
			$db->setQuery($query);
			$exevents = $db->loadObjectList();

			foreach ($exevents as $xv){
				$temp = new stdClass();
				$icalevent = new jEventCal($temp);
				$icalevent['uid']=md5(uniqid(rand(),true));
				$icalevent['adresse_info']="";
				// TODO check this
				$icalevent['allDayEvent']="off";
				$icalevent['contact_info']=$ec->contact."&nbsp;".$ec->email;
				$icalevent['content']=$ec->description."<hr/>".$ec->url;
				//$icalevent['publish_down']
				//$icalevent['publish_up']
				$icalevent['rinterval']=$ec->recur_val;
				$icalevent['title']=$ec->title;
				$icalevent['ics_id']= $icsFileid;
				/*
				$icalevent['start_time'] = JArrayHelper::getValue( $array, "start_time","08:00");
				else $end_time 			= JArrayHelper::getValue( $array, "end_time","15:00");
				$countuntil		= JArrayHelper::getValue( $array, "countuntil","count");
				$count 			= intval(JArrayHelper::getValue( $array, "count",1);
				$until			= JArrayHelper::getValue($array, "until",$data["publish_down"]);
				$whichby			= JArrayHelper::getValue($array, "whichby","bd");
				$byd_direction		= JArrayHelper::getValue($array, "byd_direction","off")=="off"?"+":"-";
				$byyearday 			= JArrayHelper::getValue($array, "byyearday","");
				$bm_direction		= JArrayHelper::getValue($array, "bm_direction","off")=="off"?"+":"-";
				$bymonth			= JArrayHelper::getValue($array, "bymonth","");
				$bwn_direction		= JArrayHelper::getValue($array, "bwn_direction","off")=="off"?"+":"-";
				$byweekno			= JArrayHelper::getValue($array, "byweekno","");
				$bmd_direction		= JArrayHelper::getValue($array, "bmd_direction","off")=="off"?"+":"-";
				$bymonthday			= JArrayHelper::getValue($array, "bymonthday","");
				$bd_direction		= JArrayHelper::getValue($array, "bd_direction","off")=="off"?"+":"-";
				$weekdays			= JArrayHelper::getValue($array, "weekdays",array());
				$weeknums			= JArrayHelper::getValue($array, "weeknums",array());
				$vevent->catid = JArrayHelper::getValue($array, "catid",0);
				$vevent->access = JArrayHelper::getValue($array, "access",0);
				$vevent->state =  intval(JArrayHelper::getValue($array, "state",0));
				*/
			}


		}


	}

}
