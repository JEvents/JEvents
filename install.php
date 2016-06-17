<?php

/**
 * JEvents Component for Joomla 2.5.x
 *
 * @version     3.4.13
 * @releasedate January 2015
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2016 GWE Systems Ltd, 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport( 'joomla.application.component.helper' );

class Pkg_JeventsInstallerScript
{
	public function preflight ($type, $parent) {
		// Joomla! broke the update call, so we have to create a workaround check.
		$db = JFactory::getDbo();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevents'");
		$is_enabled = $db->loadResult();

		if (!$is_enabled){
			$this->hasJEventsInst = 0;
			return;
		} else {
			$this->hasJEventsInst = 1;
			if (version_compare(JVERSION, '3.0', '<')){
				Jerror::raiseWarning(null, 'This version of JEvents is desgined for Joomla 3.4.4 and later.<br/>Please update Joomla before upgrading JEvents to this version' );
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

	public function install($parent)
	{
            $this->postflightHandler("update", $parent);
            return true;
	}

	public function uninstall($parent)
	{
                $uninstall_text = JText::_('JEV_SORRY_THAT_YOU_UNINSTALL');
                $uninstall_text2 = JText::_('JEV_PLEASE_LET_US_KNOW_WHY'); 
                if ($uninstall_text ==  'JEV_SORRY_THAT_YOU_UNINSTALL'){
                    $uninstall_text = "We are sorry that you have uninstalled JEvents";
                    $uninstall_text2 = "Please let us know why at our <a href='https://www.jevents.net/forum'>support forum</a>  so we can improve our product offering for future users."; 
                }
		echo "<div class='jev_install'>
				<div class='jev_logo'><img src='https://www.jevents.net/logo/JeventsTransparent2.png' /></div>
				<div class='version'><h2>". $uninstall_text ."</h2></div>
				<div class='installed'>
					<h4>".$uninstall_text2."</h4>
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
        
        /*
	 * enable the plugins
	 */

	public function postflightHandler($type, $parent)
	{
		// CSS Styling:
		?>
		<style type="text/css">
			.adminform tr th:first-child {display:none;}
			table.adminform tr td {padding:15px;}
			div.jev_install {background-color:#f4f4f4;border:1px solid #ccc; border-radius:5px; padding:10px;}
			.installed {clear:both;display:inline-block;}
			.installed ul { width:350px;padding-left:0px;border: 1px solid #ccc;border-radius: 5px;}
			.installed ul li:first-child {border-top-left-radius: 5px;border-top-right-radius: 5px;}
			.installed ul li:last-child {border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;}
			.installed ul li {padding:8px;list-style-type:none;}
			.installed ul li:nth-child(odd) {background-color: #fff;}
			.installed ul li:nth-child(even) {background-color: #D6D6D6;}
			.proceed {display:inline-block; vertical-align:top;}
			div.proceed ul {text-align:center;list-style-type:none;}
			div.proceed ul li {padding:5px;background-color:#fff;border:1px solid #ccc;margin-bottom:10px;border-radius:5px;}
		</style>
		<?php
		// End of CSS Styling
		if ($this->hasJEventsInst == 1) { 
                    $inst_text = JText::_('JEV_INST_VERSION_UPRG'); 
                    $logo = "JeventsTransparent3.png";
                } 
                else {  
                    $inst_text = JText::_('JEV_INST_VERSION');
                    $logo = "JeventsTransparent.png";                    
                }

		echo "<div class='jev_install'>
				<div class='jev_logo'><img src='https://www.jevents.net/logo/$logo' /></div>
				<div class='version'><h2>". $inst_text .": ".$parent->get('manifest')->version."</h2></div>
				<div class='installed'>
					<ul>
						<li>JEvents Core Component</li>
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
			$db = JFactory::getDbo();
			$query = "SELECT * FROM #__extensions WHERE name='plg_content_finder' and type='plugin' and element='finder'";
			$db->setQuery($query);
			$finder_q = $db->loadObject();
			$finder = $finder_q->enabled;

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

		}
		else {
			jimport( 'joomla.filesystem.file' );
			// Ok Flatplus clean up to remove helpers
			$file1 = JPATH_SITE . '/components/com_jevents/views/flatplus/helpers/flatplusloadedfromtemplate.php';
			$file2 = JPATH_SITE . '/components/com_jevents/views/flatplus/helpers/flatpluseventmanagementdialog.php';
			$file3 = JPATH_SITE . '/components/com_jevents/views/flatplus/helpers/flatplusicaldialog.php';

			if (JFile::exists($file1)) JFile::delete($file1);
			if (JFile::exists($file2)) JFile::delete($file2);
			if (JFile::exists($file3)) JFile::delete($file3);

			$file4 = JPATH_SITE . '/components/com_jevents/libraries/checkconflict.php';
			if (JFile::exists($file4)) JFile::delete($file4);

			// Lets make sure our Core plugin is enabled..
			$db = JFactory::getDbo();
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
		if (JFactory::getApplication()->input->getCmd("option")=="com_installer" && JFactory::getApplication()->input->getCmd("view")=="update"){
                    JFactory::getApplication()->enqueueMessage("<div class='jev_logo'><img src='https://www.jevents.net/logo/JeventsTransparent3.png' /></div>".JText::_('JEV_INST_VERSION_UPRG'), 'message');
		}

	}

}

