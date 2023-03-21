<?php

// No direct access
defined('_JEXEC') or die;

JLoader::register('JevRegistry', JPATH_SITE . "/components/com_jevents/libraries/registry.php");

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

/**
 * JEvents component helper.
 *
 * @package        Jevents
 * @since          1.6
 */
class JEventsHelper
{

	public static $extention = 'com_jevents';

	public static function validateSection($context, $form = null)
	{
		// only called from com_fields
		if (JFactory::getApplication()->input->getCmd('option', 'com_jevents') == "com_fields")
		{
			$vName      = JFactory::getApplication()->input->getCmd('view', 'fields');
			$jversion = new JVersion;

			// Must load admin language files
			$lang = Factory::getLanguage();
			$lang->load("com_jevents", JPATH_ADMINISTRATOR);

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
				Factory::getApplication()->enqueueMessage(Text::_("COM_JEVENTS_JOOMLA_CUSTOM_FIELDS_INTEGRATION_WARNING"),"info");
			}
			if (!GSLMSIE10)
			{

				if ($jversion->isCompatible('4.0'))
				{
					// disable com_fields styling until we can get the toolbar buttons working!
					//return 'site';
					$app        = JFactory::getApplication();
					$dispatcher = $app->bootComponent('com_fields')->getDispatcher($app);
					$controller = $dispatcher->getController('display', 'Administrator', array('option' => 'com_fields'));

					$view = $controller->getView($vName, 'html', 'Administrator');
					$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/com_fields/$vName/tmpl/");
				}
				else
				{
					$controller = JControllerLegacy::getInstance("Fields");

					$view = $controller->getView($vName, 'html', 'fieldsView');
					$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/com_fields/$vName/tmpl/");
				}
			}
		}

		$vName      = Factory::getApplication()->input->getCmd('view', 'categories');

