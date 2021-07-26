<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

if (isset($moddata))
{
	if (is_array($moddata))
	{
		$count = 0;
		$mode  = 0;
		foreach ($moddata as $md)
		{
			PluginHelper::importPlugin('content');

			JLoader::register('JevRegistry', JPATH_SITE . "/components/com_jevents/libraries/registry.php");

			$eventdata       = new stdClass();
			$eventdata->text = $md;
			$params          = new JevRegistry(null);
			$results         = Factory::getApplication()->triggerEvent('onContentPrepare', array('com_jevents', & $eventdata, & $params, 0));
			$md              = $eventdata->text;

			echo "<div class='jevmodrowcount$count jevmodrow$mode' >" . $md . "</div>";
			$count++;
			$mode = $count % 2;
		}
	}
	else
	{
		echo $moddata;
	}
}
