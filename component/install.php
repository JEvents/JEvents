<?php

/**
 * copyright (C) 2012-JEVENTS_COPYRIGHT GWESystems Ltd - All rights reserved
 * @license GNU/GPLv3 www.gnu.org/licenses/gpl-3.0.html
 * */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;

#[\AllowDynamicProperties]
class com_jeventsInstallerScript
{

	//
	// Joomla installer functions
	//

	function install($parent)
	{

		if (version_compare(PHP_VERSION, '5.3.10', '<'))
		{
			Factory::getApplication()->enqueueMessage('Your webhost needs to use PHP 5.3.10 or higher to run this version of JEvents.  Please see http://php.net/eol.php', 'error');

			return false;
		}

		$this->createTables();

		$this->updateTables();

		return true;

	}

	//
	// Some sites are having autoIncremental value reset - So lets set it correctly.
	//

	private function fixAutoIncrementals() {
		$db = Factory::getDbo();

		// Fix auto incremental values on repetition table
		$query = $db
			->getQuery(true)
			->select('rp_id')
			->from($db->quoteName('#__jevents_repetition'))
			->order($db->quoteName('rp_id') . ' DESC')
			->setLimit(1);

		$db->setQuery($query);
		$lastRpId = (int) $db->loadResult();
		$nextRpId = $lastRpId +2; // Increase by one on the off chance someone created an event at the same time we install.

		$sql = <<<SQL
ALTER TABLE #__jevents_repetition AUTO_INCREMENT = $nextRpId;
SQL;
		$db->setQuery($sql);

		try {
			$db->execute();
		} catch (Throwable $e) {
			echo $e . "<br>";
            echo "Problem altering #__jevents_repetition<br>";

        }

		// Fix auto incremental values on event table
		$query = $db
			->getQuery(true)
			->select('ev_id')
			->from($db->quoteName('#__jevents_vevent'))
			->order($db->quoteName('ev_id') . ' DESC')
			->setLimit(1);

		$db->setQuery($query);
		$lastEv_id = (int) $db->loadResult();
		$nextEv_id = $lastEv_id +2; // Increase by one on the off chance someone created an event at the same time we install.

		$sql = <<<SQL
ALTER TABLE #__jevents_vevent AUTO_INCREMENT = $nextEv_id;
SQL;
		$db->setQuery($sql);

		try {
			$db->execute();
		} catch (Throwable $e) {
            echo $e . "<br>";
            echo "Problem altering #__jevents_vevent<br>";
		}

		// Fix auto incremental values on event detail table
		$query = $db
			->getQuery(true)
			->select('evdet_id')
			->from($db->quoteName('#__jevents_vevdetail'))
			->order($db->quoteName('evdet_id') . ' DESC')
			->setLimit(1);

		$db->setQuery($query);
		$lastEvd_id = (int) $db->loadResult();
		$nextEvd_id = $lastEvd_id +2; // Increase by one on the off chance someone created an event at the same time we install.

		$sql = <<<SQL
ALTER TABLE #__jevents_vevdetail AUTO_INCREMENT = $nextEvd_id;
SQL;
		$db->setQuery($sql);

		try {
			$db->execute();
		} catch (Throwable $e) {
            echo $e . "<br>";
            echo "Problem altering #__jevents_vevdetail<br>";
		}

	}