		if ($context == "categories" && Factory::getApplication()->input->get('view') == "category"  && Factory::getApplication()->input->get('layout') == "edit")
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
			if (!GSLMSIE10)
			{
				$jversion = new Version;
				if ($jversion->isCompatible('4.0'))
				{
					// disable com_categories styling until we can get the toolbar buttons working!
					//return 'site';
					$app = Factory::getApplication();
					$component = $app->bootComponent('com_categories');
					$dispatcher = $component->getDispatcher($app);
					$controller = $dispatcher->getController('display', 'Administrator', array('option' => 'com_categories'));

					// Notice that administrator is lower case
					$view = $controller->getView($vName, 'html', 'administrator');
					$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/com_categories/$vName/tmpl/");

					include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");
				}
				else
				{
					$controller = BaseController::getInstance("Categories");
					$view       = $controller->getView('category', 'html');

					$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/com_categories/category/tmpl/");
				}
			}
		}
		return false;
	}

	/**
	 * Method to load the countItems method from the extensions
	 *
	 * @param   \stdClass[]  &$items     The category items
	 * @param   string       $extension  The category extension
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public static function countItems(&$items, $extension)
	{
		$vName      = Factory::getApplication()->input->getCmd('view', 'categories');

		if (Factory::getApplication()->input->get('view') == "categories" || (Factory::getApplication()->input->get('option') == "com_categories" && Factory::getApplication()->input->get('extension') == "com_jevents"))
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
			if (!GSLMSIE10)
			{
				$jversion = new Version;
				if ($jversion->isCompatible('4.0'))
				{
					// disable com_categories styling until we can get the toolbar buttons working!
					//return 'site';
					$app = Factory::getApplication();
					$component = $app->bootComponent('com_categories');
					$dispatcher = $component->getDispatcher($app);
					$controller = $dispatcher->getController('display', 'Administrator', array('option' => 'com_categories'));

					// Notice that administrator is lower case
					$view = $controller->getView($vName, 'html', 'administrator');
					$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/com_categories/$vName/tmpl/");

					include_once(JPATH_ADMINISTRATOR . "/components/com_jevents/jevents.defines.php");
				}
				else
				{
					$controller = BaseController::getInstance("Categories");
					$view       = $controller->getView('category', 'html');

					$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/com_categories/category/tmpl/");
				}
			}
		}

		$db = Factory::getDbo();

		// Allow custom state / condition values and custom column names to support custom components
		$counter_names = array(
			'-1' => 'count_trashed',
			'0'  => 'count_unpublished',
			'1'  => 'count_published'
		);

		// Index category objects by their ID
		$records = array();

		foreach ($items as $item)
		{
			$records[(int) $item->id] = $item;
		}

		// The relation query does not return a value for cases without relations of a particular state / condition, set zero as default
		foreach ($items as $item)
		{
			foreach ($counter_names as $n)
			{
				$item->{$n} = 0;
			}
		}

		/**
		 * Get relation counts for all category objects with single query
		 * NOTE: 'state IN', allows counting specific states / conditions only, also prevents warnings with custom states / conditions, do not remove
		 */

		$params = ComponentHelper::getParams('com_jevents');
		if ($params->get("multicategory", 0))
		{
			// Table alias for related data table below will be 'c', and state / condition column is inside related data table
			$recid_col = $db->quoteName('map.catid');
			$state_col = $db->quoteName('evt.state');

			$query = $db->getQuery(true)
				->from($db->quoteName('#__jevents_catmap', 'map'));

			$query
				->select($recid_col . ' AS catid, ' .  $state_col . ' AS state, COUNT(distinct map.evid) AS count')
				->leftJoin($db->quoteName('#__jevents_vevent', 'evt') . ' ON evt.ev_id = map.evid')
				->where($recid_col . ' IN (' . implode(',', array_keys($records)) . ')')
				->where($state_col . ' IN (' . implode(',', array_keys($counter_names)) . ')')
				->group( $recid_col . ', ' . $state_col);
		}
		else
		{
			// Table alias for related data table below will be 'c', and state / condition column is inside related data table
			$recid_col = $db->quoteName('c.catid');
			$state_col = $db->quoteName('c.state');

			$query = $db->getQuery(true)
				->from($db->quoteName('#__jevents_vevent', 'c'));

			$query
				->select($recid_col . ' AS catid, ' . $state_col . ' AS state, COUNT(distinct c.ev_id) AS count')
				->where($recid_col . ' IN (' . implode(',', array_keys($records)) . ')')
				->where($state_col . ' IN (' . implode(',', array_keys($counter_names)) . ')')
				->group($recid_col . ', ' . $state_col);
		}

		$relationsAll = $db->setQuery($query)->loadObjectList();

		// Loop through the DB data overwriting the above zeros with the found count
		foreach ($relationsAll as $relation)
		{
			// Sanity check in case someone removes the state IN above ... and some views may start throwing warnings
			if (isset($counter_names[$relation->state]))
			{
				$id = (int) $relation->catid;
				$cn = $counter_names[$relation->state];

				$records[$id]->{$cn} = $relation->count;
			}
		}

		return $items;

	}

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
				$jversion = new Version;
				if ($jversion->isCompatible('4.0'))
				{
					// disable com_fields styling until we can get the toolbar buttons working!
					//return 'site';
					$app = Factory::getApplication();
					$dispatcher = $app->bootComponent('com_categories')->getDispatcher($app);
					$controller = $dispatcher->getController('display', 'Administrator', array('option' => 'com_categories'));

					$view = $controller->getView($vName, 'html', 'Administrator');
					$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/com_categories/$vName/tmpl/");
				}
				else
				{
					$controller = BaseController::getInstance("Categories");
					$view       = $controller->getView("categories", 'html', 'categoriesView');

					$view->addTemplatePath(JPATH_ADMINISTRATOR . "/components/com_jevents/views/com_categories/categories/tmpl/");
				}
			}

			$doc = Factory::getDocument();

			$style = '#toolbar-options {'
				. 'display:none;'
				. '}';


			// Category styling
			$style .= <<<STYLE
