<?php

/**
 * JEvents Component for Joomla 2.5.x
 *
 * @version     JEVENTS_VERSION
 * @releasedate JEVENTS_DATE
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.helper');

#[\AllowDynamicProperties]
class Pkg_JeventsInstallerScript
{
	public function preflight($type, $parent)
	{

		define('JEVENTS_MINIMUM_PHP', '7.0.0');

		if (version_compare(PHP_VERSION, JEVENTS_MINIMUM_PHP, '<'))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf("COM_JEVENTS_PHP_VERSION_WARNING", PHP_VERSION), 'warning');
		}

		// Joomla! broke the update call, so we have to create a workaround check.
		$db = Factory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevents'");
		$is_enabled = $db->loadResult();

		if (!$is_enabled)
		{
			$this->hasJEventsInst = 0;
			if (version_compare(JVERSION, '3.9.0', '<'))
			{
				Factory::getApplication()->enqueueMessage('Warning! You are running a very insecure version of Joomla! <br/>Please update Joomla! to at least 3.8.0 before installing JEvents. This will also prevent issues with JEvents', 'warning');

				return false;
			}

			return;
		}
		else
		{
			$this->hasJEventsInst = 1;
			if (version_compare(JVERSION, '3.9.0', '<'))
			{
				Factory::getApplication()->enqueueMessage('This version of JEvents is designed for Joomla 3.8.0 and later.<br/>Please update Joomla! before upgrading JEvents to this version', 'warning');

				return false;
			}

			return;
		}
	}

	public function update($parent)
	{

		$this->postflightHandler("update", $parent);

		return true;
	}

	public function postflightHandler($type, $parent)
	{

		$app    = Factory::getApplication();

		// CSS Styling:
		?>
		<style type="text/css">
			.adminform tr th:first-child {
				display: none;
			}

			table.adminform tr td {
				padding: 15px;
			}

			div.jev_install {
				background-color: #f4f4f4;
				border: 1px solid #ccc;
				border-radius: 5px;
				padding: 10px;
			}

			.installed {
				clear: both;
				display: inline-block;
			}

			.installed ul {
				width: 350px;
				padding-left: 0px;
				border: 1px solid #ccc;
				border-radius: 5px;
			}

			.installed ul li:first-child {
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}

			.installed ul li:last-child {
				border-bottom-left-radius: 5px;
				border-bottom-right-radius: 5px;
			}

			.installed ul li {
				padding: 8px;
				list-style-type: none;
			}

			.installed ul li:nth-child(odd) {
				background-color: #fff;
			}

			.installed ul li:nth-child(even) {
				background-color: #D6D6D6;
			}

			.proceed {
				display: inline-block;
				vertical-align: top;
			}

			div.proceed ul {
				text-align: center;
				list-style-type: none;
			}

			div.proceed ul li {
				padding: 5px;
				background-color: #fff;
				border: 1px solid #ccc;
				margin-bottom: 10px;
				border-radius: 5px;
			}
		</style>
		<?php
		// End of CSS Styling
		if ($this->hasJEventsInst == 1)
		{
			$inst_text = Text::_('JEV_INST_VERSION_UPRG');
			$logo      = "JeventsTransparent3.png";
		}
		else
		{
			$inst_text = Text::_('JEV_INST_VERSION');
			$logo      = "JeventsTransparent.png";
		}

		// TODO Replace ADD_VERSION
		echo "<div class='jev_install'>
				<div class='jev_logo'><a href='index.php?option=com_jevents' ><img src='https://www.jevents.net/logo/$logo' /></a></div>
				<div class='version'><h2 class='gsl-h2'>" . $inst_text . ": ADD_VERSION </h2></div>
				<h3>Exciting New Styling & Joomla 4 Support</h3>
				<p>JEvents 3.6 brings you</p>
				<ul>
					<li>A new backend theme (with new configuration options)</li>
					<li>A new dashboard that gives you an overview and graphical summary of your events (you can choose this or the events list as your landing page)</li>
					<li>A new, more modern, interface for event creation/editing (with new configuration options)</li>
					<li>Joomla 4.0 support</li>
				</ul>
				<p>Please see our latest <a href='https://www.jevents.net/blog/jevents-3-6-exciting-new-styling-featured' target='_blank'>blog post</a> for more information about these features and how they can be configured.</p>
				<div class='installed'>
					<ul>
						<li><a href='index.php?option=com_jevents' >JEvents Core Component</a></li>
						<li>JEvents Module - Latest Events </li>
						<li>JEvents Module - Mini Calendar </li>
						<li>JEvents Module - Filter </li>
						<li>JEvents Module - Legend </li>
						<li>JEvents Module - Switch View </li>
						<li>JEvents Plugin - Search </li>
						<li>JEvents Plugin - Finder </li>
					</ul>
				</div>

				<div class='proceed'>
					<ul>
						<li><a href='index.php?option=com_jevents&task=params.edit' alt='JEvents Configuration'><img src='components/com_jevents/assets/images/jevents_config_sml.png' alt='Configuration Page' /><br/> Configuration</a><br/></li>
						<li><a href='https://www.jevents.net/forum' alt='JEvents Forum'><img src='components/com_jevents/assets/images/support_forum.jpg' alt='JEvents Forum' /><br/>Support Forums</a><br/></li>
						<li><a href='https://www.jevents.net/docs/jevents' alt='JEvents Documentation'><img src='components/com_jevents/assets/images/documentation.jpg' alt='JEvents Documentation' /><br/>Documentation</a></li>
					</ul>
				</div>";

		if ($this->hasJEventsInst == 0)
		{
			// enable plugin
			$db    = Factory::getDbo();
			$query = "SELECT * FROM #__extensions WHERE name='plg_content_finder' and type='plugin' and element='finder'";
			$db->setQuery($query);
			$finder_q = $db->loadObject();
			$finder   = $finder_q->enabled;

			$query = 'SHOW TABLES LIKE "' . $db->getPrefix() . 'finder_types"';
			$db->setQuery($query);
			$finder_types = $db->loadObjectList();

			if (!count($finder_types))
			{
				echo "<div class='alert alert-warning'> Warning! your Joomla! installation is missing the finder_types database table.<br/><br/> You should run a database check and then fix if an error is reporting by <a href='index.php?option=com_installer&view=database' class='btn-warning btn button'>Clicking Here</a></div>";
			}

			if ($finder == 1 && count($finder_types))
			{
				$query = "UPDATE #__extensions SET enabled=1 WHERE folder='finder' and type='plugin' and element='jevents'";
				$db->setQuery($query);
				$db->execute();
			}

			$query = "UPDATE #__extensions SET enabled=1 WHERE folder='search' and type='plugin' and element='eventsearch'";
			$db->setQuery($query);
			$db->execute();

			// Enable new JEvents Plugin
			$query = "UPDATE #__extensions SET enabled=1 WHERE folder='content' and type='plugin' and element='jevents'";
			$db->setQuery($query);
			$db->execute();

			// Enable JSON Plugin
			$query = "UPDATE #__extensions SET enabled=1 WHERE folder='system' and type='plugin' and element='gwejson'";
			$db->setQuery($query);
			$db->execute();

			// Enable Jevents Installer Plugin
			$query = "UPDATE #__extensions SET enabled=1 WHERE folder='installer' and type='plugin' and element='jeventsinstaller'";
			$db->setQuery($query);
			$db->execute();

		}
		else
		{
			jimport('joomla.filesystem.file');
			// Ok Flatplus clean up to remove helpers
			$file1 = JPATH_SITE . '/components/com_jevents/views/flatplus/helpers/flatplusloadedfromtemplate.php';
			$file2 = JPATH_SITE . '/components/com_jevents/views/flatplus/helpers/flatpluseventmanagementdialog.php';
			$file3 = JPATH_SITE . '/components/com_jevents/views/flatplus/helpers/flatplusicaldialog.php';

			if (File::exists($file1)) File::delete($file1);
			if (File::exists($file2)) File::delete($file2);
			if (File::exists($file3)) File::delete($file3);

			$file4 = JPATH_SITE . '/components/com_jevents/libraries/checkconflict.php';
			if (File::exists($file4)) File::delete($file4);

			// Lets make sure our Core plugin is enabled..
			$db    = Factory::getDbo();
			$query = "UPDATE #__extensions SET enabled=1 WHERE folder='content' and type='plugin' and element='jevents'";
			$db->setQuery($query);
			$db->execute();

			// Enable JSON Plugin
			$query = "UPDATE #__extensions SET enabled=1 WHERE folder='system' and type='plugin' and element='gwejson'";
			$db->setQuery($query);
			$db->execute();

		}

		echo "</div>";
		// Joomla updater special case
		if ($app->input->getCmd("option") == "com_installer" && $app->input->getCmd("view") == "update")
		{
			$app->enqueueMessage("<div class='jev_logo'><img src='https://www.jevents.net/logo/JeventsTransparent3.png' /></div>" . Text::_('JEV_INST_VERSION_UPRG') . " :: ADD_VERSION", 'message');
		}

		if (version_compare(JVERSION, '4.0', 'ge'))
		{
			$query = "UPDATE #__modules SET published=1, position='cpanel-jevents' WHERE client_id=1 and module='mod_jevents_dashboard'";
			$db->setQuery($query);
			$db->execute();
		}

	}

	public function install($parent)
	{

		$this->postflightHandler("update", $parent);

		return true;
	}

	/*
	 * enable the plugins
	 */

	public function uninstall($parent)
	{

		$uninstall_text  = Text::_('JEV_SORRY_THAT_YOU_UNINSTALL');
		$uninstall_text2 = Text::_('JEV_PLEASE_LET_US_KNOW_WHY');
		if ($uninstall_text == 'JEV_SORRY_THAT_YOU_UNINSTALL')
		{
			$uninstall_text  = "We are sorry that you have uninstalled JEvents";
			$uninstall_text2 = "Please let us know why at our <a href='https://www.jevents.net/forum'>support forum</a>  so we can improve our product offering for future users.";
		}
		echo "<div class='jev_install'>
				<div class='jev_logo'><img src='https://www.jevents.net/logo/JeventsTransparent2.png' /></div>
				<div class='version'><h2 class='gsl-h2'>" . $uninstall_text . "</h2></div>
				<div class='installed'>
					<h4>" . $uninstall_text2 . "</h4>
                                        <br/><br/><br/>
				</div>";

		return true;
	}

	/*
 * enable the plugins
 */

	public function postflight($type, $parent)
	{

		return;
		//return $this->postflightHandler($type, $parent);
	}

}

