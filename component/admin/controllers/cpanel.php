<?php

/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: cpanel.php 3546 2012-04-20 09:08:44Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.controller');

class AdminCpanelController extends JController
{

	/**
	 * Controler for the Control Panel
	 * @param array		configuration
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('show', 'cpanel');
		$this->registerDefaultTask("cpanel");

	}

	function cpanel()
	{
		// check DB
		// check the latest column addition or change
		// do this in a way that supports mysql 4
		$db = & JFactory::getDBO();

		$sql = "SHOW COLUMNS FROM `#__jevents_catmap`";
		$db->setQuery($sql);
		$cols = $db->loadObjectList('Field');
		if (is_null($cols) || !isset($cols['evid']))
		{
			$this->setRedirect(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&task=config.dbsetup", false), JText::_('DATABASE_TABLE_SETUP_WAS_REQUIRED'));
			$this->redirect();
			//return;
		}

		$sql = "SHOW COLUMNS FROM `#__jevents_exception`";
		$db->setQuery($sql);

		$cols = $db->loadObjectList('Field');
		if (!isset($cols['tempfield']))
		{
			$session = JFactory::getSession();
			$session->set('fixexceptions', 1);
			$this->setRedirect(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&task=config.dbsetup", false), JText::_('DATABASE_TABLE_SETUP_WAS_REQUIRED'));
			$this->redirect();
			//return;
		}
		else
		{
			$session = JFactory::getSession();
			if ($session->get("fixexceptions", 0) == 1)
			{
				$session->set('fixexceptions', 0);
				$this->fixExceptions();
			}
		}

		// category table overlaps
		if (!JVersion::isCompatible("1.6.0"))
		{
			$sql = "SHOW COLUMNS FROM `#__jevents_categories`";
			$db->setQuery($sql);

			$cols = $db->loadObjectList('Field');
			if (!isset($cols['overlaps']))
			{
				$this->setRedirect(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&task=config.dbsetup", false), JText::_('DATABASE_TABLE_SETUP_WAS_REQUIRED'));
				$this->redirect();
				//return;
			}
		}

		// are config values setup correctyl
		$params = JComponentHelper::getParams(JEV_COM_COMPONENT);
		$jevadmin = $params->getValue("jevadmin", -1);
		if ($jevadmin == -1)
		{
			$this->setRedirect(JRoute::_("index.php?option=" . JEV_COM_COMPONENT . "&task=params.edit", false), JText::_('PLEASE_CHECK_CONFIGURATION_AND_SAVE'));
			$this->redirect();
		}

		// Make sure jevlayout is copied and up to date
		if ($params->getValue("installlayouts", 0))
		{

			// RSH Fix to allow the installation to work with J!1.6 11/19/10 - Adapater is now a subclass of JAdapterInstance!
			$jevlayout_file = (JVersion::isCompatible("1.6.0")) ? 'jevlayout_1.6.php' : 'jevlayout.php';

			jimport('joomla.filesystem.file');
			if (!JFile::exists(JPATH_SITE . "/libraries/joomla/installer/adapters/jevlayout.php") ||
					md5_file(JEV_ADMINLIBS . $jevlayout_file) != md5_file(JPATH_SITE . "/libraries/joomla/installer/adapters/jevlayout.php"))
			{
				JFile::copy(JEV_ADMINLIBS . $jevlayout_file, JPATH_SITE . "/libraries/joomla/installer/adapters/jevlayout.php");
			}
		}

		// get the view
		$this->view = & $this->getView("cpanel", "html");
		$sql = 'SHOW TABLES LIKE "' . $db->getPrefix() . 'events"';
		$db->setQuery($sql);
		$tables = $db->loadObjectList();
		if (count($tables) > 0)
		{
			$this->view->assign('migrated', 1);
		}
		else
		{
			$this->view->assign('migrated', 0);
		}

		if (JVersion::isCompatible("1.6.0")){
			$this->checkCategoryAssets();
		}

		// get all the raw native calendars
		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
		$nativeCals = $this->dataModel->queryModel->getNativeIcalendars();
		if (is_null($nativeCals) || count($nativeCals) == 0)
		{
			$this->view->assign("warning", JText::_('CALENDARS_NOT_SETUP_PROPERLY'));
		}

		// Set the layout
		$this->view->setLayout('cpanel');
		$this->view->assign('title', JText::_('CONTROL_PANEL'));

		$this->view->display();

	}

	function fixExceptions()
	{

		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__jevents_exception where exception_type=1 AND (oldstartrepeat='0000-00-00 00:00:00' OR  oldstartrepeat is null) ORDER BY eventid ASC, startrepeat asc");
		//$db->setQuery("SELECT * FROM #__jevents_exception where exception_type=1 ORDER BY eventid ASC, startrepeat asc");
		$rows = $db->loadObjectList("rp_id");
		echo $db->getErrorMsg();

		$eventexceptions = array();
		foreach ($rows as $row)
		{
			if (!array_key_exists($row->eventid, $eventexceptions))
			{
				$eventexceptions[$row->eventid] = array();
			}
			$eventexceptions[$row->eventid][$row->rp_id] = $row;
		}

		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");

		foreach ($eventexceptions as $eventid => $exceptions)
		{

			//echo "<hr/>processing event $eventid<br/>";
			$db->setQuery("SELECT * FROM #__jevents_exception where exception_type=0 and eventid=$eventid ORDER BY eventid ASC, startrepeat asc");
			$deletedexceptions = $db->loadObjectList("rp_id");

			$vevent = $this->dataModel->queryModel->getVEventById($eventid);
			// skip any orphans
			if (!$vevent)
			{
				// double check it doesn't exist then remove it
				$db->setQuery("SELECT ev.* FROM #__jevents_vevent as ev WHERE ev.ev_id = '$eventid'");
				$row = $db->loadObject();

				continue;
			}
			$event = new jIcalEventDB($vevent);
			if (!$event)
			{
				// we have a problem
				continue;
			}

			$array = get_object_vars($vevent);
			foreach ($array as $k => $v)
			{
				$array[strtoupper($k)] = $v;
			}
			$icalevent = iCalEvent::iCalEventFromDB($array);

			// fix the rrule data
			$icalevent->rrule->eventid = $eventid;
			if ($icalevent->rrule->until == 0)
				$icalevent->rrule->until = "";

			ob_start();
			$generatedrepetitions = $icalevent->getRepetitions(true);
			ob_get_clean();

			// Now put in the pseudo repeat ids
			$icalevent = $this->dataModel->queryModel->getEventById($eventid, 1, 'icaldb');
			$firstrepeat = $icalevent->getOriginalFirstRepeat();
			for ($r = 0; $r < count($generatedrepetitions); $r++)
			{
				$generatedrepetitions[$r]->pseudo_rp_id = $firstrepeat->rp_id() + $r;
			}

			// get the current repeats (will not include deleted ones)
			$db->setQuery("Select rpt.* from #__jevents_repetition as rpt where rpt.eventid = $eventid order by rpt.rp_id asc");
			$currentreprows = $db->loadObjectList("rp_id");
			$rpids = array_merge(array_keys($currentreprows), array_keys($deletedexceptions));
			sort($rpids);

			$currentrepetitions = array();
			$rindex = 0;
			foreach ($rpids as $rid)
			{
				if (!array_key_exists($rid, $currentreprows))
				{
					$rindex+=1;
					continue;
				}
				else
					$currentrepetitions[$rindex] = $currentreprows[$rid];
				$rindex+=1;
			}

			if (count($currentrepetitions) > 0)
			{
				if (count($generatedrepetitions) > 0)
				{

					// The repetitions should be in the same sequence
					$countcurrent = count($currentrepetitions);
					$countgenerated = count($generatedrepetitions);
					foreach ($currentrepetitions as $c => $current)
					{
						foreach ($generatedrepetitions as $g => $generated)
						{
							if ($current->startrepeat == $generated->startrepeat)
							{
								// now set the oldstartrepeat field if this is an exception
								//echo "matched $current->startrepeat rpid=" . $current->rp_id . " pseudo rp_id= " . $generated->pseudo_rp_id . "<br/>";
								if (array_key_exists($current->rp_id, $exceptions))
								{
									$db->setQuery("Update #__jevents_exception set oldstartrepeat=" . $db->Quote($current->startrepeat) . " WHERE rp_id=" . $current->rp_id);
									$db->query();
									unset($eventexceptions[$eventid][$current->rp_id]);
									unset($exceptions[$current->rp_id]);
								}
								unset($currentrepetitions[$c]);
								unset($generatedrepetitions[$g]);
							}
						}
					}

					// We have now dealt with the exceptions with matching dates (the easy ones!)
					// we have no more exceptions to look through so continue
					if (count($generatedrepetitions) == 0)
						continue;

					// This won't deal with scenario where a repeat has been moved and then deleted!
					if (count($deletedexceptions) > 0)
					{
						foreach ($deletedexceptions as $delrpid => $delex)
						{
							foreach ($generatedrepetitions as $g => $generated)
							{
								if ($generated->startrepeat == $delex->startrepeat)
								{
									unset($generatedrepetitions[$g]);
									unset($deletedexceptions[$delrpid]);
								}
							}
						}
					}

					// now match them by pseudo rp_id
					if (count($currentrepetitions) > 0)
					{
						//echo "Still more to process<br/>";
						foreach ($currentrepetitions as $c => $current)
						{
							foreach ($generatedrepetitions as $g => $generated)
							{
								if ($current->rp_id == $generated->pseudo_rp_id)
								{
									//echo "matched $current->startrepeat rpid=" . $current->rp_id . " pseudo rp_id= " . $generated->pseudo_rp_id . "<br/>";
									if (array_key_exists($current->rp_id, $exceptions))
									{
										$db->setQuery("Update #__jevents_exception set oldstartrepeat=" . $db->Quote($current->startrepeat) . " WHERE rp_id=" . $current->rp_id);
										$db->query();
										unset($eventexceptions[$eventid][$current->rp_id]);
										unset($exceptions[$current->rp_id]);
									}
									unset($currentrepetitions[$c]);
									unset($generatedrepetitions[$g]);
								}
							}
						}
					}

					if (count($deletedexceptions) == 0 && count($generatedrepetitions) == count($currentrepetitions))
					{
						$countcurrent = count($currentrepetitions);
						$gplus = 0;
						foreach ($currentrepetitions as $c => $current)
						{
							if (!array_key_exists($c, $generatedrepetitions))
							{
								$x = 1;
							}
							if (array_key_exists($current->rp_id, $exceptions))
							{
								$generated = $generatedrepetitions[$c];
								$db->setQuery("Update #__jevents_exception set oldstartrepeat=" . $db->Quote($generated->startrepeat) . " WHERE rp_id=" . $current->rp_id);
								$db->query();
								unset($eventexceptions[$eventid][$current->rp_id]);
								unset($exceptions[$current->rp_id]);
							}
							unset($currentrepetitions[$c]);
							unset($generatedrepetitions[$c]);
						}
					}

					foreach ($exceptions as $rpid => $exception)
					{
						$matched = false;
						foreach ($generatedrepetitions as $generatedrepetition)
						{
							if ($generatedrepetition->startrepeat == $exception->startrepeat)
							{
								
							}
						}
					}

					foreach ($currentrepetitions as $rep)
					{

						if (array_key_exists($rep->rp_id, $exceptions))
						{

							$db->setQuery("Update #__jevents_exception set oldstartrepeat=" . $db->Quote($rep->startrepeat) . " WHERE rp_id=" . $rep->rp_id);
							$db->query();
							//echo $rep->startrepeat . " " . $rep->rp_id . "<Br/>";
						}
					}
				}
				else
				{
					echo "no repeats?<br/>";
				}
			}
		}
		echo "all done";
		return;

	}

	public function checkCategoryAssets()
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT * FROM #__categories WHERE asset_id=0 and extension='com_jevents' order by level , id");
		$missingassets = $db->loadObjectList();
		if (count($missingassets) > 0)
		{
			foreach ($missingassets as $missingasset)
			{
				$this->insertAsset($missingasset);
			}
		}

		// Fix assets with no permissions set!
		$db->setQuery("SELECT * FROM #__assets WHERE name like 'com_jevents.category.%' AND rules=''");
		$blankruleassets = $db->loadObjectList('id');
		if ($blankruleassets && count ($blankruleassets)>0){
			$db->setQuery("UPDATE #__assets SET rules='".'{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}'."' WHERE name like 'com_jevents.category.%' AND rules=''");
			$db->query();
		}
		
	}

	private function insertAsset($object)
	{
		$db = JFactory::getDbo();
		// Getting the asset table
		$table = JTable::getInstance('Asset', 'JTable', array('dbo' => $db));

		// Getting the categories id's
		$db->setQuery("SELECT * FROM #__categories WHERE extension='com_jevents'");
		$categories = $db->loadObjectList('id');

		$db->setQuery("SELECT * FROM #__assets WHERE name like 'com_jevents.category.%'");
		$assets = $db->loadObjectList('id');

		$db->setQuery("SELECT * FROM #__assets WHERE name = 'com_jevents' and parent_id=1");
		$rootasset = $db->loadObject();

		$assets[$rootasset->id] = $rootasset;

		//
		// Correct extension
		//
		$id = $object->id;
		$table->name = "com_jevents.category.{$id}";

		// Setting rules values
		$table->rules = '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';
		$table->title = $db->escape($object->title);

		if ($object->parent_id==1)
		{
			$table->parent_id = $rootasset->id;
			// Check for logic 
			/*
			$db->setQuery("SELECT * FROM #__assets WHERE name = 'com_jevents.category." . $object->id . "'");
			$asset = $db->loadObject();
			echo $asset->name." ".$asset->parent_id . " vs ".$table->name . " ".$table->parent_id . "<br/>";
			 * 
			 */	 
		}
		else if (array_key_exists($object->parent_id, $categories) && $categories[$object->parent_id]->asset_id > 0)
		{
			$table->parent_id = $categories[$object->parent_id]->asset_id;
			// Check for logic 
			/*
			$db->setQuery("SELECT * FROM #__assets WHERE name = 'com_jevents.category." . $object->id . "'");
			$asset = $db->loadObject();
			echo $asset->name." ".$asset->parent_id . " vs ".$table->name . " ".$table->parent_id . "<br/>";
			 * 
			 */
		}
		
		// Make sure this asset doesn't exist already
		$db->setQuery("SELECT * FROM #__assets WHERE name = ".$db->quote($table->name));		
		$asset = $db->loadObject();
		if (!$asset){
			// Insert the asset
			$table->store();
		}
		else {
			$table = $asset;
		}

		// updating the category asset_id;
		$updatetable = '#__categories';
		$query = "UPDATE {$updatetable} SET asset_id = {$table->id}"
				. " WHERE id = {$id}";
		$db->setQuery($query);
		$db->query();

		// Check for query error.
		$error = $db->getErrorMsg();

		if ($error)
		{
			throw new Exception($error);
			return false;
		}

		return true;

	}

}
