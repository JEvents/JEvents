<?php

// No direct access
defined('_JEXEC') or die;

JLoader::register('JevRegistry', JPATH_SITE . "/components/com_jevents/libraries/registry.php");

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * JEvents component helper.
 *
 * @package        Jevents
 * @since          1.6
 */
class JEventsHelper
{

	public static $extention = 'com_jevents';

	/**
	 * Configure the Linkbar.
	 *
	 * @param    string    The name of the active view.
	 */
	public static function addSubmenu($vName = "")
	{
		if (!defined("GSLMSIE10"))
		{
			if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false || strpos($_SERVER['HTTP_USER_AGENT'], "Internet Explorer") !== false))
			{
				define ("GSLMSIE10" , 1);
			}
			else
			{
				define ("GSLMSIE10" , 0);
			}
		}
		$input = Factory::getApplication()->input;

		$task   = $input->getCmd("task", "cpanel.cpanel");
		$option = $input->getCmd("option", "com_categories");

		if ($option !== 'com_categories' && !GSLMSIE10)
		{
			return;
		}
		if ($option == 'com_categories' )
		{
			if (!GSLMSIE10)
			{
				$controller = JControllerLegacy::getInstance("Categories");
				$view       = $controller->getView("categories", 'html', 'categoriesView');

				$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/categories/tmpl/");
			}

			$doc = Factory::getDocument();

			$hide_options = '#toolbar-options {'
				. 'display:none;'
				. '}';

			$doc->addStyleDeclaration($hide_options);
			// Category styling 
			$style = <<<STYLE
#categoryList td.center a {
    border:none;
}
STYLE;
			Factory::getDbo()->setQuery("SELECT * FROM #__categories WHERE extension='com_jevents'");
			$categories = Factory::getDbo()->loadObjectList('id');
			foreach ($categories as $cat)
			{
				$catparams = new JevRegistry($cat->params);
				if ($catparams->get("catcolour"))
				{
					$style .= "tr[item-id='$cat->id'] a {  border-left:solid 3px  " . $catparams->get("catcolour") . ";padding-left:5px;}\n";
				}
			}

			$doc->addStyleDeclaration($style);
		}

		if ($vName == "")
		{
			$vName = $task;
		}
		// could be called from categories component
		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");

		JHtmlSidebar::addEntry(
			JText::_('CONTROL_PANEL'), 'index.php?option=com_jevents', $vName == 'cpanel.cpanel'
		);

		JHtmlSidebar::addEntry(
			JText::_('JEV_ADMIN_ICAL_EVENTS'), 'index.php?option=com_jevents&task=icalevent.list', $vName == 'icalevent.list'
		);

		if (JEVHelper::isAdminUser())
		{
			JHtmlSidebar::addEntry(
				JText::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS'), 'index.php?option=com_jevents&task=icals.list', $vName == 'icals.list'
			);
		}
		JHtmlSidebar::addEntry(
			JText::_('JEV_INSTAL_CATS'), "index.php?option=com_categories&extension=com_jevents", $vName == 'categories'
		);
		if (JEVHelper::isAdminUser())
		{
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("authorisedonly", 0))
			{
				JHtmlSidebar::addEntry(
					JText::_('JEV_MANAGE_USERS'), 'index.php?option=com_jevents&task=user.list', $vName == 'user.list'
				);
			}
			JHtmlSidebar::addEntry(
				JText::_('JEV_INSTAL_CONFIG'), 'index.php?option=com_jevents&task=params.edit', $vName == 'params.edit'
			);
			JHtmlSidebar::addEntry(
				JText::_('JEV_LAYOUT_DEFAULTS'), 'index.php?option=com_jevents&task=defaults.list', in_array($vName, array('defaults.list', 'defaults.overview'))
			);

			//Support & CSS Customs should only be for Admins really.
			JHtmlSidebar::addEntry(
				JText::_('SUPPORT_INFO'), 'index.php?option=com_jevents&task=cpanel.support', $vName == 'cpanel.support'
			);
			JHtmlSidebar::addEntry(
				JText::_('JEV_CUSTOM_CSS'), 'index.php?option=com_jevents&view=customcss', $vName == 'customcss'
			);

			// Links to addons
			// Managed Locations
			$db = Factory::getDbo();
			$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevlocations' AND type='component' ");
			$is_enabled = $db->loadResult();
			if ($is_enabled)
			{
				$link = "index.php?option=com_jevlocations";
				Factory::getLanguage()->load("com_jevlocations", JPATH_ADMINISTRATOR);
				JHtmlSidebar::addEntry(
					JText::_('COM_JEVLOCATIONS'), $link, $vName == 'cpanel.managed_locations'
				);
			}

			// Managed People
			$db = Factory::getDbo();
			$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_jevpeople' AND type='component' ");
			$is_enabled = $db->loadResult();
			if ($is_enabled)
			{
				$link = "index.php?option=com_jevpeople";
				Factory::getLanguage()->load("com_jevpeople", JPATH_ADMINISTRATOR);
				JHtmlSidebar::addEntry(
					JText::_('COM_JEVPEOPLE'), $link, $vName == 'cpanel.managed_people'
				);

			}
			// RSVP Pro
			$db = Factory::getDbo();
			$db->setQuery("SELECT enabled FROM #__extensions WHERE element = 'com_rsvppro' AND type='component' ");
			$is_enabled = $db->loadResult();
			if ($is_enabled)
			{
				$link = "index.php?option=com_rsvppro";
				Factory::getLanguage()->load("com_rsvppro", JPATH_ADMINISTRATOR);
				JHtmlSidebar::addEntry(
					JText::_('COM_RSVPPRO'), $link, $vName == 'cpanel.rsvppro'
				);

			}
			// Custom Fields				
			$db = Factory::getDbo();
			$db->setQuery("SELECT * FROM #__extensions WHERE element = 'jevcustomfields' AND type='plugin' AND folder='jevents' ");
			$extension = $db->loadObject();
			// Stop if user is not authorised to manage JEvents
			if ($extension && $extension->enabled && JEVHelper::isAdminUser())
			{
				$manifestCache = json_decode($extension->manifest_cache);
				if (version_compare($manifestCache->version, "3.5.0dev", "ge"))
				{
					$link = "index.php?option=com_jevents&task=plugin.jev_customfields.overview";
					Factory::getLanguage()->load("plg_jevents_jevcustomfields", JPATH_ADMINISTRATOR);
					JHtmlSidebar::addEntry(
						JText::_('JEV_CUSTOM_FIELDS'), $link, $vName == 'plugin.jev_customfields.overview'
					);
				}
			}

		}


	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param    int        The category ID.
	 * @param    int        The article ID.
	 *
	 * @return    JObject
	 */
	public function getActions($categoryId = 0, $articleId = 0)
	{

		$user   = Factory::getUser();
		$result = new stdClass;

		if (empty($articleId) && empty($categoryId))
		{
			$assetName = 'com_jevents';
		}
		else if (empty($articleId))
		{
			$assetName = 'com_jevents.category.' . (int) $categoryId;
		}
		else
		{
			$assetName = 'com_jevents.article.' . (int) $articleId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result = JEventsHelper::ObjectSettter($action, $user->authorise($action, $assetName));
		}

		return $result;

	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 *
	 * @return  mixed  Previous value of the property.
	 *
	 * @since   11.1
	 */
	public function ObjectSettter($property, $value = null)
	{
		$previous = isset($this->$property) ? $this->$property : null;
		$this->$property = $value;

		return $previous;
	}

}