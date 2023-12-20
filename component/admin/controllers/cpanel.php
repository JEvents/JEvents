<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: cpanel.php 3546 2012-04-20 09:08:44Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('JPATH_BASE') or die('Direct Access to this location is not allowed.');

jimport('joomla.application.component.controlleradmin');

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;

#[\AllowDynamicProperties]
class AdminCpanelController extends AdminController
{

	/**
	 * Controler for the Control Panel
	 *
	 * @param array        configuration
	 */
	function __construct($config = array())
	{

		parent::__construct($config);
		$this->registerTask('show', 'cpanel');
		$this->registerDefaultTask("cpanel");

	}

	function cpanel()
	{

        $app    = Factory::getApplication();
		$db     = Factory::getDbo();

		// Make sure RSVP Pro and RSVP are not both enabled
		$rsvpplugin    = PluginHelper::isEnabled("jevents", "jevrsvp");
		$rsvpproplugin = PluginHelper::isEnabled("jevents", "jevrsvppro");
		if ($rsvpproplugin && $rsvpplugin)
		{
			$app->enqueueMessage(Text::_("JEV_INSTALLED_RSVP_AND_RSVPPRO_INCOMPATIBLE"), "ERROR");
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->update('#__extensions')
				->set('enabled = 0')
				->where('enabled = 1')
				->where('type = ' . $db->quote('plugin'))
				->where('folder = ' . $db->quote('jevents'))
				->where('element = ' . $db->quote('jevrsvp'))
				->where('state IN (0,1)');
			$db->setQuery($query);
			$db->execute();
		}

		// Add one category by default if none exist already
		$sql = "SELECT id from #__categories where extension='com_jevents'";
		$db->setQuery($sql);
		$catid = $db->loadResult();

		if (!$catid)
		{
			JLoader::register('JEventsCategory', JEV_ADMINPATH . "/libraries/categoryClass.php");
			$cat = new JEventsCategory($db);
			$cat->bind(array("title" => Text::_('DEFAULT'), "alias" => "default", "published" => 1, "color" => "#CCCCFF", "access" => 1));
			$cat->store();
			$catid = $cat->id;
		}

		// Add one native calendar by default if none exist already
		$sql = "SELECT ics_id from #__jevents_icsfile WHERE icaltype=2";
		$db->setQuery($sql);
		$ics = $db->loadResult();

		if (!$ics || is_null($ics) || $ics == 0)
		{
			$sql = "INSERT INTO #__jevents_icsfile (label,filename,	icaltype,state,	access,	catid, isdefault) VALUES ('Default','Initial ICS File',2,1,1,$catid,1)";
			$db->setQuery($sql);
			try {
				$db->execute();
			} catch (Exception $e) {
				echo $e;
			}
		}

		if (file_exists(JEV_ADMINPATH . "install.php"))
		{
			include_once(JEV_ADMINPATH . "install.php");
			$installer = new com_jeventsInstallerScript();
			$installer->update(false);
		}

		// Are the config values setup correct
		$params   = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$jevadmin = $params->get("jevadmin", -1);

		if ($jevadmin == -1)
		{
			$this->setRedirect(Route::_("index.php?option=" . JEV_COM_COMPONENT . "&task=params.edit", false), Text::_('PLEASE_CHECK_CONFIGURATION_AND_SAVE'));
			$this->redirect();
		}

		// Make sure jevlayout is copied and up to date
		if ($params->get("installlayouts", 0))
		{

			// RSH Fix to allow the installation to work with J!1.6 11/19/10 - Adapater is now a subclass of JAdapterInstance!
			$jevlayout_file = 'jevlayout.php';

			jimport('joomla.filesystem.file');
			if (!File::exists(JPATH_SITE . "/libraries/joomla/installer/adapters/jevlayout.php") ||
				md5_file(JEV_ADMINLIBS . $jevlayout_file) != md5_file(JPATH_SITE . "/libraries/joomla/installer/adapters/jevlayout.php"))
			{
				File::copy(JEV_ADMINLIBS . $jevlayout_file, JPATH_SITE . "/libraries/joomla/installer/adapters/jevlayout.php");
			}
		}

		$this->mergeMenus();

		// get the view
		$this->view = $this->getView("cpanel", "html");
		$sql        = 'SHOW TABLES LIKE "' . $db->getPrefix() . 'events"';
		$db->setQuery($sql);
		$tables = $db->loadObjectList();
		if (count($tables) > 0)
		{
			$this->view->migrated = 1;
		}
		else
		{
			$this->view->migrated = 0;
		}
		$this->checkCategoryAssets();


		// get all the raw native calendars
		$this->dataModel = new JEventsDataModel("JEventsAdminDBModel");
		$nativeCals      = $this->dataModel->queryModel->getNativeIcalendars();
		if (is_null($nativeCals) || count($nativeCals) == 0)
		{

			Factory::getApplication()->enqueueMessage(Text::_("CALENDARS_NOT_SETUP_PROPERLY"), 'warning');
		}

		/*
		 * We disable this check as in Joomla 3.4 they changed the way packages names are stored in DB and it's no longer necessary.
		 if (JEVHelper::isAdminUser())
		{
			$this->checkLanguagePackages();
		}*/

		// Check for orphan events and correct this
		$this->fixOrphanEvents();

		// Set the layout
		$this->view->setLayout('cpanel');
		$this->view->title = Text::_('CONTROL_PANEL');

        $this->checkcollations();

		$this->view->display();

	}

