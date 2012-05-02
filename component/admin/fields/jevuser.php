<?php
/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevuser.php 1957 2011-04-25 08:28:48Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldJEVuser extends JFormFieldList
{

	protected $type = 'JEVuser';

	public function getOptions()
	{
		$params = JComponentHelper::getParams("com_jevents");

		$db = & JFactory::getDBO();

		$rules = JAccess::getAssetRules("com_jevents", true);
		$creatorgroups = $rules->getData();
		if (strpos($this->name, "jevadmin") === 0)
		{
			$action = "core.admin";
		}
		else if (strpos($this->name, "jeveditor") === 0)
		{
			$action = "core.edit";
		}
		else if (strpos($this->name, "jevpublisher") === 0)
		{
			$action = "core.edit.state";
		}
		else if (strpos($this->name, "admin") === 0)
		{
			$action = "core.edit.state";
		}
		else
		{
			$action = "core.create";
		}
		// need to merge the arrays because of stupid way Joomla checks super user permissions
		$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups[$action]->getData());

		$users = array(0);
		foreach ($creatorgroups as $creatorgroup => $permission)
		{
			if ($permission == 1)
			{
				$users = array_merge(JAccess::getUsersByGroup($creatorgroup, true), $users);
			}
		}
		$sql = "SELECT id AS value, name AS text FROM #__users where id IN (" . implode(",", array_values($users)) . ") ORDER BY name asc";
		$db->setQuery($sql);
		$users = $db->loadObjectList();
		
		$nulluser = new stdClass();
		$nulluser->value = 0;
		$nulluser->text = JText::_("SELECT_ADMIN");
		array_unshift($users, $nulluser);
		
		return $users;
		
	}
}
