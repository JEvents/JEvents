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
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_( 'CONTROL_PANEL' ),
			'index.php?option=com_jevents',
			$vName == ''
		);
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
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($articleId) && empty($categoryId)) {
			$assetName = 'com_jevents';
		}
		else if (empty($articleId)) {
			$assetName = 'com_jevents.category.'.(int) $categoryId;
		}
		else {
			$assetName = 'com_jevents.article.'.(int) $articleId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
}