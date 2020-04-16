<?php

/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3543 2012-04-20 08:17:42Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Feed\FeedFactory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\String\StringHelper;

/**
 * HTML View class for the component
 *
 * @static
 */
class AdminCpanelViewCpanel extends JEventsAbstractView
{

	/**
	 * Control Panel display function
	 *
	 * @param template $tpl
	 */
	function cpanel($tpl = null)
	{
		jimport('joomla.html.pane');

		$document = Factory::getDocument();
		$document->setTitle(Text::_('JEVENTS_DASHBOARD'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('JEVENTS_DASHBOARD'), 'jevents');

		JEventsHelper::addSubmenu();



		if (GSLMSIE10)
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		$this->checkForAddons();

		$this->setUpdateUrls();

		$this->cleanupUpdateUrls();


		$this->dataModel  = new JEventsDataModel("JEventsAdminDBModel");

		$counts = $this->dataModel->queryModel->getEventCounts();
		$this->totalEvents = $counts[0];
		$this->pastEvents = $counts[2];
		$this->futureEvents = $counts[1];

		$count = $this->dataModel->queryModel->getUnpublishedEventCounts();
		$this->unpublishedEvents = $count;

		$counts = $this->dataModel->queryModel->getNewEventCounts();
		$this->newEvents = $counts[1];
		$this->newThisMonth = $counts[2];

		$counts = $this->dataModel->queryModel->getUpcomingEventAttendeesCounts();
		$this->upcomingAttendees = $counts[1];
		$this->upcomingAttendeesThisMonth = $counts[2];

		$data = $this->dataModel->queryModel->getEventCountsByCategory(8);
		$this->eventsByCat = array();
		$this->eventsByCatCounts = array();

		foreach ($data as $datapoint)
		{
			$this->eventsByCat[] = $datapoint->title;
			$this->eventsByCatCounts[] = $datapoint->count;
			$params = @json_decode($datapoint->params);
			if (isset($params->catcolour) && !empty($params->catcolour))
			{
				$this->eventsByCatColours[] = $params->catcolour;
			}
			else
			{
				// otherwise a random colour
				$this->eventsByCatColours[] = '#'.str_pad(dechex(mt_rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);
			}
		}

		$data = $this->dataModel->queryModel->getEventCountsByDay();

		$this->eventCountsByDay = array(0,0,0,0,0,0,0);

		foreach ($data as $datapoint)
		{
			$this->eventCountsByDay[$datapoint->weekday - 1] = $datapoint->count;
		}

		$data = $this->dataModel->queryModel->getEventCountsByDay();

		$this->eventCountsByDay = array(0,0,0,0,0,0,0);

		foreach ($data as $datapoint)
		{
			$this->eventCountsByDay[$datapoint->weekday - 1] = $datapoint->count;
		}


		$this->attendeeCountsByEvent = $this->dataModel->queryModel->getUpcomingEventAttendees();

		$data = $this->dataModel->queryModel->getEventCountsByWeek();

		$this->eventCountByWeek = array();
		$this->eventCountByWeekLabels = array();
		foreach ($data as $datapoint)
		{
			$this->eventCountByWeek[] = $datapoint->count;
			$this->eventCountByWeekLabels[] = $datapoint->weekstart;
		}

	}

	protected function checkForAddons()
	{

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("clubcode", "") && Joomla\String\StringHelper::strlen($params->get("clubcode", "")) > 10)
		{
			return;
		}

		$db = Factory::getDbo();
		// find list of installed addons
		$installed = 'element="com_jevlocations"  OR element="com_jeventstags"  OR element="com_jevpeople"  OR element="com_rsvppro" ';
		$installed .= ' OR element="extplus"  OR element="ruthin"  OR element="iconic"  OR element="flatplus"   OR element="smartphone" OR element="float"';
		// extend this list !!!
		$installed .= " OR element in ('agendaminutes','jevcustomfields','jevfiles','jevhiddendetail','jevlocations','jevmetatags','jevnotify','jevpeople','jevrsvppro','jevrsvp','jevtags','jevtimelimit','jevusers','jevvalidgroups') ";
		$sql       = 'SELECT element,extension_id FROM #__extensions  where  (
		' . $installed . '
		)';
		$db->setQuery($sql);
		$installed = $db->loadObjectList();

		if (count($installed))
		{
			Factory::getApplication()->enqueueMessage(Text::_("JEV_SET_UPDATER_CODE") . "<br/><br/>" . Text::_("JEV_JOOMLA_UPDATE_CLUBCODE_INFO"), "warning");

			return;
		}
	}