	private function mergeMenus()
	{
		$db     = Factory::getDbo();

		// use frogs to simulate the need to merge
		//$extensions = array("frogs", "jevlocations", "jeventstags", "jevpeople", "rsvppro");
		$extensions = array("jevlocations", "jeventstags", "jevpeople", "rsvppro");
		$toDisable = "";
		foreach ($extensions as $extension)
		{
			$toDisable .= strlen($toDisable) > 0 ? ' OR ' : '';
			$toDisable .= ' link LIKE "%index.php?option=com_' . $extension . '%"';
		}

		$sql = 'SELECT * FROM  #__menu 
		where client_id IN (1,2) AND menutype = "main"  and parent_id = 1 AND (' . $toDisable . ')';
		$db->setQuery($sql);
		$oldrows = $db->loadObjectList();

		$sql = 'SELECT * FROM  #__menu 
		where client_id = 2 AND menutype = "main"  AND (' . $toDisable . ')';
		$db->setQuery($sql);
		$disabledrows = $db->loadObjectList();

		// Delete the old menu items (i.e. with parent_id = 1)
		// use client_id = 2  since published = 0 doesn't disable it!
		if (count($oldrows))
		{
			$sql = 'DELETE FROM  #__menu
		where client_id IN (1,2) AND menutype = "main"  and parent_id = 1 AND (' . $toDisable . ')';
			$db->setQuery($sql);
			$db->execute();
		}

		// Disable uninstalled extensions
		$rebuild = false;
		foreach ($extensions as $extension)
		{
			// getComponent strict = true
			$component = ComponentHelper::getComponent("com_" . $extension, true);
			if (!$component->enabled)
			{
				$rebuild = true;
				$sql = 'UPDATE  #__menu
		set client_id= 2 
		where client_id=1 AND menutype = "main"  AND ' . ' link LIKE "%index.php?option=com_' . $extension . '%"';
				$db->setQuery($sql);
				$db->execute();
			}
			else if (count($disabledrows))
			{
				foreach ($disabledrows as $disabledrow)
				{
					if (strpos($disabledrow->link, "index.php?option=com_" . $extension) === 0)
					{
						$rebuild = true;
						$sql = 'UPDATE  #__menu
		set client_id = 1
		where client_id =2 AND menutype = "main"  AND ' . ' link LIKE "%index.php?option=com_' . $extension . '%"';
						$db->setQuery($sql);
						$db->execute();
					}
				}

			}
		}

		if (count($oldrows) || $rebuild)
		{
			JLoader::register('TableMenu', JPATH_PLATFORM . '/joomla/database/table/menu.php');
			// rebuild the menus
			$menu = Table::getInstance('Menu');
			$menu->rebuild();
		}
	}

