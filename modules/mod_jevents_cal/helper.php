<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: helper.php 3194 2012-01-16 11:04:20Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Calendar
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;

class modJeventsCalHelper
{

	public function __construct()
	{

		// setup for all required function and classes
		$file = JPATH_SITE . '/components/com_jevents/mod.defines.php';
		if (file_exists($file))
		{
			include_once($file);
			include_once(JEV_LIBS . "/modfunctions.php");

		}
		else
		{
			die ("JEvents Calendar\n<br />This module needs the JEvents component");
		}

		// load language constants
		JEVHelper::loadLanguage('modcal');
	}

	public function getViewClass($theme, $module, $layout, $params = false)
	{

		// If we have a specified over ride then use it here
		if ($params && StringHelper::strlen($params->get("layout", "")) > 0)
		{
			$speciallayout = strtolower($params->get("layout", ""));
			// Build the template and base path for the layout
			$tPath = JPATH_SITE . '/' . 'templates' . '/' . Factory::getApplication()->getTemplate() . '/' . 'html' . '/' . $module . '/' . $theme . '/' . $speciallayout . '.php';

			// If the template has a layout override use it
			if (file_exists($tPath))
			{
				$viewclass = "Override" . ucfirst($theme) . "ModCalView" . ucfirst($speciallayout);
				require_once($tPath);
				if (class_exists($viewclass))
				{
					return $viewclass;
				}
			}
		}
		if ($layout == "" || $layout == "global")
		{
			$layout = JEV_CommonFunctions::getJEventsViewName();
		}

		// Build the template and base path for the layout
		$tPath = JPATH_SITE . '/' . 'templates' . '/' . Factory::getApplication()->getTemplate() . '/' . 'html' . '/' . $module . '/' . $layout . '.php';
		$bPath = JPATH_SITE . '/' . 'modules' . '/' . $module . '/' . 'tmpl' . '/' . $layout . '.php';

		jimport('joomla.filesystem.file');
		// If the template has a layout override use it
		if (File::exists($tPath))
		{
			require_once($tPath);
			$viewclass = "Override" . ucfirst($theme) . "ModCalView";
			if (class_exists($viewclass))
			{
				return $viewclass;
			}
			else
			{
				// fall back to badly declared template override!
				$viewclass = ucfirst($theme) . "ModCalView";
				if (class_exists($viewclass))
				{
					return $viewclass;
				}
			}
		}
		if (File::exists($bPath))
		{
			require_once($bPath);
			$viewclass = ucfirst($theme) . "ModCalView";

			return $viewclass;
		}
		else
		{
			echo "<strong>" . Text::sprintf("JEV_PLEASE_REINSTALL_LAYOUT", $theme) . "</strong>";
			$bPath = JPATH_SITE . '/' . 'modules' . '/' . $module . '/' . 'tmpl' . '/' . 'default' . '/' . 'calendar.php';
			require_once($bPath);
			$viewclass = "DefaultModCalView";

			return $viewclass;

		}
	}

}