	function setUpdateUrls()
	{

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$updates = array(
			array("element" => "pkg_jevents", "name" => "com_jevents", "type" => "package"),
			array("element" => "pkg_jevlocations", "name" => "com_jevlocations", "type" => "package"),
			array("element" => "pkg_jevpeople", "name" => "com_jevpeople", "type" => "package"),
			array("element" => "pkg_rsvppro", "name" => "com_rsvppro", "type" => "package"),
			array("element" => "pkg_jeventstags", "name" => "com_jeventstags", "type" => "package"),

			// Silver - AnonUsers
			array("element" => "jevanonuser", "name" => "jevanonuser", "folder" => "jevents", "type" => "plugin"),
			// Silver - AutoTweet
			array("element" => "jevsendfb", "name" => "jevsendfb", "folder" => "jevents", "type" => "plugin"),
			array("element" => "autotweetjevents", "name" => "autotweetjevents", "folder" => "system", "type" => "plugin"),
			// Silver - MatchingEvents
			array("element" => "jevmatchingevents", "name" => "jevmatchingevents", "folder" => "jevents", "type" => "plugin"),
			// Silver - StandardImage
			array("element" => "jevfiles", "name" => "jevfiles", "folder" => "jevents", "type" => "plugin"),
			// Silver - agendaminutes
			array("element" => "agendaminutes", "name" => "agendaminutes", "folder" => "jevents", "type" => "plugin"),
			array("element" => "jevent_embed", "name" => "jevent_embed", "folder" => "content", "type" => "plugin"),
			// Silver - authorisedusers
			array("element" => "jevuser", "name" => "jevuser", "folder" => "user", "type" => "plugin"),
			// Silver - calendar
			array("element" => "jevcalendar", "name" => "jevcalendar", "folder" => "jevents", "type" => "plugin"),
			// Silver - catcal
			array("element" => "jevcatcal", "name" => "jevcatcal", "folder" => "jevents", "type" => "plugin"),
			// Silver - cck
			array("element" => "jevcck", "name" => "jevcck", "folder" => "jevents", "type" => "plugin"),
			array("element" => "k2embedded", "name" => "k2embedded", "folder" => "k2", "type" => "plugin"),
			// Silver - creator
			array("element" => "jevcreator", "name" => "jevcreator", "folder" => "content", "type" => "plugin"),
			// Silver - customfields
			array("element" => "jevcustomfields", "name" => "jevcustomfields", "folder" => "jevents", "type" => "plugin"),
			// Silver - Dynamic legend
			array("element" => "mod_jevents_dynamiclegend", "name" => "mod_jevents_dynamiclegend", "type" => "module"),
			// Silver - Calendar Plus
			array("element" => "mod_jevents_calendarplus", "name" => "mod_jevents_calendarplus", "type" => "module"),
			// Silver - Slideshow Module
			array("element" => "mod_jevents_slideshow", "name" => "mod_jevents_slideshow", "type" => "module"),
			// Silver - facebook
			array("element" => "jevfacebook", "name" => "jevfacebook", "folder" => "jevents", "type" => "plugin"),
			// Silver - facebook social
			array("element" => "jevfacebooksocial", "name" => "jevfacebooksocial", "folder" => "jevents", "type" => "plugin"),
			// Silver - featured
			array("element" => "jevfeatured", "name" => "jevfeatured", "folder" => "jevents", "type" => "plugin"),
			// Silver - hiddendetail
			array("element" => "jevhiddendetail", "name" => "jevhiddendetail", "folder" => "jevents", "type" => "plugin"),
			// Silver - jomsocial -  TODO
			array("element" => "jevjsstream", "name" => "jevjsstream", "folder" => "jevents", "type" => "plugin"),
			array("element" => "jevents", "name" => "jevents", "folder" => "community", "type" => "plugin"),
			// Silver - layouts
			array("element" => "extplus", "name" => "extplus", "type" => "file"),
			array("element" => "ruthin", "name" => "ruthin", "type" => "file"),
			array("element" => "flatplus", "name" => "flatplus", "type" => "file"),
			array("element" => "iconic", "name" => "iconic", "type" => "file"),
			array("element" => "map", "name" => "map", "type" => "file"),
			array("element" => "smartphone", "name" => "smartphone", "type" => "file"),
			array("element" => "zim", "name" => "zim", "type" => "file"),
			array("element" => "float", "name" => "float", "type" => "file"),

			// These have been renamed in the XML file - need to be careful doing that!!!
			array("element" => "JEventsExtplusLayout", "name" => "extplus", "type" => "file"),
			array("element" => "JEventsRuthinLayout", "name" => "ruthin", "type" => "file"),
			array("element" => "JEventsFlatplusLayout", "name" => "flatplus", "type" => "file"),
			array("element" => "JEventsIconicLayout", "name" => "iconic", "type" => "file"),
			array("element" => "JEventsMapLayout", "name" => "map", "type" => "file"),
			array("element" => "JEventsSmartphoneLayout", "name" => "smartphone", "type" => "file"),
			array("element" => "JEventsZimLayout", "name" => "zim", "type" => "file"),
			array("element" => "JEventsFloatLayout", "name" => "float", "type" => "file"),

			// Silver - Jevents Categories
			array("element" => "mod_jevents_categories", "name" => "mod_jevents_categories", "type" => "module"),
			// Silver - Newsletters - some TODO
			array("element" => "tagjevents_jevents", "name" => "tagjevents_jevents", "folder" => "acymailing", "type" => "plugin"),
			// Silver - Nnotifications
			array("element" => "jevnotify", "name" => "jevnotify", "folder" => "jevents", "type" => "plugin"),
			array("element" => "mod_jevents_notify", "name" => "mod_jevents_notify", "type" => "module"),
			// Silver - simpleattend
			array("element" => "jevrsvp", "name" => "jevrsvp", "folder" => "jevents", "type" => "plugin"),
			// Silver - tabbed modules
			array("element" => "mod_tabbedmodules", "name" => "mod_tabbedmodules", "type" => "module"),
			// Silver - time Limit
			array("element" => "jevtimelimit", "name" => "jevtimelimit", "folder" => "jevents", "type" => "plugin"),
			// Silver - User Events
			array("element" => "jevusers", "name" => "jevusers", "folder" => "jevents", "type" => "plugin"),
			// Silver - Week Days
			array("element" => "jevweekdays", "name" => "jevweekdays", "folder" => "jevents", "type" => "plugin"),

			// GOLD addons - PaidSubs - TODO check Virtuemart for Joomla 3.0 is available
			array("element" => "jevpaidsubs", "name" => "jevpaidsubs", "folder" => "jevents", "type" => "plugin"),
			array("element" => "mod_jevents_paidsubs", "name" => "mod_jevents_paidsubs", "type" => "module"),

			// Translations - TODO club translations.  Normal JEvents translations handled below!

			// Bronze - editor button
			array("element" => "jevents", "name" => "jevents", "folder" => "editors-xtd", "type" => "plugin"),

			// Bronze - Meta tags
			array("element" => "jevmetatags", "name" => "jevmetatags", "folder" => "jevents", "type" => "plugin"),

			// Bronze - Missing Events
			array("element" => "jevmissingevent", "name" => "jevmissingevent", "folder" => "jevents", "type" => "plugin"),

			// Bronze - Popups
			array("element" => "jevpopupdetail", "name" => "jevpopupdetail", "folder" => "jevents", "type" => "plugin"),

			// Bronze - sh404sef - TODO

		);
		// Do the language files for Joomla
		$db = Factory::getDbo();
		$db->setQuery("SELECT * FROM #__extensions where type='file' AND element LIKE '%_JEvents' AND element NOT LIKE '%_JEvents_Addons' and element NOT LIKE '%_JEventsAddons' ");
		$translations = $db->loadObjectList();
		foreach ($translations as $translation)
		{
			if ($translation->name == "")
			{
				$translation->name = "JEvents Translation - " . $translation->element;
			}
			//	array("element"=>"ar-AA_JEvents","name"=>"Arabic translation for JEvents","type"=>"file"),
			$updates[] = array("element" => $translation->element, "name" => $translation->name, "type" => "file");
		}

		$db->setQuery("SELECT * FROM #__extensions where type='file' AND (element LIKE '%_JEvents_Addons' OR element LIKE '%_JEventsAddons') ");
		$translations = $db->loadObjectList();
		foreach ($translations as $translation)
		{
			//	array("element"=>"ar-AA_JEvents","name"=>"Arabic translation for JEvents","type"=>"file"),
			$elem      = str_replace("_Addons", "Addons_", $translation->element);
			$updates[] = array("element" => $elem, "name" => $translation->name, "type" => "file");
		}

		foreach ($updates as $package)
		{
			$this->setUpdateUrlsByPackage($package);
		}
	}