	private function mergeMenusOLD()
	{

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$db     = Factory::getDbo();

		// merge/unmerge menu items?
		// Joomla 2.5 version
		$sql = 'select id from #__menu where client_id=1 and parent_id=1 and (title="com_jevents" OR title="COM_JEVENTS_MENU")';
		$db->setQuery($sql);
		$parent = $db->loadResult();

		$tochange = 'LOWER(title)="com_jevlocations"  OR LOWER(title)="com_jeventstags"  OR LOWER(title)="com_jevpeople"  OR LOWER(title)="com_rsvppro" OR LOWER(title)="com_jevlocations_menu"  OR LOWER(title)="com_jeventstags_menu"  OR LOWER(title)="com_jevpeople_menu"  OR LOWER(title)="com_rsvppro_menu" OR LOWER(alias)="jevents-tags"  ';
		$toexist  = ' link="index.php?option=com_jevlocations"  OR link="index.php?option=com_jeventstags"  OR link="index.php?option=com_jevpeople"  OR link="index.php?option=com_rsvppro" ';

		// is this an upgrade of JEvents in which case we may have lost the submenu items and may need to recreate them
		$sql = 'SELECT id, title, alias, link FROM #__menu where client_id=1 AND (
		' . $toexist . '
		)';

		// Order by parent id to remove the appropriate duplicate - list the ones we want to keep first
		$sql .= ' ORDER BY parent_id ' . ($params->get("mergemenus", 1) ? 'desc' : 'asc');
		$db->setQuery($sql);
		$existingmenus = $db->loadObjectList();

		if (!$existingmenus)
		{
			$existingmenus = array();
		}

		// are there any duplicates
		$links       = array();
		$updatemenus = false;
		foreach ($existingmenus as $em)
		{
			if (array_key_exists($em->link, $links))
			{
				$sql = "DELETE FROM #__menu where client_id and id=$em->id OR parent_id=$em->id";
				$db->setQuery($sql);
				$db->execute();
				$updatemenus = true;
			}
			else
			{
				$links[$em->link] = $em;
			}
		}
		if ($updatemenus)
		{
			JLoader::register('TableMenu', JPATH_PLATFORM . '/joomla/database/table/menu.php');
			// rebuild the menus
			$menu = Table::getInstance('Menu');
			$menu->rebuild();
		}

