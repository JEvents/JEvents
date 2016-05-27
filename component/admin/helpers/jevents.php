<?php

// No direct access
defined('_JEXEC') or die;

JLoader::register('JevJoomlaVersion',JPATH_ADMINISTRATOR."/components/com_jevents/libraries/version.php");

/**
 * JEvents component helper.
 *
 * @package		Jevents
 * @since		1.6
 */
class JEventsHelper
{

	public static $extention = 'com_jevents';

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName = "")
	{
		$jinput = JFactory::getApplication()->input;

		$task = $jinput->getCmd("task", "cpanel.cpanel");
		$option = $jinput->getCmd("option", "com_categories");

		if ($option == 'com_categories')
		{
			$doc = JFactory::getDocument();
			if (!JevJoomlaVersion::isCompatible("3.0"))
			{
				$hide_options = '#toolbar-popup-options {'
						. 'display:none;'
						. '}';
			}
			else
			{
				$hide_options = '#toolbar-options {'
						. 'display:none;'
						. '}';
			}
			$doc->addStyleDeclaration($hide_options);
			// Category styling 
			$style = <<<STYLE
#categoryList td.center a {
    border:none;
}
STYLE;
			JFactory::getDbo()->setQuery("SELECT * FROM #__categories WHERE extension='com_jevents'");
			$categories = JFactory::getDbo()->loadObjectList('id');
			foreach ($categories as $cat){
				$catparams = new JRegistry($cat->params);
				if ($catparams->get("catcolour")) {
					$style .=  "tr[item-id='$cat->id'] a {  border-left:solid 3px  ".$catparams->get("catcolour").";padding-left:5px;}\n";
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
                        $params = JComponentHelper::getParams(JEV_COM_COMPONENT);
                        if ($params->get("authorisedonly", 0)) {
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
                                        JText::_('JEV_CUSTOM_CSS'), 'index.php?option=com_jevents&task=cpanel.custom_css', $vName == 'cpanel.custom_css'
                        );
                        
                        // Links to addons
                        // Managed Locations
                        $db = JFactory::getDbo ();
                        $db->setQuery ( "SELECT enabled FROM #__extensions WHERE element = 'com_jevlocations' AND type='component' " );
                        $is_enabled = $db->loadResult ();
                        if ($is_enabled) {
                                $link = "index.php?option=com_jevlocations";
                                JFactory::getLanguage()->load("com_jevlocations", JPATH_ADMINISTRATOR);
                                JHtmlSidebar::addEntry(
                                        JText::_('COM_JEVLOCATIONS'), $link, $vName == 'cpanel.managed_locations'
                                );
                        }
                        
                        // Managed People
                        $db = JFactory::getDbo ();
                        $db->setQuery ( "SELECT enabled FROM #__extensions WHERE element = 'com_jevpeople' AND type='component' " );
                        $is_enabled = $db->loadResult ();
                        if ($is_enabled) {
                                $link = "index.php?option=com_jevpeople";
                                JFactory::getLanguage()->load("com_jevpeople", JPATH_ADMINISTRATOR);
                                JHtmlSidebar::addEntry(
                                        JText::_('COM_JEVPEOPLE'), $link, $vName == 'cpanel.managed_people'
                                );
                                
                        }
                        // RSVP Pro
                        $db = JFactory::getDbo ();
                        $db->setQuery ( "SELECT enabled FROM #__extensions WHERE element = 'com_rsvppro' AND type='component' " );
                        $is_enabled = $db->loadResult ();
                        if ($is_enabled) {
                                $link = "index.php?option=com_rsvppro";
                                JFactory::getLanguage()->load("com_rsvppro", JPATH_ADMINISTRATOR);
                                JHtmlSidebar::addEntry(
                                        JText::_('COM_RSVPPRO'), $link, $vName == 'cpanel.rsvppro'
                                );
                                
                        }
                        // Custom Fields				
                        $db = JFactory::getDbo ();
                        $db->setQuery ( "SELECT * FROM #__extensions WHERE element = 'jevcustomfields' AND type='plugin' AND folder='jevents' " );
                        $extension = $db->loadObject();
                        // Stop if user is not authorised to manage JEvents
                        if ($extension && $extension->enabled && JEVHelper::isAdminUser()) {
                                $manifestCache = json_decode($extension->manifest_cache);
                                if (version_compare($manifestCache->version, "3.5.0RC", "ge") )
                                {
                                        $link = "index.php?option=com_jevents&task=plugin.jev_customfields.overview";
                                        JFactory::getLanguage()->load("plg_jevents_jevcustomfields", JPATH_ADMINISTRATOR);
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
	 * @param	int		The category ID.
	 * @param	int		The article ID.
	 *
	 * @return	JObject
	 */
	public static function getActions($categoryId = 0, $articleId = 0)
	{
		$user = JFactory::getUser();
		$result = new JObject;

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
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;

	}

}