#categoryList td.center a {
   /* border:none;*/
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
			Text::_('CONTROL_PANEL'), 'index.php?option=com_jevents', $vName == 'cpanel.cpanel'
		);

		JHtmlSidebar::addEntry(
			Text::_('JEV_ADMIN_ICAL_EVENTS'), 'index.php?option=com_jevents&task=icalevent.list', $vName == 'icalevent.list'
		);

		if (JEVHelper::isAdminUser())
		{
			JHtmlSidebar::addEntry(
				Text::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS'), 'index.php?option=com_jevents&task=icals.list', $vName == 'icals.list'
			);
		}
		JHtmlSidebar::addEntry(
			Text::_('JEV_INSTAL_CATS'), "index.php?option=com_categories&view=categories&extension=com_jevents", $vName == 'categories'
		);
		if (JEVHelper::isAdminUser())
		{
			$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
			if ($params->get("authorisedonly", 0))
			{
				JHtmlSidebar::addEntry(
					Text::_('JEV_MANAGE_USERS'), 'index.php?option=com_jevents&task=user.list', $vName == 'user.list'
				);
			}
			JHtmlSidebar::addEntry(
				Text::_('JEV_INSTAL_CONFIG'), 'index.php?option=com_jevents&task=params.edit', $vName == 'params.edit'
			);
			JHtmlSidebar::addEntry(
				Text::_('JEV_LAYOUT_DEFAULTS'), 'index.php?option=com_jevents&task=defaults.list', in_array($vName, array('defaults.list', 'defaults.overview'))
			);

			//Support & CSS Customs should only be for Admins really.
			JHtmlSidebar::addEntry(
				Text::_('SUPPORT_INFO'), 'index.php?option=com_jevents&task=cpanel.support', $vName == 'cpanel.support'
			);
			JHtmlSidebar::addEntry(
				Text::_('JEV_CUSTOM_CSS'), 'index.php?option=com_jevents&view=customcss', $vName == 'customcss'
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
					Text::_('COM_JEVLOCATIONS'), $link, $vName == 'cpanel.managed_locations'
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
					Text::_('COM_JEVPEOPLE'), $link, $vName == 'cpanel.managed_people'
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
					Text::_('COM_RSVPPRO'), $link, $vName == 'cpanel.rsvppro'
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
				if (version_compare($manifestCache->version, "3.6.17", "ge"))
				{
					$link = "index.php?option=com_jevents&task=plugin.jev_customfields.overview";
					Factory::getLanguage()->load("plg_jevents_jevcustomfields", JPATH_ADMINISTRATOR);
					JHtmlSidebar::addEntry(
						Text::_('JEV_CUSTOM_FIELDS'), $link, $vName == 'plugin.jev_customfields.overview'
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
	 * @return    CMSObject
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
			$result = JEventsHelper::ObjectSetter($action, $user->authorise($action, $assetName));
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
	public function ObjectSetter($property, $value = null)
	{
		$previous = isset($this->$property) ? $this->$property : null;
		$this->$property = $value;

		return $previous;
	}

	static public function showOnRel($form, $fieldid)
	{
		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		if (!$params->get("enableshowon", 0))
		{
			return "";
		}

		$field = $form->getField($fieldid);
		$rel = "";
		$showon = false;

		if ($field && $field->showon)
		{
			$showon = $field->showon;
		}
		else if ($field && $field->getAttribute('showontabtest', false) && $params->get("com_single_pane_edit", 0))
		{
			$showon = $field->getAttribute('showontabtest', false);
		}
		if ($showon)
		{
		    HTMLHelper::_('jquery.framework');
            JEVHelper::script('showon.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
//            HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true), array('defer' => true));

			$rel           = ' data-showon-gsl=\'' .
				json_encode(FormHelper::parseShowOnConditions($showon, $field->formControl, $field->group)) . '\'';
		}
		echo $rel;
	}

	static public function JEvents_Version($outputinput = true)
	{
		static $packageversionset = false;
		static $packageversion = 'JEVENTS_VERSION';

		if (!$packageversionset)
		{
			$packageversionset = true;
			// When installed directly from github the manifest cache is not kept up to date also YOURSITES_VERSION needs to be replaced
			if ($packageversion == ('JEVENTS_' . 'VERSION') && file_exists(dirname(dirname(dirname(__DIR__))) . "/package/pkg_jevents.xml"))
			{
				$pkgcontents = file_get_contents(dirname(dirname(dirname(__DIR__))) . "/package/pkg_jevents.xml");
				$matches     = array();
				preg_match('#<version>(.*)<\/version>#', $pkgcontents, $matches);
				if (count($matches) == 2)
				{
					$packageversion = $matches[1];
				}
			}
		}
		if ($outputinput)
		{
			?>
			<input type="hidden" id="jevents_version" value="<?php echo $packageversion; ?>"/>
			<?php
		}
		return $packageversion;
	}

	/**
	 * Adds Count Items for Tag Manager.
	 *
	 * @param   \stdClass[]  $items      The content objects
	 * @param   string       $extension  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 * @throws  \Exception
	 */
	public static function countTagItems(array $items, string $extension)
	{
		$db = Factory::getDbo();

		// Allow custom state / condition values and custom column names to support custom components
		$counter_names =  array(
			'-2' => 'count_trashed',
			'0'  => 'count_unpublished',
			'1'  => 'count_published',
			'2'  => 'count_archived',
		);

		// The relation query does not return a value for cases without relations of a particular state / condition, set zero as default
		foreach ($items as $item) {
			foreach ($counter_names as $n) {
				$item->{$n} = 0;
			}
		}

		$query = "select evt.state, count(evt.state) FROM #__contentitem_tag_map AS ctm
		INNER JOIN #__ucm_content as uc on ctm.core_content_id = uc.core_content_id AND uc.core_type_alias = 'com_jevents.eventdetail'
		INNER JOIN #__jevents_vevdetail AS det ON det.evdet_id = uc.core_content_item_id
		INNER JOIN #__jevents_vevent AS evt ON evt.detail_id = det.evdet_id
		GROUP BY evt.state";

		$relationsAll = $db->setQuery($query)->loadObjectList();

		// Loop through the DB data overwriting the above zeros with the found count
		foreach ($relationsAll as $relation) {
			// Sanity check in case someone removes the state IN above ... and some views may start throwing warnings
			if (isset($counter_names[$relation->state])) {
				$id = (int) $relation->catid;
				$cn = $counter_names[$relation->state];

				$records[$id]->{$cn} = $relation->count;
			}
		}


	}

}
