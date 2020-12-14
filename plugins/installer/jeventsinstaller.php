<?php

/**
 * @package     GWE Systems
 * @subpackage  Installer.JEventsInstaller
 *
 * @copyright   Copyright (C)  2016 - JEVENTS_COPYRIGHT GWE Systems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_BASE') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Factory;

class PlgInstallerJeventsinstaller extends CMSPlugin
{

	/*
	 * Download Package URL checking - called from InstallerHelper::downloadPackage
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{

		// Fix the update URL!
		$pos = strpos($url, "www.jevents.net/updates/download");
		if ($pos > 0)
		{
			$downloadroot = "https://www.jevents.net/updates/download/";
			$updatesroot  = "https://www.jevents.net/updates/";

			$tempurl = str_replace("www.jevents.net/updates/download/", "", substr($url, $pos));
			$parts   = explode("/", $tempurl);
			if (count($parts) == 2)
			{
				list($codepart, $filepart) = $parts;
				$filename = substr($filepart, 0, strpos($filepart, "-update-"));
				//echo $filename."<Br/>";
				$db = Factory::getDbo();
				$db->setQuery("SELECT * FROM #__update_sites WHERE location LIKE " . $db->quote("%" . $filename . "-update-%"));
				$updatesite = $db->loadObject();
				if ($updatesite)
				{
					$newurl = str_replace(array($updatesroot, ".xml"), array($downloadroot, ".zip"), $updatesite->location);
					$url    = $newurl;
					//   echo "new url = ".$newurl;
				}
			}
		}

	}

}