	function setUpdateUrlsByPackage($package)
	{

		$pkg    = $package["element"];
		$com    = $package["name"];
		$folder = isset($package["folder"]) ? $package["folder"] : "";
		$type   = $package["type"];

		$db = Factory::getDbo();

		// Process the package
		$db = Factory::getDbo();
		// Do we already have a record for the update URL for the component - we should remove this in JEvents 3.0!!
		if ($folder == "" && $package['type'] != "file" && $package['type'] != "module" && $package['type'] != "plugin")
		{
			$this->removeComponentUpdate($com);
		}

		static $extensiondata = false;
		if (!$extensiondata)
		{
			$db->setQuery("select *, exn.extension_id as extension_id , exn.type as extension_type, exn.element as extension_element, exn.folder as extension_folder  from #__extensions as exn
	LEFT JOIN #__update_sites_extensions as map on map.extension_id=exn.extension_id
	LEFT JOIN #__update_sites as us on us.update_site_id=map.update_site_id");
			$extensiondata = $db->loadObjectList('extension_id');
		}

		// Now check and setup the package update URL
		/*
		$db->setQuery("select *, exn.extension_id as extension_id , exn.type as extension_type  from #__extensions as exn
LEFT JOIN #__update_sites_extensions as map on map.extension_id=exn.extension_id
LEFT JOIN #__update_sites as us on us.update_site_id=map.update_site_id
where exn.type='$type'
and exn.element='$pkg' and exn.folder='$folder'
");
		$pkgupdate = $db->loadObject();
		*/
		$pkgupdate = false;
		foreach ($extensiondata as $ed)
		{
			if ($ed->extension_type == $type && $ed->extension_element == $pkg && $ed->extension_folder == $folder)
			{
				$pkgupdate = $ed;
			}
		}

		// we have a package and an update record
		if ($pkgupdate && $pkgupdate->update_site_id)
		{
			// Now update package update URL
			$this->setPackageUpdateUrl($pkgupdate);
		}
		// we have a package but not an update record
		else if ($pkgupdate && $pkgupdate->extension_id)
		{
			// Now set package update URL
			$this->setPackageUpdateUrl($pkgupdate);
		}
		else
		{
			// No package installed so fall back to component and set it to update using the package URL :)

			// Do we already have a record for the update URL for the component - we should remove this
			/*
			$db->setQuery("select *, exn.extension_id as extension_id  from #__extensions as exn
	LEFT JOIN #__update_sites_extensions as map on map.extension_id=exn.extension_id
	LEFT JOIN #__update_sites as us on us.update_site_id=map.update_site_id
	where exn.type='component'
	and exn.element='$com'
	");
			$cpupdate = $db->loadObject();
			*/

			$cpupdate = false;
			foreach ($extensiondata as $ed)
			{
				if ($ed->extension_type == 'component' && $ed->extension_element == $com)
				{
					$cpupdate = $ed;
				}
			}

			if ($cpupdate && $cpupdate->update_site_id)
			{
				$db->setQuery("DELETE FROM #__update_sites where update_site_id=" . $cpupdate->update_site_id);
				$db->execute();
				$db->setQuery("DELETE FROM #__update_sites_extensions where update_site_id=" . $cpupdate->update_site_id . " AND extension_id=" . $cpupdate->extension_id);
				$db->execute();
			}

			// Now set package update URL for the component as opposed to the package ;)
			if ($cpupdate && $cpupdate->extension_id)
			{
				$this->setPackageUpdateUrl($cpupdate);
			}
		}

	}

	private
	function removeComponentUpdate($com)
	{

		$db      = Factory::getDbo();
		$version = JEventsVersion::getInstance();
		$release = $version->get("RELEASE");

		// Do we already have a record for the update URL for the component - we should remove this in JEvents 3.0!!
		static $comdata = false;
		if (!$comdata)
		{
			$db->setQuery("select * , exn.element as element from #__extensions as exn
			LEFT JOIN #__update_sites_extensions as map on map.extension_id=exn.extension_id
			LEFT JOIN #__update_sites as us on us.update_site_id=map.update_site_id
			where exn.type='component'
			");
			$comdata = $db->loadObjectList('element');
		}
		$cpupdate = isset($comdata[$com]) ? $comdata[$com] : false;
		if ($cpupdate && $cpupdate->update_site_id)
		{
			$db->setQuery("DELETE FROM #__update_sites where update_site_id=" . $cpupdate->update_site_id);
			$db->execute();
			$db->setQuery("DELETE FROM #__update_sites_extensions where update_site_id=" . $cpupdate->update_site_id . " AND extension_id=" . $cpupdate->extension_id);
			$db->execute();
		}

	}

	private
	function setPackageUpdateUrl($pkgupdate)
	{

		$db = Factory::getDbo();

		$sitedomain = rtrim(str_replace(array('https://', 'http://'), "", Uri::root()), '/');

		$params   = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$clubcode = $params->get("clubcode", "");
		$filter   = new InputFilter();
		$clubcode = $filter->clean($clubcode, "CMD");
		//$clubcode = $filter->clean($clubcode, "BASE64")."-".$sitedomain;
		//$clubcode = base64_encode($clubcode);
		$clubcode = $clubcode . "-" . base64_encode($sitedomain);

		$version = JEventsVersion::getInstance();
		$version = $version->get('RELEASE');
		$version = str_replace(" ", "", $version);
		//$domain = "http://ubu.j33jq.com";
		$domain = "https://www.jevents.net";

		//$extension  = Table::getInstance("Extension");
		//$extension->load($pkgupdate->extension_id);

		// Save DB queries!
		static $extensiondata = false;
		if (!$extensiondata)
		{
			$db->setQuery("Select * from #__extensions");
			$extensiondata = $db->loadObjectList('extension_id');
		}

		$extension = isset($extensiondata[$pkgupdate->extension_id]) ? $extensiondata[$pkgupdate->extension_id] : null;

		// Packages are installed with client_id = 0 which stops the update from taking place to we update the extension to client_id=1
		/*
		if ($pkgupdate->client_id==0 && $pkgupdate->extension_type=="package"){
			$db->setQuery("UPDATE #__extensions SET client_id=1 WHERE extension_id = $pkgupdate->extension_id");
			$db->execute();
			echo $db->getErrorMsg();
		}
		 */

		// We already have an update site
		if ($pkgupdate->update_site_id)
		{
			$extensionname = str_replace(" ", "_", $extension->element);
			if ($extension->folder)
			{
				$extensionname = "plg_" . $extension->folder . "_" . $extensionname;
			}
			/*
			 // set the JEvents Version number in the update URL
			if (isset($extension->manifest_cache)){
				$extensionmanifest = json_decode($extension->manifest_cache);
				if (isset($extensionmanifest->version)) {
					$version = $extensionmanifest->version;
				}
			}
			*/
			if ($pkgupdate->name != ucwords($extension->name) || $pkgupdate->location != "$domain/updates/$clubcode/$extensionname-update-$version.xml" || $pkgupdate->enabled != 1)
			{
				$db->setQuery("UPDATE #__update_sites set name=" . $db->quote(ucwords($extension->name)) . ", location=" . $db->quote("$domain/updates/$clubcode/$extensionname-update-$version.xml") . ", enabled = 1 WHERE update_site_id=" . $pkgupdate->update_site_id);

				try {
					$db->execute();
				} catch (Exception $e) {
					echo $e;
				}
			}
		}
		else
		{
			$extensionname = str_replace(" ", "_", $extension->element);
			if ($extension->folder)
			{
				$extensionname = "plg_" . $extension->folder . "_" . $extensionname;
			}

			// Check data integrity and clean up if necessary
			$db->setQuery("SELECT count(update_site_id) FROM #__update_sites_extensions WHERE extension_id = $pkgupdate->extension_id");
			if ($db->loadResult() > 0)
			{

				$db->setQuery("DELETE FROM #__update_sites  WHERE update_site_id in (SELECT update_site_id FROM #__update_sites_extensions WHERE extension_id = $pkgupdate->extension_id )");
				$db->execute();

				$db->setQuery("DELETE FROM #__update_sites_extensions  WHERE extension_id = $pkgupdate->extension_id ");
				$db->execute();
			}

			$db->setQuery("INSERT INTO #__update_sites (name, type, location, enabled, last_check_timestamp) VALUES (" . $db->quote(ucwords($extension->name)) . ",'extension'," . $db->quote("$domain/updates/$clubcode/$extensionname-update-$version.xml") . ",'1','0')");

			try {
				$db->execute();
			} catch (Exception $e) {
				echo $e;
			}

			$id = 0;

			try {
				$id = $db->insertid();
			} catch (Exception $e) {
				echo $e;
			}

			$db->setQuery("REPLACE INTO #__update_sites_extensions (update_site_id, extension_id) VALUES ($id, $pkgupdate->extension_id)");

			try {
				$db->execute();
			} catch (Exception $e) {
				echo $e;
			}
		}

	}

	private function cleanupUpdateUrls()
	{

		$version = JEventsVersion::getInstance();
		$version = $version->get('RELEASE');
		$version = str_replace(" ", "", $version);

		$db = Factory::getDbo();
		$db->setQuery("SELECT * FROM #__update_sites where location like '%jevents.net%' and location not like '%$version%'");
		$strays = $db->loadObjectList('update_site_id');
		if (count($strays) > 0)
		{
			$db->setQuery("DELETE  FROM #__update_sites_extensions where update_site_id IN (" . implode(", ", array_keys($strays)) . ")");
			$db->execute();
			$db->setQuery("DELETE FROM #__update_sites where location like '%jevents.net%' and location not like '%$version%'");
			$db->execute();
		}

		// remove duplicate entries created by Joomla installer that assumes the updateserver will not change
		//$db->setQuery('SELECT * FROM #__update_sites where location like "%www.jevents.net%/%/'.$package['element'].'-update-%.xml" ');
		$db = Factory::getDbo();
		$db->setQuery('SELECT * FROM #__update_sites where location like "%www.jevents.net%/%/%-update-%.xml" order by update_site_id asc');
		$cleanupRows = $db->loadObjectList('update_site_id');
		if (count($cleanupRows) > 1)
		{
			$strays    = array();
			$processed = array();
			foreach ($cleanupRows as $update_site_id => $site)
			{
				$pgk = substr($site->location, strrpos($site->location, "/") + 1);
				$pgk = substr($pgk, 0, strrpos($pgk, "-update-"));
				if (in_array($pgk, $processed))
				{
					$strays[$update_site_id] = $pgk;
				}
				$processed[] = $pgk;
			}
			if (count($strays) > 0)
			{
				$db->setQuery("DELETE  FROM #__update_sites_extensions where update_site_id IN (" . implode(", ", array_keys($strays)) . ")");
				$db->execute();
				$db->setQuery("DELETE FROM #__update_sites where location like '%jevents.net%' and update_site_id IN (" . implode(", ", array_keys($strays)) . ")");
				$db->execute();
			}
		}

	}

	/**
	 * render News feed from JEvents portal
	 */
	function renderJEventsNews()
	{

		$cache = Factory::getCache(JEV_COM_COMPONENT, 'view');
		$cache->setLifeTime(86400);
		// In Joomla 1.7 caching of feeds doesn't work!
		$cache->setCaching(true);

		$app = Factory::getApplication();
		if (!isset($app->registeredurlparams))
		{
			$app->registeredurlparams = new stdClass();
		}

		$cache->get($this, 'renderJEventsNewsCached');

	}

	function renderJEventsNewsCached()
	{

		$output = '';

		//  get RSS parsed object

		try
		{
			$feed   = new FeedFactory;
			$rssDoc = $feed->getFeed('https://www.jevents.net/jevnews?format=feed&type=rss');
		}
		catch (InvalidArgumentException $e)
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}
		catch (RunTimeException $e)
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}
		catch (LogicException $e)
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}

