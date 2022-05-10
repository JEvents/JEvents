<?php
/**
 * @package     JEvents
 * @subpackage  com_jvents
 *
 * @copyright   Copyright (C) 2014-JEVENTS_COPYRIGHT GWESystems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
if (file_exists(JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php'))
{
	JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php');
}
else
{
// Joomla 4
	class_alias("Joomla\Component\Categories\Administrator\Helper\CategoryAssociationHelper", "CategoryHelperAssociation");
}

defined('_JEXEC') or die;

use Joomla\CMS\Language\Associations;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Component\Menus\Administrator\Helper\MenusHelper;

/**
 * Content Component Association Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       3.0
 */
abstract class JEventsHelperAssociation extends CategoryHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer $id   Id of the item
	 * @param   string  $view Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  3.0
	 */

	public static function getAssociations($id = 0, $view = null)
	{

		$user = Factory::getUser();
		$diagnostics = $user->authorise('core.admin', 'com_jevents');
		$diagnostics = false;

		static $returnData = array();
		static $defaultLang = false;

		JLoader::register('JevRegistry', JPATH_SITE . "/components/com_jevents/libraries/registry.php");

		$app    = Factory::getApplication();
		$input = $app->input;
		$view   = is_null($view) ? $input->get('view') : $view;
		$id     = empty($id) ? $input->getInt('id') : $id;

		$jevtask = $input->getCmd('jevtask', '');
		if ($jevtask == 'icalrepeat.detail')
		{
			$rp_id = $input->getInt("evid");

			if (isset($returnData[$rp_id]))
			{
				if ($diagnostics)
				{
					echo "Default Language = $defaultLang<br>";
					echo "<pre>";
					print_r($returnData[$rp_id]);
					echo "</pre>";
				}

				return $returnData[$rp_id];
			}
			$sitelangs = JLanguageHelper::getInstalledLanguages(0);
			$multilang = JLanguageMultilang::isEnabled();

			$return = array();
			if ($multilang && count($sitelangs) > 1 )
			{
				$year = $input->getInt("year");
				$month = $input->getInt("month");
				$day = $input->getInt("day");

				$menu		= $app->getMenu();
				$active		= $menu->getActive();
				$associations = array();
				if ($active)
				{
					if (version_compare(JVERSION, '4.0.0', 'lt'))
					{
						require_once JPATH_ROOT . '/administrator/components/com_menus/helpers/menus.php';
						$associations = \MenusHelper::getAssociations($active->id);
					}
					else
					{
						$associations = MenusHelper::getAssociations($active->id);
					}
				}

				$db = Factory::getDbo();

				$db->setQuery("SELECT t.* FROM #__jevents_repetition as r "
							." INNER JOIN #__jevents_translation as t ON t.evdet_id = r.eventdetail_id"
							. " WHERE r.rp_id = " . $rp_id );
				$translationdata = $db->loadObjectList('language');

				if (!$translationdata)
				{
					$translationdata = array();
				}

				$defaultLang = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');

				$baseVersion = false;
				if ($defaultLang)
				{
					// Base language data
					$db->setQuery("SELECT d.* FROM #__jevents_repetition as r "
						. " INNER JOIN #__jevents_vevdetail as d ON d.evdet_id = r.eventdetail_id"
						. " WHERE r.rp_id = " . $rp_id);
					$baseVersion = $translationdata[$defaultLang] = $db->loadObject();
				}

				if ($translationdata)
				{
					foreach ($translationdata as $key => $val)
					{
						$title = ApplicationHelper::stringURLSafe(!empty($val->summary) ? $val->summary : (isset($baseVersion->summary) ? $baseVersion->summary : ''));

						$return[$key] = "index.php?option=com_jevents&task=icalrepeat.detail&evid=" . $input->getInt("evid") . "&title=" . $title;
						$return[$key] .= $year > 0 ? "&year=$year" : "";
						$return[$key] .= $month > 0 ? "&month=$month" : "";
						$return[$key] .= $day > 0 ? "&day=$day" : "";
						$return[$key] .= "&lang=" . $key;

						if (isset($associations[$key]))
						{
							$return[$key] .= "&Itemid=" . $associations[$key];
						}
					}
				}

			}
			$returnData[$rp_id] = $return;

			return $return;
		}

		if ($view == 'category' || $view == 'categories')
		{
			return self::getCategoryAssociations($id, 'com_jevents');
		}

		return array();

	}
}