	private function createTables()
	{

		$db = Factory::getDbo();

		if (version_compare(JVERSION, "3.3", 'ge'))
		{
			$charset    = ($db->hasUTFSupport()) ? ' DEFAULT CHARACTER SET `utf8mb4` DEFAULT COLLATE=utf8mb4_unicode_ci' : '';
			$rowcharset = ($db->hasUTFSupport()) ? 'CHARACTER SET utf8mb4' : '';
		}
		else
		{
			$charset    = ($db->hasUTF()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
			$rowcharset = ($db->hasUTF()) ? 'CHARACTER SET utf8' : '';
		}

        $db->setQuery("SHOW TABLES LIKE '" . $db->getPrefix() . "jev%'");
        $tables = $db->loadColumn();

        /**
		 * create table if it doesn't exit
		 *
		 * For now :
		 *
		 * I'm ignoring attach,comment, resources, transp, attendee, related to, rdate, request-status
		 *
		 * Separate tables for rrule and exrule
		 */

        $exists = in_array( $db->getPrefix() . "jevents_vevent", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_vevent(
	ev_id int(12) NOT NULL auto_increment,
	icsid int(12) NOT NULL default 0,
	catid int(11) NOT NULL default 1,
	uid varchar(255) $rowcharset NOT NULL default "",
	refreshed datetime  NOT NULL default '0000-00-00 00:00:00',
	created datetime  NOT NULL default '0000-00-00 00:00:00',
	created_by int(11) unsigned NOT NULL default '0',
	created_by_alias varchar(100) NOT NULL default '',
	modified_by int(11) unsigned NOT NULL default '0',

	rawdata longtext NOT NULL ,
	recurrence_id varchar(30) NOT NULL default "",
	
	detail_id int(12) NOT NULL default 0,
	
	state tinyint(3) NOT NULL default 1,
	lockevent tinyint(3) NOT NULL default 0,
	author_notified tinyint(3) NOT NULL default 0,
	access int(11) unsigned NOT NULL default 0,
	
    tzid varchar(100) NOT NULL default '',
                        
	PRIMARY KEY  (ev_id),
    UNIQUE KEY (uid(190)),
	INDEX (icsid),
	INDEX stateidx (state),
    INDEX evaccess (access)                        
) $charset;
SQL;
            $db->setQuery( $sql );

            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_vevent");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

		/**
		 * create table if it doesn't exit
		 *
		 * For now :
		 *
		 * I'm ignoring attach,comment, resources, transp, attendee, related to, rdate, request-status
		 *
		 * Separate tables for rrule and exrule
		 */
        $exists = in_array( $db->getPrefix() . "jevents_vevdetail", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_vevdetail(
	evdet_id int(12) NOT NULL auto_increment,

	rawdata longtext NOT NULL ,
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
	description longtext NOT NULL ,
	geolon float NOT NULL default 0,
	geolat float NOT NULL default 0,
	location VARCHAR(500) NOT NULL default "",
	priority tinyint unsigned NOT NULL default 0,
	status varchar(20) NOT NULL default "",
	summary longtext NOT NULL ,
	contact VARCHAR(500) NOT NULL default "",
	organizer VARCHAR(500) NOT NULL default "",
	url text NOT NULL ,
	extra_info text NOT NULL,
	created varchar(30) NOT NULL default "",
	sequence int(11) NOT NULL default 1,
	state tinyint(3) NOT NULL default 1,
	modified datetime  NOT NULL default '0000-00-00 00:00:00',

	multiday tinyint(3) NOT NULL default 1,
	hits int(11) NOT NULL default 0,
	noendtime tinyint(3) NOT NULL default 0,
		
	PRIMARY KEY  (evdet_id),
	INDEX (location(190))
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_vevdetail");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

        $exists = in_array( $db->getPrefix() . "jevents_rrule", $tables );
        if (!$exists)
        {
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
	byyearday  varchar(100) NOT NULL default "",
	byweekno  varchar(50) NOT NULL default "",
	bymonth  varchar(50) NOT NULL default "",
	bysetpos  varchar(50) NOT NULL default "",
	wkst  varchar(50) NOT NULL default "",
	irregulardates text NOT NULL,
	PRIMARY KEY  (rr_id),
	INDEX (eventid)
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_rrule");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

        $exists = in_array( $db->getPrefix() . "jevents_repetition", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_repetition (
	rp_id bigint NOT NULL auto_increment,
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
	
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_repetition");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

		// exception_type 0=delete, 1=other exception
        $exists = in_array( $db->getPrefix() . "jevents_exception", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_exception (
	ex_id int(12) NOT NULL auto_increment,
	rp_id bigint NOT NULL default 0,
	eventid int(12) NOT NULL default 1,
	eventdetail_id int(12) NOT NULL default 0,	
	exception_type int(2) NOT NULL default 0,
	startrepeat datetime  NOT NULL default '0000-00-00 00:00:00',
	oldstartrepeat datetime  NOT NULL default '0000-00-00 00:00:00',
	tempfield datetime  NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (ex_id),
	KEY (eventid),
	KEY (rp_id)
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_exception");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
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
        $exists = in_array( $db->getPrefix() . "jevents_icsfile", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_icsfile(
	ics_id int(12) NOT NULL auto_increment,
	srcURL VARCHAR(500) NOT NULL default "",
	label varchar(200) NOT NULL UNIQUE default "",

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
	
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_icsfile");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

		// 1. Make sure users table exists
        $exists = in_array( $db->getPrefix() . "jev_users", $tables );
        if (!$exists)
        {
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
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jev_users");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

        $exists = in_array( $db->getPrefix() . "jev_defaults", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_defaults (
	id int( 11 ) unsigned NOT NULL AUTO_INCREMENT ,
	title varchar(100) NOT NULL default "",
	name varchar(50) NOT NULL default "",
	subject text NOT NULL ,
	value text NOT NULL ,
	state tinyint(3) NOT NULL default 1,
	params text NOT NULL ,
	language varchar(20) NOT NULL default '*',
	catid  int( 11 ) NOT NULL default '0',
	PRIMARY KEY  (id),
	INDEX (name),
	INDEX langcodename (language, catid, name )

) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jev_defaults");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }


		// Multi-category Mapping table
        $exists = in_array( $db->getPrefix() . "jevents_catmap", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_catmap(
	evid int(12) NOT NULL auto_increment,
	catid int(11) NOT NULL default 1,
	ordering int(5) unsigned NOT NULL default '0',
	UNIQUE KEY `key_event_category` (`evid`,`catid`)
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_catmap");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

		// Filter module mapping table
		// Maps filter values to URL keys
        $exists = in_array( $db->getPrefix() . "jevents_filtermap", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_filtermap (
	fid int(12) NOT NULL auto_increment,
	userid int(12) NOT NULL default 0,
	modid int(12) NOT NULL default 0,
	andor tinyint(3) NOT NULL default 0,
	filters TEXT NOT NULL,
        name varchar(255) $rowcharset NOT NULL default "",                        
	md5 VARCHAR(255) NOT NULL,
	PRIMARY KEY  (fid),
	INDEX (md5(190))
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_filtermap");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }

		/**
		 * create table if it doesn't exit
		 *
		 * For now :
		 *
		 * I'm ignoring attach,comment, resources, transp, attendee, related to, rdate, request-status
		 *
		 * Separate tables for rrule and exrule
		 */
        $exists = in_array( $db->getPrefix() . "jevents_translation", $tables );
        if (!$exists)
        {
            $sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jevents_translation (
	translation_id int(12) NOT NULL auto_increment,
	evdet_id int(12) NOT NULL default 0,

	description longtext NOT NULL ,
	location VARCHAR(500) NOT NULL default "",
	summary longtext NOT NULL ,
	contact VARCHAR(500) NOT NULL default "",
	extra_info text NOT NULL ,
	language varchar(20) NOT NULL default '*',

	PRIMARY KEY  (translation_id),
	INDEX (evdet_id),
	INDEX langdetail (evdet_id, language)
) $charset;
SQL;
            $db->setQuery( $sql );
            try
            {
                $db->execute();
            }
            catch ( Throwable $e )
            {
                Factory::getApplication()->enqueueMessage( "Problem creating #__jevents_translation");
                Factory::getApplication()->enqueueMessage($e->getMessage());
            }
        }
	}

	private function updateTables()
	{

		$db = Factory::getDbo();

		if (version_compare(JVERSION, "3.3", 'ge'))
		{
			$charset    = ($db->hasUTFSupport()) ? ' DEFAULT CHARACTER SET `utf8mb4` DEFAULT COLLATE=utf8mb4_unicode_ci' : '';
			$rowcharset = ($db->hasUTFSupport()) ? 'CHARACTER SET utf8mb4' : '';
		}
		else
		{
			$charset    = ($db->hasUTF()) ? ' DEFAULT CHARACTER SET `utf8`' : '';
			$rowcharset = ($db->hasUTF()) ? 'CHARACTER SET utf8' : '';
		}

		$sql = "SHOW COLUMNS FROM #__jevents_vevent";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("lockevent", $cols))
		{
			$sql = "alter table #__jevents_vevent add column lockevent tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("tzid", $cols))
		{
			$sql = "alter table #__jevents_vevent add column tzid varchar(100) NOT NULL default '' ";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("author_notified", $cols))
		{
			$sql = "alter table #__jevents_vevent add column author_notified tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("created", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevent ADD created datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->execute();
		}

        $sql = "SHOW INDEX FROM #__jevents_vevent";
        $db->setQuery($sql);
        $icols = @$db->loadObjectList("Key_name");

        if (array_key_exists("uid", $icols) && ((int) $icols["uid"]->Sub_part == 0 || (int) $icols["uid"]->Sub_part !== 190))
        {
            $sql = "alter table #__jevents_vevent drop index uid";
            $db->setQuery($sql);
            @$db->execute();
        }

		if (!array_key_exists("uid", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevent modify uid varchar(255) $rowcharset NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("stateidx", $icols))
		{
			$sql = "alter table #__jevents_vevent add index stateidx (state)";
			$db->setQuery($sql);
			@$db->execute();
		}

        if (!array_key_exists("uid", $icols))
        {
            $sql = "alter table #__jevents_vevent add UNIQUE KEY (uid(190))";
            $db->setQuery($sql);
            @$db->execute();
        }
        else if (array_key_exists("uid", $icols) && ((int) $icols["uid"]->Sub_part == 0 || (int) $icols["uid"]->Sub_part !== 190))
        {
            $sql = "alter table #__jevents_vevent add UNIQUE KEY (uid(190))";
            $db->setQuery($sql);
            @$db->execute();

        }

		if (!array_key_exists("evaccess", $icols))
		{
			// What is curtent value of sql_mode
			$db->setQuery("SELECT @@sql_mode");
			$sql_mode = @$db->loadResult();

			$db->setQuery("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'NO_ZERO_DATE',''))");
			@$db->execute();
			$sql = "ALTER TABLE #__jevents_vevent ADD INDEX evaccess (access)";
			$db->setQuery($sql);
			@$db->execute();

			// Return to old value
			$db->setQuery("SET SESSION sql_mode=(" . $db->quote($sql_mode) . ")");
			@$db->execute();
		}

        $sql = "SHOW COLUMNS FROM #__jevents_repetition";
        $db->setQuery($sql);
        $cols = @$db->loadObjectList("Field");
        if (array_key_exists("rp_id", $cols) && strtoupper($cols['rp_id']->Type) !== "BIGINT")
        {
            $sql = "ALTER TABLE #__jevents_repetition MODIFY COLUMN rp_id BIGINT NOT NULL auto_increment";
            $db->setQuery($sql);
            @$db->execute();
        }

        $sql = "SHOW COLUMNS FROM #__jevents_exception";
        $db->setQuery($sql);
        $cols = @$db->loadObjectList("Field");
        if (array_key_exists("rp_id", $cols) && strtoupper($cols['rp_id']->Type) !== "BIGINT")
        {
            $sql = "ALTER TABLE #__jevents_exception MODIFY COLUMN rp_id BIGINT NOT NULL";
            $db->setQuery($sql);
            @$db->execute();
        }

        $sql = "SHOW COLUMNS FROM #__jevents_vevdetail";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		$sql = "SHOW INDEX FROM #__jevents_vevdetail";
		$db->setQuery($sql);
		$indexcols = @$db->loadObjectList("Key_name");

		if (array_key_exists("location", $cols) && strtoupper($cols['location']->Type) !== "VARCHAR(500)")
		{

			if (array_key_exists("location", $indexcols))
			{
				$sql = "alter table #__jevents_vevdetail drop index location";
				$db->setQuery($sql);
				@$db->execute();
			}

			$sql = "ALTER TABLE #__jevents_vevdetail MODIFY COLUMN location VARCHAR(500) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();

			$sql = "ALTER TABLE #__jevents_vevdetail ADD INDEX location (location (190))";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (array_key_exists("contact", $cols) && strtoupper($cols['contact']->Type) !== "VARCHAR(500)")
		{
			$sql = "ALTER TABLE #__jevents_vevdetail MODIFY COLUMN contact VARCHAR(500) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();
		}
		if (array_key_exists("organizer", $cols) && strtoupper($cols['organizer']->Type) !== "VARCHAR(500)")
		{
			$sql = "ALTER TABLE #__jevents_vevdetail MODIFY COLUMN organizer VARCHAR(500) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("modified", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD modified datetime  NOT NULL default '0000-00-00 00:00:00' ";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("color", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD color varchar(20) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("multiday", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD multiday tinyint(3) NOT NULL default 1";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("noendtime", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD noendtime tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("hits", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD hits int(11) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}
		if (!array_key_exists("extra_info", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail modify extra_info text NOT NULL";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("loc_id", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD COLUMN loc_id int(11) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();

			$sql = "ALTER TABLE #__jevents_vevdetail ADD INDEX loc_id (loc_id)";
			$db->setQuery($sql);
			@$db->execute();

			// move across all the data
			$sql = "UPDATE #__jevents_vevdetail SET loc_id = CAST(location AS UNSIGNED) where location REGEXP '^-?[0-9]+$'";
			$db->setQuery($sql);
			@$db->execute();

		}

		if (!array_key_exists("location", $indexcols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD INDEX location (location (190))";
			$db->setQuery($sql);
			@$db->execute();
		}
        else
        {
            $index = $indexcols["location"];
            if ( isset($index->Sub_part) && intval($index->Sub_part) > 190)
            {
                $sql = "alter table #__jevents_vevdetail drop index location";
                $db->setQuery( $sql );
                @$db->execute();

                $sql = "ALTER TABLE #__jevents_vevdetail ADD INDEX location (location (190))";
                $db->setQuery( $sql );
                @$db->execute();
            }
        }

		if (!array_key_exists("multiday", $indexcols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD INDEX multiday (multiday)";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "SHOW COLUMNS FROM #__jevents_rrule";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("irregulardates", $cols))
		{
			$sql = "ALTER TABLE #__jevents_rrule ADD irregulardates text NOT NULL ";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "SHOW INDEX FROM #__jevents_rrule";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("eventid", $cols))
		{
			$sql = "ALTER TABLE #__jevents_rrule ADD INDEX eventid (eventid)";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "Alter table #__jevents_rrule  MODIFY COLUMN byyearday  varchar(100) NOT NULL default '' ";
		$db->setQuery($sql);
		$db->execute();

		$sql = "SHOW COLUMNS FROM #__jevents_filtermap";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

        $sql = "SHOW INDEX FROM #__jevents_filtermap";
        $db->setQuery($sql);
        $indexcols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("andor", $cols))
		{
			$sql = "ALTER TABLE #__jevents_filtermap ADD COLUMN andor tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("modid", $cols))
		{
			$sql = "ALTER TABLE #__jevents_filtermap ADD COLUMN modid int(12) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("name", $cols))
		{
			$sql = "ALTER TABLE #__jevents_filtermap ADD COLUMN name varchar(255) $rowcharset NOT NULL default '' ";
			$db->setQuery($sql);
			@$db->execute();
		}

        if (array_key_exists("md5", $indexcols))
        {
            $index = $indexcols["md5"];
            if ( isset($index->Sub_part) && intval($index->Sub_part) > 190)
            {
                $sql = "alter table #__jevents_filtermap drop index md5";
                $db->setQuery( $sql );
                @$db->execute();

                $sql = "ALTER TABLE #__jevents_filtermap ADD INDEX md5 (location (190))";
                $db->setQuery( $sql );
                @$db->execute();
            }
        }

		$sql = "SHOW INDEX FROM #__jevents_repetition";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("eventstart", $cols))
		{
			$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventstart ( eventid , startrepeat )";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("eventend", $cols))
		{
			$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventend ( eventid , endrepeat )";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("eventdetail", $cols))
		{
			$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventdetail ( eventdetail_id  )";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("startrepeat", $cols))
		{
			$sql = "alter table #__jevents_repetition add index startrepeat (startrepeat)";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("endrepeat", $cols))
		{
			$sql = "alter table #__jevents_repetition add index endrepeat (endrepeat)";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("startend", $cols))
		{
			$sql = "alter table #__jevents_repetition add index startend (startrepeat,endrepeat)";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "SHOW COLUMNS FROM #__jevents_exception";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("startrepeat", $cols))
		{
			$sql = "ALTER TABLE #__jevents_exception add column startrepeat datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("oldstartrepeat", $cols))
		{
			$sql = "ALTER TABLE #__jevents_exception add column oldstartrepeat datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("tempfield", $cols))
		{
			$sql = "ALTER TABLE #__jevents_exception add column tempfield datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "SHOW COLUMNS FROM #__jevents_icsfile";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		$sql = "SHOW INDEX FROM #__jevents_icsfile";
		$db->setQuery($sql);
		$indexcols = @$db->loadObjectList("Key_name");

		if (array_key_exists("label", $cols) && $cols["label"]->Type == "varchar(30)")
		{

			if (array_key_exists("label", $indexcols))
			{
				$sql = "alter table #__jevents_icsfile drop index label";
				$db->setQuery($sql);
				@$db->execute();
			}

			$sql = "ALTER TABLE #__jevents_icsfile MODIFY COLUMN label VARCHAR(200) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();

			$sql = "ALTER TABLE #__jevents_icsfile ADD INDEX label (label (120))";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("overlaps", $cols))
		{
			$sql = "ALTER TABLE #__jevents_icsfile ADD overlaps tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("isdefault", $cols))
		{
			// Alter table
			$sql = "Alter table #__jevents_icsfile ADD COLUMN isdefault tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("ignoreembedcat", $cols))
		{
			$sql = "Alter table #__jevents_icsfile ADD COLUMN ignoreembedcat tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("createnewcategories", $cols))
		{
			$sql = "Alter table #__jevents_icsfile ADD COLUMN createnewcategories tinyint(3) NOT NULL default 1";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("autorefresh", $cols))
		{
			$sql = "Alter table #__jevents_icsfile ADD COLUMN autorefresh tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "Alter table #__jevents_icsfile MODIFY COLUMN srcURL varchar(500) NOT NULL default ''";
		$db->setQuery($sql);
		$db->execute();

		if (!array_key_exists("stateidx", $indexcols))
		{
			$sql = "alter table #__jevents_icsfile add index stateidx (state)";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "SHOW COLUMNS FROM #__jev_users";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("categories", $cols))
		{
			$sql = "ALTER TABLE #__jev_users ADD categories varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("calendars", $cols))
		{
			$sql = "ALTER TABLE #__jev_users ADD calendars varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("created", $cols))
		{
			$sql = "ALTER TABLE #__jev_users ADD created datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "SHOW COLUMNS FROM #__jev_defaults";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("params", $cols))
		{
			$sql = "ALTER TABLE #__jev_defaults ADD params text NOT NULL ";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (array_key_exists("name", $cols) && $cols["name"]->Key == 'PRI')
		{
			$sql = "ALTER TABLE #__jev_defaults DROP PRIMARY KEY";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("id", $cols))
		{
			$sql = "ALTER TABLE #__jev_defaults ADD id int( 11 ) unsigned NOT NULL AUTO_INCREMENT , add key (id) ";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (array_key_exists("id", $cols) && $cols["id"]->Key != 'PRI')
		{
			$sql = "ALTER TABLE #__jev_defaults ADD PRIMARY KEY id  (id)";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("language", $cols))
		{
			$sql = "ALTER TABLE #__jev_defaults ADD language varchar(20) NOT NULL default '*'";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("catid", $cols))
		{
			$sql = "ALTER TABLE #__jev_defaults ADD catid  int( 11 ) NOT NULL default '0'";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (!array_key_exists("state", $cols))
		{
			$sql = "ALTER TABLE #__jev_defaults ADD state tinyint(3) NOT NULL default 1";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "SHOW INDEX FROM #__jev_defaults";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("langcodename", $cols))
		{
			$sql = "ALTER TABLE #__jev_defaults ADD INDEX langcodename (language, catid, name)";
			$db->setQuery($sql);
			@$db->execute();
		}

		$sql = "SHOW COLUMNS FROM #__jevents_translation";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (array_key_exists("location", $cols) && strtoupper($cols['location']->Type) !== "VARCHAR(500)")
		{
			$sql = "ALTER TABLE #__jevents_translation MODIFY COLUMN location VARCHAR(500) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();
		}

		if (array_key_exists("contact", $cols) && strtoupper($cols['contact']->Type) !== "VARCHAR(500)")
		{
			$sql = "ALTER TABLE #__jevents_translation MODIFY COLUMN contact VARCHAR(500) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->execute();
		}

		// fill this table if upgrading  and there are no mapped categories
		$sql = "SELECT count(*) FROM #__jevents_catmap";
		$db->setQuery($sql);
		$count = $db->loadResult();

		/*
		$sql = "DELETE FROM #__jevents_catmap";
		$db->setQuery($sql);
		$db->execute();
		*/

		if (!$count)
		{
			$sql = "REPLACE INTO #__jevents_catmap (evid, catid) SELECT ev_id, catid from #__jevents_vevent WHERE catid in (SELECT id from #__categories where extension='com_jevents')";
			$db->setQuery($sql);
			$db->execute();
		}

		$sql = "SHOW INDEX FROM #__jevents_catmap";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("key_evid", $cols))
		{
			$sql = "ALTER TABLE #__jevents_catmap ADD INDEX key_evid ( evid)";
			$db->setQuery($sql);
			@$db->execute();
		}

	}

	public function postflight($action, $adapter)
	{

		$app        = Factory::getApplication();

		$table     = Table::getInstance('extension');
		$component = "com_jevents";

		if (!$table->load(array("element" => "com_jevents", "type" => "component"))) // 1.6 mod
		{
			$app->enqueueMessage('Not a valid component', 'error');

			return false;
		}

		$params = ComponentHelper::getParams("com_jevents");

		$checkClashes = $params->get("checkclashes", 0);

		if ($params->get("noclashes", 0))
		{
			$params->set("checkconflicts", "2");
		}
		else if ($params->get("checkclashes", 0))
		{
			$params->set("checkconflicts", "1");
		}

		$paramsArray = $params->toArray();
		unset($paramsArray['checkclashes']);
		unset($paramsArray['noclashes']);
		$post['params'] = $paramsArray;
		$post['option'] = $component;

		$table->bind($post);

		// pre-save checks
		if (!$table->check())
		{
			$app->enqueueMessage($table->getError(), 'error');

			return false;
		}

		// save the changes
		if (!$table->store())
		{
			$app->enqueueMessage($table->getError(), 'error');

			return false;
		}

		return true;
	}

	function uninstall($parent)
	{
		// No nothing for now

	}

	function update($parent)
	{

		if (version_compare(PHP_VERSION, '5.3.10', '<'))
		{
			Factory::getApplication()->enqueueMessage('Your webhost needs to use PHP 5.3.10 or higher to run this version of JEvents.  Please see http://php.net/eol.php', 'error');

			return false;
		}

        $this->createTables();

        $this->updateTables();

		$this->fixAutoIncrementals();

		return true;

	}

}