		if (empty($rssDoc))
		{
			return Text::_('MOD_FEED_ERR_FEED_NOT_RETRIEVED');
		}
		else
		{
// channel header and link
			$title = str_replace(" ", "_", $rssDoc->title);
			$link  = $rssDoc->uri;

			$output = '<table class="adminlist   table table-striped">';
			$output .= '<tr><th><a href="' . $link . '" target="_blank">' . Text::_($title) . '</th></tr>';

			$items    = $rssDoc;
			$numItems = 3;
			if ($numItems == 0)
			{
				$output .= '<tr><th>' . Text::_('JEV_No_news') . '</th></tr>';
			}
			else
			{
				$k = 0;
				for ($j = 0; $j < $numItems; $j++)
				{
					if (!isset($items[$j]))
					{
						break;
					}
					$item   = @$items[$j];
					$output .= '<tr><td class="row' . $k . '">';
					$output .= '<a href="' . $item->uri . '" target="_blank">' . $item->title . '</a>';
					if ($item->content)
					{
						$description = $this->limitText($item->content, 50);
						$output      .= '<br />' . $description;
					}
					$output .= '</td></tr>';
					$k      = 1 - $k;
				}
			}

			$output .= '</table>';
		}
		// do not return the output because of differences between J15 and J17
		echo $output;

	}

	function limitText($text, $wordcount)
	{

		if (!$wordcount)
		{
			return $text;
		}

		$texts = explode(' ', $text);
		$count = count($texts);

		if ($count > $wordcount)
		{
			$text = '';
			for ($i = 0; $i < $wordcount; $i++)
			{
				$text .= ' ' . $texts[$i];
			}
			$text .= '...';
		}

		return $text;

	}

	function renderJEventsNewsCached25()
	{

		$output = '';

		//  get RSS parsed object
		$options               = array();
		$options['rssUrl']     = 'https://www.jevents.net/jevnews?format=feed&type=rss';
		$options['cache_time'] = 0;

		error_reporting(0);
		ini_set('display_errors', 0);

		$rssDoc = Factory::getFeedParser($options['rssUrl'], $options['cache_time']);

		//$rssDoc = Factory::getXMLparser('RSS', $options);

		if ($rssDoc == false)
		{
			$output = Text::_('Error: Feed not retrieved');
		}
		else
		{
// channel header and link
			$title = str_replace(" ", "_", $rssDoc->get_title());
			$link  = $rssDoc->get_link();

			$output = '<table class="adminlist   table table-striped">';
			$output .= '<tr><th><a href="' . $link . '" target="_blank">' . Text::_($title) . '</th></tr>';

			$items    = array_slice($rssDoc->get_items(), 0, 3);
			$numItems = count($items);
			if ($numItems == 0)
			{
				$output .= '<tr><th>' . Text::_('JEV_No_news') . '</th></tr>';
			}
			else
			{
				$k = 0;
				for ($j = 0; $j < $numItems; $j++)
				{
					$item   = $items[$j];
					$output .= '<tr><td class="row' . $k . '">';
					$output .= '<a href="' . $item->get_link() . '" target="_blank">' . $item->get_title() . '</a>';
					if ($item->get_description())
					{
						$description = $this->limitText($item->get_description(), 50);
						$output      .= '<br />' . $description;
					}
					$output .= '</td></tr>';
					$k      = 1 - $k;
				}
			}

			$output .= '</table>';
		}
		// do not return the output because of differences between J15 and J17
		echo $output;

	}

	function renderVersionStatusReport(& $needsupdate)
	{

		jimport("joomla.filesystem.folder");
		if (JEVHelper::isAdminUser())
		{

//  get RSS parsed object
			$options    = array();
			$rssUrl     = 'https://www.jevents.net/versions30.xml';
			$cache_time = 86400;

			error_reporting(0);
			ini_set('display_errors', 0);

			jimport('simplepie.simplepie');

			// this caching doesn't work!!!
			//$cache = Factory::getCache('feed_parser', 'callback');
			//$cache->setLifeTime($cache_time);
			//$cache->setCaching(true);

			$rssDoc = new SimplePie(null, null, 0);
			$rssDoc->enable_cache(false);
			$rssDoc->set_feed_url($rssUrl);
			$rssDoc->force_feed(true);
			$rssDoc->set_item_limit(999);

			//$results = $cache->get(array($rssDoc, 'init'), null, false, false);
			$results = $rssDoc->init();

			if ($results == false)
			{
				return false;
			}
			else
			{
				$this->generateVersionsFile($rssDoc);

				$rows  = array();
				$items = $rssDoc->get_items();

				foreach ($items as $item)
				{
					$apps = array();
					if (strpos($item->get_title(), "layout_") === 0)
					{
						$layout = str_replace("layout_", "", $item->get_title());
						if (Folder::exists(JEV_PATH . "views/$layout"))
						{
// club layouts
							$xmlfiles1 = Folder::files(JEV_PATH . "views/$layout", "manifest\.xml", true, true);
							if ($xmlfiles1 && count($xmlfiles1) > 0)
							{
								foreach ($xmlfiles1 as $manifest)
								{
									if (realpath($manifest) != $manifest)
										continue;
									if (!$manifestdata = $this->getValidManifestFile($manifest))
										continue;

									$app                                            = new stdClass();
									$app->name                                      = $manifestdata["name"];
									$app->version                                   = $manifestdata["version"];
									$apps["layout_" . basename(dirname($manifest))] = $app;
								}
							}
						}
						// package version
						if (Folder::exists(JPATH_ADMINISTRATOR . "/manifests/files"))
						{
// club layouts
							$xmlfiles1 = Folder::files(JPATH_ADMINISTRATOR . "/manifests/files", "$layout\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (realpath($manifest) != $manifest)
									continue;
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app                                            = new stdClass();
								$app->name                                      = $manifestdata["name"];
								$app->version                                   = $manifestdata["version"];
								$apps["layout_" . basename(dirname($manifest))] = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "module_") === 0)
					{
						$module = str_replace("module_", "", $item->get_title());
// modules
						if (Folder::exists(JPATH_SITE . "/modules/$module"))
						{

							$xmlfiles1 = Folder::files(JPATH_SITE . "/modules/$module", "\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (realpath($manifest) != $manifest)
									continue;
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app          = new stdClass();
								$app->name    = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name         = "module_" . str_replace(".xml", "", basename($manifest));
								$apps[$name]  = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "plugin_") === 0)
					{
						$plugin = explode("_", str_replace("plugin_", "", $item->get_title()), 2);
						if (count($plugin) < 2)
							continue;
// plugins
						if ((Folder::exists(JPATH_SITE . "/plugins/" . $plugin[0] . "/" . $plugin[1])))
						{
// plugins
							$xmlfiles1 = Folder::files(JPATH_SITE . "/plugins/" . $plugin[0] . "/" . $plugin[1], "\.xml", true, true);

							foreach ($xmlfiles1 as $manifest)
							{
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app          = new stdClass();
								$app->name    = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name         = str_replace(".xml", "", basename($manifest));
								$name         = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;
								$apps[$name]  = $app;
							}
						}
					}
					else if (strpos($item->get_title(), "component_") === 0)
					{
						$component = str_replace("component_", "", $item->get_title());

						if (Folder::exists(JPATH_ADMINISTRATOR . "/components/" . $component))
						{

// modules
							$xmlfiles1 = Folder::files(JPATH_ADMINISTRATOR . "/components/" . $component, "\.xml", true, true);
							if (!$xmlfiles1)
								continue;
							foreach ($xmlfiles1 as $manifest)
							{
								if (!$manifestdata = $this->getValidManifestFile($manifest))
									continue;

								$app          = new stdClass();
								$app->name    = $manifestdata["name"];
								$app->version = $manifestdata["version"];
								$name         = "component_" . basename(dirname($manifest));
								$apps[$name]  = $app;
							}
						}
					}
					else
					{
						continue;
					}

					foreach ($apps as $appname => $app)
					{
						$iteminfo = json_decode($item->get_description());
						if (version_compare($app->version, $iteminfo->version, "<"))
						{
							$link = $iteminfo->link != "" ? "<a href='" . $iteminfo->link . "' target='_blank'>" . $app->name . "</a>" : $app->name;
							if ($iteminfo->criticalversion != "" && version_compare($app->version, $iteminfo->criticalversion, "<"))
							{
								$rows[] = array($link, $appname, $app->version, $iteminfo->version, "<strong>" . $iteminfo->criticalversion . "</strong>");
							}
							else
							{
								$rows[] = array($link, $appname, $app->version, $iteminfo->version, "");
							}
						}
					}
				}

				if (count($rows) > 0)
				{
					$output = '<table class="versionstatuslist"><tr>';
					$output .= '<th>' . Text::_("JEV_APPNAME") . '</th>';
					$output .= '<th>' . Text::_("JEV_APPCODE") . '</th>';
					$output .= '<th>' . Text::_("JEV_CURRENTVERSION") . '</th>';
					$output .= '<th>' . Text::_("JEV_LATESTVERSION") . '</th>';
					$output .= '<th>' . Text::_("JEV_CRITICALVERSION") . '</th>';
					$output .= '</tr>';
					$k      = 0;
					foreach ($rows as $row)
					{
						$output .= '<tr class="row' . $k . '"><td>';
						$output .= implode("</td><td>", $row);
						$output .= '</td></tr>';
						$k      = ($k + 1) % 2;
					}

					$output      .= '</table>';
					$needsupdate = true;

					return $output;
				}
			}
		}

		return false;

	}

	private
	function generateVersionsFile($rssDoc)
	{

		$input    = Factory::getApplication()->input;


		if ($input->getInt("versions", 0) == 0)
		{
			return;
		}
		jimport("joomla.filesystem.folder");

		$apps = array();

		// Club layouts
		$xmlfiles1 = Folder::files(JEV_PATH . "views", "manifest\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                                            = new stdClass();
			$app->name                                      = $manifestdata["name"];
			$app->version                                   = $manifestdata["version"];
			$apps["layout_" . basename(dirname($manifest))] = $app;
		}

		$xmlfiles1 = Folder::files(JPATH_MANIFESTS . "/files", "\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                                                            = new stdClass();
			$app->name                                                      = $manifestdata["name"];
			$app->version                                                   = $manifestdata["version"];
			$apps["layout_" . str_replace(".xml", "", basename($manifest))] = $app;
		}

// plugins
		if (Folder::exists(JPATH_SITE . "/plugins"))
		{
			$xmlfiles2 = Folder::files(JPATH_SITE . "/plugins", "\.xml", true, true);
		}
		else
		{
			$xmlfiles2 = array();
		}

		foreach ($xmlfiles2 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app          = new stdClass();
			$app->name    = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name         = str_replace(".xml", "", basename($manifest));

			$name = "plugin_" . basename(dirname(dirname($manifest))) . "_" . $name;

			$apps[$name] = $app;
		}

// components (including JEvents
		$xmlfiles3 = Folder::files(JPATH_ADMINISTRATOR . "/components", "manifest\.xml", true, true);
		foreach ($xmlfiles3 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app          = new stdClass();
			$app->name    = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name         = "component_" . basename(dirname($manifest));
			$apps[$name]  = $app;
		}


// modules
		if (Folder::exists(JPATH_SITE . "/modules"))
		{
			$xmlfiles4 = Folder::files(JPATH_SITE . "/modules", "\.xml", true, true);
		}
		else
		{
			$xmlfiles4 = array();
		}
		foreach ($xmlfiles4 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                  = new stdClass();
			$app->name            = $manifestdata["name"];
			$app->version         = $manifestdata["version"];
			$app->criticalversion = "";
			$name                 = "module_" . str_replace(".xml", "", basename($manifest));
			$apps[$name]          = $app;
		}

// setup the XML file for server
		/*
		  $output = '$catmapping = array(' . "\n";
		  foreach ($apps as $appname => $app)
		  {
		  $output .='"' . $appname . '"=> 0,' . "\n";
		  }
		  $output = StringHelper::substr($output, 0, StringHelper::strlen($output) - 2) . ");\n\n";
		 */
		$criticaldata = file_get_contents('http://ubu.jev20j16.com/importantversions.txt');
		$criticaldata = explode("\n", $criticaldata);
		$criticals    = array();
		foreach ($criticaldata as $critical)
		{
			$critical = explode("|", $critical);
			if (count($critical) > 1)
			{
				$criticals[$critical[0]] = $critical[1];
			}
		}
		$catmapping = array(
			"layout_extplus"                   => 3,
			"layout_iconic"                    => 3,
			"layout_ruthin"                    => 3,
			"layout_flatplus"                  => 3,
			"layout_smartphone"                => 3,
			"layout_map"                       => 3,
			"layout_float"                     => 3,
			"plugin_acymailing_tagjevents"     => 41,
			"plugin_community_jevents"         => 7,
			"plugin_content_jevcreator"        => 34,
			"plugin_content_jevent_embed"      => 113,
			"plugin_jevents_agendaminutes"     => 12,
			"plugin_jevents_jevanonuser"       => 25,
			"plugin_jevents_jevcalendar"       => 15,
			"plugin_jevents_jevcatcal"         => 15,
			"plugin_jevents_jevcb"             => 18,
			"plugin_jevents_jevcck"            => 64,
			"plugin_jevents_jevcustomfields"   => 10,
			"plugin_jevents_jevfacebook"       => 46,
			"plugin_jevents_jevfeatured"       => 16,
			"plugin_jevents_jevfiles"          => 24,
			"plugin_jevents_jevhiddendetail"   => 51,
			"plugin_jevents_jevjsstream"       => 7,
			"plugin_jevents_jevjxcoments"      => 19,
			"plugin_jevents_jevlocations"      => 4,
			"plugin_jevents_jevmatchingevents" => 47,
			"plugin_jevents_jevmetatags"       => 58,
			"plugin_jevents_jevmissingevent"   => 56,
			"plugin_jevents_jevnotify"         => 61,
			"plugin_jevents_jevpaidsubs"       => 48,
			"plugin_jevents_jevpeople"         => 13,
			"plugin_jevents_jevpopupdetail"    => 50,
			"plugin_jevents_jevrsvp"           => 14,
			"plugin_jevents_jevrsvppro"        => 62,
			"plugin_jevents_jevsendfb"         => 45,
			"plugin_jevents_jevsessions"       => 21,
			"plugin_jevents_jevtags"           => 9,
			"plugin_jevents_jevtimelimit"      => 17,
			"plugin_jevents_jevusers"          => 8,
			"plugin_jevents_jevweekdays"       => 59,
			"plugin_jnews_jnewsjevents"        => 24,
			"plugin_rsvppro_manual"            => 62,
			"plugin_rsvppro_paypalipn"         => 62,
			"plugin_rsvppro_virtuemart"        => 62,
			//"plugin_search_eventsearch" => 52, // JEvents 2.2
			"plugin_search_eventsearch"        => 71,
			"plugin_search_jevlocsearch"       => 4,
			"plugin_search_jevtagsearch"       => 9,
			"plugin_system_autotweetjevents"   => 45,
			"plugin_user_juser"                => 24,
			"component_com_attend_jevents"     => 21,
			//"component_com_jevents" => 52, // JEvents 2.0
			//"component_com_jevents" => 65, // JEvents 2.1
			"component_com_jevents"            => 71, // JEvents 3.0
			"component_com_jeventstags"        => 9,
			"component_com_jevlocations-old"   => 4,
			"component_com_jevlocations"       => 4,
			"component_com_jevpeople"          => 13,
			"component_com_rsvppro"            => 62,
			"module_mod_jevents_cal"           => 71,
			"module_mod_jevents_categories"    => 76,
			//"module_mod_jevents_filter" => 52,
			//"module_mod_jevents_latest" => 52,
			//"module_mod_jevents_legend" => 52,
			"module_mod_jevents_filter"        => 71,
			"module_mod_jevents_latest"        => 71,
			"module_mod_jevents_legend"        => 71,
			"module_mod_jevents_notify"        => 61,
			"module_mod_jevents_paidsubs"      => 48,
			"module_mod_jevents_switchview"    => 52
		);
		$output     = "";
		foreach ($apps as $appname => $app)
		{
			$row                  = new stdClass();
			$row->version         = $app->version;
			$row->criticalversion = "";
			if (array_key_exists($appname, $criticals))
			{
				$row->criticalversion = $criticals[$appname];
			}
			$row->link = array_key_exists($appname, $catmapping) ? "https://www.jevents.net/downloads/category/" . $catmapping[$appname] : "";
			if ($row->link == "")
				continue;
			$output .= "<item>\n<title>$appname</title>\n<description><![CDATA[" . json_encode($row) . "]]></description>\n</item>\n";
		}
		$output      .= "";
		$needsupdate = true;
		ob_end_clean();
		echo $output;
		die();

	}

	private
	function getValidManifestFile($manifest)
	{

		$filecontent = file_get_contents($manifest);
		if (stripos($filecontent, "jevents.net") === false
			&& stripos($filecontent, "gwesystems.com") === false
			&& stripos($filecontent, "joomlacontenteditor") === false
			&& stripos($filecontent, "virtuemart") === false
			&& stripos($filecontent, "sh404sef") === false
			&& stripos($filecontent, "comprofiler") === false
			&& stripos($filecontent, "community") === false
			&& stripos($filecontent, "TechJoomla") === false
			&& stripos($filecontent, "hikashop") === false
			&& stripos($filecontent, "acymailing") === false)
		{
			return false;
		}
		// for JCE and Virtuemart only check component version number
		if (stripos($filecontent, "joomlacontenteditor") !== false
			|| stripos($filecontent, "virtuemart") !== false
			|| stripos($filecontent, "sh404sef") !== false
			|| strpos($filecontent, "JCE") !== false
			|| strpos($filecontent, "Community") !== false
			|| strpos($filecontent, "Comprofiler") !== false
			|| strpos($filecontent, "TechJoomla") !== false
			|| strpos($filecontent, "hikashop") !== false)
		{
			if (strpos($filecontent, "type='component'") === false && strpos($filecontent, 'type="component"') === false)
			{
				return false;
			}
		}

		$manifestdata = Installer::parseXMLInstallFile($manifest);
		if (!$manifestdata)
			return false;
		if (strpos($manifestdata["authorUrl"], "jevents") === false
			&& strpos($manifestdata["authorUrl"], "gwesystems") === false
			&& strpos($manifestdata["authorUrl"], "joomlacontenteditor") === false
			&& strpos($manifestdata["authorUrl"], "virtuemart") === false
			&& strpos($manifestdata['name'], "sh404SEF") === false
			&& strpos($manifestdata['name'], "Community") === false
			&& strpos($manifestdata['name'], "comprofiler") === false
			&& strpos($manifestdata['author'], "TechJoomla") === false
			&& strpos($manifestdata['name'], "HikaShop") === false
			&& strpos($manifestdata['name'], "AcyMailing") === false
		)
		{
			return false;
		}

		return $manifestdata;

	}

	// remove stray entries!

	public
	function renderVersionsForClipboard()
	{

		if (!JEVHelper::isAdminUser())
		{
			return;
		}

		jimport("joomla.filesystem.folder");
		$apps = array();

		// Joomla
		$app              = new stdClass();
		$app->name        = "Joomla";
		$version          = new Version();
		$app->version     = $version->getShortVersion();
		$apps[$app->name] = $app;

		// TODO :  Can we do this from the database???
		// components (including JEvents)
		$xmlfiles3 = array_merge(
			Folder::files(JPATH_ADMINISTRATOR . "/components", "manifest\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "sh404sef\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "virtuemart\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "jce\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "comprofiler\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "community\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "jmailalerts\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "hikashop\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "hikashop_j3\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "jev_latestevents\.xml", true, true),
			Folder::files(JPATH_ADMINISTRATOR . "/components", "acymailing\.xml", true, true));
		foreach ($xmlfiles3 as $manifest)
		{
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app          = new stdClass();
			$app->name    = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			// is sh404sef disabled ?
			if (basename(dirname($manifest)) == "com_sh404sef")
			{
				if (is_callable("Sh404sefFactory::getConfig"))
				{
					$sefConfig = Sh404sefFactory::getConfig();
					if (!$sefConfig->Enabled)
					{
						$app->version = $manifestdata["version"] . " (Disabled in SH404 settings)";
					}
				}
				else
				{
					$app->version = $manifestdata["version"] . " (sh404sef system plugins not enabled)";
				}
			}
			$name        = "component_" . basename(dirname($manifest));
			$apps[$name] = $app;
		}

// modules
		if (Folder::exists(JPATH_SITE . "/modules"))
		{
			$xmlfiles4 = Folder::files(JPATH_SITE . "/modules", "\.xml", true, true);
		}
		else
		{
			$xmlfiles4 = array();
		}
		foreach ($xmlfiles4 as $manifest)
		{
			if (strpos($manifest, "mod_") === false)
				continue;

			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                  = new stdClass();
			$app->name            = $manifestdata["name"];
			$app->version         = $manifestdata["version"];
			$app->criticalversion = "";
			$name                 = "module_" . str_replace(".xml", "", basename($manifest));
			$apps[$name]          = $app;
		}

// club layouts
		$xmlfiles1 = Folder::files(JEV_PATH . "views", "manifest\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (realpath($manifest) != $manifest)
				continue;
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                                            = new stdClass();
			$app->name                                      = $manifestdata["name"];
			$app->version                                   = $manifestdata["version"];
			$apps["layout_" . basename(dirname($manifest))] = $app;
		}

		$xmlfiles1 = Folder::files(JPATH_ADMINISTRATOR . "/manifests/files", "\.xml", true, true);
		foreach ($xmlfiles1 as $manifest)
		{
			if (realpath($manifest) != $manifest)
				continue;
			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app                                                            = new stdClass();
			$app->name                                                      = $manifestdata["name"];
			$app->version                                                   = $manifestdata["version"];
			$apps[str_replace(".xml", "", "layout_" . basename($manifest))] = $app;
		}

// plugins
		if (Folder::exists(JPATH_SITE . "/plugins"))
		{
			$xmlfiles2 = Folder::files(JPATH_SITE . "/plugins", "\.xml", true, true);
		}
		else
		{
			$xmlfiles2 = array();
		}

		foreach ($xmlfiles2 as $manifest)
		{
			if (strpos($manifest, "Zend") > 0 || strpos($manifest, "invalid") === 0 || strpos($manifest, "invalid") > 0)
				continue;

			if (!$manifestdata = $this->getValidManifestFile($manifest))
				continue;

			$app          = new stdClass();
			$app->name    = $manifestdata["name"];
			$app->version = $manifestdata["version"];
			$name         = str_replace(".xml", "", basename($manifest));
			$group        = basename(dirname(dirname($manifest)));
			$plugin       = PluginHelper::getPlugin($group, $name);
			if (!$plugin)
			{
				$app->version .= " (not enabled)";
			}

			$name        = "plugin_" . $group . "_" . $name;
			$apps[$name] = $app;
		}

		$output = "<textarea rows='40' cols='80' class='versionsinfo'>[code]\n";
		$output .= "PHP Version : " . phpversion() . "\n";
		$output .= "MySQL Version : " . Factory::getDbo()->getVersion() . "\n";
		$output .= "Server Information : " . php_uname() . "\n";

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if ($params->get("fixjquery", -1) == -1)
		{
			$output .= "*** CONFIG NOT SAVED*** \n";
		}
		$output .= "Fix jQuery? : " . ($params->get("fixjquery", 1) ? "Yes" : "No") . "\n";
		$output .= "Load JEvents Bootstrap CSS? : " . ($params->get("bootstrapcss", 1) ? "Yes" : "No") . "\n";
		//$output .= "Load JEvents Bootstrap JS? : " . ($params->get("bootstrapjs", 1)?"Yes":"No"). "\n";
		if (ini_get("max_input_vars") > 0 && ini_get("max_input_vars") <= 10000)
		{
			$output .= "Max Input Vars ? : " . ini_get("max_input_vars") . "\n";
		}
		$output    .= "Club code set? : " . ($params->get("clubcode", false) ? "Yes" : "No") . "  \n";
		$server    = new \Joomla\Input\Input($_SERVER);
		$useragent = $server->get('HTTP_USER_AGENT', false, "string");
		$output    .= $useragent ? "User Agent : " . $useragent . "  \n" : "";
		foreach ($apps as $appname => $app)
		{
			$output .= "$appname : $app->version\n";
		}
		$output .= "[/code]</textarea>";

		return $output;

	}

	function getTranslatorLink()
	{

		$translatorUrl = Text::_("JEV_TRANSLATION_AUTHOR");
		//$translatorUrl = Text::_("JEV_TRANSLATION_AUTHOR_URL");
		//$translatorUrl = "<a href=\"$translatorUrl\">$translatorName</a>";

		return $translatorUrl;

	}

	function support()
	{

		jimport('joomla.html.pane');

		$document = Factory::getDocument();
		$document->setTitle(Text::_('JEVENTS') . ' :: ' . Text::_('JEVENTS'));

		JToolbarHelper::title(Text::_('JEVENTS') . ' :: ' . Text::_('JEVENTS'), 'jevents');



		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		if (ini_get("max_input_vars") > 0 && ini_get("max_input_vars") <= 1000)
		{

			Factory::getApplication()->enqueueMessage('234 - ' . Text::sprintf("MAX_INPUT_VARS_LOW_WARNING", ini_get("max_input_vars")), 'warning');

		}


	}

}

