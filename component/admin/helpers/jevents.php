<?php

// No direct access
defined('_JEXEC') or die;

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
		$task = JRequest::getCmd("task", "cpanel.cpanel");
		$option = JRequest::getCmd("option", "com_categories");
		
		if ($vName == "")
		{
			$vName = $task;
		}		
		JSubMenuHelper::addEntry(
				JText::_('CONTROL_PANEL'), 'index.php?option=com_jevents', $vName == 'cpanel.cpanel'
		);

		JSubMenuHelper::addEntry(
				JText::_('JEV_ADMIN_ICAL_EVENTS'), 'index.php?option=com_jevents&task=icalevent.list', $vName == 'icalevent.list'
		);
		
		// could be called from categories component
		JLoader::register('JEVHelper',JPATH_SITE."/components/com_jevents/libraries/helper.php");		
		if (JEVHelper::isAdminUser())
		{
			JSubMenuHelper::addEntry(
				JText::_('JEV_ADMIN_ICAL_SUBSCRIPTIONS'), 'index.php?option=com_jevents&task=icals.list', $vName == 'icals.list'
			);
		}
		JSubMenuHelper::addEntry(
				JText::_('JEV_INSTAL_CATS'), "index.php?option=com_categories&extension=com_jevents" , $vName == 'categories'
		);
		if (JEVHelper::isAdminUser())
		{
			JSubMenuHelper::addEntry(
					JText::_('JEV_MANAGE_USERS'), 'index.php?option=com_jevents&task=user.list', $vName == 'user.list'
			);
			JSubMenuHelper::addEntry(
					JText::_('JEV_INSTAL_CONFIG'), 'index.php?option=com_jevents&task=params.edit', $vName == 'params.edit'
			);
			JSubMenuHelper::addEntry(
					JText::_('JEV_LAYOUT_DEFAULTS'), 'index.php?option=com_jevents&task=defaults.list', in_array($vName , array('defaults.list','defaults.overview'))
			);
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