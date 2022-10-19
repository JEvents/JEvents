<?php
/**
 * JEvents Locations Component for Joomla 1.5.x
 *
 * @version     $Id: jevuser.php 1957 2011-04-25 08:28:48Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!

defined('JPATH_BASE') or die;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
FormHelper::loadFieldClass('list');

class JFormFieldJEVuser extends JFormFieldList
{

	protected $type = 'JEVuser';

	protected function getInput()
	{

		JLoader::register('JEVHelper', JPATH_SITE . "/components/com_jevents/libraries/helper.php");
		JEVHelper::ConditionalFields($this->element, $this->form->getName());

		// if no value set then default to zero
		if (intval($this->value) == 0)
		{
			if ($this->name == "jform[params][admin]"
				&& Factory::getApplication()->input->getCmd("option") == "com_categories")
			{
				$this->value = 0;
			}
			else
			{
				$options = (array) $this->getOptions();
				foreach ($options as $option)
				{
					if ($option->sendEmail)
					{
						$this->value = $option->value;
						break;
					}
				}
			}
		}

		return parent::getInput();
	}

	public function getOptions()
	{

		$params = ComponentHelper::getParams("com_jevents");

		$db = Factory::getDbo();

		// if editing category then find the rules for a specific category
		if ($this->name == "jform[params][admin]"
			&& Factory::getApplication()->input->getCmd("option") == "com_categories"
			&& Factory::getApplication()->input->getInt("id") > 0)
		{
			$rules = Access::getAssetRules("com_jevents.category." . Factory::getApplication()->input->getInt("id"), true);
		}
		else
		{
			//Access::preload(array("com_jevents"));
			$rules = Access::getAssetRules("com_jevents", true);
		}
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
		//$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups[$action]->getData());
		// use union orf arrays sincee getData no longer has string keys in the resultant array
		//$creatorgroups = $creatorgroups["core.admin"]->getData()+ $creatorgroups["core.create"]->getData();
		// use union orf arrays sincee getData no longer has string keys in the resultant array
		$creatorgroupsdata = isset($creatorgroups["core.admin"]) ? $creatorgroups["core.admin"]->getData() : array();
		// take the higher permission setting
		if (isset($creatorgroups["core.create"]))
		{
			foreach ($creatorgroups[$action]->getData() as $creatorgroup => $permission)
			{
				if ($permission)
				{
					$creatorgroupsdata[$creatorgroup] = $permission;
				}
			}
		}
		$users = array(0);
		foreach ($creatorgroupsdata as $creatorgroup => $permission)
		{
			if ($permission == 1)
			{
				$users = array_merge(Access::getUsersByGroup($creatorgroup, true), $users);
			}
		}

		$sql = "SELECT id AS value, name AS text , sendEmail FROM #__users where id IN (" . implode(",", array_values($users)) . ") ORDER BY name asc";
		$db->setQuery($sql);
		$users = $db->loadObjectList();

		$nulluser            = new stdClass();
		$nulluser->value     = 0;
		$nulluser->sendEmail = 0;
		$nulluser->text      = Text::_("SELECT_ADMIN");
		array_unshift($users, $nulluser);

		return $users;

	}
}
