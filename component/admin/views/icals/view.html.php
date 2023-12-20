<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3548 2012-04-20 09:25:43Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-JEVENTS_COPYRIGHT GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * HTML View class for the component
 *
 * @static
 */
#[\AllowDynamicProperties]
class AdminIcalsViewIcals extends JEventsAbstractView
{

	function overview($tpl = null)
	{

		$document = Factory::getDocument();
		$document->setTitle(Text::_('ICALS'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('ICALS'), 'jevents');

		JToolbarHelper::addNew('icals.edit');
		JToolbarHelper::editList('icals.edit');
		JToolbarHelper::publishList('icals.publish');
		JToolbarHelper::unpublishList('icals.unpublish');
		JToolbarHelper::deleteList(Text::_("COM_JEVENTS_MANAGE_CALENDARS_OVERVIEW_DELETE_WARNING", true), 'icals.delete');
		JToolbarHelper::spacer();

		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');


	}

	function edit($tpl = null)
	{

		JEVHelper::script('editicalJQ.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		if (!GSLMSIE10)
		{
			JEVHelper::script('editicalGSL.js', 'components/' . JEV_COM_COMPONENT . '/assets/js/');
		}

		$document = Factory::getDocument();
		$document->setTitle(Text::_('EDIT_ICS'));

		// Set toolbar items for the page
		JToolbarHelper::title(Text::_('EDIT_ICS'), 'jevents');

		//JToolbarHelper::save('icals.save');
		$bar = JToolBar::getInstance('toolbar');
		if ($this->editItem && isset($this->editItem->ics_id) && $this->editItem->ics_id > 0)
		{
			JToolbarHelper::save('icals.savedetails');
		}
		JToolbarHelper::cancel('icals.list');
		//JToolbarHelper::help( 'screen.icals.edit', true);

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		//$section = $params->get("section",0);

		$db = Factory::getDbo();
		if ($params->get("authorisedonly", 0))
		{
			// get authorised users
			$sql = "SELECT u.* FROM #__jev_users as jev LEFT JOIN #__users as u on u.id=jev.user_id where jev.published=1 and jev.cancreate=1";
			$db  = Factory::getDbo();
			$db->setQuery($sql);
			$users = $db->loadObjectList();
		}
		else
		{
			$rules         = Access::getAssetRules("com_jevents", true);
			$creatorgroups = $rules->getData();
			// need to merge the arrays because of stupid way Joomla checks super user permissions
			//$creatorgroups = array_merge($creatorgroups["core.admin"]->getData(), $creatorgroups["core.create"]->getData());
			// use union orf arrays sincee getData no longer has string keys in the resultant array
			//$creatorgroups = $creatorgroups["core.admin"]->getData()+ $creatorgroups["core.create"]->getData();
			// use union orf arrays sincee getData no longer has string keys in the resultant array
			$creatorgroupsdata = isset($creatorgroups["core.admin"]) ? $creatorgroups["core.admin"]->getData() : array();
			// take the higher permission setting
			if (isset($creatorgroups["core.create"]))
			{
				foreach ($creatorgroups["core.create"]->getData() as $creatorgroup => $permission)
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
			$sql = "SELECT * FROM #__users where id IN (" . implode(",", array_values($users)) . ") ORDER BY name asc";
			$db->setQuery($sql);
			$users = $db->loadObjectList();
		}
		$userOptions = array();
		foreach ($users as $user)
		{
			$userOptions[] = HTMLHelper::_('select.option', $user->id, $user->name . " ($user->username)");
		}
		$jevuser = Factory::getUser();
		if ($this->editItem && isset($this->editItem->ics_id) && $this->editItem->ics_id > 0 && $this->editItem->created_by > 0)
		{
			$created_by = $this->editItem->created_by;
		}
		else
		{
			$created_by = $jevuser->id;
		}
		if (count($userOptions) > 0)
		{
			$userlist = HTMLHelper::_('select.genericlist', $userOptions, 'created_by', 'class="inputbox" size="1" ', 'value', 'text', $created_by);
		}
		else
		{
			$userList = "";
		}
		$this->users = $userlist;




		$this->setLayout("edit");

	}

}