		// find list of installed addons
		$installed = 'element="com_jevlocations"  OR element="com_jeventstags"  OR element="com_jevpeople"  OR element="com_rsvppro" ';
		$sql       = 'SELECT element,extension_id FROM #__extensions  where type="component" AND (
		' . $installed . '
		)';

		$db->setQuery($sql);
		$installed = $db->loadObjectList();

		foreach ($installed as $missingmenu)
		{
			if (array_key_exists("index.php?option=" . $missingmenu->element, $links))
			{
				continue;
			}
			JLoader::register('TableMenu', JPATH_PLATFORM . '/joomla/database/table/menu.php');
			$table                   = Table::getInstance('Menu', 'Table');
			$table->id               = 0;
			$table->title            = $missingmenu->element;
			$table->alias            = str_replace("_", "-", $missingmenu->element);
			$table->path             = $table->alias;
			$table->link             = "index.php?option=" . $missingmenu->element;
			$table->type             = "component";
			$table->img              = "class:component";
			$table->parent_id        = 1;
			$table->client_id        = 1;
			$table->component_id     = $missingmenu->extension_id;
			$table->level            = 1;
			$table->home             = 0;
			$table->checked_out      = 0;
			$table->checked_out_time = $db->getNullDate();
			$table->setLocation(1, "last-child");
			$table->store();
		}

		// Fix Tags menu item title if needed
		$sql = 'UPDATE  #__menu
		set title = "COM_JEVENTSTAGS"
		where client_id=1 AND alias="jevents-tags"';

		$db->setQuery($sql);
		try {
			$db->execute();
		} catch (Exception $e) {
			echo $e;
		}

		// Fix Managed People menu item if needed
		$sql = 'UPDATE  #__menu
		set menutype = "main" where client_id=1 AND menutype="" AND alias="com-jevpeople"';

		$db->setQuery($sql);
		try {
			$db->execute();
		} catch (Exception $e) {
			echo $e;
		}

		// Is the JEvents menu item completely missing or corrupted - if so then skip the rest
		if (!$parent)
		{
			return;
		}

		$updatemenus = false;
		if ($params->get("mergemenus", 1))
		{

			$sql = 'SELECT count(id) FROM #__menu 
			where client_id=1 AND parent_id=1 AND (
				' . $tochange . '
			)';
			$db->setQuery($sql);
			$mus = $db->loadResult();
			if ($mus)
			{
				// check to see if we are creating a duplicate from an upgrade of an addon!
				$sql = 'SELECT * FROM #__menu 
				where client_id=1 AND parent_id=1 AND (
					' . $tochange . '
				)';
				$db->setQuery($sql);
				$tomerge = $db->loadObjectList();

				$sql = 'SELECT * FROM #__menu
				where client_id=1 AND parent_id=' . $parent . '  AND (
					' . $tochange . '
				)';
				$db->setQuery($sql);
				$alreadymerged = $db->loadObjectList();

				if ($alreadymerged)
				{
					foreach ($tomerge as $checkitem)
					{
						foreach ($alreadymerged as $merged)
						{
							if ($merged->alias == $checkitem->alias && $merged->link == $checkitem->link)
							{
								// remove duplicates
								$sql = "DELETE FROM #__menu  where id=$checkitem->id";
								$db->setQuery($sql);
								$db->execute();
							}
						}
					}
				}
				$updatemenus = true;

				$sql = 'UPDATE  #__menu 
				set parent_id = ' . $parent . ', level = 2
				where client_id=1 AND parent_id=1 AND (
				' . $tochange . '
				)';
				$db->setQuery($sql);
				try {
					$db->execute();
				} catch (Exception $e) {
					echo $e;
				}
			}
		}
		else
		{
			$sql = 'SELECT count(id) FROM #__menu 
			where client_id=1 AND parent_id=' . $parent . ' AND (
			' . $tochange . '
			)';
			$db->setQuery($sql);
			$mus = $db->loadResult();
			if ($mus)
			{
				$updatemenus = true;
				// Joomla 2.5 version
				$sql = 'UPDATE  #__menu 
					set parent_id = 1, level = 1
					where client_id=1 AND parent_id=' . $parent . ' AND (
					' . $tochange . '
					)';
				$db->setQuery($sql);
				try {
					$db->execute();
				} catch (Exception $e) {
					echo $e;
				}
			}
		}

		if ($updatemenus)
		{
			JLoader::register('TableMenu', JPATH_PLATFORM . '/joomla/database/table/menu.php');
			// rebuild the menus
			$menu = Table::getInstance('Menu');
			$menu->rebuild();
		}
	}

	public function checkCategoryAssets()
	{

		$db = Factory::getDbo();
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
		if ($blankruleassets && count($blankruleassets) > 0)
		{
			$db->setQuery("UPDATE #__assets SET rules='" . '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}' . "' WHERE name like 'com_jevents.category.%' AND rules=''");
			$db->execute();
		}

		// Fix assets with no parents set!
		$db->setQuery("SELECT * FROM #__assets WHERE name like 'com_jevents.category.%' AND parent_id=0");
		$blankparentassets = $db->loadObjectList('id');
		foreach ($blankparentassets as $blankparentasset)
		{
			$catid = str_replace('com_jevents.category.', "", $blankparentasset->name);
			$cat   = Table::getInstance("category");
			$cat->load($catid);
			$cat->store();
		}

		// Fix assets with no parents set!
		$db->setQuery("SELECT * FROM #__assets WHERE name like 'com_jevpeople.category.%' AND parent_id=0");
		$blankparentassets = $db->loadObjectList('id');
		foreach ($blankparentassets as $blankparentasset)
		{
			$catid = str_replace('com_jevpeople.category.', "", $blankparentasset->name);
			$cat   = Table::getInstance("category");
			$cat->load($catid);
			$cat->store();
		}

	}

	private function insertAsset($object)
	{

		$db = Factory::getDbo();
		// Getting the asset table
		$table = Table::getInstance('Asset', 'Table', array('dbo' => $db));

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
		$id          = $object->id;
		$table->name = "com_jevents.category.{$id}";

		// Setting rules values
		$table->rules = '{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';
		$table->title = $db->escape($object->title);

		if ($object->parent_id == 1)
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
		$db->setQuery("SELECT * FROM #__assets WHERE name = " . $db->quote($table->name));
		$asset = $db->loadObject();
		if (!$asset)
		{
			// Insert the asset
			$table->store();
		}
		else
		{
			$table = $asset;
		}

		// updating the category asset_id;
		$updatetable = '#__categories';
		$query       = "UPDATE {$updatetable} SET asset_id = {$table->id}"
			. " WHERE id = {$id}";
		$db->setQuery($query);

		try {
			$db->execute();
		} catch (Exception $e) {
			throw new Exception($e);
			return false;
		}


		return true;

	}

	private function fixOrphanEvents()
	{

		$db         = Factory::getDbo();
		$hasOrphans = false;

		$sql = "SELECT ev.ev_id from #__jevents_vevent as ev
LEFT JOIN #__categories as cat on cat.id=ev.catid AND extension='com_jevents'
WHERE cat.id is null
";
		$db->setQuery($sql);
		$orphans = $db->loadColumn();

		if ($orphans && count($orphans) > 0)
		{
			$catid = $this->createOrphanCategory();
			if ($catid)
			{
				$sql = "UPDATE #__jevents_vevent SET catid=$catid where ev_id IN (" . implode(",", $orphans) . ")";
				$db->setQuery($sql);
				$db->execute();
				$hasOrphans = true;
			}
		}

		$params         = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$multicategory  = $params->get("multicategory",0);
		if ($multicategory) {

			// Start by removing orphans from the category mapping table itself
			$sql = "DELETE FROM #__jevents_catmap 
			WHERE evid NOT IN (SELECT ev_id FROM #__jevents_vevent)";
			$db->setQuery($sql);
			$db->execute();

			// Do the bad category Ids first
			$sql = "SELECT catmap.* FROM #__jevents_catmap as catmap 
	LEFT JOIN #__categories as cat on cat.id=catmap.catid AND extension='com_jevents'
	WHERE cat.id is null	";
			$db->setQuery($sql);
			$orphans = $db->loadObjectList("evid");

			if ($orphans && count($orphans) > 0)
			{
				$catid = $this->createOrphanCategory();
				if ($catid)
				{
					foreach ($orphans as $orphan)
					{
						// should not set multiple copies of this
						$sql = "SELECT catmap.* FROM #__jevents_catmap as catmap  where evid=" . $orphan->evid . " AND catid=" . $catid;
						$db->setQuery($sql);
						if ($db->loadObject())
						{
							$sql = "DELETE FROM #__jevents_catmap where evid=" . $orphan->evid . " AND catid=" . $orphan->catid;
							$db->setQuery($sql);
							$db->execute();
						}
						else
						{
							$sql = "UPDATE #__jevents_catmap SET catid=$catid where evid=" . $orphan->evid . " AND catid=" . $orphan->catid;
							$db->setQuery($sql);
							$db->execute();
						}
						$hasOrphans = true;
					}
				}
			}
			// Now do the missing category mappings!
			$sql = "SELECT ev.ev_id from #__jevents_vevent as ev
	LEFT JOIN  #__jevents_catmap as catmap on ev.ev_id=catmap.evid
	LEFT JOIN #__categories as cat on cat.id=catmap.catid AND extension='com_jevents'
	WHERE cat.id is null
	";
			$db->setQuery($sql);
			$orphans = $db->loadColumn();

			if ($orphans && count($orphans) > 0)
			{
				$catid = $this->createOrphanCategory();
				if ($catid)
				{
					foreach ($orphans as $orphan)
					{
						$sql = "REPLACE INTO #__jevents_catmap (evid, catid) VALUES($orphan, $catid)";
						$db->setQuery($sql);
						$db->execute();
						$hasOrphans = true;
					}
				}
			}
		}

		$sql = "SELECT ev.ev_id from #__jevents_vevent as ev
LEFT JOIN #__jevents_icsfile as ics on ics.ics_id=ev.icsid
WHERE ics.ics_id is null
";
		$db->setQuery($sql);
		$orphans = $db->loadColumn();

		if ($orphans && count($orphans) > 0)
		{
			$icsid = $this->createOrphanCalendar();
			if ($icsid)
			{
				$sql = "UPDATE #__jevents_vevent SET icsid=$icsid where ev_id IN (" . implode(",", $orphans) . ")";
				$db->setQuery($sql);
				$db->execute();
				$hasOrphans = true;
			}
		}


		if ($hasOrphans)
		{
			Factory::getApplication()->enqueueMessage(Text::_("JEV_ORPHAN_EVENTS_DETECTED_AND_PLACED_IN_SPECIAL_ORPHAN_EVENT_CATEGORY_OR_CALENDAR"));
		}


	}

	private function createOrphanCategory()
	{

		$db = Factory::getDbo();

		$sql = "SELECT id from #__categories where extension='com_jevents' AND title='Orphans'";
		$db->setQuery($sql);
		$catid = $db->loadResult();

		// Add orphan category if none exist already
		if (!$catid)
		{
			JLoader::register('JEventsCategory', JEV_ADMINPATH . "/libraries/categoryClass.php");
			$cat = new JEventsCategory($db);
			$cat->bind(array("title" => "Orphans", "published" => 0, "color" => "#CCCCFF", "access" => 1));
			$cat->store();
			$catid = $cat->id;
		}

		return $catid;

	}

	private function createOrphanCalendar()
	{

		$catid = $this->createOrphanCategory();
		$db    = Factory::getDbo();
		// Add orphan native calendar by default if none exist already
		$sql = "SELECT ics_id from #__jevents_icsfile WHERE icaltype=2 and label='Orphans'";
		$db->setQuery($sql);
		$ics = $db->loadResult();

		if (!$ics || is_null($ics) || $ics == 0)
		{
			$sql = "INSERT INTO #__jevents_icsfile (label,filename,	icaltype,state,	access,	catid, isdefault) VALUES ('Orphans','Orphan ICS File',2,0,1,$catid,0)";
			$db->setQuery($sql);
			$db->execute();

			$sql = "SELECT ics_id from #__jevents_icsfile WHERE icaltype=2 and label='Orphans'";
			$db->setQuery($sql);
			$ics = $db->loadResult();
		}

		return $ics;
	}

	function support()
	{

		//Get the view
		$this->view = $this->getView("cpanel", "html");

		// Set the layout
		$this->view->setLayout('support');
		$this->view->title = Text::_('CONTROL_PANEL');

		$this->view->display();
	}

	function fixExceptions()
	{

		$db = Factory::getDbo();
		$db->setQuery("SELECT * FROM #__jevents_exception where exception_type=1 AND (oldstartrepeat='0000-00-00 00:00:00' OR  oldstartrepeat is null) ORDER BY eventid ASC, startrepeat asc");
		//$db->setQuery("SELECT * FROM #__jevents_exception where exception_type=1 ORDER BY eventid ASC, startrepeat asc");
		try
		{
			$rows = $db->loadObjectList("rp_id");
		} catch (Exception $e) {
			echo $e;
		}

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
			$icalevent   = $this->dataModel->queryModel->getEventById($eventid, 1, 'icaldb');
			$firstrepeat = $icalevent->getOriginalFirstRepeat();
			for ($r = 0; $r < count($generatedrepetitions); $r++)
			{
				$generatedrepetitions[$r]->pseudo_rp_id = $firstrepeat->rp_id() + $r;
			}

			// get the current repeats (will not include deleted ones)
			$db->setQuery("Select rpt.* from #__jevents_repetition as rpt where rpt.eventid = $eventid order by rpt.rp_id asc");
			$currentreprows = $db->loadObjectList("rp_id");
			$rpids          = array_merge(array_keys($currentreprows), array_keys($deletedexceptions));
			sort($rpids);

			$currentrepetitions = array();
			$rindex             = 0;
			foreach ($rpids as $rid)
			{
				if (!array_key_exists($rid, $currentreprows))
				{
					$rindex += 1;
					continue;
				}
				else
					$currentrepetitions[$rindex] = $currentreprows[$rid];
				$rindex += 1;
			}

			if (count($currentrepetitions) > 0)
			{
				if (count($generatedrepetitions) > 0)
				{

					// The repetitions should be in the same sequence
					$countcurrent   = count($currentrepetitions);
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
									$db->execute();
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
										$db->execute();
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
						$gplus        = 0;
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
								$db->execute();
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
							$db->execute();
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

    public function dbfix()
    {
        include_once JPATH_COMPONENT_ADMINISTRATOR . "/install.php";
        $installer = new com_jeventsInstallerScript();
        $installer->update(false);
    }

    public function checkcollations()
    {
        $force = false;
        $app    = Factory::getApplication();
        $db = Factory::getDbo();

        if (!JEVHelper::isAdminUser())
        {
            return;
        }

        // find collation for com_content
        $db->setQuery("SHOW TABLE STATUS WHERE Name = '" . $db->getPrefix() . "content'");
        $contentTable = $db->loadObject();
        $collation = "";
        if ($contentTable)
        {
            $collation =$contentTable->Collation;
        }
        if (empty($collation))
        {
            return;
        }

        $db->setQuery("SHOW TABLE STATUS LIKE '" . $db->getPrefix() . "jev%'");
        $tables = $db->loadObjectList('Name');

        foreach ($tables as $tablename => $table)
        {
            if (
                strpos($tablename, $db->getPrefix() . "jev_customfields") === 0
                || strpos($tablename, $db->getPrefix() . "jev_tags") === 0
                || strpos($tablename, $db->getPrefix() . "jevents_filtermap") === 0
                || strpos($tablename, $db->getPrefix() . "jev_invitelist") === 0
                || strpos($tablename, $db->getPrefix() . "jev_notifications") === 0
            )
            {
                unset($tables[$tablename]);
            }
        }

        $fixtables = false;
        foreach ($tables as $tablename => $table)
        {
            /*
            $db->setQuery("SHOW INDEX FROM $tablename");
            $indexdata = $db->loadObjectList();
            foreach ($indexdata as $index)
            {
                if (isset($index->Sub_part) && intval($index->Sub_part) > 200)
                {
                    $app->enqueueMessage("The index for column '" . $index->Column_name . "' on table '$tablename' May be too long - please report this to the developers" ,   "warning"      );
                }
            }
            */
            if ($force || $table->Collation != $collation)
            {
                $fixtables = true;
               //  $app->enqueueMessage("Table  $tablename collation needs updating" ,   "warning"      );
                break;
            }
/*
            $db->setQuery("SHOW FULL COLUMNS FROM $tablename");
            $columndata = $db->loadObjectList('Field');
            foreach ($columndata as $columnname => $columndata)
            {
                if ($columndata->Collation && ($force || $columndata->Collation != $collation))
                {
                    $app->enqueueMessage("Table  $tablename and column $columnname has a collation that needs updating" ,   "warning"      );
                    $fixtables = true;
                }
            }
*/
        }
        if ($fixtables)
        {
            $app->enqueueMessage(Text::_("COM_JEVENTS_COLLATIONS_NEED_UPDATING" ) ,   "warning"      );
        }

    }

	public function fixcollations()
	{
        $force = 0;

		$app    = Factory::getApplication();
		$input  = $app->input;

		if (!JEVHelper::isAdminUser())
		{
			$app->enqueueMessage( "Not Authorised - must be admin", 'warning');
			$app->redirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel");
			return;
		}

		$db = Factory::getDbo();

        // find collation for com_content
        $db->setQuery("SHOW TABLE STATUS WHERE Name = '" . $db->getPrefix() . "content'");
        $contentTable = $db->loadObject();
        $collation = "";
        if ($contentTable)
        {
            $collation =$contentTable->Collation;
        }
        if (empty($collation))
        {
            return;
        }

		$db->setQuery("SHOW TABLE STATUS LIKE '" . $db->getPrefix() . "jev%'");
		$tables = $db->loadObjectList('Name');

        foreach ($tables as $tablename => $table)
        {
            if (
                strpos($tablename, $db->getPrefix() . "jev_customfields") === 0
                || strpos($tablename, $db->getPrefix() . "jev_tags") === 0
                || strpos($tablename, $db->getPrefix() . "jevents_filtermap") === 0
                || strpos($tablename, $db->getPrefix() . "jev_invitelist") === 0
                || strpos($tablename, $db->getPrefix() . "jev_notifications") === 0
            )
            {
                unset($tables[$tablename]);
            }
        }

		if ($input->getInt("ft", 0))
		{
			foreach ($tables as $tablename => $table)
			{
				if ($force || $table->Collation != $collation)
				{
                    $db->setQuery("ALTER TABLE $tablename convert to character set utf8mb4 collate $collation");
                    try
                    {
                        $db->execute();
                    }
                    catch (Throwable $e)
                    {
                        $app->enqueueMessage($tablename . " could not be converted : " . $e->getMessage() ,   "error"      );
                        $fixtables = true;
                    }
				}
            }

            $db->setQuery("SHOW TABLE STATUS LIKE '" . $db->getPrefix() . "jev%'");
            $tables = $db->loadObjectList('Name');
		}

		$fixtables = false;
		foreach ($tables as $tablename => $table)
		{
			if ($force || $table->Collation != $collation)
			{
                $app->enqueueMessage( "Table $tablename still has collation " . $table->Collation . " it should probably be " . $collation , "warning");
				$fixtables = true;
			}
/*
			$db->setQuery("SHOW FULL COLUMNS FROM $tablename");
			$columndata = $db->loadObjectList('Field');

			foreach ($columndata as $columnname => $columndata)
			{
				if ($columndata->Collation && ($force || $columndata->Collation != $collation))
				{
                    $db->setQuery("ALTER TABLE $tablename convert to character set utf8mb4 collate $collation");
                    try
                    {
                        $db->execute();
                    }
                    catch (Throwable $e)
                    {
                        $app->enqueueMessage("Column $columnname could not be converted : " . $e->getMessage() ,   "error"      );
                        $fixtables = true;
                    }
				}
			}
*/
		}
        if (!$fixtables)
        {
            $app->enqueueMessage(Text::_("COM_JEVENTS_COLLATIONS_UPDATED_SUCCESSFULLY" ) ,   "info"      );
        }

        $app->redirect("index.php?option=" . JEV_COM_COMPONENT . "&task=cpanel.cpanel");

	}

	private function checkLanguagePackages()
	{

		$languages = Language::getKnownLanguages();

		foreach ($languages as $language)
		{
			$oldPackage = false;
			if (!in_array($language['tag'], array("en-GB")))
			{
				if (is_file(JPATH_SITE . "/language/" . $language['tag'] . "/" . $language['tag'] . ".com_jevents.ini") || is_file(JPATH_ADMINISTRATOR . "/language/" . $language['tag'] . "/" . $language['tag'] . ".com_jevents.ini"))
				{
					$oldPackage = true;

					$db = Factory::getDbo();
					// Add one category by default if none exist already
					$sql = "SELECT element from #__extensions WHERE type = 'file'";
					$db->setQuery($sql);
					$elements = $db->loadObjectList();
					foreach ($elements as $element)
					{
						if ($element->element === $language['tag'] . "_JEvents")
						{
							$oldPackage = false;
						}
					}
				}

				if ($oldPackage)
				{
					if (Text::_("JEV_UPDATE_LANGUAGE_PACKAGE") == "JEV_UPDATE_LANGUAGE_PACKAGE")
					{
						$updateLanguagePackMessage = Text::sprintf('Your JEvents language package for %s is not the latest official release from JEvents. Please go to <a href="http://www.jevents.net/translations">JEvents site</a> and get the latest version to enable live update system for JEvents languages.', $language['name']);
					}
					else
					{
						$updateLanguagePackMessage = Text::sprintf('JEV_UPDATE_LANGUAGE_PACKAGE', $language['name']);
					}
					Factory::getApplication()->enqueueMessage($updateLanguagePackMessage, 'notice');
				}
			}
		}
	}
}
