<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * This file based on Joomla config component Copyright (C) 2005 - 2008 Open Source Matters.
 *
 * @version     $Id: params.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class AdminParamsController extends JControllerAdmin
{

	/**
	 * Custom Constructor
	 */
	function __construct($default = array())
	{
		$user = JFactory::getUser();

		if (!JEVHelper::isAdminUser())
		{
			JFactory::getApplication()->redirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel", "Not Authorised - must be admin");
			return;
		}

		$default['default_task'] = 'edit';
		parent::__construct($default);

		$this->registerTask('apply', 'save');

	}

	/**
	 * Show the configuration edit form
	 * @param string The URL option
	 */
	function edit()
	{

		// get the view
		$this->view = & $this->getView("params", "html");

		//$model = $this->getModel('params');
		$model = $this->getModel('component');
		$table = & JTable::getInstance('extension');
		if (!$table->load(array("element" => "com_jevents", "type" => "component"))) // 1.6 mod
		{
			JError::raiseWarning(500, 'Not a valid component');
			return false;
		}
		// Backwards compatatbility
		$table->id = $table->extension_id;
		$table->option = $table->element;

		// Set the layout
		$this->view->setLayout('edit');

		$this->view->assignRef('component', $table);
		$this->view->setModel($model, true);
		$this->view->display();

	}

	/**
	 * Save the configuration
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		//echo $this->getTask();
		//exit;
		$component = JEV_COM_COMPONENT;

		$model = $this->getModel('params');
		$table = & JTable::getInstance('extension');
		//if (!$table->loadByOption( $component ))
		if (!$table->load(array("element" => "com_jevents", "type" => "component"))) // 1.6 mod
		{
			JError::raiseWarning(500, 'Not a valid component');
			return false;
		}
		

		$post = JRequest::get('post');
		$post['params'] = JRequest::getVar('jform', array(), 'post', 'array');
		$post['option'] = $component;
		$table->bind($post);

		// pre-save checks
		if (!$table->check())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// if switching from single cat to multi cat then reset the table entries
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!$params->get("multicategory", 0) && isset($post["params"]['multicategory']) && $post["params"]['multicategory'] == 1)
		{
			$db = JFactory::getDbo();
			$sql = "DELETE FROM #__jevents_catmap";
			$db->setQuery($sql);
			$db->query();			
			
			$sql = "REPLACE INTO #__jevents_catmap (evid, catid) SELECT ev_id, catid from #__jevents_vevent WHERE catid in (SELECT id from #__categories where extension='com_jevents')";
			$db->setQuery($sql);
			$db->query();
		}

		// save the changes
		if (!$table->store())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// Now save the form permissions data
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$option = JEV_COM_COMPONENT;
		$comp = JComponentHelper::getComponent(JEV_COM_COMPONENT);
		$id = $comp->id;
		// Validate the posted data.
		JForm::addFormPath(JPATH_COMPONENT);
		JForm::addFieldPath(JPATH_COMPONENT . '/elements');

		$form = $model->getForm();
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();
			$app = JFactory::getApplication();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i]))
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);
			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . '&task=params.edit', false));
			return false;
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id' => $id,
			'option' => $option
		);
		$return = $model->saveRules($data);
		
		//SAVE AND APPLY CODE FROM PRAKASH
		switch ($this->getTask()) {
			case 'apply':
				$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . '&task=params.edit', JText::_('CONFIG_SAVED'));
				break;
			default:
				$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=cpanel.cpanel", JText::_('CONFIG_SAVED'));
				break;
		}
		//$this->setRedirect( 'index.php?option='.JEV_COM_COMPONENT."&task=cpanel.cpanel", JText::_( 'CONFIG_SAVED' ) );
		//$this->setMessage(JText::_( 'CONFIG_SAVED' ));
		//$this->edit();

	}

	/**
	 * Apply the configuration
	 */
	function apply()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$component = JEV_COM_COMPONENT;

		$model = $this->getModel('params');
		$table = & JTable::getInstance('extension');
		//if (!$table->loadByOption( $component ))
		if (!$table->load(array("element" => "com_jevents", "type" => "component"))) // 1.6 mod
		{
			JError::raiseWarning(500, 'Not a valid component');
			return false;
		}
		

		$post = JRequest::get('post');
		$post['option'] = $component;
		$table->bind($post);

		// pre-save checks
		if (!$table->check())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// save the changes
		if (!$table->store())
		{
			JError::raiseWarning(500, $table->getError());
			return false;
		}

		// Now save the form permissions data
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$option = JEV_COM_COMPONENT;
		$comp = JComponentHelper::getComponent(JEV_COM_COMPONENT);
		$id = $comp->id;
		// Validate the posted data.
		JForm::addFormPath(JPATH_COMPONENT);
		JForm::addFieldPath(JPATH_COMPONENT . '/elements');
		$form = $model->getForm();
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			$app = JFactory::getApplication();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if (JError::isError($errors[$i]))
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_config.config.global.data', $data);
			// Redirect back to the edit screen.
			$this->setRedirect(JRoute::_('index.php?option=' . JEV_COM_COMPONENT . '&task=params.edit', false));
			return false;
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id' => $id,
			'option' => $option
		);
		$return = $model->saveRules($data);
		
		$this->setRedirect('index.php?option=' . JEV_COM_COMPONENT . "&task=params.edit", JText::_('CONFIG_SAVED'));
		//$this->setMessage(JText::_( 'CONFIG_SAVED' ));
		//$this->edit();

	}

	/**
	 * Cancel operation
	 */
	function cancel()
	{
		$this->setRedirect('index.php');

	}

	function dbsetup()
	{
		$db = & JFactory::getDBO();
		$db->setDebug(0);
		
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

		$sql = "SHOW COLUMNS FROM #__jevents_vevent";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("lockevent", $cols))
		{
			$sql = "alter table #__jevents_vevent add column lockevent tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("author_notified", $cols))
		{
			$sql = "alter table #__jevents_vevent add column author_notified tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("created", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevent ADD created datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->query();
		}

		$sql = "SHOW INDEX FROM #__jevents_vevent";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("stateidx", $cols))
		{
			$sql = "alter table #__jevents_vevent add index stateidx (state)";
			$db->setQuery($sql);
			@$db->query();
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

		$sql = "SHOW COLUMNS FROM #__jevents_vevdetail";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("modified", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD modified datetime  NOT NULL default '0000-00-00 00:00:00' ";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("color", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD color varchar(20) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("multiday", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD multiday tinyint(3) NOT NULL default 1";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("noendtime", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD noendtime tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("hits", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD hits int(11) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		$sql = "SHOW INDEX FROM #__jevents_vevdetail";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("searchIdx", $cols))
		{
			$sql = "ALTER TABLE #__jevents_vevdetail ADD FULLTEXT searchIdx (summary,description)";
			$db->setQuery($sql);
			@$db->query();
		}


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

		$sql = "SHOW INDEX FROM #__jevents_rrule";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("eventid", $cols))
		{
			$sql = "ALTER TABLE #__jevents_rrule ADD INDEX eventid (eventid)";
			$db->setQuery($sql);
			@$db->query();
		}

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

		$sql = "SHOW INDEX FROM #__jevents_repetition";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("eventstart", $cols))
		{
			$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventstart ( eventid , startrepeat )";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("eventend", $cols))
		{
			$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventend ( eventid , endrepeat )";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("eventdetail", $cols))
		{
			$sql = "ALTER TABLE #__jevents_repetition ADD INDEX eventdetail ( eventdetail_id  )";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("startrepeat", $cols))
		{
			$sql = "alter table #__jevents_repetition add index startrepeat (startrepeat)";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("endrepeat", $cols))
		{
			$sql = "alter table #__jevents_repetition add index endrepeat (endrepeat)";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("startend", $cols))
		{
			$sql = "alter table #__jevents_repetition add index startend (startrepeat,endrepeat)";
			$db->setQuery($sql);
			@$db->query();
		}

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

		$sql = "SHOW COLUMNS FROM #__jevents_exception";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("startrepeat", $cols))
		{
			$sql = "ALTER TABLE #__jevents_exception add column startrepeat datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("oldstartrepeat", $cols))
		{
			$sql = "ALTER TABLE #__jevents_exception add column oldstartrepeat datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("tempfield", $cols))
		{
			$sql = "ALTER TABLE #__jevents_exception add column tempfield datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->query();
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


		$sql = "SHOW COLUMNS FROM #__jevents_icsfile";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("overlaps", $cols))
		{
			$sql = "ALTER TABLE #__jevents_icsfile ADD overlaps tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("isdefault", $cols))
		{
			// Alter table
			$sql = "Alter table #__jevents_icsfile ADD COLUMN isdefault tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("ignoreembedcat", $cols))
		{
			$sql = "Alter table #__jevents_icsfile ADD COLUMN ignoreembedcat tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("autorefresh", $cols))
		{
			$sql = "Alter table #__jevents_icsfile ADD COLUMN autorefresh tinyint(3) NOT NULL default 0";
			$db->setQuery($sql);
			@$db->query();
		}

		$sql = "Alter table #__jevents_icsfile MODIFY COLUMN srcURL varchar(255) NOT NULL default '' ";
		$db->setQuery($sql);
		$db->query();

		$sql = "SHOW INDEX FROM #__jevents_icsfile";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Key_name");

		if (!array_key_exists("stateidx", $cols))
		{
			$sql = "alter table #__jevents_icsfile add index stateidx (state)";
			$db->setQuery($sql);
			@$db->query();
		}

		// Add one category by default if none exist already
		$sql = "SELECT id from #__categories where extension='com_jevents'";
		$db->setQuery($sql);
		$catid = $db->loadResult();
		
		if (!$catid) {
			JLoader::register('JEventsCategory',JEV_ADMINPATH."/libraries/categoryClass.php");
			$cat = new JEventsCategory($db);
			$cat->bind(array("title"=>JText::_( 'DEFAULT' ), "published"=>1, "color"=>"#CCCCFF", "access"=>1));
			$cat->store();
			$catid=$cat->id;
		}
		
		// Add one native calendar by default if none exist already
		$sql = "SELECT ics_id from #__jevents_icsfile WHERE icaltype=2";
		$db->setQuery($sql);
		$ics = $db->loadResult();

		if (!$ics || is_null($ics) || $ics == 0)
		{
			$sql = "INSERT INTO #__jevents_icsfile (label,filename,	icaltype,state,	access,	catid, isdefault) VALUES ('Default','Initial ICS File',2,1,1,$catid,1)";
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
		$db->setQuery($sql);
		if (!$db->query())
		{
			echo $db->getErrorMsg();
		}

		$sql = "SHOW COLUMNS FROM #__jev_users";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("categories", $cols))
		{
			$sql = "ALTER TABLE #__jev_users ADD categories varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("calendars", $cols))
		{
			$sql = "ALTER TABLE #__jev_users ADD calendars varchar(255) NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("created", $cols))
		{
			$sql = "ALTER TABLE #__jev_users ADD created datetime  NOT NULL default '0000-00-00 00:00:00'";
			$db->setQuery($sql);
			@$db->query();
		}


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

		$sql = "SHOW COLUMNS FROM #__jev_defaults";
		$db->setQuery($sql);
		$cols = @$db->loadObjectList("Field");

		if (!array_key_exists("params", $cols))
		{
			$sql = "ALTER TABLE #__jev_defaults ADD params text NOT NULL default ''";
			$db->setQuery($sql);
			@$db->query();
		}
			
		if (array_key_exists("name", $cols) && $cols["name"]->Key == 'PRI')
		{
			$sql = "ALTER TABLE #__jev_defaults DROP PRIMARY KEY";
			$db->setQuery($sql);
			@$db->query();
		}

		if (!array_key_exists("id", $cols))
		{
			$sql = "ALTER TABLE #__jev_defaults ADD id int( 11 ) unsigned NOT NULL AUTO_INCREMENT , add key (id) ";
			$db->setQuery($sql);
			@$db->query();
		}

		if (array_key_exists("id", $cols) && $cols["id"]->Key != 'PRI')
		{
			$sql = "ALTER TABLE #__jev_defaults ADD PRIMARY KEY id  (id)";
			$db->setQuery($sql);
			@$db->query();
		}

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
		if ($catmaps < 5)
		{
			$sql = "REPLACE INTO #__jevents_catmap (evid, catid) SELECT ev_id, catid from #__jevents_vevent";
			$db->setQuery($sql);
			$db->query();
		}

		// 
		// get the view
		$this->view = & $this->getView("params", "html");

		// Set the layout
		$this->view->setLayout('dbsetup');

		$this->view->display();

	}

}